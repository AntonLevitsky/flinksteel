@props(['icon' => 'default', 'class' => 'w-12 h-12'])

<svg viewBox="0 0 48 48" class="{{ $class }}" fill="none" xmlns="http://www.w3.org/2000/svg">
@switch($icon)
    @case('bars')
        <circle cx="16" cy="24" r="6" stroke="#1e3a8a" stroke-width="2" fill="#dbeafe"/>
        <rect x="26" y="18" width="14" height="4" rx="1" stroke="#1e3a8a" stroke-width="1.5" fill="#dbeafe"/>
        <rect x="26" y="26" width="14" height="4" rx="1" stroke="#1e3a8a" stroke-width="1.5" fill="#dbeafe"/>
        @break
    @case('profile')
        <path d="M10 10 L38 10 L38 18 L28 18 L28 38 L20 38 L20 18 L10 18 Z" stroke="#1e3a8a" stroke-width="2" fill="#dbeafe"/>
        @break
    @case('sheet')
        <path d="M8 20 L20 12 L40 12 L40 32 L28 40 L8 40 Z" stroke="#1e3a8a" stroke-width="2" fill="#dbeafe"/>
        @break
    @case('tube')
        <circle cx="24" cy="24" r="14" stroke="#1e3a8a" stroke-width="2" fill="#dbeafe"/>
        <circle cx="24" cy="24" r="8" stroke="#1e3a8a" stroke-width="1.5" fill="white"/>
        @break
    @case('stainless')
        <circle cx="24" cy="24" r="12" stroke="#1e3a8a" stroke-width="2" fill="#dbeafe"/>
        <path d="M18 24 L22 28 L30 20" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('nonferrous')
        <rect x="12" y="12" width="24" height="24" rx="4" stroke="#1e3a8a" stroke-width="2" fill="#dbeafe"/>
        <text x="24" y="28" text-anchor="middle" font-size="10" font-weight="bold" fill="#1e3a8a">NE</text>
        @break
    @default
        <rect x="10" y="10" width="28" height="28" rx="4" stroke="#1e3a8a" stroke-width="2" fill="#dbeafe"/>
@endswitch
</svg>
