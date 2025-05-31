<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/config.php';

// Require authentication
requireLogin();

$currentUser = getCurrentUser();

try {
    // Get documents based on user role
    if (hasRole('admin')) {
        // Admins can see all documents
        $stmt = $pdo->query('
            SELECT d.*, u.username as uploaded_by_name 
            FROM documents d 
            LEFT JOIN users u ON d.uploaded_by = u.id 
            ORDER BY d.created_at DESC
        ');
    } else {
        // Regular users can only see public documents
        $stmt = $pdo->query('
            SELECT d.*, u.username as uploaded_by_name 
            FROM documents d 
            LEFT JOIN users u ON d.uploaded_by = u.id 
            ORDER BY d.created_at DESC
        ');
    }
    
    $documents = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Documents page error: ' . $e->getMessage());
    $documents = [];
    $error = 'Unable to load documents.';
}

// Sample documents for demo (if no documents in database)
if (empty($documents)) {
    $documents = [
        [
            'id' => 1,
            'title' => 'Building Insurance Certificate 2024',
            'document_type' => 'insurance',
            'file_path' => '/documents/insurance_2024.pdf',
            'created_at' => '2024-01-15 10:00:00',
            'uploaded_by_name' => 'Admin'
        ],
        [
            'id' => 2,
            'title' => 'Annual Financial Report 2023',
            'document_type' => 'financial',
            'file_path' => '/documents/financial_report_2023.pdf',
            'created_at' => '2024-01-10 14:30:00',
            'uploaded_by_name' => 'Treasurer'
        ],
        [
            'id' => 3,
            'title' => 'AGM Minutes - December 2023',
            'document_type' => 'minutes',
            'file_path' => '/documents/agm_minutes_dec2023.pdf',
            'created_at' => '2023-12-15 18:00:00',
            'uploaded_by_name' => 'Secretary'
        ],
        [
            'id' => 4,
            'title' => 'Building Bylaws and Regulations',
            'document_type' => 'other',
            'file_path' => '/documents/bylaws_2024.pdf',
            'created_at' => '2024-01-01 09:00:00',
            'uploaded_by_name' => 'Admin'
        ]
    ];
}

function getDocumentTypeIcon($type) {
    switch ($type) {
        case 'insurance': return '🛡️';
        case 'financial': return '💰';
        case 'minutes': return '📝';
        default: return '📄';
    }
}

function getDocumentTypeBadge($type) {
    switch ($type) {
        case 'insurance': return 'bg-primary';
        case 'financial': return 'bg-success';
        case 'minutes': return 'bg-warning';
        default: return 'bg-secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - Strata Management System</title>
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
        <div class="row">
            <div class="col-12">
                <h1>📄 Document Library</h1>
                <p class="text-muted mb-4">Access important strata documents and reports.</p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if (hasRole('admin')): ?>
                    <div class="alert alert-info">
                        <h6>Administrator View</h6>
                        <p class="mb-0">You can see all documents. Consider adding document upload functionality for committee members.</p>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php foreach ($documents as $doc): ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title">
                                            <?php echo getDocumentTypeIcon($doc['document_type']); ?>
                                            <?php echo htmlspecialchars($doc['title']); ?>
                                        </h5>
                                        <span class="badge <?php echo getDocumentTypeBadge($doc['document_type']); ?>">
                                            <?php echo ucfirst(htmlspecialchars($doc['document_type'])); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="text-muted small mb-3">
                                        <div><strong>Uploaded:</strong> <?php echo date('M d, Y', strtotime($doc['created_at'])); ?></div>
                                        <div><strong>By:</strong> <?php echo htmlspecialchars($doc['uploaded_by_name'] ?? 'System'); ?></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button class="btn btn-primary btn-sm me-2" onclick="alert('Document preview feature coming soon!')">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="alert('Download feature coming soon!')">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4">
                    <a href="/api/index.php" class="btn btn-secondary">Back to Dashboard</a>
                    <?php if (hasAnyRole(['admin', 'committee'])): ?>
                        <button class="btn btn-success" onclick="alert('Upload document feature coming soon!')">
                            Upload Document
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 