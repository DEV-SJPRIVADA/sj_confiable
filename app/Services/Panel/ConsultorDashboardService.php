<?php

declare(strict_types=1);

namespace App\Services\Panel;

use App\Models\CatServicio;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Solicitud;
use App\Models\SolicitudUsuario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ConsultorDashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function build(Request $request): array
    {
        $estadoColors = $this->estadoColorMap();

        $base = Solicitud::query()->where('activo', true);
        $this->applySolicitudFilters($base, $request);

        $byEstado = (clone $base)
            ->selectRaw('estado, COUNT(*) as c')
            ->groupBy('estado')
            ->pluck('c', 'estado');

        $chartEstados = $this->formatEstadoChart($byEstado, $estadoColors);
        $chartServicios = $this->buildServicioBarData(clone $base);
        $chartEmpresas = $this->buildEmpresaDonutData(clone $base);
        $chartEvolucion = $this->buildMonthTrendSplit(clone $base);
        $recentSolicitudes = (clone $base)
            ->with(['creador.cliente', 'servicio', 'paquete'])
            ->orderByDesc('fecha_creacion')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $solUsuQuery = SolicitudUsuario::query()
            ->with(['cliente', 'solicitante'])
            ->orderByDesc('fecha_solicitud')
            ->orderByDesc('id_solicitud');

        if ($request->filled('id_cliente')) {
            $cid = (int) $request->input('id_cliente');
            $solUsuQuery->where('id_cliente', $cid);
        }

        $recentSolicitudesUsuario = $solUsuQuery->limit(5)->get();

        $estadosList = Solicitud::query()
            ->where('activo', true)
            ->distinct()
            ->orderBy('estado')
            ->pluck('estado');

        $countSolicitudesFiltradas = (clone $base)->count();

        return [
            'countUsuarios' => Usuario::query()->where('activo', 1)->count(),
            'countClientes' => Cliente::query()->where('activo', 1)->count(),
            'countAsociados' => Proveedor::query()->count(),
            'countSolicitudesActivas' => $countSolicitudesFiltradas,
            'countSolicitudesUsuarioPendientes' => SolicitudUsuario::query()->where('estado', 'Pendiente')->count(),
            'clientes' => Cliente::query()->orderBy('razon_social')->get(['id_cliente', 'razon_social']),
            'servicios' => CatServicio::query()->orderBy('nombre')->get(),
            'estados' => $estadosList,
            'filtros' => [
                'id_cliente' => $request->input('id_cliente'),
                'servicio_id' => $request->input('servicio_id'),
                'estado' => $request->input('estado'),
                'desde' => $request->input('desde'),
                'hasta' => $request->input('hasta'),
            ],
            'chartEstados' => $chartEstados,
            'chartServicios' => $chartServicios,
            'chartEmpresas' => $chartEmpresas,
            'chartEvolucion' => $chartEvolucion,
            'recentSolicitudes' => $recentSolicitudes,
            'recentSolicitudesUsuario' => $recentSolicitudesUsuario,
        ];
    }

    private function applySolicitudFilters(Builder $q, Request $request): void
    {
        if ($request->filled('id_cliente')) {
            $id = (int) $request->input('id_cliente');
            $q->whereHas('creador', static function (Builder $u) use ($id): void {
                $u->where('id_cliente', $id);
            });
        }

        if ($request->filled('estado')) {
            $q->where('estado', $request->string('estado'));
        }

        if ($request->filled('desde')) {
            $q->whereDate('fecha_creacion', '>=', $request->date('desde'));
        }

        if ($request->filled('hasta')) {
            $q->whereDate('fecha_creacion', '<=', $request->date('hasta'));
        }

        if ($request->filled('servicio_id')) {
            $sid = (int) $request->input('servicio_id');
            $q->where(function (Builder $b) use ($sid): void {
                $b->where('solicitudes.servicio_id', $sid)
                    ->orWhereHas(
                        'serviciosPivote',
                        static function ($rel) use ($sid): void {
                            $rel->wherePivot('servicio_id', $sid);
                        }
                    );
            });
        }
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
        $rows = (clone $base)->with(['servicio', 'paquete'])->get(['id', 'servicio_id', 'paquete_id']);
        $paq = [];
        $ind = [];
        foreach ($rows as $s) {
            if ($s->paquete_id !== null) {
                $l = trim((string) ($s->paquete?->nombre ?? '')) ?: 'Paquetes';
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
        $labels = [];
        $dataPaquetes = [];
        $dataInd = [];
        if ($union->isEmpty()) {
            return [
                'labels' => ['Sin datos'],
                'paquetes' => [0],
                'individuales' => [0],
            ];
        }
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
    private function buildEmpresaDonutData(Builder $base): array
    {
        $rows = (clone $base)->with(['creador.cliente'])->get(['id', 'empresa_solicitante', 'usuario_id']);
        $counts = [];
        foreach ($rows as $s) {
            $e = trim((string) $s->empresa_solicitante);
            if ($e === '') {
                $e = trim((string) ($s->creador?->cliente?->razon_social ?? ''));
            }
            if ($e === '') {
                $e = 'Sin empresa';
            }
            if (mb_strlen($e) > 48) {
                $e = mb_substr($e, 0, 45).'…';
            }
            $counts[$e] = ($counts[$e] ?? 0) + 1;
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
        if ($labels === []) {
            return [
                'labels' => ['Sin datos'],
                'data' => [0],
                'colors' => ['#dee2e6'],
            ];
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
