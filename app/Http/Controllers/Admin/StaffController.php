<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        return view('admin.staff.index', [
            'staff' => User::whereIn('role', ['admin', 'teknisi'])->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.staff.form', ['user' => new User(['role' => 'teknisi'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:admin,teknisi'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create($data + ['email_verified_at' => now()]);

        return redirect()->route('admin.pengguna.index')->with('success', 'Akun ditambahkan.');
    }

    public function edit(User $user)
    {
        abort_unless($user->isStaff(), 404);

        return view('admin.staff.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->isStaff(), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:admin,teknisi'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak bisa menurunkan peran akun Anda sendiri.');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.pengguna.index')->with('success', 'Akun diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->isStaff(), 404);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Akun dihapus.');
    }
}
