<?php
// ============================================================
// GREEN HAVEN - Admin Orders
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

// Handle Status Update via AJAX (Simulated)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    exit(json_encode(['success' => true]));
}

$metaTitle = 'Manage Orders – Admin | ' . SITE_NAME;
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
    
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="<?= SITE_URL ?>/admin/index.php" class="sidebar-logo">
            <span class="sidebar-logo-icon">🌿</span>
            <div class="sidebar-logo-text">Green<span>Admin</span></div>
        </a>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/admin/index.php" class="sidebar-nav-item"><span class="nav-icon">📊</span> Dashboard</a>
            <a href="<?= SITE_URL ?>/admin/products.php" class="sidebar-nav-item"><span class="nav-icon">🪴</span> Products</a>
            <a href="<?= SITE_URL ?>/admin/orders.php" class="sidebar-nav-item active"><span class="nav-icon">📦</span> Orders</a>
            <a href="<?= SITE_URL ?>/admin/customers.php" class="sidebar-nav-item"><span class="nav-icon">👥</span> Customers</a>
            <a href="<?= SITE_URL ?>/admin/reports.php" class="sidebar-nav-item"><span class="nav-icon">📈</span> Reports</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title">Orders Management</h1>
        </header>

        <div class="admin-content">
            
            <div class="admin-toolbar">
                <div class="admin-search">
                    <span class="admin-search-icon">🔍</span>
                    <input type="text" class="admin-search-input" placeholder="Search by Order ID or Customer Name...">
                </div>
                <select class="admin-filter-select">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="out_for_delivery">Out for Delivery</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <input type="date" class="admin-filter-select">
            </div>
            
            <div class="admin-card">
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th width="80">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $orders = $db->query("
                                SELECT o.*, u.name as customer_name 
                                FROM orders o 
                                JOIN users u ON o.user_id = u.id 
                                ORDER BY o.placed_at DESC
                            ")->fetchAll();
                            
                            $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered', 'cancelled'];
                            
                            foreach($orders as $o): 
                            ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: var(--green-deep);"><?= $o['order_number'] ?></div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);"><?= count(json_decode($o['id'] ?? '[]') ?? [1]) ?> items</div>
                                </td>
                                <td>
                                    <div><?= date('M d, Y', strtotime($o['placed_at'])) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);"><?= date('g:i A', strtotime($o['placed_at'])) ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 500; color: var(--gray-800);"><?= sanitize($o['customer_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);"><?= sanitize($o['delivery_city']) ?></div>
                                </td>
                                <td style="font-weight: 600; color: var(--gray-800);"><?= formatPrice($o['total']) ?></td>
                                <td>
                                    <div style="text-transform: uppercase; font-size: 0.8rem; font-weight: 600; color: var(--gray-600);"><?= sanitize($o['payment_method']) ?></div>
                                    <?php if($o['payment_status'] === 'paid'): ?>
                                        <span style="color: #10b981; font-size: 0.75rem; font-weight: 600;">Paid</span>
                                    <?php else: ?>
                                        <span style="color: var(--orange); font-size: 0.75rem; font-weight: 600;">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <select class="status-select" onchange="updateStatus(<?= $o['id'] ?>, this.value)" style="border-color: <?= $o['status'] === 'delivered' ? '#10b981' : 'var(--gray-200)' ?>">
                                        <?php foreach($statuses as $s): ?>
                                            <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-outline-sm" onclick="openOrderModal(<?= $o['id'] ?>)">View</button>
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

<!-- Order Detail Modal (Template) -->
<div class="admin-modal-overlay" id="orderModal">
    <div class="admin-modal">
        <div class="modal-header">
            <h2 class="modal-title">Order Details: <span style="color:var(--gray-800);" id="modalOrderNum"></span></h2>
            <button class="modal-close" onclick="closeOrderModal()">×</button>
        </div>
        <div class="modal-body" id="modalBody">
            <div style="text-align: center; padding: 40px; color: var(--gray-500);">Loading...</div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeOrderModal()">Close</button>
            <button class="btn btn-green">Print Invoice</button>
        </div>
    </div>
</div>

<script>
function updateStatus(orderId, status) {
    // Simulated AJAX
    fetch('orders.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `order_id=${orderId}&status=${status}`
    }).then(res => res.json()).then(data => {
        if(data.success) {
            // Optional flash toast
            console.log('Status updated');
        }
    });
}

function openOrderModal(orderId) {
    document.getElementById('orderModal').classList.add('open');
    document.getElementById('modalOrderNum').innerText = '#' + orderId;
    
    // Simulate loading details
    setTimeout(() => {
        document.getElementById('modalBody').innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div>
                    <h4 style="color: var(--gray-500); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 8px;">Customer Info</h4>
                    <div style="font-weight: 600; color: var(--gray-800);">Customer Name</div>
                    <div style="font-size: 0.9rem; color: var(--gray-600);">customer@example.com<br>+91 9876543210</div>
                </div>
                <div>
                    <h4 style="color: var(--gray-500); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 8px;">Delivery Address</h4>
                    <div style="font-size: 0.9rem; color: var(--gray-600); line-height: 1.5;">123 Street Name, Building<br>City Name, State<br>123456</div>
                </div>
            </div>
            
            <table class="data-table">
                <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                <tbody>
                    <tr><td>Mock Plant Name</td><td>1</td><td>₹499</td><td>₹499</td></tr>
                </tbody>
            </table>
        `;
    }, 500);
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.remove('open');
    setTimeout(() => {
        document.getElementById('modalBody').innerHTML = '<div style="text-align: center; padding: 40px; color: var(--gray-500);">Loading...</div>';
    }, 300);
}
</script>

</body>
</html>
