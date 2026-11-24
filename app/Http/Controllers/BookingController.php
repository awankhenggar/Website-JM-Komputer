<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function create()
    {
        $orders = auth()->user()->orders()
            ->whereIn('status', ['dikirim', 'selesai'])
            ->latest()->get();

        return view('booking.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_type' => ['required', 'in:umum,garansi'],
            'order_id' => ['nullable', Rule::exists('orders', 'id')->where('user_id', auth()->id())],
            'device_type' => ['required', 'in:Laptop,PC / Komputer,Lainnya'],
            'brand_model' => ['required', 'string', 'max:150'],
            'complaint' => ['required', 'string', 'max:2000'],
            'handover' => ['required', 'in:datang,kurir'],
            'visit_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        if ($data['service_type'] === 'umum') {
            $data['order_id'] = null;
        }

        $service = ServiceRequest::create($data + [
            'user_id' => auth()->id(),
            'code' => ServiceRequest::generateCode(),
            'status' => 'booking',
        ]);
        $service->log('booking', 'Booking servis dibuat oleh pelanggan.');

        return redirect()->route('account.services.show', $service)
            ->with('success', 'Booking diterima. Simpan nomor servis '.$service->code.'.');
    }
}
