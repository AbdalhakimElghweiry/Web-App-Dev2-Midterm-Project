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
        $habit->update($this->validatedHabit($request));

        return redirect()->route('admin.habits.index')->with('success', 'Habit updated.');
    }

    public function destroy(Habit $habit): RedirectResponse
    {
        $habit->delete();

        return redirect()->route('admin.habits.index')->with('success', 'Habit deleted.');
    }

    protected function validatedHabit(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
