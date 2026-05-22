<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ block kung hindi naka-login na admin
if (!isset($_SESSION['admin_email'])) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Location: log_admin.php");
    exit();
}

error_reporting(0);
ini_set('display_errors', 0);
include 'database.php';

// ✅ Handle stock check request
if (isset($_GET['action']) && $_GET['action'] === 'check_stock' && isset($_GET['product_id'])) {
  header('Content-Type: application/json; charset=utf-8');
  $pid = intval($_GET['product_id']);
  $res = $conn->query("SELECT stocks FROM inventory WHERE product_id = $pid");
  if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    echo json_encode(["stocks" => intval($row['stocks'])]);
  } else {
    echo json_encode(["stocks" => 0]);
  }
  exit;
}

// ✅ Handle insertion of new walk-in and related purchases
if (isset($_POST['action']) && $_POST['action'] === 'add_walkin') {
  header('Content-Type: application/json; charset=utf-8');

  $full_name = trim($_POST['full_name']);
  $total = floatval($_POST['total']);
  $items = json_decode($_POST['items'], true);

  if ($full_name === "" || $total <= 0 || empty($items)) {
    echo json_encode(["status" => "error", "message" => "Invalid data."]);
    exit;
  }

  $conn->begin_transaction();

  try {
    $walkin_sql = "INSERT INTO walk_in (walk_in_name, total, walk_in_date) VALUES (?, ?, CURDATE())";
    $walkin_stmt = $conn->prepare($walkin_sql);
    $walkin_stmt->bind_param("sd", $full_name, $total);
    $walkin_stmt->execute();
    $walk_in_id = $conn->insert_id;

    $purchase_sql = "INSERT INTO purchase (walk_in_id, totalAmount, purchaseMethod, purchaseDate)
                     VALUES (?, ?, 'Walk-In', CURDATE())";
    $purchase_stmt = $conn->prepare($purchase_sql);
    $purchase_stmt->bind_param("id", $walk_in_id, $total);
    $purchase_stmt->execute();
    $purchase_id = $conn->insert_id;

    $item_sql = "INSERT INTO purchase_items (purchase_id, product_id, quantity, amount)
                 VALUES (?, ?, ?, ?)";
    $item_stmt = $conn->prepare($item_sql);

    $update_inv_sql = "UPDATE inventory 
                       SET stocks = stocks - ?, sold_count = sold_count + ? 
                       WHERE product_id = ?";
    $inv_stmt = $conn->prepare($update_inv_sql);

foreach ($items as $it) {
    $pid = intval($it['product_id']);
    $qty = intval($it['quantity']);
    $amount = floatval($it['amount']);

    // Bind and execute insert to purchase_items
    $item_stmt->bind_param("iiid", $purchase_id, $pid, $qty, $amount);
    $item_stmt->execute();

    // Bind and execute inventory update
    $inv_stmt->bind_param("iii", $qty, $qty, $pid);
    $inv_stmt->execute();

    // LOW STOCK ALERT CHECK LOGIC
    $min_stock_level = 3;
    $stockCheckStmt = $conn->prepare("SELECT p.product_name, i.stocks 
                                      FROM inventory i 
                                      INNER JOIN products p ON i.product_id = p.product_id 
                                      WHERE i.product_id = ? AND i.stocks <= ?");
    $stockCheckStmt->bind_param("ii", $pid, $min_stock_level);
    $stockCheckStmt->execute();
    $stockResult = $stockCheckStmt->get_result();

    if ($stockResult->num_rows > 0) {
        $row = $stockResult->fetch_assoc();

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

    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Order completed, saved, and inventory updated successfully!"]);
  } catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => "Transaction failed: " . $e->getMessage()]);
  }
 

  exit;
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Beauty Product Order Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
  html, body {
    height: 100%;
    margin: 0;
    font-family: 'Poppins', sans-serif !important;
    color: #6d2e3a;
    font-size: 16px; /* Gaya ng sa customer */
}

.body {
     background-color: #fff !important;
}

:root {
    --pink-very-light: #F8D7DC;
    --pink-soft: #E8A9B2;
    --pink-mid: #D96D84;
    --pink-dark: #6D2E3A;
    --bg-light: #fff5f9;
    --text-main: var(--pink-dark);
    --text-soft: var(--pink-mid);
    --border-main: var(--pink-mid);
    --btn-bg: var(--pink-mid);
    --btn-hover: var(--pink-dark);
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%) !important;
    box-shadow: 0 10px 28px rgba(0,0,0,0.12) !important;
    position: sticky;
    top: 0;
    z-index: 100;
    flex-wrap: wrap;
}

.header-container i {
    color: #6d2e3a;
}

.heading {
    color: #6d2e3a;
}

    .container {
    display: flex;
    justify-content: space-between;
    background-color: #a95469;
    width: 96%;
    border-radius: 0;
    padding: 20px;
    box-shadow: 0 0 10px rgba(136, 136, 136, 0.4);
    flex-wrap: wrap;
}

.left-panel, .right-panel {
    width: 48%;
    background-color: #fff;
    padding: 40px;
    border-radius: 0;
    box-sizing: border-box;
}

h2 { text-align: center; color: white; margin-bottom: 20px; }
label { display: block; margin-top: 10px; font-weight: 600; }

