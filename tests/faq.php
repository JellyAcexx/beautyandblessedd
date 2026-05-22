<style>
/* 🌸 FAQ Layout & Style */
.faq-section {
  background-color: #ec7699;
  font-family: 'Poppins', sans-serif;
  color: #fae6e7;
  padding: 4rem 1rem;
}

.faq-section h1 {
  color: #fae6e7;
  font-weight: 700;
  text-align: center;
  margin-bottom: 1rem;
  font-size: 3rem;
}

.faq-section p.lead {
  text-align: center;
  max-width: 800px;
  margin: 0 auto 3rem;
  color: #fae6e7;
  opacity: 0.9;
  font-size: 0.95rem;
}

/* 🌸 Left Categories */
.faq-categories {
  list-style: none;
  padding: 0;
}

.faq-categories li {
  background-color: #fae6e7;
  color: #ec7699;
  padding: 12px 20px;
  margin-bottom: 10px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.faq-categories li:hover {
  background-color: #fdd8e2;
}

.faq-categories li.active {
  background-color: #ec7699;
  color: #fae6e7;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* 🌸 Accordion */
.accordion-item {
  border-radius: 10px;
  overflow: hidden;
  background-color: #fae6e7;
  transition: 0.2s ease;
}

.accordion-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.1);
}

.accordion-button {
  background-color: #fae6e7 !important;
  color: #ec7699 !important;
  font-weight: 600;
  box-shadow: none !important;
}

.accordion-body {
  color: #ec7699;
  line-height: 1.8;
  padding: 1.5rem 2rem;
  font-size: 0.95rem;
  text-align: justify !important;         /* FIX TEXT JUSTIFY */
  text-justify: inter-word !important;
}

/* 🌸 Desktop Layout */
.faq-layout {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 2rem;
}

