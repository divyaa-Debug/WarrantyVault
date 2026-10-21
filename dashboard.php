<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "config/db.php";

$user_id = $_SESSION["user_id"];

$sql = "SELECT * FROM products WHERE user_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$total = $result->num_rows;

$active = 0;
$expired = 0;
$expiring = 0;

$today = new DateTime(date("Y-m-d"));

while ($row = $result->fetch_assoc()) {
    if (empty($row["expiry_date"])) {
        continue;
    }

    $expiry = new DateTime($row["expiry_date"]);
    $difference = $today->diff($expiry);
    $days = (int)$difference->format("%r%a");

    if ($days < 0) {
        $expired++;
    } elseif ($days <= 30) {
        $expiring++;
    } else {
        $active++;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WarrantyVault</title>
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
            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
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
            -webkit-font-smoothing: antialiased;
        }

        .navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--slate-200);
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1080px;
            margin: 0 auto;
            height: 60px;
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
            font-size: 1.1rem;
            letter-spacing: -0.01em;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .nav-link {
            text-decoration: none;
            color: var(--slate-600);
            font-size: 0.875rem;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: var(--radius-md);
            transition: all 0.15s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--slate-900);
            background-color: var(--slate-100);
        }

        .nav-logout {
            color: var(--slate-500);
        }

        .nav-logout:hover {
            color: var(--danger);
            background-color: #fef2f2;
        }

        .container {
            max-width: 1080px;
            margin: 36px auto 60px;
            padding: 0 20px;
        }

        .dash-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .dash-title {
            font-size: 1.625rem;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.02em;
        }

        .dash-subtitle {
            font-size: 0.875rem;
            color: var(--slate-500);
            margin-top: 4px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
        }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .card-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--slate-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .card-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .dot-total { background-color: var(--primary); }
        .dot-active { background-color: var(--success); }
        .dot-expiring { background-color: var(--warning); }
        .dot-expired { background-color: var(--danger); }

        .number {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1;
            color: var(--slate-900);
        }

        .card-subtext {
            font-size: 0.8125rem;
            color: var(--slate-400);
            margin-top: 8px;
        }

        .actions-bar {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
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

        @media (max-width: 840px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 520px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
            .actions-bar {
                flex-direction: column;
                width: 100%;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="dashboard.php" class="brand">
            <span>🔐</span>
            <span>WarrantyVault</span>
        </a>
        <div class="nav-links">
            <a href="dashboard.php" class="nav-link active">Dashboard</a>
            <a href="products.php" class="nav-link">My Products</a>
            <a href="add_product.php" class="nav-link">+ Add Product</a>
            <span style="font-size: 0.875rem; color: var(--slate-500); margin: 0 4px;">
                Welcome, <strong style="color: var(--slate-800);"><?php echo htmlspecialchars($_SESSION["user_name"] ?? 'User'); ?></strong>
            </span>
            <a href="logout.php" class="nav-link nav-logout">Logout</a>
        </div>
    </div>
</nav>

<div class="container">

    <div class="dash-header">
        <div>
            <h1 class="dash-title">Dashboard Overview</h1>
            <p class="dash-subtitle">Here is a quick snapshot of all your tracked product warranties.</p>
        </div>
        <div class="actions-bar">
            <a href="add_product.php" class="btn btn-primary">
                + Add Product
            </a>
            <a href="products.php" class="btn btn-secondary">
                View My Products →
            </a>
        </div>
    </div>

    <div class="cards-grid">

        <a href="products.php" class="card" style="text-decoration: none; color: inherit;">
            <div class="card-top">
                <span class="card-label">Total Products</span>
                <span class="card-dot dot-total"></span>
            </div>
            <div class="number">
                <?php echo $total; ?>
            </div>
            <div class="card-subtext">Total tracked items &rarr;</div>
        </a>

        <a href="products.php" class="card" style="text-decoration: none; color: inherit;">
            <div class="card-top">
                <span class="card-label">Active</span>
                <span class="card-dot dot-active"></span>
            </div>
            <div class="number" style="color: var(--success);">
                <?php echo $active; ?>
            </div>
            <div class="card-subtext">Currently under warranty &rarr;</div>
        </a>

        <a href="products.php" class="card" style="text-decoration: none; color: inherit;">
            <div class="card-top">
                <span class="card-label">Expiring Soon</span>
                <span class="card-dot dot-expiring"></span>
            </div>
            <div class="number" style="color: var(--warning);">
                <?php echo $expiring; ?>
            </div>
            <div class="card-subtext">Within the next 30 days &rarr;</div>
        </a>

        <a href="products.php" class="card" style="text-decoration: none; color: inherit;">
            <div class="card-top">
                <span class="card-label">Expired</span>
                <span class="card-dot dot-expired"></span>
            </div>
            <div class="number" style="color: var(--danger);">
                <?php echo $expired; ?>
            </div>
            <div class="card-subtext">Coverage has ended &rarr;</div>
        </a>

    </div>

</div>

</body>
</html>