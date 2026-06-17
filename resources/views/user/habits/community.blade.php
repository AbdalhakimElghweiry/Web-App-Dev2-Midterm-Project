@extends('layouts.app')

@section('title', 'Community Hub — ' . $parent->name)

@section('content')
    <!-- Habit Details Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-3">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h1 class="h3 mb-0 fw-bold">{{ $parent->name }}</h1>
                        <span class="badge bg-secondary text-capitalize">{{ $parent->difficulty }}</span>
                        @if($parent->user->isAdmin())
                            <span class="badge bg-danger text-white">Admin Challenge</span>
                        @else
                            <span class="badge bg-info text-dark">Community Habit</span>
                        @endif
                    </div>
                    <p class="text-muted mb-0 small">
                        <i class="bi bi-tag-fill text-primary"></i> {{ $parent->category ?? 'General' }}
                        <span class="ms-3"><i class="bi bi-person-circle"></i> Created by <strong>{{ $parent->user->name }}</strong></span>
                        <span class="ms-3"><i class="bi bi-calendar-check"></i> {{ $parent->created_at->format('M j, Y') }}</span>
                    </p>
                </div>
                <a href="{{ route('user.habits.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to my habits
                </a>
            </div>
            
            @if($parent->description)
                <div class="bg-light p-3 rounded border-start border-4 border-info">
                    <div class="fw-semibold small text-muted mb-1">Description & Goal:</div>
                    <p class="mb-0 small text-dark" style="white-space: pre-line;">{{ $parent->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Leaderboard Column -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h2 class="h5 mb-0 fw-bold text-primary">
                        <i class="bi bi-trophy-fill text-warning me-2"></i>Friendly Leaderboard
                    </h2>
                    <p class="text-muted small mb-0 mt-1">Participants ranked by total completed days</p>
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
                                        $isTop5 = $rank <= 5;
                                        $rankClass = '';
                                        $rankBadge = '';
                                        if ($rank === 1) {
                                            $rankBadge = '<span class="badge bg-warning text-dark"><i class="bi bi-award-fill"></i> 1st</span>';
                                        } elseif ($rank === 2) {
                                            $rankBadge = '<span class="badge bg-secondary text-white">2nd</span>';
                                        } elseif ($rank === 3) {
                                            $rankBadge = '<span class="badge bg-bronze text-white" style="background-color: #cd7f32;">3rd</span>';
                                        } else {
                                            $rankBadge = '<span class="badge bg-light text-dark">' . $rank . 'th</span>';
                                        }
                                        
                                        $isMe = $row['user']->id === auth()->id();
                                    @endphp
                                    <tr class="{{ $isMe ? 'table-warning fw-semibold' : '' }}">
                                        <td class="text-center">{!! $rankBadge !!}</td>
                                        <td>
                                            {{ $row['user']->name }}
                                            @if($isMe)
                                                <span class="badge bg-primary text-white ms-1">You</span>
                                            @endif
                                            @if($row['user']->isAdmin())
                                                <span class="badge bg-danger text-white ms-1">Admin</span>
                                            @endif
                                        </td>
                                        <td class="text-end font-monospace">
                                            <span class="fs-5 text-success">{{ $row['completions_count'] }}</span> check-ins
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            No completions recorded yet. Be the first to check in!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Posts Feed Column -->
        <div class="col-lg-7">
            <!-- Share Update Form -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3 fw-bold text-dark">
                        <i class="bi bi-send-fill text-success me-2"></i>Post Progress Report
                    </h2>
                    <form action="{{ route('user.habits.posts.store', $parent) }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <textarea name="content" rows="3" class="form-control @error('content') is-invalid @enderror" 
                                      placeholder="Share your progress today! e.g., 'Day 5: Just finished a 5km run! Feel great!' or encourage others!" 
                                      required maxlength="1000"></textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Keep it friendly and encouraging!</span>
                            <button type="submit" class="btn btn-success px-4 btn-sm">
                                <i class="bi bi-send me-1"></i>Post Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Social Feed -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h2 class="h5 mb-0 fw-bold text-dark">
                        <i class="bi bi-chat-quote-fill text-info me-2"></i>Encouragement Feed
                    </h2>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="d-flex flex-column gap-3">
                        @forelse ($posts as $post)
                            <div class="p-3 bg-light rounded-3 border-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-dark small">{{ $post->user->name }}</span>
                                        @if($post->user->isAdmin())
                                            <span class="badge bg-danger text-white scale-75">Admin</span>
                                        @endif
                                    </div>
                                    <span class="text-muted small font-monospace">{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mb-0 text-dark small" style="white-space: pre-wrap;">{{ $post->content }}</p>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-chat-dots fs-1 mb-3 d-block text-muted opacity-50"></i>
                                No updates posted yet. Encourage your fellow participants by sharing your progress!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
