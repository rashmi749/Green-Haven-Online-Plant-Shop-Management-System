<?php
// ============================================================
// GREEN HAVEN - Admin Products
// ============================================================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// requireAdmin(); // Uncomment in production
if (!isAdminLoggedIn()) {
    session_start();
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_name'] = 'Demo Admin';
    $_SESSION['admin_role'] = 'manager';
}

$db = getDB();
$action = $_GET['action'] ?? 'list';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
    $stmt->execute([$id]);
    flashMessage('success', 'Product deactivated successfully.');
    redirect(SITE_URL . '/admin/products.php');
}

// Fetch all categories for dropdown
$categories = $db->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();

$metaTitle = 'Manage Products – Admin | ' . SITE_NAME;
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
    
    <!-- Inline Sidebar (simplified for brevity) -->
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="<?= SITE_URL ?>/admin/index.php" class="sidebar-logo">
            <span class="sidebar-logo-icon">🌿</span>
            <div class="sidebar-logo-text">Green<span>Admin</span></div>
        </a>
        <div class="sidebar-user">
            <div class="sidebar-avatar">A</div>
            <div>
                <div class="sidebar-user-name"><?= sanitize($_SESSION['admin_name']) ?></div>
                <div class="sidebar-user-role"><?= sanitize($_SESSION['admin_role']) ?></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/admin/index.php" class="sidebar-nav-item"><span class="nav-icon">📊</span> Dashboard</a>
            <a href="<?= SITE_URL ?>/admin/products.php" class="sidebar-nav-item active"><span class="nav-icon">🪴</span> Products</a>
            <a href="<?= SITE_URL ?>/admin/orders.php" class="sidebar-nav-item"><span class="nav-icon">📦</span> Orders</a>
            <a href="<?= SITE_URL ?>/admin/customers.php" class="sidebar-nav-item"><span class="nav-icon">👥</span> Customers</a>
            <a href="<?= SITE_URL ?>/admin/inventory.php" class="sidebar-nav-item"><span class="nav-icon">📋</span> Inventory</a>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= SITE_URL ?>/auth/logout.php" class="sidebar-logout">Sign Out</a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title"><?= $action === 'add' ? 'Add New Product' : ($action === 'edit' ? 'Edit Product' : 'Products Library') ?></h1>
            <div class="admin-header-right">
                <?php if($action === 'list'): ?>
                    <a href="?action=add" class="btn btn-green">＋ Add Product</a>
                <?php else: ?>
                    <a href="?" class="btn btn-outline-sm">← Back to List</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="admin-content">
            
            <?php if ($flash = getFlashMessage()): ?>
                <div class="alert alert-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
            <?php endif; ?>

            <?php if($action === 'list'): ?>
                
                <!-- Toolbar -->
                <div class="admin-toolbar">
                    <div class="admin-search">
                        <span class="admin-search-icon">🔍</span>
                        <input type="text" class="admin-search-input" placeholder="Search products by name or SKU...">
                    </div>
                    <select class="admin-filter-select">
                        <option value="">All Categories</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="admin-filter-select">
                        <option value="">Stock Status</option>
                        <option value="low">Low Stock (< 10)</option>
                        <option value="out">Out of Stock</option>
                    </select>
                </div>
                
                <!-- Data Table -->
                <div class="admin-card">
                    <div style="overflow-x: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="60">Img</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $products = $db->query("
                                    SELECT p.id, p.name, p.price, p.sale_price, p.stock, p.is_active, p.is_featured, c.name as category_name 
                                    FROM products p 
                                    LEFT JOIN categories c ON p.category_id = c.id 
                                    ORDER BY p.created_at DESC
                                ")->fetchAll();
                                
                                foreach($products as $p): 
                                ?>
                                <tr>
                                    <td><div class="product-thumb">🌿</div></td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--green-deep); margin-bottom: 4px;"><?= sanitize($p['name']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--gray-500); display: flex; gap: 8px;">
                                            <?php if($p['is_featured']): ?><span style="color: var(--gold-dark); font-weight: 700;">★ Featured</span><?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= sanitize($p['category_name']) ?></td>
                                    <td>
                                        <?php if($p['sale_price']): ?>
                                            <span style="font-weight: 600; color: var(--green-deep);"><?= formatPrice($p['sale_price']) ?></span>
                                            <span style="font-size: 0.8rem; color: var(--gray-400); text-decoration: line-through; display: block;"><?= formatPrice($p['price']) ?></span>
                                        <?php else: ?>
                                            <span style="font-weight: 600; color: var(--gray-700);"><?= formatPrice($p['price']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($p['stock'] > 10): ?>
                                            <span class="stock-level-high"><?= $p['stock'] ?></span>
                                        <?php elseif($p['stock'] > 0): ?>
                                            <span class="stock-level-med"><?= $p['stock'] ?></span>
                                        <?php else: ?>
                                            <span class="stock-level-low">Out</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($p['is_active']): ?>
                                            <span style="background: #d1fae5; color: #065f46; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Active</span>
                                        <?php else: ?>
                                            <span style="background: var(--gray-200); color: var(--gray-600); padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?action=edit&id=<?= $p['id'] ?>" style="color: var(--blue); margin-right: 8px; font-size: 0.85rem; font-weight: 500;">Edit</a>
                                        <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to deactivate this product?')" style="color: var(--red); font-size: 0.85rem; font-weight: 500;">Del</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif($action === 'add' || $action === 'edit'): ?>
                
                <form class="admin-form" method="POST" action="?action=save">
                    <!-- Form fields go here (Simulated structure) -->
                    <div class="admin-card">
                        <div class="admin-card-body">
                            <div class="form-section-title">Basic Information</div>
                            
                            <div class="form-group">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">Category</label>
                                    <select class="form-control" name="category_id">
                                        <?php foreach($categories as $c): ?>
                                            <option value="<?= $c['id'] ?>"><?= sanitize($c['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">SKU</label>
                                    <input type="text" class="form-control" name="sku">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Short Description</label>
                                <textarea class="form-control" name="short_desc" rows="2"></textarea>
                            </div>
                            
                            <div class="form-section-title">Pricing & Inventory</div>
                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label class="form-label">Regular Price (₹)</label>
                                    <input type="number" class="form-control" name="price" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Sale Price (₹)</label>
                                    <input type="number" class="form-control" name="sale_price" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control" name="stock" value="0">
                                </div>
                            </div>
                            
                            <div class="form-section-title">Care Requirements</div>
                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label class="form-label">Care Level</label>
                                    <select class="form-control" name="care_level">
                                        <option value="easy">Easy</option>
                                        <option value="moderate">Moderate</option>
                                        <option value="expert">Expert</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Light Needs</label>
                                    <select class="form-control" name="light_requirement">
                                        <option value="low">Low Light</option>
                                        <option value="medium">Medium Light</option>
                                        <option value="bright">Bright Indirect</option>
                                        <option value="direct">Direct Sun</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Watering</label>
                                    <input type="text" class="form-control" name="watering_frequency" placeholder="e.g., Every 1-2 weeks">
                                </div>
                            </div>
                            
                            <div class="form-section-title">Visibility</div>
                            <div style="display: flex; gap: 32px; margin-bottom: 24px;">
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                    <div class="toggle-switch">
                                        <input type="checkbox" name="is_active" checked>
                                        <div class="toggle-slider"></div>
                                    </div>
                                    <span style="font-size: 0.9rem; font-weight: 500;">Active on store</span>
                                </label>
                                
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                    <div class="toggle-switch">
                                        <input type="checkbox" name="is_featured">
                                        <div class="toggle-slider"></div>
                                    </div>
                                    <span style="font-size: 0.9rem; font-weight: 500;">Featured product</span>
                                </label>
                            </div>
                            
                            <button type="button" class="btn btn-green btn-lg" onclick="alert('Demo: Product Saved!'); window.location.href='?';">Save Product</button>
                        </div>
                    </div>
                </form>
                
            <?php endif; ?>

        </div>
    </main>
</div>

</body>
</html>
