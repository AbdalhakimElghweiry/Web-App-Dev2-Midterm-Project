<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\View\View;

class BadgeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.role']);
    }

    public function index(): View
    {
        $earned = auth()->user()->badges()->orderByPivot('earned_at', 'desc')->get();
        $locked = Badge::query()
            ->whereNotIn('id', $earned->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('user.badges.index', compact('earned', 'locked'));
    }
}
