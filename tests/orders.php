<?php
session_start();
include 'database.php';

// Redirect if not logged in
if (!isset($_SESSION['register_id'])) {
    header("Location: homepage.php");
    exit();
}

$register_id = (int)$_SESSION['register_id'];

// Get selected date from querystring (define early to prevent warnings)
$selectedDate = isset($_GET['purchase_date']) ? $_GET['purchase_date'] : '';

// 1. Fetch distinct purchase dates for dropdown (DATE type, so direct column)
$dateStmt = $conn->prepare("
    SELECT DISTINCT p.purchaseDate AS purchase_date
    FROM purchase p
    INNER JOIN reservations r ON p.reservation_id = r.reservation_id
    WHERE r.register_id = ?
    ORDER BY purchase_date DESC, purchase_id DESC
");
$dateStmt->bind_param("i", $register_id);
$dateStmt->execute();
$dateResult = $dateStmt->get_result();

// 2. Fetch orders, filter by selected date if set
if (!empty($selectedDate)) {
    // Filter by date
    $orderSql = "
        SELECT p.purchase_id, p.totalAmount, p.purchaseDate
        FROM purchase p
        INNER JOIN reservations r ON p.reservation_id = r.reservation_id
        WHERE r.register_id = ? AND p.purchaseDate = ?
        ORDER BY p.purchaseDate DESC, p.purchase_id DESC
    ";
    $orderQuery = $conn->prepare($orderSql);
    $orderQuery->bind_param("is", $register_id, $selectedDate);

} else {
    // No date filter
    $orderSql = "
        SELECT p.purchase_id, p.totalAmount, p.purchaseDate
        FROM purchase p
        INNER JOIN reservations r ON p.reservation_id = r.reservation_id
        WHERE r.register_id = ?
        ORDER BY p.purchaseDate DESC, p.purchase_id DESC
    ";
    $orderQuery = $conn->prepare($orderSql);
    $orderQuery->bind_param("i", $register_id);
}
$orderQuery->execute();
$orderResult = $orderQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        html, body {
            overflow: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE 10+ */
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            overscroll-behavior: none;
        }

        html::-webkit-scrollbar, body::-webkit-scrollbar {
            display: none;
        }

        body { 
            font-family: 'Poppins', sans-serif;
        }

        /* Header Title Card (flush and flat, with icon) */
        .order-header-card {
            background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%) !important;
            box-shadow: 0 10px 28px rgba(0,0,0,0.12) !important;
            padding: 14px 19px;
            border-radius: 0;
            border: 0;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-direction: row;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .header-icon {
            margin-right: 10px;
            font-size: 1.8em;
            color: #6D2E3A;
            display: flex;
            align-items: center;
        }

        .header-text {
            font-weight: 700;
            font-size: 1.8em;
            color: #6D2E3A;
            letter-spacing: 0.5px;
            margin: 0;
        }

        /* Order card styling */
        .order-card {
            border-radius: 12px; 
            border: 1px solid #F8D7DC; 
            margin-bottom: 1.5rem; 
            background: #fff; 
            box-shadow: 0 2px 8px rgba(237,82,130,0.07);
            padding: 0;
            width: 100%; 
        }

        /* Order header inside card */
        .order-header {
            background: none;
            padding: 15px 20px;
            border-top-left-radius: 12px; 
            border-top-right-radius: 12px; 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start;
            border-bottom: 1px solid #F8D7DC;
            width: 100%; 
        }

        /* Order header font */
        .order-header h5, .order-header span {
            font-family: 'Poppins', sans-serif;
            color: #6D2E3A;
        }

        .btn-clear {
            color: #6D2E3A !important;
        }

        /* Table header style */
        .table-pink th {
            background: #6d2e3a !important;
            color: #fff !important;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.07em;
            letter-spacing: 0.04em;
            border-right: 1px solid #E8A9B2;
            text-align: center;
        }
        .table-pink th:last-child {
            border-right: none;
        }

        /* Table body style */
        .table-bordered td {
            font-family: 'Poppins', sans-serif;
            color: #6d2e3a;
            font-weight: 500;
            font-size: 1em;
            text-align: center;
            border-right: 1px solid #E8A9B2;
        }
        .table-bordered td:last-child {
            border-right: none;
        }

        /* Table stripes (zebra) */
        .table-striped tbody tr:nth-of-type(even) td {
            background-color: #F8D7DC !important;
        }
        .table-striped tbody tr:nth-of-type(odd) td {
            background-color: #fffafc !important;
        }
        .table-striped tbody tr:hover td {
            background-color: #ffe4ec !important;
            transition: background 0.25s;
        }

        .table-responsive {
            padding: 0;
            margin: 0;
        }

        .table {
            width: 100%;
            margin: 0;
            border-radius: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        /* Image column for order items */
        .order-items img {
            width: 50px; 
            height: 50px; 
            object-fit: cover; 
            border-radius: 5px; 
            display: block;
            margin: auto;
        }

        .container-fluid {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .order-header-card.sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .order-header-main {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .order-header-filter {
            margin-right: 0px;
        }

        .date-form .form-control {
            width: 130px !important;
            max-width: 130px !important;
            font-size: 0.9rem;
            padding: 4px 8px;
            border: 1px solid #6D2E3A;
            color: #6D2E3A;
        }

        .date-form .form-control:focus {
            border-color: #6D2E3A;
            box-shadow: 0 0 0 0.15rem rgba(109, 46, 58, 0.35);
            outline: none;
        }

        /* Dropdown label and select style */
        .filter-label {
            margin: 0;
            color: #6D2E3A;
            text-align: right;
            font-family: 'Poppins',sans-serif;
            min-width: 54px;
        }

        .filter-dropdown {
            min-width: 140px;
            max-width: 140px;
            border-color: #6D2E3A;
            color: #6D2E3A;
        }

        .order-number-total {
            margin-bottom: 0;
        }

        .order-date {
            margin-top: 2px;
        }

        @media (max-width: 767px) {
            .table th, .table td { 
                padding: 7px 2px; 
                font-size: 0.96em;
            }
            .container-fluid {
                padding-left: 18px !important;
                padding-right: 18px !important;
            }
            .header-icon,
            .header-text {
                font-size: 25px;
            }
            .header-icon {
                margin-left: 16px;
            }
            .order-header-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px 8px;
            }
            .order-header-main {
                width: 100%;
                margin-bottom: 6px;
            }
            .order-header-filter {
                width: 100%;
                display: flex;
                justify-content: flex-end;
                margin-right: 20px; /* margin for dropdown */
            }
            .btn-clear {
                margin-right: 5px !important;
            }
            .filter-dropdown {
                min-width: 135px;
                max-width: 135px;
            }
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px 8px;
            }
            .order-number-total, .order-date {
                text-align: left;
            }
        }
    </style>

</head>
<body>
    <div class="container-fluid px-3 px-md-5 py-3">
        <!-- HEADER SECTION, SAME AS CART -->
        <div class="order-header-card sticky-top mb-4">
            <div class="d-flex align-items-center order-header-main">
                <span class="header-icon">
                    <i class="bi bi-bag-fill"></i>
                </span>
                <span class="header-text"> Purchase History</span>
            </div>
            <div class="order-header-filter">
                <form method="get" class="d-flex align-items-center date-form" style="gap:8px;">
                    <label for="purchase_date" class="filter-label">
                        Date:
                    </label>
                    <input
                        type="text"
                        name="purchase_date"
                        id="purchaseDateInput"
                        class="form-control filter-dropdown"
                        value="<?= htmlspecialchars($selectedDate) ?>"
                        placeholder="Select date"
                        autocomplete="off"
                    >
                    <button type="button" onclick="document.getElementById('purchaseDateInput').value=''; this.form.submit();" class="btn btn-clear p-0 ms-2" title="Show All">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <?php
        if($orderResult->num_rows > 0){
            while($order = $orderResult->fetch_assoc()){
                $purchase_id = $order['purchase_id'];
                $total = number_format($order['totalAmount'],2);
                $date = date("F j, Y", strtotime($order['purchaseDate']));
                // Fetch items for this purchase
                $itemQuery = $conn->prepare("
                    SELECT pi.quantity, pi.amount, pr.product_name, pr.price, pr.image_path
                    FROM purchase_items pi
                    INNER JOIN products pr ON pi.product_id = pr.product_id
                    WHERE pi.purchase_id = ?
                ");
                $itemQuery->bind_param("i", $purchase_id);
                $itemQuery->execute();
                $itemResult = $itemQuery->get_result();
        ?>
        <div class="order-card shadow-sm">
            <div class="order-header">
                <div>
                    <div class="order-number-total" style="font-weight: 600;font-size:1.08em;color:#6d2e3a;">
                        Order #<?= $purchase_id ?> - ₱<?= $total ?>
                    </div>
                    <div class="order-date" style="font-size:0.97em;color:#6d2e3a;">
                        <?= $date ?>
                    </div>
                </div>
            </div>
            <div class="order-items p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-pink align-middle mb-0">
                        <thead class="table-pink text-center">
                            <tr>
                                <th style="width:100px;">Image</th>
                                <th style="width:600px;">Product Name</th>
                                <th style="width:200px;">Price</th>
                                <th style="width:100px;">Quantity</th>
                                <th style="width:200px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($itemResult->num_rows > 0){
                                while($item = $itemResult->fetch_assoc()){
                                    $amount = number_format($item['amount'],2);
                                    $price = number_format($item['price'],2);
                                    echo '<tr>
                                            <td class="text-center"><img src="'.$item['image_path'].'" alt="'.$item['product_name'].'"></td>
                                            <td>'.$item['product_name'].'</td>
                                            <td>₱'.$price.'</td>
                                            <td class="text-center">'.$item['quantity'].'</td>
                                            <td>₱'.$amount.'</td>
                                        </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5" class="text-center text-muted">No items found.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
            }
        } else {
            echo '<p class="text-center text-muted">No orders found.</p>';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#purchaseDateInput", {
                dateFormat: "Y-m-d",   // Para tugma sa database date format mo
                allowInput: true,      // Pwede rin manual type
                defaultDate: "<?= $selectedDate ? htmlspecialchars($selectedDate) : '' ?>",
                onChange: function(selectedDates, dateStr, instance) {
                    // Auto-submit form kapag may napili
                    instance.input.form.submit();
                }
            });
        });
    </script>
</body>
</html>