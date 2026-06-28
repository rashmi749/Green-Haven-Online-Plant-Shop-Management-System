<?php
// ============================================================
// GREEN HAVEN - Wishlist
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin('/wishlist.php');
$user_id = $_SESSION['user_id'];
$db = getDB();

// Fetch wishlist items
$stmt = $db->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN wishlists w ON p.id = w.product_id 
    JOIN categories c ON p.category_id = c.id 
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$user_id]);
$products = $stmt->fetchAll();

$metaTitle = 'My Wishlist – ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<div class="page-header" style="padding-top: calc(var(--navbar-height) + 40px); padding-bottom: 40px;">
    <h1>My Wishlist</h1>
</div>

<div class="section">
    <?php if(count($products) > 0): ?>
        
        <div class="products-grid">
            <?php foreach($products as $product): ?>
            <div class="product-card" id="wishlist-item-<?= $product['id'] ?>">
                <div class="product-card-image">
                    <div class="product-badges">
                        <?php if($product['sale_price']): ?>
                            <span class="badge badge-sale">Sale</span>
                        <?php endif; ?>
                    </div>
                    
                    <button class="product-wishlist-btn active" 
                            onclick="removeFromWishlist(<?= $product['id'] ?>)" 
                            aria-label="Remove from wishlist">
                        ×
                    </button>
                    
                    <a href="<?= SITE_URL ?>/product.php?slug=<?= $product['slug'] ?>">
                        <div class="product-card-image-placeholder">🌿</div>
                    </a>
                </div>
                
                <div class="product-card-body">
                    <span class="product-category"><?= sanitize($product['category_name']) ?></span>
                    <h3 class="product-name">
                        <a href="<?= SITE_URL ?>/product.php?slug=<?= $product['slug'] ?>">
                            <?= sanitize($product['name']) ?>
                        </a>
                    </h3>
                    
                    <div class="product-footer" style="margin-top: 16px;">
                        <div class="product-price">
                            <?php if($product['sale_price']): ?>
                                <span class="price-current"><?= formatPrice($product['sale_price']) ?></span>
                                <span class="price-original"><?= formatPrice($product['price']) ?></span>
                            <?php else: ?>
                                <span class="price-current"><?= formatPrice($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button class="btn btn-gold btn-sm" onclick="moveToCart(<?= $product['id'] ?>)" style="padding: 6px 12px;">
                            Move to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
    <?php else: ?>
        
        <div style="text-align: center; padding: 60px 20px; background: var(--gray-50); border-radius: var(--radius-lg);">
            <div style="font-size: 4rem; margin-bottom: 24px;">💚</div>
            <h2 style="color: var(--green-deep); margin-bottom: 12px;">Your wishlist is empty</h2>
            <p style="color: var(--gray-500); margin-bottom: 32px;">Save your favorite plants here and decide later.</p>
            <a href="<?= SITE_URL ?>/shop.php" class="btn btn-gold btn-lg">Explore Plants</a>
        </div>
        
    <?php endif; ?>
</div>

<script>
function removeFromWishlist(id) {
    if(confirm('Remove this item from your wishlist?')) {
        // Mock AJAX call
        const card = document.getElementById('wishlist-item-' + id);
        if(card) {
            card.style.opacity = '0';
            setTimeout(() => {
                card.remove();
                showFlash('info', 'Item removed from wishlist');
                
                // If grid is empty, reload to show empty state
                if(document.querySelectorAll('.product-card').length === 0) {
                    window.location.reload();
                }
            }, 300);
        }
    }
}

function moveToCart(id) {
    addToCart(id);
    // After adding to cart, optionally remove from wishlist
    setTimeout(() => {
        const card = document.getElementById('wishlist-item-' + id);
        if(card) {
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 300);
        }
    }, 500);
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
