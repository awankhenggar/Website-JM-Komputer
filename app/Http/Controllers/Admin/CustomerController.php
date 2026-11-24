<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = User::where('role', 'pelanggan')
            ->withCount(['orders', 'serviceRequests'])
            ->when($request->query('q'), function ($q, $v) {
                $q->where(function ($w) use ($v) {
                    $w->where('name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%");
                });
            })
            ->latest()->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        abort_unless($user->role === 'pelanggan', 404);

        $user->load([
            'orders' => fn ($q) => $q->latest(),
            'serviceRequests' => fn ($q) => $q->latest(),
        ]);

        return view('admin.customers.show', ['customer' => $user]);
    }
}
