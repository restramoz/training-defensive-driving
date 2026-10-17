@extends('layouts.app')

@section('title', 'Login Administrator - Training Defensive Driving')
@section('meta_description', 'Masuk ke panel manajemen administrator Training Defensive Driving.')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[var(--color-background)]">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-[20px] border border-[var(--color-border)] shadow-sm">
        <div class="text-center space-y-2">
            <div class="inline-flex w-12 h-12 rounded-[14px] bg-[var(--color-primary)] text-white items-center justify-center font-heading font-bold text-2xl shadow-sm mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h1 class="font-heading font-bold text-2xl text-[var(--color-secondary)]">
                Admin Panel Login
            </h1>
            <p class="text-xs text-[var(--color-text-muted)] font-sans">
                Akses terbatas untuk administrator Training Defensive Driving
            </p>
        </div>

        @if(session('status'))
            <div class="p-3.5 rounded-[12px] bg-[var(--color-primary-soft)] border border-[#C2E4D8] text-xs text-[var(--color-primary)] font-medium">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 rounded-[12px] bg-red-50 border border-red-200 text-xs text-[var(--color-danger)] font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-xs font-heading font-semibold text-[var(--color-secondary)] mb-1">
                    Alamat Email Admin
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    value="{{ old('email') }}"
                    placeholder="admin@example.com"
                    class="w-full px-4 py-2.5 rounded-[12px] border border-[var(--color-border)] text-sm text-[var(--color-text)] bg-[var(--color-surface)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent transition"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-heading font-semibold text-[var(--color-secondary)] mb-1">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    placeholder="••••••••"
                    class="w-full px-4 py-2.5 rounded-[12px] border border-[var(--color-border)] text-sm text-[var(--color-text)] bg-[var(--color-surface)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent transition"
                >
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-[var(--color-text-muted)]">
                    <input
                        type="checkbox"
                        name="remember"
                        class="rounded border-[var(--color-border)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                    >
                    <span>Ingat saya</span>
                </label>
            </div>

            <div>
                <button
                    type="submit"
                    class="btn-primary w-full py-3 text-sm font-bold shadow-sm"
                >
                    Masuk ke Admin Panel
                </button>
            </div>
        </form>

        <div class="text-center pt-2 border-t border-[var(--color-border)]">
            <a href="{{ route('home') }}" class="text-xs text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
