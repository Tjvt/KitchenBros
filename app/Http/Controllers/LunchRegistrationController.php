<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLunchRegistrationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class LunchRegistrationController extends Controller
{
    public function store(StoreLunchRegistrationRequest $request): RedirectResponse
    {
        if (! $request->session()->has('lunch_registration_id')) {
            $id = DB::table('lunch_registrations')->insertGetId([
                'name' => $request->validated('name'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $request->session()->put('lunch_registration_id', $id);
        }

        return to_route('home');
    }
}
