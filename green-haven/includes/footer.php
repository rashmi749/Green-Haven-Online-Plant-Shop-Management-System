<?php
// ============================================================
// GREEN HAVEN - Site Footer
// ============================================================
?>
<!-- Footer -->
<footer class="footer">
    <div class="footer-wave">
        <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#013220"/>
        </svg>
    </div>
    <div class="footer-body">
        <div class="footer-grid">
            <!-- Brand -->
            <div class="footer-brand">
                <a href="<?= SITE_URL ?>/index.php" class="footer-logo">
                    <span class="logo-icon">🌿</span>
                    <span class="logo-text">Green<span class="logo-accent">Haven</span></span>
                </a>
                <p class="footer-tagline">Bringing nature's beauty into your home. Carefully curated plants, delivered with love.</p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="#" class="social-link" aria-label="WhatsApp">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438C8.34 21.475 10.11 22 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
                    </a>
                    <a href="#" class="social-link" aria-label="YouTube">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#013220"/></svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-col">
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
                    <li><a href="<?= SITE_URL ?>/shop.php">Shop All Plants</a></li>
                    <li><a href="<?= SITE_URL ?>/tips.php">Plant Care Guides</a></li>
                    <li><a href="<?= SITE_URL ?>/subscription.php">Plant of the Month</a></li>
                    <li><a href="<?= SITE_URL ?>/contact.php">Contact Us</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="footer-col">
                <h4 class="footer-heading">Categories</h4>
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>/shop.php?category=indoor">🏠 Indoor Plants</a></li>
                    <li><a href="<?= SITE_URL ?>/shop.php?category=outdoor">🌳 Outdoor Plants</a></li>
                    <li><a href="<?= SITE_URL ?>/shop.php?category=succulents">🌵 Succulents & Cacti</a></li>
                    <li><a href="<?= SITE_URL ?>/shop.php?category=accessories">🪴 Accessories</a></li>
                </ul>
            </div>

            <!-- Contact & Newsletter -->
            <div class="footer-col">
                <h4 class="footer-heading">Stay Connected</h4>
                <div class="footer-contact">
                    <p>📍 42, Green Avenue, Mumbai, Maharashtra</p>
                    <p>📞 <a href="tel:+919876543210">+91 98765 43210</a></p>
                    <p>✉️ <a href="mailto:hello@greenhaven.in">hello@greenhaven.in</a></p>
                </div>
                <div class="newsletter">
                    <p class="newsletter-label">Get plant tips in your inbox</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Your email address" class="newsletter-input">
                        <button class="btn btn-gold btn-sm newsletter-btn">Subscribe</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copy">© <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. Made with 💚 for plant lovers.</p>
            <div class="footer-badges">
                <span class="badge-footer">🔒 Secure Checkout</span>
                <span class="badge-footer">🚚 Pan-India Delivery</span>
                <span class="badge-footer">🌱 100% Natural</span>
            </div>
        </div>
    </div>
</footer>

<!-- Cart Sidebar Overlay -->
<div class="cart-overlay" id="cart-overlay"></div>

<!-- Back to Top -->
<button class="back-to-top" id="back-to-top" aria-label="Back to top">↑</button>

<!-- Scripts -->
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>
