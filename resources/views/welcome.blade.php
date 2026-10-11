<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SIPUS-Del | Sistem Informasi Perpustakaan Del</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-ivory text-charcoal">
        <main class="mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-8 sm:px-10 lg:px-16 lg:py-12">
            <header class="flex items-center justify-between border-b border-border-warm pb-6">
                <a href="{{ route('bukus.index') }}" class="flex items-center gap-3 font-display text-2xl font-semibold tracking-tight text-forest">
                    <img src="{{ asset('images/logo-itdel.png') }}" alt="Logo Institut Teknologi Del" class="h-12 w-12 rounded bg-white object-contain p-0.5">
                    <span>SIPUS-Del</span>
                </a>
                <span class="hidden text-xs font-semibold uppercase tracking-[0.2em] text-muted sm:block">Perpustakaan Del</span>
            </header>

            <section class="grid flex-1 items-center gap-12 py-16 lg:grid-cols-[1.15fr_0.85fr] lg:gap-20">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-brass">Sistem Informasi Perpustakaan Del</p>
                    <h1 class="mt-6 max-w-3xl font-display text-5xl font-semibold leading-[1.05] tracking-tight text-forest sm:text-6xl lg:text-7xl">Ruang tenang untuk menemukan pengetahuan.</h1>
                    <p class="mt-7 max-w-xl text-base leading-8 text-muted">Kelola koleksi buku dan kategori perpustakaan Del melalui katalog yang sederhana, teratur, dan mudah digunakan.</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('bukus.index') }}" class="inline-flex items-center justify-center rounded-md bg-forest px-5 py-3 text-sm font-semibold text-white hover:bg-forest/90">Lihat Koleksi Buku</a>
                        <a href="{{ route('kategoris.index') }}" class="inline-flex items-center justify-center rounded-md border border-border-warm px-5 py-3 text-sm font-semibold text-forest hover:bg-sand">Kelola Kategori</a>
                    </div>
                </div>

                <div class="border border-forest bg-forest p-8 text-white sm:p-10">
                    <div class="flex items-center justify-between border-b border-white/20 pb-6">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brass">Koleksi Del</span>
                        <svg class="h-8 w-8 text-white/75" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/><path d="M4 5.5v16M8 7h8M8 11h8"/></svg>
                    </div>
                    <p class="mt-10 font-display text-3xl leading-tight">Katalog buku yang tertata untuk sivitas akademika.</p>
                    <p class="mt-5 text-sm leading-7 text-white/65">SIPUS-Del membantu menjaga informasi koleksi tetap jelas dan mudah diakses.</p>
                    <div class="mt-12 h-px w-16 bg-brass"></div>
                </div>
            </section>

            <footer class="border-t border-border-warm pt-6 text-xs text-muted">
                SIPUS-Del · Sistem Informasi Perpustakaan Del
            </footer>
        </main>
    </body>
</html>
