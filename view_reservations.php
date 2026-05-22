<?php
include 'database.php';
$custId = $_GET['custId'] ?? 0;
$statusFilter = $_GET['status'] ?? 'pending'; // default to pending

// Fetch reservations for this customer
$resQuery = $conn->prepare("SELECT * FROM reservations WHERE register_id=?");
$resQuery->bind_param("i", $custId);
$resQuery->execute();
$reservations = $resQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Skip sorting for 'all', just keep the default order
// Filter reservations based on selected status
foreach($reservations as $res){
    $resId = $res['reservation_id'];
    $status = $res['status'];

    // Skip if status doesn't match the filter
    if ($status !== $statusFilter) continue;

    $resDate = $res['reservation_date'];
    $pickupDate = $res['pickup_date'];
    $cancelDate = $res['cancel_date'] ?? '';
    $datePickedUp = $res['date_picked_up'] ?? '';

    // Fetch items
    $itemQuery = $conn->prepare("
        SELECT p.product_name, ri.quantity, p.price
        FROM reservation_items ri
        JOIN products p ON ri.product_id = p.product_id
        WHERE ri.reservation_id = ?
    ");
    $itemQuery->bind_param("i", $resId);
    $itemQuery->execute();
    $result = $itemQuery->get_result();
    $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    // Card container
    echo "<div class='innerCardClass' style='border:1px solid #E8A9B2; padding:10px; margin-bottom:10px; background: #F8D7DC;'>";

    // Show dates
    if ($status === 'pending') {
        echo "
        <div class='reservation-dates-row'>
            <div class='reservation-date-block'>
                <strong>Reservation Date:</strong>
                <span class='reservation-date-value'>$resDate</span>
            </div>
            <div class='pickup-date-block'>
                <strong>Pick-up Date:</strong>
                <span class='pickup-date-value'>$pickupDate</span>
            </div>
        </div>";
    } elseif ($status === 'cancelled') {
        echo "<div><strong>Cancel Date:</strong> $cancelDate</div>";
    } elseif ($status === 'picked_up') {
        echo "<div><strong>Pick-up Date:</strong> $pickupDate</div>";
    }

    // Items table
    echo "<table style='width:100%; border-collapse:collapse; margin-top:5px;'>
            <thead style='background:#a95469;color:white;'>
                <tr>
                    <th style='border-right:1px solid #fff;'>Item</th>
                    <th style='border-right:1px solid #fff;'>Qty</th>
                    <th style='border-right:1px solid #fff;'>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>";

    $total = 0;
    if(!empty($items)){
        foreach($items as $item){
            $qty = $item['quantity'] ?? 0;
            $productName = $item['product_name'] ?? '-';
            $price = $item['price'] ?? 0;
            $lineTotal = $qty * $price;
            $total += $lineTotal;

            echo "<tr>
                    <td style='border-right:1px solid #ddd;'>$productName</td>
                    <td style='border-right:1px solid #ddd;'>$qty</td>
                    <td style='border-right:1px solid #ddd;'>₱$price</td>
                    <td>₱$lineTotal</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4' style='text-align:center;'>No items found</td></tr>";
    }

    echo "</tbody></table>";

    // Total and Confirm button
    echo "<div style='margin-top:5px; font-weight:bold; display:flex; justify-content:space-between; align-items:center;'>
            <span>Total: ₱$total</span>";

    if($status === 'pending'){
        echo "<button class='confirmBtn' data-resid='$resId' style='background:#6D2E3A;color:white;border:none;padding:5px 10px;cursor:pointer;'>Confirm</button>";
    }

    echo "</div>"; // close total + button div
    echo "</div>"; // close card
}
?>
