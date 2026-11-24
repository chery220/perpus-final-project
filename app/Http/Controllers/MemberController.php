<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $members = $query->paginate(10)->withQueryString();
        return view('members.index', compact('members'));
    }

    public function create()
    {
        // 2. Arahkan ke view form tambah data (members.create)
        return view('members.create');
    }

    public function show(string $id)
    {
        $member = Member::with(['loans.loanItems.book', 'loans.user'])->findOrFail($id);

        return view('members.show', compact('member'));
    }
    
    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();

    Member::create($validated);

    return redirect()->route('members.index')
        ->with('success', "Anggota \"{$validated['nama']}\" berhasil ditambahkan.");
    }

    public function edit(string $id)
    {
        $member = Member::findOrFail($id);

    return view('members.edit', compact('member'));
    }

    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);

    $validated = $request->validate([
        'nama' => 'required|string|max:100',
        'nim' => 'required|string|max:20|unique:members,nim,'.$member->id,
        'email' => 'required|email|max:100|unique:members,email,'.$member->id,
        'nomor_telepon' => 'required|string|max:15',
        'alamat' => 'required|string',
        'status' => 'required|in:aktif,nonaktif',
    ]);

    $member->update($validated);

    return redirect()->route('members.index')
        ->with('success', "Anggota \"{$validated['nama']}\" berhasil diperbarui.");
    }

    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
    $member->delete();

    return redirect()->route('members.index')
        ->with('success', 'Anggota berhasil dihapus.');
    }
}