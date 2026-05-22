<style>
/* Page fade animation */
section#visit {
  animation: fadeUp 1s ease forwards;
  opacity: 0;
  transform: translateY(20px);
}
@keyframes fadeUp {
  to { opacity: 1; transform: translateY(0); }
}

/* REAL responsive map wrapper (fixes overflow) */
.map-wrapper {
  width: 100%;
  max-width: 1100px;
  margin: 0 auto;
  border: 4px solid #fff;
  border-radius: 30px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.map-wrapper iframe {
  width: 100%;
  height: 450px;
  border: 0;
  display: block;
  border-radius: 30px;
}

/* Desktop */
@media (min-width: 1200px) {
  .map-wrapper iframe {
    height: 520px;
  }
}

/* Tablet */
@media (max-width: 991px) {
  #visit h1 { font-size: 2.6rem !important; }
  #visit p { font-size: 0.95rem !important; }
  .map-wrapper iframe { height: 380px; }
}

/* Mobile */
@media (max-width: 600px) {
  #visit h1 { font-size: 2.2rem !important; }
  #visit p { font-size: 0.85rem !important; }
  .map-wrapper iframe { height: 300px; }
}

/* Store Info Card */
.store-card {
  background: #fff;
  color: #6d2e3a;
  padding: 30px;
  border-radius: 25px;
  max-width: 600px;
  margin: 40px auto 10px auto;
  box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}
</style>



<section id="visit" class="py-5 text-center" style="background-color: #a95469; font-family:'Poppins', sans-serif; color: #fff;"> 

  <div class="container" style="max-width:1100px;">

    <!-- Header -->
    <h1 class="fw-bold mb-4" style="font-size:2rem;">Visit Us</h1>
    <p class="mb-5" style="font-size:1rem;">
      Discover <strong>Beauty & Blessed</strong> in person!<br>
      You can find us at <b>C. Alvarez, Nasugbu, Batangas</b>.<br>
      Drop by to explore our beauty collections, get personalized product tips,  
      or claim your reserved items with ease. 
    </p>

    <!-- Map Wrapper (fixed version) -->
    <div class="map-wrapper mb-4">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d490.7983113233677!2d120.6331056455367!3d14.07274523106766!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd7cb6d0b0e50b%3A0x9abbd1e0b8d3cf67!2sBeauty%20and%20Blessed%2C%20C.%20Alvarez%2C%20Nasugbu%2C%20Batangas!5e0!3m2!1sen!2sph!4v1731303279983!5m2!1sen!2sph"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>


    <!-- Store Information Card -->
    <div class="store-card text-start">
      <h4 class="fw-bold mb-3">Store Information</h4>
      <ul class="list-unstyled" style="line-height:2;">
        <li><i class="fa-solid fa-location-dot me-2"></i> C. Alvarez, Nasugbu, Batangas</li>
        <li><i class="fa-solid fa-clock me-2"></i> Open Daily — 9:00 AM to 6:00 PM</li>
        <li><i class="fa-solid fa-phone me-2"></i> +63 912 345 6789</li>
        <li><i class="fa-solid fa-envelope me-2"></i> beautyandblessed@gmail.com</li>
      </ul>
    </div>


    <!-- Directions Button -->
    <div class="mt-3">
      <a href="https://www.google.com/maps/dir/?api=1&destination=Beauty+and+Blessed,+C+Alvarez,+Nasugbu,+Batangas" 
         target="_blank" 
         class="btn fw-semibold"
         style="background-color: #fff; color: #6d2e3a; border-radius:30px; padding:12px 28px; text-transform:uppercase; font-size:0.9rem;">
        Get Directions
      </a>
    </div>

  </div>
</section>




<script>
// Fix Google Maps not showing after AJAX load
document.addEventListener("DOMContentLoaded", () => {
  const observer = new MutationObserver(() => {
    const map = document.querySelector("iframe[src*='google.com/maps']");
    if (map && !map.classList.contains("reloaded")) {
      map.src = map.src;
      map.classList.add("reloaded");
    }
  });

  const targetSection = document.getElementById("visit");
  if (targetSection) {
    observer.observe(targetSection, { childList: true, subtree: true });
  }
});
</script>
