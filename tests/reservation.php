<?php
session_start();
include 'database.php';

// Check login
if (!isset($_SESSION['register_id'])) {
    header("Location: homepage.php");
    exit();
}

$register_id = (int) $_SESSION['register_id'];

// GET filters
$status = $_GET['status'] ?? 'all';
$reservation_date = $_GET['reservation_date'] ?? '';

// Reservation_date options depend on status
$res_dates = [];
if ($status !== 'all') {
    $dates_query = "
        SELECT DISTINCT DATE(reservation_date) AS res_date
        FROM reservations
        WHERE register_id = ? AND status = ?
        ORDER BY res_date DESC, reservation_id DESC
    ";
    $res_date_stmt = $conn->prepare($dates_query);
    $res_date_stmt->bind_param('is', $register_id, $status);
    $res_date_stmt->execute();
    $res_date_result = $res_date_stmt->get_result();
    $res_dates = [];
    while ($row = $res_date_result->fetch_assoc()) {
        $res_dates[] = $row['res_date']; // 'YYYY-MM-DD'
    }
} else {
    $dates_query = "
        SELECT DISTINCT DATE(reservation_date) AS res_date
        FROM reservations
        WHERE register_id = ?
        ORDER BY res_date DESC, reservation_id DESC
    ";
    $res_date_stmt = $conn->prepare($dates_query);
    $res_date_stmt->bind_param('i', $register_id);
    $res_date_stmt->execute();
    $res_date_result = $res_date_stmt->get_result();
    $res_dates = [];
    while ($row = $res_date_result->fetch_assoc()) {
        $res_dates[] = $row['res_date'];
    }
}

// Reservations query
$query = "SELECT reservation_id, total, status, reservation_date, pickup_date, date_picked_up, cancel_date 
          FROM reservations WHERE register_id = ?";
