<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Privacy Policy | Beauty & Blessed</title>

  <!-- BOOTSTRAP REQUIRED -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      min-height: 100vh;
      background-color:#a95469;
      font-family:'Poppins', sans-serif;
    }

    /* Back button (fixed) */
    .back-btn {
  display: inline-block;
  background: #9d4b63;
  padding: 5px 18px;
  border-radius: 12px;
  font-weight: 600;
  color: white;
  text-decoration: none;
  margin-left: -10px;
  box-shadow: 0 6px 16px rgba(0,0,0,0.15);
  transition: 0.2s ease;
}
    .back-btn:hover {
      background: #6d2e3a;
    }

    /* ===== YOUR CSS: ACCORDION, COLORS, RESPONSIVE, ETC ===== */
   /* BEAUTY & BLESSED ACCORDION FIX */

/* Outer card look */
.accordion-item {
  background-color: #ffffffff !important;
  border-radius: 15px !important;
  margin-bottom: 18px;
  border: none !important;
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.accordion-item:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 26px rgba(0,0,0,0.12);
}

/* Header button */
.accordion-button {
  background-color: #f8d7dc !important;
  color: #6d2e3a !important;
  font-weight: 600;
  font-size: 1.15rem;
  padding: 1.3rem 1.5rem;
  border: none !important;
  box-shadow: none !important;
}

/* Focus (pink glow) */
.accordion-button:focus {
  box-shadow: 0 0 0 0.70rem #fae6e7 !important;
  border-color: #fae6e7 !important;
  outline: none !important;
}

/* Arrow color */
.accordion-button::after {
  filter: brightness(0) saturate(100%) invert(56%) sepia(36%) saturate(1219%)
          hue-rotate(305deg) brightness(96%) contrast(90%);
}

/* Open state */
.accordion-button:not(.collapsed) {
  color: #6d2e3a !important;
  background-color: #f8d7dc !important;
  box-shadow: inset 0 -1px 0 rgba(0,0,0,0.1) !important;
}

/* Body */
.accordion-body {
  background-color: #fff !important;
  color: #6d2e3a !important;
  padding: 1.7rem 2rem !important;
  font-size: 1rem;
  line-height: 1.8;
  text-align: justify;
}

/* Divider between items (optional, pwede mong i-remove kung ayaw mo) */
.accordion-item + .accordion-item {
  border-top: 0;
}


    @media (max-width: 768px) {
      h1 {
        font-size: 2.5rem !important;
      }
      .accordion-body {
        font-size: 0.85rem !important;
        padding: 1rem 1.2rem !important;
      }
      p.text-center {
        font-size: 0.85rem !important;
      }
      .accordion-button {
        font-size: 1rem !important;
      }
      .container {
        padding: 0 1rem;
      }
    }

    @media (max-width: 480px) {
      h1 {
        font-size: 2rem !important;
      }
      .accordion-button {
        font-size: 0.95rem !important;
      }
    }

    .tc-intro {
      font-size: 0.95rem;
      color: #fff;
      text-align: justify;
      text-justify: inter-word;
      line-height: 1.7;
      max-width: 900px;
      margin: 0 auto 2rem auto;
    }
    @media (max-width: 768px) {
      .tc-intro {
        font-size: 0.90rem !important;
        text-align: justify !important;
        text-justify: inter-word !important;
        padding: 0 12px !important;
        display: block !important;
        width: 100% !important;
      }
    }
    @media (max-width: 480px) {
      .tc-intro {
        font-size: 0.85rem !important;
      }
    }
  </style>
