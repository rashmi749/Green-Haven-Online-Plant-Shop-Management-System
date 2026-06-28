<?php
// ============================================================
// GREEN HAVEN - Shopping Cart
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin('/cart.php');
$user_id = $_SESSION['user_id'];
$cartData = getCartTotal($user_id);
$items = $cartData['items'];

$metaTitle = 'Shopping Cart – ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<div class="page-header" style="padding-top: calc(var(--navbar-height) + 40px); padding-bottom: 40px;">
    <h1>Your Cart</h1>
</div>

<div class="cart-layout">
    <?php if(count($items) > 0): ?>
        
        <!-- Cart Items Table -->
        <div>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th colspan="2">Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td style="width: 100px;">
                            <div class="cart-product-image">🌿</div>
                        </td>
                        <td>
                            <div class="cart-product-info">
                                <div class="name"><?= sanitize($item['name']) ?></div>
                                <div class="variants">
                                    <?php 
                                    $vars = [];
                                    if($item['pot_variant']) $vars[] = $item['pot_variant'];
                                    if($item['size_variant']) $vars[] = $item['size_variant'];
                                    if($item['soil_variant']) $vars[] = $item['soil_variant'];
                                    echo implode(' | ', $vars);
                                    ?>
                                </div>
                            </div>
                        </td>
                        <td><?= formatPrice($item['effective_price']) ?></td>
                        <td>
                            <div class="qty-control" style="width: 100px; height: 32px;">
                                <button type="button" class="qty-btn" style="height:32px; width:30px;" onclick="updateCartQty(<?= $item['id'] ?>, -1)">-</button>
                                <input type="number" class="qty-input" style="height:32px; padding:0; width:40px;" value="<?= $item['quantity'] ?>" readonly>
                                <button type="button" class="qty-btn" style="height:32px; width:30px;" onclick="updateCartQty(<?= $item['id'] ?>, 1)">+</button>
                            </div>
                        </td>
                        <td style="font-weight: 600; color: var(--green-deep);"><?= formatPrice($item['effective_price'] * $item['quantity']) ?></td>
                        <td>
                            <button class="cart-remove-btn" aria-label="Remove item" onclick="removeCartItem(<?= $item['id'] ?>)">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 24px;">
                <a href="<?= SITE_URL ?>/shop.php" style="color: var(--green-deep); font-weight: 600; text-decoration: underline;">← Continue Shopping</a>
            </div>
        </div>
        
        <!-- Order Summary -->
        <aside class="order-summary">
            <h3>Order Summary</h3>
            
            <div class="summary-line">
                <span class="label">Subtotal</span>
                <span class="value"><?= formatPrice($cartData['subtotal']) ?></span>
            </div>
            
            <div class="summary-line">
                <span class="label">Delivery</span>
                <span class="value"><?= $cartData['delivery'] == 0 ? '<span style="color:#10b981;">Free</span>' : formatPrice($cartData['delivery']) ?></span>
            </div>
            
            <?php if($cartData['delivery'] > 0): ?>
                <?php $amountNeeded = FREE_SHIPPING_ABOVE - $cartData['subtotal']; ?>
                <div class="free-shipping-notice">
                    Add <?= formatPrice($amountNeeded) ?> more to get FREE shipping!
                </div>
            <?php else: ?>
                <div class="free-shipping-notice">
                    🎉 You've unlocked FREE shipping!
                </div>
            <?php endif; ?>
            
            <div class="promo-input-group">
                <input type="text" placeholder="Promo Code" id="promoCode">
                <button class="btn btn-outline" style="padding: 10px 16px;" onclick="applyPromo()">Apply</button>
            </div>
            
            <div class="summary-total">
                <span class="label">Total</span>
                <span class="value"><?= formatPrice($cartData['total']) ?></span>
            </div>
            
            <a href="<?= SITE_URL ?>/checkout.php" class="btn btn-gold btn-full btn-lg" style="margin-top: 24px;">Proceed to Checkout</a>
            
            <div style="margin-top: 24px; display: flex; gap: 8px; justify-content: center;">
                <span title="Secure Checkout">🔒</span>
                <span title="Quality Guarantee">✨</span>
                <span title="Support 24/7">💬</span>
            </div>
        </aside>
        
    <?php else: ?>
        
        <!-- Empty Cart -->
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--gray-50); border-radius: var(--radius-lg);">
            <div style="font-size: 4rem; margin-bottom: 24px;">🛒</div>
            <h2 style="color: var(--green-deep); margin-bottom: 12px;">Your cart is empty</h2>
            <p style="color: var(--gray-500); margin-bottom: 32px;">Looks like you haven't added any plants yet.</p>
            <a href="<?= SITE_URL ?>/shop.php" class="btn btn-gold btn-lg">Start Shopping</a>
        </div>
        
    <?php endif; ?>
</div>

<script>
// Mock functions for cart actions
function updateCartQty(id, change) {
    showFlash('info', 'Updating cart...');
    setTimeout(() => window.location.reload(), 500);
}

function removeCartItem(id) {
    if(confirm('Remove this item from your cart?')) {
        showFlash('info', 'Item removed.');
        setTimeout(() => window.location.reload(), 500);
    }
}

function applyPromo() {
    const code = document.getElementById('promoCode').value.trim();
    if(code === '') return;
    
    // Mock promo apply
    showFlash('info', 'Applying promo code...');
    setTimeout(() => {
        if(code.toUpperCase() === 'WELCOME10') {
            showFlash('success', 'Promo code applied!');
            // Reload to show discount
        } else {
            showFlash('error', 'Invalid promo code');
        }
    }, 800);
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
