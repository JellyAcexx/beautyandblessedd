<?php

// ✅ Proteksyon: bawal pumasok kung walang admin session
if (!isset($_SESSION['admin_email'])) {
    // optional anti-cache
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");

    header("Location: log_admin.php");
    exit();
}

include "database.php";

$query = $conn->query("SELECT * FROM audit_logs ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        :root{
            --pink-very-light:#F8D7DC;
            --pink-soft:#E8A9B2;
            --pink-mid:#D96D84;
            --pink-dark:#6D2E3A;
            --bg-light:#fff5f9;
            --text-main:var(--pink-dark);
            --border-main:var(--pink-mid);
        }

        html, body {
            overflow: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            overscroll-behavior: none;
            background: #f7f7f7;
        }

        body, * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: var(--text-main);
        }

        /* Header */
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
            margin-bottom: 10px;
        }

        .heading {
            color: var(--pink-dark);
            font-weight: bold;
        }

        /* Card frame identical to customers.php */
        .table-frame {
            width: 100%;
            background: #fff;
            padding: 0;
            box-shadow: 0px 2px 7px rgba(0,0,0,0.08);
        }

        /* Scroll wrapper same as customers.php */
        /* FIX STICKY HEADER OVERLAP */
        .table-scroll {
            max-height: 560px;
            overflow-y: auto;
            position: relative;
        }

        .table thead {
            position: sticky;
            top: 0;
            z-index: 15;
        }

        .table thead th {
            background: #a95469 !important;
            color: #fff !important;
            z-index: 20;
        }


        /* Sticky header */
        .table thead th {
            background: #a95469 !important;
            color: white !important;
            font-weight: bold;
            font-size: 1.1rem;
            text-align: center;
            border: none;
            padding: 18px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* CUSTOM ROW COLORS (pink stripes same as customers.php) */
        .table-striped-pink tbody tr:nth-child(odd) td {
            background: #fff !important;
        }

        .table-striped-pink tbody tr:nth-child(even) td {
            background: #fae1ec !important;
        }

        /* Table cells style */
        .table tbody td {
            color: var(--pink-dark);
            text-align: center;
            font-size: 1.05rem;
            border: none;
            padding: 20px;
            vertical-align: middle;
        }

        /* Soft border (horizontal only) */
        .table tbody tr {
            border-top: 1px solid var(--pink-soft) !important;
            border-bottom: 1px solid var(--pink-soft) !important;
        }

        /* Remove vertical borders */
        .table,
        .table-bordered th,
        .table-bordered td {
            border-left: none !important;
            border-right: none !important;
        }

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
        }

    </style>
</head>

<body>

<div>
    <div class="header-container">
        <h1 class="heading" style="display:flex;align-items:center;font-size:2em;">
            <i class="fa-solid fa-note-sticky"style="margin-left: 12px; margin-right: 12px;"></i>
            Audit Logs
        </h1>
    </div>

    <div class="table-frame">
        <div class="table-responsive">
            <div class="table-scroll">
                <table class="table table-bordered table-hover table-striped-pink">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Role</th>
                            <th>Task Performed</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $query->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['role'] ?></td>
                            <td><?= $row['task_performed'] ?></td>
                            <td><?= $row['date_time'] ?></td>
                        </tr>
                        <?php endwhile; ?>

                        <?php if ($query->num_rows == 0): ?>
                        <tr>
                            <td colspan="4" class="text-muted">No logs found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
