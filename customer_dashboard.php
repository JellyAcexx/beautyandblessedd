<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// ✅ Only logged-in users with login_id can view
if (!isset($_SESSION['login_id'])) {
    header("Location: homepage.php");
    exit();
}

$login_id = $_SESSION['login_id'];
$register_id = $_SESSION['register_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

// ---------------------------
// LOAD PROFILE INFORMATION
// ---------------------------
include 'database.php';
$user_data = null;

if ($register_id) {
    $stmt = $conn->prepare("
        SELECT register_fname, register_lname, register_email, phone_number
        FROM registers_tb
        WHERE register_id = ?
    ");
    $stmt->bind_param("i", $register_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
    }
    $stmt->close();
}


// bilang ng unread notifications ng logged-in customer
$sqlCount = "SELECT COUNT(*) AS unread_count 
             FROM notifcustomer 
             WHERE register_id = ? AND is_read = 0";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param("i", $register_id);
$stmtCount->execute();
$resCount = $stmtCount->get_result();
$rowCount = $resCount->fetch_assoc();
$stmtCount->close();

$unreadCount = (int)$rowCount['unread_count'];

// Count cart_items for this customer
$cartCount = 0;
$sqlCart = "
    SELECT COUNT(*) AS cart_count
    FROM cart_items ci
    INNER JOIN cart c ON ci.cart_id = c.cart_id
    INNER JOIN login_tb l ON c.login_id = l.login_id
    WHERE l.register_id = ?
";
$stmtCart = $conn->prepare($sqlCart);
$stmtCart->bind_param("i", $register_id);
$stmtCart->execute();
$resCart = $stmtCart->get_result();
if ($rowCart = $resCart->fetch_assoc()) {
    $cartCount = (int)$rowCart['cart_count'];
}
$stmtCart->close();

$initialCartCount = $cartCount;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?famil=Poppins:wght@400;500;600;700&display=swap">

    <style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: auto;
        overflow-x: hidden;
        scrollbar-width: none;     /* Firefox */
        -ms-overflow-style: none;  /* IE 10+ */
        overscroll-behavior: none;
        font-family: 'Poppins', sans-serif !important;
        color: #6d2e3a;
        background-color: #fff;
        font-size: 18px;
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
    .content-inner {
        background: #fffafc;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.03);
        height: 100%;
        padding: 22px;
        box-sizing: border-box;
        overflow: hidden;
    }
    button:focus-visible,
    a.btn:focus-visible {
      outline: 2px solid #ec7699;
      outline-offset: 2px;
    }
    .fa-user {
      color: #6d2e3a !important;
    }
    #sidebar {
      background: linear-gradient(180deg, #FCE6EC 0%, #F8D7DC 40%, #F8D7DC 100%);
      border-right: 1px solid #e6b8c2;
      min-width: 260px;
      max-width: 260px;
    }
    #sidebarNav .nav-link,
    .logout-btn {
        padding: 10px 15px;
        border-radius: 8px;
        transition: background-color 0.2s ease, color 0.2s ease;
        font-weight: 500;
    }
    #sidebarNav .nav-item + .nav-item {
      margin-top: 2px;
    }
    #sidebar .nav-link {
      border-radius: 8px;
      padding: 8px 12px;
      font-size: 15px !important;
      display: flex;
      align-items: center;
      gap: 8px;
      color: #6D2E3A;
      transition: background-color .15s ease, color .15s ease, transform .1s ease;
    }
    #sidebar .nav-link:hover {
      background-color: rgba(232,169,178,0.15);
      transform: translateX(2px);
    }
    #sidebar .nav-link i {
      font-size: 16px;
    }
    #sidebar .nav-link.active {
      background-color: transparent !important; 
      border-radius: 5px;
      font-weight: 900;
    }
    #sidebar .nav-link.active i {
      color: #6D2E3A !important;    
      font-size: 16.5px;
    }
    .profile-card .card-body p.small {
      font-size: 12px;
      color: #a95469;
    }
    .profile-card {
      border-radius: 14px !important;
      border: none !important;
      background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 40%, #ffffff 100%);
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
      position: relative;
      overflow: hidden;
    }
    .profile-card::before {
      content: "";
      position: absolute;
      top: -40px;
      right: -40px;
      width: 90px;
      height: 90px;
      background: rgba(109,46,58,0.12);
      border-radius: 50%;
    }
    .profile-card .card-body {
      position: relative;
      z-index: 1;
    }
    .profile-card .avatar-ring {
      width: 68px;
      height: 68px;
      border-radius: 50%;
      border: 3px solid #a95469;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 6px;
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .profile-card .avatar-ring i {
      color: #6d2e3a;
      font-size: 26px;
    }
    .profile-card .avatar-ring:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }
    .profile-name {
      font-size: 16px;
      font-weight: 700;
      color: #6d2e3a;
    }
    #profileFullname {
      font-size: 17px !important;
    }
    #loadingModal {
        display: none;
        position: fixed;
        top: 0; 
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.45);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .loader-container {
        display: flex;
        flex-direction: column; /* spinner sa taas, text sa baba */
        justify-content: center;
        align-items: center;
        gap: 10px;              /* maliit na space sa pagitan */
        min-width: 150px;       /* optional para di mukhang dikit sa left */
    }
    .spinner {
        border: 6px solid  #6D2E3A;
        border-top: 6px solid var(--pink-soft);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .loader-text {
        color: #6D2E3A;
        font-weight: bold;
        font-size: 16px;
    }
    .form-label {
      color: #6D2E3A !important;
      font-weight: bold;
    }
    .form-control {
      color: #6D2E3A !important;
      border: 1px solid #6D2E3A;
    }
    .form-control:focus {
      outline: none;
      border-color: #6D2E3A;
      box-shadow:
        0 0 0 0.15rem rgba(109, 46, 58, 0.35);
    }
    #successAlert {
        border-left: 5px solid #D96D84 ;
        font-size: 16px;
        padding: 10px 15px;
    }
    .app-row {
        height: 100vh;
        overflow: hidden;
    }
    .content-col {
        padding: 0;
        height: 100vh;
        overflow: hidden;
    }
    #dashboardFrame {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
        border-radius: 10px;
    }
    #mobileFooterNav .nav-item {
      flex: 1 1 0%;     /* Each item gets equal width */
      min-width: 0;     /* No overflow */
      text-align: center;
      padding: 0;
      margin: 0;
    }
    #mobileFooterNav .footer-btn i {
      transition: transform .12s ease, color .12s ease;
    }
    #mobileFooterNav .footer-btn:active i {
      transform: scale(.92) translateY(1px);
    }
    #mobileFooterNav .footer-btn {
      width: 100%;      /* Button fills li */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding-top: 2px;
      padding-bottom: 2px;
    }
   
    #cartBadgeFooter,
    #notifBadgeFooter {
      position: absolute;
      top: -5px;
      right: -7px;
      background: #d90429;
      color: #fff;
      font-size: 12px;
      min-width: 16px;
      height: 16px;
      display: flex;
      align-items: center;     /* vertical center */
      justify-content: center; /* horizontal center */
      border-radius: 50%;
      border: 2px solid #fff;
      padding: 0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      font-weight: 700;
      z-index: 1;
    }
    #mobileFooterNav {
      background: #ffffff !important;
      border-top: 1px solid #e6b8c2;
      box-shadow: 0 -3px 12px rgba(0,0,0,0.06);
      color: #6D2E3A;
      z-index: 1055;
      font-family: 'Poppins', sans-serif;
      height: 52px;
      min-height: 52px;
    }
    #mobileFooterNav .footer-btn,
    #mobileFooterNav .footer-profile-btn,
    #mobileFooterNav .footer-logout-btn {
      color: #6D2E3A;
      font-size: 11px;
      font-weight: 500;
    }

    #mobileFooterNav .footer-btn i,
    #mobileFooterNav .footer-profile-btn i,
    #mobileFooterNav .footer-logout-btn i {
      font-size: 20px !important;
      transition: transform .15s ease, color .15s ease;
    }

    #mobileFooterNav .footer-btn.active i,
    #mobileFooterNav .footer-btn.active .footer-label {
      color: #ec7699 !important;
    }

    #mobileFooterNav .footer-btn.active i {
      transform: translateY(-1px);
    }
    .footer-profile-btn,
    .footer-logout-btn {
      background: none;
      border: none;
      outline: none;
      width: 100%;
      text-align: center;
      color: #6D2E3A;
      font-size: 13px;
      padding: 0;
      transition: color 0.2s;
    }
    .footer-btn {
      background: none;
      border: none;
      outline: none;
      width: 100%;
      text-align: center;
      color: #6D2E3A;
      font-size: 13px;
      padding: 0;
      transition: color 0.2s;
    }
    .footer-btn.active,
    .footer-btn:active,
    .footer-btn:focus {
      color: #ec7699 !important;
    }
    .footer-btn.active .footer-label,
    .footer-btn:active .footer-label,
    .footer-btn:focus .footer-label {
      color: #ec7699 !important;
    }
    .footer-label {
      font-size: 10px;
      margin-top: -6px;
      letter-spacing: 0.02em;
      color: #6D2E3A;
      padding-bottom: 8px !important;
    }
    .icon-wrap {
      position: relative; /* para ang badge ay absolute relative dito */
      display: inline-block;
      justify-content: center;
    }
    .icon-wrap i {
      font-size: 16px !important;
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
      left: 16px;
      right: 16px;
      bottom: 0;
      height: 1px;
      background: rgba(232, 169, 178, 0.8); /* soft pink line */
    }

    .pretty-modal .modal-title {
      font-weight: 700;
      color: #6d2e3a;
      font-size: 18px;
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
    @media (max-width: 991.98px) {
        .app-row {
            height: auto;
        }
        #sidebar {
            display: none;
        }
        .content-col {
            height: auto;
        }
        .content-inner {
            padding: 12px;
            min-height: 100vh;
            padding-bottom: 62px !important;
            margin-bottom: 0 !important;
            background: transparent;
        }
    }
    @media (min-width: 992px) {
        html, body {
            overflow: hidden;
        }
        #mobileFooterNav { display: none !important; }
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
      .pretty-modal .modal-body .form-label {
        font-size: 12px;
      }
      .pretty-modal .modal-body .form-control {
        font-size: 13px;
        padding: 6px 8px;
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
      .rounded-circle {
        width: 55px !important;
        height: 55px !important;
      }
      #profileFullname {
        font-size: 16px !important;
      }
    }
    
    #cartBadgeSidebar,
    #cartBadgeFooter,
    #notifBadgeSidebar,
    #notifBadgeFooter {
      background: #d90429;
      color: #fff;
      border-radius: 50%;
      min-width: 18px;
      height: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 700;
      padding: 0;
    }
    
