<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use App\Services\BadgeAwardService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Fills every main column with realistic demo content for presentations / local testing.
 */
class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@habitbuilder.test'],
            [
                'name' => 'Morgan Rivera',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $demo = User::updateOrCreate(
            ['email' => 'user@habitbuilder.test'],
            [
                'name' => 'Jamie Chen',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $alex = User::updateOrCreate(
            ['email' => 'alex.martinez@example.com'],
            [
                'name' => 'Alex Martinez',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $jordan = User::updateOrCreate(
            ['email' => 'jordan.kim@example.com'],
            [
                'name' => 'Jordan Kim',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        // Demo user — rich habits (covers categories / difficulties / descriptions).
        $this->seedHabitsForUser($demo, [
            [
                'name' => 'Morning Gym Session',
                'category' => 'Fitness',
                'difficulty' => 'medium',
                'description' => '45 minutes of strength training or cardio before 9am.',
            ],
            [
                'name' => 'Deep Study Block',
                'category' => 'Education',
                'difficulty' => 'hard',
                'description' => 'Two focused 25-minute Pomodoro sessions with a short break.',
            ],
            [
                'name' => 'Drink 8 Glasses of Water',
                'category' => 'Wellness',
                'difficulty' => 'easy',
                'description' => 'Track hydration; one log counts when you finish the full goal.',
            ],
            [
                'name' => 'Evening Reading',
                'category' => 'Personal growth',
                'difficulty' => 'easy',
                'description' => 'Read at least 15 pages of any book or long-form article.',
            ],
            [
                'name' => 'Lights Out by 11pm',
                'category' => 'Sleep',
                'difficulty' => 'medium',
                'description' => 'Start wind-down routine by 10:30pm; lights off by 11.',
            ],
        ]);

        // Alex — fewer habits, still realistic.
        $this->seedHabitsForUser($alex, [
            [
                'name' => 'Walk 10k Steps',
                'category' => 'Health',
                'difficulty' => 'easy',
                'description' => 'Outdoor walk or treadmill; sync with phone if available.',
            ],
            [
                'name' => 'Study Spanish Vocabulary',
                'category' => 'Study',
                'difficulty' => 'medium',
                'description' => 'Review 20 flashcards and complete one short listening exercise.',
            ],
        ]);

        // Jordan — mix including “water” and “study” for badge demos.
        $this->seedHabitsForUser($jordan, [
            [
                'name' => 'Track Water Intake',
                'category' => 'Hydration',
                'difficulty' => 'easy',
                'description' => 'Log each bottle; goal is 2 liters.',
            ],
            [
                'name' => 'Library Study Hour',
                'category' => 'Academic',
                'difficulty' => 'hard',
                'description' => 'Quiet desk work on assignments or exam prep.',
            ],
        ]);

        // Admin gets one habit so admin/habits lists are not empty for that account (optional).
        $this->seedHabitsForUser($admin, [
            [
                'name' => 'Review team dashboards',
                'category' => 'Leadership',
                'difficulty' => 'medium',
                'description' => 'Skim analytics and note any anomalies for the weekly stand-up.',
            ],
        ]);

        $this->seedHabitLogsForDemoUser($demo);
        $this->seedHabitLogsForAlex($alex);
        $this->seedHabitLogsForJordan($jordan);
        $this->seedHabitLogsForAdmin($admin);

        // Extra historical completions so badge thresholds (totals, name-based) unlock in demos.
        $this->seedDailyCompletionsForHabit($demo, 'Morning Gym Session', 110);
        $this->seedDailyCompletionsForHabit($alex, 'Study Spanish Vocabulary', 35);
        $this->seedDailyCompletionsForHabit($jordan, 'Track Water Intake', 24);
        $this->seedDailyCompletionsForHabit($jordan, 'Library Study Hour', 35);

        $firstBadge = Badge::query()->where('name', 'Beginner Builder')->first();
        if ($firstBadge && ! $admin->badges()->where('badges.id', $firstBadge->id)->exists()) {
            $admin->badges()->attach($firstBadge->id, ['earned_at' => Carbon::now()->subDays(2)]);
        }

        /** @var BadgeAwardService $awards */
        $awards = app(BadgeAwardService::class);
        foreach (User::query()->where('role', 'user')->get() as $user) {
            $awards->evaluateAndAward($user);
        }
    }

    /**
     * @param  array<int, array{name: string, category: string|null, difficulty: string, description: string|null}>  $rows
     */
    protected function seedHabitsForUser(User $user, array $rows): void
    {
        foreach ($rows as $i => $row) {
            Habit::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $row['name'],
                ],
                [
                    'category' => $row['category'],
                    'difficulty' => $row['difficulty'],
                    'description' => $row['description'],
                    'created_at' => Carbon::now()->subDays(20 - $i),
                    'updated_at' => Carbon::now()->subDays(2),
                ]
            );
        }
    }

    protected function seedHabitLogsForDemoUser(User $user): void
    {
        $user->load('habits');
        $habitByName = $user->habits->keyBy('name');

        // Last 12 days: at least one completion per day (supports streak badges).
        for ($d = 11; $d >= 0; $d--) {
            $date = Carbon::today()->subDays($d)->toDateString();
            foreach (['Morning Gym Session', 'Drink 8 Glasses of Water'] as $name) {
                $habit = $habitByName->get($name);
                if ($habit) {
                    HabitLog::updateOrCreate(
                        ['habit_id' => $habit->id, 'completed_date' => $date],
                        ['user_id' => $user->id, 'is_completed' => true]
                    );
                }
            }
            // Extra completions for totals / multi-habit days.
            if ($d % 2 === 0) {
                foreach (['Deep Study Block', 'Evening Reading'] as $name) {
                    $habit = $habitByName->get($name);
                    if ($habit) {
                        HabitLog::updateOrCreate(
                            ['habit_id' => $habit->id, 'completed_date' => $date],
                            ['user_id' => $user->id, 'is_completed' => true]
                        );
                    }
                }
            }
            if ($d === 0 || $d === 3) {
                $sleep = $habitByName->get('Lights Out by 11pm');
                if ($sleep) {
                    HabitLog::updateOrCreate(
                        ['habit_id' => $sleep->id, 'completed_date' => $date],
                        ['user_id' => $user->id, 'is_completed' => true]
                    );
                }
            }
        }

        // One “all habits” day: mark every habit complete on the same date.
        $allDay = Carbon::today()->subDays(5)->toDateString();
        foreach ($user->habits as $habit) {
            HabitLog::updateOrCreate(
                ['habit_id' => $habit->id, 'completed_date' => $allDay],
                ['user_id' => $user->id, 'is_completed' => true]
            );
        }
    }

    protected function seedHabitLogsForAlex(User $user): void
    {
        $user->load('habits');
        foreach ($user->habits as $habit) {
            for ($d = 6; $d >= 0; $d--) {
                if ($d % 2 === 0) {
                    $date = Carbon::today()->subDays($d)->toDateString();
                    HabitLog::updateOrCreate(
                        ['habit_id' => $habit->id, 'completed_date' => $date],
                        ['user_id' => $user->id, 'is_completed' => true]
                    );
                }
            }
        }
    }

    protected function seedHabitLogsForJordan(User $user): void
    {
        $user->load('habits');
        foreach ($user->habits as $habit) {
            for ($d = 25; $d >= 0; $d--) {
                if ($d % 3 !== 0) {
                    continue;
                }
                $date = Carbon::today()->subDays($d)->toDateString();
                HabitLog::updateOrCreate(
                    ['habit_id' => $habit->id, 'completed_date' => $date],
                    ['user_id' => $user->id, 'is_completed' => true]
                );
            }
        }
    }

    protected function seedHabitLogsForAdmin(User $user): void
    {
        $user->load('habits');
        $habit = $user->habits->first();
        if (! $habit) {
            return;
        }
        for ($d = 4; $d >= 0; $d--) {
            $date = Carbon::today()->subDays($d)->toDateString();
            HabitLog::updateOrCreate(
                ['habit_id' => $habit->id, 'completed_date' => $date],
                ['user_id' => $user->id, 'is_completed' => true]
            );
        }
    }

    /**
     * Creates one completed log per calendar day going backward from yesterday.
     */
    protected function seedDailyCompletionsForHabit(User $user, string $habitName, int $dayCount): void
    {
        $habit = $user->habits()->where('name', $habitName)->first();
        if (! $habit) {
            return;
        }

        for ($i = 1; $i <= $dayCount; $i++) {
            $date = Carbon::today()->subDays($i)->toDateString();
            HabitLog::updateOrCreate(
                ['habit_id' => $habit->id, 'completed_date' => $date],
                ['user_id' => $user->id, 'is_completed' => true]
            );
        }
    }
}
