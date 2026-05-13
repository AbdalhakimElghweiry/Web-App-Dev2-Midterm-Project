@extends('layouts.app')

@section('title', 'Create habit')

@section('content')
    <h1 class="h3 mb-3">Create habit (any user)</h1>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.habits.store') }}">
                @csrf
                @include('admin.habits.partials.form', ['habit' => null, 'users' => $users])
                <button class="btn btn-primary" type="submit">Save</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.habits.index') }}">Cancel</a>
            </form>
        </div>
    </div>
@endsection
