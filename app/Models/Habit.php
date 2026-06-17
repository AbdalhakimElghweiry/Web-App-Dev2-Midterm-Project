<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'parent_id', ##this is the privet habbit that the users copies from
        'name',
        'category',
        'difficulty',
        'description',
    ];

    /**
     * Owner of this habit (normal users; admins can still manage rows globally).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Habit::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Habit::class, 'parent_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(HabitPost::class, 'habit_id');
    }

    public function habitLogs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }
}
