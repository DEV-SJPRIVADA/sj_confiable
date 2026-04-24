<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PDO;

/**
 * Carga estructura + datos mínimos desde database/sql/bootstrap_legacy.sql (proyecto legado 127_0_0_1).
 * Idempotente: no hace nada si ya existe t_usuarios.
 * Solo entorno local y driver mysql/mariadb (evita conflictos con phpunit: sqlite en memoria).
 */
class LegacySchemaImportSeeder extends Seeder
{
    public function run(): void
    {
        if (! $this->shouldRun()) {
            $this->command?->info('LegacySchemaImportSeeder: omitido (entorno, driver o esquema ya presente).');

            return;
        }

        $path = database_path('sql/bootstrap_legacy.sql');
        if (! is_readable($path)) {
            $this->command?->error('Falta el archivo: '.$path);

            return;
        }

        $sql = (string) file_get_contents($path);
        if (! Str::contains($sql, 'CREATE TABLE')) {
            $this->command?->error('SQL inválido o vacío: '.$path);

            return;
        }

        $pdo = DB::connection()->getPdo();
        if (defined('PDO::MYSQL_ATTR_MULTI_STATEMENTS')) {
            $pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, true);
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
        try {
            $pdo->exec($sql);
        } finally {
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command?->info('Esquema y datos iniciales importados desde bootstrap_legacy.sql');
    }

    private function shouldRun(): bool
    {
        if (! app()->environment('local')) {
            return false;
        }

        if (filter_var((string) env('RUN_LEGACY_SQL_IMPORT', true), FILTER_VALIDATE_BOOL) === false) {
            return false;
        }

        $connection = (string) config('database.default', 'mysql');
        $cfg = config("database.connections.{$connection}", []);
        $driver = (string) ($cfg['driver'] ?? '');

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return false;
        }

        if (Schema::hasTable('t_usuarios')) {
            return false;
        }

        return true;
    }
}