/* ===== Inputs & Selects ===== */
input, .custom-select .select-selected {
    width: 100%;
    padding: 6px;
    border: 2px solid #6d2e3a;   /* 2px border */
    border-radius: 2px;           /* 2px rounded */
    margin-top: 5px;
    margin-bottom: 10px;
    background-color: #eee;        /* default gray */
    color: #6d2e3a;               /* text color */
    font-size: 13px;
}

input:focus, .custom-select .select-selected:focus {
    outline: 2px solid #6d2e3a;   /* blue highlight on focus */
}

#nameSuggestions {
    position: absolute;
    background-color: #eee;
    color: #6d2e3a;
    border-radius: 8px;
    max-height: 120px;
    overflow-y: auto;
    width: calc(100% - 12px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    z-index: 99;
    display: none;
}

#nameSuggestions div {
    padding: 6px;
    cursor: pointer;
    border-radius: 8px;
}

#nameSuggestions div:hover {
    background-color: #d96d84;
    color: white;
}

/* ===== Inputs ===== */
input, .custom-select .select-selected {
    border-radius: 6px;  /* dating 2px lang, pwedeng palitan */
}


.custom-select {
    position: relative;
    width: 100%;
    margin-bottom: 10px;
    font-size: 13px;
}

.select-selected {
    background-color: #eee;
    color: #6d2e3a;
    padding: 6px;
    border-radius: 6px;
    cursor: pointer;
    user-select: none;
    font-size: 13px;
}

