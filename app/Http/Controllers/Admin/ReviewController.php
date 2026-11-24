<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['user', 'product'])
            ->when($request->query('bintang'), fn ($q, $v) => $q->where('rating', (int) $v))
            ->when($request->query('status') === 'tampil', fn ($q) => $q->where('is_hidden', false))
            ->when($request->query('status') === 'disembunyikan', fn ($q) => $q->where('is_hidden', true))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'total' => Review::count(),
            'avg' => round((float) Review::visible()->avg('rating'), 1),
        ]);
    }

    public function toggle(Review $review)
    {
        $review->update(['is_hidden' => ! $review->is_hidden, 'is_featured' => $review->is_hidden ? $review->is_featured : false]);

        return back()->with('success', $review->is_hidden ? 'Ulasan disembunyikan.' : 'Ulasan ditampilkan kembali.');
    }


    public function toggleFeatured(Review $review)
    {
        if ($review->is_hidden) {
            return back()->with('error', 'Ulasan yang disembunyikan tidak dapat dipilih untuk beranda.');
        }

        $review->update(['is_featured' => ! $review->is_featured]);

        return back()->with('success', $review->is_featured
            ? 'Ulasan ditambahkan ke pilihan beranda.'
            : 'Ulasan dihapus dari pilihan beranda.');
    }

    public function reply(Request $request, Review $review)
    {
        $data = $request->validate(['admin_reply' => ['nullable', 'string', 'max:1000']]);
        $review->update(['admin_reply' => $data['admin_reply'] ?? null]);

        return back()->with('success', 'Balasan disimpan.');
    }

    public function destroy(Review $review)
    {
        if ($review->photo) {
            Storage::disk('public')->delete($review->photo);
        }

        $review->delete();

        return back()->with('success', 'Ulasan dihapus.');
    }
}
