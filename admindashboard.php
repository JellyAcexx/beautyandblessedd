<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


ob_start(); 


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
    "notification" => "Notification",
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

:root{
  --pink-bg:#fce6ec;
  --pink-bg-2:#f8d7dc;
  --pink-accent:#d96d84;
  --pink-dark:#6d2e3a;
}


html,body{
  height:100%;
  margin:0;
  font-family:'Poppins',sans-serif !important;
  color:var(--pink-dark);
  background:linear-gradient(180deg,#fff5f9,#fce6ec);
  overflow-x:hidden;
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

#sidebar{
  position:fixed;
  top:0;
  left:0;
  width:250px;
  height:100vh;
  background-color: #f8d7dc;  /* plain pink lang */
  color:var(--pink-dark);
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  border-right:1px solid #e6b8c2;
  box-shadow:0 10px 25px rgba(0,0,0,0.08);
  transition:transform .3s ease-in-out;
  z-index:999;
  overflow:hidden;
}




/* SECTION LABELS (MAIN, MANAGEMENT…) */
.sidebar-title{
  font-size:11px;
  font-weight:600;
  letter-spacing:0.18em;
  color:#b46a7a;
  text-transform:uppercase;
  margin:16px 0 4px;
  padding-left:20px;
}

/* NAV LINKS – unified look */
#sidebar .nav-link{
  color:var(--pink-dark) !important;
  font-weight:500;
  font-size:14px;
  padding:8px 18px;
  margin:2px 5px;
  border-radius:10px;
  display:flex;
  align-items:center;
  gap:10px;
  text-decoration:none;
  transition:
    background-color .18s ease,
    transform .1s ease,
    box-shadow .18s ease,
    color .18s ease;
}

#sidebar .nav-link i{
  font-size:17px;
}

#sidebar .nav-link:hover{
  background:rgba(232,169,178,0.22);
  transform:translateX(2px);
  box-shadow:none;              /* remove shadow */
}



#sidebar .nav-link,
#sidebar .nav-link:hover,
#sidebar .nav-link.active {
    border-radius: 0 !important;
}



#sidebar .nav-link,
#offcanvasSidebar .nav-link {
    font-weight: 450;    /* Regular lang, hindi bold by default */
    font-size: 15px;
}

#sidebar .nav-link:hover,
#offcanvasSidebar .nav-link:hover {
    background-color: transparent !important;
    border-radius: 5px;
    /* font-weight di na kailangan dito, depende sa style, kadalasan regular pa rin sa hover */
}

#sidebar .nav-link.active,
#offcanvasSidebar .nav-link.active {
    color: #6d2a3a !important;
    border-radius: 5px;
    font-weight: 800;    /* Bold or extra bold lang pag ACTIVE */
}



#sidebar .nav-link.active i {
  color: #6d2a3a !important;
}

#sidebar .nav-link:not(.active):hover i {
  color: #6D2E3A !important;
}



/* ADMIN HEADER GLOWY */
#sidebar .admin-header{
  text-align:center;
  padding:18px 10px 16px;
  border-bottom:1px solid rgba(214,51,108,0.28);
}
#sidebar .admin-header i{
  font-size:42px;
  color:var(--pink-dark);
  margin-bottom:6px;
}
#sidebar .admin-header h5{
  font-size:16px;
  font-weight:700;
  letter-spacing:0.14em;
  color:var(--pink-dark);
}


.sidebar-title {
  font-size: 12px;
  font-weight: 500;
  padding-left: 20px;       /* 👈 PARA PANTAY SA BUTTONS */
  margin-top: 20px;
  margin-bottom: 4px;
  color: #6D2E3A;
  opacity: 0.8;
}

#sidebar .nav-link.active::before{
  content:"";
  position:absolute;
  left:0;
  top:6px;
  bottom:6px;
  width:3px;
  border-radius:20px;
  background:var(--pink-accent);
}

