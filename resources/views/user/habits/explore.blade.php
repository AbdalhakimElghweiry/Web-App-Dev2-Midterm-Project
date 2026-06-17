@extends('layouts.app')

@section('title', 'Explore Challenges')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-compass text-primary me-2"></i>Explore Challenges</h1>
            <p class="text-muted mb-0 small">Join habits shared by the community or posted by admins to compete and track together.</p>
        </div>
        <a href="{{ route('user.habits.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to my habits
        </a>
    </div>

    <div class="row g-3">
        @forelse ($habits as $habit)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h2 class="h5 mb-0 text-truncate" style="max-width: 65%;">{{ $habit->name }}</h2>
                            <div>
                                <span class="badge bg-secondary text-capitalize">{{ $habit->difficulty }}</span>
                                @if($habit->user->isAdmin())
                                    <span class="badge bg-danger text-white">Admin Challenge</span>
                                @else
                                    <span class="badge bg-info text-dark">Community</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-tag text-primary"></i> {{ $habit->category ?? 'General' }}
                            <span class="ms-2"><i class="bi bi-person text-muted"></i> By {{ $habit->user->name }}</span>
                        </p>
                        @if ($habit->description)
                            <p class="small text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($habit->description, 150) }}</p>
                        @else
                            <p class="small text-muted flex-grow-1 italic">No description provided.</p>
                        @endif
                        <div class="mt-3">
                            <form action="{{ route('user.habits.join', $habit) }}" method="post">
                                @csrf
                                <button class="btn btn-primary btn-sm w-100" type="submit">
                                    <i class="bi bi-plus-circle-fill me-1"></i>Join this Habit
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4">
                    <div class="display-1 text-muted mb-3"><i class="bi bi-journal-check"></i></div>
                    <h3 class="h5">No new challenges available!</h3>
                    <p class="text-muted max-w-md mx-auto">You have already joined or created all public habits currently available. Check back later or create a new public habit yourself to invite others!</p>
                    <a href="{{ route('user.habits.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-lg me-1"></i>Create a Public Habit
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $habits->links() }}
    </div>
@endsection
