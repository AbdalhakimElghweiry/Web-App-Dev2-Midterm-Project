@extends('layouts.app')

@section('title', 'Manage badges')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Badges</h1>
        <a href="{{ route('admin.badges.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New badge</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Rule</th>
                    <th>Value</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($badges as $badge)
                    <tr>
                        <td class="text-primary fs-4"><i class="bi {{ $badge->icon ?: 'bi-award' }}"></i></td>
                        <td>
                            <div class="fw-semibold">{{ $badge->name }}</div>
                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($badge->description, 80) }}</div>
                        </td>
                        <td><code>{{ $badge->requirement_type }}</code></td>
                        <td>{{ $badge->requirement_value }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.badges.edit', $badge) }}">Edit</a>
                            <form class="d-inline" method="post" action="{{ route('admin.badges.destroy', $badge) }}" onsubmit="return confirm('Delete badge?');">
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
        <div class="card-footer bg-white">{{ $badges->links() }}</div>
    </div>
@endsection
