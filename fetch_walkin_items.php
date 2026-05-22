<?php
include 'database.php';

if(isset($_GET['purchase_id'])){
    $purchase_id = intval($_GET['purchase_id']);

    $sql = "
    SELECT pi.quantity, pi.amount, p.product_name, p.image_path, pur.purchaseDate
    FROM purchase_items pi
    JOIN purchase pur ON pi.purchase_id = pur.purchase_id
    JOIN products p ON pi.product_id = p.product_id
    WHERE pi.purchase_id = $purchase_id
    ";

    $result = $conn->query($sql);
    $items = [];
    while($row = $result->fetch_assoc()){
        $items[] = [
            'product_name' => $row['product_name'],
            'image_path' => $row['image_path'],
            'quantity' => $row['quantity'],
            'amount' => $row['amount'],
            'purchaseDate' => date('F j, Y', strtotime($row['purchaseDate']))
        ];
    }

    echo json_encode($items);
}
?>
