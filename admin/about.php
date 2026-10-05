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
   UPDATE ABOUT
========================= */

if (isset($_POST['update_about'])) {

    $heading = mysqli_real_escape_string($conn, trim($_POST['heading']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $education = mysqli_real_escape_string($conn, trim($_POST['education']));
    $course = mysqli_real_escape_string($conn, trim($_POST['course']));
    $goal = mysqli_real_escape_string($conn, trim($_POST['goal']));

    /* Check if About record exists */

    $check = mysqli_query($conn, "SELECT id FROM about LIMIT 1");

    if (mysqli_num_rows($check) > 0) {

        $row = mysqli_fetch_assoc($check);
        $id = $row['id'];

        $query = "UPDATE about SET
                    heading='$heading',
                    description='$description',
                    education='$education',
                    course='$course',
                    goal='$goal'
                  WHERE id='$id'";

    } else {

        $query = "INSERT INTO about
                    (heading, description, education, course, goal)
                  VALUES
                    ('$heading', '$description', '$education', '$course', '$goal')";
    }

    if (mysqli_query($conn, $query)) {

        $message = "About section updated successfully!";
        $message_type = "success";

    } else {

        $message = "Something went wrong while updating!";
        $message_type = "error";
    }
}


/* =========================
   GET ABOUT DATA
========================= */

$query = "SELECT * FROM about LIMIT 1";
$result = mysqli_query($conn, $query);

$about = mysqli_fetch_assoc($result);


/* =========================
   DEFAULT VALUES
========================= */

if (!$about) {

    $about = [
        'heading' => 'Passionate Full Stack Developer',
        'description' => '',
        'education' => 'B.A. (DU SOL)',
        'course' => 'ADWE',
        'goal' => 'Full Stack Developer'
    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage About | Farooq Admin</title>

    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">

    <!-- Remix Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet"
    >

    <style>

        /* =========================
           PAGE
        ========================= */

        .page-content {
            padding: 35px;
            position: relative;
            z-index: 2;
        }


        /* =========================
           HEADER
        ========================= */

        .page-header {
            margin-bottom: 28px;
        }

        .page-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 8px;
        }

        .page-header h2 {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.5px;
        }

        .page-header p {
            margin: 0;
            color: #a8a8bb;
            font-size: 14px;
        }


        /* =========================
           HEADER BUTTONS
        ========================= */

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 11px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: .3s ease;
            white-space: nowrap;
        }

        .dashboard-btn {
            color: #fff;
            border: 1px solid rgba(255,255,255,.10);
            background: rgba(255,255,255,.05);
        }

        .dashboard-btn:hover {
            transform: translateY(-2px);
            border-color: rgba(139,92,246,.55);
            background: rgba(139,92,246,.12);
            box-shadow: 0 8px 25px rgba(0,0,0,.2);
        }

        .portfolio-btn {
            color: #fff;
            background: linear-gradient(135deg,#7c3aed,#06b6d4);
            border: none;
            box-shadow: 0 8px 25px rgba(124,58,237,.22);
        }

        .portfolio-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(124,58,237,.35);
        }


        /* =========================
           NOTIFICATION
        ========================= */

        .about-alert {
            width: 100%;
            max-width: 950px;
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 15px 18px;
            margin-bottom: 22px;
            border-radius: 13px;
            font-size: 14px;
            font-weight: 600;
            animation: alertShow .45s ease;
            position: relative;
            overflow: hidden;
        }

        .about-alert::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
        }

        .about-alert i {
            font-size: 22px;
        }

        .alert-success {
            color: #b7ffd1;
            background: rgba(34,197,94,.09);
            border: 1px solid rgba(34,197,94,.25);
        }

        .alert-success::before {
            background: #22c55e;
        }

        .alert-success i {
            color: #22c55e;
        }

        .alert-error {
            color: #ffd0d0;
            background: rgba(239,68,68,.09);
            border: 1px solid rgba(239,68,68,.25);
        }

        .alert-error::before {
            background: #ef4444;
        }

        .alert-error i {
            color: #ef4444;
        }

        @keyframes alertShow {
            from {
                opacity: 0;
                transform: translateY(-12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /* =========================
           MAIN GRID
        ========================= */

        .about-layout {
            display: grid;
            grid-template-columns: minmax(0, 950px);
            gap: 25px;
        }


        /* =========================
           ABOUT CARD
        ========================= */

        .about-card {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.075),
                    rgba(255,255,255,.025)
                );
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 20px;
            padding: 30px;
            box-shadow:
                0 25px 70px rgba(0,0,0,.25),
                inset 0 1px 0 rgba(255,255,255,.04);
            backdrop-filter: blur(18px);
            animation: cardShow .6s ease;
        }

        .about-card::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            right: -180px;
            top: -180px;
            background: radial-gradient(
                circle,
                rgba(124,58,237,.20),
                transparent 68%
            );
            pointer-events: none;
        }

        .about-card::after {
            content: "";
            position: absolute;
            width: 250px;
            height: 250px;
            left: -180px;
            bottom: -180px;
            background: radial-gradient(
                circle,
                rgba(6,182,212,.12),
                transparent 68%
            );
            pointer-events: none;
        }

        @keyframes cardShow {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /* =========================
           CARD TITLE
        ========================= */

        .card-heading {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 13px;
            padding-bottom: 22px;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .card-heading-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            color: #fff;
            font-size: 22px;
            background: linear-gradient(
                135deg,
                #7c3aed,
                #06b6d4
            );
            box-shadow:
                0 8px 25px rgba(124,58,237,.25);
        }

        .card-heading-text h3 {
            margin: 0 0 3px;
            color: #fff;
            font-size: 17px;
        }

        .card-heading-text span {
            color: #9292a6;
            font-size: 12px;
        }


        /* =========================
           FORM
        ========================= */

        .about-form {
            position: relative;
            z-index: 2;
        }

        .form-group {
            margin-bottom: 21px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 8px;
            color: #e8e8f2;
            font-size: 13px;
            font-weight: 600;
        }

        .form-group label i {
            color: #9b6cff;
            font-size: 16px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 13px 15px;
            border-radius: 11px;
            border: 1px solid rgba(255,255,255,.10);
            background: rgba(0,0,0,.20);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            outline: none;
            transition: .3s ease;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #686879;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: rgba(139,92,246,.8);
            background: rgba(0,0,0,.28);
            box-shadow:
                0 0 0 3px rgba(124,58,237,.10),
                0 0 25px rgba(124,58,237,.07);
        }

        .form-group textarea {
            min-height: 175px;
            resize: vertical;
            line-height: 1.7;
        }


        /* =========================
           FORM HINT
        ========================= */

        .form-hint {
            display: block;
            margin-top: 7px;
            color: #77778b;
            font-size: 11px;
        }


        /* =========================
           FORM ROW
        ========================= */

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }


        /* =========================
           SAVE BUTTON
        ========================= */

        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 8px;
        }

        .save-btn {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: none;
            color: #fff;
            padding: 13px 24px;
            border-radius: 11px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(
                135deg,
                #7c3aed,
                #06b6d4
            );
            box-shadow:
                0 10px 28px rgba(124,58,237,.24);
            transition: .3s ease;
        }

        .save-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 70%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255,255,255,.22),
                transparent
            );
            transform: skewX(-20deg);
            transition: .6s ease;
        }

        .save-btn:hover::before {
            left: 130%;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow:
                0 14px 35px rgba(124,58,237,.35);
        }

        .save-btn i {
            font-size: 18px;
        }


        /* =========================
           RESET BUTTON
        ========================= */

        .reset-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 17px;
            border-radius: 11px;
            border: 1px solid rgba(255,255,255,.09);
            background: rgba(255,255,255,.04);
            color: #bdbdcc;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: .3s ease;
        }

        .reset-btn:hover {
            color: #fff;
            background: rgba(255,255,255,.07);
            transform: translateY(-2px);
        }


        /* =========================
           INFO BOXES
        ========================= */

        .about-info {
            display: grid;
            grid-template-columns: repeat(3,1fr);
            gap: 14px;
            margin-bottom: 24px;
        }

        .info-box {
            padding: 17px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.07);
            background: rgba(255,255,255,.035);
            transition: .3s ease;
        }

        .info-box:hover {
            transform: translateY(-3px);
            border-color: rgba(124,58,237,.30);
            background: rgba(124,58,237,.06);
        }

        .info-box i {
            display: block;
            color: #9b6cff;
            font-size: 21px;
            margin-bottom: 8px;
        }

        .info-box strong {
            display: block;
            color: #eeeef5;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .info-box span {
            color: #818195;
            font-size: 11px;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media(max-width: 900px) {

            .page-content {
                padding: 25px;
            }

            .page-header-top {
                align-items: flex-start;
                flex-direction: column;
            }

            .about-layout {
                grid-template-columns: 1fr;
            }

        }


        @media(max-width: 700px) {

            .page-content {
                padding: 20px 15px;
            }

            .page-header h2 {
                font-size: 24px;
            }

            .header-actions {
                width: 100%;
            }

            .header-btn {
                flex: 1;
                justify-content: center;
            }

            .about-card {
                padding: 21px;
                border-radius: 17px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .about-info {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .save-btn,
            .reset-btn {
                width: 100%;
            }

        }


        @media(prefers-reduced-motion: reduce) {

            .about-card,
            .about-alert {
                animation: none;
            }

            .save-btn,
            .header-btn,
            .info-box {
                transition: none;
            }

        }

    </style>

</head>


<body>


<!-- ================= SIDEBAR ================= -->

<div class="sidebar">

    <div class="logo">
        <h2>Farooq<span>.</span></h2>
    </div>

    <ul>

        <li>
            <a href="dashboard.php">
                <i class="ri-dashboard-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="active">
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


<!-- ================= MAIN ================= -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h2>About</h2>

        </div>

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

        <div class="page-header">

            <div class="page-header-top">

                <div>

                    <h2>Manage About Section</h2>

                    <p>
                        Update the information displayed on your portfolio's About section.
                    </p>

                </div>


                <div class="header-actions">

                    <a
                        href="dashboard.php"
                        class="header-btn dashboard-btn"
                    >
                        <i class="ri-arrow-left-line"></i>
                        Back to Dashboard
                    </a>

                    <a
                        href="../index.php"
                        target="_blank"
                        class="header-btn portfolio-btn"
                    >
                        <i class="ri-external-link-line"></i>
                        View Portfolio
                    </a>

                </div>

            </div>

        </div>


        <!-- ================= ALERT ================= -->

        <?php if ($message != "") { ?>

            <div
                class="about-alert <?php echo $message_type == 'success' ? 'alert-success' : 'alert-error'; ?>"
            >

                <?php if ($message_type == 'success') { ?>

                    <i class="ri-checkbox-circle-fill"></i>

                <?php } else { ?>

                    <i class="ri-error-warning-fill"></i>

                <?php } ?>


                <span>
                    <?php echo htmlspecialchars($message); ?>
                </span>

            </div>

        <?php } ?>


        <!-- ================= ABOUT INFO ================= -->

        <div class="about-info">

            <div class="info-box">

                <i class="ri-graduation-cap-line"></i>

                <strong>Education</strong>

                <span>
                    Keep your latest education here
                </span>

            </div>


            <div class="info-box">

                <i class="ri-code-s-slash-line"></i>

                <strong>Professional Course</strong>

                <span>
                    Add your current technical course
                </span>

            </div>


            <div class="info-box">

                <i class="ri-rocket-line"></i>

                <strong>Career Goal</strong>

                <span>
                    Show your professional target
                </span>

            </div>

        </div>


        <!-- ================= ABOUT CARD ================= -->

        <div class="about-layout">

            <div class="about-card">


                <!-- CARD HEADING -->

                <div class="card-heading">

                    <div class="card-heading-icon">
                        <i class="ri-user-star-line"></i>
                    </div>

                    <div class="card-heading-text">

                        <h3>Personal & Professional Information</h3>

                        <span>
                            These details will appear on your public portfolio.
                        </span>

                    </div>

                </div>


                <!-- FORM -->

                <form
                    method="POST"
                    class="about-form"
                >


                    <!-- HEADING -->

                    <div class="form-group">

                        <label>

                            <i class="ri-heading"></i>

                            About Heading

                        </label>

                        <input
                            type="text"
                            name="heading"
                            value="<?php echo htmlspecialchars($about['heading']); ?>"
                            placeholder="Example: Passionate Full Stack Developer"
                            maxlength="150"
                            required
                        >

                        <span class="form-hint">
                            Main heading displayed in the About section.
                        </span>

                    </div>


                    <!-- DESCRIPTION -->

                    <div class="form-group">

                        <label>

                            <i class="ri-file-text-line"></i>

                            About Description

                        </label>

                        <textarea
                            name="description"
                            placeholder="Write your About description..."
                            maxlength="1500"
                            required
                        ><?php echo htmlspecialchars($about['description']); ?></textarea>

                        <span class="form-hint">
                            Write a short professional introduction about yourself.
                        </span>

                    </div>


                    <!-- EDUCATION + COURSE -->

                    <div class="form-row">


                        <div class="form-group">

                            <label>

                                <i class="ri-graduation-cap-line"></i>

                                Education

                            </label>

                            <input
                                type="text"
                                name="education"
                                value="<?php echo htmlspecialchars($about['education']); ?>"
                                placeholder="Example: B.A. (DU SOL)"
                                maxlength="150"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label>

                                <i class="ri-code-box-line"></i>

                                Professional Course

                            </label>

                            <input
                                type="text"
                                name="course"
                                value="<?php echo htmlspecialchars($about['course']); ?>"
                                placeholder="Example: Advanced Diploma in Website Engineering"
                                maxlength="200"
                                required
                            >

                        </div>


                    </div>


                    <!-- GOAL -->

                    <div class="form-group">

                        <label>

                            <i class="ri-rocket-2-line"></i>

                            Career Goal

                        </label>

                        <input
                            type="text"
                            name="goal"
                            value="<?php echo htmlspecialchars($about['goal']); ?>"
                            placeholder="Example: Full Stack Developer"
                            maxlength="150"
                            required
                        >

                        <span class="form-hint">
                            Your desired professional role or career direction.
                        </span>

                    </div>


                    <!-- ACTIONS -->

                    <div class="form-actions">

                        <button
                            type="submit"
                            name="update_about"
                            class="save-btn"
                        >

                            <i class="ri-save-3-line"></i>

                            Save Changes

                        </button>


                        <button
                            type="reset"
                            class="reset-btn"
                        >

                            <i class="ri-refresh-line"></i>

                            Reset

                        </button>

                    </div>


                </form>

            </div>

        </div>

    </div>

</div>


<script>

    /* =========================
       AUTO HIDE NOTIFICATION
    ========================= */

    setTimeout(function () {

        const alertBox = document.querySelector('.about-alert');

        if (alertBox) {

            alertBox.style.transition = 'opacity .4s ease, transform .4s ease';

            alertBox.style.opacity = '0';

            alertBox.style.transform = 'translateY(-8px)';

            setTimeout(function () {

                alertBox.remove();

            }, 400);

        }

    }, 4000);


</script>


</body>

</html>