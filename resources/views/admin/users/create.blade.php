@extends('layouts.app')

@section('title', 'Create user')

@section('content')
    <h1 class="h3 mb-3">Create user</h1>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.users.store') }}">
                @csrf
                @include('admin.users.partials.form', ['user' => null])
                <button class="btn btn-primary" type="submit">Save</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
            </form>
        </div>
    </div>
@endsection
