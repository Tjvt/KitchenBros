<?php

it('lets a guest join with only a name', function () {
    $this->post(route('lunch.store'), ['name' => '  Alex  ', 'email' => 'ignored@example.com'])
        ->assertRedirect(route('home'))->assertSessionHas('lunch_registration_id');

    $this->assertDatabaseHas('lunch_registrations', ['name' => 'Alex']);
    $this->assertDatabaseCount('lunch_registrations', 1);
});

it('does not duplicate a registration from the same session', function () {
    $this->post(route('lunch.store'), ['name' => 'Alex']);

    $this->post(route('lunch.store'), ['name' => 'Alex'])->assertRedirect(route('home'));

    $this->assertDatabaseCount('lunch_registrations', 1);
});

it('rejects invalid names', function ($name, $message) {
    $this->post(route('lunch.store'), ['name' => $name])->assertSessionHasErrors(['name' => $message]);

    $this->assertDatabaseCount('lunch_registrations', 0);
})->with([
    ['', 'Verrätst du uns noch deinen Namen?'],
    [str_repeat('a', 81), 'Dein Name darf höchstens 80 Zeichen lang sein.'],
    [['Alex'], 'Bitte gib einen gültigen Namen ein.'],
]);
