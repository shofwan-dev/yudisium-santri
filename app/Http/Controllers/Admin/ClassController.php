<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::withCount('students')->get();
        return view('admin.classes.index', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kelas' => 'required|string|max:255|unique:classes,nama_kelas']);
        ClassRoom::create($request->only('nama_kelas'));
        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, ClassRoom $class)
    {
        $request->validate(['nama_kelas' => 'required|string|max:255|unique:classes,nama_kelas,' . $class->id]);
        $class->update($request->only('nama_kelas'));
        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(ClassRoom $class)
    {
        if ($class->students()->count() > 0) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki data santri.');
        }
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
