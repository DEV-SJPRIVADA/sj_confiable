<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_cat_roles', function (Blueprint $table) {
            $table->increments('id_rol');
            $table->string('nombre', 245);
            $table->string('descripcion', 245)->nullable();
        });

        Schema::create('t_cat_servicio', function (Blueprint $table) {
            $table->increments('id_servicio');
            $table->string('nombre', 255);
            $table->string('descripcion', 45)->nullable();
        });

        Schema::create('t_paquetes_servicio', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
        });

        Schema::create('t_clientes', function (Blueprint $table) {
            $table->increments('id_cliente');
            $table->integer('NIT');
            $table->string('razon_social', 255);
            $table->string('direccion_cliente', 255)->nullable();
            $table->string('ciudad_cliente', 20)->nullable();
            $table->string('telefono_cliente', 20)->nullable();
            $table->string('correo_cliente', 255)->nullable();
            $table->tinyInteger('activo')->default(1);
            $table->string('nombre', 100)->nullable();
            $table->string('cargo', 100)->nullable();
            $table->string('tipo_cliente', 100);
        });

        Schema::create('t_persona', function (Blueprint $table) {
            $table->increments('id_persona');
            $table->string('paterno', 245);
            $table->string('materno', 245)->nullable();
            $table->string('nombre', 245);
            $table->string('telefono', 15)->nullable();
            $table->string('correo', 245);
            $table->string('identificacion', 50)->nullable();
            $table->dateTime('fechaInsert')->useCurrent();
            $table->string('celular', 15);
            $table->string('direccion', 100)->nullable();
        });

        Schema::create('t_proveedores', function (Blueprint $table) {
            $table->increments('id_proveedor');
            $table->integer('NIT_proveedor');
            $table->string('razon_social_proveedor', 50);
            $table->string('nombre_comercial', 50);
            $table->string('correo_proveedor', 50);
            $table->string('telefono_proveedor', 50)->nullable();
            $table->string('celular_proveedor', 50);
            $table->string('direccion_proveedor', 50);
            $table->string('ciudad_proveedor', 50);
            $table->string('nombre_contacto_proveedor', 50);
            $table->string('cargo_contacto_proveedor', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_proveedores');
        Schema::dropIfExists('t_persona');
        Schema::dropIfExists('t_clientes');
        Schema::dropIfExists('t_paquetes_servicio');
        Schema::dropIfExists('t_cat_servicio');
        Schema::dropIfExists('t_cat_roles');
    }
};
