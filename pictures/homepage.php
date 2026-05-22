<?php
session_start();

$register_id = isset($_SESSION['register_id']) ? $_SESSION['register_id'] : null;
$login_id = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOMEPAGE</title>
    <link rel="stylesheet" href="bootstrap5/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Meie+Script&display=swap" rel="stylesheet">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


    <style>
        html, body {
            overflow: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE 10+ */
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            overscroll-behavior: none;
        }

        html::-webkit-scrollbar, body::-webkit-scrollbar {
            display: none;
        }
        
        body {
            font-family: 'Poppins', sans-serif !important;
            padding-top: 0px;
            font color: #fff !important;
        }

        .line {
            background-color: #fff; 
            color: #6d2e3a;
        }

        .navbar-brand {
            margin-left: 70px;
        }

        .brand-logo {
            height: auto;
            max-height: 100px;
            width: auto;
        }

        .brand-title {
            font-size: clamp(1.2rem, 3vw, 3.5rem); 
            font-weight: 700;
            color: #6d2e3a;
            line-height: 1.1;
            margin: 0;
            padding: 0;
            white-space: nowrap;
        }

        .brand-subtitle {
            font-size: clamp(0.6rem, 1.2vw, 1.3rem);
            font-weight: 550;
            color: #6d2e3a;
            margin: 0;
            line-height: 1;
        }

        .search-wrapper {
            max-width: 400px;
            margin-left: 30px;
        }

        .dual-btn {
            display: flex;
            align-items: center;
            color: #6d2e3a;
            justify-content: center;
            gap: 3px;
            padding: 6px 3px;
            border: none;
            background-color: #fae6e7;
            font-weight: 600;
            cursor: pointer;
        }

        .dual-btn span {
            position: relative;
            padding: 4px 6px;
            transition: color 0.2s ease-in-out;
        }

        .dual-btn span::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0%;
            height: 2px;
            background-color: #6d2e3a;
            transition: width 0.3s ease-in-out;
        }

        .dual-btn span:hover::after {
            width: 100%;
        }

        .divider {
            width: 2px;
            height: 20px;
            background-color: #6d2e3a;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            filter: invert(0%) sepia(100%) saturate(0%) hue-rotate(0deg) brightness(0%) contrast(100%);
        }

        .mobile-search {
            display: none;
        }

        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 230px; 
            height: 100%;
            background-color: #fae6e7;
            z-index: 2000;
            transition: left 0.3s ease-in-out;
            padding: 20px 10px;
            overflow-y: auto;
        }

        .mobile-sidebar.show {
            left: 0;
        }

        .mobile-sidebar .navbar-nav {
            flex-direction: column;
            display: flex !important;
        }

        .mobile-sidebar .auth-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;     
            margin-bottom: 20px;   
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 1040;
        }

        .sidebar-backdrop.active {
            display: block;
        }

        .navbar-nav {
            display: flex;
        }

        .nav-link.active {
            font-weight: 800;
            border-radius: 5px;
            transition: 0.3s;
        }
        
        .nav-link:hover {
            background: #6d2e3a; 
            border-radius: 5px;
            color: white !important;
        }

        .mobile-sidebar .nav-link { 
            color: white; 
            width: calc(100% - 8px);
            margin: 5px auto;
            text-align: left;
            font-weight: 500; 
            padding: 15px 15px;
            border-radius: 5px; 
            display: block; 
            align-items: center; 
            gap: 10px; 
        }

        .mobile-sidebar .nav-link.active { 
            background: #6d2e3a; 
            color: white !important; 
            font-weight: bold; 
            border-radius: 5px;
            display: block;
        }

        .mobile-sidebar .nav-link:hover { 
            background: #d96d84; 
            border-radius: 5px;
            color: #6d2e3a; 
        }

        #sidebarToggle {
            display: none;
        }

        #myTabContent p.fs-4 {
            font-size: 1.5rem !important;
        }

        input[type="search"]:focus {
            outline: none;
            box-shadow: none;
            border: 2px solid #6d2e3a;
        }

        input::placeholder {
            color: #6d2e3a !important;
            opacity: 1;
        }

        .productCarousel .custom-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #a95469;
            border: none;
            color: #fff;
            font-size: 24px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
            cursor: pointer;
        }

        .productCarousel .carousel-control-prev.custom-control {
            left: -30px;
        }

        .productCarousel .carousel-control-next.custom-control {
            right: -30px;
        }

        .productCarousel .custom-control:hover {
            background: #6d2e3a;
            color: white;
        }

        .productCarousel .carousel-inner {
            display: flex;
            flex-direction: row;
            overflow: hidden;
        }

        .productCarousel .carousel-item {
            width: 100%;
        }

        .btn-xs {
            padding: 4px 8px;
            font-size: 11px;
            border-radius: 3px;
            display: none;
        }

        .card {
            width: 220px !important;
            flex: 0 0 auto !important;
            position: relative;
            overflow: hidden;
            box-shadow: 0 -2px 20px rgba(0,0,0,0.05);
        }

        .card-title {
            font-family: 'Poppins', sans-serif;
            font-size: 16px !important;
            font-weight: 700;
            color: #6d2e3a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-img-top {
            display: block;
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .zoom-btn {
            position: absolute;
            opacity: 0;
            color: #6d2e3a;
            border-radius: 50%;
            transition: opacity 0.3s ease, transform 0.3s ease;
            border: none;
            right: 10px;
            top: 10px;
            transform: scale(0.8);
            z-index: 10;
        }

        /* add to cart button*/
        .add-to-cart-btn {
            position: absolute;
            top: 260px;
            left: 0;
            height: 40px;
            width: 100%;
            border: none;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            padding: 5px 0;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 2;
        }

        .card:hover .zoom-btn,
        .card:hover .add-to-cart-btn {
            opacity: 1;
            transform: scale(1);
        }

        .card:hover {
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.4);
            transition: box-shadow 0.3s ease;
        }

        .fa-magnifying-glass-plus {
            font-size: 18px;
        }

        .transition-scale {
            transition: transform 0.4s ease;
        }

        .zoomed-in {
            transform: scale(1.6);
            cursor: zoom-out;
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.3);
        }

        .zoom-out-btn {
            top: 10px;
            right: 10px;
            color: #6d2e3a;
            opacity: 0.9;
            border-radius: 50%;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .fa-magnifying-glass-minus {
            font-size: 25px;
        }

        .product-row {
            display: flex;
            justify-content: center;
            align-items: stretch;
            flex-wrap: nowrap;
        }

        .custom-control.disabled i {
            opacity: 0.3;
        }

        /* add to cart 2 */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* slightly softer overlay */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        /* 💖 Modal Box */
        .modal-box {
            background: #fff;
            padding: 20px 24px;
            text-align: center;
            border-radius: 12px;
            width: 300px; /* smaller size */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            font-family: 'Poppins', sans-serif;
            color: #6d2e3a;
        }

        /* 💕 Modal Title */
        .modal-box h4 {
            color: #6d2e3a;
            font-weight: 600;
            margin-bottom: 8px;
        }

        /* 💗 Input Field */
        .modal-box input[type="number"] {
            background: #fff;
            padding: 20px 24px;
            text-align: center;
            border-radius: 12px;
            width: 300px; /* smaller size */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            font-family: 'Poppins', sans-serif;
            color: #6d2e3a;
        }

        .modal-box input[type="number"]:focus {
            box-shadow: 0 0 4px #f8bbd0;
        }

        /* 💞 Total Text */
        .modal-box p {
            margin-top: 8px;
            font-weight: 400;
            color: #6d2e3a;
        }

        /* 💖 Buttons */
        .btn-modal {
            display: inline-block;
            width: 55px !important; /* smaller width */
            height: 40px;
            padding: 6px 0;
            border: none;
            border-radius: 6px;
            text-transform: uppercase;
            font-size: 0.8rem;
            margin: 8px 6px 0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        /* 🌷 Add Button (Dark Pink) */
        #confirmAddBtn.btn-modal {
            background:#6d2e3a;
            color: #fff;
        }

        /* 💗 Cancel Button (Light Pink) */
        #cancelAddBtn.btn-modal {
            background: #5f5b5bff; 
            color: white;
        }

        .highlight-product {
            outline: 4px solid #6d2e3a;
            outline-offset: 3px;
            transition: outline-offset 0.3s ease;
        }

        #successModal {
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .footer-link {
            text-decoration: none;
            color: #6d2e3a;
            transition: color .2s ease, opacity .2s ease, transform .2s ease;
        }

        .footer-link:hover {
            color: #fea6e7;
            opacity: 0.85;
            transform: translateY(-1px);
        }

        footer {
            box-shadow: 0 -2px 20px rgba(0,0,0,0.05);
        }

        footer i {
            color: #6d2e3a;
        }

        footer ul li {
            margin-bottom: .5rem;
        }

        footer h6 {
            letter-spacing: 0.5px;
        }

        footer .fs-4 i {
            font-size: 1.5rem;
        }

        .footer-divider {
            border: 0;
            height: 1.5px;
            background-color: #6d2e3a; /* solid pink line */
            opacity: 0.6; /* optional, para soft lang. gawin 1 kung gusto mo intense */
            margin: 1rem 0;
        }

        .footer-brand {
            font-family: 'Meie Script', cursive;
            font-size: 6rem;
            letter-spacing: 1px;
            background: linear-gradient(90deg, #6d2e3a, #e8a9b2, #a95469);
            background-size: 200% auto;
            color: transparent;
            background-clip: text;
            animation: shine 4s linear infinite;
            text-shadow: 0 0 10px rgba(236, 73, 112, 0.3), 0 0 20px rgba(255, 200, 210, 0.4);
            -webkit-text-fill-color: transparent;
            -webkit-background-clip: text;
            animation: shine 4s linear infinite;
            backface-visibility: hidden;    /* IMPORTANT FIX */
            transform: translateZ(0);       /* Fix for Chrome glitch */
        }

        @keyframes shine {
            0% { background-position: 200% center; }
            100% { background-position: 0% center; }
        }

        .footer-brand:hover {
            transform: scale(1.03);
            text-shadow: 0 4px 12px rgba(236, 73, 112, 0.25);
        }
        
        #content {
            transition: opacity 0.3s ease;
        }
        #content.fade-out {
            opacity: 0.3;
        }

        #searchResultsWrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 35px;
        }

        #searchResultsGrid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center !important;
            gap: 60px !important; /* space between cards */
            max-width: 1200px; /* controls centering width */
        }

        /* KEEP CARD SIZE EXACTLY AS ORIGINAL */
        #searchResultsGrid .card {
            width: 220px !important;
            margin: 0;
            box-sizing: border-box;
            box-shadow: 0 -2px 20px rgba(0,0,0,0.05);
        }

        .small-swal {
            width: 320px !important;
            padding: 1.1rem !important;
            font-size: 0.98rem !important;
        }

        .swal2-title,
        .swal2-html-container {
            color: #6d2e3a !important; /* Match your theme pink */
            font-family: 'Poppins', sans-serif;
        }

        .swal2-popup {
            border-radius: 12px;
        }

        /* Kapag naliit na ang screen mag-adjust ang mga size at display ng button, etc. */
        @media (max-width: 1250px) {
            .navbar-toggler {
                order: -1;
                margin-right: auto;
            }
            .navbar .dual-btn {
                display: none !important;
            }
            .navbar .navbar-nav {
                display: none !important;
            }
            #sidebarToggle {
                display: block !important;
            }
            #searchResultsGrid {
                max-width: 900px;
            }
        }

        @media (max-width: 992px) {
            .brand-logo {
                max-height: 60px;
            }
            .brand-title {
                font-size: 2rem;
            }
            .brand-subtitle {
                font-size: 0.9rem;
            }
            .search-wrapper {
                max-width: 300px;
            }
            .btn-animated:hover { 
                transform: none !important; 
            }
            #searchResultsGrid {
                max-width: 600px;
            }
        }

        @media (max-width: 767px) {
            .brand-logo {
                max-height: 45px;
            }
            .brand-title {
                font-size: 1.5rem;
            }
            .brand-subtitle {
                font-size: 0.8rem;
            }
            .search-wrapper {
                max-width: 200px;
            }
            .card:hover .zoom-btn,
            .card:hover .add-to-cart-btn {
                opacity: 0 !important;
                pointer-events: none;
            }
            .zoom-btn {
                display: none !important;
            }
            .add-to-cart-btn {
                display: none !important;
            }
            .add-to-cart-btn-mobile {
                display: flex !important;
            }
            #addToCartModal .modal-box,
            #successModal .modal-box {
                width: 250px !important;              /* mas maliit sa screen width */
                padding: 15px;           /* bawas padding */
                border-radius: 10px;     /* softer corners */
                font-size: 14px;         /* smaller text */
            }
            #addToCartModal h3,
            #successModal h3 {
                font-size: 14px;
            }
            #addToCartModal button,
            #successModal button {
                font-size: 12px;
                padding: 8px 12px;
            }
            footer .text-md-end, footer .text-md-start {
                text-align: center !important;
            }
            footer .fs-4 {
                justify-content: center;
            }
            .footer-brand {
                font-size: 2.5rem;
            }
            .footer-content {
                display: none;
            }
            /* Make titles clickable */
            .footer-title {
                display: flex;
                justify-content: space-between;
                align-items: center;
                cursor: pointer;
                font-size: 16px !important;
            }
            .footer-title::after {
                font-family: "Font Awesome 6 Free";
                content: "\f078"; /* down chevron icon */
                font-weight: 900; /* depende sa FA icon style */
                transition: 0.3s;
            }
            .footer-title.active::after {
                transform: rotate(180deg);
            }
            .footer-section h6 {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 6px; /* maliit na space sa pagitan ng text at arrow */
                width: 100%;
                text-align: center;
            }
            /* Center dropdown contents */
            .footer-content {
                text-align: center !important;
            }
            .footer-content ul li {
                text-align: center;
            }
            .footer-content a {
                display: inline-block;
            }
        }

        @media (max-width: 576px) {
            .brand-logo {
                max-height: 45px;
            }
            .brand-title {
                font-size: 1.3rem;
            }
            .brand-subtitle {
                font-size: 0.6rem;
            }
            .navbar {
                flex-wrap: wrap;
            }
            .search-wrapper {
                display: none !important;
            }
            .mobile-search {
                display: block;
            }
            #searchResultsGrid {
                max-width: 320px;
            }
        }

        @media (max-width: 400px) {
            .navbar-brand {
                margin-left: 4px;
            }
        }
