<?php

use App\Models\Boat;
use App\Models\Reservation;
use App\Models\User;

test('a reservation belongs to its user and boat', function () {
    $user = User::factory()->create();
    $boat = Boat::forceCreate([
        'color' => 'Rouge',
        'position' => 1,
    ]);

    $reservation = Reservation::create([
        'user_id' => $user->id,
        'boat_id' => $boat->id,
        'date' => now()->addDay()->toDateString(),
        'slot' => 'morning',
    ]);

    expect($reservation->user->is($user))->toBeTrue()
        ->and($reservation->boat->is($boat))->toBeTrue();
});

test('a user can have multiple reservations', function () {
    $user = User::factory()->create();
    $firstBoat = Boat::forceCreate(['color' => 'Rouge', 'position' => 1]);
    $secondBoat = Boat::forceCreate(['color' => 'Bleu', 'position' => 2]);

    $firstReservation = Reservation::create([
        'user_id' => $user->id,
        'boat_id' => $firstBoat->id,
        'date' => now()->addDay()->toDateString(),
        'slot' => 'morning',
    ]);
    $secondReservation = Reservation::create([
        'user_id' => $user->id,
        'boat_id' => $secondBoat->id,
        'date' => now()->addDays(2)->toDateString(),
        'slot' => 'afternoon',
    ]);

    expect($user->reservations)->toHaveCount(2)
        ->and($user->reservations->pluck('id')->all())
        ->toEqual([$firstReservation->id, $secondReservation->id]);
});

test('reservation store rejects invalid data', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('reservations.store'), [
            'boat_id' => 999,
            'date' => 'not-a-date',
            'slot' => 'evening',
        ])
        ->assertSessionHasErrors(['boat_id', 'date', 'slot']);
});

test('reservation store rejects a slot that is already booked', function () {
    $user = User::factory()->create();
    $boat = Boat::forceCreate(['color' => 'Rouge', 'position' => 1]);
    $date = now()->addDay()->toDateString();

    Reservation::create([
        'user_id' => $user->id,
        'boat_id' => $boat->id,
        'date' => $date,
        'slot' => 'morning',
    ]);

    $this->actingAs($user)
        ->post(route('reservations.store'), [
            'boat_id' => $boat->id,
            'date' => $date,
            'slot' => 'morning',
        ])
        ->assertSessionHas('error', 'Ce créneau n\'est pas disponible.');

    expect(Reservation::where('boat_id', $boat->id)->count())->toBe(1);
});

test('reservation store creates a reservation with valid data', function () {
    $user = User::factory()->create();
    $boat = Boat::forceCreate(['color' => 'Bleu', 'position' => 2]);
    $date = now()->addDay()->toDateString();

    $this->actingAs($user)
        ->post(route('reservations.store'), [
            'boat_id' => $boat->id,
            'date' => $date,
            'slot' => 'afternoon',
        ])
        ->assertRedirect(route('reservations.index', ['date' => $date]))
        ->assertSessionHas('success', 'Réservation confirmée.');

    $this->assertDatabaseHas('reservations', [
        'user_id' => $user->id,
        'boat_id' => $boat->id,
        'date' => $date,
        'slot' => 'afternoon',
    ]);
});
