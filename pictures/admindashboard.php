<?php
session_start();

// ✅ Check if admin is logged in
if (!isset($_SESSION['admin_email'])) {
    echo "<script>
        window.location.href = 'log_admin.php';
    </script>";
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$titles = [
    "dashboard" => "Welcome to Your Dashboard",
    "customers" => "Customer Records",
    "addproducts" => "Add New Products",
    "sales" => "Sales Transactions",
    "reservations" => "Reservation Management",
    "walkin" => "Walk-in Orders",
    "audit" => "System Logs",
    "logout" => "You have logged out"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


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

/* Main display area */
#main-display {
  background: none !important; /* Tanggalin ang gradient + puti */
  box-shadow: none !important; /* Tanggalin ang shadow */
  border-radius: 0 !important; /* Flat, no corners */
  transition: all 0.3s ease;
  padding: 0 !important;       /* You may adjust padding after */
}

.admin-nav, nav, .navigation, .navbar, .main-nav {
    font-size: 18px !important;
    font-family: 'Poppins', sans-serif !important;
}

 html, body {
    height: 100%;
    margin: 0;
    font-family: 'Poppins', sans-serif !important;
    color: #6d2e3a;
    background-color: #fff;
}

/* SIDEBAR */
#sidebar { 
  position: fixed;          /* ✅ Fixed position para di gumalaw pag scroll */
  top: 0;
  left: 0;
  width: 250px;
  height: 100vh;            /* ✅ Full height ng screen */
  background: #F8D7DC;
  color: #6D2E3A;
  display: flex; 
  flex-direction: column; 
  justify-content: space-between; /* ✅ Para laging nasa baba si logout */
  transition: transform 0.3s ease-in-out; 
  overflow: hidden;          /* ✅ Alisin scrollbar sa loob ng sidebar */
  z-index: 999;
}

  
/* ============================
   UNIFIED SIDEBAR NAV-LINK THEME
   ============================ */

/* DEFAULT STATE (all nav links including logout) */
#sidebar .nav-link {
  color: #6D2E3A !important;         /* deep wine */
  font-weight: 600;
  padding: 12px 20px;
  border-radius: 6px;
  margin: 4px 10px;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.2s ease-in-out;
}

#sidebar .nav-link,
#offcanvasSidebar .nav-link {
    color: #6D2E3A !important;
    font-weight: 450;    /* Regular lang, hindi bold by default */
}

#sidebar .nav-link:hover,
#offcanvasSidebar .nav-link:hover {
    background-color: rgba(214, 51, 108, 0.06);
    border-radius: 5px;
    /* font-weight di na kailangan dito, depende sa style, kadalasan regular pa rin sa hover */
}

#sidebar .nav-link.active,
#offcanvasSidebar .nav-link.active {
    background-color: #a95469 !important;
    color: #fff !important;
    border-radius: 5px;
    font-weight: 800;    /* Bold or extra bold lang pag ACTIVE */
}



#sidebar .nav-link.active i {
  color: #fff !important;
}

#sidebar .nav-link:not(.active):hover i {
  color: #6D2E3A !important;
}


  /* ADMIN HEADER (icon + text) */
#sidebar .admin-header {
  text-align: center; 
  padding: 20px 10px; 
  border-bottom: 1px solid rgba(214, 51, 108, 0.3);
}
#sidebar .admin-header i { 
  font-size: 40px; 
  display: block; 
  margin-bottom: 5px; 
  color: #6D2E3A;
}
#sidebar .admin-header h5 {
  font-weight: 700; 
  letter-spacing: 1px; 
  color: #6D2E3A;
}

 /* Content sa right side */
#content {
  margin-left: 250px;        /* ✅ Para di matabunan ng sidebar */
  padding: 20px;
}
  #main-header { background: #F8D7DC; color: #6D2E3A; padding: clamp(6px, 2vw, 13px); border-radius: 8px; margin-bottom: 20px; text-align: center; }
  #main-header h4 { font-size: clamp(14px, 2vw, 22px); margin: 0; }
  #main-display {  
    background: #fff; 
    border-radius: 10px;
    min-height: 300px;
    padding: 20px 20px 60px; /* ➕ dagdag bottom space */
   }
  
  
  .table { 
  border: 1px solid #f3a9be !important; 
  border-collapse: collapse !important; 
  font-size: 14px; 
  text-align: left; 
}
.table thead { 
  background-color: #F8D7DC;
  color: #6D2E3A;
}
.table thead th { 
  padding: 12px 10px !important; 
  font-weight: bold !important; 
  border: 1px solid #D96D84;
}
.table tbody td { 
  background-color: #fff !important; 
  padding: 10px !important; 
  border: 1px solid #f3a9be !important; 
}
.table tbody tr:hover { 
  background-color: #F8D7DC;
}

