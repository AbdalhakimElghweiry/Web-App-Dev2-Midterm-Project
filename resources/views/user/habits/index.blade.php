@extends('layouts.app')

@section('title', 'My habits')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">My habits</h1>
            <p class="text-muted mb-0 small">Create, edit, or remove habits you want to track.</p>
        </div>
        <a href="{{ route('user.habits.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add habit</a>
    </div>

    <div class="row g-3">
        @forelse ($habits as $habit)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h2 class="h5 mb-0">{{ $habit->name }}</h2>
                            <span class="badge bg-secondary text-capitalize">{{ $habit->difficulty }}</span>
                        </div>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-tag"></i> {{ $habit->category ?? 'General' }}
                        </p>
                        @if ($habit->description)
                            <p class="small text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($habit->description, 120) }}</p>
                        @endif
                        <div class="d-flex gap-2 mt-3">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('user.habits.edit', $habit) }}">Edit</a>
                            <form action="{{ route('user.habits.destroy', $habit) }}" method="post" onsubmit="return confirm('Delete this habit?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm">
                    No habits yet. Use “Add habit” to create your first one (Gym, Study, Water, Reading, or anything custom).
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $habits->links() }}
    </div>
@endsection
