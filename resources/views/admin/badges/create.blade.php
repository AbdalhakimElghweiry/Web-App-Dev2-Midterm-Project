@extends('layouts.app')

@section('title', 'Create badge')

@section('content')
    <h1 class="h3 mb-3">Create badge</h1>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.badges.store') }}">
                @csrf
                @include('admin.badges.partials.form', ['badge' => null])
                <button class="btn btn-primary" type="submit">Save</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.badges.index') }}">Cancel</a>
            </form>
        </div>
    </div>
@endsection
