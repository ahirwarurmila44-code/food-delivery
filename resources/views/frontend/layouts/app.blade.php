<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - Food Delivery</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css"/>
  <style>
    body {
      background-color: #f4f6f9;
    }
    .navbar {
      border-bottom: 1px solid #ddd;
    }
    .nav-link.active {
      font-weight: 600;
      color: #0d6efd !important;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .cart-animate {
    animation: pop 0.3s ease-in-out;
}
@keyframes pop {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}

.translate-middle {
    transform: translate(-86%, -76%) !important;
}
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">🍔 FoodDelivery</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-shop"></i> Restaurants</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-box"></i> Products</a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link position-relative" href="#">
            <i class="bi bi-cart-fill"></i> Cart
            <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            {{ session('cart') ? count(session('cart')) : 0 }}
            </span>
            {{-- <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span> --}}
          </a>
        </li> -->
        <li class="nav-item dropdown">
          <a class="nav-link position-relative dropdown-toggle" href="#" id="cartDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-cart-fill"></i>
            <span id="cart-count" class="badge bg-danger position-absolute top-2  translate-middle rounded-pill">
                {{ session('cart') ? count(session('cart')) : 0 }}
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width: 300px;" id="cart-dropdown-list">
            <li class="text-muted text-center">Cart is empty</li>
          </ul>
        </li>
        
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="#">🧾 Order History</a></li>
            <li>
              <form action="{{ route('user.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
              </form>
            </li>
          </ul>
        </li>
        <li>
          <ul class="navbar-nav ms-auto">
            @auth
                <li class="nav-item"><a class="nav-link" href="/checkout">Checkout</a></li>
                <li class="nav-item"><form method="POST" action="{{ route('user.logout') }}">@csrf
                    <button class="btn btn-link nav-link text-danger"> <strong>Logout</strong> </button></form></li>
            @else
                <li class="nav-item "><a class="nav-link " href="{{ route('user.login') }}"><strong>
                  Login</strong></a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('user.register') }}">Register</a></li>
            @endauth
        </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
    <main class="container my-4">
        @yield('content')
    </main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" ></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

@stack('scripts')
</body>
</html>
