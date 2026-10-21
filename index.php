<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WarrantyVault - Track & Manage Product Warranties</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--slate-50);
            color: var(--slate-800);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .landing-nav {
            background-color: #ffffff;
            border-bottom: 1px solid var(--slate-200);
            padding: 0 24px;
        }

        .nav-inner {
            max-width: 1080px;
            margin: 0 auto;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--slate-900);
            font-weight: 700;
            font-size: 1.15rem;
        }

        .hero {
            max-width: 1040px;
            margin: 48px auto 72px;
            padding: 0 24px;
            text-align: center;
            flex: 1;
        }



        .unique-title {
            font-size: 3rem;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.15;
            letter-spacing: -0.035em;
            margin-bottom: 16px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 60%, #0d9488 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .unique-subtitle {
            font-size: 1.125rem;
            color: var(--slate-500);
            max-width: 620px;
            margin: 0 auto 32px;
            line-height: 1.6;
        }

        .hero-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9375rem;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: #ffffff;
            color: var(--slate-700);
            border: 1px solid var(--slate-300);
        }

        .btn-secondary:hover {
            background-color: var(--slate-50);
            color: var(--slate-900);
            border-color: var(--slate-400);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 64px;
            text-align: left;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .feature-icon {
            font-size: 1.5rem;
            margin-bottom: 12px;
        }

        .feature-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 6px;
        }

        .feature-desc {
            font-size: 0.875rem;
            color: var(--slate-500);
            line-height: 1.5;
        }

        .footer {
            text-align: center;
            padding: 24px;
            color: var(--slate-400);
            font-size: 0.8125rem;
            border-top: 1px solid var(--slate-200);
            background-color: #ffffff;
        }

        @media (max-width: 680px) {
            .hero {
                margin: 40px auto;
            }
            .hero-title {
                font-size: 2rem;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
            .hero-cta {
                flex-direction: column;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<nav class="landing-nav">
    <div class="nav-inner">
        <a href="index.php" class="brand">
            <span>🔐</span>
            <span>WarrantyVault</span>
        </a>
        <div>
            <a href="login.php" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.875rem;">Login</a>
        </div>
    </div>
</nav>

<main class="hero">


    <h1 class="unique-title">
        All Your Warranties. <br><span class="gradient-text">One Digital Vault.</span>
    </h1>

    <p class="unique-subtitle">
        Never let another receipt fade or a warranty lapse. Store purchase records, track expiry dates in real time, and protect your investments seamlessly.
    </p>

    <div class="hero-cta">
        <a href="register.php" class="btn btn-primary btn-glow">Create Free Account →</a>
        <a href="login.php" class="btn btn-secondary">Sign In to Vault</a>
    </div>


    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">⏱️</div>
            <h3 class="feature-title">Expiry Tracking</h3>
            <p class="feature-desc">Automatic expiry calculation with instant remaining-days countdowns.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📁</div>
            <h3 class="feature-title">Organized Records</h3>
            <p class="feature-desc">Store serial numbers, model details, stores, and custom notes in one place.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3 class="feature-title">Smart Dashboard</h3>
            <p class="feature-desc">View total active warranties, items expiring soon, and past coverage at a glance.</p>
        </div>
    </div>
</main>

<footer class="footer">
    &copy; <?php echo date("Y"); ?> WarrantyVault. All rights reserved.
</footer>

</body>
</html>