.select-items {
    position: absolute;
    background-color: #fff;
    top: 100%;
    left: 0;
    right: 0;
    border-radius: 6px;
    z-index: 99;
    display: none;
    max-height: 150px;
    overflow-y: auto;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.select-items div {
    color: #6d2e3a;
    padding: 6px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
}

.select-items div:hover, .same-as-selected {
    background-color: #726c6cff;
    color: white;
}

/* ===== Buttons ===== */
.walkin-button {
    background-color: #6d2e3a;
    color: white;
    border: none;
    padding: 8px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
    font-weight: 600;
    margin-top: 10px;
}

.walkin-button:hover { background-color: #d96d84; }
/* Titles */
.left-panel h2 {
    color: #6d2e3a;  /* dark wine */
    text-align: center;
    margin-bottom: 20px;
}

.right-panel {
    flex: 0 0 74%;
    background-color: #fff;
    padding: 40px;
    border-radius: 0;
    box-sizing: border-box;
}

.right-panel h2 {
    color: #6d2e3a;  /* bright blue para makita sa white bg */
    text-align: center;
    margin-bottom: 20px;
}

/* Table */
/* Table */
.right-panel table {
    width: 100%;            /* full width ng panel */
    border-collapse: collapse;
    margin-top: 10px;
    table-layout: auto;     /* flexible columns */
    color: #6d2e3a;
    /* remove display:block and overflow-x:auto */
}

.right-panel th, .right-panel td {
    border-bottom: 1px solid #6d2e3a;
    padding: 8px 6px;
    text-align: center;
}

/* Table striped rows */
.right-panel tbody tr:nth-child(odd) { background-color: #fff; }
.right-panel tbody tr:nth-child(even) { background-color: #ffe4f2; }

/* Table header */
.right-panel th {
    background-color: #a95469;
    color: white;
    font-weight: 600;
}

/* Optional: flexible columns */
.right-panel th, .right-panel td {
    white-space: normal;
}


/* Table column sizing */
.right-panel th:nth-child(1) { width: 50px; }   /* Image */
.right-panel th:nth-child(2) { width: 130px; }  /* Name */
.right-panel th:nth-child(3) { width: 50px; }   /* Price */
.right-panel th:nth-child(4) { width: 50px; }   /* Quantity */
.right-panel th:nth-child(5) { width: 50px; }   /* Total */
.right-panel th:nth-child(6) { width: 60px; }   /* Actions */


.right-panel td:last-child {
    display: table-cell !important;
    text-align: center;
    vertical-align: middle;
}

.right-panel td:last-child button {
    display: inline-block !important;
    margin: 2 8px !important;   /* ← dagdag space dito */
    width: 34px;
    height: 34px;
    padding: 4px;
    vertical-align: middle;
}


/* ===== Panels Flex ===== */
.left-panel { flex: 0 0 25%; }
.right-panel { flex: 0 0 74%; }

@media (max-width: 768px) {
    .container { flex-direction: column; align-items: center; }
    .left-panel, .right-panel { width: 100%; margin-bottom: 20px; }
}


.order-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: nowrap;
}

.product-img {
    width: 60px;        /* adjust size */
    height: 60px;
    object-fit: cover;  /* para hindi ma-distort */
    border-radius: 5px; /* optional, para malinis tingnan */
}

/* Limit name column width + allow wrapping */
.right-panel td:nth-child(2) {
    max-width: 180px;
    white-space: normal;
    word-break: break-word;
}


/* =========================
   SWEETALERT MODAL SIZE - SMALL VERSION
========================= */
.small-swal, .my-swal-size {
  width: 280px !important;        /* mas maliit kaysa dati 340px */
  max-width: 90%;                 /* responsive sa maliit na screen */
  padding: 1rem !important;       /* less padding */
  font-size: 0.85rem !important;  /* maliit pero readable */
  color: #6d2e3a !important;
  border-radius: 10px !important; /* maliit na round corners */
}

/* =========================
   SWEETALERT BUTTONS - SMALL VERSION
========================= */
.btn-confirm {
  background-color: #6d2e3a !important;
  color: #fff !important;
  border: none !important;
  border-radius: 6px !important; /* mas maliit na curve */
  padding: 6px 16px !important;  /* compact button */
  font-weight: 600 !important;
  margin: 0 4px !important;
  font-size: 0.8rem !important;  /* maliit na font */
  transition: all 0.2s ease;
}
.btn-confirm:hover {
  background-color: #d96d84 !important;
}

.btn-cancel {
  background-color: #bdb6b8ff !important;
  color: #6d2e3a !important;
  border: none !important;
  border-radius: 6px !important;
  padding: 6px 16px !important;  /* compact button */
  font-weight: 600 !important;
  margin: 0 4px !important;
  font-size: 0.8rem !important;  /* maliit na font */
  transition: all 0.2s ease;
}
.btn-cancel:hover {
  background-color: #d96d84 !important;
}

/* Rectangle, compact modal (no change here) */
.swal2-popup, .swal2-modal, .small-swal, .my-swal-size {
  border-radius: 8px !important;
  width: 290px !important;
  max-width: 95vw !important;
  font-size: 0.96rem !important;
  padding: 1.1rem !important;
  color: #6D2E3A !important;
}

/* Confirm button: dark wine */
.swal2-confirm, .swal2-styled.swal2-confirm {
  background: #6D2E3A !important;
  color: #fff !important;
  border-radius: 7px !important;
  min-width: 65px;
  border: none !important;
  box-shadow: none !important;
  font-weight: 600;
  padding: 7px 32px !important;
  font-size: 0.98em !important;
  transition: background 0.15s;
}

/* Cancel button: soft gray base, dark wine text */
.swal2-cancel, .swal2-styled.swal2-cancel {
  background: #eee !important;
  color: #6D2E3A !important;
  border-radius: 7px !important;
  min-width: 65px;
  border: none !important;
  box-shadow: none !important;
  font-weight: 600;
  padding: 7px 32px !important;
  font-size: 0.98em !important;
  transition: background 0.15s;
}

/* Hover style for both: always pink-on-hover for confirm, slightly darker gray for cancel */
.swal2-confirm:hover, .swal2-confirm:focus {
  background: #F8D7DC !important;
  color: #6D2E3A !important;
}
.swal2-cancel:hover, .swal2-cancel:focus {
  background: #d3d3d3 !important;   /* light gray hover */
  color: #6D2E3A !important;
}

.swal2-confirm:hover, .swal2-cancel:hover,
.swal2-confirm:focus, .swal2-cancel:focus {
  background: #6d2e3a !important;     /* light pink hover */
  color: #fff !important;
  outline: none !important;
  box-shadow: none !important;
}

/* 3. Icon and font: dark palette */
.swal2-title, .swal2-html-container, .swal2-content {
  color: #6D2E3A !important;
}
./* Super small and compact SweetAlert2 icon */
.swal2-icon {
  font-size: 1.22rem !important;        /* smaller than normal */
  width: 1.7em !important;
  height: 1.7em !important;
  line-height: 1.7em !important;
  margin: 0 auto 0.44em auto !important;
  border-width: 2px !important;
  color: #6D2E3A !important;           /* still dark palette */
  border-color: #6D2E3A !important;
}

.my-swal-size .swal2-icon.swal2-success,
.my-swal-size .swal2-icon.swal2-error,
.my-swal-size .swal2-icon.swal2-question,
.my-swal-size .swal2-icon.swal2-warning {
    border-color: #6d2e3a !important;
    color: #6d2e3a !important;
}

.my-swal-size .swal2-success-ring,
.my-swal-size .swal2-error-ring,
.my-swal-size .swal2-question-ring,
.my-swal-size .swal2-warning-ring {
    border-color: rgba(109, 46, 58, 0.4) !important;
}

.my-swal-size .swal2-success-line-tip,
.my-swal-size .swal2-success-line-long,
.my-swal-size .swal2-error-line-tip,
.my-swal-size .swal2-error-line-long,
.my-swal-size .swal2-question-line-tip,
.my-swal-size .swal2-question-line-long,
.my-swal-size .swal2-warning-line-tip,
.my-swal-size .swal2-warning-line-long{
    background-color: #6d2e3a !important;
}

/* ============================
   📱 FULL MOBILE RESPONSIVE FIX
============================ */

@media (max-width: 768px) {
    .right-panel table {
        display: block;
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }
    
  .right-panel td:last-child {
      display: table-cell !important;
      white-space: nowrap;
  }

  .right-panel td:last-child button {
      display: inline-block !important;
      margin: 0 3px;
      width: 30px;
      height: 30px;
  }
  
    #orderTableWrapper table th,
    #orderTableWrapper table td {
        font-size: 11px;
        padding: 6px 4px;
    }
    
    #orderTableWrapper table th:nth-child(1),
    #orderTableWrapper table td:nth-child(1) {
        width: 45px;
    }

    #orderTableWrapper table th:nth-child(2),
    #orderTableWrapper table td:nth-child(2) {
        width: 130px; /* Name column pwede mong gawing 120–140 */
        white-space: normal;
        word-break: break-word;
    }

    #orderTableWrapper table th:nth-child(3),
    #orderTableWrapper table td:nth-child(3),
    #orderTableWrapper table th:nth-child(4),
    #orderTableWrapper table td:nth-child(4),
    #orderTableWrapper table th:nth-child(5),
    #orderTableWrapper table td:nth-child(5) {
        width: 60px;
        text-align: center;
    }

    #orderTableWrapper table th:nth-child(6),
    #orderTableWrapper table td:nth-child(6) {
        width: 70px;
        text-align: center;
        white-space: nowrap;
    }

