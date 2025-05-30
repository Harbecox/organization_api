<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $allBuildings = Building::all();
        $allActivities = Activity::pluck('id')->toArray();

        Organization::factory(1000)
            ->state(function () use ($allBuildings) {
                return [
                    'building_id' => $allBuildings->random()->id,
                ];
            })
            ->create()
            ->each(function ($organization) use ($allActivities) {
                $organization->activities()->attach(array_rand(array_flip($allActivities), rand(1, 3)));
            });

    }

}
