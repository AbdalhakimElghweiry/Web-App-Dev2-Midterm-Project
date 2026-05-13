@extends('layouts.app')

@section('title', 'Edit badge')

@section('content')
    <h1 class="h3 mb-3">Edit badge</h1>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.badges.update', $badge) }}">
                @csrf
                @method('PUT')
                @include('admin.badges.partials.form', ['badge' => $badge])
                <button class="btn btn-primary" type="submit">Update</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.badges.index') }}">Cancel</a>
            </form>
        </div>
    </div>
@endsection
