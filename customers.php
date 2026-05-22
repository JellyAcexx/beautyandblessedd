<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ block kung hindi naka-login na admin
if (!isset($_SESSION['admin_email'])) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Location: log_admin.php");
    exit();
}

include 'database.php';

/* ==========================
   FETCH ALL PURCHASES
   ========================== */
$allSql = "
SELECT 
    p.purchase_id,
    p.totalAmount,
    p.purchaseDate,
    p.purchaseMethod,

    CASE 
        WHEN p.purchaseMethod = 'Walk-In' 
            THEN w.walk_in_name
        WHEN p.purchaseMethod = 'Reservation'
            THEN CONCAT(reg.register_fname, ' ', reg.register_lname)
        ELSE 'Unknown'
    END AS customer_name

FROM purchase p

LEFT JOIN walk_in w 
    ON p.walk_in_id = w.walk_in_id

LEFT JOIN reservations res
    ON p.reservation_id = res.reservation_id

LEFT JOIN registers_tb reg
    ON res.register_id = reg.register_id

ORDER BY p.purchaseDate DESC
";



$allResult = $conn->query($allSql);

/* For autocomplete search */
$customer_names = [];
if ($allResult && $allResult->num_rows > 0) {
    $allResult->data_seek(0);
    while ($c = $allResult->fetch_assoc()) {
        if (!empty($c['customer_name'])) {
            $customer_names[] = $c['customer_name'];
        }
    }
    $allResult->data_seek(0);
}
?>

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
    font-family:'Poppins',sans-serif; 
    background:#fff; 
    color:#6d2E3A; 
    margin:0; padding:0; 
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

.card-table {
    min-height: 640px;
}

/* SEARCH BAR */
.search-container {
    position:relative;
    display:flex;
    align-items:center;
    gap:3px;
    flex-wrap:wrap;
}

/* PLACEHOLDER COLOR */
#searchInput::placeholder {
    color:#6D2E3A;
    opacity:0.7;
}

/* BUTTONS */
#searchBtn, #refreshBtn {
    background: #A95469; /* SAME COLOR AS TITLE */
    color:#fff;
    border:none;
}

#filterSelect {
    background:#fff;
    border:2px solid #6d2E3A; /* search/filter border */
    color: #6D2E3A; /* filter text color */
    font-size: 14px;
    font-weight: 500;
}

/* Status text */
.status-label {
    color:#6D2E3A;
    font-weight:bold;
    font-size: 15px;
}

.table-wrapper { margin-top:20px; }

/* SEARCH BAR FINAL VERSION */
#searchInput {
    padding:5px 8px;
    border-radius:5px;
    border:2px solid #6D2E3A;/* pink border */
    font-size:13px;
    color: #A95469; /* text inside */
    background:#fff;
    outline:none;
    flex:1;
    min-width:150px;
}

/* PLACEHOLDER COLOR */
#searchInput::placeholder {
    color: #6d2e3a;
    opacity:0.6;

}


/* BUTTONS */
#searchBtn, #refreshBtn, #filterSelect {
    padding:5px 10px;
    border-radius:5px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
    border:none;
}

/* BUTTONS FINAL VERSION */
#searchBtn, #refreshBtn {
    background:#a95469; /* warm mid-pink */
    color:#fff;
    border:none;
    padding:5px 10px;
    border-radius:5px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
}

/* FILTER DROPDOWN FINAL VERSION */
#filterSelect {
    background:#fff;
    border:2px solid #6d2e3a; /* same border as search */
    color: #6d2e3a; /* same text color */
    padding:5px 10px;
    border-radius:5px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
}

/* AUTOCOMPLETE */
.suggestions { 
    position:absolute; 
    top:38px; left:0; right:0; 
    background:#fff; 
    border:1px solid #6d2e3a; 
    border-radius:6px; 
    max-height:150px; 
    overflow-y:auto; 
    display:none; 
    z-index:10; 
}
.suggestion-item { 
    padding:8px; 
    cursor:pointer; 
    color:#6d2e3a; 
}
.suggestion-item:hover { 
    background:#F8D7DC; 
    font-weight:bold; 
}

