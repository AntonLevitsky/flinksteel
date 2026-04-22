@props(['form' => 'default', 'class' => 'w-full h-full'])

@php
$slug = is_object($form) ? $form->slug : $form;
@endphp

<svg viewBox="0 0 120 120" class="{{ $class }}" fill="none" xmlns="http://www.w3.org/2000/svg">
@switch($slug)
    @case('rundstahl')
        <circle cx="60" cy="60" r="35" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        <ellipse cx="60" cy="60" rx="35" ry="35" stroke="#6b7280" stroke-width="1" stroke-dasharray="4 4" opacity="0.5"/>
        <circle cx="60" cy="60" r="3" fill="#6b7280"/>
        @break
    @case('flachstahl')
        <rect x="20" y="40" width="80" height="40" rx="2" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        @break
    @case('winkelstahl')
        <path d="M30 30 L30 90 L90 90 L90 78 L42 78 L42 30 Z" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        @break
    @case('ipe-traeger')
        <path d="M35 25 L85 25 L85 35 L65 35 L65 85 L85 85 L85 95 L35 95 L35 85 L55 85 L55 35 L35 35 Z" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        @break
    @case('hea-traeger')
    @case('heb-traeger')
        <path d="M30 25 L90 25 L90 38 L68 38 L68 82 L90 82 L90 95 L30 95 L30 82 L52 82 L52 38 L30 38 Z" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        @break
    @case('u-stahl')
        <path d="M35 25 L35 95 L85 95 L85 25 L73 25 L73 83 L47 83 L47 25 Z" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        @break
    @case('t-stahl')
        <path d="M30 25 L90 25 L90 40 L67 40 L67 95 L53 95 L53 40 L30 40 Z" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        @break
    @case('rechteckrohr')
        <rect x="25" y="35" width="70" height="50" rx="3" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        <rect x="33" y="43" width="54" height="34" rx="2" stroke="#6b7280" stroke-width="1.5" fill="white"/>
        @break
    @case('quadratrohr')
        <rect x="30" y="30" width="60" height="60" rx="3" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        <rect x="38" y="38" width="44" height="44" rx="2" stroke="#6b7280" stroke-width="1.5" fill="white"/>
        @break
    @case('rundrohr')
    @case('praezisionsrohr')
        <circle cx="60" cy="60" r="35" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        <circle cx="60" cy="60" r="27" stroke="#6b7280" stroke-width="1.5" fill="white"/>
        @break
    @case('blech')
        <path d="M20 50 L50 30 L100 30 L100 75 L70 95 L20 95 Z" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        <path d="M50 30 L50 75 L20 95" stroke="#6b7280" stroke-width="1" stroke-dasharray="3 3" opacity="0.5"/>
        <path d="M50 75 L100 75" stroke="#6b7280" stroke-width="1" stroke-dasharray="3 3" opacity="0.5"/>
        @break
    @case('traenenblech')
        <path d="M20 50 L50 30 L100 30 L100 75 L70 95 L20 95 Z" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        <circle cx="45" cy="62" r="2" fill="#9ca3af"/>
        <circle cx="55" cy="55" r="2" fill="#9ca3af"/>
        <circle cx="65" cy="48" r="2" fill="#9ca3af"/>
        <circle cx="75" cy="55" r="2" fill="#9ca3af"/>
        <circle cx="60" cy="70" r="2" fill="#9ca3af"/>
        <circle cx="70" cy="63" r="2" fill="#9ca3af"/>
        <circle cx="80" cy="63" r="2" fill="#9ca3af"/>
        <circle cx="50" cy="76" r="2" fill="#9ca3af"/>
        <circle cx="60" cy="83" r="2" fill="#9ca3af"/>
        @break
    @default
        <rect x="25" y="25" width="70" height="70" rx="4" stroke="#6b7280" stroke-width="2" fill="#f3f4f6"/>
        <text x="60" y="64" text-anchor="middle" font-size="10" fill="#6b7280">Stahl</text>
@endswitch
</svg>
