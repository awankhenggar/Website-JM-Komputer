<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceItem;
use Illuminate\Http\Request;

class ServiceItemController extends Controller
{
    public function index()
    {
        return view('admin.service-items.index', [
            'items' => ServiceItem::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.service-items.form', ['item' => new ServiceItem(['is_active' => true, 'sort_order' => 0])]);
    }

    public function store(Request $request)
    {
        ServiceItem::create($this->validated($request));

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan ditambahkan.');
    }

    public function edit(ServiceItem $serviceItem)
    {
        return view('admin.service-items.form', ['item' => $serviceItem]);
    }

    public function update(Request $request, ServiceItem $serviceItem)
    {
        $serviceItem->update($this->validated($request));

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan diperbarui.');
    }

    public function destroy(ServiceItem $serviceItem)
    {
        $serviceItem->delete();

        return back()->with('success', 'Layanan dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'price_from' => ['nullable', 'integer', 'min:0'],
            'est_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
