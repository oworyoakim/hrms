<?php

use App\Models\Title;
use Illuminate\Database\Seeder;

class TitlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Title::query()->truncate();

        Title::query()->create([
            'slug' => 'Mr',
            'title' => 'Mr',
        ]);
        Title::query()->create([
            'slug' => 'Mrs',
            'title' => 'Mrs',
        ]);
        Title::query()->create([
            'slug' => 'Miss',
            'title' => 'Miss',
        ]);
        Title::query()->create([
            'slug' => 'Ms',
            'title' => 'Ms',
        ]);
        Title::query()->create([
            'slug' => 'Dr',
            'title' => 'Dr',
        ]);
        Title::query()->create([
            'slug' => 'Prof',
            'title' => 'Prof',
        ]);
        Title::query()->create([
            'slug' => 'Eng',
            'title' => 'Eng',
        ]);
        Title::query()->create([
            'slug' => 'Hon',
            'title' => 'Hon',
        ]);
    }
}
