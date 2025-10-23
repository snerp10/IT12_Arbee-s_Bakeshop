<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arbees Bakeshop - @yield('title')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Arbee\'s_logo_round.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/Arbee\'s_logo_round.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/Arbee\'s_logo_round.png') }}">
    
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/sea-styles.css">
    <link rel="stylesheet" href="/css/admin-styles.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Left Sidebar -->
        <div class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand-container">
                    <div class="sidebar-brand" onclick="toggleSidebarCollapse()">
                        <img src="{{ asset('images/Arbee\'s_Logo.png') }}" alt="Arbee's Logo">
                        <div class="brand-text text-white">
                            <div>Arbees Bakeshop</div>
                            <small class="text-muted-sea" style="color: #EEF5DB !important;">Admin Panel</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <div class="nav-section">
                    <div class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}" data-title="Dashboard">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>
                
                <!-- User Management -->
                <div class="nav-section">
                    <div class="nav-section-title">User & Employee Management</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ (Request::is('admin/users*') && !Request::is('admin/users/pending')) ? 'active' : '' }}" data-title="User Management">
                            <i class="fas fa-users nav-icon"></i>
                            <span>User Management</span>
                        </a>
                        <div class="nav-subitem ms-4">
                            @if(\App\Models\User::where('status', 'pending')->count() > 0)
                                <span class="badge bg-warning text-dark ms-2">{{ \App\Models\User::where('status', 'pending')->count() }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.employees.index') }}" class="nav-link {{ Request::is('admin/employees*') ? 'active' : '' }}" data-title="Employee Management">
                            <i class="fas fa-id-badge nav-icon"></i>
                            <span>Employee Management</span>
                        </a>
                    </div>
                </div>
                
                <!-- Product Management -->
                <div class="nav-section">
                    <div class="nav-section-title">Product & Inventory</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.products.index') }}" class="nav-link {{ Request::is('admin/products*') ? 'active' : '' }}" data-title="Product Management">
                            <i class="fas fa-box nav-icon"></i>
                            <span>Product Management</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ Request::is('admin/categories*') ? 'active' : '' }}" data-title="Categories">
                            <i class="fas fa-tags nav-icon"></i>
                            <span>Categories</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.inventory.index') }}" class="nav-link {{ Request::is('admin/inventory') || Request::is('admin/inventory/create') || Request::is('admin/inventory/*/edit') || (Request::is('admin/inventory/*') && !Request::is('admin/inventory-reports*')) ? 'active' : '' }}" data-title="Inventory Management">
                            <i class="fas fa-warehouse nav-icon"></i>
                            <span>Inventory Management</span>
                        </a>
                    </div>
                </div>
                
                <!-- Operations -->
                <div class="nav-section">
                    <div class="nav-section-title">Operations</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.productions.index') }}" class="nav-link {{ Request::is('admin/productions*') ? 'active' : '' }}" data-title="Production">
                            <i class="fas fa-bread-slice nav-icon"></i>
                            <span>Production</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.sales.index') }}" class="nav-link {{ Request::is('admin/sales') || Request::is('admin/sales/create') || Request::is('admin/sales/*/edit') || preg_match('/^admin\/sales\/\d+$/', Request::path()) ? 'active' : '' }}" data-title="Sales Management">
                            <i class="fas fa-cash-register nav-icon"></i>
                            <span>Sales Management</span>
                        </a>
                    </div>
                </div>
                
                <!-- Reports & Analytics -->
                <div class="nav-section">
                    <div class="nav-section-title">Reports & Analytics</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.sales-reports.index') }}" class="nav-link {{ Request::is('admin/sales-reports*') ? 'active' : '' }}" data-title="Sales Reports">
                            <i class="fas fa-chart-line nav-icon"></i>
                            <span>Sales Reports</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.inventory-reports.index') }}" class="nav-link {{ Request::is('admin/inventory-reports*') ? 'active' : '' }}" data-title="Inventory Reports">
                            <i class="fas fa-chart-pie nav-icon"></i>
                            <span>Inventory Reports</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ Request::is('admin/analytics*') ? 'active' : '' }}" data-title="Analytics Dashboard">
                            <i class="fas fa-chart-line nav-icon"></i>
                            <span>Analytics Dashboard</span>
                        </a>
                    </div>
                </div>
                
                <!-- System -->
                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ Request::is('admin/audit-logs*') ? 'active' : '' }}" data-title="Audit Logs">
                            <i class="fas fa-history nav-icon"></i>
                            <span>Audit Logs</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ Request::is('admin/settings*') ? 'active' : '' }}" data-title="System Settings">
                            <i class="fas fa-cog nav-icon"></i>
                            <span>System Settings</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.backup.index') }}" class="nav-link {{ Request::is('admin/backup*') ? 'active' : '' }}" data-title="Backup & Restore">
                            <i class="fas fa-database nav-icon"></i>
                            <span>Backup & Restore</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Early restore of sidebar scroll to prevent visible jump -->
        <script>
            (function() {
                try {
                    var sidebar = document.getElementById('adminSidebar');
                    if (sidebar) {
                        var saved = parseInt(localStorage.getItem('sidebarScrollTop') || '0', 10);
                        if (!isNaN(saved) && saved >= 0) {
                            sidebar.scrollTop = saved;
                        }
                        // Also restore collapsed state as early as possible
                        var collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                        if (collapsed) {
                            sidebar.classList.add('collapsed');
                            var adminMain = document.querySelector('.admin-main');
                            if (adminMain) adminMain.classList.add('sidebar-collapsed');
                        }
                    }
                } catch (e) {}
            })();
        </script>

        <!-- Main Content Area -->
        <div class="admin-main">
            <!-- Top Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="mobile-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->employee ? Auth::user()->employee->first_name . ' ' . Auth::user()->employee->last_name : Auth::user()->username }}</span>
                        <span class="role-badge">ADMIN</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Content -->
            <main class="admin-content">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Admin Scripts -->
    <script>
        // Desktop sidebar collapse toggle
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('adminSidebar');
            const adminMain = document.querySelector('.admin-main');
            sidebar.classList.toggle('collapsed');
            adminMain.classList.toggle('sidebar-collapsed');
            
            // Save the sidebar state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }
        
        // Restore sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const adminMain = document.querySelector('.admin-main');

            // Restore collapsed state
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                adminMain.classList.add('sidebar-collapsed');
            }

            // Restore sidebar scroll position
            const savedScroll = parseInt(localStorage.getItem('sidebarScrollTop') || '0', 10);
            if (!isNaN(savedScroll)) {
                sidebar.scrollTop = savedScroll;
            }

            // Persist scroll position on scroll (throttled)
            let scrollTick = false;
            sidebar.addEventListener('scroll', function() {
                if (!scrollTick) {
                    window.requestAnimationFrame(function() {
                        localStorage.setItem('sidebarScrollTop', String(sidebar.scrollTop));
                        scrollTick = false;
                    });
                    scrollTick = true;
                }
            });

            // Handle navigation link clicks without expanding sidebar in collapsed mode
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (sidebar.classList.contains('collapsed')) {
                        e.stopPropagation();
                    }
                    // Persist current scroll immediately on navigation clicks
                    try {
                        localStorage.setItem('sidebarScrollTop', String(sidebar.scrollTop));
                    } catch (err) {}
                });
            });

            // Persist scroll before page unload as a last resort
            window.addEventListener('beforeunload', function() {
                try {
                    localStorage.setItem('sidebarScrollTop', String(sidebar.scrollTop));
                } catch (err) {}
            });

        });
        
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('show');
        }
        
        // Auto-hide success alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-success');
            alerts.forEach(function(alert) {
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);

        // CSRF token setup for AJAX requests
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }
    </script>
    
    @stack('scripts')
</body>
</html>