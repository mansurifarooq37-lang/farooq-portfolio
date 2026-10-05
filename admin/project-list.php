<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/db.php");


/* =========================================================
   PROJECTS
========================================================= */

$query = "
    SELECT 
        p.*,
        (
            SELECT COUNT(*)
            FROM project_images pi
            WHERE pi.project_id = p.id
        ) AS gallery_count
    FROM projects p
    ORDER BY p.id DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Failed to load projects.");
}


/* =========================================================
   FLASH MESSAGES
========================================================= */

$project_success = $_SESSION['project_success'] ?? '';
$project_error   = $_SESSION['project_error'] ?? '';

unset($_SESSION['project_success']);
unset($_SESSION['project_error']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Projects | Farooq Admin</title>


    <!-- GOOGLE FONT -->

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- REMIX ICONS -->

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css"
        rel="stylesheet"
    >


    <!-- ADMIN CSS -->

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >


<style>

/* =========================================================
   PAGE
========================================================= */

.projects-page {
    position: relative;
    z-index: 2;
}


/* =========================================================
   TOPBAR
========================================================= */

.project-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
    margin-bottom: 30px;
}

.project-heading {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.project-heading h2 {
    margin: 0;
    color: #ffffff;
    font-size: 29px;
    font-weight: 700;
    letter-spacing: -0.7px;
}

.project-heading p {
    margin: 0;
    color: #858da2;
    font-size: 13px;
}


/* =========================================================
   ADD PROJECT
========================================================= */

.project-add-btn {
    position: relative;
    overflow: hidden;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;

    min-height: 47px;
    padding: 0 21px;

    border-radius: 13px;

    color: #ffffff;
    background:
        linear-gradient(
            135deg,
            #795cff,
            #5436db
        );

    border: 1px solid rgba(145,120,255,.40);

    text-decoration: none;

    font-size: 12px;
    font-weight: 700;

    box-shadow:
        0 12px 30px
        rgba(103,72,255,.22);

    transition: .3s ease;
}

.project-add-btn i {
    font-size: 18px;
}

.project-add-btn::before {
    content: "";

    position: absolute;

    top: 0;
    left: -110%;

    width: 75%;
    height: 100%;

    background:
        linear-gradient(
            90deg,
            transparent,
            rgba(255,255,255,.22),
            transparent
        );

    transform: skewX(-20deg);

    transition: .65s ease;
}

.project-add-btn:hover::before {
    left: 140%;
}

.project-add-btn:hover {
    transform: translateY(-3px);

    color: #ffffff;

    box-shadow:
        0 17px 38px
        rgba(103,72,255,.34);
}


/* =========================================================
   NOTIFICATION
========================================================= */

.project-action-message {

    display: flex;
    align-items: center;
    gap: 14px;

    width: 100%;

    margin-bottom: 24px;

    padding: 15px 17px;

    border-radius: 15px;

    animation:
        projectMessageIn
        .45s
        cubic-bezier(.2,.8,.2,1)
        forwards;
}

.project-action-success {

    background:
        linear-gradient(
            135deg,
            rgba(34,197,94,.11),
            rgba(34,197,94,.025)
        );

    border:
        1px solid
        rgba(34,197,94,.30);

    box-shadow:
        0 12px 35px
        rgba(34,197,94,.06);
}

.project-action-error {

    background:
        linear-gradient(
            135deg,
            rgba(239,68,68,.11),
            rgba(239,68,68,.025)
        );

    border:
        1px solid
        rgba(239,68,68,.30);
}

.project-action-icon {

    width: 43px;
    height: 43px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 13px;

    font-size: 22px;
}

.project-action-success
.project-action-icon {

    color: #35e477;

    background:
        rgba(34,197,94,.13);
}

.project-action-error
.project-action-icon {

    color: #ff5d72;

    background:
        rgba(239,68,68,.13);
}

.project-action-content {

    display: flex;
    flex-direction: column;
    gap: 3px;
}

.project-action-content strong {

    color: #ffffff;

    font-size: 13px;
    font-weight: 700;
}

.project-action-success
.project-action-content strong {
    color: #35e477;
}

.project-action-error
.project-action-content strong {
    color: #ff667b;
}

.project-action-content span {

    color: #858da2;

    font-size: 11px;

    line-height: 1.5;
}

@keyframes projectMessageIn {

    from {
        opacity: 0;
        transform:
            translateY(-10px)
            scale(.98);
    }

    to {
        opacity: 1;
        transform:
            translateY(0)
            scale(1);
    }
}


/* =========================================================
   PROJECT CONTAINER
========================================================= */

.projects-container {

    width: 100%;

    padding: 6px;

    border-radius: 22px;

    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,.055),
            rgba(255,255,255,.015)
        );

    border:
        1px solid
        rgba(255,255,255,.065);

    box-shadow:
        0 25px 75px
        rgba(0,0,0,.24);

    overflow: hidden;
}

