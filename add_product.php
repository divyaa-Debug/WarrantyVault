<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];

    $product_name = $_POST["product_name"];
    $category = $_POST["category"];
    $brand = $_POST["brand"];
    $model = $_POST["model"];
    $serial_number = $_POST["serial_number"];
    $purchase_date = $_POST["purchase_date"];
    $warranty_duration = $_POST["warranty_duration"];
    $price = $_POST["price"];
    $store_name = $_POST["store_name"];
    $notes = $_POST["notes"];

    $date = new DateTime($purchase_date);

    $date->modify("+" . $warranty_duration . " year");

    $expiry_date = $date->format("Y-m-d");

    $sql = "INSERT INTO products
    (
        user_id,
        product_name,
        category,
        brand,
        model,
        serial_number,
        purchase_date,
        warranty_duration,
        expiry_date,
        price,
        store_name,
        notes
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "issssssisdss",
        $user_id,
        $product_name,
        $category,
        $brand,
        $model,
        $serial_number,
        $purchase_date,
        $warranty_duration,
        $expiry_date,
        $price,
        $store_name,
        $notes
    );

    if ($stmt->execute()) {

        header("Location: products.php");
        exit();

    } else {

        $message = "Error adding product.";

    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - WarrantyVault</title>
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

        *,
        *::before,
        *::after {
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

        /* Top Navbar */
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

        .nav-link:hover {
            color: var(--slate-900);
            background-color: var(--slate-100);
        }

        .nav-link.nav-logout {
            color: var(--slate-500);
        }

        .nav-link.nav-logout:hover {
            color: var(--danger);
            background-color: var(--danger-light);
        }

        /* Page Layout */
        .page-wrapper {
            max-width: 680px;
            margin: 36px auto 60px;
            padding: 0 20px;
        }

        .breadcrumb {
            margin-bottom: 18px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--slate-600);
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .back-link:hover {
            color: var(--primary);
        }

        /* Card */
        .form-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.04);
            padding: 32px 36px;
        }

        .card-header {
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--slate-100);
        }

        .card-title {
            font-size: 1.375rem;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.02em;
        }

        .card-subtitle {
            font-size: 0.875rem;
            color: var(--slate-500);
            margin-top: 4px;
        }

        /* Alert message */
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            margin-bottom: 20px;
        }

        .alert-error {
            background-color: var(--danger-light);
            border: 1px solid var(--danger-border);
            color: var(--danger);
        }

        /* Form Structure */
        .form-group {
            margin-bottom: 18px;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: 6px;
        }

        .required {
            color: var(--danger);
            margin-left: 2px;
        }

        /* Controls */
        .form-input,
        .form-select,
        .form-textarea {
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

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: var(--slate-400);
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
        }

        .form-select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
            cursor: pointer;
        }

        .form-textarea {
            height: auto;
            min-height: 96px;
            padding: 10px 13px;
            resize: vertical;
            line-height: 1.5;
        }

        /* Actions */
        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 26px;
            padding-top: 20px;
            border-top: 1px solid var(--slate-100);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            line-height: 1.25;
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
            color: var(--slate-600);
            border-color: var(--slate-300);
        }

        .btn-secondary:hover {
            background-color: var(--slate-50);
            color: var(--slate-800);
            border-color: var(--slate-400);
        }

        /* Responsive */
        @media (max-width: 640px) {
            .page-wrapper {
                margin: 20px auto 40px;
                padding: 0 14px;
            }

            .form-card {
                padding: 24px 18px;
            }

            .form-grid-2 {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
                text-align: center;
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
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="products.php" class="nav-link">My Products</a>
                <a href="add_product.php" class="nav-link active">+ Add Product</a>
                <span style="font-size: 0.875rem; color: var(--slate-500); margin: 0 4px;">
                    Welcome, <strong
                        style="color: var(--slate-800);"><?php echo htmlspecialchars($_SESSION["user_name"] ?? 'User'); ?></strong>
                </span>
                <a href="logout.php" class="nav-link nav-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">

        <div class="breadcrumb">
            <a href="dashboard.php" class="back-link">
                ← Back to Dashboard
            </a>
        </div>

        <div class="form-card">

            <div class="card-header">
                <h1 class="card-title">Add Product</h1>
                <p class="card-subtitle">Enter product details to record coverage and calculate warranty expiration.</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-error">
                    <span>⚠</span>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="form-group">
                    <label class="form-label" for="product_name">
                        Product Name <span class="required">*</span>
                    </label>
                    <input type="text" id="product_name" name="product_name" class="form-input"
                        placeholder="e.g ; iPhone 15 Pro" required>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="category">Category</label>
                        <select id="category" name="category" class="form-select">
                            <option value="Electronics">Electronics</option>
                            <option value="Appliance">Appliance</option>
                            <option value="Mobile">Mobile</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="brand">Brand</label>
                        <input type="text" id="brand" name="brand" class="form-input"
                            placeholder="e.g., Apple, Sony, LG">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="model">Model</label>
                        <input type="text" id="model" name="model" class="form-input" placeholder="e.g., A2848">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="serial_number">Serial Number</label>
                        <input type="text" id="serial_number" name="serial_number" class="form-input"
                            placeholder="e.g., SN-88392019">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="purchase_date">
                            Purchase Date <span class="required">*</span>
                        </label>
                        <input type="date" id="purchase_date" name="purchase_date" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="warranty_duration">
                            Warranty Duration (Years) <span class="required">*</span>
                        </label>
                        <input type="number" id="warranty_duration" name="warranty_duration" class="form-input" min="1"
                            placeholder="e.g., 1 or 2" required>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="price">Price</label>
                        <input type="number" id="price" name="price" class="form-input" step="0.01" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="store_name">Store Name</label>
                        <input type="text" id="store_name" name="store_name" class="form-input"
                            placeholder="e.g., Amazon, Apple Store">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="form-textarea"
                        placeholder="Add any coverage terms, invoice details, or additional notes..."></textarea>
                </div>

                <div class="form-actions">
                    <a href="dashboard.php" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Save Product
                    </button>
                </div>

            </form>

        </div>

    </div>

</body>

</html>