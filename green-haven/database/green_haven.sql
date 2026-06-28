-- ============================================================
-- GREEN HAVEN - Online Plant Shop Database
-- Import into phpMyAdmin or run via MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS green_haven CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE green_haven;

-- ============================================================
-- USERS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    profile_pic VARCHAR(255) DEFAULT NULL,
    is_blocked TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- ADMINS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('manager','staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- CATEGORIES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(100) DEFAULT '🌿',
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) DEFAULT NULL,
    stock INT DEFAULT 0,
    sku VARCHAR(100),
    care_level ENUM('easy','moderate','expert') DEFAULT 'easy',
    light_requirement ENUM('low','medium','bright','direct') DEFAULT 'medium',
    watering_frequency VARCHAR(100),
    image VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    is_seasonal TINYINT(1) DEFAULT 0,
    is_subscription TINYINT(1) DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0.00,
    review_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_featured (is_featured),
    INDEX idx_price (price)
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCT IMAGES (Gallery)
-- ============================================================
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCT VARIANTS (Pot type, size, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    variant_type ENUM('pot','size','soil','addon') NOT NULL,
    variant_name VARCHAR(100) NOT NULL,
    extra_price DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- CART TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    pot_variant VARCHAR(100) DEFAULT NULL,
    size_variant VARCHAR(100) DEFAULT NULL,
    soil_variant VARCHAR(100) DEFAULT NULL,
    addons TEXT DEFAULT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_user_cart (user_id)
) ENGINE=InnoDB;

-- ============================================================
-- WISHLISTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- PROMOTIONS / DISCOUNT CODES
-- ============================================================
CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    discount_type ENUM('percentage','fixed') DEFAULT 'percentage',
    discount_value DECIMAL(10,2) NOT NULL,
    min_order_value DECIMAL(10,2) DEFAULT 0.00,
    max_uses INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    expires_at DATE DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- ORDERS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending','confirmed','processing','shipped','out_for_delivery','delivered','cancelled') DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0.00,
    delivery_fee DECIMAL(10,2) DEFAULT 50.00,
    total DECIMAL(10,2) NOT NULL,
    promo_code VARCHAR(50) DEFAULT NULL,
    payment_method ENUM('card','upi','cod','wallet') DEFAULT 'cod',
    payment_status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
    transaction_id VARCHAR(255) DEFAULT NULL,
    delivery_name VARCHAR(100),
    delivery_phone VARCHAR(20),
    delivery_address TEXT,
    delivery_city VARCHAR(100),
    delivery_state VARCHAR(100),
    delivery_pincode VARCHAR(10),
    delivery_notes TEXT,
    estimated_delivery DATE DEFAULT NULL,
    placed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_orders (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================================
-- ORDER ITEMS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_image VARCHAR(255),
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    pot_variant VARCHAR(100) DEFAULT NULL,
    size_variant VARCHAR(100) DEFAULT NULL,
    soil_variant VARCHAR(100) DEFAULT NULL,
    addons TEXT DEFAULT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_order_items (order_id)
) ENGINE=InnoDB;

-- ============================================================
-- REVIEWS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(200),
    body TEXT,
    is_approved TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (product_id, user_id)
) ENGINE=InnoDB;

-- ============================================================
-- TIPS / ARTICLES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS tips_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    category VARCHAR(100) DEFAULT 'General',
    excerpt TEXT,
    content LONGTEXT,
    image VARCHAR(255),
    read_time INT DEFAULT 5,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- SUBSCRIPTIONS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan ENUM('monthly','quarterly','yearly') DEFAULT 'monthly',
    status ENUM('active','paused','cancelled') DEFAULT 'active',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    next_delivery DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SUPPORT TICKETS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    type ENUM('inquiry','complaint','faq','other') DEFAULT 'inquiry',
    message TEXT NOT NULL,
    status ENUM('open','in_progress','resolved','closed') DEFAULT 'open',
    admin_reply TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- INVENTORY LOGS
-- ============================================================
CREATE TABLE IF NOT EXISTS inventory_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    action ENUM('restock','sale','adjustment','damage') NOT NULL,
    quantity_change INT NOT NULL,
    quantity_after INT NOT NULL,
    note VARCHAR(255),
    admin_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA - Categories
