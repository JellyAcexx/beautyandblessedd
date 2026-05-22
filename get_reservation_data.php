<?php
include 'database.php';

$custId = $_GET['custId'] ?? 0;

$resQuery = $conn->prepare("SELECT r.*, ri.quantity, p.product_name, p.price 
                             FROM reservations r 
                             LEFT JOIN reservation_items ri ON r.reservation_id = ri.reservation_id
                             LEFT JOIN products p ON ri.product_id = p.product_id
                             WHERE r.register_id = ?");
$resQuery->bind_param("i", $custId);
$resQuery->execute();
$result = $resQuery->get_result();

$reservationItems = [];
while($row = $result->fetch_assoc()){
    $resId = $row['reservation_id'];
    if(!isset($reservationItems[$custId][$resId])){
        $reservationItems[$custId][$resId] = [];
    }
    $reservationItems[$custId][$resId][] = $row;
}

echo json_encode($reservationItems);