.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

@media (max-width: 992px) {
  #sidebar {
    width: 250px;
  }

  #content {
    margin-left: 200px;
  }
}

  .modal-dialog { margin: 1.5rem auto; }
  .modal.fade .modal-dialog { transform: translate(0, 0); }


  /* MOBILE SIDEBAR */
#overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  display: none;
  z-index: 998;
}


@media (min-width: 769px) {
    #topbar {
        display: none !important;
    }
}

@media (max-width: 768px) {
  #sidebar {
    position: fixed;
    top: 0; left: 0;
    height: 100vh;
    transform: translateX(-100%);
    z-index: 1060;
  }
  #sidebar.active {
    transform: translateX(0);
  }
  #topbar {
    display: flex;
  }
  #content {
    margin-left: 0 !important;
    padding: 15px;
  }
  .topbar-title {
    font-size: 15px;
  }
}

@media (max-width: 600px) {
    #topbar button {
        left: 8px;
    }
    .topbar-title {
        font-size: 1.4em !important;
    }
}

/* Prevent page scroll */
html, body {
  min-height: 100%;
  overflow-x: hidden;
  overflow-y: auto; /* ➕ Ito ang kulang! */
}

/* Ensure content fits the visible area */
#content {
  min-height: 100vh;
  overflow-y: auto;
}


  #main-header {
  display: none !important;
}

/* --- MOBILE TOPBAR STYLES: white bg, #6D2E3A font --- */
#topbar {
    display: flex;
    align-items: center;
    justify-content: center;
    position: sticky;      /* stays on top during scroll */
    top: 0;
    z-index: 1050;         /* Dapat MAS MATAAS dito para matakpan din ng sidebar overlay! */
    background: #fff5f9 !important;
    color: var(--pink-dark) !important;
    min-height: 60px;
    box-shadow: 0 2px 18px #eee1e5a3;
    border-bottom: 1.8px solid #eee1e5;
    position: relative;    /* <--- IMPORTANTE para gumana ang absolute ng button! */
}

#topbar button {
    position: absolute !important;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--pink-dark) !important;
    font-size: 24px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.topbar-title {
    flex: 1;
    text-align: center;
    color: var(--pink-dark) !important;
    font-weight: 700 !important;
    font-size: 2em !important;
    margin: 0 auto;
    position: relative;
    pointer-events: none;
}

/* SWEETALERT2: Lahat ng confirm at cancel buttons auto-small */
.swal2-confirm, .swal2-cancel, .swal2-styled {
  min-width: 54px !important;
  font-size: 0.91em !important;
  padding: 5px 18px !important;
  border-radius: 6px !important;
  font-weight: 600 !important;
  line-height: 1.19 !important;
}
.swal2-actions {
  gap: 7px !important;
}


</style>
</head>

<!-- ✅ Added for mobile toggle -->
<div id="overlay"></div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="margin: 1.5rem auto; max-width: 400px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">Are you sure you want to logout?</div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <a href="admin_logout.php" class="btn btn-danger">Yes</a>
      </div>
    </div>
  </div>
</div>

<body>
<!-- ✅ Added topbar toggle button (for mobile view) -->
<div id="topbar" class="d-md-none">
  <button id="menu-toggle"><i class="bi bi-list"></i></button>
  <span class="topbar-title mb-0">Admin Dashboard</span>
</div>

