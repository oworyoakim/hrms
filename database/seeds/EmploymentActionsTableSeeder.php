<?php

use App\Models\EmploymentAction;
use Illuminate\Database\Seeder;

class EmploymentActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmploymentAction::query()->truncate();

        EmploymentAction::create([
            'slug' => 'appointment',
            'title' => 'Appointment',
            'description' => 'Appointment',
        ]);

        EmploymentAction::create([
            'slug' => 'delegation',
            'title' => 'Delegation',
            'description' => 'Delegation',
        ]);

        EmploymentAction::create([
            'slug' => 'demotion',
            'title' => 'Demotion',
            'description' => 'Demotion',
        ]);

        EmploymentAction::create([
            'slug' => 'promotion',
            'title' => 'Promotion',
            'description' => 'Promotion',
        ]);

        EmploymentAction::create([
            'slug' => 'dismissal',
            'title' => 'Dismissal',
            'description' => 'Dismissal',
        ]);

        EmploymentAction::create([
            'slug' => 'retirement',
            'title' => 'Retirement',
            'description' => 'Retirement',
        ]);

        EmploymentAction::create([
            'slug' => 'termination',
            'title' => 'Termination',
            'description' => 'Termination',
        ]);

        EmploymentAction::create([
            'slug' => 'suspension',
            'title' => 'Suspension',
            'description' => 'Suspensions',
        ]);

    }
}
