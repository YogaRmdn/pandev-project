@props(['messages' => null])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-destructive space-y-1 text-sm']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
