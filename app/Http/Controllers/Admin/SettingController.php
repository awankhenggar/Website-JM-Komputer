<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'about' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:300'],
            'hours' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'regex:/^[0-9]{8,15}$/'],
            'maps_embed' => ['nullable', 'url', 'max:1000'],
            'maps_link' => ['nullable', 'url', 'max:500'],
            'bank_info' => ['nullable', 'string', 'max:300'],
            'payment_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'return_days' => ['required', 'integer', 'min:0', 'max:30'],
            'warranty_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $old = Setting::where('key', 'logo')->value('value');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $data['logo'] = $request->file('logo')->store('settings', 'public');
        } else {
            unset($data['logo']);
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('store_settings');

        return back()->with('success', 'Pengaturan toko disimpan.');
    }
}
