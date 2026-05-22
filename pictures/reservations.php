<?php
include 'database.php';

// Fetch all customers who have reservations
$customerSql = "
SELECT DISTINCT r.register_id, CONCAT(r.register_fname,' ', r.register_lname) AS customer_name
FROM reservations res
JOIN registers_tb r ON res.register_id = r.register_id
ORDER BY customer_name ASC
";
$customerResult = $conn->query($customerSql);

// Fetch all reservations and their items
$reservationsSql = "
SELECT res.*, ri.reservation_item_id, ri.product_id, ri.quantity, p.product_name, p.price, p.image_path,
       r.register_fname, r.register_lname
FROM reservations res
LEFT JOIN reservation_items ri ON res.reservation_id = ri.reservation_id
LEFT JOIN products p ON ri.product_id = p.product_id
JOIN registers_tb r ON res.register_id = r.register_id
ORDER BY res.reservation_date ASC, res.reservation_id ASC
";

$reservationItems = [];
$resResult = $conn->query($reservationsSql);
while($row = $resResult->fetch_assoc()){
    $reservationItems[$row['register_id']][$row['reservation_id']][] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


<style>
/* ==========================
   GENERAL BODY & FONT
========================== */
html, body { 
    font-family:'Poppins',sans-serif; 
    background:#fff; 
    color: #6D2E3A;
    margin:0; 
    padding:0; 
    height: 100%;
    overflow: auto;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE 10+ */
    overflow-x: hidden;
    overscroll-behavior: none;
}
html::-webkit-scrollbar, body::-webkit-scrollbar {
    display: none;
}

:root {
    --pink-very-light: #F8D7DC;
    --pink-soft: #E8A9B2;
    --pink-mid: #D96D84;
    --pink-dark: #6D2E3A;
    --bg-light: #fff5f9;
    --text-main: var(--pink-dark);
    --text-soft: var(--pink-mid);
    --border-main: var(--pink-mid);
    --btn-bg: var(--pink-mid);
    --btn-hover: var(--pink-dark);
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 100;
    flex-wrap: wrap;
}

.header-container i {
    color: #6d2e3a;
}

.heading {
    color: #6d2e3a;
}
/* ==========================
   SEARCH BAR & BUTTONS
========================== */
.search-container {
    display:flex;
    gap:5px;
    align-items:center;
    flex-wrap:wrap;
    position: relative; /* for suggestions */
}
.search-container input[type="text"] {
    padding:6px 10px;       
    border-radius:6px;      
    border:2px solid #6D2E3A;  /* border */
    background:#fff;
    color: #6D2E3A;           /* text */
    font-weight:normal;
    font-size:14px;          
    outline:none;
    transition: border 0.2s;
}
.search-container input[type="text"]:focus { 
    border-color:#6D2E3A; 
}
.search-container input::placeholder { 
    color: #6D2E3A; 
    opacity:0.6;
}
.search-container button {
    background: #a95469;
    color:#fff;
    border:none;
    padding:6px 12px;        
    border-radius:6px;      
    cursor:pointer;
    font-weight:bold;
    font-size:14px;          
}

/* ==========================
   SUGGESTIONS DROPDOWN
========================== */
#suggestions {
    position:absolute;
    top:100%;
    left:0;
    width:100%;
    max-height:150px;
    overflow-y:auto;
    border-radius:6px;
    z-index:10;
    display:none;
    box-shadow:0 2px 6px rgba(0,0,0,0.2);
    background:#fff;
}
#suggestions div {
    padding:8px 10px;
    cursor:pointer;
    color: #6d2e3a;
}
#suggestions div:hover { background: #ffe4ee; font-weight:bold; }
.suggestion-highlight { font-weight:bold; background:none; color: #6d2e3a; }

/* ==========================
   STATUS FILTER
========================== */
.search-status-container {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}
.status-container {
    display:flex;
    align-items:center;
    gap:8px;
}
.status-container label {
    font-weight:bold;
    color: #6d2e3a;
    font-size:14px;
}
.status-container select {
    border:2px solid #6D2E3A;
    border-radius:6px;
    padding:4px 6px;
    color: #6d2e3a;
    font-size:14px;
    background:#fff;
    outline:none;
    cursor:pointer;
    transition: border 0.2s;
}
.status-container select:focus {
    border-color: #6D2E3A;
    box-shadow:0 0 5px rgba(236,64,122,0.5);
}



/* ==========================
   CUSTOMER TABLE
========================== */
.customer-table-container {
    width: 95%;
    margin: 0 auto 30px auto;
    padding: 10px;
    border-radius: 10px;
    background: #fff;
    overflow: hidden; /* parent hides overflow */
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* =========================
   MAIN TABLE
   (SCROLLABLE BODY ONLY)
========================== */
#customerTable {
    width: 100%;
    max-width: none; 
    margin-bottom: 20px;
    border-collapse: separate; /* for rounded corners */
    border-spacing: 0;
    table-layout: fixed; /* para aligned rows */
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-top: 1px solid #f5a8c7;   /* only top border */
    border-bottom: 1px solid #f5a8c7; /* only bottom border */
}

/* table head sticky */
#customerTable thead {
    display: table;
    width:100%;
    table-layout: fixed;
}
#customerTable thead th {
    position: sticky;
    top:0;
    background-color: #a95469;
    color:white;
    font-weight:600;
    padding:12px;
    border-bottom:2px solid #6d2e3a;
    text-align:center;
    z-index:2;
}

