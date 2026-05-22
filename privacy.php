<style>
.accordion-item {
  transition: transform .2s ease, box-shadow .2s ease;
}
.accordion-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.1);
}
.accordion-button:focus {
  box-shadow: 0 0 0 0.70rem #fae6e7 !important; /* light pink glow */
  border-color: #fae6e7 !important; /* pink border focus */
  outline: none !important;
}
.accordion-body {
  padding: 1.5rem 2rem; /* default is 1rem */
  font-size: 0.95rem;
  line-height: 1.8;
}
.accordion-body {
  background-color: #ffffff !important;
  color: #6d2e3a; /* para pareho pa rin sa theme mo */
}

.accordion-item + .accordion-item {
  border-top: 2px solid rgba(236, 73, 112, 0.15);
}
/* Mobile Responsive Adjustments */
@media (max-width: 768px) {
  h1 {
    font-size: 2.5rem !important; /* mas maliit sa mobile */
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
    font-size: 2rem !important; /* super small phones */
  }

  .accordion-button {
    font-size: 0.95rem !important;
  }
}
/* 🔥 Default paragraph style */
.tc-intro {
  font-size: 0.95rem;
  color: #fae6e7;
  text-align: justify;
  text-justify: inter-word;
  line-height: 1.7;
  max-width: 900px;
  margin: 0 auto 2rem auto;
}

/* 🔥 Mobile Fix — force justify properly */
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

<section class="py-5" style="background-color: #a95469; font-family:'Poppins', sans-serif; color: #fff;">
  <div class="container" style="max-width:1200px;">

    <!-- Header -->
    <h1 class="fw-bold text-center mb-4" style="color: #fff; font-size: 2.5rem;">Privacy Policy</h1>
    <p class="tc-intro mb-5" style="font-size:0.95rem; color: #fff; text-align:justify;">
      At <strong>Beauty & Blessed</strong>, your privacy is important to us. 
      We are committed to protecting your personal information and ensuring that your data is handled 
      responsibly, transparently, and securely. This Privacy Policy explains how we collect, use, 
      store, and safeguard your information whenever you browse our website, create an account, 
      reserve products, or purchase items in-store.
    </p>

    <div class="accordion" id="privacyAccordion">

      <!-- 1. About this Policy -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy1"
            style="background-color: #fae6e7; color: #6d2e3a;">
            About this Policy
          </button>
        </h2>
        <div id="policy1" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            This Privacy Policy outlines how <strong>Beauty & Blessed</strong> collects, handles, and protects the
            personal information you provide when accessing our website or using our reservation and sales system.
            By using our services, you acknowledge and agree to the practices described in this policy. 
            We encourage you to read everything carefully so you understand how your data is managed and secured.
          </div>
        </div>
      </div>

      <!-- 2. What Personal Data is Collected -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy2"
            style="background-color:#fae6e7; color: #6d2e3a;">
            What Personal Data is Collected?
          </button>
        </h2>
        <div id="policy2" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            We collect specific information to ensure accurate reservation processing, smooth customer service, 
            and transparent sales reporting. These include your name, email address, contact number, account login details, 
            and your reservation or purchase history.  
            <br><br>
            For walk-in customers, providing a name is optional and is only recorded to complete the transaction properly.  
            We do <strong>not</strong> collect or store sensitive financial information since all payments are made strictly upon pick-up.
          </div>
        </div>
      </div>

      <!-- 3. How Your Data is Used -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy3"
            style="background-color: #fae6e7; color: #6d2e3a;">
            How is Your Personal Data Used?
          </button>
        </h2>
        <div id="policy3" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            Your information helps us provide an organized and efficient shopping experience. 
            We use your data to process online reservations, verify identity during pick-up, update sales and inventory records, 
            maintain accurate reservation logs, and offer reliable customer support. 
            <br><br>
            This also ensures that all transactions — whether walk-in or reservation-based — are recorded correctly, 
            giving both the business and the customer a transparent view of completed purchases.
          </div>
        </div>
      </div>

      <!-- 4. Reservation-Related Data Use -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy4"
            style="background-color:#fae6e7; color: #6d2e3a;">
            Reservation & Pick-Up Data Handling
          </button>
        </h2>
        <div id="policy4" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            Since our system follows a <strong>3-day pick-up rule</strong>, your data also helps track your reservation status.  
            We use your information to confirm reserved items upon pick-up, update inventory automatically, 
            and maintain accurate reservation history.  
            <br><br>
            Reservations that remain unclaimed after 3 days are automatically cancelled by the system to maintain stock fairness. 
            Please ensure that all reservation details you submit are accurate, as customers are not allowed to cancel once submitted.
          </div>
        </div>
      </div>

      <!-- 5. Your Rights -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy5"
            style="background-color: #fae6e7; color: #6d2e3a;">
            Your Rights as a User
          </button>
        </h2>
        <div id="policy5" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            As a valued Beauty & Blessed user, you have full control of your personal information.  
            You may request corrections to inaccurate details, update your contact information, 
            or request account deletion at any time.  
            <br><br>
            Walk-in customers are not required to create an account, 
            but online users benefit from viewing reservation history, pick-up records, and profile settings securely.
          </div>
        </div>
      </div>

      <!-- 6. Data Security -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy6"
            style="background-color:#fae6e7; color: #6d2e3a;">
            Data Security & Protection
          </button>
        </h2>
        <div id="policy6" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            We implement strong security measures such as encrypted passwords, secure servers, daily monitoring, 
            and limited admin access. These safeguards protect your information from unauthorized access, 
            misuse, or accidental loss.  
            <br><br>
            Beauty & Blessed also ensures that no payment data is stored, as all payments are handled 
            in person during pick-up for maximum safety.
          </div>
        </div>
      </div>

      <!-- 7. Cookies -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy7"
            style="background-color: #fae6e7; color: #6d2e3a;">
            Cookies
          </button>

        </h2>
        <div id="policy7" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            Our website may use cookies to improve loading speed, enhance user experience, and help the system perform smoothly.  
            Cookies do not collect sensitive personal data and can be disabled anytime in your browser settings.
          </div>
        </div>
      </div>

      <!-- 8. Updates -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#policy8"
            style="background-color: #fae6e7; color: #6d2e3a;">
            Updates to This Policy
          </button>
        </h2>
        <div id="policy8" class="accordion-collapse collapse" data-bs-parent="#privacyAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            Beauty & Blessed may revise or update this Privacy Policy at any time to reflect system improvements 
            or comply with updated business practices.  
            Continued use of our website means acceptance of any new changes.
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>