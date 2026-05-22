<?php
include 'database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Message placeholders
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $code = $conn->real_escape_string($_POST['code']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        $sql = "SELECT * FROM admin_forgot_tb WHERE email='$email' AND code='$code' ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $date_time = date("Y-m-d H:i:s");

            $updateSql = "UPDATE admin_login_tb SET password='$hashedPassword' WHERE email='$email'";
            if ($conn->query($updateSql) === TRUE) {
                $updateForgot = "UPDATE admin_forgot_tb 
                                 SET password='$hashedPassword', date_time='$date_time' 
                                 WHERE email='$email' AND code='$code'";
                $conn->query($updateForgot);

                $success = "Password successfully updated! Please login.";
            } else {
                $error = "Error updating password. Please try again.";
            }
        } else {
            $error = "Invalid reset code or email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
/* 🌸 RESET */
    html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; }
    body { background: #000; }
    .bg-blur { position: fixed; inset: 0; background: url('new44.jpg') center center no-repeat; background-size: cover; transform: scale(1.15); transform-origin: center; filter: blur(6px); z-index: -1; }
    .page-center { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; }
    .reset-card { width: 100%; padding: 35px 30px; background: rgba(255,255,255,0.40); backdrop-filter: blur(12px); border-radius: 18px; color: #fff; box-shadow: 0 10px 35px rgba(0,0,0,0.25); transform: scale(1); transform-origin: center; }
    h3 { text-align: center; font-weight: 700; margin-bottom: 20px; color: #fff; }
    label { color: #fff; font-weight: 500; }
    .form-control { border-radius: 10px; border: none; }
    .btn-login { background:#6d2e3a; color:#fff; border-radius:10px; font-weight:bold; }
    .btn-login:hover { background:#6d2e3a; }
    .alert { border-radius:10px; font-size:0.9rem; }
    .requirement { font-size:12px; margin-top:5px; }
    .valid { color: #77ff85; } .invalid { color: #000000ff; }

    @media (max-width: 768px) { .page-center { width: 90%; } }
    @media (min-width: 768px) { .reset-card { transform: scale(1.07); } }
    @media (min-width: 1024px) { .reset-card { transform: scale(1.75); } }
    @media (min-width: 1280px) { .reset-card { transform: scale(1.17); } }
    @media (min-width: 1600px) { .reset-card { transform: scale(1.22); } }
    @media (max-height: 700px) { .reset-card { transform: scale(1) !important; } }
    @media (max-height: 650px) { .reset-card { transform: scale(0.95) !important; } }

    /* 🌸 SWEETALERT STYLING */
    .my-swal-size { width: 350px !important; padding: 1.2rem !important; font-size: 0.9rem !important; color: #ec7699 !important; }
    .btn-confirm { background-color: #ec407a !important; color: #fff !important; border: none !important; border-radius: 6px !important; padding: 8px 18px !important; font-weight: 600 !important; margin: 0 6px !important; transition: all 0.2s ease; }
    .btn-confirm:hover { background-color: #d81b60 !important; }
    .btn-cancel { background-color: #f48fb1 !important; color: #fff !important; border: none !important; border-radius: 6px !important; padding: 8px 18px !important; font-weight: 600 !important; margin: 0 6px !important; transition: all 0.2s ease; }
    .btn-cancel:hover { background-color: #ec407a !important; }
    .swal2-actions { gap: 10px !important; }
    .swal2-title, .swal2-html-container { color: #ec407a !important; font-family: 'Poppins', sans-serif; }
    .swal2-icon.swal2-question, .swal2-icon.swal2-warning, .swal2-icon.swal2-error, .swal2-icon.swal2-success { border-color: #ec407a !important; color: #ec407a !important; }

  .form-control {
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 0.5rem;
    color: #333;
    font-size: 14px;
  }

  .btn-danger {
    background: #ec7699;
    border: none;
    color: #fff;
    font-weight: bold;
    transition: all 0.3s ease;
  }

  .btn-danger:hover {
    background: #ffe3ed;
    color: #ec7699;
    transform: translateY(-2px);
  }

  .requirement {
    font-size: 12px;
    margin-top: 5px;
    color: #fff;
  }

  .valid { color: #00ff6a; }
  .invalid { color: #ffd6d6; }

  a { color: #fff; text-decoration: none; font-size: 14px; }
  a:hover { text-decoration: underline; }

  /* 📱 Mobile View */
  @media (max-width: 768px) {
    body {
      overflow-y: auto; /* ✅ scrollable on mobile only */
    }
    .reset-container {
      flex-direction: column;
      height: auto;
      overflow-y: visible; /* ensure internal elements don't cut off */
    }
    .reset-left {
      height: 180px;
      padding: 1rem;
    }
    .welcome-text, .center-brand {
      display: none;
    }
    .top-logo {
      position: relative;
      top: auto;
      left: auto;
    }
    .top-logo img {
      width: 90px;
      height: 90px;
    }
    .reset-right {
      padding: 2rem 1.5rem;
    }
  }

/* 🌸 SWEETALERT STYLING */
.small-swal {
  width: 320px !important;
  padding: 1.2rem !important;
  font-size: 0.9rem !important;
  color: #6D2E3A !important;
}

/* 💖 Confirm Button */
.btn-confirm {
  background-color: #6D2E3A !important;
  color: #fff !important;
  border: none !important;
  border-radius: 6px !important;
  padding: 8px 18px !important;
  font-weight: 600 !important;
  margin: 0 6px !important;
  transition: all 0.2s ease;
}
.btn-confirm:hover {
  background-color: #6D2E3A !important;
}

/* 💗 Cancel Button */
.btn-cancel {
  background-color: #6D2E3A !important;
  color: #fff !important;
  border: none !important;
  border-radius: 6px !important;
  padding: 8px 18px !important;
  font-weight: 600 !important;
  margin: 0 6px !important;
  transition: all 0.2s ease;
}
.btn-cancel:hover {
  background-color: #6D2E3A !important;
}

/* 💕 Space between buttons */
.swal2-actions {
  gap: 10px !important;
}

.swal2-title,
.swal2-html-container {
  color: #6D2E3A !important;
  font-family: 'Poppins', sans-serif;
}

/* Pink Question Icon */
.swal2-icon.swal2-question,
.swal2-icon.swal2-warning,
.swal2-icon.swal2-error,
.swal2-icon.swal2-success {
  border-color: #6D2E3A !important;
  color: #6D2E3A !important;
}

/* ✅ ADDED FOR SWEETALERT RESIZE */
.my-swal-size {
    width: 350px !important;
    padding: 1.2rem !important;
}
  </style>
</head>

<body>
  <div class="bg-blur"></div>

    <div class="page-center">
      <div class="reset-card">

        <h3>Reset Password</h3>

        <form id="resetForm" method="POST" action="admin_resetpass.php">
          
          <div class="mb-3">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter email"
                   value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" required>
          </div>

          <div class="mb-3">
            <label for="code">Reset Code</label>
            <input type="text" id="code" name="code" class="form-control" placeholder="Enter code"
                   value="<?php echo htmlspecialchars($_GET['code'] ?? ''); ?>" required>
          </div>

          <div class="mb-3">
            <label for="new_password">New Password</label>
            <div class="input-group">
              <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password" required>
              <button type="button" class="btn btn-outline-light" onclick="togglePassword('new_password', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <small id="passwordHelp" class="text-danger">
              <i class="bi bi-info-circle"></i> Password must contain 8+ chars, 1 uppercase, 1 lowercase, 1 number, 1 symbol.
            </small>
          </div>

          <div class="mb-3">
            <label for="confirm_password">Confirm Password</label>
            <div class="input-group">
              <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Enter new password" required>
              <button type="button" class="btn btn-outline-light" onclick="togglePassword('confirm_password', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <button type="button" class="btn btn-danger w-100 py-2 mt-2" id="resetBtn">Reset Password</button>
        </form>
      </div>
    </div>

  <script>
    function togglePassword(id, el) {
      const input = document.getElementById(id);
      const icon = el.querySelector("i");
      if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("bi-eye", "bi-eye-slash");
      } else {
        input.type = "password";
        icon.classList.replace("bi-eye-slash", "bi-eye");
      }
    }

    // Password strength live validation
    document.getElementById("new_password").addEventListener("input", function() {
      const password = this.value;
      const helpText = document.getElementById("passwordHelp");
      const rules = [/.{8,}/, /[A-Z]/, /[a-z]/, /[0-9]/, /[^A-Za-z0-9]/];
      const allPassed = rules.every(r => r.test(password));
      if (allPassed) {
        helpText.classList.remove("text-danger");
        helpText.classList.add("text-success");
        helpText.innerHTML = "<i class='bi bi-check-circle'></i> Strong password";
      } else {
        helpText.classList.remove("text-success");
        helpText.classList.add("text-danger");
        helpText.innerHTML = "<i class='bi bi-info-circle'></i> Password must contain 8+ chars, 1 uppercase, 1 lowercase, 1 number, 1 symbol.";
      }
    });

    // Confirm submission with SweetAlert
    document.getElementById("resetBtn").addEventListener("click", function() {
      const pass = document.getElementById("new_password").value.trim();
      const confirm = document.getElementById("confirm_password").value.trim();

      if (!pass || !confirm) {
        Swal.fire({
          title: 'Missing Fields!',
          text: 'Please fill in both password fields.',
          icon: 'warning',
          customClass: { popup: 'my-swal-size' } ,
          confirmButtonColor: '#6D2E3A'
        });
        return;
      }

      if (pass !== confirm) {
        Swal.fire({
          title: 'Passwords Do Not Match!',
          icon: 'error',
          customClass: { popup: 'my-swal-size' } ,
          confirmButtonColor: '#6D2E3A'
        });
        return;
      }

      Swal.fire({
        title: 'Confirm Reset?',
        text: 'Do you really want to reset your password?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6D2E3A',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, reset it!',
        customClass: { popup: 'my-swal-size' } 
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById("resetForm").submit();
        }
      });
    });
  </script>

  <?php if (!empty($success)): ?>
  <script>
    Swal.fire({
      title: 'Password Updated!',
      text: 'Please wait...',
      icon: 'success',
      customClass: { popup: 'my-swal-size' },
      showConfirmButton: false,
      allowOutsideClick: false,
      timer: 1500,
      didClose: () => {
        Swal.fire({
          title: 'Redirecting into login...',
          icon: 'info',
          customClass: { popup: 'my-swal-size' },
          showConfirmButton: false,
          allowOutsideClick: false,
          timer: 2000,
          didClose: () => {
            window.location.href = 'log_admin.php';
          }
        });
      }
    });
  </script>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
  <script>
    Swal.fire({
      title: 'Error!',
      text: '<?= $error ?>',
      icon: 'error',
      customClass: { popup: 'my-swal-size' },
      confirmButtonColor: '#6D2E3A'
    });
  </script>
  <?php endif; ?>
</body>
</html>