/* Sidebar notification badge only */
#cartBadgeSidebar,
#notifBadgeSidebar {
  position: absolute;
  top: -7px;
  right: -8px;           /* adjust mo kung gusto mo ilapit/ilayo */
  background: #d90429;
  color: #fff;
  font-size: 10px;
  min-width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  border: 2px solid #fff;
  padding: 0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  font-weight: 700;
  z-index: 1;
}

  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row flex-nowrap app-row">
    <!-- SIDEBAR -->
    <div class="col-auto d-none d-md-flex flex-column p-3 min-vh-100" id="sidebar">
      <div class="card text-center mx-auto mb-4 w-100 profile-card">
        <div class="card-body p-3">
          <div class="avatar-ring"
              data-bs-toggle="modal" data-bs-target="#profileModal">
            <i class="fa-solid fa-user"></i>
          </div>
          <p class="m-0 profile-name">
            <?php echo $user_data ? htmlspecialchars($user_data['register_fname']) : 'Customer'; ?>
          </p>
          <p class="small">Welcome back to your dashboard</p>
        </div>
      </div>

      <ul class="nav flex-column" role="tablist" id="sidebarNav">
        <li class="nav-item mb-2"><a class="nav-link desktop-nav active" href="#" onclick="loadPage('homepage.php', this)" data-page="homepage.php"><i class="fa-solid fa-house me-2"></i> Home</a></li>
        <li class="nav-item mb-2 position-relative">
            <a class="nav-link desktop-nav" href="#" 
               onclick="loadPage('customer_notifications.php', this)" 
               data-page="customer_notifications.php">
                 <div class="icon-wrap position-relative">
                    <i class="bi bi-bell-fill"></i>
                        <span id="notifBadgeSidebar" class="badge notif-badge position-absolute"">
                        <?= $unreadCount > 0 ? $unreadCount : '' ?>
                    </span>
                </div>
                Notifications
            </a>
        </li>
        <li class="nav-item mb-2 position-relative">
            <a class="nav-link desktop-nav" href="#"
              onclick="loadPage('cart.php', this)"
              data-page="cart.php">
                <div class="icon-wrap position-relative me-2">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span id="cartBadgeSidebar"
                          class="badge rounded-pill position-absolute"
                          style="display: <?= ($initialCartCount ?? 0) > 0 ? 'inline-flex' : 'none'; ?>;">
                        <?= $initialCartCount ?? '' ?>
                    </span>
                </div>
                Cart
            </a>
        </li>
        <li class="nav-item mb-2"><a class="nav-link desktop-nav" href="#" onclick="loadPage('reservation.php', this)" data-page="reservation.php"><i class="fa-solid fa-calendar-days me-2"></i> Reservations</a></li>
        <li class="nav-item mb-2"><a class="nav-link desktop-nav" href="#" onclick="loadPage('orders.php', this)" data-page="orders.php"><i class="fa-solid fa-box-archive me-2"></i> Purchase History</a></li>
      </ul>

      <div class="mt-auto w-100 px-2 mb-2">
        <a href="#" class="nav-link desktop-nav logout-btn d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="fa-solid fa-right-from-bracket me-2"></i>
          <span>Log Out</span>
        </a>
      </div>
    </div>

    <!-- RIGHT CONTENT: iframe fills this column entirely -->
    <div class="col content-col">
      <div class="content-inner">
        <?php if (!empty($success_message)): ?>
          <div id="successAlert" class="alert alert-success alert-dismissible fade show text-center m-3" role="alert">
            <strong><?php echo htmlspecialchars($success_message); ?></strong>
          </div>
        <?php endif; ?>

        <iframe id="dashboardFrame" src="homepage.php" title="Dashboard Frame"></iframe>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER NAVIGATION - MOBILE -->
