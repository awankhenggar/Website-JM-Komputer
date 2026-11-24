<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = ServiceRequest::with('user')
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->query('q'), function ($q, $v) {
                $q->where(function ($w) use ($v) {
                    $w->where('code', 'like', "%{$v}%")->orWhere('brand_model', 'like', "%{$v}%");
                });
            })
            ->latest()->paginate(15)->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load('user', 'order', 'updates');

        return view('admin.services.show', ['service' => $serviceRequest]);
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(ServiceRequest::STATUSES))],
            'diagnosis' => ['nullable', 'string'],
            'estimate_cost' => ['nullable', 'integer', 'min:0'],
            'final_cost' => ['nullable', 'integer', 'min:0'],
            'admin_notes' => ['nullable', 'string'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $estimate = $data['estimate_cost'] ?? null;
        $final = $data['final_cost'] ?? null;

        if ($data['status'] === 'menunggu_persetujuan' && ! $estimate) {
            return back()->withInput()->withErrors(['estimate_cost' => 'Isi estimasi biaya sebelum meminta persetujuan pelanggan.']);
        }

        if (in_array($data['status'], ['selesai', 'lunas'], true)) {
            if (! $final) {
                return back()->withInput()->withErrors(['final_cost' => 'Isi biaya akhir untuk status selesai.']);
            }
            if ($estimate && $final > $estimate * 1.2) {
                return back()->withInput()->withErrors(['final_cost' => 'Biaya akhir melebihi estimasi lebih dari 20 persen. Ubah status ke Menunggu persetujuan dengan estimasi baru, lalu minta persetujuan ulang pelanggan.']);
            }
        }

        $oldStatus = $serviceRequest->status;

        $serviceRequest->update([
            'status' => $data['status'],
            'diagnosis' => $data['diagnosis'] ?? null,
            'estimate_cost' => $estimate,
            'final_cost' => $final,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        if ($oldStatus !== $data['status'] || ! empty($data['note'])) {
            $serviceRequest->log($data['status'], $data['note'] ?? null);
        }

        return back()->with('success', 'Tiket servis diperbarui.');
    }
}
