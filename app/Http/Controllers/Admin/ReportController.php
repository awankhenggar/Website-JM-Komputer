<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = ($request->date('from') ?? now()->startOfMonth())->copy()->startOfDay();
        $to = ($request->date('to') ?? now())->copy()->endOfDay();

        $paidOrders = Order::whereIn('status', Order::PAID)->whereBetween('created_at', [$from, $to]);

        $summary = [
            'sales' => (clone $paidOrders)->sum('total'),
            'orders' => (clone $paidOrders)->count(),
            'orders_all' => Order::whereBetween('created_at', [$from, $to])->count(),
            'services' => ServiceRequest::whereBetween('created_at', [$from, $to])->count(),
            'services_done' => ServiceRequest::whereIn('status', ['selesai', 'lunas'])->whereBetween('created_at', [$from, $to])->count(),
            'service_revenue' => ServiceRequest::where('status', 'lunas')->whereBetween('created_at', [$from, $to])->sum('final_cost'),
        ];

        $daily = (clone $paidOrders)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as orders, SUM(total) as total')
            ->groupBy('day')->orderBy('day')->get();

        $top = OrderItem::selectRaw('name, SUM(qty) as sold, SUM(qty * price) as revenue')
            ->whereHas('order', fn ($q) => $q->whereIn('status', Order::PAID)->whereBetween('created_at', [$from, $to]))
            ->groupBy('name')->orderByDesc('sold')->take(5)->get();

        return view('admin.reports.index', [
            'from' => $from, 'to' => $to, 'summary' => $summary, 'daily' => $daily, 'top' => $top,
        ]);
    }
}
