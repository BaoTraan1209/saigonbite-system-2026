<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In | SaigonBite</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-body">

{{-- Decorative organic shapes --}}
<span class="auth-blob auth-blob--top" aria-hidden="true"></span>
<span class="auth-blob auth-blob--bottom" aria-hidden="true"></span>

<main class="auth-page">

    {{-- Brand logo --}}
    <a href="{{ url('/') }}" class="auth-logo" aria-label="SaigonBite">
        <span class="auth-logo__saigon">Saigon</span><span class="auth-logo__bite">Bite</span>
    </a>

    {{-- Left: welcome --}}
    <section class="auth-welcome">
        <div class="auth-welcome__text">
            <h1 class="auth-welcome__title">
                <span class="d-block text-green">Welcome to</span>
                <span class="d-block"><span class="text-green">Saigon</span><span class="text-orange">Bite</span></span>
            </h1>
            <p class="auth-welcome__desc">
                Sign in as an admin to manage the menu, or as a customer to place an order.
            </p>
        </div>

        <div class="auth-sushi" aria-hidden="true">
            <span class="auth-sushi__circle"></span>

            {{-- Sushi illustration (inline SVG, no external image needed) --}}
            <svg class="auth-sushi__img" viewBox="0 0 520 340" xmlns="http://www.w3.org/2000/svg" role="img">
                <defs>
                    <radialGradient id="plateGrad" cx="50%" cy="40%" r="65%">
                        <stop offset="0%" stop-color="#FFFFFF"/>
                        <stop offset="100%" stop-color="#EDE7DF"/>
                    </radialGradient>
                    <linearGradient id="salmonGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#FF9A6B"/>
                        <stop offset="100%" stop-color="#F26A3D"/>
                    </linearGradient>
                    <linearGradient id="woodGrad" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#C99A62"/>
                        <stop offset="100%" stop-color="#A97B46"/>
                    </linearGradient>
                </defs>

                {{-- soft ground shadow --}}
                <ellipse cx="250" cy="300" rx="215" ry="26" fill="#173B28" opacity=".08"/>

                {{-- chopsticks --}}
                <rect x="60" y="262" width="400" height="7" rx="3.5" fill="url(#woodGrad)" transform="rotate(-6 260 265)"/>
                <rect x="70" y="278" width="400" height="7" rx="3.5" fill="url(#woodGrad)" transform="rotate(-6 270 281)"/>

                {{-- plate --}}
                <ellipse cx="230" cy="240" rx="200" ry="62" fill="url(#plateGrad)" stroke="#E4DDD3"/>
                <ellipse cx="230" cy="236" rx="160" ry="44" fill="#FFFFFF" stroke="#EFE9E1"/>

                {{-- sushi rolls --}}
                <g>
                    <g transform="translate(110 188)">
                        <rect x="0" y="0" width="64" height="48" rx="14" fill="#FFFFFF" stroke="#E9E3DA"/>
                        <rect x="-4" y="-12" width="72" height="26" rx="13" fill="url(#salmonGrad)"/>
                        <path d="M6 -2 C 22 -10, 44 -10, 60 -2" stroke="#FFC2A3" stroke-width="3" fill="none" stroke-linecap="round"/>
                        <rect x="0" y="34" width="64" height="12" rx="6" fill="#173B28" opacity=".9"/>
                    </g>
                    <g transform="translate(186 196)">
                        <rect x="0" y="0" width="64" height="48" rx="14" fill="#FFFFFF" stroke="#E9E3DA"/>
                        <rect x="-4" y="-12" width="72" height="26" rx="13" fill="url(#salmonGrad)"/>
                        <path d="M6 -2 C 22 -10, 44 -10, 60 -2" stroke="#FFC2A3" stroke-width="3" fill="none" stroke-linecap="round"/>
                        <rect x="0" y="34" width="64" height="12" rx="6" fill="#173B28" opacity=".9"/>
                    </g>
                    <g transform="translate(262 188)">
                        <rect x="0" y="0" width="64" height="48" rx="14" fill="#FFFFFF" stroke="#E9E3DA"/>
                        <rect x="-4" y="-12" width="72" height="26" rx="13" fill="url(#salmonGrad)"/>
                        <path d="M6 -2 C 22 -10, 44 -10, 60 -2" stroke="#FFC2A3" stroke-width="3" fill="none" stroke-linecap="round"/>
                        <rect x="0" y="34" width="64" height="12" rx="6" fill="#173B28" opacity=".9"/>
                    </g>
                </g>

                {{-- green garnish --}}
                <g fill="#60775A">
                    <ellipse cx="160" cy="176" rx="9" ry="5" transform="rotate(-25 160 176)"/>
                    <ellipse cx="240" cy="184" rx="9" ry="5" transform="rotate(20 240 184)"/>
                    <ellipse cx="320" cy="176" rx="9" ry="5" transform="rotate(-15 320 176)"/>
                </g>
                <circle cx="344" cy="226" r="7" fill="#7FA06F"/>
                <circle cx="354" cy="232" r="5" fill="#60775A"/>

                {{-- soy sauce bowl --}}
                <ellipse cx="415" cy="226" rx="52" ry="16" fill="#FFFFFF" stroke="#E4DDD3"/>
                <path d="M363 226 C 366 262, 464 262, 467 226 Z" fill="#FFFFFF" stroke="#E4DDD3"/>
                <ellipse cx="415" cy="226" rx="42" ry="10" fill="#3A1F14"/>
                <ellipse cx="404" cy="223" rx="12" ry="3" fill="#FFFFFF" opacity=".25"/>
            </svg>
        </div>
    </section>

    {{-- Right: login card --}}
    <section class="auth-form-wrap">
        <div class="auth-card">
            <header class="auth-card__header">
                <h2 class="auth-card__title">Sign In</h2>
                <p class="auth-card__subtitle">Use your admin or customer account to continue.</p>
            </header>

            {{-- Authentication / status alerts --}}
            @if (session('status'))
                <div class="alert auth-alert auth-alert--success" role="status">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any() && ! $errors->has('login') && ! $errors->has('password'))
                <div class="alert auth-alert auth-alert--danger" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <div id="authAlert" class="alert auth-alert auth-alert--danger d-none" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span id="authAlertText"></span>
            </div>

            <form id="loginForm" method="POST" action="{{ route('login.store') }}" novalidate>
                @csrf

                {{-- Account --}}
                <div class="auth-field">
                    <label for="email" class="auth-label">Username or email <span class="req">*</span></label>
                    <div class="auth-input @error('login') is-invalid @enderror">
                        <i class="bi bi-envelope auth-input__icon"></i>
                        <input type="text"
                               id="email"
                               name="login"
                               value="{{ old('login') }}"
                               placeholder="Enter admin, customer, or email"
                               autocomplete="username"
                               autofocus
                               required
                               aria-describedby="emailError">
                    </div>
                    <div id="emailError" class="auth-error" role="alert">@error('login'){{ $message }}@enderror</div>
                </div>

                {{-- Password --}}
                <div class="auth-field">
                    <label for="password" class="auth-label">Password <span class="req">*</span></label>
                    <div class="auth-input @error('password') is-invalid @enderror">
                        <i class="bi bi-lock auth-input__icon"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               required
                               aria-describedby="passwordError">
                        <button type="button" id="togglePassword" class="auth-input__toggle"
                                aria-label="Show password" aria-pressed="false">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div id="passwordError" class="auth-error" role="alert">@error('password'){{ $message }}@enderror</div>
                </div>

                {{-- Account options --}}
                <div class="auth-options">
                    <div class="form-check auth-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <span class="auth-forgot">Admin · Customer access</span>
                </div>

                {{-- Submit --}}
                <button type="submit" id="submitBtn" class="auth-submit">
                        <span class="auth-submit__idle">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Sign In</span>
                        </span>
                    <span class="auth-submit__loading" aria-live="polite">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>Signing in...</span>
                        </span>
                </button>
            </form>
            <div class="auth-demo-accounts">
                <span>Admin</span><code>admin / 123456</code>
                <span>Customer</span><code>customer / 123456</code>
            </div>
        </div>
    </section>