.header-container {
        flex-direction: column;
        gap: 12px;
        text-align: left;           /* ← Gawing left align ang header container */
        align-items: flex-start;    /* ← Left align content sa container */
    }
    .heading {
        font-size: 25px !important;
        justify-content: flex-start; /* ← Icon + text ay left-justify sa flex */
        text-align: left !important; /* ← Text ng header ay left aligned */
        width: 100%;
    }

  /* Container full width */
  .container {
    display: block;
    width: 100%;
    max-width: 100%;
    padding: 10px;
    margin: 0 auto;
  }

  /* Panels stacked */
  .left-panel, 
  .right-panel {
    width: 100% !important;
    padding: 20px !important;
    margin-bottom: 20px;
  }

  /* Table responsive */
  .right-panel table {
    display: block;
    width: 100%;
    overflow-x: auto;
    white-space: nowrap;
  }

  /* Smaller product image */
  .product-img {
    width: 45px;
    height: 45px;
  }

  /* Buttons full width */
  .walkin-button {
    font-size: 15px;
    padding: 12px;
  }

    /* Fix custom dropdowns */
  .custom-select {
    width: 100% !important;
  }

  .select-selected,
  .select-items div {
    font-size: 14px;
    padding: 8px;
  }

  /* Fix autocomplete box */
  #nameSuggestions {
    width: 100% !important;
    left: 0 !important;
  }

  #checkoutBtn {
    display: block;
    margin-top: 2px !important;
    margin: 0 auto 0 auto;
    font-size: 10px;
    padding: 9px 25px;
    font-weight: 600;
    background: #6d2e3a;
    color: white;
    border: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.10);
    cursor: pointer;
    transition: background 0.2s;
  }
  #completeBtn {
      font-size: 10px;
      padding: 8px 23px;
  }
}

</style>
</head>
<body>
<div class="container-fluid" style="padding:0;">
  <div class="header-container mb-3" style="width:100%;">
      <h1 class="heading" style="display: flex; align-items: center; font-size: 2em; font-weight: bold;">
          <i class="fa-solid fa-shop" style="margin-left: 12px; margin-right: 12px;"></i> Walk-in Orders
      </h1>
  </div>

  <div class="container" style="width:100%;">
    <div class="left-panel">
      <h2>Order Form</h2>

      <label>Name:</label>
      <div style="position: relative;">
        <input type="text" id="name" placeholder="Enter your full name" autocomplete="off" required>
        <div id="nameSuggestions"></div>
      </div>

      <label>Select Category:</label>
      <div class="custom-select" id="categorySelect">
        <div class="select-selected">-- Select Category --</div>
        <div class="select-items"></div>
      </div>

      <label>Select Product:</label>
      <div class="custom-select" id="productSelect">
        <div class="select-selected">-- Select Product --</div>
        <div class="select-items"></div>
      </div>

      <label>Available Stock:</label>
          <input
            type="text"
            id="stockField"
            readonly
            value=""
            style="
              background-color:#eee;
              color:#6d2e3a;
              border:2px solid #6d2e3a;
              border-radius:6px;
              padding:6px;
              width:100%;
              font-size:13px;
              margin-top:5px;
              margin-bottom:10px;
            "
          >


      <label>Quantity:</label>
      <input type="number" id="quantity" min="0" value="0">

      <div style="display: flex; justify-content: center; margin-top: 15px;">
        <button class="walkin-button" id="checkoutBtn">Checkout</button>
      </div>

    </div>

    <div class="right-panel">
      <h2>Order Summary</h2>

      <div id="emptyState" style="text-align:center; padding:30px 15px; color:#777;">
        <p style="margin-bottom:6px;">Your purchased items will be displayed here.</p>
        <p style="font-size:13px;">Click <span style="font-weight:600;">Checkout</span> to add items to this summary.</p>
      </div>



      <!-- TABLE + BUTTON (nakahide pag empty) -->
      <div id="orderTableWrapper" style="display:none;">
        <table>
          <thead>
            <tr>
              <th>Image</th>
              <th>Name</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="orderTableBody"></tbody>
        </table>
        <button class="complete-btn walkin-button" id="completeBtn">Order Complete</button>
      </div>
    </div>
  </div>
</div>




<!-- Modal -->
<div id="orderModal" class="modal">
  <div class="modal-content">
    <p>Order Completed! Thank you 💕</p>
    <button class="close-btn walkin-button" id="closeModalBtn">Close</button>
  </div>
</div>

<!-- Receipt Modal -->
<div id="detailsModal" style="
  display:none;
  position:fixed;
  top:0; left:0;
  width:100%; height:100%;
  background:rgba(0,0,0,0.4);
  justify-content:center;
  align-items:center;
  z-index:1000;
