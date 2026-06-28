<?php
// ============================================================
// GREEN HAVEN - Product Detail Page
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    redirect(SITE_URL . '/shop.php');
}

$product = getProduct($slug, true);
if (!$product) {
    // 404 Not Found
    header("HTTP/1.0 404 Not Found");
    $metaTitle = 'Product Not Found – ' . SITE_NAME;
    include __DIR__ . '/includes/header.php';
    echo '<div class="section text-center" style="padding:100px 20px;"><h1 style="color:var(--green-deep); margin-bottom:20px;">Product Not Found</h1><a href="'.SITE_URL.'/shop.php" class="btn btn-gold">Return to Shop</a></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Fetch related data
$variants = getProductVariants($product['id']);
$reviews = getProductReviews($product['id']);

// Related products (same category)
$db = getDB();
$stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? LIMIT 4");
$stmt->execute([$product['category_id'], $product['id']]);
$relatedProducts = $stmt->fetchAll();

$metaTitle = sanitize($product['name']) . ' – ' . SITE_NAME;
$metaDescription = sanitize(substr($product['description'], 0, 160));
include __DIR__ . '/includes/header.php';
?>

<div class="product-detail">
    <!-- Left Column: Gallery -->
    <div class="product-gallery">
        <div class="gallery-main" id="galleryMain">
            <div class="product-badges" style="z-index: 10;">
                <?php if($product['sale_price']): ?><span class="badge badge-sale">Sale</span><?php endif; ?>
                <?php if($product['is_seasonal']): ?><span class="badge badge-new">Seasonal</span><?php endif; ?>
            </div>
            <!-- In a real app, use $product['image'], for now use placeholder -->
            <div class="gallery-placeholder">🌿</div>
        </div>
        
        <div class="gallery-thumbnails">
            <!-- Mock thumbnails -->
            <div class="gallery-thumb active">🌿</div>
            <div class="gallery-thumb">🪴</div>
            <div class="gallery-thumb">🪴</div>
            <div class="gallery-thumb">🪴</div>
        </div>
    </div>
    
    <!-- Right Column: Info -->
    <div class="product-info">
        <a href="<?= SITE_URL ?>/shop.php?category=<?= $product['category_slug'] ?>" class="product-category"><?= sanitize($product['category_name']) ?></a>
        
        <h1><?= sanitize($product['name']) ?></h1>
        
        <div class="product-meta">
            <div class="product-rating">
                <?= getStarRating($product['rating']) ?>
                <a href="#reviews" style="font-size: 0.85rem; color: var(--gray-500); text-decoration: underline; margin-left: 8px;">(<?= $product['review_count'] ?> reviews)</a>
            </div>
            
            <div class="stock-indicator">
                <?php if($product['stock'] > 10): ?>
                    <span class="stock-dot in"></span><span style="color:#10b981; font-weight:600;">In Stock</span>
                <?php elseif($product['stock'] > 0): ?>
                    <span class="stock-dot low"></span><span style="color:var(--orange); font-weight:600;">Only <?= $product['stock'] ?> left!</span>
                <?php else: ?>
                    <span class="stock-dot out"></span><span style="color:var(--red); font-weight:600;">Out of Stock</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="product-price-detail">
            <?php if($product['sale_price']): ?>
                <span class="price-detail-current" id="priceDisplay"><?= formatPrice($product['sale_price']) ?></span>
                <span class="price-detail-original"><?= formatPrice($product['price']) ?></span>
                <?php 
                $savings = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
                ?>
                <span class="price-detail-savings">Save <?= $savings ?>%</span>
            <?php else: ?>
                <span class="price-detail-current" id="priceDisplay"><?= formatPrice($product['price']) ?></span>
            <?php endif; ?>
        </div>
        
        <p class="product-short-desc">
            <?= sanitize($product['short_description'] ?? $product['description']) ?>
        </p>
        
        <!-- Care Highlights Grid -->
        <div class="care-info-grid">
            <div class="care-info-item">
                <div class="care-info-icon">
                    <?php 
                    if($product['care_level'] == 'easy') echo '🌱';
                    elseif($product['care_level'] == 'moderate') echo '🪴';
                    else echo '🔬';
                    ?>
                </div>
                <div class="care-info-label">Care Level</div>
                <div class="care-info-value"><?= ucfirst($product['care_level']) ?></div>
            </div>
            <div class="care-info-item">
                <div class="care-info-icon">☀️</div>
                <div class="care-info-label">Light Needs</div>
                <div class="care-info-value"><?= ucfirst($product['light_requirement']) ?></div>
            </div>
            <div class="care-info-item">
                <div class="care-info-icon">💧</div>
                <div class="care-info-label">Watering</div>
                <div class="care-info-value"><?= sanitize($product['watering_frequency']) ?></div>
            </div>
        </div>
        
        <form id="addToCartForm" onsubmit="handleAddToCart(event)">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            
            <!-- Customizations / Variants -->
            <?php if(!empty($variants)): ?>
                <?php foreach(['pot' => 'Choose Pot', 'size' => 'Plant Size', 'soil' => 'Soil Mix', 'addon' => 'Add-ons'] as $type => $label): ?>
                    <?php if(isset($variants[$type])): ?>
                    <div class="customization-section">
                        <div class="custom-title"><?= $label ?></div>
                        <div class="custom-options">
                            <?php foreach($variants[$type] as $i => $v): ?>
                            <label class="custom-option <?= $i===0 && $type!='addon' ? 'selected' : '' ?>">
                                <input type="<?= $type=='addon' ? 'checkbox' : 'radio' ?>" 
                                       name="variant_<?= $type ?><?= $type=='addon'?'[]':'' ?>" 
                                       value="<?= sanitize($v['variant_name']) ?>" 
                                       data-price="<?= $v['extra_price'] ?>"
                                       <?= $i===0 && $type!='addon' ? 'checked' : '' ?>
                                       style="display: none;"
                                       onchange="updateSelection(this)">
                                <?= sanitize($v['variant_name']) ?>
                                <?php if($v['extra_price'] > 0): ?>
                                    <span class="extra-price">+<?= formatPrice($v['extra_price']) ?></span>
                                <?php endif; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="quantity-section">
                <div class="qty-label">Quantity</div>
                <div class="qty-control">
                    <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
                    <input type="number" id="qtyInput" name="quantity" class="qty-input" value="1" min="1" max="<?= $product['stock'] > 0 ? min(10, $product['stock']) : 1 ?>" readonly>
                    <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                </div>
            </div>
            
            <div class="product-actions">
                <button type="submit" class="btn btn-gold btn-lg btn-full" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                    <?= $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
                </button>
                <button type="button" class="btn btn-outline btn-lg" style="width: auto; padding: 0 20px;" onclick="toggleWishlist(<?= $product['id'] ?>, this)" aria-label="Add to Wishlist">
                    <span style="font-size: 1.4rem; line-height: 1;" class="<?= isInWishlist($_SESSION['user_id'] ?? 0, $product['id']) ? 'active' : '' ?>">♥</span>
                </button>
            </div>
        </form>
        
        <div style="font-size: 0.8rem; color: var(--gray-500); display: flex; gap: 16px; align-items: center; border-top: 1px solid var(--gray-100); padding-top: 20px; margin-top: 20px;">
            <span style="display:flex; align-items:center; gap:6px;">🚚 Delivery in 3-5 days</span>
            <span style="display:flex; align-items:center; gap:6px;">💳 Secure Payments</span>
        </div>
    </div>
</div>

<!-- Tabs Section -->
<div class="section" style="padding-top: 0;" id="reviews">
    <div style="border-bottom: 1px solid var(--gray-200); margin-bottom: 32px; display: flex; gap: 32px;">
        <button class="tab-btn active" onclick="switchTab('description', this)">Description</button>
        <button class="tab-btn" onclick="switchTab('care', this)">Care Guide</button>
        <button class="tab-btn" onclick="switchTab('reviews-tab', this)">Reviews (<?= $product['review_count'] ?>)</button>
    </div>
    
    <div id="tab-description" class="tab-content" style="display: block; max-width: 800px; color: var(--gray-700); line-height: 1.8;">
        <?= nl2br(sanitize($product['description'])) ?>
    </div>
    
    <div id="tab-care" class="tab-content" style="display: none; max-width: 800px;">
        <h3 style="color:var(--green-deep); margin-bottom:16px;">How to care for your <?= sanitize($product['name']) ?></h3>
        <p style="color:var(--gray-600); margin-bottom:24px;">Follow these simple instructions to keep your plant happy and thriving.</p>
        
        <div style="display: grid; gap: 20px;">
            <div style="background:var(--gray-50); padding:20px; border-radius:var(--radius-md);">
                <h4 style="color:var(--green-deep); margin-bottom:8px; display:flex; align-items:center; gap:8px;">☀️ Light Requirement</h4>
                <p style="font-size:0.9rem; color:var(--gray-700);">This plant prefers <strong><?= $product['light_requirement'] ?></strong> light. Avoid placing it in completely dark corners or harsh, direct afternoon sun (unless it's a cactus/succulent).</p>
            </div>
            <div style="background:var(--gray-50); padding:20px; border-radius:var(--radius-md);">
                <h4 style="color:var(--green-deep); margin-bottom:8px; display:flex; align-items:center; gap:8px;">💧 Watering Schedule</h4>
                <p style="font-size:0.9rem; color:var(--gray-700);">Water <strong><?= sanitize($product['watering_frequency']) ?></strong>. Always check the top 2 inches of soil; if it feels dry, it's time to water. Overwatering is a common mistake!</p>
            </div>
        </div>
    </div>
    
    <div id="tab-reviews-tab" class="tab-content" style="display: none;">
        <div class="review-summary">
            <div class="review-avg">
                <div class="review-avg-number"><?= $product['rating'] ?></div>
                <div class="review-avg-stars"><?= getStarRating($product['rating']) ?></div>
                <div class="review-avg-count">Based on <?= $product['review_count'] ?> reviews</div>
            </div>
            
            <div class="review-bars">
                <!-- Mock bars for visual effect -->
                <div class="review-bar-row"><div class="bar-label">5★</div><div class="bar-track"><div class="bar-fill" style="width: 70%;"></div></div><div class="bar-count">70%</div></div>
                <div class="review-bar-row"><div class="bar-label">4★</div><div class="bar-track"><div class="bar-fill" style="width: 20%;"></div></div><div class="bar-count">20%</div></div>
                <div class="review-bar-row"><div class="bar-label">3★</div><div class="bar-track"><div class="bar-fill" style="width: 5%;"></div></div><div class="bar-count">5%</div></div>
                <div class="review-bar-row"><div class="bar-label">2★</div><div class="bar-track"><div class="bar-fill" style="width: 3%;"></div></div><div class="bar-count">3%</div></div>
                <div class="review-bar-row"><div class="bar-label">1★</div><div class="bar-track"><div class="bar-fill" style="width: 2%;"></div></div><div class="bar-count">2%</div></div>
            </div>
        </div>
        
        <div style="max-width: 800px;">
            <h3 style="color:var(--green-deep); margin-bottom:24px;">Customer Reviews</h3>
            
            <?php if(count($reviews) > 0): ?>
                <?php foreach($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar"><?= substr($review['user_name'], 0, 1) ?></div>
                        <div class="review-meta">
                            <div class="reviewer-name"><?= sanitize($review['user_name']) ?></div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="star-rating" style="font-size: 0.7rem;"><?= getStarRating($review['rating']) ?></span>
                                <span class="review-date"><?= date('M d, Y', strtotime($review['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="review-title"><?= sanitize($review['title']) ?></div>
                    <div class="review-body"><?= nl2br(sanitize($review['body'])) ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:var(--gray-500);">No reviews yet. Be the first to review this plant!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Related Products -->
<?php if(count($relatedProducts) > 0): ?>
<section class="section" style="background: var(--gray-50); padding: 60px 24px;">
    <h2 class="section-title text-center" style="text-align: center; margin-bottom: 40px;">You May Also Like</h2>
    <div class="products-grid">
        <?php foreach($relatedProducts as $relProduct): ?>
        <div class="product-card">
            <div class="product-card-image">
                <a href="<?= SITE_URL ?>/product.php?slug=<?= $relProduct['slug'] ?>">
                    <div class="product-card-image-placeholder">🌿</div>
                </a>
            </div>
            <div class="product-card-body">
                <h3 class="product-name">
                    <a href="<?= SITE_URL ?>/product.php?slug=<?= $relProduct['slug'] ?>"><?= sanitize($relProduct['name']) ?></a>
                </h3>
                <div class="product-price">
                    <span class="price-current"><?= formatPrice($relProduct['sale_price'] ?? $relProduct['price']) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<style>
/* Local styles for tabs */
.tab-btn {
    background: none;
    border: none;
    padding: 0 0 12px;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--gray-400);
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all var(--transition-fast);
}
.tab-btn:hover { color: var(--green-deep); }
.tab-btn.active { color: var(--green-deep); border-bottom-color: var(--gold); }
</style>

<script>
const basePrice = <?= $product['sale_price'] ?? $product['price'] ?>;

function updateSelection(radio) {
    // Visual update for radio buttons in the same group
    if (radio.type === 'radio') {
        const group = document.querySelectorAll(`input[name="${radio.name}"]`);
        group.forEach(input => {
            input.parentElement.classList.remove('selected');
        });
        radio.parentElement.classList.add('selected');
    } else {
        radio.parentElement.classList.toggle('selected');
    }
    
    // Calculate new price
    let extraPrice = 0;
    const selectedOptions = document.querySelectorAll('.custom-option input:checked');
    selectedOptions.forEach(opt => {
        extraPrice += parseFloat(opt.dataset.price || 0);
    });
    
    // Update display (simple format for JS)
    const newPrice = basePrice + extraPrice;
    document.getElementById('priceDisplay').innerText = '₹' + newPrice.toFixed(2);
}

function updateQty(change) {
    const input = document.getElementById('qtyInput');
    const max = parseInt(input.max);
    let newVal = parseInt(input.value) + change;
    
    if(newVal >= 1 && newVal <= max) {
        input.value = newVal;
    }
}

function handleAddToCart(e) {
    e.preventDefault();
    const qty = parseInt(document.getElementById('qtyInput').value);
    addToCart(<?= $product['id'] ?>, qty);
}

function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
    
    btn.classList.add('active');
    document.getElementById('tab-' + tabId).style.display = 'block';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
