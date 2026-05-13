@extends('layouts.app')

@section('title', 'Edit user')

@section('content')
    <h1 class="h3 mb-3">Edit user</h1>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                @include('admin.users.partials.form', ['user' => $user])
                <button class="btn btn-primary" type="submit">Update</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
            </form>
        </div>
    </div>
@endsection
