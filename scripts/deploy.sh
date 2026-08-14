#!/usr/bin/env bash
#
# Deploy this site to Hostinger from your own machine.
#
#   ./scripts/deploy.sh            deploy
#   ./scripts/deploy.sh --dry-run  show exactly what would change, transfer nothing
#
# Does the same work as .github/workflows/deploy.yml, without needing GitHub
# Actions minutes. Requires SSH key access to the host — see the README section
# at the bottom of this file if `ssh` still asks for a password.
#
set -euo pipefail

SSH_HOST="${SSH_HOST:-82.180.138.245}"
SSH_PORT="${SSH_PORT:-65002}"
SSH_USER="${SSH_USER:-u800077713}"
APP_DIR="${APP_DIR:-/home/u800077713/carhire}"
WEB_DIR="${WEB_DIR:-/home/u800077713/public_html}"
SITE_URL="${SITE_URL:-https://ghostwhite-mink-815498.hostingersite.com}"

DRY=""
[[ "${1:-}" == "--dry-run" ]] && DRY="--dry-run"

cd "$(dirname "$0")/.."
ROOT=$(pwd)

bold() { printf '\n\033[1m%s\033[0m\n' "$1"; }
ok()   { printf '  \033[32m✓\033[0m %s\n' "$1"; }
die()  { printf '  \033[31m✗ %s\033[0m\n' "$1" >&2; exit 1; }

SSH="ssh -p $SSH_PORT -o ConnectTimeout=20 -o BatchMode=yes ${SSH_USER}@${SSH_HOST}"

# ---------------------------------------------------------------- preflight
bold "Preflight"

$SSH "echo ok" >/dev/null 2>&1 \
  || die "SSH to ${SSH_USER}@${SSH_HOST}:${SSH_PORT} failed.
     Key auth must work without a password. Check:
       ssh -p ${SSH_PORT} ${SSH_USER}@${SSH_HOST}
     If that returns '/sbin/nologin', enable SSH in hPanel first."
ok "SSH reachable"

$SSH "test -f ${APP_DIR}/artisan" \
  || die "No Laravel app at ${APP_DIR} — do the first install before deploying."
ok "Application found at ${APP_DIR}"

$SSH "test -f ${APP_DIR}/.env" \
  || die "No .env at ${APP_DIR}/.env — the app cannot boot without it."
ok ".env present on the server (never overwritten by this script)"

command -v composer >/dev/null || die "composer not installed locally"
ok "composer available"

# ---------------------------------------------------------------- build
bold "Build"

composer install --no-dev --optimize-autoloader --no-interaction --quiet
ok "production dependencies installed ($(ls vendor | wc -l | tr -d ' ') packages)"

# The committed public/index.php points at ../vendor, which is right for local
# dev. The server needs ../carhire/vendor. Patch a copy, never the working file.
STAGE=$(mktemp -d)
trap 'rm -rf "$STAGE"' EXIT
cp -R public/. "$STAGE/"
sed -i '' \
  -e "s#__DIR__\.'/\.\./vendor/autoload\.php'#__DIR__.'/../carhire/vendor/autoload.php'#" \
  -e "s#__DIR__\.'/\.\./bootstrap/app\.php'#__DIR__.'/../carhire/bootstrap/app.php'#" \
  -e "s#__DIR__\.'/\.\./storage/framework/maintenance\.php'#__DIR__.'/../carhire/storage/framework/maintenance.php'#" \
  "$STAGE/index.php"
grep -q "carhire/vendor" "$STAGE/index.php" || die "index.php repoint failed"
ok "index.php repointed at ../carhire"

# ---------------------------------------------------------------- transfer
bold "Transfer${DRY:+ (dry run — nothing will change)}"

# Only these paths move. storage/ and .env are not in the list, so --delete
# cannot reach uploaded photos, logs or credentials.
rsync -az --delete $DRY --info=stats1 \
  -e "ssh -p $SSH_PORT" \
  --exclude='bootstrap/cache/*.php' \
  --exclude='database/database.sqlite' \
  app bootstrap config database resources routes vendor \
  artisan composer.json composer.lock \
  "${SSH_USER}@${SSH_HOST}:${APP_DIR}/" | sed 's/^/  /'
ok "application synced"

rsync -az --delete $DRY --info=stats1 \
  -e "ssh -p $SSH_PORT" \
  --exclude='storage' \
  --exclude='setup.php' \
  "$STAGE/" \
  "${SSH_USER}@${SSH_HOST}:${WEB_DIR}/" | sed 's/^/  /'
ok "web root synced"

if [[ -n "$DRY" ]]; then
  bold "Dry run complete — no changes made, no migrations run."
  exit 0
fi

# ---------------------------------------------------------------- migrate
bold "Migrate and rebuild caches"

# db:seed is deliberately NOT run. The seeder uses updateOrCreate keyed on slug,
# so running it here would overwrite everything edited in the dashboard with the
# original seeded text. Seeding is a first-install step only.
$SSH "cd ${APP_DIR} && \
  php artisan migrate --force && \
  php artisan config:clear && php artisan config:cache && \
  php artisan route:clear  && php artisan route:cache && \
  php artisan view:clear   && php artisan view:cache && \
  php artisan cache:clear" 2>&1 | sed 's/^/  /'
ok "migrations applied, caches rebuilt"

# ---------------------------------------------------------------- verify
bold "Verify"

for attempt in 1 2 3; do
  code=$(curl -s -o /dev/null -w '%{http_code}' --max-time 25 "${SITE_URL}/" || echo 000)
  if [[ "$code" == "200" ]]; then
    ok "${SITE_URL} returned 200"
    bold "Deployed."
    exit 0
  fi
  printf '  attempt %d: HTTP %s\n' "$attempt" "$code"
  sleep 8
done

die "Site did not return 200. Check ${APP_DIR}/storage/logs/laravel.log"