.footer-link {
  text-decoration: none;
  color: #6d2e3a;
  transition: color .2s ease, opacity .2s ease, transform .2s ease;
}
.footer-link:hover {
  color: #fea6e7;
  opacity: 0.85;
  transform: translateY(-1px);
}
footer {
  box-shadow: 0 -2px 20px rgba(0,0,0,0.05);
}
footer i {
  color: #6d2e3a;
}
footer ul li {
  margin-bottom: .5rem;
}
footer h6 {
  letter-spacing: 0.5px;
}
footer .fs-4 i {
  font-size: 1.5rem;
}

.footer-divider {
  border: 0;
  height: 1.5px;
  background-color: #6d2e3a; /* solid pink line */
  opacity: 0.6; /* optional, para soft lang. gawin 1 kung gusto mo intense */
  margin: 1rem 0;
}

@media (max-width: 768px) {
  footer .text-md-end, footer .text-md-start {
    text-align: center !important;
  }
  footer .fs-4 {
    justify-content: center;
  }
}

.footer-brand {
  font-family: 'Meie Script', cursive;
  font-size: 6rem;
  letter-spacing: 1px;
  background: linear-gradient(90deg, #6d2e3a, #f7a2b6, #a95469);
  background-size: 200% auto;
  color: transparent;
  background-clip: text;
  animation: shine 4s linear infinite;
  text-shadow: 0 0 10px rgba(236, 73, 112, 0.3), 0 0 20px rgba(255, 200, 210, 0.4);
}

@keyframes shine {
  0% { background-position: 200% center; }
  100% { background-position: 0% center; }
}


.footer-brand:hover {
  transform: scale(1.03);
  text-shadow: 0 4px 12px rgba(236, 73, 112, 0.25);
}

@media (max-width: 768px) {
  .footer-brand {
    font-size: 2.5rem;
  }
}
  #content {
  transition: opacity 0.3s ease;
}
#content.fade-out {
  opacity: 0.3;
}


/* MOBILE ACCORDION */
@media (max-width: 768px) {

   .footer-content {
      display: none;
      text-align: center !important;
  }

  .footer-title {
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      font-size: 16px !important;
      position: relative;
      width: 100%;
  }

  /* ▼ Triangle arrow */
  .footer-title::after {
      content: "";
      width: 0;
      height: 0;
      margin-left: 6px;
      border-left: 6px solid transparent;
      border-right: 6px solid transparent;
      border-top: 6px solid #6d2e3a;
      transition: transform 0.3s;
  }

  /* ▲ rotate when active */
  .footer-title.active::after {
      transform: rotate(180deg);
  }

  /* FIX BOX BUG — DO NOT USE FLEX HERE */
  .footer-section h6 {
      width: 100%;
      text-align: center;
  }

  .footer-content ul li {
      text-align: center;
  }

  .footer-content a {
      display: inline-block;
  }

  /* ------------ SPACING FIX (IMPORTANT) ------------ */

  /* Remove bottom margin that creates huge space */
  .footer .row.mb-5 {
    margin-bottom: 0 !important;
  }

  /* Remove gy-5 vertical gap completely */
  .footer .row.gy-5 {
    row-gap: 0 !important;
    gap: 0 !important;
  }

  /* Fix Bootstrap's automatic bottom spacing for each column */
  .footer .row > [class*='col-'] {
    margin-bottom: 0 !important;
    padding-top: 5px !important;
    padding-bottom: 5px !important;
  }

  /* Remove leftover margin from footer-section */
  .footer-section {
    margin: 0 !important;
    padding: 0 !important;
  }

  /* MOBILE-ONLY: Fix paragraph alignment WITHOUT touching desktop */
  .footer .footer-content p {
      text-align: center !important;
      line-height: 1.6 !important;
  }

  /* OPTIONAL: small text for mobile only */
  .footer .footer-content p {
      font-size: 14px !important;
      padding: 0 10px;
  }
}


/* 🌷 TOP SELLING — FINAL CLEAN VERSION */
/* Card Design */
.top-selling-card { 
  background: #fff;
  border-radius: 20px;
  width: 250px;
  height: 420px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 20px;
}

.top-selling-card img {
  height: 220px;
  width: 100%;
  object-fit: cover;
  border-radius: 12px;
}

.top-selling-card h5 {
  color: #6d2e3a;
  font-weight: bold;
  text-transform: uppercase;
  line-height: 20px;
  max-height: 40px;
  overflow: hidden;
  white-space: normal;
}

/* 🌸 MOBILE FADE SLIDESHOW (1 per slide) */
.top-selling-mobile {
  position: relative;
  height: 450px;
}

.mobile-fade-card {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;

  opacity: 0;
  transition: opacity 1s ease-in-out;

  display: flex;
  justify-content: center;
}

.mobile-fade-card.show {
  opacity: 1;
  z-index: 5;
}

/* 🌸 TABLET FADE SLIDESHOW (2 per slide) */
.top-selling-tablet {
  position: relative;
  height: 450px;
}

.tablet-fade-set {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;

  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.tablet-fade-set.show {
  opacity: 1;
  z-index: 5;
}

.tablet-fade-wrapper {
  display: flex;
  justify-content: center;
  gap: 40px;
}

/* Hide tablet slideshow on mobile & desktop */
@media (max-width: 767px) {
  .top-selling-tablet {
    display: none !important;
  }
}

@media (min-width: 992px) {
  .top-selling-tablet {
    display: none !important;
  }
}

/* Hide mobile slideshow on tablet & desktop */
@media (min-width: 768px) {
  .top-selling-mobile {
    display: none !important;
  }
}


/* HERO BANNER */
.beauty-hero {
    position: relative;
    width: 100%;
    height: 80vh;
    overflow: hidden;
}

.beauty-hero .hero-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 30%; /* DESKTOP: beautiful crop on face */
}

.hero-overlay-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    text-align: center;
    z-index: 10;
}

.hero-overlay-text h1 {
    font-size: 3.5rem;
    font-weight: 800;
}

.hero-overlay-text h2 {
    font-size: 2rem;
    font-style: italic;
    margin-top: -10px;
}

/* SPLIT ROW (DESKTOP) */
.beauty-row {
    position: relative;
    display: flex;
    width: 100%;
}

.beauty-left,
.beauty-right {
    width: 50%;
    aspect-ratio: 16 / 10;
    overflow: hidden;
    position: relative;
}

