<?php
require_once __DIR__ . '/../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    http_response_code(403);
    echo '<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owners Directory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome, Owner!</h1>
        <p>This is the Owners Directory page. (Add your content here.)</p>
    </div>
</body>
</html> 