<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arbee's Manager - @yield('title')</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/sea-styles.css">
    <link rel="stylesheet" href="/css/manager-styles.css">
    <style>
        /* Manager layout specific overrides only */
    </style>
</head>
<body class="manager-layout">
    <header class="manager-header">
        <div class="manager-logo">Arbee's Bakeshop [MANAGER]</div>
        <div class="user-info">
            <span>Welcome, {{ Auth::user()->full_name }}</span>
            <span class="manager-badge role-badge">MANAGER</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>

    <nav class="manager-nav">
        <ul>
            <li><a href="/dashboard/manager" class="{{ Request::is('dashboard/manager') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="/dashboard/manager/users" class="{{ Request::is('dashboard/manager/users') ? 'active' : '' }}">Manage Users</a></li>
            <li><a href="/dashboard/manager/reports" class="{{ Request::is('dashboard/manager/reports') ? 'active' : '' }}">Reports</a></li>
            <li><a href="/dashboard/manager/inventory" class="{{ Request::is('dashboard/manager/inventory') ? 'active' : '' }}">Inventory</a></li>
        </ul>
    </nav>

    <main class="manager-content">
        <div class="content-header">
            <h1>@yield('page-title')</h1>
        </div>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2025 Sea++ Bakery Management System - Manager Panel</p>
    </footer>
</body>
</html>