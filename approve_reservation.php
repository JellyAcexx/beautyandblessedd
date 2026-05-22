<?php
include 'database.php';

// Set header
header('Content-Type: application/json');

// Decode input
$data = json_decode(file_get_contents('php://input'), true);
$reservation_id = $data['reservation_id'] ?? null;

if (!$reservation_id) {
    echo json_encode(['success' => false, 'message' => 'No reservation ID provided']);
    exit;
}

// 1. Get reservation info
$resQuery = $conn->prepare("
    SELECT r.*, c.register_fname, c.register_lname, c.register_email, c.register_id
    FROM reservations r
    INNER JOIN registers_tb c ON r.register_id = c.register_id
    WHERE r.reservation_id = ?
");
$resQuery->bind_param("i", $reservation_id);
$resQuery->execute();
$res = $resQuery->get_result()->fetch_assoc();

if (!$res) {
    echo json_encode(['success' => false, 'message' => 'Reservation not found']);
    exit;
}

$customer_id    = $res['register_id'];
$customer_fname = $res['register_fname'];
$customer_lname = $res['register_lname'];
$customer_email = $res['register_email'];


// 2. Fetch items with product info (price + name)
$itemsQuery = $conn->prepare("
    SELECT ri.quantity, p.product_name, p.price, p.product_id
    FROM reservation_items ri
    LEFT JOIN products p ON ri.product_id = p.product_id
    WHERE ri.reservation_id = ?
");
$itemsQuery->bind_param("i", $reservation_id);
$itemsQuery->execute();
$resItems = $itemsQuery->get_result()->fetch_all(MYSQLI_ASSOC);

if (!is_array($resItems) || count($resItems) === 0) {
    echo json_encode(['success' => false, 'message' => 'No items in reservation']);
    exit;
}

// 3. Calculate total
$totalAmount = 0;
foreach ($resItems as $item) {
    $totalAmount += $item['quantity'] * $item['price'];
}

// 4. Insert into purchase
$purchaseStmt = $conn->prepare("
    INSERT INTO purchase (walk_in_id, reservation_id, totalAmount, purchaseMethod, purchaseDate) 
    VALUES (NULL, ?, ?, 'Reservation', NOW())
");
$purchaseStmt->bind_param("id", $reservation_id, $totalAmount);
$purchaseStmt->execute();
$purchase_id = $conn->insert_id;

// 5. Insert into purchase_items & update inventory
$insertItemStmt = $conn->prepare("INSERT INTO purchase_items (purchase_id, product_id, quantity, amount) VALUES (?, ?, ?, ?)");
$updateInventoryStmt = $conn->prepare("UPDATE inventory SET sold_count = sold_count + ?, stocks = stocks - ? WHERE product_id=?");

foreach ($resItems as $item) {
    $qty = $item['quantity'];
    $price = $item['price'];
    $product_id = $item['product_id'];
    $amount = $qty * $price;

    $insertItemStmt->bind_param("iiid", $purchase_id, $product_id, $qty, $amount);
    $insertItemStmt->execute();

    $updateInventoryStmt->bind_param("iii", $qty, $qty, $product_id);
    $updateInventoryStmt->execute();
}

$min_stock_level = 3; // Threshold for low stock alert

foreach ($resItems as $item) {
    $qty = $item['quantity'];
    $product_id = $item['product_id'];

    // LOW STOCK ALERT CHECK LOGIC
    $stockCheckStmt = $conn->prepare("
        SELECT p.product_name, i.stocks 
        FROM inventory i 
        INNER JOIN products p ON i.product_id = p.product_id 
        WHERE i.product_id = ? AND i.stocks <= ?
    ");
    $stockCheckStmt->bind_param("ii", $product_id, $min_stock_level);
    $stockCheckStmt->execute();
    $stockResult = $stockCheckStmt->get_result();

    if ($stockResult->num_rows > 0) {
        $row = $stockResult->fetch_assoc();

        // Admin notification
        $admin_id = 1;
        $notif_message = "Low stock alert! {$row['product_name']} (Stock left: {$row['stocks']})";
        $notif_type = "low_stock";
        $notif_link = "add_product.php";

        $notifSql = "INSERT INTO notifadmin (register_id, notif_message, notif_type, notif_link) VALUES (?, ?, ?, ?)";
        $notifStmt = $conn->prepare($notifSql);
        $notifStmt->bind_param("isss", $admin_id, $notif_message, $notif_type, $notif_link);
        $notifStmt->execute();
        $notifStmt->close();

        // Optional: Send email to admin
        $admin_email = "detorresjanellemae@gmail.com";
        $admin_subject = "Low Stock Alert - {$row['product_name']}";
        $admin_message = "The product {$row['product_name']} is low on stock ({$row['stocks']} left).";
        $admin_headers = "From: reservationsystem@beautyandblessed.online\r\n";
        mail($admin_email, $admin_subject, $admin_message, $admin_headers);
    }
    $stockCheckStmt->close();
}

// 6. Update reservation status + picked_up date (3 days later)
$pickupDate = date('Y-m-d', strtotime('+3 days'));
$updateRes = $conn->prepare("UPDATE reservations SET status='picked_up', pickup_date=? WHERE reservation_id=?");
$updateRes->bind_param("si", $pickupDate, $reservation_id);
$updateRes->execute();

// 6.1 Insert notif sa notifcustomer
$notifMsg = "Your reservation #$reservation_id has been approved.";
$notifType = "reservation_approved";
$notifLink = "reservation.php"; // adjust kung iba path mo

$notifCustSql = "INSERT INTO notifcustomer (register_id, notif_message, notif_type, notif_link, created_at, is_read)
                 VALUES (?, ?, ?, ?, NOW(), 0)";
$notifCustStmt = $conn->prepare($notifCustSql);
$notifCustStmt->bind_param("isss", $customer_id, $notifMsg, $notifType, $notifLink);
$notifCustStmt->execute();
$notifCustStmt->close();

// 6.2 Send email to customer about approved reservation
$to      = $customer_email;
$subject = "Your reservation has been approved";
$message = "Hi $customer_fname,

Your reservation #$reservation_id has been approved.
Pickup date: $pickupDate.

Thank you for reserving at Beauty and Blessed!";

$headers  = "From: reservationsystem@beautyandblessed.online\r\n";
$headers .= "Reply-To: reservationsystem@beautyandblessed.online\r\n";

mail($to, $subject, $message, $headers);

// 7. JSON response para sa AJAX (para bumalik yung 'Reservation confirmed' message at reload)
echo json_encode([
    'success' => true,
    'message' => 'Reservation approved successfully',
    'pickup_date' => $pickupDate
]);
exit;

?>
