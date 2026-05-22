<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include 'database.php';

$swal = ""; // SweetAlert holder

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);

    $result = $conn->query("SELECT register_id, register_fname, register_lname FROM registers_tb WHERE register_email='$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['register_id'];
        $name = $user['register_fname'] . " " . $user['register_lname'];

        $token = bin2hex(random_bytes(32));
        $code = rand(1000, 9999);
        $expires_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        $conn->query("INSERT INTO forgot_password_tb (user_id, email, token, code, expires_at) 
                      VALUES ('$user_id', '$email', '$token', '$code', '$expires_at')");

         // Reset link
        $resetLink = "https://beautyandblessed.online/reset_password.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "paulitomapagmahal9@gmail.com"; 
            $mail->Password = "qnkr tpqm vdio kciz"; 
            $mail->SMTPSecure = "tls";
            $mail->Port = 587;

            $mail->setFrom("paulitomapagmahal9@gmail.com", "Beauty and Blessed Support");
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $mail->Body = "
                <h2>Hello $name,</h2>
                <p>We received a request to reset your password.</p>
                <p><b>Your reset code:</b> $code</p>
                <p>Or click here to reset: <a href='$resetLink'>$resetLink</a></p>
                <br>
                <p>If this wasn’t you, please ignore this email or change your password immediately.</p>
            ";

            $mail->send();

            // Success SweetAlert
            $swal = "
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Email Sent!',
                text: 'Reset instructions have been sent to your email.',
                confirmButtonColor: '#6D2E3A',
                confirmButtonText: 'OK',
                customClass: { popup: 'my-swal-size' }
            });
            </script>";
        } catch (Exception $e) {
            $swal = "
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Mailer Error',
                text: '".addslashes($mail->ErrorInfo)."',
                confirmButtonColor: '#6D2E3A',
                confirmButtonText: 'OK',
                customClass: { popup: 'my-swal-size' }
            });
            </script>";
        }
    } else {
        $swal = "
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Email Not Found',
            text: 'No account found with that email address.',
            confirmButtonColor: '#6D2E3A',
            confirmButtonText: 'OK',
            customClass: { popup: 'my-swal-size' }
        });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- SweetAlert2 -->
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
  background: #000; /* fallback */
}

/* 🌸 ALWAYS-FULL BLUR LAYER */
.bg-blur {
  position: fixed;
  inset: 0;

  background: url("new44.jpg") center center no-repeat;
  background-size: cover;

  /* keeps BG zoomed so card doesn't look far */
  transform: scale(1.15);
  transform-origin: center;

  filter: blur(6px);
  z-index: -1;
}

/* 🌸 CENTER WRAPPER */
.page-center {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 400px;
}

/* 🌸 FORGOT CARD */
.login-card {
  width: 100%;
  padding: 40px 35px;
  background: rgba(255,255,255,0.40);
  backdrop-filter: blur(12px);
  border-radius: 18px;
  color: #fff;
  box-shadow: 0 10px 35px rgba(0,0,0,0.25);

  /* default scale */
  transform: scale(1);
  transform-origin: center;
}

/* TITLE */
h3 {
  text-align: center;
  font-weight: 700;
  margin-bottom: 20px;
  color: #fff;
}

/* TEXTS */
p { color:#fff; }

/* LABELS */
label {
  color: #fff;
  font-weight: 500;
}

/* INPUTS */
.form-control {
  border-radius: 10px;
  border: none;
}

/* BUTTON */
.btn-login {
  background: #6d2e3a;
  color: #fff;
  font-weight: bold;
  border-radius: 10px;
}

.btn-login:hover {
  background: #6d2e3a;
}

/* ALERTS */
.alert {
  border-radius: 10px;
  font-size: 0.9rem;
}

/* 🌸 SMALL SCREENS */
@media (max-width: 768px) {
  .page-center {
    width: 90%;
  }
}

/* ⭐ RESPONSIVE CARD ZOOM FIX (MATCHING LOGIN) */

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

/* desktops */
@media (min-width: 1280px) {
  .login-card {
    transform: scale(1.17);
  }
}

/* wide desktops */
@media (min-width: 1600px) {
  .login-card {
    transform: scale(1.22);
  }
}

/* short screens fix */
@media (max-height: 700px) {
  .login-card {
    transform: scale(1) !important;
  }
}

@media (max-height: 650px) {
  .login-card {
    transform: scale(0.95) !important;
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
    <div class="login-card">
        <h3>Forgot Password</h3>
        <p class="text-center mb-3">Enter your email to receive reset instructions</p>

        <form method="POST" action="">
            <div class="mb-3">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-login w-100 py-2">Send Reset Link</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php" class="fw-bold text-white">Back to Login</a>
        </div>
    </div>
</div>

<?php if (!empty($swal)) echo $swal; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
