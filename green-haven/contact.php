<?php
// ============================================================
// GREEN HAVEN - Contact Us
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $type = trim($_POST['type'] ?? 'inquiry');
        $message = trim($_POST['message'] ?? '');
        $user_id = $_SESSION['user_id'] ?? null;
        
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO support_tickets (user_id, name, email, subject, type, message) VALUES (?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$user_id, $name, $email, $subject, $type, $message]);
                $success = true;
            } catch (Exception $e) {
                $error = 'Something went wrong. Please try again later.';
            }
        }
    }
}

$metaTitle = 'Contact Us – ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<div class="page-header" style="background: var(--gray-50); padding: 80px 20px 60px;">
    <h1 style="font-family: var(--font-serif); font-size: 3rem; color: var(--green-deep); margin-bottom: 20px;">Contact Us</h1>
    <p style="font-size: 1.1rem; color: var(--gray-600); max-width: 600px; margin: 0 auto;">Have a question about your plant? Need help with an order? We're here for you.</p>
</div>

<div class="section" style="max-width: 1200px; margin: 0 auto; padding-top: 0;">
    
    <div style="display: grid; grid-template-columns: 1fr; gap: 0; background: white; border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-md); transform: translateY(-30px);">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            
            <!-- Contact Info -->
            <div style="background: var(--green-deep); color: white; padding: 60px 40px;">
                <h2 style="font-family: var(--font-serif); font-size: 2rem; margin-bottom: 32px;">Get in Touch</h2>
                
                <div style="display: flex; flex-direction: column; gap: 24px; margin-bottom: 40px;">
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="font-size: 1.5rem; color: var(--gold);">📍</div>
                        <div>
                            <div style="font-weight: 700; margin-bottom: 4px;">Address</div>
                            <div style="color: rgba(255,255,255,0.8); line-height: 1.6;">123 Botanical Lane, Green Park<br>New Delhi, 110016<br>India</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="font-size: 1.5rem; color: var(--gold);">📞</div>
                        <div>
                            <div style="font-weight: 700; margin-bottom: 4px;">Phone</div>
                            <div style="color: rgba(255,255,255,0.8); line-height: 1.6;">+91 98765 43210</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="font-size: 1.5rem; color: var(--gold);">✉️</div>
                        <div>
                            <div style="font-weight: 700; margin-bottom: 4px;">Email</div>
                            <div style="color: rgba(255,255,255,0.8); line-height: 1.6;">support@greenhaven.com</div>
                        </div>
                    </div>
                </div>
                
                <div style="padding-top: 32px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <h3 style="font-size: 1.1rem; margin-bottom: 16px;">Opening Hours</h3>
                    <div style="display: flex; justify-content: space-between; color: rgba(255,255,255,0.8); margin-bottom: 8px;">
                        <span>Mon - Fri</span><span>9:00 AM - 6:00 PM</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; color: rgba(255,255,255,0.8);">
                        <span>Sat - Sun</span><span>10:00 AM - 4:00 PM</span>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div style="padding: 60px 40px;">
                <?php if ($success): ?>
                    <div style="text-align: center; padding: 40px 20px;">
                        <div style="font-size: 4rem; margin-bottom: 20px;">✅</div>
                        <h3 style="color: var(--green-deep); margin-bottom: 12px; font-size: 1.5rem;">Message Sent!</h3>
                        <p style="color: var(--gray-600);">Thank you for reaching out. Our support team will get back to you within 24 hours.</p>
                        <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline" style="margin-top: 24px;">Send Another Message</a>
                    </div>
                <?php else: ?>
                    <h2 style="font-family: var(--font-serif); font-size: 1.8rem; color: var(--green-deep); margin-bottom: 24px;">Send us a message</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error" style="margin-bottom: 24px;">
                            <?= sanitize($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        
                        <div class="form-group">
                            <label class="form-label" for="name">Your Name <span style="color:var(--red);">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= sanitize($_POST['name'] ?? ($_SESSION['user_name'] ?? '')) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address <span style="color:var(--red);">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" value="<?= sanitize($_POST['email'] ?? ($_SESSION['user_email'] ?? '')) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="type">Inquiry Type</label>
                            <select id="type" name="type" class="form-control">
                                <option value="inquiry" <?= ($_POST['type'] ?? '') == 'inquiry' ? 'selected' : '' ?>>General Inquiry</option>
                                <option value="order" <?= ($_POST['type'] ?? '') == 'order' ? 'selected' : '' ?>>Order Issue</option>
                                <option value="care" <?= ($_POST['type'] ?? '') == 'care' ? 'selected' : '' ?>>Plant Care Help</option>
                                <option value="complaint" <?= ($_POST['type'] ?? '') == 'complaint' ? 'selected' : '' ?>>Complaint</option>
                                <option value="other" <?= ($_POST['type'] ?? '') == 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="subject">Subject <span style="color:var(--red);">*</span></label>
                            <input type="text" id="subject" name="subject" class="form-control" value="<?= sanitize($_POST['subject'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="message">Message <span style="color:var(--red);">*</span></label>
                            <textarea id="message" name="message" class="form-control" style="min-height: 120px;" required><?= sanitize($_POST['message'] ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-gold btn-full btn-lg">Send Message</button>
                    </form>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
    
    <!-- FAQ Section -->
    <div style="margin-top: 80px; max-width: 800px; margin-left: auto; margin-right: auto;">
        <div class="section-header text-center">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <div class="section-divider" style="margin: 0 auto;"></div>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: 20px;">
                <h4 style="color: var(--green-deep); margin-bottom: 8px;">Do you deliver all across India?</h4>
                <p style="color: var(--gray-600); font-size: 0.9rem;">Yes, we deliver to all major pin codes across India using our specialized plant-safe packaging.</p>
            </div>
            <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: 20px;">
                <h4 style="color: var(--green-deep); margin-bottom: 8px;">What if my plant arrives damaged?</h4>
                <p style="color: var(--gray-600); font-size: 0.9rem;">We offer a 7-day transit guarantee. If your plant arrives damaged, send us a photo within 24 hours and we'll send a free replacement.</p>
            </div>
            <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: 20px;">
                <h4 style="color: var(--green-deep); margin-bottom: 8px;">Do the plants come with pots?</h4>
                <p style="color: var(--gray-600); font-size: 0.9rem;">All plants come in a standard nursery grower pot. You can choose to upgrade to a premium ceramic or terracotta pot on the product page.</p>
            </div>
            <div style="border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: 20px;">
                <h4 style="color: var(--green-deep); margin-bottom: 8px;">How do I know how to take care of my new plant?</h4>
                <p style="color: var(--gray-600); font-size: 0.9rem;">Every plant order includes a detailed physical care card. You can also refer to the Care Guide tab on the product page or check our Tips section.</p>
            </div>
        </div>
    </div>
    
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
