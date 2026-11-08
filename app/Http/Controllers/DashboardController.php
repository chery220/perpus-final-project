<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedCategory = $request->input('category');

        //Statistik di tampilan awal
        $stats = [
            'total_buku' => Book::sum('stok') ?? Book::count(),
            'total_anggota' => Member::count(),
            'peminjaman_aktif' => Loan::whereRaw('LOWER(status) LIKE ?', ['%pinjam%'])->count(),
            ];


        //Data baru untuk tampilan buku Best Seller
        $bestSellers = Book::orderBy('stok', 'asc')->take(5)->get(); // Buku dengan stok terendah
        $categories = Category::all();                               // Semua kategori untuk tombol filter

        $books = Book::when($selectedCategory, function ($query, $selectedCategory) {
        return $query->where('category_id', $selectedCategory);
        })->paginate(10);                       // Semua buku beserta relasi kategorinya

    // menggunakan return view dengan menyertakan variabel secara eksplisit
        return view('dashboard', [
        'stats' => $stats,
        'bestSellers' => $bestSellers,
        'categories' => $categories,
        'books' => $books,
        ]);
    }
}
