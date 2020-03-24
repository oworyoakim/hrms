<?php

use App\Models\SalaryScale;
use Illuminate\Database\Seeder;

class SalaryScalesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SalaryScale::query()->truncate();

        SalaryScale::query()->create([
            'scale' => 'EB1',
            'rank' => 1,
            'description' => '',
        ]);

        SalaryScale::query()->create([
            'scale' => 'EB2',
            'rank' => 2,
            'description' => '',
        ]);

        SalaryScale::query()->create([
            'scale' => 'EB3U',
            'rank' => 3,
            'description' => '',
        ]);
        SalaryScale::query()->create([
            'scale' => 'EB3L',
            'rank' => 4,
            'description' => '',
        ]);
        SalaryScale::query()->create([
            'scale' => 'EB4',
            'rank' => 5,
            'description' => '',
        ]);
        SalaryScale::query()->create([
            'scale' => 'EB5',
            'rank' => 6,
            'description' => '',
        ]);
        SalaryScale::query()->create([
            'scale' => 'EB6',
            'rank' => 7,
            'description' => '',
        ]);

    }
}
