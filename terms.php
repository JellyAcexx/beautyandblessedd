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
  background-color: #ffffff !important;
  color: #6d2e3a;
}


.accordion-item + .accordion-item {
  border-top: 2px solid rgba(236, 73, 112, 0.15);
}

/* Mobile Responsive Adjustments for Terms & Conditions */
@media (max-width: 768px) {

  /* Title */
  h1 {
    font-size: 2.5rem !important;
    line-height: 1.2 !important;
    text-align: center !important;
  }

  /* Intro text */
  p,
  p.text-center {
    font-size: 0.9rem !important;
    line-height: 1.6 !important;
    padding: 0 1rem !important;
  }

  /* Accordion header buttons */
  .accordion-button {
    font-size: 1rem !important;
    padding: 1rem !important;
  }

  /* Accordion Expanded Content */
  .accordion-body {
    font-size: 0.85rem !important;
    padding: 1rem 1.2rem !important;
  }

 .accordion-body {
  background-color: #ffffff !important;
  color: #6d2e3a; /* para pareho pa rin sa theme mo */
}

  /* Container spacing */
  .container {
    padding: 0 1rem !important;
  }
}

/* Extra small devices */
@media (max-width: 480px) {

  h1 {
    font-size: 2rem !important;
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
    <h1 class="fw-bold text-center mb-4" style="color: #fff; font-size:2.5rem;">Terms & Conditions</h1>
    <p class="tc-intro mb-5" style="font-size:0.95rem; color: #fff; text-align:justify;">
      Welcome to <strong>Beauty & Blessed</strong>. By accessing or using our website, you agree to comply with 
      and be bound by these terms and conditions. Please read them carefully before using our services.
    </p>
    <!-- ACCORDION -->
    <div class="accordion" id="termsAccordion">


      <!-- RESERVATIONS & PRODUCT AVAILABILITY -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#term1"
            style="background-color: #fae6e7; color: #6d2e3a;">
            Online Reservations & Product Availability
          </button>
        </h2>
        <div id="term1" class="accordion-collapse collapse" data-bs-parent="#termsAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            Customers may browse and reserve products currently available in stock. Only items with sufficient stock are 
            eligible for online reservation. All reservations follow a strict three-day pick-up policy, requiring customers 
            to claim their reserved items within three (3) days. Any unclaimed reservation beyond this period will be 
            automatically cancelled by the system. Customers cannot cancel their reservation once submitted since shop implemented (cancel one, cancel all), and all 
            payments are strictly made in-store upon pick-up. No online or advance payment methods are accepted.
          </div>
        </div>
      </div>


      <!-- PICK-UP & PAYMENT POLICY -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#term2"
            style="background-color:#fae6e7; color:#6d2e3a;">
            Pick-Up & Payment Policy
          </button>
        </h2>
        <div id="term2" class="accordion-collapse collapse" data-bs-parent="#termsAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            All payments must be completed only at the store during the pick-up of reserved items. 
            Upon the customer’s arrival, the admin will verify and confirm the reservation through the system, ensuring 
            accuracy in inventory updates and sales tracking. No online payments or digital transactions are processed 
            through the website.
          </div>
        </div>
      </div>


      <!-- WALK-IN PURCHASE POLICY -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#term3"
            style="background-color: #fae6e7; color:#6d2e3a;">
            Walk-In Purchase Policy
          </button>
        </h2>
        <div id="term3" class="accordion-collapse collapse" data-bs-parent="#termsAccordion">
          <div class="accordion-body" style="color:#6d2e3a; text-align:justify;">
            Walk-in customers may directly purchase products at the store without creating an account. 
            Providing a name is optional, and all walk-in transactions are manually recorded by the admin 
            to ensure proper inventory and sales updates.
          </div>
        </div>
      </div>


      <!-- SALES & TRANSPARENCY -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color: #fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#term4"
            style="background-color:#fae6e7; color:#6d2e3a;">
            Sales & Record Transparency
          </button>
        </h2>
        <div id="term4" class="accordion-collapse collapse" data-bs-parent="#termsAccordion">
          <div class="accordion-body" style="color: #6d2e3a; text-align:justify;">
            The system maintains transparency by recording both online reservations and walk-in sales separately for 
            accurate business analysis. Inventory updates occur automatically after each confirmed transaction, ensuring 
            real-time accuracy. All customer records, sales logs, and inventory movements are stored securely for 
            monitoring and auditing.
          </div>
        </div>
      </div>


      <!-- LIMITATION OF LIABILITY -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color:#fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#term5"
            style="background-color:#fae6e7; color: #6d2e3a;">
            Limitation of Liability
          </button>
        </h2>
        <div id="term5" class="accordion-collapse collapse" data-bs-parent="#termsAccordion">
          <div class="accordion-body" style="color:#6d2e3a; text-align:justify;">
            Beauty & Blessed shall not be held liable for failure to pick up reservations within the required three-day 
            window, losses caused by incorrect customer-provided information, or delays and inaccuracies resulting from 
            misuse of the system. Customers are responsible for entering accurate details and claiming their reservations 
            on time.
          </div>
        </div>
      </div>


      <!-- UPDATES TO TERMS -->
      <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden; background-color:#fae6e7;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#term6"
            style="background-color:#fae6e7; color:#6d2e3a;">
            Updates to Terms & Conditions
          </button>
        </h2>
        <div id="term6" class="accordion-collapse collapse" data-bs-parent="#termsAccordion">
          <div class="accordion-body" style="color:#6d2e3a; text-align:justify;">
            Beauty & Blessed reserves the right to modify, update, or revise these Terms & Conditions at any time without 
            prior notice. Continued use of the website or reservation system constitutes acceptance of any changes made.
          </div>
        </div>
      </div>

    </div>

    <p class="text-center mt-5 small" style="opacity:0.9; color:#fae6e7;">
      <em>Last updated: November 2025</em>
    </p>
  </div>
</section>
