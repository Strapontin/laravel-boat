<?php

use App\Models\Boat;
use App\Models\Reservation;
use App\Models\User;

test('an admin can move a boat outside the warehouse', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $boat = Boat::forceCreate(['color' => 'Rouge', 'position' => 2]);

    $this->actingAs($admin)
        ->post(route('admin.boats.move-outside', $boat))
        ->assertRedirect(route('admin.index', ['reorganize' => 1]));

    expect($boat->refresh()->position)->toBeNull();
});

test('an admin can move a boat into the next warehouse position', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Boat::forceCreate(['color' => 'Rouge', 'position' => 4]);
    $boat = Boat::forceCreate(['color' => 'Bleu', 'position' => null]);

    $this->actingAs($admin)
        ->post(route('admin.boats.move-inside', $boat))
        ->assertRedirect(route('admin.index', ['reorganize' => 1]));

    expect($boat->refresh()->position)->toBe(5);
});

test('an admin moves the first outside boat to position one', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $boat = Boat::forceCreate(['color' => 'Rouge', 'position' => null]);

    $this->actingAs($admin)
        ->post(route('admin.boats.move-inside', $boat));

    expect($boat->refresh()->position)->toBe(1);
});

test('the admin dashboard keeps reorganization mode after moving a boat', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $boat = Boat::forceCreate(['color' => 'Rouge', 'position' => 2]);

    $this->actingAs($admin)
        ->post(route('admin.boats.move-outside', $boat))
        ->assertRedirect(route('admin.index', ['reorganize' => 1]));
});

test('the admin dashboard shows the lock for reserved boats outside the warehouse', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $boat = Boat::forceCreate(['color' => 'Rouge', 'position' => null]);
    Reservation::create([
        'user_id' => $admin->id,
        'boat_id' => $boat->id,
        'date' => now()->addDay()->toDateString(),
        'slot' => 'morning',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.index'))
        ->assertOk()
        ->assertSee('images/Lock.png');
});