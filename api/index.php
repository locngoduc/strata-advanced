<?php
// api/index.php
require_once __DIR__ . '/includes/session.php';

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strata Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/api/index.php">Strata Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/api/index.php">Home</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/documents.php">Documents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/maintenance.php">Maintenance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/levies.php">Levies</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/api/pages/owners.php">Owners Directory</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <span class="navbar-text me-3">
                                Welcome, <?php echo htmlspecialchars($currentUser['username']); ?>
                                <span class="badge bg-<?php 
                                    echo $currentUser['role'] === 'admin' ? 'danger' : 
                                        ($currentUser['role'] === 'committee' ? 'warning' : 'primary'); 
                                ?>"><?php echo htmlspecialchars(ucfirst($currentUser['role'])); ?></span>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/api/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/api/pages/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/api/pages/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (!isLoggedIn()): ?>
            <div class="alert alert-info">
                <h5>Welcome to Strata Management System</h5>
                <p>Please <a href="/api/pages/login.php" class="alert-link">login</a> or <a href="/api/pages/register.php" class="alert-link">register</a> to access the system.</p>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <h1>Welcome to Strata Management System</h1>
                <p class="lead">Efficiently manage your strata property with our comprehensive management system.</p>
                
                <?php if (isLoggedIn()): ?>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Quick Links</h5>
                                <ul class="list-unstyled">
                                    <li><a href="/documents.php" class="text-decoration-none">📄 View Documents</a></li>
                                    <li><a href="/maintenance.php" class="text-decoration-none">🔧 Submit Maintenance Request</a></li>
                                    <li><a href="/levies.php" class="text-decoration-none">💰 Pay Levies</a></li>
                                    <li><a href="/api/pages/owners.php" class="text-decoration-none">👥 Owners Directory</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Recent Updates</h5>
                                <div id="updates">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Loading updates...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (isLoggedIn()): ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Important Notices</h5>
                        <div id="notices">
                            <div class="text-center">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                Loading notices...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isLoggedIn()): ?>
    <script>
        // Load updates and notices via AJAX only if user is logged in
        fetch('/api/updates.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('updates').innerHTML = data.html;
                } else {
                    document.getElementById('updates').innerHTML = '<div class="text-danger">Error loading updates</div>';
                }
            })
            .catch(error => {
                console.error('Error loading updates:', error);
                document.getElementById('updates').innerHTML = '<div class="text-danger">Error loading updates</div>';
            });

        fetch('/api/notices.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('notices').innerHTML = data.html;
                } else {
                    document.getElementById('notices').innerHTML = '<div class="text-danger">Error loading notices</div>';
                }
            })
            .catch(error => {
                console.error('Error loading notices:', error);
                document.getElementById('notices').innerHTML = '<div class="text-danger">Error loading notices</div>';
            });
    </script>
    <?php endif; ?>
</body>
</html>