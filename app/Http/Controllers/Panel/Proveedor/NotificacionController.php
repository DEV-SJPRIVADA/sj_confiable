<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Proveedor;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarcarNotificacionesProveedorRequest;
use App\Services\Panel\NotificacionProveedorService;
use Illuminate\Http\RedirectResponse;

class NotificacionController extends Controller
{
    public function marcarLeidas(
        MarcarNotificacionesProveedorRequest $request,
        NotificacionProveedorService $service,
    ): RedirectResponse {
        $ids = $request->input('ids', []);
        if (! is_array($ids)) {
            $ids = [];
        }
        $todas = $request->boolean('todas');
        $service->marcarLeidas($request->user(), array_map('intval', $ids), $todas);

        return redirect()->back()->with('status', 'Notificaciones actualizadas.');
    }
}
