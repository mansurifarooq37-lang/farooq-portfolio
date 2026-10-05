<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/db.php");

$settings_success = $_SESSION['settings_success'] ?? '';
$settings_error   = $_SESSION['settings_error'] ?? '';

unset($_SESSION['settings_success']);
unset($_SESSION['settings_error']);


/* =========================
   UPDATE SETTINGS
========================= */

if (isset($_POST['update'])) {

    $email = trim($_POST['email'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $location = trim($_POST['location'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $_SESSION['settings_error'] = "Please enter a valid email address.";
        header("Location: settings.php");
        exit();

    }

    if (empty($instagram) || empty($location)) {

        $_SESSION['settings_error'] = "Please fill in all settings fields.";
        header("Location: settings.php");
        exit();

    }

    $email = mysqli_real_escape_string($conn, $email);
    $instagram = mysqli_real_escape_string($conn, $instagram);
    $location = mysqli_real_escape_string($conn, $location);

    $update_query = "
        UPDATE settings
        SET
            email = '$email',
            instagram = '$instagram',
            location = '$location'
        WHERE id = 1
    ";

    if (mysqli_query($conn, $update_query)) {

        $_SESSION['settings_success'] =
            "Settings updated successfully!";

        header("Location: settings.php");
        exit();

    } else {

        $_SESSION['settings_error'] =
            "Failed to update settings.";

        header("Location: settings.php");
        exit();
    }
}


/* =========================
   FETCH SETTINGS
========================= */

$result = mysqli_query(
    $conn,
    "SELECT * FROM settings LIMIT 1"
);

$settings = mysqli_fetch_assoc($result);

if (!$settings) {
    die("Settings data not found.");
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

    <title>Settings | Farooq Admin</title>

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet"
    >


    <style>

        /* =========================================
           PAGE
        ========================================= */

        .settings-page {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 5px 40px;
        }


        /* =========================================
           HERO
        ========================================= */

        .settings-hero {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 25px;

            padding: 30px;

            margin-bottom: 22px;

            border-radius: 22px;

            overflow: hidden;

            background:
                linear-gradient(
                    135deg,
                    rgba(124,58,237,.18),
                    rgba(6,182,212,.08)
                );

            border: 1px solid rgba(139,92,246,.22);

            box-shadow:
                0 18px 50px rgba(0,0,0,.20);
        }

        .settings-hero::before {
            content: "";

            position: absolute;

            width: 220px;
            height: 220px;

            right: -90px;
            top: -110px;

            border-radius: 50%;

            border: 1px solid rgba(139,92,246,.22);

            box-shadow:
                0 0 35px rgba(124,58,237,.10);
        }

        .settings-hero::after {
            content: "";

            position: absolute;

            width: 110px;
            height: 110px;

            right: 70px;
            bottom: -65px;

            border-radius: 50%;

            border: 1px solid rgba(6,182,212,.20);
        }

        .settings-hero-content {
            position: relative;
            z-index: 2;
        }

        .settings-hero-content h1 {
            margin: 0 0 7px;

            color: #fff;

            font-size: 29px;
            font-weight: 800;

            letter-spacing: -.6px;
        }

        .settings-hero-content p {
            margin: 0;

            max-width: 650px;

            color: rgba(255,255,255,.60);

            font-size: 13px;
            line-height: 1.6;
        }

        .settings-hero-icon {
            position: relative;
            z-index: 2;

            width: 70px;
            height: 70px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 19px;

            color: #fff;

            font-size: 31px;

            background:
                linear-gradient(
                    135deg,
                    #7c3aed,
                    #06b6d4
                );

            box-shadow:
                0 12px 35px rgba(124,58,237,.30);

            animation:
                settingsFloat 3s ease-in-out infinite;
        }

        @keyframes settingsFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }


        /* =========================================
           HEADER ACTIONS
        ========================================= */

        .settings-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;

            margin-bottom: 20px;
        }

        .settings-header-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            padding: 11px 16px;

            border-radius: 11px;

            text-decoration: none;

            color: #fff;

            font-size: 12px;
            font-weight: 700;

            transition: .3s ease;
        }

        .settings-dashboard-btn {
            background: rgba(255,255,255,.045);

            border: 1px solid rgba(255,255,255,.09);
        }

        .settings-dashboard-btn:hover {
            transform: translateY(-2px);

            background: rgba(124,58,237,.10);

            border-color:
                rgba(139,92,246,.40);
        }

        .settings-portfolio-btn {
            background:
                linear-gradient(
                    135deg,
                    #7c3aed,
                    #06b6d4
                );

            box-shadow:
                0 8px 25px rgba(124,58,237,.20);
        }

        .settings-portfolio-btn:hover {
            transform: translateY(-2px);

            box-shadow:
                0 12px 30px rgba(124,58,237,.35);
        }


        /* =========================================
           NOTIFICATIONS
        ========================================= */

        .settings-notification {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 14px 18px;

            margin-bottom: 20px;

            border-radius: 13px;

            font-size: 13px;
            font-weight: 600;

            animation:
                notificationShow .4s ease;
        }

        .settings-notification i {
            font-size: 21px;
        }

        .settings-success {
            color: #8af0aa;

            background:
                rgba(34,197,94,.09);

            border:
                1px solid rgba(34,197,94,.22);
        }

        .settings-error {
            color: #ff8c8c;

            background:
                rgba(239,68,68,.09);

            border:
                1px solid rgba(239,68,68,.22);
        }

        @keyframes notificationShow {

            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /* =========================================
           GRID
        ========================================= */

        .settings-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1.5fr)
                minmax(250px, .8fr);

            gap: 22px;

            align-items: start;
        }


        /* =========================================
           MAIN CARD
        ========================================= */

        .settings-card {
            position: relative;

            padding: 29px;

            border-radius: 21px;

            overflow: hidden;

            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.075),
                    rgba(255,255,255,.025)
                );

            border:
                1px solid rgba(255,255,255,.08);

            box-shadow:
                0 20px 55px rgba(0,0,0,.18);

            backdrop-filter: blur(15px);
        }

        .settings-card::after {
            content: "";

            position: absolute;

            width: 220px;
            height: 220px;

            right: -130px;
            bottom: -130px;

            border-radius: 50%;

            background:
                radial-gradient(
                    circle,
                    rgba(124,58,237,.14),
                    transparent 70%
                );

            pointer-events: none;
        }


        /* =========================================
           CARD HEADER
        ========================================= */

        .card-heading {
            position: relative;
            z-index: 2;

            display: flex;
            align-items: center;
            gap: 13px;

            padding-bottom: 21px;

            margin-bottom: 24px;

            border-bottom:
                1px solid rgba(255,255,255,.07);
        }

        .card-heading-icon {
            width: 44px;
            height: 44px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;

            color: #a99cff;

            font-size: 21px;

            background:
                rgba(124,58,237,.12);

            border:
                1px solid rgba(139,92,246,.22);
        }

        .card-heading h2 {
            margin: 0;

            color: #fff;

            font-size: 18px;
        }

        .card-heading p {
            margin: 3px 0 0;

            color: rgba(255,255,255,.42);

            font-size: 11px;
        }


        /* =========================================
           FORM
        ========================================= */

        .settings-form {
            position: relative;
            z-index: 3;
        }

        .settings-form-group {
            margin-bottom: 20px;
        }

        .settings-form-label {
            display: flex;
            align-items: center;
            gap: 7px;

            margin-bottom: 8px;

            color: rgba(255,255,255,.86);

            font-size: 12px;
            font-weight: 700;
        }

        .settings-form-label i {
            color: #9b7cff;
            font-size: 16px;
        }


        /* =========================================
           INPUT
        ========================================= */

        .settings-input-wrap {
            position: relative;
        }

        .settings-input-wrap > i {
            position: absolute;

            left: 15px;
            top: 50%;

            transform:
                translateY(-50%);

            color:
                rgba(255,255,255,.34);

            font-size: 17px;

            pointer-events: none;
        }

        .settings-input {
            width: 100%;
            height: 49px;

            padding:
                0 15px 0 45px;

            border-radius: 12px;

            border:
                1px solid rgba(255,255,255,.09);

            outline: none;

            background:
                rgba(0,0,0,.18);

            color: #fff;

            font-family: inherit;

            font-size: 13px;

            transition: .25s ease;
        }

        .settings-input::placeholder {
            color:
                rgba(255,255,255,.25);
        }

        .settings-input:hover {
            border-color:
                rgba(139,92,246,.35);
        }

        .settings-input:focus {
            border-color: #8b5cf6;

            background:
                rgba(124,58,237,.06);

            box-shadow:
                0 0 0 3px rgba(124,58,237,.10);
        }

        .settings-help {
            display: block;

            margin-top: 7px;

            color:
                rgba(255,255,255,.36);

            font-size: 10px;
        }


        /* =========================================
           ACTION
        ========================================= */

        .settings-actions {
            margin-top: 27px;

            padding-top: 20px;

            border-top:
                1px solid rgba(255,255,255,.07);
        }

        .save-settings-btn {
            position: relative;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 8px;

            min-width: 160px;
            height: 46px;

            padding: 0 23px;

            border: none;
            border-radius: 12px;

            overflow: hidden;

            cursor: pointer;

            color: #fff;

            font-family: inherit;

            font-size: 12px;
            font-weight: 800;

            background:
                linear-gradient(
                    135deg,
                    #7c3aed,
                    #06b6d4
                );

            box-shadow:
                0 10px 27px rgba(124,58,237,.24);

            transition: .3s ease;
        }

        .save-settings-btn::before {
            content: "";

            position: absolute;

            top: 0;
            left: -100%;

            width: 70%;
            height: 100%;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    rgba(255,255,255,.24),
                    transparent
                );

            transform:
                skewX(-20deg);

            transition:
                left .6s ease;
        }

        .save-settings-btn:hover {
            transform: translateY(-2px);

            box-shadow:
                0 15px 35px rgba(124,58,237,.34);
        }

        .save-settings-btn:hover::before {
            left: 140%;
        }

        .save-settings-btn i {
            font-size: 17px;
        }


        /* =========================================
           SIDE CARDS
        ========================================= */

        .settings-side {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .info-card {
            position: relative;

            padding: 21px;

            border-radius: 18px;

            overflow: hidden;

            background:
                rgba(255,255,255,.045);

            border:
                1px solid rgba(255,255,255,.075);

            box-shadow:
                0 12px 35px rgba(0,0,0,.12);

            transition: .3s ease;
        }

        .info-card:hover {
            transform: translateY(-3px);

            border-color:
                rgba(139,92,246,.24);

            background:
                rgba(124,58,237,.055);
        }

        .info-card::after {
            content: "";

            position: absolute;

            width: 90px;
            height: 90px;

            right: -45px;
            top: -45px;

            border-radius: 50%;

            border:
                1px solid rgba(139,92,246,.16);
        }

        .info-icon {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 13px;

            border-radius: 11px;

            color: #a99cff;

            font-size: 19px;

            background:
                rgba(124,58,237,.11);

            border:
                1px solid rgba(139,92,246,.15);
        }

        .info-card h3 {
            margin: 0 0 6px;

            color: #fff;

            font-size: 14px;
        }

        .info-card p {
            margin: 0;

            color:
                rgba(255,255,255,.46);

            font-size: 11px;

            line-height: 1.65;
        }

        .info-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;

            margin-top: 13px;

            padding: 5px 9px;

            border-radius: 20px;

            color: #72e39a;

            font-size: 9px;
            font-weight: 700;

            background:
                rgba(34,197,94,.08);

            border:
                1px solid rgba(34,197,94,.15);
        }

        .info-status span {
            width: 6px;
            height: 6px;

            border-radius: 50%;

            background: #4ade80;

            box-shadow:
                0 0 8px #4ade80;
        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media(max-width: 950px) {

            .settings-grid {
                grid-template-columns: 1fr;
            }

            .settings-page {
                margin: 25px 15px;
            }

        }


        @media(max-width: 650px) {

            .settings-page {
                margin: 20px 12px;
            }

            .settings-hero {
                padding: 22px;
            }

            .settings-hero-content h1 {
                font-size: 23px;
            }

            .settings-hero-content p {
                font-size: 11px;
            }

            .settings-hero-icon {
                width: 55px;
                height: 55px;
                font-size: 24px;
            }

            .settings-header-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .settings-header-btn {
                width: 100%;
            }

            .settings-card {
                padding: 20px;
            }

            .save-settings-btn {
                width: 100%;
            }

        }


        @media(prefers-reduced-motion: reduce) {

            .settings-hero-icon {
                animation: none;
            }

            .save-settings-btn,
            .info-card,
            .settings-header-btn {
                transition: none;
            }

        }

    </style>

</head>


<body>


<!-- =========================================
     SIDEBAR
========================================= -->

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

                <span>Dashboard</span>

            </a>

        </li>


        <li>

            <a href="about.php">

                <i class="ri-user-3-fill"></i>

                <span>About</span>

            </a>

        </li>


        <li>

            <a href="skills.php">

                <i class="ri-code-box-fill"></i>

                <span>Skills</span>

            </a>

        </li>


        <li>

            <a href="project-list.php">

                <i class="ri-folder-fill"></i>

                <span>Projects</span>

            </a>

        </li>


        <li>

            <a href="resume-edit.php">

                <i class="ri-file-text-fill"></i>

                <span>Resume</span>

            </a>

        </li>


        <li>

            <a href="messages.php">

                <i class="ri-message-3-fill"></i>

                <span>Messages</span>

            </a>

        </li>


        <li class="active">

            <a href="settings.php">

                <i class="ri-settings-3-fill"></i>

                <span>Settings</span>

            </a>

        </li>


        <li>

            <a href="profile.php">

                <i class="ri-user-settings-line"></i>

                <span>Admin Profile</span>

            </a>

        </li>


        <li>

            <a href="logout.php">

                <i class="ri-logout-box-r-fill"></i>

                <span>Logout</span>

            </a>

        </li>

    </ul>

</div>


<!-- =========================================
     MAIN
========================================= -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <h2>
            Settings
        </h2>

        <div class="admin">

            Welcome,

            <strong>
                <?php
                echo htmlspecialchars($_SESSION['admin']);
                ?>
            </strong>

        </div>

    </div>


    <!-- =========================================
         SETTINGS CONTENT
    ========================================= -->

    <div class="settings-page">


        <!-- HEADER BUTTONS -->

        <div class="settings-header-actions">

            <a
                href="dashboard.php"
                class="settings-header-btn settings-dashboard-btn"
            >

                <i class="ri-arrow-left-line"></i>

                Back to Dashboard

            </a>


            <a
                href="../index.php"
                target="_blank"
                class="settings-header-btn settings-portfolio-btn"
            >

                <i class="ri-external-link-line"></i>

                View Portfolio

            </a>

        </div>


        <!-- HERO -->

        <div class="settings-hero">

            <div class="settings-hero-content">

                <h1>
                    Control Center
                </h1>

                <p>
                    Manage the contact information displayed
                    across your public portfolio.
                </p>

            </div>


            <div class="settings-hero-icon">

                <i class="ri-settings-5-line"></i>

            </div>

        </div>


        <!-- SUCCESS -->

        <?php if (!empty($settings_success)) { ?>

            <div class="settings-notification settings-success">

                <i class="ri-checkbox-circle-fill"></i>

                <span>
                    <?php echo htmlspecialchars($settings_success); ?>
                </span>

            </div>

        <?php } ?>


        <!-- ERROR -->

        <?php if (!empty($settings_error)) { ?>

            <div class="settings-notification settings-error">

                <i class="ri-error-warning-fill"></i>

                <span>
                    <?php echo htmlspecialchars($settings_error); ?>
                </span>

            </div>

        <?php } ?>


        <!-- GRID -->

        <div class="settings-grid">


            <!-- FORM CARD -->

            <div class="settings-card">


                <div class="card-heading">

                    <div class="card-heading-icon">

                        <i class="ri-global-line"></i>

                    </div>


                    <div>

                        <h2>
                            Contact Information
                        </h2>

                        <p>
                            Update your public contact details
                        </p>

                    </div>

                </div>


                <form
                    method="POST"
                    class="settings-form"
                >


                    <!-- EMAIL -->

                    <div class="settings-form-group">

                        <label class="settings-form-label">

                            <i class="ri-mail-line"></i>

                            Email Address

                        </label>


                        <div class="settings-input-wrap">

                            <i class="ri-mail-line"></i>

                            <input
                                type="email"
                                name="email"
                                class="settings-input"
                                value="<?php
                                    echo htmlspecialchars(
                                        $settings['email']
                                    );
                                ?>"
                                placeholder="your@email.com"
                                maxlength="255"
                                required
                            >

                        </div>

                    </div>


                    <!-- INSTAGRAM -->

                    <div class="settings-form-group">

                        <label class="settings-form-label">

                            <i class="ri-instagram-line"></i>

                            Instagram Username

                        </label>


                        <div class="settings-input-wrap">

                            <i class="ri-instagram-line"></i>

                            <input
                                type="text"
                                name="instagram"
                                class="settings-input"
                                value="<?php
                                    echo htmlspecialchars(
                                        $settings['instagram']
                                    );
                                ?>"
                                placeholder="farlly._.exe"
                                maxlength="255"
                                required
                            >

                        </div>


                        <small class="settings-help">
                            Username only — do not include @
                        </small>

                    </div>


                    <!-- LOCATION -->

                    <div class="settings-form-group">

                        <label class="settings-form-label">

                            <i class="ri-map-pin-line"></i>

                            Location

                        </label>


                        <div class="settings-input-wrap">

                            <i class="ri-map-pin-line"></i>

                            <input
                                type="text"
                                name="location"
                                class="settings-input"
                                value="<?php
                                    echo htmlspecialchars(
                                        $settings['location']
                                    );
                                ?>"
                                placeholder="Delhi, India"
                                maxlength="255"
                                required
                            >

                        </div>

                    </div>


                    <!-- SAVE -->

                    <div class="settings-actions">

                        <button
                            type="submit"
                            name="update"
                            class="save-settings-btn"
                        >

                            <i class="ri-save-3-line"></i>

                            Save Changes

                        </button>

                    </div>


                </form>

            </div>


            <!-- SIDE INFORMATION -->

            <div class="settings-side">


                <!-- PUBLIC -->

                <div class="info-card">

                    <div class="info-icon">

                        <i class="ri-global-line"></i>

                    </div>

                    <h3>
                        Public Information
                    </h3>

                    <p>
                        These details are used throughout
                        your portfolio contact section and footer.
                    </p>

                    <div class="info-status">

                        <span></span>

                        Live on Portfolio

                    </div>

                </div>


                <!-- SECURITY -->

                <div class="info-card">

                    <div class="info-icon">

                        <i class="ri-shield-check-line"></i>

                    </div>

                    <h3>
                        Admin Protected
                    </h3>

                    <p>
                        Only authenticated administrators
                        can modify these settings.
                    </p>

                </div>


                <!-- RESPONSIVE -->

                <div class="info-card">

                    <div class="info-icon">

                        <i class="ri-smartphone-line"></i>

                    </div>

                    <h3>
                        Responsive Panel
                    </h3>

                    <p>
                        The settings interface adapts
                        automatically to smaller screens.
                    </p>

                </div>


            </div>

        </div>

    </div>

</div>


<!-- =========================================
     AUTO HIDE NOTIFICATION
========================================= -->

<script>

    setTimeout(function () {

        const notification =
            document.querySelector(".settings-notification");

        if (notification) {

            notification.style.transition =
                "opacity .4s ease, transform .4s ease";

            notification.style.opacity = "0";

            notification.style.transform =
                "translateY(-8px)";

            setTimeout(function () {

                notification.remove();

            }, 400);

        }

    }, 4000);

</script>


</body>

</html>