/* SCROLL TABLE */
.table-scroll {
    max-height: 700px;
    overflow-y: auto;
}

/* TABLE */
#masterTable {
    width:100%;
    border-collapse:collapse;
}

#masterTable thead th {
    position:sticky;
    top:0;
    background:#A95469; /* header */
    color:#fff;
    font-weight:600;
    padding:12px;
    border-bottom:2px solid #6d2e3a;
    text-align:center;
    z-index:2;
}

#masterTable tbody td {
    padding:10px;
    border-bottom:1px solid #F8D7DC;
    border-right:1px solid #F8D7DC;
    color:#6D2E3A;  /* deep wine */
    text-align:center;
}
#masterTable tbody td:last-child { border-right:none; }

#masterTable tbody tr:nth-child(even) {
    background:#F8D7DC;
}
#masterTable tbody tr:nth-child(odd) {
    background:#fff;
}

/* VIEW BUTTON */
.view-btn {
    background:#a95469;
    color:#fff;
    border:none;
    padding:4px 7px;
    border-radius:6px;
    cursor:pointer;
    font-weight:600;
    font-size:12px;
}

/* HIGHLIGHTED SEARCH */
.highlight, .highlight td { 
    background:#A95469 !important; 
    color:white !important; 
    font-weight:bold; 
}
.view-btn:hover { background-color: #914060; }

/* MODAL */
#detailsModal { 
    display:none; position:fixed; top:0; left:0; 
    width:100%; height:100%; 
    background:rgba(0,0,0,0.5); 
    justify-content:center; 
    align-items:center;
}
#detailsModalContent { 
    background:white; padding:20px; 
    border-radius:12px; min-width:300px; 
    color:#A95469; 
}
#detailsModalContent th { 
    background:#D96D84; 
    color:#fff; padding:8px; 
}

/* NO RESULT MODAL */
#noResultModal { 
    display:none; position:fixed; top:0; left:0; 
    width:100%; height:100%; 
    background:rgba(0,0,0,0.5); 
    justify-content:center; 
    align-items:center;
}
#noResultModal div { 
    background:#fff; padding:20px; border-radius:12px; 
}

/* PRINT */
@media print {
    body * { visibility: hidden; }
    #detailsModalContent, #detailsModalContent * {
        visibility: visible;
    }
    #detailsModalContent {
        position:absolute;
        left:0; top:0;
        width:100%;
        box-shadow:none;
        border:none;
    }
    #closeDetailsModal, #detailsModal button {
        display:none !important;
    }
}

/* LOADING */
#loadingModal {
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background-color:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:9999;
}

.loader-container {
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:10px;
}

.spinner {
    border:6px solid #6d2e3a;
    border-top:6px solid #D96D84;
    border-radius:50%;
    width:50px;
    height:50px;
    animation:spin 1s linear infinite;
}

@keyframes spin {
    0% { transform:rotate(0deg); }
    100% { transform:rotate(360deg); }
}

.loader-text {
    color: #6d2e3a;
    font-weight:bold;
    font-size:16px;
}

  /* Normal state */
    #exportPDFbtn {
        border: 2px solid #6D2E3A !important;
        color: #6D2E3A !important;
        background: #fff;
    }

    /* Hover state */
    #exportPDFbtn:hover {
        background: #E8A9B2; /* light palette color */
        color: #6D2E3A !important; /* dark text so visible */
        border-color: #6D2E3A !important; /* keep border dark */
    }

     /* PRINT BUTTON - NORMAL */
    #printBtn {
        background: #fff !important;
        color: #6D2E3A !important;
        border: 2px solid #6D2E3A !important;
    }

    /* PRINT BUTTON - HOVER */
    #printBtn:hover {
        background: #E8A9B2 !important; /* lighter shade from palette */
        border-color: #A95469 !important;
        color: #fff !important; /* keep text readable */
    }

    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%) !important;
        box-shadow: 0 10px 28px rgba(0,0,0,0.12) !important;
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

    /* Responsive for mobile */
