<?php

use App\Models\Boat;
use App\Models\User;

test('an admin can move a boat outside the warehouse', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $boat = Boat::create(['color' => 'Rouge', 'position' => 2]);

    $this->actingAs($admin)
        ->post(route('admin.boats.move-outside', $boat))
        ->assertRedirect(route('admin.index'));

    expect($boat->refresh()->position)->toBeNull();
});

test('an admin can move a boat into the next warehouse position', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Boat::create(['color' => 'Rouge', 'position' => 4]);
    $boat = Boat::create(['color' => 'Bleu', 'position' => null]);

    $this->actingAs($admin)
        ->post(route('admin.boats.move-inside', $boat))
        ->assertRedirect(route('admin.index'));

    expect($boat->refresh()->position)->toBe(5);
});

test('an admin moves the first outside boat to position one', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $boat = Boat::create(['color' => 'Rouge', 'position' => null]);

    $this->actingAs($admin)
        ->post(route('admin.boats.move-inside', $boat));

    expect($boat->refresh()->position)->toBe(1);
});