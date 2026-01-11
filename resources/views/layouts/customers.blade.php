<!DOCTYPE html>
<html>

<head>
    <title>The Flash⚡- Customer Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>


<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">The Flash⚡PC service</a>
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('customer.report') }}">Report Issue</a>
                <a class="nav-link" href="{{ route('customer.track') }}">Track Status</a>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</body>

</html>