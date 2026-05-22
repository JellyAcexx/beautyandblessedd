<?php
include 'database.php';
session_start();

if (!isset($_SESSION['register_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

if (!isset($_POST['ids'])) {
    http_response_code(400);
    echo "No items selected.";
    exit();
}

$ids = json_decode($_POST['ids'], true);

if (empty($ids)) {
    echo "No valid IDs provided.";
    exit();
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));

$query = "DELETE FROM cart_items WHERE cart_items_id IN ($placeholders)";
$stmt = $conn->prepare($query);

$stmt->bind_param($types, ...$ids);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Deleted successfully";
} else {
    echo "No rows deleted.";
}

$stmt->close();
$conn->close();
?>