{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
        --primary-color: #213448;
        --text-muted: #717171;
        --border-color: #e5e7eb;
        --radius: 16px;
    }
    
        body {
            background-color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background-color: #ffffff;
            border: 1px solid black;
            border-radius: 12px;
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
            color: #212529;
            letter-spacing: 2px;
        }

        .form-label {
            font-weight: 600;
            color: #000000ff;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1px solid #213448;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        /* .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        } */

        .btn-login {
            background-color: #212529;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            margin-top: 1.5rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-login:hover {
            background-color: #000000;
        }

        .remember-me {
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .remember-me label {
            margin: 0;
            font-size: 0.9rem;
            color: #6c757d;
            cursor: pointer;
        }

        .alert {
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        /* Dark mode support */
        body.dark-mode {
            background-color: #1a1a1a;
        }

        body.dark-mode .login-container {
            background-color: #2a2a2a;
            border-color: #4a90e2;
            color: #e0e0e0;
        }

        body.dark-mode .login-title,
        body.dark-mode .form-label {
            color: #e0e0e0;
        }

        body.dark-mode .form-control {
            background-color: #3a3a3a;
            border-color: #4a4a4a;
            color: #e0e0e0;
        }

        body.dark-mode .form-control:focus {
            background-color: #3a3a3a;
            border-color: #4a90e2;
        }

        body.dark-mode .btn-login {
            background-color: #4a90e2;
        }

        body.dark-mode .btn-login:hover {
            background-color: #3a7bc8;
        }
    </style>
</head>
<body>
    <div class="login-container">
        {{ $slot }}
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>