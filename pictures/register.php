<?php
include 'database.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$alert_script = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname  = $conn->real_escape_string($_POST['lastname']);
    $email     = $conn->real_escape_string($_POST['email']);
    $phone     = $conn->real_escape_string($_POST['phone']);
    $password  = $conn->real_escape_string($_POST['password']);
    $confirm   = $conn->real_escape_string($_POST['confirm_password']);

    if ($password !== $confirm) {
        $alert_script = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'Passwords do not match.',
                confirmButtonColor: '#6d2e3a'
            });
        </script>";
    } else {
        if ($conn->query("SELECT * FROM registers_tb WHERE register_fname='$firstname'")->num_rows > 0) {
            $alert_script = "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Duplicate Name',
                    text: 'This first name is already registered.',
                    confirmButtonColor: '#6d2e3a'
                });
            </script>";
            $_POST['firstname'] = "";
        }
        elseif ($conn->query("SELECT * FROM registers_tb WHERE register_email = '$email'")->num_rows > 0) {
            $alert_script = "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Email Exists',
                    text: 'Please use another email address.',
                    confirmButtonColor: '#6d2e3a'
                });
            </script>";
            $_POST['email'] = "";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql1 = "INSERT INTO registers_tb 
                (register_fname, register_lname, register_email, phone_number, register_password, created_at) 
                VALUES ('$firstname', '$lastname', '$email', '$phone', '$hashedPassword', NOW())";

            if ($conn->query($sql1) === TRUE) {

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'paulitomapagmahal9@gmail.com';
                    $mail->Password   = 'qnkr tpqm vdio kciz';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('paulitomapagmahal9@gmail.com', 'Beauty and Blessed Shop');
                    $mail->addAddress($email, $firstname . " " . $lastname);

                    $mail->isHTML(true);
                    $mail->Subject = "Welcome to Beauty and Blessed Shop!";
                    $mail->Body    = "
                        <h2>Thank you for registering, $firstname!</h2>
                        <p>Welcome to our shop!</p>
                        <p><b>Login here:</b></p>
                        <p><a href='http://localhost/beauty_blessed_project/login.php'
                              style='padding:10px 15px;background:#dc3545;color:white;text-decoration:none;border-radius:5px;'>
                              Click here to Login</a></p>";

                    $mail->send();
                } catch (Exception $e) {}

                $alert_script = "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        confirmButtonColor: '#ec7699'
                    }).then(() => {
                        window.location = 'login.php?success=1';
                    });
                </script>";

            } else {
                $alert_script = "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Database Error',
                        text: '". addslashes($conn->error) ."',
                        confirmButtonColor: '#6d2e3a'
                    });
                </script>";
            }
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




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
  background: #000;
}

/* 🌸 BACKGROUND BLUR */
.bg-blur {
  position: fixed;
  inset: 0;
  background: url("new44.jpg") center center no-repeat;
  background-size: cover;
  transform: scale(1.15);
  transform-origin: center;
  filter: blur(6px);
  z-index: -1;
}

/* 🌸 CENTERED CARD WRAPPER */
.page-center {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 480px;   /* default desktop width */
}

/* 🌸 GLASS CARD */
.login-card {
  width: 100%;
  padding: 10px 30px;
  background: rgba(255,255,255,0.40);
  backdrop-filter: blur(12px);
  border-radius: 18px;
  color: white;
  box-shadow: 0 10px 35px rgba(0,0,0,0.25);

  /* default scale */
  transform: scale(1);
  transform-origin: center;
}

/* 🌸 TITLE */
.login-card h3 {
  text-align: center;
  font-weight: 700;
  margin-bottom: 15px;
}

/* 🌸 LABELS */
label {
  font-weight: 500;
  color: #fff;
}

/* 🌸 INPUTS */
.form-control {
  border-radius: 10px;
  border: none;
  background: white;
  font-size: 14px;
}

/* 🌸 BUTTON */
.btn-login {
  background: #6D2E3A;
  color: white;
  font-weight: bold;
  border-radius: 10px;
  margin-top: 10px;
}

.btn-login:hover {
  background: #6D2E3A;
}

/* 🌸 PASSWORD REQUIREMENT */
.requirement {
  font-size: 13px;
  margin-top: 4px;
}

