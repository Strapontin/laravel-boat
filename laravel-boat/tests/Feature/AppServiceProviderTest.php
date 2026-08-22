<?php

use Illuminate\Validation\Rules\Password;

test('production password defaults require a strong password', function () {
    $this->app->detectEnvironment(fn() => 'production');

    $rules = Password::defaults()->toPasswordRulesString();

    expect($rules)->toBe(
        'minlength: 12; required: lower; required: upper; required: digit; required: special;',
    );
});