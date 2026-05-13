<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Carbon\Carbon;

/**
 * Central place for badge rules. Called after a habit completion is saved.
 *
 * Beginner-friendly note: we load every badge once, skip badges the user already
 * earned, then run a small rule function per requirement_type.
 */
class BadgeAwardService
{
    public function evaluateAndAward(User $user): void
    {
        $earnedIds = $user->badges()->pluck('badges.id')->all();

        Badge::query()->orderBy('id')->each(function (Badge $badge) use ($user, $earnedIds): void {
            if (in_array($badge->id, $earnedIds, true)) {
                return;
            }

            if ($this->qualifies($user, $badge)) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);
                $earnedIds[] = $badge->id;
            }
        });
    }

    protected function qualifies(User $user, Badge $badge): bool
    {
        return match ($badge->requirement_type) {
            Badge::TYPE_FIRST_COMPLETION => $this->totalCompletedLogs($user) >= max(1, $badge->requirement_value),
            Badge::TYPE_CONSECUTIVE_DAYS => $this->longestDailyStreak($user) >= $badge->requirement_value,
            Badge::TYPE_TOTAL_COMPLETIONS => $this->totalCompletedLogs($user) >= $badge->requirement_value,
            Badge::TYPE_HABIT_NAME_COMPLETIONS => $this->completedLogsForHabitName($user, (string) ($badge->meta['name_contains'] ?? '')) >= $badge->requirement_value,
            Badge::TYPE_ALL_HABITS_ONE_DAY => $this->countDaysAllHabitsCompleted($user) >= $badge->requirement_value,
            Badge::TYPE_MULTI_HABIT_ONE_DAY => $this->maxCompletedHabitsInOneDay($user) >= $badge->requirement_value,
            default => false,
        };
    }

    protected function totalCompletedLogs(User $user): int
    {
        return (int) HabitLog::query()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();
    }

    /**
     * Longest run of consecutive calendar days where the user completed at least one habit.
     */
    protected function longestDailyStreak(User $user): int
    {
        $dates = HabitLog::query()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->selectRaw('DATE(completed_date) as d')
            ->distinct()
            ->orderBy('d')
            ->pluck('d')
            ->map(fn ($d) => Carbon::parse($d)->startOfDay())
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $best = 1;
        $current = 1;

        for ($i = 1; $i < $dates->count(); $i++) {
            $prev = $dates[$i - 1];
            $curr = $dates[$i];

            if ($prev->copy()->addDay()->equalTo($curr)) {
                $current++;
                $best = max($best, $current);
            } else {
                $current = 1;
            }
        }

        return $best;
    }

    protected function completedLogsForHabitName(User $user, string $needle): int
    {
        $needle = trim(mb_strtolower($needle));
        if ($needle === '') {
            return 0;
        }

        return (int) HabitLog::query()
            ->where('habit_logs.user_id', $user->id)
            ->where('habit_logs.is_completed', true)
            ->whereHas('habit', function ($q) use ($needle): void {
                $q->whereRaw('LOWER(name) LIKE ?', ['%'.$needle.'%']);
            })
            ->count();
    }

    /**
     * Count days where the user completed every habit they already had on that day.
     */
    protected function countDaysAllHabitsCompleted(User $user): int
    {
        $count = 0;

        $days = HabitLog::query()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->select('completed_date')
            ->distinct()
            ->pluck('completed_date');

        foreach ($days as $day) {
            $dayEnd = Carbon::parse($day)->endOfDay();

            $expected = Habit::query()
                ->where('user_id', $user->id)
                ->where('created_at', '<=', $dayEnd)
                ->count();

            if ($expected === 0) {
                continue;
            }

            $completedDistinct = HabitLog::query()
                ->where('user_id', $user->id)
                ->whereDate('completed_date', $day)
                ->where('is_completed', true)
                ->pluck('habit_id')
                ->unique()
                ->count();

            if ($completedDistinct >= $expected) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Largest number of different habits completed on a single calendar day.
     */
    protected function maxCompletedHabitsInOneDay(User $user): int
    {
        $max = (int) HabitLog::query()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->selectRaw('completed_date, COUNT(DISTINCT habit_id) as habit_count')
            ->groupBy('completed_date')
            ->orderByDesc('habit_count')
            ->value('habit_count');

        return $max;
    }
}
