<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Toastr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <!-- <link rel="stylesheet" href="{{ asset('admin/assets/css/styles.css') }}"> -->
    <style>
.toggle-status:hover {
    opacity: 0.85;
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="d-flex" id="admin-wrapper" >
    <!-- Sidebar -->
    <nav class="bg-dark sidebar p-3" id="sidebar" style="min-width: 220px; height: 100vh;">
        <h5 class="text-white mb-4">🍽 Food Admin</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.categories.index') }}" class="nav-link text-white {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                    <i class="bi bi-list me-2"></i> Categories
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.customers.index') }}" class="nav-link text-white {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Customers
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.orders.index') }}" class="nav-link text-white {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                    <i class="bi bi-pen me-2"></i> Orders
                </a>
            </li>
             <li class="nav-item">
                <a href="{{ route('admin.restaurants.index') }}" class="nav-link text-white {{ request()->routeIs('admin.restaurants.index') ? 'active' : '' }}">
                    <i class="bi bi-house me-2"></i> Restaurants
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.products.index') }}" class="nav-link text-white {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                    <i class="bi bi-diagram-2 me-2"></i> Products
                </a>
            </li>
            <li class="nav-item mt-3">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="btn btn-danger w-100" type="submit">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="flex-grow-1">
        <header class="bg-white border-bottom py-3 px-4 shadow-sm d-flex justify-content-between align-items-center">
            <h4 class="mb-0">@yield('title')</h4>
            <button class="btn btn-outline-secondary d-lg-none" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>
        </header>

        <main class="p-4" style="height: 80vh;">
            @yield('content')
        </main>

        <footer class="text-center py-3 mb-0 text-muted small border-top">
            © {{ date('Y') }} Food Delivery Admin Panel
        </footer>
    </div>
</div>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Flash message
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif

    // Sidebar toggle
    $('#toggleSidebar').on('click', function () {
        $('#sidebar').toggleClass('d-none');
    });
</script>
@stack('scripts')
</body>
</html>
