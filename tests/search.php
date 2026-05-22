<?php
include 'database.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($query !== '') {
    $stmt = $conn->prepare("SELECT product_id, category_id, product_name 
                        FROM products 
                        WHERE product_name LIKE ? 
                        LIMIT 10");

    $like = '%' . $query . '%';   // para maging contains search
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }

    echo json_encode($suggestions);
}
?>