@extends('layouts.admin')
@section('title', 'Audit Log Details')
@section('page-title', 'Audit Log Details')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-muted-sea">
      <i class="fas fa-history me-2"></i> Audit Entry
    </h1>
    <a href="{{ route('admin.audit-logs.index') }}" class="btn-admin-light"><i class="fas fa-arrow-left me-1"></i> Back</a>
  </div>

  <div class="card kpi-card shadow">
    <div class="card-body admin-card-body">
      <table class="table table-borderless">
        <tr><td><strong>Date:</strong></td><td>{{ $audit_log->created_at->format('Y-m-d H:i:s') }}</td></tr>
        <tr><td><strong>User:</strong></td><td>{{ $audit_log->user->username ?? '—' }}</td></tr>
        <tr><td><strong>Action:</strong></td><td>{{ ucfirst($audit_log->action) }}</td></tr>
        <tr><td><strong>Table:</strong></td><td>{{ $audit_log->table_name ?? '—' }}</td></tr>
        <tr><td><strong>Record ID:</strong></td><td>{{ $audit_log->record_id ?? '—' }}</td></tr>
        <tr><td><strong>Description:</strong></td><td>{{ $audit_log->description }}</td></tr>
        <tr>
          <td><strong>Old Values:</strong></td>
          <td><pre class="mb-0" style="white-space: pre-wrap;">{{ json_encode($audit_log->old_values, JSON_PRETTY_PRINT) }}</pre></td>
        </tr>
        <tr>
          <td><strong>New Values:</strong></td>
          <td><pre class="mb-0" style="white-space: pre-wrap;">{{ json_encode($audit_log->new_values, JSON_PRETTY_PRINT) }}</pre></td>
        </tr>
      </table>
    </div>
  </div>
</div>
@endsection