.beauty-row img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Overlay in the middle (desktop) */
.row-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 3rem;
    font-weight: 700;
    color: white;
    letter-spacing: 2px;
    z-index: 10;
}

/* TEXT BELOW */
.beauty-text {
    padding: 25px 20px;
    text-align: center;
    max-width: 900px;
    margin: auto;
}

.beauty-text p {
    font-size: 1rem;
    color: #6d2e3a;
    text-align: justify;          /* ← para pantay yung left & right */
    text-justify: inter-word;     /* optional, mas maayos spacing ng words */
    margin: 0 auto;               /* stay centered as a block */
}


/* ----------------------------------- */
/* MOBILE SETTINGS */
/* ----------------------------------- */
@media (max-width: 768px) {

    /* HERO — show full face, no zoom */
    .beauty-hero {
        height: 55vh;
    }

    .beauty-hero .hero-img {
        object-fit: cover;
        object-position: center 55%;  /* MOBILE: shows whole face */
        width: 100%;
        height: 100%;
    }

    /* HERO TEXT scaling */
    .hero-overlay-text {
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: 100%;
        padding: 0 10px;
        text-align: center;
    }

    .hero-overlay-text h1 {
        font-size: 2.2rem;
        line-height: 1.1;
    }

    .hero-overlay-text h2 {
        font-size: 1.4rem;
        margin-top: 5px;
    }

    /* ROW becomes stacked (1 image only) */
    .beauty-row {
        flex-direction: column;
    }

    /* Show only image 1,3,5 */
    .beauty-right {
        display: none;
    }

    .beauty-left {
        width: 100%;
        aspect-ratio: 1 / 1.3;
    }

    /* Overlay centered for mobile */
    .row-overlay {
        font-size: 2rem;
        top: 50%;
    }
}
/*footer sa baba yung pink*/
.footer-bottom-bg {
    background-color: #6d2e3a; /* pink background */
    width: 100vw;              /* FULL WIDTH kahit nasa loob ng footer */
    margin-left: 50%;          /* trick para i-center ang full width */
    transform: translateX(-50%);
    padding: 9px 15px;           /* manipis lang */
    border-radius: 0;          /* no rounded corners */
}

.footer-bottom-bg * {
    color: #fff !important;    /* white text & icons */
}

/* Shared NEW badge */
.badge-new {
  position: absolute;
  top: 10px;
  left: 10px;
  background: #6d2e3a;
  color: #fff;
  padding: 5px 10px;
  font-size: 12px;
  border-radius: 6px;
  font-weight: bold;
}

/* Card general */
.new-card {
  border-radius: 12px;
  min-height: 330px;
}

/* GRID SYSTEM */
.new-arrivals-grid {
  display: grid;
  justify-items: center;
  gap: 20px;
  width: 100%;
}

/* Desktop – 5 per row */
@media (min-width: 992px) {
  .new-arrivals-grid {
    grid-template-columns: repeat(5, 1fr);
  }

  .new-arrival-wrapper {
    max-width: 200px;
  }
}

/* Tablet – 3 per row */
@media (min-width: 768px) and (max-width: 991px) {
  .new-arrivals-grid {
    grid-template-columns: repeat(3, 1fr);
  }

  .new-arrival-wrapper {
    max-width: 200px;
  }
}

/* ⭐ NEW ARRIVALS — FINAL MOBILE CENTER FIX ⭐ */
@media (max-width: 767px) {

  /* Center slide content */
  .carousel-item {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
  }

  /* Remove grid effects */
  .new-arrivals-grid {
    display: none !important;
  }

  /* Clean mobile card */
  .new-card img {
    height: 180px !important;
  }
}
/* NEW ARRIVALS – FIX PRODUCT NAME HEIGHT */
.new-card h6 {
  height: 40px; 
  line-height: 20px;
  overflow: hidden;
  text-overflow: ellipsis;
}


/* Wrapper */
.new-arrival-wrapper {
  width: 100%;
}


.learn-more-btn {
  background: #6d2e3a;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  transition: 0.3s;
}

.learn-more-btn:hover {
  background: #d96d84;
}
/* ✨ PRODUCT HIGHLIGHT EFFECT  */
.highlight-product {
  animation: productFlash 1.5s ease-in-out;
  box-shadow: 0 0 0px rgba(236,118,153,0.6);
  border-radius: 12px;
}

@keyframes productFlash {
  0% {
    transform: scale(1);
    box-shadow: 0 0 0px rgba(236,118,153,0.6);
  }
  40% {
    transform: scale(1.06);
    box-shadow: 0 0 25px rgba(236,118,153,0.9);
  }
  60% {
    transform: scale(1.03);
    box-shadow: 0 0 18px rgba(236,118,153,0.7);
  }
  100% {
    transform: scale(1);
    box-shadow: 0 0 0px rgba(236,118,153,0.0);
  }
}

/* SECTION WRAPPER */
.promo-grid-section {
  padding: 20px 0;
  background: #ffffff;
  display: flex;
  justify-content: center;
}

/* MAIN DESKTOP GRID */
.promo-grid {
  display: grid;
  grid-template-columns: 1.3fr 1.8fr 1.3fr;
  gap: 0;
  width: 95%;
  max-width: 1400px;
}

/* LEFT + RIGHT 2x2 GRID */
.promo-left,
.promo-right {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0;
}

.promo-left .promo-box,
.promo-right .promo-box {
  height: 230px;
  aspect-ratio: unset !important;
}

/* CENTER BIG IMAGE */
.promo-center .promo-box.big {
  height: calc(230px * 2);
  position: relative;
  overflow: hidden;
  border-radius: 10px;
}

/* SHARED BOX STYLE */
.promo-box {
  position: relative;
  overflow: hidden;
}

.promo-box img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: .4s;
}

/* DESKTOP HOVER EFFECT */
@media (min-width: 768px) {
  .promo-box:hover img {
    filter: brightness(55%);
    transform: scale(1.08);
  }

  .promo-box:hover .promo-text {
    opacity: 1;
  }
}

/* TEXT OVERLAY */
.promo-text {
  position: absolute;
  inset: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #fff;
  text-align: center;
  padding: 10px;
  font-size: 18px;
  font-weight: bold;
  opacity: 0;
  transition: .4s;
  background: rgba(0,0,0,0.3);
}

/* CENTER BIG TEXT */
.big-text {
  font-size: 28px;
  font-weight: bold;
  padding: 20px;
  line-height: 1.3;
}

/* RESPONSIVE */
@media (max-width: 992px) {

  .promo-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .promo-left, .promo-right {
    grid-template-columns: 1fr 1fr;
  }

  .promo-center .promo-box.big {
    height: 400px !important;
  }

  /* MOBILE/TABLET → text hidden unless tapped */
  .promo-text {
    opacity: 0;
  }

  .promo-text.show {
    opacity: 1 !important;
  }
}

/* EXTRA SMALL */
@media (max-width: 600px) {

  .promo-left .promo-box,
  .promo-right .promo-box {
    height: 160px;
  }

  .promo-center .promo-box.big {
    height: 330px !important;
  }

  .big-text {
    font-size: 18px;
  }
}


</style>

