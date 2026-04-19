<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('guru');
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $gurus = $query->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.gurus.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.gurus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Username/Email sudah digunakan.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $guruRole = Role::firstOrCreate(['name' => 'guru']);
        $user->assignRole($guruRole);

        return redirect()->route('admin.gurus.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit(User $guru)
    {
        return view('admin.gurus.edit', compact('guru'));
    }

    public function update(Request $request, User $guru)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($guru->id),
            ],
            'password' => 'nullable|string|min:6',
        ], [
            'email.unique' => 'Username/Email sudah digunakan.'
        ]);

        $guru->name = $request->name;
        $guru->email = $request->email;
        if ($request->filled('password')) {
            $guru->password = bcrypt($request->password);
        }
        $guru->save();

        return redirect()->route('admin.gurus.index')->with('success', 'Data Guru berhasil diupdate.');
    }

    public function destroy(User $guru)
    {
        $guru->delete();
        return redirect()->route('admin.gurus.index')->with('success', 'Guru berhasil dihapus.');
    }
}