@media (max-width: 768px) {
    .search-container {
        flex-direction: column;
        gap: 10px;
        width: 100%;
        align-items: center;
    }

    .mobile-btn-row {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        width: 100%;
        gap: 5px;
        margin-bottom: 2px;
    }
    #searchInput {
        flex: 2;
        min-width: 0;
        max-width: 200px;
    }
    #searchBtn, #refreshBtn {
        flex: 1;
        min-width: 60px;
        max-width: 70px;
    }

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
}

</style>



<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <div class="header-container">
        <h1 class="heading" style="display: flex; align-items: center; font-size: 2em; font-weight: bold;">
            <i class="fa-solid fa-users" style="margin-left: 12px; margin-right: 12px;"></i>
            Customers
        </h1>
        <div class="search-container">
            <div class="mobile-btn-row">
                <input type="text" id="searchInput" placeholder="Search customer...">
                <div class="suggestions" id="suggestionsList"></div>
                <button id="searchBtn" onclick="searchCustomer()">Search</button>
                <button id="refreshBtn" onclick="refreshTable()">Refresh</button>
            </div>
            <div class="mobile-status-row">
                <span class="status-label">STATUS:</span>
                <select id="filterSelect" onchange="filterTable()">
                    <option value="All">All</option>
                    <option value="Walk-In">Walk-In</option>
                    <option value="Reservation">Reservation</option>
                </select>
            </div>
        </div>
    </div>

<!-- TABLE -->
<div class="card-table mt-3">
    <div class="customer-table-container">
        <div class="table-scroll">
            <table id="masterTable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Customer Name</th>
                        <th>Total Amount</th>
                        <th>Purchase Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($allResult->num_rows>0): while($row = $allResult->fetch_assoc()): ?>
                    <tr data-type="<?= $row['purchaseMethod']; ?>">
                        <td>
                        <button class="view-btn" data-purchase-id="<?= $row['purchase_id']; ?>">View</button>
                        </td>
                        <td><?= htmlspecialchars($row['customer_name']); ?></td>
                        <td>₱<?= number_format($row['totalAmount'],2); ?></td>
                        <td><?= $row['purchaseDate'] ? date('F j, Y', strtotime($row['purchaseDate'])) : '-'; ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="4">No customers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal">
    <div class="loader-container">
        <div class="spinner"></div>
        <div class="loader-text">Please wait...</div>
    </div>
</div>

<!-- Receipt Modal -->
<div id="detailsModal" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    justify-content:center;
    align-items:center;
    z-index:1000;
