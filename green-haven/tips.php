<?php
// ============================================================
// GREEN HAVEN - Plant Care Tips
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$metaTitle = 'Plant Care Guides & Tips – ' . SITE_NAME;
include __DIR__ . '/includes/header.php';

// Mock tips data for now (since we don't have a tips table in DB)
$articles = [
    [
        'category' => 'Watering',
        'icon' => '💧',
        'title' => 'The Ultimate Guide to Watering Indoor Plants',
        'excerpt' => 'Overwatering is the #1 killer of houseplants. Learn how to read the signs and water perfectly every time.',
        'time' => '5 min read',
        'date' => 'Oct 12, 2023'
    ],
    [
        'category' => 'Indoor',
        'icon' => '🪴',
        'title' => 'Top 10 Low-Light Plants for Beginners',
        'excerpt' => 'Don\'t have bright windows? These resilient plants will thrive even in your darkest corners.',
        'time' => '4 min read',
        'date' => 'Oct 05, 2023'
    ],
    [
        'category' => 'Succulents',
        'icon' => '🌵',
        'title' => 'How to Make Your Succulents Thrive, Not Just Survive',
        'excerpt' => 'Succulents need more than just neglect. Master the perfect soil mix and watering schedule.',
        'time' => '6 min read',
        'date' => 'Sep 28, 2023'
    ],
    [
        'category' => 'Decor',
        'icon' => '✨',
        'title' => 'Styling Your Space with Large Statement Plants',
        'excerpt' => 'How to choose and position large plants like Monsteras and Fiddle Leaf Figs for maximum impact.',
        'time' => '7 min read',
        'date' => 'Sep 15, 2023'
    ]
];
?>

<div class="page-header" style="background: var(--green-pale); color: var(--green-deep); padding: 60px 20px;">
    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; margin-bottom: 16px;">Plant Care Guides</h1>
    <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto; color: var(--green-medium);">Everything you need to know to keep your green friends happy, healthy, and thriving.</p>
</div>

<div class="section">
    
    <!-- Filter Tabs -->
    <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; margin-bottom: 40px;">
        <button class="btn btn-gold" style="border-radius: var(--radius-full);">All Guides</button>
        <button class="btn btn-outline" style="border-radius: var(--radius-full); border-color: var(--gray-300); color: var(--gray-600);">Watering</button>
        <button class="btn btn-outline" style="border-radius: var(--radius-full); border-color: var(--gray-300); color: var(--gray-600);">Indoor</button>
        <button class="btn btn-outline" style="border-radius: var(--radius-full); border-color: var(--gray-300); color: var(--gray-600);">Succulents</button>
        <button class="btn btn-outline" style="border-radius: var(--radius-full); border-color: var(--gray-300); color: var(--gray-600);">Decor</button>
    </div>
    
    <!-- Articles Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 32px; margin-bottom: 60px;">
        <?php foreach($articles as $article): ?>
        <a href="#" style="display: block; text-decoration: none; color: inherit; background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--gray-100); overflow: hidden; transition: transform var(--transition-fast), box-shadow var(--transition-fast);" class="hover-lift">
            <div style="height: 160px; background: var(--gray-50); display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                <?= $article['icon'] ?>
            </div>
            <div style="padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--gold-dark); font-weight: 700;"><?= $article['category'] ?></span>
                    <span style="font-size: 0.8rem; color: var(--gray-400);"><?= $article['time'] ?></span>
                </div>
                <h3 style="font-family: var(--font-serif); font-size: 1.25rem; color: var(--green-deep); margin-bottom: 12px; line-height: 1.4;"><?= $article['title'] ?></h3>
                <p style="font-size: 0.9rem; color: var(--gray-600); line-height: 1.6; margin-bottom: 20px;"><?= $article['excerpt'] ?></p>
                
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--gray-100); padding-top: 16px;">
                    <span style="font-size: 0.8rem; color: var(--gray-400);"><?= $article['date'] ?></span>
                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--green-deep);">Read More →</span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Quick Tips -->
    <div style="background: var(--green-deep); border-radius: var(--radius-xl); padding: 60px 40px; color: white;">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-family: var(--font-serif); font-size: 2rem; margin-bottom: 16px;">Quick Care Tips</h2>
            <p style="color: rgba(255,255,255,0.8); max-width: 500px; margin: 0 auto;">Remember these golden rules of plant care for a thriving indoor jungle.</p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px;">
            <div style="background: rgba(255,255,255,0.1); padding: 24px; border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: 16px;">☝️</div>
                <h4 style="font-size: 1.1rem; margin-bottom: 8px; color: var(--gold);">Check before watering</h4>
                <p style="font-size: 0.9rem; color: rgba(255,255,255,0.8); line-height: 1.5;">Always stick your finger 2 inches into the soil. If it's dry, water. If moist, wait.</p>
            </div>
            
            <div style="background: rgba(255,255,255,0.1); padding: 24px; border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: 16px;">☀️</div>
                <h4 style="font-size: 1.1rem; margin-bottom: 8px; color: var(--gold);">Acclimate to light</h4>
                <p style="font-size: 0.9rem; color: rgba(255,255,255,0.8); line-height: 1.5;">Moving a plant to brighter light? Do it gradually over a week to prevent sunburn.</p>
            </div>
            
            <div style="background: rgba(255,255,255,0.1); padding: 24px; border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: 16px;">🧽</div>
                <h4 style="font-size: 1.1rem; margin-bottom: 8px; color: var(--gold);">Wipe the leaves</h4>
                <p style="font-size: 0.9rem; color: rgba(255,255,255,0.8); line-height: 1.5;">Dusty leaves can't photosynthesize properly. Wipe them monthly with a damp cloth.</p>
            </div>
            
            <div style="background: rgba(255,255,255,0.1); padding: 24px; border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: 16px;">🕳️</div>
                <h4 style="font-size: 1.1rem; margin-bottom: 8px; color: var(--gold);">Drainage is non-negotiable</h4>
                <p style="font-size: 0.9rem; color: rgba(255,255,255,0.8); line-height: 1.5;">Never plant in a pot without drainage holes unless you are using a nursery pot inside it.</p>
            </div>
        </div>
    </div>
    
</div>

<style>
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
