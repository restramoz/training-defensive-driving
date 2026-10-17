@extends('admin.layout')

@section('title', 'Manajemen Artikel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-heading font-bold text-2xl text-[var(--color-secondary)]">
                Manajemen Artikel
            </h1>
            <p class="text-xs text-[#64748B]">
                Kelola artikel edukasi Defensive Driving dan konten regional
            </p>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="btn-primary text-xs py-2.5 px-4 shadow-sm inline-flex items-center gap-1.5 self-start sm:self-auto">
            <span>+ Buat Artikel Baru</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-[16px] border border-[#E2E8F0] shadow-xs">
        <form method="GET" action="{{ route('admin.articles.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-6">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari judul atau slug artikel..."
                    class="w-full px-3.5 py-2 text-xs rounded-[10px] border border-[#CBD5E1] bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                >
            </div>
            <div class="sm:col-span-4">
                <select name="status" class="w-full px-3.5 py-2 text-xs rounded-[10px] border border-[#CBD5E1] bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full px-4 py-2 bg-[var(--color-secondary)] hover:bg-[#0E1726] text-white text-xs font-heading font-semibold rounded-[10px] transition">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.articles.index') }}" class="px-3 py-2 bg-[#F1F5F9] text-[#64748B] hover:text-[#1E293B] text-xs font-heading font-semibold rounded-[10px] transition flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[16px] border border-[#E2E8F0] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#334155]">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[11px] font-heading font-semibold text-[#64748B] uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Artikel</th>
                        <th class="py-3 px-4">Wilayah Target</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Tanggal Buat</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse($articles as $article)
                        <tr class="hover:bg-[#F8FAFC]/80 transition">
                            <td class="py-3.5 px-4 max-w-md">
                                <div class="font-heading font-bold text-sm text-[var(--color-secondary)] mb-0.5">
                                    {{ $article->title }}
                                </div>
                                <div class="text-[11px] text-[#64748B] font-mono">
                                    /artikel/{{ $article->slug }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($article->target_city_slug)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-heading font-semibold bg-[#F1F5F9] text-[var(--color-secondary)]">
                                        {{ $article->target_city_slug }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-[#94A3B8]">Nasional / Global</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($article->status === 'published')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-heading font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-heading font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-[#64748B]">
                                {{ $article->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if($article->status === 'published')
                                        <a href="{{ route('articles.show', $article->slug) }}" target="_blank" class="px-2.5 py-1 text-[11px] font-semibold text-[#0284C7] hover:bg-sky-50 rounded-lg transition" title="Lihat di publik">
                                            Lihat
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.articles.edit', $article->id) }}" class="px-2.5 py-1 text-[11px] font-semibold text-[var(--color-primary)] hover:bg-[var(--color-primary-soft)] rounded-lg transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold text-red-600 hover:bg-red-50 rounded-lg transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-[#94A3B8]">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <p class="text-sm">Belum ada artikel yang tersimpan.</p>
                                    <a href="{{ route('admin.articles.create') }}" class="btn-primary text-xs py-2 px-4 shadow-sm inline-block">
                                        Buat Artikel Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="p-4 border-t border-[#E2E8F0]">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
