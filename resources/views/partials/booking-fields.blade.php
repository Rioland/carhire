@php
    $context = $context ?? 'page';
    $vehicles = $vehicles ?? collect();
@endphp

@if($errors->any() && old('_context') === $context)
    <div class="errors">
        <ul>
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('booking.store') }}">
    @csrf
    <input type="hidden" name="_context" value="{{ $context }}">
    <input type="hidden" name="source_url" value="{{ url()->current() }}">
    <div class="hp"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

    <div class="field">
        <label for="{{ $context }}-name">Full name</label>
        <input id="{{ $context }}-name" type="text" name="name" value="{{ old('name') }}" required>
    </div>

    <div class="field field--row">
        <div>
            <label for="{{ $context }}-phone">Phone</label>
            <input id="{{ $context }}-phone" type="tel" name="phone" value="{{ old('phone') }}" required placeholder="080...">
        </div>
        <div>
            <label for="{{ $context }}-date">Pickup date</label>
            <input id="{{ $context }}-date" type="date" name="pickup_date" value="{{ old('pickup_date') }}">
        </div>
    </div>

    <div class="field">
        <label for="{{ $context }}-vehicle">Vehicle</label>
        <select id="{{ $context }}-vehicle" name="vehicle_id">
            <option value="">Not sure yet — advise me</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>{{ $vehicle->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="field field--row">
        <div>
            <label for="{{ $context }}-pickup">Pickup location</label>
            <input id="{{ $context }}-pickup" type="text" name="pickup_location" value="{{ old('pickup_location') }}" placeholder="e.g. Ikeja">
        </div>
        <div>
            <label for="{{ $context }}-days">Number of days</label>
            <input id="{{ $context }}-days" type="number" name="days" min="1" value="{{ old('days', 1) }}">
        </div>
    </div>

    <div class="field">
        <label for="{{ $context }}-destination">Where are you going?</label>
        <input id="{{ $context }}-destination" type="text" name="destination" value="{{ old('destination') }}" placeholder="Areas or route">
    </div>

    <input type="hidden" name="service" value="{{ old('service') }}">

    <div class="field">
        <label for="{{ $context }}-notes">Anything else</label>
        <textarea id="{{ $context }}-notes" name="notes" style="min-height:80px">{{ old('notes') }}</textarea>
    </div>

    <button type="submit" class="btn btn--primary btn--block">Send booking request</button>
    <p class="formNote">We reply within minutes during working hours. No payment is taken on this form.</p>
</form>
