@php
    $habit = $habit ?? null;
    $isPrivate = $habit && $habit->type === 'private';
@endphp

<div class="mb-3">
    <label class="form-label" for="user_id">Owner</label>
    <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required @disabled($isPrivate)>
        @foreach ($users as $u)
            <option value="{{ $u->id }}" @selected(old('user_id', $habit->user_id ?? '') == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
        @endforeach
    </select>
    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="name">Name</label>
    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $habit->name ?? '') }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="type">Privacy Type</label>
    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required @disabled($isPrivate)>
        <option value="private" @selected(old('type', $habit->type ?? 'private') === 'private')>Private</option>
        <option value="public" @selected(old('type', $habit->type ?? 'public') === 'public')>Public</option>
    </select>
    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($isPrivate)
        <input type="hidden" name="type" value="private">
        <input type="hidden" name="user_id" value="{{ $habit->user_id }}">
    @endif
</div>

@if ($isPrivate)
    <div class="alert alert-warning border-0 small mb-3">
        <i class="bi bi-shield-lock-fill me-1"></i> This is a private habit. The description, category, and difficulty are hidden and cannot be updated.
    </div>
@endif

<div class="mb-3">
    <label class="form-label" for="category">Category</label>
    <input id="category" name="category" type="text" class="form-control @error('category') is-invalid @enderror" 
           value="{{ $isPrivate ? '[Private]' : old('category', $habit->category ?? '') }}" @disabled($isPrivate)>
    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="difficulty">Difficulty</label>
    <select id="difficulty" name="difficulty" class="form-select @error('difficulty') is-invalid @enderror" required @disabled($isPrivate)>
        @if ($isPrivate)
            <option value="easy" selected>[Private]</option>
        @else
            @foreach (['easy', 'medium', 'hard'] as $value)
                <option value="{{ $value }}" @selected(old('difficulty', $habit->difficulty ?? 'easy') === $value)>{{ ucfirst($value) }}</option>
            @endforeach
        @endif
    </select>
    @error('difficulty')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror" @disabled($isPrivate)>{{ $isPrivate ? '[Private]' : old('description', $habit->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
