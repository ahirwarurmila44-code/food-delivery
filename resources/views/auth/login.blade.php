<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Login - Food Delivery</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .login-card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .login-header {
      font-weight: 600;
      color: #5a5a5a;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card login-card p-4">
        <div class="text-center mb-4">
          <h4 class="login-header">🍽️ Food Delivery - User Login</h4>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Whoops!</strong> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form method="POST" action="{{ route('user.login.submit') }}">
          @csrf

          <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
              <input type="email" class="form-control" name="email" id="email" required autofocus>
            </div>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
              <input type="password" class="form-control" name="password" id="password" required>
            </div>
          </div>

          <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
          </div>

          <div class="text-center">
            <small class="text-muted">Don't have an account? <a href="#">Register</a></small>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
