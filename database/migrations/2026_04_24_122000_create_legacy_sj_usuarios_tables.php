<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_usuarios', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->unsignedInteger('id_rol')->index();
            $table->unsignedInteger('id_persona')->index('fkPersona_idx');
            $table->string('usuario', 245);
            $table->string('password', 245);
            $table->integer('activo')->default(1);
            $table->string('ciudad', 255);
            $table->date('fecha_insert');
            $table->string('estado_conexion', 50)->default('Desconectado');
            $table->unsignedInteger('id_cliente')->nullable()->index();
            $table->integer('creado_por');
            $table->string('reset_token', 64)->nullable();
            $table->dateTime('reset_token_expiry')->nullable();
            $table->unsignedInteger('id_proveedor')->nullable()->index('fk_usuario_proveedor');
            $table->tinyInteger('permiso_ver_documentos')->default(0);
            $table->tinyInteger('permiso_subir_documentos')->default(0);
            $table->tinyInteger('permiso_crear_solicitudes')->default(0);

            $table->foreign('id_rol')->references('id_rol')->on('t_cat_roles');
            $table->foreign('id_persona')->references('id_persona')->on('t_persona');
            $table->foreign('id_cliente')->references('id_cliente')->on('t_clientes');
            $table->foreign('id_proveedor')->references('id_proveedor')->on('t_proveedores')->nullOnDelete();
        });

        Schema::create('t_login_attempts', function (Blueprint $table) {
            $table->unsignedInteger('id_usuario')->primary();
            $table->integer('intentos')->default(0);
            $table->dateTime('last_attempt')->useCurrent();

            $table->foreign('id_usuario')->references('id_usuario')->on('t_usuarios')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_login_attempts');
        Schema::dropIfExists('t_usuarios');
    }
};
