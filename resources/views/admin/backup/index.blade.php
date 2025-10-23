@extends('layouts.admin')
@section('title', 'Backup & Restore')
@section('page-title', 'Backup & Restore')

@section('content')
<div class="container-fluid">
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card kpi-card shadow h-100">
        <div class="card-body admin-card-body">
          <h5 class="mb-3"><i class="fas fa-download me-2"></i> Download Backup</h5>
          <p class="text-muted">Download a copy of the current database. For production MySQL, integrate a proper backup tool.</p>
          <button type="button" onclick="window.location='{{ route('admin.backup.download') }}'" class="btn-admin-primary">
            <i class="fas fa-file-archive me-2"></i> Download
          </button>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card kpi-card shadow h-100">
        <div class="card-body admin-card-body">
          <h5 class="mb-3"><i class="fas fa-upload me-2"></i> Restore Backup</h5>
          <p class="text-muted">Upload a database file to restore. Site enters maintenance mode during restore.</p>
          <form method="POST" action="{{ route('admin.backup.restore') }}" enctype="multipart/form-data" class="row g-3">
            @csrf
            <div class="col-12">
              <input type="file" name="backup_file" class="form-control" required>
            </div>
            <div class="col-12">
              <button type="submit" class="btn-admin-secondary"><i class="fas fa-rotate me-2"></i> Restore</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
