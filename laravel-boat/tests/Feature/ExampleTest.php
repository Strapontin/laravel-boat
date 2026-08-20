<?php

test('returns a successful response', function () {
    $response = $this->get(route('reservations.index'));

    $response->assertRedirect(route('login'));
});
