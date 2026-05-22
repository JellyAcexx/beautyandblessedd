<?php
include 'database.php';

$today = date('Y-m-d'); // today's date

$query = "
    SELECT r.reservation_id,
           reg.register_fname,
           reg.register_lname,
           r.reservation_date,
           (SELECT SUM(quantity) 
            FROM reservation_items ri 
            WHERE ri.reservation_id = r.reservation_id) AS total_items
    FROM reservations r
    JOIN registers_tb reg ON r.register_id = reg.register_id
    WHERE r.status = 'pending' 
      AND DATE(r.pickup_date) = ?
    ORDER BY r.reservation_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $today);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
while($row = $result->fetch_assoc()){
    $row['customer'] = $row['register_fname'] . ' ' . $row['register_lname'];
    $reservations[] = $row;
}

// Return JSON for JS
header('Content-Type: application/json');
echo json_encode($reservations);