">
  <div id="detailsModalContent" style="
    position:relative;
    background:#fff;
    border:2px solid #6D2E3A;
    border-radius:10px;
    padding:25px 35px;
    color: #6D2E3A;
    font-family:'Poppins', sans-serif;
    box-shadow:0 8px 15px rgba(0,0,0,0.3);
    width:auto;
    min-width:320px;
    max-width:450px;
    text-align:left;
  ">
    <span id="closeDetailsModal" style="
      position:absolute;
      top:10px;
      right:15px;
      cursor:pointer;
      font-weight:bold;
      font-size:20px;
      color:#6D2E3A;
    ">×</span>
    <h2 style="text-align:center; font-weight:700; margin-bottom:5px; font-size:22px; color: #6D2E3A;">
      Beauty and Blessed
    </h2>
    <div style="text-align:center; font-size:12px; margin-top:-3px; margin-bottom:10px; color:#A95469;">
      Brgy. 4, Nasugbu Batangas<br>
      +63 993 726 0000
    </div>
    <div style="border-bottom:2px dashed #A95469; margin-bottom:10px;"></div>
    <div style="margin:10px 0; line-height:1.6; font-size:14px; color:#6D2E3A;">
      <div><strong>Customer:</strong> <span id="receiptCustomer"></span></div>
      <div><strong>Purchase Method:</strong> <span id="receiptMethod"></span></div>
      <div><strong>Purchase Date:</strong> <span id="receiptDate"></span></div>
    </div>
    <div id="modalItems" style="
      border-top:1px dashed #A95469;
      border-bottom:1px dashed #A95469;
      padding:12px 0;
      font-size:14px;
      margin-bottom:10px;
    "></div>
    <div style="text-align:right; font-weight:700; font-size:16px; margin-top:10px; color:#6D2E3A;">
      Total: <span id="modalTotal">₱0.00</span>
    </div>
    <div style="text-align:center; margin-top:12px; font-style:italic; font-size:13px; color:#A95469;">
      Thank you for your purchase!
    </div>
    <div style="text-align:center; margin-top:15px; display:flex; justify-content:center; gap:8px;">
      <button class="walkin-button" id="printBtn" onclick="printReceipt()" style="
        border-radius:5px; padding:5px 12px; font-weight:600;
        cursor:pointer; font-size:13px;">🖨️ Print</button>
      <button class="walkin-button" id="exportPDFbtn" onclick="exportPDF()" style="
        border-radius:5px; padding:4px 10px; font-weight:600;
        cursor:pointer; font-size:13px;">📄 Export PDF</button>
    </div>
  </div>
</div>

<!-- PDF Loading Modal -->
<div id="pdfLoading" style="
  display:none;
  position:fixed;
  top:0; left:0;
  width:100%; height:100%; background-color:rgba(0,0,0,0.5);
  justify-content:center; align-items:center;
  z-index:99999;
">
  <div class="loader-container">
    <div class="spinner"></div>
    <div style="color:white; font-family:'Poppins'; font-size:14px;">
      Generating PDF...
    </div>
  </div>
</div>

<script>
function openReceiptModal(orderDetails) {
  document.getElementById('receiptCustomer').textContent = orderDetails.customer || '';
  document.getElementById('receiptMethod').textContent = 'Walk-In';
  document.getElementById('receiptDate').textContent = orderDetails.date || (new Date().toLocaleDateString());
  const itemsContainer = document.getElementById("modalItems");
  itemsContainer.innerHTML = "";
  let total = 0;
  orderDetails.items.forEach(item => {
    const div = document.createElement("div");
    div.style.display = "flex";
    div.style.justifyContent = "space-between";
    div.style.marginBottom = "8px";
    div.style.alignItems = "center";
    div.innerHTML = `
      <div style="display:flex;flex-direction:column;">
        <span style="font-weight:600; color:#6D2E3A;">${item.product_name}</span>
        <span style="font-size:12px; color:#A95469;">
          Price: ₱${parseFloat(item.amount).toFixed(2)} x ${item.quantity}
        </span>
      </div>
      <div style="font-weight:700; color:#6D2E3A;">
        ₱${(parseFloat(item.amount) * item.quantity).toFixed(2)}
      </div>
    `;
    itemsContainer.appendChild(div);
    total += parseFloat(item.amount) * parseInt(item.quantity);
  });
  document.getElementById("modalTotal").textContent = "₱" + total.toFixed(2);
  document.getElementById("detailsModal").style.display = "flex";
}

document.getElementById("closeDetailsModal").onclick = function() {
  document.getElementById("detailsModal").style.display = "none";
};

function printReceipt() {
  window.print();
}

// For exportPDF, sample using html2pdf (make sure lib is loaded)
function exportPDF() {
  const modalContent = document.getElementById("detailsModalContent");
  const closeBtn = document.getElementById("closeDetailsModal");
  const buttons = modalContent.querySelectorAll("button");

  document.getElementById("pdfLoading").style.display = "flex";
  closeBtn.style.display = "none";
  buttons.forEach(btn => btn.style.display = "none");

  const customer = document.getElementById("receiptCustomer").textContent.trim() || "Customer";
  const date = document.getElementById("receiptDate").textContent.trim().replace(/[^\w-]/g, "_") || "Date";
  const filename = `Receipt_${customer}_${date}.pdf`;

  const opt = {
    margin: 0.5,
    filename: filename,
    image: { type: "jpeg", quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: "in", format: "letter", orientation: "portrait" },
  };

  setTimeout(() => {
    html2pdf().set(opt).from(modalContent).save().then(() => {
      closeBtn.style.display = "";
      buttons.forEach(btn => btn.style.display = "");
      document.getElementById("pdfLoading").style.display = "none";
    });
  }, 800);
}


