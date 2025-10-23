@extends('layouts.admin')
@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-muted-sea">
      <i class="fas fa-history me-2"></i> Audit Logs
    </h1>
    <div class="btn-group">
      <a href="{{ route('admin.audit-logs.export') }}" class="btn-admin-light me-2">
        <i class="fas fa-file-csv me-2"></i> CSV
      </a>
      <a href="{{ route('admin.audit-logs.export-pdf', request()->all()) }}" class="btn-admin-primary">
        <i class="fas fa-file-pdf me-2"></i> PDF
      </a>
    </div>
  </div>

  <div class="card admin-filter-card shadow mb-4">
    <div class="card-body admin-card-body">
      <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-2">
          <label class="form-label">Action</label>
          <select name="action" class="form-select">
            <option value="">All</option>
            @foreach($actions as $a)
              <option value="{{ $a }}" {{ request('action')===$a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Table</label>
          <input type="text" name="table_name" class="form-control" value="{{ request('table_name') }}" placeholder="e.g., products">
        </div>
        <div class="col-md-2">
          <label class="form-label">User</label>
          <select name="user_id" class="form-select">
            <option value="">All</option>
            @foreach($users as $u)
              <option value="{{ $u->user_id }}" {{ request('user_id')==$u->user_id ? 'selected' : '' }}>{{ $u->username }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Date From</label>
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label">Date To</label>
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label">Search</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Notes, action, table">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn-admin-secondary"><i class="fas fa-filter me-1"></i> Apply</button>
            <button type="reset" onclick="window.location='{{ route('admin.audit-logs.index') }}'" class="btn-admin-light ms-2">
                <i class="fas fa-undo me-1"></i> Reset
            </button>        
        </div>
      </form>
    </div>
  </div>

  <div class="card kpi-card shadow">
    <div class="card-body admin-card-body">
      <div class="table-responsive">
        <table class="table table-borderless align-middle">
          <thead>
            <tr>
              <th>Date</th>
              <th>User</th>
              <th>Action</th>
              <th>Table</th>
              <th>Record ID</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
              <tr>
                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $log->user->username ?? '—' }}</td>
                <td><span class="badge-admin-role {{ $log->action==='delete' ? 'admin' : ($log->action==='update' ? 'baker' : '') }}">{{ ucfirst($log->action) }}</span></td>
                <td>{{ $log->table_name ?? '—' }}</td>
                <td>{{ $log->record_id ?? '—' }}</td>
                <td>{{ $log->description }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted">No audit entries found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center mt-4">
        {{ $logs->links('vendor.pagination.admin') }}
      </div>
    </div>
  </div>
</div>
@endsection