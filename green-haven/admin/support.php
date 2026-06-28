<?php
// ============================================================
// GREEN HAVEN - Admin Support
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

$metaTitle = 'Support Tickets – Admin | ' . SITE_NAME;
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
            <a href="<?= SITE_URL ?>/admin/support.php" class="sidebar-nav-item active"><span class="nav-icon">💬</span> Support</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title">Customer Support</h1>
        </header>

        <div class="admin-content">
            
            <div style="display: grid; grid-template-columns: 350px 1fr; gap: 24px; align-items: start;">
                
                <!-- Ticket List -->
                <div class="admin-card" style="margin: 0;">
                    <div class="admin-card-header" style="padding: 16px;">
                        <input type="text" class="admin-search-input" style="padding: 8px 12px; font-size: 0.85rem;" placeholder="Search tickets...">
                    </div>
                    
                    <div style="display: flex; border-bottom: 1px solid var(--gray-100);">
                        <button style="flex:1; padding:12px; border:none; background:white; border-bottom:2px solid var(--gold); color:var(--green-deep); font-weight:600; font-size:0.85rem; cursor:pointer;">Open</button>
                        <button style="flex:1; padding:12px; border:none; background:white; border-bottom:2px solid transparent; color:var(--gray-500); font-weight:600; font-size:0.85rem; cursor:pointer;">Resolved</button>
                    </div>
                    
                    <div style="padding: 16px; overflow-y: auto; max-height: calc(100vh - 200px);">
                        <?php 
                        $tickets = $db->query("SELECT * FROM support_tickets ORDER BY created_at DESC")->fetchAll();
                        
                        foreach($tickets as $t): 
                        ?>
                        <div class="ticket-card <?= $t['status'] === 'open' ? 'open' : ($t['status'] === 'in_progress' ? 'in_progress' : 'resolved') ?>">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                <span style="font-size: 0.75rem; color: var(--gray-500); font-weight: 600; text-transform: uppercase;"><?= sanitize($t['type']) ?></span>
                                <span style="font-size: 0.75rem; color: var(--gray-400);"><?= date('M d', strtotime($t['created_at'])) ?></span>
                            </div>
                            <div style="font-weight: 600; font-size: 0.9rem; color: var(--gray-800); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= sanitize($t['subject']) ?>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--gray-500); margin-bottom: 8px;">
                                From: <?= sanitize($t['name']) ?>
                            </div>
                            <div>
                                <?php if($t['status'] === 'open'): ?>
                                    <span style="background: #eff6ff; color: #1d4ed8; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">Open</span>
                                <?php elseif($t['status'] === 'in_progress'): ?>
                                    <span style="background: #fff7ed; color: #c2410c; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">In Progress</span>
                                <?php else: ?>
                                    <span style="background: #f0fdf4; color: #15803d; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">Resolved</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if(empty($tickets)): ?>
                            <div style="text-align: center; color: var(--gray-500); padding: 40px 0; font-size: 0.9rem;">No tickets found.</div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Ticket Detail -->
                <div class="admin-card" style="margin: 0; min-height: 500px; display: flex; flex-direction: column;">
                    <?php if(!empty($tickets)): $active = $tickets[0]; ?>
                    
                    <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 1.1rem; font-weight: 700; color: var(--green-deep); margin-bottom: 4px;"><?= sanitize($active['subject']) ?></div>
                            <div style="font-size: 0.85rem; color: var(--gray-500);">Ticket #GH-<?= str_pad($active['id'], 4, '0', STR_PAD_LEFT) ?> • <?= sanitize($active['type']) ?></div>
                        </div>
                        <div>
                            <select class="status-select" style="border-color: var(--blue);">
                                <option value="open" <?= $active['status']=='open'?'selected':'' ?>>Open</option>
                                <option value="in_progress" <?= $active['status']=='in_progress'?'selected':'' ?>>In Progress</option>
                                <option value="resolved" <?= $active['status']=='resolved'?'selected':'' ?>>Resolved</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="admin-card-body" style="flex: 1; overflow-y: auto; background: #f9fafb;">
                        
                        <!-- Customer Message -->
                        <div style="display: flex; gap: 16px; margin-bottom: 24px;">
                            <div class="sidebar-avatar" style="background: var(--gray-200); color: var(--gray-600);"><?= substr($active['name'], 0, 1) ?></div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--gray-800);"><?= sanitize($active['name']) ?> <span style="font-weight:400; color:var(--gray-500); font-size:0.8rem;">(<?= sanitize($active['email']) ?>)</span></div>
                                    <div style="font-size: 0.8rem; color: var(--gray-400);"><?= date('M d, Y g:i A', strtotime($active['created_at'])) ?></div>
                                </div>
                                <div style="background: white; padding: 16px; border-radius: 0 12px 12px 12px; border: 1px solid var(--gray-200); color: var(--gray-700); font-size: 0.95rem; line-height: 1.6; box-shadow: var(--shadow-sm);">
                                    <?= nl2br(sanitize($active['message'])) ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Admin Reply (if exists) -->
                        <?php if($active['admin_reply']): ?>
                        <div style="display: flex; gap: 16px; margin-bottom: 24px; flex-direction: row-reverse;">
                            <div class="sidebar-avatar"><?= substr($_SESSION['admin_name'], 0, 1) ?></div>
                            <div style="flex: 1; text-align: right;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; flex-direction: row-reverse;">
                                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--green-deep);"><?= sanitize($_SESSION['admin_name']) ?> <span style="font-weight:400; color:var(--gray-500); font-size:0.8rem;">(Support Team)</span></div>
                                    <div style="font-size: 0.8rem; color: var(--gray-400);"><?= date('M d, Y', strtotime($active['updated_at'])) ?></div>
                                </div>
                                <div style="background: var(--green-pale); padding: 16px; border-radius: 12px 0 12px 12px; color: var(--green-deep); font-size: 0.95rem; line-height: 1.6; text-align: left; display: inline-block;">
                                    <?= nl2br(sanitize($active['admin_reply'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                    
                    <div style="padding: 16px; border-top: 1px solid var(--gray-200); background: white;">
                        <textarea class="form-control" style="min-height: 100px; margin-bottom: 12px;" placeholder="Type your reply here..."></textarea>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <button class="btn btn-outline-sm" style="border:none; color:var(--gray-500);">📎 Attach File</button>
                            </div>
                            <button class="btn btn-green">Send Reply</button>
                        </div>
                    </div>
                    
                    <?php else: ?>
                        <div style="flex: 1; display: flex; align-items: center; justify-content: center; color: var(--gray-500); flex-direction: column;">
                            <div style="font-size: 4rem; margin-bottom: 16px; opacity: 0.5;">💬</div>
                            <p>Select a ticket to view details</p>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>

        </div>
    </main>
</div>

</body>
</html>
