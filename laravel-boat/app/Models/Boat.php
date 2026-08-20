<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Boat extends Model
{
    protected $fillable = ['position'];

    /**
     * Un bateau peut avoir plusieurs réservations
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}