<?php
session_start();
include 'database.php';

// Enable error reporting for debugging (optional, remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// All responses will be JSON
header('Content-Type: application/json');

if (isset($_POST['confirmAddBtn'])) {
    if (!isset($_SESSION['register_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'You must be logged in.',
            'cartCount' => 0
        ]);
        exit;
    }

    $register_id = $_SESSION['register_id'];
    $login_id = $_SESSION['login_id'] ?? null;

    if (!$login_id) {
        $findLogin = $conn->prepare("SELECT login_id FROM login_tb WHERE register_id = ?");
        $findLogin->bind_param("i", $register_id);
        $findLogin->execute();
        $result = $findLogin->get_result();

        if ($result->num_rows > 0) {
            $loginRow = $result->fetch_assoc();
            $login_id = $loginRow['login_id'];
        } else {
            $insertLogin = $conn->prepare("INSERT INTO login_tb (register_id) VALUES (?)");
            $insertLogin->bind_param("i", $register_id);
            $insertLogin->execute();
            $login_id = $conn->insert_id;
        }

        $_SESSION['login_id'] = $login_id;
    }

    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);

    // --- Quantity validation ---
    if ($quantity <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid quantity for this item.',
            'cartCount' => 0
        ]);
        exit;
    }

    // --- Product price and stock validation from inventory ---
    $productQuery = "
        SELECT p.price, i.stocks 
        FROM products p
        JOIN inventory i ON p.product_id = i.product_id
        WHERE p.product_id = ?
    ";
    $stmt = $conn->prepare($productQuery);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found.',
            'cartCount' => 0
        ]);
        exit;
    }

    $product = $result->fetch_assoc();
    $price = $product['price'];
    $stocks = $product['stocks'];
    $amount = $price * $quantity;

    if ($stocks <= 10) {
        echo json_encode([
            'success' => false,
            'message' => 'You cannot add this item to the cart because the stock is too low!',
            'cartCount' => 0
        ]);
        exit;
    }
        
    if ($quantity > $stocks) {
        echo json_encode([
            'success' => false,
            'message' => 'Not enough stock for the quantity you want to add.',
            'cartCount' => 0
        ]);
        exit;
    }
        
    // STEP 2: Check if cart exists
    $cartQuery = "
        SELECT c.*
        FROM cart AS c
        INNER JOIN login_tb AS l ON c.login_id = l.login_id
        WHERE l.register_id = ?
    ";
    $stmt = $conn->prepare($cartQuery);
    $stmt->bind_param("i", $register_id);
    $stmt->execute();
    $cartResult = $stmt->get_result();

    if ($cartResult->num_rows > 0) {
        $cart = $cartResult->fetch_assoc();
        $cart_id = $cart['cart_id'];
    } else {
        $createCart = "INSERT INTO cart (login_id, total, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($createCart);
        $stmt->bind_param("id", $login_id, $amount);
        $stmt->execute();
        $cart_id = $conn->insert_id;
    }

    // STEP 3: Check if item already exists
    $checkItem = "SELECT quantity, amount FROM cart_items WHERE cart_id = ? AND product_id = ?";
    $stmt = $conn->prepare($checkItem);
    $stmt->bind_param("ii", $cart_id, $product_id);
    $stmt->execute();
    $existingItem = $stmt->get_result();

    if ($existingItem->num_rows > 0) {
        // ✔ Existing item → update quantity & amount only
        $row = $existingItem->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;

        // Check if new quantity exceeds stock
        if ($newQuantity > $stocks) {
            echo json_encode([
                'success' => false,
                'message' => 'Not enough stock for the total quantity after update.',
                'cartCount' => 0
            ]);
            exit;
        }

        $newAmount = $price * $newQuantity;
        $updateItem = "
            UPDATE cart_items 
            SET quantity = ?, amount = ?
            WHERE cart_id = ? AND product_id = ?
        ";
        $stmt = $conn->prepare($updateItem);
        $stmt->bind_param("idii", $newQuantity, $newAmount, $cart_id, $product_id);
        $stmt->execute();
    } else {
        // ➕ New item → INSERT + add_date = NOW()
        $insertItem = "
            INSERT INTO cart_items (cart_id, product_id, quantity, amount, add_date) 
            VALUES (?, ?, ?, ?, NOW())
        ";
        $stmt = $conn->prepare($insertItem);
        $stmt->bind_param("iiid", $cart_id, $product_id, $quantity, $amount);
        $stmt->execute();
    }

    // STEP 4: Update cart total
    $updateTotalQuery = "
        UPDATE cart 
        SET total = (SELECT SUM(amount) FROM cart_items WHERE cart_id = ?)
        WHERE cart_id = ?
    ";
    $stmt = $conn->prepare($updateTotalQuery);
    $stmt->bind_param("ii", $cart_id, $cart_id);
    $stmt->execute();

    // STEP 5: Compute total cart_items for this customer (all cart_items of this cart)
    $cartCount = 0;
    $countQuery = "SELECT COUNT(*) AS cart_count FROM cart_items WHERE cart_id = ?";
    $stmt = $conn->prepare($countQuery);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $countRes = $stmt->get_result();
    if ($countRow = $countRes->fetch_assoc()) {
        $cartCount = (int)$countRow['cart_count'];
    }

    // Final JSON success response
    echo json_encode([
        'success'   => true,
        'message'   => 'Successfully added item to cart.',
        'cartCount' => $cartCount
    ]);
    exit;
}
?>
