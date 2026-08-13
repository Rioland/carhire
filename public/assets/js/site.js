/* Public site behaviour. No dependencies. */
(function () {
  'use strict';

  var on = function (el, evt, fn) { if (el) el.addEventListener(evt, fn); };
  var all = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

  /* ---------- Mobile navigation ---------- */
  var burger = document.querySelector('[data-burger]');
  var mobileNav = document.querySelector('[data-mobile-nav]');
  on(burger, 'click', function () {
    var open = mobileNav.getAttribute('data-open') === 'true';
    mobileNav.setAttribute('data-open', String(!open));
    burger.setAttribute('aria-expanded', String(!open));
  });

  /* ---------- Header dropdowns ---------- */
  all('[data-dropdown]').forEach(function (group) {
    var toggle = group.querySelector('.nav__toggle');
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
          card.style.display = match ? '' : 'none';
        });
      });
    });
  }

  /* ---------- Review carousel ---------- */
  var track = document.querySelector('[data-carousel]');
  if (track) {
    var step = function () {
      var first = track.querySelector('.review');
      return first ? first.offsetWidth + 20 : 320;
    };
    on(document.querySelector('[data-carousel-prev]'), 'click', function () {
      track.scrollBy({ left: -step(), behavior: 'smooth' });
    });
    on(document.querySelector('[data-carousel-next]'), 'click', function () {
      track.scrollBy({ left: step(), behavior: 'smooth' });
    });
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
  var modal = document.querySelector('[data-modal]');
  var openModal = function (vehicleId, vehicleName, service) {
    if (!modal) return;
    var select = modal.querySelector('[name="vehicle_id"]');
    var serviceInput = modal.querySelector('[name="service"]');
    var title = modal.querySelector('[data-modal-title]');
    if (select && vehicleId) select.value = vehicleId;
    if (serviceInput && service) serviceInput.value = service;
    if (title) title.textContent = vehicleName ? 'Book the ' + vehicleName : 'Request a vehicle';
    modal.setAttribute('data-open', 'true');
    document.body.style.overflow = 'hidden';
    var firstField = modal.querySelector('input, select, textarea');
    if (firstField) firstField.focus();
  };
  var closeModal = function () {
    if (!modal) return;
    modal.setAttribute('data-open', 'false');
    document.body.style.overflow = '';
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
  on(document, 'keydown', function (e) { if (e.key === 'Escape') closeModal(); });

  /* ---------- Reveal on scroll ---------- */
  var reveals = all('.reveal');
  if (reveals.length && 'IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -60px 0px' });
    reveals.forEach(function (el) { observer.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('is-visible'); });
  }
})();
