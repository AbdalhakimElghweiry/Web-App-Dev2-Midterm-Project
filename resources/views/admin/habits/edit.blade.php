@extends('layouts.app')

@section('title', 'Edit habit')

@section('content')
    <h1 class="h3 mb-3">Edit habit</h1>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.habits.update', $habit) }}">
                @csrf
                @method('PUT')
                @include('admin.habits.partials.form', ['habit' => $habit, 'users' => $users])
                <button class="btn btn-primary" type="submit">Update</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.habits.index') }}">Cancel</a>
            </form>
        </div>
    </div>
@endsection
