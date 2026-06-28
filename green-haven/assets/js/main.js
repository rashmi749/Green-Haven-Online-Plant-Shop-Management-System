/* ============================================================
   GREEN HAVEN – Main JavaScript
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- Mobile Navbar Toggle ---
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('nav-links');
    
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('open');
            navLinks.classList.toggle('open');
            hamburger.setAttribute('aria-expanded', hamburger.classList.contains('open'));
        });
    }

    // --- Search Bar Toggle ---
    const searchToggle = document.getElementById('search-toggle');
    const searchBar = document.getElementById('search-bar');
    const searchClose = document.getElementById('search-close');
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');

    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', (e) => {
            e.preventDefault();
            searchBar.classList.add('active');
            setTimeout(() => searchInput.focus(), 100);
        });

        searchClose.addEventListener('click', () => {
            searchBar.classList.remove('active');
        });

        // Search submit
        searchBtn.addEventListener('click', () => {
            if(searchInput.value.trim() !== '') {
                window.location.href = SITE_URL + '/shop.php?search=' + encodeURIComponent(searchInput.value.trim());
            }
        });
        
        searchInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter' && searchInput.value.trim() !== '') {
                window.location.href = SITE_URL + '/shop.php?search=' + encodeURIComponent(searchInput.value.trim());
            }
        });
    }

    // --- Navbar Scroll Effect ---
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // --- Scroll Reveal Animations ---
    const reveals = document.querySelectorAll('.reveal');
    const revealOnScroll = () => {
        const windowHeight = window.innerHeight;
        const elementVisible = 100;
        
        reveals.forEach((reveal) => {
            const elementTop = reveal.getBoundingClientRect().top;
            if (elementTop < windowHeight - elementVisible) {
                reveal.classList.add('visible');
            }
        });
    };
    
    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll(); // Trigger on load

    // --- Back to Top Button ---
    const backToTopBtn = document.getElementById('back-to-top');
    if (backToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});

// --- Wishlist Toggle ---
window.toggleWishlist = function(productId, btn) {
    // In a real app, this would be an AJAX call to api/wishlist.php
    // For this prototype, we'll just toggle the UI state and show a mock flash message
    const isActive = btn.classList.contains('active');
    
    if (isActive) {
        btn.classList.remove('active');
        showFlash('info', 'Item removed from wishlist');
    } else {
        btn.classList.add('active');
        showFlash('success', 'Item added to wishlist');
    }
};

// --- Add to Cart ---
window.addToCart = function(productId, qty = 1, options = {}) {
    // Show loading state on button
    // In a real app, make AJAX call to api/cart.php
    showFlash('success', 'Added to cart successfully!');
    
    // Update cart counter visually
    const cartBadge = document.getElementById('cart-count');
    if (cartBadge) {
        let currentCount = parseInt(cartBadge.innerText);
        cartBadge.innerText = currentCount + qty;
        
        // Trigger pop animation
        cartBadge.style.animation = 'none';
        cartBadge.offsetHeight; /* trigger reflow */
        cartBadge.style.animation = null; 
    } else {
        // If badge doesn't exist, reload page to show it
        window.location.reload();
    }
};

// --- Flash Messages ---
window.showFlash = function(type, message) {
    // Remove existing if any
    const existing = document.getElementById('dynamic-flash');
    if (existing) existing.remove();

    const flash = document.createElement('div');
    flash.id = 'dynamic-flash';
    flash.className = `flash-message flash-${type}`;
    flash.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="flash-close">×</button>
    `;
    
    document.body.appendChild(flash);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if(flash.parentElement) {
            flash.style.opacity = '0';
            flash.style.transform = 'translateX(100%)';
            setTimeout(() => flash.remove(), 300);
        }
    }, 5000);
};

// Define SITE_URL globally if not already defined (useful for external scripts)
const SITE_URL = window.location.origin + '/green-haven';
