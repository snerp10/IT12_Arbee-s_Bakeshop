<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cashier Dashboard') - Arbees Bakeshop</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Arbee\'s_logo_round.png') }}">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Sea-style CSS -->
    <link href="{{ asset('css/sea-styles.css') }}" rel="stylesheet">
    <!-- Admin (Sea-style) CSS for button/badge parity -->
    <link href="{{ asset('css/admin-styles.css') }}" rel="stylesheet">
    
    <!-- Cashier-specific CSS -->
    <link href="{{ asset('css/cashier-styles.css') }}" rel="stylesheet">

    @yield('extra-css')
</head>
<body class="cashier-body">
    
    <!-- Cashier Header -->
    <header class="cashier-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/Arbee\'s_Logo.png') }}" alt="Arbee's Logo" class="header-logo me-3">
                    <div class="brand-content">
                        <div class="logo">Arbees Bakeshop</div>
                        <div class="company-name">Cashier Portal</div>
                    </div>
                </div>
                
                <div class="user-info">
                    <span class="user-name">
                        <i class="fas fa-user-circle me-1"></i>
                        {{ auth()->user()->employee->first_name ?? 'User' }} {{ auth()->user()->employee->last_name ?? '' }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="cashier-nav">
        <div class="container-fluid">
            <ul>
                <li>
                    <a href="{{ route('cashier.dashboard') }}" 
                       class="{{ request()->routeIs('cashier.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line me-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('cashier.pos.index') }}" 
                       class="{{ request()->routeIs('cashier.pos.*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register me-2"></i> Point of Sale
                    </a>
                </li>
                <li>
                    <a href="{{ route('cashier.sales.index') }}" 
                       class="{{ request()->routeIs('cashier.sales.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt me-2"></i> Sales History
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="cashier-content">
        <div class="container-fluid py-4">
            
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Validation Errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </main>



    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    @yield('extra-js')
</body>
</html>