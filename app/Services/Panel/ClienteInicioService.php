<?php

declare(strict_types=1);

namespace App\Services\Panel;

use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Inicio del panel cliente: mismos criterios de filtro que el listado (organización vía creador.id_cliente).
 */
final class ClienteInicioService
{
    /**
     * @return array<string, mixed>
     */
    public function build(Request $request, Usuario $actor): array
    {
        if ($actor->id_cliente === null) {
            return $this->emptyPayload($request, $actor);
        }

        $idCliente = (int) $actor->id_cliente;
        $estadoColors = $this->estadoColorMap();
        $base = $this->baseSolicitudQuery($idCliente);
        $this->applyFiltros($base, $request);

        $byEstado = (clone $base)
            ->selectRaw('estado, COUNT(*) as c')
            ->groupBy('estado')
            ->pluck('c', 'estado');

        $chartEstados = $this->formatEstadoChart($byEstado, $estadoColors);
        $chartServicios = $this->buildServicioBarData(clone $base);
        $chartCiudades = $this->buildCiudadDonutData(clone $base);
        $chartEvolucion = $this->buildMonthTrendSplit(clone $base);

        $perPage = min(max((int) $request->input('per_page', 10), 5), 100);
        /** @var LengthAwarePaginator<int, Solicitud> $solicitudes */
        $solicitudes = (clone $base)
            ->with(['creador.cliente', 'creador', 'servicio', 'serviciosPivote', 'paquete', 'proveedorAsignado'])
            ->withCount(['documentos', 'evaluados'])
            ->orderByDesc('fecha_creacion')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return [
            'bienvenidaNombre' => $this->etiquetaBienvenida($actor),
            'chartEstados' => $chartEstados,
            'chartServicios' => $chartServicios,
            'chartCiudades' => $chartCiudades,
            'chartEvolucion' => $chartEvolucion,
            'solicitudes' => $solicitudes,
            'q' => $request->string('q')->toString(),
            'filtros' => [
                'estado' => $request->input('estado'),
                'desde' => $request->input('desde'),
                'hasta' => $request->input('hasta'),
            ],
            'estados' => $this->estadosForOrg($idCliente),
        ];
    }

    private function emptyPayload(Request $request, Usuario $actor): array
    {
        $empty = ['labels' => ['Sin datos'], 'data' => [0], 'colors' => ['#dee2e6']];
        $emptyBar = ['labels' => ['Sin datos'], 'paquetes' => [0], 'individuales' => [0]];
        $emptyTrend = [
            'labels' => collect(range(0, 11))->map(fn () => '—')->all(),
            'paquetes' => array_fill(0, 12, 0),
            'individuales' => array_fill(0, 12, 0),
        ];
        $solicitudes = new LengthAwarePaginator(
            collect(),
            0,
            10,
            (int) max(1, (int) $request->input('page', 1)),
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'page',
            ],
        );

