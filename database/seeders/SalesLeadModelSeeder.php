<?php

namespace Database\Seeders;

use App\Models\SalesLeadModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesLeadModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sales_lead_models')->truncate();
        $models = [
            '614-600',
            '614-710',
            '714-710',
            '714-710 (AT)',
            '714-730',
            '816-710',
            '816-720',
            '916-730',
            'GD8JLSA',
            'FG8JM7A (AT)',
            'FG8JP7A',
            'GH8JJ7A',
            'GH8JR7A',
            'GH8JM7A',
            'GH8JM7A (AT)',
            'FM2PR7A',
            'SH1EESA',
            'ZS1EPVA',
            'J7 TH 4×2',
            'J7 TH 6×4',
            'J6P TH 4×2',
            'J6P 8×4 Dump',
            'J6P 6×4 Dump',
            'JH6 TH 4×2',
            'JH6 8×4 Dump',
            'JH6 8×4 Mixer',
            'Tiger VH',
            'J6P 6x4 chassis',
            'J6P 8x4 Mixer',
            'J6P 8x4 chassis'
        ];

        foreach ($models as $model) {
            SalesLeadModel::create(['name' => $model]);
        }
    }
}
