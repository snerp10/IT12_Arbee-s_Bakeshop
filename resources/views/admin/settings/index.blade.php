@extends('layouts.admin')
@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="container-fluid">
  <div class="card kpi-card shadow">
    <div class="card-body admin-card-body">
      <form method="POST" action="{{ route('admin.settings.update') }}" class="row g-3">
        @csrf
        @method('PUT')
        <div class="col-md-6">
          <label class="form-label">Store Name</label>
          <input type="text" class="form-control" name="store_name" value="{{ $settings['store.name'] }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Branch</label>
          <input type="text" class="form-control" name="store_branch" value="{{ $settings['store.branch'] }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Theme</label>
          <select class="form-select" name="ui_theme">
            <option value="sea" {{ $settings['ui.theme']==='sea' ? 'selected' : '' }}>Sea Style</option>
          </select>
        </div>
        <div class="col-12">
          <button class="btn-admin-primary" type="submit"><i class="fas fa-save me-2"></i> Save Settings</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