">

  <div id="detailsModalContent" style="
      position:relative;
      background:#fff;
      border:2px solid #6D2E3A;
      border-radius:10px;
      padding:25px 35px;
      color: #6D2E3A;
      font-family:'Poppins', sans-serif;
      box-shadow:0 8px 15px rgba(0,0,0,0.3);
      width:auto;
      min-width:320px;
      max-width:450px;
      text-align:left;
  ">
      <!-- X / Close -->
      <span id="closeDetailsModal" style="
          position:absolute;
          top:10px;
          right:15px;
          cursor:pointer;
          font-weight:bold;
          font-size:20px;
          color:#6D2E3A;
      ">×</span>

      <!-- Header -->
      <h2 style="
          text-align:center;
          font-weight:700;
          margin:0;
          margin-bottom:5px;
          font-size:22px;
          color: #6D2E3A;
      ">
          Beauty and Blessed
      </h2>


      <!-- Address + Contact -->
      <div style="
          text-align:center;
          font-size:12px;
          margin-top:-3px;
          margin-bottom:10px;
          color:#A95469;
      ">
          Brgy. 4, Nasugbu Batangas<br>
          +63 993 726 0000
      </div>

      <div style="border-bottom:2px dashed #A95469; margin-bottom:10px;"></div>


       <!-- Customer Info -->
      <div style="margin:10px 0; line-height:1.6; font-size:14px; color:#6D2E3A;">
          <div><strong>Customer:</strong> <span id="receiptCustomer"></span></div>
          <div><strong>Purchase Method:</strong> <span id="receiptMethod"></span></div>
          <div><strong>Purchase Date:</strong> <span id="receiptDate"></span></div>
      </div>

      <!-- Item List Style Container -->
      <div id="modalItems" style="
          border-top:1px dashed #A95469;
          border-bottom:1px dashed #A95469;
          padding:12px 0;
          font-size:14px;
          margin-bottom:10px;
      ">
          <!-- JS will inject items here -->
      </div>

       <!-- Total -->
      <div style="
          text-align:right;
          font-weight:700;
          font-size:16px;
          margin-top:10px;
          color:#6D2E3A;
      ">
          Total: <span id="modalTotal">₱0.00</span>
      </div>

      <!-- Footer -->
      <div style="
          text-align:center;
          margin-top:12px;
          font-style:italic;
          font-size:13px;
          color:#A95469;
      ">
          Thank you for your purchase!
      </div>

       <!-- Action Buttons -->
      <div style="text-align:center; margin-top:15px; display:flex; justify-content:center; gap:8px;">
          <button id="printBtn" onclick="printReceipt()" style="
            border-radius:5px;
            padding:5px 12px;
            font-weight:600;
            cursor:pointer;
            font-size:13px;
        ">
            🖨️ Print
        </button>

         <button id="exportPDFbtn" onclick="exportPDF()" style="
                border-radius:5px;
                padding:4px 10px;
                font-weight:600;
                cursor:pointer;
                font-size:13px;
            ">
                📄 Export PDF
            </button>

      </div>

  </div>
</div>

<!-- PDF Loading Modal -->
<div id="pdfLoading" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background-color:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:99999;
">
    <div class="loader-container">
        <div class="spinner"></div>
        <div style="color:white; font-family:'Poppins'; font-size:14px;">
            Generating PDF...
        </div>
    </div>
</div>





<!-- Include html2pdf.js (CDN) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
/* ============================
   Grab table body once
   ============================ */
const tableBody = document.querySelector("#masterTable tbody");

/* ============================
   Apply row stripes
   ============================ */
function applyStripes() {
    let visibleIndex = 0;
    [...tableBody.rows].forEach(tr => {
        if (tr.style.display === "none") return;
        tr.style.background = visibleIndex % 2 === 0 ? "#fff" : "#ffe4ee";
        visibleIndex++;
    });
}


/* ============================
   Autocomplete & search (unique suggestions, partial match)
   ============================ */
searchInput.addEventListener("input", e => {
    const val = e.target.value.toLowerCase();
    const filter = document.getElementById("filterSelect").value.toLowerCase();

    suggestionsList.innerHTML = "";

    if (val.length === 0) {
        suggestionsList.style.display = "none";
        applyStripes();
        return;
    }

    const seen = new Set(); // track unique names
    [...tableBody.rows].forEach(tr => {
        const type = tr.getAttribute("data-type").toLowerCase();
        const name = tr.cells[1].textContent;

        if ((filter === "all" || filter === type) && name.toLowerCase().includes(val)) {
            if (!seen.has(name)) {
                seen.add(name);

                const div = document.createElement("div");
                div.classList.add("suggestion-item");

                // highlight the matched part in suggestion
                const startIndex = name.toLowerCase().indexOf(val);
                if(startIndex !== -1){
                    const beforeMatch = name.substring(0, startIndex);
                    const matchText = name.substring(startIndex, startIndex + val.length);
                    const afterMatch = name.substring(startIndex + val.length);
                    div.innerHTML = `${beforeMatch}<strong>${matchText}</strong>${afterMatch}`;
                } else {
                    div.textContent = name;
                }

                div.onclick = () => searchCustomer(name); // show row(s) in table
                suggestionsList.appendChild(div);
            }
        }
    });

    suggestionsList.style.display = seen.size > 0 ? "block" : "none";
});


