@extends('layouts.admin')

@section('title', 'Add Product')
@section('page-title', 'Add Product')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4" style="position:relative;">
        <a href="{{ route('admin.products.index') }}" class="btn-admin-light me-3 position-absolute start-0">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-box"></i> Add Product
        </h1>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}">
        @csrf
        @php($mode='create')
        @include('admin.products._form', ['mode' => $mode])
    </form>
</div>
@endsection
