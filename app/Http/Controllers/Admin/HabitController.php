<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HabitController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(): View
    {
        $habits = Habit::query()->with('user')->orderByDesc('id')->paginate(20);

        return view('admin.habits.index', compact('habits'));
    }

    public function create(): View
    {
        $users = User::query()->orderBy('name')->get();

        return view('admin.habits.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedHabit($request);
        Habit::create($data);

        return redirect()->route('admin.habits.index')->with('success', 'Habit created.');
    }

    public function edit(Habit $habit): View
    {
        $users = User::query()->orderBy('name')->get();

        return view('admin.habits.edit', compact('habit', 'users'));
    }

    public function update(Request $request, Habit $habit): RedirectResponse
    {
        if ($habit->type === 'private') {
            $data = $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'name' => ['required', 'string', 'max:255'],
                'type' => ['required', 'in:public,private'],
            ]);
        } else {
            $data = $this->validatedHabit($request);
        }

        $habit->update($data);

        return redirect()->route('admin.habits.index')->with('success', 'Habit updated.');
    }

    public function destroy(Habit $habit): RedirectResponse
    {
        $habit->delete();

        return redirect()->route('admin.habits.index')->with('success', 'Habit deleted.');
    }

    public function showDetails(Habit $habit): View
    {
        abort_unless($habit->type === 'public', 403);
        
        $parent = $habit->parent_id ? $habit->parent : $habit;
        
        $participations = Habit::query()
            ->where('parent_id', $parent->id)
            ->orWhere('id', $parent->id)
            ->with(['user', 'habitLogs'])
            ->get();
            
        $leaderboard = $participations->map(function ($part) {
            $completionsCount = $part->habitLogs()->where('is_completed', true)->count();
            return [
                'user' => $part->user,
                'completions_count' => $completionsCount,
            ];
        })->sortByDesc('completions_count')->values();
        
        $posts = $parent->posts()->with('user')->orderByDesc('created_at')->get();
        
        return view('admin.habits.show', compact('parent', 'leaderboard', 'posts'));
    }

    public function awardCredits(Request $request, Habit $habit): RedirectResponse
    {
        abort_unless($habit->type === 'public', 403);
        $parent = $habit->parent_id ? $habit->parent : $habit;
        
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);
        $amount = $validated['amount'];
        
        $participations = Habit::query()
            ->where('parent_id', $parent->id)
            ->orWhere('id', $parent->id)
            ->with(['user', 'habitLogs'])
            ->get();
            
        $leaderboard = $participations->map(function ($part) {
            $completionsCount = $part->habitLogs()->where('is_completed', true)->count();
            return [
                'user' => $part->user,
                'completions_count' => $completionsCount,
            ];
        })->sortByDesc('completions_count')->values();
        
        // Take top 5 entries
        $top5 = $leaderboard->take(5);
        
        $awardedUsers = [];
        foreach ($top5 as $entry) {
            $user = $entry['user'];
            if ($user) {
                $user->increment('credits', $amount);
                $awardedUsers[] = $user->name;
            }
        }
        
        $names = count($awardedUsers) > 0 ? implode(', ', $awardedUsers) : 'None';
        
        return back()->with('success', "Awarded {$amount} credits to top participants: {$names}!");
    }

    protected function validatedHabit(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:public,private'],
            'category' => ['nullable', 'string', 'max:100'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