-- ============================================================
INSERT INTO categories (name, slug, description, icon, is_active) VALUES
('Indoor Plants', 'indoor', 'Beautiful plants perfect for home and office interiors', '🏠', 1),
('Outdoor Plants', 'outdoor', 'Hardy plants that thrive in gardens and balconies', '🌳', 1),
('Succulents & Cacti', 'succulents', 'Low-maintenance drought-tolerant beauties', '🌵', 1),
('Accessories', 'accessories', 'Pots, tools, soil mixes, and plant care essentials', '🪴', 1);

-- ============================================================
-- SEED DATA - Products
-- ============================================================
INSERT INTO products (category_id, name, slug, description, short_description, price, sale_price, stock, sku, care_level, light_requirement, watering_frequency, image, is_featured, is_seasonal, is_subscription, rating, review_count) VALUES
(1, 'Monstera Deliciosa', 'monstera-deliciosa', 'The iconic Swiss cheese plant known for its dramatic, split leaves. Perfect for bright, indirect light spaces. A statement piece for any modern interior.', 'Iconic tropical plant with distinctive split leaves. Low maintenance and fast-growing.', 1299.00, 999.00, 45, 'GH-IN-001', 'easy', 'medium', 'Once a week', 'monstera.jpg', 1, 0, 1, 4.80, 124),
(1, 'Peace Lily', 'peace-lily', 'One of the best air-purifying plants that thrives in low light. Produces elegant white blooms that can brighten any corner of your home.', 'Elegant white blooms, excellent air purifier, thrives in low light.', 849.00, NULL, 32, 'GH-IN-002', 'easy', 'low', 'Twice a week', 'peace-lily.jpg', 1, 0, 0, 4.60, 89),
(1, 'Fiddle Leaf Fig', 'fiddle-leaf-fig', 'A trendsetter in interior design. The large, glossy, violin-shaped leaves make it a showstopper. Loves bright indirect light and consistent care.', 'Architectural beauty with large violin-shaped leaves. A designer favourite.', 2499.00, 1999.00, 18, 'GH-IN-003', 'moderate', 'bright', 'Once a week', 'fiddle-leaf.jpg', 1, 0, 1, 4.40, 67),
(1, 'Snake Plant', 'snake-plant', 'Virtually indestructible and one of the best plants for beginners. Purifies air, tolerates neglect, and thrives in almost any light condition.', 'Hardy, air-purifying, tolerates low light and occasional neglect perfectly.', 599.00, NULL, 78, 'GH-IN-004', 'easy', 'low', 'Every 2 weeks', 'snake-plant.jpg', 0, 0, 0, 4.90, 203),
(1, 'Pothos Golden', 'pothos-golden', 'A trailing vine with golden-green variegated leaves. Perfect for hanging baskets or letting cascade over shelves. Nearly impossible to kill.', 'Trailing golden vine, perfect for shelves and hanging baskets.', 349.00, NULL, 95, 'GH-IN-005', 'easy', 'low', 'Once a week', 'pothos.jpg', 0, 0, 0, 4.70, 156),
(2, 'Bougainvillea', 'bougainvillea', 'Stunning flowering plant with vibrant magenta bracts. Perfect for gates, trellises, and balcony railings. Drought-tolerant once established.', 'Vibrant flowering climber, perfect for outdoor spaces and balconies.', 899.00, 749.00, 40, 'GH-OUT-001', 'moderate', 'direct', 'Twice a week', 'bougainvillea.jpg', 1, 1, 0, 4.50, 78),
(2, 'Hibiscus', 'hibiscus', 'Classic tropical flowering shrub producing large, showy blooms in red, pink, yellow, and orange. Attracts butterflies and pollinators.', 'Tropical shrub with large colourful blooms. Great for gardens and pots.', 699.00, NULL, 55, 'GH-OUT-002', 'easy', 'direct', 'Daily', 'hibiscus.jpg', 0, 1, 0, 4.30, 45),
(2, 'Bamboo Plant', 'bamboo-plant', 'Fast-growing, elegant bamboo adds a zen, tropical feel to any garden or large patio container. Great for privacy screens and windbreaks.', 'Fast-growing, elegant privacy plant. Creates a zen, tropical atmosphere.', 1499.00, NULL, 22, 'GH-OUT-003', 'easy', 'bright', 'Every 2 days', 'bamboo.jpg', 0, 0, 0, 4.20, 34),
(3, 'Echeveria Collection', 'echeveria-collection', 'A stunning collection of 3 Echeveria succulents in complementary rosette forms. Perfect for desks, windowsills, and terrariums.', 'Set of 3 rosette-shaped Echeverias in complementary colours.', 799.00, 599.00, 60, 'GH-SUC-001', 'easy', 'bright', 'Every 2 weeks', 'echeveria.jpg', 1, 0, 1, 4.85, 112),
(3, 'Cactus Mix', 'cactus-mix', 'A curated selection of 4 different cacti in a decorative pot. No fuss, no drama — just bold, sculptural beauty.', 'Set of 4 assorted cacti, zero-maintenance, bold architectural look.', 549.00, NULL, 80, 'GH-SUC-002', 'easy', 'direct', 'Monthly', 'cactus.jpg', 0, 0, 0, 4.60, 88),
(3, 'Aloe Vera', 'aloe-vera', 'The ultimate multipurpose plant. Aloe gel soothes burns and skin irritation. Incredibly easy to grow and nearly impossible to kill.', 'Multipurpose medicinal plant. Easy to grow, great for skin care.', 299.00, NULL, 120, 'GH-SUC-003', 'easy', 'bright', 'Every 3 weeks', 'aloe.jpg', 0, 0, 0, 4.95, 289),
(4, 'Premium Terracotta Pot Set', 'terracotta-pot-set', 'A set of 3 hand-finished terracotta pots in graduated sizes. Naturally breathable, enhancing root health and soil drainage.', 'Set of 3 graduated terracotta pots, handcrafted and breathable.', 649.00, 499.00, 35, 'GH-ACC-001', 'easy', 'low', 'N/A', 'terracotta.jpg', 1, 0, 0, 4.70, 67);

