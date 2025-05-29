<?php
// api/index.php
require_once __DIR__ . '/includes/session.php';
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
            <a class="navbar-brand" href="/">Strata Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
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
                        <a class="nav-link" href="/api/pages/owners.php">Owners</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <h1>Welcome to Strata Management System</h1>
                <p class="lead">Efficiently manage your strata property with our comprehensive management system.</p>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Quick Links</h5>
                                <ul class="list-unstyled">
                                    <li><a href="/documents.php">View Documents</a></li>
                                    <li><a href="/maintenance.php">Submit Maintenance Request</a></li>
                                    <li><a href="/levies.php">Pay Levies</a></li>
                                    <li><a href="/api/pages/owners.php">Owners Directory</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Recent Updates</h5>
                                <div id="updates">
                                    <!-- Updates will be loaded dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Important Notices</h5>
                        <div id="notices">
                            <!-- Notices will be loaded dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load updates and notices via AJAX
        fetch('/api/updates.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('updates').innerHTML = data.html;
            });

        fetch('/api/notices.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('notices').innerHTML = data.html;
            });
    </script>
</body>
</html>