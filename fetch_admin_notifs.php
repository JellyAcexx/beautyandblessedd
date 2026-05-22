<?php
include 'database.php';
session_start(); // Kung naka-session based admin login ka

$admin_id = 1; // Palitan mo dito ang admin register_id mo, o kunin sa $_SESSION kung dynamic
$sql = "SELECT * FROM notifadmin WHERE register_id = ? ORDER BY created_at DESC LIMIT 20";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
echo json_encode($notifications); // Para sa AJAX/JS
?>
