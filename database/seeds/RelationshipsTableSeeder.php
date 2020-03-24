<?php

use App\Models\Relationship;
use Illuminate\Database\Seeder;

class RelationshipsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Relationship::query()->truncate();

        Relationship::query()->create([
            'slug' => 'mother',
            'title' => 'Mother',
        ]);
        Relationship::query()->create([
            'slug' => 'father',
            'title' => 'Father',
        ]);
        Relationship::query()->create([
            'slug' => 'spouse',
            'title' => 'Spouse',
        ]);

        Relationship::query()->create([
            'slug' => 'son',
            'title' => 'Son',
        ]);

        Relationship::query()->create([
            'slug' => 'daughter',
            'title' => 'Daughter',
        ]);

        Relationship::query()->create([
            'slug' => 'other',
            'title' => 'Other',
        ]);
    }
}
