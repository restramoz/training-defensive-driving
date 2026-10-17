<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('favico.png') }}">
    <title>
        @hasSection('title')
            @yield('title') - {{ config('app.name', 'Training Defensive Driving') }}
        @else
            {{ config('app.name', 'Training Defensive Driving') }} - Pelatihan Keselamatan Berkendara Nasional
        @endif
    </title>
    <meta name="description" content="@yield('meta_description', 'Pelatihan Defensive Driving untuk individu dan perusahaan. Membangun kebiasaan berkendara yang sadar, antisipatif, terukur, dan bertanggung jawab.')">
    
    @hasSection('canonical')
    <link rel="canonical" href="@yield('canonical')">
    @endif

    <meta property="og:site_name" content="{{ config('app.name', 'Training Defensive Driving') }}">
    <meta property="og:title" content="@yield('title', config('app.name', 'Training Defensive Driving'))">
    <meta property="og:description" content="@yield('meta_description', 'Pelatihan Defensive Driving untuk individu dan perusahaan di Indonesia.')">
    <meta property="og:type" content="website">

    <!-- Preconnect & Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('schema')
</head>
<body class="bg-[var(--color-background)] text-[var(--color-text)] font-sans antialiased min-h-screen flex flex-col selection:bg-[var(--color-primary-soft)] selection:text-[var(--color-primary)]">
    <!-- Accessible Skip Link -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-[var(--color-primary)] focus:text-white focus:rounded-[12px] focus:shadow-md">
        Menuju ke konten utama
    </a>

    <!-- Top Navigation Header -->
    <header class="bg-[var(--color-surface)] border-b border-[var(--color-border)] sticky top-0 z-40 backdrop-blur-md bg-opacity-95" aria-label="Header Navigasi Utama">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Brand Identity -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-primary)] rounded-lg p-1" aria-label="Beranda Training Defensive Driving">
                    <span class="w-10 h-10 rounded-[12px] bg-[var(--color-secondary)] text-white flex items-center justify-center shadow-xs group-hover:bg-[var(--color-primary)] transition-colors">
                        <svg class="w-5 h-5 text-[var(--color-electric)] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </span>
                    <div class="flex flex-col text-left">
                        <span class="font-heading font-bold text-lg sm:text-xl tracking-tight text-[var(--color-secondary)] group-hover:text-[var(--color-primary)] transition-colors">
                            Training Defensive Driving
                        </span>
                        <span class="text-[11px] font-sans font-medium uppercase tracking-wider text-[var(--color-text-muted)]">
                            Edukasi &amp; Budaya Keselamatan Berkendara
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center space-x-6" aria-label="Navigasi Menu">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors">Beranda</a>
                    <a href="{{ route('home') }}#program" class="text-sm font-medium text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors">Program</a>
                    <a href="{{ route('home') }}#individu" class="text-sm font-medium text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors">Untuk Individu</a>
                    <a href="{{ route('home') }}#perusahaan" class="text-sm font-medium text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors">Untuk Perusahaan</a>
                    <a href="{{ route('home') }}#tentang" class="text-sm font-medium text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors">Tentang Pelatihan</a>
                    <a href="{{ route('home') }}#wilayah" class="text-sm font-medium text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors">Wilayah</a>
                    <a href="{{ route('home') }}#faq" class="text-sm font-medium text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors">FAQ</a>
                    <a href="{{ route('articles.index') }}" class="text-sm font-medium text-[var(--color-text)] hover:text-[var(--color-primary)] transition-colors">Artikel</a>
                </nav>

                <!-- Desktop CTA -->
                <div class="hidden lg:flex items-center gap-3">
                    <a href="{{ route('home') }}#konsultasi" class="btn-primary text-sm py-2.5 px-5">
                        Konsultasi Pelatihan
                    </a>
                </div>

                <!-- Mobile Menu Toggle Button -->
                <button id="mobile-menu-btn" type="button" class="lg:hidden p-2 rounded-lg text-[var(--color-secondary)] hover:bg-[var(--color-surface-muted)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" aria-expanded="false" aria-controls="mobile-menu" aria-label="Buka menu navigasi">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path id="menu-icon-bars" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path id="menu-icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Navigation -->
        <nav id="mobile-menu" class="hidden lg:hidden border-t border-[var(--color-border)] bg-[var(--color-surface)] px-4 pt-3 pb-6 space-y-2 shadow-lg" aria-label="Navigasi Menu Mobile">
            <a href="{{ route('home') }}" class="block py-2.5 px-3 rounded-lg text-base font-medium text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-primary)]">Beranda</a>
            <a href="{{ route('home') }}#program" class="block py-2.5 px-3 rounded-lg text-base font-medium text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-primary)]">Prinsip &amp; Program</a>
            <a href="{{ route('home') }}#individu" class="block py-2.5 px-3 rounded-lg text-base font-medium text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-primary)]">Untuk Individu</a>
            <a href="{{ route('home') }}#perusahaan" class="block py-2.5 px-3 rounded-lg text-base font-medium text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-primary)]">Untuk Perusahaan</a>
            <a href="{{ route('home') }}#alur" class="block py-2.5 px-3 rounded-lg text-base font-medium text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-primary)]">Alur Pelatihan</a>
            <a href="{{ route('home') }}#tentang" class="block py-2.5 px-3 rounded-lg text-base font-medium text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-primary)]">Tentang Pelatihan</a>
            <a href="{{ route('home') }}#wilayah" class="block py-2.5 px-3 rounded-lg text-base font-medium text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-primary)]">Cakupan Wilayah</a>
            <a href="{{ route('home') }}#faq" class="block py-2.5 px-3 rounded-lg text-base font-medium text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-primary)]">FAQ</a>
            <a href="{{ route('articles.index') }}" class="block py-2.5 px-3 rounded-lg text-base font-medium text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-primary)]">Artikel &amp; Panduan</a>
            <div class="pt-3 border-t border-[var(--color-border)]">
                <a href="{{ route('home') }}#konsultasi" class="btn-primary w-full text-center py-3 text-sm">
                    Konsultasi Pelatihan
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Content Landmark -->
    <main id="main-content" class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[var(--color-midnight-navy)] text-[#E2E8F0] border-t border-[var(--color-deep-navy)] pt-14 pb-8" aria-label="Footer Informasi">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-12 border-b border-[#1E293B]">
                <!-- Brand Info -->
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-[10px] bg-[var(--color-primary)] text-white flex items-center justify-center font-heading font-bold text-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                        <span class="font-heading font-bold text-xl text-white tracking-tight">
                            Training Defensive Driving
                        </span>
                    </div>
                    <p class="text-sm text-[#94A3B8] leading-relaxed max-w-sm font-sans">
                        Penyedia program edukasi dan pelatihan Defensive Driving untuk individu dan armada operasional perusahaan dalam membangun kebiasaan berkendara yang aman, terukur, dan bertanggung jawab di seluruh Indonesia.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-[#94A3B8]">
                        <span class="inline-block w-2 h-2 rounded-full bg-[var(--color-electric)]"></span>
                        <span>Konsultasi Program Pengemudi Individu &amp; Korporasi</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="md:col-span-3 space-y-3">
                    <h3 class="font-heading font-semibold text-sm uppercase tracking-wider text-white">Navigasi Utama</h3>
                    <ul class="space-y-2 text-sm text-[#94A3B8]">
                        <li><a href="{{ route('home') }}#program" class="hover:text-white transition-colors">Prinsip Defensive Driving</a></li>
                        <li><a href="{{ route('home') }}#individu" class="hover:text-white transition-colors">Program Individu</a></li>
                        <li><a href="{{ route('home') }}#perusahaan" class="hover:text-white transition-colors">Program Perusahaan &amp; Armada</a></li>
                        <li><a href="{{ route('home') }}#alur" class="hover:text-white transition-colors">Alur Pelatihan</a></li>
                        <li><a href="{{ route('home') }}#tentang" class="hover:text-white transition-colors">Tentang Pelatihan</a></li>
                        <li><a href="{{ route('home') }}#wilayah" class="hover:text-white transition-colors">Cakupan Wilayah</a></li>
                        <li><a href="{{ route('home') }}#faq" class="hover:text-white transition-colors">Pertanyaan Umum (FAQ)</a></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-white transition-colors">Artikel Edukasi</a></li>
                    </ul>
                </div>

                <!-- Consultation Information -->
                <div class="md:col-span-4 space-y-3">
                    <h3 class="font-heading font-semibold text-sm uppercase tracking-wider text-white">Konsultasi Pelatihan</h3>
                    <p class="text-xs text-[#94A3B8] leading-relaxed">
                        Diskusikan kebutuhan pelatihan keselamatan berkendara untuk diri sendiri atau pengemudi armada perusahaan Anda bersama tim koordinasi kami.
                    </p>
                    <div class="p-3.5 bg-[#111C2E] rounded-[12px] border border-[#1E293B] text-xs space-y-1.5">
                        <span class="font-heading font-semibold text-[var(--color-electric)] block">Layanan Konsultasi:</span>
                        <span class="text-[#94A3B8] block">Melayani koordinasi program di berbagai wilayah Indonesia</span>
                        <span class="text-[#94A3B8] block">Format in-house dan pembahasan kurikulum disesuaikan dengan kebutuhan</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Disclaimer & Copyright -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#94A3B8]">
                <p>© {{ date('Y') }} {{ config('app.name', 'Training Defensive Driving') }}. Hak Cipta Dilindungi.</p>
                <p class="text-center sm:text-right">Membangun kesadaran berkendara aman, antisipatif, dan bertanggung jawab.</p>
            </div>
        </div>
    </footer>

    <!-- Interactive script for mobile menu -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const bars = document.getElementById('menu-icon-bars');
            const close = document.getElementById('menu-icon-close');

            if (btn && menu) {
                btn.addEventListener('click', function () {
                    const isExpanded = btn.getAttribute('aria-expanded') === 'true';
                    btn.setAttribute('aria-expanded', !isExpanded);
                    menu.classList.toggle('hidden');
                    if (bars && close) {
                        bars.classList.toggle('hidden');
                        close.classList.toggle('hidden');
                    }
                });

                // Close mobile menu when a link is clicked
                menu.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        menu.classList.add('hidden');
                        btn.setAttribute('aria-expanded', 'false');
                        if (bars && close) {
                            bars.classList.remove('hidden');
                            close.classList.add('hidden');
                        }
                    });
                });
            }
        });
    </script>

    <!-- Floating WhatsApp Button (Active only when configured) -->
    <x-wa-float />
</body>
</html>
