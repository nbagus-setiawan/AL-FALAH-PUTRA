<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — SIAP AFP</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lexend:500,600,700|ibm-plex-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-cream-100 text-ink-900 antialiased">

    {{--
        Bungkus seluruh shell (sidebar + konten) dalam satu kartu besar rounded,
        mengikuti struktur referensi desain: dashboard duduk sebagai satu panel
        besar di atas latar yang sedikit berbeda tone-nya. Di layar kecil, kartu
        ini melebur full-bleed (tanpa padding/rounding) supaya sidebar overlay
        tetap berfungsi normal.
    --}}
    <div class="min-h-screen p-0 lg:p-6">
        <div class="mx-auto flex min-h-screen max-w-[1600px] bg-cream-50 lg:min-h-[calc(100vh-3rem)] lg:overflow-hidden lg:rounded-[28px] lg:shadow-xl lg:ring-1 lg:ring-ink-900/5">

            {{-- Backdrop untuk sidebar mobile --}}
            <div id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-ink-900/40 lg:hidden" onclick="toggleSidebar()"></div>

            {{-- Sidebar --}}
            <aside id="sidebar"
                   class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-forest-900 transition-transform duration-200 ease-in-out lg:static lg:translate-x-0">

                {{-- Brand mark: pakai logo asli kalau sudah ditaruh di public/images/logo.*,
                     kalau belum ada, otomatis fallback ke monogram "AFP" --}}
                @php
                    $logoPath = collect(['images/logo.svg', 'images/logo.png', 'images/logo.jpg', 'images/logo.webp'])
                        ->first(fn ($path) => file_exists(public_path($path)));
                @endphp
                <div class="flex items-center gap-3 px-6 py-6">
                    @if ($logoPath)
                        <img src="{{ asset($logoPath) }}" alt="Logo SIAP AFP"
                             class="h-10 w-10 shrink-0 rounded-xl bg-white object-contain p-1 ring-1 ring-white/10">
                    @else
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500/15 font-display text-xs font-bold tracking-wide text-gold-400 ring-1 ring-gold-500/20">
                            AFP
                        </span>
                    @endif
                    <div class="min-w-0">
                        <p class="font-display truncate text-base font-semibold text-white">SIAP AFP</p>
                        <p class="truncate text-xs text-forest-300">{{ $roleLabel ?? '' }}</p>
                    </div>
                </div>

                <nav class="flex-1 overflow-y-auto px-4 pb-4">
                    @foreach ($navGroups ?? [] as $group)
                        <div class="mb-6">
                            @if (! empty($group['label']))
                                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-forest-300/70">
                                    {{ $group['label'] }}
                                </p>
                            @endif
                            <ul class="space-y-1">
                                @foreach ($group['items'] as $item)
                                    @php $active = request()->routeIs($item['pattern'] ?? $item['route']); @endphp
                                    <li>
                                        {{-- Item aktif = pill emas penuh (mengikuti pola "active pill" pada
                                             referensi), item lain tetap transparan dengan hover halus. --}}
                                        <a href="{{ route($item['route']) }}"
                                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                                               {{ $active ? 'bg-gold-500 text-forest-950 shadow-sm' : 'text-forest-100/80 hover:bg-forest-800/60 hover:text-white' }}">
                                            <x-icon :name="$item['icon']"
                                                    class="h-5 w-5 shrink-0 {{ $active ? 'text-forest-950' : 'text-forest-300' }}" />
                                            <span class="truncate">{{ $item['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </nav>

                {{-- Kartu user + logout --}}
                <div class="relative overflow-hidden border-t border-white/5 px-4 py-4">
                    <svg class="pointer-events-none absolute -bottom-6 -right-6 h-28 w-28 text-white/5" viewBox="0 0 100 100" fill="none">
                        <path d="M10 95V50a40 40 0 0 1 80 0v45" stroke="currentColor" stroke-width="2" />
                    </svg>
                    <div class="relative flex items-center gap-3 rounded-xl bg-forest-800/60 p-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gold-500 font-display text-sm font-semibold text-forest-950">
                            {{ strtoupper(mb_substr(auth()->user()->name ?? '?', 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-forest-300">{{ '@'.auth()->user()->username }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-lg p-2 text-forest-300 transition hover:bg-forest-700 hover:text-white" title="Keluar">
                                <x-icon name="logout" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Konten utama --}}
            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-ink-900/5 bg-cream-50/90 px-4 py-4 backdrop-blur sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" class="shrink-0 rounded-lg p-2 text-ink-900 hover:bg-ink-900/5 lg:hidden" onclick="toggleSidebar()">
                            <x-icon name="menu" class="h-5 w-5" />
                        </button>
                        <div class="min-w-0">
                            <p class="font-display truncate text-lg font-semibold text-ink-900">@yield('title', 'Dashboard')</p>
                            @hasSection('subtitle')
                                <p class="truncate text-sm text-slate-500">@yield('subtitle')</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-3">
                        @yield('header-actions')
                        <span class="hidden h-9 w-9 items-center justify-center rounded-full bg-forest-800 font-display text-sm font-semibold text-white sm:flex">
                            {{ strtoupper(mb_substr(auth()->user()->name ?? '?', 0, 1)) }}
                        </span>
                    </div>
                </header>

                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            <x-icon name="shield-check" class="mt-0.5 h-4 w-4 shrink-0" />
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-backdrop').classList.toggle('hidden');
        }
    </script>
</body>
</html>