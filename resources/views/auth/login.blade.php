<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SIAP AFP</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lexend:500,600,700|ibm-plex-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body min-h-screen bg-cream-50 text-ink-900 antialiased">
    <div class="flex min-h-screen">

        {{-- Panel kiri: brand (disembunyikan di layar kecil) --}}
        <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-forest-900 p-12 text-white lg:flex">
            <div class="relative z-10">
                <p class="font-display text-xl font-semibold tracking-tight">SIAP AFP</p>
                <p class="mt-1 text-sm text-forest-200">Sistem Informasi Administrasi Pondok Al Falah Putra</p>
            </div>
            <div class="relative z-10 max-w-sm">
                <p class="font-display text-2xl font-medium leading-snug text-white/90">
                    &ldquo;Mengelola data santri, persuratan, perizinan, dan RAPB dalam satu sistem yang tertata.&rdquo;
                </p>
            </div>
            <svg class="pointer-events-none absolute -bottom-16 -right-16 h-80 w-80 text-forest-800/60" viewBox="0 0 200 200" fill="none">
                <path d="M20 190V90a80 80 0 0 1 160 0v100" stroke="currentColor" stroke-width="1.5" />
                <path d="M45 190v-95a55 55 0 0 1 110 0v95" stroke="currentColor" stroke-width="1.5" opacity=".6" />
            </svg>
        </div>

        {{-- Panel kanan: form login --}}
        <div class="flex w-full flex-col justify-center px-6 py-12 sm:px-12 lg:w-1/2 lg:px-20">
            <div class="mx-auto w-full max-w-sm">
                <p class="font-display text-lg font-semibold text-ink-900 lg:hidden">SIAP AFP</p>
                <h1 class="mt-6 font-display text-2xl font-semibold text-ink-900">Masuk ke akun Anda</h1>
                <p class="mt-2 text-sm text-slate-500">Gunakan username dan kata sandi yang terdaftar.</p>

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="username" class="block text-sm font-medium text-ink-900">Username</label>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" autofocus
                               class="mt-1.5 block w-full rounded-xl border border-ink-900/10 bg-white px-3.5 py-2.5 text-sm text-ink-900 shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />
                        @error('username')
                            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-ink-900">Kata Sandi</label>
                        <input id="password" name="password" type="password"
                               class="mt-1.5 block w-full rounded-xl border border-ink-900/10 bg-white px-3.5 py-2.5 text-sm text-ink-900 shadow-sm focus:border-forest-600 focus:outline-none focus:ring-2 focus:ring-forest-600/20" />
                        @error('password')
                            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-ink-900/20 text-forest-700 focus:ring-forest-600/30" />
                        Ingat saya
                    </label>
                    <button type="submit"
                            class="w-full rounded-xl bg-forest-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-forest-700">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
