<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f4f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { width: 100%; max-width: 440px; border: none; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,.1); }
        .auth-header { background: linear-gradient(135deg, #2d6a4f, #52b788); border-radius: 16px 16px 0 0; padding: 2rem; text-align: center; color: white; }
        .btn-primary { background: #2d6a4f; border-color: #2d6a4f; }
        .btn-primary:hover { background: #1b4332; border-color: #1b4332; }
        a { color: #2d6a4f; }
    </style>
</head>
<body>
    <div class="auth-card card">
        <div class="auth-header">
            <a href="{{ route('home') }}" class="text-white text-decoration-none">
                <i class="bi bi-shop fs-2"></i>
                <div class="fw-bold fs-4 mt-1">E-Shop</div>
            </a>
        </div>
        <div class="card-body p-4">
            {{ $slot }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>