</head>
<body style="background-color: #a95469;">
    <nav class="navbar sticky-top navbar-expand-lg" style="background-color: #fae6e7;">
        <div class="container-fluid flex-column">
            <div class="d-flex justify-content-between align-items-center">
                <button class="navbar-toggler d-lg-none me-1" style="background-color: #fae6e7; border: none; padding: 0.17rem 0.35rem;" type="button" id="sidebarToggle">
                    <i class="bi bi-list" style="font-size: 30px; color: #6d2e3a;"></i>
                </button>
                <a class="navbar-brand d-flex align-items-center">
                    <img src="logo13.png" alt="Logo" class="brand-logo me-2" style="border-radius: 50px;">
                    <div class="brand-text d-flex flex-column align-items-center">
                        <span class="brand-title">BEAUTY & BLESSED</span>
                        <span class="brand-subtitle">(Makeup Cosmetics Personal Care)</span>
                    </div>
                </a>
                <div class="flex-grow-1 d-flex justify-content-center px-3 search-wrapper">
                    <form class="d-flex ms-auto me-2 w-100" action="homepage.php" method="GET">
                        <div style="position: relative; width: 100%;">
                            <input type="search" placeholder="Search" aria-label="Search" class="form-control" name="q"
                                value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q'], ENT_QUOTES) : ''; ?>"
                                style="width: 100%; padding: 10px 45px 10px 10px; border-radius: 6px; border: 2px solid #6d2e3a; 
                                font-size: 16px; color: #6d2e3a; accent-color: #6d2e3a;">
                            <div style="position: absolute; right: 38px; top: 50%; transform: translateY(-50%); height: 60%;
                                width: 3px; background-color: #6d2e3a; opacity: 0.5;"></div>
                            <button type="submit" style="position: absolute; right: 5px; top: 50%;
                                transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                                <i class="bi bi-search" style="font-size: 20px; color: #6d2e3a;"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="d-none d-lg-block">
                    <?php if (!isset($_SESSION['user_email'])): ?>
                    <div class="d-none d-lg-block">
                        <button class="dual-btn fs-6 py-1 px-2">
                            <span id="signup" onclick="window.location.href='register.php'">Sign up</span>
                            <div class="divider"></div>
                            <span id="login" onclick="window.location.href='login.php'">Log in</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="d-none d-lg-block"></div>
                <?php endif; ?>

                </div>
            </div>
            <div class="mobile-search d-block d-sm-none w-100 px-2 mt-2">
                <form class="d-flex w-100" action="homepage.php" method="GET">
                    <div style="position: relative; width: 100%;">
                        <input class="form-control w-100" type="search" placeholder="Search" aria-label="Search" name="q"
                            value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q'], ENT_QUOTES) : ''; ?>"
                            style="border: 2px solid #6d2e3a; padding: 10px 45px 10px 12px; border-radius: 6px; font-size: 16px; color: #6d2e3a;">
                        <div style="position: absolute; right: 38px; top: 50%; transform: translateY(-50%); height: 60%; width: 2px;
                            background-color: #6d2e3a; opacity: 0.6; pointer-events: none;"></div>
                        <button type="submit" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%);
                            background: none; border: none; cursor: pointer;">
                            <i class="bi bi-search" style="font-size: 20px; color: #6d2e3a;"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="collapse navbar-collapse w-100 mt-2" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-1" style="margin-top: 10px;" id="myTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" style="color: #6d2e3a;" id="home-tab" data-bs-toggle="tab" 
                                data-bs-target="#home" role="tab" aria-controls="home" aria-selected="true">
                            HOME
                        </button>
                    </li>

                    <?php
                    include "database.php";
                    $catQuery = "SELECT * FROM category";
                    $catResult = mysqli_query($conn, $catQuery);
                    while ($cat = mysqli_fetch_assoc($catResult)) { ?>
                        <li class="nav-item" role="presentation">
                        <button class="nav-link" style="color: #6d2e3a;" id="tab-<?php echo $cat['category_id']; ?>" data-bs-toggle="tab" 
                                data-bs-target="#cat-<?php echo $cat['category_id']; ?>" type="button" role="tab">
                            <?php echo strtoupper($cat['category_name']); ?>
                        </button>
                        </li>
                    <?php } ?>

                    <!-- ✅ HIDDEN SEARCH TAB (DESKTOP) -->
                    <li class="nav-item d-none">
                        <button class="nav-link" style="color: #6d2e3a;"
                                id="search-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#search-results"
                                role="tab">
                            SEARCH
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="mobile-sidebar" id="mobileSidebar">
        <?php if (!isset($_SESSION['user_email'])): ?>
            <div class="auth-buttons d-lg-none">
                <button class="dual-btn fs-6 py-1 px-2" style="width: 160px; height: 35px;">
                    <span id="signup" onclick="window.location.href='register.php'">Sign up</span>
                    <div class="divider"></div>
                    <span id="login" onclick="window.location.href='login.php'">Log in</span>
                </button>
            </div>
        <?php endif; ?>

        <hr style="border: 2px solid #6d2e3a; margin: 15px 0;">

        <ul class="navbar-nav flex-grow-1">
            <li class="nav-item">
                <button class="nav-link active mb-0" style="color: #6d2e3a;" data-bs-toggle="tab" data-bs-target="#home">
                        HOME
                </button>
            </li>

            <?php 
            mysqli_data_seek($catResult, 0); // rewind result for reuse
            while ($cat = mysqli_fetch_assoc($catResult)) { ?>
                <li class="nav-item">
                    <button class="nav-link mb-0"
                            style="color:#6d2e3a;"
                            data-bs-toggle="tab"
                            data-bs-target="#cat-<?php echo $cat['category_id']; ?>">
                        <?php echo strtoupper($cat['category_name']); ?>
                    </button>
                </li>
            <?php } ?>

            <!-- ✅ HIDDEN SEARCH TAB (MOBILE) -->
            <li class="nav-item d-none">
                <button class="nav-link"
                        id="search-tab-mobile"
                        data-bs-toggle="tab"
                        data-bs-target="#search-results">
                    SEARCH
                </button>
            </li>
        </ul>
    </div>
    
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <main id="content">
        <div id="main-section">
            <div class="tab-content mt-3" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <p class="fs-4 text-center"  style="color: white;"><b>HOME</b></p>
                    <div id="featuredCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="https://image.slidesdocs.com/responsive-images/slides/0-beauty-and-makeup-product-introduction-powerpoint-background_e444745b92__960_540.jpg" class="d-block w-100" alt="Image 1">
                            </div>
                            <div class="carousel-item">
                                <img src="https://media.slidesgo.com/storage/53727814/responsive-images/makeup-products1715692208___media_library_original_783_440.jpg" class="d-block w-100" alt="Image 2">
                            </div>
                            <div class="carousel-item">
                                <img src="https://i.pinimg.com/originals/bb/c4/06/bbc40637adcfa93cfcbb3bb83902fdfa.png" class="d-block w-100" alt="Image 3">
                            </div>
                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
                </div>

                <div class="tab-content" id="myTabContent">
                    <?php 
                    mysqli_data_seek($catResult, 0);
                    while ($cat = mysqli_fetch_assoc($catResult)) { ?>
                    <div class="tab-pane fade" id="cat-<?php echo $cat['category_id']; ?>" role="tabpanel">
                        <center><p class="fs-4 mb-0" style="color: #fff ;"><b><?php echo strtoupper($cat['category_name']); ?></b></p></center>
                        <div class="container py-3 mt-0">
                            <div class="d-flex flex-wrap gap-10 justify-content-center">
                                <div class="position-relative" style="position: relative;">
                                    <div id="productCarousel<?php echo $cat['category_id']; ?>" class="carousel slide productCarousel" data-bs-interval="false">
                                        <div class="carousel-inner" id="productCarouselInner<?php echo $cat['category_id']; ?>">
                                            <?php
                                                include "database.php";
                                                $prodQuery = "SELECT * FROM products WHERE category_id = ".$cat['category_id'];
                                                $prodResult = mysqli_query($conn, $prodQuery);

                                                while ($row = mysqli_fetch_assoc($prodResult)) {
                                                    $imgPath = $row['image_path'];
                                                    if (!str_starts_with($imgPath, 'pictures/')) {
                                                        $imgPath = 'pictures/' . $imgPath;
                                                    }
                                                    if (empty($imgPath) || !file_exists($imgPath)) {
                                                        $imgPath = 'pictures/noimage.png';
                                                    }
                                                ?>
                                                <div class="card shadow-sm mx-2"
                                                    style="background-color: #fae6e7; width: 220px; border-radius: 10px; flex: 0 0 auto;"
                                                    data-product-id="<?php echo $row['product_id']; ?>"
                                                    data-category-id="<?php echo $row['category_id']; ?>">
                                                    <img src="<?php echo $row['image_path']; ?>" 
                                                        alt="<?php echo $row['product_name']; ?>" 
                                                        class="card-img-top"
                                                        style="height: 300px; object-fit: cover;">
                                                        <button class="zoom-btn" data-bs-toggle="modal" data-bs-target="#zoomModal" 
                                                            data-img="<?php echo $row['image_path']; ?>">
                                                            <i class="bi bi-zoom-in"></i>
                                                        </button>
                                                        <button class="add-to-cart-btn" style="background-color: #a95469; color: #fae6e7;"
                                                            data-product-id="<?php echo $row['product_id']; ?>"
                                                            data-product-price="<?php echo $row['price']; ?>"
                                                            data-product-name="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>"
                                                            data-is-logged-in="<?php echo (!empty($_SESSION['user_email']) ? '1' : '0'); ?>">
                                                            ADD TO CART
                                                        </button>
                                                    <div class="card-body d-flex flex-column align-items-center">
                                                        <p class="card-title mb-0 text-center" style="color: #6d2e3a;">
                                                            <?php echo $row['product_name']; ?>
                                                        </p>
                                                        <p class="card-text fw-bold mb-0 text-center" style="color: #646060ff;">
                                                            ₱<?php echo number_format($row['price'], 2); ?>
                                                        </p>
                                                        <button class="btn btn-xs add-to-cart-btn-mobile mt-2" 
                                                            style="background-color: #6d2e3a; color: #fae6e7"
                                                            data-product-id="<?php echo $row['product_id']; ?>"
                                                            data-product-price="<?php echo $row['price']; ?>"
                                                            data-product-name="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>"
                                                            data-is-logged-in="<?php echo (!empty($_SESSION['user_email']) ? '1' : '0'); ?>">
                                                            ADD TO CART
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <button class="carousel-control-prev custom-control" type="button"  data-bs-target="#productCarousel" data-bs-slide="prev" id="prevSlide<?php echo $cat['category_id']; ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </button>
                                        <button class="carousel-control-next custom-control" type="button"  data-bs-target="#productCarousel" data-bs-slide="next" id="nextSlide<?php echo $cat['category_id']; ?>">
                                             <i class="bi bi-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <!-- SEARCH RESULTS TAB -->
                <div class="tab-pane fade" id="search-results" role="tabpanel">
                    <?php
                    if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
                        include "database.php";
                        $search = mysqli_real_escape_string($conn, $_GET['q']);
                        echo "<h4 class='text-center mt-3 fw-bold' style='color: #6d2e3a; margin-bottom: 40px;'>Search results for <strong>\"$search\"</strong>...</h4>";

                        $prodQuery = "SELECT * FROM products WHERE product_name LIKE '$search%'";
                        $prodResult = mysqli_query($conn, $prodQuery);

                        if (mysqli_num_rows($prodResult) > 0) { ?>
                            <div id="searchResultsWrapper">
                                <div class="d-flex flex-wrap justify-content-start gap-3" id="searchResultsGrid">
                                    <?php while ($row = mysqli_fetch_assoc($prodResult)) {
                                        $imgPath = $row['image_path'];
                                        if (!str_starts_with($imgPath, 'pictures/')) $imgPath = 'pictures/' . $imgPath;
                                        if (empty($imgPath) || !file_exists($imgPath)) $imgPath = 'pictures/noimage.png';
                                    ?>
                                        <div class="card shadow-sm mx-2"
                                            style="background-color: #fae6e7; width: 220px; border-radius: 10px; flex: 0 0 auto;"
                                            data-product-id="<?php echo $row['product_id']; ?>"
                                            data-category-id="<?php echo $row['category_id']; ?>">
                                            <img src="<?php echo $imgPath; ?>" 
                                                alt="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>" 
                                                class="card-img-top">
                                            <!-- Zoom button -->
                                            <button class="zoom-btn" data-bs-toggle="modal" data-bs-target="#zoomModal" 
                                                    data-img="<?php echo $imgPath; ?>">
                                                <i class="bi bi-zoom-in"></i>
                                            </button>
                                            <!-- Desktop ADD TO CART -->
                                            <button class="add-to-cart-btn" style="background-color: #6d2e3a; color: #fae6e7"
                                                    data-product-id="<?php echo $row['product_id']; ?>"
                                                    data-product-price="<?php echo $row['price']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>"
                                                    data-is-logged-in="<?php echo (!empty($_SESSION['user_email']) ? '1' : '0'); ?>">
                                                ADD TO CART
                                            </button>
                                            <div class="card-body d-flex flex-column align-items-center">
                                                <p class="card-title mb-0 text-center" style="color: #6d2e3a;">
                                                    <?php echo $row['product_name']; ?>
                                                </p>
                                                <p class="card-text  mb-0 text-center" style="color: #6d2e3a;">
                                                    ₱<?php echo number_format($row['price'], 2); ?>
                                                </p>
                                                <!-- Mobile ADD TO CART -->
                                                <button class="btn btn-xs add-to-cart-btn-mobile mt-2" 
                                                        style="background-color: #a95469; color: #fff"
                                                        data-product-id="<?php echo $row['product_id']; ?>"
                                                        data-product-price="<?php echo $row['price']; ?>"
                                                        data-product-name="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>"
                                                        data-is-logged-in="<?php echo (!empty($_SESSION['user_email']) ? '1' : '0'); ?>">
                                                    ADD TO CART
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                    <?php
                        } else {
                            echo "<p class='text-center mt-4 fw-bold' style='color: #6d2e3a;'>No matching products found.</p>";
                        }
                    }
                    ?>

                </div>

              
<!-- 🌷 TOP SELLING SECTION --> 
<section class="py-5" style="background-color: #fff;">
  <div class="container text-center">

    <h1 class="fw-bold mb-4" style="color: #6d2e3a;">TOP SELLING</h1>

    <?php
      include 'database.php';
      $query = "SELECT 
                  p.product_id,
                  p.product_name,
                  p.price,
                  p.image_path,
                  i.sold_count
                FROM inventory i
                JOIN products p ON i.product_id = p.product_id
                ORDER BY i.sold_count DESC
                LIMIT 4";

      $result = $conn->query($query);
      $products = [];
      $maxSold = 0;

      if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $products[] = $row;
          if ($row['sold_count'] > $maxSold) $maxSold = $row['sold_count'];
        }
      }
    ?>

    <!-- 🌸 DESKTOP : 4 CARDS -->
    <div class="row justify-content-center d-none d-lg-flex">
      <?php foreach ($products as $row):
        $img = $row['image_path'];
        if (!str_starts_with($img, 'pictures/')) $img = 'pictures/' . $img;
        if (!file_exists($img)) $img = 'pictures/noimage.png';
        $rating = ($maxSold > 0) ? round(($row['sold_count'] / $maxSold) * 5) : 0;
      ?>
      <div class="col-lg-3 mb-4 d-flex justify-content-center">
        <div class="top-selling-card shadow-sm">
          <img src="<?= $img ?>" class="rounded mb-3">
          <h5><?= $row['product_name'] ?></h5>
          <p class="text-muted small mb-1">₱<?= number_format($row['price'],2) ?></p>
          <p class="text-muted small mb-2">Sold: <?= $row['sold_count'] ?></p>

          <div>
            <?php for ($i=1; $i<=5; $i++): ?>
              <i class="bi bi-star-fill" style="color:<?= $i <= $rating ? '#6d2e3a' : '#ddd'; ?>"></i>
            <?php endfor; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>


    <!-- 🌸 TABLET : FADE (2 per slide) -->
    <div class="top-selling-tablet d-none d-md-flex d-lg-none">

      <?php 
        $tabletSlides = array_chunk($products, 2);
        foreach ($tabletSlides as $slideIndex => $slideItems):
      ?>

      <div class="tablet-fade-set <?= $slideIndex == 0 ? 'show' : '' ?>">
        <div class="tablet-fade-wrapper">

          <?php foreach ($slideItems as $row):
            $img = $row['image_path'];
            if (!str_starts_with($img, 'pictures/')) $img = 'pictures/' . $img;
            if (!file_exists($img)) $img = 'pictures/noimage.png';
            $rating = ($maxSold > 0) ? round(($row['sold_count'] / $maxSold) * 5) : 0;
          ?>

          <div class="top-selling-card shadow-sm">
            <img src="<?= $img ?>" class="rounded mb-3">
            <h5><?= $row['product_name'] ?></h5>
            <p class="text-muted small mb-1">₱<?= number_format($row['price'],2) ?></p>
            <p class="text-muted small mb-2">Sold: <?= $row['sold_count'] ?></p>

            <div>
              <?php for ($i=1; $i<=5; $i++): ?>
                <i class="bi bi-star-fill" style="color:<?= $i <= $rating ? '#6d2e3a' : '#ddd'; ?>"></i>
              <?php endfor; ?>
            </div>
          </div>

          <?php endforeach; ?>

        </div>
      </div>

      <?php endforeach; ?>

    </div>


    <!-- 🌸 MOBILE : FADE (1 per slide) -->
    <div class="top-selling-mobile d-block d-md-none">

      <?php foreach ($products as $index => $row):
        $img = $row['image_path'];
        if (!str_starts_with($img, 'pictures/')) $img = 'pictures/' . $img;
        if (!file_exists($img)) $img = 'pictures/noimage.png';
        $rating = ($maxSold > 0) ? round(($row['sold_count'] / $maxSold) * 5) : 0;
      ?>

      <div class="mobile-fade-card <?= $index == 0 ? 'show' : '' ?>">
        <div class="top-selling-card shadow-sm">
          <img src="<?= $img ?>" class="rounded mb-3">
          <h5><?= $row['product_name'] ?></h5>
          <p class="text-muted small mb-1">₱<?= number_format($row['price'],2) ?></p>
          <p class="text-muted small mb-2">Sold: <?= $row['sold_count'] ?></p>

          <div>
            <?php for ($i=1; $i<=5; $i++): ?>
              <i class="bi bi-star-fill" style="color:<?= $i <= $rating ? '#6d2e3a' : '#ddd'; ?>"></i>
            <?php endfor; ?>
          </div>
        </div>
      </div>

      <?php endforeach; ?>

    </div>


  </div>
