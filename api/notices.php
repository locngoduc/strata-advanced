<?php
require_once __DIR__ . '/database/config.php';
require_once __DIR__ . '/includes/session.php';

header('Content-Type: application/json');

try {
    // Get important notices from the database
    $stmt = $pdo->query('SELECT * FROM notices WHERE is_important = 1 ORDER BY created_at DESC LIMIT 3');
    $notices = $stmt->fetchAll();

    $html = '';
    foreach ($notices as $notice) {
        $html .= sprintf(
            '<div class="notice-item mb-3">
                <h6 class="text-danger">%s</h6>
                <p class="small">%s</p>
                <small class="text-muted">Posted: %s</small>
            </div>',
            htmlspecialchars($notice['title']),
            htmlspecialchars($notice['content']),
            date('M d, Y', strtotime($notice['created_at']))
        );
    }

    echo json_encode(['html' => $html]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?> 