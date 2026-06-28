<?php
// ============================================================
// GREEN HAVEN - Checkout
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin('/checkout.php');
$user_id = $_SESSION['user_id'];
$user = getCurrentUser();
$cartData = getCartTotal($user_id);
$items = $cartData['items'];

if (count($items) === 0) {
    redirect(SITE_URL . '/cart.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process Order (Mock implementation)
    // Normally you'd validate the form, create an order in DB, clear cart, then redirect
    // We will just redirect to a mock order confirmation
    $orderId = rand(1000, 9999);
    redirect(SITE_URL . '/order-confirmation.php?order_id=' . $orderId);
}

$metaTitle = 'Checkout – ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<div class="page-header" style="padding-top: calc(var(--navbar-height) + 40px); padding-bottom: 40px;">
    <h1>Secure Checkout</h1>
</div>

<div class="checkout-layout">
    
    <div class="checkout-form-area">
        
        <!-- Steps Indicator -->
        <div class="checkout-steps">
            <div class="step active">
                <div class="step-circle">1</div>
                <div class="step-label">Delivery</div>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-circle">2</div>
                <div class="step-label">Payment</div>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-circle">3</div>
                <div class="step-label">Confirm</div>
            </div>
        </div>
        
        <form method="POST" action="" id="checkoutForm">
            <!-- Delivery Details -->
            <section class="checkout-section">
                <h3><span style="font-size: 1.2rem;">📍</span> Delivery Address</h3>
                
                <div class="form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?= sanitize($user['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" value="<?= sanitize($user['phone'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Street Address</label>
                    <input type="text" name="address" class="form-control" value="<?= sanitize($user['address'] ?? '') ?>" required>
                </div>
                
                <div class="form-grid-3" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?= sanitize($user['city'] ?? '') ?>" required>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="<?= sanitize($user['state'] ?? '') ?>" required>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label">PIN Code</label>
                        <input type="text" name="pincode" class="form-control" value="<?= sanitize($user['pincode'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">Delivery Notes (Optional)</label>
                    <textarea name="notes" class="form-control" style="min-height: 80px;" placeholder="E.g., Leave at front door..."></textarea>
                </div>
            </section>
            
            <!-- Payment Method -->
            <section class="checkout-section">
                <h3><span style="font-size: 1.2rem;">💳</span> Payment Method</h3>
                
                <div class="payment-methods">
                    <label class="payment-option selected" onclick="selectPayment('card', this)">
                        <input type="radio" name="payment" value="card" class="payment-radio" checked style="display:none;">
                        <div class="payment-icon">💳</div>
                        <div>
                            <div class="payment-label">Credit / Debit Card</div>
                            <div class="payment-desc">Visa, Mastercard, RuPay</div>
                        </div>
                    </label>
                    
                    <label class="payment-option" onclick="selectPayment('upi', this)">
                        <input type="radio" name="payment" value="upi" class="payment-radio" style="display:none;">
                        <div class="payment-icon">📱</div>
                        <div>
                            <div class="payment-label">UPI</div>
                            <div class="payment-desc">Google Pay, PhonePe, Paytm</div>
                        </div>
                    </label>
                    
                    <label class="payment-option" onclick="selectPayment('cod', this)">
                        <input type="radio" name="payment" value="cod" class="payment-radio" style="display:none;">
                        <div class="payment-icon">💵</div>
                        <div>
                            <div class="payment-label">Cash on Delivery</div>
                            <div class="payment-desc">Pay when you receive your order</div>
                        </div>
                    </label>
                </div>
                
                <!-- Card Details Form (Simulated) -->
                <div class="payment-details" id="cardDetails">
                    <div class="form-group">
                        <label class="form-label">Card Number</label>
                        <input type="text" class="form-control card-number-input" placeholder="0000 0000 0000 0000" maxlength="19">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label">Expiry Date</label>
                            <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label">CVV</label>
                            <input type="text" class="form-control" placeholder="123" maxlength="3">
                        </div>
                    </div>
                </div>
                
                <div class="payment-details" id="upiDetails" style="display: none;">
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label">UPI ID</label>
                        <input type="text" class="form-control" placeholder="username@upi">
                    </div>
                </div>
            </section>
            
            <button type="submit" class="btn btn-gold btn-full btn-lg" id="placeOrderBtn">
                Place Order • <?= formatPrice($cartData['total']) ?>
            </button>
            
            <div style="text-align: center; font-size: 0.8rem; color: var(--gray-500); margin-top: 16px;">
                By placing this order, you agree to our Terms & Conditions.
            </div>
            
        </form>
    </div>
    
    <!-- Order Summary Sidebar -->
    <aside class="order-summary" style="position: sticky; top: calc(var(--navbar-height) + 24px);">
        <h3>Order Summary</h3>
        
        <div style="margin-bottom: 20px; border-bottom: 1px solid var(--gray-100); padding-bottom: 20px;">
            <?php foreach($items as $item): ?>
            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="width: 50px; height: 50px; background: var(--green-pale); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">🌿</div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--green-deep);"><?= sanitize($item['name']) ?></div>
                    <div style="font-size: 0.8rem; color: var(--gray-500);">Qty: <?= $item['quantity'] ?></div>
                </div>
                <div style="font-weight: 600; font-size: 0.9rem; color: var(--gray-800);">
                    <?= formatPrice($item['effective_price'] * $item['quantity']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="summary-line">
            <span class="label">Subtotal</span>
            <span class="value"><?= formatPrice($cartData['subtotal']) ?></span>
        </div>
        
        <div class="summary-line">
            <span class="label">Delivery</span>
            <span class="value"><?= $cartData['delivery'] == 0 ? '<span style="color:#10b981;">Free</span>' : formatPrice($cartData['delivery']) ?></span>
        </div>
        
        <div class="summary-total">
            <span class="label">Total to Pay</span>
            <span class="value"><?= formatPrice($cartData['total']) ?></span>
        </div>
    </aside>
    
</div>

<script>
function selectPayment(type, element) {
    // Update active class
    document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');
    
    // Check the radio input
    element.querySelector('input').checked = true;
    
    // Show/hide detail forms
    document.getElementById('cardDetails').style.display = (type === 'card') ? 'block' : 'none';
    document.getElementById('upiDetails').style.display = (type === 'upi') ? 'block' : 'none';
}

document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('placeOrderBtn');
    btn.innerHTML = '<span class="spinner" style="width:20px;height:20px;margin:0;border-top-color:var(--green-deep);"></span> Processing...';
    btn.style.opacity = '0.8';
    btn.disabled = true;
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
