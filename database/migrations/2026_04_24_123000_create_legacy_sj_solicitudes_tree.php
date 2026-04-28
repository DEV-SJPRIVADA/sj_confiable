<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('empresa_solicitante', 255);
            $table->string('nit_empresa_solicitante', 50);
            $table->string('cliente_final', 150)->nullable();
            $table->enum('tipo_cliente', ['Interno', 'Externo'])->nullable();
            $table->unsignedInteger('servicio_id')->nullable()->index('solicitudes_ibfk_1');
            $table->unsignedInteger('paquete_id')->nullable()->index('fk_solicitudes_paquete');
            $table->string('ciudad_prestacion_servicio', 255);
            $table->string('ciudad_solicitud_servicio', 255);
            $table->string('nombres', 255);
            $table->string('apellidos', 255);
            $table->string('tipo_identificacion', 50);
            $table->string('numero_documento', 50);
            $table->date('fecha_expedicion')->nullable();
            $table->string('lugar_expedicion', 255)->nullable();
            $table->string('telefono_fijo', 50)->nullable();
            $table->string('celular', 50);
            $table->string('ciudad_residencia_evaluado', 255);
            $table->string('direccion_residencia', 255);
            $table->string('cargo_candidato', 100)->default('');
            $table->text('comentarios')->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->unsignedInteger('usuario_id')->index('solicitudes_jbh_2');
            $table->string('estado', 50)->default('Registrado');
            $table->tinyInteger('activo')->default(1);
            $table->unsignedInteger('id_proveedor')->nullable()->index('idx_id_proveedor');
            $table->dateTime('fecha_asignacion_proveedor')->nullable();

            $table->index('cargo_candidato', 'idx_solicitudes_cargo_candidato');

            $table->foreign('servicio_id')->references('id_servicio')->on('t_cat_servicio')->nullOnDelete();
            $table->foreign('paquete_id')->references('id')->on('t_paquetes_servicio')->nullOnDelete();
            $table->foreign('usuario_id')->references('id_usuario')->on('t_usuarios');
            $table->foreign('id_proveedor')->references('id_proveedor')->on('t_proveedores')->nullOnDelete();
        });

        Schema::create('solicitud_servicios', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('solicitud_id');
            $table->unsignedInteger('servicio_id');
            $table->timestamp('creado_en')->useCurrent();

            $table->unique(['solicitud_id', 'servicio_id'], 'uq_solicitud_servicio');
            $table->index('solicitud_id', 'idx_solicitud');
            $table->index('servicio_id', 'idx_servicio');

            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->cascadeOnDelete();
            $table->foreign('servicio_id')->references('id_servicio')->on('t_cat_servicio');
        });

        Schema::create('documentos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('solicitud_id')->index();
            $table->string('nombre_documento', 255);
            $table->string('ruta_documento', 255);
            $table->timestamp('fecha_subida')->useCurrent();

            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
        });

        Schema::create('evaluados', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('solicitud_id')->index();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('tipo_identificacion', 5);
            $table->string('numero_documento', 20);
            $table->date('fecha_expedicion');
            $table->string('lugar_expedicion', 100);
            $table->string('telefono_fijo', 20)->nullable();
            $table->string('celular', 20);
            $table->string('ciudad_residencia_evaluado', 100);
            $table->string('direccion_residencia', 255);
            $table->string('cargo_candidato', 100)->default('');
            $table->dateTime('fecha_creacion')->useCurrent();

            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->cascadeOnDelete();
        });

        Schema::create('respuesta_madre', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('solicitud_id')->index();
            $table->unsignedInteger('usuario_id')->index();
            $table->text('respuesta');
            $table->string('estado_actual', 50);
            $table->dateTime('fecha_creacion')->useCurrent();

            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
            $table->foreign('usuario_id')->references('id_usuario')->on('t_usuarios');
        });

        Schema::create('documentos_respuesta', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('respuesta_madre_id')->nullable()->index('idx_madre');
            $table->string('nombre_documentoResp', 255);
            $table->string('ruta_documentoResp', 255);
            $table->dateTime('fecha_subidaResp')->useCurrent();

            $table->foreign('respuesta_madre_id')->references('id')->on('respuesta_madre')->cascadeOnDelete();
        });

        Schema::create('respuesta_solicitudes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('solicitud_id')->index();
            $table->unsignedInteger('usuario_id')->index();
            $table->text('respuesta');
            $table->string('documento_respuesta', 255)->nullable();
            $table->dateTime('fecha_respuesta')->useCurrent();
            $table->string('estado_anterior', 50)->nullable();
            $table->string('estado_actual', 50)->nullable();

            $table->index('fecha_respuesta', 'idx_resp_fecha');

            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
            $table->foreign('usuario_id')->references('id_usuario')->on('t_usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuesta_solicitudes');
        Schema::dropIfExists('documentos_respuesta');
        Schema::dropIfExists('respuesta_madre');
        Schema::dropIfExists('evaluados');
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('solicitud_servicios');
        Schema::dropIfExists('solicitudes');
    }
};