/* tbody scrollable only */
#customerTable tbody {
    width: 100%;
    display: block;
    max-height: 400px; /* adjust for scroll */
    overflow-y: auto;
    background-color: white;
}

#customerTable tbody tr {
    display: table;
    width:100%;
    table-layout:fixed;
}

#customerTable tbody td {
    padding: 10px;
    border-bottom: 1px solid #f5a8c7;
    border-right: 1px solid #f5a8c7;
    text-align: center;
    color: #6d2e3a;
}
/* restore default scrollbar for table */
#customerTable tbody::-webkit-scrollbar {
    width: 8px;  /* width ng scrollbar */
}

#customerTable tbody::-webkit-scrollbar-thumb {
    background-color: rgba(169, 84, 105, 0.6); /* kulay ng scrollbar */
    border-radius: 4px;
}

#customerTable tbody::-webkit-scrollbar-track {
    background-color: #f1f1f1; /* track color */
}

/* borders & zebra stripes */
#customerTable tbody tr:nth-child(odd){ background-color:#fff;}
#customerTable tbody tr:nth-child(even){ background-color: #ffe4ee;}
#customerTable tbody td {
    padding:10px;
    border-bottom:1px solid #f5a8c7;
    border-right:1px solid #f5a8c7;
    text-align:center;
}
#customerTable tbody td:last-child{ border-right:none; }


/* View button */
.view-btn {
    background-color: #a95469;
    color:#fff;
    border:none;
    padding:5px 10px;
    border-radius:6px;
    cursor:pointer;
    font-weight:600;
    font-size:12px;
}
.view-btn:hover { background-color: #914060; }




/* ==========================
   HIGHLIGHT ROW
========================== */
.highlight-row, .highlight-row td {
    background-color: #6d2e3a !important;
    color: #ffffff !important;
    font-weight: bold !important;
}

/* Container ng buong reservations dashboard */
#reservationsFrame {
    width: 100%;
    height: calc(100vh - 20px);           /* full frame height ng screen, adjust kung gusto mo */
    display: flex;
    flex-direction: column;
    overflow: hidden;       /* page mismo hindi na mag-scroll */
    box-sizing: border-box;
    margin: 0;
}

/* Cards stay on top, hindi nag-scroll */
.reservation-cards-container {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    flex-shrink: 0;        /* hindi mai-compress ng scroll */
}


/* ==========================
   RESERVATIONS MODAL
========================== */
#reservationsModal { 
    display:none; 
    position:fixed; 
    top:0; 
    left:0; 
    width:100%; 
    height:100%; 
    background-color:rgba(0,0,0,0.5); 
    justify-content:center; 
    align-items:center; 
    z-index:1000; 
}

/* Modal content */
#reservationsModalContent { 
    display:flex;
    gap:10px;
    width:70%;            
    max-width:850px;       
    height:500px;           
    background-color:#fff; 
    border-radius:12px; 
    padding:20px; 
    color: #6d2e3a;
    position:relative;
    overflow:hidden; /* no scroll here */
}

/* Close button */
.close-btn { 
    cursor:pointer; 
    font-size:28px; 
    font-weight:bold; 
    position:absolute; 
    top:10px; 
    right:10px; 
    color: #6d2e3a;
}


/* ==========================
   MODALS: NO RESULTS & LOADING
========================== */
#noResultsModal, #loadingModal {
    display:none;
    position:fixed;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    background:#fff;
    color:#ec7699;
    padding:20px 30px;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.2);
    z-index:2000;
    text-align:center;
}
#loadingModal { background:rgba(0,0,0,0.5); z-index:9999; display:flex; justify-content:center; align-items:center; }
.loader-container { display:flex; flex-direction: column; align-items:center; gap:10px; }
.spinner { border:6px solid #ffe4ee; border-top:6px solid #6d2e3a; border-radius:50%; width:50px; height:50px; animation: spin 1s linear infinite; }
@keyframes spin {0% {transform:rotate(0deg);}100% {transform:rotate(360deg);}}
.loader-text { color: #a95469; font-weight:bold; font-size:16px; }

/* Scrollable content below header */
.reservation-scrollable {
    flex: 1;             /* fill remaining height */
    overflow-y: auto;    /* scroll happens here */
    padding-top: 10px;
}

/* Optional: smooth scroll */
.reservation-scrollable {
    scroll-behavior: smooth;
}


/* Hide scrollbar for all browsers */

/* For Chrome, Safari, and Opera */
.reservation-scrollable::-webkit-scrollbar,
#customerTable tbody::-webkit-scrollbar {
    display: none;
}

/* For IE, Edge */
.reservation-scrollable {
    -ms-overflow-style: none;
}

/* For Firefox */
.reservation-scrollable {
    scrollbar-width: none;
}
#customerTable tbody {
    scrollbar-width: none;
}

