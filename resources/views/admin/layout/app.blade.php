<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - INOVANT E-commerce</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Admin Styles -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .sidebar-heading {
            color: #fff;
            font-weight: 700;
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .page-header {
            background: white;
            padding: 20px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 600;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <!-- Admin Panel Header -->
                    <div class="sidebar-heading">
                        <h4><i class="fas fa-store"></i> INOVANT</h4>
                        <small>E-commerce Admin</small>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <ul class="nav flex-column mt-4">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                               href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" 
                               href="{{ route('admin.products.index') }}">
                                <i class="fas fa-box me-2"></i>
                                Products
                                <span class="badge bg-light text-dark ms-auto">{{ App\Models\Product::count() }}</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" 
                               href="{{ route('admin.orders.index') }}">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Orders
                                <span class="badge bg-warning text-dark ms-auto">{{ App\Models\Order::where('status', 'pending')->count() }}</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.carts.*') ? 'active' : '' }}" 
                               href="{{ route('admin.carts.index') }}">
                                <i class="fas fa-shopping-basket me-2"></i>
                                Cart Management
                                <span class="badge bg-info text-dark ms-auto">{{ App\Models\Cart::count() }}</span>
                            </a>
                        </li>
                        
                        <li class="nav-item mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                            <a class="nav-link" href="{{ url('/') }}" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>
                                View Website
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/api/products') }}" target="_blank">
                                <i class="fas fa-code me-2"></i>
                                API Documentation
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Admin Footer -->
                    <div class="mt-auto pt-4 pb-3 text-center" style="border-top: 1px solid rgba(255,255,255,0.2);">
                        <small class="text-white-50">
                            <i class="fas fa-user-shield me-1"></i>
                            Admin Panel v1.0
                        </small>
                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <div>
                            <h1 class="h2 mb-0">@yield('page-title', 'Dashboard')</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    @yield('breadcrumbs')
                                </ol>
                            </nav>
                        </div>
                        
                        <div class="btn-toolbar mb-2 mb-md-0">
                            @yield('page-actions')
                        </div>
                    </div>
                </div>

                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Validation Error:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Page Content -->
                <div class="content">
                    @yield('content')
                </div>

                <!-- Footer -->
                <footer class="mt-5 py-4 text-center text-muted">
                    <hr>
                    <p>&copy; {{ date('Y') }} INOVANT E-commerce Admin Panel. 
                       Built with <i class="fas fa-heart text-danger"></i> using Laravel {{ app()->version() }}</p>
                </footer>
            </main>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (for enhanced interactions) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom Admin Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Confirm delete actions
        $('form[method="POST"]').on('submit', function(e) {
            if ($(this).find('button[type="submit"]').hasClass('btn-danger')) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            }
        });
        
        // Enhanced file upload preview
        $('input[type="file"][accept*="image"]').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = $(this).siblings('.image-preview');
                    if (preview.length) {
                        preview.html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">');
                    }
                }.bind(this);
                reader.readAsDataURL(file);
            }
        });
        
        // Real-time search functionality
        $('.search-input').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('.searchable-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>
