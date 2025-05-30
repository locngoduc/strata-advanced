<?php
require_once __DIR__ . '/includes/session.php';

// Simple debug page to check session status
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Debug - Strata Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Session Debug Information</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Login Status</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Is Logged In:</strong> <?php echo isLoggedIn() ? 'YES' : 'NO'; ?></p>
                        <?php if (isLoggedIn()): ?>
                            <?php $user = getCurrentUser(); ?>
                            <p><strong>User ID:</strong> <?php echo htmlspecialchars($user['id'] ?? 'Not set'); ?></p>
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username'] ?? 'Not set'); ?></p>
                            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role'] ?? 'Not set'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Session Data</h5>
                    </div>
                    <div class="card-body">
                        <pre><?php print_r($_SESSION); ?></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Cookie Data</h5>
                    </div>
                    <div class="card-body">
                        <pre><?php print_r($_COOKIE); ?></pre>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Server Info</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>HTTPS:</strong> <?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'YES' : 'NO'; ?></p>
                        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
                        <p><strong>Session Name:</strong> <?php echo session_name(); ?></p>
                        <p><strong>Cookie Secure:</strong> <?php echo ini_get('session.cookie_secure') ? 'YES' : 'NO'; ?></p>
                        <p><strong>Cookie SameSite:</strong> <?php echo ini_get('session.cookie_samesite'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="/api/index.php" class="btn btn-primary">Back to Home</a>
            <?php if (!isLoggedIn()): ?>
                <a href="/api/pages/login.php" class="btn btn-success">Login</a>
            <?php else: ?>
                <a href="/api/logout.php" class="btn btn-danger">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 