<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(): View
    {
        $stats = [
            'users' => User::query()->count(),
            'admins' => User::query()->where('role', 'admin')->count(),
            'habits' => Habit::query()->count(),
            'completions_today' => HabitLog::query()->whereDate('completed_date', today())->where('is_completed', true)->count(),
            'badges_awarded' => UserBadge::query()->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