$types = "i";
$values = [$register_id];
if ($status !== 'all') {
    $query .= " AND status = ?";
    $types .= "s";
    $values[] = $status;
}
if (!empty($reservation_date)) {
    $query .= " AND reservation_date = ?";
    $types .= "s";
    $values[] = $reservation_date;
}
$query .= " ORDER BY reservation_date DESC, reservation_id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$values);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[$row['status']][] = $row; // Group by status
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        }

        .reservations-header {
            background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%) !important;
            box-shadow: 0 10px 28px rgba(0,0,0,0.12) !important;
            padding: 15px 20px;
            border-radius: 0;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .reservations-header.sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .header-icon {
            margin-right: 24px !important;
            font-size: 1.8em;
            color: #6D2E3A;
            display: flex;
            align-items: center;
        }

        .header-text {
            font-weight: 700;
            font-size: 1.8em;
            color: #6D2E3A;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .card,
        .reservation-table-card,
        .reservation-status-card {
            padding: 0;
            border-radius: 10px;
            border: 1.2px solid #6d2e3a;
            box-shadow: 0 2px 8px rgba(237,82,130,0.07);
            margin-bottom: 28px;
            width: 100%;
        }

        .card-body {
            padding: 18px 24px !important;
            width: 100%;
            box-sizing: border-box;
        }

        .table {
            width: 100%;
            margin: 0;
            border-radius: 0;
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
        }

        .table th,
        .table td {
            vertical-align: middle;
            padding: 12px 10px;
            border: 1px solid #E8A9B2;
            box-sizing: border-box;
            text-align: center;
        }

        .table th {
            background-color: #6d2e3a !important;
            color: #fff !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 1em;
            letter-spacing: 0.03em;
            border-right: 1px solid #6d2e3a;
        }

        .table td {
            color: #6D2E3A;
            font-weight: 500;
            background: #fff;
            border-right: 1px solid #E8A9B2;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #F8D7DC !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #fffafc !important;
        }

        .status-card-header {
            background: none;
            color: #6D2E3A;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0 0 10px 0;
            border-radius: 0;
            margin-bottom: 14px;
            font-size: 1.13em;
            border: none;
            letter-spacing: 0.02em;
        }

        .img-col img {
            max-width: 62px;
            max-height: 62px;
            border-radius: 8px;
            object-fit: cover;
            display: block;
            margin: auto;
            border: 1.5px solid #f8bbd0;
        }

        .btn-pink,
        .btn-reserve,
        .btn-delete,
        .viewSummaryBtn {
            background: #6d2e3a;
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 15px;
            font-weight: 600;
            padding: 6px 15px;
            transition: 0.2s;
        }

        .btn-pink:hover,
        .btn-reserve:hover:not(:disabled),
        .btn-delete:hover:not(:disabled),
        .viewSummaryBtn:hover {
            background: #6d2e3a;
            color: #fff;
        }

        .viewSummaryBtn {
            margin-left: 4px;
        }

        .d-flex.justify-content-between.align-items-center.mt-3 {
            margin-left: auto;
            margin-right: auto;
            padding: 0 18px 14px 0;
            width: 100%;
            box-sizing: border-box;
        }

        .modal-content {
            border-radius: 14px;
            border: 1.5px solid #F8D7DC;
        }

        .modal-header {
            background-color: #fff;
            color: #6d2e3a;
        }

        .btn-close {
            filter: invert(36%) sepia(58%) saturate(3342%) hue-rotate(315deg) brightness(90%) contrast(90%);
        }

        .resCalendar .form-control {
            width: 130px !important;
            max-width: 130px !important;
            font-size: 0.9rem;
            padding: 4px 8px;
            border: 1px solid #6D2E3A;
            color: #6D2E3A;
        }

        .resCalendar .form-control:focus {
            border-color: #6D2E3A;
            box-shadow: 0 0 0 0.15rem rgba(109, 46, 58, 0.35);
            outline: none;
        }

        .filter-label {
            font-size: 0.9rem;
            color: #6D2E3A;
            font-weight: 600;
            margin-right: 6px;
            white-space: nowrap;
        }

        #loadingModal {
            display: none;
            position: fixed;
            top: 0; 
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.45);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .loader-container {
            display: flex;
            flex-direction: column; /* spinner sa taas, text sa baba */
            justify-content: center;
            align-items: center;
            gap: 10px;              /* maliit na space sa pagitan */
            min-width: 150px;       /* optional para di mukhang dikit sa left */
        }
        .spinner {
            border: 6px solid  #6D2E3A;
            border-top: 6px solid var(--pink-soft);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loader-text {
            color: #6D2E3A;
            font-weight: bold;
            font-size: 16px;
        }

        .container-fluid {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .header-filters {
            margin-top: 0;
        }

        .pretty-modal {
            border-radius: 18px !important;
            border: none !important;
            background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%);
            box-shadow: 0 10px 28px rgba(0,0,0,0.12);
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

        @media (max-width: 767px) {
            .card-body {
                padding: 12px 4px !important;
            }
            .table th,
            .table td {
                padding: 8px 4px;
                font-size: 0.98em;
            }
            .container-fluid {
                padding-left: 18px !important;
                padding-right: 18px !important;
            }
            .header-icon,
            .header-text {
                font-size: 25px;
            }
            .header-icon {
                margin-left: 16px;
            }
            .reservations-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                padding: 10px 6px;
            }

            .header-filters {
                width: 100%;
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-end !important;        /* Right-align */
                gap: 12px !important;                   /* Space between filter groups */
                margin-top: 12px;
            }

            .header-filters .d-flex {
                flex-direction: row !important;
                align-items: center !important;
                gap: 6px !important;
                width: auto !important;
                margin-bottom: 0 !important;
            }

            .header-filters label {
                margin-right: 0 !important;
            }

            .header-filters select {
                width: 120px !important;
                min-width: 120px !important;
                max-width: 120px !important;
                margin-left: 0 !important;
            }
            .pretty-modal .modal-header {
                padding: 10px 14px;
            }
            .pretty-modal .modal-title {
                font-size: 17px;
            }
            .pretty-modal .btn-close {
                transform: scale(0.9);
            }
            .pretty-modal .modal-body {
                padding: 14px 16px;
                font-size: 13px;
            }
            .reservation-status-card .status-card-header {
                text-align: center !important;
                margin-left: auto !important;
                margin-right: auto !important;
                display: block !important;
                font-size: 1.15em !important;
            }
            .reservation-table-card {
                margin-bottom: 22px !important;
            }
            .dates-row {
                display: flex;
                flex-direction: column;
                gap: 4px;
                margin-bottom: 10px;
                width: 100%;
            }
            .dates-row .date-line {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                width: 100%;
            }
            .date-label {
                font-weight: 600;
                color: #6D2E3A;
                font-size: 0.98em;
            }
            .date-value {
                font-size: 0.98em;
                color: #6D2E3A;
                font-weight: 500;
                margin-left: 8px;
                text-align: right;
                min-width: 110px;
                max-width: 160px;
            }
            .reservation-table-card .table th {
                font-size: 0.75em !important;
                padding: 6px 2px !important;
                /* Wag galawin ang width: */
            }
            .reservation-table-card .table td {
                font-size: 0.65em !important;
            }
            .viewSummaryBtn {
                min-height: 26px !important;
                padding: 2px 12px !important;
                font-size: 0.95em !important;
                border-radius: 5px !important;
            }
            .summary-row-mobile {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 12px;
                width: 100%;
                margin-top: 14px;
                margin-bottom: 4px;
            }
            .summary-row-mobile h5 {
                margin: 0 !important;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid px-3 px-md-5 py-3">
        <div class="reservations-header sticky-top mb-4">
            <div class="d-flex align-items-center">
                <span class="header-icon"><i class="bi bi-calendar-fill"></i></span>
                <span class="header-text"> Reservations</span>
            </div>
            <form id="filterForm" method="GET" class="filter-form d-flex align-items-center gap-3 header-filters">
                <div class="d-flex align-items-center">
                    <label for="statusDropdown" class="filter-label">Status:</label>
                    <select class="form-select" name="status" id="statusDropdown"
                            onchange="this.form.reservation_date.value=''; this.form.submit();">
                        <option value="all" <?= (!isset($_GET['status']) || $_GET['status'] === 'all') ? 'selected' : '' ?>>All</option>
                        <option value="pending" <?= (@$_GET['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="picked_up" <?= (@$_GET['status'] === 'picked_up') ? 'selected' : '' ?>>Picked_up</option>
                        <option value="cancelled" <?= (@$_GET['status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="d-flex align-items-center resCalendar">
                    <label for="reservationDateInput" class="filter-label">Reservation Date:</label>
                    <input
                        type="text"
                        class="form-control"
                        name="reservation_date"
                        id="reservationDateInput"
                        value="<?= htmlspecialchars($reservation_date) ?>"
                        placeholder="Select date"
                    >
                </div>
            </form>
        </div>
        <?php if (!empty($reservations)): ?>
            <?php foreach ($reservations as $status => $statusReservations): ?>
                <div class="card mb-4 reservation-status-card">
                    <div class="card-body">
                        <h4 class="mb-2 status-card-header"><?= htmlspecialchars($status) ?></h4>
                        <?php foreach ($statusReservations as $row): ?>
                            <?php
                            $reservation_id = $row['reservation_id'];
                            $total = number_format($row['total'],2);
                            $res_date = date("M d, Y", strtotime($row['reservation_date']));
                            $pickup_date = !empty($row['pickup_date'])    ? date("M d, Y", strtotime($row['pickup_date']))    : '';
                            $date_picked_up = !empty($row['date_picked_up']) ? date("M d, Y", strtotime($row['date_picked_up'])) : '';
                            $cancel_date = !empty($row['cancel_date'])    ? date("M d, Y", strtotime($row['cancel_date']))    : '';
                            $status_value = $row['status'];
                            // Fetch items
                            $itemQuery = "
                                SELECT p.image_path, p.product_name, p.price, ri.quantity, (p.price * ri.quantity) AS subtotal
                                FROM reservation_items ri
                                INNER JOIN products p ON ri.product_id = p.product_id
                                WHERE ri.reservation_id = ?
                            ";
                            $itemStmt = $conn->prepare($itemQuery);
                            $itemStmt->bind_param("i", $reservation_id);
                            $itemStmt->execute();
                            $itemResult = $itemStmt->get_result();
                            ?>
                            <div class="card reservation-table-card mb-2" style="border-radius: 12px; border: 1px solid #ec7699;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="dates-row">
                                            <div class="date-line">
                                                <span class="date-label"><strong>Reservation Date:</strong></span>
                                                <span class="date-value"><?= $res_date ?></span>
                                            </div>
                                            <div class="date-line">
                                                <?php if ($status_value === 'pending'): ?>
                                                    <span class="date-label"><strong>Pick Up Date:</strong></span>
                                                    <span class="date-value"><?= $pickup_date ?></span>
                                                <?php elseif ($status_value === 'picked_up'): ?>
                                                    <span class="date-label"><strong>Date Picked Up:</strong></span>
                                                    <span class="date-value"><?= $date_picked_up ?></span>
                                                <?php elseif ($status_value === 'cancelled'): ?>
                                                    <span class="date-label"><strong>Cancelled Date:</strong></span>
                                                    <span class="date-value"><?= $cancel_date ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle mb-0">
                                            <thead class="table-pink text-center">
                                                <tr>
                                                    <th class="img-col" style="width: 100px;">Image</th>
                                                    <th style="width: 600px;">Product Name</th>
                                                    <th style="width: 200px;">Price</th>
                                                    <th style="width: 100px;">Quantity</th>
                                                    <th style="width: 200px;">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($itemResult->num_rows > 0) {
                                                    while ($item = $itemResult->fetch_assoc()) {
                                                        echo "<tr>
                                                            <td class='img-col'><img src='{$item['image_path']}'></td>
                                                            <td>{$item['product_name']}</td>
                                                            <td>₱" . number_format($item['price'],2) . "</td>
                                                            <td>{$item['quantity']}</td>
                                                            <td>₱" . number_format($item['subtotal'],2) . "</td>
                                                        </tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='5' class='text-center text-muted'>No items found.</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3 mb-0 summary-row-mobile">
                                        <button class="btn btn-pink btn-sm viewSummaryBtn" data-id="<?= $reservation_id ?>">View Summary</button>
                                        <h5 style="color: #6d2e3a; margin: 0;">
                                            <strong>Total:</strong> ₱<span id="totalAmount"><?= $total ?></span>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">No reservations found.</p>
        <?php endif; ?>
    </div>

    <!-- Summary Modal -->
    <div class="modal fade" id="summaryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pretty-modal">
                <div class="modal-header py-3">
                    <h5 class="modal-title"><strong>Reservation Summary</strong></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reservationSummaryBody" style="font-weight: 500;">
                    <div class="text-center text-muted">Loading summary...</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            // View summary modal logic
            document.querySelectorAll('.viewSummaryBtn').forEach(button => {
                button.addEventListener('click', () => {
                    const reservationId = button.getAttribute('data-id');
                    const summaryBody = document.getElementById('reservationSummaryBody');
                    summaryBody.innerHTML = "<div class='text-center text-muted'>Loading summary...</div>";

                    document.getElementById("loadingModal").style.display = "flex";
                    setTimeout(() => {
                        fetch('fetch_reservation_summary.php?reservation_id=' + reservationId)
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById("loadingModal").style.display = "none";
                            if (!data.success) {
                                summaryBody.innerHTML = `<div class='text-danger text-center'>${data.message}</div>`;
                                return;
                            }
                            const r = data.reservation;
                            let itemsHTML = "";
                            r.items.forEach(i => {
                                itemsHTML += `
                                <tr>
                                    <td>${i.product_name}</td>
                                    <td class="text-center">${i.quantity}</td>
                                    <td class="text-center">₱${i.amount}</td>
                                </tr>`;
                            });
                            summaryBody.innerHTML = `
                                <p><strong>Customer:</strong> ${r.customer_name}</p>
                                <p><strong>Status:</strong> ${r.status}</p>
                                <p><strong>Reservation Date:</strong> ${r.reservation_date}</p>
                                <p><strong>Pickup Date:</strong> ${r.pickup_date}</p>
                                <hr style="margin: 10px 0;">
                                <div style="font-family: 'Roboto Mono', monospace; font-size: 15px;">
                                    ${r.items.map(i => `
                                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #ccc; padding: 7px 0;">
                                            <div style="flex:2; text-align:left;">${i.product_name}</div>
                                            <div style="flex:1; text-align:center;">x${i.quantity}</div>
                                            <div style="flex:1; text-align:right;">₱${i.amount}</div>
                                        </div>
                                    `).join('')}
                                    <div style="display: flex; justify-content: flex-end; margin-top:14px;">
                                        <strong style="font-size: 16px; color: #6d2e3a;">
                                            Total: ₱${r.total}
                                        </strong>
                                    </div>
                                </div>
                            `;
                            new bootstrap.Modal(document.getElementById('summaryModal')).show();
                        })
                        .catch(err => {
                            document.getElementById("loadingModal").style.display = "none";
                            summaryBody.innerHTML = "<div class='text-center text-danger'>Error loading summary.</div>";
                        });
                    }, 500);
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#reservationDateInput", {
                dateFormat: "Y-m-d",      // para tugma sa PHP/DB filter mo
                allowInput: true,
                defaultDate: "<?= $reservation_date ? htmlspecialchars($reservation_date) : '' ?>",
                onChange: function(selectedDates, dateStr, instance) {
                    // auto-submit same as dati
                    document.getElementById('filterForm').submit();
                }
            });
        });
    </script>
</body>
</html>
