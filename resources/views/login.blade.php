<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HRIS Core</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div class="container-custom">
        <div class="login-wrapper">
            <div class="company-panel">
                <div class="logo-section mb-4 d-flex align-items-center gap-3">
                    <div class="logo-icon"><i class="fas fa-mountain"></i></div>
                    <h5 class="m-0 fw-bold">HRIS</h5>
                </div>

                <div class="company-content">
                    <h1>Tingkatkan<br>Produktif Anda.</h1>
                    <div class="feature-list mt-4">
                        <div class="feature-badge">
                            <i class="fas fa-check-circle"></i> Logika Ruang Kerja Tingkat Lanjut
                        </div>
                        <div class="feature-badge">
                            <i class="fas fa-check-circle"></i> Sinkronisasi Alur Kerja Alami
                        </div>
                        <div class="feature-badge">
                            <i class="fas fa-check-circle"></i> Infrastruktur yang Aman
                        </div>
                    </div>
                </div>

                <div class="mt-auto d-none d-md-block">
                    <p style="font-size: 11px; opacity: 0.6; letter-spacing: 1px;">
                        © 2026 PT RIZQI COMPUTER.
                    </p>
                </div>
            </div>

            <div class="login-panel">
                <div class="login-header mb-4">
                    <h2>Sign In</h2>
                    <p class="text-muted small">Enter your credentials to manage your workspace.</p>
                </div>

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="position-relative">
                            <i class="fas fa-user-circle position-absolute" style="left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                            <input type="text" name="username" class="form-input" placeholder="Enter your identity" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="position-relative">
                            <i class="fas fa-lock position-absolute" style="left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                            <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 login-button">
                        Login
                    </button>
                </form>

                <div class="text-center mt-4 mt-lg-5">
                    <p class="text-muted small mb-0">
                        Technical issues? <a href="#" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Support Desk</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
