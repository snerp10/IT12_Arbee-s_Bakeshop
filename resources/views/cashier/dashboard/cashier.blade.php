@extends('layouts.cashier')

@section('title', 'Dashboard')
@section('page-title', 'Cashier Dashboard')

@section('content')
    <div class="dashboard-cards">
        <div class="card">
            <h3>Point of Sale</h3>
            <p>Process customer orders and payments</p>
            <a href="/dashboard/cashier/sales">New Sale</a>
        </div>
        <div class="card">
            <h3>Today's Orders</h3>
            <p>View and manage pending orders</p>
            <a href="/dashboard/cashier/orders">View Orders</a>
        </div>
        <div class="card">
            <h3>Customer Management</h3>
            <p>Manage customer information and loyalty programs</p>
            <a href="/dashboard/cashier/customers">View Customers</a>
        </div>
        <div class="card">
            <h3>Daily Summary</h3>
            <p>View today's sales summary and transactions</p>
            <a href="/dashboard/cashier/summary">View Summary</a>
        </div>
    </div>
    
    <div class="card">
        <h3>Cashier Notes</h3>
        <p>Welcome to the Cashier Dashboard! Here you can process sales, manage orders, and handle customer transactions.</p>
    </div>
@endsection