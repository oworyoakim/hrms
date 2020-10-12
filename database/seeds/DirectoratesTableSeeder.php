<?php

use App\Models\Department;
use App\Models\Directorate;
use Illuminate\Database\Seeder;

class DirectoratesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Directorate::query()->truncate();

        if ($directorate = Directorate::query()->create([
            'id' => 0,
            'title' => 'Office of the Executive Director',
            'description' => '',
        ]))
        {
            Department::create(['title' => 'Procurement and Disposal Units',]);
            Department::create(['title' => 'Internal Audits', ]);
            Department::create(['title' => 'Estates Management', ]);
            Department::create(['title' => 'NAPE',]);
            Department::create(['title' => 'Public Relations',]);
            Department::create(['title' => 'Legal Affairs', ]);
        }

        if ($directorate = Directorate::query()->create([
                'title' => 'Finance, Projects and Planning',
                'description' => '',
            ]))
        {
            $directorate->departments()->saveMany([
                new Department(['title' => 'Accounting',]),
                new Department(['title' => 'Finance',]),
                new Department(['title' => 'Planing',]),
            ]);
        }

        if ($directorate = Directorate::query()->create([
            'title' => 'Technology and Reprographics',
            'description' => '',
        ]))
        {
            $directorate->departments()->saveMany([
                new Department(['title' => 'Printery',]),
                new Department(['title' => 'Information and Communications Technology',]),
            ]);
        }

        if ($directorate = Directorate::query()->create([
            'title' => 'Human Resource and Administration',
            'description' => '',
        ]))
        {
            $directorate->departments()->saveMany([
                new Department(['title' => 'Administration',]),
                new Department(['title' => 'Human Resource Management',]),
            ]);
        }

        if ($directorate = Directorate::query()->create([
            'title' => 'Examinations',
            'description' => '',
        ]))
        {
            $directorate->departments()->saveMany([
                new Department(['title' => 'Examination Management',]),
                new Department(['title' => 'Test Development',]),
            ]);
        }

        if ($directorate = Directorate::query()->create([
            'title' => 'Research and Development',
            'description' => '',
        ]))
        {
            $directorate->departments()->saveMany([
                new Department(['title' => 'Research Development', ]),
                new Department(['title' => 'Field Administration', ]),
                new Department(['title' => 'Data Management', ]),
            ]);
        }

    }
}
