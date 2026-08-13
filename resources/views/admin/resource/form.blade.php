@extends('admin.layout')
@section('title', ($record->exists ? 'Edit ' : 'New ') . strtolower($config['singular']))

@section('content')

<div class="topbar">
    <div>
        <h1>{{ $record->exists ? 'Edit' : 'New' }} {{ strtolower($config['singular']) }}</h1>
        <p class="topbar__sub"><a href="{{ route('admin.resource.index', $resource) }}">← Back to {{ strtolower($config['label']) }}</a></p>
    </div>
</div>

<form method="POST" enctype="multipart/form-data"
      action="{{ $record->exists ? route('admin.resource.update', [$resource, $record->getKey()]) : route('admin.resource.store', $resource) }}">
    @csrf
    @if($record->exists) @method('PUT') @endif

    <div class="panel">
        <div class="panel__body">
            <div class="formGrid">
                @foreach($config['fields'] as $name => $field)
                    @php
                        $current = old($name, $record->{$name} ?? ($field['default'] ?? null));
                        $full = ($field['width'] ?? 'full') !== 'half';
                    @endphp

                    <div class="field {{ $full ? 'field--full' : '' }}">
                        @if($field['type'] === 'boolean')
                            <div class="check">
                                <input type="hidden" name="{{ $name }}" value="0">
                                <input type="checkbox" id="f-{{ $name }}" name="{{ $name }}" value="1" @checked($current)>
                                <label for="f-{{ $name }}">{{ $field['label'] }}</label>
                            </div>

                        @elseif($field['type'] === 'image')
                            <label>{{ $field['label'] }}</label>
                            <div class="imageField">
                                @if($record->{$name})
                                    <div>
                                        <img src="{{ asset('storage/' . $record->{$name}) }}" alt="">
                                        <div class="check" style="margin-top:.4rem">
                                            <input type="checkbox" id="rm-{{ $name }}" name="remove_{{ $name }}" value="1">
                                            <label for="rm-{{ $name }}" style="font-size:.8125rem">Remove</label>
                                        </div>
                                    </div>
                                @endif
                                <div style="flex:1">
                                    <input type="file" id="f-{{ $name }}" name="{{ $name }}" accept="image/*">
                                    @if(! empty($field['hint']))<div class="hint">{{ $field['hint'] }}</div>@endif
                                </div>
                            </div>

                        @else
                            <label for="f-{{ $name }}">{{ $field['label'] }}</label>

                            @switch($field['type'])
                                @case('textarea')
                                    <textarea id="f-{{ $name }}" name="{{ $name }}">{{ $current }}</textarea>
                                    @break

                                @case('editor')
                                    <textarea id="f-{{ $name }}" name="{{ $name }}" class="tall">{{ $current }}</textarea>
                                    <div class="hint">Basic HTML is allowed: &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;a&gt;, &lt;strong&gt;.</div>
                                    @break

                                @case('relation')
                                    <select id="f-{{ $name }}" name="{{ $name }}">
                                        <option value="">— none —</option>
                                        @foreach($field['model']::orderBy($field['display'])->get() as $option)
                                            <option value="{{ $option->getKey() }}" @selected((string) $current === (string) $option->getKey())>
                                                {{ $option->{$field['display']} }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('select')
                                    <select id="f-{{ $name }}" name="{{ $name }}">
                                        @foreach($field['options'] as $value => $label)
                                            <option value="{{ $value }}" @selected((string) $current === (string) $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('number')
                                @case('money')
                                    <input id="f-{{ $name }}" type="number" name="{{ $name }}" value="{{ $current }}">
                                    @break

                                @case('date')
                                    <input id="f-{{ $name }}" type="date" name="{{ $name }}"
                                           value="{{ $current ? \Illuminate\Support\Carbon::parse($current)->format('Y-m-d') : '' }}">
                                    @break

                                @case('slug')
                                    <input id="f-{{ $name }}" type="text" name="{{ $name }}" value="{{ $current }}"
                                           placeholder="Leave empty and we will build it from the {{ $field['from'] ?? 'name' }}">
                                    @break

                                @default
                                    <input id="f-{{ $name }}" type="text" name="{{ $name }}" value="{{ $current }}">
                            @endswitch

                            @if(! empty($field['hint']))<div class="hint">{{ $field['hint'] }}</div>@endif
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="formActions">
                <button class="btn btn--primary" type="submit">{{ $record->exists ? 'Save changes' : 'Create ' . strtolower($config['singular']) }}</button>
                <a class="btn btn--ghost" href="{{ route('admin.resource.index', $resource) }}">Cancel</a>
            </div>
        </div>
    </div>
</form>

@endsection