// last
const nameInput = document.getElementById("name");
const suggestionBox = document.getElementById("nameSuggestions");

nameInput.addEventListener("input", function() {
  const query = this.value.trim();
  if (query.length === 0) {
    suggestionBox.style.display = "none";
    return;
  }
  fetch(`order_dashboard.php?action=search&q=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      suggestionBox.innerHTML = "";
      if (data.length === 0) {
        suggestionBox.style.display = "none";
        return;
      }
      data.forEach(item => {
        const div = document.createElement("div");
        div.textContent = item.full_name;
        div.onclick = function() {
          nameInput.value = item.full_name;
          suggestionBox.style.display = "none";
        };
        suggestionBox.appendChild(div);
      });
      suggestionBox.style.display = "block";
    });
});

document.addEventListener("click", function(e) {
  if (!e.target.closest("#name")) suggestionBox.style.display = "none";
});

const categories = <?php
  $cat_sql = "SELECT category_id, category_name FROM category";
  $cat_res = $conn->query($cat_sql);
  $cats = [];
  while($row = $cat_res->fetch_assoc()) {
    $cats[$row['category_id']] = $row['category_name'];
  }
  echo json_encode($cats, JSON_UNESCAPED_SLASHES);
?>;

const productsByCategory = <?php
  $prod_sql = "SELECT product_id, category_id, product_name, price, image_path FROM products";
  $prod_res = $conn->query($prod_sql);
  $data = [];
  while($row = $prod_res->fetch_assoc()) {
    $cid = $row['category_id'];
    if(!isset($data[$cid])) $data[$cid] = [];
    $data[$cid][] = $row;
  }
  echo json_encode($data, JSON_UNESCAPED_SLASHES);
?>;

function setupCustomSelect(selectId, itemsList, onSelectCallback){
  const select = document.getElementById(selectId);
  const selected = select.querySelector(".select-selected");
  const items = select.querySelector(".select-items");
  items.innerHTML = "";
  itemsList.forEach(text=>{
    const div = document.createElement("div");
    div.textContent = text;
    div.addEventListener("click", ()=>{
      selected.textContent = text;
      items.querySelectorAll("div").forEach(opt=>opt.classList.remove("same-as-selected"));
      div.classList.add("same-as-selected");
      items.style.display = "none";
      if(onSelectCallback) onSelectCallback(text);
    });
    items.appendChild(div);
  });
  selected.onclick = (e)=>{
    e.stopPropagation();
    closeAllSelect(select);
    items.style.display = items.style.display==="block"?"none":"block";
  };
}

function closeAllSelect(except){
  document.querySelectorAll(".select-items").forEach(box=>{
    if(box.parentNode!==except) box.style.display="none";
  });
}
document.addEventListener("click", e=>{
  if(!e.target.matches('.select-selected')) closeAllSelect(null);
});

function populateProducts(categoryName){
  const categoryId = Object.keys(categories).find(key => categories[key] === categoryName);
  const products = productsByCategory[categoryId] || [];
  const productNames = products.map(p=>p.product_name);
  setupCustomSelect("productSelect", productNames);
  document.querySelector("#productSelect .select-selected").textContent="-- Select Product --";
  document.getElementById("quantity").value=0;
}

setupCustomSelect("categorySelect", Object.values(categories), populateProducts);
setupCustomSelect("productSelect", []);

// ✅ New function to check stock before adding
async function checkStock(productId, qty, productName) {
  const res = await fetch(`order_dashboard.php?action=check_stock&product_id=${productId}`);
  const data = await res.json();
  const stock = data.stocks ?? 0;

  if (stock === 0) {
    await Swal.fire({
      icon: 'error',
      title: 'Out of Stock!',
      text: `${productName} is currently unavailable.`,
      customClass: { popup: 'my-swal-size' }
    });
    return false;
  }

  if (stock <= 5) {
    await Swal.fire({
      icon: 'warning',
      title: 'Low Stock Warning!',
      text: `${productName} only has ${stock} left in stock.`,
      confirmButtonText: 'Proceed Anyway',
      customClass: { popup: 'my-swal-size' }
    });
  }

  if (qty > stock) {
    await Swal.fire({
      icon: 'error',
      title: 'Insufficient Stock!',
      text: `Only ${stock} pieces left of ${productName}.`,
      customClass: { popup: 'my-swal-size' }
    });
    return false;
  }

  return true;
}

// 🛒 Checkout button
document.getElementById("checkoutBtn").addEventListener("click", async ()=>{
  const full_name = document.getElementById("name").value.trim();
  const productName = document.querySelector("#productSelect .select-selected").textContent;
  const categoryName = document.querySelector("#categorySelect .select-selected").textContent;
  const qty = parseInt(document.getElementById("quantity").value);
  
  if(full_name === ""){
    Swal.fire({ title: "Oops!", text: "Please enter customer name!", icon: "warning", customClass: { popup: 'my-swal-size' } });
    return;
  }

  if(productName==="-- Select Product --"){
    Swal.fire({ title: "Oops!", text: "Please select product!", icon: "warning", customClass: { popup: 'my-swal-size' } });
    return;
  }

  if (qty == ""){
     Swal.fire({ title: "Invalid Quantity", text: "Enter quantity", icon: "error", customClass: { popup: 'my-swal-size' } });
    return;
  }
  else if (!qty || isNaN(qty) || qty <= 0) {
    Swal.fire({ title: "Invalid Quantity", text: "Quantity must be a positive number!", icon: "error", customClass: { popup: 'my-swal-size' } });
    return;
  }

  const catId = Object.keys(categories).find(key=>categories[key]===categoryName);
  const product = productsByCategory[catId].find(p=>p.product_name===productName);

  const canAdd = await checkStock(product.product_id, qty, productName);
  if (!canAdd) return;

   addToTable(productName, categoryName, qty);

  document.getElementById("name").disabled = true;
  document.querySelector("#categorySelect .select-selected").textContent = "-- Select Category --";
  document.querySelector("#productSelect .select-selected").textContent = "-- Select Product --";
  document.querySelector("#productSelect .select-items").innerHTML = "";
  document.getElementById("quantity").value = 0;
  document.getElementById("stockField").value = ""; // ← clear after Checkout

  Swal.fire({ title: "Added!", text: "Product added to summary.", icon: "success", customClass: { popup: 'my-swal-size' } });
});



document.getElementById("completeBtn").addEventListener("click", () => {
  const full_name = document.getElementById("name").value.trim();
  const tbody = document.getElementById("orderTableBody");

  if (full_name === "") {
    Swal.fire({ title: "Oops!", text: "Enter customer name first!", icon: "warning", customClass: { popup: 'my-swal-size' } });
    return;
  }
  if (tbody.querySelectorAll("tr").length === 0) {
    Swal.fire({ title: "Oops!", text: "No items in the order!", icon: "warning", customClass: { popup: 'my-swal-size' } });
    return;
  }

  let totalAmount = 0;
  tbody.querySelectorAll("tr").forEach(row => {
    const totalCell = row.querySelector(".total-cell");
    if (totalCell) {
      const val = parseFloat(totalCell.textContent.replace("₱", "").trim());
      if (!isNaN(val)) totalAmount += val;
    }
  });

  Swal.fire({
    title: "Complete Order?",
    text: `Save ${full_name}'s order total ₱${totalAmount.toFixed(2)}?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, Save",
    cancelButtonText: "Cancel",
    customClass: { popup: 'my-swal-size' }
  }).then(result => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Saving...",
        text: "Please wait.",
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); },
        customClass: { popup: 'my-swal-size' }
      });

      saveWalkIn(full_name, totalAmount)
        .then(response => {
          Swal.close();
          if (response.status === "success") {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              html: `Order completed, saved, and inventory updated successfully!`,
              showCancelButton: true,
              confirmButtonText: 'View Receipt',
              cancelButtonText: 'OK',
              customClass: { popup: 'my-swal-size' }
            }).then(result => {
              if (result.isConfirmed) {
                // => View Receipt Clicked
                const orderItems = getOrderItemsFromTable();
                openReceiptModal({
                  customer: full_name,
                  date: new Date().toLocaleDateString(),
                  items: orderItems
                });
                // Clear the table **after** the modal is closed
                document.getElementById("closeDetailsModal").onclick = function() {
                  document.getElementById("detailsModal").style.display = "none";
                  tbody.innerHTML = "";
                  document.getElementById("quantity").value = 0;
                  document.getElementById("name").value = "";
                  document.getElementById("name").disabled = false;
                  updateOrderSummaryView();
                };
              } else {
                // => OK or dismissed, clear immediately
                tbody.innerHTML = "";
                document.getElementById("quantity").value = 0;
                document.getElementById("name").value = "";
                document.getElementById("name").disabled = false;
                updateOrderSummaryView();
              }
            });
          } else {
            Swal.fire({ title: "Error", text: response.message, icon: "error", customClass: { popup: 'my-swal-size' } });
          }
        })
        .catch(err => {
          Swal.close();
          Swal.fire({ title: "Error", text: "Failed to save order.", icon: "error", customClass: { popup: 'my-swal-size' } });
          console.error(err);
        });
    }
  });
});