.valid { color: #8dff9b; }
.invalid { color: #000000ff; }

/* ----------------------------------------- */
/*          ⭐ WIDTH BREAKPOINTS             */
/* ----------------------------------------- */

/* ⭐ Large desktops */
@media (min-width: 1600px) {
  .page-center { width: 520px; }
}

/* ⭐ Standard desktop */
@media (max-width: 1280px) and (min-width: 1025px) {
  .page-center { width: 420px; }
}

/* ⭐ Tablets landscape & portrait */
@media (max-width: 1024px) and (min-width: 821px) {
  .page-center { width: 400px; }
}

/* ⭐ Small tablets (iPad Mini, Galaxy Tab Small) */
@media (max-width: 820px) and (min-width: 600px) {
  .page-center { width: 380px; }
}

/* ⭐ Large phones */
@media (max-width: 600px) {
  .page-center { width: 90%; }
}

/* ⭐ Small phones */
@media (max-width: 480px) {
  html, body { overflow: auto !important; } /* enable scroll */
  
  .page-center {
    position: relative !important;
    top: auto; left: auto;
    transform: none !important;
    margin: 30px auto;
    width: 92%;
  }

  .login-card {
    padding: 20px 20px;
  }

  label, .form-control, .btn-login {
    font-size: 13px !important;
  }
}

/* ----------------------------------------- */
/*          ⭐ SCALE BREAKPOINTS             */
/* ----------------------------------------- */

/* tablets */
@media (min-width: 768px) {
  .login-card { transform: scale(1.07); }
}

/* iPad Pro / big tablets */
@media (min-width: 1024px) {
  .login-card { transform: scale(1.70); }
}

/* standard desktops */
@media (min-width: 1280px) {
  .login-card { transform: scale(1.17); }
}

/* large desktops */
@media (min-width: 1600px) {
  .login-card { transform: scale(1.20); }
}

/* SHORT SCREENS = REMOVE ZOOM */
@media (max-height: 700px) {
  .login-card { transform: scale(1) !important; }
}

/* VERY short screens */
@media (max-height: 650px) {
  .login-card { transform: scale(0.92) !important; }
}

</style>


</head>

<body>

<div class="bg-blur"></div>

<div class="page-center">
  <div class="login-card">

      <h3>Register</h3>

      <form id="registerForm" method="POST" action="">

        <div class="mb-2">
          <label>First Name</label>
          <input type="text" name="firstname" class="form-control"  placeholder="Enter your Firstname"
            value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>" required>
        </div>

        <div class="mb-2">
          <label>Last Name</label>
          <input type="text" name="lastname" class="form-control" placeholder="Enter your Lastname"
            value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>" required>
        </div>

        <div class="mb-2">
          <label>Email</label>
          <input type="email" name="email" class="form-control"  placeholder="Enter your Email"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
        </div>

        <div class="mb-2">
          <label>Phone</label>
          <input type="text" name="phone" class="form-control"  placeholder="Enter your Phone"
            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
        </div>

        <div class="mb-2">
          <label>Password</label>
          <div class="input-group">
            <input type="password" id="password" name="password"  placeholder="Enter your Password"class="form-control" required>
            <button type="button" onclick="togglePassword('password', this)" class="btn btn-outline-light">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <div id="passwordRequirement" class="requirement invalid">
            <i class="bi bi-info-circle"></i> Must be 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 symbol.
          </div>
        </div>

        <div class="mb-2">
          <label>Confirm Password</label>
          <div class="input-group">
            <input type="password" id="confirm_password" name="confirm_password"  placeholder="Confirm Password" class="form-control" required>
            <button type="button" onclick="togglePassword('confirm_password', this)" class="btn btn-outline-light">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-check my-2">
          <input class="form-check-input" type="checkbox" id="privacyCheck" required>
          <label class="form-check-label">
            I agree to the <a href="privacy.php" target="_blank" class="fw-bold text-white">Privacy Policy</a>
          </label>
        </div>

        <button type="button" id="registerBtn" class="btn-login w-100 py-2 mt-3" disabled>
          Register
        </button>

      </form>

      <div class="text-center mt-3">
        <a href="login.php" class="fw-bold text-white">Back to Login</a>
      </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS VALIDATION + TOGGLE (unchanged) -->
<script>

// Password toggle
function togglePassword(id, btn) {
  const input = document.getElementById(id);
  const icon = btn.querySelector("i");

  input.type = input.type === "password" ? "text" : "password";
  icon.classList.toggle("bi-eye");
  icon.classList.toggle("bi-eye-slash");
}

// Password strength
const password = document.getElementById("password");
const confirmPass = document.getElementById("confirm_password");
const passReq = document.getElementById("passwordRequirement");
const registerBtn = document.getElementById("registerBtn");
const privacyCheck = document.getElementById("privacyCheck");

function validatePass() {
  const val = password.value;
  const req = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

  if (req.test(val)) {
    passReq.classList.remove("invalid");
    passReq.classList.add("valid");
  } else {
    passReq.classList.remove("valid");
    passReq.classList.add("invalid");
  }

  registerBtn.disabled = !(req.test(val) && password.value === confirmPass.value && privacyCheck.checked);
}

password.addEventListener("input", validatePass);
confirmPass.addEventListener("input", validatePass);
privacyCheck.addEventListener("change", validatePass);

// SweetAlert submit
const registerForm = document.getElementById("registerForm");

registerBtn.addEventListener("click", () => {
  Swal.fire({
    icon: 'question',
    title: 'Confirm Registration?',
    showCancelButton: true,
    confirmButtonColor: '#ec7699',
    cancelButtonColor: '#777',
    confirmButtonText: 'Yes, Register'
  }).then((r) => {
    if (r.isConfirmed) registerForm.submit();
  });
});
</script>

<?= $alert_script ?>

</body>
</html>
