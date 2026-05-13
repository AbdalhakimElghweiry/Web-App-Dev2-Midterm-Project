<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Services\BadgeAwardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.role']);
    }

    /**
     * User home: quick stats + today's habit checklist.
     */
    public function index(): View
    {
        $user = auth()->user();

        $today = Carbon::today();

        $habits = $user->habits()->orderBy('name')->get();

        $todayLogs = HabitLog::query()
            ->where('user_id', $user->id)
            ->whereDate('completed_date', $today)
            ->get()
            ->keyBy('habit_id');

        $completedToday = $todayLogs->where('is_completed', true)->count();

        $weekStart = $today->copy()->startOfWeek();
        $completionsThisWeek = HabitLog::query()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereBetween('completed_date', [$weekStart, $today])
            ->count();

        $badgeCount = $user->badges()->count();

        return view('user.dashboard', compact(
            'habits',
            'todayLogs',
            'completedToday',
            'completionsThisWeek',
            'badgeCount',
            'today'
        ));
    }
}
