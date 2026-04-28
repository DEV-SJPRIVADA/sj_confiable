<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $this->call(LegacyCatalogSeeder::class);
        $this->call(LegacyIdentitySeeder::class);
        $this->call(LegacyOperationalDataSeeder::class);

        if ((bool) env('SEED_DEV_PASSWORDS', true)) {
            $this->call(LocalDevPasswordSeeder::class);
        }
    }
}
