<?php
include 'database.php';

// --- GET FILTER SELECTION ---
$selectedMonth = $_GET['month'] ?? 'all';
$selectedYear  = $_GET['year'] ?? 'all';

// --- CLEAN MONTH LIST (NO “all” INSIDE) ---
$months = [
    '01'=>'January','02'=>'February','03'=>'March','04'=>'April',
    '05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September',
    '10'=>'October','11'=>'November','12'=>'December'
];

// Build a clean list of numeric years; we'll render "All" manually with value="all"
$yearOptions = range(2023, date('Y'));

// --- CENTRAL FILTER FUNCTION ---
function getWhereCondition($month, $year) {
    $conditions = [];

    if ($month !== 'all') {
        $conditions[] = "MONTH(purchaseDate) = '$month'";
    }

    if ($year !== 'all') {
        $conditions[] = "YEAR(purchaseDate) = '$year'";
    }

    return empty($conditions) ? "1" : implode(" AND ", $conditions);
}

$whereCard = getWhereCondition($selectedMonth, $selectedYear);

// --- DASHBOARD DATA ---
$total_sales = $total_reservations = $total_walkin = $total_products = 0;

// --- TOTAL SALES ---
$res = $conn->query("SELECT SUM(totalAmount) AS total_sales FROM purchase WHERE $whereCard");
$total_sales = ($res && $row=$res->fetch_assoc()) ? ($row['total_sales'] ?? 0) : 0;

