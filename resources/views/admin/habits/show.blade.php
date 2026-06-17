@extends('layouts.app')

@section('title', 'Admin — Habit Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Public Habit Details</h1>
            <p class="text-muted mb-0 small">Admin review panel for public habits, community participations, and rewarding credits.</p>
        </div>
        <a href="{{ route('admin.habits.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to habits
        </a>
    </div>

    <div class="row g-4">
        <!-- Main details & Rewards column -->
        <div class="col-lg-7">
            <!-- Habit Stats Card -->
            <div class="card border-0 shadow-sm mb-4 rounded-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3 text-dark">Habit Info</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 120px;">Name:</td>
                                    <td class="fw-semibold">{{ $parent->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Creator:</td>
                                    <td>{{ $parent->user->name }} ({{ $parent->user->email }})</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Category:</td>
                                    <td>{{ $parent->category ?? 'General' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Difficulty:</td>
                                    <td class="text-capitalize">{{ $parent->difficulty }}</td>
                                </tr>
                                @if ($parent->description)
                                    <tr>
                                        <td class="text-muted">Description:</td>
                                        <td class="small text-muted" style="white-space: pre-wrap;">{{ $parent->description }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reward Credits Card -->
            <div class="card border-0 shadow-sm mb-4 rounded-4 border-start border-4 border-warning">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold text-dark mb-2">
                        <i class="bi bi-gift-fill text-warning me-1"></i>Award Credits to Top 5
                    </h2>
                    <p class="text-muted small mb-3">Distribute credits directly to the top 5 participants currently listed on the leaderboard.</p>
                    
                    <form action="{{ route('admin.habits.award-credits', $parent) }}" method="post">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="amount" class="col-form-label fw-semibold">Credit Amount:</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" id="amount" name="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror" 
                                       value="50" min="1" max="1000" required style="width: 100px;">
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-warning btn-sm fw-semibold" onsubmit="return confirm('Award credits to the top 5 participants?');">
                                    <i class="bi bi-check-circle-fill me-1"></i>Award Credits
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Social Feed / Progress Posts -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h2 class="h5 mb-0 fw-bold text-dark">
                        <i class="bi bi-chat-left-quote-fill text-info me-1"></i>Progress Posts Feed
                    </h2>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="d-flex flex-column gap-3">
                        @forelse ($posts as $post)
                            <div class="p-3 bg-light rounded-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold small">{{ $post->user->name }}</span>
                                    <span class="text-muted small">{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mb-0 small text-dark">{{ $post->content }}</p>
                            </div>
                        @empty
                            <p class="text-center py-4 text-muted small">No progress reports posted yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard column -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h2 class="h5 mb-0 fw-bold text-primary">
                        <i class="bi bi-trophy-fill text-warning me-1"></i>Participant Leaderboard
                    </h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;" class="text-center">Rank</th>
                                    <th>User</th>
                                    <th class="text-end">Completions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leaderboard as $index => $row)
                                    @php
                                        $rank = $index + 1;
                                    @endphp
                                    <tr class="{{ $rank <= 5 ? 'table-light' : '' }}">
                                        <td class="text-center">
                                            @if($rank <= 5)
                                                <span class="badge bg-warning text-dark fw-bold">Top {{ $rank }}</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $rank }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $row['user']->name }}</div>
                                            <div class="text-muted small font-monospace" style="font-size: 0.75rem;">{{ $row['user']->email }}</div>
                                        </td>
                                        <td class="text-end font-monospace">
                                            <span class="fs-6 text-success fw-bold">{{ $row['completions_count'] }}</span> logs
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">No participants yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
