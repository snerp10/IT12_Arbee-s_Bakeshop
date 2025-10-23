@extends('layouts.admin')

@section('title', 'Create Category')
@section('page-title', 'Create Category')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.categories.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-plus-circle"></i> New Category
        </h1>
    </div>

    <div class="card shadow-sm kpi-card info">
        <div class="card-body admin-card-body">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="needs-validation" novalidate>
                @csrf
                @include('admin.categories._form')

                <div class="row justify-content-center mt-3">
                    <div class="col-12 mx-auto d-flex justify-content-end gap-2">
                        <button type="reset" class="btn-admin-light w-25">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                        <button type="submit" class="btn-admin-secondary w-25">
                            <i class="fas fa-save me-2"></i> Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection