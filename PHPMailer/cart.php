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
<link href="https://fonts.googleapis.com/css2?family=Meie+Script&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html, body {
            overflow: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            overscroll-behavior: none;
        }
        body {
            font-size: 17px;
            color: #6D2E3A;
            font-family: "Poppins", sans-serif !important;
        }
        .container-fluid {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }
        .cart-header-card {
            background: #fff;
            padding: 15px 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            border-radius: 0;
            margin-bottom: 24px;
            display: flex;
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
        .cart-main-row { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 24px; 
        }
        .cart-items-col { 
            flex: 2 1 340px; 
            min-width: 0; 
        }
        .cart-summary-col { 
            flex: 1 1 270px; 
            min-width: 0; 
        }
        @media (max-width: 991.98px) {
            .cart-main-row { 
                flex-direction: column; 
            }
            .cart-items-col,
            .cart-summary-col { 
                width: 100%; 
                flex: 1 1 100%; 
            }
        }
        .cart-items-card {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 12px rgba(236,118,153,0.08);
            padding: 12px 0px 0 0px;
            border: 3px solid #E8A9B2;
            max-height: 630px;
            overflow-y: auto;     /* Scroll bar will be on the outside/right edge of the card */
            overflow-x: hidden;
        }
        .item-actions { 
            margin-top: 5px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-end;
            gap: 14px; /* adjust gap as needed, same as gap-3 */
        }
        .btn-item-delete {
            margin-top: 0;
            min-width: 100px;
            background: #dd052cff;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 7px;
            font-size: 15px;
            padding: 3px 17px;
            margin-top: 4px;
        }
        .amount-label {
            margin-right: 10px;
        }
        .cart-items-card .single-cart-item:first-child {
            padding-top: 10px;
        }
        .single-cart-item {
            display: flex;
            align-items: center;
            padding: 22px 28px 18px 17px;
        }
        .single-cart-item + .single-cart-item {
            border-top: 1.5px solid #E8A9B2;
        }
        .cart-img-col {
            width: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .img-square {
            width: 80px;
            height: 80px;
            background: #fff7f8;
            border-radius: 10px;
            overflow: hidden;
            border: 1.8px solid #E8A9B2;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
        }
        .include-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 5px;
        }
        .include-label input[type="checkbox"] {
            accent-color: #6d2e3a;
            width: 18px;
            height: 18px;
            margin-bottom: 2px;
        }
        .include-label span {
            font-size: 14px;
            color: #6d2e3a;
        }
        .cart-item-details {
            flex: 1 1 auto;
            padding-left: 23px;
            display: flex;
            flex-direction: column;
            gap: 7px;
        }
        .product-title {
            font-weight: 600;
            color: #6d2e3a;
            font-size: 16px;
        }
        .price {
            color: #a95469;
            font-weight: 500;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .qty-btn {
            border: none;
            background: #ffe6ea;
            padding: 2px 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500 !important;
            color: #6d2e3a;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .qty-val {
            font-weight: 600;
            min-width: 22px;
            text-align: center;
        }
        .amount {
            color: #6d2e3a;
            font-weight: 700;
            margin-top: 3px;
        }
        .bi-trash-fill {
            margin-right: 5px;
        }
        /* SUMMARY CARD */
        .cart-summary-card {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 12px rgba(236,118,153,0.10);
            padding: 24px 23px;
            border: 3px solid #E8A9B2;
            display: flex;
            flex-direction: column;
            gap: 7px;
            min-height:240px;
        }
        .summary-title {
            font-weight: 700;
            font-size: 17px;
            text-align: center;
            color: #6d2e3a;
            margin-bottom: 10px;
        }
        .order-summary-list {
            border-radius: 8px;
            background: #fff8fa;
            padding: 12px 10px;
            margin-bottom: 15px;
        }
        .order-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
            font-weight: 500;
            color: #6d2e3a;
            border-bottom: 1px dashed #e6b8c2;
            padding: 3px 0 3px 0;
        }
        .order-summary-row:last-child {
            border-bottom: none;
        }
        .summary-total-row {
            font-weight: 700;
            font-size: 1.1em;
            color: #a95469;
            padding-top: 5px;
            display: flex;
            justify-content: space-between;
        }
        .summary-actions {
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }
        #reserveBtn,
        #deleteBtn {
            flex: 1;
            font-size: 17px;
            border-radius: 8px;
        }
        #reserveBtn {
            color: white;
            background-color: #6d2e3a;
        }
        #deleteBtn {
            color: white;
            background-color: #dd052cff;
        }
        #loadingModal {
            display: none;
            position: fixed;
            top:0; left:0;
            width: 100%; 
            height:100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .loader-container {
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            gap: 10px;
        }
        .spinner {
        border: 6px solid #ffe4ee;
        border-top: 6px solid #6d2e3a;
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
            color: #ec7699; 
            font-weight: bold; 
            font-size:16px; 
        }
        .swal2-actions {
            gap: 10px !important;
        }
        .swal2-title,
        .swal2-html-container {
            color: #6d2e3a !important;
            font-family: 'Poppins', sans-serif;
        }
        .swal2-icon.swal2-question, 
        .swal2-icon.swal2-warning {
            border-color: #6d2e3a !important;
            color: #6d2e3a !important;
        }
        .sweet-cart-modal {
            width: 320px !important;
        }

        @media (max-width: 767px) {
            .header-icon,
            .header-text {
                font-size: 25px;
            }
            .container-fluid {
                padding-left: 18px !important;
                padding-right: 18px !important;
            }
            .single-cart-item {
                flex-direction: row !important;
                align-items: flex-start;
                padding: 11px 4px !important;
                font-size: 13.5px;
                gap: 0;
            }
            .cart-img-col {
                min-width: 50px;
            }
            .img-square {
                width: 60px;
                height: 60px;
                margin-bottom: 2px !important;
            }
            .product-img {
                width: 44px;
                height: 44px;
            }
            .include-label span,
            .product-title, .price {
                font-size: 14px !important;
            }
            .cart-item-details {
                padding-left: 10px !important;
                min-width: 0;
            }
            .product-title {
                font-size: 15px !important;
                font-weight: 600;
                margin-bottom: 0 !important;
            }
            .qty-btn, .qty-val {
                font-size: 13px !important;
                min-width: 27px;
            }
            .item-actions {
                display: flex !important;
                flex-direction: row !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 10px !important;
                margin-right: 25px !important;
                width: 100%;
                text-align: center;
            }
            .amount-row {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 2px !important;
                font-size: 14px !important;
                font-weight: 600 !important;
                color: #a95469 !important;
            }
            .amount-label {
                font-size: 13px !important;
                color: #a95469 !important;
                font-weight: 600 !important;
                margin: 0 3px 0 0 !important;
                white-space: nowrap !important;
            }
            .amount {
                font-size: 15px !important;
                color: #6d2e3a !important;
                font-weight: bold !important;
                display: inline !important;
                margin: 0 !important;
            }
            .btn-item-delete {
                display: inline-block !important;
                font-size: 15px !important;
                min-width: 40px !important;
                max-width: 70px !important;
                height: 30px !important;
                border-radius: 5px !important;
                text-align: center;
                align-items: center !important;
                justify-content: center;
                line-height: 1; 
                padding: 0;
                cursor: pointer;
            }
            .bi-trash-fill {
                margin-right: 0 !important;
            }
            .delete-name {
                display: none !important;
            }
            .summary-title {
                font-size: 18px !important;
                margin-bottom: 7px !important;
            }
            .order-summary-row {
                font-size: 12px !important; 
                font-weight: 400 !important;
            }
            .summary-total-row {
                font-size: 15px !important; 
                font-weight: 400 !important;
            }
            .summary-actions {
                display: flex !important;
                flex-direction: row !important;
                gap: 10px !important;
                width: 100%;
                margin-bottom: 3px !important;
                margin-top: 10px !important;
            }
            #reserveBtn, #deleteBtn {
                width: 50% !important;
                font-size: 12px !important;
                min-width: 0 !important;
                height: 32px !important;
                border-radius: 5px !important;
                white-space: nowrap !important;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid px-3 px-md-5 py-3">
        <div class="cart-header-card sticky-top d-flex align-items-center mb-4">
            <span class="header-icon"><i class="bi bi-cart-fill"></i></span>
            <span class="header-text"> My Cart</span>
        </div>

        <div class="cart-main-row">
            <!-- LEFT: CART ITEMS -->
            <div class="cart-items-col mb-1">
                <div class="cart-items-card">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="single-cart-item" data-id="<?= $row['cart_items_id']; ?>" data-price="<?= $row['price']; ?>">
                            <!-- IMAGE W/ INCLUDE -->
                            <div class="cart-img-col d-flex flex-column align-items-center">
                                <div class="img-square mb-2">
                                    <img src="<?= htmlspecialchars($row['image_path'] ?: 'images/no-image.png'); ?>" alt="Product Image" class="product-img">
                                </div>
                                <label class="include-label">
                                    <input type="checkbox" class="select-item">
                                    <span class="text-muted">Add</span>
                                </label>
                            </div>
                            <!-- DETAILS -->
                            <div class="cart-item-details flex-grow-1 ps-3 ps-md-4">
                                <div class="product-title"><?= htmlspecialchars($row['product_name']); ?></div>
                                <div class="price">₱<?= number_format($row['price'], 2); ?></div>
                                <div class="quantity-controls mb-2">
                                    <button class="qty-btn minus">−</button>
                                    <span class="qty-val"><?= $row['quantity']; ?></span>
                                    <button class="qty-btn plus">+</button>
                                </div>
                                <div class="item-actions">
                                    <span class="amount-row">
                                        <span class="amount-label fw-semibold" style="font-size: 15px; color: #a95469;">Amount:</span>
                                        <span class="amount" style="font-weight: 700; color: #6d2e3a;">₱<?= number_format($row['amount'], 2); ?></span>
                                    </span>
                                    <button class="btn-item-delete"><i class="bi bi-trash-fill"></i><span class="delete-name">Delete</span></button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4"><i>No items in your cart yet.</i></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT: ORDER SUMMARY -->
            <div class="cart-summary-col">
                <div class="cart-summary-card">
                    <div class="summary-title mb-2">Order Summary</div>
                    <div class="order-summary-list" id="orderSummaryList"></div>
                    <div class="summary-total-row">
                        <span>Total:</span>
                        <span id="totalValue">₱0.00</span>
                    </div>
                    <div class="summary-actions">
                        <button class="btn" id="reserveBtn" disabled>RESERVE</button>
                        <button class="btn" id="deleteBtn" disabled>DELETE ALL</button>
                    </div>
                </div>
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

    <!-- Delete Success Modal -->
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

    <!-- Reserve Success Modal -->
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

    <!-- Reservation Summary Modal -->
    <div class="modal fade" id="reservationSummaryModal" tabindex="-1" aria-labelledby="reservationSummaryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
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
                    <div class="mb-3"><strong id="customerName"></strong></div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center align-middle mb-2" style="font-size: 13px;">
                            <thead>
                                <tr class="receipt-header">
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody class="items" id="reservationItems"></tbody>
                        </table>
                    </div>
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
            const cartItems = document.querySelectorAll('.single-cart-item');
            const totalDisplay = document.getElementById('totalValue');
            const btnReserve = document.getElementById('reserveBtn');
            const btnDelete = document.getElementById('deleteBtn');
            const summaryList = document.getElementById('orderSummaryList');

            function updateSummary() {
                let total = 0;
                let summaryHtml = '';
                document.querySelectorAll('.single-cart-item').forEach(function(item) {
                    const checkbox = item.querySelector('.select-item');
                    if(checkbox.checked) {
                        const name = item.querySelector('.product-title').textContent.trim();
                        const qty = item.querySelector('.qty-val').textContent.trim();
                        const amount = item.querySelector('.amount').textContent.replace('₱','').replace(/,/g,'').trim();
                        total += parseFloat(amount);
                        summaryHtml += `
                            <div class="order-summary-row">
                                <span>${name} × ${qty}</span>
                                <span>₱${parseFloat(amount).toFixed(2)}</span>
                            </div>
                        `;
                    }
                });
                summaryList.innerHTML = summaryHtml || '<div class="text-muted" style="font-size:14px;"><i>No selected item(s).</i></div>';
                totalDisplay.textContent = '₱' + total.toFixed(2);

                const anyChecked = document.querySelectorAll('.single-cart-item .select-item:checked').length > 0;
                btnReserve.disabled = !anyChecked;
                btnDelete.disabled = !anyChecked;
            }

            document.querySelectorAll('.single-cart-item .select-item').forEach(cb => {
                cb.addEventListener('change', updateSummary);
            });

            document.querySelectorAll('.single-cart-item .qty-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const item = btn.closest('.single-cart-item');
                    const qtySpan = item.querySelector('.qty-val');
                    let qty = parseInt(qtySpan.textContent);
                    const price = parseFloat(item.dataset.price);
                    if(btn.classList.contains('plus')) qty++;
                    if(btn.classList.contains('minus') && qty > 1) qty--;

                    qtySpan.textContent = qty;
                    const newAmount = qty * price;
                    item.querySelector('.amount').textContent = '₱' + newAmount.toFixed(2);

                    updateSummary();
                    fetch('update_cart_items.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `cart_items_id=${item.dataset.id}&quantity=${qty}`
                    });
                });
            });

            // Item Delete (row level)
            document.querySelectorAll('.btn-item-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const item = btn.closest('.single-cart-item');
                    const id = item.dataset.id;
                    Swal.fire({
                        icon:'warning', 
                        title:'Confirm',
                        text:'Do you want to remove this item?',
                        width: '320px',
                        showCancelButton:true, 
                        confirmButtonText:'Yes, delete it!', 
                        confirmButtonColor: '#6d2e3a',
                        cancelButtonText: 'Cancel'
                    }).then((result)=>{
                        if(result.isConfirmed){
                            fetch('delete_cart_items.php',{
                                method:'POST',
                                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                                body:`ids=${JSON.stringify([id])}`
                            })
                            .then(res=>res.text())
                            .then(() => {
                                item.remove();
                                updateSummary();
                                if(document.querySelectorAll('.single-cart-item').length===0){
                                    document.querySelector('.cart-items-card').innerHTML='<div class="text-center text-muted py-4"><i>No items in your cart yet.</i></div>';
                                }
                                Swal.fire('Deleted!','The item was removed.','success');
                            });
                        }
                    });
                });
            });

            // DELETE ALL (selected)
            btnDelete.addEventListener('click',function(){
                const checkedItems = Array.from(document.querySelectorAll('.single-cart-item .select-item:checked')).map(cb => cb.closest('.single-cart-item').dataset.id);
                if(checkedItems.length === 0) return;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to delete the selected item(s)?",
                    width: '320px',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete all!',
                    confirmButtonColor: '#6d2e3a',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if(result.isConfirmed){
                        fetch('delete_cart_items.php',{
                            method:'POST',
                            headers:{'Content-Type':'application/x-www-form-urlencoded'},
                            body:`ids=${JSON.stringify(checkedItems)}`
                        })
                        .then(res=>res.text())
                        .then(() => {
                            checkedItems.forEach(id=>{
                                const row = document.querySelector('.single-cart-item[data-id="'+id+'"]');
                                if(row) row.remove();
                            });
                            updateSummary();
                            if(document.querySelectorAll('.single-cart-item').length===0){
                                document.querySelector('.cart-items-card').innerHTML='<div class="text-center text-muted py-4"><i>No items in your cart yet.</i></div>';
                            }
                            Swal.fire('Deleted!','Selected items were removed.','success');
                        });
                    }
                });
            });

            // RESERVE
            btnReserve.addEventListener('click',function(){
                const checkedItems = Array.from(document.querySelectorAll('.single-cart-item .select-item:checked')).map(cb => cb.closest('.single-cart-item').dataset.id);
                const totalValue = parseFloat(totalDisplay.textContent.replace('₱','').replace(/,/g,''));

                if(checkedItems.length === 0) return;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to reserve the selected item(s)?",
                    width: '320px',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, reserve it!',
                    confirmButtonColor: '#6d2e3a',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if(result.isConfirmed){
                        // showLoading(); // optionally, use your loader/modal here
                        fetch('reserve.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ cart_items: checkedItems, total: totalValue })
                        })
                        .then(res => res.json())
                        .then(data => {
                            // hideLoading();
                            if(!data.success) {
                                Swal.fire({icon:'error',title:'Cannot proceed!',text:data.message});
                                return;
                            }
                            if(data.success) {
                                Swal.fire('Reserved!','Item(s) reserved successfully.','success').then(() => location.reload());
                            }
                        });
                    }
                });
            });

            updateSummary();
        });

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