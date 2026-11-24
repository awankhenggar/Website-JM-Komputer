<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\Review;
use App\Models\ServiceRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => Product::count(),
            'low_stock' => Product::where('stock', '<=', 3)->count(),
            'to_pack' => Order::where('status', 'dibayar')->count(),
            'unpaid' => Order::where('status', 'menunggu_bayar')->count(),
            'services_active' => ServiceRequest::whereNotIn('status', ['lunas', 'dibatalkan'])->count(),
            'returns_new' => ReturnRequest::where('status', 'baru')->count(),
            'reviews' => Review::count(),
            'revenue' => Order::whereIn('status', Order::PAID)->where('created_at', '>=', now()->startOfMonth())->sum('total'),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'latestOrders' => Order::with('user')->latest()->take(5)->get(),
            'latestServices' => ServiceRequest::with('user')->latest()->take(5)->get(),
        ]);
    }
}
