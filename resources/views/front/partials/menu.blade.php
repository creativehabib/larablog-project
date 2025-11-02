{{-- resources/views/front/partials/menu.blade.php --}}
@if($items->isNotEmpty())
    <ul class="{{ $isSubmenu ? 'dropdown-menu' : 'navbar-nav' }}">
        @foreach($items as $item)
            <li class="{{ $item->children->isNotEmpty() ? 'nav-item dropdown' : 'nav-item' }}">
                <a href="{{ $item->url }}" target="{{ $item->target }}"
                   class="{{ $item->children->isNotEmpty() ? 'nav-link dropdown-toggle' : 'nav-link' }}"
                   @if($item->children->isNotEmpty()) role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @endif>
                    {{ $item->title }}
                </a>
                @if($item->children->isNotEmpty())
                    @include('front.partials.menu', ['items' => $item->children, 'isSubmenu' => true])
                @endif
            </li>
        @endforeach
    </ul>
@endif