function saveWalkIn(full_name, totalAmount){
  const tbody = document.getElementById("orderTableBody");
  const items = [];

  tbody.querySelectorAll("tr").forEach(row => {
    const productId = row.dataset.productId;
    const qty = parseInt(row.querySelector("td:nth-child(4)").textContent);
    const amount = parseFloat(row.querySelector(".total-cell").textContent.replace(/[₱,]/g, "").trim());
    
    items.push({
      product_id: productId,
      quantity: qty,
      amount: amount
    });
  });

  const formData = new FormData();
  formData.append("action", "add_walkin");
  formData.append("full_name", full_name);
  formData.append("total", totalAmount);
  formData.append("items", JSON.stringify(items));

  return fetch("order_dashboard.php", {
    method: "POST",
    body: formData
  }).then(res => res.json());
}

function addToTable(productName, categoryName, qty){
  const tbody = document.getElementById("orderTableBody");
  const catId = Object.keys(categories).find(key=>categories[key]===categoryName);
  const product = productsByCategory[catId].find(p=>p.product_name===productName);
  const total = (parseFloat(product.price)*qty).toFixed(2);

  let imgPath = product.image_path || '';

  if (!imgPath.startsWith('pictures/')) {
    imgPath = 'pictures/' + imgPath;
  }

  const imgTag = `<img src="${imgPath}" class="product-img"
                  onerror="this.src='pictures/no-image.png'">`;

  const row = document.createElement("tr");
  row.dataset.productId = product.product_id;

  row.innerHTML = `
    <td>${imgTag}</td>
    <td>${productName}</td>
    <td>₱${product.price}</td>
    <td class="qty-cell">${qty}</td>
    <td class="total-cell">₱${total}</td>
    <td>
      <button class="edit-btn" onclick="editRow(this)">
        <i class="fa-solid fa-pen-to-square"></i>
      </button>
      <button class="delete-btn" onclick="deleteRow(this)">
        <i class="fa-solid fa-trash"></i>
      </button>
    </td>
  `;

  tbody.appendChild(row);
  updateOrderSummaryView(); 
}

