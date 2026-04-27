<?php

declare(strict_types=1);

use App\Domain\Routing\RoleHome;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Panel\Cliente\SolicitudController as ClienteSolicitudController;
use App\Http\Controllers\Panel\Consultor\AsociadosController;
use App\Http\Controllers\Panel\Consultor\ClientesController;
use App\Http\Controllers\Panel\Consultor\DashboardController;
use App\Http\Controllers\Panel\Consultor\InformesController;
use App\Http\Controllers\Panel\Consultor\SolicitudController as ConsultorSolicitudController;
use App\Http\Controllers\Panel\Consultor\SolicitudesUsuarioController;
use App\Http\Controllers\Panel\Consultor\UsuariosController;
use App\Http\Controllers\Panel\Proveedor\SolicitudController as ProveedorSolicitudController;
use App\Models\Usuario;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();
    if ($user instanceof Usuario) {
        return redirect(RoleHome::pathFor($user));
    }

    return redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:2,3'])->prefix('panel/consultor')->name('panel.consultor.')->group(function () {
    Route::redirect('/', '/panel/consultor/inicio');
    Route::get('inicio', [DashboardController::class, 'index'])->name('inicio');
    Route::get('informes', [InformesController::class, 'index'])->name('informes.index');
    Route::get('informes/exportar', [InformesController::class, 'export'])->name('informes.export');

    Route::get('usuarios/crear', [UsuariosController::class, 'create'])->name('usuarios.create');
    Route::post('usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
    Route::get('usuarios/{usuario}/editar', [UsuariosController::class, 'edit'])->name('usuarios.edit');
    Route::put('usuarios/{usuario}', [UsuariosController::class, 'update'])->name('usuarios.update');
    Route::post('usuarios/{usuario}/toggle-activo', [UsuariosController::class, 'toggleActivo'])->name('usuarios.toggle-activo');
    Route::get('usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');

    Route::get('clientes/crear', [ClientesController::class, 'create'])->name('clientes.create');
    Route::post('clientes', [ClientesController::class, 'store'])->name('clientes.store');
    Route::get('clientes/{cliente}/editar', [ClientesController::class, 'edit'])->name('clientes.edit');
    Route::put('clientes/{cliente}', [ClientesController::class, 'update'])->name('clientes.update');
    Route::patch('clientes/{cliente}/activo', [ClientesController::class, 'toggleActivo'])->name('clientes.toggle-activo');
    Route::get('clientes', [ClientesController::class, 'index'])->name('clientes.index');

    Route::get('asociados/crear', [AsociadosController::class, 'create'])->name('asociados.create');
    Route::post('asociados', [AsociadosController::class, 'store'])->name('asociados.store');
    Route::get('asociados/{proveedor}/editar', [AsociadosController::class, 'edit'])->name('asociados.edit');
    Route::put('asociados/{proveedor}', [AsociadosController::class, 'update'])->name('asociados.update');
    Route::delete('asociados/{proveedor}', [AsociadosController::class, 'destroy'])->name('asociados.destroy');
    Route::get('asociados', [AsociadosController::class, 'index'])->name('asociados.index');

    Route::post('solicitudes-usuarios/{solicitudUsuario}/responder', [SolicitudesUsuarioController::class, 'responder'])->name('solicitudes-usuarios.responder');
    Route::get('solicitudes-usuarios', [SolicitudesUsuarioController::class, 'index'])->name('solicitudes-usuarios.index');

    Route::get('solicitudes', [ConsultorSolicitudController::class, 'index'])->name('solicitudes.index');
    Route::post('solicitudes/{solicitud}/asignar', [ConsultorSolicitudController::class, 'asignar'])->name('solicitudes.asignar');
    Route::get('solicitudes/{solicitud}', [ConsultorSolicitudController::class, 'show'])->name('solicitudes.show');
});

Route::middleware(['auth', 'role:1,4,5'])->prefix('panel/cliente')->name('panel.cliente.')->group(function () {
    Route::redirect('/', '/panel/cliente/solicitudes');
    Route::get('solicitudes', [ClienteSolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('solicitudes/{solicitud}', [ClienteSolicitudController::class, 'show'])->name('solicitudes.show');
});

Route::middleware(['auth', 'role:6'])->prefix('panel/proveedor')->name('panel.proveedor.')->group(function () {
    Route::redirect('/', '/panel/proveedor/solicitudes');
    Route::get('solicitudes', [ProveedorSolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('solicitudes/{solicitud}', [ProveedorSolicitudController::class, 'show'])->name('solicitudes.show');
});