/* Right-side actions (search + filter) */
.reservation-actions {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

/* Search input */
#reservationSearch {
    padding: 5px 8px;
    border-radius: 5px;
    border: 2px solid #6d2e3a;
    font-size: 13px;
    color: #6d2e3a;
    background: #fff;
    outline: none;
    min-width: 150px;
}

/* Search buttons */
.search-container button {
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    background: #a95469;
    color: #fff;
}

/* Status filter */
.status-container {
    display: flex;
    align-items: center;
    gap: 5px;
     font-size: 14px;
    font-weight: 500;
}

.status-container label {
    font-weight: bold;
     font-size: 15px;
}

/* Space between card and table */
.reservation-table-wrapper {
    margin-top: 20px;
}

/* ==========================
   RESERVATION INTERNAL CARDS – DASHBOARD STYLE FIXED ito yung 4 cards style
========================== */
.reservation-cards-container {
    display: flex;
    gap: 78px;                   /* space between cards */
    flex-wrap: wrap;              /* wrap to next line on smaller screens */
    justify-content: center;      /* center cards like dashboard */
    margin-bottom: 20px;          /* spacing before table */
}

/* Individual card – match dashboard */
.res-card {
    flex: 1;                       /* grow/shrink equally */
    min-width: 180px;               /* won't shrink too small */
    max-width: 220px;               /* optional max width */
    background: linear-gradient(to bottom, #fff0f4, #ffe6eb); /* same gradient as dashboard cards */
    border-radius: 16px;            /* rounded like dashboard */
    padding: 20px 15px;             /* matches dashboard padding */
    text-align: center;
    box-shadow: 0 4px 12px rgba(236,64,122,0.15); /* subtle pinkish shadow like dashboard */
    color: #6d2e3a;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Card title / number inside */
.res-card-number {
    font-size: 22px;
    font-weight: 700;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .res-card {
        flex: 0 0 calc(50% - 10px); /* 2 per row */
    }
}

@media (max-width: 480px) {
    .res-card {
        flex: 1 1 100%; /* 1 per row */
    }
}

/* No Results Message */
#noResultsInline {
    display: none; /* hidden by default */
    text-align: center;
    font-size: 16px;
    color: #555;
    margin-top: 20px;
}

#noResultsInline p {
    margin: 0;
}


/*  modal */
/* === MODAL === */
.modal {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
    overflow-y: auto;
    z-index: 1000;
}

/* Modal content */
.modal-content {
    position: relative;
    background-color: white; /* white background */
    margin: 50px auto;
    padding: 20px;
    border-radius: 10px;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

/* Close button */
.modal-content .close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 28px;
    cursor: pointer;
    color: #6D2E3A;
    z-index: 1001;
}

/* Modal body (scrollable) */
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

/* Outer card per status */
.outerCardClass {
    border: 2px solid #D96D84;
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 15px;
    background-color: #fff; /* can use white or pastel if you prefer */
}

.outerCardClass h3 {
    color: #6D2E3A;
    margin-bottom: 10px;
}

/* Inner reservation card */
.innerCardClass {
    border: 1px solid #fff;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 10px;
    background-color: white;
}

/* Table in inner card */
.innerCardClass table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
}

.innerCardClass table th {
    background-color: #a95469;
    color: white;
    padding: 10px;
    text-align: left;
}

.innerCardClass table td {
    padding: 10px;
    text-align: left;
    color: #6D2E3A;
}

/* Table striped rows */
.innerCardClass table tbody tr:nth-child(even) {
    background-color: #F8D7DC; /* pink stripe */
}

.innerCardClass table tbody tr:nth-child(odd) {
    background-color: white;
}

/* Footer of inner card */
.innerCardClass > div:last-child {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    font-weight: bold;
}

/* Confirm button */
.innerCardClass button {
    background-color: #6D2E3A;
    color: white;
    border: none;
    padding: 6px 12px;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.2s;
}

.innerCardClass button:hover {
    background-color: #50232a;
}

/* === LOADING MODAL === */
#loadingModal {
    display: none; /* hidden by default */
    position: fixed;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 9999;
    transform: translate(-50%, -50%);
   

}

.loader-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.spinner {
    border: 6px solid #6d2e3a;
    border-top: 6px solid #D96D84;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loader-text {
    color: #6d2e3a;
    font-weight: bold;
    font-size: 16px;
}

/* Bottom Cards Section */
.reservation-bottom-cards {
    display: flex;
    flex-direction: column;
    gap: 30px; /* space between top products & recent reservations */
    padding: 10px 0;
}


/* Top Reserved Products Card */
.res-card-bottom {
    background: #fff;
    padding: 15px;
    box-shadow: 0 4px 10px rgba(109,46,58,0.15);
}

.res-card-bottom-header {
    font-size: 1.5em;
    font-weight: bold;
    color: #6D2E3A;
    margin-bottom: 15px;
    border-bottom: 2px solid #fff;
    padding-bottom: 5px;
}

