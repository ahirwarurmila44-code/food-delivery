<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'FoodZone')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
      .py-4{
        hight:100vh;
      }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/">FoodZone</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <!-- <li class="nav-item"><a class="nav-link" href="/cart">Cart</a></li> -->
        <li class="nav-item"><a class="nav-link" href="/register">Register</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="py-4">
  @yield('content')
</main>
@if(Route::has('login'))
<footer class="bg-dark text-white text-center py-3 mt-5">
  &copy; {{ date('Y') }} FoodZone. All rights reserved.
</footer>
@endif
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

</body>
</html>
