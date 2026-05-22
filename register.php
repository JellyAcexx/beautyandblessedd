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

    // Password check
    if ($password !== $confirm) {
        $alert_script = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'Passwords do not match. Please try again.',
                customClass: { popup: 'my-swal-size' },
                confirmButtonColor: '#6d2e3a'
            });
        </script>";
    } else {
        // Duplicate name check
        $checkName = $conn->query("SELECT * FROM registers_tb WHERE register_fname='$firstname'");
        if ($checkName && $checkName->num_rows > 0) {
            $alert_script = "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Duplicate Name',
                    text: 'This first name is already registered. Please use a different name.',
                    customClass: { popup: 'my-swal-size' },
                    confirmButtonColor: '#6d2e3a'
                });
            </script>";
            $_POST['firstname'] = "";
        }
        // Duplicate email check
        else {
        $checkEmail = $conn->query("SELECT * FROM registers_tb WHERE register_email = '$email'");
        if ($checkEmail && $checkEmail->num_rows > 0) {
            $alert_script = "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Email Already Exists',
                    text: 'Please use another email address.',
                    customClass: { popup: 'my-swal-size' },
                    confirmButtonColor: '#6d2e3a'
                });
            </script>";
            $_POST['email'] = "";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $sql1 = "INSERT INTO registers_tb (register_fname, register_lname, register_email, phone_number, register_password, created_at)
                     VALUES ('$firstname', '$lastname', '$email', '$phone', '$hashedPassword', NOW())";

            $registrationResult = $conn->query($sql1);

            if ($registrationResult !== false) {

                // Kunin yung ID ng bagong registered customer
                $register_id = $conn->insert_id; // last inserted auto-increment id [web:1][web:2]

                // Get admin details
                $admin_email = "detorresjanellemae@gmail.com";
                $admin_res = $conn->query("SELECT register_id FROM registers_tb WHERE register_email='$admin_email'");
                $admin_id = 1; // fallback
                if ($admin_res && $admin_res->num_rows > 0) {
                    $admin_row = $admin_res->fetch_assoc();
                    $admin_id = $admin_row['register_id'];
                }

                // 1. Insert admin notification for new user
                $notif_sql = "INSERT INTO notifadmin (register_id, notif_message, notif_type, notif_link) VALUES (?, ?, ?, ?)";
                $notif_stmt = $conn->prepare($notif_sql);
                $notif_message_admin = "New user registered: $firstname $lastname, Email: $email";
                $notif_type_admin = 'new_user';
                $notif_link_admin = '';
                if ($notif_stmt) {
                    $notif_stmt->bind_param("isss", $admin_id, $notif_message_admin, $notif_type_admin, $notif_link_admin);
                    $notif_stmt->execute();
                    $notif_stmt->close();
                }

                // 2. Send email to admin about new user registration
                $mailAdmin = new PHPMailer(true);
                try {
                    $mailAdmin->isSMTP();
                    $mailAdmin->Host       = 'smtp.gmail.com';
                    $mailAdmin->SMTPAuth   = true;
                    $mailAdmin->Username   = 'paulitomapagmahal9@gmail.com';
                    $mailAdmin->Password   = 'qnkr tpqm vdio kciz';
                    $mailAdmin->SMTPSecure = 'tls';
                    $mailAdmin->Port       = 587; // common Gmail TLS port [web:10][web:13]
                    $mailAdmin->setFrom('paulitomapagmahal9@gmail.com', 'Beauty and Blessed Shop System');
                    $mailAdmin->addAddress($admin_email, "Admin");
                    $mailAdmin->isHTML(true);
                    $mailAdmin->Subject = "New User Registration Notification";
                    $mailAdmin->Body    = "
                        <h2>New User Registered</h2>
                        <p>Name: <b>$firstname $lastname</b></p>
                        <p>Email: $email<br>Phone: $phone</p>";
                    $mailAdmin->send();
                } catch (Exception $e) {
                    // Optional: log error
                }

                // 2.1 Insert welcome notification to notifcustomer
                $sqlNotif = "INSERT INTO notifcustomer (register_id, notif_message, notif_type, notif_link, created_at, is_read)
                             VALUES (?, ?, ?, ?, NOW(), 0)";
                $stmtNotif = $conn->prepare($sqlNotif);
                $notif_message = "Welcome to Beauty and Blessed! Thanks for registering, $firstname.";
                $notif_type = "new_user";
                $notif_link = "homepage.php";
                // Types: i = int, s = string; created_at and is_read fixed na sa query [web:6][web:12][web:19]
                if ($stmtNotif) {
                    $stmtNotif->bind_param("isss", $register_id, $notif_message, $notif_type, $notif_link);
                    $stmtNotif->execute();
                    $stmtNotif->close();
                }

                // 3. Send welcome email to user
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
                        <p>We’re happy to have you in our shop.</p>
                        <p><b>Here is the link to sign in:</b></p>
                        <p><a href='https://beautyandblessed.online/login.php'
                              style='padding:10px 15px; background:#dc3545; color:#fff; text-decoration:none; border-radius:5px;'>
                              Click here to Login
                            </a></p>
                        <br>
                        <p>Best regards,<br>Beauty Blessed Shop Team</p>
                    ";
                    $mail->send();

                } catch (Exception $e) {
                    $alert_script = "<script>
                        Swal.fire({
                            icon: 'warning',
                            title: 'Registered but Email Failed',
                            text: 'User registered but email not sent. Error: " . addslashes($mail->ErrorInfo) . "',
                            customClass: { popup: 'my-swal-size' },
                            confirmButtonColor: '#6d2e3a'
                        });
                    </script>";
                }

                $alert_script = "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: 'Welcome to Beauty and Blessed Shop!',
                        customClass: { popup: 'my-swal-size' },
                        confirmButtonColor: '#6d2e3a'
                    }).then(() => {
                        window.location = 'https://beautyandblessed.online/login.php?success=1';
                    });
                </script>";

            } else {
                $alert_script = "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Database Error',
                        text: 'Error saving registration: " . addslashes($conn->error) . "',
                        customClass: { popup: 'my-swal-size' },
                        confirmButtonColor: '#6d2e3a'
                    });
                </script>";
            }
            $conn->close();
        }
        }
    }
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous" />
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
/* 🌸 REMOVE BLUE FOCUS BORDER FROM INPUTS */
.form-control:focus,
.input-group-text:focus,
.btn:focus {
  outline: none !important;
  box-shadow: none !important;
  border-color: #d8bfd8 !important; /* soft purple/pink border */
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

      <h3 class="mt-2">Register</h3>

      <form id="registerForm" method="POST" action="">

        <div class="mb-2">
          <label>First Name</label>
          <div class="input-group">
            <span class="input-group-text" style="background: rgba(109,46,58,0.13); border:none;">
              <i class="bi bi-person-fill" style="color: #6d2e3a; font-size: 15px;"></i>
            </span>
            <input type="text" name="firstname" class="form-control"  placeholder="Enter your firstname"
              value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>" required>
          </div>

        <div class="mb-2">
          <label>Last Name</label>
          <div class="input-group">
            <span class="input-group-text" style="background: rgba(109,46,58,0.13); border:none;">
              <i class="bi bi-person-fill" style="color: #6d2e3a; font-size: 15px;"></i>
            </span>
          <input type="text" name="lastname" class="form-control" placeholder="Enter your lastname"
            value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>" required>
        </div>
         </div>

        <div class="mb-2">
          <label>Email</label>
          <div class="input-group">
            <span class="input-group-text" style="background: rgba(109,46,58,0.13); border:none;">
              <i class="bi bi-envelope-fill" style="color: #6d2e3a; font-size: 15px;"></i>
            </span>
          <input type="email" name="email" class="form-control"  placeholder="Enter your email"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
        </div>
         </div>

        <div class="mb-2">
          <label>Phone</label>
          <div class="input-group">
            <span class="input-group-text" style="background: rgba(109,46,58,0.13); border:none;">
              <i class="bi bi-telephone-fill" style="color: #6d2e3a; font-size: 15px;"></i>
            </span>
          <input type="text" name="phone" class="form-control"  placeholder="Enter your phone number"
            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
        </div>
         </div>

        <div class="mb-2">
          <label>Password</label>
          <div class="input-group">
            <span class="input-group-text" style="background: rgba(109,46,58,0.13); border:none;">
              <i class="bi bi-lock-fill" style="color: #6d2e3a; font-size: 15px;"></i>
            </span>
          
            <input type="password" id="password" name="password"  placeholder="Enter your password" class="form-control" required>
            <button type="button" onclick="togglePassword('password', this)" class="btn btn-outline-light">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <div id="passwordRequirement" class="requirement invalid">
            <i class="bi bi-info-circle"></i> Must be 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 symbol.
          </div>
        </div>
         </div>

        <div class="mb-2">
          <label>Confirm Password</label>
          <div class="input-group">
            <span class="input-group-text" style="background: rgba(109,46,58,0.13); border:none;">
              <i class="bi bi-lock-fill" style="color: #6d2e3a; font-size: 15px;"></i>
            </span>
            <input type="password" id="confirm_password" name="confirm_password"  placeholder="Confirm password" class="form-control" required>
            <button type="button" onclick="togglePassword('confirm_password', this)" class="btn btn-outline-light">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-check my-2">
          <input class="form-check-input" type="checkbox" id="privacyCheck" required>
          <label class="form-check-label">
            I agree to the <a href="policy.php" class="fw-bold text-white">Privacy Policy</a>
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

<script>
    function togglePassword(id, btn) {
      const input = document.getElementById(id);
      const icon = btn.querySelector("i");
      input.type = input.type === "password" ? "text" : "password";
      icon.classList.toggle("bi-eye");
      icon.classList.toggle("bi-eye-slash");
    }

    const password = document.getElementById("password");
    const passwordRequirement = document.getElementById("passwordRequirement");
    password.addEventListener("input", () => {
      const value = password.value;
      const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;
      if (regex.test(value)) {
        passwordRequirement.classList.add("valid");
        passwordRequirement.classList.remove("invalid");
        passwordRequirement.innerHTML = '<p class="text-success"><i class="fa-solid fa-circle-check text-success"></i> Password meets all requirements.</p>';
      } else {
        passwordRequirement.classList.add("invalid");
        passwordRequirement.classList.remove("valid");
        passwordRequirement.innerHTML = '<p class="text-danger"><i class="fa-solid fa-circle-info text-danger"></i> Password must contain: At least 8 characters, 1 uppercase, 1 lowercase, 1 number, and 1 symbol.</p>';
      }
    });

    const privacyCheck = document.getElementById("privacyCheck");
    const registerBtn = document.getElementById("registerBtn");
    const registerForm = document.getElementById("registerForm");

    privacyCheck.addEventListener("change", () => {
      registerBtn.disabled = !privacyCheck.checked;
    });

    registerBtn.addEventListener("click", () => {
      const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;
      const firstname = document.querySelector("[name='firstname']").value.trim();
      const lastname = document.querySelector("[name='lastname']").value.trim();
      const email = document.querySelector("[name='email']").value.trim();
      const phone = document.querySelector("[name='phone']").value.trim();
      const pass = document.getElementById("password").value;
      const confirmPass = document.getElementById("confirm_password").value;

      if (!firstname || !lastname || !email || !phone) {
        Swal.fire({icon:'error', title:'Missing Fields', text:'Please fill out all fields.', customClass: { popup: 'my-swal-size' } , confirmButtonColor:'#6d2e3a'});
        return;
      }

      if (!regex.test(pass)) {
        Swal.fire({icon:'error', title:'Weak Password', text:'Password does not meet requirements.', customClass: { popup: 'my-swal-size' } , confirmButtonColor:'#6d2e3a'});
        return;
      }

      if (pass !== confirmPass) {
        Swal.fire({icon:'error', title:'Password Mismatch', text:'Passwords do not match.', customClass: { popup: 'my-swal-size' } , confirmButtonColor:'#6d2e3a'});
        return;
      }

      Swal.fire({
        title: 'Confirm Registration',
        text: 'Are you sure you want to register?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6d2e3a',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Yes, Register',
        customClass: { popup: 'my-swal-size' } 
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Processing...',
            text: 'Please wait...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          // delay ng konti para makita ang loading bago mag submit
          setTimeout(() => {
            registerForm.submit();
          }, 3000);
        }
      });
    });
  </script>

  <?php echo $alert_script; ?>

</body>
</html>