</head>
<body>

  <!-- MAIN PRIVACY CONTENT -->
  <section class="py-5" style="color:#fae6e7;">
  <div class="container" style="max-width:1200px;">
    <!-- Back Button -->
  <a onclick="history.back()" class="back-btn" style="cursor:pointer;">← Back to Register</a>

      <h1 class="fw-bold text-center mb-4" style="font-size: 3rem;">Privacy Policy</h1>

     <p class="tc-intro mb-5" style="font-size:0.95rem; color:#fff; text-align:justify;">
      At <strong>Beauty & Blessed</strong>, your privacy is important to us. 
      We are committed to protecting your personal information and ensuring that your data is handled 
      responsibly, transparently, and securely. This Privacy Policy explains how we collect, use, 
      store, and safeguard your information whenever you browse our website, create an account, 
      reserve products, or purchase items in-store.
    </p>

    <div class="accordion" id="privacyAccordion">


        <!-- 1 -->
        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; background-color:#fae6e7;">
          <h2 class="accordion-header">
            <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy1"
              style="background-color:#fae6e7; color:#fff;">
              About this Policy
            </button>
          </h2>
          <div id="policy1" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
            <div class="accordion-body" text-align:justify;">
              This Privacy Policy outlines how <strong>Beauty & Blessed</strong> collects, handles, and protects the
            personal information you provide when accessing our website or using our reservation and sales system.
            By using our services, you acknowledge and agree to the practices described in this policy. 
            We encourage you to read everything carefully so you understand how your data is managed and secured.
            </div>
          </div>
        </div>

        <!-- 2 -->
        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; background-color:#fae6e7;">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy2"
              style="background-color:#fae6e7; color:#ec7699;">
              What Personal Data is Collected?
            </button>
          </h2>
          <div id="policy2" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
            <div class="accordion-body" style="color:#ec7699; text-align:justify;">
             We collect specific information to ensure accurate reservation processing, smooth customer service, 
            and transparent sales reporting. These include your name, email address, contact number, account login details, 
            and your reservation or purchase history.  
            <br><br>
            For walk-in customers, providing a name is optional and is only recorded to complete the transaction properly.  
            We do <strong>not</strong> collect or store sensitive financial information since all payments are made strictly upon pick-up.

            </div>
          </div>
        </div>

        <!-- 3 -->
        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; background-color:#fae6e7;">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy3"
              style="background-color:#fae6e7; color:#ec7699;">
              How is Your Personal Data Used?
            </button>
          </h2>
          <div id="policy3" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
            <div class="accordion-body" style="color:#ec7699; text-align:justify;">
              Your information helps us provide an organized and efficient shopping experience. 
            We use your data to process online reservations, verify identity during pick-up, update sales and inventory records, 
            maintain accurate reservation logs, and offer reliable customer support. 
            <br><br>
            This also ensures that all transactions — whether walk-in or reservation-based — are recorded correctly, 
            giving both the business and the customer a transparent view of completed purchases.

            </div>
          </div>
        </div>

        <!-- 4 -->
        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; background-color:#fae6e7;">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy4"
              style="background-color:#fae6e7; color:#ec7699;">
              Reservation & Pick-Up Data Handling
            </button>
          </h2>
          <div id="policy4" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
            <div class="accordion-body" style="color:#ec7699; text-align:justify;">
              iv class="accordion-body" style="color:#ec7699; text-align:justify;">
            Since our system follows a <strong>3-day pick-up rule</strong>, your data also helps track your reservation status.  
            We use your information to confirm reserved items upon pick-up, update inventory automatically, 
            and maintain accurate reservation history.  
            <br><br>
            Reservations that remain unclaimed after 3 days are automatically cancelled by the system to maintain stock fairness. 
            Please ensure that all reservation details you submit are accurate, as customers are not allowed to cancel once submitted.

            </div>
          </div>
        </div>

        <!-- 5 -->
        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; background-color:#fae6e7;">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy5"
              style="background-color:#fae6e7; color:#ec7699;">
              Your Rights as a User
            </button>
          </h2>
          <div id="policy5" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
            <div class="accordion-body" style="color:#ec7699; text-align:justify;">
              As a valued Beauty & Blessed user, you have full control of your personal information.  
            You may request corrections to inaccurate details, update your contact information, 
            or request account deletion at any time.  
            <br><br>
            Walk-in customers are not required to create an account, 
            but online users benefit from viewing reservation history, pick-up records, and profile settings securely.

            </div>
          </div>
        </div>

        <!-- 6 -->
        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; background-color:#fae6e7;">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy6"
              style="background-color:#fae6e7; color:#ec7699;">
              Data Security & Protection
            </button>
          </h2>
          <div id="policy6" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
            <div class="accordion-body" style="color:#ec7699; text-align:justify;">
             We implement strong security measures such as encrypted passwords, secure servers, daily monitoring, 
            and limited admin access. These safeguards protect your information from unauthorized access, 
            misuse, or accidental loss.  
            <br><br>
            Beauty & Blessed also ensures that no payment data is stored, as all payments are handled 
            in person during pick-up for maximum safety.

            </div>
          </div>
        </div>

        <!-- 7 -->
        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; background-color:#fae6e7;">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy7"
              style="background-color:#fae6e7; color:#ec7699;">
              Cookies
            </button>
          </h2>
          <div id="policy7" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
            <div class="accordion-body" style="color:#ec7699; text-align:justify;">
              Our website may use cookies to improve loading speed, enhance user experience, and help the system perform smoothly.  
            Cookies do not collect sensitive personal data and can be disabled anytime in your browser settings.
            </div>
          </div>
        </div>

        <!-- 8 -->
        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; background-color:#fae6e7;">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy8"
              style="background-color:#fae6e7; color:#ec7699;">
              Updates to This Policy
            </button>
          </h2>
          <div id="policy8" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
            <div class="accordion-body" style="color:#ec7699; text-align:justify;">
              Beauty & Blessed may revise or update this Privacy Policy at any time to reflect system improvements 
            or comply with updated business practices.  
            Continued use of our website means acceptance of any new changes.
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