</section>

<hr class="line">

<!-- NEW ARRIVALS -->
<section class="py-5" style="background-color: #fff; ">
  <div class="container-fluid text-center px-4">

    <h1 class="fw-bold mb-4" style="color: #6d2e3a;">NEW ARRIVALS</h1>

    <?php
      include 'database.php';
      $query = "SELECT * FROM products ORDER BY product_id DESC LIMIT 10";
      $result = $conn->query($query);
      $newProds = [];
      while ($row = $result->fetch_assoc()) {
        $newProds[] = $row;
      }
    ?>

    <!-- DESKTOP -->
    <div id="newArrivalsDesktop"
         class="carousel slide carousel-fade d-none d-lg-block"
         data-bs-ride="carousel"
         data-bs-interval="2500">

      <div class="carousel-inner">
        <?php 
          $slide = 0;
          for ($i = 0; $i < count($newProds); $i += 5):
            $active = ($slide == 0) ? "active" : "";
        ?>
        <div class="carousel-item <?= $active ?>">
          <div class="new-arrivals-grid">

            <?php for ($j = $i; $j < $i + 5 && $j < count($newProds); $j++):
              $p = $newProds[$j];
              $img = $p['image_path'];
              if (!str_starts_with($img, 'pictures/')) $img = 'pictures/' . $img;
              if (!file_exists($img)) $img = 'pictures/noimage.png';
            ?>

            <div class="new-arrival-wrapper">
              <div class="card border-0 shadow-sm p-3 position-relative new-card">
                <span class="badge-new">NEW</span>
                <img src="<?= $img ?>" style="height:200px; width:100%; object-fit:cover;" class="rounded mb-3">
                <h6 class="fw-bold text-uppercase" style="color: #6d2e3a; font-size:14px;"><?= $p['product_name'] ?></h6>
                <p class="text-muted small mb-0">₱<?= number_format($p['price'],2) ?></p>
              <button class="learn-more-btn" 
        data-product-id="<?= $p['product_id'] ?>"
        data-category-id="<?= $p['category_id'] ?>">
   Go to Product
</button>

              </div>
            </div>

            <?php endfor; ?>

          </div>
        </div>
        <?php $slide++; endfor; ?>
      </div>
    </div>

<!-- TABLET -->
<div id="newArrivalsTablet"
     class="carousel slide carousel-fade d-none d-md-block d-lg-none"
     data-bs-ride="carousel"
     data-bs-interval="2000">

  <div class="carousel-inner">
    <?php 
      $slide = 0;
      for ($i = 0; $i < count($newProds); $i += 3):
        $active = ($slide == 0) ? "active" : "";
    ?>
    <div class="carousel-item <?= $active ?>">
      <div class="new-arrivals-grid">

        <?php for ($j = $i; $j < $i + 3 && $j < count($newProds); $j++):
          $p = $newProds[$j];
          $img = $p['image_path'];
          if (!str_starts_with($img, 'pictures/')) $img = 'pictures/' . $img;
          if (!file_exists($img)) $img = 'pictures/noimage.png';
        ?>

        <div class="new-arrival-wrapper">
          <div class="card border-0 shadow-sm p-3 position-relative new-card">
            <span class="badge-new">NEW</span>
            <img src="<?= $img ?>" style="height:200px; width:100%; object-fit:cover;" class="rounded mb-3">
            <h6 class="fw-bold text-uppercase" style="color: #6d2e3a; font-size:14px;"><?= $p['product_name'] ?></h6>
            <p class="text-muted small mb-0">₱<?= number_format($p['price'],2) ?></p>
        <button class="learn-more-btn" 
        data-product-id="<?= $p['product_id'] ?>"
        data-category-id="<?= $p['category_id'] ?>">
   Go to Product
</button>


          </div>
        </div>

        <?php endfor; ?>

      </div>
    </div>
    <?php $slide++; endfor; ?>
  </div>
</div>

<!-- MOBILE -->
<div id="newArrivalsMobile"
     class="carousel slide carousel-fade d-block d-md-none"
     data-bs-ride="carousel"
     data-bs-interval="2000">

  <div class="carousel-inner">

    <?php 
      foreach ($newProds as $index => $p):
        $img = $p['image_path'];
        if (!str_starts_with($img, 'pictures/')) $img = 'pictures/' . $img;
        if (!file_exists($img)) $img = 'pictures/noimage.png';
        $active = ($index == 0) ? "active" : "";
    ?>

    <div class="carousel-item <?= $active ?>">
      <div class="d-flex justify-content-center w-100">

        <div class="card border-0 shadow-sm p-3 position-relative new-card" style="max-width:260px; width:100%;">
          <span class="badge-new">NEW</span>
          <img src="<?= $img ?>" style="height:200px; width:100%; object-fit:cover;" class="rounded mb-3">
          <h6 class="fw-bold text-uppercase" style="color: #6d2e3a; font-size:14px;">
            <?= $p['product_name'] ?>
          </h6>
          <p class="text-muted small mb-0">₱<?= number_format($p['price'],2) ?></p>
          <button class="learn-more-btn" 
        data-product-id="<?= $p['product_id'] ?>"
        data-category-id="<?= $p['category_id'] ?>">
   Go to Product
</button>


        </div>

      </div>
    </div>

    <?php endforeach; ?>

  </div>
</div>

  </div>
</section>