// --- TOTAL CUSTOMERS (unique customer count) ---
$res = $conn->query("
    SELECT COUNT(DISTINCT 
        CASE 
            WHEN walk_in_id IS NOT NULL THEN walk_in_id
            WHEN reservation_id IS NOT NULL THEN reservation_id
            ELSE NULL
        END
    ) AS total_customers
    FROM purchase
    WHERE $whereCard
");
$total_customers = ($res && $row=$res->fetch_assoc()) ? ($row['total_customers'] ?? 0) : 0;


// --- TOTAL WALK-IN ORDERS ---
$res = $conn->query("SELECT COUNT(*) AS total_walkin FROM purchase WHERE purchaseMethod='Walk-In' AND $whereCard");
$total_walkin = ($res && $row=$res->fetch_assoc()) ? ($row['total_walkin'] ?? 0) : 0;

// --- TOTAL RESERVATIONS ---
$res = $conn->query("SELECT COUNT(*) AS total_reservations FROM purchase WHERE purchaseMethod='Reservation' AND $whereCard");
$total_reservations = ($res && $row=$res->fetch_assoc()) ? ($row['total_reservations'] ?? 0) : 0;

// --- TOTAL PRODUCTS ---
$res = $conn->query("SELECT COUNT(*) AS total_products FROM products");
$total_products = ($res && $row=$res->fetch_assoc()) ? ($row['total_products'] ?? 0) : 0;

// --- PIE CHART: Sales by Category ---
$category_sales = [];
$res = $conn->query("
    SELECT c.category_name, SUM(pi.amount) AS total
    FROM purchase_items pi
    JOIN products pr ON pi.product_id = pr.product_id
    JOIN category c ON pr.category_id = c.category_id
    JOIN purchase p ON p.purchase_id = pi.purchase_id
    WHERE $whereCard
    GROUP BY c.category_name
");
if($res) while($row=$res->fetch_assoc()) $category_sales[$row['category_name']] = $row['total'] ?? 0;

// --- MONTHLY REVENUE CHART ---
$monthly_revenue = [];
$allMonthNames = array_values($months);
foreach($allMonthNames as $mname) {
    $monthly_revenue[$mname] = ['walk-in'=>0,'reservation'=>0];
}

$res = $conn->query("
    SELECT MONTHNAME(purchaseDate) AS month, purchaseMethod, SUM(totalAmount) AS total
    FROM purchase
    WHERE $whereCard
    GROUP BY MONTH(purchaseDate), purchaseMethod
    ORDER BY MONTH(purchaseDate)
");
if($res){
    while($row=$res->fetch_assoc()){
        $month = $row['month'];
        $methodKey = ($row['purchaseMethod']=='Walk-In') ? 'walk-in' : 'reservation';
        if(isset($monthly_revenue[$month])){
            $monthly_revenue[$month][$methodKey] = $row['total'] ?? 0;
        }
    }
}

$top_customers = [];
$res = $conn->query("
    SELECT
        CASE
            WHEN p.walk_in_id IS NOT NULL THEN COALESCE(w.walk_in_name, 'Anonymous')
            WHEN p.reservation_id IS NOT NULL THEN 
                COALESCE(CONCAT(r.register_fname,' ',r.register_lname), 'Anonymous')
            ELSE 'Anonymous'
        END AS customer_name,
        SUM(p.totalAmount) AS total_spent
    FROM purchase p
    LEFT JOIN walk_in w ON p.walk_in_id = w.walk_in_id
    LEFT JOIN reservations resv ON p.reservation_id = resv.reservation_id
    LEFT JOIN registers_tb r ON resv.register_id = r.register_id
    WHERE $whereCard
    GROUP BY customer_name
    ORDER BY total_spent DESC
    LIMIT 10
");

if($res) while($row=$res->fetch_assoc()) $top_customers[] = $row;


// --- TOP 8 PRODUCTS ---
$top_products = [];
$res = $conn->query("
    SELECT pr.product_name, pr.image_path, SUM(pi.quantity) AS total_sold
    FROM purchase_items pi
    JOIN products pr ON pi.product_id = pr.product_id
    JOIN purchase p ON pi.purchase_id = p.purchase_id
    WHERE $whereCard
    GROUP BY pr.product_id
    ORDER BY total_sold DESC
    LIMIT 8
");
if($res) while($row=$res->fetch_assoc()) $top_products[] = $row;

// --- HELPER FUNCTION ---
function formatNumberShort($num) {
    if(!is_numeric($num)) return $num;
    if($num >= 1000000) return round($num/1000000,1) . 'M';
    if($num >= 1000)    return round($num/1000,0) . 'k';
    return $num;
}

// --- TOTAL DATA CHECK ---
$totalData = ($total_sales ?? 0)
           + ($total_reservations ?? 0)
           + ($total_walkin ?? 0)
           + ($total_products ?? 0)
           + array_sum($category_sales)
           + array_sum(array_map(fn($m)=>($m['walk-in'] ?? 0)+($m['reservation'] ?? 0), $monthly_revenue));
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

html::-webkit-scrollbar,
body::-webkit-scrollbar {
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

body, * {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    color: var(--text-main);
}

h1, h2, h3, h4, h5, h6 {
    color: var(--text-main);
    font-weight: 700;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
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

.filter-form {
    display: flex;
    gap: 10px;
}

.filter-form input,
.filter-form select,
.filter-form textarea {
    border: 2px solid var(--pink-dark);
    border-radius: 6px;
    padding: 4px 6px !important;
    background: #fff;
    color: #6d2e3a;
    outline: none;
}

.filter-form input:focus,
.filter-form select:focus,
.filter-form textarea:focus {
    border-color: var(--pink-dark);
    box-shadow: 0 0 4px rgba(169,84,105,0.5);
}

.filter-form select {
    color: #6d2e3a !important;
    height: 30px !important;
    font-size: 14px;
    font-weight: 300;
    align-items: center !important;
    justify-content: center;
}

.filter-button {
    max-height: 30px;
    width: 60px !important;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 1px;
    color: #fff;
    background-color: #a95469;
    border: none !important;
    align-items: center !important;
    justify-content: center;
    line-height: 1; 
    border-radius: 8px;
    padding: 0;
    cursor: pointer;
}

.filter-button:hover {
    background-color: #a95469;
}

.summary-cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 20px;
}

.summary-card {
    flex: 1;
    min-width: 180px;
    border: none;
    border-radius: 16px;
    padding: 20px;
    background: var(--pink-very-light);
    box-shadow: 0 4px 12px rgba(217,109,132,0.1);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px; /* space between icon, label, value */
}

.summary-card i {
    color: #6d2e3a;
    font-size: 28px;
}

.summary-card .card-label {
    color: #6d2e3a;
    font-weight: 600;
    font-size: 0.95em;
}

.summary-card .card-value {
    color: #D96D84;
    font-weight: 700;
    font-size: 1.3em;
}

.top-product-box,
.top-customer-box {
    border: none;
    border-radius: 16px;
    padding: 20px;
    background: var(--pink-very-light);
    box-shadow: 0 4px 12px rgba(217,109,132,0.12);
}

table {
    border-color: var(--border-main);
    border-collapse: collapse;
    width: 100%;
}

table th {
    background: var(--pink-soft);
    color: var(--pink-dark);
    padding: 8px;
}

table td {
    color: var(--text-soft);
    border: 1px solid var(--border-main);
    padding: 6px;
}

button, .btn {
    border: 2px solid var(--btn-bg);
    background: var(--btn-bg);
    color: #fff;
    border-radius: 8px;
    padding: 6px 12px;
    cursor: pointer;
}

button:hover,
.btn:hover {
    background: var(--btn-hover);
    border-color: var(--btn-hover);
}

a {
    color: var(--text-soft);
}
a:hover {
    color: var(--pink-dark);
}

.chart-box,
.chart-container,
.graph-box {
    width: 100%;
    min-height: 280px;
    max-width: 100%;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(217,109,132,0.1);
    background: linear-gradient(to bottom, #fff0f4, #ffe6eb);
}

.graph-section {
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    justify-content: center;
    align-items: flex-start;
}

.graph-box {
    flex: 1;
    min-width: 380px;
}

@media (max-width: 768px) {
    .filter-form {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
    }
    .summary-card {
        flex: 1 1 calc(50% - 10px);
        min-width: 180px;
    }
    .top-product-box {
        flex: 1 1 calc(50% - 20px);
    }
    .graph-box {
        flex: 1 1 100%;
    }
    .chart-box canvas {
        width: 100% !important;
        max-width: 100%;
        height: auto !important;
    }
}

@media (max-width: 480px) {
    .summary-card,
    .top-product-box {
        flex: 1 1 100%;
    }
}

.icon-pink {
    color: #d6336c;
}

.summary-cards,
.graph-section,
.top-section {
    margin-bottom: 25px;
}

.products-grid img {
    width: 100%;
    max-width: 110px;
    height: 100px;
    object-fit: contain;
    border-radius: 10px;
    background: #fff; /* White bg for image only */
    margin-bottom: 7px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.05);
}

.products-grid p {
    font-size: 0.95em; /* Smaller font for product name */
    margin-bottom: 2px;
}

.products-grid small {
    font-size: 0.90em; /* Smaller font for sold label */
    color: #6d2e3a;
}

.products-grid > div {
    background: #f8d7dc; /* Pink bg for the card */
}

@media (max-width: 767px) {
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
}

/* Responsive layout for product images */
@media (max-width: 600px) {
    .products-grid {
        display: flex !important;
        flex-direction: column !important;
        gap: 18px !important;
    }
    .products-grid > div {
        width: 100% !important;
        max-width: 360px;
        margin: 0 auto;
    }
    .products-grid img {
        width: 100% !important;
        max-width: 150px;
        height: auto;
        margin-bottom: 8px;
    }
}

</style>
</head>
<body>

<div class="header-container">
    <h1 class="heading" style="display: flex; align-items: center; font-size: 2em; font-weight: bold;">
        <i class="fa-solid fa-chart-line" style="margin-left: 12px; margin-right: 12px;"></i> Dashboard Overview
    </h1>

    <form method="GET" class="filter-form">
        <select name="month">
            <option value="all" <?php if($selectedMonth=='all') echo 'selected'; ?>>All Months</option>
            <?php foreach($months as $num=>$name): ?>
                <option value="<?php echo $num; ?>" <?php if($num==$selectedMonth) echo 'selected'; ?>><?php echo $name; ?></option>
            <?php endforeach; ?>
        </select>

        <select name="year">
            <option value="all" <?php if($selectedYear=='all') echo 'selected'; ?>>All Years</option>
            <?php foreach($yearOptions as $y): ?>
                <option value="<?php echo $y; ?>" <?php if((string)$y=== (string)$selectedYear) echo 'selected'; ?>><?php echo $y; ?></option>
            <?php endforeach; ?>
        </select>

        <button class="filter-button" type="submit">Filter</button>
    </form>
</div>

<div style="max-height:calc(100vh - 80px);overflow-y:auto;padding:15px;">
<?php if($totalData==0): ?>
    <p style="text-align:center;font-weight:700;font-size:1.2em;">No data for selected month/year</p>
<?php else: ?>

    <!-- SUMMARY CARDS -->
<div class="summary-cards">
    <?php
    $cards = [
        ['fa-solid fa-money-bill-wave','Total Sales','₱'.number_format($total_sales,2)],
        ['fa-solid fa-users','Total Customers',$total_customers],
        ['fa-solid fa-box','Products',$total_products],
        ['fa-solid fa-calendar-check','Reservations',$total_reservations],
        ['fa-solid fa-store','Walk-in Orders',$total_walkin]
    ];
    foreach($cards as $card):
    ?>
    <div class="summary-card">
       <i class="<?php echo $card[0]; ?> icon-pink" style="font-size:28px;margin-bottom:8px;"></i>
        <p class="card-label"><?php echo $card[1]; ?></p>
        <h2 class="card-value">
            <?php 
            if(is_numeric(str_replace(['₱',','],'',$card[2]))){
                $numOnly = floatval(str_replace(['₱',','],'',$card[2]));
                echo (strpos($card[2],'₱')!==false?'₱':'') . formatNumberShort($numOnly);
            } else echo $card[2];
            ?>
        </h2>
    </div>
    <?php endforeach; ?>
</div>

    <!-- GRAPHS -->
    <div class="graph-section">
        <div class="graph-box">
            <h3 style="text-align:center;"><i class="fa-solid fa-pie-chart"></i> Sales by Category</h3>
            <canvas id="categoryPieChart" height="250"></canvas>
        </div>

        <div class="graph-box">
            <h3 style="text-align:center;"><i class="fa-solid fa-chart-column"></i> Monthly Revenue</h3>
            <canvas id="monthlyBarChart" height="250"></canvas>
        </div>
    </div>

  <!-- TOP CUSTOMERS & TOP PRODUCTS SIDE-BY-SIDE -->
<div class="top-section" style="display:flex;flex-wrap:wrap;gap:20px;justify-content:center;align-items:flex-start;">

    <!-- TOP CUSTOMERS -->
    <div class="top-customer-box" style="flex:0 0 380px;max-width:400px;">
        <h3 class="pink-heading">Top Customers</h3>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <?php $rank=1; foreach($top_customers as $c): ?>
            <div style="display:flex;align-items:center;justify-content:space-between;background:#fff;border-radius:12px;padding:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="background:#d6336c;color:#fff;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.9em;"><?php echo $rank++; ?></div>
                    <span><?php echo htmlspecialchars($c['customer_name']); ?></span>
                </div>
                <span>₱<?php echo formatNumberShort($c['total_spent']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

  
<div class="top-product-box" style="width:100%;max-width:800px;margin:0 auto;">
  <h3 class="pink-heading" style="margin-bottom:20px;text-align:center;font-size:1.4em;">Top Products</h3>
  <div class="products-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
    <?php $rank=1; foreach($top_products as $p): ?>
      <div style="
        background: #fff;
        border-radius:16px;
        box-shadow:0 3px 8px rgba(0,0,0,0.10);
        display:flex;
        flex-direction:column;
        align-items:center;
        padding:16px 10px 10px 10px;
        min-width:0;
      ">
        <!-- Image: white bg, full contain, no crop -->
        <img src="<?php echo htmlspecialchars($p['image_path']); ?>"
            alt="Product"
            style="
              width:100%;
              max-width:110px;
              height:100px;
              object-fit:contain;
              border-radius:10px;
              background:#fff;
              margin-bottom:7px;
              box-shadow:0 1px 6px rgba(0,0,0,0.05);
            ">
        <p style="font-weight:600;text-align:center;font-size:0.95em; margin-bottom:2px;">
          <?php echo htmlspecialchars($p['product_name']); ?>
        </p>
        <small style="font-size:0.90em;color:#6d2e3a;">Sold: <?php echo $p['total_sold']; ?></small>
      </div>
    <?php endforeach; ?>
  </div>
</div>



</div>
<?php endif; ?>
</div>

<div class="modal fade" id="noDataModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-body">
        <h4 class="mb-2 text-danger"><i class="fa-solid fa-circle-info me-2"></i> No Data Available</h4>
        <div>No chart can be displayed right now for the selected filter/date.</div>
        <button type="button" class="btn btn-secondary mt-3" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// PIE CHART (same)
const categoryLabels = <?php echo json_encode(array_keys($category_sales)); ?>;
const categoryData   = <?php echo json_encode(array_values($category_sales)); ?>;
const piePalette = ['#F8D7DC','#E8A9B2','#D96D84','#A95469','#6D2E3A'];

new Chart(document.getElementById('categoryPieChart'),{
    type:'pie',
    data:{labels:categoryLabels,datasets:[{data:categoryData,backgroundColor:piePalette}]},
    options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{family:'Poppins'},color:'#6D2E3A'}}}}
});

// BAR CHART
const monthlyLabels  = <?php echo json_encode($allMonthNames); ?>;
const walkinData     = <?php echo json_encode(array_map(fn($m)=>$m['walk-in'],$monthly_revenue)); ?>;
const reservationData= <?php echo json_encode(array_map(fn($m)=>$m['reservation'],$monthly_revenue)); ?>;

// ✅ FIXED COLORS: Walk-in = light pink, Reservation = deep pink
const walkinColor = 'rgba(255,135,180,0.8)';
const reservationColor = 'rgba(198,44,99,0.8)';

new Chart(document.getElementById('monthlyBarChart'),{
    type:'bar',
    data:{
        labels: monthlyLabels,
        datasets:[
            {
                label:'Walk-in',
                data: walkinData,
                backgroundColor: walkinColor,
                borderColor: walkinColor.replace('0.8','1'),
                borderWidth:1
            },
            {
                label:'Reservation',
                data: reservationData,
                backgroundColor: reservationColor,
                borderColor: reservationColor.replace('0.8','1'),
                borderWidth:1
            }
        ]
    },
    options:{
        responsive:true,
        plugins:{legend:{labels:{font:{family:'Poppins'},color:'#6D2E3A'}}},
        scales:{
            y:{beginAtZero:true,title:{display:true,text:'Revenue (₱)',font:{family:'Poppins',weight:'600',size:14},color:'#6D2E3A'}},
            x:{title:{display:true,text:'Month',font:{family:'Poppins',weight:'600',size:14},color:'#6D2E3A'}}
        }
    }
});



// Condition: Is there any data in categoryData or walkinData or reservationData?
const hasPieData = categoryData.some(val => val > 0);
const hasBarData = (walkinData.some(val => val > 0) || reservationData.some(val => val > 0));

if (!hasPieData && !hasBarData) {
  var modal = new bootstrap.Modal(document.getElementById('noDataModal'));
  modal.show();
}


</script>


</body>
</html>