/* Container for horizontal product cards */
.top-products-container {
    display: flex;
    flex-direction: row;
    gap: 30px; /* space between products */
    overflow-x: auto; /* scroll if more than available width */
    padding-bottom: 10px;
}

/* Each product mini-card */
.top-product-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 200px; /* fixed width per card */
    height: 330px;
    flex-shrink: 0; /* prevent shrinking */
    background: #fff;
    box-shadow: 0 4px 10px rgba(109,46,58,0.15);
    padding: 10px;
}

.top-product-card img {
    width: 100%;
    height: 200px;
    object-fit: cover; /* maintain aspect, crop if needed */
    margin-bottom: 10px;
    border-radius: 5px;
}

.top-product-name {
    font-weight: bold;
    font-size: 1.1em;
    color: #6D2E3A;
    margin-bottom: 5px;
    text-align: center;
}

.top-product-total {
    font-size: 1em;
    color: #D96D84;
    text-align: center;
}

/* Table Styling */
#pickupTodayTable {
    width: 100%;
    border-collapse: collapse;
    font-family: 'poppins', sans-serif;
}


/* Table Head */
#pickupTodayTable th {
    background-color: #a95469; /* keep your pink header */
    color: #fff; 
    font-weight: 600;
    padding: 12px 15px;
    text-align: center; /* center the text */
    border-bottom: 2px solid #fff;
}


/* Table Rows */
#pickupTodayTable td {
    padding: 10px 15px;
    border-bottom: 1px solid #f8d7dc;
    text-align: center; /* center the data */
}


/* Striped Rows */
#pickupTodayTable tbody tr:nth-child(even) {
    background-color: #fbf6f7ff;
}


/* No Data Row */
#pickupTodayTable tbody td[colspan="3"] {
    text-align: center;
    font-style: italic;
    color: #f8d7dc;
    padding: 20px 0;
}

.res-card-bottom-header {
    font-weight: 600;
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 9px; /* space between icon and text */
    color: #6d2e3a;
}

.card-icon {
    font-size: 20px; /* adjust icon size */
}


.swal2-small-popup {
    font-size: 0.9rem !important;
    max-width: 280px !important;   /* hindi malapad */
    padding: 0.5em 1em !important; /* mas maikli yung taas */
}
.swal2-small-icon {
    font-size: 1.2em !important;    /* maliit na icon */
}



/* Hide scrollbar  ng top products*/
#topProductsContainer {
    overflow: hidden; /* hide scrollbar by default */
}

/* Show vertical scroll kapag hover */
#topProductsContainer:hover {
    overflow-y: auto;
}

/* Optional: style scrollbar para mas maliit at clean */
#topProductsContainer::-webkit-scrollbar {
    width: 6px; /* width ng scrollbar */
}

#topProductsContainer::-webkit-scrollbar-thumb {
    background-color: rgba(169, 84, 105, 0.6); /* kulay ng scrollbar */
    border-radius: 3px;
}


.res-card {
  background: #f8d7dc;}


  /* ===== RESERVATIONS RESPONSIVE ===== */

/* Tablet down */
@media (max-width: 768px) {

    .header-container {
        flex-direction: column;
        gap: 12px;
        text-align: left;           /* ← Gawing left align ang header container */
        align-items: flex-start;    /* ← Left align content sa container */
    }
    .heading {
        font-size: 25px !important;
        justify-content: flex-start; /* ← Icon + text ay left-justify sa flex */
        text-align: left !important; /* ← Text ng header ay left aligned */
        width: 100%;
    }

    .reservation-cards-container {
        gap: 15px !important;
    }

    .res-card, .reservation-card, .card-table {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100vw !important;
        margin-left: 12px !important;
        margin-right: 12px !important;
        border-radius: 15px;
    }
    
    .reservation-actions {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .reservation-actions .search-container {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
    }

    .reservation-actions input,
    .reservation-actions button,
    .reservation-actions select {
        font-size: 13px;
        padding: 5px 8px;
    }

    .res-card {
        flex: 1 1 calc(50% - 10px);
        min-width: 150px;
    }

    /* Bottom cards – gawin vertical sa tablet */
    .reservation-bottom-cards {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .res-card-bottom {
        width: 90vw !important;     /* Medyo mas malapad kaysa dati, pero may konting spacing sa gilid */
        max-width: 90vw !important; /* Para hindi sumobra, kahit ano pa laman */
        margin: 0 auto 10px auto;   /* Centered at may space sa ilalim */
        box-sizing: border-box;
        border-radius: 10px;        /* Optional: para mas card-like ang dating */
        padding: 12px;              /* Optional: dagdag space sa loob */
    }

    #customerTable {
        max-width: 430px !important; /* para lumitaw horizontal scroll kung masikip */
        font-size: 13px;
    }

    #customerTable th,
    #customerTable td {
        padding: 4px 6px;
    }

    .top-product-card {
        height: 300px;
    }
}

/* Phone size: cards + tables stacked */
@media (max-width: 480px) {
    /* Stat cards 1 per row */
    .res-card {
        flex: 1 1 100%;
    }

    /* Bottom cards full width stacked */
    .reservation-bottom-cards {
        gap: 12px;
    }

    /* Table text smaller */
    #customerTable {
        font-size: 12px;
    }

    .view-btn {
        font-size: 11px;
        padding: 3px 6px;
    }

    /* Modal: mas sakto sa phone */
    #reservationModal .modal-content {
        width: 95%;
        max-width: 420px;
        margin: 0 auto;
    }

    #modalCardsContainer {
        max-height: 70vh;
    }
}

