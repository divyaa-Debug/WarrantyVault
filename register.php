<?php

include "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {

        header("Location: login.php");
        exit();

    } else {

        $message = "Email already exists.";

    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - WarrantyVault</title>
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
            --danger: #dc2626;
            --danger-light: #fef2f2;
            --danger-border: #fecaca;
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--slate-50);
            color: var(--slate-800);
            line-height: 1.5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.04);
            padding: 36px 32px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-logo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--slate-900);
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 12px;
        }

        .auth-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--slate-900);
        }

        .auth-subtitle {
            font-size: 0.875rem;
            color: var(--slate-500);
            margin-top: 4px;
        }

        .alert {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            margin-bottom: 20px;
            background-color: var(--danger-light);
            border: 1px solid var(--danger-border);
            color: var(--danger);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            height: 42px;
            padding: 0 13px;
            font-size: 0.875rem;
            font-family: inherit;
            color: var(--slate-900);
            background-color: #ffffff;
            border: 1px solid var(--slate-300);
            border-radius: var(--radius-md);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
        }

        .btn-submit {
            width: 100%;
            height: 42px;
            margin-top: 8px;
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
        }

        .auth-footer {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--slate-100);
            text-align: center;
            font-size: 0.875rem;
            color: var(--slate-500);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-header">
        <a href="index.php" class="auth-logo">
            <span>🔐</span>
            <span>WarrantyVault</span>
        </a>
        <h1 class="auth-title">Create an Account</h1>
        <p class="auth-subtitle">Start organizing your warranties with ease</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert">
            <span>⚠</span>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-input"
                placeholder="e.g., Jane Doe"
                required
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-input"
                placeholder="name@example.com"
                required
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                placeholder="Create a secure password"
                required
            >
        </div>

        <button type="submit" class="btn-submit">
            Create Account
        </button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="login.php">Sign in</a>
    </div>
</div>

</body>
</html>