.projects-container-inner {

    width: 100%;

    overflow-x: auto;

    border-radius: 17px;

    scrollbar-width: thin;

    scrollbar-color:
        rgba(124,92,255,.55)
        transparent;
}


/* =========================================================
   TABLE
========================================================= */

.project-table {

    width: 100%;

    min-width: 920px;

    border-collapse: collapse;

    background:
        rgba(7,9,17,.72);
}

.project-table th {

    padding: 17px 18px;

    text-align: left;

    color: #777f96;

    font-size: 10px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: .9px;

    background:
        rgba(255,255,255,.025);

    border-bottom:
        1px solid
        rgba(255,255,255,.07);
}

.project-table td {

    padding: 15px 18px;

    color: #d8dce8;

    font-size: 12px;

    border-bottom:
        1px solid
        rgba(255,255,255,.045);

    vertical-align: middle;
}

.project-table tbody tr {

    transition: .28s ease;
}

.project-table tbody tr:hover {

    background:
        rgba(124,92,255,.045);
}

.project-table tbody tr:hover
.project-image-box img {

    transform: scale(1.08);
}

.project-table tbody tr:last-child td {
    border-bottom: none;
}


/* =========================================================
   ID
========================================================= */

.project-id {

    width: 55px;

    color: #6f778d !important;

    font-weight: 600;
}


/* =========================================================
   IMAGE
========================================================= */

.project-image-box {

    position: relative;

    width: 78px;
    height: 54px;

    overflow: hidden;

    border-radius: 11px;

    background:
        linear-gradient(
            135deg,
            rgba(124,92,255,.12),
            rgba(34,211,238,.06)
        );

    border:
        1px solid
        rgba(255,255,255,.08);

    box-shadow:
        0 7px 20px
        rgba(0,0,0,.18);
}

.project-image-box img {

    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    transition: .4s ease;
}

.no-project-image {

    width: 100%;
    height: 100%;

    display: flex;

    align-items: center;
    justify-content: center;

    color: #737b91;

    font-size: 20px;
}


/* =========================================================
   PROJECT INFO
========================================================= */

.project-title {

    color: #ffffff;

    font-weight: 600;

    font-size: 13px;

    max-width: 270px;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}

.project-description {

    margin-top: 4px;

    color: #737b90;

    font-size: 10px;
}


/* =========================================================
   GALLERY COUNT
========================================================= */

.gallery-count {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    min-width: 72px;

    padding: 7px 10px;

    border-radius: 9px;

    color: #9f91ff;

    background:
        rgba(124,92,255,.09);

    border:
        1px solid
        rgba(124,92,255,.18);

    font-size: 10px;

    font-weight: 700;

    transition: .25s ease;
}

.gallery-count i {
    font-size: 14px;
}

.project-table tr:hover
.gallery-count {

    color: #c5bcff;

    background:
        rgba(124,92,255,.15);

    border-color:
        rgba(124,92,255,.30);
}


/* =========================================================
   STATUS
========================================================= */

.project-status {

    display: inline-flex;

    align-items: center;

    gap: 7px;

    padding: 7px 11px;

    border-radius: 999px;

    font-size: 10px;

    font-weight: 700;
}

.project-status::before {

    content: "";

    width: 6px;
    height: 6px;

    border-radius: 50%;
}

