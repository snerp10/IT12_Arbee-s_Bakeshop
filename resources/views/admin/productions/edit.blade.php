@extends('layouts.admin')
@section('title', 'Edit Production')
@section('page-title', 'Edit Production')

@section('content')
<div class="d-flex align-items-center mb-4" style="position:relative;">
    <a href="{{ route('admin.productions.index') }}" class="btn-admin-light me-3 position-absolute start-0">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
    <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
        <i class="fas fa-edit me-2"></i> Edit Production - {{ $production->batch_number }}
    </h1>
</div>

<form method="POST" action="{{ route('admin.productions.update', $production) }}">
    @csrf
    @method('PUT')
    @include('admin.productions._form')
</form>
@endsection
