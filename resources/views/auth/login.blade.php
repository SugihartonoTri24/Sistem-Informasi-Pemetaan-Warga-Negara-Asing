<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Peta Sebaran WNA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-login {
            max-width: 400px;
            width: 100%;
            border: none;
            border-radius: 12px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

<div class="card card-login shadow-sm p-4">
    <div class="text-center mb-4">
        <h4 class="fw-bold">Masuk Sistem</h4>
        <p class="text-muted small">Sistem Informasi Sebaran Lokasi WNA</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" 
                   placeholder="nama@email.com" 
                   required 
                   autofocus>
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" 
                   name="password" 
                   id="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   placeholder="••••••••" 
                   required>
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label text-muted small" for="remember">Ingat Saya</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            Masuk
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>