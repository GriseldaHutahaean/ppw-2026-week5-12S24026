<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'SIPUS-Del' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-ivory text-charcoal">
        <aside class="hidden border-r border-border-warm bg-ivory lg:fixed lg:inset-y-0 lg:flex lg:w-72 lg:flex-col">
            <div class="flex h-full flex-col px-8 py-9">
                <a href="{{ route('bukus.index') }}" class="group">
                    <span class="text-xs font-semibold uppercase tracking-[0.24em] text-forest">Perpustakaan</span>
                    <span class="mt-2 block font-display text-3xl font-semibold tracking-tight text-charcoal">SIPUS-Del</span>
                    <span class="mt-2 block text-sm leading-6 text-charcoal/60">Sistem Informasi Perpustakaan Del</span>
                </a>

                <nav class="mt-16 space-y-2" aria-label="Navigasi utama">
                    <a href="{{ route('bukus.index') }}" class="flex items-center gap-3 border-l-2 px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('bukus.*') ? 'border-forest bg-forest/10 text-forest' : 'border-transparent text-charcoal/65 hover:border-forest/40 hover:bg-sand/30 hover:text-forest' }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/><path d="M4 5.5v16M8 7h8M8 11h8"/></svg>
                        <span>Buku</span>
                    </a>
                    <a href="{{ route('kategoris.index') }}" class="flex items-center gap-3 border-l-2 px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('kategoris.*') ? 'border-forest bg-forest/10 text-forest' : 'border-transparent text-charcoal/65 hover:border-forest/40 hover:bg-sand/30 hover:text-forest' }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/><path d="M4 5.5A2.5 2.5 0 0 1 6.5 8H20"/></svg>
                        <span>Kategori</span>
                    </a>
                </nav>

                <div class="mt-auto border-t border-border-warm pt-6 text-xs leading-5 text-charcoal/50">
                    Koleksi, pengetahuan, dan cerita untuk sivitas Del.
                </div>
            </div>
        </aside>

        <div class="lg:ml-72">
            <header class="border-b border-border-warm bg-ivory/95 lg:hidden">
                <div class="flex items-center justify-between px-5 py-5">
                    <a href="{{ route('bukus.index') }}" class="font-display text-2xl font-semibold text-charcoal">SIPUS-Del</a>
                    <nav class="flex gap-1" aria-label="Navigasi utama">
                        <a href="{{ route('bukus.index') }}" class="rounded-md px-3 py-2 text-sm font-semibold {{ request()->routeIs('bukus.*') ? 'bg-forest text-white' : 'text-charcoal/65 hover:bg-sand/40' }}">Buku</a>
                        <a href="{{ route('kategoris.index') }}" class="rounded-md px-3 py-2 text-sm font-semibold {{ request()->routeIs('kategoris.*') ? 'bg-forest text-white' : 'text-charcoal/65 hover:bg-sand/40' }}">Kategori</a>
                    </nav>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-5 py-10 sm:px-8 lg:px-12 lg:py-14">
            @if (session('success'))
                <div class="mb-8 border-l-4 border-forest bg-sand/35 px-4 py-3 text-sm text-charcoal" role="status">
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