@media (max-width: 480px) {
    .res-card-bottom {
        max-width: 420px;
        margin: 0 auto;
    }
}

@media (max-width: 600px) {
    .photos-grid, .products-grid, .top-products-container {
        display: flex !important;
        flex-direction: column !important;
        gap: 16px !important;
        align-items: center;
        width: 100%;
    }
    .photos-grid > div,
    .products-grid > div,
    .top-products-container > div {
        width: 100% !important;
        max-width: 350px; /* adjust as needed */
        margin: 0 auto;
    }
    .photos-grid img,
    .products-grid img,
    .top-products-container img {
        width: 100% !important;
        max-width: 180px;
        height: auto;
        display: block;
        margin: 0 auto 8px auto;
    }
}



</style>


<!-- === RESERVATIONS FRAME === -->
<div id="reservationsFrame">

        <div class="header-container">
            <h1 class="heading" style="display: flex; align-items: center; font-size: 2em; font-weight: bold;">
                <i class="fa-solid fa-box" style="margin-left: 12px; margin-right: 12px;"></i> Reservations
            </h1>

            <div class="reservation-actions">
                <div class="search-container">
                    <input type="text" id="reservationSearch" placeholder="Search customer..." oninput="showSuggestions()">
                    <button onclick="searchReservations()">Search</button>
                    <button onclick="refreshReservations()">Refresh</button>
                    <div id="suggestions"></div>
                </div>

                <div class="status-container">
                    <label for="statusFilter" class="status-label">STATUS :</label>
                    <select id="statusFilter" onchange="filterReservations()">
                        <option value="pending" selected>Pending</option>
                        <option value="picked_up">Picked Up</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

    <!-- === SCROLLABLE CONTENT === -->
    <div class="reservation-scrollable">

        <!-- === RESERVATION DASHBOARD CARDS === -->
        <div class="reservation-cards-container">
            <div class="res-card">
                <div class="res-card-title">Total Reservations</div>
                <div class="res-card-number" id="totalReservations">0</div>
            </div>
            <div class="res-card">
                <div class="res-card-title">Cancelled</div>
                <div class="res-card-number" id="canceledReservations">0</div>
            </div>
            <div class="res-card">
                <div class="res-card-title">Pending </div>
                <div class="res-card-number" id="pendingReservations">0</div>
            </div>
            <div class="res-card">
                <div class="res-card-title">Picked Up </div>
                <div class="res-card-number" id="pickedUpReservations">0</div>
            </div>
        </div>

        <!-- === CUSTOMER TABLE === -->
        <div>
            <table id="customerTable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Customer Name</th>
                        <th>Total Reservations</th>
                        <th>Reservation Date</th>
                        <th>Pickup Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($customer = $customerResult->fetch_assoc()): 
                        $custId = $customer['register_id'];
                        $totalReservations = isset($reservationItems[$custId]) ? count($reservationItems[$custId]) : 0;

                        $firstReservation = $reservationItems[$custId] ?? [];
                        $firstResId = !empty($firstReservation) ? array_keys($firstReservation)[0] : null;
                        $pickupDate = $firstResId ? $firstReservation[$firstResId][0]['pickup_date'] : '';
                        $reservationDate = $firstResId ? $firstReservation[$firstResId][0]['reservation_date'] : '';
                    ?>
                    <tr>
                        <td><button class="view-btn" onclick="viewReservations('<?= $custId ?>')">View All</button></td>
                        <td><?= htmlspecialchars($customer['customer_name']) ?></td>
                        <td><?= $totalReservations ?></td>
                        <td><?= $reservationDate ?></td>
                        <td><?= $pickupDate ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- NO RESULTS MESSAGE -->
            <div id="noResultsInline" class="text-muted"><i>No reservations found.</i></div>
        </div>

        <!-- === Bottom Cards Section === -->
        <div class="reservation-bottom-cards">
            <!-- Recent Reservations -->
            <div class="res-card-bottom">
                <div class="res-card-bottom-header">
                    <i class="fas fa-clock card-icon"></i> PICK UP TODAY!
                </div>
                <table id="pickupTodayTable">
                    <thead>
                        <tr>
                            <th style="width: 300px;">Customer Name</th>
                            <th style="width: 300px;">Reservation ID</th>
                            <th style="width: 300px;">Total Items</th>
                        </tr>
                    </thead>
                    <tbody id="recentReservationsBody"></tbody>
                </table>
            </div>

            <!-- Top Reserved Products -->
            <div class="res-card-bottom">
                <div class="res-card-bottom-header">
                    <i class="fas fa-box card-icon"></i> TOP RESERVE PRODUCTS
                </div>
                <div class="top-products-container justify-content-between align-items-center" id="topProductsContainer"></div>
            </div>
        </div>
    </div>
