@extends('layouts.app')

@section('title', 'Manage habits')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">All habits</h1>
        <a href="{{ route('admin.habits.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New habit</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>Habit</th>
                    <th>Owner</th>
                    <th>Category</th>
                    <th>Difficulty</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($habits as $habit)
                    <tr>
                        <td class="fw-semibold">{{ $habit->name }}</td>
                        <td>{{ $habit->user->name }}</td>
                        <td>{{ $habit->category ?? '—' }}</td>
                        <td><span class="badge bg-secondary text-capitalize">{{ $habit->difficulty }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.habits.edit', $habit) }}">Edit</a>
                            <form class="d-inline" method="post" action="{{ route('admin.habits.destroy', $habit) }}" onsubmit="return confirm('Delete this habit?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $habits->links() }}</div>
    </div>
@endsection
