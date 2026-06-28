<?php
// ============================================================
// GREEN HAVEN - Homepage
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$metaTitle = SITE_NAME . ' – Premium Indoor & Outdoor Plants';
$metaDescription = 'Shop beautiful, healthy plants for your home and garden. Expert care tips, premium accessories, and fast delivery across India.';

include __DIR__ . '/includes/header.php';
?>

<!-- Promo Banner -->
<div class="promo-banner">
    🌿 FREE SHIPPING on orders above <?= formatPrice(FREE_SHIPPING_ABOVE) ?> | Use code WELCOME10 for 10% off
</div>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">
        <!-- Hero Content -->
        <div class="hero-content">
            <span class="hero-badge">✨ New Arrivals</span>
            <h1 class="hero-title">
                Bring Nature
                <span class="hero-title-accent">Home</span>
            </h1>
            <p class="hero-subtitle">
                Transform your living space with our carefully curated collection of premium indoor and outdoor plants. 
                Expertly grown and delivered straight to your door.
            </p>
            <div class="hero-actions">
                <a href="<?= SITE_URL ?>/shop.php" class="btn btn-gold btn-lg">Shop Now</a>
                <a href="<?= SITE_URL ?>/tips.php" class="btn btn-outline-sm btn-lg" style="color: white; border-color: white;">Plant Care Guide</a>
            </div>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Plant Varieties</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4.9★</div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>
        </div>

        <!-- Hero Visual -->
        <div class="hero-visual">
            <div class="hero-circle-1"></div>
            <div class="hero-circle-2"></div>
            
            <div class="hero-image-wrapper">
                <div class="hero-image">🪴</div>
                
                <div class="hero-card hero-card-tl">
                    <div class="hero-card-inner">
                        <div class="hero-card-icon">🌱</div>
                        <div class="hero-card-text">
                            <div class="label">Today's Pick</div>
                            <div class="value">Monstera Deliciosa</div>
                        </div>
                    </div>
                </div>
                
                <div class="hero-card hero-card-br">
                    <div class="hero-card-inner">
                        <div class="hero-card-icon">🚚</div>
                        <div class="hero-card-text">
                            <div class="label">Free Delivery</div>
                            <div class="value">Orders above <?= formatPrice(FREE_SHIPPING_ABOVE) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section">
    <div class="section-header reveal">
        <span class="section-tag">Browse by Category</span>
        <h2 class="section-title">Find Your Perfect Plant</h2>
        <div class="section-divider"></div>
    </div>
    
    <div class="categories-grid">
        <?php 
        $categories = getCategories();
        $delay = 1;
        foreach($categories as $cat): 
            // Get product count for this category
            $db = getDB();
            $stmt = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ? AND stock > 0");
            $stmt->execute([$cat['id']]);
            $count = $stmt->fetchColumn();
        ?>
        <a href="<?= SITE_URL ?>/shop.php?category=<?= $cat['slug'] ?>" class="category-card reveal reveal-delay-<?= $delay++ ?>">
            <span class="cat-card-icon"><?= $cat['icon'] ?></span>
            <h3 class="cat-card-name"><?= sanitize($cat['name']) ?></h3>
            <span class="cat-card-count"><?= $count ?> Products</span>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products Section -->
