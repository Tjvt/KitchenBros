<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('the configured admin sees the lunch registrations', function () {
    config(['admin.email' => 'admin@example.com']);
    $admin = User::factory()->create(['email' => 'admin@example.com']);
    DB::table('lunch_registrations')->insert([
        'name' => 'Alex',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('registrationCount', 1)
            ->where('registrations.0.name', 'Alex'));
});

test('authenticated users without the configured admin email receive 403', function () {
    config(['admin.email' => 'admin@example.com']);
    $user = User::factory()->create(['email' => 'colleague@example.com']);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});

test('the dashboard stays closed when no admin email is configured', function () {
    config(['admin.email' => null]);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});
