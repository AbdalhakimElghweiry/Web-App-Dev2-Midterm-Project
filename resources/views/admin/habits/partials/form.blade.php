@php
    $habit = $habit ?? null;
@endphp

<div class="mb-3">
    <label class="form-label" for="user_id">Owner</label>
    <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
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
    <label class="form-label" for="category">Category</label>
    <input id="category" name="category" type="text" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $habit->category ?? '') }}">
    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="difficulty">Difficulty</label>
    <select id="difficulty" name="difficulty" class="form-select @error('difficulty') is-invalid @enderror" required>
        @foreach (['easy', 'medium', 'hard'] as $value)
            <option value="{{ $value }}" @selected(old('difficulty', $habit->difficulty ?? 'easy') === $value)>{{ ucfirst($value) }}</option>
        @endforeach
    </select>
    @error('difficulty')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $habit->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