function deleteRow(btn) {
  Swal.fire({
    title: "Remove Item?",
    text: "Do you want to remove this product from the order?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, Remove",
    cancelButtonText: "Cancel",
    customClass: { popup: 'my-swal-size' } 
  }).then((result) => {
    if (result.isConfirmed) {
      btn.closest("tr").remove();
      updateOrderSummaryView(); 

      Swal.fire({
        title: "Removed!",
        text: "Product has been removed.",
        icon: "success",
        customClass: { popup: "my-swal-size" }
      });
    }
  });
}

function editRow(btn) {
  const row = btn.closest("tr");
  const qtyCell = row.querySelector(".qty-cell");
  const totalCell = row.querySelector(".total-cell");
  const price = parseFloat(row.querySelector("td:nth-child(3)").textContent.replace("₱",""));
  const oldQty = parseInt(qtyCell.textContent);

  Swal.fire({
    title: "Edit Quantity",
    input: "number",
    inputValue: oldQty,
    inputAttributes: { min: 1 },
    showCancelButton: true,
    confirmButtonText: "Update",
    customClass: { popup: "my-swal-size" }
  }).then(result => {
    if (!result.isConfirmed) return;

    const newQty = parseInt(result.value);
    if (isNaN(newQty) || newQty <= 0) {
      Swal.fire({
        title: "Invalid",
        text: "Quantity must be greater than 0",
        icon: "error",
        customClass: { popup: "my-swal-size" }
      });
      return;
    }

    const newTotal = (price * newQty).toFixed(2);

    qtyCell.textContent = newQty;
    totalCell.textContent = `₱${newTotal}`;

    Swal.fire({
      title: "Updated!",
      text: "Quantity updated successfully.",
      icon: "success",
      customClass: { popup: "my-swal-size" }
    });
  });
} 

window.testReceiptModal = function() {
  openReceiptModal({
    customer: "Test Customer",
    date: "2025-11-21",
    items: [
      { product_name: "Lipstick", amount: 199, quantity: 2 },
      { product_name: "Blush", amount: 249, quantity: 1 }
    ]
  });
}

function getOrderItemsFromTable() {
    const items = [];
    const tbody = document.getElementById("orderTableBody");
    tbody.querySelectorAll("tr").forEach(row => {
        const tds = row.querySelectorAll("td");
        if (tds.length < 5) return; // skip incomplete rows
        const productName = tds[1]?.textContent.trim();
        const amount = parseFloat(tds[2]?.textContent.replace(/[₱,]/g, "").trim());
        const quantity = parseInt(tds[3]?.textContent.trim());
        if (productName && !isNaN(amount) && !isNaN(quantity)) {
            items.push({
                product_name: productName,
                amount: amount,
                quantity: quantity
            });
        }
    });
    console.log(items); // log mo ito para makita sa devtools
    return items;
}


function updateOrderSummaryView() {
  const tbody = document.getElementById("orderTableBody");
  const emptyState = document.getElementById("emptyState");
  const wrapper = document.getElementById("orderTableWrapper");

  const hasRows = tbody.querySelectorAll("tr").length > 0;

  if (hasRows) {
    emptyState.style.display = "none";
    wrapper.style.display = "block";
  } else {
    emptyState.style.display = "block";
    wrapper.style.display = "none";
  }
}

document.addEventListener("DOMContentLoaded", function () {
  updateOrderSummaryView();
});


function updateStockField(productId) {
  if (!productId) {
    document.getElementById('stockField').value = '';
    return;
  }

  fetch('order_dashboard.php?action=check_stock&product_id=' + productId)
    .then(res => res.json())
    .then(data => {
      const stockInput = document.getElementById('stockField');
      if (stockInput) {
        stockInput.value = (data.stocks ?? 0) + ' pcs';
      }
    })
    .catch(() => {
      const stockInput = document.getElementById('stockField');
      if (stockInput) {
        stockInput.value = 'N/A';
      }
    });
}

function populateProducts(categoryName){
  const categoryId = Object.keys(categories).find(key => categories[key] === categoryName);
  const products = productsByCategory[categoryId] || [];
  const productNames = products.map(p=>p.product_name);

  setupCustomSelect("productSelect", productNames, (selectedName) => {
    const selectedProduct = products.find(p => p.product_name === selectedName);
    if (selectedProduct) {
      updateStockField(selectedProduct.product_id); // ← DITO lalabas yung stock
    } else {
      updateStockField(null);
    }
  });

  document.querySelector("#productSelect .select-selected").textContent="-- Select Product --";
  document.getElementById("quantity").value=0;
  document.getElementById("stockField").value = ""; // reset kapag nagpalit ka ng category
}


</script>

</body>
</html>
