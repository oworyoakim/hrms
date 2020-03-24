<?php

use App\Models\EmploymentType;
use Illuminate\Database\Seeder;

class EmploymentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmploymentType::query()->truncate();

        EmploymentType::query()->create([
            'slug' => 'full-time',
            'title' => 'Full time',
        ]);

        EmploymentType::query()->create([
            'slug' => 'part-time',
            'title' => 'Part time',
        ]);
    }
}