-- ============================================================
-- SEED DATA - Product Variants
-- ============================================================
INSERT INTO product_variants (product_id, variant_type, variant_name, extra_price) VALUES
(1, 'pot', 'Nursery Pot (Included)', 0.00),
(1, 'pot', 'Terracotta Pot', 150.00),
(1, 'pot', 'Ceramic White Pot', 250.00),
(1, 'pot', 'Hanging Basket', 200.00),
(1, 'size', 'Small (20-30cm)', 0.00),
(1, 'size', 'Medium (40-60cm)', 300.00),
(1, 'size', 'Large (70-90cm)', 700.00),
(1, 'soil', 'Standard Potting Mix (Included)', 0.00),
(1, 'soil', 'Premium Enriched Mix', 99.00),
(1, 'soil', 'Perlite Blend (Aerated)', 79.00),
(1, 'addon', 'Fertilizer Pack', 149.00),
(1, 'addon', 'Watering Can', 299.00),
(1, 'addon', 'Plant Food Spikes', 99.00);

-- Add variants for other products
INSERT INTO product_variants (product_id, variant_type, variant_name, extra_price) VALUES
(2, 'pot', 'Nursery Pot (Included)', 0.00),
(2, 'pot', 'Ceramic Pot', 200.00),
(2, 'size', 'Small', 0.00),
(2, 'size', 'Medium', 250.00),
(3, 'pot', 'Nursery Pot (Included)', 0.00),
(3, 'pot', 'Premium Pot', 350.00),
(3, 'size', 'Medium (50-70cm)', 0.00),
(3, 'size', 'Large (80-120cm)', 800.00),
(4, 'pot', 'Nursery Pot (Included)', 0.00),
(4, 'pot', 'Terracotta Pot', 120.00),
(4, 'size', 'Small', 0.00),
(4, 'size', 'Medium', 200.00),
(4, 'size', 'Large', 500.00);

