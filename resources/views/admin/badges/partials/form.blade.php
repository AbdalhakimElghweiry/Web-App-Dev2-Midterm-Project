@php
    use App\Models\Badge;
    $badge = $badge ?? null;
    $types = [
        Badge::TYPE_FIRST_COMPLETION => 'First completion (any habit)',
        Badge::TYPE_CONSECUTIVE_DAYS => 'Longest daily streak (≥1 habit / day)',
        Badge::TYPE_TOTAL_COMPLETIONS => 'Total completed logs (all habits)',
        Badge::TYPE_HABIT_NAME_COMPLETIONS => 'Completions for habits whose name contains text (meta)',
        Badge::TYPE_ALL_HABITS_ONE_DAY => 'Days where user finished every habit they had that day',
        Badge::TYPE_MULTI_HABIT_ONE_DAY => 'Most different habits finished in a single day',
    ];
    $metaDefault = old('meta_json', $badge && $badge->meta ? json_encode($badge->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '');
@endphp

<div class="mb-3">
    <label class="form-label" for="name">Name</label>
    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $badge->name ?? '') }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" rows="2" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $badge->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label" for="icon">Icon (Bootstrap Icons class)</label>
    <input id="icon" name="icon" type="text" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $badge->icon ?? '') }}" placeholder="bi-droplet">
    <div class="form-text">See <a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">Bootstrap Icons</a> (example: <code>bi-trophy-fill</code>).</div>
    @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label" for="requirement_type">Requirement type</label>
        <select id="requirement_type" name="requirement_type" class="form-select @error('requirement_type') is-invalid @enderror" required>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected(old('requirement_type', $badge->requirement_type ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('requirement_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="requirement_value">Requirement value</label>
        <input id="requirement_value" name="requirement_value" type="number" min="1" class="form-control @error('requirement_value') is-invalid @enderror" value="{{ old('requirement_value', $badge->requirement_value ?? 1) }}" required>
        @error('requirement_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label" for="meta_json">Meta JSON (optional)</label>
    <textarea id="meta_json" name="meta_json" rows="3" class="form-control @error('meta_json') is-invalid @enderror" placeholder='{"name_contains":"water"}'>{{ $metaDefault }}</textarea>
    <div class="form-text">Required for “habit name contains” badges.</div>
    @error('meta_json')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
