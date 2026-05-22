<?php
include 'database.php';

if (!$conn) {
    die(json_encode(["error"=>"DB connection failed"]));
}

$purchase_id = isset($_GET['purchase_id']) ? intval($_GET['purchase_id']) : 0;

$sql = "
SELECT p.product_name, pi.quantity, pi.amount
FROM purchase_items pi
JOIN products p ON pi.product_id = p.product_id
WHERE pi.purchase_id = $purchase_id
";

$result = $conn->query($sql);

if(!$result){
    die(json_encode(["error"=>$conn->error]));
}

$items = [];
while($row = $result->fetch_assoc()){
    $items[] = $row;
}

header('Content-Type: application/json');
echo json_encode($items);
