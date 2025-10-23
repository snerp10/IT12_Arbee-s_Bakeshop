@extends('layouts.admin')
@section('title', 'Pending User Approvals')
@section('page-title', 'Pending User Approvals')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <h1 class="h3 mb-0 text-muted-sea text-center flex-grow-1">
            <i class="fas fa-user-clock"></i> Pending User Approvals
        </h1>
    </div>
    <div class="card kpi-card shadow">
        <div class="card-body admin-card-body">
            @if($pendingUsers->count())
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Requested</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingUsers as $user)
                                <tr>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge-admin-role {{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                                    <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-admin-primary btn-sm"><i class="fas fa-check me-1"></i> Approve</button>
                                        </form>
                                        <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="d-inline ms-2">
                                            @csrf
                                            <button type="submit" class="btn-admin-light btn-sm"><i class="fas fa-times me-1"></i> Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-user-clock fa-3x text-gray-300 mb-3"></i>
                    <h5>No pending user requests</h5>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