<!-- PICTURE SECTION -->
<section class="promo-grid-section">
  <div class="promo-grid">

    <div class="promo-left">
      <div class="promo-box"><img src="img1.jpg"><div class="promo-text">Unleash your bold beauty.</div></div>
      <div class="promo-box"><img src="photo21.jpg"><div class="promo-text">Define your eyes with timeless glam.</div></div>
      <div class="promo-box"><img src="img3.jpg"><div class="promo-text">Glow like never before.</div></div>
      <div class="promo-box"><img src="img4.jpg"><div class="promo-text">Radiance that lasts all day.</div></div>
    </div>

    <div class="promo-center">
      <div class="promo-box big">
        <img src="photo1.jpg">
        <div class="promo-text big-text">
          SUPERCHARGED <br> Green Tea Ceramide Milk
        </div>
      </div>
    </div>

    <div class="promo-right">
      <div class="promo-box"><img src="photo2.jpg"><div class="promo-text">Beauty made effortless.</div></div>
      <div class="promo-box"><img src="photo5.jpg"><div class="promo-text">Blend. Build. Glow.</div></div>
      <div class="promo-box"><img src="photo8.jpg"><div class="promo-text">Your shade, your story.</div></div>
      <div class="promo-box"><img src="photo4.jpg"><div class="promo-text">Confidence starts with flawless skin.</div></div>
    </div>

  </div>
</section>


            </div>

            <div class="modal fade" id="zoomModal" tabindex="-1" aria-labelledby="zoomModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="background-color: #6d2e3a; border: none;">
                        <div class="modal-body p-0 position-relative">
                            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                            <img id="zoomImage" src="" class="img-fluid w-100 rounded" alt="Zoomed Image">
                            <button type="button" class="btn btn-light btn-sm position-absolute zoom-out-btn"
                                    data-bs-dismiss="modal" aria-label="Close">
                                <i class="bi bi-zoom-out"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Required Modal -->
            <div class="modal" id="loginPromptModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);
                    display: none; justify-content: center; align-items: center; z-index: 9999;">
                <div style="background: white; width: 380px; max-width: 90%; border-radius: 12px; 
                        overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.25);">
                    <div style="background: #fae6e7; color: #6d2e3a; padding: 14px 18px;
                            display: flex; justify-content: space-between; align-items: center; height: 60px;">
                        <h3 style="margin: 0; font-size: 20px;"><strong>Login Required</strong></h3>
                        <button type="button" onclick="closeLoginPrompt()"
                                style="background: none; border: none; font-size: 30px; color: #6d2e3a; cursor: pointer;">
                            &times;
                        </button>
                    </div>
                    <div style="padding: 22px; text-align: center; color: #6d2e3a;">
                        <p style="margin-bottom:18px;">
                            You must login or sign up first before adding items to cart.
                        </p>
                        <a href="register.php" style="text-decoration: none;">
                            <button style="width: 50%; background: #6d2e3a; color: white;
                                        border: none; padding: 10px 0; border-radius: 6px;
                                        font-size: 16px; cursor: pointer;">
                                Sign Up
                            </button>
                        </a>
                        <p style="margin-top: 18px; color: #6d2e3a;">
                            Already have an account?
                            <a href="login.php" style="color: #6d2e3a; font-weight: bold;">Login</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Add-to-Cart Modal (card style) -->
            <div id="addToCartModal" class="modal-overlay" style="display: none;">
                <div class="modal-box" style="width: 380px; max-width: 95%;">
                    <h4 id="modalProductName" style="margin-bottom: 8px;"></h4>

                    <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin: 10px 0;">
                        <input id="qtyInput" type="number" min="1" value="1" style="width: 70px; text-align: center; padding: 6px; border: 1px solid #6d2e3a; border-radius: 6px;">
                    </div>

                    <div style="margin-top: 8px;">
                        <p style="margin: 0 0 8px 0;">Total: <strong id="modalTotal">₱0.00</strong></p>
                    </div>

                    <div style="display: flex; gap: 8px; margin-top: 12px;">
                        <button id="confirmAddBtn" class="btn-modal" style="flex: 1;">ADD</button>
                        <button id="cancelAddBtn" class="btn-modal" style="flex: 1;">CANCEL</button>
                    </div>
                </div>
            </div>

            <div id="successModal" class="modal-overlay" style="display: none;">
                <div class="modal-box" style="width: 200px;">
                    <h5 style="color: #6d2e3a; margin-bottom: 8px;">Items successfully added to your cart!</h4>
                    <button id="closeSuccessBtn" class="btn-modal" 
                            style="background: #6d2e3a; color: white; height: 20px; width: 40px; border-radius: 6px; font-size: 10px;">
                        OK
                    </button>
                </div>
            </div>
        
        </div>
        <div id="info-section" style="display: none;"></div>
    </main>

    <footer class="footer mt-5 pt-5 text-md-start" style="background-color: #fae6e7; font-family:'Poppins', sans-serif;">
        <div class="container" style="max-width:1180px;">

            <div class="row gy-5 gx-lg-5 mb-5">
                <div class="col-lg-4 col-md-6 footer-section">
                    <h6 class="fw-bold mb-3 text-uppercase footer-title" style="color: #6d2e3a;">BEAUTY & BLESSED</h6>

                    <div class="footer-content">
                        <p class="small mb-0" style="color: #6d2e3a; opacity:0.9; text-align:justify; line-height:1.9;">
                            At <strong>Beauty & Blessed</strong>, we believe beauty should be kind to skin,
                            people, and the planet. Every product we make is designed to bring confidence,
                            care, and joy to your everyday routine.
                        </p>
                    </div>
                </div>

                <!-- ABOUT US -->
                <div class="col-lg-2 col-md-6 footer-section">
                    <h6 class="fw-bold mb-3 text-uppercase footer-title" style="color: #6d2e3a;">ABOUT US</h6>

                    <div class="footer-content">
                        <ul class="list-unstyled small mb-0" style="line-height:2;">
                            <li><a href="#" class="footer-link ajax-link" data-page="ourstory.php">Our Story</a></li>
                            <li><a href="#" class="footer-link ajax-link" data-page="privacy.php">Privacy Policy</a></li>
                            <li><a href="#" class="footer-link ajax-link" data-page="terms.php">Terms & Conditions</a></li>
                        </ul>
                    </div>
                </div>

                <!-- SUPPORT -->
                <div class="col-lg-2 col-md-6 footer-section">
                    <h6 class="fw-bold mb-3 text-uppercase footer-title" style="color: #6d2e3a;">SUPPORT</h6>

                    <div class="footer-content">
                        <ul class="list-unstyled small mb-0" style="line-height:2;">
                            <li><a href="#" class="footer-link ajax-link" data-page="findus.php">Find Us</a></li>
                            <li><a href="#" class="footer-link ajax-link" data-page="faq.php">FAQ</a></li>
                        </ul>
                    </div>
                </div>

                <!-- OUR PROMISE -->
                <div class="col-lg-4 col-md-6 footer-section">
                    <h6 class="fw-bold mb-3 text-uppercase footer-title" style="color: #6d2e3a;">OUR PROMISE</h6>

                    <div class="footer-content">
                        <p class="small mb-0" style="color: #6d2e3a; opacity:0.9; text-align:justify; line-height:1.9;">
                            We’re committed to crafting cruelty-free, skin-safe, and inclusive
                            products made with care because beauty should always feel kind.
                        </p>
                    </div>
                </div>
            </div>

          <hr class="footer-divider">

    <!-- Center Brand -->
    <div class="text-center mb-4">
      <h1 class="footer-brand">Beauty & Blessed</h1>
    </div>

    <!-- Bottom -->
    <!-- FULL-WIDTH FOOTER BOTTOM -->
<div class="footer-bottom-full">
  <div class="footer-bottom-bg d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pb-3 pt-3">

    <div class="text-center text-md-start small fw-bold">
            © 2025 <strong>Beauty & Blessed</strong>. All rights reserved.
    </div>

  <div class="text-center text-md-end" style="margin-right:20px;">
  <h6 class="fw-bold mb-3 text-uppercase">CONNECT WITH US</h6>

  <div class="d-flex justify-content-md-end justify-content-center gap-4 fs-4">

    <!-- FACEBOOK -->
    <a href="https://www.facebook.com/beautyandblessedbatangas" 
       class="footer-link" 
       target="_blank" 
       aria-label="Facebook">
       <i class="bi bi-facebook"></i>

    </a>

    <!-- EMAIL -->
    <a href="mailto:beautyandblessed@gmail.com" 
   onclick="if(navigator.userAgent.match(/Android|iPhone|iPad/i)){ this.href='https://mail.google.com/mail/?view=cm&fs=1&to=beautyandblessed@gmail.com'; }" 
   class="footer-link">
   <i class="bi bi-envelope-fill"></i>

</a>


    <!-- PHONE -->
    <a href="tel:09669445591" 
       class="footer-link" 
       aria-label="Call">
   <i class="bi bi-telephone-fill"></i>

    </a>

  </div>
</div>

  </div>
    </div>

  </div>
</footer>

    <script src="bootstrap5/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

// SCIRPT PARA SA PICTURE NG MOBILE NA NAWWLA AFTER 2SECS

