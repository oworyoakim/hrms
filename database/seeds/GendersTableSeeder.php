<?php

use App\Models\Gender;
use Illuminate\Database\Seeder;

class GendersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Gender::query()->truncate();

        Gender::query()->create([
            'slug' => 'male',
            'title' => 'Male',
        ]);
        Gender::query()->create([
            'slug' => 'female',
            'title' => 'Female',
        ]);
//        Gender::query()->create([
//            'slug' => 'other',
//            'title' => 'Other',
//        ]);
    }
}
