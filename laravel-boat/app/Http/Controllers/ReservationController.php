<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boat;
use App\Models\Reservation;

class ReservationController extends Controller
{
    // Récupére les infos en base et les envoies au front
    public function index(Request $request)
    {
        $startDate = now()->startOfDay();
        $dates = collect(range(1, 7))->map(
            fn(int $offset) => $startDate->copy()->addDays($offset)->toDateString()
        );

        $boats = Boat::with([
            'reservations' => function ($query) use ($dates) {
                $query->whereBetween('date', [$dates->first(), $dates->last()]);
            }
        ])->get();

        return view('reservations.index', compact('boats', 'dates'));
    }

    public function store(Request $request)
    {
        // Check que les données soient valides
        $validated = $request->validate([
            'boat_id' => 'required|exists:boats,id',
            'date' => 'required|date',
            'slot' => 'required|in:morning,afternoon',
        ]);

        // Vérifie que le créneau soit dispo
        $exists = Reservation::where('boat_id', $validated['boat_id'])
            ->where('date', $validated['date'])
            ->where('slot', $validated['slot'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ce créneau n\'est pas disponible.');
        }

        Reservation::create([
            'user_id' => auth()->id(),
            'boat_id' => $validated['boat_id'],
            'date' => $validated['date'],
            'slot' => $validated['slot'],
        ]);

        return redirect()->route('reservations.index', ['date' => $validated['date']])
            ->with('success', 'Réservation confirmée.');
    }
}
