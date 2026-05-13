@extends('layouts.app')

@section('title', 'Admin dashboard')

@section('content')
    <h1 class="h3 mb-4">Admin dashboard</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Users</div>
                    <div class="display-6 fw-bold text-primary">{{ $stats['users'] }}</div>
                    <div class="small text-muted">{{ $stats['admins'] }} admin accounts</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Habits</div>
                    <div class="display-6 fw-bold text-success">{{ $stats['habits'] }}</div>
                    <div class="small text-muted">Across all users</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Activity today</div>
                    <div class="display-6 fw-bold text-warning">{{ $stats['completions_today'] }}</div>
                    <div class="small text-muted">Completed habit logs (today)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <div>
                <div class="fw-semibold">Badges awarded (all time)</div>
                <div class="text-muted small">Pivot rows in <code>user_badges</code></div>
            </div>
            <div class="fs-2 fw-bold">{{ $stats['badges_awarded'] }}</div>
        </div>
    </div>
@endsection
