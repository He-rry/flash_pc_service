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
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">The Flash⚡PC service</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @unless(auth()->user()->isLogManager() ?? false)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.services.index') }}">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.statuses.index') }}">Status Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.service-types.index') }}">Service Types</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.maps.index') }}" class="nav-link">
                            <i class="fas fa-map-marked-alt"></i> Route Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.maps.saved') }}" class="nav-link">
                            <i class="fas fa-list"></i> View Saved Routes
                        </a>
                    </li>
                    @can('manage-shops')
                    <li class="nav-item">
                        <a href="{{ route('admin.shops.create') }}" class="nav-link">
                            <i class="fas fa-plus-circle"></i> Add New Shop
                        </a>
                    </li>
                    @endcan
                    @endunless
                    @can('view-logs')
                    <li class="nav-item">
                        <a href="{{ route('admin.logs.index') }}" class="nav-link">
                            <i class="fas fa-history"></i> Activity Logs
                        </a>
                    </li>
                    @endcan
                </ul>
                <ul class="navbar-nav ms-auto">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
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

    {{-- Success/Error Alert တွေကို content ထဲမှာ ထည့်တာ ပိုကောင်းပါတယ် --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>