.status-completed {

    color: #4ade80;

    background:
        rgba(34,197,94,.09);

    border:
        1px solid
        rgba(34,197,94,.18);
}

.status-completed::before {

    background: #4ade80;

    box-shadow:
        0 0 8px
        rgba(74,222,128,.75);
}

.status-development {

    color: #fbbf24;

    background:
        rgba(251,191,36,.09);

    border:
        1px solid
        rgba(251,191,36,.18);
}

.status-development::before {

    background: #fbbf24;

    box-shadow:
        0 0 8px
        rgba(251,191,36,.75);
}


/* =========================================================
   ACTIONS
========================================================= */

.project-actions {

    display: flex;

    align-items: center;

    gap: 8px;
}

.project-edit-btn,
.project-delete-btn {

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 6px;

    min-height: 34px;

    padding: 0 11px;

    border-radius: 9px;

    font-size: 10px;

    font-weight: 700;

    text-decoration: none;

    transition: .25s ease;

    cursor: pointer;
}

.project-edit-btn {

    color: #aa9aff;

    background:
        rgba(124,92,255,.09);

    border:
        1px solid
        rgba(124,92,255,.18);
}

.project-edit-btn:hover {

    color: #ffffff;

    background:
        rgba(124,92,255,.20);

    border-color:
        rgba(124,92,255,.35);

    transform:
        translateY(-2px);
}

.project-delete-btn {

    color: #ff6578;

    background:
        rgba(239,68,68,.07);

    border:
        1px solid
        rgba(239,68,68,.15);
}

.project-delete-btn:hover {

    color: #ffffff;

    background:
        rgba(239,68,68,.18);

    border-color:
        rgba(239,68,68,.32);

    transform:
        translateY(-2px);
}

.project-edit-btn i,
.project-delete-btn i {

    font-size: 14px;
}


/* =========================================================
   EMPTY
========================================================= */

.project-empty {

    padding: 75px 25px;

    text-align: center;

    background:
        rgba(8,10,18,.70);
}

.project-empty-icon {

    width: 72px;
    height: 72px;

    margin: 0 auto 18px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 20px;

    color: #9d88ff;

    background:
        rgba(124,92,255,.10);

    border:
        1px solid
        rgba(124,92,255,.20);

    font-size: 32px;
}

.project-empty h3 {

    margin: 0 0 7px;

    color: #ffffff;

    font-size: 18px;
}

.project-empty p {

    margin: 0 0 22px;

    color: #777f95;

    font-size: 12px;
}


/* =========================================================
   DELETE MODAL
========================================================= */

.delete-modal-overlay {

    position: fixed;

    inset: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    padding: 20px;

    background:
        rgba(2,4,12,.82);

    backdrop-filter: blur(13px);

    -webkit-backdrop-filter: blur(13px);

    opacity: 0;

    visibility: hidden;

    pointer-events: none;

    transition:
        opacity .25s ease,
        visibility .25s ease;

    z-index: 99999;
}

.delete-modal-overlay.active {

    opacity: 1;

    visibility: visible;

    pointer-events: auto;
}

.delete-modal {

    position: relative;

    width: min(430px,100%);

    padding: 38px 32px 30px;

    border-radius: 24px;

    text-align: center;

    background:

        radial-gradient(
            circle at 50% 0%,
            rgba(124,92,255,.20),
            transparent 48%
        ),

        linear-gradient(
            145deg,
            #111522,
            #0b0d16
        );

    border:
        1px solid
        rgba(255,255,255,.09);

    box-shadow:

        0 35px 100px
        rgba(0,0,0,.70),

        0 0 55px
        rgba(108,78,255,.13);

    transform:
        translateY(25px)
        scale(.94);

    opacity: 0;

    transition:
        transform .3s cubic-bezier(.2,.8,.2,1),
        opacity .3s ease;
}

.delete-modal-overlay.active
.delete-modal {

    transform:
        translateY(0)
        scale(1);

    opacity: 1;
}


/* =========================================================
   CLOSE
========================================================= */

