<?php
// ============================================================
// GREEN HAVEN - Common Helper Functions
// ============================================================
require_once __DIR__ . '/db.php';

function formatPrice($amount) {
    return CURRENCY . number_format($amount, 2);
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function generateOrderNumber() {
    return 'GH-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));
}

function getCategories($activeOnly = true) {
    $db = getDB();
    $sql = "SELECT * FROM categories";
    if ($activeOnly) $sql .= " WHERE is_active = 1";
    $sql .= " ORDER BY name ASC";
    return $db->query($sql)->fetchAll();
}

function getFeaturedProducts($limit = 6) {
    $db = getDB();
    $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 AND p.stock > 0 LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getSeasonalProducts($limit = 4) {
    $db = getDB();
    $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_seasonal = 1 AND p.stock > 0 LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getProduct($id_or_slug, $bySlug = false) {
    $db = getDB();
    $col = $bySlug ? 'slug' : 'id';
    $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.$col = ?");
    $stmt->execute([$id_or_slug]);
    return $stmt->fetch();
}

function getProductVariants($product_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM product_variants WHERE product_id = ? ORDER BY variant_type, extra_price ASC");
    $stmt->execute([$product_id]);
    $variants = $stmt->fetchAll();
    $grouped = [];
    foreach ($variants as $v) {
        $grouped[$v['variant_type']][] = $v;
    }
    return $grouped;
}

function getProductReviews($product_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? AND r.is_approved = 1 ORDER BY r.created_at DESC");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll();
}

function getCartItems($user_id) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT c.*, p.name, p.price, p.sale_price, p.image, p.stock,
               COALESCE(p.sale_price, p.price) as effective_price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function getCartTotal($user_id) {
    $items = getCartItems($user_id);
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += $item['effective_price'] * $item['quantity'];
    }
    $delivery = ($subtotal >= FREE_SHIPPING_ABOVE) ? 0 : DELIVERY_FEE;
    return [
        'items'    => $items,
        'subtotal' => $subtotal,
        'delivery' => $delivery,
        'total'    => $subtotal + $delivery,
        'count'    => count($items),
    ];
}

function isInWishlist($user_id, $product_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    return $stmt->fetch() !== false;
}

function getWishlistItems($user_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT w.*, p.name, p.price, p.sale_price, p.image, p.stock, p.slug FROM wishlists w JOIN products p ON w.product_id = p.id WHERE w.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function applyPromoCode($code, $subtotal) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM promotions WHERE code = ? AND is_active = 1 AND (expires_at IS NULL OR expires_at >= CURDATE()) AND (max_uses IS NULL OR used_count < max_uses)");
    $stmt->execute([strtoupper($code)]);
    $promo = $stmt->fetch();
    if (!$promo) return ['success' => false, 'message' => 'Invalid or expired promo code.'];
    if ($subtotal < $promo['min_order_value']) {
        return ['success' => false, 'message' => 'Minimum order value of ' . formatPrice($promo['min_order_value']) . ' required.'];
    }
    $discount = $promo['discount_type'] === 'percentage'
        ? ($subtotal * $promo['discount_value'] / 100)
        : $promo['discount_value'];
    return ['success' => true, 'discount' => round($discount, 2), 'promo' => $promo];
}

function getStarRating($rating, $max = 5) {
    $html = '<div class="star-rating">';
    for ($i = 1; $i <= $max; $i++) {
        if ($i <= floor($rating)) {
            $html .= '<span class="star full">★</span>';
        } elseif ($i - $rating < 1 && $i - $rating > 0) {
            $html .= '<span class="star half">★</span>';
        } else {
            $html .= '<span class="star empty">☆</span>';
        }
    }
    $html .= '</div>';
    return $html;
}

function getPlaceholderImage($name = 'Plant') {
    // Returns a colored SVG placeholder
    return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='400' viewBox='0 0 400 400'%3E%3Crect width='400' height='400' fill='%23013220'/%3E%3Ctext x='200' y='200' font-family='Arial' font-size='80' text-anchor='middle' dy='.3em' fill='%23D4AF37'%3E🌿%3C/text%3E%3Ctext x='200' y='280' font-family='Arial' font-size='20' text-anchor='middle' fill='%23F5F5DC'%3E" . urlencode($name) . "%3C/text%3E%3C/svg%3E";
}

function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->d == 0) {
        if ($diff->h == 0) return $diff->i . ' minutes ago';
        return $diff->h . ' hours ago';
    }
    if ($diff->d < 7) return $diff->d . ' days ago';
    if ($diff->m < 1) return ceil($diff->d / 7) . ' weeks ago';
    if ($diff->y < 1) return $diff->m . ' months ago';
    return $diff->y . ' years ago';
}

function getLowStockProducts() {
    $db = getDB();
    return $db->query("SELECT * FROM products WHERE stock <= " . LOW_STOCK_THRESHOLD . " AND stock > 0 ORDER BY stock ASC")->fetchAll();
}

function getOrderStatusBadge($status) {
    $badges = [
        'pending'          => ['class' => 'badge-warning',  'label' => '⏳ Pending'],
        'confirmed'        => ['class' => 'badge-info',     'label' => '✅ Confirmed'],
        'processing'       => ['class' => 'badge-primary',  'label' => '⚙️ Processing'],
        'shipped'          => ['class' => 'badge-purple',   'label' => '📦 Shipped'],
        'out_for_delivery' => ['class' => 'badge-orange',   'label' => '🚗 Out for Delivery'],
        'delivered'        => ['class' => 'badge-success',  'label' => '✅ Delivered'],
        'cancelled'        => ['class' => 'badge-danger',   'label' => '❌ Cancelled'],
    ];
    $b = $badges[$status] ?? ['class' => 'badge-secondary', 'label' => ucfirst($status)];
    return "<span class='badge {$b['class']}'>{$b['label']}</span>";
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function flashMessage($type, $message) {
    startSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage() {
    startSession();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