</main>

<script>
    (function () {
        var form      = document.getElementById('loginForm');
        var email     = document.getElementById('email');
        var password  = document.getElementById('password');
        var toggle    = document.getElementById('togglePassword');
        var submitBtn = document.getElementById('submitBtn');
        var emailErr  = document.getElementById('emailError');
        var passErr   = document.getElementById('passwordError');
        var submitting = false;

        function setError(input, box, message) {
            var wrap = input.closest('.auth-input');
            box.textContent = message || '';
            wrap.classList.toggle('is-invalid', !!message);
            input.setAttribute('aria-invalid', message ? 'true' : 'false');
        }

        function validateEmail() {
            var v = email.value.trim();
            if (!v) { setError(email, emailErr, 'Please enter your username or email.'); return false; }
            setError(email, emailErr, '');
            return true;
        }

        function validatePassword() {
            if (!password.value) { setError(password, passErr, 'Please enter your password.'); return false; }
            setError(password, passErr, '');
            return true;
        }

        // Show / hide password
        toggle.addEventListener('click', function () {
            var show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
            toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            toggle.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            password.focus();
        });

        // Clear errors while typing
        email.addEventListener('input', function () { if (emailErr.textContent) validateEmail(); });
        password.addEventListener('input', function () { if (passErr.textContent) validatePassword(); });
        email.addEventListener('blur', function () { if (email.value) validateEmail(); });

        // Submit with validation, loading state and double-submit guard
        form.addEventListener('submit', function (e) {
            if (submitting) { e.preventDefault(); return; }

            var okEmail = validateEmail();
            var okPass  = validatePassword();

            if (!okEmail || !okPass) {
                e.preventDefault();
                (okEmail ? password : email).focus();
                return;
            }

            submitting = true;
            submitBtn.classList.add('is-loading');
            submitBtn.setAttribute('aria-busy', 'true');
            submitBtn.disabled = true;
        });

        // Restore button if user returns via browser back (bfcache)
        window.addEventListener('pageshow', function (ev) {
            if (ev.persisted) {
                submitting = false;
                submitBtn.disabled = false;
                submitBtn.classList.remove('is-loading');
                submitBtn.removeAttribute('aria-busy');
            }
        });
    })();
</script>
</body>
</html>
