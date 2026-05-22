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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'database.php';

// ==========================
// AUDIT LOG FUNCTION
// ==========================
function addAuditLog($conn, $role, $task) {
    $stmt = $conn->prepare("INSERT INTO audit_logs (role, task_performed, date_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $role, $task);
    $stmt->execute();
    $stmt->close();
}

// --- CATEGORY CRUD HANDLERS ---
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === "add_category") {
        $name = trim($_POST['category_name']);
        if (!empty($name)) {
            $stmt = $conn->prepare("INSERT INTO category (category_name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $last_id = $stmt->insert_id;
            $stmt->close();

               // AUDIT LOG
            addAuditLog($conn, "Admin", "Add Category ($name)");

            echo json_encode([
                "status" => "success",
                "message" => "Category added successfully!",
                "id" => $last_id,
                "name" => $name
            ]);
        }
        exit;
    }

    if ($action === "update_category") {
        $id = $_POST['category_id'];
        $name = trim($_POST['category_name']);

        $stmt = $conn->prepare("UPDATE category SET category_name=? WHERE category_id=?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $stmt->close();

        // AUDIT LOG
        addAuditLog($conn, "Admin", "Update Category (ID: $id, Name: $name)");

        echo json_encode([
            "status" => "success",
            "message" => "Category updated successfully!",
            "id" => $id,
            "name" => $name
        ]);
        exit;
    }

    if ($action === "delete_category") {
        $id = $_POST['category_id'];

        // Delete products under category
        $stmt = $conn->prepare("DELETE FROM products WHERE category_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Delete category
        $stmt2 = $conn->prepare("DELETE FROM category WHERE category_id=?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $stmt2->close();

        // AUDIT LOG
        addAuditLog($conn, "Admin", "Delete Category (ID: $id)");

        echo json_encode([
            "status" => "success",
            "message" => "Category and related products deleted successfully!",
            "id" => $id
        ]);
        exit;
    }
}

// --- ADD PRODUCT ---
if (isset($_POST['add_product'])) {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stocks = isset($_POST['stocks']) ? intval($_POST['stocks']) : 0;

    if ($stocks < 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Stocks cannot be negative."
        ]);
        exit;
    }

    // In your ADD PRODUCT section, replace the file upload code:
$image = "";
if (!empty($_FILES['image']['name'])) {
    $targetDir = "pictures/";
    
    // Create directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $originalName = basename($_FILES['image']['name']);
    $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    
    // Whitelist allowed extensions
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
    
    if (in_array($fileExt, $allowedExts)) {
        // Optional: Rename with timestamp to avoid duplicates
        $fileName = time() . '_' . $originalName;
        $targetFilePath = $targetDir . $fileName;
        
        // Verify file was uploaded and move it
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $image = $fileName; // Save only the filename
        } else {
            $image = ""; // If upload fails, leave empty
        }
    } else {
        // Unsupported file type - set error or ignore
        echo json_encode([
            "status" => "error",
            "message" => "Only JPG, PNG, GIF, WEBP, BMP files are allowed."
        ]);
        exit;
    }
}

    // Insert product
    $stmt = $conn->prepare("INSERT INTO products (category_id, product_name, price, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $category_id, $name, $price, $image);
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();

    // Insert into inventory
    $stmt2 = $conn->prepare("INSERT INTO inventory (product_id, stocks, sold_count) VALUES (?, ?, 0)");
    $stmt2->bind_param("ii", $product_id, $stocks);
    $stmt2->execute();
    $stmt2->close();

    // AUDIT LOG
    addAuditLog($conn, "Admin", "Add Product ($name)");

    echo json_encode(["status" => "success", "action" => "product_added", "product_id" => $product_id]);
    exit;
}


// --- UPDATE PRODUCT ---
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stocks = isset($_POST['stocks']) ? intval($_POST['stocks']) : 0;
    $image = $_POST['existing_image'];

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "pictures/";
        $image = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath);
    }
    
    
    
    // Get old stock before update
    $oldStocks = 0;
    $getOldStmt = $conn->prepare("SELECT stocks FROM inventory WHERE product_id=?");
    $getOldStmt->bind_param("i", $product_id);
    $getOldStmt->execute();
    $oldResult = $getOldStmt->get_result();
    if ($oldResult && $oldResult->num_rows > 0) {
        $oldRow = $oldResult->fetch_assoc();
        $oldStocks = intval($oldRow['stocks']);
    }
    $getOldStmt->close();
    

    // --- UPDATE PRODUCT INFO
    $stmt = $conn->prepare("UPDATE products SET category_id=?, product_name=?, price=?, image_path=? WHERE product_id=?");
    $stmt->bind_param("isdsi", $category_id, $name, $price, $image, $product_id);
    $stmt->execute();
    $stmt->close();

    // --- UPDATE STOCKS IN INVENTORY
    $check = $conn->prepare("SELECT inventory_id FROM inventory WHERE product_id=?");
    $check->bind_param("i", $product_id);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows > 0) {
        $stmt2 = $conn->prepare("UPDATE inventory SET stocks=? WHERE product_id=?");
        $stmt2->bind_param("ii", $stocks, $product_id);
        $stmt2->execute();
        $stmt2->close();
    } else {
        $stmt2 = $conn->prepare("INSERT INTO inventory (product_id, stocks, sold_count) VALUES (?, ?, 0)");
        $stmt2->bind_param("ii", $product_id, $stocks);
        $stmt2->execute();
        $stmt2->close();
    }


  $min_stock_level = 3;
    $admin_id = 1;
    $admin_email = "detorresjanellemae@gmail.com";
    $admin_headers = "From: reservationsystem@beautyandblessed.online\r\n";

    // --- RESTOCK NOTIFICATION ---
    if ($oldStocks <= $min_stock_level && $stocks > $min_stock_level) {
        $notif_message = "Restock Alert! {$name} now has {$stocks} in stock.";
        $notif_type = "restock";
        $notif_link = "add_product.php";
        $notifSql = "INSERT INTO notifadmin (register_id, notif_message, notif_type, notif_link) VALUES (?, ?, ?, ?)";
        $notifStmt = $conn->prepare($notifSql);
        $notifStmt->bind_param("isss", $admin_id, $notif_message, $notif_type, $notif_link);
        $notifStmt->execute();
        $notifStmt->close();
        mail($admin_email, "Restock Alert - $name", "The product $name was restocked and now has $stocks units.", $admin_headers);
    }
    // --- LOW STOCK ALERT ---
    $lowStockStmt = $conn->prepare("SELECT p.product_name, i.stocks
        FROM inventory i
        INNER JOIN products p ON i.product_id = p.product_id
        WHERE i.product_id = ? AND i.stocks <= ?");
    $lowStockStmt->bind_param("ii", $product_id, $min_stock_level);
    $lowStockStmt->execute();
    $lowStockResult = $lowStockStmt->get_result();

    if ($lowStockResult->num_rows > 0) {
        $row = $lowStockResult->fetch_assoc();

        $notif_message = "Low stock alert! {$row['product_name']} (Stock left: {$row['stocks']})";
        $notif_type = "low_stock";
        $notif_link = "add_product.php";
        $notifSql = "INSERT INTO notifadmin (register_id, notif_message, notif_type, notif_link) VALUES (?, ?, ?, ?)";
        $notifStmt = $conn->prepare($notifSql);
        $notifStmt->bind_param("isss", $admin_id, $notif_message, $notif_type, $notif_link);

        if ($notifStmt->execute()) {
            mail($admin_email, "Low Stock Alert - {$row['product_name']}", "The product {$row['product_name']} is low on stock ({$row['stocks']} left).", $admin_headers);
        }
        $notifStmt->close();
    }
    $lowStockStmt->close();



    // AUDIT LOG
    addAuditLog($conn, "Admin", "Update Product (ID: $product_id)");

    echo json_encode(["status" => "success", "action" => "updated", "product_id" => $product_id]);
    exit;
}

