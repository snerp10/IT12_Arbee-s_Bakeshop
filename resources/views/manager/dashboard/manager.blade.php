@extends('layouts.manager')

@section('title', 'Dashboard')
@section('page-title', 'Manager Dashboard')

@section('content')
    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Users</h3>
            <p>Manage all bakery staff and their roles</p>
            <a href="/dashboard/manager/users">View Users</a>
        </div>
        <div class="card">
            <h3>Daily Reports</h3>
            <p>View sales, production, and financial reports</p>
            <a href="/dashboard/manager/reports">View Reports</a>
        </div>
        <div class="card">
            <h3>Inventory Overview</h3>
            <p>Monitor ingredient levels and supplies</p>
            <a href="/dashboard/manager/inventory">View Inventory</a>
        </div>
        <div class="card">
            <h3>Performance Metrics</h3>
            <p>Track bakery performance and efficiency</p>
            <a href="/dashboard/manager/metrics">View Metrics</a>
        </div>
    </div>
    
    <div class="card">
        <h3>Quick Actions</h3>
        <p>Welcome to the Manager Dashboard! You have full access to manage users, view reports, and oversee bakery operations.</p>
    </div>
@endsection