/* tanggalin yung left pink line sa active */
#sidebar .nav-link.active::before{
  content:none;
}


/* Logout (same style) */
#sidebar .mt-auto .nav-link{
  margin-top:10px;
}

/* NOTIF BADGE – bilog sa taas ng bell */
#notif-count.notif-badge{
  background:#d90429 !important;
  color:#fff !important;
  border-radius:50% !important;
  width:18px !important;
  height:18px !important;
  display:flex !important;
  align-items:center !important;
  justify-content:center !important;
  font-size:11px !important;
  position:absolute !important;
  right:-10px !important;
  top:-10px !important;
  font-weight:700 !important;
  border:2px solid #fce6ec !important;
  box-shadow:0 2px 8px rgba(0,0,0,0.15) !important;
  padding:0 !important;
}

/* RIGHT CONTENT – glass card */
#content{
  margin-left:250px;
  padding:18px 22px;
  min-height:100vh;
  display:flex;
}
#main-display{
  flex:1;
  background:rgba(255,255,255,0.9);
  border-radius:18px;
  padding:18px 20px 30px;
  box-shadow:0 14px 32px rgba(0,0,0,0.10);
}

/* TABLES same pink scheme */
.table{
  border:1px solid #f3a9be !important;
  border-collapse:collapse !important;
  font-size:14px;
}
.table thead{
  background-color:#f8d7dc;
  color:var(--pink-dark);
}
.table thead th{
  padding:12px 10px !important;
  font-weight:700 !important;
  border:1px solid #d96d84;
}
.table tbody td{
  background:#fff !important;
  padding:10px !important;
  border:1px solid #f3a9be !important;
}
.table tbody tr:hover{
  background:#fde8ee;
}

/* MOBILE – slide in sidebar + topbar mo */
@media(max-width:768px){
  #sidebar{
    transform:translateX(-100%);
  }
  #sidebar.active{
    transform:translateX(0);
  }
  #content{
    margin-left:0;
    padding:14px;
  }
  #main-display{
    border-radius:14px;
    padding:14px 14px 22px;
  }
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

.notif-badge {
  background: #ab475c !important;      /* force color */
  color: #fff !important;
  border-radius: 50% !important;       /* force bilog kahit anong number! */
  width: 9px !important;              /* fixed width */
  height: 9px !important;             /* fixed height */
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 2.5px !important;          /* px not em! */
  position: absolute !important;
  right: -13px !important;
  top: -13px !important;
  font-weight: bold !important;
  box-shadow: 0 2px 8px #bebebe12 !important;
  box-sizing: border-box !important;
  font-family: 'Poppins', Arial, sans-serif !important;
  z-index: 20 !important;
  padding: 0 !important;
  text-align: center !important;
  letter-spacing: 0 !important;
}

    .pretty-modal {
      border-radius: 18px !important;
      border: none !important;
      background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%);
      box-shadow: 0 10px 28px rgba(0,0,0,0.12);
      overflow: hidden;
    }

    .pretty-modal .modal-header {
      border: none;
      background: transparent;
      padding-bottom: 0.25rem;
      position: relative;
    }

    .pretty-modal .modal-header::after {
      content: "";
      position: absolute;
      left: 12px;
      right: 12px;
      bottom: 0;
      height: 2px;
      background: rgba(232, 169, 178, 0.8); /* soft pink line */
    }

    .pretty-modal .modal-title {
      font-weight: 700;
      color: #6d2e3a;
      font-size: 20px;
    }

    .pretty-modal .modal-body {
      color: #6d2e3a;
      font-size: 16px;
    }

    .pretty-modal .modal-footer {
      border: none;
      padding-top: 0;
    }

    .pretty-modal .btn-primary-main {
      background-color: #6d2e3a;
      border-color: #6d2e3a;
      color: #fff;
    }

    .pretty-modal .btn-primary-main:hover {
      background-color: #4f2029;
      border-color: #4f2029;
    }

    .pretty-modal .btn-secondary {
      background-color: #fff;
      color: #6d2e3a;
      border: 1px solid #e8a9b2;
    }

    .pretty-modal .btn-secondary:hover {
      background-color: #ffeaf0;
    }
    
    #logoutModal .modal-dialog {
      margin: 0 auto;              /* center horizontally */
      display: flex;
      align-items: center;         /* center vertically */
      min-height: 100vh;           /* occupy full viewport height */
    }

