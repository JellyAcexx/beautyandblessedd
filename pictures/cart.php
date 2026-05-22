<?php
session_start();
include 'database.php';

if (!isset($_SESSION['login_id']) || !isset($_SESSION['register_id'])) {
    header("Location: homepage.php");
    exit();
}

$register_id = (int) $_SESSION['register_id'];

$query = "
    SELECT 
        ci.cart_items_id, 
        ci.cart_id, 
        ci.product_id, 
        ci.quantity, 
        ci.amount, 
        p.product_name, 
        p.price, 
        p.image_path
    FROM cart_items ci
    INNER JOIN cart c ON ci.cart_id = c.cart_id
    INNER JOIN products p ON ci.product_id = p.product_id
    INNER JOIN login_tb l ON c.login_id = l.login_id
    WHERE l.register_id = ?
    ORDER BY ci.cart_items_id DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $register_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            font-size: 17px;
            color:  #6D2E3A;
            font-family: "Poppins", sans-serif !important;
        }

        h3 {
            color: #6D2E3A;
            text-align: center;
            margin-bottom: 20px;
        }

        h3.text-danger {
            color: #6D2E3A !important;
            font-weight: 700;
        }

        .cart-header-card {
            background: #fff;
            padding: 15px 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            border-radius: 0;
            border: 0;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-icon {
            margin-right: 10px;
            font-size: 1.8em;
            color: #6d2e3a;
            display: flex;
            align-items: center;
        }
        .header-text {
            font-weight: 700;
            font-size: 1.7em;
            color: #6d2e3a;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .cart-header-card.sticky-top {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #fff;
        }
        .cart-list-scroll {
            max-height: 70vh; /* adjust depende sa gusto mong visible area */
            overflow-y: auto;
            padding-right: 5px; /* for scrollbar space */
        }

        /* 🌸 Table Styling - no outer border, only inner grid lines */
        #cartTable {
            width: 100%;
            border-collapse: collapse !important;
            border: none !important;
        }

        /* 🌸 Header (pink background + inner vertical lines only) */
        #cartTable thead tr th {
            background-color: #a95469 !important;
            color: #fff !important;
            text-align: center;
            font-size: 15px;
            text-transform: uppercase;
            padding: 12px;
            border-top: none !important;
            border-bottom: none !important;  /* <-- REMOVE THE SOLID BORDER! */
            border-left: 1.5px solid #6D2E3A !important;
            border-right: 1.5px solid #6D2E3A !important;
        }
        /* Remove outermost header borders */
        #cartTable thead tr th:first-child {
            border-left: none !important;
        }
        #cartTable thead tr th:last-child {
            border-right: none !important;
        }

        .cart-container {
            margin-top: 25px; /* dagdagan kung gusto mo mas malaking gap */
        }


        /* 🌸 Body rows - only inner vertical & horizontal lines */
        #cartTable tbody tr td {
            background-color: #fff !important;
            color: #6D2E3A !important;
            text-align: center;
            padding: 10px;
            border-top: 1px solid #E8A9B2 !important;
            border-bottom: 1px solid #E8A9B2 !important;
            border-left: 1px solid #E8A9B2 !important;
            border-right: 1px solid #E8A9B2 !important;
        }

        /* Remove left & right outermost borders */
        #cartTable tbody tr td:first-child {
            border-left: none !important;
            border-left: #6D2E3A !important;
        }
        #cartTable tbody tr td:last-child {
            border-right: none !important;
            border-right: #6D2E3A !important;
        }

        #cartTable tbody tr:hover td {
            background-color: #fff5f7 !important;
        }

        /* 🌸 Product Image */
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1.5px solid #E8A9B2;
        }

        /* 🌸 Quantity Buttons */
        .qty-btn {
            border: none;
            background: #ffe6ea;
            padding: 2px 7px;
            cursor: pointer;
            font-size: 13px;
            color: #6d2e3a;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .qty-btn:hover {
            background: #E8A9B2;
            color: white;
        }

        /* 🌸 Checkbox Customization */
        input[type="checkbox"] {
            accent-color: white;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* 🌸 Total Section */
        .total-box {
            text-align: right;
            font-weight: bold;
            color: #6d2e3a;
            margin-top: 15px;
            font-size: 18px;
            padding-right: 10px;
        }

        /* 🌸 Buttons */
        .btn-reserve {
            background-color: #E8A9B2;
            color: white;
            border: 2px solid transparent;
            padding: 4px 14px;
            font-size: 16px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .btn-reserve:hover:not(:disabled) {
            background-color: white;
            color: #6d2e3a;
            border: 2px solid #6d2e3a;
        }

        .btn-delete {
            background-color: red;
            color: white;
            border: 2px solid transparent;
            padding: 4px 14px;
            font-size: 16px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .btn-delete:hover:not(:disabled) {
            background-color: white;
            color: red;
            border: 2px solid red;
        }

        .btn-reserve:disabled,
        .btn-delete:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        
        /* 🌸 SWEETALERT STYLING */
        .small-swal {
            width: 320px !important;          /* smaller width */
            padding: 1.2rem !important;
            font-size: 0.9rem !important;
            color: #6d2e3a !important;        /* pink text */
        }

        /* 💖 Confirm Button (Dark Pink) */
        .btn-confirm {
            background-color: #6d2e3a !important;
            color: #fff !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 8px 18px !important;
            font-weight: 600 !important;
            margin: 0 6px !important;
            transition: all 0.2s ease;
        }

        .btn-confirm:hover {
            background-color:  #6d2e3a  !important;
        }

        /* 💗 Cancel Button (Soft Red/Pink) */
        .btn-cancel {
            background-color: #E8A9B2 !important;
            color: #fff !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 8px 18px !important;
            font-weight: 600 !important;
            margin: 0 6px !important;
            transition: all 0.2s ease;
        }

        .btn-cancel:hover {
            background-color: #6d2e3a !important;
        }

        /* 💕 Space between buttons */
        .swal2-actions {
            gap: 10px !important;
        }

        .swal2-title,
        .swal2-html-container {
            color: #6d2e3a !important; /* pink text */
            font-family: 'Poppins', sans-serif; /* optional: cute clean font */
        }

        .swal2-icon.swal2-question {
            border-color: #6d2e3a !important; /* pink border for ? */
            color: #6d2e3a !important;        /* pink question mark */
        }

        /* 🩷 Container holds table + summary */
        .cart-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* 🧾 Table wrapper: only this scrolls on mobile */
        .cart-table-wrapper {
            max-height: 550px;
            overflow-y: auto;
            border: 1px solid #E8A9B2;
            border-radius: 10px;
        }

        /* 🌸 Summary card always below (not scrolling with table) */
        .cart-summary-card {
            width: 100%;
            background: #fff;
            border: 2px solid #E8A9B2;
            border-radius: 15px;
            padding: 15px 20px;
            box-shadow: 0 4px 10px rgba(236, 118, 153, 0.15);
        }

        /* Right-aligned content */
        .summary-inner {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .summary-inner span {
            font-size: 16px;
            color: #6d2e3a;
        }

        .btn-close-receipt,
        .receipt-header, .items {
            color: #6d2e3a !important;
        }

        #reservationSummaryModal .table {
            border: 1px solid #6d2e3a;
            border-radius: 10px;
            overflow: hidden;
        }

        #reservationSummaryModal .table thead th {
            background-color: #6d2e3a !important;
            color: white !important;
            text-align: center;
            font-weight: 600;
            border: none;
        }

        #reservationSummaryModal .table tbody td {
            color: #6d2e3a;
            vertical-align: middle;
            text-align: center;
            border: 1px solid #E8A9B2;
        }

        /* striped effect for summary table */
        #reservationSummaryModal .table-striped tbody tr:nth-of-type(odd) {
            background-color: #fffafc; /* very light pink */
        }

        #reservationSummaryModal .table-striped tbody tr:hover {
            background-color: #ffe4ec; /* hover effect */
        }

        #loadingModal {
            display: none;
            position: fixed;
            top:0; left:0;
            width:100%; height:100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader-container {
            display:flex;
            flex-direction: column;
            align-items:center;
            gap:10px;
        }

        .spinner {
            border: 6px solid #ffe4ee; /* background ring */
            border-top: 6px solid #6d2e3a; /* pink spin */
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
            color:#ec7699;
            font-weight:bold;
            font-size:16px;
        }

        .container-fluid {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        /* 🩷 Responsive adjustments for smaller screens */
        @media (max-width: 767px) {
            .container-fluid {
                padding-left: 18px !important;
                padding-right: 18px !important;
            }
            .header-icon,
            .header-text {
                font-size: 25px;
            }
            .cart-table-wrapper {
                max-height: 60vh; /* make table scrollable on mobile */
            }
            .summary-inner {
                flex-direction: column;
                align-items: flex-end;
                gap: 8px;
            }
            .btn-reserve,
            .btn-delete {
                width: 100%;
            }
        }
    </style>


</head>
<body>
    <div class="container-fluid px-3 px-md-5 py-3">
        <div class="cart-header-card sticky-top d-flex align-items-center mb-4">
            <span class="header-icon">
                <i class="bi bi-cart-fill"></i>
            </span>
            <span class="header-text"> My Cart</span>
        </div>

        <div class="cart-container">
            <div class="cart-table-wrapper">
                <table class="table table-hover table-striped shadow-sm align-middle" id="cartTable">
                    <thead>
                        <tr>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Select</th>
                        </tr>
                    </thead>
                    <tbody class="mb-0">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr data-id="<?= $row['cart_items_id']; ?>" data-price="<?= $row['price']; ?>">
                                    <td><?php 
                                        $imagePath = !empty($row['image_path']) ? $row['image_path'] : 'images/no-image.png'; // fallback
                                        ?>
                                        <img src="<?= htmlspecialchars($imagePath); ?>" alt="Product Image" style="width: 80px; height: 80px; object-fit: cover;">
                                    </td>
                                    <td><?= htmlspecialchars($row['product_name']); ?></td>
                                    <td>₱<?= number_format($row['price'], 2); ?></td>
                                    <td>
                                        <button class="qty-btn minus">−</button>
                                        <span class="mx-2 qty-val"><?= $row['quantity']; ?></span>
                                        <button class="qty-btn plus">+</button>
                                    </td>
                                    <td class="amount">₱<?= number_format($row['amount'], 2); ?></td>
                                    <td><input type="checkbox" class="select-item"></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="empty-row">No items in your cart yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-summary-card mt-0">
                <div class="summary-inner">
                    <span class="fw-bold me-2">Total: ₱<span id="totalValue">0.00</span></span>
                    <button class="btn-reserve" id="reserveBtn" disabled>Reserve</button>
                    <button class="btn-delete" id="deleteBtn" disabled>Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingModal">
        <div class="loader-container">
            <div class="spinner"></div>
            <div class="loader-text">Please wait...</div>
        </div>
    </div>

    <div class="modal fade" id="deleteSuccessModal" tabindex="-1" aria-labelledby="deleteSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="background-color: #fffafc; border-radius: 10px; border: none; padding: 10px;">
                <div class="modal-body text-center py-3" style="color: #d6336c;">
                    <h5>Cart item/s deleted successfully.</h5>
                </div>
                <div class="modal-footer py-2" style="border: none; justify-content: center;">
                    <button type="button" class="btn btn-sm" data-bs-dismiss="modal" 
                            style="background-color: #d6336c; color: white; border-radius: 6px; font-size: 13px; padding: 3px 10px;">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reserveSuccessModal" tabindex="-1" aria-labelledby="reserveSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content"  style="background-color: #fffafc; border-radius: 10px; border: none; padding: 10px;">
                <div class="modal-body text-center py-3" style="color: #d6336c;">
                    <h5>Your reservation has been placed successfully.</h5>
                </div>
                <div class="modal-footer py-2" style="border: none; justify-content: center;">
                    <button type="button" class="btn btn-sm" data-bs-dismiss="modal"
                            style="background-color: #d6336c; color: white; height: 40px; width: 80px; border-radius: 6px; font-size: 13px;">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reservationSummaryModal" tabindex="-1" aria-labelledby="reservationSummaryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md"> <!-- ✅ smaller modal -->
            <div class="modal-content" style="border-radius: 10px; overflow: hidden; font-size: 14px;">
            
                <div class="modal-header py-3" style="background-color: #fae6e7; color: #d6336c;">
                    <h6 class="modal-title" id="reservationSummaryLabel"><strong>Reservation Summary</strong></h6>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        style="filter: invert(36%) sepia(58%) saturate(3342%) hue-rotate(315deg) brightness(90%) contrast(90%);">
                    </button>
                </div>

                <div class="modal-body" id="reservationSummaryBody" style="color: #ec7699; padding: 15px 20px;">
                    <div class="text-center">
                        <p id="reservationDate" class="mb-1" style="font-size:14px;"></p>
                        <p id="pickupDate" class="mb-3" style="font-size:14px;"></p>
                    </div>
                    <div class="mb-3">
                        <strong id="customerName"></strong>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center align-middle mb-2" style="font-size: 13px;">
                            <thead>
                                <tr class="receipt-header">
                                    <strong>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Amount</th>
                                    </strong>
                                </tr>
                            </thead>
                            <tbody class="items" id="reservationItems"></tbody>
                        </table>
                    </div>

                    <!-- ✅ Total -->
                    <div class="text-end mt-2">
                        <strong>Total: ₱<span id="reservationTotal">0.00</span></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap5/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('cartTable');
            const totalDisplay = document.getElementById('totalValue');
            const btnReserve = document.getElementById('reserveBtn');
            const btnDelete = document.getElementById('deleteBtn');

            // ✅ Compute running total of checked items
            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.select-item:checked').forEach(cb => {
                    const row = cb.closest('tr');
                    const amount = parseFloat(row.querySelector('.amount').textContent.replace('₱', '').replace(',', ''));
                    total += amount;
                });
                totalDisplay.textContent = total.toFixed(2);
            }

            // ✅ Enable/Disable buttons based on selection
            function updateButtonState() {
                const anyChecked = document.querySelectorAll('.select-item:checked').length > 0;
                btnReserve.disabled = !anyChecked;
                btnDelete.disabled = !anyChecked;
            }

            // ✅ Quantity buttons (+ / -)
            table.addEventListener('click', function(e) {
                const btn = e.target;
                if (btn.classList.contains('plus') || btn.classList.contains('minus')) {
                    const row = btn.closest('tr');
                    const qtySpan = row.querySelector('.qty-val');
                    const price = parseFloat(row.dataset.price);
                    let qty = parseInt(qtySpan.textContent);

                    if (btn.classList.contains('plus')) qty++;
                    if (btn.classList.contains('minus') && qty > 1) qty--;

                    qtySpan.textContent = qty;
                    const newAmount = qty * price;
                    row.querySelector('.amount').textContent = '₱' + newAmount.toFixed(2);

                    updateTotal();

                    // Update in database
                    fetch('update_cart_items.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `cart_items_id=${row.dataset.id}&quantity=${qty}`
                    })
                    .then(res => res.text())
                    .then(data => console.log(data))
                    .catch(err => console.error(err));
                }
            });

            // ✅ Checkbox change
            table.addEventListener('change', function(e) {
                if (e.target.classList.contains('select-item')) {
                    updateTotal();
                    updateButtonState();
                }
            });

            function showLoading() {
                const modal = document.getElementById("loadingModal");
                modal.style.display = "flex";
            }

            function hideLoading() {
                const modal = document.getElementById("loadingModal");
                modal.style.display = "none";
            }

            // ✅ Delete selected cart items
            btnDelete.addEventListener('click', function() {
                const checkedItems = Array.from(document.querySelectorAll('.select-item:checked'))
                    .map(cb => cb.closest('tr').dataset.id);

                if (checkedItems.length === 0) return;

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to delete the selected item(s)?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ec407a',
                    cancelButtonColor: '#f8a9bb',
                    confirmButtonText: 'Yes, delete!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'small-swal'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('delete_cart_items.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: `ids=${JSON.stringify(checkedItems)}`
                        })
                        .then(res => res.text())
                        .then(data => {
                            // Remove deleted rows from DOM
                            checkedItems.forEach(id => {
                                const row = document.querySelector(`tr[data-id="${id}"]`);
                                if (row) row.remove();
                            });

                            updateTotal();
                            updateButtonState();

                            // If no rows left
                            if (document.querySelectorAll('#cartTable tbody tr').length === 0) {
                                const tbody = document.querySelector('#cartTable tbody');
                                tbody.innerHTML = `<tr><td colspan="6" class="empty-row">No items in your cart yet.</td></tr>`;
                            }

                            // Success Alert
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Selected item(s) have been removed from your cart.',
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'small-swal'
                                }
                            });
                        })
                        .catch(err => {
                            Swal.fire('Error', 'Something went wrong while deleting items.', 'error');
                        });
                    }
                });
            });

            // ✅ Reserve selected items (SweetAlert customized)
            btnReserve.addEventListener('click', function() {
                const checkedItems = Array.from(document.querySelectorAll('.select-item:checked'))
                    .map(cb => cb.closest('tr').dataset.id);

                const totalValue = parseFloat(document.getElementById('totalValue').textContent);

                if (checkedItems.length === 0) return;

                // ⬇️ SWEETALERT CONFIRMATION PROMPT (Styled)
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to reserve the selected item(s)?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ec407a',  // 💖 Dark pink
                    cancelButtonColor: '#5f5b5bff',  
                    confirmButtonText: 'Yes, reserve it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'small-swal' // 🔽 Apply smaller modal
                    }
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        showLoading();

                        // ⏳ Delay fetch by 1 second
                        setTimeout(() => {

                            fetch('reserve.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ cart_items: checkedItems, total: totalValue })
                            })
                            .then(res => res.json())
                            .then(data => {
                                hideLoading();

                                if (data.success) {
                                    const successModal = new bootstrap.Modal(document.getElementById('reserveSuccessModal'));
                                    successModal.show();

                                    document.getElementById('reserveSuccessModal').addEventListener('hidden.bs.modal', function () {
                                        if (data.reservation) {
                                            document.getElementById('customerName').textContent = data.reservation.customer_name;
                                            document.getElementById('reservationDate').textContent = "Reservation Date: " + data.reservation.reservation_date;
                                            document.getElementById('pickupDate').textContent = "Pick-up Date: " + data.reservation.pickup_date;

                                            const tbody = document.getElementById('reservationItems');
                                            tbody.innerHTML = "";

                                            data.reservation.items.forEach(item => {
                                                tbody.innerHTML += `
                                                    <tr>
                                                        <td>${item.product_name}</td>
                                                        <td>${item.quantity}</td>
                                                        <td>₱${parseFloat(item.amount).toFixed(2)}</td>
                                                    </tr>`;
                                            });

                                            document.getElementById('reservationTotal').textContent = parseFloat(data.reservation.total).toFixed(2);

                                            const summaryModal = new bootstrap.Modal(document.getElementById('reservationSummaryModal'));
                                            summaryModal.show();

                                            document.getElementById('reservationSummaryModal').addEventListener('hidden.bs.modal', function () {
                                                location.reload();
                                            });
                                        } else {
                                            location.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            })
                            .catch(err => {
                                hideLoading();
                                console.error(err);
                            });

                        }, 1000); // ⏳ delay of 1 second before showing success modal
                    }
                });
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

<?php
$stmt->close();
$conn->close();
?>