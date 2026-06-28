<?php
// ============================================================
// GREEN HAVEN - Admin Login
// ============================================================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password.';
        } elseif (loginAdmin($email, $password)) {
            redirect(SITE_URL . '/admin/index.php');
        } else {
            $error = 'Invalid credentials or you do not have admin access.';
        }
    }
}

$metaTitle = 'Admin Secure Login – ' . SITE_NAME;
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
    <style>
        body { background: #011d13; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: var(--font-sans); }
        .admin-login-card { background: white; width: 100%; max-width: 420px; border-radius: var(--radius-xl); padding: 40px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); }
        .admin-logo { text-align: center; margin-bottom: 32px; font-family: var(--font-serif); font-size: 1.5rem; color: var(--green-deep); font-weight: 700; }
    </style>
</head>
<body>

<div class="admin-login-card">
    <div class="admin-logo">
        <div style="font-size: 2.5rem; margin-bottom: 8px;">🌿</div>
        Green Haven Admin
    </div>
    
    <h2 style="font-size: 1.1rem; color: var(--gray-600); text-align: center; margin-bottom: 24px;">Secure Access Portal</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <?= sanitize($error) ?>
        </div>
    <?php endif; ?>
    
    <!-- DEMO ACCOUNTS INFO -->
    <div style="background: var(--green-pale); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.8rem; color: var(--green-deep); border: 1px solid var(--gray-200);">
        <strong>Demo Accounts:</strong><br>
        admin@greenhaven.com / admin123 (Manager)<br>
        staff@greenhaven.com / staff123 (Staff)
    </div>
    
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="form-group">
            <label class="form-label" for="email">Admin Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="admin@greenhaven.com" required autofocus>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-green btn-full btn-lg" style="margin-top: 16px;">Secure Login</button>
    </form>
    
    <div style="text-align: center; margin-top: 24px;">
        <a href="<?= SITE_URL ?>/index.php" style="font-size: 0.85rem; color: var(--gray-500); text-decoration: underline;">← Return to Storefront</a>
    </div>
</div>

</body>
</html>
