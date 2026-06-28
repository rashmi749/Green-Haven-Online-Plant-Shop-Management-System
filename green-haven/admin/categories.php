<?php
// ============================================================
// GREEN HAVEN - Admin Categories
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

$metaTitle = 'Categories – Admin | ' . SITE_NAME;
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
            <a href="<?= SITE_URL ?>/admin/categories.php" class="sidebar-nav-item active"><span class="nav-icon">🏷️</span> Categories</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title">Product Categories</h1>
            <button class="btn btn-green">＋ Add Category</button>
        </header>

        <div class="admin-content">
            
            <div class="admin-card">
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="60">Icon</th>
                                <th>Category Name</th>
                                <th>Slug</th>
                                <th>Products Count</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $cats = $db->query("
                                SELECT c.*, COUNT(p.id) as product_count 
                                FROM categories c 
                                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
                                GROUP BY c.id
                                ORDER BY c.name ASC
                            ")->fetchAll();
                            
                            foreach($cats as $c): 
                            ?>
                            <tr>
                                <td><div style="font-size: 1.5rem; text-align: center;"><?= sanitize($c['icon']) ?></div></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--gray-800);"><?= sanitize($c['name']) ?></div>
                                    <div style="font-size: 0.8rem; color: var(--gray-500);"><?= sanitize($c['description'] ?? 'No description') ?></div>
                                </td>
                                <td style="font-family: monospace; font-size: 0.85rem; color: var(--gray-600);"><?= sanitize($c['slug']) ?></td>
                                <td style="font-weight: 600;"><?= $c['product_count'] ?></td>
                                <td>
                                    <?php if($c['is_active']): ?>
                                        <span style="background: #d1fae5; color: #065f46; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Active</span>
                                    <?php else: ?>
                                        <span style="background: var(--gray-200); color: var(--gray-600); padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="#" style="color: var(--blue); margin-right: 8px; font-size: 0.85rem;">Edit</a>
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
