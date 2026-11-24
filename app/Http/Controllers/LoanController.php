<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $loans = Loan::with(['member', 'user', 'loanItems.book'])->paginate(10);

    return view('loans.index', compact('loans'));
    }

    public function report(Request $request)
    {
        $body = ['data' => [], 'meta' => null];

        try {
            $response = Http::get(config('services.internal_api.base_url').'/api/loans', [
                'page' => $request->query('page', 1),
            ]);

            if ($response->successful()) {
                $body = $response->json();
            }
        } catch (ConnectionException $e) {
            // Server internal API (port 8011) tidak bisa dihubungi - tabel laporan tampil kosong.
        }

        return view('loans.report', [
            'loans' => $body['data'] ?? [],
            'meta' => $body['meta'] ?? null,
        ]);
    }


    public function exportReport(Request $request)
    {
        $period = $request->query('period', 'daily'); // daily | weekly | monthly
        $now = Carbon::now();

        $query = Loan::with(['member', 'user', 'loanItems.book'])->orderBy('id', 'desc');

        if ($period === 'weekly') {
            $startDate = $now->copy()->startOfWeek()->toDateString();
            $endDate = $now->copy()->endOfWeek()->toDateString();
            $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
        } elseif ($period === 'monthly') {
            $startDate = $now->copy()->startOfMonth()->toDateString();
            $endDate = $now->copy()->endOfMonth()->toDateString();
            $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
        } else {
            $period = 'daily';
            $targetDate = $now->toDateString();
            $query->whereDate('tanggal_pinjam', $targetDate);
        }

        $loans = $query->get();
        $filename = "laporan-peminjaman-{$period}-{$now->format('Y-m-d')}.csv";

        return response()->streamDownload(function () use ($loans) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM agar karakter dan tanda baca rapi saat dibuka di Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header kolom sesuai tampilan laporan
            fputcsv($handle, [
                'ID',
                'Anggota',
                'Petugas',
                'Buku',
                'Tgl Pinjam',
                'Tgl Kembali',
                'Status',
            ], ';');

            foreach ($loans as $loan) {
                $books = $loan->loanItems
                    ->map(fn ($item) => $item->book->judul ?? null)
                    ->filter()
                    ->implode(', ');

                fputcsv($handle, [
                    '#' . $loan->id,
                    $loan->member->nama ?? '-',
                    $loan->user->name ?? '-',
                    $books !== '' ? $books : '-',
                    $loan->tanggal_pinjam ? Carbon::parse($loan->tanggal_pinjam)->format('Y-m-d') : '-',
                    $loan->tanggal_kembali ? Carbon::parse($loan->tanggal_kembali)->format('Y-m-d') : '-',
                    $loan->status ?? '-',
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::all();
        $books = Book::all();

        return view('loans.create', compact('members', 'books'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'books' => 'required|array|min:1',
            'books*' => 'integer|exists:books,id',
            ]);

        $loan = Loan::create([
            'member_id' => $validated['member_id'],
            'user_id' => auth()->id(),
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali' => $validated['tanggal_kembali'],
            'status' => 'dipinjam',
        ]);

        foreach ($validated['books'] as $bookId) {
            $loan->loanItems()->create(['book_id' => $bookId]);
        }

        // Kurangi stok buku secara otomatis
        $book = Book::find($bookId);
        if ($book && $book->stok > 0) {
            $book->stok -= 1;
            $book->save();
        }

        return redirect()->route('loans.index')
            ->with('success', 'Transaksi peminjaman berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    $loan = Loan::with(['member', 'user', 'loanItems.book'])->findOrFail($id);
    return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $loan = Loan::with('loanItems')->findOrFail($id);
        $members = Member::all(); // 
        $books = Book::all();     //

        return view('loans.edit', compact('loan', 'members', 'books'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $loan = Loan::findOrFail($id);

    $validated = $request->validate([
        'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        'status' => 'required|in:dipinjam,dikembalikan,terlambat',
    ], [
        'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
        'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
    ]);

    $loan->update($validated);

    return redirect()->route('loans.index')
        ->with('success', 'Transaksi peminjaman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $loan = Loan::findOrFail($id);
    $loan->loanItems()->delete();
    $loan->delete();

    return redirect()->route('loans.index')
        ->with('success', 'Transaksi peminjaman berhasil dihapus.');
    }
    
    public function kembalikan($id)
    {
        $loan = Loan::with('loanItems.book')->findOrFail($id);

        // Cek jika sebelumnya belum dikembalikan, kembalikan stok bukunya
        if (strtolower($loan->status) !== 'dikembalikan') {
            foreach ($loan->loanItems as $item) {
                if ($item->book) {
                    $item->book->stok += 1;
                    $item->book->save();
                }
            }
            $loan->status = 'dikembalikan';
            $loan->save();
        }

        return redirect()->route('loans.index')->with('success', 'Buku berhasil dikembalikan dan stok diperbarui!');
    }
    }