        return [
            'bienvenidaNombre' => $this->etiquetaBienvenida($actor),
            'chartEstados' => $empty,
            'chartServicios' => $emptyBar,
            'chartCiudades' => $empty,
            'chartEvolucion' => $emptyTrend,
            'solicitudes' => $solicitudes,
            'q' => '',
            'filtros' => ['estado' => null, 'desde' => null, 'hasta' => null],
            'estados' => collect(),
        ];
    }

    private function etiquetaBienvenida(Usuario $actor): string
    {
        $actor->loadMissing('persona');
        if ($actor->persona) {
            $n = trim(implode(' ', array_filter([
                $actor->persona->nombre,
                $actor->persona->paterno,
                trim((string) ($actor->persona->materno ?? '')) !== '' ? trim((string) $actor->persona->materno) : null,
            ])));
            if ($n !== '') {
                return $n;
            }
        }

        return (string) $actor->usuario;
    }

    private function baseSolicitudQuery(int $idCliente): Builder
    {
        return Solicitud::query()
            ->where('activo', 1)
            ->whereHas('creador', static function (Builder $u) use ($idCliente): void {
                $u->where('id_cliente', $idCliente);
            });
    }

    private function applyFiltros(Builder $q, Request $request): void
    {
        if ($request->filled('estado')) {
            $q->where('estado', $request->string('estado'));
        }
        if ($request->filled('desde')) {
            $q->whereDate('fecha_creacion', '>=', $request->date('desde'));
        }
        if ($request->filled('hasta')) {
            $q->whereDate('fecha_creacion', '<=', $request->date('hasta'));
        }
        if ($request->filled('q')) {
            $raw = $request->string('q')->trim();
            if ($raw !== '') {
                $like = '%'.$raw.'%';
                $q->where(function (Builder $w) use ($like, $raw): void {
                    $w->where('solicitudes.nombres', 'like', $like)
                        ->orWhere('solicitudes.apellidos', 'like', $like)
                        ->orWhere('solicitudes.numero_documento', 'like', $like)
                        ->orWhere('solicitudes.tipo_identificacion', 'like', $like)
                        ->orWhere('solicitudes.ciudad_solicitud_servicio', 'like', $like)
                        ->orWhere('solicitudes.ciudad_residencia_evaluado', 'like', $like)
                        ->orWhere('solicitudes.estado', 'like', $like)
                        ->orWhereHas('creador', function (Builder $c) use ($like): void {
                            $c->where('t_usuarios.usuario', 'like', $like);
                        });
                    if (preg_match('/^\d+$/', (string) $raw) === 1) {
                        $w->orWhere('solicitudes.id', (int) $raw);
                    }
                });
            }
        }
    }

    private function estadosForOrg(int $idCliente): Collection
    {
        return $this->baseSolicitudQuery($idCliente)
            ->select('estado')
            ->distinct()
            ->orderBy('estado')
            ->pluck('estado');
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>, colors: array<int, string>}
     */
    private function formatEstadoChart(Collection $byEstado, array $estadoColors): array
    {
        $labels = [];
        $data = [];
        $colors = [];
        foreach ($byEstado as $estado => $count) {
            $labels[] = (string) $estado;
            $data[] = (int) $count;
            $key = mb_strtolower((string) $estado);
            $colors[] = $estadoColors[$key] ?? '#6c757d';
        }
        if ($labels === []) {
            $labels = ['Sin datos'];
            $data = [0];
            $colors = ['#dee2e6'];
        }

        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }

    /**
     * @return array{labels: array<int, string>, paquetes: array<int, int>, individuales: array<int, int>}
     */
    private function buildMonthTrendSplit(Builder $base): array
    {
        $start = now()->subMonths(11)->startOfMonth();
        $expr = $this->sqlYearMonth('fecha_creacion');

        $rowsPaq = (clone $base)
            ->whereNotNull('paquete_id')
            ->selectRaw("{$expr} as ym, COUNT(*) as c")
            ->where('fecha_creacion', '>=', $start)
            ->groupBy(DB::raw($expr))
            ->orderBy(DB::raw($expr))
            ->pluck('c', 'ym');

        $rowsInd = (clone $base)
            ->whereNull('paquete_id')
            ->selectRaw("{$expr} as ym, COUNT(*) as c")
            ->where('fecha_creacion', '>=', $start)
            ->groupBy(DB::raw($expr))
            ->orderBy(DB::raw($expr))
            ->pluck('c', 'ym');

        $labels = [];
        $paquetes = [];
        $individuales = [];
        for ($i = 0; $i < 12; $i++) {
            $d = $start->copy()->addMonths($i);
            $key = $d->format('Y-m');
            $labels[] = $d->locale(app()->getLocale())->translatedFormat('M Y');
            $paquetes[] = (int) ($rowsPaq[$key] ?? 0);
            $individuales[] = (int) ($rowsInd[$key] ?? 0);
        }

        return ['labels' => $labels, 'paquetes' => $paquetes, 'individuales' => $individuales];
    }

    /**
     * @return array{labels: array<int, string>, paquetes: array<int, int>, individuales: array<int, int>}
     */
    private function buildServicioBarData(Builder $base): array
    {
        $rows = (clone $base)
            ->with(['servicio', 'paquete', 'serviciosPivote'])
            ->get(['id', 'servicio_id', 'paquete_id']);
        $paq = [];
        $ind = [];
        foreach ($rows as $s) {
            if ($s->serviciosPivote->isNotEmpty()) {
                foreach ($s->serviciosPivote as $svc) {
                    $l = trim((string) ($svc->nombre ?? '')) ?: 'Servicios';
                    $ind[$l] = ($ind[$l] ?? 0) + 1;
                }

                continue;
            }
            if ($s->paquete_id !== null) {
                $l = trim((string) ($s->paquete?->nombre ?? '')) ?: 'Paquetes de servicios';
                $paq[$l] = ($paq[$l] ?? 0) + 1;
            } else {
                $l = trim((string) ($s->servicio?->nombre ?? '')) ?: 'Servicios individuales';
                $ind[$l] = ($ind[$l] ?? 0) + 1;
            }
        }
        $union = collect(array_unique(array_merge(array_keys($paq), array_keys($ind))))
            ->sortByDesc(fn (string $l): int => (int) (($paq[$l] ?? 0) + ($ind[$l] ?? 0)))
            ->values();
        $maxBars = 12;
        if ($union->isEmpty()) {
            return [
                'labels' => ['Sin datos'],
                'paquetes' => [0],
                'individuales' => [0],
            ];
        }
        $labels = [];
        $dataPaquetes = [];
        $dataInd = [];
        if ($union->count() <= $maxBars) {
            foreach ($union as $l) {
                $labels[] = $l;
                $dataPaquetes[] = (int) ($paq[$l] ?? 0);
                $dataInd[] = (int) ($ind[$l] ?? 0);
            }
        } else {
            foreach ($union->take($maxBars - 1) as $l) {
                $labels[] = $l;
                $dataPaquetes[] = (int) ($paq[$l] ?? 0);
                $dataInd[] = (int) ($ind[$l] ?? 0);
            }
            $rp = 0;
            $ri = 0;
            foreach ($union->slice($maxBars - 1) as $l) {
                $rp += (int) ($paq[$l] ?? 0);
                $ri += (int) ($ind[$l] ?? 0);
            }
            $labels[] = 'Otros';
            $dataPaquetes[] = $rp;
            $dataInd[] = $ri;
        }

        return [
            'labels' => $labels,
            'paquetes' => $dataPaquetes,
            'individuales' => $dataInd,
        ];
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>, colors: array<int, string>}
     */
    private function buildCiudadDonutData(Builder $base): array
    {
        $rows = (clone $base)->get(['id', 'ciudad_solicitud_servicio', 'ciudad_residencia_evaluado']);
        $counts = [];
        foreach ($rows as $s) {
            $c = trim((string) ($s->ciudad_solicitud_servicio ?? ''));
            if ($c === '') {
                $c = trim((string) ($s->ciudad_residencia_evaluado ?? ''));
            }
            if ($c === '') {
                $c = 'Sin ciudad';
            }
            if (mb_strlen($c) > 48) {
                $c = mb_substr($c, 0, 45).'…';
            }
            $counts[$c] = ($counts[$c] ?? 0) + 1;
        }
        arsort($counts, SORT_NUMERIC);
        $items = collect($counts);
        if ($items->isEmpty()) {
            return [
                'labels' => ['Sin datos'],
                'data' => [0],
                'colors' => ['#dee2e6'],
            ];
        }
        $top = $items->take(9);
        $otros = (int) $items->slice(9)->sum();
        $labels = $top->keys()->all();
        $data = $top->values()->map(static fn ($v) => (int) $v)->all();
        if ($otros > 0) {
            $labels[] = 'Otros';
            $data[] = $otros;
        }
        $palette = ['#6c9bd1', '#9ca3af', '#0d6efd', '#198754', '#ffc107', '#e83e8c', '#20c997', '#6610f2', '#fd7e14', '#0dcaf0', '#495057', '#adb5bd'];
        $colors = [];
        for ($i = 0, $n = count($labels); $i < $n; $i++) {
            $colors[] = $palette[$i % count($palette)];
        }

        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }

    private function sqlYearMonth(string $column): string
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            return "DATE_FORMAT({$column}, '%Y-%m')";
        }

        return "strftime('%Y-%m', {$column})";
    }

    /**
     * @return array<string, string>
     */
    private function estadoColorMap(): array
    {
        return [
            'nuevo' => '#0d3b66',
            'en proceso' => '#ffc107',
            'asignada' => '#17a2b8',
            'completado' => '#28a745',
            'cancelado' => '#e83e8c',
            'registrado' => '#6c757d',
        ];
    }
}
