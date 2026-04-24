<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Unifica la contraseña de usuarios de prueba (ids del dump bootstrap_legacy.sql) para login local.
 * Usa .env: SEED_ADMIN_PASSWORD (nunca en producción con datos reales).
 */
class LocalDevPasswordSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $plain = env('SEED_ADMIN_PASSWORD');
        if (! is_string($plain) || $plain === '') {
            $this->command?->warn('SEED_ADMIN_PASSWORD vacío: no se actualizan contraseñas de prueba.');

            return;
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('t_usuarios')) {
            $this->command?->warn('Tabla t_usuarios inexistente: ejecute primero las migraciones.');

            return;
        }

        $hash = Hash::make($plain);
        $ids = [1, 2, 3, 4, 5, 6, 7];

        DB::table('t_usuarios')->whereIn('id_usuario', $ids)->update(['password' => $hash]);

        $this->command?->info('Contraseñas de prueba actualizadas (ids '.implode(',', $ids).') con SEED_ADMIN_PASSWORD.');
    }
}
