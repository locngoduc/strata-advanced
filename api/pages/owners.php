<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/config.php';

// Require authentication and appropriate role
requireRole(['owner', 'committee', 'admin']);

$currentUser = getCurrentUser();

try {
    // Get owners data based on user role
    if (hasRole('admin')) {
        // Admins can see all owners with detailed info
        $stmt = $pdo->query('
            SELECT u.id, u.username, u.email, u.role, u.created_at,
                   un.unit_number, un.floor_number, un.unit_entitlements
            FROM users u 
            LEFT JOIN units un ON u.id = un.owner_id 
            WHERE u.role IN ("owner", "committee", "admin")
            ORDER BY u.role, u.username
        ');
    } elseif (hasRole('committee')) {
        // Committee can see owner names and unit info but not emails
        $stmt = $pdo->query('
            SELECT u.id, u.username, u.role,
                   un.unit_number, un.floor_number, un.unit_entitlements
            FROM users u 
            LEFT JOIN units un ON u.id = un.owner_id 
            WHERE u.role = "owner"
            ORDER BY u.username
        ');
    } else {
        // Owners can only see basic directory (names and unit numbers)
        $stmt = $pdo->query('
            SELECT u.id, u.username,
                   un.unit_number, un.floor_number
            FROM users u 
            LEFT JOIN units un ON u.id = un.owner_id 
            WHERE u.role = "owner"
            ORDER BY un.unit_number
        ');
    }
    
    $owners = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Owners page error: ' . $e->getMessage());
    $owners = [];
    $error = 'Unable to load owners data.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owners Directory - Strata Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/api/index.php">Strata Management</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($currentUser['username']); ?> (<?php echo htmlspecialchars(ucfirst($currentUser['role'])); ?>)</span>
                <a class="nav-link" href="/api/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>Owners Directory</h1>
                <p class="text-muted mb-4">
                    <?php if (hasRole('admin')): ?>
                        Administrative view: All user details
                    <?php elseif (hasRole('committee')): ?>
                        Committee view: Owner contact information
                    <?php else: ?>
                        Directory of property owners
                    <?php endif; ?>
                </p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if (empty($owners)): ?>
                    <div class="alert alert-info">No owners found in the system.</div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <?php if (hasRole('admin')): ?>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Member Since</th>
                                            <?php endif; ?>
                                            <th>Unit Number</th>
                                            <th>Floor</th>
                                            <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                                <th>Entitlements</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($owners as $owner): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($owner['username']); ?></td>
                                                <?php if (hasRole('admin')): ?>
                                                    <td><?php echo htmlspecialchars($owner['email'] ?? 'N/A'); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            echo $owner['role'] === 'admin' ? 'danger' : 
                                                                ($owner['role'] === 'committee' ? 'warning' : 'primary'); 
                                                        ?>">
                                                            <?php echo htmlspecialchars(ucfirst($owner['role'])); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars(date('M d, Y', strtotime($owner['created_at']))); ?></td>
                                                <?php endif; ?>
                                                <td><?php echo htmlspecialchars($owner['unit_number'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($owner['floor_number'] ?? 'N/A'); ?></td>
                                                <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                                    <td><?php echo htmlspecialchars($owner['unit_entitlements'] ?? 'N/A'); ?></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="/api/index.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 