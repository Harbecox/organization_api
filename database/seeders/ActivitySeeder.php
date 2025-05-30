<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        $this->generateRandomActivities();
    }

    private function generateRandomActivities()
    {
        $activityNames = [
            'Еда', 'Напитки', 'Автомобили', 'Технологии', 'Отдых', 'Спорт', 'Книги', 'Мода', 'Игры', 'Обучение'
        ];

        $numberOfTopLevelCategories = rand(3, 5);

        for ($i = 0; $i < $numberOfTopLevelCategories; $i++) {
            $topLevelActivity = Activity::create([
                'name' => $this->getRandomName($activityNames),
                'parent_id' => null,
            ]);

            $this->createActivityChildren($topLevelActivity->id, $activityNames, 1);
        }
    }

    private function createActivityChildren($parentId, $activityNames, $currentLevel)
    {
        if ($currentLevel > 2) {
            return;
        }

        $numberOfChildren = rand(1, 3);

        for ($i = 0; $i < $numberOfChildren; $i++) {
            $subActivity = Activity::create([
                'name' => $this->getRandomName($activityNames),
                'parent_id' => $parentId,
            ]);

            $this->createActivityChildren($subActivity->id, $activityNames, $currentLevel + 1);
        }
    }

    private function getRandomName(&$names)
    {
        if (empty($names)) {
            return 'Без названия ' . uniqid();
        }

        $key = array_rand($names);
        $name = $names[$key];

        unset($names[$key]);

        return $name;
    }

}

