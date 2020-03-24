<?php

use App\Models\LeaveApplicationStatus;
use Illuminate\Database\Seeder;

class LeaveApplicationStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LeaveApplicationStatus::query()->truncate();

        LeaveApplicationStatus::query()->create([
            'slug' => 'pending',
            'title' => 'Pending',
        ]);
        LeaveApplicationStatus::query()->create([
            'slug' => 'approved',
            'title' => 'Approved',
        ]);
        LeaveApplicationStatus::query()->create([
            'slug' => 'declined',
            'title' => 'Declined',
        ]);
        LeaveApplicationStatus::query()->create([
            'slug' => 'granted',
            'title' => 'Granted',
        ]);
        LeaveApplicationStatus::query()->create([
            'slug' => 'rejected',
            'title' => 'Rejected',
        ]);
    }
}
