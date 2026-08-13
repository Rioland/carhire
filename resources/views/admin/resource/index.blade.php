@extends('admin.layout')
@section('title', $config['label'])

@section('content')

<div class="topbar">
    <div>
        <h1>{{ $config['label'] }}</h1>
        <p class="topbar__sub">{{ $records->total() }} {{ Str::plural('item', $records->total()) }}</p>
    </div>
    <a class="btn btn--primary" href="{{ route('admin.resource.create', $resource) }}">Add {{ strtolower($config['singular']) }}</a>
</div>

@if(! empty($config['note']))
    <div class="note">{{ $config['note'] }}</div>
@endif

@if(! empty($config['search']))
    <form class="toolbar" method="GET">
        <input type="search" name="q" value="{{ $term }}" placeholder="Search {{ strtolower($config['label']) }}">
        <button class="btn btn--ghost btn--sm" type="submit">Search</button>
        @if($term)<a class="btn btn--ghost btn--sm" href="{{ route('admin.resource.index', $resource) }}">Clear</a>@endif
    </form>
@endif

<div class="panel">
    @if($records->isEmpty())
        <div class="empty">
            Nothing here yet.
            <div style="margin-top:1rem"><a class="btn btn--primary" href="{{ route('admin.resource.create', $resource) }}">Add the first {{ strtolower($config['singular']) }}</a></div>
        </div>
    @else
        <div class="tableWrap">
            <table>
                <thead>
                <tr>
                    @foreach($config['columns'] as $column)
                        <th>{{ $column['label'] }}</th>
                    @endforeach
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($records as $record)
                    <tr>
                        @foreach($config['columns'] as $key => $column)
                            @php $value = data_get($record, $key); @endphp
                            <td>
                                @switch($column['type'] ?? 'text')
                                    @case('thumb')
                                        @if($value)
                                            <img class="thumb" src="{{ asset('storage/' . $value) }}" alt="">
                                        @else
                                            <span class="thumb" style="display:inline-block"></span>
                                        @endif
                                        @break
                                    @case('boolean')
                                        <span class="dot {{ $value ? 'dot--on' : '' }}">{{ $value ? 'Live' : 'Hidden' }}</span>
                                        @break
                                    @case('money')
                                        <span class="mono" style="font-size:.8125rem">{{ $value ? 'NGN ' . number_format($value) : '—' }}</span>
                                        @break
                                    @case('date')
                                        {{ $value ? \Illuminate\Support\Carbon::parse($value)->format('j M Y') : '—' }}
                                        @break
                                    @default
                                        {{ $value !== null && $value !== '' ? Str::limit((string) $value, 60) : '—' }}
                                @endswitch
                            </td>
                        @endforeach
                        <td class="actions">
                            <a class="btn btn--ghost btn--sm" href="{{ route('admin.resource.edit', [$resource, $record->getKey()]) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.resource.destroy', [$resource, $record->getKey()]) }}"
                                  style="display:inline" onsubmit="return confirm('Delete this {{ strtolower($config['singular']) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="btn btn--danger btn--sm" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $records->links() }}</div>
    @endif
</div>

@endsection