</div>

<!-- === LOADING MODAL === -->
<div id="loadingModal">
    <div class="loader-container">
        <div class="spinner"></div>
        <div class="loader-text">Loading...</div>
    </div>
</div>

 
<!-- === MODAL === -->
<div id="reservationModal" class="modal" style="display:none;">
  <div class="modal-content">
    <!-- Header with Title and Close -->
    <div id="modalHeader" style="display:flex; justify-content:space-between; align-items:center; padding:10px; background:#6D2E3A; color:white; position:sticky; top:0; z-index:10;">
      <h2 id="modalTitle" style="margin:0; font-size:18px;">Reservations</h2>
      <span class="close-btn" style="cursor:pointer; font-size:20px; color:white;">&times;</span>
    </div>

    <!-- Scrollable Cards Container -->
    <div id="modalCardsContainer" style="max-height:600px; overflow-y:auto; padding:10px;"></div>
  </div>
</div>


<script>

function viewReservations(custId, statusFilter = 'pending') {

    
    showLoading(); // ⬅️ SHOW LOADING AGAD

    const modal = document.getElementById('reservationModal');
    const cardsContainer = document.getElementById('modalCardsContainer');
    const modalTitle = document.getElementById('modalTitle');
    const modalHeader = document.getElementById('modalHeader'); // <- add this
    if (!modal || !cardsContainer || !modalTitle || !modalHeader) return;

    // Update modal title based on status
    switch(statusFilter){
        case 'pending':
            modalTitle.textContent = 'Pending Reservations';
            modalHeader.style.background = '#6D2E3A';
            break;
        case 'picked_up':
            modalTitle.textContent = 'Picked Up Reservations';
            modalHeader.style.background = '#6D2E3A';
            break;
        case 'cancelled':
            modalTitle.textContent = 'Cancelled Reservations';
            modalHeader.style.background = '#A93232';
            break;
        default:
            modalTitle.textContent = 'Reservations';
            modalHeader.style.background = '#6D2E3A';
    }

    let url = `view_reservations.php?custId=${custId}`;
    if (statusFilter) url += `&status=${statusFilter}`;

    let startTime = Date.now(); // para malaman gaano kabilis ang fetch

        fetch(url)
            .then(res => res.text())
            .then(html => {
                html = html.trim();
                let elapsed = Date.now() - startTime;
                let minimum = 500; // 500ms (half second), pwede 700 para mas kita

                let delay = Math.max(0, minimum - elapsed);

                setTimeout(() => {

                    if (!html) {
                        modal.style.display = 'none';
                        removeCustomerRowIfNoPending(custId);
                        hideLoading();
                        return;
                    }

                    cardsContainer.innerHTML = html;

                    document.querySelectorAll('.confirmBtn').forEach(btn => {
                        btn.onclick = () => {
                            const resId = btn.dataset.resid;
                            confirmReservation(resId, custId, statusFilter);
                        }
                    });

                    modal.style.display = 'block';
                    hideLoading();

                }, delay);
            })
            .catch(err => {
                console.error(err);
                hideLoading();
            });
        }


// loading

function showLoading() {
    document.getElementById("loadingModal").style.display = "flex";
}
function hideLoading() {
    document.getElementById("loadingModal").style.display = "none";
}

function removeCustomerRowIfNoPending(custId) {
    // Hanapin ang row sa customerTable para sa specific customer
    const rows = Array.from(document.querySelectorAll('#customerTable tbody tr'));
    for (const row of rows) {
        const btn = row.querySelector('button.view-btn');
        // btn onclick usually like: viewReservations(123,'pending')
        if (btn && btn.getAttribute('onclick').includes(custId)) {
            // Optional: i-check kung Total Reservations column = 0 bago alisin
            const totalCell = row.cells[2];
            if (totalCell && parseInt(totalCell.textContent, 10) === 0) {
                row.remove();
                break;
            }
        }
    }
    // PAALALA: Kahit anong branch, mag-hide na ng loadingModal dito
    hideLoading();
}

function showNoResultsIfTableEmpty() {
    const tbody = document.querySelector('#customerTable tbody');
    if (tbody.children.length === 0) {
        document.getElementById('noResultsInline').style.display = 'block';
    }
}

function decrementReservationCount(custId) {
    const row = Array.from(document.querySelectorAll('#customerTable tbody tr')).find(r =>
        r.querySelector('button.view-btn')?.getAttribute('onclick').includes(custId)
    );
    if (!row) return;
    const totalCell = row.cells[2]; // Total Reservations column
    let total = parseInt(totalCell.textContent, 10) || 0;
    total = Math.max(0, total - 1);
    totalCell.textContent = total;
    if (total === 0) {
        row.remove();
        showNoResultsIfTableEmpty(); // ← TAWAG dito after remove!
    }
}