<nav id="mobileFooterNav" class="d-md-none fixed-bottom bg-light border-top py-1">
  <div class="container-fluid px-0">
    <ul class="nav justify-content-between align-items-center mb-0" style="width: 100%;">
      <li class="nav-item flex-fill">
        <button class="footer-btn py-2" onclick="loadPage('homepage.php', this)" data-page="homepage.php">
          <div class="icon-wrap mb-1"><i class="fa-solid fa-house fa-2x"></i></div>
          <div class="footer-label">Home</div>
        </button>
      </li>
      <li class="nav-item flex-fill position-relative">
       <button class="footer-btn py-2 position-relative" 
        onclick="loadPage('customer_notifications.php', this)" 
        data-page="customer_notifications.php">
          <div class="icon-wrap mb-1 position-relative">
            <i class="fa-solid fa-bell fa-2x"></i>
            <span id="notifBadgeFooter" class="badge rounded-pill position-absolute">
                <?= $unreadCount > 0 ? $unreadCount : '' ?>
            </span>
          </div>
          <div class="footer-label">Notifications</div>
        </button>
      </li>
      <li class="nav-item flex-fill position-relative">
          <button class="footer-btn py-2 position-relative"
                  onclick="loadPage('cart.php', this)"
                  data-page="cart.php">
              <div class="icon-wrap mb-1 position-relative">
                  <i class="fa-solid fa-cart-shopping fa-2x"></i>
                  <span id="cartBadgeFooter"
                        class="badge rounded-pill position-absolute"
                        style="display: <?= ($initialCartCount ?? 0) > 0 ? 'inline-flex' : 'none'; ?>;">
                      <?= $initialCartCount ?? '' ?>
                  </span>
              </div>
              <div class="footer-label">Cart</div>
          </button>
      </li>
      <li class="nav-item flex-fill">
        <button class="footer-btn py-2" onclick="loadPage('reservation.php', this)" data-page="reservation.php">
          <div class="icon-wrap mb-1"><i class="fa-solid fa-calendar-days fa-2x"></i></div>
          <div class="footer-label">Reservations</div>
        </button>
      </li>
      <li class="nav-item flex-fill">
        <button class="footer-btn py-2" onclick="loadPage('orders.php', this)" data-page="orders.php">
          <div class="icon-wrap mb-1"><i class="fa-solid fa-box-archive fa-2x"></i></div>
          <div class="footer-label">History</div>
        </button>
      </li>
      <li class="nav-item flex-fill">
        <button class="footer-profile-btn profile-btn-footer py-2" data-bs-toggle="modal" data-bs-target="#profileModal">
          <div class="icon-wrap mb-1"><i class="fa-solid fa-user fa-2x"></i></div>
          <div class="footer-label">Profile</div>
        </button>
      </li>
      <li class="nav-item flex-fill">
        <button class="footer-logout-btn py-2" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <div class="icon-wrap mb-1"><i class="fa-solid fa-right-from-bracket fa-2x"></i></div>
          <div class="footer-label">Logout</div>
        </button>
      </li>
    </ul>
  </div>
