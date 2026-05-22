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


// ✅ Decode JSON data from frontend
$data = json_decode(file_get_contents('php://input'), true);
$cart_items = $data['cart_items'] ?? [];
$total = isset($data['total']) ? (float) $data['total'] : 0.00;

if (empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'No items selected.']);
    exit;
}

try {
    $conn->begin_transaction();

    // ✅ Fetch product_id and quantity for selected cart_items
    $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
    $sql_cart = "SELECT cart_items_id, product_id, quantity FROM cart_items WHERE cart_items_id IN ($placeholders)";
    $stmt_cart = $conn->prepare($sql_cart);
    $types = str_repeat('i', count($cart_items));
    $stmt_cart->bind_param($types, ...$cart_items);
    $stmt_cart->execute();
    $cart_result = $stmt_cart->get_result();

    // ✅ STOCK CHECK MUNA DITO
    $outOfStock = [];
    $cartItemsData = [];

    while ($row = $cart_result->fetch_assoc()) {
        $product_id = (int)$row['product_id'];
        $quantity   = (int)$row['quantity'];

        // palitan ang query na ito kung nasa ibang table ang stock mo (e.g. inventory table)
        $stmt_stock = $conn->prepare("
            SELECT p.product_name, i.stocks
            FROM products p
            INNER JOIN inventory i ON i.product_id = p.product_id
            WHERE p.product_id = ?
        ");
        $stmt_stock->bind_param("i", $product_id);
        $stmt_stock->execute();
        $stock_res = $stmt_stock->get_result()->fetch_assoc();
        $stmt_stock->close();

        $available = isset($stock_res['stocks']) ? (int)$stock_res['stocks'] : 0;

        if ($quantity > $available) {
            $outOfStock[] = $stock_res['product_name'] . " (available: {$available}, requested: {$quantity})";
        }

        $cartItemsData[] = [
            'product_id' => $product_id,
            'quantity'   => $quantity
        ];
    }

    if (!empty($outOfStock)) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => "Insufficient stock for: " . implode(', ', $outOfStock)
        ]);
        exit;
    }

    // ✅ Insert into reservations table (safe na kasi pasado na sa stock check)
    // binago ko Pending to "pending"
    $sql_reservation = "
        INSERT INTO reservations (register_id, total, status, reservation_date, pickup_date)
        VALUES (?, ?, 'pending', NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY))
    ";
    $stmt_res = $conn->prepare($sql_reservation);
    $stmt_res->bind_param("id", $register_id, $total);
    $stmt_res->execute();
    $reservation_id = $conn->insert_id;

    // ✅ Insert each product into reservation_items
    $sql_item = "INSERT INTO reservation_items (reservation_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);

    foreach ($cartItemsData as $item) {
        $product_id = $item['product_id'];
        $quantity   = $item['quantity'];
        $stmt_item->bind_param("iii", $reservation_id, $product_id, $quantity);
        $stmt_item->execute();
    }

    // ✅ Delete reserved cart items
    $delete_sql = "DELETE FROM cart_items WHERE cart_items_id IN ($placeholders)";
    $stmt_del = $conn->prepare($delete_sql);
    $stmt_del->bind_param($types, ...$cart_items);
    $stmt_del->execute();

    // ✅ Commit transaction
    $conn->commit();

    // 🌸 Fetch customer name and reservation details for summary
    $sql_summary = "
        SELECT r.reservation_date, r.pickup_date, r.total, 
               CONCAT(u.register_fname, ' ', u.register_lname) AS customer_name
        FROM reservations r
        INNER JOIN registers_tb u ON r.register_id = u.register_id
        WHERE r.reservation_id = ?
    ";
    $stmt_sum = $conn->prepare($sql_summary);
    $stmt_sum->bind_param("i", $reservation_id);
    $stmt_sum->execute();
    $summary_result = $stmt_sum->get_result()->fetch_assoc();

    // 🌸 Fetch product details for this reservation
    $sql_items = "
        SELECT p.product_name, ri.quantity, (p.price * ri.quantity) AS amount
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

    // ✅ Return everything to frontend
    echo json_encode([
        'success' => true,
        'message' => 'Reservation created successfully!',
        'reservation_id' => $reservation_id,
        'reservation' => [
            'customer_name' => $summary_result['customer_name'],
            'reservation_date' => date("F d, Y", strtotime($summary_result['reservation_date'])),
            'pickup_date' => date("F d, Y", strtotime($summary_result['pickup_date'])),
            'items' => $items,
            'total' => number_format($summary_result['total'], 2)
        ]
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error creating reservation: ' . $e->getMessage()]);
}

// ✅ Cleanup
if (isset($stmt_res))   $stmt_res->close();
if (isset($stmt_cart))  $stmt_cart->close();
if (isset($stmt_item))  $stmt_item->close();
if (isset($stmt_del))   $stmt_del->close();
if (isset($stmt_sum))   $stmt_sum->close();
if (isset($stmt_items)) $stmt_items->close();
$conn->close();
?>