// Small modal style config
const swalSmallStyle = {
    width: 'auto',        // auto-fit width
    padding: '0.5em 1em', // konting padding lang
    customClass: {
        popup: 'swal2-small-popup',
        icon: 'swal2-small-icon'
    }
};

// CSS for small popup & icon (ilagay sa HTML <style> o JS head)
const style = document.createElement('style');
style.innerHTML = `
.swal2-small-popup {
    font-size: 0.9rem !important;
    max-width: 280px !important;   /* hindi malapad */
    padding: 0.8em 1em !important; /* liitan ang taas */
}
.swal2-small-icon {
    font-size: 1.2em !important;  /* maliit na icon */
}
`;
document.head.appendChild(style);

// Confirm reservation function
// Confirm reservation function
async function confirmReservation(reservation_id, custId, statusFilter = null) {
    // 1️⃣ CONFIRMATION
    const confirmResult = await Swal.fire({
        title: 'Are you sure?',
        text: "Approve this reservation?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6D2E3A',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Yes!',
        ...swalSmallStyle
    });

    if (!confirmResult.isConfirmed) return;

    // 2️⃣ LOADING
    Swal.fire({
        title: 'Processing Request...',
        html: '<b style="color:#6D2E3A;">Please wait...</b>',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        ...swalSmallStyle
    });

    // Small delay for smoother effect
    await new Promise(resolve => setTimeout(resolve, 800));

    try {
        // 3️⃣ FETCH
        const response = await fetch('approve_reservation.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ reservation_id })
        });

        const data = await response.json();

        // 4️⃣ CLOSE LOADING + ENSURE loadingModal hidden!
        Swal.close();
        hideLoading(); // <-- ADD this line, fallback para laging mawawala ang loadingModal!
        await new Promise(resolve => setTimeout(resolve, 600));

        // 5️⃣ SUCCESS / ERROR
        if (data.success) {
            await Swal.fire({
                title: 'Approved!',
                text: 'Reservation approved successfully!',
                icon: 'success',
                confirmButtonColor: '#6D2E3A',
                ...swalSmallStyle
            });

            decrementReservationCount(custId);
            viewReservations(custId, statusFilter);

        } else {
            await Swal.fire({
                title: 'Error',
                text: data.message || 'Unknown error',
                icon: 'error',
                confirmButtonColor: '#6D2E3A',
                ...swalSmallStyle
            });
        }

    } catch (error) {
        Swal.close();
        hideLoading(); // <-- ADD also here, para safe from errors!
        await Swal.fire({
            title: 'Error',
            text: 'Something went wrong',
            icon: 'error',
            confirmButtonColor: '#6D2E3A',
            ...swalSmallStyle
        });
    }
}

// Close modal
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('close-btn')) {
        const modal = document.getElementById('reservationModal');
        if(modal) modal.style.display = 'none';
    }
});



// di to kasali AHAH wag mo alisin
const reservationData = <?= json_encode($reservationItems); ?>;
const allCustomers = Array.from(document.querySelectorAll('#customerTable tbody tr')).map(tr=>({ tr, name:tr.children[1].textContent }));
let filteredCustomers = [...allCustomers];


// =========================
//     AUTO UPDATE TOTAL
// =========================
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('quantity-input')) {
        const row = e.target.closest('tr');
        const price = parseFloat(row.children[2].textContent.replace('₱', ''));
        const qty = parseInt(e.target.value);
        row.children[4].textContent = '₱' + (price * qty).toFixed(2);
    }
});

// =========================
//     AUTOCOMPLETE SEARCH
// =========================
function showSuggestions() {
    const input = document.getElementById('reservationSearch').value.toLowerCase();
    const sugDiv = document.getElementById('suggestions');
    sugDiv.innerHTML = '';

    if (input.length === 0) {
        sugDiv.style.display = 'none';
        return;
    }

    const matched = filteredCustomers.filter(c =>
        c.name.toLowerCase().includes(input)
    );

    matched.forEach(c => {
        const regex = new RegExp(`(${input})`, 'i');
        const html = c.name.replace(regex, `<span class="suggestion-highlight">$1</span>`);

        const div = document.createElement('div');
        div.innerHTML = html;

        div.addEventListener('click', () => {
            document.getElementById('reservationSearch').value = c.name;
            searchReservations();
            sugDiv.style.display = 'none';
        });

        sugDiv.appendChild(div);
    });

    sugDiv.style.display = matched.length > 0 ? 'block' : 'none';
}
// ======================
//     SEARCH FUNCTION
// ======================
function searchReservations() {
    showLoading(); // show loading immediately

    setTimeout(() => { // delay so spinner is visible
        const input = document.getElementById('reservationSearch').value.toLowerCase();
        let found = false;

        // remove previous highlights
        allCustomers.forEach(c => c.tr.classList.remove('highlight-row'));

        // highlight matching rows
        filteredCustomers.forEach(c => {
            if (c.name.toLowerCase() === input) {
                c.tr.classList.add('highlight-row');
                c.tr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                found = true;
            }
        });

        // only show "no results" if nothing found
        const noResultsModal = document.getElementById('noResultsModal');
        if (noResultsModal) noResultsModal.style.display = found ? 'none' : 'block';

        // keep suggestions visible! (don’t hide)
        // document.getElementById('suggestions').style.display = 'none'; <-- remove this

        hideLoading(); // hide spinner after processing
    }, 500); // small delay for spinner to show
}

