<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->boolean('visible_para_cliente')->default(true)->after('fecha_subida');
            $table->boolean('cargado_desde_panel_cliente')->default(false)->after('visible_para_cliente');
        });

        Schema::table('documentos_respuesta', function (Blueprint $table) {
            $table->boolean('visible_para_cliente')->default(true)->after('fecha_subidaResp');
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn(['visible_para_cliente', 'cargado_desde_panel_cliente']);
        });

        Schema::table('documentos_respuesta', function (Blueprint $table) {
            $table->dropColumn('visible_para_cliente');
        });
    }
};
