@props(['name', 'class' => 'h-5 w-5'])

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
     stroke-linecap="round" stroke-linejoin="round" {{ $attributes->merge(['class' => $class]) }}>
    @switch($name)
        @case('home')
            <path d="M4 11.5 12 4l8 7.5" />
            <path d="M6 10v9a1 1 0 0 0 1 1h3v-6h4v6h3a1 1 0 0 0 1-1v-9" />
            @break

        @case('users')
            <circle cx="9" cy="8" r="3" />
            <path d="M3.5 20c.5-3.2 2.8-5 5.5-5s5 1.8 5.5 5" />
            <circle cx="17" cy="9" r="2.2" />
            <path d="M15.3 13.8c2 .4 3.6 2.1 4.2 4.2" />
            @break

        @case('id-badge')
            <rect x="5" y="4" width="14" height="17" rx="2" />
            <circle cx="12" cy="10" r="2.4" />
            <path d="M8.3 16.5c.7-1.6 2-2.4 3.7-2.4s3 .8 3.7 2.4" />
            <path d="M9 7h6" />
            @break

        @case('academic-cap')
            <path d="M2 9 12 4l10 5-10 5-10-5Z" />
            <path d="M6 11.5V16c0 1.4 2.8 2.5 6 2.5s6-1.1 6-2.5v-4.5" />
            <path d="M21 9.5V15" />
            @break

        @case('layers')
            <path d="M12 3 3 8l9 5 9-5-9-5Z" />
            <path d="M3 12l9 5 9-5" />
            <path d="M3 16l9 5 9-5" />
            @break

        @case('building')
            <rect x="4" y="3" width="16" height="18" rx="1.5" />
            <path d="M8 7h1.5M8 11h1.5M8 15h1.5M14.5 7H16M14.5 11H16M14.5 15H16" />
            <path d="M10 21v-4h4v4" />
            @break

        @case('trending-up')
            <circle cx="12" cy="12" r="9" />
            <path d="M8 13 12 9l4 4" />
            <path d="M12 9v6" />
            @break

        @case('tag')
            <path d="M3 12 12 3h6a3 3 0 0 1 3 3v6l-9 9a2 2 0 0 1-2.8 0L3 14.8a2 2 0 0 1 0-2.8Z" />
            <circle cx="16" cy="8" r="1.4" />
            @break

        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <path d="m3.5 6 8.5 7 8.5-7" />
            @break

        @case('calendar')
            <rect x="3.5" y="5" width="17" height="16" rx="2" />
            <path d="M8 3.2v3.6M16 3.2v3.6M3.5 10h17" />
            @break

        @case('alert-triangle')
            <path d="M12 4 22 20H2L12 4Z" />
            <path d="M12 10.3v4.3" />
            <circle cx="12" cy="17.2" r=".9" fill="currentColor" stroke="none" />
            @break

        @case('user-group')
            <circle cx="12" cy="8" r="3" />
            <path d="M5.5 20c.6-3.6 3-5.6 6.5-5.6s5.9 2 6.5 5.6" />
            <circle cx="5" cy="9" r="1.8" />
            <circle cx="19" cy="9" r="1.8" />
            <path d="M2 16.5c.4-2 1.7-3.2 3.4-3.4M18.6 13.1c1.7.2 3 1.4 3.4 3.4" />
            @break

        @case('clock')
            <circle cx="12" cy="12" r="9" />
            <path d="M12 7v5l3.5 2" />
            @break

        @case('banknote')
            <rect x="2.5" y="6.5" width="19" height="11" rx="2" />
            <circle cx="12" cy="12" r="2.6" />
            @break

        @case('shield-check')
            <path d="M12 3 5 6v6c0 4.5 3 7.5 7 9 4-1.5 7-4.5 7-9V6l-7-3Z" />
            <path d="m9 12 2 2 4-4.3" />
            @break

        @case('logout')
            <path d="M9 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h3" />
            <path d="M14 8l4 4-4 4" />
            <path d="M18 12H9" />
            @break

        @case('chevron-down')
            <path d="m6 9 6 6 6-6" />
            @break

        @case('menu')
            <path d="M4 6h16M4 12h16M4 18h16" />
            @break

        @case('x-mark')
            <path d="M6 6l12 12M18 6 6 18" />
            @break

        @case('bell')
            <path d="M6 10a6 6 0 1 1 12 0c0 4 1.3 5.5 1.3 5.5H4.7S6 14 6 10Z" />
            <path d="M10 18.5a2 2 0 0 0 4 0" />
            @break

        @default
            <circle cx="12" cy="12" r="3" />
    @endswitch
</svg>
