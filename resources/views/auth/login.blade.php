
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARCLUS | Administrator Login</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            color-scheme: dark;
            --blue: #1685ff;
            --cyan: #38bdf8;
            --muted: #a8bad2;
            --panel: #0d1828;
            --border: #30425a;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #071426;
            color: #f5f8ff;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            gap: 5%;
            padding: 5% 7%;
            background:
                radial-gradient(ellipse at 15% 85%,
                    #06336b 0%, transparent 36%),
                linear-gradient(125deg, #09182d, #07111f 75%);
        }

        .brand {
            max-width: 650px;
        }

        .brand-heading {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .shield {
            width: 95px;
            height: 110px;
            flex-shrink: 0;
        }

        .brand-name {
            margin: 0;
            font-size: clamp(2.5rem, 4.4vw, 4.5rem);
            letter-spacing: -2px;
            line-height: 1.1;
        }

        .brand-name span {
            color: var(--blue);
        }

        .subtitle {
            color: #c1d0e4;
            font-size: 17px;
            margin-top: 12px;
        }

        .tagline {
            margin-top: 45px;
            font-size: 28px;
            font-weight: 700;
        }

        .description {
            color: var(--muted);
            line-height: 1.7;
            font-size: 17px;
        }

        .features {
            display: grid;
            gap: 22px;
            margin-top: 32px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 17px;
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 50%;
            background: #102d53;
            color: #49baff;
            font-size: 25px;
            border: 1px solid #1b4778;
        }

        .feature strong {
            font-size: 17px;
        }

        .feature p {
            color: var(--muted);
            margin: 6px 0 0;
            line-height: 1.5;
        }

        .network {
            position: relative;
            height: 130px;
            margin-top: 40px;
            overflow: hidden;
            border-bottom: 1px solid #167bff;
            background:
                radial-gradient(ellipse at bottom,
                    #0755a6 0%, transparent 65%);
        }

        .network::before {
            content: "";
            position: absolute;
            inset: 20px 0 0;
            background-image:
                linear-gradient(150deg, transparent 49.5%,
                    #147bd9 50%, transparent 50.5%),
                linear-gradient(30deg, transparent 49.5%,
                    #147bd9 50%, transparent 50.5%);
            background-size: 95px 65px;
            opacity: .55;
        }

        .server-row {
            position: absolute;
            bottom: 0;
            left: 8%;
            right: 8%;
            display: flex;
            justify-content: space-around;
            align-items: end;
            gap: 10px;
        }

        .server {
            width: 48px;
            height: 78px;
            padding: 8px 5px;
            background: linear-gradient(90deg, #0b2341, #174c83);
            border: 1px solid #319bff;
            box-shadow: 0 0 20px #087cff55;
        }

        .server.main {
            width: 65px;
            height: 110px;
        }

        .server i {
            display: block;
            height: 4px;
            margin-bottom: 7px;
            background: #3daaff;
            box-shadow: 0 0 6px #008cff;
        }

        .login-panel {
            width: 100%;
            max-width: 570px;
            justify-self: end;
            padding: 42px;
            border: 1px solid var(--border);
            border-radius: 17px;
            background: linear-gradient(145deg,
                #111e30, #0b1523);
            box-shadow: 0 25px 80px #0004;
        }

        .login-heading {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-heading .shield {
            width: 70px;
            height: 80px;
        }

        .login-heading h2 {
            font-size: 30px;
            margin: 18px 0 8px;
        }

        .login-heading p {
            color: var(--muted);
            margin: 0;
            line-height: 1.5;
        }

        .field {
            margin-bottom: 24px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .input-wrap {
            display: flex;
            align-items: center;
            border: 1px solid #3a4d67;
            border-radius: 10px;
            background: #0b1523;
            overflow: hidden;
        }

        .input-icon {
            padding: 0 15px;
            color: #a8bad2;
            font-size: 20px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            min-width: 0;
            padding: 17px 12px;
            color: white;
            font: inherit;
            background: transparent;
            border: 0;
            outline: none;
        }

        .input-wrap:focus-within {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px #1685ff20;
        }

        .toggle-password {
            padding: 12px;
            border: 0;
            background: transparent;
            color: var(--muted);
            cursor: pointer;
        }

        .options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 4px 0 30px;
            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            cursor: pointer;
        }

        .remember input {
            width: 17px;
            height: 17px;
            accent-color: var(--blue);
        }

        a {
            color: #38bdf8;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 17px;
            border: 0;
            border-radius: 9px;
            background: linear-gradient(100deg,
                #1685ff, #0875ed);
            color: white;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: filter .2s, transform .2s;
        }

        .login-button:hover {
            filter: brightness(1.13);
            transform: translateY(-1px);
        }

        .error {
            color: #ff9eaa;
            font-size: 13px;
            margin-top: 7px;
        }

        .notice {
            padding: 12px;
            border-radius: 8px;
            background: #7f1d1d44;
            color: #fecaca;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 25px;
            border-top: 1px solid #29384d;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.9;
        }

        .footer strong {
            color: #e2eaf7;
        }

        @media (max-width: 900px) {
            .page {
                grid-template-columns: 1fr;
                justify-items: center;
                padding: 30px 20px;
                gap: 35px;
            }

            .brand {
                width: 100%;
                max-width: 570px;
            }

            .brand-heading {
                gap: 15px;
            }

            .shield {
                width: 65px;
                height: 75px;
            }

            .tagline {
                margin-top: 25px;
            }

            .network {
                display: none;
            }

            .login-panel {
                justify-self: center;
            }
        }

        @media (max-width: 480px) {
            .login-panel {
                padding: 27px 20px;
            }

            .brand-name {
                font-size: 35px;
                letter-spacing: -1px;
            }

            .subtitle {
                font-size: 13px;
            }

            .tagline {
                font-size: 23px;
            }

            .description {
                font-size: 15px;
            }

            .options {
                font-size: 13px;
            }

            .feature-icon {
                width: 47px;
                height: 47px;
            }
        }
    </style>
</head>

<body>
<div class="page">

    <section class="brand">
        <div class="brand-heading">
            <svg class="shield" viewBox="0 0 100 115"
                 fill="none" aria-hidden="true">
                <defs>
                    <linearGradient id="shieldBlue"
                        x1="0" y1="0" x2="1" y2="1">
                        <stop stop-color="#42c4ff"/>
                        <stop offset="1" stop-color="#075be5"/>
                    </linearGradient>
                </defs>
                <path d="M50 5 L91 23 V53
                    C91 79 72 98 50 110
                    C28 98 9 79 9 53 V23 Z"
                    stroke="url(#shieldBlue)" stroke-width="8"/>
                <path d="M19 52 Q50 17 81 52
                    Q50 88 19 52Z"
                    stroke="#f2f8ff" stroke-width="5"/>
                <circle cx="50" cy="52" r="13"
                    fill="#1685ff" stroke="#fff" stroke-width="4"/>
            </svg>

            <div>
                <h1 class="brand-name">
                    ARCLUS
                </h1>
                <div class="subtitle">
                    LAN Monitoring and Administration System
                </div>
            </div>
        </div>

        <div class="tagline">Monitor. Manage. Secure.</div>
        <p class="description">
            Centralized control for all your networked computers
            in one place.
        </p>

        <div class="features">
            <div class="feature">
                <div class="feature-icon">▣</div>
                <div>
                    <strong>PC Monitoring</strong>
                    <p>Track system information, CPU, RAM and more.</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon">⌁</div>
                <div>
                    <strong>Live Status</strong>
                    <p>See online/offline status and heartbeat activity.</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon">⚙</div>
                <div>
                    <strong>PC Administration</strong>
                    <p>Manage and control authorized devices.</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon">♢</div>
                <div>
                    <strong>Security &amp; Logs</strong>
                    <p>Keep your network safe and monitored.</p>
                </div>
            </div>
        </div>

        <div class="network" aria-hidden="true">
            <div class="server-row">
                <div class="server"><i></i><i></i><i></i></div>
                <div class="server"><i></i><i></i><i></i></div>
                <div class="server main">
                    <i></i><i></i><i></i><i></i>
                    <i></i><i></i><i></i>
                </div>
                <div class="server"><i></i><i></i><i></i></div>
                <div class="server"><i></i><i></i><i></i></div>
            </div>
        </div>
    </section>

    <main class="login-panel">
        <div class="login-heading">
            <svg class="shield" viewBox="0 0 100 115"
                 fill="none" aria-hidden="true">
                <path d="M50 5 L91 23 V53
                    C91 79 72 98 50 110
                    C28 98 9 79 9 53 V23 Z"
                    stroke="#1685ff" stroke-width="8"/>
                <path d="M19 52 Q50 17 81 52
                    Q50 88 19 52Z"
                    stroke="#f2f8ff" stroke-width="5"/>
                <circle cx="50" cy="52" r="13"
                    fill="#1685ff" stroke="#fff" stroke-width="4"/>
            </svg>

            <h2>Administrator Login</h2>
            <p>Sign in to access the ARCLUS system</p>
        </div>

        @if ($errors->any())
            <div class="notice" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="notice">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label for="email">Email / Administrator Account</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">♙</span>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your admin email"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">♙</span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                    <button
                        class="toggle-password"
                        type="button"
                        onclick="togglePassword()"
                        aria-label="Show or hide password"
                    >◉</button>
                </div>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="options">
                <label class="remember">
                    <input type="checkbox" name="remember">
                    Remember me
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button class="login-button" type="submit">
                ⇥ &nbsp; Login
            </button>
        </form>

        <footer class="footer">
            <strong>ARCLUS</strong> &nbsp; v1.0<br>
            Secure &nbsp; • &nbsp; Reliable &nbsp; • &nbsp; Efficient
        </footer>
    </main>
</div>

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        password.type =
            password.type === 'password' ? 'text' : 'password';
    }
</script>
</body>
</html>
