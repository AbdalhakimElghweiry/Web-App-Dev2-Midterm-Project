<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Services\BadgeAwardService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HabitController extends Controller
{
    public function __construct(
        protected BadgeAwardService $badges
    ) {
        $this->middleware(['auth', 'user.role']);
    }

    public function index(): View
    {
        $habits = auth()->user()->habits()->orderBy('name')->paginate(12);

        return view('user.habits.index', compact('habits'));
    }

    public function create(): View
    {
        return view('user.habits.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedHabit($request);
        $data['user_id'] = auth()->id();

        Habit::create($data);

        return redirect()->route('user.habits.index')->with('success', 'Habit created successfully.');
    }

    public function edit(Habit $habit): View
    {
        $this->ensureOwned($habit);

        return view('user.habits.edit', compact('habit'));
    }

    public function update(Request $request, Habit $habit): RedirectResponse
    {
        $this->ensureOwned($habit);

        $habit->update($this->validatedHabit($request));

        return redirect()->route('user.habits.index')->with('success', 'Habit updated successfully.');
    }

    public function destroy(Habit $habit): RedirectResponse
    {
        $this->ensureOwned($habit);

        $habit->delete();

        return redirect()->route('user.habits.index')->with('success', 'Habit deleted.');
    }

    /**
     * Mark a habit complete/incomplete for a calendar day (defaults to today).
     */
    public function saveLog(Request $request, Habit $habit): RedirectResponse
    {
        $this->ensureOwned($habit);

        $validated = $request->validate([
            'completed_date' => ['nullable', 'date'],
            'is_completed' => ['required', 'boolean'],
        ]);

        $date = isset($validated['completed_date'])
            ? Carbon::parse($validated['completed_date'])->toDateString()
            : Carbon::today()->toDateString();

        HabitLog::updateOrCreate(
            [
                'habit_id' => $habit->id,
                'completed_date' => $date,
            ],
            [
                'user_id' => auth()->id(),
                'is_completed' => $validated['is_completed'],
            ]
        );

        $this->badges->evaluateAndAward(auth()->user());

        return back()->with('success', 'Progress saved.');
    }

    public function explore(): View
    {
        $user = auth()->user();
        $joinedParentIds = $user->habits()->whereNotNull('parent_id')->pluck('parent_id')->all();
        $myPublicHabitIds = $user->habits()->whereNull('parent_id')->where('type', 'public')->pluck('id')->all();
        
        $excludeIds = array_merge($joinedParentIds, $myPublicHabitIds);
        
        $habits = Habit::query()
            ->whereNull('parent_id')
            ->where('type', 'public')
            ->when(!empty($excludeIds), function($q) use ($excludeIds) {
                $q->whereNotIn('id', $excludeIds);
            })
            ->orderBy('name')
            ->paginate(12);

        return view('user.habits.explore', compact('habits'));
    }

    public function join(Habit $habit): RedirectResponse
    {
        abort_unless($habit->type === 'public' && is_null($habit->parent_id), 403);
        
        $alreadyJoined = auth()->user()->habits()->where('parent_id', $habit->id)->exists();
        abort_if($alreadyJoined || $habit->user_id === auth()->id(), 403);

        Habit::create([
            'user_id' => auth()->id(),
            'parent_id' => $habit->id,
            'name' => $habit->name,
            'type' => 'public',
            'category' => $habit->category,
            'difficulty' => $habit->difficulty,
            'description' => $habit->description,
        ]);

        return redirect()->route('user.habits.index')->with('success', 'Joined habit successfully!');
    }

    public function showCommunity(Habit $habit): View
    {
        $parent = $habit->parent_id ? $habit->parent : $habit;
        abort_unless($parent->type === 'public', 403);
        
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
        
        return view('user.habits.community', compact('parent', 'leaderboard', 'posts'));
    }

    public function storePost(Request $request, Habit $habit): RedirectResponse
    {
        $parent = $habit->parent_id ? $habit->parent : $habit;
        abort_unless($parent->type === 'public', 403);
        
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);
        
        $parent->posts()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);
        
        return back()->with('success', 'Progress update posted!');
    }

    protected function validatedHabit(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:public,private'],
            'category' => ['nullable', 'string', 'max:100'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    protected function ensureOwned(Habit $habit): void
    {
        abort_unless($habit->user_id === auth()->id(), 403);
    }
}
