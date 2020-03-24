<?php

use App\Models\EmployeeStatus;
use Illuminate\Database\Seeder;

class EmployeeStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmployeeStatus::query()->truncate();

        EmployeeStatus::query()->create([
            'slug' => 'active',
            'title' => 'Active',
        ]);

        EmployeeStatus::query()->create([
            'slug' => 'exited',
            'title' => 'Exited',
        ]);

        EmployeeStatus::query()->create([
            'slug' => 'suspended',
            'title' => 'Suspended',
        ]);

        EmployeeStatus::query()->create([
            'slug' => 'onleave',
            'title' => 'On leave',
        ]);
    }
}
