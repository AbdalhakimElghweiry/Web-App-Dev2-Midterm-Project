@extends('layouts.app')

@section('title', 'Dashboard — ' . config('app.name'))

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-0">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="text-muted mb-0">Track today’s habits and watch your streaks grow.</p>
        </div>
        <a href="{{ route('user.habits.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New habit
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase">Completed today</div>
                        <div class="fs-3 fw-semibold">{{ $completedToday }} / {{ $habits->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                        <i class="bi bi-calendar-week fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase">This week</div>
                        <div class="fs-3 fw-semibold">{{ $completionsThisWeek }}</div>
                        <div class="small text-muted">Total check-ins</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                        <i class="bi bi-award fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase">Badges earned</div>
                        <div class="fs-3 fw-semibold">{{ $badgeCount }}</div>
                        <a class="small" href="{{ route('user.badges.index') }}">View achievements</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="bi bi-sunrise me-1 text-warning"></i>Today — {{ $today->format('l, M j') }}</span>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('user.habits.index') }}">Manage habits</a>
        </div>
        <div class="card-body p-0">
            @if ($habits->isEmpty())
                <div class="p-4 text-center text-muted">
                    You have no habits yet. Create your first one to start earning badges.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Habit</th>
                            <th>Category</th>
                            <th>Difficulty</th>
                            <th class="text-end">Today</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($habits as $habit)
                            @php
                                $log = $todayLogs->get($habit->id);
                                $done = $log?->is_completed ?? false;
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $habit->name }}</td>
                                <td>{{ $habit->category ?? '—' }}</td>
                                <td><span class="badge bg-secondary text-capitalize">{{ $habit->difficulty }}</span></td>
                                <td class="text-end">
                                    <form action="{{ route('user.habits.log', $habit) }}" method="post" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="is_completed" value="{{ $done ? 0 : 1 }}">
                                        <button type="submit" class="btn btn-sm {{ $done ? 'btn-success' : 'btn-outline-primary' }}">
                                            @if ($done)
                                                <i class="bi bi-check-lg me-1"></i>Done
                                            @else
                                                Mark done
                                            @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
