<?php
include 'database.php';

// Get top 5 products based on reserved quantity with image
$query = "
    SELECT p.product_name, p.image_path, SUM(ri.quantity) as total_reserved
    FROM reservation_items ri
    JOIN products p ON ri.product_id = p.product_id
    GROUP BY ri.product_id
    ORDER BY total_reserved DESC
    LIMIT 5
";

$result = $conn->query($query);
$topProducts = [];

while($row = $result->fetch_assoc()){
    $topProducts[] = $row;
}

echo json_encode($topProducts);
