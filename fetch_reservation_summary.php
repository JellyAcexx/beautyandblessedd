<?php
session_start();
include 'database.php';
header('Content-Type: application/json');

// ✅ Check login
if (!isset($_SESSION['login_id']) || !isset($_SESSION['register_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must log in first.']);
    exit;
}

$register_id = (int) $_SESSION['register_id'];
$reservation_id = isset($_GET['reservation_id']) ? (int) $_GET['reservation_id'] : 0;

if ($reservation_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid reservation ID.']);
    exit;
}

try {
    // 🌸 Fetch reservation + customer info
    $sql_summary = "
        SELECT 
            r.reservation_id,
            r.reservation_date,
            r.pickup_date,
            r.total,
            r.status,
            CONCAT(u.register_fname, ' ', u.register_lname) AS customer_name
        FROM reservations r
        INNER JOIN registers_tb u ON r.register_id = u.register_id
        WHERE r.reservation_id = ? AND r.register_id = ?
    ";
    $stmt_sum = $conn->prepare($sql_summary);
    $stmt_sum->bind_param("ii", $reservation_id, $register_id);
    $stmt_sum->execute();
    $summary_result = $stmt_sum->get_result()->fetch_assoc();

    if (!$summary_result) {
        echo json_encode(['success' => false, 'message' => 'Reservation not found.']);
        exit;
    }

    // 🌸 Fetch product items under this reservation
    $sql_items = "
        SELECT 
            p.product_name, 
            ri.quantity, 
            (p.price * ri.quantity) AS amount
        FROM reservation_items ri
        INNER JOIN products p ON ri.product_id = p.product_id
        WHERE ri.reservation_id = ?
    ";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $reservation_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();

    $items = [];
    while ($row = $items_result->fetch_assoc()) {
        $items[] = [
            'product_name' => $row['product_name'],
            'quantity' => $row['quantity'],
            'amount' => number_format($row['amount'], 2)
        ];
    }

    // ✅ Return everything in structured JSON
    echo json_encode([
        'success' => true,
        'reservation' => [
            'customer_name' => $summary_result['customer_name'],
            'status' => $summary_result['status'],
            'reservation_date' => date("F d, Y", strtotime($summary_result['reservation_date'])),
            'pickup_date' => date("F d, Y", strtotime($summary_result['pickup_date'])),
            'items' => $items,
            'total' => number_format($summary_result['total'], 2)
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching summary: ' . $e->getMessage()]);
}

if (isset($stmt_sum)) $stmt_sum->close();
if (isset($stmt_items)) $stmt_items->close();
$conn->close();
?>