// --- DELETE PRODUCT ---
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    // Delete product
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    // Delete from inventory
    $stmt2 = $conn->prepare("DELETE FROM inventory WHERE product_id=?");
    $stmt2->bind_param("i", $product_id);
    $stmt2->execute();
    $stmt2->close();

    // AUDIT LOG
    addAuditLog($conn, "Admin", "Delete Product (ID: $product_id)");

    echo json_encode(["status" => "success", "action" => "deleted", "product_id" => $product_id]);
    exit;
}

// Function to get categories
function getCategories($conn)
{
    $res = $conn->query("SELECT * FROM category ORDER BY category_id ASC");
    $cats = [];
    while ($row = $res->fetch_assoc()) {
        $cats[] = $row;
    }
    return $cats;
}

// Function to get products
function getProducts($conn)
{
    $res = $conn->query("SELECT p.*, c.category_name as category_name, IFNULL(i.stocks, 0) as stocks FROM products p JOIN category c ON p.category_id = c.category_id LEFT JOIN inventory i ON p.product_id = i.product_id ORDER BY p.product_id ASC");
    $prods = [];
    while ($row = $res->fetch_assoc()) {
        $prods[] = $row;
    }
    return $prods;
}

$categories = getCategories($conn);
$products = getProducts($conn);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Products</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">


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
   /* 🌸 MAIN BASE */
body {
  font-family: 'Poppins', sans-serif;
  background: #fff;
  color: #6d2e3a;
  margin: 0;
  padding: 0;
}

/* 🌸 TABLE SCROLL */
.table-container { 
  max-height: 575px; 
  overflow-y: auto; 
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

/* 🌸 CARD DESIGN */
.card {
  margin: 20px auto;
  max-width: 100%;
  box-shadow: 0 4px 4px rgba(0, 0, 0, 0.2); 
  border-radius: 0; /* matalas edges */
  height: 650px; /* Fixed value — pwede mo palitan kung gusto mo mas mahaba */
}

/* 🌸 CATEGORY + FILTER WRAPPER */
.search-status-container {
  display: flex;
  align-items: center;
  gap: 15px;
}

/* 🌸 CATEGORY LABEL */
.status-container label {
    font-weight: bold;
  font-weight: 900;
  color: #6d2e3a;
  font-size: 16px;
  text-transform: uppercase; 
}

/* 🌸 FILTER SELECT BOX (clean flat design) */
.status-container {
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
}

.status-container select {
  border: 2px solid #6d2e3a;
  border-radius: 6px; /* matalas edges */
  padding: 2px 20px 6px 8px;
  color: #6d2e3a;
  font-size: 14px;
  font-weight: 500;
  background: #fff;
  outline: none;
  cursor: pointer;
  appearance: none; 
  background-image: none;
  box-shadow: none;
}

/* 🌸 Remove hover/focus shine */
.status-container select:hover,
.status-container select:focus {
  background-color: #fff;
  color: #6d2e3a;
  border-color: #6d2e3a;
  box-shadow: none;
}

/* 🌸 Custom arrow-down (v) */
.status-container::after {
  content: "\f078"; /* Font Awesome angle-down icon */
  font-family: "Font Awesome 7 Free";
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  color: #ec407a;
  pointer-events: none;
}

/* 🌸 OPTIONS STYLE */
.status-container select option {
  background: #fff;
  color: #6d2e3a;
  font-weight: 600;
}


/* 🌸 Filter container must be relative for arrow positioning */
.status-container {
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative; /* needed for ::after positioning */
}

/* 🌸 Filter select box */
#filterSelect {
    border: 2px solid #6d2e3a;
    border-radius: 6px;
    padding: 4px 30px 4px 8px; /* right padding for arrow */
    color: #6d2e3a;
    font-size: 14px;
    font-weight: 500;
    background-color: #fff;
    cursor: pointer;
    outline: none;
    appearance: none; /* remove default arrow */
    -webkit-appearance: none;
    -moz-appearance: none;
}