.delete-modal-close {

    position: absolute;

    top: 14px;
    right: 14px;

    width: 35px;
    height: 35px;

    border:
        1px solid
        rgba(255,255,255,.06);

    border-radius: 50%;

    background:
        rgba(255,255,255,.045);

    color: #858ca0;

    display: flex;

    align-items: center;
    justify-content: center;

    cursor: pointer;

    transition: .25s ease;
}

.delete-modal-close:hover {

    color: #ffffff;

    background:
        rgba(255,255,255,.10);

    transform:
        rotate(90deg);
}


/* =========================================================
   DELETE ICON
========================================================= */

.delete-modal-icon {

    width: 78px;
    height: 78px;

    margin:
        0 auto 20px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 22px;

    color: #ff536d;

    background:
        linear-gradient(
            135deg,
            rgba(255,70,100,.17),
            rgba(124,92,255,.11)
        );

    border:
        1px solid
        rgba(255,80,100,.22);

    font-size: 34px;

    box-shadow:
        0 0 35px
        rgba(255,60,90,.10);

    animation:
        deleteIconPulse
        2s
        ease-in-out
        infinite;
}

@keyframes deleteIconPulse {

    0%,100% {

        box-shadow:
            0 0 20px
            rgba(255,60,90,.08);
    }

    50% {

        box-shadow:
            0 0 38px
            rgba(255,60,90,.23);
    }
}


/* =========================================================
   MODAL TEXT
========================================================= */

.delete-modal h2 {

    margin:
        0 0 10px;

    color: #ffffff;

    font-size: 24px;

    font-weight: 700;
}

.delete-modal p {

    margin:
        0 auto 18px;

    max-width: 340px;

    color: #9299ac;

    font-size: 13px;

    line-height: 1.7;
}

.delete-modal p strong {

    color: #ffffff;

    font-weight: 600;
}


/* =========================================================
   WARNING
========================================================= */

.delete-warning {

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    margin-bottom: 25px;

    padding: 8px 13px;

    border-radius: 10px;

    color: #ffb4bd;

    background:
        rgba(255,70,90,.07);

    border:
        1px solid
        rgba(255,70,90,.13);

    font-size: 11px;
}

.delete-warning i {
    font-size: 15px;
}


/* =========================================================
   MODAL BUTTONS
========================================================= */

.delete-modal-actions {

    display: flex;

    gap: 12px;
}

.cancel-delete,
.confirm-delete {

    flex: 1;

    min-height: 46px;

    padding: 0 18px;

    border-radius: 12px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 8px;

    font-family: inherit;

    font-size: 12px;

    font-weight: 700;

    cursor: pointer;

    transition: .25s ease;
}

.cancel-delete {

    border:
        1px solid
        rgba(255,255,255,.08);

    background:
        rgba(255,255,255,.045);

    color: #c6cad5;
}

.cancel-delete:hover {

    background:
        rgba(255,255,255,.09);

    color: #ffffff;

    transform:
        translateY(-2px);
}

.confirm-delete {

    color: #ffffff;

    background:
        linear-gradient(
            135deg,
            #ff4565,
            #d72e53
        );

    border:
        1px solid
        rgba(255,90,110,.35);

    box-shadow:
        0 8px 25px
        rgba(255,50,80,.16);

    text-decoration: none;
}

.confirm-delete:hover {

    color: #ffffff;

    transform:
        translateY(-2px);

    box-shadow:
        0 13px 35px
        rgba(255,50,80,.28);
}


/* =========================================================
   BODY LOCK
========================================================= */

body.modal-open {
    overflow: hidden;
}


/* =========================================================
   LOGOUT MODAL
========================================================= */

.logout-modal-overlay {

    position: fixed;

    inset: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    padding: 20px;

    background:
        rgba(2,4,12,.82);

    backdrop-filter: blur(13px);

    -webkit-backdrop-filter: blur(13px);

    opacity: 0;

    visibility: hidden;

    pointer-events: none;

    transition:
        opacity .25s ease,
        visibility .25s ease;

    z-index: 100000;
}

.logout-modal-overlay.active {

    opacity: 1;

    visibility: visible;

    pointer-events: auto;
}