document.querySelectorAll('.promo-box').forEach(box => {
  box.addEventListener('click', () => {

    if (window.innerWidth <= 992) {  // mobile & tablet only

      const text = box.querySelector('.promo-text');

      // Show text
      text.classList.add('show');

      // Remove after 2 seconds
      setTimeout(() => {
        text.classList.remove('show');
      }, 2000);

    }

  });
});
// ETO NAMAN PARA MAPUNTA SA CARETGORY SA TAAS AT MA HIGHLIGHT

   document.querySelectorAll(".learn-more-btn").forEach(btn => {
  btn.addEventListener("click", function() {

    let prodID = this.dataset.productId;
    let catID  = this.dataset.categoryId;

    // Switch tab
    let tabBtn = document.querySelector(`#tab-${catID}`);
    tabBtn.click();

    setTimeout(() => {
      let card = document.querySelector(`[data-product-id="${prodID}"]`);
      if (card) {
        card.classList.add("highlight-product");

        // scroll to the card smoothly
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // remove highlight after animation
        setTimeout(() => {
          card.classList.remove("highlight-product");
        }, 1800);
      }
    }, 400); // small delay to let tab open fully

  });
}); 
function openCategory(catId) {
    // Activate the correct tab
    document.querySelector(`#tab-${catId}`).click();

    // Scroll to top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

  
// 🌸 MOBILE FADE
let mobileSlides = document.querySelectorAll('.mobile-fade-card');
let mobileIndex = 0;

setInterval(() => {
  mobileSlides[mobileIndex].classList.remove('show');
  mobileIndex = (mobileIndex + 1) % mobileSlides.length;
  mobileSlides[mobileIndex].classList.add('show');
}, 2000);


// 🌸 TABLET FADE
let tabletSlides = document.querySelectorAll('.tablet-fade-set');
let tabIndex = 0;

setInterval(() => {
  tabletSlides[tabIndex].classList.remove('show');
  tabIndex = (tabIndex + 1) % tabletSlides.length;
  tabletSlides[tabIndex].classList.add('show');
}, 2000);

// ETO NAMAN AY SA FOOTER NATIN , NAVIGATION, SUPPORT US ETC. PAG NAKA DESKTOP SHOW ALL, KAPAG NAKA MOBILE NAKA DOWPDOWN
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".footer-title").forEach(title => {
        title.addEventListener("click", () => {

            if (window.innerWidth > 768) return; // desktop unaffected

            const content = title.nextElementSibling;
            const isOpen = content.style.display === "block";

            // close all
            document.querySelectorAll(".footer-content").forEach(c => c.style.display = "none");
            document.querySelectorAll(".footer-title").forEach(t => t.classList.remove("active"));

            if (!isOpen) {
                content.style.display = "block";
                title.classList.add("active");
            }
        });
    });
});




/* 🌸 AJAX Page Loader */

