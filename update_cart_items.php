<?php
session_start();
include 'database.php';

if (!isset($_SESSION['login_id'])) {
    http_response_code(403);
    echo "Not logged in";
    exit;
}

if (!isset($_POST['cart_items_id']) || !isset($_POST['quantity'])) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

$cart_items_id = intval($_POST['cart_items_id']);
$newQty = intval($_POST['quantity']);

// Kunin price at cart_id ng item
$query = "
    SELECT p.price, ci.cart_id 
    FROM cart_items ci
    INNER JOIN products p ON ci.product_id = p.product_id
    WHERE ci.cart_items_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $cart_items_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo "Item not found";
    exit;
}

$data = $res->fetch_assoc();
$price = $data['price'];
$cart_id = $data['cart_id'];
$newAmount = $newQty * $price;

// Update cart_items
$updateItem = "UPDATE cart_items SET quantity = ?, amount = ? WHERE cart_items_id = ?";
$stmt = $conn->prepare($updateItem);
$stmt->bind_param("idi", $newQty, $newAmount, $cart_items_id);
$stmt->execute();

// Update cart total
$updateTotal = "
    UPDATE cart 
    SET total = (SELECT SUM(amount) FROM cart_items WHERE cart_id = ?)
    WHERE cart_id = ?
";
$stmt = $conn->prepare($updateTotal);
$stmt->bind_param("ii", $cart_id, $cart_id);
$stmt->execute();

echo "Quantity updated successfully";