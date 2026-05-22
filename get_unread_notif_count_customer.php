<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['register_id']) || !is_numeric($_SESSION['register_id'])) {
    echo json_encode(['count' => 0]);
    exit();
}

$customer_id = (int)$_SESSION['register_id'];

include 'database.php';

$sql = "SELECT COUNT(*) AS unread_count 
        FROM notifcustomer 
        WHERE register_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

echo json_encode(['count' => (int)$row['unread_count']]);