-- ============================================================
-- SEED DATA - Admins (password: admin123)
-- ============================================================
INSERT INTO admins (name, email, password, role) VALUES
('Admin Manager', 'admin@greenhaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
('Staff Member', 'staff@greenhaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff');

-- ============================================================
-- SEED DATA - Users (password: password123)
-- ============================================================
INSERT INTO users (name, email, password, phone, address, city, state, pincode) VALUES
('Priya Sharma', 'customer@greenhaven.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543210', '42, Green Avenue', 'Mumbai', 'Maharashtra', '400001'),
('Arjun Mehta', 'arjun@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9123456789', '15, Lotus Lane', 'Bangalore', 'Karnataka', '560001'),
('Kavya Nair', 'kavya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9988776655', '7, Palm Street', 'Chennai', 'Tamil Nadu', '600001');

-- ============================================================
-- SEED DATA - Sample Orders
-- ============================================================
INSERT INTO orders (user_id, order_number, status, subtotal, discount, delivery_fee, total, payment_method, payment_status, transaction_id, delivery_name, delivery_phone, delivery_address, delivery_city, delivery_state, delivery_pincode, estimated_delivery, placed_at) VALUES
(1, 'GH-2024-001', 'delivered', 1998.00, 0.00, 50.00, 2048.00, 'upi', 'paid', 'TXN123456', 'Priya Sharma', '9876543210', '42, Green Avenue', 'Mumbai', 'Maharashtra', '400001', '2024-12-15', '2024-12-10 10:30:00'),
(1, 'GH-2024-002', 'shipped', 999.00, 100.00, 0.00, 899.00, 'card', 'paid', 'TXN789012', 'Priya Sharma', '9876543210', '42, Green Avenue', 'Mumbai', 'Maharashtra', '400001', '2024-12-25', '2024-12-20 14:20:00'),
(2, 'GH-2024-003', 'processing', 1598.00, 0.00, 50.00, 1648.00, 'cod', 'pending', NULL, 'Arjun Mehta', '9123456789', '15, Lotus Lane', 'Bangalore', 'Karnataka', '560001', '2024-12-28', '2024-12-22 09:15:00');

INSERT INTO order_items (order_id, product_id, product_name, product_image, quantity, unit_price) VALUES
(1, 1, 'Monstera Deliciosa', 'monstera.jpg', 1, 999.00),
(1, 4, 'Snake Plant', 'snake-plant.jpg', 1, 599.00),
(1, 5, 'Pothos Golden', 'pothos.jpg', 1, 349.00),
(2, 3, 'Fiddle Leaf Fig', 'fiddle-leaf.jpg', 1, 1999.00),
(3, 9, 'Echeveria Collection', 'echeveria.jpg', 1, 599.00),
(3, 6, 'Bougainvillea', 'bougainvillea.jpg', 1, 749.00);

-- ============================================================
-- SEED DATA - Promotions
-- ============================================================
INSERT INTO promotions (code, description, discount_type, discount_value, min_order_value, max_uses, expires_at, is_active) VALUES
('WELCOME10', 'Welcome discount for new customers', 'percentage', 10.00, 500.00, 500, '2025-12-31', 1),
('FREESHIP', 'Free shipping on any order', 'fixed', 50.00, 299.00, NULL, '2025-06-30', 1),
('PLANT20', 'Get 20% off on orders above ₹2000', 'percentage', 20.00, 2000.00, 200, '2025-03-31', 1),
('MONSOON50', 'Monsoon special - ₹50 off', 'fixed', 50.00, 800.00, 100, '2024-09-30', 0);

-- ============================================================
-- SEED DATA - Reviews
-- ============================================================
INSERT INTO reviews (product_id, user_id, rating, title, body) VALUES
(1, 1, 5, 'Absolutely gorgeous!', 'My Monstera arrived in perfect condition, well-packed and healthy. It has already put out two new leaves in just 3 weeks. The packaging was excellent and the plant guide card was very helpful!'),
(1, 2, 5, 'Great quality plant', 'Very healthy plant, exactly as described. Delivery was prompt. Will definitely order again from Green Haven.'),
(4, 1, 5, 'Best plant for beginners', 'I have killed every plant I have owned but not this one! Snake plant is truly bulletproof. Great purchase.'),
(9, 3, 5, 'Beautiful collection', 'The echeveria set is stunning. Each one is a slightly different shade of pink-purple and they look amazing together on my desk.');

-- ============================================================
-- SEED DATA - Tips Articles
-- ============================================================
INSERT INTO tips_articles (title, slug, category, excerpt, content, image, read_time, is_published) VALUES
('How to Water Indoor Plants the Right Way', 'how-to-water-indoor-plants', 'Watering', 'Overwatering is the #1 killer of houseplants. Learn the finger test and how to read your plant\'s thirst signals.', '<p>The most common mistake plant owners make is overwatering. Most houseplants prefer their soil to dry out slightly between waterings. Here\'s how to get it right...</p><h3>The Finger Test</h3><p>Push your finger 1-2 inches into the soil. If it feels dry, it\'s time to water. If it\'s still moist, wait another day or two.</p><h3>Signs of Overwatering</h3><ul><li>Yellow leaves</li><li>Soggy soil for extended periods</li><li>Root rot (musty smell)</li><li>Wilting despite wet soil</li></ul><h3>Signs of Underwatering</h3><ul><li>Dry, crispy leaf edges</li><li>Drooping/wilting</li><li>Very light pot (water adds weight)</li></ul>', 'watering-guide.jpg', 5, 1),
('Best Low-Light Plants for Dark Rooms', 'best-low-light-plants', 'Indoor', 'Think you can\'t grow plants in a north-facing room? Think again. These plants actually prefer shade.', '<p>Many beautiful houseplants actually prefer low light conditions, making them perfect for offices, north-facing rooms, and dimly lit corners.</p><h3>Top Picks</h3><ul><li><strong>Snake Plant</strong> - Almost indestructible in low light</li><li><strong>Peace Lily</strong> - Blooms even in shade</li><li><strong>Pothos</strong> - Trails beautifully in any light</li><li><strong>ZZ Plant</strong> - Thrives on neglect</li><li><strong>Cast Iron Plant</strong> - Lives up to its name</li></ul>', 'low-light-plants.jpg', 4, 1),
('Succulent Care 101: The Complete Guide', 'succulent-care-guide', 'Succulents', 'Succulents store water in their leaves. Here\'s everything you need to know to keep them thriving and plump.', '<p>Succulents are incredibly resilient but do have specific needs. Understanding these will keep your collection thriving.</p><h3>Light</h3><p>Succulents need at least 6 hours of bright light per day. A south or east-facing windowsill is ideal.</p><h3>Watering</h3><p>Water deeply but infrequently. Let the soil completely dry out between waterings. In summer, water every 2 weeks; in winter, monthly is enough.</p><h3>Soil</h3><p>Use a well-draining succulent mix. Regular potting soil holds too much moisture and will cause rot.</p>', 'succulent-guide.jpg', 7, 1),
('Creating a Monstera-Themed Living Room', 'monstera-living-room-decor', 'Decor', 'The Monstera is the undisputed king of indoor plants. Here\'s how to style your space around it.', '<p>The Monstera Deliciosa has become an interior design icon. Its dramatic split leaves photograph beautifully and add instant tropical glamour to any space.</p><h3>Placement Tips</h3><ul><li>Place near a window with bright indirect light</li><li>Allow space for the plant to spread (they can reach 2-3m indoors)</li><li>Use a moss pole to encourage upward growth</li></ul><h3>Styling Ideas</h3><p>Pair with neutral tones — white walls, beige linen, natural wood — and let the plant be the star. A single large Monstera makes more impact than 10 small plants.</p>', 'monstera-decor.jpg', 6, 1);

-- ============================================================
-- SEED DATA - Support Tickets
-- ============================================================
INSERT INTO support_tickets (user_id, name, email, subject, type, message, status, admin_reply) VALUES
(1, 'Priya Sharma', 'customer@greenhaven.com', 'My order seems delayed', 'complaint', 'I placed order GH-2024-002 on Dec 20th and was expecting delivery by Dec 25th. It is now Dec 27th and I haven\'t received it. Please help.', 'resolved', 'We sincerely apologize for the delay! Your order has been dispatched and will arrive by tomorrow. We\'ve added a ₹100 store credit to your account as an apology.'),
(NULL, 'Anonymous Customer', 'anon@example.com', 'Do you ship to Tier 2 cities?', 'inquiry', 'I am from Nagpur. Do you ship there? What are the delivery charges?', 'resolved', 'Yes, we ship across India! Delivery to Nagpur typically takes 5-7 business days. Delivery fee is ₹50 for orders under ₹999, and FREE for orders above that.');

DELIMITER $$

-- Trigger to update product rating after review insert
CREATE TRIGGER update_product_rating_after_insert
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    UPDATE products
    SET rating = (SELECT AVG(rating) FROM reviews WHERE product_id = NEW.product_id AND is_approved = 1),
        review_count = (SELECT COUNT(*) FROM reviews WHERE product_id = NEW.product_id AND is_approved = 1)
    WHERE id = NEW.product_id;
END$$

-- Trigger to log inventory changes on order
CREATE TRIGGER log_inventory_on_order
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'confirmed' AND OLD.status = 'pending' THEN
        UPDATE products p
        JOIN order_items oi ON oi.product_id = p.id
        SET p.stock = p.stock - oi.quantity
        WHERE oi.order_id = NEW.id;
    END IF;
END$$

DELIMITER ;
