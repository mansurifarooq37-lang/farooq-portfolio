<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/db.php");

/* =========================
   FETCH MESSAGES
========================= */

$result = mysqli_query(
    $conn,
    "SELECT * FROM messages ORDER BY id DESC"
);

/* =========================
   NOTIFICATIONS
========================= */

$message_success = $_SESSION['message_success'] ?? '';
$message_error   = $_SESSION['message_error'] ?? '';

unset($_SESSION['message_success']);
unset($_SESSION['message_error']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Messages | Admin Panel</title>

    <!-- ADMIN CSS -->
    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

    <!-- REMIX ICONS -->
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css"
        rel="stylesheet"
    >

    <style>

        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .topbar-left h2 {
            margin: 0;
        }

        .topbar-left p {
            margin: 5px 0 0;
            color: #8f96a8;
            font-size: 13px;
        }


        /* =====================================================
           BACK TO DASHBOARD
        ===================================================== */

        .back-dashboard {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border-radius: 11px;
            color: #cfd3ff;
            text-decoration: none;
            background: rgba(124, 92, 255, 0.10);
            border: 1px solid rgba(124, 92, 255, 0.25);
            font-size: 13px;
            font-weight: 600;
            transition: .25s ease;
            white-space: nowrap;
        }

        .back-dashboard i {
            font-size: 17px;
        }

        .back-dashboard:hover {
            color: #fff;
            background: rgba(124, 92, 255, 0.20);
            border-color: rgba(124, 92, 255, 0.50);
            transform: translateX(-3px);
            box-shadow: 0 8px 25px rgba(124, 92, 255, 0.15);
        }


        /* =====================================================
           MESSAGES BOX
        ===================================================== */

        .messages-box {
            margin-top: 30px;
            background: rgba(255,255,255,.96);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 15px 45px rgba(0,0,0,.07);
            border: 1px solid rgba(0,0,0,.05);
        }


        /* =====================================================
           MESSAGES HEADER
        ===================================================== */

        .messages-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .messages-header h3 {
            margin: 0;
            font-size: 22px;
            color: #222;
        }


        /* =====================================================
           NOTIFICATIONS
        ===================================================== */

        .message-notification {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            animation: notificationSlide .35s ease;
        }

        .message-notification i {
            font-size: 22px;
        }

        .message-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .message-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        @keyframes notificationSlide {

            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }


        /* =====================================================
           TABLE WRAPPER
        ===================================================== */

        .message-table-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border-radius: 12px;
            -webkit-overflow-scrolling: touch;
        }

        .message-table-wrapper::-webkit-scrollbar {
            height: 8px;
        }

        .message-table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .message-table-wrapper::-webkit-scrollbar-thumb {
            background: #b8b1ff;
            border-radius: 10px;
        }

        .message-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #7c5cff;
        }


        /* =====================================================
           MESSAGE TABLE
        ===================================================== */

        .message-table {
            width: 100%;
            min-width: 1120px;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
        }

        .message-table th {
            background: #f5f6f8;
            padding: 14px 13px;
            text-align: left;
            font-size: 13px;
            color: #555;
            font-weight: 700;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .message-table td {
            padding: 14px 13px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
            vertical-align: top;
        }

        .message-table tr:last-child td {
            border-bottom: none;
        }

        .message-table tbody tr {
            transition: .25s ease;
        }

        .message-table tbody tr:hover {
            background: #fafaff;
        }


        /* =====================================================
           COLUMN WIDTHS
        ===================================================== */

        .message-table th:nth-child(1),
        .message-table td:nth-child(1) {
            width: 55px;
            min-width: 55px;
        }

        .message-table th:nth-child(2),
        .message-table td:nth-child(2) {
            width: 145px;
            min-width: 145px;
        }

        .message-table th:nth-child(3),
        .message-table td:nth-child(3) {
            width: 215px;
            min-width: 215px;
        }

        .message-table th:nth-child(4),
        .message-table td:nth-child(4) {
            width: 125px;
            min-width: 125px;
        }

        .message-table th:nth-child(5),
        .message-table td:nth-child(5) {
            width: 150px;
            min-width: 150px;
        }

        .message-table th:nth-child(6),
        .message-table td:nth-child(6) {
            width: 300px;
            min-width: 300px;
        }

        .message-table th:nth-child(7),
        .message-table td:nth-child(7) {
            width: 165px;
            min-width: 165px;
        }

        .message-table th:nth-child(8),
        .message-table td:nth-child(8) {
            width: 105px;
            min-width: 105px;
        }


        /* =====================================================
           MESSAGE TEXT
        ===================================================== */

        .message-text {
            max-width: 300px;
            line-height: 1.6;
            color: #555 !important;
            word-break: break-word;
        }


        /* =====================================================
           EMAIL
        ===================================================== */

        .email-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            white-space: nowrap;
        }

        .email-link:hover {
            text-decoration: underline;
        }


        /* =====================================================
           PHONE
        ===================================================== */

        .phone-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #059669;
            text-decoration: none;
            font-weight: 600;
            white-space: nowrap;
        }

        .phone-link i {
            font-size: 15px;
        }

        .phone-link:hover {
            color: #047857;
            text-decoration: underline;
        }

        .phone-empty {
            color: #9ca3af;
            font-style: italic;
        }


        /* =====================================================
           DATE
        ===================================================== */

        .message-date {
            white-space: nowrap;
            color: #555;
        }


        /* =====================================================
           ACTION
        ===================================================== */

        .action-cell {
            text-align: center;
            white-space: nowrap;
        }

        .delete-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 13px;
            background: #fee2e2;
            color: #dc2626;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: .25s ease;
            white-space: nowrap;
        }

        .delete-btn:hover {
            background: #dc2626;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 7px 18px rgba(220,38,38,.20);
        }

        .delete-btn i {
            font-size: 15px;
        }


        /* =====================================================
           NO MESSAGE
        ===================================================== */

        .no-message {
            text-align: center;
            padding: 55px 20px;
            color: #777;
        }

        .no-message i {
            display: block;
            margin-bottom: 12px;
            color: #7c5cff;
        }

        .no-message p {
            margin: 0;
            font-size: 14px;
        }


        /* =====================================================
           DELETE MODAL
        ===================================================== */

        .delete-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        }

        .delete-modal.active {
            display: flex;
        }


        /* =====================================================
           MODAL OVERLAY
        ===================================================== */

        .delete-modal-overlay {
            position: absolute;
            inset: 0;
            background: rgba(2,4,12,.78);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }


        /* =====================================================
           MODAL BOX
        ===================================================== */

        .delete-modal-box {
            position: relative;
            width: min(430px, calc(100% - 40px));
            padding: 38px 32px 30px;
            border-radius: 24px;

            background:
                radial-gradient(
                    circle at 50% 0%,
                    rgba(124,92,255,.18),
                    transparent 48%
                ),
                #0d0f1a;

            border: 1px solid rgba(255,255,255,.09);

            box-shadow:
                0 30px 80px rgba(0,0,0,.65),
                0 0 50px rgba(108,78,255,.15);

            text-align: center;
            z-index: 2;

            animation:
                deleteModalShow
                .3s
                cubic-bezier(.2,.8,.2,1);
        }

        @keyframes deleteModalShow {

            from {
                opacity: 0;
                transform: translateY(25px) scale(.94);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

        }


        /* =====================================================
           DELETE ICON
        ===================================================== */

        .delete-modal-icon {
            width: 78px;
            height: 78px;
            margin: 0 auto 20px;
            border-radius: 22px;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    rgba(255,70,100,.18),
                    rgba(124,92,255,.12)
                );

            border: 1px solid rgba(255,80,100,.22);

            color: #ff536d;
            font-size: 34px;

            box-shadow:
                0 0 35px rgba(255,60,90,.12);

            animation:
                deleteIconPulse
                2s
                ease-in-out
                infinite;
        }

        @keyframes deleteIconPulse {

            0%,
            100% {
                box-shadow:
                    0 0 20px
                    rgba(255,60,90,.08);
            }

            50% {
                box-shadow:
                    0 0 35px
                    rgba(255,60,90,.22);
            }

        }


        /* =====================================================
           MODAL TITLE
        ===================================================== */

        .delete-modal-box h3 {
            margin: 0 0 10px;
            color: #fff;
            font-size: 25px;
            font-weight: 700;
        }


        /* =====================================================
           MODAL TEXT
        ===================================================== */

        .delete-modal-box p {
            margin: 0 auto 18px;
            max-width: 330px;
            color: #9ca3af;
            font-size: 14px;
            line-height: 1.7;
        }

        .delete-modal-name {
            display: block;
            margin-top: 8px;
            font-weight: 700;
            color: #fff;
        }


        /* =====================================================
           WARNING
        ===================================================== */

        .delete-warning {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;

            margin-bottom: 25px;
            padding: 8px 13px;

            border-radius: 10px;

            color: #ffb4bd;

            background: rgba(255,70,90,.08);

            border: 1px solid rgba(255,70,90,.12);

            font-size: 12px;
        }

        .delete-warning i {
            font-size: 15px;
        }


        /* =====================================================
           MODAL BUTTONS
        ===================================================== */

        .delete-modal-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .modal-cancel,
        .modal-delete {
            height: 46px;
            padding: 0 20px;
            border-radius: 12px;

            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            font-family: inherit;
            font-size: 13px;
            font-weight: 600;

            cursor: pointer;
            transition: .25s ease;
        }


        /* =====================================================
           CANCEL
        ===================================================== */

        .modal-cancel {
            flex: 1;
            border: 1px solid rgba(255,255,255,.09);
            background: rgba(255,255,255,.05);
            color: #d1d5db;
        }

        .modal-cancel:hover {
            background: rgba(255,255,255,.10);
            color: #fff;
            transform: translateY(-2px);
        }


        /* =====================================================
           DELETE
        ===================================================== */

        .modal-delete {
            flex: 1;

            border: 1px solid rgba(255,90,110,.35);

            background:
                linear-gradient(
                    135deg,
                    #ff4565,
                    #d92f55
                );

            color: #fff;

            box-shadow:
                0 8px 25px
                rgba(255,50,80,.18);
        }

        .modal-delete:hover {
            background:
                linear-gradient(
                    135deg,
                    #ff5573,
                    #e2385c
                );

            transform: translateY(-2px);

            box-shadow:
                0 12px 32px
                rgba(255,50,80,.30);
        }

        .modal-delete i {
            font-size: 16px;
        }


        /* =====================================================
           BODY MODAL LOCK
        ===================================================== */

        body.modal-open {
            overflow: hidden;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media(max-width: 1100px) {

            .messages-box {
                overflow: hidden;
            }

        }


        @media(max-width: 900px) {

            .message-table {
                min-width: 1120px;
            }

        }


        @media(max-width: 700px) {

            .topbar {
                align-items: flex-start;
            }

            .topbar-left {
                gap: 12px;
            }

            .back-dashboard {
                padding: 9px 11px;
            }

            .back-dashboard {
                font-size: 0;
            }

            .back-dashboard i {
                font-size: 18px;
            }

        }


        @media(max-width: 500px) {

            .messages-box {
                padding: 18px;
            }

            .messages-header h3 {
                font-size: 19px;
            }

            .delete-modal-box {
                padding: 34px 22px 24px;
                border-radius: 20px;
            }

            .delete-modal-icon {
                width: 68px;
                height: 68px;
                font-size: 29px;
            }

            .delete-modal-box h3 {
                font-size: 22px;
            }

            .delete-modal-actions {
                flex-direction: column;
            }

            .modal-cancel,
            .modal-delete {
                width: 100%;
            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<div class="sidebar">

    <div class="logo">

        <h2>
            Farooq<span>.</span>
        </h2>

    </div>


    <ul>

        <!-- DASHBOARD -->
        <li>

            <a href="dashboard.php">

                <i class="ri-dashboard-fill"></i>

                Dashboard

            </a>

        </li>


        <!-- ABOUT -->
        <li>

            <a href="about.php">

                <i class="ri-user-3-fill"></i>

                About

            </a>

        </li>


        <!-- SKILLS -->
        <li>

            <a href="skills.php">

                <i class="ri-code-box-fill"></i>

                Skills

            </a>

        </li>


        <!-- PROJECTS -->
        <li>

            <a href="project-list.php">

                <i class="ri-folder-fill"></i>

                Projects

            </a>

        </li>


        <!-- RESUME -->
        <li>

            <a href="resume-edit.php">

                <i class="ri-file-text-fill"></i>

                Resume

            </a>

        </li>


        <!-- MESSAGES -->
        <li class="active">

            <a href="messages.php">

                <i class="ri-message-3-fill"></i>

                Messages

            </a>

        </li>


        <!-- LOGOUT -->
        <li>

            <a href="logout.php">

                <i class="ri-logout-box-r-fill"></i>

                Logout

            </a>

        </li>

    </ul>

</div>


<!-- =====================================================
     MAIN
===================================================== -->

<div class="main">


    <!-- =================================================
         TOPBAR
    ================================================= -->

    <div class="topbar">


        <div class="topbar-left">


            <!-- BACK DASHBOARD -->

            <a
                href="dashboard.php"
                class="back-dashboard"
            >

                <i class="ri-arrow-left-line"></i>

                Back to Dashboard

            </a>


            <!-- PAGE TITLE -->

            <div>

                <h2>
                    Messages
                </h2>

                <p>
                    View and manage contact messages.
                </p>

            </div>


        </div>


        <!-- ADMIN -->

        <div class="admin">

            Welcome,

            <strong>

                <?php
                echo htmlspecialchars(
                    $_SESSION['admin']
                );
                ?>

            </strong>

        </div>


    </div>


    <!-- =================================================
         MESSAGES BOX
    ================================================= -->

    <div class="messages-box">


        <div class="messages-header">

            <h3>
                Contact Messages
            </h3>

        </div>


        <!-- SUCCESS MESSAGE -->

        <?php if (!empty($message_success)) { ?>

            <div
                class="message-notification message-success"
            >

                <i class="ri-checkbox-circle-fill"></i>

                <span>

                    <?php
                    echo htmlspecialchars(
                        $message_success
                    );
                    ?>

                </span>

            </div>

        <?php } ?>


        <!-- ERROR MESSAGE -->

        <?php if (!empty($message_error)) { ?>

            <div
                class="message-notification message-error"
            >

                <i class="ri-error-warning-fill"></i>

                <span>

                    <?php
                    echo htmlspecialchars(
                        $message_error
                    );
                    ?>

                </span>

            </div>

        <?php } ?>


        <!-- =================================================
             MESSAGES TABLE
        ================================================= -->

        <?php if (mysqli_num_rows($result) > 0) { ?>


            <div class="message-table-wrapper">


                <table class="message-table">


                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Name</th>

                            <th>Email</th>

                            <th>Phone</th>

                            <th>Subject</th>

                            <th>Message</th>

                            <th>Date</th>

                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>


                            <tr>


                                <!-- ID -->

                                <td>

                                    <?php
                                    echo (int)$row['id'];
                                    ?>

                                </td>


                                <!-- NAME -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $row['name'] ?? ''
                                    );
                                    ?>

                                </td>


                                <!-- EMAIL -->

                                <td>

                                    <a
                                        class="email-link"
                                        href="mailto:<?php
                                            echo htmlspecialchars(
                                                $row['email'] ?? ''
                                            );
                                        ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $row['email'] ?? ''
                                        );
                                        ?>

                                    </a>

                                </td>


                                <!-- PHONE -->

                                <td>

                                    <?php if (!empty($row['phone'])) { ?>

                                        <a
                                            class="phone-link"
                                            href="tel:<?php
                                                echo htmlspecialchars(
                                                    $row['phone']
                                                );
                                            ?>"
                                        >

                                            <i class="ri-phone-line"></i>

                                            <?php
                                            echo htmlspecialchars(
                                                $row['phone']
                                            );
                                            ?>

                                        </a>

                                    <?php } else { ?>

                                        <span class="phone-empty">
                                            Not provided
                                        </span>

                                    <?php } ?>

                                </td>


                                <!-- SUBJECT -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $row['subject'] ?? ''
                                    );
                                    ?>

                                </td>


                                <!-- MESSAGE -->

                                <td class="message-text">

                                    <?php

                                    echo nl2br(
                                        htmlspecialchars(
                                            $row['message'] ?? ''
                                        )
                                    );

                                    ?>

                                </td>


                                <!-- DATE -->

                                <td class="message-date">

                                    <?php
                                    echo htmlspecialchars(
                                        $row['created_at'] ?? ''
                                    );
                                    ?>

                                </td>


                                <!-- ACTION -->

                                <td class="action-cell">


                                    <button
                                        type="button"
                                        class="delete-btn"
                                        onclick="openDeleteModal(
                                            <?php echo (int)$row['id']; ?>,
                                            <?php
                                            echo htmlspecialchars(
                                                json_encode(
                                                    $row['name'] ?? ''
                                                ),
                                                ENT_QUOTES
                                            );
                                            ?>
                                        )"
                                    >

                                        <i class="ri-delete-bin-line"></i>

                                        Delete

                                    </button>


                                </td>


                            </tr>


                        <?php } ?>


                    </tbody>


                </table>


            </div>


        <?php } else { ?>


            <!-- NO MESSAGES -->

            <div class="no-message">


                <i
                    class="ri-mail-open-line"
                    style="font-size:40px;"
                ></i>


                <p>
                    No messages found.
                </p>


            </div>


        <?php } ?>


    </div>