<section class="section" style="background: var(--gray-50);">
    <div class="section-header reveal">
        <span class="section-tag">Handpicked for You</span>
        <h2 class="section-title">Featured Plants</h2>
        <div class="section-divider"></div>
    </div>
    
    <div class="products-grid">
        <?php 
        $featured = getFeaturedProducts(6);
        $delay = 1;
        foreach($featured as $product): 
        ?>
        <div class="product-card reveal reveal-delay-<?= $delay++ ?>">
            <div class="product-card-image">
                <div class="product-badges">
                    <?php if($product['sale_price']): ?>
                        <span class="badge badge-sale">Sale</span>
                    <?php endif; ?>
                    <?php if($product['care_level'] == 'easy'): ?>
                        <span class="badge badge-easy">Easy Care</span>
                    <?php endif; ?>
                </div>
                
                <button class="product-wishlist-btn <?= isInWishlist($_SESSION['user_id'] ?? 0, $product['id']) ? 'active' : '' ?>" 
                        onclick="toggleWishlist(<?= $product['id'] ?>, this)" 
                        aria-label="Add to wishlist">
                    ♥
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
                
                <div class="product-rating">
                    <?= getStarRating($product['rating']) ?>
                    <span class="rating-count">(<?= $product['review_count'] ?>)</span>
                </div>
                
                <div class="product-care">
                    <span class="care-tag" title="Light Requirement">☀️ <?= ucfirst($product['light_requirement']) ?> Light</span>
                    <span class="care-tag" title="Watering">💧 <?= sanitize($product['watering_frequency']) ?></span>
                </div>
                
                <div class="product-footer">
                    <div class="product-price">
                        <?php if($product['sale_price']): ?>
                            <span class="price-current"><?= formatPrice($product['sale_price']) ?></span>
                            <span class="price-original"><?= formatPrice($product['price']) ?></span>
                        <?php else: ?>
                            <span class="price-current"><?= formatPrice($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <button class="add-to-cart-btn" onclick="addToCart(<?= $product['id'] ?>)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        Add
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center" style="margin-top: 40px; text-align: center;">
        <a href="<?= SITE_URL ?>/shop.php" class="btn btn-outline-gold btn-lg">View All Plants</a>
    </div>
</section>

<!-- Seasonal Offers Banner -->
<section class="section" style="padding: 0;">
    <div style="background: linear-gradient(135deg, var(--green-deep), var(--green-medium)); border-radius: var(--radius-xl); padding: 60px 40px; color: white; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 40px;">
        <div style="flex: 1; min-width: 300px;" class="reveal">
            <span class="section-tag" style="background: rgba(255,255,255,0.1); color: var(--gold);">Monsoon Special</span>
            <h2 style="font-family: var(--font-serif); font-size: 2.5rem; margin-bottom: 16px; color: white;">Bring the freshness of monsoon indoors.</h2>
            <p style="color: rgba(255,255,255,0.8); margin-bottom: 24px; max-width: 400px;">Discover our curated collection of humidity-loving plants perfect for the season. Get up to 30% off.</p>
            <a href="<?= SITE_URL ?>/shop.php?seasonal=1" class="btn btn-gold">Shop Seasonal Collection</a>
        </div>
        
        <div style="flex: 1.5; min-width: 300px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <?php 
            $seasonal = getSeasonalProducts(2);
            foreach($seasonal as $product):
            ?>
            <a href="<?= SITE_URL ?>/product.php?slug=<?= $product['slug'] ?>" class="reveal" style="background: white; border-radius: var(--radius-lg); padding: 16px; display: flex; align-items: center; gap: 16px; text-decoration: none; color: inherit; transition: transform var(--transition-fast);">
                <div style="width: 80px; height: 80px; background: var(--green-pale); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 2rem;">🌿</div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--green-muted); text-transform: uppercase; font-weight: 600;"><?= sanitize($product['category_name']) ?></div>
                    <div style="font-family: var(--font-serif); font-weight: 600; color: var(--green-deep); margin: 4px 0;"><?= sanitize($product['name']) ?></div>
                    <div style="font-weight: 700; color: var(--gold-dark);"><?= formatPrice($product['sale_price'] ?? $product['price']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section">
    <div class="section-header reveal">
        <h2 class="section-title">Why Green Haven?</h2>
        <div class="section-divider"></div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 32px;">
        <div class="text-center reveal reveal-delay-1" style="text-align: center;">
            <div style="width: 80px; height: 80px; background: var(--green-pale); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px; color: var(--green-deep);">🚚</div>
            <h3 style="font-size: 1.1rem; color: var(--green-deep); margin-bottom: 12px;">Fast & Safe Delivery</h3>
            <p style="font-size: 0.9rem; color: var(--gray-500);">Special packaging ensures your plants arrive healthy and beautiful.</p>
        </div>
        <div class="text-center reveal reveal-delay-2" style="text-align: center;">
            <div style="width: 80px; height: 80px; background: var(--green-pale); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px; color: var(--green-deep);">📖</div>
            <h3 style="font-size: 1.1rem; color: var(--green-deep); margin-bottom: 12px;">Expert Care Guides</h3>
            <p style="font-size: 0.9rem; color: var(--gray-500);">Detailed care instructions with every plant to help you succeed.</p>
        </div>
        <div class="text-center reveal reveal-delay-3" style="text-align: center;">
            <div style="width: 80px; height: 80px; background: var(--green-pale); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px; color: var(--green-deep);">✨</div>
            <h3 style="font-size: 1.1rem; color: var(--green-deep); margin-bottom: 12px;">Premium Quality</h3>
            <p style="font-size: 0.9rem; color: var(--gray-500);">Sourced from top nurseries and nurtured by our expert horticulturists.</p>
        </div>
        <div class="text-center reveal reveal-delay-4" style="text-align: center;">
            <div style="width: 80px; height: 80px; background: var(--green-pale); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px; color: var(--green-deep);">💚</div>
            <h3 style="font-size: 1.1rem; color: var(--green-deep); margin-bottom: 12px;">Happy Customers</h3>
            <p style="font-size: 0.9rem; color: var(--gray-500);">Join over 10,000 satisfied plant parents across the country.</p>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section">
    <div class="section-header reveal" style="margin-bottom: 40px;">
        <h2 class="section-title" style="color: white;">Loved by Plant Parents</h2>
        <div class="section-divider" style="background: var(--gold);"></div>
    </div>
    
    <div class="testimonials-grid">
        <div class="testimonial-card reveal reveal-delay-1">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"My Monstera arrived in perfect condition, well-packed and healthy. It has already put out two new leaves in just 3 weeks. The packaging was excellent and the plant guide card was very helpful!"</p>
            <div class="testimonial-author">
                <div class="author-avatar">P</div>
                <div>
                    <div class="author-name">Priya Sharma</div>
                    <div class="author-location">Mumbai</div>
                </div>
            </div>
        </div>
        <div class="testimonial-card reveal reveal-delay-2">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"Very healthy plant, exactly as described. Delivery was prompt. Will definitely order again from Green Haven. The customer support was also very responsive when I had a question about watering."</p>
            <div class="testimonial-author">
                <div class="author-avatar">A</div>
                <div>
                    <div class="author-name">Arjun Mehta</div>
                    <div class="author-location">Bangalore</div>
                </div>
            </div>
        </div>
        <div class="testimonial-card reveal reveal-delay-3">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"I have killed every plant I have owned but not the Snake plant I bought here! It's truly bulletproof. Great purchase. The terracotta pot it came with is also gorgeous."</p>
            <div class="testimonial-author">
                <div class="author-avatar">K</div>
                <div>
                    <div class="author-name">Kavya Nair</div>
                    <div class="author-location">Chennai</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
