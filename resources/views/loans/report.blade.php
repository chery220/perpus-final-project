@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-8 px-4 space-y-6">
    <!-- Header & Deskripsi -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#1E4D3B]">Laporan Peminjaman</h1>
        </div>

        <!-- Tombol Export CSV -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('loans.report.export', ['period' => 'daily']) }}"
               class="px-3.5 py-2 text-xs font-semibold rounded-lg border border-[#1E4D3B] text-[#1E4D3B] hover:bg-[#1E4D3B] hover:text-white transition">
                Export Harian (CSV)
            </a>
            <a href="{{ route('loans.report.export', ['period' => 'weekly']) }}"
               class="px-3.5 py-2 text-xs font-semibold rounded-lg border border-[#1E4D3B] text-[#1E4D3B] hover:bg-[#1E4D3B] hover:text-white transition">
                Export Mingguan (CSV)
            </a>
            <a href="{{ route('loans.report.export', ['period' => 'monthly']) }}"
               class="px-3.5 py-2 text-xs font-semibold rounded-lg bg-[#1E4D3B] text-white hover:opacity-90 transition">
                Export Bulanan (CSV)
            </a>
        </div>
    </div>
    
    <!-- Tabel Laporan -->
    <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-stone-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F4F0EA] border-b border-stone-200 text-stone-700 text-sm">
                        <th class="py-3.5 px-4 font-semibold text-[#1E4D3B]">ID</th>
                        <th class="py-3.5 px-4 font-semibold text-[#1E4D3B]">Anggota</th>
                        <th class="py-3.5 px-4 font-semibold text-[#1E4D3B]">Petugas</th>
                        <th class="py-3.5 px-4 font-semibold text-[#1E4D3B]">Buku</th>
                        <th class="py-3.5 px-4 font-semibold text-[#1E4D3B]">Tgl Pinjam</th>
                        <th class="py-3.5 px-4 font-semibold text-[#1E4D3B]">Tgl Kembali</th>
                        <th class="py-3.5 px-4 font-semibold text-[#1E4D3B]">Status</th>
                    </tr>
                </thead>
                <tbody id="api-loans-table" class="divide-y divide-stone-100 text-sm">
                    <tr>
                        <td colspan="7" class="py-6 text-center text-stone-500 italic">Memuat data dari API...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Wadah Pagination dari API -->
        <div id="api-loans-pagination" class="px-4 py-3 bg-[#FAF8F5] border-t border-stone-200 flex items-center justify-between text-sm text-stone-600 hidden">
            <span id="pagination-info">Halaman 1</span>
            <div class="flex items-center gap-2">
                <button type="button" id="btn-prev-page"
                        class="px-3 py-1 rounded-lg border border-stone-300 bg-white hover:bg-stone-100 disabled:opacity-40 disabled:cursor-not-allowed">
                    &laquo; Sebelumnya
                </button>
                <button type="button" id="btn-next-page"
                        class="px-3 py-1 rounded-lg border border-stone-300 bg-white hover:bg-stone-100 disabled:opacity-40 disabled:cursor-not-allowed">
                    Berikutnya &raquo;
                </button>
            </div>
        </div>
    </div>

<!-- Skrip JavaScript untuk Fetch API /api/loans -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const tbody = document.getElementById('api-loans-table');
    const paginationBox = document.getElementById('api-loans-pagination');
    const paginationInfo = document.getElementById('pagination-info');
    const btnPrev = document.getElementById('btn-prev-page');
    const btnNext = document.getElementById('btn-next-page');

    let currentPage = 1;
    let lastPage = 1;

    function loadLoans(page = 1) {
        currentPage = page;
        tbody.innerHTML = `<tr><td colspan="7" class="py-6 text-center text-stone-500 italic">Memuat data dari API...</td></tr>`;

        fetch(`/api/loans?page=${page}`)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';

                let loans = Array.isArray(data) ? data : (data.data || []);

                // Baca info halaman jika API mengembalikan format paginasi Laravel
                if (!Array.isArray(data)) {
                    currentPage = data.current_page ?? (data.meta?.current_page ?? page);
                    lastPage = data.last_page ?? (data.meta?.last_page ?? 1);
                } else {
                    lastPage = 1;
                }

                if (loans.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="py-6 text-center text-stone-500 italic">Belum ada data peminjaman.</td></tr>`;
                    paginationBox.classList.add('hidden');
                    return;
                }

                loans.forEach(loan => {
                    let bookTitles = '-';
                    if (loan.loan_items && loan.loan_items.length > 0) {
                        bookTitles = loan.loan_items.map(item => item.book ? item.book.judul : '-').join(', ');
                    } else if (loan.books && loan.books.length > 0) {
                        bookTitles = loan.books.map(b => b.judul).join(', ');
                    }

                    let badgeClass = 'bg-stone-100 text-stone-800';
                    let statusLower = (loan.status || '').toLowerCase();
                    if (statusLower === 'dipinjam') badgeClass = 'bg-blue-100 text-blue-800';
                    else if (statusLower === 'dikembalikan') badgeClass = 'bg-emerald-100 text-emerald-800';
                    else if (statusLower === 'terlambat') badgeClass = 'bg-red-100 text-red-800';

                    let petugasName = (loan.user && loan.user.name) ? loan.user.name : 'Admin PENS';

                    let row = `
                            <tr class="hover:bg-stone-50 transition">
                                <td class="py-3 px-4 text-stone-600 font-medium">#${loan.id}</td>
                                <td class="py-3 px-4 text-stone-900">${loan.member ? loan.member.nama : '-'}</td>
                                <td class="py-3 px-4 text-stone-600">${petugasName}</td>
                                <td class="py-3 px-4 text-stone-800">${bookTitles}</td>
                                <td class="py-3 px-4 text-stone-600">${loan.tanggal_pinjam}</td>
                                <td class="py-3 px-4 text-stone-600">${loan.tanggal_kembali}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full ${badgeClass}">${loan.status}</span>
                                </td>
                            </tr>
                        `;
                    tbody.innerHTML += row;
                });

                if (lastPage > 1) {
                    paginationBox.classList.remove('hidden');
                    paginationInfo.textContent = `Halaman ${currentPage} dari ${lastPage}`;
                    btnPrev.disabled = currentPage <= 1;
                    btnNext.disabled = currentPage >= lastPage;
                } else {
                    paginationBox.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Gagal mengambil data API:', error);
                tbody.innerHTML = `<tr><td colspan="7" class="py-6 text-center text-red-500 italic">Gagal memuat data dari endpoint API.</td></tr>`;
                paginationBox.classList.add('hidden');
            });
    }

    btnPrev.addEventListener('click', () => {
        if (currentPage > 1) loadLoans(currentPage - 1);
    });

    btnNext.addEventListener('click', () => {
        if (currentPage < lastPage) loadLoans(currentPage + 1);
    });

    loadLoans(1);
});
</script>
@endsection