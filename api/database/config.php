<?php
// Database configuration
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'strata_db';
$db_user = getenv('DB_USER') ?: 'postgres';
$db_pass = getenv('DB_PASS') ?: '';
$db_port = getenv('DB_PORT') ?: '5432';
$db_endpoint = getenv('DB_ENDPOINT') ?: '';

try {
    $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_pass options='endpoint=$db_endpoint' sslmode=require";
    $pdo = new PDO(
        "pgsql:$connection_string",
        null,
        null,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "Connected successfully";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 