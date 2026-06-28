<?php
// ============================================================
// GREEN HAVEN - User Registration
// ============================================================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}

$error = '';
$redirect = $_GET['redirect'] ?? '/index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (empty($name) || empty($email) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } else {
            $db = getDB();
            
            // Check if email exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                // Insert user
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
                
                try {
                    $stmt->execute([$name, $email, $phone, $hash]);
                    
                    // Auto login
                    if (loginUser($email, $password)) {
                        flashMessage('success', 'Account created successfully! Welcome to Green Haven.');
                        redirect(SITE_URL . $redirect);
                    }
                } catch (Exception $e) {
                    $error = 'Something went wrong. Please try again later.';
                }
            }
        }
    }
}

$metaTitle = 'Create Account – ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card" style="max-width: 500px;">
        <div class="auth-logo">
            <span class="logo-icon">🌿</span>
            <div class="logo-text">Green<span style="color:var(--gold);">Haven</span></div>
        </div>
        
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Join our community of plant lovers</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?= sanitize($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="redirect" value="<?= sanitize($redirect) ?>">
            
            <div class="form-group">
                <label class="form-label" for="name">Full Name <span style="color:var(--red);">*</span></label>
                <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" value="<?= sanitize($_POST['name'] ?? '') ?>" required autofocus>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address <span style="color:var(--red);">*</span></label>
                <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" value="<?= sanitize($_POST['email'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="phone">Phone Number (Optional)</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="10-digit mobile number" value="<?= sanitize($_POST['phone'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password <span style="color:var(--red);">*</span></label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Toggle password visibility">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password <span style="color:var(--red);">*</span></label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)" aria-label="Toggle password visibility">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 24px;">
                <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 0.8rem; color: var(--gray-600); cursor: pointer; line-height: 1.4;">
                    <input type="checkbox" name="terms" style="accent-color: var(--green-deep); margin-top: 3px;" required>
                    <span>I agree to Green Haven's <a href="#" style="color:var(--green-deep); text-decoration:underline;">Terms of Service</a> and <a href="#" style="color:var(--green-deep); text-decoration:underline;">Privacy Policy</a>.</span>
                </label>
            </div>
            
            <button type="submit" class="btn btn-green btn-full btn-lg">Create Account</button>
        </form>
        
        <div class="auth-divider">
            <span>Already have an account?</span>
        </div>
        
        <div class="auth-links">
            <a href="<?= SITE_URL ?>/auth/login.php?redirect=<?= urlencode($redirect) ?>">Sign In Instead</a>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22"/></svg>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
