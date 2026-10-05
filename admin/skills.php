<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/db.php");

$message = "";
$message_type = "";


/* =========================
   DELETE SKILL
========================= */

if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    if ($id > 0) {

        $delete_query = "DELETE FROM skills WHERE id = $id";

        if (mysqli_query($conn, $delete_query)) {

            $message = "Skill deleted successfully!";
            $message_type = "success";

        } else {

            $message = "Failed to delete skill!";
            $message_type = "error";
        }

    } else {

        $message = "Invalid skill!";
        $message_type = "error";
    }
}


/* =========================
   ADD SKILL
========================= */

if (isset($_POST['add_skill'])) {

    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $skill_name = mysqli_real_escape_string($conn, $_POST['skill_name']);
    $icon = mysqli_real_escape_string($conn, $_POST['icon']);

    $query = "INSERT INTO skills (category, skill_name, icon)
              VALUES ('$category', '$skill_name', '$icon')";

    if (mysqli_query($conn, $query)) {

        $message = "Skill added successfully!";
        $message_type = "success";

    } else {

        $message = "Something went wrong!";
        $message_type = "error";
    }
}


/* =========================
   GET ALL SKILLS
========================= */

$query = "SELECT * FROM skills ORDER BY category, id DESC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Skills | Admin</title>

    <link rel="stylesheet" href="assets/css/admin.css">

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet"
    >

    <style>

        /* =====================================================
           PAGE
        ===================================================== */

        .page-content {
            padding: 35px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h2 {
            font-size: 30px;
            margin-bottom: 6px;
        }

        .page-header p {
            color: #777;
            font-size: 14px;
        }


        /* =====================================================
           PAGE HEADER ACTIONS
        ===================================================== */

        .page-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .dashboard-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            padding: 11px 18px;

            border-radius: 10px;

            background: #6C63FF;
            color: #fff;

            font-size: 14px;
            font-weight: 600;

            text-decoration: none;

            transition: .3s ease;

            box-shadow:
                0 8px 20px rgba(108,99,255,.15);
        }

        .dashboard-btn i {
            font-size: 18px;
        }

        .dashboard-btn:hover {
            transform: translateY(-2px);

            background: #554de0;

            box-shadow:
                0 12px 28px rgba(108,99,255,.28);
        }


        /* =====================================================
           NOTIFICATION
        ===================================================== */

        .skill-notification {

            display: flex;
            align-items: center;
            gap: 14px;

            padding: 16px 18px;

            border-radius: 14px;

            margin-bottom: 25px;

            position: relative;
            overflow: hidden;

            animation:
                notificationSlide .45s ease;

        }

        .skill-notification.success {

            background:
                linear-gradient(
                    135deg,
                    rgba(34,197,94,.12),
                    rgba(34,197,94,.05)
                );

            border:
                1px solid
                rgba(34,197,94,.25);

            color: #15803d;

        }

        .skill-notification.error {

            background:
                linear-gradient(
                    135deg,
                    rgba(239,68,68,.12),
                    rgba(239,68,68,.05)
                );

            border:
                1px solid
                rgba(239,68,68,.25);

            color: #dc2626;

        }

        .notification-icon {

            width: 40px;
            height: 40px;

            min-width: 40px;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 21px;

        }

        .skill-notification.success
        .notification-icon {

            background:
                rgba(34,197,94,.15);

            color: #16a34a;

        }

        .skill-notification.error
        .notification-icon {

            background:
                rgba(239,68,68,.15);

            color: #dc2626;

        }

        .notification-content {

            display: flex;
            flex-direction: column;

            gap: 3px;

        }

        .notification-content strong {

            font-size: 15px;

        }

        .notification-content span {

            font-size: 13px;

            opacity: .8;

        }

        .notification-close {

            margin-left: auto;

            border: none;

            background: transparent;

            font-size: 20px;

            cursor: pointer;

            color: inherit;

            opacity: .65;

            padding: 5px;

        }

        .notification-close:hover {
            opacity: 1;
        }

        @keyframes notificationSlide {

            from {

                opacity: 0;

                transform:
                    translateY(-12px);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0);

            }

        }


        /* =====================================================
           SKILL CARD
        ===================================================== */

        .skill-card {

            background: #fff;

            padding: 30px;

            border-radius: 20px;

            box-shadow:
                0 15px 40px
                rgba(0,0,0,.08);

            margin-bottom: 30px;

            transition: .3s ease;

        }

        .skill-card:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 20px 50px
                rgba(0,0,0,.10);

        }

        .skill-card h3 {

            margin-bottom: 20px;

            color: #222 !important;

            font-size: 19px;

        }

        .skill-card h3 i {

            color: #6C63FF;

            margin-right: 6px;

        }


        /* =====================================================
           FORM
        ===================================================== */

        .form-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

        }

        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-weight: 600;

            color: #444 !important;

        }

        .form-group input,
        .form-group select {

            width: 100%;

            padding: 13px 15px;

            border:
                1px solid #ddd;

            border-radius: 10px;

            outline: none;

            font-family: inherit;

            color: #222 !important;

            background: #fff !important;

            transition: .25s ease;

        }

        .form-group input:focus,
        .form-group select:focus {

            border-color: #6C63FF;

            box-shadow:
                0 0 0 3px
                rgba(108,99,255,.08);

        }

        .form-group select option {

            color: #222 !important;

            background: #fff !important;

        }


        /* =====================================================
           ADD BUTTON
        ===================================================== */

        .add-btn {

            margin-top: 20px;

            border: none;

            background: #6C63FF;

            color: #fff;

            padding: 13px 25px;

            border-radius: 10px;

            font-size: 15px;

            font-weight: 600;

            cursor: pointer;

            transition: .25s ease;

        }

        .add-btn:hover {

            background: #554de0;

            transform:
                translateY(-2px);

            box-shadow:
                0 8px 20px
                rgba(108,99,255,.25);

        }


        /* =====================================================
           TABLE
        ===================================================== */

        .skills-table-wrapper {

            width: 100%;

            overflow-x: auto;

        }

        .skills-table {

            width: 100%;

            border-collapse: collapse;

            min-width: 650px;

        }

        .skills-table th,
        .skills-table td {

            padding: 15px;

            text-align: left;

            border-bottom:
                1px solid #eee;

        }

        .skills-table th {

            color: #555 !important;

            background: #f8f9fc;

            font-size: 13px;

            font-weight: 700;

        }

        .skills-table td {

            color: #444 !important;

            font-size: 14px;

        }

        .skills-table tbody tr {

            transition: .25s ease;

        }

        .skills-table tbody tr:hover {

            background: #fafaff;

        }

        .skills-table td strong {

            color: #222 !important;

        }

        .skill-icon {

            font-size: 22px;

            color: #6C63FF;

        }


        /* =====================================================
           CATEGORY BADGE
        ===================================================== */

        .category-badge {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            background: #f0efff;

            color: #5b54d6;

            font-size: 13px;

            font-weight: 600;

        }


        /* =====================================================
           DELETE BUTTON
        ===================================================== */

        .delete-btn {

            display: inline-flex;

            align-items: center;

            gap: 5px;

            padding: 7px 12px;

            border: none;

            border-radius: 8px;

            background: #ffeaea;

            color: #d63031;

            text-decoration: none;

            font-size: 13px;

            font-weight: 600;

            cursor: pointer;

            transition: .25s ease;

        }

        .delete-btn:hover {

            background: #d63031;

            color: #fff;

            transform:
                translateY(-1px);

        }


        /* =====================================================
           DELETE MODAL
        ===================================================== */

        .delete-modal {

            position: fixed;

            inset: 0;

            z-index: 99999;

            display: flex;

            align-items: center;

            justify-content: center;

            visibility: hidden;

            opacity: 0;

            transition: .3s ease;

        }

        .delete-modal.active {

            visibility: visible;

            opacity: 1;

        }

        .delete-modal-overlay {

            position: absolute;

            inset: 0;

            background:
                rgba(5,8,20,.72);

            backdrop-filter:
                blur(8px);

        }

        .delete-modal-box {

            position: relative;

            z-index: 2;

            width: 420px;

            max-width:
                calc(100% - 40px);

            padding: 32px;

            background: #fff;

            border-radius: 22px;

            text-align: center;

            box-shadow:
                0 25px 70px
                rgba(0,0,0,.25);

            transform:
                translateY(25px)
                scale(.95);

            transition: .3s ease;

        }

        .delete-modal.active
        .delete-modal-box {

            transform:
                translateY(0)
                scale(1);

        }

        .modal-close {

            position: absolute;

            top: 15px;

            right: 15px;

            width: 34px;

            height: 34px;

            border: none;

            border-radius: 50%;

            background: #f4f4f6;

            color: #555;

            font-size: 20px;

            cursor: pointer;

            transition: .2s ease;

        }

        .modal-close:hover {

            background: #ffeaea;

            color: #d63031;

        }

        .modal-delete-icon {

            width: 70px;

            height: 70px;

            margin:
                5px auto 20px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                rgba(214,48,49,.1);

            color: #d63031;

            font-size: 32px;

            animation:
                deleteIconPulse 1.8s infinite;

        }

        @keyframes deleteIconPulse {

            0%,
            100% {

                box-shadow:
                    0 0 0 0
                    rgba(214,48,49,.15);

            }

            50% {

                box-shadow:
                    0 0 0 10px
                    rgba(214,48,49,.03);

            }

        }

        .delete-modal-box h3 {

            margin: 0 0 10px;

            font-size: 23px;

            color: #222;

        }

        .delete-modal-box p {

            margin:
                0 auto 8px;

            max-width: 330px;

            color: #666;

            font-size: 14px;

            line-height: 1.6;

        }

        .delete-modal-box p strong {

            color: #222;

        }

        .modal-warning {

            display: block;

            margin-bottom: 25px;

            color: #d63031;

            font-size: 12px;

            font-weight: 600;

        }

        .modal-actions {

            display: flex;

            gap: 12px;

            justify-content: center;

        }

        .modal-cancel,
        .modal-delete {

            flex: 1;

            padding: 12px 18px;

            border-radius: 10px;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;

            text-decoration: none;

            transition: .25s ease;

        }

        .modal-cancel {

            border:
                1px solid #ddd;

            background: #f7f7f8;

            color: #555;

        }

        .modal-cancel:hover {

            background: #eee;

        }

        .modal-delete {

            border: none;

            background: #d63031;

            color: #fff;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            box-shadow:
                0 8px 20px
                rgba(214,48,49,.2);

        }

        .modal-delete:hover {

            background: #b92324;

            transform:
                translateY(-2px);

            box-shadow:
                0 12px 25px
                rgba(214,48,49,.3);

        }

        body.modal-open {
            overflow: hidden;
        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media(max-width:768px) {

            .page-content {
                padding: 20px;
            }

            .page-header-row {

                flex-direction: column;

                align-items: flex-start;

            }

            .dashboard-btn {

                width: 100%;

            }

            .form-grid {

                grid-template-columns: 1fr;

            }

            .skill-card {

                padding: 20px;

                overflow: hidden;

            }

            .skill-notification {

                padding: 13px;

            }

        }

        @media(max-width:500px) {

            .delete-modal-box {

                padding:
                    27px 20px;

            }

            .modal-actions {

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

        <li>

            <a href="dashboard.php">

                <i class="ri-dashboard-fill"></i>

                Dashboard

            </a>

        </li>


        <li>

            <a href="about.php">

                <i class="ri-user-3-fill"></i>

                About

            </a>

        </li>


        <li class="active">

            <a href="skills.php">

                <i class="ri-code-box-fill"></i>

                Skills

            </a>

        </li>


        <li>

            <a href="project-list.php">

                <i class="ri-folder-fill"></i>

                Projects

            </a>

        </li>


        <li>

            <a href="resume-edit.php">

                <i class="ri-file-text-fill"></i>

                Resume

            </a>

        </li>


        <li>

            <a href="messages.php">

                <i class="ri-message-3-fill"></i>

                Messages

            </a>

        </li>


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


    <!-- TOPBAR -->

    <div class="topbar">

        <h2>
            Skills
        </h2>

        <div class="admin">

            Welcome,

            <strong>
                <?php echo htmlspecialchars($_SESSION['admin']); ?>
            </strong>

        </div>

    </div>


    <!-- PAGE CONTENT -->

    <div class="page-content">


        <!-- PAGE HEADER -->

        <div class="page-header-row">

            <div class="page-header">

                <h2>
                    Manage Skills
                </h2>

                <p>
                    Add and manage the technical skills displayed on your portfolio.
                </p>

            </div>


            <!-- BACK DASHBOARD -->

            <a
                href="dashboard.php"
                class="dashboard-btn"
            >

                <i class="ri-arrow-left-line"></i>

                Back to Dashboard

            </a>

        </div>


        <!-- =================================================
             NOTIFICATION
        ================================================= -->

        <?php if ($message != "") { ?>

            <div
                class="skill-notification <?php echo htmlspecialchars($message_type); ?>"
                id="skillNotification"
            >

                <div class="notification-icon">

                    <?php if ($message_type == "success") { ?>

                        <i class="ri-checkbox-circle-fill"></i>

                    <?php } else { ?>

                        <i class="ri-error-warning-fill"></i>

                    <?php } ?>

                </div>


                <div class="notification-content">

                    <strong>

                        <?php

                        echo $message_type == "success"
                            ? "Success!"
                            : "Something went wrong!";

                        ?>

                    </strong>


                    <span>

                        <?php
                        echo htmlspecialchars($message);
                        ?>

                    </span>

                </div>


                <button
                    type="button"
                    class="notification-close"
                    onclick="closeNotification()"
                >

                    <i class="ri-close-line"></i>

                </button>

            </div>

        <?php } ?>


        <!-- =================================================
             ADD NEW SKILL
        ================================================= -->

        <div class="skill-card">

            <h3>

                <i class="ri-add-circle-line"></i>

                Add New Skill

            </h3>


            <form method="POST">


                <div class="form-grid">


                    <!-- CATEGORY -->

                    <div class="form-group">

                        <label>
                            Category
                        </label>

                        <select
                            name="category"
                            required
                        >

                            <option value="">
                                Select Category
                            </option>

                            <option value="Frontend">
                                Frontend
                            </option>

                            <option value="Backend">
                                Backend
                            </option>

                            <option value="Tools">
                                Tools
                            </option>

                        </select>

                    </div>


                    <!-- SKILL -->

                    <div class="form-group">

                        <label>
                            Skill Name
                        </label>

                        <input
                            type="text"
                            name="skill_name"
                            placeholder="Example: Laravel"
                            required
                        >

                    </div>


                    <!-- ICON -->

                    <div class="form-group">

                        <label>
                            Remix Icon
                        </label>

                        <input
                            type="text"
                            name="icon"
                            placeholder="Example: ri-code-line"
                        >

                    </div>


                </div>


                <button
                    type="submit"
                    name="add_skill"
                    class="add-btn"
                >

                    <i class="ri-add-line"></i>

                    Add Skill

                </button>


            </form>

        </div>


        <!-- =================================================
             ALL SKILLS
        ================================================= -->

        <div class="skill-card">

            <h3>

                <i class="ri-list-check-2"></i>

                All Skills

            </h3>


            <div class="skills-table-wrapper">

                <table class="skills-table">

                    <thead>

                        <tr>

                            <th>
                                Icon
                            </th>

                            <th>
                                Skill
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    if (mysqli_num_rows($result) > 0) {

                        while ($row = mysqli_fetch_assoc($result)) {

                            $skill_id = (int)$row['id'];

                            $skill_name = htmlspecialchars(
                                $row['skill_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                    ?>

                        <tr>


                            <!-- ICON -->

                            <td>

                                <?php if (!empty($row['icon'])) { ?>

                                    <i
                                        class="<?php echo htmlspecialchars($row['icon']); ?> skill-icon"
                                    ></i>

                                <?php } else { ?>

                                    <i
                                        class="ri-code-line skill-icon"
                                    ></i>

                                <?php } ?>

                            </td>


                            <!-- SKILL -->

                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $row['skill_name']
                                    );
                                    ?>

                                </strong>

                            </td>


                            <!-- CATEGORY -->

                            <td>

                                <span class="category-badge">

                                    <?php
                                    echo htmlspecialchars(
                                        $row['category']
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- DELETE -->

                            <td>

                                <button
                                    type="button"
                                    class="delete-btn"
                                    onclick="openDeleteModal(
                                        <?php echo $skill_id; ?>,
                                        '<?php echo $skill_name; ?>'
                                    )"
                                >

                                    <i class="ri-delete-bin-line"></i>

                                    Delete

                                </button>

                            </td>


                        </tr>

                    <?php

                        }

                    } else {

                    ?>

                        <tr>

                            <td
                                colspan="4"
                                style="
                                    text-align:center;
                                    padding:30px;
                                    color:#777;
                                "
                            >

                                No skills added yet.

                            </td>

                        </tr>

                    <?php

                    }

                    ?>


                    </tbody>

                </table>

            </div>

        </div>


    </div>

</div>


<!-- =====================================================
     CUSTOM DELETE CONFIRMATION MODAL
===================================================== -->

<div
    class="delete-modal"
    id="deleteModal"
>


    <div
        class="delete-modal-overlay"
        onclick="closeDeleteModal()"
    ></div>


    <div class="delete-modal-box">


        <button
            type="button"
            class="modal-close"
            onclick="closeDeleteModal()"
        >

            <i class="ri-close-line"></i>

        </button>


        <div class="modal-delete-icon">

            <i class="ri-delete-bin-6-line"></i>

        </div>


        <h3>
            Delete Skill?
        </h3>


        <p>

            Are you sure you want to delete

            <strong id="deleteSkillName"></strong>

            ?

        </p>


        <span class="modal-warning">

            This action cannot be undone.

        </span>


        <div class="modal-actions">


            <button
                type="button"
                class="modal-cancel"
                onclick="closeDeleteModal()"
            >

                Cancel

            </button>


            <a
                href="#"
                id="confirmDeleteBtn"
                class="modal-delete"
            >

                <i class="ri-delete-bin-line"></i>

                Yes, Delete

            </a>


        </div>

    </div>

</div>


<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script>


/* =========================
   OPEN DELETE MODAL
========================= */

function openDeleteModal(id, skillName) {

    const modal =
        document.getElementById("deleteModal");

    const skillText =
        document.getElementById("deleteSkillName");

    const deleteButton =
        document.getElementById("confirmDeleteBtn");


    skillText.textContent =
        skillName;


    deleteButton.href =
        "skills.php?delete=" + id;


    modal.classList.add("active");

    document.body.classList.add("modal-open");

}


/* =========================
   CLOSE DELETE MODAL
========================= */

function closeDeleteModal() {

    const modal =
        document.getElementById("deleteModal");


    modal.classList.remove("active");

    document.body.classList.remove("modal-open");

}


/* =========================
   ESC KEY
========================= */

document.addEventListener(
    "keydown",
    function(event) {

        if (event.key === "Escape") {

            closeDeleteModal();

        }

    }
);


/* =========================
   CLOSE NOTIFICATION
========================= */

function closeNotification() {

    const notification =
        document.getElementById(
            "skillNotification"
        );


    if (notification) {

        notification.style.transition =
            "0.4s ease";

        notification.style.opacity =
            "0";

        notification.style.transform =
            "translateY(-10px)";


        setTimeout(function() {

            if (notification) {

                notification.remove();

            }

        }, 400);

    }

}


/* =========================
   AUTO HIDE NOTIFICATION
========================= */

setTimeout(function() {

    closeNotification();

}, 3500);


</script>


</body>

</html>