/* 🌸 Remove hover/focus shine */
#filterSelect:hover,
#filterSelect:focus {
    border-color: #6d2e3a;
    background-color: #fff;
    box-shadow: none;
}

/* 🌸 Custom arrow-down using Font Awesome */
.status-container::after {
    content: "\f078"; /* Font Awesome angle-down icon */
    font-family: "Font Awesome 7 Free"; 
    font-weight: 900; /* required for solid icons */
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    color: #6d2e3a; /* dark palette arrow */
    pointer-events: none; /* click passes through */
}


/* 🩷 Main card wrapper (sharp edges, same as dashboard) */
.customer-card {
  background: #fff;
  padding: 15px 20px;
  margin: 20px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  border-radius: 0; /* sharp edges */
  display: flex;
  flex-direction: column;
  gap: 12px;
  width: auto;
}

/* Header row: title + filter side by side */
.customer-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  width: 100%;
}

/* 🩷 Title — with underline */
.customer-title-fixed {
  display: flex;
  align-items: center;
  font-size: 2.3em;
  font-weight: 800;
  color: #6d2e3a;
  margin: 0;
  position: relative;
}

/* underline below title */
.customer-title-fixed::after {
  content: "";
  position: absolute;
  bottom: -6px;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: #6d2e3a;
  border-radius: 0; /* sharp underline */
}

/* 🌸 Filter dropdown (CATEGORY) */
.search-container {
  position: relative;
  display: flex;
  align-items: center;
  gap: 8px;
}



/* CATEGORY label */
.search-container label {
  color: #6d2e3a;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 1.1rem;
  font-weight: bold;
}

/* Select box — clean white with sharp edges */
.search-container select.form-select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  border: 1.5px solid #6d2e3a;
  border-radius: 0; /* sharp edges */
  background-color: #fff;
  color: #6d2e3a;
  font-weight: 600;
  padding: 6px 30px 6px 10px; /* space for arrow */
  cursor: pointer;
  outline: none;
  box-shadow: none;
  transition: none;
}

/* remove hover/focus shine */
.search-container select.form-select:hover,
.search-container select.form-select:focus {
  border-color: #6d2e3a;
  background-color: #fff;
  color: #6d2e3a;
  box-shadow: none;
}


/* 🌸 Arrow beside filter */
.search-container::after {
  content: "\f078";
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 12px;
  color: #6d2e3a;
  pointer-events: none;
}

.search-container select.form-select option {
  background-color: #fff;
  color: #6d2e3a;
  font-weight: 500;
}

/* 🌸 Table wrapper */
.table-wrapper {  
  margin-top: 20px;
}

/* 🩷 Table container — scrollable */
.table-container {
  overflow-x: auto;
}

/* Table styles — slight rounded edges only on table itself */
.table-container table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 0;
  overflow: hidden;
  border-left: none !important;
  border-right: none !important;
}

.table-container th,
.table-container td {
  padding: 12px 15px;
  text-align: left;
  border-left: none;  /* remove left border */
  border-right: none;
}

.table-container tr:nth-child(even) {
  background: #f9f9f9;
}

