# Running your website

Everything on the site is edited at **yourdomain.com/admin**. Nothing here needs a
developer.

## Signing in

Use the email and password set during installation. Change the password after the
first sign-in.

## The sections

### Bookings
Every form on the website lands here. Each one gets a reference like `BK-A3F9K2`.

Open a booking to see the full request, then use **WhatsApp client** to message them
with the reference already filled in. Move it through the statuses as you go:

- **New** — nobody has touched it yet
- **Contacted** — you've reached out
- **Confirmed** — the client has agreed
- **Completed** — the trip happened
- **Cancelled** — it fell through

Use **Internal notes** for the quoted price or who's handling it. Clients never see
these.

### Fleet
Your vehicles. Each one shows a photo, a day rate and a second rate (airport by
default, but you can rename that label).

- Leave the day rate empty and fill in **Price note** instead to show something like
  "Contact us for a quote".
- **Position** controls order — lower numbers appear first.
- Untick **Show on the website** to hide a vehicle without deleting it.

### Fleet categories
The filter buttons above the fleet grid. Add or rename them freely; vehicles are
assigned to one each.

### Cities
Major cities you operate in. Each gets its own page at `/car-rental-lagos` and so on.

**Service highlights** is one item per line — those become the bullet list on the
city card on the homepage.

### Areas
Neighbourhoods inside each city: Lekki Phase 1, Maitama, Trans Amadi.

**This is the important one.** Every area is automatically combined with every
service to create its own page. Adding one area creates a page for each service you
offer. That is how the site covers hundreds of search terms without you writing
hundreds of pages.

### Services
What you offer: moving truck, airport transfer, wedding car, and so on.

- **Directory link text** is the wording on the buttons in the location directory
- **Page headline pattern** builds each page title. Use `{service}`, `{location}` and
  `{city}` — for example `{service} in {location}, {city}` becomes
  "Moving Truck in Lekki Phase 1, Lagos"
- **Include in the location directory** — untick to stop it generating area pages
- **Give it a standalone service page** — creates `/services/moving-truck` and adds it
  to the top navigation

### Blog
Articles at `/articles/your-title`.

- **Excerpt** is what shows on the blog listing card
- **Pin to the top of the blog** puts an article in the featured slot
- Untick **Published** to save a draft nobody can see
- The article body accepts basic HTML: `<p>`, `<h2>`, `<ul>`, `<li>`, `<a>`,
  `<strong>`. Wrap each paragraph in `<p>...</p>`.

### Reviews
Client testimonials for the carousel. Real ones only — invented reviews are both
against the rules of most ad platforms and easy for customers to spot.

### FAQs
The accordion on the homepage. These also feed Google's FAQ rich results, so plain,
direct answers work best.

### Pages
Standalone pages like Privacy Policy and Terms of Service, linked from the footer.

### Site settings
- **Business** — name, tagline, logo
- **Contact** — phone, WhatsApp, email, addresses. The WhatsApp number here powers
  every Book Now button on the site, so get it right: international format with no
  spaces or plus sign, e.g. `2348012345678`
- **Homepage** — hero heading, subheading, background image or video
- **Numbers** — the statistics block. Keep these honest
- **Social** — leave a field empty to hide that icon
- **Search engines** — default page title and description, plus a box for your Google
  Analytics or Search Console code

## Images

Upload landscape photos, roughly 1200×800, under 4MB. Photograph vehicles in
consistent light against a plain background — a clean set of photos does more for
bookings than any other change on this list.

## Things worth knowing

- Nothing is auto-saved. Press **Save** before leaving a form.
- Deleting is permanent. To take something off the site temporarily, untick
  **Show on the website** instead.
- Changing a slug changes the page's web address. Anyone linking to the old address
  gets a 404, so avoid changing slugs on pages that already rank.
- The booking form takes no payment. It collects the request and hands the client to
  WhatsApp.
