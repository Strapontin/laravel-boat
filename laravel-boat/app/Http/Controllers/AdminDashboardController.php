<?php

namespace App\Http\Controllers;

use App\Models\Boat;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $boats = Boat::whereNotNull('position')
            ->with([
                'reservations' => function ($query) use ($today) {
                    $query->whereDate('date', '>=', $today)
                        ->orderBy('date')
                        ->orderByRaw("CASE WHEN slot = 'morning' THEN 0 ELSE 1 END");
                }
            ])
            ->orderByDesc('position')
            ->get();
        $boatsOutsideWarehouse = Boat::whereNull('position')
            ->with([
                'reservations' => function ($query) use ($today) {
                    $query->whereDate('date', '>=', $today)
                        ->orderBy('date')
                        ->orderByRaw("CASE WHEN slot = 'morning' THEN 0 ELSE 1 END");
                }
            ])
            ->orderBy('id')
            ->get();

        return view('admin.index', compact('boats', 'boatsOutsideWarehouse'));
    }

    public function moveOutside(Boat $boat)
    {
        $boat->update(['position' => null]);

        return to_route('admin.index', ['reorganize' => 1]);
    }

    public function moveInside(Boat $boat)
    {
        $nextPosition = ((int) Boat::whereNotNull('position')->max('position')) + 1;

        $boat->update(['position' => $nextPosition]);

        return to_route('admin.index', ['reorganize' => 1]);
    }
}
