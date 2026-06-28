<?php
// ============================================================
// GREEN HAVEN - Subscription (Plant of the Month)
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$metaTitle = 'Plant of the Month Subscription – ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<div class="page-header" style="background: var(--green-deep); color: white; padding: 80px 20px;">
    <h1 style="font-family: var(--font-serif); font-size: 3rem; margin-bottom: 20px;">Never Miss a Plant</h1>
    <p style="font-size: 1.2rem; max-width: 600px; margin: 0 auto; color: rgba(255,255,255,0.8); line-height: 1.6;">
        Join our exclusive Plant of the Month club. Discover rare beauties, get expert care guides, and grow your indoor jungle effortlessly.
    </p>
</div>

<!-- What's Included -->
<div class="section" style="background: var(--gray-50);">
    <div class="section-header text-center">
        <span class="section-tag">Unboxing Joy</span>
        <h2 class="section-title">What's in the Box?</h2>
        <div class="section-divider" style="margin: 0 auto;"></div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 32px; max-width: 1000px; margin: 40px auto 0;">
        <div class="text-center" style="background: white; padding: 32px 24px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
            <div style="font-size: 3rem; margin-bottom: 16px;">🪴</div>
            <h4 style="color: var(--green-deep); margin-bottom: 8px;">Curated Plant</h4>
            <p style="font-size: 0.9rem; color: var(--gray-500);">A handpicked, healthy plant in a premium nursery pot.</p>
        </div>
        <div class="text-center" style="background: white; padding: 32px 24px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
            <div style="font-size: 3rem; margin-bottom: 16px;">🟤</div>
            <h4 style="color: var(--green-deep); margin-bottom: 8px;">Custom Soil Mix</h4>
            <p style="font-size: 0.9rem; color: var(--gray-500);">A small bag of specialized soil perfect for your new plant.</p>
        </div>
        <div class="text-center" style="background: white; padding: 32px 24px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
            <div style="font-size: 3rem; margin-bottom: 16px;">📖</div>
            <h4 style="color: var(--green-deep); margin-bottom: 8px;">Care Card</h4>
            <p style="font-size: 0.9rem; color: var(--gray-500);">Detailed instructions to help your plant thrive.</p>
        </div>
        <div class="text-center" style="background: white; padding: 32px 24px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
            <div style="font-size: 3rem; margin-bottom: 16px;">🎁</div>
            <h4 style="color: var(--green-deep); margin-bottom: 8px;">Surprise Gift</h4>
            <p style="font-size: 0.9rem; color: var(--gray-500);">Occasional treats like fertilizer, snips, or decor.</p>
        </div>
    </div>
</div>