/* 🌸 MOBILE FIXES */
@media (max-width: 768px) {
  .faq-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .faq-categories li {
    flex: 1 1 calc(50% - 10px);
    text-align: center;
  }

  .faq-layout {
    grid-template-columns: 1fr !important;
  }

  .faq-section h1 {
    font-size: 1.9rem !important;
    margin-bottom: 1rem !important;
  }

  .faq-section p.lead {
    font-size: 0.85rem !important;
    max-width: 90%;
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
<section class="faq-section">
  <h1>Frequently Asked Questions</h1>
 <p class="tc-intro mb-5">

    Here are the most common questions about <strong>Beauty & Blessed</strong> — from accounts, reservations, payments, and store policies.
  </p>

  <div class="container" style="max-width: 1200px;">
    <div class="faq-layout">
      
      <!-- LEFT CATEGORIES -->
      <ul class="faq-categories">
        <li class="active" data-category="account">Customer Account</li>
        <li data-category="reservation">Reservations</li>
        <li data-category="pickup">Pick-up & Payment</li>
        <li data-category="walkin">Walk-in Purchases</li>
        <li data-category="issues">Issues & Concerns</li>
      </ul>

      <!-- RIGHT SIDE CONTENT -->
      <div class="faq-content">

        <!-- CUSTOMER ACCOUNT -->
        <div class="faq-group" id="account">
          <div class="accordion" id="fa1">

            <!-- Dashboard -->
            <div class="accordion-item mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#acc1">
                  What can I see in my account dashboard?
                </button>
              </h2>
              <div id="acc1" class="accordion-collapse collapse" data-bs-parent="#fa1">
                <div class="accordion-body">
                  Your account dashboard allows you to manage your profile, view your online reservation history, check the status of pending reservations, and review previously picked-up transactions. Walk-in transactions are not included since they do not require an online account.
                </div>
              </div>
            </div>

            <!-- Walk-in hidden? -->
            <div class="accordion-item mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#acc2">
                  Do walk-in purchases appear in my online purchase history?
                </button>
              </h2>
              <div id="acc2" class="accordion-collapse collapse" data-bs-parent="#fa1">
                <div class="accordion-body">
                  No. Walk-in purchases do not appear in your online account because they are processed directly at the store without using a customer login. Only reservations made through your Beauty & Blessed account will be recorded in your online purchase history.
                </div>
              </div>
            </div>

            <!-- Forgot Password -->
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#acc3">
                  What should I do if I forgot my password?
                </button>
              </h2>
              <div id="acc3" class="accordion-collapse collapse" data-bs-parent="#fa1">
                <div class="accordion-body">
                  Simply click the “Forgot Password” button on the login page. Enter your registered email to receive a password reset link. If the email does not arrive within a few minutes, check your spam or promotions folder.
                </div>
              </div>
            </div>

          </div>
        </div>


        <!-- RESERVATIONS -->
        <div class="faq-group d-none" id="reservation">
          <div class="accordion" id="fa2">

            <!-- Place reservation -->
            <div class="accordion-item mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#res1">
                  How do I place an online reservation?
                </button>
              </h2>
              <div id="res1" class="accordion-collapse collapse" data-bs-parent="#fa2">
                <div class="accordion-body">
                  To place a reservation, log in to your account, browse available products, add items to your cart, and confirm your reservation. The system will only allow reservations for items with sufficient stock.
                </div>
              </div>
            </div>

            <!-- Available for reservation -->
            <div class="accordion-item mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#res2">
                  Are all products available for reservation?
                </button>
              </h2>
              <div id="res2" class="accordion-collapse collapse" data-bs-parent="#fa2">
                <div class="accordion-body">
                  Only items with adequate inventory are eligible for online reservation. Products with low stock may be reserved exclusively for walk-in customers.
                </div>
              </div>
            </div>

            <!-- 3-day policy -->
            <div class="accordion-item mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#res3">
                  What happens if I don't pick up my reservation within 3 days?
                </button>
              </h2>
              <div id="res3" class="accordion-collapse collapse" data-bs-parent="#fa2">
                <div class="accordion-body">
                  You are given a maximum of 3 days to pick up your reserved items. If not claimed within this period, the reservation will be automatically cancelled to give way to other customers.
                </div>
              </div>
            </div>

            <!-- Cancel reservation -->
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#res4">
                  Can I cancel my reservation after submitting it?
                </button>
              </h2>
              <div id="res4" class="accordion-collapse collapse" data-bs-parent="#fa2">
                <div class="accordion-body">
                  No. Customers cannot cancel their reservation once submitted. The store implemented the policy (CANCEL ONE, CANCEL ALL) only. 
                </div>
              </div>
            </div>

          </div>
        </div>


        <!-- PICK-UP & PAYMENT -->
        <div class="faq-group d-none" id="pickup">
          <div class="accordion" id="fa3">

            <div class="accordion-item mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#pick1">
                  Where do I pick up my reserved items?
                </button>
              </h2>
              <div id="pick1" class="accordion-collapse collapse" data-bs-parent="#fa3">
                <div class="accordion-body">
                  All reserved items must be picked up directly from the Beauty & Blessed physical store. The exact address is displayed on homepage (Botttom).
                </div>
              </div>
            </div>

            <div class="accordion-item mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#pick2">
                  Do I need to pay online when reserving?
                </button>
              </h2>
              <div id="pick2" class="accordion-collapse collapse" data-bs-parent="#fa3">
                <div class="accordion-body">
                  No. All payments are strictly cash-on-pickup. The store does not accept online or advance payments for reservations.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#pick3">
                  What payment methods do you accept?
                </button>
              </h2>
              <div id="pick3" class="accordion-collapse collapse" data-bs-parent="#fa3">
                <div class="accordion-body">
                  Payments are made in cash upon pick-up. Since reservations require physical confirmation, advance digital payments are not offered for online reservations.
                </div>
              </div>
            </div>

          </div>
        </div>



        <!-- WALK-IN -->
        <div class="faq-group d-none" id="walkin">
          <div class="accordion" id="fa4">

            <div class="accordion-item mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#walk1">
                  Can I buy products without a reservation?
                </button>
              </h2>
              <div id="walk1" class="accordion-collapse collapse" data-bs-parent="#fa4">
                <div class="accordion-body">
                  Yes. Customers are always welcome to purchase items directly at the store even without placing a reservation.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#walk2">
                  Do walk-in customers need an account?
                </button>
              </h2>
              <div id="walk2" class="accordion-collapse collapse" data-bs-parent="#fa4">
                <div class="accordion-body">
                  No account is required for walk-in transactions. Customers may purchase freely without logging in or creating an online profile.
                </div>
              </div>
            </div>

          </div>
        </div>


        <!-- ISSUES & CONCERNS -->
        <div class="faq-group d-none" id="issues">
          <div class="accordion" id="fa5">

            <div class="accordion-item mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#iss1">
                  What should I do if I made a mistake in my reservation details?
                </button>
              </h2>
              <div id="iss1" class="accordion-collapse collapse" data-bs-parent="#fa5">
                <div class="accordion-body">
                  If you entered incorrect details, please contact us immediately through Facebook or email. While reservations cannot be edited, the admin will assist you with the best possible solution.
                </div>
              </div>
            </div>

            <div class="accordion-item mb-3">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#iss2">
                  The product I want is out of stock — when will it be available?
                </button>
              </h2>
              <div id="iss2" class="accordion-collapse collapse" data-bs-parent="#fa5">
                <div class="accordion-body">
                  Restocks vary depending on supplier availability. Follow our Facebook page for announcements, or check the website for real-time updates on product availability.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#iss3">
                  Who should I contact for help or questions?
                </button>
              </h2>
              <div id="iss3" class="accordion-collapse collapse" data-bs-parent="#fa5">
                <div class="accordion-body">
                  You may message us directly through our official Facebook page, email us at <strong>beautyandblessed@gmail.com</strong>, or call/text us at <strong>0966 944 5591</strong> for assistance.
                </div>
              </div>
            </div>

          </div>
        </div>


      </div>
    </div>
  </div>
</section>

<script>
document.querySelectorAll(".faq-categories li").forEach(cat => {
  cat.addEventListener("click", () => {
    document.querySelectorAll(".faq-categories li").forEach(c => c.classList.remove("active"));
    cat.classList.add("active");

    document.querySelectorAll(".faq-group").forEach(g => g.classList.add("d-none"));
    const target = document.getElementById(cat.dataset.category);
    if (target) target.classList.remove("d-none");
  });
});

// 🌸 Force ALL accordion panels closed on page load
document.addEventListener("DOMContentLoaded", function () {
  
  // Close all accordion items
  document.querySelectorAll(".accordion-collapse").forEach(panel => {
    panel.classList.remove("show");
  });

  // Remove focus so Bootstrap won't auto-open anything
  document.querySelectorAll(".accordion-button").forEach(btn => {
    btn.blur();
  });

});
</script>

