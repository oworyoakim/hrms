<?php

use App\Models\Religion;
use Illuminate\Database\Seeder;

class ReligionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Religion::query()->truncate();

        Religion::query()->create([
            'title' => 'Islam',
        ]);
        Religion::query()->create([
            'title' => 'Catholic',
        ]);
        Religion::query()->create([
            'title' => 'Anglican',
        ]);
        Religion::query()->create([
            'title' => 'Hinduism',
        ]);
    }
}
