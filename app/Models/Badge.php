<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    /**
     * Supported requirement_type values (checked in BadgeAwardService).
     */
    public const TYPE_FIRST_COMPLETION = 'first_completion';

    public const TYPE_CONSECUTIVE_DAYS = 'consecutive_days';

    public const TYPE_TOTAL_COMPLETIONS = 'total_completions';

    public const TYPE_HABIT_NAME_COMPLETIONS = 'habit_name_completions';

    public const TYPE_ALL_HABITS_ONE_DAY = 'all_habits_one_day';

    public const TYPE_MULTI_HABIT_ONE_DAY = 'multi_habit_one_day';

    protected $fillable = [
        'name',
        'description',
        'icon',
        'requirement_type',
        'requirement_value',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }
}
