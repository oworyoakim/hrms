<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait FakeData
{
    private function roleData()
    {
        $name = $this->faker->unique()->userName;
        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }

    private function userData()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password,
        ];
    }

    private function leaveTypeData(){
        return [
            'title' => $this->faker->word,
            'description' => $this->faker->paragraph,
        ];
    }

    private function leaveData(){
        return [
            'start_date' => $this->faker->dateTimeBetween('now','+5 days')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('+6 days','+15 days')->format('Y-m-d'),
            'reason' => $this->faker->paragraph,
        ];
    }

    private function leaveStatuses(){
        return [
            'pending',
            'approved',
            'declined',
        ];
    }
}
