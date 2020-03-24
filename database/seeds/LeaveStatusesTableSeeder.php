<?php

use App\Models\LeaveStatus;
use Illuminate\Database\Seeder;

class LeaveStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LeaveStatus::query()->truncate();

        LeaveStatus::query()->create([
            'slug' => 'pending',
            'title' => 'Pending',
        ]);
        LeaveStatus::query()->create([
            'slug' => 'ongoing',
            'title' => 'Ongoing',
        ]);
        LeaveStatus::query()->create([
            'slug' => 'completed',
            'title' => 'Completed',
        ]);
        LeaveStatus::query()->create([
            'slug' => 'recalled',
            'title' => 'Recalled',
        ]);
        LeaveStatus::query()->create([
            'slug' => 'canceled',
            'title' => 'Canceled',
        ]);
    }
}
