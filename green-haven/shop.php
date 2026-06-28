<?php
// ============================================================
// GREEN HAVEN - Shop Page
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$metaTitle = 'Shop All Plants & Accessories – ' . SITE_NAME;
$metaDescription = 'Browse our extensive collection of indoor plants, outdoor plants, succulents, and accessories. Filter by care level, price, and more.';

// Handle Filters
$category_slug = $_GET['category'] ?? '';
$search        = $_GET['search'] ?? '';
$sort          = $_GET['sort'] ?? 'featured';
$min_price     = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price     = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 5000;
$care_levels   = $_GET['care'] ?? []; // Array of care levels
$lights        = $_GET['light'] ?? []; // Array of light reqs
$is_seasonal   = isset($_GET['seasonal']) ? 1 : 0;
$page          = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit         = 12;
$offset        = ($page - 1) * $limit;

// Build Query
$db = getDB();
$where = ["p.stock > 0"];
$params = [];

if ($category_slug) {
    $stmt = $db->prepare("SELECT id, name FROM categories WHERE slug = ?");
    $stmt->execute([$category_slug]);
    $cat = $stmt->fetch();
    if ($cat) {
        $where[] = "p.category_id = ?";
        $params[] = $cat['id'];
        $metaTitle = $cat['name'] . ' – ' . SITE_NAME;
    }
}

if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($min_price > 0) {
    $where[] = "COALESCE(p.sale_price, p.price) >= ?";
    $params[] = $min_price;
}

if ($max_price < 5000) {
    $where[] = "COALESCE(p.sale_price, p.price) <= ?";
    $params[] = $max_price;
}

if (!empty($care_levels) && is_array($care_levels)) {
    $placeholders = implode(',', array_fill(0, count($care_levels), '?'));
    $where[] = "p.care_level IN ($placeholders)";
    foreach($care_levels as $c) $params[] = $c;
}

if (!empty($lights) && is_array($lights)) {
    $placeholders = implode(',', array_fill(0, count($lights), '?'));
    $where[] = "p.light_requirement IN ($placeholders)";
    foreach($lights as $l) $params[] = $l;
}

if ($is_seasonal) {
    $where[] = "p.is_seasonal = 1";
}

$whereClause = implode(' AND ', $where);

// Sorting
$orderBy = "p.is_featured DESC, p.created_at DESC"; // Default: featured
if ($sort === 'price_asc') $orderBy = "COALESCE(p.sale_price, p.price) ASC";
if ($sort === 'price_desc') $orderBy = "COALESCE(p.sale_price, p.price) DESC";
if ($sort === 'rating') $orderBy = "p.rating DESC";
if ($sort === 'newest') $orderBy = "p.created_at DESC";

// Count total
$countQuery = "SELECT COUNT(*) FROM products p WHERE $whereClause";
$stmt = $db->prepare($countQuery);
$stmt->execute($params);
$totalProducts = $stmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// Fetch products
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE $whereClause 
          ORDER BY $orderBy 
          LIMIT $limit OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get all categories for filter
$categories = getCategories();

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <h1>Shop All Plants</h1>
    <div class="breadcrumb">
        <a href="<?= SITE_URL ?>/index.php">Home</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">Shop</span>
    </div>
</div>

