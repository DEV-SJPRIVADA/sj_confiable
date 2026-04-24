<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Models\CatServicio;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InformesController extends Controller
{
    public function index(Request $request): View
    {
        $query = Solicitud::query()
            ->where('activo', 1)
            ->with(['creador.cliente', 'servicio', 'serviciosPivote']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha_creacion', '>=', $request->date('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha_creacion', '<=', $request->date('hasta'));
        }

        if ($request->filled('servicio_id')) {
            $sid = (int) $request->input('servicio_id');
            $query->where(function ($q) use ($sid) {
                $q->where('solicitudes.servicio_id', $sid)
                    ->orWhereHas(
                        'serviciosPivote',
                        static fn ($rel) => $rel->wherePivot('servicio_id', $sid),
                    );
            });
        }

        $solicitudes = $query
            ->orderByDesc('fecha_creacion')
            ->paginate(50)
            ->withQueryString();

        $servicios = CatServicio::query()->orderBy('nombre')->get();

        return view('panel.consultor.informes.index', [
            'solicitudes' => $solicitudes,
            'servicios' => $servicios,
            'filtros' => $request->only(['estado', 'desde', 'hasta', 'servicio_id']),
        ]);
    }
}
