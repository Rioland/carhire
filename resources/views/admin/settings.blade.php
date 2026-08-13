@extends('admin.layout')
@section('title', 'Site settings')

@section('content')

<div class="topbar">
    <div>
        <h1>Site settings</h1>
        <p class="topbar__sub">Business details, homepage copy and search engine information.</p>
    </div>
    <a class="btn btn--ghost" href="{{ route('home') }}" target="_blank" rel="noopener">Preview the website</a>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    @foreach($schema as $group => $fields)
        <div class="panel" style="margin-bottom:1.25rem">
            <div class="panel__head"><h2>{{ $group }}</h2></div>
            <div class="panel__body">
                <div class="formGrid">
                    @foreach($fields as $key => $field)
                        @php $current = old($key, $values[$key] ?? ''); @endphp

                        <div class="field field--full">
                            @if($field['type'] === 'image')
                                <label>{{ $field['label'] }}</label>
                                <div class="imageField">
                                    @if(! empty($values[$key]))
                                        <div>
                                            <img src="{{ asset('storage/' . $values[$key]) }}" alt="">
                                            <div class="check" style="margin-top:.4rem">
                                                <input type="checkbox" id="rm-{{ $key }}" name="remove_{{ $key }}" value="1">
                                                <label for="rm-{{ $key }}" style="font-size:.8125rem">Remove</label>
                                            </div>
                                        </div>
                                    @endif
                                    <div style="flex:1">
                                        <input type="file" name="{{ $key }}" accept="image/*">
                                        @if(! empty($field['hint']))<div class="hint">{{ $field['hint'] }}</div>@endif
                                    </div>
                                </div>
                            @elseif($field['type'] === 'textarea')
                                <label for="s-{{ $key }}">{{ $field['label'] }}</label>
                                <textarea id="s-{{ $key }}" name="{{ $key }}">{{ $current }}</textarea>
                                @if(! empty($field['hint']))<div class="hint">{{ $field['hint'] }}</div>@endif
                            @else
                                <label for="s-{{ $key }}">{{ $field['label'] }}</label>
                                <input id="s-{{ $key }}" type="text" name="{{ $key }}" value="{{ $current }}">
                                @if(! empty($field['hint']))<div class="hint">{{ $field['hint'] }}</div>@endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <button class="btn btn--primary" type="submit">Save settings</button>
</form>

@endsection
