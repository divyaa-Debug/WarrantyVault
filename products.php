<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "config/db.php";

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT * FROM products WHERE user_id = ? ORDER BY expiry_date ASC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - WarrantyVault</title>
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
            --success-light: #f0fdf4;
            --success-border: #bbf7d0;
            --warning: #d97706;
            --warning-light: #fffbeb;
            --warning-border: #fde68a;
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

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-size: 1.625rem;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--slate-500);
            margin-top: 4px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: var(--radius-md);
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.15s ease;
            cursor: pointer;
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
            border-color: var(--slate-300);
        }

        .btn-secondary:hover {
            background-color: var(--slate-50);
            color: var(--slate-900);
            border-color: var(--slate-400);
        }

        .btn-danger-outline {
            background-color: #ffffff;
            color: var(--danger);
            border-color: var(--danger-border);
        }

        .btn-danger-outline:hover {
            background-color: var(--danger-light);
            color: #991b1b;
        }

        /* Products Grid */
        .products-grid {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            padding: 24px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .product-card:hover {
            border-color: var(--slate-300);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.06);
        }

        .product-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--slate-100);
            gap: 12px;
            flex-wrap: wrap;
        }

        .product-heading {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .product-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.01em;
        }

        .category-pill {
            display: inline-block;
            background-color: var(--slate-100);
            color: var(--slate-600);
            border: 1px solid var(--slate-200);
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .status-active {
            background-color: var(--success-light);
            color: var(--success);
            border: 1px solid var(--success-border);
        }

        .status-expiring {
            background-color: var(--warning-light);
            color: var(--warning);
            border: 1px solid var(--warning-border);
        }

        .status-expired {
            background-color: var(--danger-light);
            color: var(--danger);
            border: 1px solid var(--danger-border);
        }

        /* Details Grid */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px 20px;
            margin-bottom: 18px;
        }

        .detail-item {
            font-size: 0.875rem;
        }

        .detail-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
        }

        .detail-value {
            color: var(--slate-800);
            font-weight: 500;
        }

        .notes-section {
            background-color: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            font-size: 0.875rem;
            color: var(--slate-600);
            margin-bottom: 18px;
        }

        .card-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 14px;
            border-top: 1px solid var(--slate-100);
        }

        /* Empty State */
        .empty-state {
            background: #ffffff;
            border: 1px dashed var(--slate-300);
            border-radius: var(--radius-lg);
            padding: 60px 24px;
            text-align: center;
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 16px;
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 6px;
        }

        .empty-desc {
            font-size: 0.875rem;
            color: var(--slate-500);
            margin-bottom: 24px;
        }

        @media (max-width: 860px) {
            .details-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 520px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
            .card-actions {
                flex-direction: column;
            }
            .card-actions .btn {
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
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="products.php" class="nav-link active">My Products</a>
            <a href="logout.php" class="nav-link nav-logout">Logout</a>
        </div>
    </div>
</nav>

<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">My Products</h1>
            <p class="page-subtitle">Track your registered products and active warranty coverage.</p>
        </div>
        <div>
            <a href="add_product.php" class="btn btn-primary">
                + Add Product
            </a>
        </div>
    </div>

    <?php if ($result->num_rows > 0): ?>

        <div class="products-grid">

            <?php while ($row = $result->fetch_assoc()):

                $today = new DateTime(date("Y-m-d"));
                $expiry = new DateTime($row["expiry_date"]);
                $difference = $today->diff($expiry);
                $days = (int)$difference->format("%r%a");

                if ($days < 0) {
                    $status = "Expired";
                    $status_class = "status-expired";
                } elseif ($days <= 30) {
                    $status = "Expiring Soon";
                    $status_class = "status-expiring";
                } else {
                    $status = "Active";
                    $status_class = "status-active";
                }
            ?>

                <div class="product-card">

                    <div class="product-top">
                        <div class="product-heading">
                            <h2 class="product-title">
                                <?php echo htmlspecialchars($row["product_name"]); ?>
                            </h2>
                            <span class="category-pill">
                                <?php echo htmlspecialchars($row["category"] ?? 'General'); ?>
                            </span>
                        </div>

                        <div class="status-pill <?php echo $status_class; ?>">
                            <span>●</span>
                            <span><?php echo $status; ?></span>
                            <?php if ($days >= 0): ?>
                                <span>(<?php echo $days; ?> days left)</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">Brand & Model</div>
                            <div class="detail-value">
                                <?php
                                $bm = trim(($row["brand"] ?? '') . ' ' . ($row["model"] ?? ''));
                                echo htmlspecialchars(!empty($bm) ? $bm : '—');
                                ?>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Serial Number</div>
                            <div class="detail-value">
                                <?php echo htmlspecialchars(!empty($row["serial_number"]) ? $row["serial_number"] : '—'); ?>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Purchase Date</div>
                            <div class="detail-value">
                                <?php echo htmlspecialchars($row["purchase_date"]); ?>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Warranty Expiry</div>
                            <div class="detail-value">
                                <?php echo htmlspecialchars($row["expiry_date"]); ?>
                                <span style="font-size: 0.75rem; color: var(--slate-400);">
                                    (<?php echo $row["warranty_duration"]; ?> yr<?php echo $row["warranty_duration"] > 1 ? 's' : ''; ?>)
                                </span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Price</div>
                            <div class="detail-value">
                                <?php echo !empty($row["price"]) ? '₹' . number_format($row["price"], 2) : '—'; ?>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Store / Retailer</div>
                            <div class="detail-value">
                                <?php echo htmlspecialchars(!empty($row["store_name"]) ? $row["store_name"] : '—'); ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($row["notes"])): ?>
                        <div class="notes-section">
                            <strong style="color: var(--slate-700);">Notes:</strong>
                            <?php echo nl2br(htmlspecialchars($row["notes"])); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card-actions">
                        <a class="btn btn-secondary" href="edit_product.php?id=<?php echo $row["id"]; ?>">
                            Edit Product
                        </a>
                        <a
                            class="btn btn-danger-outline"
                            href="delete_product.php?id=<?php echo $row["id"]; ?>"
                            onclick="return confirm('Are you sure you want to delete this product?');"
                        >
                            Delete
                        </a>
                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="empty-state">
            <div class="empty-icon">📦</div>
            <h2 class="empty-title">No Products Found</h2>
            <p class="empty-desc">You have not added any products to your vault yet.</p>
            <a href="add_product.php" class="btn btn-primary">
                Add Your First Product
            </a>
        </div>

    <?php endif; ?>

</div>

</body>
</html>