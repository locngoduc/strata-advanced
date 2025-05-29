<?php
// Database configuration
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'strata_db';
$db_user = getenv('DB_USER') ?: 'postgres';
$db_pass = getenv('DB_PASS') ?: '';
$db_port = getenv('DB_PORT') ?: '5432';
$db_endpoint = getenv('DB_ENDPOINT') ?: '';

$connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_pass options='endpoint=$db_endpoint' sslmode=require";

$dbconn = pg_connect($connection_string);

if (!$dbconn) {
    die("Connection failed: " . pg_last_error());
}
echo "Connected successfully";
?> 