<?php
// ============================================================
// GREEN HAVEN - Admin Customers
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

$db = getDB();

// Handle Block/Unblock
if (isset($_GET['toggle_block'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $val = (int)$_GET['toggle_block'];
    $stmt = $db->prepare("UPDATE users SET is_blocked = ? WHERE id = ?");
    $stmt->execute([$val, $id]);
    
    flashMessage('success', 'Customer status updated.');
    redirect(SITE_URL . '/admin/customers.php');
}

$metaTitle = 'Manage Customers – Admin | ' . SITE_NAME;
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
            <a href="<?= SITE_URL ?>/admin/customers.php" class="sidebar-nav-item active"><span class="nav-icon">👥</span> Customers</a>
            <a href="<?= SITE_URL ?>/admin/reports.php" class="sidebar-nav-item"><span class="nav-icon">📈</span> Reports</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title">Customers Directory</h1>
        </header>

        <div class="admin-content">
            
            <?php if ($flash = getFlashMessage()): ?>
                <div class="alert alert-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
            <?php endif; ?>
            
            <div class="admin-toolbar">
                <div class="admin-search">
                    <span class="admin-search-icon">🔍</span>
                    <input type="text" class="admin-search-input" placeholder="Search by name, email, or phone...">
                </div>
                <select class="admin-filter-select">
                    <option value="">All Customers</option>
                    <option value="active">Active</option>
                    <option value="blocked">Blocked</option>
                </select>
            </div>
            
            <div class="admin-card">
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name / Email</th>
                                <th>Phone</th>
                                <th>Orders</th>
                                <th>Total Spent</th>
                                <th>Joined</th>
                                <th>Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $users = $db->query("
                                SELECT u.id, u.name, u.email, u.phone, u.created_at, u.is_blocked,
                                       COUNT(o.id) as order_count, SUM(o.total) as total_spent
                                FROM users u
                                LEFT JOIN orders o ON u.id = o.user_id AND o.status != 'cancelled'
                                WHERE u.role = 'customer'
                                GROUP BY u.id
                                ORDER BY u.created_at DESC
                            ")->fetchAll();
                            
                            foreach($users as $u): 
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="sidebar-avatar" style="width: 32px; height: 32px; font-size: 0.8rem;"><?= substr($u['name'], 0, 1) ?></div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--gray-800);"><?= sanitize($u['name']) ?></div>
                                            <div style="font-size: 0.8rem; color: var(--gray-500);"><?= sanitize($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size: 0.9rem;"><?= sanitize($u['phone']) ?: '-' ?></td>
                                <td style="font-weight: 600;"><?= $u['order_count'] ?></td>
                                <td style="font-weight: 600; color: var(--green-deep);"><?= formatPrice($u['total_spent'] ?? 0) ?></td>
                                <td style="font-size: 0.85rem; color: var(--gray-600);"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <?php if($u['is_blocked']): ?>
                                        <span style="background: #fee2e2; color: #991b1b; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Blocked</span>
                                    <?php else: ?>
                                        <span style="background: #d1fae5; color: #065f46; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-outline-sm" style="margin-right: 4px;" onclick="alert('View Customer Profile')">View</button>
                                    
                                    <?php if($u['is_blocked']): ?>
                                        <a href="?toggle_block=0&id=<?= $u['id'] ?>" class="btn btn-outline-sm" style="color: var(--green-deep); border-color: var(--green-deep);" onclick="return confirm('Unblock this user?')">Unblock</a>
                                    <?php else: ?>
                                        <a href="?toggle_block=1&id=<?= $u['id'] ?>" class="btn btn-outline-sm" style="color: var(--red); border-color: var(--red);" onclick="return confirm('Are you sure you want to block this user?')">Block</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>
