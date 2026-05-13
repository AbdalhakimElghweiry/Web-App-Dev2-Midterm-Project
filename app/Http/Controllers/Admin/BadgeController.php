<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BadgeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(): View
    {
        $badges = Badge::query()->orderBy('name')->paginate(20);

        return view('admin.badges.index', compact('badges'));
    }

    public function create(): View
    {
        return view('admin.badges.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Badge::create($this->validatedBadge($request));

        return redirect()->route('admin.badges.index')->with('success', 'Badge created.');
    }

    public function edit(Badge $badge): View
    {
        return view('admin.badges.edit', compact('badge'));
    }

    public function update(Request $request, Badge $badge): RedirectResponse
    {
        $badge->update($this->validatedBadge($request));

        return redirect()->route('admin.badges.index')->with('success', 'Badge updated.');
    }

    public function destroy(Badge $badge): RedirectResponse
    {
        $badge->delete();

        return redirect()->route('admin.badges.index')->with('success', 'Badge deleted.');
    }

    protected function validatedBadge(Request $request): array
    {
        $types = [
            Badge::TYPE_FIRST_COMPLETION,
            Badge::TYPE_CONSECUTIVE_DAYS,
            Badge::TYPE_TOTAL_COMPLETIONS,
            Badge::TYPE_HABIT_NAME_COMPLETIONS,
            Badge::TYPE_ALL_HABITS_ONE_DAY,
            Badge::TYPE_MULTI_HABIT_ONE_DAY,
        ];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:191'],
            'requirement_type' => ['required', Rule::in($types)],
            'requirement_value' => ['required', 'integer', 'min:1'],
            'meta_json' => ['nullable', 'string', 'max:2000'],
        ]);

        $meta = null;
        $rawMeta = $data['meta_json'] ?? null;
        unset($data['meta_json']);

        if ($rawMeta !== null && trim($rawMeta) !== '') {
            $decoded = json_decode($rawMeta, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages([
                    'meta_json' => 'Meta must be valid JSON (example: {"name_contains":"water"}).',
                ]);
            }
            $meta = $decoded;
        }

        $data['meta'] = $meta;

        return $data;
    }
}
