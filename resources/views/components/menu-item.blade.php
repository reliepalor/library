@props(['href', 'text', 'class' => ''])

@php
    $isActive = request()->is($href) || request()->is($href . '/*');
@endphp

<li class="nav-item">
    <a href="{{ $href }}" class="nav-link {{ $isActive ? 'active' : '' }} {{ $class }}">
        {{ $text }}
    </a>
</li>
