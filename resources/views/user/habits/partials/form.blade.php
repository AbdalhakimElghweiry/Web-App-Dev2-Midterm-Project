@php
    $habit = $habit ?? null;
@endphp

<div class="mb-3">
    <label class="form-label" for="name">Name</label>
    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $habit->name ?? '') }}" required maxlength="255" placeholder="e.g. Morning gym">
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="category">Category</label>
    <input id="category" name="category" type="text" class="form-control @error('category') is-invalid @enderror"
           value="{{ old('category', $habit->category ?? '') }}" maxlength="100" placeholder="Health, Study, Wellness…">
    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="difficulty">Difficulty</label>
    <select id="difficulty" name="difficulty" class="form-select @error('difficulty') is-invalid @enderror" required>
        @foreach (['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard'] as $value => $label)
            <option value="{{ $value }}" @selected(old('difficulty', $habit->difficulty ?? 'easy') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('difficulty')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="description">Description (optional)</label>
    <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
              maxlength="2000" placeholder="Why does this habit matter to you?">{{ old('description', $habit->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
