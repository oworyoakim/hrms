<?php

use App\Models\MaritalStatus;
use Illuminate\Database\Seeder;

class MaritalStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MaritalStatus::query()->truncate();

        MaritalStatus::query()->create([
            'title' => 'Single',
        ]);

        MaritalStatus::query()->create([
            'title' => 'Married',
        ]);

        MaritalStatus::query()->create([
            'title' => 'Widowed',
        ]);

        MaritalStatus::query()->create([
            'title' => 'Cohabiting',
        ]);

        MaritalStatus::query()->create([
            'title' => 'Other',
        ]);
    }
}
