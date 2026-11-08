@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header sambutan -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-stone-200">
        <h1 class="text-2xl font-bold text-[#1E4D3B]">Dashboard Libria</h1>
        <p class="text-stone-600 mt-1">Ringkasan statistik perpustakaan dan katalog buku populer.</p>
    </div>

    <!-- Statistik Cards (Grid 3 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card Total Buku -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-stone-200 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-stone-500 uppercase tracking-wider">Total Buku</p>
                <h3 class="text-3xl font-extrabold text-[#1E4D3B] mt-2">{{ $stats['total_buku'] ?? 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-[#F4F0EA] flex items-center justify-center text-2xl">
                📚
            </div>
        </div>

        <!-- Card Total Anggota -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-stone-200 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-stone-500 uppercase tracking-wider">Total Anggota</p>
                <h3 class="text-3xl font-extrabold text-[#1E4D3B] mt-2">{{ $stats['total_anggota'] ?? 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-[#F4F0EA] flex items-center justify-center text-2xl">
                👥
            </div>
        </div>

        <!-- Card Peminjaman Aktif -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-stone-200 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-stone-500 uppercase tracking-wider">Peminjaman Aktif</p>
                <h3 class="text-3xl font-extrabold text-[#1E4D3B] mt-2">{{ $stats['peminjaman_aktif'] ?? 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-[#F4F0EA] flex items-center justify-center text-2xl">
                🔄
            </div>
        </div>
    </div>

    <!-- 🔥 SECTION: OUR BEST SELLER (Stok Terendah) -->
    <section class="bg-white p-6 rounded-2xl shadow-sm border border-stone-200">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-[#1E4D3B]">🔥 Our Best Seller</h2>
            <p class="text-sm text-stone-500">Buku yang paling diminati berdasarkan ketersediaan stok</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @foreach($bestSellers as $book)
                <div class="bg-[#FBF9F5] rounded-2xl p-4 shadow-sm hover:shadow-md transition-all border border-stone-100 flex flex-col justify-between">
                    <div>
                        <div class="relative aspect-[3/4] overflow-hidden rounded-xl mb-3 bg-stone-200">
                            @if($book->cover)
                                <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->judul }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-xs text-stone-400">No Cover</div>
                            @endif
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">
                                Sisa: {{ $book->stok }}
                            </span>
                        </div>
                        <h3 class="font-bold text-stone-900 text-sm truncate">{{ $book->judul }}</h3>
                        <p class="text-xs text-stone-500 truncate mt-0.5">{{ $book->penulis ?? 'Penulis tidak diketahui' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 📚 SECTION: KATEGORI & GRID BUKU -->
    <section class="bg-white p-6 rounded-2xl shadow-sm border border-stone-200">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-[#1E4D3B] mb-3">Kategori & Daftar Buku</h2>
            
            <!-- Filter Kategori Pill Buttons -->
            <div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-none">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-full {{ !request('category') ? 'bg-[#1E4D3B] text-white' : 'bg-stone-100 text-stone-700 hover:bg-[#1E4D3B] hover:text-white' }} font-medium text-xs shadow-sm flex-shrink-0 transition-all">
                All
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('dashboard', ['category' => $cat->id]) }}" class="px-4 py-2 rounded-full {{ request('category') == $cat->id ? 'bg-[#1E4D3B] text-white' : 'bg-stone-100 text-stone-700 hover:bg-[#1E4D3B] hover:text-white' }} font-medium text-xs transition-all flex-shrink-0">
                    {{ $cat->nama_kategori }}
                </a>
            @endforeach
        </div>
    </div>

        <!-- Grid Daftar Buku Utama -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @foreach($books as $book)
                <div class="bg-[#FBF9F5] rounded-2xl p-3 shadow-sm hover:shadow-md transition-all border border-stone-100 flex flex-col justify-between">
                    <div>
                        <div class="aspect-[3/4] rounded-xl overflow-hidden mb-2 bg-stone-200">
                            @if($book->cover)
                                <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->judul }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-xs text-stone-400">No Cover</div>
                            @endif
                        </div>
                        <h4 class="font-semibold text-stone-800 text-sm truncate">{{ $book->judul }}</h4>
                        <p class="text-xs text-stone-500 truncate mt-0.5">{{ $book->penulis ?? '-' }}</p>
                    </div>
                    <span class="inline-block mt-3 text-[10px] bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded font-medium w-max">
                        {{ $book->category->nama_kategori ?? 'Umum' }}
                    </span>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Tombol Aksi Cepat -->
    <div class="pt-4">
        <a href="{{ route('loans.report') }}" class="inline-flex items-center px-5 py-3 rounded-xl font-medium text-white bg-[#1E4D3B] hover:bg-[#16382b] transition shadow-md">
            Lihat Laporan Peminjaman &rarr;
        </a>
    </div>
</div>
@endsection