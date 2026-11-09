```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ARCLUS | LAN Management System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fc;
            color: #172554;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px 8%;
            background: #ffffff;
            border-bottom: 1px solid #e5eaf3;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 23px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #173b75;
        }

        .brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 43px;
            height: 43px;
            border-radius: 12px;
            background: #173b75;
            color: white;
            font-size: 23px;
        }

        .nav-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .hero {
            min-height: 78vh;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 60px;
            padding: 75px 10%;
            overflow: hidden;
        }

        .hero-content {
            max-width: 650px;
        }

        .eyebrow {
            display: inline-block;
            padding: 9px 16px;
            border-radius: 30px;
            background: #e5edfb;
            color: #24549a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin-bottom: 25px;
        }

        h1 {
            font-size: clamp(42px, 5vw, 65px);
            line-height: 1.12;
            margin: 0 0 22px;
            color: #142f59;
        }

        h1 span {
            color: #3478d4;
        }

        .description {
            max-width: 570px;
            color: #64748b;
            font-size: 17px;
            line-height: 1.8;
            margin-bottom: 35px;
        }

        .login-button {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 27px;
            background: #24549a;
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            border-radius: 9px;
            box-shadow: 0 8px 20px rgba(36, 84, 154, 0.2);
            transition: 0.25s;
        }

        .login-button:hover {
            background: #173b75;
            transform: translateY(-3px);
        }

        .hero-visual {
            width: 390px;
            height: 390px;
            flex-shrink: 0;
            border-radius: 35px;
            background: linear-gradient(145deg, #173b75, #3478d4);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 25px 60px rgba(23, 59, 117, 0.2);
        }

        .visual-inner {
            width: 245px;
            height: 245px;
            border: 2px solid rgba(255,255,255,0.35);
            border-radius: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .visual-symbol {
            font-size: 85px;
            margin-bottom: 15px;
        }

        .visual-title {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 3px;
        }

        .visual-subtitle {
            margin-top: 8px;
            font-size: 11px;
            letter-spacing: 2px;
            opacity: 0.8;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            padding: 35px 10% 65px;
        }

        .feature-card {
            padding: 27px;
            background: white;
            border: 1px solid #e5eaf3;
            border-radius: 14px;
        }

        .feature-icon {
            font-size: 28px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            color: #173b75;
            font-size: 17px;
            margin: 0 0 10px;
        }

        .feature-card p {
            color: #64748b;
            font-size: 13px;
            line-height: 1.7;
            margin: 0;
        }

        footer {
            padding: 25px 10%;
            background: white;
            border-top: 1px solid #e5eaf3;
            color: #94a3b8;
            text-align: center;
            font-size: 12px;
        }

        @media (max-width: 850px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding: 65px 7%;
            }

            .description {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-visual {
                width: 300px;
                height: 300px;
            }

            .visual-inner {
                width: 205px;
                height: 205px;
            }

            .features {
                grid-template-columns: 1fr;
                padding-left: 7%;
                padding-right: 7%;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 17px 6%;
            }

            .nav-label {
                display: none;
            }

            .hero-visual {
                width: 260px;
                height: 260px;
            }
        }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="brand">
            <div class="brand-icon">A</div>
            <span>ARCLUS</span>
        </div>

        <div class="nav-label">
            LOCAL AREA NETWORK MANAGEMENT SYSTEM
        </div>
    </header>

    <main>
        <section class="hero">

            <div class="hero-content">
                <div class="eyebrow">
                    SMART CAMPUS NETWORK MANAGEMENT
                </div>

                <h1>
                    Welcome to <span>ARCLUS</span>
                </h1>

                <p class="description">
                    A centralized campus network management system designed
                    to manage enrolled students, monitor connected computers,
                    and provide secure access through QR code and facial
                    verification.
                </p>

                <a href="{{ route('login') }}" class="login-button">
                    Administrator Login
                    <span>→</span>
                </a>
            </div>

            <div class="hero-visual">
                <div class="visual-inner">
                    <div class="visual-symbol">⌘</div>
                    <div class="visual-title">ARCLUS</div>
                    <div class="visual-subtitle">
                        CONNECT • MONITOR • MANAGE
                    </div>
                </div>
            </div>

        </section>

        <section class="features">

            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Student Management</h3>
                <p>
                    Manage student registrations, profiles, and enrollment
                    approvals in one centralized system.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">💻</div>
                <h3>Computer Monitoring</h3>
                <p>
                    Monitor registered computers and view their network
                    connection and device status.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🔐</div>
                <h3>Secure Verification</h3>
                <p>
                    Protect student access using QR code identification
                    and facial verification.
                </p>
            </div>

        </section>
    </main>

    <footer>
        © {{ date('Y') }} ARCLUS. All rights reserved.
        <br>
        Campus LAN Management System
    </footer>

</body>
</html>
```