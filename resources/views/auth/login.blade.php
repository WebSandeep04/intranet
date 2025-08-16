<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<!-- header -->
   <header class="w-100 px-4 py-2 mb-3 d-flex justify-content-between align-items-center border-bottom shadow-sm">
        <div class="d-flex align-items-center">
            <img src="/img/logo.jpg" alt="Logo" style="height: 40px;" class="me-2">
            <!-- <span class="fs-5 fw-bold">YourCompany</span> -->
        </div>
        <div>
            <span class="me-3" style="font-weight:600">Don't have an account?</span><a href="{{ url('/register') }}" class="btn singupbtn">Sign Up</a>
        </div>
    </header>

<div class="loginBox">
    <div class="login-card">
        <h3 class="text-center mb-4">Welcome Back!</h3>

        <!-- Sign in with social icons only -->
<div class="social-icons mb-3">
    <a href="{{ url('/auth/google') }}" class="social-btn google btn btn-light border d-flex align-items-center justify-content-center" title="Sign in with Google">
        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo" style="height: 20px; margin-right: 8px;">
        Sign in with Google
    </a>
</div>


        <!-- <div class="text-center mb-3 text-muted">OR</div> -->

        <!-- Traditional login -->
        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                    autofocus
                />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                />
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" />
                    <label class="form-check-label" for="remember">
                        Remember Me
                    </label>
                </div>
                <a href="{{ url('/forgot-password') }}" class="text-decoration-none small">Forgot Password?</a>
            </div>

            <button type="submit" class="btn w-100 loginbtn d-block mx-auto">Login</button>
        </form>

        @if ($errors->any())
            <div class="text-danger text-center mt-3 error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="text-center mt-4">
            <span>Don't have an account? <a href="{{ url('/register') }}">Sign up</a></span>
        </div>
    </div>
</div>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