.logout-modal {

    position: relative;

    width: min(420px,calc(100% - 30px));

    padding: 38px 32px 30px;

    border-radius: 24px;

    text-align: center;

    background:

        radial-gradient(
            circle at 50% 0%,
            rgba(124,92,255,.20),
            transparent 48%
        ),

        linear-gradient(
            145deg,
            #111522,
            #090b14
        );

    border:
        1px solid
        rgba(255,255,255,.09);

    box-shadow:
        0 35px 100px
        rgba(0,0,0,.70);

    transform:
        translateY(25px)
        scale(.94);

    opacity: 0;

    transition:
        transform .3s cubic-bezier(.2,.8,.2,1),
        opacity .3s ease;
}

.logout-modal-overlay.active
.logout-modal {

    transform:
        translateY(0)
        scale(1);

    opacity: 1;
}

.logout-modal-close {

    position: absolute;

    top: 14px;
    right: 14px;

    width: 35px;
    height: 35px;

    border:
        1px solid
        rgba(255,255,255,.07);

    border-radius: 50%;

    background:
        rgba(255,255,255,.045);

    color: #858ca0;

    display: flex;

    align-items: center;
    justify-content: center;

    cursor: pointer;

    transition: .25s ease;
}

.logout-modal-close:hover {

    color: #ffffff;

    background:
        rgba(255,255,255,.10);

    transform:
        rotate(90deg);
}

.logout-modal-icon {

    width: 78px;
    height: 78px;

    margin:
        0 auto 20px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 22px;

    color: #9b83ff;

    background:
        linear-gradient(
            135deg,
            rgba(124,92,255,.18),
            rgba(34,211,238,.08)
        );

    border:
        1px solid
        rgba(124,92,255,.25);

    font-size: 34px;
}

.logout-modal h2 {

    margin:
        0 0 10px;

    color: #ffffff;

    font-size: 24px;
}

.logout-modal p {

    margin:
        0 auto 25px;

    max-width: 320px;

    color: #9299ac;

    font-size: 13px;

    line-height: 1.7;
}

.logout-modal-actions {

    display: flex;

    gap: 12px;
}

.logout-cancel,
.logout-confirm {

    flex: 1;

    min-height: 46px;

    border-radius: 12px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 8px;

    font-family: inherit;

    font-size: 12px;

    font-weight: 700;

    cursor: pointer;

    transition: .25s ease;
}

.logout-cancel {

    border:
        1px solid
        rgba(255,255,255,.08);

    background:
        rgba(255,255,255,.045);

    color: #c6cad5;
}

.logout-cancel:hover {

    background:
        rgba(255,255,255,.09);

    color: #ffffff;
}

.logout-confirm {

    color: #ffffff;

    background:
        linear-gradient(
            135deg,
            #7657ff,
            #5639db
        );

    border:
        1px solid
        rgba(130,100,255,.35);

    text-decoration: none;

    box-shadow:
        0 8px 25px
        rgba(100,70,255,.18);
}

