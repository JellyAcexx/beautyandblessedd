<?php
session_start();
include 'database.php';

// ✅ Redirect if already logged in
if (isset($_SESSION['login_id'])) {
    header("Location: customer_dashboard.php");
    exit();
}

// ✅ Handle login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // 🔍 Check if email exists in registers_tb
    $sql = "SELECT * FROM registers_tb WHERE register_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // ✅ Verify password
        if (password_verify($password, $row['register_password'])) {

            // 📝 Insert the user's register_id into login_tb
            $insert = $conn->prepare("INSERT INTO login_tb (register_id) VALUES (?)");
            $insert->bind_param("i", $row['register_id']);
            $insert->execute();

            // ✅ Get the newly inserted login_id
            $login_id = $conn->insert_id;

            // ✅ Store session info
            $_SESSION['login_id'] = $login_id;
            $_SESSION['register_id'] = $row['register_id'];
            $_SESSION['user_email'] = $row['register_email'];

         
            // ✅ Response for AJAX
            echo json_encode([
                "status" => "success",
                "script" => "localStorage.removeItem('customerDashboardFrame');"
            ]);
            exit();

        } else {
            // ❌ Wrong password → HINDI na redirect, JSON lang
            echo json_encode([
                "status"  => "error",
                "field"   => "password",          // para alam ng JS kung saan maglalagay ng red text
                "message" => "*Incorrect password."
            ]);
            exit();
        }

    } else {
        // ❌ Email does not exist → HINDI na redirect, JSON lang
        echo json_encode([
            "status"  => "error",
            "field"   => "email",               // para sa email naman ang error
            "message" => "*Invalid email."
        ]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Specimen:wght@500;600;700&display=swap" rel="stylesheet">
  

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

  filter: blur(6px);
  z-index: -1;
}

/* 🌟 CENTERED WRAPPER */
.page-center {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 400px;
}

/* 🌟 LOGIN CARD */
.login-card {
  width: 100%;
  padding: 40px 35px;
  background: rgba(255,255,255,0.40);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-radius: 18px;
  box-shadow: 0 10px 35px rgba(0,0,0,0.25);
  color: #fff;
  position: relative;
}

/* Title */
.login-card h3 {
  text-align: center;
  font-weight: 700;
  margin-bottom: 25px;
  color: #fff;
}

/* Labels */
label {
  color: #fff;
  font-weight: 500;
}

/* Inputs */
.form-control {
  border-radius: 10px;
  border: none;
  font-size: 14px;
}

/* 🌸 REMOVE BLUE FOCUS BORDER FROM INPUTS */
.form-control:focus,
.input-group-text:focus,
.btn:focus {
  outline: none !important;
  box-shadow: none !important;
}

/* Button */
.btn-login {
  background: #6d2e3a;
  color: #fff;
  font-weight: bold;
  border-radius: 10px;
  transition: 0.3s;
}

.btn-login:hover {
  background: #6d2e3a;
}

/* Alerts */
.alert {
  border-radius: 10px;
  font-size: 0.9rem;
}

/* Home link */
.home-link {
  position: absolute;
  top: 15px;
  right: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}

.home-link a {
  color: #ffffffff;
  text-decoration: none;
}

.home-link a:hover {
  text-decoration: underline;
}

/* 🌸 SMALL SCREENS */
@media (max-width: 768px) {
  .page-center {
    width: 90%;
  }
}

/* ⭐ RESPONSIVE CARD ZOOM FIX */

/* default (phones + small laptops) */
.login-card {
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

.small-swal-login {
  width: 350px !important;      /* mas maliit na lapad */
  padding: 1.2rem 1.2rem !important;
}

.small-swal-login .swal2-title {
  font-size: 1.4rem !important;
  color: #6d2e3a !important;
}

.small-swal-login .swal2-html-container {
  font-size: 1rem !important;
  color: #6d2e3a !important;
}

/* Icon color */
.small-swal-login .swal2-icon.swal2-success {
  border-color: #6d2e3a !important;
  color: #6d2e3a !important;
}

/* Inner check + ring */
.small-swal-login .swal2-success-ring {
  border-color: rgba(109, 46, 58, 0.4) !important;
}

.small-swal-login .swal2-success-line-tip,
.small-swal-login .swal2-success-line-long {
  background-color: #6d2e3a !important;
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
  .login-card {
    transform: scale(1.07);
  }
}

/* iPad Pro + big tablets */
@media (min-width: 1024px) {
  .login-card {
    transform: scale(1.75);
  }
}

/* regular desktops */
@media (min-width: 1280px) {
  .login-card {
    transform: scale(1.17);
  }
}

/* large desktops / wide monitors */
@media (min-width: 1600px) {
  .login-card {
    transform: scale(1.22);
  }
}
/* ⭐ If screen is SHORT → Huwag mag-zoom */
@media (max-height: 700px) {
  .login-card {
    transform: scale(1) !important;
  }
}

/* ⭐ If screen is VERY SHORT (600px below) → even smaller */
@media (max-height: 650px) {
  .login-card {
    transform: scale(0.95) !important;
  }
}


</style>

</head>
<body>

  <div class="bg-blur"></div>
  <div class="page-center">
      <div class="login-card">

        <div class="home-link">
          <a href="homepage.php">Home</a>
        </div>

        <h3>Login</h3>

        <form id="customerLoginForm" action="login.php" method="POST" onsubmit="return showLoginLoadingCustomer();">

          <div class="mb-3">
            <label>Email</label>
            <div class="input-group">
              <span class="input-group-text" style="background: rgba(109,46,58,0.13); border:none;">
                <i class="bi bi-envelope-fill" style="color: #6d2e3a; font-size: 15px;"></i>
              </span>
              <input type="email" class="form-control" id="email" name="email"
                    placeholder="Enter your email" required>
            </div>
            <div class="invalid-feedback d-block" id="emailError" style="display:none;"></div>
          </div>

          <div class="mb-3">
            <label>Password</label>
            <div class="input-group">
              <span class="input-group-text" style="background: rgba(109,46,58,0.13); border:none;">
                <i class="bi bi-lock-fill" style="color: #6d2e3a; font-size: 15px;"></i>
              </span>
              <input type="password" id="cust_password" class="form-control" name="password"
                    placeholder="Enter your password" required>
              <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordCust(this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div class="invalid-feedback d-block" id="passwordError" style="display:none;"></div>
          </div>

          <button type="submit" class="btn btn-login w-100 py-2 mt-2">Login</button>

        </form>

        <div class="text-end mt-3">
          <a href="forgot_password.php" class="text-white">Forgot Password?</a>
        </div>

        <div class="text-center mt-3">
          <p class="mb-0 text-white">Don't have an account?
            <a href="register.php" class="fw-bold text-white">Register here</a>
          </p>
        </div>
    </div>
  </div>

  <!-- Loading Modal -->
  <div class="modal fade" id="loadingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered loading-modal-center" style="max-width:210px;">
      <div class="modal-content" style="background:#fff5f9;border-radius:15px;box-shadow:0 3px 13px #f8d7dc57;">
        <div class="modal-body text-center p-2" style="font-size:0.97em; color:#6D2E3A;">
          <div class="spinner-border" style="color:#E8A9B2;width:1.65em;height:1.65em;" role="status"></div>
          <div style="font-weight:600; font-size:0.97em; margin-top:6px;">Logging In...</div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- ✅ Auto-hide alert after 2s -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const alertBox = document.getElementById("alertBox");
      if (alertBox) {
        setTimeout(() => {
          let alert = new bootstrap.Alert(alertBox);
          alert.close();
          window.history.replaceState(null, "", window.location.pathname);
        }, 2000);
      }
    });

    function togglePasswordCust(button) {
      const input = document.getElementById("cust_password");
      const icon = button.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("bi-eye", "bi-eye-slash");
      } else {
        input.type = "password";
        icon.classList.replace("bi-eye-slash", "bi-eye");
      }
    }

    function clearErrors() {
      const emailInput = document.getElementById('email');
      const passwordInput = document.getElementById('cust_password');
      const emailError = document.getElementById('emailError');
      const passwordError = document.getElementById('passwordError');

      emailInput.classList.remove('is-invalid');
      passwordInput.classList.remove('is-invalid');

      emailError.style.display = 'none';
      passwordError.style.display = 'none';
      emailError.textContent = '';
      passwordError.textContent = '';
    }

    function showLoginLoadingCustomer() {
      clearErrors();

      const form = document.getElementById('customerLoginForm');
      const formData = new FormData(form);

      fetch('login.php', {
        method: 'POST',
        body: formData,
      })
      .then(res => res.json())
      .then(response => {
        if (response.status === "success") {
          // 👉 SUCCESS: dito lang tayo gagamit ng loading modal
          const loadingModalEl = document.getElementById('loadingModal');
          const loadingModal = new bootstrap.Modal(loadingModalEl, {
            backdrop: 'static',
            keyboard: false
          });

          loadingModal.show();

          // konting delay para makita yung loading
          setTimeout(() => {
            loadingModal.hide();

            Swal.fire({
              icon: 'success',
              title: 'Successful Login',
              text: 'Welcome, customer!',
              showConfirmButton: false,
              timer: 1700,
              customClass: {
                popup: 'small-swal-login'
              }
            });

            setTimeout(() => {
              if (response.script) {
                eval(response.script);
              }
              window.location.href = 'customer_dashboard.php';
            }, 1700);
          }, 800); // adjust kung gusto mong mas mahaba/maiksi

        } else if (response.status === "error") {
          // ❌ ERROR: walang kahit anong modal, UI errors lang
          const emailInput = document.getElementById('email');
          const passwordInput = document.getElementById('cust_password');
          const emailError = document.getElementById('emailError');
          const passwordError = document.getElementById('passwordError');

          // reset password field on fail
          passwordInput.value = '';

          if (response.field === "email") {
            emailInput.classList.add('is-invalid');
            passwordInput.classList.add('is-invalid');
            emailError.textContent = response.message || "*Invalid email.";
            emailError.style.display = 'block';
            passwordError.textContent = "*Please check your email above.";
            passwordError.style.display = 'block';
          } else if (response.field === "password") {
            passwordInput.classList.add('is-invalid');
            passwordError.textContent = response.message || "*Incorrect password.";
            passwordError.style.display = 'block';
          }
        }
      })
      .catch(err => {
        // network error lang ang may modal
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Something went wrong. Please try again.'
        });
      });

      return false; // huwag mag-normal submit
    }
  </script>

</body>
</html>