// =====================
//     REFRESH SEARCH
// =====================
function refreshReservations() {
    showLoading(); // show spinner

    setTimeout(() => {
        document.getElementById('reservationSearch').value = '';

        // remove highlights
        allCustomers.forEach(c => c.tr.classList.remove('highlight-row'));
        filteredCustomers.forEach(c => c.tr.classList.remove('highlight-row'));

        // rebuild table
        filterReservations();

        // keep suggestions visible! (don’t hide)
        // document.getElementById('suggestions').style.display = 'none'; <-- remove this
        const noResultsModal = document.getElementById('noResultsModal');
        if (noResultsModal) noResultsModal.style.display = 'none';

        // scroll top
        const tbody = document.querySelector('#customerTable tbody');
        if (tbody) tbody.scrollTop = 0;

        hideLoading(); // hide spinner after done
    }, 500);
}


// sa 4 cards to bes
function updateReservationCards() {
    let total = 0, canceled = 0, pending = 0, pickedUp = 0;

    for (const custId in reservationData) {
        for (const resId in reservationData[custId]) {
            const res = reservationData[custId][resId][0];
            total++;
            if (res.status === 'cancelled') canceled++;
            else if (res.status === 'pending') pending++;
            else if (res.status === 'picked_up') pickedUp++;
        }
    }

    document.getElementById('totalReservations').textContent = total;
    document.getElementById('canceledReservations').textContent = canceled;
    document.getElementById('pendingReservations').textContent = pending;
    document.getElementById('pickedUpReservations').textContent = pickedUp;
}
updateReservationCards();


function filterReservations() {
    const status = document.getElementById('statusFilter').value; // pending, picked_up, canceled, all
    const table = document.getElementById('customerTable');
    const tbody = table.querySelector('tbody');
    const noResults = document.getElementById('noResultsInline');

    tbody.innerHTML = '';

    // Table headers remain the same
    const thead = table.querySelector('thead tr');
    thead.innerHTML = `<th>Action</th><th>Customer Name</th><th>Total Reservations</th>`;

    let hasData = false;
    filteredCustomers = []; // reset

    for (const custId in reservationData) {
        const reservations = reservationData[custId];

        // Filter reservations based on status (no 'all' anymore)
        const filteredRes = Object.values(reservations).filter(resArr => {
            const res = resArr[0];
            return res.status === status;
        });

        if (filteredRes.length === 0) continue;
        hasData = true;

        const firstRes = filteredRes[0][0];
        const customerName = firstRes.register_fname ? `${firstRes.register_fname} ${firstRes.register_lname}` : 'Unknown';

        const tr = document.createElement('tr');
        const custIdStr = custId.toString();

         // Always same columns
        tr.innerHTML = `
            <td><button class="view-btn" onclick="viewReservations('${custIdStr}', '${status}')">View</button></td>
            <td>${customerName}</td>
            <td>${filteredRes.length}</td>
        `;

        tbody.appendChild(tr);
        filteredCustomers.push({ tr, name: customerName });
    }

    noResults.style.display = hasData ? 'none' : 'block';
}

// Wrap filterReservations with loading
function filterReservationsWithLoading() {
    showLoading();
    setTimeout(() => {
        filterReservations();
        hideLoading();
    }, 500);
}
     
// On page load, default to pending
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('statusFilter').value = 'pending';
    filterReservations();
});

// Top Reserved Products
fetch('top_products.php')
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('topProductsContainer');
        container.innerHTML = '';

        // Limit to top 5 products
        data.slice(0, 5).forEach(p => {

            let imgPath = p.image_path || '';
            // --- SAME LOGIC AS order_dashboard ---
            if (imgPath && !imgPath.startsWith('pictures/')) {
                imgPath = 'pictures/' + imgPath;
            }

            const div = document.createElement('div');
            div.className = 'top-product-card';
            div.innerHTML = `
                <img src="${imgPath}" 
                     alt="${p.product_name}"
                     onerror="this.src='pictures/no-image.png'">
                <div class="top-product-name">${p.product_name}</div>
                <div class="top-product-total">${p.total_reserved} reservations</div>
            `;
            container.appendChild(div);
        });
    })
    .catch(err => console.error(err));

function loadTodayReservations() {
    fetch('recent_reservations.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('recentReservationsBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="3" class="text-muted">No reservations today.</td>`;
                tbody.appendChild(tr);
                return;
            }

            data.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${r.customer}</td>
                    <td>${r.reservation_id}</td>
                    <td>${r.total_items}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => console.error(err));
}

// Run after DOM loads
document.addEventListener('DOMContentLoaded', loadTodayReservations);

</script>

</body>
</html>