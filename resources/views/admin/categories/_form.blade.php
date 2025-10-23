<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" maxlength="100" required
               value="{{ old('name', $category->name ?? '') }}" placeholder="e.g., Breads">
    </div>
    <div class="col-md-6">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            @php($statusVal = old('status', $category->status ?? 'active'))
            <option value="active" {{ $statusVal==='active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $statusVal==='inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control" maxlength="500" placeholder="Optional description">{{ old('description', $category->description ?? '') }}</textarea>
    </div>
</div>