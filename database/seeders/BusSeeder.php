<?php

namespace Database\Seeders;

use App\Models\Bus;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    public function run(): void
    {
        $buses = [
            ['name' => 'باص 1', 'capacity' => 25],
            ['name' => 'باص 2', 'capacity' => 25],
            ['name' => 'باص 3', 'capacity' => 30],
            ['name' => 'باص 4', 'capacity' => 20],
        ];

        foreach ($buses as $data) {
            Bus::updateOrCreate(
                ['name' => $data['name']],
                ['capacity' => $data['capacity'], 'is_active' => true]
            );
        }
    }
}
