<?php
// ============================================================
// GREEN HAVEN - Site Header
// ============================================================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
startSession();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$cartCount   = getCartCount();
$isLoggedIn  = isLoggedIn();
$flash       = getFlashMessage();

$metaTitle       = $metaTitle ?? SITE_NAME . ' – Bring Nature Home';
$metaDescription = $metaDescription ?? 'Shop premium indoor & outdoor plants, succulents, and accessories. Expert plant care guides & fast delivery across India.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= sanitize($metaDescription) ?>">
    <meta name="theme-color" content="#013220">
    <title><?= sanitize($metaTitle) ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/components.css">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌿</text></svg>">
</head>
<body class="page-<?= $currentPage ?>">

<!-- Flash Message -->
<?php if ($flash): ?>
<div class="flash-message flash-<?= $flash['type'] ?>" id="flash-msg">
    <span><?= sanitize($flash['message']) ?></span>
    <button onclick="this.parentElement.remove()" class="flash-close">×</button>
</div>
<?php endif; ?>

<!-- Navigation -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <!-- Logo -->
        <a href="<?= SITE_URL ?>/index.php" class="nav-logo" id="nav-logo">
            <span class="logo-icon">🌿</span>
            <span class="logo-text">Green<span class="logo-accent">Haven</span></span>
        </a>

        <!-- Main Nav Links -->
        <ul class="nav-links" id="nav-links">
            <li><a href="<?= SITE_URL ?>/index.php" class="nav-link <?= $currentPage==='index'?'active':'' ?>">Home</a></li>
            <li><a href="<?= SITE_URL ?>/shop.php" class="nav-link <?= $currentPage==='shop'?'active':'' ?>">Shop</a></li>
            <li class="dropdown">
                <a href="#" class="nav-link dropdown-trigger">Categories <span class="arrow">▾</span></a>
                <div class="dropdown-menu">
                    <?php foreach(getCategories() as $cat): ?>
                    <a href="<?= SITE_URL ?>/shop.php?category=<?= $cat['slug'] ?>" class="dropdown-item">
                        <span class="cat-icon"><?= $cat['icon'] ?></span> <?= sanitize($cat['name']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </li>
            <li><a href="<?= SITE_URL ?>/tips.php" class="nav-link <?= $currentPage==='tips'?'active':'' ?>">Plant Care</a></li>
            <li><a href="<?= SITE_URL ?>/subscription.php" class="nav-link <?= $currentPage==='subscription'?'active':'' ?>">Subscribe</a></li>
            <li><a href="<?= SITE_URL ?>/contact.php" class="nav-link <?= $currentPage==='contact'?'active':'' ?>">Contact</a></li>
        </ul>

        <!-- Nav Actions -->
        <div class="nav-actions">
            <!-- Search -->
            <button class="nav-icon-btn" id="search-toggle" aria-label="Search" title="Search plants">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </button>

            <!-- Wishlist -->
            <?php if ($isLoggedIn): ?>
            <a href="<?= SITE_URL ?>/wishlist.php" class="nav-icon-btn" aria-label="Wishlist" title="Your wishlist">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </a>
            <?php endif; ?>

            <!-- Cart -->
            <a href="<?= SITE_URL ?>/cart.php" class="nav-icon-btn cart-btn" aria-label="Shopping Cart" title="View cart">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <?php if ($cartCount > 0): ?>
                <span class="cart-badge" id="cart-count"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>

            <!-- Auth -->
            <?php if ($isLoggedIn): ?>
            <div class="dropdown">
                <button class="nav-icon-btn dropdown-trigger" aria-label="Account">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </button>
                <div class="dropdown-menu dropdown-right">
                    <div class="dropdown-header">Hello, <?= sanitize($_SESSION['user_name']) ?> 👋</div>
                    <a href="<?= SITE_URL ?>/auth/profile.php" class="dropdown-item">My Profile</a>
                    <a href="<?= SITE_URL ?>/tracking.php" class="dropdown-item">My Orders</a>
                    <a href="<?= SITE_URL ?>/wishlist.php" class="dropdown-item">Wishlist</a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= SITE_URL ?>/auth/logout.php" class="dropdown-item text-danger">Sign Out</a>
                </div>
            </div>
            <?php else: ?>
            <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-outline-sm" id="login-btn">Sign In</a>
            <?php endif; ?>

            <!-- Mobile Menu Toggle -->
            <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar" id="search-bar">
        <div class="search-container">
            <input type="text" id="search-input" placeholder="Search for plants, accessories..." autocomplete="off">
            <button id="search-btn" class="btn btn-gold">Search</button>
            <button id="search-close" class="search-close-btn">×</button>
        </div>
        <div class="search-suggestions" id="search-suggestions"></div>
    </div>
</nav>
