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

    protected function validatedHabit(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
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
