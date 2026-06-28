<?php
// ============================================================
// GREEN HAVEN - Admin Promotions
// ============================================================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdminLoggedIn()) {
    session_start();
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_name'] = 'Demo Admin';
    $_SESSION['admin_role'] = 'manager';
}

$metaTitle = 'Promotions – Admin | ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($metaTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">

<div class="admin-layout">
    
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="<?= SITE_URL ?>/admin/index.php" class="sidebar-logo">
            <span class="sidebar-logo-icon">🌿</span>
            <div class="sidebar-logo-text">Green<span>Admin</span></div>
        </a>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/admin/index.php" class="sidebar-nav-item"><span class="nav-icon">📊</span> Dashboard</a>
            <a href="<?= SITE_URL ?>/admin/products.php" class="sidebar-nav-item"><span class="nav-icon">🪴</span> Products</a>
            <a href="<?= SITE_URL ?>/admin/orders.php" class="sidebar-nav-item"><span class="nav-icon">📦</span> Orders</a>
            <a href="<?= SITE_URL ?>/admin/customers.php" class="sidebar-nav-item"><span class="nav-icon">👥</span> Customers</a>
            <a href="<?= SITE_URL ?>/admin/promotions.php" class="sidebar-nav-item active"><span class="nav-icon">🎟️</span> Promotions</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title">Discount Codes & Promotions</h1>
            <button class="btn btn-green">＋ Create Promo Code</button>
        </header>

        <div class="admin-content">
            
            <div class="admin-card">
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Usage Limit</th>
                                <th>Valid Until</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Mock Data -->
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--gold-dark); font-family: monospace; font-size: 1.1rem;">WELCOME10</div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">10% off on first order</div>
                                </td>
                                <td style="font-weight: 600;">10%</td>
                                <td>
                                    <div style="font-size: 0.8rem; color: var(--gray-600); margin-bottom: 4px;">45 / Unlimited</div>
                                    <div class="stock-bar-track" style="width: 100px;">
                                        <div class="stock-bar-fill" style="width: 10%; background: var(--green-deep);"></div>
                                    </div>
                                </td>
                                <td>No expiry</td>
                                <td><span style="background: #d1fae5; color: #065f46; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Active</span></td>
                                <td>
                                    <a href="#" style="color: var(--blue); margin-right: 8px; font-size: 0.85rem;">Edit</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--gold-dark); font-family: monospace; font-size: 1.1rem;">MONSOON30</div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">Flat ₹300 off on orders > ₹1500</div>
                                </td>
                                <td style="font-weight: 600;">₹300</td>
                                <td>
                                    <div style="font-size: 0.8rem; color: var(--gray-600); margin-bottom: 4px;">98 / 100</div>
                                    <div class="stock-bar-track" style="width: 100px;">
                                        <div class="stock-bar-fill" style="width: 98%; background: var(--orange);"></div>
                                    </div>
                                </td>
                                <td>Aug 31, 2026</td>
                                <td><span style="background: #d1fae5; color: #065f46; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Active</span></td>
                                <td>
                                    <a href="#" style="color: var(--blue); margin-right: 8px; font-size: 0.85rem;">Edit</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--gray-500); font-family: monospace; font-size: 1.1rem;">SPRING2023</div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">20% off all plants</div>
                                </td>
                                <td style="font-weight: 600; color: var(--gray-500);">20%</td>
                                <td>
                                    <div style="font-size: 0.8rem; color: var(--gray-400); margin-bottom: 4px;">500 / 500</div>
                                    <div class="stock-bar-track" style="width: 100px;">
                                        <div class="stock-bar-fill" style="width: 100%; background: var(--red);"></div>
                                    </div>
                                </td>
                                <td style="color: var(--gray-500);">May 31, 2023</td>
                                <td><span style="background: #fee2e2; color: #991b1b; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Expired</span></td>
                                <td>
                                    <a href="#" style="color: var(--blue); margin-right: 8px; font-size: 0.85rem;">View</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>
