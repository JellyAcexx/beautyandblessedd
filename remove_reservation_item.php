<?php
session_start();
include 'database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['register_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit();
}

if (!isset($_POST['reservation_item_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing item ID.']);
    exit();
}

$item_id = (int) $_POST['reservation_item_id'];
$register_id = (int) $_SESSION['register_id'];

// Verify ownership through join
$query = "
    SELECT ri.reservation_item_id, ri.reservation_id 
    FROM reservation_items ri
    INNER JOIN reservations r ON ri.reservation_id = r.reservation_id
    WHERE ri.reservation_item_id = ? AND r.register_id = ?
";
$check = $conn->prepare($query);
$check->bind_param("ii", $item_id, $register_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Item not found or unauthorized.']);
    exit();
}

$row = $result->fetch_assoc();
$reservation_id = (int) $row['reservation_id'];

// ✅ Delete the item
$delete = $conn->prepare("DELETE FROM reservation_items WHERE reservation_item_id = ?");
$delete->bind_param("i", $item_id);
if ($delete->execute()) {
    // ✅ Update total
    $update = $conn->prepare("
        UPDATE reservations
        SET total_amount = (
            SELECT COALESCE(SUM(p.price * ri.quantity), 0)
            FROM reservation_items ri
            INNER JOIN products p ON ri.product_id = p.product_id
            WHERE ri.reservation_id = ?
        )
        WHERE reservation_id = ?
    ");
    $update->bind_param("ii", $reservation_id, $reservation_id);
    $update->execute();
    
    // ✅ Return new total
    $getTotal = $conn->prepare("SELECT total_amount FROM reservations WHERE reservation_id = ?");
    $getTotal->bind_param("i", $reservation_id);
    $getTotal->execute();
    $res = $getTotal->get_result();
    $newTotal = 0;
    if ($r = $res->fetch_assoc()) {
        $newTotal = number_format($r['total_amount'], 2);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Item removed successfully.',
        'new_total' => $newTotal
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item.']);
}
?>