document.addEventListener("DOMContentLoaded", () => {
  const infoSection = document.getElementById("info-section");
  const mainSection = document.getElementById("main-section");

  function loadPage(url) {
    fetch(url)
      .then(res => res.text())
      .then(data => {
        // Show AJAX page
        mainSection.style.display = "none";
        infoSection.innerHTML = data;
        infoSection.style.display = "block";
        window.scrollTo({ top: 0, behavior: "smooth" });
      })
      .catch(err => console.error("Error loading page:", err));
  }

  // AJAX Links (footer)
  document.querySelectorAll(".ajax-link").forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();
      loadPage(link.dataset.page);
    });
  });

  // MOBILE + DESKTOP TAB FIX
  document.addEventListener("click", function(e) {
    const btn = e.target.closest(".nav-link");

    // Skip unrelated clicks
  if (!btn || !btn.getAttribute("data-bs-target")) return;


    // ALWAYS return to homepage content
    infoSection.style.display = "none";
    mainSection.style.display = "block";

    // Activate correct tab
    const targetID = btn.dataset.bsTarget;
    const tabTrigger = document.querySelector(`button[data-bs-target="${targetID}"]`);

    if (tabTrigger) {
      const tab = new bootstrap.Tab(tabTrigger);
      tab.show();
    }

    // Hide mobile sidebar
    document.getElementById("mobileSidebar")?.classList.remove("show");
    document.getElementById("sidebarBackdrop")?.classList.remove("active");
  });
});




        const sidebarToggle = document.getElementById("sidebarToggle");
        const mobileSidebar = document.getElementById("mobileSidebar");
        const sidebarBackdrop = document.getElementById("sidebarBackdrop");

        sidebarToggle.addEventListener("click", () => {
            mobileSidebar.classList.toggle("show");
            sidebarBackdrop.classList.toggle("active");
        });

        sidebarBackdrop.addEventListener("click", () => {
            mobileSidebar.classList.remove("show");
            sidebarBackdrop.classList.remove("active");
        });

        //Close ang sidebar kapag nag-click ng nav-item or kapag pinindot ang outer part ng sidebar
        document.querySelectorAll('.mobile-sidebar .nav-link').forEach(link => {
            link.addEventListener("click", () => {
                mobileSidebar.classList.remove("show");
                sidebarBackdrop.classList.remove("active");
            });
        });

        document.querySelectorAll('.mobile-sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                // Remove 'active' sa ibang nav-links
                document.querySelectorAll('.mobile-sidebar .nav-link').forEach(item => item.classList.remove('active'));

                // Add 'active' sa na-clicked na nav-link
                this.classList.add('active');

                //Pagdidisplay ng mga tab-pane
                const target = link.getAttribute("data-target");
                if (target) {
                    const desktopBtn = document.querySelector(`#myTab .nav-link[data-bs-target="${target}"]`);

                    if (desktopBtn) {
                        const tab = new bootstrap.Tab(desktopBtn);
                        tab.show();
                    }
                }

                mobileSidebar.classList.remove("show");
                sidebarBackdrop.classList.remove("active");
            });
        });

        window.addEventListener("resize", () => {
            if (window.innerWidth >= 992) { 
                mobileSidebar.classList.remove("show");
                sidebarBackdrop.classList.remove("active");
            }
        });

        document.addEventListener("DOMContentLoaded", function () {
            function setupCarousel(carouselInnerId, prevBtnId, nextBtnId) {
                const carouselInner = document.getElementById(carouselInnerId);
                const items = Array.from(carouselInner.children);
                const prevBtn = document.getElementById(prevBtnId);
                const nextBtn = document.getElementById(nextBtnId);

                let currentIndex = 0;

                function getItemsPerSlide() {
                    if (window.innerWidth >= 1200) return 4;
                    if (window.innerWidth >= 992) return 3;
                    if (window.innerWidth >= 768) return 2;
                    return 1;
                }

                function createSlides() {
                    const itemsPerSlide = getItemsPerSlide();
                    const totalSlides = Math.ceil(items.length / itemsPerSlide);

                    carouselInner.innerHTML = "";

                    for (let i = 0; i < totalSlides; i++) {
                        const slide = document.createElement("div");
                        slide.classList.add("carousel-item");
                        if (i === 0) slide.classList.add("active");

                        const wrapper = document.createElement("div");
                        wrapper.classList.add(
                            "d-flex",
                            "justify-content-center",
                            "align-items-stretch",
                            "flex-nowrap"
                        );
                        wrapper.style.padding = "20px 0";
                        wrapper.style.gap = "60px"; // ✅ more spacing

                        const start = i * itemsPerSlide;
                        const end = start + itemsPerSlide;

                        items.slice(start, end).forEach(item => {
                            item.style.display = "block";
                            wrapper.appendChild(item);
                        });

                        slide.appendChild(wrapper);
                        carouselInner.appendChild(slide);
                    }

                    currentIndex = 0;
                    updateButtons();
                }

                function updateButtons() {
                    const slides = carouselInner.querySelectorAll(".carousel-item");

                    if (slides.length <= 1) {
                        prevBtn.style.opacity = "0.3";
                        nextBtn.style.opacity = "0.3";
                        prevBtn.style.pointerEvents = "none";
                        nextBtn.style.pointerEvents = "none";
                    } else {
                        prevBtn.style.opacity = "1";
                        nextBtn.style.opacity = "1";
                        prevBtn.style.pointerEvents = "auto";
                        nextBtn.style.pointerEvents = "auto";
                    }
                }

                window.addEventListener("resize", createSlides);

                nextBtn.addEventListener("click", () => {
                    const slides = carouselInner.querySelectorAll(".carousel-item");
                    if (slides.length <= 1) return;

                    slides[currentIndex].classList.remove("active");
                    currentIndex = (currentIndex + 1) % slides.length;
                    slides[currentIndex].classList.add("active");
                });

                prevBtn.addEventListener("click", () => {
                    const slides = carouselInner.querySelectorAll(".carousel-item");
                    if (slides.length <= 1) return;

                    slides[currentIndex].classList.remove("active");
                    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                    slides[currentIndex].classList.add("active");
                });

                createSlides();
            }

            for (let i = 1; i <= 11; i++) {
                setupCarousel(`productCarouselInner${i}`, `prevSlide${i}`, `nextSlide${i}`);
            }
        });

        function closeLoginPrompt() {
            document.getElementById("loginPromptModal").style.display = "none";
        }

        //Zoom in and out ng mga image
        document.addEventListener("DOMContentLoaded", function () {
            const zoomButtons = document.querySelectorAll(".zoom-btn");
            const zoomImage = document.getElementById("zoomImage");

            zoomButtons.forEach(btn => {
                btn.addEventListener("click", function () {
                    const imgSrc = this.getAttribute("data-img");
                    zoomImage.src = imgSrc;
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get("q");

            if (searchQuery) {
                const desktopTab = document.getElementById("search-tab");
                if (desktopTab) desktopTab.click();

                const mobileTab = document.getElementById("search-tab-mobile");
                if (mobileTab) mobileTab.click();

                // Remove ?q=... from URL
                window.history.replaceState({}, document.title, "homepage.php");
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const navButtons = document.querySelectorAll('#myTab button.nav-link');

            navButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const searchResultsTab = document.getElementById('search-results');
                    if (searchResultsTab) {
                        // Itago ang search results content
                        searchResultsTab.classList.remove('show', 'active');
                        searchResultsTab.innerHTML = ""; // optional: para malinis talaga

                        // Alisin din ang 'active' state ng hidden search-tab
                        const searchTabBtn = document.getElementById('search-tab');
                        if (searchTabBtn) searchTabBtn.classList.remove('active');

                        // I-clear din ang search input field
                        document.querySelectorAll('input[type="search"]').forEach(inp => inp.value = "");
                    }
                });
            });
        });
        
        // ✅ Real-time search suggestions + highlight on click
        document.addEventListener("DOMContentLoaded", function() {
            const searchInputs = document.querySelectorAll('input[type="search"]');

            searchInputs.forEach(input => {
                const suggestionBox = document.createElement("div");
                suggestionBox.style.position = "absolute";
                suggestionBox.style.background = "white";
                suggestionBox.style.color = "#6d2e3a";
                suggestionBox.style.border = "2px solid #6d2e3a";
                suggestionBox.style.borderTop = "none";
                suggestionBox.style.width = input.offsetWidth + "px";
                suggestionBox.style.maxHeight = "200px";
                suggestionBox.style.overflowY = "auto";
                suggestionBox.style.zIndex = "9999";
                suggestionBox.style.display = "none";
                suggestionBox.style.fontSize = "14px";
                suggestionBox.style.borderRadius = "0 0 8px 8px";
                suggestionBox.style.boxShadow = "0 4px 8px rgba(0,0,0,0.1)";
                suggestionBox.style.cursor = "pointer";

                document.body.appendChild(suggestionBox);

                function positionBox() {
                    const rect = input.getBoundingClientRect();
                    suggestionBox.style.left = rect.left + window.scrollX + "px";
                    suggestionBox.style.top = rect.bottom + window.scrollY + "px";
                    suggestionBox.style.width = rect.width + "px";
                }

                window.addEventListener("resize", positionBox);
                input.addEventListener("focus", positionBox);

                // 🟣 Highlight matching letters
                function highlightMatch(text, query) {
                    const regex = new RegExp(`(${query})`, "gi");
                    return text.replace(regex, "<strong style='color:#ec7699;'>$1</strong>");
                }

                // 🟣 Fetch suggestions from DB in real-time
                input.addEventListener("input", function() {
                    const query = this.value.trim();

                    if (query.length === 0) {
                        suggestionBox.style.display = "none";
                        return;
                    }

                    fetch(`search.php?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            suggestionBox.innerHTML = "";
                            if (data.length === 0) {
                                suggestionBox.style.display = "none";
                                return;
                            }

                            data.forEach(item => {
                                const div = document.createElement("div");
                                div.innerHTML = highlightMatch(item.product_name, query);
                                div.style.padding = "6px 10px";
                                div.style.borderBottom = "1px solid #f8d7d6";

                                div.addEventListener("mouseover", () => div.style.background = "#fae6e7");
                                div.addEventListener("mouseout", () => div.style.background = "white");

                                // 🖱️ kapag na-click ang suggestion
                                div.addEventListener("click", () => {
                                    suggestionBox.style.display = "none";
                                    input.value = item.product_name;

                                    // 1️⃣ OPEN THE CATEGORY TAB
                                    const categoryTab = document.querySelector(`#myTab button[data-bs-target="#cat-${item.category_id}"]`);
                                    if (categoryTab) {
                                        const tab = new bootstrap.Tab(categoryTab);
                                        tab.show();
                                    }

                                    // 2️⃣ DELAY para sure loaded ang carousel
                                    setTimeout(() => {

                                        // Hanapin ang product card sa DOM
                                        const targetCard = document.querySelector(`.card[data-product-id="${item.product_id}"]`);
                                        if (!targetCard) return;

                                        // Hanapin ang carousel inner ng category
                                        const carouselInner = targetCard.closest(".carousel-inner");
                                        if (!carouselInner) return;

                                        const slides = carouselInner.querySelectorAll(".carousel-item");

                                        // 3️⃣ Find which slide contains the card
                                        let targetSlideIndex = 0;
                                        slides.forEach((slide, index) => {
                                            if (slide.contains(targetCard)) {
                                                targetSlideIndex = index;
                                            }
                                        });

                                        // 4️⃣ Go to the correct slide
                                        slides.forEach(s => s.classList.remove("active"));
                                        slides[targetSlideIndex].classList.add("active");

                                        // 5️⃣ Scroll + highlight effect
                                        setTimeout(() => {
                                            targetCard.classList.add("highlight-product");
                                            targetCard.scrollIntoView({ behavior: "smooth", block: "center" });

                                            setTimeout(() => {
                                                targetCard.classList.remove("highlight-product");
                                            }, 2500);
                                        }, 300);
                                    }, 600);
                                });

                                suggestionBox.appendChild(div);
                            });

                            suggestionBox.style.display = "block";
                            positionBox();
                        })
                    .catch(err => {
                        console.error("Search error:", err);
                        suggestionBox.style.display = "none";
                    });
                });

                // 🟣 Hide suggestions if clicking outside
                document.addEventListener("click", function(e) {
                    if (!suggestionBox.contains(e.target) && e.target !== input) {
                        suggestionBox.style.display = "none";
                    }
                });
            });
        });

        // --------------------------------------------------
        // 1. INSTANT JUMP TO A SPECIFIC PRODUCT SLIDE
        // --------------------------------------------------
        function goToProductInstant(carouselId, productIndex, itemsPerSlide = 4) {
            const carouselElement = document.getElementById(carouselId);
            if (!carouselElement) return;

            let bsCarousel = bootstrap.Carousel.getInstance(carouselElement);
            if (!bsCarousel) {
                bsCarousel = new bootstrap.Carousel(carouselElement, {
                    interval: false,
                    ride: false,
                    wrap: true
                });
            }

            const slideIndex = Math.floor(productIndex / itemsPerSlide);
            const inner = carouselElement.querySelector(".carousel-inner");

            // 🔹 Step 1: Hide carousel temporarily
            carouselElement.style.visibility = "hidden";

            // 🔹 Step 2: Remove transition & jump instantly
            inner.style.transition = "none";
            bsCarousel.to(slideIndex);

            // 🔹 Step 3: Restore visibility & transition
            setTimeout(() => {
                inner.style.transition = "";
                carouselElement.style.visibility = "visible";
            }, 50);
        }

        // --------------------------------------------------
        // 2. SWITCH TO THE CORRECT TAB BEFORE SLIDING
        // --------------------------------------------------
        function openCategoryTab(category) {
            const tabBtn = document.querySelector(`[data-bs-target="#${category}"]`);
            if (tabBtn) tabBtn.click();
        }


        // --------------------------------------------------
        // 3. HANDLE CLICK ON SEARCH SUGGESTION
        // --------------------------------------------------
        document.addEventListener("click", function (e) {

            // Only run when clicking a suggestion item
            if (e.target.classList.contains("suggestion-item")) {

                const productIndex = parseInt(e.target.dataset.index);   // pang-ilang product
                const category = e.target.dataset.category;              // category ID
                const carouselId = e.target.dataset.carousel;            // carousel ID (ex: "carousel_5")

                // Step 1 — Open the category tab
                openCategoryTab(category);

                // Step 2 — Allow tab to load first
                setTimeout(() => {

                    // Jump instantly without showing earlier slides
                    goToProductInstant(carouselId, productIndex, 4);

                }, 300);

            }
        });

        document.addEventListener("DOMContentLoaded", function () {
            const addToCartButtons = document.querySelectorAll(".add-to-cart-btn, .add-to-cart-btn-mobile");
            const addToCartModal = document.getElementById("addToCartModal");
            const loginPromptModal = document.getElementById("loginPromptModal");

            const modalProductName = document.getElementById("modalProductName");
            const qtyInput = document.getElementById("qtyInput");
            const modalTotal = document.getElementById("modalTotal");
            const confirmAddBtn = document.getElementById("confirmAddBtn");
            const cancelAddBtn = document.getElementById("cancelAddBtn");

            let selectedPrice = 0;
            let selectedProductId = null;

            // 🔹 When user clicks "Add to Cart" button
            addToCartButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const isLoggedIn = this.dataset.isLoggedIn === "1";

                    if (!isLoggedIn) {
                        // ❌ Show login prompt modal if not logged in
                        loginPromptModal.style.display = "flex";
                        return;
                    }

                    // ✅ Show Add to Cart modal if logged in
                    const productName = this.dataset.productName;
                    selectedPrice = parseFloat(this.dataset.productPrice);
                    selectedProductId = this.dataset.productId; // store product ID

                    modalProductName.textContent = productName;
                    qtyInput.value = 1;
                    modalTotal.textContent = "₱" + selectedPrice.toFixed(2);
                    addToCartModal.style.display = "flex";
                });
            });

            // 🧮 Auto-update total price when quantity changes
            qtyInput.addEventListener("input", function () {
                const qty = parseInt(this.value) || 1;
                modalTotal.textContent = "₱" + (selectedPrice * qty).toFixed(2);
            });

            // 🛒 Confirm add to cart
            if (confirmAddBtn) {
                confirmAddBtn.addEventListener("click", function () {
                    const quantity = qtyInput.value;

                    fetch("add_cart.php", {
                        method: "POST",
                        body: new URLSearchParams({
                            confirmAddBtn: true,
                            product_id: selectedProductId,
                            quantity
                        })
                    })
                    .then(res => res.text())
                    .then(msg => {
                        addToCartModal.style.display = "none";

                        // SweetAlert logic + styles
                        const swalOptions = {
                            icon: '',
                            title: '',
                            text: msg,
                            customClass: {
                                popup: 'small-swal'
                            },
                            showConfirmButton: true,
                            confirmButtonColor: '#6d2e3a',
                        };

                        if (
                            msg.includes("Please enter a valid quantity") ||
                            msg.includes("You cannot add this item") ||
                            msg.includes("Not enough stock") ||
                            msg.includes("Product not found") ||
                            msg.includes("You must be logged in.") ||
                            msg.includes("total quantity after update")
                        ) {
                            swalOptions.icon = 'error';
                            swalOptions.title = 'Oops!';
                            swalOptions.confirmButtonColor = '#6d2e3a';
                        } else {
                            swalOptions.icon = 'success';
                            swalOptions.title = 'Success!';
                            swalOptions.confirmButtonColor = '#6d2e3a';
                        }

                        Swal.fire(swalOptions);
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'Something went wrong while adding to cart.',
                            customClass: {
                                popup: 'small-swal'
                            },
                            confirmButtonColor: '#6d2e3a'
                        });
                    });
                });
            }

            // ❌ Cancel button - close add-to-cart modal
            if (cancelAddBtn) {
                cancelAddBtn.addEventListener("click", function () {
                    addToCartModal.style.display = "none";
                });
            }

            // ❌ Close login prompt when clicking outside
            if (loginPromptModal) {
                loginPromptModal.addEventListener("click", function (e) {
                    if (e.target === loginPromptModal) {
                        loginPromptModal.style.display = "none";
                    }
                });
            }

            // ❌ Close add-to-cart modal when clicking outside
            if (addToCartModal) {
                addToCartModal.addEventListener("click", function (e) {
                    if (e.target === addToCartModal) {
                        addToCartModal.style.display = "none";
                    }
                });
            }

            closeSuccessBtn.addEventListener("click", function () {
                successModal.style.display = "none";
            });

            // Optional: close success modal when clicking outside
            successModal.addEventListener("click", function (e) {
                if (e.target === successModal) {
                    successModal.style.display = "none";
                }
            });
        });

        // Apply Poppins globally
        document.addEventListener("DOMContentLoaded", () => {
            document.body.style.fontFamily = "'Poppins', sans-serif";

            // Override iframe content
            const iframe = document.getElementById('dashboardFrame');
            iframe.onload = () => {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            if (!doc) return;

            // Inject Poppins style into iframe head
            const style = document.createElement('style');
            style.innerHTML = "* { font-family: 'Poppins', sans-serif !important; }";
            doc.head.appendChild(style);
            };
        });

    </script>

</body>
</html>