</nav>

<!-- LOGOUT MODAL -->
<div class="modal fade" id="logoutModal" tabindex="-1">
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
        <a href="logout.php" class="btn btn-primary-main px-3">Log Out</a>
        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- PROFILE MODAL -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content pretty-modal text-center">
      <div class="modal-header py-3">
        <h5 class="modal-title fw-bold">Customer Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <div class="rounded-circle bg-light mx-auto d-flex align-items-center justify-content-center"
             style="width: 80px; height: 80px; border: 3px solid #6d2e3a;">
          <i class="fa-solid fa-user fa-2x text-dark"></i>
        </div>
        <h5 id="profileFullname" class="fw-bold mt-3 mb-0" style="color: var(--pink-dark);">
          <?php echo $user_data ? htmlspecialchars($user_data['register_fname'] . ' ' . $user_data['register_lname']) : 'John Doe Maranan'; ?>
        </h5>
        <hr class="my-3">
        <div class="mb-2" style="color: var(--pink-dark);">
          <i class="fa-solid fa-envelope me-2"></i>
          <span id="profileEmail">
            <?php echo $user_data ? htmlspecialchars($user_data['register_email']) : 'john@example.com'; ?>
          </span>
        </div>
        <div class="mb-4" style="color: var(--pink-dark);">
          <i class="fa-solid fa-phone me-2"></i>
          <span id="profilePhone">
            <?php echo $user_data ? htmlspecialchars($user_data['phone_number']) : '+63 912 345 6789'; ?>
          </span>
        </div>
        <div class="d-grid">
          <button class="btn btn-primary-main" data-bs-toggle="modal" data-bs-target="#editProfileModal" data-bs-dismiss="modal">
            Edit Profile
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- EDIT PROFILE MODAL -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content pretty-modal">
      <div class="modal-header py-3">
        <h5 class="modal-title">
          <i class="fa-solid fa-pen me-2"></i>Edit Profile
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="editProfileForm">
        <div class="modal-body" style="padding: 20px 24px 10px;">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="editFname" class="form-label">First Name</label>
              <input type="text" class="form-control" id="editFname" name="fname" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="editLname" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="editLname" name="lname" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="editEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editEmail" name="email" required>
          </div>

          <div>
            <label for="editPhone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="editPhone" name="phone" required>
          </div>
        </div>

        <div class="modal-footer justify-content-end px-4 pb-4">
          <button type="submit" class="btn btn-primary-main px-4">Save</button>
          <button type="button" class="btn btn-secondary px-3 ms-2" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="loadingModal">
  <div class="loader-container">
      <div class="spinner"></div>
      <div class="loader-text">Please wait...</div>
  </div>
