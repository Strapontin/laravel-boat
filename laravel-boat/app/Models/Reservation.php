<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = ['user_id', 'boat_id', 'date', 'slot'];

    /**
     * La réservation appartient à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * La réservation appartient à un bateau
     */
    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class);
    }
}