<?php
// ============================================================
// GREEN HAVEN - Order Confirmation
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$orderId = $_GET['order_id'] ?? '';
// In a real app, we would fetch order details from DB
// For prototype, we will just use mock data

$metaTitle = 'Order Confirmed – ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<div class="section" style="max-width: 600px; margin: 0 auto; text-align: center; padding-top: 100px;">
    
    <div style="font-size: 5rem; margin-bottom: 24px; animation: popIn 0.5s ease;">✅</div>
    
    <h1 style="font-family: var(--font-serif); color: var(--green-deep); margin-bottom: 16px;">Order Placed Successfully!</h1>
    
    <p style="color: var(--gray-600); margin-bottom: 32px; font-size: 1.1rem;">
        Thank you for your purchase. Your plant babies are getting ready for their journey to your home.
    </p>
    
    <div style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 32px; margin-bottom: 40px; text-align: left;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--gray-200);">
            <div>
                <div style="font-size: 0.8rem; color: var(--gray-500); text-transform: uppercase; font-weight: 600;">Order Number</div>
                <div style="font-weight: 700; color: var(--green-deep); font-size: 1.1rem;">GH-<?= $orderId ?></div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.8rem; color: var(--gray-500); text-transform: uppercase; font-weight: 600;">Estimated Delivery</div>
                <div style="font-weight: 700; color: var(--green-deep); font-size: 1.1rem;"><?= date('M d, Y', strtotime('+4 days')) ?></div>
            </div>
        </div>
        
        <h4 style="color: var(--gray-700); margin-bottom: 16px;">What's Next?</h4>
        
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <div style="width: 24px; height: 24px; background: var(--green-pale); color: var(--green-deep); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; margin-top: 2px;">1</div>
                <div>
                    <div style="font-weight: 600; color: var(--gray-800); font-size: 0.9rem;">Confirmation Email</div>
                    <div style="font-size: 0.85rem; color: var(--gray-500);">We've sent a confirmation email with order details.</div>
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <div style="width: 24px; height: 24px; background: var(--green-pale); color: var(--green-deep); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; margin-top: 2px;">2</div>
                <div>
                    <div style="font-weight: 600; color: var(--gray-800); font-size: 0.9rem;">Careful Preparation</div>
                    <div style="font-size: 0.85rem; color: var(--gray-500);">Our experts are handpicking and securely packing your plants.</div>
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <div style="width: 24px; height: 24px; background: var(--green-pale); color: var(--green-deep); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; margin-top: 2px;">3</div>
                <div>
                    <div style="font-weight: 600; color: var(--gray-800); font-size: 0.9rem;">Out for Delivery</div>
                    <div style="font-size: 0.85rem; color: var(--gray-500);">You'll receive a tracking link once your order is dispatched.</div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="display: flex; gap: 16px; justify-content: center;">
        <a href="<?= SITE_URL ?>/tracking.php" class="btn btn-gold btn-lg">Track Order</a>
        <a href="<?= SITE_URL ?>/shop.php" class="btn btn-outline btn-lg">Continue Shopping</a>
    </div>
    
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
