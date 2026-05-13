@extends('layouts.app')

@section('title', 'Achievements')

@section('content')
    <h1 class="h3 mb-1">Achievement badges</h1>
    <p class="text-muted mb-4">Earn badges by staying consistent — streaks, totals, and special challenges all count.</p>

    <h2 class="h5 mb-3">Earned</h2>
    <div class="row g-3 mb-5">
        @forelse ($earned as $badge)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100 border border-2 border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="display-6 text-success lh-1">
                                <i class="bi {{ $badge->icon ?: 'bi-award-fill' }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $badge->name }}</div>
                                <p class="small text-muted mb-1">{{ $badge->description }}</p>
                                <div class="small text-success">
                                    Earned {{ \Carbon\Carbon::parse($badge->pivot->earned_at)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border shadow-sm">No badges yet — complete your first habit today.</div>
            </div>
        @endforelse
    </div>

    <h2 class="h5 mb-3">Still locked</h2>
    <div class="row g-3">
        @foreach ($locked as $badge)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 opacity-75">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="display-6 text-muted lh-1">
                                <i class="bi {{ $badge->icon ?: 'bi-lock' }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $badge->name }}</div>
                                <p class="small text-muted mb-0">{{ $badge->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
