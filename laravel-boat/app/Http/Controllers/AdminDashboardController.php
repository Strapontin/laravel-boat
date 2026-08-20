<?php

namespace App\Http\Controllers;

use App\Models\Boat;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $boats = Boat::whereNotNull('position')
            ->orderByDesc('position')
            ->get();
        $boatsOutsideWarehouse = Boat::whereNull('position')
            ->orderBy('id')
            ->get();

        return view('admin.index', compact('boats', 'boatsOutsideWarehouse'));
    }
}
