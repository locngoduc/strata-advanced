<?php
require_once __DIR__ . '/database/config.php';
require_once __DIR__ . '/includes/session.php';

header('Content-Type: application/json');

try {
    // Get recent updates from the database
    $stmt = $pdo->query('SELECT * FROM updates ORDER BY created_at DESC LIMIT 5');
    $updates = $stmt->fetchAll();

    $html = '';
    foreach ($updates as $update) {
        $html .= sprintf(
            '<div class="update-item mb-2">
                <h6>%s</h6>
                <p class="text-muted small">%s</p>
            </div>',
            htmlspecialchars($update['title']),
            htmlspecialchars($update['content'])
        );
    }

    echo json_encode(['html' => $html]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?> 