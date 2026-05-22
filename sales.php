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

include 'database.php';

// --- DATA GENERATION ---

$today = date("Y-m-d");

// Total sales today (walk-in + reservation)
$todaySales = $conn->query("
    SELECT COALESCE(SUM(totalAmount),0) AS total 
    FROM purchase 
    WHERE DATE(purchaseDate) = '$today'
")->fetch_assoc()['total'] ?? 0;

// Walk-in today
$todayWalkin = $conn->query("
    SELECT COALESCE(SUM(totalAmount),0) AS total 
    FROM purchase 
    WHERE DATE(purchaseDate) = '$today'
      AND purchaseMethod='Walk-In'
")->fetch_assoc()['total'] ?? 0;

// Reservation today
$todayReserve = $conn->query("
    SELECT COALESCE(SUM(totalAmount),0) AS total 
    FROM purchase 
    WHERE DATE(purchaseDate) = '$today'
      AND purchaseMethod='Reservation'
")->fetch_assoc()['total'] ?? 0;

// Orders today
$todayOrders = $conn->query("
    SELECT COUNT(*) AS total 
    FROM purchase 
    WHERE DATE(purchaseDate) = '$today'
")->fetch_assoc()['total'] ?? 0;

// --- WEEKLY SALES (Monday–Sunday) ---
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd   = date('Y-m-d', strtotime('sunday this week'));

$weeklySales = $conn->query("
    SELECT COALESCE(SUM(totalAmount),0) AS total
    FROM purchase 
    WHERE DATE(purchaseDate) BETWEEN '$weekStart' AND '$weekEnd'
")->fetch_assoc()['total'] ?? 0;

// --- TOP 5 PRODUCTS THIS WEEK ---
$topProducts = $conn->query("
    SELECT 
        products.product_name,
        SUM(purchase_items.quantity) AS qty
    FROM purchase_items
    INNER JOIN purchase 
        ON purchase_items.purchase_id = purchase.purchase_id
    INNER JOIN products 
        ON purchase_items.product_id = products.product_id
    WHERE DATE(purchase.purchaseDate) BETWEEN '$weekStart' AND '$weekEnd'
    GROUP BY purchase_items.product_id
    ORDER BY qty DESC
    LIMIT 5
");

$prodNames = [];
$prodQty = [];
if($topProducts){
    while ($row = $topProducts->fetch_assoc()) {
        $prodNames[] = $row['product_name'];
        $prodQty[] = (int)$row['qty'];
    }
}

// --- WEEKLY DAILY SALES GROUPED --- ADDED LOGIC
$weekDaily = $conn->query("
    SELECT 
        DATE(purchaseDate) AS d,
        COALESCE(SUM(CASE WHEN purchaseMethod='Walk-In' THEN totalAmount ELSE 0 END),0) AS walkin,
        COALESCE(SUM(CASE WHEN purchaseMethod='Reservation' THEN totalAmount ELSE 0 END),0) AS reservation
    FROM purchase
    WHERE DATE(purchaseDate) BETWEEN '$weekStart' AND '$weekEnd'
    GROUP BY DATE(purchaseDate)
    ORDER BY d ASC
");

$wdLabels = [];
$walkinTotals = [];
$reservationTotals = [];
if($weekDaily){
    while ($row = $weekDaily->fetch_assoc()) {
        $wdLabels[] = $row['d'];
        $walkinTotals[] = (float)$row['walkin'];
        $reservationTotals[] = (float)$row['reservation'];
    }
}

// --- WEEKLY SALES PER PRODUCT ---
$salesPerProduct = $conn->query("
    SELECT 
        products.product_name,
        SUM(purchase_items.quantity) AS total_qty,
        SUM(purchase_items.amount) AS total_sales
    FROM purchase_items
    INNER JOIN purchase 
        ON purchase_items.purchase_id = purchase.purchase_id
    INNER JOIN products 
        ON purchase_items.product_id = products.product_id
    WHERE DATE(purchase.purchaseDate) BETWEEN '$weekStart' AND '$weekEnd'
    GROUP BY purchase_items.product_id
    ORDER BY total_sales DESC
");

// --- DATE RANGE FILTER (default values) ---
$filterStart = isset($_GET['start']) ? $_GET['start'] : date("Y-m-01");
$filterEnd   = isset($_GET['end'])   ? $_GET['end']   : date("Y-m-d");

// For SQL Safety
$startSQL = $conn->real_escape_string($filterStart);
$endSQL   = $conn->real_escape_string($filterEnd);

// Total sales based on filter
$filteredSales = $conn->query("
    SELECT COALESCE(SUM(totalAmount),0) AS total 
    FROM purchase 
    WHERE DATE(purchaseDate) BETWEEN '$startSQL' AND '$endSQL'
")->fetch_assoc()['total'] ?? 0;

// SALES TABLE for print layout
$filteredRecords = $conn->query("
    SELECT purchase_id, purchaseDate, totalAmount, purchaseMethod
    FROM purchase
    WHERE DATE(purchaseDate) BETWEEN '$startSQL' AND '$endSQL'
    ORDER BY purchaseDate ASC
");


// Get today's walk-in and reservation sales/counter
$todayWalkinCount = $conn->query("
    SELECT COUNT(*) AS total 
    FROM purchase
    WHERE DATE(purchaseDate) = '$today'
      AND purchaseMethod='Walk-In'
")->fetch_assoc()['total'] ?? 0;

$todayReserveCount = $conn->query("
    SELECT COUNT(*) AS total 
    FROM purchase
    WHERE DATE(purchaseDate) = '$today'
      AND purchaseMethod='Reservation'
")->fetch_assoc()['total'] ?? 0;

$todayWalkinSales = $conn->query("
    SELECT COALESCE(SUM(totalAmount),0) AS total
    FROM purchase
    WHERE DATE(purchaseDate) = '$today'
      AND purchaseMethod='Walk-In'
")->fetch_assoc()['total'] ?? 0;

$todayReserveSales = $conn->query("
    SELECT COALESCE(SUM(totalAmount),0) AS total
    FROM purchase
    WHERE DATE(purchaseDate) = '$today'
      AND purchaseMethod='Reservation'
")->fetch_assoc()['total'] ?? 0;

$topProductsToday = $conn->query("
    SELECT
        products.product_name,
        SUM(purchase_items.quantity) AS qty
    FROM purchase_items
    INNER JOIN purchase ON purchase_items.purchase_id = purchase.purchase_id
    INNER JOIN products ON purchase_items.product_id = products.product_id
    WHERE DATE(purchase.purchaseDate) = '$today'
    GROUP BY purchase_items.product_id
    ORDER BY qty DESC
    LIMIT 5
");



    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        // Build the fragment HTML (the inner content that replaces #salesContent)
        ob_start();
?>

<?php
    $html = ob_get_clean();

    // Return JSON payload for AJAX usage
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'html' => $html,
        'wdLabels' => $wdLabels,
        'walkinTotals' => $walkinTotals,
        'reservationTotals' => $reservationTotals,
        'prodNames' => $prodNames,
        'prodQty' => $prodQty,
        'filterStart' => $filterStart,
        'filterEnd' => $filterEnd
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sales Dashboard</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* ============================
   COLORS & ROOT VARS
============================ */
html, body {
    overflow: auto;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE 10+ */
    margin: 0;
    padding: 0;
    overflow-x: hidden;
    overscroll-behavior: none;
    background: #f7f7f7;
}
html::-webkit-scrollbar, body::-webkit-scrollbar {
    display: none;
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

/* ============================
   MAIN CONTAINER & SPACING
============================ */
.container {
    max-width: 98vw;
    width: 100%;
    padding-left: 10px !important;
    padding-right: 10px !important;
    padding-top: 22px !important;
    margin: 0 auto;
}
@media (min-width: 1200px) {
    .container { max-width: 1280px; }
}

/* Pantay at malinis ang pagitan ng rows */
.row {
    margin-bottom: 0 !important;
}
.row.g-3, .row.g-4, .row.weekly-section {
    margin-bottom: 18px !important;
    margin-top: 0 !important;
}
.row.g-4 > div, .row.g-3 > div {
    padding-left: 10px;
    padding-right: 10px;
}
.mb-2 { margin-bottom: 10px !important; }
.mb-3 { margin-bottom: 15px !important; }
/* Para ayusin margin sa first row */
.row:first-child { margin-top: 0 !important; }

/* ============================
   CARD DESIGN
============================ */
.card, .card.shadow-sm, .card.shadow {
    border: none;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(232,169,178,0.08);
    margin-bottom: 0 !important;
}
.sales-custom-card {
    border: 2.4px solid #f8d7dc;
    background: #fff;
}
.custom-card { background-color: #f8d7dc; }
.custom-card_dash, .custom-card_fil { background-color: #fff5f9; }


.custom-card .card-body {
    background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%) !important;
    box-shadow: 0 10px 28px rgba(0,0,0,0.12) !important;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    justify-content: center;  /* vertical centering */
    align-items: center;      /* horizontal centering */
    height: 100%;             /* ensures card-body takes up full space */
    text-align: center;       /* aligns text for inner elements */
}

/* ============================
   CARD BODY LAYOUT
============================ */
.card-body {
    padding: 20px 20px !important;
    color: #6D2E3A;
    font-size: 1.11em;
}
.sales-custom-card .card-body { font-size: 1.13em; }

.list-unstyled li {
  display: flex;
  justify-content: space-between;
  align-items: flex-start; /* Important: para di pumantay sa gitna, kundi sa taas */
  padding-top: 13px;
  padding-bottom: 13px;
}

#top-rank {
  font-size: 1.4em;
  font-weight: 700;
  color: #D96D84;
  min-width: 38px;
}

#top-name {
  color: #6D2E3A;
  font-size: 1.1em;
  font-weight: 500;
  flex: 1;
  margin-left: 10px;
  margin-right: 16px;
  word-break: break-word; /* Allows wrapping */
  white-space: normal;    /* Reset to default so it wraps */
}

#top-sold {
  background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%) !important;
  padding: 8px 18px;
  border-radius: 6px;
  font-weight: 600;
  color: #6D2E3A;
  font-size: 1em;
  min-width: 65px;
  text-align: center;
  align-self: flex-start; /* Laging nasa taas ng row */
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

/* ============================
   SECTION TITLES & DIVIDERS
============================ */
.dashboard-section-title {
    color: #D96D84;
    font-weight: 800;
    font-size: 1.49em;
    letter-spacing: .6px;
    margin-bottom: 2px;
}
.dashboard-section-desc {
    font-size: 1em;
    color: #a95469;
    margin-bottom: 8px;
}
.section-divider {
    height: 3px;
    width: 190px;
    margin: 7px auto 17px auto;
    background: linear-gradient(90deg,#E8A9B2 15%, #fff 50%, #E8A9B2 85%);
    border-radius: 99px;
    opacity: 0.7;
}
.top-selling-divider {
    width:120px;height:2px;margin:13px auto 0 auto;
    background: linear-gradient(90deg,#E8A9B2 30%, #fff 70%);
    border-radius:99px;opacity:.5;
}

/* ============================
   TABLE DESIGN - SCROLL WRAPPER
============================ */
/*
   Ito ang critical: lagyan ng margin-bottom para laging may puwang (space)
   bago sumunod na card (gaya ng PRINT REPORTS card)
*/
.table-scroll-wrapper {
    max-height: 340px;
    overflow-y: auto;
    position: relative;
    margin-bottom: 22px !important; /* ——— Ito ang sikreto! */
}
.table-scroll-wrapper table {
    width: 100%;
    border-collapse: separate;
}
.table-scroll-wrapper thead th {
    position: sticky;
    top: 0;
    background: #a95469 !important;
    color: #fff !important;
    z-index: 4;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    padding: 12px 15px;
}
.table-scroll-wrapper tbody tr:nth-child(even) td { background: #F8D7DC !important; }
.table-scroll-wrapper tbody tr:nth-child(odd) td { background: #fff !important; }
.table, .table th, .table td {
    border-left: none !important;
    border-right: none !important;
}
.table th, .table td {
    padding: 12px 15px;
    text-align: left;
}
.table thead th {
    background-color: #a95469 !important;
    color: white !important;
}
.table tbody td { color: #6D2E3A !important; }
.table tbody tr:hover { background-color: #d96d84 !important; cursor: pointer; }
.table-striped tbody tr:nth-child(odd) { background-color: #df9494ff !important; }

.fa-print { color: var(--pink-mid); font-size: 1.3em; }

/* ============================
   BUTTONS 
============================ */
.custom-btnf, .custom-btnp {
    background-color: #D96D84;
    border-color: #D96D84;
    color: #fff !important;
}
.custom-btnp:hover, .custom-btnf:hover {
    background-color: #F8D7DC;
    border-color: #F8D7DC;
    color: #6D2E3A !important;
}

/* ============================
   RESPONSIVE CHANGES
============================ */
@media (max-width:768px) {
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
    .filter-form{width:100%;justify-content:center;flex-wrap:wrap;}
    .summary-card,
    .top-product-box { flex:1 1 calc(50% - 10px); min-width:180px;}
    .graph-box{flex:1 1 100%;}
    .chart-box canvas{width:100% !important;max-width:100%;height:auto !important;}
    .sales-custom-card { margin-bottom: 18px !important; }
    #top-sold { font-size: 0.7em !important; }
}
@media (max-width:480px) {
    .summary-card,.top-product-box{flex:1 1 100%;}
}

/* ============================
   PRINT-SPECIFIC
============================ */
@media print {
    body{ background:white !important; }
    .no-print{ display:none !important; }
}

.sales-custom-card {
    border: 2.4px solid #E8A9B2;
    border-radius: 14px;
    box-shadow: 10 10px 10px rgba(232,169,178,0.08);
    background: #ead5d5ff;
}

.table-scroll-wrapper {
    max-height: 340px;
    overflow-y: auto;
    overflow-x: hidden;
    position: relative;
}

/* Para hindi gumalaw ang header habang scroll */
.table-scroll-wrapper thead th {
    position: sticky;
    top: 0;
    background: #a95469 !important;
    color: #fff !important;
    z-index: 5;
}

.main-outer {
    height: calc(100vh - 90px); /* minus header height */
    overflow-y: auto;
    overflow-x: hidden;
}

/* Hide scrollbar for WebKit browsers */
.main-outer::-webkit-scrollbar {
    width: 0px;
    background: transparent; /* optional */
}

/* Hide scrollbar for Firefox */
.main-outer {
    scrollbar-width: none;
    -ms-overflow-style: none; /* IE 10+ */
}



</style>

    <div class="header-container no-print"> 
        <h1 class="heading" style="display: flex; align-items: center; font-size: 2em; font-weight: bold;">
            <i class="fa-solid fa-coins" style="margin-left: 12px; margin-right: 12px;"></i> Sales Overview
        </h1>
    </div>

<div class="main-outer">
    <div id="salesContent"><!-- initial content will be replaced by server-rendered fragment via AJAX mechanism below --></div>

    <div class="container">

            <!-- DAILY REPORTS TITLE + DIVIDER -->
            <div class="row"style="margin-top:18px;">
            <div class="col-12">
                <div class="dashboard-section-title text-center">
                Daily Sales Report Summary
                </div>
                <div class="dashboard-section-desc text-center">
                (For today's analytics and reporting)
                </div>
                <div class="section-divider mx-auto"></div>
            </div>
            </div>

                    <!-- CARDS -->
            <div class="row g-3 mb-2">
                <div class="col-md-3">
                    <div class="card shadow-sm custom-card">
                        <div class="card-body">
                            <h6>Sales Today</h6>
                            <h3>₱<?= number_format($todaySales, 2) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm custom-card">
                        <div class="card-body">
                            <h6>Walk-In Today</h6>
                            <h3>₱<?= number_format($todayWalkin, 2) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm custom-card">
                        <div class="card-body">
                            <h6>Reservation Today</h6>
                            <h3>₱<?= number_format($todayReserve, 2) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm custom-card">
                        <div class="card-body">
                            <h6>Total Customers Today</h6>
                            <h3><?= $todayOrders ?></h3>
                        </div>
                    </div>
                </div>
            </div>

    <div class="row mb-5 g-4">
        <!-- Left Card: Sales Method Breakdown (NO PRICE) -->
        <div class="col-md-6"style="margin-top:22px;">
            <div class="card shadow-sm h-100 sales-custom-card" style="background:#fff;">
                <div class="card-body p-4">
                    <h5 class="mb-4" style="color:#6D2E3A;font-weight:600;">Sales Method Breakdown</h5>
                    
                    <!-- Percentage Bars (walang price) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:#A95469;font-weight:500;">Walk-In</span>
                            <span style="color:#6D2E3A;font-weight:700;font-size:1.1em;">
                                <?= $todaySales > 0 ? round(($todayWalkin/$todaySales)*100) : 0 ?>%
                            </span>
                        </div>
                        <div class="progress" style="height:32px;border-radius:8px;background:#F8D7DC;">
                            <div class="progress-bar" style="width:<?= $todaySales > 0 ? round(($todayWalkin/$todaySales)*100) : 0 ?>%;background:#E8A9B2;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:#A95469;font-weight:500;">Reservation</span>
                            <span style="color:#6D2E3A;font-weight:700;font-size:1.1em;">
                                <?= $todaySales > 0 ? round(($todayReserve/$todaySales)*100) : 0 ?>%
                            </span>
                        </div>
                        <div class="progress" style="height:32px;border-radius:8px;background:#F8D7DC;">
                            <div class="progress-bar" style="width:<?= $todaySales > 0 ? round(($todayReserve/$todaySales)*100) : 0 ?>%;background:#D96D84;"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    <!-- Right Card: Top Selling Products Today (SAME HEIGHT) -->
    <div class="col-md-6"style="margin-top:22px;">
        <div class="card shadow-sm h-100 sales-custom-card"  style="background:#fff;">
            <div class="card-body p-4">
                <h5 class="mb-4" style="color:#6D2E3A;font-weight:600;">Top Selling Products Today</h5>
                
                <?php if($topProductsToday && $topProductsToday->num_rows): ?>
                    <ul class="list-unstyled" style="margin:0;">
                        <?php 
                        $rank = 1; 
                        $count = 0;
                        foreach($topProductsToday as $prod): 
                            if($count >= 2) break; // Show only 2 items to match height
                            $count++;
                        ?>
                        <li class="d-flex justify-content-between align-items-center py-3 mb-3" style="border-bottom:1px solid #F8D7DC;">
                            <div class="d-flex align-items-center gap-3">
                                <span id="top-rank" style="font-size:1.4em;font-weight:700;color:#D96D84;min-width:35px;">#<?= $rank++ ?></span>
                                <span id="top-name" style="color:#6D2E3A;font-size:1.1em;font-weight:500;"><?= htmlspecialchars($prod['product_name']) ?></span>
                            </div>
                            <span id="top-sold" style="background:#F8D7DC;padding:8px 18px;border-radius:6px;font-weight:600;color:#6D2E3A;font-size:1em;">
                                <?= $prod['qty'] ?> sold
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted text-center" style="margin:0;padding:40px 0;">No sales today.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

    <div class="row">
    <div class="col-12"style="margin-top:22px;">
        <div class="dashboard-section-title text-center">
        Weekly Report Summary
        </div>
        <div class="dashboard-section-desc text-center">
        (Covers Monday–Sunday, all recorded orders)
        </div>
        <div class="section-divider mx-auto"></div>
    </div>
    </div>

        <!-- WEEKLY SALES -->
        <div class="row mb-4"style="margin-top:22px;">
            <div class="col-md-12">
                <div class="card border-0 shadow custom-card">
                    <div class="card-body">
                        <h5 class="card-title mb-2">Total Sales This Week</h5>
                        <h2>₱<?= number_format($weeklySales, 2) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHARTS -->
        <div class="row g-4">

            <!-- WEEKLY SALES CHART -->
            <div class="col-md-8"style="margin-top:22px;">
                <div class="card shadow custom-card_dash">
                    <div class="card-body">
                        <h5 class="mb-3">Weekly Sales Overview</h5>

                        <!-- Canvas (Screen Only) -->
                        <canvas id="weeklySalesChart"></canvas>

                        <!-- For print (we populate src via JS) -->
                        <img id="printWeeklyChartImg" style="width:100%; display:none; margin-top:20px;">
                    </div>
                </div>
            </div>

            <!-- TOP PRODUCTS -->
            <div class="col-md-4"style="margin-top:22px;">
                <div class="card shadow custom-card_dash">
                    <div class="card-body">
                        <h5 class="mb-3">Top Products This Week</h5>

                        <!-- Canvas (Screen Only) -->
                        <canvas id="topProductsChart"></canvas>

                        <!-- For print -->
                        <img id="printTopProductsChartImg" style="width:100%; display:none; margin-top:20px;">
                    </div>
                </div>
            </div>

        </div>

    <!-- WEEKLY SALES PER PRODUCT TABLE -->
    <div class="row mt-3">
    <div class="col-md-12">
        <div class="card shadow">
        <div class="card-body">
            <h4 class="mb-3">Weekly Sales Per Product</h4>

            <!-- SCROLLABLE WRAPPER START -->
            <div class="table-scroll-wrapper">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Total Quantity Sold</th>
                    <th>Total Sales (₱)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($salesPerProduct) {
                    $salesPerProduct->data_seek(0);
                    while ($row = $salesPerProduct->fetch_assoc()) {
                    echo '<tr>
                        <td>' . htmlspecialchars($row['product_name']) . '</td>
                        <td>' . $row['total_qty'] . '</td>
                        <td>₱' . number_format($row['total_sales'], 2) . '</td>
                    </tr>';
                    }
                }
                ?>
                </tbody>
            </table>
            </div>
            <!-- SCROLLABLE WRAPPER END -->

        </div>
        </div>
    </div>
    </div>

    <!-- FILTERING & PRINT REPORT CARD (Moved to bottom) -->
    <div class="row mb-5" style="margin-top:24px;">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm custom-card_fil">
                <div class="card-header d-flex align-items-center">
                    <i class="fa-solid fa-print me-2"></i>
                    <h5 class="mb-0">PRINT REPORTS</h5>
                </div>
                <div class="card-body">
                    <form class="row g-3" id="salesFilterFormAjax">
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start" value="<?= htmlspecialchars($filterStart) ?>" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end" value="<?= htmlspecialchars($filterEnd) ?>" class="form-control" required>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn custom-btnf w-100" id="filterSubmitBtn">Filter</button>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" onclick="printReport()" class="btn custom-btnp w-100">
                                Print Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

        <!-- PRINT SECTION (hidden on screen, used to build print output) -->
        <div id="printSectionContent" style="display:none;">

            <h2>Sales Report (<?= htmlspecialchars($filterStart) ?> to <?= htmlspecialchars($filterEnd) ?>)</h2>

            <!-- SUMMARY CARDS FOR PRINT -->
            <h3>Summary</h3>
            <table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse;margin-bottom:20px;">
                <tr>
                    <th>Sales Today</th>
                    <td>₱<?= number_format($todaySales,2) ?></td>
                </tr>
                <tr>
                    <th>Walk-In Today</th>
                    <td>₱<?= number_format($todayWalkin,2) ?></td>
                </tr>
                <tr>
                    <th>Reservation Today</th>
                    <td>₱<?= number_format($todayReserve,2) ?></td>
                </tr>
                <tr>
                    <th>Total Orders Today</th>
                    <td><?= $todayOrders ?></td>
                </tr>
            </table>

            <!-- Charts will be inserted by JS -->
            <div id="printChartsContainer"></div>

            <h3 class="mt-3">Sales Records</h3>
            <table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($filteredRecords){
                        $filteredRecords->data_seek(0);
                        while ($row = $filteredRecords->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['purchaseDate']) ?></td>
                        <td><?= htmlspecialchars($row['purchaseMethod']) ?></td>
                        <td>₱<?= number_format($row['totalAmount'],2) ?></td>
                    </tr>
                    <?php endwhile;
                    }
                    ?>
                </tbody>
            </table>

            <h4>Total Sales: ₱<?= number_format($filteredSales, 2) ?></h4>
        </div>
    </div>
</div>

</head>
<body>


<script>

const initialData = {
    html: null,
    wdLabels: <?= json_encode($wdLabels) ?>,
    walkinTotals: <?= json_encode($walkinTotals) ?>,
    reservationTotals: <?= json_encode($reservationTotals) ?>,
    prodNames: <?= json_encode($prodNames) ?>,
    prodQty: <?= json_encode($prodQty) ?>,
    filterStart: <?= json_encode($filterStart) ?>,
    filterEnd: <?= json_encode($filterEnd) ?>
};

let weeklyChart = null;
let topChart = null;

// --- Render Charts ---
function renderCharts(labels, walkinTotals, reservationTotals, prodNames, prodQty) {
    // Destroy previous charts
    if (weeklyChart) { try { weeklyChart.destroy(); } catch(e){} weeklyChart = null; }
    if (topChart)    { try { topChart.destroy(); } catch(e){} topChart = null; }

    // Weekly grouped bar chart (walk-in & reservation side-by-side)
    const wkCtx = document.getElementById('weeklySalesChart');
    if (wkCtx) {
        weeklyChart = new Chart(wkCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Walk-In',
                        data: walkinTotals,
                        backgroundColor: '#F8D7DC',
                        borderColor: '#E8A9B2',
                        borderWidth: 1
                    },
                    {
                        label: 'Reservation',
                        data: reservationTotals,
                        backgroundColor: '#D96D84',
                        borderColor: '#A95469',
                        borderWidth: 1
                    }
                ]
            },
            options: { 
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#6D2E3A', font: {family: 'Poppins', size: 15, weight: '600'} } }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Top products chart
    const tpCtx = document.getElementById('topProductsChart');
    if (tpCtx) {
        topChart = new Chart(tpCtx, {
            type: 'pie',
            data: {
                labels: prodNames,
                datasets: [{
                    data: prodQty,
                    backgroundColor: ['#F8D7DC','#E8A9B2','#D96D84','#6D2E3A','#fff5f9']
                }]
            }
        });
    }
}

async function loadFragment(start = initialData.filterStart, end = initialData.filterEnd, pushState = true) {
    const url = `<?= basename(__FILE__) ?>?ajax=1&start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`;
    const res = await fetch(url);
    if (!res.ok) {
        document.getElementById('salesContent').innerHTML = '<div class="container"><div class="alert alert-danger">Failed to load sales data.</div></div>';
        return;
    }
    const data = await res.json();
    document.getElementById('salesContent').innerHTML = data.html;

    // render charts with new arrays!
    renderCharts(data.wdLabels, data.walkinTotals, data.reservationTotals, data.prodNames, data.prodQty);


    // attach event to newly inserted form and filter button
    const form = document.querySelector('#salesFilterFormAjax') || document.querySelector('#salesFilterForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const start = form.querySelector("input[name='start']").value;
            const end = form.querySelector("input[name='end']").value;
            // prevent double submission quickly by disabling button for 600ms
            const btn = form.querySelector('button[type="submit"], #filterSubmitBtn');
            if (btn) { btn.disabled = true; setTimeout(()=>btn.disabled=false,600); }
            loadFragment(start, end, true);
        });
    }

    // re-bind printReport function to new print button (if any)
    // (printReport uses canvases currently on the page)
    // update history
    if (pushState && history && history.replaceState) {
        const newUrl = `${location.pathname}?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`;
        history.replaceState(null, '', newUrl);
    }
}

// ----------------------- Print: open clean window with charts + table -----------------------
function printReport() {
    // ensure charts images exist (generate from canvases)
    const wkCan = document.getElementById('weeklySalesChart');
    const tpCan = document.getElementById('topProductsChart');

    const wkImgSrc = wkCan ? wkCan.toDataURL('image/png') : null;
    const tpImgSrc = tpCan ? tpCan.toDataURL('image/png') : null;

    // get print-section HTML from hidden element inserted by server
    const printSection = document.getElementById('printSectionContent');
    if (!printSection) {
        alert('Print section not found.');
        return;
    }

    // clone the print section so we can inject images
    const clone = printSection.cloneNode(true);
    clone.style.display = 'block';

    // insert images into #printChartsContainer
    const chartsContainer = clone.querySelector('#printChartsContainer');
    if (chartsContainer) {
        if (wkImgSrc) {
            const img = document.createElement('img');
            img.src = wkImgSrc;
            img.style.width = '100%';
            img.style.display = 'block';
            img.style.marginBottom = '12px';
            chartsContainer.appendChild(img);
        }
        if (tpImgSrc) {
            const img2 = document.createElement('img');
            img2.src = tpImgSrc;
            img2.style.width = '60%';
            img2.style.display = 'block';
            img2.style.marginBottom = '12px';
            chartsContainer.appendChild(img2);
        }
    }

    // open new window and write minimal html for printing
    const printWindow = window.open('', '_blank', 'width=900,height=700');
    const css = `
        <style>
            body{ font-family: Arial, Helvetica, sans-serif; padding:20px; color:#222; }
            table{ width:100%; border-collapse:collapse; margin-top:10px; }
            table, th, td { border:1px solid #444; }
            th, td { padding:8px 6px; text-align:left; }
            h2,h3,h4{ margin:6px 0; }
        </style>
    `;
    printWindow.document.open();
    printWindow.document.write('<!doctype html><html><head><meta charset="utf-8"><title>Sales Report Print</title>' + css + '</head><body>');
    printWindow.document.write(clone.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    // Wait a little to allow images to load, then print
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        // optionally close the window after printing
        // printWindow.close();
    }, 700);
}

// ----------------------- On page load: load fragment with defaults -----------------------
document.addEventListener('DOMContentLoaded', function(){
    // initial load uses server's computed default filterStart/filterEnd
    loadFragment(initialData.filterStart, initialData.filterEnd, false);
});
</script>

</body>
</html>
