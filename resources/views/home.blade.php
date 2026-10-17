@extends('layouts.app')

@section('title', 'Training Defensive Driving - Edukasi & Budaya Keselamatan Berkendara')
@section('meta_description', 'Program pelatihan Defensive Driving untuk individu dan perusahaan. Membangun kebiasaan berkendara yang sadar, antisipatif, terukur, dan bertanggung jawab di seluruh Indonesia.')

@php
    $waService = app(\App\Services\WhatsAppService::class);
    $isWaEnabled = $waService->isEnabled();
    $waIndividualUrl = $waService->getIndividualUrl();
    $waCorporateUrl = $waService->getCorporateUrl();
@endphp

@section('content')

    <!-- Section B: Hero Section (Midnight Navy & Automotive Editorial Aesthetic) -->
    <section class="py-16 lg:py-24 bg-gradient-to-b from-[var(--color-midnight-navy)] via-[#0e1726] to-[var(--color-deep-navy)] text-white border-b border-[#1E293B]" aria-labelledby="hero-title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6 text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[rgba(37,99,235,0.15)] border border-[rgba(96,165,250,0.3)] text-[var(--color-electric)] text-xs font-heading font-semibold tracking-wide uppercase">
                        <span class="w-2 h-2 rounded-full bg-[var(--color-electric)]"></span>
                        Program Edukasi Keselamatan Berkendara Nasional
                    </div>

                    <h1 id="hero-title" class="font-heading font-bold text-3xl sm:text-5xl lg:text-6xl text-white leading-[1.15] tracking-tight">
                        Berkendara Lebih Aman Dimulai dari Keputusan yang Tepat.
                    </h1>

                    <p class="text-base sm:text-lg text-[#94A3B8] leading-relaxed max-w-2xl font-sans">
                        Pelatihan Defensive Driving untuk individu dan perusahaan yang ingin membangun kebiasaan berkendara lebih sadar, terukur, dan bertanggung jawab. Fokus pada kesadaran situasional, antisipasi bahaya di jalan raya, dan pengelolaan risiko perjalanan.
                    </p>

                    <!-- Dual Primary CTA Paths -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-2">
                        <a href="#individu" class="btn-dark-primary text-base py-3.5 px-6 shadow-md text-center">
                            Saya Pengemudi Individu
                        </a>
                        <a href="#perusahaan" class="btn-dark-secondary text-base py-3.5 px-6 text-center">
                            Saya Mewakili Perusahaan
                        </a>
                    </div>

                    <!-- Honest Core Attributes Without Fabrication -->
                    <div class="pt-8 border-t border-[#1E293B] grid grid-cols-3 gap-6">
                        <div>
                            <span class="block font-heading font-bold text-xl sm:text-2xl text-[var(--color-electric)]">Antisipatif</span>
                            <span class="text-xs text-[#94A3B8] font-medium">Observasi Bahaya Dini</span>
                        </div>
                        <div>
                            <span class="block font-heading font-bold text-xl sm:text-2xl text-white">Terukur</span>
                            <span class="text-xs text-[#94A3B8] font-medium">Jarak &amp; Kecepatan Aman</span>
                        </div>
                        <div>
                            <span class="block font-heading font-bold text-xl sm:text-2xl text-[var(--color-accent)]">Nasional</span>
                            <span class="text-xs text-[#94A3B8] font-medium">Jangkauan Seluruh Wilayah</span>
                        </div>
                    </div>
                </div>

                <!-- Hero Visual Graphic Card (Automotive Focus) -->
                <div class="lg:col-span-5">
                    <div class="card-dark p-6 sm:p-8 bg-[#111C2E] border border-[rgba(255,255,255,0.08)] shadow-xl space-y-6">
                        <div class="flex items-center justify-between border-b border-[#1E293B] pb-4">
                            <span class="font-heading font-bold text-xs uppercase tracking-wider text-[#E2E8F0]">
                                SIKLUS DEFENSIVE DRIVING
                            </span>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-heading font-semibold bg-[rgba(245,184,46,0.15)] text-[var(--color-accent)] border border-[rgba(245,184,46,0.3)]">
                                Kesadaran Aktif
                            </span>
                        </div>

                        <div class="space-y-4 text-xs font-sans">
                            <div class="p-3.5 rounded-[12px] bg-[#0B1220] border border-[#1E293B] flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-xs shrink-0">1</span>
                                <div>
                                    <strong class="font-heading text-sm text-white block mb-0.5">Pemindaian Situasional (Scan)</strong>
                                    <p class="text-[#94A3B8]">Memantau kondisi jalan raya 360 derajat, spion berkala, blind spot, dan pergerakan pengguna jalan lain.</p>
                                </div>
                            </div>
                            <div class="p-3.5 rounded-[12px] bg-[#0B1220] border border-[#1E293B] flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-[var(--color-accent)] text-[#0B1220] flex items-center justify-center font-bold text-xs shrink-0">2</span>
                                <div>
                                    <strong class="font-heading text-sm text-white block mb-0.5">Antisipasi Bahaya (Anticipate)</strong>
                                    <p class="text-[#94A3B8]">Mendeteksi potensi kesalahan pengemudi lain, perubahan cuaca mendadak, serta kondisi jalan licin atau berlubang.</p>
                                </div>
                            </div>
                            <div class="p-3.5 rounded-[12px] bg-[#0B1220] border border-[#1E293B] flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-xs shrink-0">3</span>
                                <div>
                                    <strong class="font-heading text-sm text-white block mb-0.5">Keputusan Bertanggung Jawab (Decide)</strong>
                                    <p class="text-[#94A3B8]">Menjaga jarak aman minimal 3 detik, menyesuaikan kecepatan, dan memilih ruang pelarian darurat.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-[12px] bg-[#0B1220] border border-[#1E293B] text-xs text-[#94A3B8] flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-[var(--color-electric)] shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Dirancang untuk pengemudi harian maupun pengemudi profesional perusahaan.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section C: Core Principles Section -->
    <section id="program" class="py-16 sm:py-20 bg-[var(--color-surface)] border-b border-[var(--color-border)]" aria-labelledby="program-title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
                <span class="text-xs font-heading font-bold uppercase tracking-wider text-[var(--color-primary)] block">TEMA EDUKASI UMUM</span>
                <h2 id="program-title" class="font-heading font-bold text-3xl sm:text-4xl text-[var(--color-secondary)]">
                    Prinsip Utama Defensive Driving
                </h2>
                <p class="text-base text-[var(--color-text-muted)]">
                    Pembelajaran berfokus pada pencegahan proaktif dan pembentukan disiplin berkendara untuk meminimalkan risiko di berbagai situasi jalan raya.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Principle 1 -->
                <div class="card-premium p-6 space-y-4">
                    <div class="w-12 h-12 rounded-[12px] bg-[var(--color-primary-soft)] text-[var(--color-primary)] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-[var(--color-secondary)]">Kesadaran Situasional</h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Mengembangkan kebiasaan memantau lingkungan sekitar secara berkelanjutan, mengenali titik buta (blind spot), dan memahami dinamika lalu lintas di depan maupun belakang kendaraan.
                    </p>
                </div>

                <!-- Principle 2 -->
                <div class="card-premium p-6 space-y-4">
                    <div class="w-12 h-12 rounded-[12px] bg-[var(--color-primary-soft)] text-[var(--color-primary)] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-[var(--color-secondary)]">Antisipasi Potensi Bahaya</h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Mengenali tanda-tanda awal bahaya, seperti manuver kendaraan lain yang tidak menentu, penyeberang jalan mendadak, serta kondisi permukaan jalan yang rawan insiden.
                    </p>
                </div>

                <!-- Principle 3 -->
                <div class="card-premium p-6 space-y-4">
                    <div class="w-12 h-12 rounded-[12px] bg-[var(--color-primary-soft)] text-[var(--color-primary)] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-[var(--color-secondary)]">Manajemen Jarak Aman</h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Menerapkan aturan jarak waktu aman (seperti aturan 3 detik) untuk memberikan ruang reaksi yang cukup bagi pengemudi saat kendaraan di depan melakukan pengereman mendadak.
                    </p>
                </div>

                <!-- Principle 4 -->
                <div class="card-premium p-6 space-y-4">
                    <div class="w-12 h-12 rounded-[12px] bg-[var(--color-primary-soft)] text-[var(--color-primary)] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-[var(--color-secondary)]">Penyesuaian Cuaca &amp; Jalan</h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Menyesuaikan kecepatan dan teknik berkendara saat hujan lebat, jalan basah, kondisi berkabut, atau jalur bergelombang demi menjaga traksi dan stabilitas kendaraan.
                    </p>
                </div>

                <!-- Principle 5 -->
                <div class="card-premium p-6 space-y-4">
                    <div class="w-12 h-12 rounded-[12px] bg-[var(--color-primary-soft)] text-[var(--color-primary)] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-[var(--color-secondary)]">Kelelahan &amp; Distraksi</h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Memahami batas fisik tubuh pengemudi, pentingnya manajemen waktu istirahat dalam perjalanan panjang, serta disiplin menghindari penggunaan gawai saat menyetir.
                    </p>
                </div>

                <!-- Principle 6 -->
                <div class="card-premium p-6 space-y-4">
                    <div class="w-12 h-12 rounded-[12px] bg-[var(--color-primary-soft)] text-[var(--color-primary)] flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-[var(--color-secondary)]">Keputusan Bertanggung Jawab</h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Menempatkan keselamatan di atas ego berkendara, menghormati hak sesama pengguna jalan, dan selalu memilih tindakan berkendara yang tenang dan terencana.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section D: Individual Driver Pathway -->
    <section id="individu" class="py-16 sm:py-20 bg-[var(--color-surface-muted)] border-b border-[var(--color-border)]" aria-labelledby="individu-title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <span class="text-xs font-heading font-bold uppercase tracking-wider text-[var(--color-primary)] block">PROGRAM UNTUK PENGEMUDI INDIVIDU</span>
                    <h2 id="individu-title" class="font-heading font-bold text-3xl sm:text-4xl text-[var(--color-secondary)] leading-tight">
                        Bangun Ketenangan dan Kendali Penuh di Setiap Perjalanan
                    </h2>
                    <p class="text-base text-[var(--color-text-muted)] leading-relaxed">
                        Program edukasi Defensive Driving untuk pengemudi individu dirancang bagi siapa saja yang ingin memperdalam pemahaman mengenai risiko jalan raya, meningkatkan kepekaan antisipasi bahaya, dan membentuk pola berkendara harian yang lebih aman untuk diri sendiri serta keluarga.
                    </p>

                    <div class="space-y-3.5 text-sm">
                        <div class="flex items-start gap-3 p-3.5 rounded-[12px] bg-white border border-[var(--color-border)]">
                            <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] shrink-0 mt-2"></span>
                            <span class="text-[var(--color-text)]">Pengemudi yang ingin meningkatkan kesadaran situasional di lalu lintas perkotaan maupun jalan tol.</span>
                        </div>
                        <div class="flex items-start gap-3 p-3.5 rounded-[12px] bg-white border border-[var(--color-border)]">
                            <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] shrink-0 mt-2"></span>
                            <span class="text-[var(--color-text)]">Pengemudi pemula atau yang ingin menyegarkan pemahaman seputar pengelolaan jarak aman dan reaksi darurat.</span>
                        </div>
                        <div class="flex items-start gap-3 p-3.5 rounded-[12px] bg-white border border-[var(--color-border)]">
                            <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] shrink-0 mt-2"></span>
                            <span class="text-[var(--color-text)]">Peserta yang ingin membangun kebiasaan berkendara hemat emosi, disiplin, dan bertanggung jawab.</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="#konsultasi" class="btn-primary text-sm py-3 px-6 shadow-sm">
                            Tanyakan Program untuk Individu
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="card-premium p-6 sm:p-8 bg-white space-y-5 border border-[var(--color-border)]">
                        <span class="text-xs font-heading font-bold uppercase tracking-wider text-[var(--color-secondary)] block border-b border-[var(--color-border)] pb-3">
                            ALUR KONSULTASI INDIVIDU
                        </span>
                        <div class="space-y-3 text-xs text-[var(--color-text-muted)] leading-relaxed">
                            <p>1. Hubungi tim kami untuk menyampaikan kebutuhan atau minat Anda terhadap materi Defensive Driving.</p>
                            <p>2. Dapatkan informasi mengenai ketersediaan jadwal, format sesi pemaparan, dan materi edukasi yang sesuai.</p>
                            <p>3. Konfirmasi pendaftaran dan ikuti panduan koordinasi yang disediakan oleh penyelenggara.</p>
                        </div>
                        <div class="p-3.5 rounded-[10px] bg-[var(--color-primary-soft)] text-xs text-[var(--color-primary)] font-medium">
                            Rincian jadwal dan ketentuan pelaksanaan dapat dikonfirmasikan langsung bersama tim penyelenggara.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section E: Corporate and Fleet Pathway -->
    <section id="perusahaan" class="py-16 sm:py-20 bg-[var(--color-surface)] border-b border-[var(--color-border)]" aria-labelledby="perusahaan-title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-5 order-2 lg:order-1">
                    <div class="card-premium p-6 sm:p-8 bg-[var(--color-surface-muted)] space-y-5 border border-[var(--color-border)]">
                        <span class="text-xs font-heading font-bold uppercase tracking-wider text-[var(--color-secondary)] block border-b border-[var(--color-border)] pb-3">
                            FOKUS PROGRAM KORPORASI &amp; ARMADA
                        </span>
                        <div class="space-y-3 text-xs text-[var(--color-text-muted)] leading-relaxed">
                            <div class="p-3 bg-white rounded-[10px] border border-[var(--color-border)]">
                                <strong class="font-heading font-semibold text-[var(--color-secondary)] block mb-1">Pengemudi Operasional &amp; Kantor</strong>
                                <p>Fokus pada etika berkendara profesional, kepatuhan batas kecepatan, dan mitigasi stres perjalanan dinas.</p>
                            </div>
                            <div class="p-3 bg-white rounded-[10px] border border-[var(--color-border)]">
                                <strong class="font-heading font-semibold text-[var(--color-secondary)] block mb-1">Armada Niaga, Logistik, &amp; Distribusi</strong>
                                <p>Pemeriksaan pra-perjalanan kendaraan, antisipasi muatan, rute antar-kota, dan manajemen kelelahan pengemudi.</p>
                            </div>
                            <div class="p-3 bg-white rounded-[10px] border border-[var(--color-border)]">
                                <strong class="font-heading font-semibold text-[var(--color-secondary)] block mb-1">Budaya Keselamatan Organisasi</strong>
                                <p>Membangun komitmen bersama antara manajemen dan pengemudi dalam menjaga standar keselamatan berkendara.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 space-y-6 order-1 lg:order-2">
                    <span class="text-xs font-heading font-bold uppercase tracking-wider text-[var(--color-primary)] block">PROGRAM PERUSAHAAN &amp; FLEET OPERATOR</span>
                    <h2 id="perusahaan-title" class="font-heading font-bold text-3xl sm:text-4xl text-[var(--color-secondary)] leading-tight">
                        Tingkatkan Budaya Keselamatan Berkendara untuk Pengemudi &amp; Armada Kerja
                    </h2>
                    <p class="text-base text-[var(--color-text-muted)] leading-relaxed">
                        Bagi perusahaan, keselamatan operasional di jalan raya adalah investasi penting yang melindungi aset, kelancaran bisnis, dan keselamatan personil. Program Defensive Driving untuk organisasi dirancang untuk mendiskusikan risiko operasional spesifik yang dihadapi tim pengemudi Anda di lapangan.
                    </p>

                    <div class="space-y-3.5 text-sm">
                        <div class="flex items-start gap-3 p-3.5 rounded-[12px] bg-[var(--color-surface-muted)] border border-[var(--color-border)]">
                            <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] shrink-0 mt-2"></span>
                            <span class="text-[var(--color-text)]">Penyelarasan topik dengan karakteristik armada (kendaraan operasional ringan, MPV, van, atau truk distribusi).</span>
                        </div>
                        <div class="flex items-start gap-3 p-3.5 rounded-[12px] bg-[var(--color-surface-muted)] border border-[var(--color-border)]">
                            <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] shrink-0 mt-2"></span>
                            <span class="text-[var(--color-text)]">Diskusi interaktif studi kasus lalu lintas dan telaah potensi risiko rute perjalanan harian perusahaan.</span>
                        </div>
                        <div class="flex items-start gap-3 p-3.5 rounded-[12px] bg-[var(--color-surface-muted)] border border-[var(--color-border)]">
                            <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] shrink-0 mt-2"></span>
                            <span class="text-[var(--color-text)]">Peluang penyesuaian materi dan format pelaksanaan berdasarkan kesepakatan bersama dengan pihak penyelenggara.</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="#konsultasi" class="btn-primary text-sm py-3 px-6 shadow-sm">
                            Diskusikan Kebutuhan Perusahaan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section F: Training Approach (Illustrative Journey) -->
    <section id="alur" class="py-16 sm:py-20 bg-[var(--color-surface-muted)] border-b border-[var(--color-border)]" aria-labelledby="alur-title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
                <span class="text-xs font-heading font-bold uppercase tracking-wider text-[var(--color-primary)] block">TAHAPAN KOORDINASI</span>
                <h2 id="alur-title" class="font-heading font-bold text-3xl sm:text-4xl text-[var(--color-secondary)]">
                    Alur Konsultasi &amp; Pelaksanaan Pelatihan
                </h2>
                <p class="text-base text-[var(--color-text-muted)]">
                    Gambaran proses koordinasi dari konsultasi awal hingga penyelenggaraan program pelatihan yang disepakati.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="p-6 rounded-[16px] bg-white border border-[var(--color-border)] space-y-3 relative">
                    <span class="w-9 h-9 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-heading font-bold text-sm mb-2">1</span>
                    <h3 class="font-heading font-bold text-lg text-[var(--color-secondary)]">Konsultasi Awal</h3>
                    <p class="text-xs text-[var(--color-text-muted)] leading-relaxed">
                        Sampaikan kebutuhan pelatihan Anda, baik untuk individu maupun estimasi jumlah pengemudi armada perusahaan.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-6 rounded-[16px] bg-white border border-[var(--color-border)] space-y-3 relative">
                    <span class="w-9 h-9 rounded-full bg-[var(--color-secondary)] text-white flex items-center justify-center font-heading font-bold text-sm mb-2">2</span>
                    <h3 class="font-heading font-bold text-lg text-[var(--color-secondary)]">Diskusi Kebutuhan</h3>
                    <p class="text-xs text-[var(--color-text-muted)] leading-relaxed">
                        Pembahasan konteks operasional, profil kendaraan, medan jalan yang dilalui, serta penekanan materi yang diperlukan.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-6 rounded-[16px] bg-white border border-[var(--color-border)] space-y-3 relative">
                    <span class="w-9 h-9 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-heading font-bold text-sm mb-2">3</span>
                    <h3 class="font-heading font-bold text-lg text-[var(--color-secondary)]">Konfirmasi Program</h3>
                    <p class="text-xs text-[var(--color-text-muted)] leading-relaxed">
                        Penetapan lingkup materi, jadwal pelaksanaan, lokasi atau media sesi, serta rincian teknis yang disetujui bersama.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="p-6 rounded-[16px] bg-white border border-[var(--color-border)] space-y-3 relative">
                    <span class="w-9 h-9 rounded-full bg-[var(--color-secondary)] text-white flex items-center justify-center font-heading font-bold text-sm mb-2">4</span>
                    <h3 class="font-heading font-bold text-lg text-[var(--color-secondary)]">Pelaksanaan Pelatihan</h3>
                    <p class="text-xs text-[var(--color-text-muted)] leading-relaxed">
                        Penyampaian materi edukasi, pembahasan studi kasus nyata, dan evaluasi pemahaman prinsip berkendara aman.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section G: About and Credibility -->
    <section id="tentang" class="py-16 sm:py-20 bg-[var(--color-surface)] border-b border-[var(--color-border)]" aria-labelledby="tentang-title">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <span class="text-xs font-heading font-bold uppercase tracking-wider text-[var(--color-primary)] block">TENTANG PELATIHAN DEFENSIVE DRIVING</span>
            <h2 id="tentang-title" class="font-heading font-bold text-3xl sm:text-4xl text-[var(--color-secondary)]">
                Komitmen untuk Keselamatan Jalan Raya yang Lebih Baik
            </h2>
            <p class="text-base text-[var(--color-text-muted)] leading-relaxed">
                Training Defensive Driving hadir sebagai wadah edukasi dan pengembangan kompetensi berkendara bagi masyarakat Indonesia. Kami meyakini bahwa keselamatan di jalan berawal dari kesadaran individu dalam mengambil keputusan berkendara yang tenang, antisipatif, dan menghargai keselamatan sesama.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 text-left">
                <div class="card-premium p-5 space-y-2">
                    <h3 class="font-heading font-bold text-base text-[var(--color-secondary)]">Fokus Edukatif</h3>
                    <p class="text-xs text-[var(--color-text-muted)] leading-relaxed">
                        Menitikberatkan pada pemahaman konsep pencegahan risiko dan studi kasus praktis di jalan raya Indonesia.
                    </p>
                </div>
                <div class="card-premium p-5 space-y-2">
                    <h3 class="font-heading font-bold text-base text-[var(--color-secondary)]">Relevansi Nyata</h3>
                    <p class="text-xs text-[var(--color-text-muted)] leading-relaxed">
                        Materi yang dapat langsung diterapkan dalam rutinitas perjalanan harian maupun tugas operasional kerja.
                    </p>
                </div>
                <div class="card-premium p-5 space-y-2">
                    <h3 class="font-heading font-bold text-base text-[var(--color-secondary)]">Kemitraan Terbuka</h3>
                    <p class="text-xs text-[var(--color-text-muted)] leading-relaxed">
                        Membuka ruang diskusi yang fleksibel bagi perusahaan untuk merancang sesi pembelajaran armada yang tepat guna.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section I: Dedicated Regional Directory Section (Critical Addendum) -->
    <section id="wilayah" class="py-16 sm:py-20 bg-[var(--color-surface-muted)] border-b border-[var(--color-border)]" aria-labelledby="wilayah-title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-xs font-heading font-bold uppercase tracking-wider text-[var(--color-primary)] block mb-2">CAKUPAN WILAYAH NASIONAL</span>
                    <h2 id="wilayah-title" class="font-heading font-bold text-3xl sm:text-4xl text-[var(--color-secondary)]">
                        Jelajahi Informasi Training Defensive Driving Berdasarkan Wilayah
                    </h2>
                    <p class="text-sm text-[var(--color-text-muted)] mt-1 max-w-2xl">
                        Informasi program dan koordinasi pelatihan Defensive Driving untuk berbagai kota dan kabupaten di seluruh wilayah Indonesia.
                    </p>
                </div>
                <div class="text-xs text-[var(--color-text-muted)] bg-white px-3 py-2 rounded-lg border border-[var(--color-border)] shrink-0">
                    514 Kota &amp; Kabupaten Terdata
                </div>
            </div>

            <!-- Client-side Fast Search Input -->
            <div class="mb-8">
                <div class="relative max-w-md">
                    <input
                        type="text"
                        id="city-search-input"
                        placeholder="Cari nama kota atau kabupaten..."
                        class="w-full pl-10 pr-10 py-2.5 text-sm bg-white border border-[var(--color-border)] rounded-[12px] focus:outline-none focus:border-[var(--color-primary)] focus:ring-1 focus:ring-[var(--color-primary)]"
                        aria-label="Cari kota atau kabupaten"
                        autocomplete="off"
                    >
                    <svg class="w-4 h-4 text-[var(--color-text-muted)] absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <button type="button" id="city-search-clear" class="absolute right-3 top-2.5 text-[var(--color-text-muted)] hover:text-[var(--color-secondary)] transition-colors hidden" aria-label="Hapus pencarian">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p id="city-search-meta" class="text-xs text-[var(--color-text-muted)] mt-2 hidden"></p>
            </div>

            <!-- Search Results Grid (dynamically toggled) -->
            <div id="city-search-results" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 hidden"></div>

            <!-- Default Paginated Grid -->
            <div id="city-paginated-grid">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5">
                    @foreach($paginatedCities as $city)
                    <a href="{{ route('city.landing', ['citySlug' => $city['slug']]) }}" class="card-premium p-4 flex flex-col justify-between hover:border-[var(--color-primary)] hover:bg-[var(--color-primary-soft)] transition-colors group">
                        <div>
                            <span class="text-[10px] font-heading font-semibold uppercase px-1.5 py-0.5 rounded bg-[var(--color-surface-muted)] text-[var(--color-text-muted)] group-hover:bg-white inline-block mb-1.5">
                                {{ $city['type'] }}
                            </span>
                            <h3 class="font-heading font-bold text-sm text-[var(--color-secondary)] group-hover:text-[var(--color-primary)]">
                                {{ $city['raw_name'] }}
                            </h3>
                            <span class="text-[11px] text-[var(--color-text-muted)] block mt-0.5">
                                {{ $city['province'] }}
                            </span>
                        </div>
                        <span class="text-[11px] font-heading font-semibold text-[var(--color-primary)] mt-3 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            Info Wilayah &rarr;
                        </span>
                    </a>
                    @endforeach
                </div>

                <div class="mt-6">{{ $paginatedCities->links() }}</div>
            </div>

            <p class="text-xs text-[var(--color-text-muted)] mt-6 text-center">
                Mencari informasi di wilayah lainnya? Seluruh kota dan kabupaten di Indonesia terhubung dalam sistem navigasi wilayah kami.
            </p>
        </div>
    </section>

    <!-- Section H: FAQ Section -->
    <section id="faq" class="py-16 sm:py-20 bg-[var(--color-surface)] border-b border-[var(--color-border)]" aria-labelledby="faq-title">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 space-y-3">
                <span class="text-xs font-heading font-bold uppercase tracking-wider text-[var(--color-primary)] block">INFORMASI PELATIHAN</span>
                <h2 id="faq-title" class="font-heading font-bold text-3xl sm:text-4xl text-[var(--color-secondary)]">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-base text-[var(--color-text-muted)]">
                    Informasi penting seputar konsep, peserta, jadwal, dan konsultasi Training Defensive Driving.
                </p>
            </div>

            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="card-premium p-6 bg-white border border-[var(--color-border)]">
                    <h3 class="font-heading font-bold text-base text-[var(--color-secondary)] mb-2">
                        Apa itu Defensive Driving?
                    </h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Defensive Driving adalah pendekatan berkendara yang berfokus pada kesadaran aktif, antisipasi potensi bahaya di jalan raya, dan pengambilan keputusan yang bertanggung jawab untuk mencegah kecelakaan terlepas dari kondisi sekitar atau kesalahan pengguna jalan lain.
                    </p>
                </div>

                <!-- FAQ 2 -->
                <div class="card-premium p-6 bg-white border border-[var(--color-border)]">
                    <h3 class="font-heading font-bold text-base text-[var(--color-secondary)] mb-2">
                        Siapa yang dapat mempertimbangkan pelatihan ini?
                    </h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Pelatihan ini dapat dipertimbangkan oleh pengemudi harian individu yang ingin berkendara lebih aman, maupun perusahaan, pengelola armada (fleet operator), dan instansi yang mempekerjakan pengemudi operasional untuk mendukung budaya keselamatan kerja.
                    </p>
                </div>

                <!-- FAQ 3 -->
                <div class="card-premium p-6 bg-white border border-[var(--color-border)]">
                    <h3 class="font-heading font-bold text-base text-[var(--color-secondary)] mb-2">
                        Apakah tersedia program untuk perusahaan?
                    </h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Ya. Kami membuka kesempatan diskusi program in-house untuk organisasi atau perusahaan yang ingin menyelenggarakan sesi edukasi bagi pengemudi operasional, staf lapangan, maupun armada niaga sesuai kebutuhan operasional kerja.
                    </p>
                </div>

                <!-- FAQ 4 -->
                <div class="card-premium p-6 bg-white border border-[var(--color-border)]">
                    <h3 class="font-heading font-bold text-base text-[var(--color-secondary)] mb-2">
                        Bagaimana cara menanyakan jadwal dan biaya?
                    </h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Jadwal, durasi, dan ketentuan komersial ditentukan melalui komunikasi langsung bersama pihak penyelenggara berdasarkan format sesi, jumlah peserta, dan lokasi penyelenggaraan yang diajukan.
                    </p>
                </div>

                <!-- FAQ 5 -->
                <div class="card-premium p-6 bg-white border border-[var(--color-border)]">
                    <h3 class="font-heading font-bold text-base text-[var(--color-secondary)] mb-2">
                        Apakah materi dapat disesuaikan dengan kebutuhan peserta?
                    </h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Penyelenggara dapat mendiskusikan penyesuaian materi agar relevan dengan karakteristik medan rute, profil kendaraan operasional yang digunakan, serta fokus keselamatan spesifik yang menjadi prioritas organisasi Anda.
                    </p>
                </div>

                <!-- FAQ 6 -->
                <div class="card-premium p-6 bg-white border border-[var(--color-border)]">
                    <h3 class="font-heading font-bold text-base text-[var(--color-secondary)] mb-2">
                        Apakah tersedia sertifikat setelah pelatihan?
                    </h3>
                    <p class="text-sm text-[var(--color-text-muted)] leading-relaxed">
                        Ketersediaan, jenis dokumentasi, atau sertifikat penyelesaian sesi pelatihan wajib dikonfirmasikan terlebih dahulu dengan pihak penyelenggara pada saat konsultasi awal sebelum program disepakati.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section J: Closing Consultation CTA Section -->
    <section id="konsultasi" class="py-16 sm:py-20 bg-[var(--color-midnight-navy)] text-white" aria-labelledby="cta-title">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
            <span class="inline-block px-3 py-1 rounded-full bg-[rgba(37,99,235,0.2)] border border-[rgba(96,165,250,0.3)] text-[var(--color-electric)] text-xs font-heading font-semibold uppercase tracking-wider">
                KONSULTASI PROGRAM
            </span>
            <div class="space-y-3">
                <h2 id="cta-title" class="font-heading font-bold text-3xl sm:text-4xl text-white">
                    Mulai Diskusikan Kebutuhan Pelatihan Berkendara Anda.
                </h2>
                <p class="text-base text-[#94A3B8] max-w-2xl mx-auto leading-relaxed">
                    Pilih jalur konsultasi yang sesuai dengan profil Anda untuk mendapatkan informasi menyeluruh mengenai materi, ketersediaan jadwal, dan teknis pelaksanaan.
                </p>
            </div>

            <!-- Two Consultation Cards: Individual vs Corporate -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left max-w-3xl mx-auto pt-2">
                <!-- Individual Driver Path -->
                <div class="card-dark p-6 sm:p-7 bg-[#111C2E] border border-[#1E293B] space-y-4 flex flex-col justify-between">
                    <div class="space-y-2">
                        <span class="text-xs font-heading font-semibold uppercase text-[var(--color-electric)] tracking-wider">
                            JALUR PENGEMUDI INDIVIDU
                        </span>
                        <h3 class="font-heading font-bold text-xl text-white">
                            Konsultasi Pengemudi Individu
                        </h3>
                        <p class="text-xs text-[#94A3B8] leading-relaxed">
                            Bagi pengendara yang ingin mempelajari prinsip antisipasi bahaya, aturan jarak aman, dan kendali emosi saat berkendara sehari-hari.
                        </p>
                    </div>

                    <div class="pt-2">
                        @if($isWaEnabled && $waIndividualUrl)
                            <a href="{{ $waIndividualUrl }}" target="_blank" rel="noopener noreferrer" class="btn-dark-primary w-full text-center text-xs py-3 font-semibold">
                                Hubungi via WhatsApp (Individu)
                            </a>
                        @else
                            <div class="p-3 rounded-[10px] bg-[#0B1220] border border-[#1E293B] text-center text-xs text-[#94A3B8]">
                                <span class="font-medium text-white block mb-0.5">Saluran WhatsApp Segera Hadir</span>
                                <span>Kontak resmi sedang disiapkan oleh penyelenggara.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Corporate Fleet Path -->
                <div class="card-dark p-6 sm:p-7 bg-[#111C2E] border border-[#1E293B] space-y-4 flex flex-col justify-between">
                    <div class="space-y-2">
                        <span class="text-xs font-heading font-semibold uppercase text-[var(--color-accent)] tracking-wider">
                            JALUR PERUSAHAAN &amp; FLEET
                        </span>
                        <h3 class="font-heading font-bold text-xl text-white">
                            Konsultasi Armada &amp; Perusahaan
                        </h3>
                        <p class="text-xs text-[#94A3B8] leading-relaxed">
                            Bagi HRD, HSE Manager, atau pimpinan armada yang ingin mendiskusikan kebutuhan pelatihan in-house bagi pengemudi kerja.
                        </p>
                    </div>

                    <div class="pt-2">
                        @if($isWaEnabled && $waCorporateUrl)
                            <a href="{{ $waCorporateUrl }}" target="_blank" rel="noopener noreferrer" class="btn-dark-primary w-full text-center text-xs py-3 font-semibold">
                                Hubungi via WhatsApp (Perusahaan)
                            </a>
                        @else
                            <div class="p-3 rounded-[10px] bg-[#0B1220] border border-[#1E293B] text-center text-xs text-[#94A3B8]">
                                <span class="font-medium text-white block mb-0.5">Saluran WhatsApp Segera Hadir</span>
                                <span>Kontak resmi sedang disiapkan oleh penyelenggara.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Client-side City Search Script using preloaded JSON from HomeController -->
    <script>
        (function () {
            const ALL_CITIES = @json($allCitiesForSearch);

            const input     = document.getElementById('city-search-input');
            const clearBtn  = document.getElementById('city-search-clear');
            const meta      = document.getElementById('city-search-meta');
            const results   = document.getElementById('city-search-results');
            const paginated = document.getElementById('city-paginated-grid');

            if (!input) { return; }

            const cityUrlTemplate = '{{ route('city.landing', ['citySlug' => ':slug']) }}';

            function cityCardHtml(city) {
                return '<a href="' + cityUrlTemplate.replace(':slug', city.slug) + '" class="card-premium p-4 flex flex-col justify-between hover:border-[var(--color-primary)] hover:bg-[var(--color-primary-soft)] transition-colors group">'
                    + '<div>'
                    + '<span class="text-[10px] font-heading font-semibold uppercase px-1.5 py-0.5 rounded bg-[var(--color-surface-muted)] text-[var(--color-text-muted)] group-hover:bg-white inline-block mb-1.5">'
                    + city.type
                    + '</span>'
                    + '<h3 class="font-heading font-bold text-sm text-[var(--color-secondary)] group-hover:text-[var(--color-primary)]">' + city.raw_name + '</h3>'
                    + '<span class="text-[11px] text-[var(--color-text-muted)] block mt-0.5">' + city.province + '</span>'
                    + '</div>'
                    + '<span class="text-[11px] font-heading font-semibold text-[var(--color-primary)] mt-3 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">Info Wilayah &rarr;</span>'
                    + '</a>';
            }

            function doSearch(query) {
                const q = query.trim().toLowerCase();

                if (q === '') {
                    results.classList.add('hidden');
                    results.innerHTML = '';
                    paginated.classList.remove('hidden');
                    meta.classList.add('hidden');
                    clearBtn.classList.add('hidden');
                    return;
                }

                clearBtn.classList.remove('hidden');

                const matches = ALL_CITIES.filter(function (c) {
                    return c.raw_name.toLowerCase().includes(q)
                        || c.name.toLowerCase().includes(q)
                        || c.province.toLowerCase().includes(q);
                });

                paginated.classList.add('hidden');
                results.classList.remove('hidden');
                meta.classList.remove('hidden');

                if (matches.length === 0) {
                    results.innerHTML = '';
                    meta.textContent = 'Tidak ditemukan kota/kabupaten dengan kata kunci "' + query.trim() + '".';
                    return;
                }

                meta.textContent = 'Ditemukan ' + matches.length + ' wilayah untuk kata kunci "' + query.trim() + '":';
                results.innerHTML = matches.map(cityCardHtml).join('');
            }

            let debounceTimer = null;
            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    doSearch(input.value);
                }, 150);
            });

            clearBtn.addEventListener('click', function () {
                input.value = '';
                doSearch('');
                input.focus();
            });
        })();
    </script>

@endsection
