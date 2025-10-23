@extends('layouts.admin')

@section('title', 'Category Management')
@section('page-title', 'Category Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-muted-sea">
            <i class="fas fa-tags"></i> Category Management
        </h1>
        <a href="{{ route('admin.categories.create') }}" class="btn-admin-secondary">
            <i class="fas fa-plus me-2"></i> Add Category
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body admin-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Category name">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn-admin-secondary me-2"><i class="fas fa-search me-2"></i>Filter</button>
                    <button type="button" onclick="window.location='{{ route('admin.categories.index') }}'" class="btn-admin-light">
                        <i class="fas fa-times me-2"></i>Clear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card shadow-sm">
        <div class="card-header p-3 d-flex align-items-center justify-content-between card-header-sea-light">
            <h2 class="h6 mb-0 text-sea-dark"><i class="fas fa-list me-2"></i> Categories</h2>
        </div>
        <div class="card-body admin-card-body">
            @if($categories && $categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th class="text-center">Products</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.categories.show', $category) }}" class="text-sea-dark text-decoration-none">
                                        <i class="fas fa-tag text-sea-primary me-2"></i>{{ $category->name }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($category->description, 80) }}</td>
                                <td class="text-center"><span class="badge-chip bg-sea-light">{{ $category->products_count ?? 0 }}</span></td>
                                <td>
                                    @if($category->status === 'active')
                                        <span class="badge-admin-role" style="background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%) !important; color: #fff !important;">
                                            <i class="fas fa-check-circle me-1" aria-label="Active"></i> Active
                                        </span>
                                    @else
                                        <span class="badge-admin-role baker"><i class="fas fa-times-circle me-1" aria-label="Inactive"></i> Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 align-items-center">
                                        <form method="POST" action="{{ route('admin.categories.toggle-status', $category) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            @if($category->status==='active')
                                                <button type="submit" class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1" title="Set Inactive">
                                                    <i class="fas fa-toggle-on" aria-label="Set Inactive"></i>
                                                    <span class="small text-muted">Inactive</span>
                                                </button>
                                            @else
                                                <button type="submit" class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1" title="Set Active">
                                                    <i class="fas fa-toggle-off" aria-label="Set Active"></i>
                                                    <span class="small text-muted">Active</span>
                                                </button>
                                            @endif
                                        </form>
                                        <button type="button" class="btn-admin-light btn-sm border border-1 d-flex align-items-center gap-1" title="View" onclick="window.location='{{ route('admin.categories.show', $category) }}'">
                                            <i class="fas fa-eye" aria-label="View"></i>
                                            <span class="small text-muted">View</span>
                                        </button>
                                        <button type="button" class="btn-admin-secondary btn-sm border border-1 d-flex align-items-center gap-1" title="Edit" onclick="window.location='{{ route('admin.categories.edit', $category) }}'">
                                            <i class="fas fa-edit" aria-label="Edit"></i>
                                            <span class="small text-muted">Edit</span>
                                        </button>
                                        <button type="button" class="btn-admin-delete btn-sm border border-1 d-flex align-items-center gap-1" title="Delete"
                                                data-action="{{ route('admin.categories.destroy', $category) }}"
                                                data-name="{{ $category->name }}"
                                                onclick="openDeleteCategoryModal(this)">
                                            <i class="fas fa-trash" aria-label="Delete"></i>
                                            <span class="small text-white">Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $categories->appends(request()->query())->links('vendor.pagination.admin') }}
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-tags fa-3x mb-3 d-block"></i>
                    No categories found.
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteCategoryModal" class="sea-modal" aria-hidden="true">
        <div class="sea-modal-dialog">
            <div class="sea-modal-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 flex-grow-1" style="flex-basis:80%;max-width:80%;">
                    <i class="fas fa-trash me-2"></i> Confirm Deletion - <span id="deleteCategoryName"></span>
                </h5>
                <div style="flex-basis:10%;max-width:10%;" class="text-end">
                    <button type="button" class="btn-admin-light btn-sm" onclick="closeModal('deleteCategoryModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="sea-modal-body">
                <form id="deleteCategoryForm" method="POST" action="#">
                    @csrf
                    @method('DELETE')
                    <p>Are you sure you want to delete <strong id="deleteCategoryName"></strong>? This action cannot be undone.</p>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn-admin-light me-2" onclick="closeModal('deleteCategoryModal')">Cancel</button>
                        <button type="submit" class="btn-admin-primary">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDeleteCategoryModal(btn){
    const modal = document.getElementById('deleteCategoryModal');
    document.getElementById('deleteCategoryName').textContent = btn.dataset.name;
    const form = document.getElementById('deleteCategoryForm');
    form.action = btn.dataset.action;
    modal.classList.add('show');
}
function closeModal(id){ document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.sea-modal').forEach(m => {
    m.addEventListener('click', e => { if(e.target === m){ m.classList.remove('show'); } });
});
</script>
@endpush