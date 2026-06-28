<?php
// ============================================================
// GREEN HAVEN - Admin Dashboard
// ============================================================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Temporarily bypass admin check for development if needed, or enforce it
// requireAdmin(); // Uncomment in production

// Mock admin session for demo if not logged in
if (!isAdminLoggedIn()) {
    session_start();
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_name'] = 'Demo Admin';
    $_SESSION['admin_role'] = 'manager';
}

$db = getDB();

// Fetch KPIs
$totalRevenue = $db->query("SELECT SUM(total) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?: 0;
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 0;
$activeCustomers = $db->query("SELECT COUNT(*) FROM users WHERE is_blocked = 0")->fetchColumn() ?: 0;
$productsInStock = $db->query("SELECT SUM(stock) FROM products")->fetchColumn() ?: 0;

// Fetch Recent Orders
$recentOrders = $db->query("
    SELECT o.id, o.order_number, o.placed_at, o.total, o.status, u.name as customer_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.placed_at DESC LIMIT 10
")->fetchAll();

// Fetch Low Stock
$lowStock = $db->query("SELECT id, name, stock FROM products WHERE stock <= 10 ORDER BY stock ASC")->fetchAll();

$metaTitle = 'Dashboard – Admin | ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($metaTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="admin-body">

<div class="admin-layout">
    
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="<?= SITE_URL ?>/admin/index.php" class="sidebar-logo">
            <span class="sidebar-logo-icon">🌿</span>
            <div class="sidebar-logo-text">Green<span>Admin</span></div>
        </a>
        
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= substr($_SESSION['admin_name'], 0, 1) ?></div>
            <div>
                <div class="sidebar-user-name"><?= sanitize($_SESSION['admin_name']) ?></div>
                <div class="sidebar-user-role"><?= sanitize($_SESSION['admin_role']) ?></div>
            </div>
            <span class="sidebar-badge <?= $_SESSION['admin_role'] ?>"><?= substr($_SESSION['admin_role'], 0, 3) ?></span>
        </div>
        
        <nav class="sidebar-nav">
            <div class="sidebar-nav-label">Main Menu</div>
            <a href="<?= SITE_URL ?>/admin/index.php" class="sidebar-nav-item active">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <a href="#" class="sidebar-nav-item">
                <span class="nav-icon">🪴</span> Products
            </a>
            <a href="#" class="sidebar-nav-item">
                <span class="nav-icon">📦</span> Orders
                <?php $pending = $db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn(); ?>
                <?php if($pending): ?><span class="sidebar-nav-badge"><?= $pending ?></span><?php endif; ?>
            </a>
            <a href="#" class="sidebar-nav-item">
                <span class="nav-icon">👥</span> Customers
            </a>
            
            <div class="sidebar-nav-label" style="margin-top: 16px;">Management</div>
            <a href="#" class="sidebar-nav-item">
                <span class="nav-icon">📋</span> Inventory
                <?php if(count($lowStock) > 0): ?><span class="sidebar-nav-badge" style="background:var(--orange);"><?= count($lowStock) ?></span><?php endif; ?>
            </a>
            <a href="#" class="sidebar-nav-item">
                <span class="nav-icon">🏷️</span> Categories
            </a>
            <a href="#" class="sidebar-nav-item">
                <span class="nav-icon">🎟️</span> Promotions
            </a>
            
            <div class="sidebar-nav-label" style="margin-top: 16px;">Analytics & Support</div>
            <a href="#" class="sidebar-nav-item">
                <span class="nav-icon">📈</span> Reports
            </a>
            <a href="#" class="sidebar-nav-item">
                <span class="nav-icon">💬</span> Support
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="<?= SITE_URL ?>/auth/logout.php" class="sidebar-logout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Sign Out
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        
        <header class="admin-header">
            <div class="admin-header-left">
                <button class="admin-header-btn" id="sidebarToggle" style="display: none;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <h1 class="admin-page-title">Dashboard Overview</h1>
            </div>
            
            <div class="admin-header-right">
                <a href="<?= SITE_URL ?>/index.php" target="_blank" class="btn btn-outline-sm" style="color: var(--green-deep); border-color: var(--gray-200);">View Store</a>
                <button class="admin-header-btn" aria-label="Notifications">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <span style="position:absolute; top:8px; right:8px; width:8px; height:8px; background:var(--red); border-radius:50%;"></span>
                </button>
            </div>
        </header>

        <div class="admin-content">
            
            <!-- Welcome Message -->
            <div style="margin-bottom: 28px;">
                <h2 style="font-size: 1.1rem; color: var(--gray-700);">Welcome back, <?= sanitize($_SESSION['admin_name']) ?> 👋</h2>
                <p style="color: var(--gray-500); font-size: 0.9rem;">Here's what's happening with your store today.</p>
            </div>

            <!-- KPIs -->
            <div class="kpi-grid">
                <div class="kpi-card green">
                    <div class="kpi-top">
                        <div class="kpi-icon green">💰</div>
                        <div class="kpi-change up">↑ 12%</div>
                    </div>
                    <div class="kpi-value"><?= formatPrice($totalRevenue) ?></div>
                    <div class="kpi-label">Total Revenue</div>
                </div>
                <div class="kpi-card blue">
                    <div class="kpi-top">
                        <div class="kpi-icon blue">📦</div>
                        <div class="kpi-change up">↑ 8%</div>
                    </div>
                    <div class="kpi-value"><?= number_format($totalOrders) ?></div>
                    <div class="kpi-label">Total Orders</div>
                </div>
                <div class="kpi-card gold">
                    <div class="kpi-top">
                        <div class="kpi-icon gold">👥</div>
                        <div class="kpi-change up">↑ 5%</div>
                    </div>
                    <div class="kpi-value"><?= number_format($activeCustomers) ?></div>
                    <div class="kpi-label">Active Customers</div>
                </div>
                <div class="kpi-card orange">
                    <div class="kpi-top">
                        <div class="kpi-icon orange">🪴</div>
                        <div class="kpi-change down">↓ 2%</div>
                    </div>
                    <div class="kpi-value"><?= number_format($productsInStock) ?></div>
                    <div class="kpi-label">Products in Stock</div>
                </div>
            </div>

            <!-- Charts & Alerts -->
            <div class="charts-grid">
                
                <div class="admin-card" style="margin-bottom: 0;">
                    <div class="admin-card-header">
                        <div class="admin-card-title">Revenue (Last 7 Days)</div>
                        <select class="admin-filter-select" style="padding: 4px 10px; font-size: 0.8rem;">
                            <option>Last 7 Days</option>
                            <option>This Month</option>
                        </select>
                    </div>
                    <div class="admin-card-body">
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="admin-card" style="margin-bottom: 0;">
                    <div class="admin-card-header" style="background: #fff7ed; border-bottom-color: #ffedd5;">
                        <div class="admin-card-title" style="color: #ea580c;">⚠️ Low Stock Alerts</div>
                    </div>
                    <div class="admin-card-body" style="padding: 16px;">
                        <?php if(count($lowStock) > 0): ?>
                            <div class="low-stock-list">
                                <?php foreach($lowStock as $item): ?>
                                <div class="low-stock-item">
                                    <div class="low-stock-icon">🪴</div>
                                    <div class="low-stock-name"><?= sanitize($item['name']) ?></div>
                                    <div class="low-stock-qty"><?= $item['stock'] ?> left</div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <a href="#" class="btn btn-outline-sm btn-full" style="margin-top: 16px; color: var(--green-deep); border-color: var(--gray-200);">View Inventory</a>
                        <?php else: ?>
                            <div style="text-align: center; padding: 30px 10px; color: var(--gray-500);">
                                <div style="font-size: 2rem; margin-bottom: 10px;">✅</div>
                                All products are well stocked.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>

            <!-- Recent Orders -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Recent Orders</div>
                    <a href="#" class="btn btn-outline-sm" style="color: var(--green-deep); border-color: var(--gray-200); padding: 4px 12px;">View All</a>
                </div>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($recentOrders) > 0): ?>
                                <?php foreach($recentOrders as $order): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--green-deep);"><?= $order['order_number'] ?></td>
                                    <td><?= sanitize($order['customer_name']) ?></td>
                                    <td><?= date('M d, Y g:i A', strtotime($order['placed_at'])) ?></td>
                                    <td style="font-weight: 600;"><?= formatPrice($order['total']) ?></td>
                                    <td><?= getOrderStatusBadge($order['status']) ?></td>
                                    <td>
                                        <a href="#" style="color: var(--blue); font-weight: 500; font-size: 0.8rem;">View Details</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px;">No recent orders found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
// Mock Chart Data
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart');
    if(ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue (₹)',
                    data: [12500, 15000, 11000, 18500, 14000, 22000, 25000],
                    borderColor: '#013220',
                    backgroundColor: 'rgba(1, 50, 32, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#D4AF37',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#f3f4f6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Sidebar Toggle for Mobile
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.getElementById('sidebarToggle');
    
    if(window.innerWidth <= 768) {
        toggle.style.display = 'flex';
    }
    
    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
});
</script>

</body>
</html>