</div>

<!-- ✅ SUCCESS MODAL CARD -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content pretty-modal text-center">
    <div class="modal-header py-3">
      <h5 class="modal-title"><strong>Success</strong></h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body text-center py-3" style="color: var(--pink-dark);">
        Profile updated successfully!
      </div>
      <div class="modal-footer py-2" style="border: none; justify-content: center;">
        <button type="button" class="btn btn-primary-main" data-bs-dismiss="modal">
          OK
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://kit.fontawesome.com/6430335d41.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<script>
// Sync current page globally (with persistence using localStorage)
let currentPage = 'homepage.php'; // Default frame page

// --- Highlight active nav-link/nav-item in both sidebar and mobileFooterNav ---
function setActiveNavLinks(page) {
    // Standard sync (NO MODAL OPEN)
    document.querySelectorAll('#sidebar .nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('data-page') === page) {
            link.classList.add('active');
        }
    });
    document.querySelectorAll('#mobileFooterNav .footer-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-page') === page) {
            btn.classList.add('active');
        }
    });
}

// --- Unified loader function ---
function loadPage(page, element = null) {
    document.getElementById('dashboardFrame').src = page;
    currentPage = page.split('/').pop();
    setActiveNavLinks(currentPage);
    localStorage.setItem('customerDashboardFrame', currentPage);
}

