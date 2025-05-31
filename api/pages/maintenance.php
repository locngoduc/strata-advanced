<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/config.php';

// Require authentication
requireLogin();

$currentUser = getCurrentUser();
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrfToken)) {
        $error = 'Invalid request. Please try again.';
    } else {
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $unit_id = $_POST['unit_id'] ?? null;

        if (empty($title) || empty($description)) {
            $error = 'Please fill in all fields.';
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO maintenance_requests (unit_id, title, description, created_by) VALUES (?, ?, ?, ?)');
                if ($stmt->execute([$unit_id, $title, $description, $currentUser['id']])) {
                    $success = 'Maintenance request submitted successfully!';
                } else {
                    $error = 'Failed to submit maintenance request.';
                }
            } catch (PDOException $e) {
                error_log('Maintenance request error: ' . $e->getMessage());
                $error = 'Database error. Please try again.';
            }
        }
    }
}

// Get maintenance requests
try {
    if (hasRole('admin') || hasRole('committee')) {
        // Admins and committee can see all requests
        $stmt = $pdo->query('
            SELECT mr.*, u.username as created_by_name, un.unit_number 
            FROM maintenance_requests mr 
            LEFT JOIN users u ON mr.created_by = u.id 
            LEFT JOIN units un ON mr.unit_id = un.id 
            ORDER BY mr.created_at DESC
        ');
    } else {
        // Owners can only see their own requests
        $stmt = $pdo->prepare('
            SELECT mr.*, u.username as created_by_name, un.unit_number 
            FROM maintenance_requests mr 
            LEFT JOIN users u ON mr.created_by = u.id 
            LEFT JOIN units un ON mr.unit_id = un.id 
            WHERE mr.created_by = ?
            ORDER BY mr.created_at DESC
        ');
        $stmt->execute([$currentUser['id']]);
    }
    
    $requests = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Maintenance requests error: ' . $e->getMessage());
    $requests = [];
}

// Sample data if no requests in database
if (empty($requests)) {
    $requests = [
        [
            'id' => 1,
            'title' => 'Leaking tap in bathroom',
            'description' => 'The bathroom tap is constantly dripping and needs repair.',
            'status' => 'pending',
            'unit_number' => '101',
            'created_by_name' => 'John Smith',
            'created_at' => '2024-01-15 10:00:00'
        ],
        [
            'id' => 2,
            'title' => 'Elevator making strange noises',
            'description' => 'The elevator is making unusual noises when moving between floors.',
            'status' => 'in_progress',
            'unit_number' => 'Common Area',
            'created_by_name' => 'Jane Doe',
            'created_at' => '2024-01-10 14:30:00'
        ],
        [
            'id' => 3,
            'title' => 'Broken light in parking garage',
            'description' => 'Light fixture in parking space B15 is not working.',
            'status' => 'completed',
            'unit_number' => 'Parking',
            'created_by_name' => 'Mike Johnson',
            'created_at' => '2024-01-05 16:45:00'
        ]
    ];
}

function getStatusBadge($status) {
    switch ($status) {
        case 'pending': return 'bg-warning text-dark';
        case 'in_progress': return 'bg-info';
        case 'completed': return 'bg-success';
        default: return 'bg-secondary';
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - Strata Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/api/index.php">Strata Management</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($currentUser['username']); ?>
                    <span class="badge bg-<?php 
                        echo $currentUser['role'] === 'admin' ? 'danger' : 
                            ($currentUser['role'] === 'committee' ? 'warning' : 'primary'); 
                    ?>"><?php echo htmlspecialchars(ucfirst($currentUser['role'])); ?></span>
                </span>
                <a class="nav-link" href="/api/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>🔧 Maintenance Requests</h1>
        <p class="text-muted mb-4">Submit and track maintenance requests for the building.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Submit New Request Form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Submit New Request</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Issue Title *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Location</label>
                                <select class="form-select" id="unit_id" name="unit_id">
                                    <option value="">Select location (optional)</option>
                                    <option value="1">Unit 101</option>
                                    <option value="2">Unit 102</option>
                                    <option value="3">Unit 201</option>
                                    <option value="4">Unit 202</option>
                                    <option value="">Common Area</option>
                                    <option value="">Parking Garage</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Request Statistics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Request Statistics</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $pending = array_filter($requests, fn($r) => $r['status'] === 'pending');
                        $inProgress = array_filter($requests, fn($r) => $r['status'] === 'in_progress');
                        $completed = array_filter($requests, fn($r) => $r['status'] === 'completed');
                        ?>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-3 bg-warning bg-opacity-25 rounded">
                                    <h3 class="text-warning"><?php echo count($pending); ?></h3>
                                    <small>Pending</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-info bg-opacity-25 rounded">
                                    <h3 class="text-info"><?php echo count($inProgress); ?></h3>
                                    <small>In Progress</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-success bg-opacity-25 rounded">
                                    <h3 class="text-success"><?php echo count($completed); ?></h3>
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests List -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <?php echo hasAnyRole(['admin', 'committee']) ? 'All Maintenance Requests' : 'Your Maintenance Requests'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($requests)): ?>
                            <div class="text-center text-muted py-4">
                                <p>No maintenance requests found.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Location</th>
                                            <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                                <th>Requested By</th>
                                            <?php endif; ?>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($requests as $request): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($request['title']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($request['description']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo getStatusBadge($request['status']); ?>">
                                                        <?php echo ucfirst(htmlspecialchars($request['status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($request['unit_number'] ?? 'Not specified'); ?></td>
                                                <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                                    <td><?php echo htmlspecialchars($request['created_by_name'] ?? 'Unknown'); ?></td>
                                                <?php endif; ?>
                                                <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="/api/index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 