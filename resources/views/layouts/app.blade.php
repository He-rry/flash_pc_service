<!DOCTYPE html>
<html lang="en">

<head>
    @stack('styles')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Service Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            border-radius: 15px 15px 0 0;
        }

        .nav-link p {
            display: inline;
            margin: 0;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, .8);
        }

        .navbar-dark .navbar-nav .nav-link:hover {
            color: #fff;
        }

        .dropdown-item i {
            width: 20px;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <span class="text-warning">The Flash</span>⚡PC Service
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @can('view-services')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.services.index') }}">Services</a>
                    </li>
                    @endcan
                    @can('view-settings')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="settingsDrop" data-bs-toggle="dropdown">
                            Settings
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.statuses.index') }}">Status Settings</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.service-types.index') }}">Service Types</a></li>
                        </ul>
                    </li>
                    @endcan
                    @can('route-view')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="routesDrop" data-bs-toggle="dropdown">
                            Routes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.maps.index') }}"><i class="fas fa-map-marked-alt"></i> Route Planner</a></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.maps.saved') }}">
                                    <i class="fas fa-route"></i> Saved Routes
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endcan
                    @can('view-shop-management')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="shopDrop" data-bs-toggle="dropdown">
                            Shops
                        </a>
                        <ul class="dropdown-menu">
                            @can('shop-create')
                            <li><a class="dropdown-item" href="{{ route('admin.shops.create') }}"><i class="fas fa-plus-circle"></i> Add New Shop</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcan

                    {{-- ၅။ Admin Tools (Super Admin & Log Manager Only) --}}
                    @if(auth()->user()->hasAnyRole(['super-admin', 'log-manager']))
                    <li class="nav-item dropdown border-start ms-lg-2 ps-lg-2">
                        <a class="nav-link dropdown-toggle text-info" href="#" id="adminTools" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield"></i> Admin Tools
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            @hasrole('super-admin')
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users-cog"></i> User Management
                                </a>
                            </li>
                            @endhasrole

                            @can('view-logs')
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.logs.index') }}">
                                    <i class="fas fa-history"></i> Activity Logs
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endif
                </ul>

                <ul class="navbar-nav ms-auto">
                    {{-- User Profile & Logout Section --}}
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header small text-muted">Role: {{ auth()->user()->getRoleNames()->first() ?? 'User' }}</li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mb-5">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>