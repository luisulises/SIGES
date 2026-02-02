<?php

namespace App\Http\Controllers;

use App\Models\EstadoTicket;
use App\Models\Sistema;
use Inertia\Inertia;
use Inertia\Response;

class BusquedaController extends Controller
{
    public function index(): Response
    {
        $estados = EstadoTicket::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $sistemas = Sistema::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'activo']);

        return Inertia::render('Search', [
            'catalogs' => [
                'estados' => $estados,
                'sistemas' => $sistemas,
            ],
        ]);
    }
}

