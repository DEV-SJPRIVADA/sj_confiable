<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingreso — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,400;0,500;0,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body { background: #f8f9fa; font-family: 'Exo', system-ui, sans-serif; }
        .login-card { max-width: 28rem; border-radius: 0.5rem; border: 1px solid #dee2e6; }
    </style>
</head>
<body class="d-flex min-vh-100 align-items-center">
<div class="container py-4">
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo-sj-confiable.png') }}" alt="SJ Confiable" class="img-fluid" style="height:2.7rem; width:auto; max-width:16rem; object-fit:contain;">
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <p class="text-muted text-center small mb-4" style="font-family:'Poppins',sans-serif;">SJ Seguridad — acceso a la plataforma</p>
            @if ($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('login.store') }}" class="card login-card shadow-sm bg-white" autocomplete="off" novalidate>
                <div class="card-body p-4">
                @csrf
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" value="{{ old('usuario') }}" required autofocus maxlength="245">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required maxlength="500">
                </div>
                <button type="submit" class="btn btn-primary w-100" style="font-family:'Poppins',sans-serif;">Ingresar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