/* ============================
   Search & highlight row
   ============================ */
function searchCustomer(value){
    const searchVal = value ? value.toLowerCase() : searchInput.value.toLowerCase();
    const filter = document.getElementById("filterSelect").value.toLowerCase();


    // Show loading modal
    const loading = document.getElementById("loadingModal");
    loading.style.display = "flex";

    setTimeout(() => { // small delay to show spinner

    let found = false; // <-- ADD THIS

    [...tableBody.rows].forEach(tr => {
        const type = tr.getAttribute("data-type").toLowerCase();
        const name = tr.cells[1].textContent.toLowerCase();

        // Apply filter first
        if(filter !== "all" && type !== filter){
            tr.style.display = "none";
            return;
        }

        // Show all matching rows, hide non-matching
        if(name.includes(searchVal)){
            tr.style.display = "table-row";
            found = true; // <-- update found
        } else {
            tr.style.display = "none";
        }

        tr.classList.remove("highlight"); // remove highlight
    });

    // Hide autocomplete suggestions
    suggestionsList.style.display = "none";

    // Show mini modal if nothing found
    if(!found){
        const modal = document.getElementById("miniNoResultModal");
        modal.style.display = "block";

        document.getElementById("closeMiniModal").onclick = function() {
            modal.style.display = "none";

            // Restore all rows after closing modal
            [...tableBody.rows].forEach(tr => {
                const type = tr.getAttribute("data-type").toLowerCase();
                tr.style.display = (filter === "all" || type === filter) ? "table-row" : "none";
            });

            searchInput.value = "";
            applyStripes();
        };
    }

    applyStripes(); // <-- always call after updating row display

    // Hide loading modal
     loading.style.display = "none";
    }, 300); // adjust delay if needed
}


/* ============================
   Refresh table
   ============================ */
function refreshTable() {
    const filter = document.getElementById("filterSelect").value.toLowerCase();

     // Show loading modal
    const loading = document.getElementById("loadingModal");
    loading.style.display = "flex";

    setTimeout(() => { // small delay to show spinner

    [...tableBody.rows].forEach(tr => {
        tr.classList.remove("highlight");
        const type = tr.getAttribute("data-type").toLowerCase();
        tr.style.display = (filter === "all" || type === filter) ? "table-row" : "none";
    });

    searchInput.value = "";
    suggestionsList.style.display = "none";
    document.querySelector(".table-scroll").scrollTop = 0;
    applyStripes();

     // Hide loading modal
        loading.style.display = "none";
    }, 300); // adjust delay if needed
}

/* ============================
   Filter table by dropdown
   ============================ */
function filterTable() {
    const filter = document.getElementById("filterSelect").value.toLowerCase();

    [...tableBody.rows].forEach(tr => {
        const type = tr.getAttribute("data-type").toLowerCase();
        tr.style.display = (filter === "all" || type === filter) ? "table-row" : "none";
        tr.classList.remove("highlight");
    });

    searchInput.value = "";
    suggestionsList.style.display = "none";
    document.querySelector(".table-scroll").scrollTop = 0;
    applyStripes();
}


