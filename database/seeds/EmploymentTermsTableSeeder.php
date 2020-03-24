<?php

use App\Models\EmploymentTerm;
use Illuminate\Database\Seeder;

class EmploymentTermsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmploymentTerm::query()->truncate();

        EmploymentTerm::query()->create([
            'slug' => 'permanent',
            'title' => 'Permanent',
        ]);

        EmploymentTerm::query()->create([
            'slug' => 'contract',
            'title' => 'Contract',
        ]);
    }
}
