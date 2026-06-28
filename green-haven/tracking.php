<?php
// ============================================================
// GREEN HAVEN - Order Tracking
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin('/tracking.php');
$user_id = $_SESSION['user_id'];
$db = getDB();

$order_id = $_GET['order_id'] ?? null;

if ($order_id) {
    // Show specific order tracking
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        flashMessage('error', 'Order not found.');
        redirect(SITE_URL . '/tracking.php');
    }
    
    $stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$order['id']]);
    $items = $stmt->fetchAll();
    
    $metaTitle = 'Track Order ' . $order['order_number'] . ' – ' . SITE_NAME;
    include __DIR__ . '/includes/header.php';
    ?>
    
    <div class="page-header" style="padding-top: calc(var(--navbar-height) + 40px); padding-bottom: 40px;">
        <h1>Track Your Order</h1>
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/tracking.php">My Orders</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current"><?= $order['order_number'] ?></span>
        </div>
    </div>
    
    <div class="section" style="max-width: 800px; margin: 0 auto;">
        
        <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--gray-100); padding: 32px; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; border-bottom: 1px solid var(--gray-100); padding-bottom: 20px;">
                <div>
                    <div style="font-weight: 700; color: var(--green-deep); font-size: 1.2rem;"><?= $order['order_number'] ?></div>
                    <div style="font-size: 0.85rem; color: var(--gray-500);">Placed on <?= date('M d, Y', strtotime($order['placed_at'])) ?></div>
                </div>
                <div>
                    <?= getOrderStatusBadge($order['status']) ?>
                </div>
            </div>
            
            <?php
            // Determine status progression
            $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered'];
            $currentIndex = array_search($order['status'], $statuses);
            if ($currentIndex === false && $order['status'] != 'cancelled') $currentIndex = 0;
            ?>
            
            <?php if ($order['status'] == 'cancelled'): ?>
                <div class="alert alert-error">This order was cancelled.</div>
            <?php else: ?>
                <div class="tracking-timeline">
                    <!-- Step 1: Placed -->
                    <div class="tracking-step <?= $currentIndex >= 0 ? 'done' : '' ?> <?= $currentIndex == 0 ? 'current' : '' ?>">
                        <div class="tracking-step-dot">📋</div>
                        <div class="tracking-step-title">Order Placed</div>
                        <div class="tracking-step-time"><?= date('M d, Y g:i A', strtotime($order['placed_at'])) ?></div>
                        <div class="tracking-step-desc">We have received your order.</div>
                    </div>
                    
                    <!-- Step 2: Confirmed -->
                    <div class="tracking-step <?= $currentIndex >= 1 ? 'done' : '' ?> <?= $currentIndex == 1 ? 'current' : '' ?>">
                        <div class="tracking-step-dot">✅</div>
                        <div class="tracking-step-title">Order Confirmed</div>
                        <?= $currentIndex >= 1 ? '<div class="tracking-step-desc">Payment verified and order confirmed.</div>' : '' ?>
                    </div>
                    
                    <!-- Step 3: Processing -->
                    <div class="tracking-step <?= $currentIndex >= 2 ? 'done' : '' ?> <?= $currentIndex == 2 ? 'current' : '' ?>">
                        <div class="tracking-step-dot">🪴</div>
                        <div class="tracking-step-title">Preparing Order</div>
                        <?= $currentIndex >= 2 ? '<div class="tracking-step-desc">Our experts are handpicking and packing your plants.</div>' : '' ?>
                    </div>
                    
                    <!-- Step 4: Shipped -->
                    <div class="tracking-step <?= $currentIndex >= 3 ? 'done' : '' ?> <?= $currentIndex == 3 ? 'current' : '' ?>">
                        <div class="tracking-step-dot">📦</div>
                        <div class="tracking-step-title">Order Shipped</div>
                        <?= $currentIndex >= 3 ? '<div class="tracking-step-desc">Handed over to our delivery partner.</div>' : '' ?>
                    </div>
                    
                    <!-- Step 5: Out for delivery -->
                    <div class="tracking-step <?= $currentIndex >= 4 ? 'done' : '' ?> <?= $currentIndex == 4 ? 'current' : '' ?>">
                        <div class="tracking-step-dot">🚚</div>
                        <div class="tracking-step-title">Out for Delivery</div>
                        <?= $currentIndex >= 4 ? '<div class="tracking-step-desc">Order is out for delivery today.</div>' : '' ?>
                    </div>
                    
                    <!-- Step 6: Delivered -->
                    <div class="tracking-step <?= $currentIndex >= 5 ? 'done' : '' ?> <?= $currentIndex == 5 ? 'current' : '' ?>">
                        <div class="tracking-step-dot">🎉</div>
                        <div class="tracking-step-title">Delivered</div>
                        <?= $currentIndex >= 5 ? '<div class="tracking-step-desc">Package delivered successfully!</div>' : '' ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <!-- Order Details -->
            <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--gray-100); padding: 24px;">
                <h3 style="font-size: 1rem; color: var(--green-deep); margin-bottom: 16px;">Order Items</h3>
                <div style="margin-bottom: 16px; border-bottom: 1px solid var(--gray-100); padding-bottom: 16px;">
                    <?php foreach($items as $item): ?>
                    <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 40px; height: 40px; background: var(--green-pale); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">🌿</div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 0.9rem; color: var(--gray-800);"><?= sanitize($item['product_name']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--gray-500);">Qty: <?= $item['quantity'] ?> × <?= formatPrice($item['unit_price']) ?></div>
                        </div>
                        <div style="font-weight: 600; font-size: 0.9rem; color: var(--green-deep);">
                            <?= formatPrice($item['quantity'] * $item['unit_price']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: var(--gray-600);">
                    <span>Subtotal</span><span><?= formatPrice($order['subtotal']) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: var(--gray-600);">
                    <span>Delivery</span><span><?= formatPrice($order['delivery_fee']) ?></span>
                </div>
                <?php if($order['discount'] > 0): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.9rem; color: #10b981;">
                    <span>Discount</span><span>-<?= formatPrice($order['discount']) ?></span>
                </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--gray-100); font-weight: 700; color: var(--green-deep);">
                    <span>Total Paid</span><span><?= formatPrice($order['total']) ?></span>
                </div>
            </div>
            
            <!-- Delivery Info -->
            <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--gray-100); padding: 24px;">
                <h3 style="font-size: 1rem; color: var(--green-deep); margin-bottom: 16px;">Delivery Details</h3>
                
                <div style="margin-bottom: 20px;">
                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--gray-800); margin-bottom: 4px;"><?= sanitize($order['delivery_name']) ?></div>
                    <div style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.6;">
                        <?= sanitize($order['delivery_address']) ?><br>
                        <?= sanitize($order['delivery_city']) ?>, <?= sanitize($order['delivery_state']) ?> - <?= sanitize($order['delivery_pincode']) ?><br>
                        Phone: <?= sanitize($order['delivery_phone']) ?>
                    </div>
                </div>
                
                <h3 style="font-size: 1rem; color: var(--green-deep); margin-bottom: 12px; padding-top: 16px; border-top: 1px solid var(--gray-100);">Payment Method</h3>
                <div style="font-size: 0.85rem; color: var(--gray-600); text-transform: uppercase;">
                    <?= sanitize($order['payment_method']) ?> (<?= sanitize($order['payment_status']) ?>)
                </div>
            </div>
        </div>
        
    </div>
    
    <?php
    include __DIR__ . '/includes/footer.php';
} else {
    // Show list of all orders
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY placed_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
    
    $metaTitle = 'My Orders – ' . SITE_NAME;
    include __DIR__ . '/includes/header.php';
    ?>
    
    <div class="page-header" style="padding-top: calc(var(--navbar-height) + 40px); padding-bottom: 40px;">
        <h1>My Orders</h1>
    </div>
    
    <div class="section" style="max-width: 900px; margin: 0 auto;">
        <?php if(count($orders) > 0): ?>
            
            <?php foreach($orders as $order): ?>
            <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--gray-100); margin-bottom: 20px; overflow: hidden; box-shadow: var(--shadow-sm); transition: transform var(--transition-fast);">
                <div style="background: var(--gray-50); padding: 16px 24px; border-bottom: 1px solid var(--gray-100); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div style="display: flex; gap: 32px; flex-wrap: wrap;">
                        <div>
                            <div style="font-size: 0.75rem; color: var(--gray-500); text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Order Placed</div>
                            <div style="font-size: 0.9rem; color: var(--gray-800); font-weight: 500;"><?= date('M d, Y', strtotime($order['placed_at'])) ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--gray-500); text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Total</div>
                            <div style="font-size: 0.9rem; color: var(--green-deep); font-weight: 700;"><?= formatPrice($order['total']) ?></div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.75rem; color: var(--gray-500); text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Order #</div>
                        <div style="font-size: 0.9rem; color: var(--gray-800); font-weight: 500;"><?= $order['order_number'] ?></div>
                    </div>
                </div>
                
                <div style="padding: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 24px;">
                    <div>
                        <div style="margin-bottom: 12px;"><?= getOrderStatusBadge($order['status']) ?></div>
                        <?php if($order['status'] != 'delivered' && $order['status'] != 'cancelled'): ?>
                            <div style="font-size: 0.9rem; color: var(--gray-600);">Expected delivery: <strong style="color:var(--gray-800);"><?= date('M d, Y', strtotime('+4 days')) ?></strong></div>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <a href="<?= SITE_URL ?>/tracking.php?order_id=<?= $order['order_number'] ?>" class="btn btn-outline" style="padding: 8px 24px;">Track Order</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
        <?php else: ?>
            
            <div style="text-align: center; padding: 60px 20px; background: var(--gray-50); border-radius: var(--radius-lg);">
                <div style="font-size: 4rem; margin-bottom: 24px;">📦</div>
                <h2 style="color: var(--green-deep); margin-bottom: 12px;">No orders yet</h2>
                <p style="color: var(--gray-500); margin-bottom: 32px;">You haven't placed any orders with us yet.</p>
                <a href="<?= SITE_URL ?>/shop.php" class="btn btn-gold btn-lg">Start Shopping</a>
            </div>
            
        <?php endif; ?>
    </div>
    
    <?php
    include __DIR__ . '/includes/footer.php';
}
?>
