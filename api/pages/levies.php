<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/config.php';

// Require authentication
requireLogin();

$currentUser = getCurrentUser();

// Get levies data
try {
    if (hasRole('admin') || hasRole('committee')) {
        // Admins and committee can see all levies
        $stmt = $pdo->query('
            SELECT l.*, u.username as owner_name, un.unit_number 
            FROM levies l 
            LEFT JOIN units un ON l.unit_id = un.id 
            LEFT JOIN users u ON un.owner_id = u.id 
            ORDER BY l.due_date DESC
        ');
    } else {
        // Owners can only see their own levies
        $stmt = $pdo->prepare('
            SELECT l.*, u.username as owner_name, un.unit_number 
            FROM levies l 
            LEFT JOIN units un ON l.unit_id = un.id 
            LEFT JOIN users u ON un.owner_id = u.id 
            WHERE un.owner_id = ?
            ORDER BY l.due_date DESC
        ');
        $stmt->execute([$currentUser['id']]);
    }
    
    $levies = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Levies page error: ' . $e->getMessage());
    $levies = [];
    $error = 'Unable to load levy information.';
}

// Sample data if no levies in database
if (empty($levies)) {
    $levies = [
        [
            'id' => 1,
            'amount' => 450.00,
            'due_date' => '2024-02-15',
            'status' => 'pending',
            'unit_number' => '101',
            'owner_name' => 'John Smith',
            'created_at' => '2024-01-15 10:00:00'
        ],
        [
            'id' => 2,
            'amount' => 380.00,
            'due_date' => '2024-01-15',
            'status' => 'paid',
            'unit_number' => '102',
            'owner_name' => 'Jane Doe',
            'created_at' => '2023-12-15 10:00:00'
        ],
        [
            'id' => 3,
            'amount' => 525.00,
            'due_date' => '2024-01-10',
            'status' => 'overdue',
            'unit_number' => '201',
            'owner_name' => 'Mike Johnson',
            'created_at' => '2023-12-10 10:00:00'
        ],
        [
            'id' => 4,
            'amount' => 410.00,
            'due_date' => '2024-03-15',
            'status' => 'pending',
            'unit_number' => '202',
            'owner_name' => 'Sarah Wilson',
            'created_at' => '2024-02-15 10:00:00'
        ]
    ];
}

function getStatusBadge($status) {
    switch ($status) {
        case 'pending': return 'bg-warning text-dark';
        case 'paid': return 'bg-success';
        case 'overdue': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function getDaysUntilDue($dueDate) {
    $today = new DateTime();
    $due = new DateTime($dueDate);
    $diff = $today->diff($due);
    
    if ($due < $today) {
        return -$diff->days; // Negative for overdue
    } else {
        return $diff->days; // Positive for days remaining
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Levies - Strata Management System</title>
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
        <h1>💰 Levy Management</h1>
        <p class="text-muted mb-4">
            <?php echo hasAnyRole(['admin', 'committee']) ? 'Manage strata levies and payments for all units.' : 'View and pay your strata levies.'; ?>
        </p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Levy Summary Cards -->
        <div class="row mb-4">
            <?php
            $totalPending = 0;
            $totalOverdue = 0;
            $totalPaid = 0;
            
            foreach ($levies as $levy) {
                switch ($levy['status']) {
                    case 'pending':
                        $totalPending += $levy['amount'];
                        break;
                    case 'overdue':
                        $totalOverdue += $levy['amount'];
                        break;
                    case 'paid':
                        $totalPaid += $levy['amount'];
                        break;
                }
            }
            ?>
            
            <div class="col-md-4">
                <div class="card bg-warning bg-opacity-25">
                    <div class="card-body text-center">
                        <h4 class="text-warning">$<?php echo number_format($totalPending, 2); ?></h4>
                        <p class="mb-0">Pending Payments</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-danger bg-opacity-25">
                    <div class="card-body text-center">
                        <h4 class="text-danger">$<?php echo number_format($totalOverdue, 2); ?></h4>
                        <p class="mb-0">Overdue Payments</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-success bg-opacity-25">
                    <div class="card-body text-center">
                        <h4 class="text-success">$<?php echo number_format($totalPaid, 2); ?></h4>
                        <p class="mb-0">Paid This Period</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Levies Table -->
        <div class="card">
            <div class="card-header">
                <h5>
                    <?php echo hasAnyRole(['admin', 'committee']) ? 'All Levy Notices' : 'Your Levy Notices'; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($levies)): ?>
                    <div class="text-center text-muted py-4">
                        <p>No levy notices found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                        <th>Unit</th>
                                        <th>Owner</th>
                                    <?php endif; ?>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Days</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($levies as $levy): ?>
                                    <?php $daysUntilDue = getDaysUntilDue($levy['due_date']); ?>
                                    <tr>
                                        <?php if (hasAnyRole(['admin', 'committee'])): ?>
                                            <td><?php echo htmlspecialchars($levy['unit_number'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($levy['owner_name'] ?? 'Unknown'); ?></td>
                                        <?php endif; ?>
                                        <td><strong>$<?php echo number_format($levy['amount'], 2); ?></strong></td>
                                        <td><?php echo date('M d, Y', strtotime($levy['due_date'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadge($levy['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($levy['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($daysUntilDue < 0): ?>
                                                <span class="text-danger"><?php echo abs($daysUntilDue); ?> days overdue</span>
                                            <?php elseif ($daysUntilDue == 0): ?>
                                                <span class="text-warning">Due today</span>
                                            <?php else: ?>
                                                <span class="text-muted"><?php echo $daysUntilDue; ?> days remaining</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($levy['status'] === 'pending' || $levy['status'] === 'overdue'): ?>
                                                <button class="btn btn-primary btn-sm" 
                                                        onclick="alert('Payment processing feature coming soon!\n\nAmount: $<?php echo number_format($levy['amount'], 2); ?>\nDue: <?php echo date('M d, Y', strtotime($levy['due_date'])); ?>')">
                                                    Pay Now
                                                </button>
                                            <?php elseif ($levy['status'] === 'paid'): ?>
                                                <button class="btn btn-outline-success btn-sm" 
                                                        onclick="alert('Receipt download feature coming soon!')">
                                                    Receipt
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Information -->
        <?php if (!hasRole('admin')): ?>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6>Payment Information</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Bank Details:</strong></p>
                            <ul class="list-unstyled">
                                <li><strong>BSB:</strong> 123-456</li>
                                <li><strong>Account:</strong> 987654321</li>
                                <li><strong>Name:</strong> Strata Management Fund</li>
                                <li><strong>Reference:</strong> Unit <?php echo htmlspecialchars($currentUser['username'] ?? 'XXX'); ?></li>
                            </ul>
                            <small class="text-muted">Please include your unit number as the payment reference.</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6>Payment Options</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li>💳 <strong>Online Banking:</strong> Use the bank details provided</li>
                                <li>🏧 <strong>Direct Debit:</strong> Contact the strata manager</li>
                                <li>💻 <strong>Online Portal:</strong> Pay now button (coming soon)</li>
                                <li>📞 <strong>Phone:</strong> Call (02) 1234 5678</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="/api/index.php" class="btn btn-secondary">Back to Dashboard</a>
            <?php if (hasRole('admin')): ?>
                <button class="btn btn-success" onclick="alert('Generate levy notices feature coming soon!')">
                    Generate New Levy Notice
                </button>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 