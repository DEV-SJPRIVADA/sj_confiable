<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Models\CatServicio;
use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InformesController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $this->authorize('viewAny', Solicitud::class);

        $perPage = (int) $request->input('per_page', 50);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 50;
        }

        $query = $this->buildInformesQuery($request)
            ->orderByDesc('solicitudes.fecha_creacion')
            ->orderByDesc('solicitudes.id');

        $solicitudes = $query
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        $servicios = CatServicio::query()->orderBy('nombre')->get();

        return view('panel.consultor.informes.index', [
            'solicitudes' => $solicitudes,
            'servicios' => $servicios,
            'perPage' => $perPage,
            'filtros' => $request->only(['estado', 'desde', 'hasta', 'servicio_id', 'per_page']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Solicitud::class);

        $fileName = 'informe_solicitudes_'.now()->format('Y-m-d_His').'.csv';

        $query = $this->buildInformesQuery($request)
            ->orderByDesc('solicitudes.fecha_creacion')
            ->orderByDesc('solicitudes.id');

        $servicioTexto = static function (Solicitud $s): string {
            if ($s->serviciosPivote->isNotEmpty()) {
                return $s->serviciosPivote->pluck('nombre')->implode(', ');
            }

            return (string) ($s->servicio?->nombre ?? '');
        };

        return response()->streamDownload(function () use ($query, $servicioTexto): void {
            $out = fopen('php://output', 'wb');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Cliente', 'Documento', 'Nombres', 'Fecha creación', 'Estado', 'Servicio'], ';');
            foreach ($query->cursor() as $s) {
                $nombreCompleto = mb_strtoupper(trim((string) (($s->nombres ?? '').' '.($s->apellidos ?? ''))), 'UTF-8');
                $doc = trim((string) ($s->tipo_identificacion ?? '')).' '.trim((string) ($s->numero_documento ?? ''));
                $fc = $s->fecha_creacion?->format('d/m/Y, H:i') ?? '';
                fputcsv($out, [
                    (string) ($s->creador?->cliente?->razon_social ?? ''),
                    trim($doc),
                    $nombreCompleto,
                    $fc,
                    (string) ($s->estado ?? ''),
                    $servicioTexto($s),
                ], ';');
            }
            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildInformesQuery(Request $request): Builder
    {
        $query = Solicitud::query()
            ->where('solicitudes.activo', 1)
            ->with(['creador.cliente', 'servicio', 'serviciosPivote']);

        if ($request->filled('estado')) {
            $query->where('solicitudes.estado', $request->string('estado'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('solicitudes.fecha_creacion', '>=', $request->date('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('solicitudes.fecha_creacion', '<=', $request->date('hasta'));
        }

        if ($request->filled('servicio_id')) {
            $sid = (int) $request->input('servicio_id');
            $query->where(function ($q) use ($sid): void {
                $q->where('solicitudes.servicio_id', $sid)
                    ->orWhereHas(
                        'serviciosPivote',
                        static fn ($rel) => $rel->wherePivot('servicio_id', $sid),
                    );
            });
        }

        return $query;
    }
}