<div class="d-flex" id="wrapper">

  <!-- Sidebar -->
  <nav id="sidebar">
    <div class="admin-header">
      <i class="bi bi-person-circle"></i>
      <h5>ADMINISTRATOR</h5>
    </div>
    <div class="list-group list-group-flush flex-grow-1">    
    <a href="admindashboard.php?page=dashboard" class="nav-link <?php if($page=='dashboard') echo 'active'; ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>
      <a href="admindashboard.php?page=audit" class="nav-link <?php if($page=='audit') echo 'active'; ?>">
        <i class="bi bi-clipboard-data-fill"></i> Audit Logs
      </a>
      <a href="admindashboard.php?page=customers" class="nav-link <?php if($page=='customers') echo 'active'; ?>">
        <i class="bi bi-people-fill"></i> Customers
      </a>
      <a href="admindashboard.php?page=addproducts" class="nav-link <?php if($page=='addproducts') echo 'active'; ?>">
        <i class="bi bi-plus-square-fill"></i> Inventory
      </a>
      <a href="admindashboard.php?page=sales" class="nav-link <?php if($page=='sales') echo 'active'; ?>">
        <i class="bi bi-cash-stack"></i> Sales Record
      </a>
      <a href="admindashboard.php?page=reservations" class="nav-link <?php if($page=='reservations') echo 'active'; ?>">
        <i class="bi bi-calendar-check-fill"></i> Reservations
      </a>
      <a href="admindashboard.php?page=walkin" class="nav-link <?php if($page=='walkin') echo 'active'; ?>">
        <i class="bi bi-bag-plus-fill"></i>Walk-in 
      </a>
    </div>
    <div class="mt-auto mb-3 px-2">
      <a href="#" class="nav-link"
        data-bs-toggle="modal" data-bs-target="#logoutModal" style="font-weight: 1000;">
        <i class="fa-solid fa-right-from-bracket me-2"></i> Log Out
      </a>
    </div>
  </nav>

  <!-- Page Content -->
  <div id="content" class="flex-grow-1">
    <?php if(isset($_GET['success'])): ?>
      <div class="alert alert-success d-flex align-items-center fade show" role="alert" id="successAlert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <div><?php echo htmlspecialchars($_GET['success']); ?></div>
      </div>
    <?php endif; ?>

    <div id="main-header">
      <h4><?php echo isset($titles[$page]) ? $titles[$page] : ucfirst($page); ?></h4>
    </div>

    <div id="main-display" class="p-3">
      <?php
        switch($page) {
          case 'dashboard':
            include("dashboard.php");
            break;
          case 'audit':
            echo '<div class="table-responsive">';
            include("audit_log.php");
            break;
          case 'customers':
            echo '<div class="table-responsive">';
            include("customers.php");
            echo '</div>';
            break;
          case 'addproducts':
            include("add_product.php");
            break;
          case 'sales':
            include("sales.php");
            break;
          case 'reservations':
            echo '<div class="table-responsive">';
            include("reservations.php");
            break;
          case 'walkin':
              include("order_dashboard.php");
            break;
          case 'logout':
            echo "<p>You have successfully logged out.</p>";
            break;
          default:
            echo "<p>No content yet.</p>";
        }
      ?>
    </div>
  </div>
</div>

<script>
  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.getElementById("menu-toggle");
  const navLinks = document.querySelectorAll("#sidebar .nav-link");
  const overlay = document.getElementById("overlay");

  // ✅ Added for mobile sidebar toggle
  if(toggleBtn){
    toggleBtn.addEventListener("click", () => {
      sidebar.classList.toggle("active");
      overlay.style.display = sidebar.classList.contains("active") ? "block" : "none";
    });
  }
  navLinks.forEach(link => {
    link.addEventListener("click", () => {
      if(window.innerWidth <= 768){
        sidebar.classList.remove("active");
        overlay.style.display = "none";
      }
    });
  });
  overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.style.display = "none";
  });

  document.addEventListener("DOMContentLoaded", function () {
      let alertBox = document.getElementById("successAlert");
      if (alertBox) {
          setTimeout(() => {
            let bsAlert = new bootstrap.Alert(alertBox);
            bsAlert.close();
        }, 5000);
      }
  });

    // Apply Poppins globally
  document.addEventListener("DOMContentLoaded", () => {
    document.body.style.fontFamily = "'Poppins', sans-serif";

    // Override iframe content
    const iframe = document.getElementById('dashboardFrame');
    iframe.onload = () => {
      const doc = iframe.contentDocument || iframe.contentWindow.document;
      if (!doc) return;

      // Inject Poppins style into iframe head
      const style = document.createElement('style');
      style.innerHTML = "* { font-family: 'Poppins', sans-serif !important; }";
      doc.head.appendChild(style);
    };
  });
  
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
