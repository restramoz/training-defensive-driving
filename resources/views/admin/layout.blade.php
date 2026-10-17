<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Training Defensive Driving') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] font-sans antialiased min-h-screen flex flex-col">
    <!-- Admin Header / Navbar -->
    <header class="bg-white border-b border-[#E2E8F0] sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-[10px] bg-[var(--color-primary)] text-white flex items-center justify-center font-heading font-bold text-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </span>
                        <div>
                            <span class="font-heading font-bold text-base text-[var(--color-secondary)] tracking-tight block leading-none">
                                TRAINING DEFENSIVE DRIVING
                            </span>
                            <span class="text-[10px] font-sans uppercase tracking-wider text-[var(--color-primary)] font-semibold">
                                Admin Dashboard
                            </span>
                        </div>
                    </a>

                    <!-- Nav items -->
                    <nav class="hidden md:flex items-center space-x-1 pl-4 border-l border-[#E2E8F0]">
                        <a href="{{ route('admin.articles.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-heading font-semibold {{ request()->routeIs('admin.articles.index') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)]' : 'text-[#64748B] hover:text-[var(--color-secondary)] hover:bg-[#F1F5F9]' }} transition">
                            Semua Artikel
                        </a>
                        <a href="{{ route('admin.articles.create') }}" class="px-3 py-1.5 rounded-lg text-xs font-heading font-semibold {{ request()->routeIs('admin.articles.create') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)]' : 'text-[#64748B] hover:text-[var(--color-secondary)] hover:bg-[#F1F5F9]' }} transition">
                            + Tulis / AI Generator
                        </a>
                    </nav>
                </div>

                <!-- Right items: View site, User, Logout -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="text-xs text-[#64748B] hover:text-[var(--color-primary)] flex items-center gap-1 font-medium transition">
                        <span>Lihat Website</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>

                    <div class="h-4 w-px bg-[#E2E8F0]"></div>

                    <span class="text-xs font-medium text-[#475569] hidden sm:inline">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-heading font-semibold text-red-600 hover:bg-red-50 border border-red-200 transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-[14px] bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-[14px] bg-red-50 border border-red-200 text-xs font-medium text-red-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-[14px] bg-red-50 border border-red-200 text-xs text-red-800 space-y-2">
                <div class="flex items-center gap-2 font-heading font-semibold text-red-900">
                    <svg class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span>Terdapat beberapa kesalahan pengisian form:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-6 text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Admin Footer -->
    <footer class="bg-white border-t border-[#E2E8F0] py-4 text-center text-xs text-[#94A3B8]">
        Training Defensive Driving Administrator Panel &copy; {{ date('Y') }}
    </footer>

    @stack('scripts')
</body>
</html>
