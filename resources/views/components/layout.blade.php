<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'SIPUS-Del' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-ivory text-charcoal">
        <aside class="hidden bg-forest text-white lg:fixed lg:inset-y-0 lg:flex lg:w-72 lg:flex-col">
            <div class="flex h-full flex-col px-7 py-8">
                <a href="{{ route('bukus.index') }}" class="group border-b border-white/15 pb-8">
                    <span class="text-xs font-semibold uppercase tracking-[0.24em] text-brass">Perpustakaan Del</span>
                    <span class="mt-3 block font-display text-3xl font-semibold tracking-tight text-white">SIPUS-Del</span>
                    <span class="mt-3 block max-w-52 text-sm leading-6 text-white/65">Sistem Informasi Perpustakaan Del</span>
                </a>

                <nav class="mt-10 space-y-2" aria-label="Navigasi utama">
                    <a href="{{ route('bukus.index') }}" @class([
                        'flex items-center gap-3 border-l-2 px-4 py-3 text-sm font-semibold',
                        'border-brass bg-white/10 text-white' => request()->routeIs('bukus.*'),
                        'border-transparent text-white/70 hover:border-white/40 hover:bg-white/10 hover:text-white' => ! request()->routeIs('bukus.*'),
                    ]) @if (request()->routeIs('bukus.*')) aria-current="page" @endif>
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/><path d="M4 5.5v16M8 7h8M8 11h8"/></svg>
                        <span>Buku</span>
                    </a>
                    <a href="{{ route('kategoris.index') }}" @class([
                        'flex items-center gap-3 border-l-2 px-4 py-3 text-sm font-semibold',
                        'border-brass bg-white/10 text-white' => request()->routeIs('kategoris.*'),
                        'border-transparent text-white/70 hover:border-white/40 hover:bg-white/10 hover:text-white' => ! request()->routeIs('kategoris.*'),
                    ]) @if (request()->routeIs('kategoris.*')) aria-current="page" @endif>
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/><path d="M4 5.5A2.5 2.5 0 0 1 6.5 8H20"/></svg>
                        <span>Kategori</span>
                    </a>
                </nav>

                <div class="mt-auto border-t border-white/15 pt-6 text-xs leading-5 text-white/55">
                    Koleksi, pengetahuan, dan cerita untuk sivitas Del.
                </div>
            </div>
        </aside>

        <div class="lg:ml-72">
            <header class="border-b border-forest/15 bg-forest text-white lg:hidden">
                <div class="flex items-center justify-between gap-4 px-5 py-4">
                    <a href="{{ route('bukus.index') }}" class="font-display text-2xl font-semibold text-white">SIPUS-Del</a>
                    <nav class="flex gap-1" aria-label="Navigasi utama">
                        <a href="{{ route('bukus.index') }}" @class([
                            'rounded-md px-3 py-2 text-sm font-semibold',
                            'bg-white/15 text-white' => request()->routeIs('bukus.*'),
                            'text-white/70 hover:bg-white/10 hover:text-white' => ! request()->routeIs('bukus.*'),
                        ])>Buku</a>
                        <a href="{{ route('kategoris.index') }}" @class([
                            'rounded-md px-3 py-2 text-sm font-semibold',
                            'bg-white/15 text-white' => request()->routeIs('kategoris.*'),
                            'text-white/70 hover:bg-white/10 hover:text-white' => ! request()->routeIs('kategoris.*'),
                        ])>Kategori</a>
                    </nav>
                </div>
            </header>

            <main class="mx-auto max-w-[1500px] px-5 py-8 sm:px-8 lg:px-12 lg:py-12">
                @if (session('success'))
                    <div class="mb-8 border-l-4 border-forest bg-[#E7EFEA] px-4 py-3 text-sm text-forest" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-8 border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
