<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Seed default achievement definitions (rules are evaluated in BadgeAwardService).
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Beginner Builder',
                'description' => 'Complete your very first habit check-in.',
                'icon' => 'bi-star-fill',
                'requirement_type' => Badge::TYPE_FIRST_COMPLETION,
                'requirement_value' => 1,
                'meta' => null,
            ],
            [
                'name' => '7-Day Warrior',
                'description' => 'Maintain a 7-day streak of completing at least one habit each day.',
                'icon' => 'bi-lightning-charge-fill',
                'requirement_type' => Badge::TYPE_CONSECUTIVE_DAYS,
                'requirement_value' => 7,
                'meta' => null,
            ],
            [
                'name' => 'Hydration Master',
                'description' => 'Complete a habit with “water” in the name 20 times.',
                'icon' => 'bi-droplet-half',
                'requirement_type' => Badge::TYPE_HABIT_NAME_COMPLETIONS,
                'requirement_value' => 20,
                'meta' => ['name_contains' => 'water'],
            ],
            [
                'name' => 'Study Champion',
                'description' => 'Complete a habit with “study” in the name 30 times.',
                'icon' => 'bi-book-half',
                'requirement_type' => Badge::TYPE_HABIT_NAME_COMPLETIONS,
                'requirement_value' => 30,
                'meta' => ['name_contains' => 'study'],
            ],
            [
                'name' => 'Consistency King',
                'description' => 'Finish every habit you had on at least one full day.',
                'icon' => 'bi-calendar-check',
                'requirement_type' => Badge::TYPE_ALL_HABITS_ONE_DAY,
                'requirement_value' => 1,
                'meta' => null,
            ],
            [
                'name' => 'Power Day',
                'description' => 'Complete at least 3 different habits on the same day.',
                'icon' => 'bi-fire',
                'requirement_type' => Badge::TYPE_MULTI_HABIT_ONE_DAY,
                'requirement_value' => 3,
                'meta' => null,
            ],
            [
                'name' => 'Century Club',
                'description' => 'Reach 100 total completed habit logs.',
                'icon' => 'bi-emoji-sunglasses',
                'requirement_type' => Badge::TYPE_TOTAL_COMPLETIONS,
                'requirement_value' => 100,
                'meta' => null,
            ],
        ];

        foreach ($badges as $row) {
            Badge::updateOrCreate(
                ['name' => $row['name']],
                $row
            );
        }
    }
}
