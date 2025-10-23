@extends('layouts.admin')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.categories.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-edit"></i> Edit Category
        </h1>
    </div>

    <div class="card shadow-sm kpi-card secondary">
        <div class="card-body admin-card-body">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                @include('admin.categories._form')

                <div class="col-12 mx-auto d-flex justify-content-end gap-2 mt-4">
                    <button type="reset" class="btn-admin-light w-25">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn-admin-secondary w-25">
                        <i class="fas fa-save me-2"></i> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection