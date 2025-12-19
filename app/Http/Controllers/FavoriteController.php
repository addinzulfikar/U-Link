<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favorites()->with('umkm')->latest()->paginate(12);
        return view('favorites.index', compact('favorites'));
    }

    public function toggle($umkmId)
    {
        $umkm = Umkm::findOrFail($umkmId);

        $favorite = Favorite::where('user_id', Auth::id())
            ->where('umkm_id', $umkmId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'UMKM dihapus dari favorit.';
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'umkm_id' => $umkmId,
            ]);
            $message = 'UMKM ditambahkan ke favorit.';
        }

        if (request()->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }
}
