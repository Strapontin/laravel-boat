<?php

use App\Models\Boat;
use App\Models\Reservation;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('reservations.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit reservations', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('reservations.index'));
    $response->assertOk();
});

test('reservation index provides ordered boats and only reservations for the next seven days', function () {
    $user = User::factory()->create();
    $lowerPositionBoat = Boat::forceCreate(['color' => 'Rouge', 'position' => 1]);
    $higherPositionBoat = Boat::forceCreate(['color' => 'Bleu', 'position' => 2]);
    $inRangeDate = now()->addDays(3)->toDateString();
    $outOfRangeDate = now()->addDays(8)->toDateString();

    Reservation::create([
        'user_id' => $user->id,
        'boat_id' => $higherPositionBoat->id,
        'date' => $inRangeDate,
        'slot' => 'morning',
    ]);
    Reservation::create([
        'user_id' => $user->id,
        'boat_id' => $higherPositionBoat->id,
        'date' => $outOfRangeDate,
        'slot' => 'afternoon',
    ]);
    Reservation::create([
        'user_id' => $user->id,
        'boat_id' => $lowerPositionBoat->id,
        'date' => $inRangeDate,
        'slot' => 'afternoon',
    ]);

    $response = $this->actingAs($user)->get(route('reservations.index'));

    $response->assertOk()
        ->assertViewHas('dates', fn($dates) => $dates->count() === 7);

    $boats = $response->viewData('boats');

    expect($boats->pluck('position')->all())->toEqual([1, 2]);
    expect($boats->first()->reservations)->toHaveCount(1);
    expect($boats->first()->reservations->first()->slot)->toBe('afternoon');
    expect($boats->last()->reservations)->toHaveCount(1);
    expect((string) $boats->last()->reservations->first()->date)->toBe($inRangeDate);
    expect($boats->last()->reservations->first()->slot)->toBe('morning');
});
