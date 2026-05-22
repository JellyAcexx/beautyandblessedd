<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include 'database.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);

    $sql = "SELECT * FROM admin_login_tb WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $oldPassword = $row['password'];

        $code = rand(1000, 9999);
        $date_time = date("Y-m-d H:i:s");

        $conn->query("INSERT INTO admin_forgot_tb (email, code, password, date_time) 
                      VALUES ('$email', '$code', '$oldPassword', '$date_time')");

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "paulitomapagmahal9@gmail.com";
            $mail->Password = "qnkr tpqm vdio kciz"; 
            $mail->SMTPSecure = "ssl";
            $mail->Port = 465;

            $mail->setFrom("paulitomapagmahal9@gmail.com", "Admin Support");
            $mail->addAddress($email);

            $resetLink = "https://beautyandblessed.online/admin_resetpass.php?email=$email&code=$code";
            

            $mail->isHTML(true);
            $mail->Subject = "Admin Password Reset";
            $mail->Body = "
                <h3>Password Reset Request</h3>
                <p><b>Email:</b> $email</p>
                <p><b>Code:</b> $code</p>
                <p>Click the link below to reset your password:</p>
                <a href='$resetLink'>$resetLink</a>
            ";

            $mail->send();
            $success = "A reset code has been sent to your email.";
        } catch (Exception $e) {
            $error = "Email could not be sent. Please try again.";
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
  <title>Admin Forgot Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Specimen:wght@500;600;700&display=swap" rel="stylesheet">
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
.forgot-card {
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
  .forgot-card {
    transform: scale(1.07);
  }
}

/* iPad Pro + big tablets */
@media (min-width: 1024px) {
  .forgot-card {
    transform: scale(1.75);
  }
}

/* desktops */
@media (min-width: 1280px) {
  .forgot-card {
    transform: scale(1.17);
  }
}

/* wide desktops */
@media (min-width: 1600px) {
  .forgot-card {
    transform: scale(1.22);
  }
}

/* short screens fix */
@media (max-height: 700px) {
  .forgot-card {
    transform: scale(1) !important;
  }
}

@media (max-height: 650px) {
  .forgot-card {
    transform: scale(0.95) !important;
  }
}

/* 🌸 SWEETALERT STYLING */
.small-swal {
  width: 320px !important;
  padding: 1.2rem !important;
  font-size: 0.9rem !important;
  color: #ec7699 !important;
}

/* 💖 Confirm Button */
.btn-confirm {
  background-color: #ec407a !important;
  color: #fff !important;
  border: none !important;
  border-radius: 6px !important;
  padding: 8px 18px !important;
  font-weight: 600 !important;
  margin: 0 6px !important;
  transition: all 0.2s ease;
}
.btn-confirm:hover {
  background-color: #d81b60 !important;
}

/* 💗 Cancel Button */
.btn-cancel {
  background-color: #f48fb1 !important;
  color: #fff !important;
  border: none !important;
  border-radius: 6px !important;
  padding: 8px 18px !important;
  font-weight: 600 !important;
  margin: 0 6px !important;
  transition: all 0.2s ease;
}
.btn-cancel:hover {
  background-color: #ec407a !important;
}

/* 💕 Space between buttons */
.swal2-actions {
  gap: 10px !important;
}

.swal2-title,
.swal2-html-container {
  color: #ec407a !important;
  font-family: 'Poppins', sans-serif;
}

/* Pink Question Icon */
.swal2-icon.swal2-question,
.swal2-icon.swal2-warning,
.swal2-icon.swal2-error,
.swal2-icon.swal2-success {
  border-color: #ec407a !important;
  color: #ec407a !important;
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
      <div class="forgot-card">
        <h3>Forgot Password</h3>
         <p class="text-center mb-3">Enter your email to receive reset instructions</p>

        <form action="" method="POST">
          <div class="mb-3">
            <label>Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
          </div>
          <button type="submit" class="btn btn-login w-100 mt-2 py-2">Send Reset Link</button>
        </form>

        <div class="text-center mt-3">
          <a href="log_admin.php" class="fw-bold text-white">Back to Login</a>
        </div>
      </div>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
      Swal.fire({
        title: 'Sending email...',
        text: 'Please wait while we process your request.',
        customClass: { popup: 'my-swal-size' },
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });

      setTimeout(() => {
        <?php if (!empty($success)): ?>
          Swal.fire({
            icon: 'success',
            title: 'Email Sent!',
            text: '<?= $success ?>',
            confirmButtonText: 'OK',
            customClass: { popup: 'my-swal-size' },
            confirmButtonColor: '#ec7699'
          }).then(() => { window.location.href = 'log_admin.php'; });
        <?php elseif (!empty($error)): ?>
          Swal.fire({
            icon: 'error',
            title: 'Error',
            customClass: { popup: 'my-swal-size' } ,
            text: '<?= $error ?>',
            confirmButtonColor: '#ec7699'
          });
        <?php endif; ?>
      }, 1500);
    <?php endif; ?>
  });
  </script>
</body>
</html>