<!-- Pricing Plans -->
<div class="section">
    <div class="section-header text-center">
        <h2 class="section-title">Choose Your Plan</h2>
        <div class="section-divider" style="margin: 0 auto;"></div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 32px; max-width: 1000px; margin: 40px auto 0;">
        
        <!-- Monthly -->
        <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 40px 32px; text-align: center; position: relative;">
            <h3 style="font-size: 1.2rem; color: var(--gray-600); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.1em;">Monthly</h3>
            <div style="font-family: var(--font-serif); font-size: 2.5rem; color: var(--green-deep); font-weight: 700; margin-bottom: 8px;">₹999<span style="font-size: 1rem; color: var(--gray-400); font-family: var(--font-sans); font-weight: 400;">/mo</span></div>
            <p style="font-size: 0.9rem; color: var(--gray-500); margin-bottom: 32px;">Billed every month. Cancel anytime.</p>
            
            <ul style="list-style: none; padding: 0; margin: 0 0 32px; text-align: left;">
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--green-deep);">✓</span> 1 Exclusive Plant</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--green-deep);">✓</span> Care Guide</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--green-deep);">✓</span> Free Shipping</li>
            </ul>
            
            <button class="btn btn-outline btn-full btn-lg">Select Plan</button>
        </div>
        
        <!-- Quarterly (Popular) -->
        <div style="background: var(--green-deep); color: white; border-radius: var(--radius-lg); padding: 40px 32px; text-align: center; position: relative; transform: scale(1.05); box-shadow: var(--shadow-lg);">
            <div style="position: absolute; top: 0; left: 50%; transform: translate(-50%, -50%); background: var(--gold); color: var(--green-deep); font-size: 0.75rem; font-weight: 700; padding: 6px 16px; border-radius: var(--radius-full); text-transform: uppercase; letter-spacing: 0.1em;">Most Popular</div>
            
            <h3 style="font-size: 1.2rem; color: rgba(255,255,255,0.8); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.1em;">Quarterly</h3>
            <div style="font-family: var(--font-serif); font-size: 2.5rem; color: var(--gold); font-weight: 700; margin-bottom: 8px;">₹2,499<span style="font-size: 1rem; color: rgba(255,255,255,0.5); font-family: var(--font-sans); font-weight: 400;">/3 mo</span></div>
            <p style="font-size: 0.9rem; color: rgba(255,255,255,0.6); margin-bottom: 32px;">Save ₹498. Billed every 3 months.</p>
            
            <ul style="list-style: none; padding: 0; margin: 0 0 32px; text-align: left;">
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--gold);">✓</span> 1 Exclusive Plant/mo</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--gold);">✓</span> Care Guide</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--gold);">✓</span> Free Shipping</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--gold);">✓</span> Surprise Gift</li>
            </ul>
            
            <button class="btn btn-gold btn-full btn-lg">Select Plan</button>
        </div>
        
        <!-- Yearly -->
        <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 40px 32px; text-align: center; position: relative;">
            <h3 style="font-size: 1.2rem; color: var(--gray-600); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.1em;">Yearly</h3>
            <div style="font-family: var(--font-serif); font-size: 2.5rem; color: var(--green-deep); font-weight: 700; margin-bottom: 8px;">₹7,999<span style="font-size: 1rem; color: var(--gray-400); font-family: var(--font-sans); font-weight: 400;">/yr</span></div>
            <p style="font-size: 0.9rem; color: var(--gray-500); margin-bottom: 32px;">Get 4 months free! Billed annually.</p>
            
            <ul style="list-style: none; padding: 0; margin: 0 0 32px; text-align: left;">
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--green-deep);">✓</span> 1 Exclusive Plant/mo</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--green-deep);">✓</span> Care Guide</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--green-deep);">✓</span> Free Shipping</li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;"><span style="color: var(--green-deep);">✓</span> Premium Pots included</li>
            </ul>
            
            <button class="btn btn-outline btn-full btn-lg">Select Plan</button>
        </div>
        
    </div>
</div>

<!-- Past Months Showcase -->
<div class="section" style="background: var(--gray-50);">
    <div class="section-header text-center">
        <h2 class="section-title">Past Deliveries</h2>
        <div class="section-divider" style="margin: 0 auto;"></div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px; max-width: 1000px; margin: 40px auto 0;">
        <div style="background: white; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
            <div style="height: 200px; background: var(--green-pale); display: flex; align-items: center; justify-content: center; font-size: 5rem;">🌿</div>
            <div style="padding: 24px; text-align: center;">
                <div style="font-size: 0.8rem; color: var(--gold-dark); text-transform: uppercase; font-weight: 700; margin-bottom: 8px;">December</div>
                <h4 style="font-size: 1.2rem; color: var(--green-deep);">Monstera Deliciosa</h4>
            </div>
        </div>
        <div style="background: white; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
            <div style="height: 200px; background: var(--green-pale); display: flex; align-items: center; justify-content: center; font-size: 5rem;">🪴</div>
            <div style="padding: 24px; text-align: center;">
                <div style="font-size: 0.8rem; color: var(--gold-dark); text-transform: uppercase; font-weight: 700; margin-bottom: 8px;">November</div>
                <h4 style="font-size: 1.2rem; color: var(--green-deep);">Snake Plant Laurentii</h4>
            </div>
        </div>
        <div style="background: white; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
            <div style="height: 200px; background: var(--green-pale); display: flex; align-items: center; justify-content: center; font-size: 5rem;">🌵</div>
            <div style="padding: 24px; text-align: center;">
                <div style="font-size: 0.8rem; color: var(--gold-dark); text-transform: uppercase; font-weight: 700; margin-bottom: 8px;">October</div>
                <h4 style="font-size: 1.2rem; color: var(--green-deep);">Echeveria Elegans</h4>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
