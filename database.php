<?php
$databaseUrl = getenv('DATABASE_URL');

if ($databaseUrl) {
    $databaseConfig = parse_url($databaseUrl);

    $host = $databaseConfig['host'] ?? 'localhost';
    $user = $databaseConfig['user'] ?? 'root';
    $pass = $databaseConfig['pass'] ?? '';
    $db   = isset($databaseConfig['path']) ? ltrim($databaseConfig['path'], '/') : 'u937180775_bblessed_db';
    $port = isset($databaseConfig['port']) ? (int) $databaseConfig['port'] : 3306;
} else {
    $host = getenv('DB_HOST') ?: "localhost";
    $user = getenv('DB_USER') ?: "root";
    $pass = getenv('DB_PASSWORD') ?: "";
    $db   = getenv('DB_NAME') ?: "u937180775_bblessed_db";
    $port = (int) (getenv('DB_PORT') ?: 3306);
}

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
