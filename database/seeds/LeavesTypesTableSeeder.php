<?php

use App\Models\LeaveType;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class LeavesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LeaveType::query()->truncate();

        LeaveType::query()->create([
            'title' => 'Annual Leave',
            'description' => 'Annual Leave 21 days',
        ]);

        LeaveType::query()->create([
            'title' => 'Casual Leave',
            'description' => 'Casual Leave 12 days',
        ]);

        LeaveType::query()->create([
            'title' => 'Maternity Leave',
            'description' => 'Maternity Leave',
        ]);

        LeaveType::query()->create([
            'title' => 'Sick Leave',
            'description' => 'Sick Leave',
            'active' => false,
        ]);

        LeaveType::query()->create([
            'title' => 'Marriage Leave',
            'description' => 'Marriage Leave',
        ]);
    }
}
