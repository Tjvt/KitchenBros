<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LunchRegistrationController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'registrations' => DB::table('lunch_registrations')
                ->select(['id', 'name', 'created_at'])
                ->orderByDesc('created_at')
                ->get(),
            'registrationCount' => DB::table('lunch_registrations')->count(),
            'todayCount' => DB::table('lunch_registrations')
                ->whereDate('created_at', today())
                ->count(),
        ]);
    }
}
