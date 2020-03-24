<?php

use App\Models\EmploymentStatus;
use Illuminate\Database\Seeder;

class EmploymentStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmploymentStatus::query()->truncate();

        EmploymentStatus::query()->create([
            'slug' => 'probation',
            'title' => 'Probation',
        ]);

        EmploymentStatus::query()->create([
            'slug' => 'confirmed',
            'title' => 'Confirmed',
        ]);
    }
}