/* ============================
   Open Details Modal
============================ */
function openModal(purchase_id) {
    // 🔹 1. Show loading spinner
    document.getElementById("loadingModal").style.display = "flex";

    fetch("fetch_items.php?purchase_id=" + purchase_id)
    .then(res => res.json())
    .then(data => {
        // 🔹 2. Populate customer info
        const tr = [...tableBody.rows].find(tr => parseInt(tr.querySelector('.view-btn').dataset.purchaseId) == purchase_id);
        if(tr){
            document.getElementById("receiptCustomer").textContent = tr.cells[1].textContent;
            document.getElementById("receiptMethod").textContent = tr.getAttribute('data-type');
            document.getElementById("receiptDate").textContent = tr.cells[3].textContent;
        }

        // 🔹 3. Fill items
            const itemsContainer = document.getElementById("modalItems");
            itemsContainer.innerHTML = "";
            let total = 0;

            if(data.length === 0){
                itemsContainer.innerHTML = "<div style='text-align:center;'>No items</div>";
            } else {
                data.forEach(item => {
                    // amount = LINE TOTAL (price × qty) galing sa DB
                    const qty       = parseInt(item.quantity) || 0;
                    const lineTotal = parseFloat(item.amount) || 0;
                
                    // kunin unit price mula sa line total
                    const unitPrice = qty > 0 ? lineTotal / qty : lineTotal;
                
                    const div = document.createElement("div");
                    div.style.display = "flex";
                    div.style.justifyContent = "space-between";
                    div.style.marginBottom = "8px";
                    div.style.alignItems = "center";
                
                    div.innerHTML = `
                        <div style="display:flex; flex-direction:column;">
                            <span style="font-weight:600; color:#6D2E3A;">${item.product_name}</span>
                            <span style="font-size:12px; color:#A95469;">
                                Price: ₱${unitPrice.toFixed(2)} x ${qty}
                            </span>
                        </div>
                
                        <div style="font-weight:700; color:#6D2E3A;">
                            ₱${lineTotal.toFixed(2)}
                        </div>
                    `;
                
                    itemsContainer.appendChild(div);
                
                    // TOTAL = diretsong add ng bawat line total (walang extra × quantity)
                    total += lineTotal;
                });
            }

            document.getElementById("modalTotal").textContent = "₱" + total.toFixed(2);


        // 🔹 4. Hide loading spinner and show receipt
        setTimeout(() => {
            document.getElementById("loadingModal").style.display = "none";
            document.getElementById("detailsModal").style.display = "flex";
        }, 300); // small delay for smoother feel
    })
    .catch(err => {
        document.getElementById("loadingModal").style.display = "none";
        alert("Failed to load items.");
    });
}


/* Close Modal */
document.getElementById("closeDetailsModal").onclick = function() {
    document.getElementById("detailsModal").style.display = "none";
};

/* Print Function */
function printReceipt() {
    window.print();
}

/* Export as Real PDF (auto-download) */
/* Export as Real PDF with Loading */
function exportPDF() {
    const modalContent = document.getElementById("detailsModalContent");
    const closeBtn = document.getElementById("closeDetailsModal");
    const buttons = modalContent.querySelectorAll("button");

    // Show loading spinner
    document.getElementById("pdfLoading").style.display = "flex";

    // Temporarily hide buttons/close
    closeBtn.style.display = "none";
    buttons.forEach(btn => btn.style.display = "none");

    // Prepare file name
    const customer = document.getElementById("receiptCustomer").textContent.trim() || "Customer";
    const date = document.getElementById("receiptDate").textContent.trim().replace(/[^\w-]/g, "_") || "Date";
    const filename = `Receipt_${customer}_${date}.pdf`;

    const opt = {
        margin: 0.5,
        filename: filename,
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: "in", format: "letter", orientation: "portrait" },
    };

    // Timeout para mukhang may processing talaga
    setTimeout(() => {
        html2pdf().set(opt).from(modalContent).save().then(() => {

            // Restore buttons after exporting
            closeBtn.style.display = "";
            buttons.forEach(btn => btn.style.display = "");

            // Hide loading
            document.getElementById("pdfLoading").style.display = "none";
        });
    }, 800); // delay 0.8s for smooth effect
}


/* ============================
   Event delegation for view button
============================ */
document.querySelector("#masterTable tbody").addEventListener("click", function(e){
    if(e.target.classList.contains("view-btn")){
        const purchase_id = e.target.dataset.purchaseId;
        openModal(purchase_id);
    }
});

/* ============================
   Initial setup
============================ */
applyStripes();

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

