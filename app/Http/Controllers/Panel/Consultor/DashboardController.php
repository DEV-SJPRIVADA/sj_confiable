<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel\Consultor;

use App\Http\Controllers\Controller;
use App\Services\Panel\ConsultorDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ConsultorDashboardService $dashboard,
    ) {}

    public function index(Request $request): View
    {
        return view('panel.consultor.dashboard', $this->dashboard->build($request));
    }
}