</div>


<!-- =====================================================
     DELETE MODAL
===================================================== -->

<div
    class="delete-modal"
    id="deleteModal"
>


    <!-- OVERLAY -->

    <div
        class="delete-modal-overlay"
        onclick="closeDeleteModal()"
    ></div>


    <!-- MODAL BOX -->

    <div class="delete-modal-box">


        <!-- ICON -->

        <div class="delete-modal-icon">

            <i class="ri-delete-bin-6-line"></i>

        </div>


        <!-- TITLE -->

        <h3>
            Delete Message?
        </h3>


        <!-- MESSAGE -->

        <p>

            Are you sure you want to delete this message?

            <span
                class="delete-modal-name"
                id="deleteMessageName"
            ></span>

        </p>


        <!-- WARNING -->

        <span class="delete-warning">

            <i class="ri-error-warning-line"></i>

            This action cannot be undone.

        </span>


        <!-- ACTIONS -->

        <div class="delete-modal-actions">


            <!-- CANCEL -->

            <button
                type="button"
                class="modal-cancel"
                onclick="closeDeleteModal()"
            >

                <i class="ri-close-line"></i>

                Cancel

            </button>


            <!-- DELETE -->

            <button
                type="button"
                class="modal-delete"
                onclick="confirmDeleteMessage()"
            >

                <i class="ri-delete-bin-line"></i>

                Yes, Delete

            </button>


        </div>


    </div>

