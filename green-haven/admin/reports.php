<?php
// ============================================================
// GREEN HAVEN - Admin Reports
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

$metaTitle = 'Reports & Analytics – Admin | ' . SITE_NAME;
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="<?= SITE_URL ?>/admin/reports.php" class="sidebar-nav-item active"><span class="nav-icon">📈</span> Reports</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title">Sales Reports & Analytics</h1>
            <div class="admin-header-right">
                <select class="admin-filter-select" style="padding: 6px 12px;">
                    <option>Last 30 Days</option>
                    <option>This Month</option>
                    <option>Last Quarter</option>
                    <option>This Year</option>
                </select>
                <button class="btn btn-outline-sm">Export CSV</button>
            </div>
        </header>

        <div class="admin-content">
            
            <div class="kpi-grid">
                <div class="kpi-card green">
                    <div class="kpi-top"><div class="kpi-label">Gross Revenue</div></div>
                    <div class="kpi-value">₹1,24,500</div>
                    <div class="kpi-change up">↑ 15% vs last period</div>
                </div>
                <div class="kpi-card blue">
                    <div class="kpi-top"><div class="kpi-label">Average Order Value</div></div>
                    <div class="kpi-value">₹1,850</div>
                    <div class="kpi-change up">↑ 5% vs last period</div>
                </div>
                <div class="kpi-card orange">
                    <div class="kpi-top"><div class="kpi-label">Conversion Rate</div></div>
                    <div class="kpi-value">3.2%</div>
                    <div class="kpi-change down">↓ 0.4% vs last period</div>
                </div>
                <div class="kpi-card gold">
                    <div class="kpi-top"><div class="kpi-label">New Customers</div></div>
                    <div class="kpi-value">128</div>
                    <div class="kpi-change up">↑ 22% vs last period</div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
                
                <div class="admin-card" style="margin: 0;">
                    <div class="admin-card-header"><div class="admin-card-title">Revenue Trend</div></div>
                    <div class="admin-card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="admin-card" style="margin: 0;">
                    <div class="admin-card-header"><div class="admin-card-title">Sales by Category</div></div>
                    <div class="admin-card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <div class="admin-card">
                <div class="admin-card-header"><div class="admin-card-title">Top Selling Products</div></div>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Units Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><div style="font-weight: 600; color: var(--green-deep);">Monstera Deliciosa</div></td>
                                <td>Indoor Plants</td>
                                <td style="font-weight: 600;">145</td>
                                <td style="font-weight: 600; color: var(--green-deep);">₹86,855</td>
                            </tr>
                            <tr>
                                <td><div style="font-weight: 600; color: var(--green-deep);">Snake Plant</div></td>
                                <td>Air Purifying</td>
                                <td style="font-weight: 600;">120</td>
                                <td style="font-weight: 600; color: var(--green-deep);">₹47,880</td>
                            </tr>
                            <tr>
                                <td><div style="font-weight: 600; color: var(--green-deep);">Premium Potting Mix</div></td>
                                <td>Accessories</td>
                                <td style="font-weight: 600;">210</td>
                                <td style="font-weight: 600; color: var(--green-deep);">₹31,500</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revCtx = document.getElementById('revenueChart');
    if(revCtx) {
        new Chart(revCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Revenue (₹)',
                    data: [25000, 32000, 28000, 39500],
                    backgroundColor: '#013220',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    
    // Category Chart
    const catCtx = document.getElementById('categoryChart');
    if(catCtx) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: ['Indoor', 'Outdoor', 'Succulents', 'Accessories'],
                datasets: [{
                    data: [45, 25, 15, 15],
                    backgroundColor: ['#013220', '#10b981', '#D4AF37', '#fcd34d'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                }
            }
        });
    }
});
</script>

</body>
</html>