.logout-confirm:hover {

    color: #ffffff;

    transform:
        translateY(-2px);

    box-shadow:
        0 13px 35px
        rgba(100,70,255,.32);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {

    .project-topbar {

        align-items: flex-start;

        flex-direction: column;
    }

    .project-add-btn {

        width: 100%;
    }

    .project-heading h2 {
        font-size: 24px;
    }

    .projects-container {
        border-radius: 17px;
    }
}


@media (max-width: 500px) {

    .project-heading h2 {
        font-size: 21px;
    }

    .project-heading p {
        font-size: 11px;
    }

    .delete-modal,
    .logout-modal {

        padding:
            34px 22px 25px;

        border-radius: 20px;
    }

    .delete-modal-icon,
    .logout-modal-icon {

        width: 68px;
        height: 68px;

        font-size: 29px;
    }

    .delete-modal h2,
    .logout-modal h2 {

        font-size: 22px;
    }

    .delete-modal-actions,
    .logout-modal-actions {

        flex-direction: column-reverse;
    }

    .cancel-delete,
    .confirm-delete,
    .logout-cancel,
    .logout-confirm {

        width: 100%;
    }
}


@media (prefers-reduced-motion: reduce) {

    .project-action-message,
    .delete-modal-icon {

        animation: none;
    }

    * {

        scroll-behavior: auto !important;

        transition-duration:
            .01ms !important;
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

                <span>Dashboard</span>

            </a>

        </li>


        <!-- PROJECTS -->

        <li class="active">

            <a href="project-list.php">

                <i class="ri-folder-fill"></i>

                <span>Projects</span>

            </a>

        </li>


        <!-- LOGOUT -->

        <li>

            <a
                href="javascript:void(0);"
                onclick="openLogoutModal()"
            >

                <i class="ri-logout-box-r-fill"></i>

                <span>Logout</span>

            </a>

        </li>

    </ul>

</div>



<!-- =====================================================
     MAIN
===================================================== -->

<div class="main projects-page">


    <!-- TOPBAR -->

    <div class="project-topbar">

        <div class="project-heading">

            <h2>
                Manage Projects
            </h2>

            <p>
                Create, update and manage your portfolio projects.
            </p>

        </div>


        <a
            href="project-add.php"
            class="project-add-btn"
        >

            <i class="ri-add-line"></i>

            Add Project

        </a>

    </div>



    <!-- =================================================
         SUCCESS
    ================================================= -->

    <?php if (!empty($project_success)): ?>

        <div
            class="project-action-message project-action-success"
            id="projectSuccessMessage"
        >

            <div class="project-action-icon">

                <i class="ri-checkbox-circle-fill"></i>

            </div>


            <div class="project-action-content">

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $project_success,
                        ENT_QUOTES
                    );
                    ?>
                </strong>

                <span>
                    The project action was completed successfully.
                </span>

            </div>

        </div>

    <?php endif; ?>



    <!-- =================================================
         ERROR
    ================================================= -->

    <?php if (!empty($project_error)): ?>

        <div
            class="project-action-message project-action-error"
            id="projectErrorMessage"
        >

            <div class="project-action-icon">

                <i class="ri-error-warning-fill"></i>

            </div>


            <div class="project-action-content">

                <strong>
                    Action Failed
                </strong>

                <span>
                    <?php
                    echo htmlspecialchars(
                        $project_error,
                        ENT_QUOTES
                    );
                    ?>
                </span>

            </div>

        </div>

    <?php endif; ?>



    <!-- =================================================
         PROJECT TABLE
    ================================================= -->

    <div class="projects-container">

        <div class="projects-container-inner">


            <?php if (mysqli_num_rows($result) > 0): ?>


                <table class="project-table">


                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Image
                            </th>

                            <th>
                                Project
                            </th>

                            <th>
                                Gallery
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>



                    <tbody>


                    <?php while ($row = mysqli_fetch_assoc($result)): ?>


                        <?php

                        $status = trim(
                            $row['status'] ?? ''
                        );

                        $status_class =
                            ($status === 'Completed')
                            ? 'status-completed'
                            : 'status-development';

                        $gallery_count =
                            (int)($row['gallery_count'] ?? 0);

                        ?>


                        <tr>


                            <!-- ID -->

                            <td class="project-id">

                                #
                                <?php
                                echo (int)$row['id'];
                                ?>

                            </td>



                            <!-- IMAGE -->

                            <td>

                                <div class="project-image-box">


                                    <?php if (!empty($row['image'])): ?>


                                        <img
                                            src="uploads/projects/<?php
                                                echo htmlspecialchars(
                                                    $row['image'],
                                                    ENT_QUOTES
                                                );
                                            ?>"
                                            alt="<?php
                                                echo htmlspecialchars(
                                                    $row['title']
                                                    ?? 'Project',
                                                    ENT_QUOTES
                                                );
                                            ?>"
                                        >


                                    <?php else: ?>


                                        <div class="no-project-image">

                                            <i class="ri-image-line"></i>

                                        </div>


                                    <?php endif; ?>


                                </div>

                            </td>



                            <!-- PROJECT -->

                            <td>

                                <div class="project-title">

                                    <?php

                                    echo htmlspecialchars(
                                        $row['title']
                                        ?? 'Untitled Project',
                                        ENT_QUOTES
                                    );

                                    ?>

                                </div>


                                <?php if (!empty($row['technologies'])): ?>

                                    <div class="project-description">

                                        <?php

                                        echo htmlspecialchars(
                                            $row['technologies'],
                                            ENT_QUOTES
                                        );

                                        ?>

                                    </div>

                                <?php endif; ?>


                            </td>



                            <!-- GALLERY -->

                            <td>

                                <span class="gallery-count">

                                    <i class="ri-gallery-line"></i>

                                    <?php
                                    echo $gallery_count;
                                    ?>

                                    <?php
                                    echo
                                        $gallery_count == 1
                                        ? 'Photo'
                                        : 'Photos';
                                    ?>

                                </span>

                            </td>



                            <!-- STATUS -->

                            <td>

                                <span
                                    class="project-status
                                    <?php
                                        echo $status_class;
                                    ?>"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $status
                                        ?: 'Unknown',
                                        ENT_QUOTES
                                    );

                                    ?>

                                </span>

                            </td>



                            <!-- ACTION -->

                            <td>

                                <div class="project-actions">


                                    <!-- EDIT -->

                                    <a
                                        href="project-edit.php?id=<?php
                                            echo (int)$row['id'];
                                        ?>"
                                        class="project-edit-btn"
                                    >

                                        <i class="ri-edit-line"></i>

                                        Edit

                                    </a>



                                    <!-- DELETE -->

                                    <a
                                        href="project-delete.php?id=<?php
                                            echo (int)$row['id'];
                                        ?>"
                                        class="project-delete-btn delete-project"
                                        data-id="<?php
                                            echo (int)$row['id'];
                                        ?>"
                                        data-title="<?php
                                            echo htmlspecialchars(
                                                $row['title']
                                                ?? 'this project',
                                                ENT_QUOTES
                                            );
                                        ?>"
                                    >

                                        <i class="ri-delete-bin-line"></i>

                                        Delete

                                    </a>


                                </div>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                    </tbody>

                </table>


            <?php else: ?>


                <!-- EMPTY -->

                <div class="project-empty">

                    <div class="project-empty-icon">

                        <i class="ri-folder-open-line"></i>

                    </div>


                    <h3>
                        No Projects Yet
                    </h3>


                    <p>
                        Start building your portfolio by adding your first project.
                    </p>


                    <a
                        href="project-add.php"
                        class="project-add-btn"
                    >

                        <i class="ri-add-line"></i>

                        Add Your First Project

                    </a>

                </div>


            <?php endif; ?>


        </div>

    </div>


</div>



<!-- =====================================================
     DELETE MODAL
===================================================== -->

<div
    class="delete-modal-overlay"
    id="deleteModal"
>


    <div class="delete-modal">


        <!-- CLOSE -->

        <button
            type="button"
            class="delete-modal-close"
            id="closeDeleteModal"
            aria-label="Close"
        >

            <i class="ri-close-line"></i>

        </button>



        <!-- ICON -->

        <div class="delete-modal-icon">

            <i class="ri-delete-bin-6-line"></i>

        </div>



        <!-- TITLE -->

        <h2>
            Delete Project?
        </h2>



        <!-- MESSAGE -->

        <p>

            Are you sure you want to delete

            <strong id="deleteProjectName">
                this project
            </strong>?

        </p>



        <!-- WARNING -->

        <span class="delete-warning">

            <i class="ri-error-warning-line"></i>

            This action cannot be undone.

        </span>



        <!-- BUTTONS -->

        <div class="delete-modal-actions">


            <button
                type="button"
                class="cancel-delete"
                id="cancelDelete"
            >

                <i class="ri-close-line"></i>

                Cancel

            </button>


            <a
                href="#"
                class="confirm-delete"
                id="confirmDelete"
            >

                <i class="ri-delete-bin-line"></i>

                Delete Project

            </a>


        </div>


    </div>

</div>



<!-- =====================================================
     LOGOUT MODAL
===================================================== -->

<div
    class="logout-modal-overlay"
    id="logoutModal"
>


    <div class="logout-modal">


        <button
            type="button"
            class="logout-modal-close"
            onclick="closeLogoutModal()"
        >

            <i class="ri-close-line"></i>

        </button>


        <div class="logout-modal-icon">

            <i class="ri-logout-box-r-line"></i>

        </div>


        <h2>
            Logout?
        </h2>


        <p>
            Are you sure you want to leave the admin panel?
        </p>


        <div class="logout-modal-actions">


            <button
                type="button"
                class="logout-cancel"
                onclick="closeLogoutModal()"
            >

                Cancel

            </button>


            <a
                href="logout.php"
                class="logout-confirm"
            >

                <i class="ri-logout-box-r-line"></i>

                Yes, Logout

            </a>


        </div>

    </div>

</div>



<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script>


/* =========================================================
   DELETE MODAL
========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    function () {


        const modal =
            document.getElementById(
                "deleteModal"
            );


        const closeBtn =
            document.getElementById(
                "closeDeleteModal"
            );


        const cancelBtn =
            document.getElementById(
                "cancelDelete"
            );


        const confirmBtn =
            document.getElementById(
                "confirmDelete"
            );


        const projectName =
            document.getElementById(
                "deleteProjectName"
            );


        const deleteButtons =
            document.querySelectorAll(
                ".delete-project"
            );



        /* =========================================
           OPEN DELETE MODAL
        ========================================= */

        deleteButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function (e) {

                        e.preventDefault();


                        const id =
                            this.dataset.id;


                        const title =
                            this.dataset.title
                            || "this project";


                        projectName.textContent =
                            '"' + title + '"';


                        confirmBtn.href =
                        "project-delete.php?id=" + id;


                        modal.classList.add(
                            "active"
                        );


                        document.body.classList.add(
                            "modal-open"
                        );

                    }
                );

            }
        );



        /* =========================================
           CLOSE
        ========================================= */

        function closeDeleteModal() {

            modal.classList.remove(
                "active"
            );

            document.body.classList.remove(
                "modal-open"
            );

        }


        closeBtn.addEventListener(
            "click",
            closeDeleteModal
        );


        cancelBtn.addEventListener(
            "click",
            closeDeleteModal
        );



        /* =========================================
           CLICK OUTSIDE
        ========================================= */

        modal.addEventListener(
            "click",
            function (e) {

                if (e.target === modal) {

                    closeDeleteModal();

                }

            }
        );



        /* =========================================
           ESC
        ========================================= */

        document.addEventListener(
            "keydown",
            function (e) {

                if (
                    e.key === "Escape"
                    &&
                    modal.classList.contains(
                        "active"
                    )
                ) {

                    closeDeleteModal();

                }

            }
        );



        /* =========================================
           AUTO HIDE NOTIFICATIONS
        ========================================= */

        const messages =
            document.querySelectorAll(
                ".project-action-message"
            );


        messages.forEach(
            function (message) {

                setTimeout(
                    function () {

                        message.style.transition =
                            "opacity .4s ease, transform .4s ease";

                        message.style.opacity =
                            "0";

                        message.style.transform =
                            "translateY(-8px)";


                        setTimeout(
                            function () {

                                message.remove();

                            },
                            400
                        );

                    },
                    4000
                );

            }
        );

    }
);



/* =========================================================
   LOGOUT MODAL
========================================================= */

function openLogoutModal() {

    const modal =
        document.getElementById(
            "logoutModal"
        );


    if (!modal) {
        return;
    }


    modal.classList.add(
        "active"
    );


    document.body.classList.add(
        "modal-open"
    );

}


function closeLogoutModal() {

    const modal =
        document.getElementById(
            "logoutModal"
        );


    if (!modal) {
        return;
    }


    modal.classList.remove(
        "active"
    );


    document.body.classList.remove(
        "modal-open"
    );

}



/* =========================================================
   LOGOUT ESC
========================================================= */

document.addEventListener(
    "keydown",
    function (e) {

        if (e.key === "Escape") {

            closeLogoutModal();

        }

    }
);



/* =========================================================
   LOGOUT OUTSIDE CLICK
========================================================= */

document.addEventListener(
    "click",
    function (e) {

        const modal =
            document.getElementById(
                "logoutModal"
            );


        if (
            modal
            &&
            e.target === modal
        ) {

            closeLogoutModal();

        }

    }
);

</script>


</body>

</html>