<!-- Shop Layout -->
<div class="shop-layout section">
    
    <!-- Sidebar / Filters -->
    <aside class="shop-sidebar">
        <div class="filter-panel">
            <div class="filter-header">
                Filters
                <a href="?" class="filter-clear-btn">Clear All</a>
            </div>
            
            <form action="" method="GET" id="filterForm">
                <?php if($search): ?>
                    <input type="hidden" name="search" value="<?= sanitize($search) ?>">
                <?php endif; ?>
                <input type="hidden" name="sort" value="<?= sanitize($sort) ?>" id="filterSort">
                
                <!-- Category Filter -->
                <div class="filter-section">
                    <div class="filter-title" onclick="this.classList.toggle('collapsed'); this.nextElementSibling.style.display = this.classList.contains('collapsed') ? 'none' : 'flex';">
                        Category <span class="toggle-icon">▼</span>
                    </div>
                    <div class="filter-options">
                        <?php foreach($categories as $cat): ?>
                        <label class="filter-option">
                            <input type="radio" name="category" value="<?= $cat['slug'] ?>" <?= $category_slug === $cat['slug'] ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()">
                            <?= sanitize($cat['name']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Price Filter -->
                <div class="filter-section">
                    <div class="filter-title">Price Range</div>
                    <div class="price-range-wrapper">
                        <div class="price-inputs">
                            <input type="number" name="min_price" class="price-input" value="<?= $min_price ?>" min="0" max="5000">
                            <span class="price-sep">to</span>
                            <input type="number" name="max_price" class="price-input" value="<?= $max_price ?>" min="0" max="5000">
                        </div>
                        <button type="submit" class="btn btn-outline-sm btn-full" style="color: var(--green-deep); border-color: var(--gray-200); margin-top: 10px;">Apply</button>
                    </div>
                </div>
                
                <!-- Care Level -->
                <div class="filter-section">
                    <div class="filter-title">Care Level</div>
                    <div class="filter-options">
                        <label class="filter-option">
                            <input type="checkbox" name="care[]" value="easy" <?= in_array('easy', $care_levels) ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()"> Easy
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="care[]" value="moderate" <?= in_array('moderate', $care_levels) ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()"> Moderate
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="care[]" value="expert" <?= in_array('expert', $care_levels) ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()"> Expert
                        </label>
                    </div>
                </div>
                
                <!-- Light Requirement -->
                <div class="filter-section">
                    <div class="filter-title">Light Requirement</div>
                    <div class="filter-options">
                        <label class="filter-option">
                            <input type="checkbox" name="light[]" value="low" <?= in_array('low', $lights) ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()"> Low Light
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="light[]" value="medium" <?= in_array('medium', $lights) ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()"> Medium Light
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="light[]" value="bright" <?= in_array('bright', $lights) ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()"> Bright Indirect
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="light[]" value="direct" <?= in_array('direct', $lights) ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()"> Direct Sun
                        </label>
                    </div>
                </div>
                
            </form>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="shop-main">
        
        <!-- Toolbar -->
        <div class="shop-toolbar">
            <div class="shop-result-count">
                Showing <?= min($offset + 1, $totalProducts) ?> - <?= min($offset + $limit, $totalProducts) ?> of <?= $totalProducts ?> products
                <?php if($search): ?> for "<strong><?= sanitize($search) ?></strong>"<?php endif; ?>
            </div>
            
            <div class="shop-sort">
                <span class="sort-label">Sort by:</span>
                <select class="sort-select" onchange="document.getElementById('filterSort').value = this.value; document.getElementById('filterForm').submit();">
                    <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Featured</option>
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Top Rated</option>
                </select>
                
                <div class="view-toggle">
                    <button class="view-btn active" id="gridViewBtn" title="Grid View">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </button>
                    <button class="view-btn" id="listViewBtn" title="List View">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Active Filters Display -->
        <?php if($category_slug || $search || !empty($care_levels) || !empty($lights) || $min_price > 0 || $max_price < 5000): ?>
        <div class="active-filters">
            <?php if($category_slug): ?>
                <span class="active-filter-tag">Category: <?= sanitize($cat['name'] ?? $category_slug) ?> <button onclick="window.location.href='?<?= http_build_query(array_merge($_GET, ['category' => ''])) ?>'">×</button></span>
            <?php endif; ?>
            <?php if($search): ?>
                <span class="active-filter-tag">Search: <?= sanitize($search) ?> <button onclick="window.location.href='?<?= http_build_query(array_merge($_GET, ['search' => ''])) ?>'">×</button></span>
            <?php endif; ?>
            <?php foreach($care_levels as $c): ?>
                <span class="active-filter-tag">Care: <?= ucfirst($c) ?> <button onclick="removeArrayFilter('care', '<?= $c ?>')">×</button></span>
            <?php endforeach; ?>
            <?php foreach($lights as $l): ?>
                <span class="active-filter-tag">Light: <?= ucfirst($l) ?> <button onclick="removeArrayFilter('light', '<?= $l ?>')">×</button></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Products Grid -->
        <?php if($totalProducts > 0): ?>
            <div class="products-grid" id="productsGrid">
                <?php foreach($products as $product): ?>
                <div class="product-card">
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
                            <span class="care-tag" title="Light Requirement">☀️ <?= ucfirst($product['light_requirement']) ?></span>
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
            
            <!-- Pagination -->
            <?php if($totalPages > 1): ?>
            <div class="pagination">
                <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="page-btn <?= $page === $i ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; background: var(--gray-50); border-radius: var(--radius-md);">
                <div style="font-size: 3rem; margin-bottom: 20px;">🌵</div>
                <h3 style="font-size: 1.2rem; color: var(--green-deep); margin-bottom: 10px;">No plants found</h3>
                <p style="color: var(--gray-500); margin-bottom: 20px;">We couldn't find any plants matching your current filters.</p>
                <a href="?" class="btn btn-outline-gold">Clear Filters</a>
            </div>
        <?php endif; ?>
        
    </main>
</div>

<script>
// Toggle List/Grid View
document.getElementById('listViewBtn')?.addEventListener('click', function() {
    this.classList.add('active');
    document.getElementById('gridViewBtn').classList.remove('active');
    document.getElementById('productsGrid').classList.add('list-view');
});

document.getElementById('gridViewBtn')?.addEventListener('click', function() {
    this.classList.add('active');
    document.getElementById('listViewBtn').classList.remove('active');
    document.getElementById('productsGrid').classList.remove('list-view');
});

function removeArrayFilter(name, value) {
    // In a real app, parse URL params, remove the value, and redirect
    // For simplicity, just clearing everything to show intent
    window.location.href = '?'; 
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
