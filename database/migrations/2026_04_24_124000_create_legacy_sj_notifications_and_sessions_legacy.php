<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo', 30);
            $table->string('cliente_nombre', 100);
            $table->integer('id_solicitud');
            $table->string('mensaje', 255);
            $table->integer('rol_destino');
            $table->tinyInteger('leido')->default(0);
            $table->dateTime('fecha')->useCurrent();

            $table->index(['rol_destino', 'leido'], 'idx_notif_rol_leido');
            $table->index('fecha', 'idx_notif_fecha');
        });

        Schema::create('notificaciones_cliente', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo', 30);
            $table->string('cliente_nombre', 100);
            $table->integer('id_solicitud');
            $table->string('mensaje', 255);
            $table->unsignedInteger('id_usuario_destino');
            $table->tinyInteger('leido')->default(0);
            $table->dateTime('fecha')->useCurrent();

            $table->index(['id_usuario_destino', 'leido'], 'idx_notif_cli_usuario_leido');
            $table->index('fecha', 'idx_notif_cli_fecha');
        });

        Schema::create('notificaciones_proveedor', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo', 30);
            $table->string('proveedor_nombre', 100);
            $table->integer('id_solicitud');
            $table->string('mensaje', 255);
            $table->unsignedInteger('id_proveedor_destino');
            $table->unsignedInteger('id_usuario_destino')->nullable();
            $table->tinyInteger('leido')->default(0);
            $table->dateTime('fecha')->useCurrent();

            $table->index(['id_proveedor_destino', 'leido'], 'idx_notif_prov_leido');
            $table->index(['id_usuario_destino', 'leido'], 'idx_notif_prov_usuario_leido');
            $table->index('fecha', 'idx_notif_prov_fecha');

            $table->foreign('id_proveedor_destino')->references('id_proveedor')->on('t_proveedores')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_usuario_destino')->references('id_usuario')->on('t_usuarios')->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('t_solicitudes_usuario', function (Blueprint $table) {
            $table->increments('id_solicitud');
            $table->unsignedInteger('id_cliente');
            $table->unsignedInteger('id_usuario_solicitante');
            $table->enum('tipo', ['Crear', 'Modificar', 'Inactivar']);
            $table->json('datos_usuario');
            $table->enum('estado', ['Pendiente', 'Aprobada', 'Rechazada'])->default('Pendiente');
            $table->timestamp('fecha_solicitud')->nullable()->useCurrent();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->unsignedInteger('id_usuario_responde')->nullable();
            $table->text('comentario_respuesta')->nullable();

            $table->foreign('id_cliente')->references('id_cliente')->on('t_clientes');
            $table->foreign('id_usuario_solicitante')->references('id_usuario')->on('t_usuarios');
            $table->foreign('id_usuario_responde')->references('id_usuario')->on('t_usuarios');
        });

        Schema::create('sesiones_persistentes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_usuario');
            $table->string('selector', 12)->unique();
            $table->string('hasher', 64);
            $table->dateTime('expiracion');
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('id_usuario');

            $table->foreign('id_usuario')->references('id_usuario')->on('t_usuarios')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_persistentes');
        Schema::dropIfExists('t_solicitudes_usuario');
        Schema::dropIfExists('notificaciones_proveedor');
        Schema::dropIfExists('notificaciones_cliente');
        Schema::dropIfExists('notificaciones');
    }
};