</div>


<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script>

    let deleteMessageId = null;


    /* =================================================
       OPEN DELETE MODAL
    ================================================= */

    function openDeleteModal(id, name) {

        deleteMessageId = id;

        document.getElementById(
            "deleteMessageName"
        ).textContent = '"' + name + '"';

        document.getElementById(
            "deleteModal"
        ).classList.add("active");

        document.body.classList.add(
            "modal-open"
        );

    }


    /* =================================================
       CLOSE DELETE MODAL
    ================================================= */

    function closeDeleteModal() {

        deleteMessageId = null;

        document.getElementById(
            "deleteModal"
        ).classList.remove("active");

        document.body.classList.remove(
            "modal-open"
        );

    }


    /* =================================================
       CONFIRM DELETE
    ================================================= */

    function confirmDeleteMessage() {

        if (deleteMessageId !== null) {

            window.location.href =
                "message-delete.php?id=" +
                deleteMessageId;

        }

    }


    /* =================================================
       ESC KEY
    ================================================= */

    document.addEventListener(
        "keydown",
        function(event) {

            if (event.key === "Escape") {

                closeDeleteModal();

            }

        }
    );


    /* =================================================
       AUTO HIDE NOTIFICATION
    ================================================= */

    setTimeout(
        function() {

            const notifications =
                document.querySelectorAll(
                    ".message-notification"
                );

            notifications.forEach(
                function(notification) {

                    notification.style.transition =
                        "opacity .4s ease, transform .4s ease";

                    notification.style.opacity = "0";

                    notification.style.transform =
                        "translateY(-8px)";

                    setTimeout(
                        function() {

                            notification.remove();

                        },
                        400
                    );

                }
            );

        },
        4000
    );

</script>


</body>

</html>