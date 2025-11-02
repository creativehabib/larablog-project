@props(['items', 'level' => 0])

@php
    $items = $items instanceof \Illuminate\Support\Collection ? $items : collect($items);
@endphp

@if($items->isNotEmpty())
    <ul class="{{ $level === 0 ? 'headerMenuUl' : 'headerSubMenu' }}">
        @foreach($items as $item)
            @php
                $hasChildren = $item->children->isNotEmpty();
                $currentUrl = url()->current();
                $isActive = rtrim($currentUrl, '/') === rtrim($item->url, '/');
            @endphp
            <li class="{{ $level === 0 ? 'headerMenuItem' : 'headerSubMenuItem' }} {{ $hasChildren ? 'has-children' : '' }} {{ $isActive ? 'active' : '' }}">
                <a href="{{ $item->url }}" target="{{ $item->target }}" class="{{ $level === 0 ? 'headerMenuLink' : 'headerSubMenuLink' }}" @if($item->target === '_blank') rel="noopener" @endif>
                    {{ $item->title }}
                </a>

                @if($hasChildren)
                    @include('front.partials.primary-menu', ['items' => $item->children, 'level' => $level + 1])
                @endif
            </li>
        @endforeach
    </ul>
@endif
