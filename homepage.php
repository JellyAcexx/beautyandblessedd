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

        .navbar-main,
        .site-footer {
            background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%);
            box-shadow: 0 10px 28px rgba(0,0,0,0.12);
        }

        /* Gawing mas breathing room sa loob ng navbar */
        .navbar > .container-fluid,
        .navbar > .container {
        padding-top: 6px;
        padding-bottom: 6px;
        }

        /* Nav-links: same color, pero mas “pill” at smoother hover */
        #myTab .nav-link {
        padding: 6px 14px;
        font-size: 0.82rem;
        font-weight: 500;
        border: none;
        }
        #myTab .nav-link:hover {
        background-color: rgba(232, 169, 178, 0.12);
        }

        /* Active tab: emphasize pero gamit existing color */
        #myTab .nav-link.active {
            font-weight: 800;
            font-size: 0.84rem;
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
            background-color: transparent;
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

        #featuredCarousel .carousel-indicators {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
        }

        #featuredCarousel .carousel-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.7);
            border: none;
            margin: 0 4px;
        }

        #featuredCarousel .carousel-indicators .active {
            width: 18px;
            border-radius: 999px;
            background-color: #ffffff;
        }

        .mobile-search {
            display: none;
        }

        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 230px; 
            height: 100vh;
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
            margin-top: 0 !important;
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
        }

        #mobileSidebar .nav-link {
            color: white; 
            width: calc(100% - 8px);
            margin: 5px auto;
            text-align: left;
            font-weight: 500; 
            padding: 10px 10px;
            border-radius: 5px; 
            align-items: center; 
            gap: 5px; 
            font-size: 15px !important;
            display: block;
            transition: background-color .15s ease, color .15s ease, transform .1s ease;
        }
        #mobileSidebar .nav-link:hover {
            background-color: rgba(232,169,178,0.15);
            transform: translateX(2px);
        }
        #mobileSidebar .nav-link.active {
            background-color: transparent !important; 
            border-radius: 5px;
            font-weight: 900;
        }

        #sidebarToggle {
            display: none;
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

        .card {
            width: 220px !important;
            flex: 0 0 auto !important;
            position: relative;
            overflow: hidden;
            box-shadow: 0 -2px 20px rgba(0,0,0,0.05);
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
            right: 5px;
            top: 5.5px;
            transform: scale(0.8);
            z-index: 10;
        }

        /* add to cart button*/
        .add-to-cart-btn {
            position: absolute;
            top: 220px;
            left: 0;
            height: 40px;
            width: 100%;
            border: none;
            font-size: 0.78rem;
            font-weight: 600;
            background: #6d2e3a;
            color: #fae6e7;
            text-align: center;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px 0;
            z-index: 2;
            gap: 6px;
            cursor: pointer;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity 0.2s ease, transform 0.2s ease, background 0.2s ease;
        }

        .card:hover .add-to-cart-btn {
            opacity: 1;
            transform: translateY(0);
        }

        .add-to-cart-btn:hover {
            background: #fae6e7;
            color: #6d2e3a;
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

        .modal-box {
            background: #fff;
            padding: 20px 24px;
            text-align: center;
            border-radius: 12px;
            width: 300px;              /* ← KEEP: fixed width */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            font-family: Poppins, sans-serif;
            color: #6d2e3a;
        }

        .pretty-modal {
            border-radius: 18px !important;     /* shape */
            border: none !important;
            background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%);
            box-shadow: 0 10px 28px rgba(0,0,0,0.12);  /* a bit softer */
            overflow: hidden;
        }

        .pretty-modal .modal-header {
            border: none;
            background: transparent;
            padding-bottom: 0.25rem;
            position: relative;
        }

        .pretty-modal .modal-header::after {
            content: "";
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: 0;
            height: 2px;
            background: rgba(232, 169, 178, 0.8); /* soft pink line */
        }

        .pretty-modal .modal-title {
            font-weight: 700;
            color: #6d2e3a;
            font-size: 20px;
        }

        .pretty-modal .modal-body {
            color: #6d2e3a;
            font-size: 16px;
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

        .modal-box p {
            margin-top: 8px;
            font-weight: 400;
            color: #6d2e3a;
        }

        .btn-modal {
            display: inline-block;
            width: 55px !important;
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

        #confirmAddBtn.btn-modal {
            background:#6d2e3a;
            color: #fff;
        }

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
            background-color: #6d2e3a;
            opacity: 0.6;
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
            backface-visibility: hidden; 
            transform: translateZ(0); 
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

        .small-swal-homepage {
            width: 350px !important; 
            padding: 1.2rem 1.2rem !important;
        }

        .small-swal-homepage .swal2-title {
            font-size: 1.4rem !important;
            color: #6d2e3a !important;
        }

        .small-swal-homepage .swal2-html-container {
            font-size: 1rem !important;
            color: #6d2e3a !important;
            font-family: 'Poppins', sans-serif;
        }

        .small-swal-homepage .swal2-icon.swal2-success,
        .small-swal-homepage .swal2-icon.swal2-error {
            border-color: #6d2e3a !important;
            color: #6d2e3a !important;
        }

        .small-swal-homepage .swal2-success-ring,
        .small-swal-homepage .swal2-error-ring {
            border-color: rgba(109, 46, 58, 0.4) !important;
        }

        .small-swal-homepage .swal2-success-line-tip,
        .small-swal-homepage .swal2-success-line-long,
        .small-swal-homepage .swal2-error-line-tip,
        .small-swal-homepage .swal2-error-line-long {
            background-color: #6d2e3a !important;
        }

        .container {
            max-width: 1200px;   
            margin-left: auto;
            margin-right: auto;
        }

        .row.g-3 {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .product-card {
            width: 100%;   
            max-width: 100%;    
            margin: 0 auto;    
            background-color: fff;
            border-radius: 18px;
            overflow: hidden;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-sizing: border-box; 
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            background: #ffffff;
        }

        /* Hover: slight lift */
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .product-card-title {
            font-family: Poppins, sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            color: #6d2e3a;
            width: 100%;
            margin: 0 0 4px 0;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;      /* max 2 lines */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
        }

        /* Para yung text sa card body mag-left align din, mas “website feel” */
        .product-card-body {
            width: 100%;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%);
            box-shadow: 0 10px 28px rgba(0,0,0,0.12);
        }
        .product-card-price {
            width: 100%;
            margin-top: auto;
        }

        /* Image: fill width, responsive height */
        .product-card-img {
            width: 100%;
            height: 230px;
            object-fit: cover;
        }

        .card-body.product-card-body {
            padding: 16px 20px 18px 20px;
        }
        
        @media (max-width: 991.98px) {
            .product-card-img { height: 180px; }
            .card-body.product-card-body { padding: 12px; }
        }
        @media (max-width: 767.98px) {
            .product-card-img { height: 120px; }
            .card-body.product-card-body { padding: 8px; }
        }

        /* Text sizes desktop */
        .product-card-title {
            color: #6d2e3a;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .product-card-price {
            color: #646060ff;
            font-size: 0.9rem;
        }

        /* Gawin mas slim ang Add to Cart mobile button */
        .add-to-cart-btn-mobile {
            font-size: 0.75rem;
            display: none;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            background: #6d2e3a;
            color: #fae6e7;
            font-weight: 400;
            letter-spacing: 0.4px;
        }

        /* Desktop and large tablet (>= 992px): 4 columns via col-lg-3, card height mas mataas konti */
        @media (min-width: 992px) {
            #SEARCHES, #PRODUCTS {
                margin-bottom: 20px !important;
            }

            .product-card-img {
                height: 260px;
            }
        }

        /* Tablet (>= 768px and < 992px): 3 columns, medium height */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .product-card-img {
                height: 230px;
            }
        }

        /* Mobile (< 768px): 2 columns, liit ng card at font sizes */
        @media (max-width: 767.98px) {
            #SEARCHES, #PRODUCTS {
                margin-bottom: 0px !important;
            }

            #searchResultsGrid{
                margin: 0 !important;
            }

            .product-card-img {
                height: 180px;
            }

            .product-card-body {
                padding: 8px 8px 10px 8px;
            }

            .product-card-title {
                font-size: 0.8rem;
            }

            .product-card-price {
                font-size: 0.8rem;
            }
        }

        /* Kapag naliit na ang screen mag-adjust ang mga size at display ng button, etc. */
        @media (max-width: 1250px) {
            #navbarNav .navbar-nav {
                display: none !important;
            }
            .navbar-toggler {
                order: -1;
                margin-right: auto;
            }
            #sidebarToggle {
                display: block !important;
            }
            #desktop-auths {
                display: none !important;
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
            .add-to-cart-btn {
                display: none !important;
            }
            .add-to-cart-btn-mobile {
                display: flex !important;
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
            .hero-title {
                font-size: 20px !important;
                margin-bottom: 5px !important;
            }
            .hero-subtitle {
                font-size: 13px !important;
            }
            .card-body {
                min-height: 80px;              /* o 90/100px depende sa tingin mo */
                max-height: 120px;             /* bawasan mula 150px */
                display: flex;
                flex-direction: column;
                justify-content: center;       /* VERTICAL center */
                align-items: center;           /* HORIZONTAL center */
                padding-top: 9px !important;
                padding-bottom: 9px !important;
            }
            .card {
                width: 200px !important;
                max-height: 450px !important;
                align-items: center !important;
            }
            .card-title {
                font-size: 14px !important;
                font-weight: 600;
            }
            .card-text {
                font-size: 12px !important;
                font-weight: 500;
            }
            .card-img-top {
                max-width: 200px !important;
                max-height: 200px !important;
            }
            .card:hover .zoom-btn,
            .card:hover .add-to-cart-btn {
                opacity: 0 !important;
                pointer-events: none;
            }
            .zoom-btn {
                display: none !important;
            }
            #addToCartModal .modal-box {
                width: 250px !important;              /* mas maliit sa screen width */
                padding: 15px;           /* bawas padding */
                border-radius: 10px;     /* softer corners */
                font-size: 14px;         /* smaller text */
            }
            #addToCartModal button {
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
            .footer-title.active::after {
                transform: rotate(180deg);
            }
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
            .footer .row.mb-5 {
                margin-bottom: 0 !important;
            }
            .footer .row.gy-5 {
                row-gap: 0 !important;
                gap: 0 !important;
            }
            .footer .row > [class*='col-'] {
                margin-bottom: 0 !important;
                padding-top: 5px !important;
                padding-bottom: 5px !important;
            }
            .footer-section {
                margin: 0 !important;
                padding: 0 !important;
            }
            .footer .footer-content p {
                text-align: center !important;
                line-height: 1.6 !important;
            }
            .footer .footer-content p {
                font-size: 14px !important;
                padding: 0 10px;
            }
        }

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

        @media (max-width: 768px) {
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
            .product-grid {
                justify-content: center;
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
            #searchResultsGrid {
                margin-bottom: 10px !important;
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

        .footer-copy {
            font-size: 0.8rem;               /* mas refined na maliit na text */
            letter-spacing: 0.03em;          /* konting spacing para professional look */
            opacity: 0.9;                    /* bahagyang faded */
            color: #ffffff;                  /* assuming dark footer background */
        }

        /* Optional: center sa mobile, left sa desktop (Bootstrap pattern) */
        @media (max-width: 768px) {
            .footer-copy {
                text-align: center;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 767px) {
            .footer-bottom-bg {
                flex-direction: column;
                align-items: center !important;
                text-align: center !important;
            }
            .text-center .text-md-end {
                margin-right: 0 !important;
            }
            .footer-connect-title {
                font-size: 0.8rem;
            }
            .footer-bottom-bg .d-flex.justify-content-md-end.justify-content-center.gap-4.fs-4 {
                justify-content: center !important;  /* siguradong gitna sa mobile */
                gap: 1.2rem;
                font-size: 0.3rem;                   /* icons smaller sa mobile */
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

        .tab-breadcrumb-wrapper {
            width: 100%;
            padding: 8px 16px 0 16px;
            display: flex;
            justify-content: center; /* LEFT side */
        }

        .tab-breadcrumb {
            margin: 0;
            padding: 0;
            background: none;           /* walang pill background */
            border: none;               /* walang border */
            box-shadow: none;           /* walang shadow */
            color: #fff;
            font-size: 1.15rem;
            letter-spacing: 0.4px;
        }

        .tab-breadcrumb .separator {
            opacity: 0.75;
            margin: 0 4px;
        }

        /* Mobile tweak (optional) */
        @media (max-width: 767.98px) {
            .tab-breadcrumb-wrapper {
                padding: 6px 10px 0 10px;
            }
            .tab-breadcrumb {
                font-size: 0.8rem;
            }
            .search-breadcrumb {
                margin-top: 0.25rem;
                margin-bottom: 0; /* bawas sa malaking gap */
                line-height: 0.5;       /* para mas dikit pa kung kailangan */
            }
        }

    </style>

</head>
<body style="background-color: #a95469;">
    <nav class="navbar navbar-main sticky-top navbar-expand-lg">
        <div class="container-fluid flex-column">
            <div class="d-flex justify-content-between align-items-center">
                <button class="navbar-toggler d-lg-none me-1" style="background-color: transparent; border: none; padding: 0.17rem 0.35rem;" type="button" id="sidebarToggle">
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
                <div class="d-none d-md-block">
                    <?php if (!isset($_SESSION['user_email'])): ?>
                    <div class="d-none d-md-block">
                        <button class="dual-btn fs-6 py-1 px-2" id="desktop-auths">
                            <span id="signup" onclick="window.location.href='register.php'">Sign up</span>
                            <div class="divider"></div>
                            <span id="login" onclick="window.location.href='login.php'">Log in</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="d-none d-md-block"></div>
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
                <ul class="navbar-nav mx-auto gap-1" style="margin-top: 5px;" id="myTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" style="color: #6d2e3a;" id="home-tab" data-bs-toggle="tab" 
                                data-bs-target="#home" role="tab" aria-controls="home" aria-selected="true">
                            HOME
                        </button>
                    </li>

                    <?php
                    include "database.php";
                    $catQuery = "
                        SELECT c.*
                        FROM category c
                        JOIN products p
                        ON p.category_id = c.category_id
                        GROUP BY c.category_id
                    ";
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
        <hr style="border: 2px solid #6d2e3a; margin: 4px 0;">
        <ul class="navbar-nav flex-grow-1">
            <li class="nav-item">
                <button class="nav-link active mb-0" style="color: #6d2e3a;" data-bs-toggle="tab" data-bs-target="#home">
                        HOME
                </button>
            </li>

            <?php 
            mysqli_data_seek($catResult, 0);

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
        <hr style="border: 2px solid #6d2e3a; margin: 4px 0;">
        <?php if (!isset($_SESSION['user_email'])): ?>
            <div class="auth-buttons w-100 px-2 mb-2">
                <button class="dual-btn fs-6 py-1 px-2" style="width: 160px; height: 35px;">
                    <span id="signup" onclick="window.location.href='register.php'">Sign up</span>
                    <div class="divider"></div>
                    <span id="login" onclick="window.location.href='login.php'">Log in</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <main id="content">
        <div id="main-section">
            <div class="tab-breadcrumb-wrapper">
                <p id="tabBreadcrumb" class="tab-breadcrumb">
                    HOME
                </p>
            </div>
            <div class="tab-content mt-3" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <?php
                    // PHP data for banners
                    $banners = [
                        'banner1.png',
                        'banner2.png',
                        'banner3.png'
                    ];
                    ?>

                    <div id="featuredCarousel"
                        class="carousel slide"
                        data-bs-ride="carousel"
                        data-bs-interval="3000"
                        data-bs-touch="true"
                        data-bs-wrap="true">

                        <!-- Indicators (dots) -->
                        <div class="carousel-indicators">
                            <?php foreach ($banners as $index => $img): ?>
                                <button
                                    type="button"
                                    data-bs-target="#featuredCarousel"
                                    data-bs-slide-to="<?= $index ?>"
                                    class="<?= $index === 0 ? 'active' : '' ?>"
                                    <?= $index === 0 ? 'aria-current="true"' : '' ?>
                                    aria-label="Slide <?= $index + 1 ?>">
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Slides -->
                        <div class="carousel-inner">
                            <?php foreach ($banners as $index => $img): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= $img ?>" class="d-block w-100" alt="Banner <?= $index + 1 ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="myTabContent">
                <?php 
                    mysqli_data_seek($catResult, 0);
                    while ($cat = mysqli_fetch_assoc($catResult)) { ?>
                    <div class="tab-pane fade" id="cat-<?php echo $cat['category_id']; ?>" role="tabpanel">
                        <?php
                        include 'database.php';
                        $prodQuery = "
                            SELECT p.*, i.stocks 
                            FROM products p 
                            LEFT JOIN inventory i ON i.product_id = p.product_id 
                            WHERE p.category_id = " . $cat['category_id'];
                        $prodResult = mysqli_query($conn, $prodQuery);
                        ?>

                        <div class="container py-0 mt-0">
                            <div class="row product-grid g-3 justify-content-center" id="productGrid<?php echo $cat['category_id']; ?>">

                                <?php
                                while ($row = mysqli_fetch_assoc($prodResult)) {
                                    $imgPath = $row['image_path'];

                                    if (!str_starts_with($imgPath, 'pictures/')) {
                                        $imgPath = 'pictures/' . $imgPath;
                                    }
                                    if (empty($imgPath) || !file_exists($imgPath)) {
                                        $imgPath = 'pictures/noimage.png';
                                    }
                                ?>
                                    <div class="col-6 col-md-4 col-lg-3 mb-4" id="PRODUCTS">
                                        <div class="card product-card shadow-sm"
                                            data-product-id="<?php echo $row['product_id']; ?>"
                                            data-category-id="<?php echo $row['category_id']; ?>">

                                            <img src="<?php echo $imgPath; ?>"
                                                alt="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>"
                                                class="card-img-top product-card-img">

                                            <!-- Zoom button -->
                                            <button class="zoom-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#zoomModal"
                                                    data-img="<?php echo $imgPath; ?>">
                                                <i class="bi bi-zoom-in"></i>
                                            </button>

                                            <!-- Desktop ADD TO CART -->
                                            <button class="add-to-cart-btn"
                                                data-product-id="<?php echo $row['product_id']; ?>"
                                                data-product-price="<?php echo $row['price']; ?>"
                                                data-product-name="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>"
                                                data-product-stocks="<?php echo isset($row['stocks']) ? (int)$row['stocks'] : 0; ?>"
                                                data-is-logged-in="<?php echo !empty($_SESSION['login_id']) ? 1 : 0; ?>">
                                                ADD TO CART
                                            </button>

                                            <div class="card-body d-flex flex-column align-items-center product-card-body">
                                                <p class="card-title fw-bold text-center mb-1 product-card-title">
                                                    <?php echo $row['product_name'] ?>
                                                </p>
                                                <p class="card-text text-center mb-1 product-card-price" style="font-size: 15px;">
                                                    ₱<?php echo number_format($row['price'], 2) ?>
                                                </p>

                                                <!-- Stocks Display -->
                                                <p class="card-text text-center mb-2" style="font-size: 13px; color: #6d2e3a;">
                                                    Stocks: <?php echo isset($row['stocks']) ? (int)$row['stocks'] : 0; ?>
                                                </p>

                                                <!-- Mobile ADD TO CART -->
                                                <button
                                                    class="btn btn-xs add-to-cart-btn-mobile mt-1"
                                                    data-product-id="<?php echo $row['product_id']; ?>"
                                                    data-product-price="<?php echo $row['price']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>"
                                                    data-product-stocks="<?php echo isset($row['stocks']) ? (int)$row['stocks'] : 0; ?>"
                                                    data-is-logged-in="<?php echo !empty($_SESSION['login_id']) ? 1 : 0; ?>">
                                                    ADD TO CART
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

                <!-- SEARCH RESULTS TAB -->
                <div class="tab-pane fade" id="search-results" role="tabpanel">
                    <?php
                        if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
                            include 'database.php';
                            $search = mysqli_real_escape_string($conn, $_GET['q']);
                            $prodQuery = "
                                SELECT p.*, i.stocks 
                                FROM products p 
                                LEFT JOIN inventory i ON i.product_id = p.product_id 
                                WHERE p.product_name LIKE '%$search%'";
                            $prodResult = mysqli_query($conn, $prodQuery);

                            if (mysqli_num_rows($prodResult) > 0) { ?>
                                <div class="container search-results-container">
                                    <div class="row g-4 justify-content-center" id="searchResultsGrid">
                                        <?php while ($row = mysqli_fetch_assoc($prodResult)) {
                                            $imgPath = $row['image_path'];
                                            if (!str_starts_with($imgPath, 'pictures/')) $imgPath = 'pictures/' . $imgPath;
                                            if (empty($imgPath) || !file_exists($imgPath)) $imgPath = 'pictures/noimage.png';
                                        ?>
                                            <div class="col-6 col-md-4 col-lg-3 mb-3" id="SEARCHES">
                                                <div class="card product-card shadow-sm"
                                                    data-product-id="<?php echo $row['product_id']; ?>"
                                                    data-category-id="<?php echo $row['category_id']; ?>">

                                                    <img src="<?php echo $imgPath; ?>"
                                                        alt="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>"
                                                        class="card-img-top product-card-img">

                                                    <!-- Zoom button -->
                                                    <button class="zoom-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#zoomModal"
                                                            data-img="<?php echo $imgPath; ?>">
                                                        <i class="bi bi-zoom-in"></i>
                                                    </button>

                                                    <!-- Desktop ADD TO CART -->
                                                    <button class="add-to-cart-btn"
                                                        data-product-id="<?php echo $row['product_id']; ?>"
                                                        data-product-price="<?php echo $row['price']; ?>"
                                                        data-product-name="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>"
                                                        data-product-stocks="<?php echo isset($row['stocks']) ? (int)$row['stocks'] : 0; ?>"
                                                        data-is-logged-in="<?php echo !empty($_SESSION['login_id']) ? 1 : 0; ?>">
                                                        ADD TO CART
                                                    </button>

                                                    <div class="card-body d-flex flex-column align-items-center product-card-body">
                                                        <p class="card-title fw-bold text-center mb-1 product-card-title">
                                                            <?php echo $row['product_name']; ?>
                                                        </p>
                                                        <p class="card-text text-center mb-1 product-card-price" style="font-size: 15px;">
                                                            ₱<?php echo number_format($row['price'], 2) ?>
                                                        </p>
                                                        <p class="card-text text-center mb-2" style="font-size: 13px; color: #6d2e3a;">
                                                            Stocks: <?php echo isset($row['stocks']) ? (int)$row['stocks'] : 0; ?>
                                                        </p>
                                                        <!-- Mobile ADD TO CART -->
                                                        <button
                                                            class="btn btn-xs add-to-cart-btn-mobile mt-1"
                                                            data-product-id="<?php echo $row['product_id']; ?>"
                                                            data-product-price="<?php echo $row['price']; ?>"
                                                            data-product-name="<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>"
                                                            data-product-stocks="<?php echo isset($row['stocks']) ? (int)$row['stocks'] : 0; ?>"
                                                            data-is-logged-in="<?php echo !empty($_SESSION['login_id']) ? 1 : 0; ?>">
                                                            ADD TO CART
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php
                            } else {
                                echo "<p class='text-center mt-4' style='color: #fff;'>No matching products found.</p>";
                            }
                        }
                    ?>
                </div>

                <div class="w-100 mt-4" style="background-color:#fff;">
                    <section class="hero-section container py-4 py-md-5 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-7 text-center text-md-start">
                                <h1 class="hero-title mb-2" style="color: #6d2e3a;">Discover Your Perfect Look</h1>
                                <p class="hero-subtitle mb-3" style="color: #6d2e3a;">
                                    Shop our curated beauty essentials and discover your new everyday favorites.
                                </p>
                            </div>
                            <div class="col-12 col-md-5 text-center text-md-end">
                                <img src="banner.jpg" alt="Beauty service"
                                    class="img-fluid hero-image">
                            </div>
                        </div>
                    </section>
                </div>

                <section class="py-5" style="background-color: #a95469;">
                    <div class="container text-center">

                        <h1 class="fw-bold mb-4" style="color: #fff;">TOP SELLING</h1>

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

                <!-- NEW ARRIVALS -->
                <section class="py-5" style="background-color: #fff;">
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
                            <div class="promo-box big" style="border-radius: none;">
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
            <div  class="modal" id="loginPromptModal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content pretty-modal text-center">
                        <div class="modal-header py-3">
                            <h3 class="modal-title"><strong></i>Login Required!</strong></h3>
                            <button type="button" class="btn-close" onclick="closeLoginPrompt()" data-bs-dismiss="modal"></button>
                        </div>
                        <div  class="modal-body text-center py-3">
                            <p>
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
            </div>

            <!-- Add-to-Cart Modal (card style) -->
            <div id="addToCartModal" class="modal-overlay" style="display:none;">
                <div class="modal-box pretty-modal">
                    <h5 id="modalProductName" style="margin-bottom: 8px;"></h5>

                    <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin: 10px 0;">
                        <input
                            id="qtyInput"
                            type="number"
                            min="1"
                            value="1"
                            style="width: 70px; text-align: center; padding: 6px; border: 1px solid #6d2e3a; border-radius: 6px;">
                    </div>
                    <p id="modalError"
                        style="margin: 4px 0 0 0; font-size: 13px; color: #c1121f; min-height: 16px;">
                    </p>
                    <p id="modalStocks" style="margin: 0 0 8px 0; font-size: 14px; color: #6d2e3a;">
                        Stocks Remaining: 0
                    </p>
                    <div style="margin-top: 4px;">
                        <p style="margin: 0 0 8px 0;">
                            Total: <strong id="modalTotal">0.00</strong>
                        </p>
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 12px;">
                        <button id="confirmAddBtn" class="btn-modal" style="flex: 1;">ADD</button>
                        <button id="cancelAddBtn" class="btn-modal" style="flex: 1;">CANCEL</button>
                    </div>
                </div>
            </div>

        </div>
        <div id="info-section" style="display: none;"></div>
    </main>

    <footer class="footer site-footer mt-5 pt-5 text-md-start" style="font-family:'Poppins', sans-serif;">
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
                    <div class="footer-copy text-center text-md-start">
                        <span>© 2025 <strong>Beauty &amp; Blessed</strong>. All rights reserved.</span>
                    </div>

                    <div class="text-center text-md-end" style="margin-right:20px;">
                        <h6 class="footer-connect-title fw-bold mb-3">CONNECT WITH US:</h6>
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

        document.addEventListener('DOMContentLoaded', function () {
            const breadcrumbEl = document.getElementById('tabBreadcrumb');
            if (!breadcrumbEl) return;

            function toTitleCase(str) {
                return (str || '')
                    .toLowerCase()
                    .split(' ')
                    .filter(Boolean)
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }

            function setBreadcrumb(text, opts = {}) {
                const el = document.getElementById('tabBreadcrumb');
                if (!el) return;

                const home = 'HOME';
                text = (text || '').trim();

                // HOME case
                if (opts.isHome || !text || text.toUpperCase() === home) {
                    el.innerHTML = home;
                    return;
                }

                if (opts.isSearch) {
                    const q = text; // huwag galawin casing ng user
                    el.innerHTML =
                        '<div class="search-breadcrumb">' +
                            '<span style="font-weight:400; opacity:0.80;">' + home + '</span>' +
                            ' <span class="separator">|</span> ' +
                            '<span style="font-weight:600;">Results for "' +
                                q.replace(/"/g, '&quot;') +
                            '"</span>' +
                        '</div>';
                    return;
                }

                const categoryTitle = toTitleCase(text);
                el.innerHTML =
                    '<span style="font-weight:400; opacity:0.80;">' + home + '</span>' +
                    ' <span class="separator">|</span> ' +
                    '<span style="font-weight:600;">' + categoryTitle + '</span>';
            }

            document.querySelectorAll('#myTab .nav-link').forEach(btn => {
                btn.addEventListener('click', function () {
                    const label = (this.textContent || '').trim();

                    if (this.id === 'home-tab') {
                        setBreadcrumb(label, { isHome: true });
                    } else if (this.id === 'search-tab') {
                        // desktop hidden SEARCH tab – gagamitin natin yung query sa URL
                        const params = new URLSearchParams(window.location.search);
                        const q = params.get('q') || 'Search';
                        setBreadcrumb(q, { isSearch: true });
                    } else {
                        setBreadcrumb(label);
                    }
                });
            });

            document.querySelectorAll('.mobile-sidebar .nav-link').forEach(btn => {
                btn.addEventListener('click', function () {
                    const label = (this.textContent || '').trim();

                    if (this.dataset.bsTarget === '#home') {
                        setBreadcrumb(label, { isHome: true });
                    } else if (this.id === 'search-tab-mobile') {
                        const params = new URLSearchParams(window.location.search);
                        const q = params.get('q') || 'Search';
                        setBreadcrumb(q, { isSearch: true });
                    } else {
                        setBreadcrumb(label);
                    }
                });
            });

            const params = new URLSearchParams(window.location.search);
            const q = params.get('q');

            if (q && q.trim() !== '') {
                // Page opened via search → set breadcrumb to Results for "{q}"
                setBreadcrumb(q.trim(), { isSearch: true });

                // Optional: i-activate mo rin ang SEARCH tab kung kailangan
                const searchTab = document.querySelector('#search-tab');
                if (searchTab) {
                    const tab = new bootstrap.Tab(searchTab);
                    tab.show();
                }
            } else {
                // Default HOME
                setBreadcrumb('HOME', { isHome: true });
            }
        });

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
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.learn-more-btn');
            if (!btn) return;

            const productId  = btn.dataset.productId;   // galing sa button data-product-id
            const categoryId = btn.dataset.categoryId;  // galing sa button data-category-id

            // Buksan tamang tab
            openCategoryTab(categoryId);

            // Hintay, then scroll + highlight
            setTimeout(() => {
                highlightProductCard(categoryId, productId);
            }, 400);
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
        
        function initFAQ() {

  // 💗 CATEGORY BUTTONS CLICK HANDLER
  document.querySelectorAll(".faq-categories li").forEach(cat => {
    cat.addEventListener("click", () => {
      document.querySelectorAll(".faq-categories li")
        .forEach(c => c.classList.remove("active"));

      cat.classList.add("active");

      document.querySelectorAll(".faq-group")
        .forEach(g => g.classList.add("d-none"));

      const target = document.getElementById(cat.dataset.category);
      if (target) target.classList.remove("d-none");
    });
  });

  // 💗 CLOSE ALL ACCORDIONS INITIALLY
  document.querySelectorAll(".accordion-collapse").forEach(panel => {
    panel.classList.remove("show");
  });

  // 💗 REMOVE FOCUS PARA DI AUTO-OPEN
  document.querySelectorAll(".accordion-button").forEach(btn => btn.blur());
}

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
            // I-target ang lahat ng nav-link buttons sa desktop at mobile
            const navButtons = document.querySelectorAll('#myTab button.nav-link, .mobile-sidebar button.nav-link');

            navButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const searchResultsTab = document.getElementById('search-results');
                    if (searchResultsTab) {
                        // Itago ang search results content
                        searchResultsTab.classList.remove('show', 'active');
                        searchResultsTab.innerHTML = ""; // optional: para malinis talaga

                        // Alisin ang 'active' ng hidden search-tab (desktop and mobile)
                        const searchTabBtn = document.getElementById('search-tab');
                        if (searchTabBtn) searchTabBtn.classList.remove('active');
                        const searchTabBtnMobile = document.getElementById('search-tab-mobile');
                        if (searchTabBtnMobile) searchTabBtnMobile.classList.remove('active');

                        // I-clear lahat ng search inputs sa page
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
                    return text.replace(regex, "<strong style='color: #6d2e3a;'>$1</strong>");
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
                                const noResultDiv = document.createElement("div");
                                noResultDiv.textContent = "No product found!";
                                noResultDiv.classList.add("text-muted");
                                noResultDiv.classList.add("text-center");
                                noResultDiv.style.padding = "6px 10px";
                                suggestionBox.appendChild(noResultDiv);
                                suggestionBox.style.display = "block";
                                return;
                            }

                            data.forEach(item => {
                                const div = document.createElement("div");

                                // important: para makilala sa global click handler
                                div.classList.add("suggestion-item");

                                // data attributes para magamit sa highlightProductCard
                                div.dataset.productId = item.product_id;      // galing sa search.php JSON
                                div.dataset.category  = item.category_id;     // galing sa search.php JSON

                                // text na may highlight ng query
                                div.innerHTML = highlightMatch(item.product_name, query);
                                div.style.padding = "6px 10px";
                                div.style.borderBottom = "1px solid #f8d7d6";

                                // hover effect
                                div.addEventListener("mouseover", () => div.style.background = "#fae6e7");
                                div.addEventListener("mouseout", () => div.style.background = "white");

                                // kapag na-click ang suggestion
                                div.addEventListener("click", () => {
                                    // itago ang suggestion box at ilagay ang text sa input
                                    suggestionBox.style.display = "none";
                                    input.value = item.product_name;

                                    // 1️⃣ open tamang category tab
                                    const categoryId = item.category_id;
                                    openCategoryTab(categoryId);

                                    // 2️⃣ delay ng konti para sure loaded na ang tab content
                                    setTimeout(() => {
                                        // 3️⃣ scroll + highlight sa tamang product card sa grid
                                        highlightProductCard(categoryId, item.product_id);
                                    }, 400);
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

        // 1. Buksan ang tamang tab
        function openCategoryTab(categoryId) {
            const tabBtn = document.querySelector(`[data-bs-target="#cat-${categoryId}"]`);
            if (tabBtn) tabBtn.click();
        }

        // 2. Hanapin at i-highlight ang product card sa grid
        function highlightProductCard(categoryId, productId) {
            // kunin ang grid container para sa category na ito
            const grid = document.getElementById('productGrid' + categoryId);
            if (!grid) return;

            // hanapin ang mismong card
            const targetCard = grid.querySelector(`.card[data-product-id="${productId}"]`);
            if (!targetCard) return;

            // alisin muna ang dating highlight sa ibang cards
            grid.querySelectorAll('.highlight-product').forEach(card => {
                card.classList.remove('highlight-product');
            });

            // scroll into view
            targetCard.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'center'
            });

            // apply highlight animation
            targetCard.classList.add('highlight-product');

            // optional: alisin ang class pagkatapos ng ilang segundo
            setTimeout(() => {
                targetCard.classList.remove('highlight-product');
            }, 2000);
        }

        document.addEventListener("click", function (e) {
            const item = e.target.closest(".suggestion-item");
            if (!item) return;

            const productId  = item.dataset.productId;   // kailangan meron nito
            const categoryId = item.dataset.category;    // existing na sa iyo

            // Step 1 — open tab
            openCategoryTab(categoryId);

            // Step 2 — wait konti, then highlight
            setTimeout(() => {
                highlightProductCard(categoryId, productId);
            }, 400);
        });

        document.addEventListener('DOMContentLoaded', function () {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn, .add-to-cart-btn-mobile');
            const addToCartModal = document.getElementById('addToCartModal');
            const loginPromptModal = document.getElementById('loginPromptModal');
            const modalProductName = document.getElementById('modalProductName');
            const qtyInput = document.getElementById('qtyInput');
            const modalTotal = document.getElementById('modalTotal');
            const confirmAddBtn = document.getElementById('confirmAddBtn');
            const cancelAddBtn = document.getElementById('cancelAddBtn');
            const modalError = document.getElementById('modalError');
            const modalStocks = document.getElementById('modalStocks');
            const successModal = document.getElementById("successModal");
            const closeSuccessBtn = document.getElementById("closeSuccessBtn");

            let selectedPrice = 0;
            let selectedProductId = null;
            let selectedStocks = 0;

            function sendCartCountToParentFromHomepage(count) {
                if (window.parent && window.parent !== window) {
                    window.parent.postMessage(
                        { type: 'cartCountUpdate', count: count },
                        '*'
                    );
                }
            }

            addToCartButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const isLoggedIn = this.dataset.isLoggedIn == 1;

                    if (!isLoggedIn) {
                        loginPromptModal.style.display = 'flex';
                        return;
                    }

                    const productName = this.dataset.productName;
                    selectedPrice = parseFloat(this.dataset.productPrice);
                    selectedProductId = this.dataset.productId;
                    selectedStocks = parseInt(this.dataset.productStocks || '0');

                    modalProductName.textContent = productName;
                    qtyInput.value = 1;
                    modalTotal.textContent = selectedPrice.toFixed(2);

                    if (modalStocks) {
                        modalStocks.textContent = 'Stocks Remaining: ' + selectedStocks;
                    }

                    // CLEAR any previous error when opening modal
                    if (modalError) {
                        modalError.textContent = '';
                    }

                    addToCartModal.style.display = 'flex';
                });
            });

            qtyInput.addEventListener('input', function () {
                let qty = parseInt(this.value) || 1;

                this.value = qty;
                modalTotal.textContent = (selectedPrice * qty).toFixed(2);
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
                    .then(res => res.json())
                    .then(data => {
                        if (modalError) modalError.textContent = '';

                        const msg = data.message || '';
                        const newCartCount = parseInt(data.cartCount || 0);
                        const isError = (data.success === false);

                        if (isError) {
                            if (modalError) {
                                modalError.textContent = msg;
                            }
                            // reset qty and total
                            qtyInput.value = 1;
                            modalTotal.textContent = selectedPrice.toFixed(2);
                            return;
                        }

                        // Success path
                        addToCartModal.style.display = "none";

                        // ✅ Update cart badge in dashboard (if homepage is inside iframe)
                        if (!isNaN(newCartCount)) {
                            sendCartCountToParentFromHomepage(newCartCount);
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: msg,
                            customClass: { popup: 'small-swal-homepage' },
                            confirmButtonColor: '#6d2e3a'
                        });
                    })
                    .catch(err => {
                        if (modalError) {
                            modalError.textContent = 'Network error, please try again.';
                        }
                    });
                });
            }

            // ❌ Cancel button - close add-to-cart modal
            if (cancelAddBtn) {
                cancelAddBtn.addEventListener("click", function () {
                    addToCartModal.style.display = "none";
                    if (modalError) {
                        modalError.textContent = '';
                    }
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
                        if (modalError) {
                            modalError.textContent = '';
                        }
                    }
                });
            }

            if (closeSuccessBtn && successModal) {
                closeSuccessBtn.addEventListener("click", function () {
                    successModal.style.display = "none";
                });

                successModal.addEventListener("click", function (e) {
                    if (e.target === successModal) {
                        successModal.style.display = "none";
                    }
                });
            }
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