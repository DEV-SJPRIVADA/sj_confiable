<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Cliente;

use App\Http\Controllers\Controller;
use App\Models\CatServicio;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportacionMasivaController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Solicitud::class);

        $modoQuery = $request->query('modo');
        $modo = $modoQuery === 'evaluados' ? 'evaluados' : 'solicitudes';

        return view('panel.cliente.importar.index', [
            'modo' => $modo,
            'servicios' => CatServicio::query()->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Solicitud::class);

        $modo = (string) $request->input('modo');
        $rules = [
            'modo' => ['required', Rule::in(['solicitudes', 'evaluados'])],
            'archivo' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ];
        if ($modo === 'evaluados') {
            $rules['servicio_id'] = ['required', 'integer', 'exists:t_cat_servicio,id_servicio'];
        }

        $request->validate($rules, [
            'archivo.required' => 'Seleccione un archivo Excel (.xlsx).',
            'archivo.mimes' => 'El archivo debe ser Excel (.xlsx o .xls).',
            'archivo.max' => 'El archivo no puede superar 10 MB.',
            'servicio_id.required' => 'Seleccione el servicio para la nueva solicitud.',
        ]);

        return redirect()
            ->route('panel.cliente.importar', ['modo' => $modo])
            ->with('status', 'Archivo recibido. El procesamiento masivo se activará cuando el módulo esté conectado al motor de importación.');
    }

    /**
     * Plantilla CSV (abre en Excel y puede guardarse como .xlsx).
     */
    public function plantillaSolicitudes(): StreamedResponse
    {
        $this->authorize('viewAny', Solicitud::class);

        return $this->csvDescarga(
            'plantilla_importacion_solicitudes.csv',
            [
                'Empresa',
                'NIT',
                'Servicio',
                'CiudadPresta',
                'CiudadSolic',
                'Nombres',
                'Apellidos',
                'TipoID',
                'NumDoc',
                'FechaExp',
                'LugarExp',
                'TelFijo',
                'Celular',
                'CiudadResidencia',
                'Direccion',
                'Comentarios',
            ],
        );
    }

    public function plantillaEvaluados(): StreamedResponse
    {
        $this->authorize('viewAny', Solicitud::class);

        return $this->csvDescarga(
            'plantilla_importacion_evaluados.csv',
            [
                'Nombres',
                'Apellidos',
                'TipoID',
                'NumDoc',
                'FechaExp',
                'LugarExp',
                'TelFijo',
                'Celular',
                'CiudadResidencia',
                'Direccion',
                'Cargo',
            ],
        );
    }

    /**
     * @param  list<string>  $cabeceras
     */
    private function csvDescarga(string $nombreArchivo, array $cabeceras): StreamedResponse
    {
        return response()->streamDownload(function () use ($cabeceras): void {
            echo "\xEF\xBB\xBF";
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fputcsv($out, $cabeceras, ';');
            fclose($out);
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
