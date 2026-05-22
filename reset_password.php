<?php
session_start();
include 'database.php';

$swal = ""; // SweetAlert2 script holder

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reset_code     = $conn->real_escape_string($_POST['reset_code']);
    $new_password   = $conn->real_escape_string($_POST['new_password']);
    $confirm_pass   = $conn->real_escape_string($_POST['confirm_password']);

    if ($new_password !== $confirm_pass) {
        $swal = "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
            icon: 'error',
            title: 'Passwords do not match!',
            confirmButtonColor: '#6D2E3A',
            confirmButtonText: 'OK',
            width: 350,
            customClass: { popup: 'my-swal-size' }
          });
        });
        </script>";
    } else {
        $sql = "SELECT * FROM forgot_password_tb WHERE code='$reset_code' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];
            $expires_at = $row['expires_at'];

            if (strtotime($expires_at) < time()) {
                $swal = "
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                    icon: 'warning',
                    title: 'Reset Code Expired',
                    text: 'Please request a new password reset.',
                    confirmButtonColor: '#6D2E3A',
                    confirmButtonText: 'OK',
                    width: 350,
                    customClass: { popup: 'my-swal-size' }
                  });
                });
                </script>";
            } else {
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

                $conn->query("UPDATE registers_tb SET register_password='$hashedPassword' WHERE register_email='$email'");
                $conn->query("DELETE FROM forgot_password_tb WHERE code='$reset_code'");

                $swal = "
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                    icon: 'success',
                    title: 'Password Reset Successful!',
                    text: 'Redirecting to login page...',
                    confirmButtonColor: '#6D2E3A',
                    confirmButtonText: 'OK',
                    width: 350,
                    customClass: { popup: 'my-swal-size' }
                  }).then(() => {
                    window.location = 'login.php';
                  });
                });
                </script>";
            }
        } else {
            $swal = "
            <script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'error',
                title: 'Invalid Reset Code',
                text: 'Please check your reset code and try again.',
                confirmButtonColor: '#6D2E3A',
                confirmButtonText: 'OK',
                width: 350,
                customClass: { popup: 'my-swal-size' }
              });
            });
            </script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* 🌸 RESET */
    html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; }
    body { background: #000; }
    .bg-blur { position: fixed; inset: 0; background: url('new44.jpg') center center no-repeat; background-size: cover; transform: scale(1.15); transform-origin: center; filter: blur(6px); z-index: -1; }
    .page-center { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; }
    .login-card { width: 100%; padding: 35px 30px; background: rgba(255,255,255,0.40); backdrop-filter: blur(12px); border-radius: 18px; color: #fff; box-shadow: 0 10px 35px rgba(0,0,0,0.25); transform: scale(1); transform-origin: center; }
    h3 { text-align: center; font-weight: 700; margin-bottom: 20px; color: #fff; }
    label { color: #fff; font-weight: 500; }
    .form-control { border-radius: 10px; border: none; }
    .btn-login { background:#6d2e3a; color:#fff; border-radius:10px; font-weight:bold; }
    .btn-login:hover { background:#6d2e3a; }
    .alert { border-radius:10px; font-size:0.9rem; }
    .requirement { font-size:12px; margin-top:5px; }
    .valid { color: #77ff85; } .invalid { color: #000000ff; }

    @media (max-width: 768px) { .page-center { width: 90%; } }
    @media (min-width: 768px) { .login-card { transform: scale(1.07); } }
    @media (min-width: 1024px) { .login-card { transform: scale(1.75); } }
    @media (min-width: 1280px) { .login-card { transform: scale(1.17); } }
    @media (min-width: 1600px) { .login-card { transform: scale(1.22); } }
    @media (max-height: 700px) { .login-card { transform: scale(1) !important; } }
    @media (max-height: 650px) { .login-card { transform: scale(0.95) !important; } }

    /* 🌸 SWEETALERT STYLING */
    .my-swal-size { width: 350px !important; padding: 1.2rem !important; font-size: 0.9rem !important; color: #6D2E3A !important; }
    .btn-confirm { background-color: #6D2E3A !important; color: #fff !important; border: none !important; border-radius: 6px !important; padding: 8px 18px !important; font-weight: 600 !important; margin: 0 6px !important; transition: all 0.2s ease; }
    .btn-confirm:hover { background-color: #6D2E3A !important; }
    .btn-cancel { background-color: #6D2E3A !important; color: #fff !important; border: none !important; border-radius: 6px !important; padding: 8px 18px !important; font-weight: 600 !important; margin: 0 6px !important; transition: all 0.2s ease; }
    .btn-cancel:hover { background-color: #6D2E3A !important; }
    .swal2-actions { gap: 10px !important; }
    .swal2-title, .swal2-html-container { color: #6D2E3A !important; font-family: 'Poppins', sans-serif; }
    .swal2-icon.swal2-question, .swal2-icon.swal2-warning, .swal2-icon.swal2-error, .swal2-icon.swal2-success { border-color: #6D2E3A !important; color: #6D2E3A !important; }
  </style>
</head>

<body>

<div class="bg-blur"></div>

<div class="page-center">
  <div class="login-card">

      <?php if (!empty($message)) : ?>
        <div id="alertBox" class="alert alert-<?= $alertType ?> text-center">
          <?= $message ?>
        </div>
      <?php endif; ?>

      <h3>Reset Password</h3>

      <form id="resetForm" action="reset_password.php" method="POST">

        <div class="mb-3">
          <label>Reset Code</label>
          <input type="text" name="reset_code" placeholder="Enter Reset Code" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>New Password</label>
          <div class="input-group">
            <input type="password" name="new_password" id="new_password"  placeholder="Enter New Password" class="form-control" required>
            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password', this)">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <div id="passwordRequirement" class="requirement invalid">
            <i class="bi bi-info-circle"></i> Must be at least 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 symbol
          </div>
        </div>

        <div class="mb-3">
          <label>Confirm Password</label>
          <div class="input-group">
            <input type="password" name="confirm_password" id="confirm_password"  placeholder="Enter Confirm Password" class="form-control" required>
            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password', this)">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <button type="button" id="resetBtn" class="btn btn-login w-100 py-2 mt-2" disabled>
          Reset Password
        </button>

      </form>

      <div class="text-center mt-3">
        <a href="login.php" class="fw-bold text-white">Back to Login</a>
      </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // 🔹 Show/Hide password
  function togglePassword(id, el) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
    el.innerHTML = input.type === "text"
      ? '<i class="bi bi-eye-slash"></i>'
      : '<i class="bi bi-eye"></i>';
  }

  const resetBtn = document.getElementById("resetBtn");
  const password = document.getElementById("new_password");
  const confirmPass = document.getElementById("confirm_password");
  const passwordRequirement = document.getElementById("passwordRequirement");
  const resetForm = document.getElementById("resetForm");
  const resetCode = document.querySelector("input[name='reset_code']");

  // 🔹 Real-time password validation
  function validatePassword() {
    const value = password.value;
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;
    if (regex.test(value)) {
      passwordRequirement.classList.replace("invalid", "valid");
      passwordRequirement.innerHTML =
        '<i class="bi bi-check-circle text-success"></i> Password meets all requirements.';
    } else {
      passwordRequirement.classList.replace("valid", "invalid");
      passwordRequirement.innerHTML =
        '<i class="bi bi-info-circle text-danger"></i> Must have 8+ chars, uppercase, lowercase, number, symbol.';
    }
    checkInputs(); // check button enable
  }

  function checkInputs() {
    const allFilled = resetCode.value.trim() && password.value.trim() && confirmPass.value.trim();
    resetBtn.disabled = !allFilled;
  }

  password.addEventListener("input", validatePassword);
  confirmPass.addEventListener("input", validatePassword);
  resetCode.addEventListener("input", checkInputs);

  // 🔹 SweetAlert validation & confirmation
  resetBtn.addEventListener("click", function () {
    const value = password.value.trim();
    const confirmValue = confirmPass.value.trim();
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;
    const swalOptions = {
      width: 350,
      customClass: { popup: 'my-swal-size' },
      confirmButtonColor: "#6D2E3A"
    };

    if (!regex.test(value)) {
      Swal.fire({
        icon: "error",
        title: "Weak Password",
        text: "Password must have at least 8 characters, including uppercase, lowercase, number, and symbol.",
        ...swalOptions
      });
      return;
    }

    if (value !== confirmValue) {
      Swal.fire({
        icon: "error",
        title: "Passwords do not match!",
        text: "Please recheck your new password and confirmation password.",
        ...swalOptions
      });
      return;
    }

    Swal.fire({
      title: "Are you sure?",
      text: "Do you really want to reset your password?",
      icon: "question",
      showCancelButton: true,
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Yes, reset it!",
      cancelButtonText: "Cancel",
      ...swalOptions
    }).then((result) => {
      if (result.isConfirmed) resetForm.submit();
    });
  });
</script>

<?php if (!empty($swal)) echo $swal; ?>

</body>
</html>
