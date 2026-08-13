/* Public site behaviour. No dependencies, no build step.
   Every enhancement here degrades to a working page if JS fails or if the
   visitor has asked for reduced motion. */
(function () {
  'use strict';

  var on = function (el, evt, fn, opts) { if (el) el.addEventListener(evt, fn, opts); };
  var all = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };
  var one = function (sel, root) { return (root || document).querySelector(sel); };

  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Mobile navigation ---------- */
  var burger = one('[data-burger]');
  var mobileNav = one('[data-mobile-nav]');
  on(burger, 'click', function () {
    var open = mobileNav.getAttribute('data-open') === 'true';
    mobileNav.setAttribute('data-open', String(!open));
    burger.setAttribute('aria-expanded', String(!open));
  });

  /* ---------- Header dropdowns ---------- */
  all('[data-dropdown]').forEach(function (group) {
    var toggle = one('.nav__toggle', group);
    on(toggle, 'click', function (e) {
      e.stopPropagation();
      var open = group.getAttribute('data-open') === 'true';
      all('[data-dropdown]').forEach(function (g) { g.setAttribute('data-open', 'false'); });
      group.setAttribute('data-open', String(!open));
      toggle.setAttribute('aria-expanded', String(!open));
    });
  });
  on(document, 'click', function () {
    all('[data-dropdown]').forEach(function (g) { g.setAttribute('data-open', 'false'); });
  });

  /* ---------- Header condense + reading progress ----------
     Both read the same scroll position, so they share one rAF-throttled pass. */
  var header = one('.header');
  var progress = one('[data-progress]');
  var toTop = one('[data-to-top]');
  var ticking = false;

  var onScroll = function () {
    var y = window.pageYOffset || document.documentElement.scrollTop;

    if (header) header.classList.toggle('is-scrolled', y > 12);
    if (toTop) toTop.classList.toggle('is-visible', y > 700);

    if (progress) {
      var doc = document.documentElement;
      var max = (doc.scrollHeight - window.innerHeight) || 1;
      progress.style.setProperty('--progress', Math.min(y / max, 1).toFixed(4));
    }

    ticking = false;
  };

  on(window, 'scroll', function () {
    if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
  }, { passive: true });
  onScroll();

  on(toTop, 'click', function () {
    window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
  });

  /* ---------- Fleet filter ---------- */
  var filters = all('[data-filter]');
  if (filters.length) {
    var cards = all('[data-category]');
    filters.forEach(function (button) {
      on(button, 'click', function () {
        var target = button.getAttribute('data-filter');
        filters.forEach(function (b) { b.setAttribute('aria-pressed', String(b === button)); });

        cards.forEach(function (card) {
          var match = target === 'all' || card.getAttribute('data-category') === target;

          if (reduced) {
            card.hidden = !match;
            return;
          }

          if (match) {
            card.hidden = false;
            // next frame, so the browser paints the hidden state before fading in
            window.requestAnimationFrame(function () { card.classList.remove('is-filtering'); });
          } else {
            card.classList.add('is-filtering');
            window.setTimeout(function () {
              if (card.classList.contains('is-filtering')) card.hidden = true;
            }, 180);
          }
        });
      });
    });
  }

  /* ---------- Reviews carousel ---------- */
  var track = one('[data-carousel]');
  if (track) {
    var slides = all('.review', track);
    var step = function () {
      var first = slides[0];
      return first ? first.offsetWidth + 20 : 320;
    };

    on(one('[data-carousel-prev]'), 'click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    on(one('[data-carousel-next]'), 'click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });

    // Dots double as a position indicator and a jump control.
    var dotWrap = one('[data-carousel-dots]');
    if (dotWrap && slides.length > 1) {
      slides.forEach(function (slide, i) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.setAttribute('aria-label', 'Go to review ' + (i + 1));
        dot.setAttribute('aria-current', String(i === 0));
        on(dot, 'click', function () { track.scrollTo({ left: i * step(), behavior: reduced ? 'auto' : 'smooth' }); });
        dotWrap.appendChild(dot);
      });

      var dots = all('button', dotWrap);
      var syncing = false;
      on(track, 'scroll', function () {
        if (syncing) return;
        syncing = true;
        window.requestAnimationFrame(function () {
          var index = Math.round(track.scrollLeft / step());
          dots.forEach(function (d, i) { d.setAttribute('aria-current', String(i === index)); });
          syncing = false;
        });
      }, { passive: true });
    }
  }

  /* ---------- FAQ accordion ---------- */
  all('[data-faq] .faq__q').forEach(function (button) {
    on(button, 'click', function () {
      var item = button.closest('.faq__item');
      var open = item.getAttribute('data-open') === 'true';
      item.setAttribute('data-open', String(!open));
      button.setAttribute('aria-expanded', String(!open));
    });
  });

  /* ---------- Booking modal ---------- */
  var modal = one('[data-modal]');
  var lastFocus = null;

  var openModal = function (vehicleId, vehicleName, service) {
    if (!modal) return;
    lastFocus = document.activeElement;

    var select = one('[name="vehicle_id"]', modal);
    var serviceInput = one('[name="service"]', modal);
    var title = one('[data-modal-title]', modal);

    if (select && vehicleId) select.value = vehicleId;
    if (serviceInput && service) serviceInput.value = service;
    if (title) title.textContent = vehicleName ? 'Book the ' + vehicleName : 'Request a vehicle';

    modal.setAttribute('data-open', 'true');
    document.body.style.overflow = 'hidden';

    var firstField = one('input, select, textarea', modal);
    if (firstField) firstField.focus();
  };

  var closeModal = function () {
    if (!modal) return;
    modal.setAttribute('data-open', 'false');
    document.body.style.overflow = '';
    if (lastFocus && lastFocus.focus) lastFocus.focus();
  };

  all('[data-book]').forEach(function (button) {
    on(button, 'click', function (e) {
      e.preventDefault();
      openModal(
        button.getAttribute('data-vehicle-id'),
        button.getAttribute('data-vehicle-name'),
        button.getAttribute('data-service')
      );
    });
  });

  all('[data-modal-close]').forEach(function (el) { on(el, 'click', closeModal); });

  on(document, 'keydown', function (e) {
    if (e.key === 'Escape') closeModal();

    // Keep tabbing inside the dialog while it is open.
    if (e.key !== 'Tab' || !modal || modal.getAttribute('data-open') !== 'true') return;

    var focusable = all('a[href], button:not([disabled]), input, select, textarea', modal)
      .filter(function (el) { return el.offsetParent !== null; });
    if (!focusable.length) return;

    var first = focusable[0];
    var last = focusable[focusable.length - 1];

    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  });

  /* ---------- Counting stats ---------- */
  var countUp = function (el) {
    var raw = el.getAttribute('data-count-to') || el.textContent;
    var match = String(raw).match(/^([^\d]*)([\d.,]+)(.*)$/);
    if (!match) return;

    var prefix = match[1];
    var suffix = match[3];
    var target = parseFloat(match[2].replace(/,/g, ''));
    if (isNaN(target)) return;

    var decimals = (match[2].split('.')[1] || '').length;
    var grouped = match[2].indexOf(',') !== -1;
    var duration = 1100;
    var startedAt = null;

    var format = function (value) {
      var out = value.toFixed(decimals);
      if (grouped) out = Number(out).toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
      return prefix + out + suffix;
    };

    var frame = function (now) {
      if (startedAt === null) startedAt = now;
      var p = Math.min((now - startedAt) / duration, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = format(target * eased);
      if (p < 1) window.requestAnimationFrame(frame);
    };

    window.requestAnimationFrame(frame);
  };

  /* ---------- Reveal on scroll ---------- */
  var revealables = all('.reveal, .stagger');

  if (!('IntersectionObserver' in window) || reduced) {
    revealables.forEach(function (el) { el.classList.add('is-visible'); });
    all('[data-count]').forEach(function (el) {
      var raw = el.getAttribute('data-count-to');
      if (raw) el.textContent = raw;
    });
  } else {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -60px 0px', threshold: 0.05 });

    revealables.forEach(function (el) { observer.observe(el); });

    var counters = all('[data-count]');
    if (counters.length) {
      var countObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          countUp(entry.target);
          countObserver.unobserve(entry.target);
        });
      }, { threshold: 0.4 });

      counters.forEach(function (el) { countObserver.observe(el); });
    }
  }
})();
