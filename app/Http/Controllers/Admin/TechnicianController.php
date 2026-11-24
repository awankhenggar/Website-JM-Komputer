<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TechnicianController extends Controller
{
    public function index()
    {
        return view('admin.technicians.index', [
            'technicians' => Technician::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.technicians.form', ['technician' => new Technician(['is_active' => true, 'sort_order' => 0])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('technicians', 'public');
        }

        Technician::create($data);

        return redirect()->route('admin.tim.index')->with('success', 'Teknisi ditambahkan.');
    }

    public function edit(Technician $technician)
    {
        return view('admin.technicians.form', compact('technician'));
    }

    public function update(Request $request, Technician $technician)
    {
        $data = $this->validated($request);

        if ($request->hasFile('photo')) {
            if ($technician->photo) {
                Storage::disk('public')->delete($technician->photo);
            }
            $data['photo'] = $request->file('photo')->store('technicians', 'public');
        }

        $technician->update($data);

        return redirect()->route('admin.tim.index')->with('success', 'Data teknisi diperbarui.');
    }

    public function destroy(Technician $technician)
    {
        if ($technician->photo) {
            Storage::disk('public')->delete($technician->photo);
        }

        $technician->delete();

        return back()->with('success', 'Teknisi dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'title' => ['nullable', 'string', 'max:100'],
            'expertise' => ['nullable', 'string', 'max:200'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($data['photo']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
