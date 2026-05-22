<?php
session_start();
include 'database.php';

$error = "";

// ✅ kung naka-login na, huwag na sa login page
if (isset($_SESSION['admin_email'])) {
    header("Location: admindashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin_login_tb WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_email'] = $row['email'];

            // Save to log_history_tb
            $stmt_log = $conn->prepare("INSERT INTO log_history_tb (email, password, status) VALUES (?, ?, 'Login')");
            $stmt_log->bind_param("ss", $row['email'], $row['password']);
            $stmt_log->execute();

            header("Location: admindashboard.php?success=Welcome admin, you are successfully logged in!");
            exit();
        } else {
            $error = "Incorrect Password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>

/* 🌸 RESET */
html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  overflow: hidden;
}

/* 🌸 BACKGROUND WRAPPER */
body {
  background: #000; /* fallback */
}

/* 🌸 ALWAYS-FULL BLUR LAYER */
.bg-blur {
  position: fixed;
  inset: 0;

  background: url("new44.jpg") center center no-repeat;
  background-size: cover;

  /* keeps BG zoomed so card doesn’t look far */
  transform: scale(1.15);
  transform-origin: center;

  filter: blur(4px);
  z-index: -1;
}

/* ⭐ CENTER */
.admin-wrapper {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 400px;
}

/* ⭐ SAME GLASS CARD STYLE AS CUSTOMER */
.admin-card {
  width: 100%;
  padding: 30px 35px;
  background: rgba(255,255,255,0.40);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-radius: 18px;
  box-shadow: 0 10px 35px rgba(0,0,0,0.25);
  color: #fff;
  position: relative;
}

/* Title */
.admin-card h3 {
  text-align: center;
  font-weight: 700;
  margin-bottom: 25px;
  color: #fff;
}

/* Labels */
.admin-card label {
  color: #fff;
  font-weight: 500;
}

/* Inputs */
.admin-card .form-control {
  border-radius: 10px;
  border: none;
  font-size: 14px;
}

/* Button – gunakan color mo ng admin */
.btn-admin-login {
  background: #6D2E3A;
  color: #fff;
  font-weight: bold;
  border-radius: 10px;
  transition: 0.3s;
}

.btn-admin-login:hover {
  background: #6D2E3A;
}

/* Alerts */
.alert {
  border-radius: 10px;
  font-size: 0.9rem;
}

/* ⭐ RESPONSIVE SCALE EXACTLY LIKE CUSTOMER LOGIN */

/* default small screens */
.admin-card {
  transform: scale(1);
  transform-origin: center;
}

.loading-modal-center {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  min-height: 100vh !important;
  top: 0 !important; 
  left: 0 !important; 
  width: 100vw !important; 
  height: 100vh !important;
}

/* Removes extra margin on very small screens for centering */
@media (max-width: 767px) {
  .modal-dialog {
    margin: 0 auto !important;
    display: flex !important;
    align-items: center !important;
    min-height: 100vh !important;
  }
  .modal-content {
    margin: 0 auto !important;
  }
}

/* tablets */
@media (min-width: 768px) {
  .admin-card {
    transform: scale(1.50);
  }
}

/* ipad / big tablets */
@media (min-width: 1024px) {
  .admin-card {
    transform: scale(1.75);
  }
}

/* desktop */
@media (min-width: 1280px) {
  .admin-card {
    transform: scale(1.17);
  }
}

/* large monitors */
@media (min-width: 1600px) {
  .admin-card {
    transform: scale(1.22);
  }
}

/* If screen height small → prevent zoom */
@media (max-height: 700px) {
  .admin-card {
    transform: scale(1) !important;
  }
}

/* Very short screens */
@media (max-height: 650px) {
  .admin-card {
    transform: scale(0.95) !important;
  }
}

</style>

</head>
<body>

<!-- 🌸 BLURRED BACKGROUND LAYER -->
<div class="bg-blur"></div>

<div class="admin-wrapper">
  <div class="admin-card">

    <!-- ALERT -->
    <?php if (!empty($error)): ?>
      <div id="errorAlert" class="alert alert-danger text-center small">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= $error ?>
      </div>
    <?php endif; ?>

    <h3>Admin Login</h3>

    <form id="adminLoginForm" method="POST" action="" onsubmit="return showLoginLoading();">
      <div class="mb-3">
        <label>Email</label>
        <div class="input-group">
          <span class="input-group-text" style="background: rgba(109,46,58,0.13); border:none;">
            <i class="bi bi-envelope-fill" style="color: #6d2e3a; font-size: 15px;"></i>
          </span>
          <input type="email" id="log_email" name="email" placeholder="Enter your email"
                class="form-control"
                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                required>
        </div>
      </div>

      <div class="mb-3">
        <label>Password</label>
        <div class="input-group">
          <span class="input-group-text" style="background: rgba(109,46,58,0.13); border:none;">
            <i class="bi bi-lock-fill" style="color: #6d2e3a; font-size: 15px;"></i>
          </span>
          <input type="password" id="log_password" name="password" placeholder="Enter your password"
                class="form-control" required>
          <button type="button" class="btn btn-outline-secondary"
                  onclick="togglePassword('log_password', this)">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-admin-login w-100 py-2 mt-2">Login</button>
    </form>

    <div class="text-center mt-3">
      <p><a href="admin_forgot.php" class="text-white">Forgot Password?</a></p>
    </div>

  </div>
</div>


<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered loading-modal-center" style="max-width:210px;">
    <div class="modal-content" style="background:#fff5f9;border-radius:15px;box-shadow:0 3px 13px #f8d7dc57;">
      <div class="modal-body text-center p-2" style="font-size:0.97em; color:#6D2E3A;">
        <div class="spinner-border" style="color:#6D2E3A;width:1.65em;height:1.65em;" role="status"></div>
        <div style="font-weight:600; font-size:0.97em; margin-top:6px;">Logging In...</div>
      </div>
    </div>
  </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Toggle Password
function togglePassword(id, el) {
  const input = document.getElementById(id);
  if (input.type === "password") {
    input.type = "text";
    el.innerHTML = '<i class="bi bi-eye-slash"></i>';
  } else {
    input.type = "password";
    el.innerHTML = '<i class="bi bi-eye"></i>';
  }
}

// Auto-hide alert
document.addEventListener("DOMContentLoaded", () => {
  const alertBox = document.getElementById("errorAlert");
  if (alertBox) {
    setTimeout(() => {
      const bsAlert = new bootstrap.Alert(alertBox);
      bsAlert.close();
    }, 500);
  }
});

function togglePassword(id, el) {
  const input = document.getElementById(id);
  if (input.type === "password") {
    input.type = "text";
    el.innerHTML = '<i class="bi bi-eye-slash"></i>';
  } else {
    input.type = "password";
    el.innerHTML = '<i class="bi bi-eye"></i>';
  }
}

// Auto-hide alert - unchanged
document.addEventListener("DOMContentLoaded", () => {
  const alertBox = document.getElementById("errorAlert");
  if (alertBox) {
    setTimeout(() => {
      const bsAlert = new bootstrap.Alert(alertBox);
      bsAlert.close();
    }, 2500);
  }
});


function togglePassword(id, el) {
  const input = document.getElementById(id);
  if (input.type === "password") {
    input.type = "text";
    el.innerHTML = '<i class="bi bi-eye-slash"></i>';
  } else {
    input.type = "password";
    el.innerHTML = '<i class="bi bi-eye"></i>';
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const alertBox = document.getElementById("errorAlert");
  if (alertBox) {
    setTimeout(() => {
      const bsAlert = new bootstrap.Alert(alertBox);
      bsAlert.close();
    }, 500);
  }
});

function showLoginLoading() {
  var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'), { backdrop: 'static', keyboard: false });
  loadingModal.show();
  setTimeout(() => {
    loadingModal.hide();
    document.getElementById('adminLoginForm').submit();
  }, 1700); // Tagal ng spinner, adjust as you want (ms)
  return false; // JS submit lang magpapatuloy
}

</script>

</body>
</html>
