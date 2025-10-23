@extends('layouts.baker')

@section('title', 'Dashboard')
@section('page-title', 'Baker Dashboard')

@section('content')
    <div class="dashboard-cards">
        <div class="card">
            <h3>Today's Production</h3>
            <p>View and manage today's baking schedule</p>
            <a href="/dashboard/baker/production">View Production</a>
        </div>
        <div class="card">
            <h3>Recipe Book</h3>
            <p>Access all recipes and baking instructions</p>
            <a href="/dashboard/baker/recipes">View Recipes</a>
        </div>
        <div class="card">
            <h3>Ingredient Inventory</h3>
            <p>Check available ingredients and supplies</p>
            <a href="/dashboard/baker/inventory">View Inventory</a>
        </div>
        <div class="card">
            <h3>Quality Control</h3>
            <p>Record quality checks and batch information</p>
            <a href="/dashboard/baker/quality">Quality Control</a>
        </div>
    </div>
    
    <div class="card">
        <h3>Baker's Notes</h3>
        <p>Welcome to the Baker Dashboard! Here you can manage your daily production, access recipes, and monitor ingredient levels.</p>
    </div>
@endsection