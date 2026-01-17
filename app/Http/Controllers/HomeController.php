<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Kategori;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage with events and categories.
     */
    public function index(Request $request)
    {
        // Get all categories untuk filter pills
        $categories = Kategori::all();

        // Buat event query dengan relasi kategori dan tikets
        $eventsQuery = Event::with(['kategori', 'tikets']);

        // Filter berdasarkan kategori jika ada
        if ($request->filled('kategori')) {
            $eventsQuery->where('kategori_id', $request->kategori);
        }

        // filter berdasarkan pencarian
        if ($request->filled('q')) {
            $q = $request->q;

            $eventsQuery->where(function ($query) use ($q) {
                $query->where('judul', 'like', "%{$q}%");
            });
        }

        // Get events
        $events = $eventsQuery->get()->map(function ($event) {
            // harga tiket termurah ke setiap event
            $event->tikets_min_harga = $event->tikets->min('harga') ?? 0;
            return $event;
        });

        return view('home', [
            'categories' => $categories,
            'events'     => $events,
        ]);
    }
}
