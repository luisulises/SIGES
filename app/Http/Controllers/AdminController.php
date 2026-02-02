<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function users(): Response
    {
        return Inertia::render('Admin/Users');
    }

    public function catalogs(): Response
    {
        return Inertia::render('Admin/Catalogs');
    }
}