/* 🌸 IMAGE & TABLE COLORS */
img.product-img { border-radius: 8px; object-fit: cover; }
tr.selected { background-color: #d96d84 !important; }
.bg-lightpink { background-color: #6D2E3A !important; }

/* 🌸 TABLE HEADER */
.table thead th {
  background-color: #a95469 !important;
  color: white !important;
  border: none !important;
}

/* 🌸 TABLE BODY */
.table tbody td { color: #6D2E3A !important; }
.table tbody tr:hover {
  background-color: #d96d84 !important;
  cursor: pointer;
  border-left: none !important;
    border-right: none !important;
}
.table-striped tbody tr:nth-child(odd) {
  background-color: #df9494ff !important;
}

body.inventory-page .modal-backdrop.show {
    opacity: 0 !important;
    background-color: transparent !important;
}

.table td:nth-child(5), /* Stocks */
.table td:nth-child(6)  /* Image */
{
    text-align: center;
    vertical-align: middle;
}

/* 🌸 BUTTON COLORS */
.btn-pink { background-color: #6d2e3a !important; color: white !important; border: none; }
.btn-pink1 { background-color: #df9494 !important; color: white !important; border: none; }
.btn-pink3 { background-color: #d9684 !important; color: white !important; border: none; }
.btn-gray { background-color: #959595ff !important; color: white !important; border: none; }
.bg-pink { background-color: #6d2e3a !important; }

/* 🌸 FORM INPUTS */
.form-control, 
.form-select {
  border: 2px solid #6d2e3a !important;
  color: #6d2e3a !important;
  border-radius: 6px;
}
.form-control::placeholder { color: #d3d3d3ff !important; } 
.form-control:focus, 
.form-select:focus {
  border-color: #6d2e3a !important;
  box-shadow: 0 0 6px #d96d94!important;
  color: #6d2e3a !important;
}
#fileLabel{
    color: #cdcdcdff !important;
}

/* 🌸 FILE INPUT BUTTONS */
h7, label { color: #6d2e3a !important;
font-weight: bold; }
input[type="file"]::-webkit-file-upload-button,
input[type="file"]::file-selector-button {
  background-color: #dfdfdfff !important;
  color: white !important;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
}
input[type="file"]::-webkit-file-upload-button:hover,
input[type="file"]::file-selector-button:hover {
  background-color: #d95f85 !important;
}

/* 🌸 SWEETALERT */
.small-swal { width: 320px !important; padding: 1.2rem !important; font-size: 0.9rem !important; color: #ec7699 !important; }
.btn-confirm { background-color: ##6d2e3a !important; color: #fff !important; border: none !important; border-radius: 6px !important; padding: 8px 18px !important; font-weight: 600 !important; margin: 0 6px !important; transition: all 0.2s ease; }
.btn-confirm:hover { background-color: #d81b60 !important; }
.btn-cancel { background-color: #f48fb1 !important; color: #fff !important; border: none !important; border-radius: 6px !important; padding: 8px 18px !important; font-weight: 600 !important; margin: 0 6px !important; transition: all 0.2s ease; }
.btn-cancel:hover { background-color: #6d2e3a !important; }
.swal2-actions { gap: 10px !important; }
.swal2-title, .swal2-html-container { color: #6d2e3a !important; font-family: 'Poppins', sans-serif; }
.swal2-icon.swal2-question, .swal2-icon.swal2-warning, .swal2-icon.swal2-error, .swal2-icon.swal2-success {
  border-color: #6d2e3a !important;
  color: #a95469 !important;
}
.my-swal-size { width: 350px !important; padding: 1.2rem !important; }

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
/* 🌟 FORM CONTAINER */
.form-container {
    background-color: #f9f9f9; 
    padding: 25px;
    border-radius: 8px;
    max-width: 600px;
    margin: 0 auto;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* 🌸 FIELD LABELS */
.form-container label {
    display: block;
    font-weight: 600;
    color: #555; /* darker gray for clarity */
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: bold; 
}

/* ✨ INPUT FIELDS */
.form-container input[type="text"],
.form-container input[type="number"],
.form-container input[type="file"],
.form-container textarea,
.form-container select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 15px;
    font-size: 14px;
    color: #333; /* text inside field */
    background-color: #fff;
}

/* 📁 CHOOSE FILE BUTTON */
.form-container input[type="file"] {
    cursor: pointer;
    border: 1px solid #bbb;
    background-color: #e0e0e0; /* gray button */
    color: #333;
    padding: 8px 12px;
}

/* 🌟 HIGHLIGHT ADD/UPDATE BUTTON */
.form-container button {
    background-color: #a95469; /* pink highlight */
    color: #fff;
    font-weight: 700;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 15px;
    transition: all 0.3s ease;
}

.form-container button:hover {
    background-color: #a95469; /* darker pink on hover */
}

/* 🖌 OPTIONAL: Focus effect for inputs */
.form-container input:focus,
.form-container select:focus,
.form-container textarea:focus {
    border-color: #6d2e3a;
    box-shadow: 0 0 5px rgba(236, 118, 153, 0.4);
    outline: none;
}

/* Custom close button for Swal */
.swal-close-btn {
    color: #6D2E3A !important; /* dark palette */
    background: transparent !important; /* no background */
    border: none !important; /* remove border */
    box-shadow: none !important; /* remove shadow */
    padding: 0 !important; /* remove extra padding */
    width: auto !important;
    height: auto !important;
    font-size: 18px !important;
    line-height: 1 !important;
    opacity: 1 !important;
}
.swal-close-btn:hover {
    color: #A95469 !important; /* optional hover effect */
    background: transparent !important; /* ensure hover no bg */
}

/* Style the file input button */
#imageInput::file-selector-button {
    background-color: #c0c0c0ff; /* light gray button look */
    color: #6d2e3a; /* dark text (your palette) */
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 5px 10px;
    font-weight: 500; /* optional */
    font-family: 'Poppins', sans-serif; /* optional font */
    cursor: pointer;
}

/* Hover effect optional */
#imageInput::file-selector-button:hover {
    background-color: #d5d5d5;
}

/* BUTTON STYLE */
.btn-custom {
    background-color: #a95469;
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
}

/* SEARCH INPUT */
#productSearch {
    border: 1px solid #a95469;
    border-radius: 4px;
    padding: 6px 10px;
}

/* STATUS CONTAINER */
.status-container select.form-select {
    border: 1px solid #a95469;
    border-radius: 4px;
    padding: 5px 10px;
}

/* ==========================
   SEARCH FIELD, BUTTONS & DROPDOWN - COMPACT STYLE
========================== */

/* Search input */
input#productSearch {
    padding: 4px 8px;        /* smaller padding */
    font-size: 13px;         /* smaller text */
    border-radius: 5px;
    border: 1px solid #ccc;
    width: 150px;            /* smaller width */
}

/* Buttons */
button#searchProductBtn,
button#refreshProductBtn {
    padding: 4px 8px;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    background: #a95469;
    color: #fff;
}

/* Dropdown */
select#filterSelect {
    padding: 4px 8px;
    font-size: 13px;
    border-radius: 5px;
    border: 2px solid #6d2e3a;
    background-color: #fff;
    color: #6d2e39;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding-right: 20px; /* space for arrow */
}

/* Container spacing - make items closer like Customers panel */
.search-status-container {
    display: flex;
    align-items: center;
    gap: 1px; /* smaller gap between fields/buttons */
}

.suggestions-dropdown {
    border: 1px solid #ccc;
    max-height: 200px;
    overflow-y: auto;
    background: #fff;
    width: 200px; /* adjust para match search input */
    display: none;
    position: absolute;
    z-index: 10;
}

.suggestions-dropdown div {
    padding: 5px 10px;
    cursor: pointer;
}

.suggestions-dropdown div:hover {
    background-color: #f0f0f0;
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
    .card {
        height: 1150px !important;
        margin-bottom: 4px !important;
    }
    .search-status-container {
        justify-content: center !important;
        width: 100%;
    }
    .status-container {
        flex-direction: row;
        align-items: center;
        justify-content: center !important;
        width: auto;
    }
}

@media (max-width: 576px) {
  #categoryModal .modal-dialog {
    margin: 1.5rem;          /* paluwagin gilid */
    max-width: 100%;
  }

  #categoryModal .modal-content {
    border-radius: 8px;
  }

  /* Optional: bawasan height at gawin scrollable kung sobrang haba */
  #categoryModal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
  }
}


  </style>
</head>

<body class="bg-light">

    <div class="container-fluid" style="padding:0;">
        <div class="header-container">
            <h1 class="heading" style="display: flex; align-items: center; font-size: 2em; font-weight: bold;">
                <i class="bi bi-box-seam-fill" style="margin-left: 12px; margin-right: 12px;"></i> Inventory
            </h1>

            <div class="search-status-container" style="display:flex; align-items:center; gap:10px;">

                <!-- CATEGORY DROPDOWN -->
                <div class="status-container">
                    <label class="category-label" for="filterSelect">Category:</label>
                    <select class="category-select" id="filterSelect" class="form-select">
                        <option value="0">All Products</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="card" style="width:100%;">
            <div class="card-body row d-flex align-items-center">
                <div class="col-md-7">
                    <div class="table-container">
                        <table class="table table-striped table-bordered align-middle text-center" id="productTable">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 30px;">ID</th>
                                    <th style="width: 130px;">Category</th>
                                    <th style="width: 250px;">Name</th>
                                    <th style="width: 150px;">Price</th>
                                    <th style="width: 40px;">Stocks</th>
                                    <th style="width: 90px;">Image</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                <?php if (count($products) > 0) : ?>
                                    <?php foreach ($products as $prod) : ?>
                                        <?php
                                        $imgPath = $prod['image_path'];
                                        if (strpos($imgPath, 'pictures/') !== 0) $imgPath = 'pictures/' . $imgPath;
                                        if (!file_exists($imgPath)) $imgPath = 'pictures/noimage.png';
                                        ?>
                                        <tr data-id="<?php echo $prod['product_id']; ?>" data-category="<?php echo $prod['category_id']; ?>" data-name="<?php echo htmlspecialchars($prod['product_name']); ?>" data-price="<?php echo $prod['price']; ?>" data-stocks="<?php echo $prod['stocks']; ?>" data-image="<?php echo $prod['image_path']; ?>">
                                            <td><?php echo $prod['product_id']; ?></td>
                                            <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                                            <td><?php echo htmlspecialchars($prod['product_name']); ?></td>
                                            <td><?php echo number_format($prod['price'], 2); ?> PHP</td>
                                            <td><?php echo $prod['stocks']; ?></td>
                                            <td><img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Product" width="50" height="50" class="product-img"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr id="noProductsRow">
                                        <td colspan="6" class="text-center">No products found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-5">
                    <h7 class="mb-3" style="display:flex; margin-top: 10px; align-items:center; gap:6px; justify-content:center; text-align:center; color:#6D2E3A; font-weight:bold; font-size:20px;">
                        Products and Category Manager
                        <i class="bi bi-info-circle-fill" 
                        onclick="showProductInfo()" 
                        style="cursor:pointer; color:#6D2E3A; font-size:20px;"
                        title="How to use"></i>
                    </h7>

                    <form method="POST" enctype="multipart/form-data" id="productForm">
                        <div class="mb-2">
                            <label>Category</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="0">Select Categories</option>
                                <?php foreach ($categories as $cat) : ?>
                                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Product Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" required>
                        </div>

                        <div class="mb-2">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="Enter price" required>
                        </div>

                        <div class="mb-2">
                            <label>Stocks</label>
                            <input type="number" name="stocks" id="stocks" class="form-control" placeholder="Enter stock quantity" required min="0" value="0">
                        </div>

                        <div class="mb-2">
                            <label>Image</label>
                            <div class="custom-file-group">
                                <input type="file" name="image" class="form-control" id="imageInput">
                                <span class="file-label" id="fileLabel">No file chosen</span>
                            </div>
                        </div>

                        <input type="hidden" name="product_id" id="product_id">
                        <input type="hidden" name="existing_image" id="existing_image">

                        <div class="d-flex gap-1 mt-1">
                            <button type="button" class="btn btn-pink" id="addBtn">Add Product</button>
                            <button type="button" class="btn btn-pink1" id="categoryBtn">Category</button>
                            <button type="button" class="btn btn-pink d-none" id="updateBtn">Update</button>
                            <button type="button" class="btn btn-danger d-none" id="deleteBtn">Delete</button>
                            <button type="button" class="btn btn-gray d-none" id="cancelBtn">Cancel</button>
                        </div>

                        <!-- CATEGORY MODAL -->
                        <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content shadow-lg border-0">
                                    <div class="modal-header bg-lightpink text-white">
                                        <h5 class="modal-title" id="categoryModalLabel">Manage Categories</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="categoryForm">
                                            <div class="mb-3">
                                                <label for="categorySelect" class="form-label">Select Category</label>
                                                <select class="form-select" id="categorySelect">
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($categories as $cat) : ?>
                                                        <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="categoryName" class="form-label">Category Name</label>
                                                <input type="text" id="categoryName" class="form-control" placeholder="Enter category name" required>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-pink" id="addCategoryBtn">Add</button>
                                                <button type="button" class="btn btn-pink1" id="updateCategoryBtn" disabled>Update</button>
                                                <button type="button" class="btn btn-danger" id="deleteCategoryBtn" disabled>Delete</button>
                                                <button type="button" class="btn btn-gray" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const form = document.getElementById("productForm");
    const addBtn = document.getElementById("addBtn");
    const updateBtn = document.getElementById("updateBtn");
    const deleteBtn = document.getElementById("deleteBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const fileLabel = document.getElementById("fileLabel");
    const fileInput = document.getElementById("imageInput");
    const filterSelect = document.getElementById("filterSelect");
    const productTableBody = document.getElementById("productTableBody");

    // CATEGORY modal elements
    const categoryBtn = document.getElementById("categoryBtn");
    const categoryModal = new bootstrap.Modal(document.getElementById("categoryModal"));
    const categorySelect = document.getElementById("categorySelect");
    const categoryName = document.getElementById("categoryName");
    const addCategoryBtn = document.getElementById("addCategoryBtn");
    const updateCategoryBtn = document.getElementById("updateCategoryBtn");
    const deleteCategoryBtn = document.getElementById("deleteCategoryBtn");

    function resetProductForm() {
        form.reset();
        document.getElementById("product_id").value = "";
        document.getElementById("existing_image").value = "";
        fileLabel.textContent = "No file chosen";

        addBtn.classList.remove("d-none");
        categoryBtn.classList.remove("d-none");
        updateBtn.classList.add("d-none");
        deleteBtn.classList.add("d-none");
        cancelBtn.classList.add("d-none");

        document.querySelectorAll("#productTable tbody tr").forEach(r => r.classList.remove("selected"));
    }

    function refreshCategories(newCategories) {
        [categorySelect, document.getElementById("category_id"), filterSelect].forEach(select => {
            const selectedVal = select.value;
            select.innerHTML = '';
            if (select === filterSelect) {
                select.innerHTML += '<option value="0">All Products</option>';
            } else {
                select.innerHTML += '<option value="">Select Category</option>';
            }

            newCategories.forEach(c => {
                const opt = document.createElement("option");
                opt.value = c.category_id;
                opt.text = c.category_name;
                select.appendChild(opt);
            });

            if (newCategories.some(c => c.category_id === selectedVal)) {
                select.value = selectedVal;
            } else {
                select.value = select === filterSelect ? "0" : "";
            }
        });
    }

    function refreshProducts(products) {
        productTableBody.innerHTML = '';

        if (products.length === 0) {
            productTableBody.innerHTML = '<tr id="noProductsRow"><td colspan="6" class="text-center">No products found</td></tr>';
            return;
        }

        products.forEach(prod => {
            let imgPath = prod.image_path || '';
            if (!imgPath.startsWith('pictures/')) imgPath = 'pictures/' + imgPath;

            const tr = document.createElement('tr');
            tr.dataset.id = prod.product_id;
            tr.dataset.category = prod.category_id;
            tr.dataset.name = prod.product_name;
            tr.dataset.price = prod.price;
            tr.dataset.stocks = prod.stocks;
            tr.dataset.image = prod.image_path;

            tr.innerHTML = `
                <td>${prod.product_id}</td>
                <td>${prod.category_name}</td>
                <td>${prod.product_name}</td>
                <td>${Number(prod.price).toFixed(2)} PHP</td>
                <td>${prod.stocks}</td>
                <td><img src="${imgPath}" width="50" height="50" class="product-img"></td>
            `;

            tr.addEventListener("click", () => selectProductRow(tr));
            productTableBody.appendChild(tr);
        });
    }

    function selectProductRow(row) {
        document.querySelectorAll("#productTable tbody tr").forEach(r => r.classList.remove("selected"));
        row.classList.add("selected");

        document.getElementById("product_id").value = row.dataset.id;
        document.getElementById("category_id").value = row.dataset.category;
        document.getElementById("name").value = row.dataset.name;
        document.getElementById("price").value = row.dataset.price;
        document.getElementById("stocks").value = row.dataset.stocks;
        document.getElementById("existing_image").value = row.dataset.image;
        fileLabel.textContent = row.dataset.image || "No file chosen";

        addBtn.classList.add("d-none");
        updateBtn.classList.remove("d-none");
        deleteBtn.classList.remove("d-none");
        cancelBtn.classList.remove("d-none");
        categoryBtn.classList.add("d-none");
    }

    fileInput.addEventListener("change", function() {
        fileLabel.textContent = this.files.length ? this.files[0].name : "No file chosen";
    });

    filterSelect.addEventListener("change", function() {
        const selectedCat = this.value;
        document.querySelectorAll("#productTable tbody tr").forEach(r => {
            if (r.id === 'noProductsRow') return;
            r.style.display = (selectedCat === '0' || r.dataset.category === selectedCat) ? '' : 'none';
        });
        resetProductForm();
    });

    function submitForm(action) {
        let formData = new FormData(form);
        formData.append(action, "1");

        Swal.fire({
            title: action === "delete_product" ? "Deleting..." :
                   action === "update_product" ? "Updating..." : "Adding...",
            html: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            customClass: { popup: 'my-swal-size' }   // ✅ ADDED
        });

        setTimeout(() => {
            fetch("add_product.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.status === "success") {
                        Swal.fire({
                            title: action === "delete_product" ? "Product deleted successfully!" :
                                   action === "update_product" ? "Product updated successfully!" :
                                   "Product added successfully!",
                            icon: "success",
                            confirmButtonText: "OK",
                            customClass: { popup: 'my-swal-size' }   // ✅ ADDED
                        });
                        fetchUpdatedData();
                        resetProductForm();
                    }
                })
                .catch(() => {
                    Swal.close();
                    Swal.fire("Error", "Please fill in all Fields.", "error", {
                        customClass: { popup: 'my-swal-size' }     // ✅ ADDED
                    });
                });
        }, 1500);
    }

    function fetchUpdatedData() {
        fetch('add_product.php')
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");

                const newProducts = Array.from(doc.querySelectorAll("#productTableBody tr")).map(tr => {
                    return {
                        product_id: tr.dataset.id,
                        category_id: tr.dataset.category,
                        product_name: tr.dataset.name,
                        price: tr.dataset.price,
                        stocks: tr.dataset.stocks,
                        image_path: tr.dataset.image,
                        category_name: tr.children[1].innerText
                    };
                });

                const newCategories = Array.from(doc.querySelectorAll("#categorySelect option")).map(opt => {
                    return {
                        category_id: opt.value,
                        category_name: opt.text
                    };
                }).filter(c => c.category_id !== "");

                refreshProducts(newProducts);
                refreshCategories(newCategories);
            });
    }

    addBtn.addEventListener("click", e => {
        e.preventDefault();

        const stocks = Number(form.stocks.value);
        const price = Number(form.price.value);

        if (!form.category_id.value || form.category_id.value === "0" ||
            !form.name.value.trim() ||
            !form.price.value.trim() ||
            !form.stocks.value.trim()) {

            Swal.fire({
                title: "Error",
                text: "Please fill in all fields.",
                icon: "warning",
                customClass: { popup: 'my-swal-size' }   // ✅ ADDED
            });

            return;
        }

        
        // OPTIONAL: prevent negative price
        if (price <= 0) {
            Swal.fire({
                title: "Invalid Price",
                text: "Price must be greater than zero.",
                icon: "warning",
                customClass: { popup: 'my-swal-size' }
            });
            return;
        }
         // ❗ CHECK IF STOCKS ARE ZERO OR NEGATIVE
        if (stocks <= 0) {
            Swal.fire({
                title: "Invalid Stock",
                text: "Stock must be greater than zero.",
                icon: "warning",
                customClass: { popup: 'my-swal-size' }
            });
            return;
        }

        Swal.fire({
            title: 'Add Product?',
            text: "Do you want to add this product?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ec7699',
            cancelButtonColor: '#5d5c5cff',
            confirmButtonText: 'Yes, Add it!',
            customClass: { popup: 'my-swal-size' }       // ✅ ADDED
        }).then(result => {
            if (result.isConfirmed) submitForm("add_product");
        });
    });

    updateBtn.addEventListener("click", e => {
        e.preventDefault();

        const stocks = Number(form.stocks.value);
         const price = Number(form.price.value);

         // ❗ DO NOT ALLOW EMPTY FIELDS
        if (!form.category_id.value || form.category_id.value === "0" ||
            !form.name.value.trim() ||
            !form.price.value.trim() ||
            !form.stocks.value.trim()) {

            Swal.fire({
                title: "Error",
                text: "Please fill in all fields.",
                icon: "warning",
                customClass: { popup: 'my-swal-size' }
            });

            return;
        }

        // OPTIONAL: block zero or negative price
        if (price <= 0) {
            Swal.fire({
                title: "Invalid Price",
                text: "Price must be greater than zero.",
                icon: "warning",
                customClass: { popup: 'my-swal-size' }
            });
            return;
        }

         // ❗ BLOCK ZERO OR NEGATIVE STOCKS
        if (stocks <= 0) {
            Swal.fire({
                title: "Invalid Stock",
                text: "Stock must be greater than zero.",
                icon: "warning",
                customClass: { popup: 'my-swal-size' }
            });
            return;
        }

        Swal.fire({
            title: 'Update Product?',
            text: "Do you want to save the changes?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ec7699',
            cancelButtonColor: '#5d5c5cff',
            confirmButtonText: 'Yes, Update',
            customClass: { popup: 'my-swal-size' }       // ✅ ADDED
        }).then(result => {
            if (result.isConfirmed) submitForm("update_product");
        });
    });

    deleteBtn.addEventListener("click", e => {
        e.preventDefault();
        Swal.fire({
            title: 'Delete Product?',
            text: "This action cannot be undone!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ec7699',
            cancelButtonColor: '#5d5c5cff',
            customClass: { popup: 'my-swal-size' }       // ✅ ADDED
        }).then(result => {
            if (result.isConfirmed) submitForm("delete_product");
        });
    });

    cancelBtn.addEventListener("click", resetProductForm);

    categoryBtn.addEventListener("click", () => {
        categoryModal.show();
        categorySelect.value = "";
        categoryName.value = "";
        updateCategoryBtn.disabled = true;
        deleteCategoryBtn.disabled = true;
    });

    categorySelect.addEventListener("change", function() {
        const selectedOption = this.options[this.selectedIndex];
        categoryName.value = selectedOption.value ? selectedOption.text : "";
        if (selectedOption.value) {
            addCategoryBtn.disabled = true;
            updateCategoryBtn.disabled = false;
            deleteCategoryBtn.disabled = false;
        } else {
            addCategoryBtn.disabled = false;
            updateCategoryBtn.disabled = true;
            deleteCategoryBtn.disabled = true;
            categoryName.value = "";
        }
    });

    function categoryAction(action, id = "", name = "") {
        const formData = new FormData();
        formData.append("action", action);
        if (id) formData.append("category_id", id);
        if (name) formData.append("category_name", name);

        fetch("add_product.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        title: data.message,
                        icon: "success",
                        confirmButtonText: "OK",
                        customClass: { popup: 'my-swal-size' }   // ✅ ADDED
                    }).then(() => {
                        fetchUpdatedData();
                        categorySelect.value = "";
                        categoryName.value = "";
                        addCategoryBtn.disabled = false;
                        updateCategoryBtn.disabled = true;
                        deleteCategoryBtn.disabled = true;
                    });
                }
            })
            .catch(() => Swal.fire({
                title: "Error",
                text: "Something went wrong.",
                icon: "error",
                customClass: { popup: 'my-swal-size' }       // ✅ ADDED
            }));
    }

    addCategoryBtn.addEventListener("click", () => {
        const name = categoryName.value.trim();
        if (!name) return Swal.fire({
            title: "Error",
            text: "Please enter a category name.",
            icon: "warning",
            customClass: { popup: 'my-swal-size' }       // ✅ ADDED
        });
        categoryAction("add_category", "", name);
    });

    updateCategoryBtn.addEventListener("click", () => {
        const id = categorySelect.value;
        const name = categoryName.value.trim();
        if (!id || !name) return Swal.fire({
            title: "Error",
            text: "Select a category first.",
            icon: "warning",
            customClass: { popup: 'my-swal-size' }       // ✅ ADDED
        });

        Swal.fire({
            title: "Update Category?",
            text: "Do you want to update this category?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#ec7699",
            cancelButtonColor: "#5d5c5cff",
            customClass: { popup: 'my-swal-size' }       // ✅ ADDED
        }).then(result => {
            if (result.isConfirmed) categoryAction("update_category", id, name);
        });
    });

    deleteCategoryBtn.addEventListener("click", () => {
        const id = categorySelect.value;
        if (!id) return Swal.fire({
            title: "Error",
            text: "Select a category first.",
            icon: "warning",
            customClass: { popup: 'my-swal-size' }       // ✅ ADDED
        });

        Swal.fire({
            title: "Delete Category?",
            text: "This will delete all products under this category!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ec7699",
            cancelButtonColor: "#5d5c5cff",
            customClass: { popup: 'my-swal-size' }       // ✅ ADDED
        }).then(result => {
            if (result.isConfirmed) categoryAction("delete_category", id);
        });
    });

    document.querySelectorAll("#productTable tbody tr").forEach(row => {
        if (row.id !== 'noProductsRow') row.addEventListener("click", () => selectProductRow(row));
    });

    function showProductInfo() {
    Swal.fire({
        html: `
            <div style="font-size:13px; line-height:1.3; text-align:left; color:#6D2E3A; margin:0; padding:0;">
                <div style="font-weight:bold; font-size:20px; margin-bottom:6px;">Quick Guidelines</div>
                <ol style="padding-left:16px; margin:0;">
                    <li><b style="color:#A95469;">Update Product</b>
                        <ul style="padding-left:14px; margin:2px 0;">
                            <li>Click a row in the product table to auto-fill fields.</li>
                            <li>Buttons switch to Update / Delete / Cancel mode.</li>
                        </ul>
                    </li>
                    <li><b style="color:#A95469;">Edit Categories</b>
                        <ul style="padding-left:14px; margin:2px 0;">
                            <li>Click Category button to Add / Update / Delete categories.</li>
                        </ul>
                    </li>
                    <li><b style="color:#A95469;">Add Product</b>
                        <ul style="padding-left:14px; margin:2px 0 0 0;">
                            <li>Fill Category, Name, Price, Stocks, Image.</li>
                            <li>Click Add Product to save the item.</li>
                        </ul>
                    </li>
                </ol>
            </div>
        `,
        showCloseButton: true, // X top-right
        showConfirmButton: false, // no bottom button
        width: 280, // compact
        background: "#fff",
        padding: "0.5em 0.8em", // tight spacing
        showClass: { popup: 'animate__animated animate__fadeInDown' },
        hideClass: { popup: 'animate__animated animate__fadeOutUp' },
        customClass: {
            closeButton: 'swal-close-btn' // custom class for X
        }
    });
    }

</script>


</body>
</html>
