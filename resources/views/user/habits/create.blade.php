@extends('layouts.app')

@section('title', 'Add habit')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-3">Add a habit</h1>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="post" action="{{ route('user.habits.store') }}">
                        @csrf
                        @include('user.habits.partials.form', ['habit' => null])
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="submit">Save habit</button>
                            <a class="btn btn-outline-secondary" href="{{ route('user.habits.index') }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
