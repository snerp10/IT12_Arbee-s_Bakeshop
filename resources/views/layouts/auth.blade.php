<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arbee's Bakeshop - @yield('title')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Arbee\'s_logo_round.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/Arbee\'s_logo_round.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/Arbee\'s_logo_round.png') }}">
    
    <!-- Circular Favicon CSS -->
    <style>
        /* Make favicon appear circular in browsers that support it */
        link[rel="icon"], link[rel="apple-touch-icon"] {
            border-radius: 50% !important;
        }
        /* Additional circular styling for favicon */
        head link[rel*="icon"] {
            border-radius: 50%;
            overflow: hidden;
        }
    </style>
    
    <link rel="stylesheet" href="/css/sea-styles.css">
    <link rel="stylesheet" href="/css/auth-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-modern-container">
    <!-- Background shapes -->
    <div class="auth-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
        <div class="shape shape-5"></div>
        <div class="shape shape-6"></div>
    </div>

    <div class="auth-main-content">
        <div class="auth-card">
            <div class="auth-card-logo">
                <img src="images/Arbee's_Logo.png" alt="Arbee's Bakeshop Logo">
            </div>
            <h2 class="auth-card-title">@yield('auth-title', 'Login')</h2>
            
            @yield('content')
        </div>
    </div>
</body>
</html>