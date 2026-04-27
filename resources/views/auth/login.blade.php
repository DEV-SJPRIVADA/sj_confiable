@php
    $sj = config('sj', []);
    $forgot = $sj['forgot_password_url'] ?? '';
    $wa = is_string($sj['whatsapp_url'] ?? null) ? $sj['whatsapp_url'] : '';
    $social = is_array($sj['social'] ?? null) ? $sj['social'] : [];
    $fb = is_string($social['facebook'] ?? null) ? $social['facebook'] : '';
    $li = is_string($social['linkedin'] ?? null) ? $social['linkedin'] : '';
    $ig = is_string($social['instagram'] ?? null) ? $social['instagram'] : '';
    $videoPath = 'videos/login.mp4';
    $hasVideo = file_exists(public_path($videoPath));
    $loginLogoRel = 'images/logo-sj-confiable.png';
    $loginLogoFile = public_path($loginLogoRel);
    $loginLogoUrl = asset($loginLogoRel).(is_file($loginLogoFile) ? '?v='.filemtime($loginLogoFile) : '');
@endphp
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Exo', system-ui, sans-serif; }
        .login-split { min-height: 100vh; }
        .login-video-pane {
            position: relative;
            background: linear-gradient(145deg, #0a1929 0%, #132f4c 45%, #0a1929 100%);
            min-height: 32vh;
            overflow: hidden;
        }
        @media (min-width: 992px) {
            .login-split { flex-wrap: nowrap !important; }
            .login-split .login-video-pane {
                flex: 0 0 70% !important;
                max-width: 70% !important;
            }
            .login-split .login-form-pane {
                flex: 0 0 30% !important;
                max-width: 30% !important;
            }
            .login-video-pane { min-height: 100vh; }
        }
        .login-video-pane video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .login-video-fallback {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            z-index: 1;
        }
        .login-form-pane {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 1.75rem 1.25rem 2.5rem;
            background: #fff;
        }
        @media (min-width: 992px) {
            .login-form-pane { padding: 2rem 1.5rem 2.5rem; }
        }
        .login-card { width: 100%; border-radius: 0.5rem; border: 1px solid #e0e0e0; }
        .btn-login { letter-spacing: 0.04em; font-size: 0.95rem; }
        .btn-social {
            width: 2.5rem; height: 2.5rem; padding: 0;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .float-wa {
            position: fixed;
            bottom: 1.25rem;
            right: 1.25rem;
            z-index: 1050;
            width: 3.25rem; height: 3.25rem;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: #25d366; color: #fff;
            font-size: 1.6rem;
            box-shadow: 0 0.2rem 0.75rem rgba(0,0,0,0.2);
        }
        .float-wa:hover { color: #fff; background: #1ebe57; }
        #password { border-right: 0; }
        .btn-toggle-pw { border-left: 0; }
        /* Mismo asset: public/images/logo-sj-confiable.png — v en query evita caché al reemplazar el PNG */
        .login-brand-logo {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            max-width: 100%;
            height: auto;
            object-fit: contain;
            /* Legado: logo protagonista; escala con la columna (~30% viewport) */
            max-height: clamp(5.5rem, 24svh, 10rem);
        }
        @media (min-width: 992px) {
            .login-brand-logo { max-height: min(10.5rem, 28svh); }
        }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <div class="row g-0 login-split">
        <div class="col-12 login-video-pane order-2 order-lg-1">
            @if ($hasVideo)
                <video autoplay muted loop playsinline @if (file_exists(public_path('videos/login-poster.jpg'))) poster="{{ asset('videos/login-poster.jpg') }}" @endif>
                    <source src="{{ asset($videoPath) }}" type="video/mp4">
                </video>
            @endif
            <div class="login-video-fallback @if($hasVideo) d-none @endif text-white-50 text-center">
                <img src="{{ $loginLogoUrl }}" alt="SJ Seguridad" class="img-fluid login-brand-logo opacity-90 mb-2">
                <p class="small text-white-50 mb-0" style="font-family:'Poppins',sans-serif;">SJ Seguridad</p>
            </div>
        </div>
        <div class="col-12 login-form-pane order-1 order-lg-2">
            <div class="w-100">
                <div class="text-center mb-2 px-1">
                    <img src="{{ $loginLogoUrl }}" alt="SJ Confiable" class="img-fluid login-brand-logo" decoding="async" fetchpriority="high">
                </div>
                <p class="text-muted text-center small mb-3 mb-lg-4" style="font-family:'Poppins',sans-serif;">SJ Seguridad — acceso a la plataforma</p>
                @if ($errors->any())
                    <div class="alert alert-danger small py-2">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="post" action="{{ route('login.store') }}" class="card login-card shadow-sm bg-white" autocomplete="off" novalidate>
                    <div class="card-body p-3 p-md-4">
                        @csrf
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" name="usuario" id="usuario" class="form-control" value="{{ old('usuario') }}" required autofocus maxlength="245" autocomplete="username">
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-baseline flex-wrap gap-1">
                                <label for="password" class="form-label mb-0">Contraseña</label>
                                <a href="{{ $forgot !== '' ? $forgot : '#' }}" class="small text-decoration-none @if ($forgot === '') text-muted @endif" style="font-family:'Poppins',sans-serif;" @if ($forgot === '') onclick="return false" @endif>¿Olvidaste tu contraseña?</a>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" id="password" class="form-control" required maxlength="500" autocomplete="current-password">
                            <button type="button" class="btn btn-outline-secondary btn-toggle-pw" id="togglePassword" title="Mostrar u ocultar" aria-label="Mostrar u ocultar contraseña">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-login text-uppercase fw-semibold py-2" style="font-family:'Poppins',sans-serif;">Ingresar</button>
                    </div>
                </form>
                <div class="text-center mt-4" style="font-family:'Poppins',sans-serif;">
                    <p class="small text-muted mb-2">Síguenos</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ $fb !== '' ? $fb : '#' }}" class="btn btn-social text-white @if ($fb === '') opacity-50 @endif" style="background:#1877f2;" @if ($fb !== '') target="_blank" rel="noopener noreferrer" @else onclick="return false" @endif title="Facebook" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="{{ $li !== '' ? $li : '#' }}" class="btn btn-social text-white @if ($li === '') opacity-50 @endif" style="background:#0a66c2;" @if ($li !== '') target="_blank" rel="noopener noreferrer" @else onclick="return false" @endif title="LinkedIn" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="{{ $ig !== '' ? $ig : '#' }}" class="btn btn-social text-white @if ($ig === '') opacity-50 @endif" style="background:linear-gradient(45deg,#f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);" @if ($ig !== '') target="_blank" rel="noopener noreferrer" @else onclick="return false" @endif title="Instagram" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<a href="{{ $wa !== '' ? $wa : '#' }}" class="float-wa @if ($wa === '') opacity-50 @endif" @if ($wa !== '') target="_blank" rel="noopener noreferrer" @else onclick="return false" @endif title="WhatsApp" aria-label="Contactar por WhatsApp"><i class="bi bi-whatsapp"></i></a>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
    (function () {
        const input = document.getElementById('password');
        const btn = document.getElementById('togglePassword');
        const icon = document.getElementById('togglePasswordIcon');
        if (!input || !btn || !icon) return;
        btn.addEventListener('click', function () {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    })();
</script>
</body>
</html>
