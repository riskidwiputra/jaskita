<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('sql/dummy-data.sql');

        if (! is_file($path)) {
            $this->command->error('File data dummy tidak ditemukan: '.$path);
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'review_photos',
            'reviews',
            'tokens',
            'dendas',
            'transaksi_sewa_barangs',
            'transaksi_sewas',
            'pengeluarans',
            'barangs',
        ] as $table) {
            DB::table($table)->truncate();
        }

        $sql = file_get_contents($path);
        preg_match_all('/INSERT INTO .*?;(?=\s*(?:INSERT INTO|\z))/s', $sql, $matches);

        foreach ($matches[0] as $statement) {
            DB::unprepared($statement);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Data dummy berhasil dimuat dari database/sql/dummy-data.sql');
    }
}
