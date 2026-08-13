@php
    $modalVehicles = \App\Models\Vehicle::active()->orderBy('sort_order')->get(['id', 'name']);
@endphp

<div class="modal" data-modal data-open="false" role="dialog" aria-modal="true" aria-labelledby="bookingTitle">
    <div class="modal__scrim" data-modal-close></div>
    <div class="modal__panel">
        <button class="modal__close" data-modal-close aria-label="Close">&times;</button>
        <div class="eyebrow">Booking request</div>
        <h2 id="bookingTitle" data-modal-title style="font-size:1.375rem">Request a vehicle</h2>
        <p style="color:var(--muted); font-size:.9375rem">Send the details and we will confirm availability. You can finish the conversation on WhatsApp straight after.</p>

        @include('partials.booking-fields', ['vehicles' => $modalVehicles, 'context' => 'modal'])
    </div>
</div>