// --- Receive messages from child iframe
window.addEventListener("message", function(event) {
    if (!event.data) return;

    if (event.data.type === "changeFrame") {
        loadPage(event.data.page);
    }

    if (event.data.type === "cartCountUpdate") {
        const count = parseInt(event.data.count) || 0;
        const badgeSidebar = document.getElementById('cartBadgeSidebar');
        const badgeFooter = document.getElementById('cartBadgeFooter');

        [badgeSidebar, badgeFooter].forEach(badge => {
            if (!badge) return;
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-flex';
            } else {
                badge.textContent = '';
                badge.style.display = 'none';
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const storedPage = localStorage.getItem('customerDashboardFrame');
    if (!storedPage) {
        // First time/login, default to homepage.php and active Home button
        loadPage('homepage.php');
        setActiveNavLinks('homepage.php');
    } else {
        // Restore last visited frame/page
        loadPage(storedPage);
        setActiveNavLinks(storedPage);
    }
});

let currentUserData = {
    fname: <?php echo json_encode($user_data ? $user_data['register_fname'] : ''); ?>,
    lname: <?php echo json_encode($user_data ? $user_data['register_lname'] : ''); ?>,
    email: <?php echo json_encode($user_data ? $user_data['register_email'] : ''); ?>,
    phone: <?php echo json_encode($user_data ? $user_data['phone_number'] : ''); ?>
};

// --- Restore previous frame and active nav on page load ---
document.addEventListener('DOMContentLoaded', function () {
    const storedPage = localStorage.getItem('customerDashboardFrame');
    const page = storedPage || currentPage;
    loadPage(page);
    setActiveNavLinks(page);

    // --- Success Alert Modal Fade ---
    let alertBox = document.getElementById("successAlert");
    if (alertBox) {
        setTimeout(() => {
            let bsAlert = new bootstrap.Alert(alertBox);
            bsAlert.close();
        }, 2000);
        alertBox.addEventListener('closed.bs.alert', function () {
            alertBox.remove();
        });
    }

    // --- Profile Modal autofill on open (EDIT PROFILE) ---
    const modalEl     = document.getElementById("editProfileModal");
    const fnameField  = document.getElementById("editFname");
    const lnameField  = document.getElementById("editLname");
    const emailField  = document.getElementById("editEmail");
    const phoneField  = document.getElementById("editPhone");
    const loadingModal = document.getElementById("loadingModal");

    function showLoading() { loadingModal.style.display = "flex"; }
    function hideLoading() { setTimeout(() => { loadingModal.style.display = "none"; }, 1000); }

    // ✅ Tuwing mag-o-open ang Edit Profile modal, laging lalagay ang current values
    modalEl.addEventListener("show.bs.modal", () => {
        fnameField.value = currentUserData.fname || '';
        lnameField.value = currentUserData.lname || '';
        emailField.value = currentUserData.email || '';
        phoneField.value = currentUserData.phone || '';
    });

    document.getElementById("editProfileForm").addEventListener("submit", function (e) {
        e.preventDefault();
        showLoading();
        const formData = new FormData(this);
        formData.append("register_id", <?php echo json_encode($register_id); ?>);

        fetch("update_profile.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            setTimeout(() => {
                if (data.success) {
                    // bagong values galing sa form
                    const newFname = fnameField.value.trim();
                    const newLname = lnameField.value.trim();
                    const newEmail = emailField.value.trim();
                    const newPhone = phoneField.value.trim();

                    // ✅ i-update ang JS copy para next open ng modal, updated pa rin
                    currentUserData = {
                        fname: newFname,
                        lname: newLname,
                        email: newEmail,
                        phone: newPhone
                    };

                    // ✅ isara Edit Profile modal
                    const editModalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (editModalInstance) editModalInstance.hide();

                    // ✅ update din ang UI (sidebar name + profile modal info)
                    const profileNameEl = document.querySelector(".profile-name");
                    if (profileNameEl) profileNameEl.textContent = newFname || 'Customer';

                    const profileFullnameEl = document.getElementById("profileFullname");
                    if (profileFullnameEl) profileFullnameEl.textContent = `${newFname} ${newLname}`.trim();

                    const profileEmailEl = document.getElementById("profileEmail");
                    if (profileEmailEl) profileEmailEl.textContent = newEmail;

                    const profilePhoneEl = document.getElementById("profilePhone");
                    if (profilePhoneEl) profilePhoneEl.textContent = newPhone;

                    // ✅ show success modal (walang page reload)
                    const successModal = new bootstrap.Modal(document.getElementById("successModal"));
                    successModal.show();

                } else {
                    alert("❌ Failed to update profile: " + data.error);
                }
            }, 1000);
        })
        .catch(err => {
            hideLoading();
            alert("❌ Error updating profile: " + err);
        });
    });
});