/* Dim ng lahat ng Bootstrap modal sa admin dashboard */
body.admin-dashboard .modal-backdrop.show {
    opacity: 0.5 !important;                       /* gaano ka-dim */
    background-color: rgba(0, 0, 0, 0.5) !important;
}

@media (max-width: 767px) {
      .modal-dialog {
        max-width: 320px;
        margin: 0 auto;
      }
      .pretty-modal .modal-header {
        padding: 10px 14px;
      }
      .pretty-modal .modal-title {
        font-size: 17px;
      }
      .pretty-modal .btn-close {
        transform: scale(0.9);
      }
      .pretty-modal .modal-body {
        padding: 14px 16px;
        font-size: 13px;
      }
      .pretty-modal .modal-footer {
        padding: 8px 14px 12px;
        gap: 6px;
      }
      .pretty-modal .modal-footer .btn {
        padding: 6px 10px;
        font-size: 13px;
        flex: 1 1 0;
        min-width: 0;
      }
}

</style>
</head>

<!-- ✅ Added for mobile toggle -->
<div id="overlay"></div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content pretty-modal">
            <div class="modal-header py-3">
                <h5 class="modal-title"><strong>Confirm Log Out</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <h6 class="mb-3">Are you sure you want to log out?</h6>
            </div>
            <div class="modal-footer justify-content-end gap-2 px-4 pb-4">
                <a href="admin_logout.php" class="btn btn-primary-main px-3">Log Out</a>
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<body class="admin-dashboard">
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
      <p class="sidebar-title">MAIN</p>    
      <a href="admindashboard.php?page=dashboard" class="nav-link <?php if($page=='dashboard') echo 'active'; ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
      <a href="admindashboard.php?page=audit" class="nav-link <?php if($page=='audit') echo 'active'; ?>">
        <i class="bi bi-clipboard-data-fill"></i> Audit Logs
      </a>
      
     <a href="admindashboard.php?page=notification" class="nav-link <?php if($page=='notification') echo 'active'; ?>">
    <span style="position:relative;">
      <i class="fa-solid fa-bell"></i>
      <span id="notif-count" class="notif-badge"></span>
    </span>
    Notifications
    </a>

      <p class="sidebar-title">MANAGEMENT</p>  
      <a href="admindashboard.php?page=customers" class="nav-link <?php if($page=='customers') echo 'active'; ?>">
        <i class="bi bi-people-fill"></i> Customers
      </a>
      <a href="admindashboard.php?page=addproducts" class="nav-link <?php if($page=='addproducts') echo 'active'; ?>">
        <i class="bi bi-plus-square-fill"></i> Inventory
      </a>
      <p class="sidebar-title">TRANSACTIONS</p>  
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
            echo '</div>';  // ➕ IDAGDAG ITO
            break;
         case 'notification':
            echo '<div class="table-responsive">';
            include("admin_notifications.php");
            echo '</div>';  // ➕ IDAGDAG ITO
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
            echo '</div>';  // ➕ IDAGDAG ITO
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
    
  document.querySelectorAll('[data-bs-target="#logoutModal"]').forEach(btn => {
    btn.addEventListener('click', function() {
      overlay.style.display = "none";
    });
  });

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
  
  
  
  
  function updateNotifCount() {
  fetch('notif_count.php')
    .then(r => r.text())
    .then(count => {
      document.getElementById('notif-count').textContent = count > 0 ? count : '';
    });
}
updateNotifCount();
setInterval(updateNotifCount, 5000);
  
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
