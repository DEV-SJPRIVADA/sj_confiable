<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarcarNotificacionesLeidasRequest;
use App\Services\Panel\NotificacionConsultorService;
use Illuminate\Http\RedirectResponse;

class NotificacionConsultorController extends Controller
{
    public function marcarLeidas(
        MarcarNotificacionesLeidasRequest $request,
        NotificacionConsultorService $service,
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
