<?php

session_start();

include("../config/db.php");

/* =========================================================
   LOGIN CHECK
========================================================= */

if (!isset($_SESSION['admin'])) {

    header("Location: login.php");
    exit();

}


/* =========================================================
   CURRENT ADMIN
========================================================= */

$admin_username = $_SESSION['admin'];


/* =========================================================
   GET ADMIN DATA
========================================================= */

$query = "SELECT * FROM admin WHERE username = '$admin_username'";

$result = mysqli_query($conn, $query);

$admin = mysqli_fetch_assoc($result);


if (!$admin) {

    die("Admin not found.");

}


$message = "";
$error = "";


/* =========================================================
   UPDATE PROFILE
========================================================= */

if (isset($_POST['update_profile'])) {


    /* ================= USERNAME ================= */

    $username = mysqli_real_escape_string(
        $conn,
        trim($_POST['username'])
    );


    /* ================= PASSWORD ================= */

    $current_password = $_POST['current_password'] ?? "";

    $new_password = $_POST['new_password'] ?? "";

    $confirm_password = $_POST['confirm_password'] ?? "";


    /* ================= PASSWORD CHECK ================= */

    if (!empty($new_password)) {


        if (
            empty($current_password) ||
            !password_verify(
                $current_password,
                $admin['password']
            )
        ) {

            $error = "Current password is incorrect.";

        }

        elseif ($new_password !== $confirm_password) {

            $error = "New passwords do not match.";

        }

        else {

            $hashed_password = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );

        }

    }


    /* =====================================================
       PHOTO UPLOAD
    ====================================================== */

    $photo_name = $admin['profile_photo'] ?? "";


    if (
        empty($error) &&
        isset($_FILES['profile_photo']) &&
        $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK
    ) {


        $allowed_types = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];


        $file_name = $_FILES['profile_photo']['name'];

        $file_tmp = $_FILES['profile_photo']['tmp_name'];


        $extension = strtolower(
            pathinfo(
                $file_name,
                PATHINFO_EXTENSION
            )
        );


        /* ================= FILE TYPE ================= */

        if (!in_array($extension, $allowed_types)) {

            $error =
                "Only JPG, JPEG, PNG and WEBP images are allowed.";

        }

        else {


            /* ================= UPLOAD DIRECTORY ================= */

            $upload_dir =
                __DIR__ . "/uploads/admin/";


            if (!is_dir($upload_dir)) {

                mkdir(
                    $upload_dir,
                    0777,
                    true
                );

            }


            /* ================= NEW FILE NAME ================= */

            $new_file_name =
                "admin_" .
                time() .
                "_" .
                rand(1000, 9999) .
                "." .
                $extension;


            /* ================= SERVER PATH ================= */

            $server_path =
                $upload_dir .
                $new_file_name;


            /* ================= MOVE FILE ================= */

            if (
                move_uploaded_file(
                    $file_tmp,
                    $server_path
                )
            ) {


                /* ================= DELETE OLD PHOTO ================= */

                if (!empty($admin['profile_photo'])) {

                    $old_photo =
                        dirname(__DIR__) .
                        "/" .
                        $admin['profile_photo'];


                    if (file_exists($old_photo)) {

                        unlink($old_photo);

                    }

                }


                /* ================= DATABASE PATH ================= */

                $photo_name =
                    "uploads/admin/" .
                    $new_file_name;

            }

            else {

                $error =
                    "Photo could not be uploaded.";

            }

        }

    }


    /* =====================================================
       DATABASE UPDATE
    ====================================================== */

    if (empty($error)) {


        if (!empty($new_password)) {

            $update_query = "
                UPDATE admin SET
                    username = '$username',
                    password = '$hashed_password',
                    profile_photo = '$photo_name'
                WHERE username = '$admin_username'
            ";

        }

        else {

            $update_query = "
                UPDATE admin SET
                    username = '$username',
                    profile_photo = '$photo_name'
                WHERE username = '$admin_username'
            ";

        }


        /* ================= RUN UPDATE ================= */

        if (mysqli_query($conn, $update_query)) {


            $_SESSION['admin'] = $username;


            $message =
                "Profile updated successfully!";


            /* ================= REFRESH DATA ================= */

            $admin_username =
                $_SESSION['admin'];


            $result = mysqli_query(
                $conn,
                "SELECT * FROM admin
                 WHERE username = '$admin_username'"
            );


            $admin =
                mysqli_fetch_assoc($result);

        }

        else {

            $error =
                "Something went wrong: " .
                mysqli_error($conn);

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Admin Profile | Farooq.</title>


<!-- Remix Icons -->

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css"
>


<style>

/* =========================================================
   RESET
========================================================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}


/* =========================================================
   ROOT
========================================================= */

:root{

    --bg:#050611;

    --panel:#10111b;

    --panel-2:#141521;

    --border:rgba(255,255,255,.08);

    --purple:#6c63ff;

    --purple-light:#8c84ff;

    --cyan:#00d9ff;

    --text:#f5f6ff;

    --muted:#9297ad;

    --danger:#ff5577;

    --success:#27e6a4;

}


/* =========================================================
   BODY
========================================================= */

body{

    min-height:100vh;

    font-family:
        "Segoe UI",
        Arial,
        sans-serif;

    color:var(--text);

    background:

        radial-gradient(
            circle at 75% 20%,
            rgba(108,99,255,.14),
            transparent 28%
        ),

        radial-gradient(
            circle at 20% 80%,
            rgba(0,217,255,.08),
            transparent 25%
        ),

        var(--bg);

    overflow-x:hidden;

}


/* =========================================================
   BACKGROUND GRID
========================================================= */

body::before{

    content:"";

    position:fixed;

    inset:0;

    pointer-events:none;

    opacity:.35;

    background-image:

        linear-gradient(
            rgba(108,99,255,.035) 1px,
            transparent 1px
        ),

        linear-gradient(
            90deg,
            rgba(108,99,255,.035) 1px,
            transparent 1px
        );

    background-size:45px 45px;

    z-index:0;

}


/* =========================================================
   GLOW ORBS
========================================================= */

.bg-orb{

    position:fixed;

    border-radius:50%;

    pointer-events:none;

    filter:blur(2px);

    z-index:0;

}


.orb-one{

    width:280px;

    height:280px;

    right:8%;

    top:18%;

    background:
        radial-gradient(
            circle,
            rgba(108,99,255,.16),
            transparent 70%
        );

    animation:
        orbFloat 8s ease-in-out infinite;

}


.orb-two{

    width:220px;

    height:220px;

    left:8%;

    bottom:10%;

    background:
        radial-gradient(
            circle,
            rgba(0,217,255,.10),
            transparent 70%
        );

    animation:
        orbFloat 6s ease-in-out infinite reverse;

}


@keyframes orbFloat{

    0%,
    100%{
        transform:translateY(0);
    }

    50%{
        transform:translateY(-25px);
    }

}


/* =========================================================
   MAIN WRAPPER
========================================================= */

.profile-page{

    position:relative;

    z-index:2;

    min-height:100vh;

    padding:45px 50px 70px;

}


/* =========================================================
   TOP BAR
========================================================= */

.profile-topbar{

    max-width:1180px;

    margin:0 auto 30px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:20px;

}


.page-heading{

    display:flex;

    align-items:center;

    gap:16px;

}


.page-heading-icon{

    width:52px;

    height:52px;

    display:flex;

    align-items:center;

    justify-content:center;

    border-radius:15px;

    color:white;

    font-size:24px;

    background:
        linear-gradient(
            135deg,
            var(--purple),
            var(--cyan)
        );

    box-shadow:
        0 10px 35px rgba(108,99,255,.25);

}


.page-heading h1{

    font-size:28px;

    font-weight:800;

    letter-spacing:-.5px;

}


.page-heading p{

    margin-top:4px;

    color:var(--muted);

    font-size:14px;

}


/* =========================================================
   BACK BUTTON
========================================================= */

.back-btn{

    display:flex;

    align-items:center;

    gap:8px;

    padding:12px 17px;

    color:#dfe2f2;

    text-decoration:none;

    border:1px solid rgba(108,99,255,.30);

    border-radius:12px;

    background:rgba(108,99,255,.07);

    transition:.3s ease;

}


.back-btn:hover{

    color:white;

    border-color:var(--purple);

    background:rgba(108,99,255,.15);

    transform:translateY(-2px);

    box-shadow:
        0 10px 30px rgba(108,99,255,.15);

}


/* =========================================================
   MAIN GRID
========================================================= */

.profile-grid{

    max-width:1180px;

    margin:auto;

    display:grid;

    grid-template-columns:
        370px
        1fr;

    gap:25px;

    align-items:start;

}


/* =========================================================
   COMMON CARD
========================================================= */

.profile-card{

    position:relative;

    background:
        linear-gradient(
            145deg,
            rgba(19,20,31,.96),
            rgba(10,11,20,.96)
        );

    border:1px solid var(--border);

    border-radius:22px;

    box-shadow:
        0 25px 70px rgba(0,0,0,.30);

    overflow:hidden;

}


/* Top glow */

.profile-card::before{

    content:"";

    position:absolute;

    left:0;

    right:0;

    top:0;

    height:1px;

    background:
        linear-gradient(
            90deg,
            transparent,
            var(--purple),
            var(--cyan),
            transparent
        );

    opacity:.8;

}


/* =========================================================
   PROFILE PREVIEW
========================================================= */

.profile-preview{

    padding:35px 28px;

    text-align:center;

}


/* =========================================================
   PHOTO
========================================================= */

.photo-wrapper{

    position:relative;

    width:145px;

    height:145px;

    margin:0 auto 22px;

}


.photo-ring{

    position:absolute;

    inset:-7px;

    border-radius:50%;

    background:
        conic-gradient(
            from 0deg,
            var(--purple),
            var(--cyan),
            var(--purple),
            transparent,
            var(--purple)
        );

    animation:
        photoRotate 5s linear infinite;

}


.photo-ring::after{

    content:"";

    position:absolute;

    inset:3px;

    border-radius:50%;

    background:var(--panel);

}


@keyframes photoRotate{

    to{
        transform:rotate(360deg);
    }

}


.profile-photo{

    position:relative;

    z-index:2;

    width:145px;

    height:145px;

    border-radius:50%;

    object-fit:cover;

    border:5px solid var(--panel);

    box-shadow:
        0 15px 45px rgba(0,0,0,.45);

}


.photo-placeholder{

    position:relative;

    z-index:2;

    width:145px;

    height:145px;

    border-radius:50%;

    display:flex;

    align-items:center;

    justify-content:center;

    color:#8e91ff;

    font-size:52px;

    background:
        linear-gradient(
            145deg,
            #181a2a,
            #0c0d16
        );

    border:5px solid var(--panel);

}


/* =========================================================
   PROFILE INFO
========================================================= */

.profile-preview h2{

    font-size:23px;

    margin-bottom:7px;

}


.profile-role{

    color:#9e9aff;

    font-size:14px;

    font-weight:600;

}


.profile-status{

    display:inline-flex;

    align-items:center;

    gap:7px;

    margin-top:18px;

    padding:7px 12px;

    border-radius:30px;

    color:#7ff0c5;

    background:rgba(39,230,164,.07);

    border:1px solid rgba(39,230,164,.18);

    font-size:12px;

}


.status-dot{

    width:7px;

    height:7px;

    border-radius:50%;

    background:var(--success);

    box-shadow:
        0 0 10px var(--success);

    animation:
        statusPulse 1.7s infinite;

}


@keyframes statusPulse{

    50%{
        opacity:.4;
        transform:scale(.7);
    }

}


/* =========================================================
   PROFILE INFO LIST
========================================================= */

.profile-info{

    margin-top:28px;

    padding-top:24px;

    border-top:1px solid var(--border);

    text-align:left;

}


.info-row{

    display:flex;

    align-items:center;

    gap:13px;

    padding:12px 0;

    color:#c8cada;

    font-size:13px;

}


.info-row i{

    width:35px;

    height:35px;

    display:flex;

    align-items:center;

    justify-content:center;

    flex-shrink:0;

    border-radius:10px;

    color:#8d87ff;

    background:rgba(108,99,255,.09);

}


.info-row span{

    word-break:break-word;

}


/* =========================================================
   SECURITY CARD
========================================================= */

.security-card{

    margin-top:25px;

    padding:22px 25px;

}


.card-title{

    display:flex;

    align-items:center;

    gap:12px;

    margin-bottom:25px;

}


.card-title-icon{

    width:40px;

    height:40px;

    display:flex;

    align-items:center;

    justify-content:center;

    border-radius:11px;

    color:white;

    font-size:20px;

    background:
        linear-gradient(
            135deg,
            var(--purple),
            #8b5cf6
        );

}


.card-title h2{

    font-size:19px;

}


.card-title p{

    margin-top:3px;

    color:var(--muted);

    font-size:12px;

}


/* =========================================================
   FORM CARD
========================================================= */

.form-card{

    padding:30px;

}


.form-section{

    padding-bottom:27px;

    margin-bottom:27px;

    border-bottom:1px solid var(--border);

}


.form-section:last-of-type{

    border-bottom:none;

    margin-bottom:0;

    padding-bottom:0;

}


.form-section-heading{

    display:flex;

    align-items:center;

    gap:10px;

    margin-bottom:20px;

}


.form-section-heading i{

    color:#8b84ff;

    font-size:20px;

}


.form-section-heading h2{

    font-size:17px;

}


/* =========================================================
   FORM GRID
========================================================= */

.form-grid{

    display:grid;

    grid-template-columns:
        1fr
        1fr;

    gap:18px;

}


.form-group{

    margin-bottom:18px;

}


.form-grid .form-group{

    margin-bottom:0;

}


.form-group label{

    display:flex;

    align-items:center;

    gap:7px;

    margin-bottom:8px;

    color:#d8dbea;

    font-size:13px;

    font-weight:600;

}


.form-group label i{

    color:#7770ff;

    font-size:15px;

}


.form-group input{

    width:100%;

    height:48px;

    padding:0 15px;

    border-radius:12px;

    border:1px solid rgba(255,255,255,.09);

    outline:none;

    background:#0b0c14;

    color:#f5f6ff;

    font-size:14px;

    transition:.3s ease;

}


.form-group input::placeholder{

    color:#626679;

}


.form-group input:focus{

    border-color:
        rgba(108,99,255,.65);

    box-shadow:
        0 0 0 3px rgba(108,99,255,.10),
        0 0 25px rgba(108,99,255,.06);

    background:#0e0f19;

}


/* =========================================================
   FILE INPUT
========================================================= */

.file-input{

    position:relative;

}


.file-input input[type="file"]{

    padding:10px;

    cursor:pointer;

}


.file-input input[type="file"]::file-selector-button{

    border:none;

    padding:8px 12px;

    margin-right:10px;

    border-radius:8px;

    color:white;

    background:
        linear-gradient(
            135deg,
            var(--purple),
            #7c73ff
        );

    cursor:pointer;

}


/* =========================================================
   PASSWORD INPUT
========================================================= */

.password-wrapper{

    position:relative;

}


.password-wrapper input{

    padding-right:48px;

}


.password-toggle{

    position:absolute;

    right:12px;

    top:50%;

    transform:translateY(-50%);

    border:none;

    background:none;

    color:#777b91;

    font-size:19px;

    cursor:pointer;

}


.password-toggle:hover{

    color:#9c96ff;

}


/* =========================================================
   SECURITY NOTE
========================================================= */

.security-note{

    display:flex;

    gap:10px;

    padding:13px 15px;

    margin-top:3px;

    border-radius:12px;

    color:#aeb2c7;

    background:rgba(108,99,255,.055);

    border:1px solid rgba(108,99,255,.12);

    font-size:12px;

    line-height:1.6;

}


.security-note i{

    color:#7d75ff;

    font-size:18px;

    flex-shrink:0;

}


/* =========================================================
   SUCCESS / ERROR
========================================================= */

.alert{

    display:flex;

    align-items:center;

    gap:10px;

    max-width:1180px;

    margin:0 auto 20px;

    padding:14px 17px;

    border-radius:12px;

    font-size:13px;

}


.alert-success{

    color:#76f1c3;

    background:rgba(39,230,164,.07);

    border:1px solid rgba(39,230,164,.18);

}


.alert-error{

    color:#ff7f99;

    background:rgba(255,85,119,.07);

    border:1px solid rgba(255,85,119,.18);

}


/* =========================================================
   SAVE BUTTON
========================================================= */

.save-button{

    position:relative;

    width:100%;

    height:52px;

    border:none;

    border-radius:13px;

    color:white;

    font-size:14px;

    font-weight:700;

    cursor:pointer;

    overflow:hidden;

    background:
        linear-gradient(
            135deg,
            #6258ff,
            #7b61ff,
            #00bfe8
        );

    box-shadow:
        0 12px 30px rgba(108,99,255,.22);

    transition:.3s ease;

}


.save-button::before{

    content:"";

    position:absolute;

    top:0;

    left:-100%;

    width:70%;

    height:100%;

    background:
        linear-gradient(
            90deg,
            transparent,
            rgba(255,255,255,.25),
            transparent
        );

    transform:skewX(-20deg);

    animation:
        buttonShine 3.5s infinite;

}


@keyframes buttonShine{

    0%{
        left:-100%;
    }

    45%,
    100%{
        left:140%;
    }

}


.save-button:hover{

    transform:translateY(-2px);

    box-shadow:
        0 18px 40px rgba(108,99,255,.30);

}


.save-button:active{

    transform:translateY(0);

}


.save-button i{

    margin-right:7px;

    font-size:17px;

}


/* =========================================================
   BOTTOM SECURITY STRIP
========================================================= */

.security-strip{

    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:15px;

    margin-top:20px;

    padding:14px 17px;

    border-radius:13px;

    background:rgba(255,255,255,.025);

    border:1px solid rgba(255,255,255,.06);

}


.security-strip span{

    display:flex;

    align-items:center;

    gap:8px;

    color:#85899f;

    font-size:11px;

}


.security-strip i{

    color:#5fe0ba;

    font-size:15px;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width:950px){

    .profile-grid{

        grid-template-columns:1fr;

    }

}


@media(max-width:700px){

    .profile-page{

        padding:25px 15px 50px;

    }

    .profile-topbar{

        align-items:flex-start;

        flex-direction:column;

    }

    .page-heading h1{

        font-size:23px;

    }

    .profile-grid{

        grid-template-columns:1fr;

    }

    .form-card{

        padding:22px 18px;

    }

    .form-grid{

        grid-template-columns:1fr;

        gap:0;

    }

}


@media(max-width:480px){

    .page-heading{

        align-items:flex-start;

    }

    .page-heading-icon{

        width:45px;

        height:45px;

        font-size:21px;

    }

    .profile-preview{

        padding:28px 20px;

    }

}


/* =========================================================
   REDUCED MOTION
========================================================= */

@media(prefers-reduced-motion:reduce){

    *,
    *::before,
    *::after{

        animation:none !important;

        transition:none !important;

    }

}

</style>

</head>


<body>


<!-- Background -->

<div class="bg-orb orb-one"></div>

<div class="bg-orb orb-two"></div>



<div class="profile-page">


    <!-- =====================================================
         TOP BAR
    ====================================================== -->

    <div class="profile-topbar">


        <div class="page-heading">

            <div class="page-heading-icon">

                <i class="ri-user-settings-line"></i>

            </div>


            <div>

                <h1>
                    Admin Profile
                </h1>

                <p>
                    Manage your account, profile photo and security.
                </p>

            </div>

        </div>


        <a
            href="dashboard.php"
            class="back-btn"
        >

            <i class="ri-arrow-left-line"></i>

            Back to Dashboard

        </a>

    </div>



    <!-- =====================================================
         ALERTS
    ====================================================== -->

    <?php if (!empty($message)): ?>

        <div class="alert alert-success">

            <i class="ri-checkbox-circle-line"></i>

            <span>
                <?php
                echo htmlspecialchars($message);
                ?>
            </span>

        </div>

    <?php endif; ?>


    <?php if (!empty($error)): ?>

        <div class="alert alert-error">

            <i class="ri-error-warning-line"></i>

            <span>
                <?php
                echo htmlspecialchars($error);
                ?>
            </span>

        </div>

    <?php endif; ?>



    <!-- =====================================================
         MAIN GRID
    ====================================================== -->

    <div class="profile-grid">


        <!-- =================================================
             LEFT - PROFILE PREVIEW
        ================================================== -->

        <div>


            <div class="profile-card profile-preview">


                <!-- PHOTO -->

                <div class="photo-wrapper">

                    <div class="photo-ring"></div>


                    <?php if (!empty($admin['profile_photo'])): ?>

                        <img
                            src="<?php
                                echo htmlspecialchars(
                                    $admin['profile_photo']
                                );
                            ?>"
                            alt="Admin Profile"
                            class="profile-photo"
                        >

                    <?php else: ?>

                        <div class="photo-placeholder">

                            <i class="ri-user-line"></i>

                        </div>

                    <?php endif; ?>

                </div>



                <!-- NAME -->

                <h2>

                    <?php
                    echo htmlspecialchars(
                        $admin['username']
                    );
                    ?>

                </h2>


                <div class="profile-role">

                    Administrator

                </div>


                <div class="profile-status">

                    <span class="status-dot"></span>

                    Account Active

                </div>



                <!-- INFO -->

                <div class="profile-info">


                    <div class="info-row">

                        <i class="ri-shield-user-line"></i>

                        <span>
                            Admin Account
                        </span>

                    </div>


                    <div class="info-row">

                        <i class="ri-lock-password-line"></i>

                        <span>
                            Password Protected
                        </span>

                    </div>


                    <div class="info-row">

                        <i class="ri-image-line"></i>

                        <span>
                            Profile Photo Enabled
                        </span>

                    </div>


                    <div class="info-row">

                        <i class="ri-dashboard-line"></i>

                        <span>
                            Full Dashboard Access
                        </span>

                    </div>


                </div>


            </div>



            <!-- SECURITY CARD -->

            <div class="profile-card security-card">


                <div class="card-title">

                    <div class="card-title-icon">

                        <i class="ri-shield-check-line"></i>

                    </div>


                    <div>

                        <h2>
                            Security
                        </h2>

                        <p>
                            Keep your admin account protected.
                        </p>

                    </div>

                </div>


                <div class="security-note">

                    <i class="ri-information-line"></i>

                    <span>
                        Use a strong password and avoid sharing
                        your admin credentials with anyone.
                    </span>

                </div>

            </div>


        </div>



        <!-- =================================================
             RIGHT - EDIT FORM
        ================================================== -->

        <div class="profile-card form-card">


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- =========================================
                     ACCOUNT
                ========================================== -->

                <div class="form-section">


                    <div class="form-section-heading">

                        <i class="ri-user-settings-line"></i>

                        <h2>
                            Account Information
                        </h2>

                    </div>


                    <div class="form-group">

                        <label>

                            <i class="ri-user-line"></i>

                            Admin Username

                        </label>


                        <input
                            type="text"
                            name="username"
                            value="<?php
                                echo htmlspecialchars(
                                    $admin['username']
                                );
                            ?>"
                            required
                        >

                    </div>


                    <div class="form-group file-input">

                        <label>

                            <i class="ri-image-line"></i>

                            Change Profile Photo

                        </label>


                        <input
                            type="file"
                            name="profile_photo"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                    </div>


                </div>



                <!-- =========================================
                     PASSWORD
                ========================================== -->

                <div class="form-section">


                    <div class="form-section-heading">

                        <i class="ri-lock-password-line"></i>

                        <h2>
                            Change Password
                        </h2>

                    </div>



                    <!-- CURRENT -->

                    <div class="form-group">

                        <label>

                            <i class="ri-key-2-line"></i>

                            Current Password

                        </label>


                        <div class="password-wrapper">

                            <input
                                type="password"
                                name="current_password"
                                id="currentPassword"
                                placeholder="Enter current password"
                            >


                            <button
                                type="button"
                                class="password-toggle"
                                onclick="togglePassword('currentPassword', this)"
                            >

                                <i class="ri-eye-line"></i>

                            </button>

                        </div>

                    </div>



                    <!-- NEW + CONFIRM -->

                    <div class="form-grid">


                        <div class="form-group">

                            <label>

                                <i class="ri-lock-line"></i>

                                New Password

                            </label>


                            <div class="password-wrapper">

                                <input
                                    type="password"
                                    name="new_password"
                                    id="newPassword"
                                    placeholder="New password"
                                >


                                <button
                                    type="button"
                                    class="password-toggle"
                                    onclick="togglePassword('newPassword', this)"
                                >

                                    <i class="ri-eye-line"></i>

                                </button>

                            </div>

                        </div>



                        <div class="form-group">

                            <label>

                                <i class="ri-lock-2-line"></i>

                                Confirm Password

                            </label>


                            <div class="password-wrapper">

                                <input
                                    type="password"
                                    name="confirm_password"
                                    id="confirmPassword"
                                    placeholder="Confirm password"
                                >


                                <button
                                    type="button"
                                    class="password-toggle"
                                    onclick="togglePassword('confirmPassword', this)"
                                >

                                    <i class="ri-eye-line"></i>

                                </button>

                            </div>

                        </div>


                    </div>


                    <div class="security-note">

                        <i class="ri-shield-keyhole-line"></i>

                        <span>
                            Leave the password fields empty if you
                            don't want to change your password.
                        </span>

                    </div>


                </div>



                <!-- =========================================
                     SAVE
                ========================================== -->

                <button
                    type="submit"
                    name="update_profile"
                    class="save-button"
                >

                    <i class="ri-save-3-line"></i>

                    Save Profile Changes

                </button>


                <div class="security-strip">

                    <span>

                        <i class="ri-shield-check-line"></i>

                        Secure Admin Panel

                    </span>


                    <span>

                        <i class="ri-lock-line"></i>

                        Protected

                    </span>

                </div>


            </form>


        </div>


    </div>


</div>



<script>

/* =========================================================
   PASSWORD SHOW / HIDE
========================================================= */

function togglePassword(inputId, button){

    const input =
        document.getElementById(inputId);

    const icon =
        button.querySelector("i");


    if(input.type === "password"){

        input.type = "text";

        icon.className =
            "ri-eye-off-line";

    }

    else{

        input.type = "password";

        icon.className =
            "ri-eye-line";

    }

}

</script>


</body>

</html>