// --- Cancel profile edit reopens profile modal ---
document.addEventListener("DOMContentLoaded", () => {
    const cancelBtn = document.querySelector("#editProfileModal .btn-secondary");
    if (cancelBtn) {
        cancelBtn.addEventListener("click", () => {
            const editModalEl = document.getElementById("editProfileModal");
            const editModal = bootstrap.Modal.getInstance(editModalEl);
            if (editModal) editModal.hide();
            setTimeout(() => {
                const profileModalEl = document.getElementById("profileModal");
                const profileModal = new bootstrap.Modal(profileModalEl);
                profileModal.show();
            }, 300);
        });
    }
});

// --- Responsive sidebar and footer nav sync on resize ---
window.addEventListener('resize', function() {
    setActiveNavLinks(currentPage);
});

// --- Font consistency for iframe dashboard frames ---
document.addEventListener("DOMContentLoaded", () => {
    document.body.style.fontFamily = "'Poppins', sans-serif";
    const iframe = document.getElementById('dashboardFrame');
    iframe.onload = () => {
        try {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            if (!doc) return;
            const style = document.createElement('style');
            style.innerHTML = "* { font-family: 'Poppins', sans-serif !important; }";
            doc.head.appendChild(style);
        } catch (e) {
            // cross-origin pages will throw; ignore gracefully
            console.warn("Could not inject styles into iframe (likely cross-origin).", e);
        }
    };
    // --- Sync navs on iframe load ---
    iframe.addEventListener("load", function () {
        let page = iframe.src.split('/').pop();
        setActiveNavLinks(page);
        currentPage = page;
        localStorage.setItem('customerDashboardFrame', page);
    });
});

// --- Fetch unread notifications and update sidebar/footer badge ---
function updateNotifBadge() {
  const badgeSidebar = document.getElementById('notifBadgeSidebar');
  const badgeFooter = document.getElementById('notifBadgeFooter');
  
fetch('get_unread_notif_count_customer.php')
    .then(res => res.json())
    .then(data => {
      [badgeSidebar, badgeFooter].forEach(badge => {
        if (badge) {
          if (data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = 'inline-block';
          } else {
            badge.style.display = 'none';
          }
        }
      });
    })
    .catch(err => console.error('Error fetching notification count:', err));
}


// --- Receive messages from child iframe ---
window.addEventListener("message", function(event) {
    if (event.data && event.data.type === "changeFrame") {
        loadPage(event.data.page);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    updateNotifBadge();
    setInterval(updateNotifBadge, 100); 
});


</script>
</body>
</html>
