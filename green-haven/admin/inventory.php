<?php
// ============================================================
// GREEN HAVEN - Admin Inventory
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

// Handle Restock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $pid = (int)$_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    $note = sanitize($_POST['note'] ?? '');
    
    // Get current stock
    $stmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $current = $stmt->fetchColumn();
    
    $newStock = $current + $qty;
    
    $db->beginTransaction();
    try {
        // Update product
        $stmt = $db->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->execute([$newStock, $pid]);
        
        // Log it
        $stmt = $db->prepare("INSERT INTO inventory_logs (product_id, admin_id, action, quantity_change, previous_stock, new_stock, notes) VALUES (?, ?, 'restock', ?, ?, ?, ?)");
        $stmt->execute([$pid, $_SESSION['admin_id'], $qty, $current, $newStock, $note]);
        
        $db->commit();
        flashMessage('success', "Stock updated successfully. New total: $newStock");
    } catch (Exception $e) {
        $db->rollBack();
        flashMessage('error', 'Error updating inventory.');
    }
    
    redirect(SITE_URL . '/admin/inventory.php');
}

$metaTitle = 'Inventory Management – Admin | ' . SITE_NAME;
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
            <a href="<?= SITE_URL ?>/admin/inventory.php" class="sidebar-nav-item active"><span class="nav-icon">📋</span> Inventory</a>
            <a href="<?= SITE_URL ?>/admin/reports.php" class="sidebar-nav-item"><span class="nav-icon">📈</span> Reports</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title">Inventory Control</h1>
        </header>

        <div class="admin-content">
            
            <?php if ($flash = getFlashMessage()): ?>
                <div class="alert alert-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                
                <!-- Inventory List -->
                <div class="admin-card" style="margin: 0;">
                    <div class="admin-card-header">
                        <div class="admin-card-title">Current Stock Levels</div>
                        <input type="text" class="admin-search-input" style="max-width: 200px; padding: 6px 12px; font-size: 0.8rem;" placeholder="Search products...">
                    </div>
                    <div style="overflow-x: auto; max-height: 600px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $products = $db->query("SELECT id, name, sku, stock FROM products WHERE is_active = 1 ORDER BY stock ASC")->fetchAll();
                                foreach($products as $p): 
                                ?>
                                <tr>
                                    <td style="font-weight: 500; color: var(--gray-800);"><?= sanitize($p['name']) ?></td>
                                    <td style="font-size: 0.8rem; color: var(--gray-500);"><?= sanitize($p['sku']) ?: 'N/A' ?></td>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <span style="width: 30px; font-weight: 700; color: <?= $p['stock'] > 10 ? '#10b981' : ($p['stock'] > 0 ? 'var(--orange)' : 'var(--red)') ?>;">
                                                <?= $p['stock'] ?>
                                            </span>
                                            <div class="stock-bar-track">
                                                <div class="stock-bar-fill" style="width: <?= min(100, ($p['stock']/50)*100) ?>%; background: <?= $p['stock'] > 10 ? '#10b981' : ($p['stock'] > 0 ? 'var(--orange)' : 'var(--red)') ?>;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($p['stock'] > 10): ?>
                                            <span style="color: #10b981; font-size: 0.75rem; font-weight: 600;">Optimal</span>
                                        <?php elseif($p['stock'] > 0): ?>
                                            <span style="color: var(--orange); font-size: 0.75rem; font-weight: 600;">Low Stock</span>
                                        <?php else: ?>
                                            <span style="color: var(--red); font-size: 0.75rem; font-weight: 600;">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-sm" onclick="prepRestock(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>')">Restock</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Actions & Logs -->
                <div>
                    
                    <div class="admin-card" style="margin-bottom: 24px;">
                        <div class="admin-card-header">
                            <div class="admin-card-title">Quick Restock</div>
                        </div>
                        <div class="admin-card-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label class="form-label">Selected Product</label>
                                    <select class="form-control" name="product_id" id="restockSelect" required>
                                        <option value="">-- Select Product --</option>
                                        <?php foreach($products as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= sanitize($p['name']) ?> (Current: <?= $p['stock'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Quantity to Add</label>
                                    <input type="number" name="quantity" class="form-control" min="1" value="10" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Notes (Optional)</label>
                                    <input type="text" name="note" class="form-control" placeholder="e.g., Supplier Batch #123">
                                </div>
                                <button type="submit" class="btn btn-green btn-full">Update Stock</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="admin-card" style="margin: 0;">
                        <div class="admin-card-header">
                            <div class="admin-card-title">Recent Activity</div>
                        </div>
                        <div style="padding: 16px;">
                            <?php 
                            $logs = $db->query("
                                SELECT l.*, p.name as product_name 
                                FROM inventory_logs l 
                                JOIN products p ON l.product_id = p.id 
                                ORDER BY l.created_at DESC LIMIT 5
                            ")->fetchAll();
                            ?>
                            
                            <?php if(count($logs) > 0): ?>
                                <?php foreach($logs as $log): ?>
                                <div style="border-bottom: 1px solid var(--gray-100); padding-bottom: 12px; margin-bottom: 12px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <strong style="font-size: 0.85rem; color: var(--gray-800);"><?= sanitize($log['product_name']) ?></strong>
                                        <span style="color: #10b981; font-weight: 700; font-size: 0.85rem;">+<?= $log['quantity_change'] ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--gray-500);">
                                        <span><?= ucfirst($log['action']) ?></span>
                                        <span><?= date('M d, g:i A', strtotime($log['created_at'])) ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="text-align: center; color: var(--gray-400); font-size: 0.85rem; padding: 20px;">No recent activity</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
            </div>

        </div>
    </main>
</div>

<script>
function prepRestock(id, name) {
    const select = document.getElementById('restockSelect');
    select.value = id;
    
    // Smooth scroll to form if needed
    select.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    // Highlight form briefly
    select.parentElement.parentElement.style.transition = 'background 0.3s';
    select.parentElement.parentElement.style.background = 'var(--green-pale)';
    setTimeout(() => {
        select.parentElement.parentElement.style.background = 'transparent';
    }, 1000);
}
</script>

</body>
</html>
