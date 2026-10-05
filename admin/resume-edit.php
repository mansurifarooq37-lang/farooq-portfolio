<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/db.php");


/* =========================================================
   VARIABLES
========================================================= */

$success = "";
$error = "";


/* =========================================================
   CHECK RESUME RECORD
========================================================= */

$result = mysqli_query($conn, "SELECT * FROM resume LIMIT 1");
$resume = mysqli_fetch_assoc($result);


/* =========================================================
   SAVE / UPDATE RESUME
========================================================= */

if (isset($_POST['save'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $location = mysqli_real_escape_string($conn, $_POST['location'] ?? '');
    $about = mysqli_real_escape_string($conn, $_POST['about'] ?? '');
    $education = mysqli_real_escape_string($conn, $_POST['education'] ?? '');
    $course = mysqli_real_escape_string($conn, $_POST['course'] ?? '');
    $skills = mysqli_real_escape_string($conn, $_POST['skills'] ?? '');
    $experience = mysqli_real_escape_string($conn, $_POST['experience'] ?? '');
    $projects = mysqli_real_escape_string($conn, $_POST['projects'] ?? '');
    $achievements = mysqli_real_escape_string($conn, $_POST['achievements'] ?? '');


    /* =====================================================
       UPDATE EXISTING RESUME
    ===================================================== */

    if ($resume) {

        $query = "UPDATE resume SET

            name='$name',
            email='$email',
            phone='$phone',
            location='$location',
            about='$about',
            education='$education',
            course='$course',
            skills='$skills',
            experience='$experience',
            projects='$projects',
            achievements='$achievements'

            WHERE id=" . intval($resume['id']);

    }


    /* =====================================================
       INSERT NEW RESUME
    ===================================================== */

    else {

        $query = "INSERT INTO resume
        (
            name,
            email,
            phone,
            location,
            about,
            education,
            course,
            skills,
            experience,
            projects,
            achievements
        )

        VALUES
        (
            '$name',
            '$email',
            '$phone',
            '$location',
            '$about',
            '$education',
            '$course',
            '$skills',
            '$experience',
            '$projects',
            '$achievements'
        )";
    }


    /* =====================================================
       EXECUTE
    ===================================================== */

    if (mysqli_query($conn, $query)) {

        $success = "Resume Updated Successfully!";

    } else {

        $error = "Unable to update resume. Please try again.";

    }
}


/* =========================================================
   GET LATEST RESUME DATA
========================================================= */

$result = mysqli_query($conn, "SELECT * FROM resume LIMIT 1");
$resume = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Edit Resume | Admin Panel</title>


<!-- ADMIN CSS -->

<link
    rel="stylesheet"
    href="assets/css/admin.css"
>


<!-- REMIX ICONS -->

<link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
    rel="stylesheet"
>


<style>

/* =========================================================
   RESUME EDIT PAGE
========================================================= */

.resume-page {

    padding: 35px;

    position: relative;

    z-index: 2;

}


/* =========================================================
   HEADER
========================================================= */

.resume-topbar {

    max-width: 1000px;

    margin: 0 auto 25px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

}


.resume-title-area h1 {

    margin: 0;

    color: #ffffff;

    font-size: 30px;

    font-weight: 800;

    letter-spacing: -0.5px;

}


.resume-title-area p {

    margin: 7px 0 0;

    color: rgba(255,255,255,.58);

    font-size: 14px;

}


/* =========================================================
   DASHBOARD BUTTON
========================================================= */

.dashboard-btn {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    padding: 11px 17px;

    border-radius: 10px;

    border: 1px solid rgba(108,99,255,.35);

    background: rgba(108,99,255,.10);

    color: #ffffff;

    text-decoration: none;

    font-size: 13px;

    font-weight: 700;

    white-space: nowrap;

    transition: .3s ease;

}


.dashboard-btn i {

    font-size: 17px;

}


.dashboard-btn:hover {

    background: #6C63FF;

    border-color: #6C63FF;

    transform: translateY(-2px);

    box-shadow:
        0 10px 25px rgba(108,99,255,.25);

}


/* =========================================================
   SAVE NOTIFICATION
========================================================= */

.resume-notification {

    max-width: 1000px;

    margin: 0 auto 22px;

    padding: 15px 18px;

    border-radius: 15px;

    display: flex;

    align-items: center;

    gap: 14px;

    backdrop-filter: blur(16px);

    -webkit-backdrop-filter: blur(16px);

    animation:
        resumeNotificationIn
        .45s
        ease forwards;

}


.resume-notification.success {

    background:
        rgba(34,197,94,.10);

    border:
        1px solid
        rgba(34,197,94,.30);

    box-shadow:
        0 12px 35px
        rgba(34,197,94,.08);

}


.resume-notification.error {

    background:
        rgba(239,68,68,.10);

    border:
        1px solid
        rgba(239,68,68,.30);

    box-shadow:
        0 12px 35px
        rgba(239,68,68,.08);

}


.resume-notification-icon {

    width: 43px;

    height: 43px;

    flex-shrink: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    font-size: 22px;

}


.resume-notification.success
.resume-notification-icon {

    color: #22c55e;

    background:
        rgba(34,197,94,.14);

    box-shadow:
        0 0 20px
        rgba(34,197,94,.12);

}


.resume-notification.error
.resume-notification-icon {

    color: #ef4444;

    background:
        rgba(239,68,68,.14);

}


.resume-notification-content {

    display: flex;

    flex-direction: column;

    gap: 3px;

}


.resume-notification-content strong {

    font-size: 14px;

    font-weight: 750;

}


.resume-notification.success
.resume-notification-content strong {

    color: #22c55e;

}


.resume-notification.error
.resume-notification-content strong {

    color: #ef4444;

}


.resume-notification-content span {

    color:
        rgba(255,255,255,.55);

    font-size: 12px;

}


@keyframes resumeNotificationIn {

    0% {

        opacity: 0;

        transform:
            translateY(-12px)
            scale(.97);

    }

    100% {

        opacity: 1;

        transform:
            translateY(0)
            scale(1);

    }

}


/* =========================================================
   MAIN EDITOR CARD
========================================================= */

.resume-editor {

    max-width: 1000px;

    margin: 0 auto;

    padding: 30px;

    border-radius: 22px;

    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,.075),
            rgba(255,255,255,.025)
        );

    border: 1px solid rgba(255,255,255,.10);

    box-shadow:

        0 25px 70px rgba(0,0,0,.30),

        inset 0 1px 0 rgba(255,255,255,.05);

    backdrop-filter: blur(18px);

}


/* =========================================================
   SECTION
========================================================= */

.resume-section {

    margin-bottom: 30px;

}


.resume-section:last-of-type {

    margin-bottom: 5px;

}


/* =========================================================
   SECTION HEADING
========================================================= */

.section-heading {

    display: flex;

    align-items: center;

    gap: 11px;

    margin-bottom: 18px;

    padding-bottom: 12px;

    border-bottom:
        1px solid rgba(255,255,255,.08);

}


.section-icon {

    width: 34px;

    height: 34px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 9px;

    background:
        linear-gradient(
            135deg,
            #6C63FF,
            #00D4FF
        );

    color: #ffffff;

    box-shadow:
        0 8px 20px rgba(108,99,255,.22);

}


.section-icon i {

    font-size: 17px;

}


.section-heading h2 {

    margin: 0;

    color: #ffffff;

    font-size: 17px;

    font-weight: 750;

}


/* =========================================================
   FORM GRID
========================================================= */

.form-grid {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 18px;

}


.form-group {

    margin-bottom: 18px;

}


.form-group.full {

    grid-column: 1 / -1;

}


/* =========================================================
   LABEL
========================================================= */

.form-group label {

    display: block;

    margin-bottom: 8px;

    color: rgba(255,255,255,.82);

    font-size: 13px;

    font-weight: 650;

}


/* =========================================================
   INPUTS
========================================================= */

.form-group input,
.form-group textarea {

    width: 100%;

    box-sizing: border-box;

    padding: 13px 15px;

    border-radius: 11px;

    border:
        1px solid rgba(255,255,255,.10);

    outline: none;

    background:
        rgba(0,0,0,.22);

    color: #ffffff;

    font-family: inherit;

    font-size: 14px;

    transition:

        border-color .25s ease,

        box-shadow .25s ease,

        background .25s ease;

}


.form-group input::placeholder,
.form-group textarea::placeholder {

    color: rgba(255,255,255,.30);

}


.form-group textarea {

    min-height: 115px;

    resize: vertical;

    line-height: 1.6;

}


/* =========================================================
   FOCUS
========================================================= */

.form-group input:focus,
.form-group textarea:focus {

    border-color: #6C63FF;

    background:
        rgba(108,99,255,.07);

    box-shadow:

        0 0 0 3px
        rgba(108,99,255,.10),

        0 8px 25px
        rgba(0,0,0,.12);

}


/* =========================================================
   HELP TEXT
========================================================= */

.field-help {

    display: block;

    margin-top: 7px;

    color:
        rgba(255,255,255,.38);

    font-size: 11px;

}


/* =========================================================
   ACHIEVEMENTS
========================================================= */

.achievement-box textarea {

    min-height: 135px;

}


/* =========================================================
   ACTION AREA
========================================================= */

.action-buttons {

    display: flex;

    align-items: center;

    gap: 12px;

    margin-top: 30px;

    padding-top: 22px;

    border-top:
        1px solid rgba(255,255,255,.08);

}


/* =========================================================
   SAVE BUTTON
========================================================= */

.save-btn {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    padding: 13px 22px;

    border: none;

    border-radius: 10px;

    background:
        linear-gradient(
            135deg,
            #6C63FF,
            #5148e8
        );

    color: #ffffff;

    font-size: 14px;

    font-weight: 750;

    cursor: pointer;

    box-shadow:
        0 10px 25px
        rgba(108,99,255,.25);

    transition: .3s ease;

}


.save-btn i {

    font-size: 17px;

}


.save-btn:hover {

    transform: translateY(-2px);

    box-shadow:
        0 15px 35px
        rgba(108,99,255,.35);

}


/* =========================================================
   VIEW RESUME
========================================================= */

.view-resume-btn {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    padding: 12px 20px;

    border-radius: 10px;

    border:
        1px solid rgba(0,212,255,.35);

    background:
        rgba(0,212,255,.08);

    color: #ffffff;

    text-decoration: none;

    font-size: 14px;

    font-weight: 700;

    transition: .3s ease;

}


.view-resume-btn i {

    font-size: 17px;

}


.view-resume-btn:hover {

    background: #00a8ff;

    border-color: #00a8ff;

    transform: translateY(-2px);

    box-shadow:
        0 12px 28px
        rgba(0,168,255,.25);

}


/* =========================================================
   DASHBOARD SECONDARY BUTTON
========================================================= */

.dashboard-secondary {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    padding: 12px 18px;

    border-radius: 10px;

    border:
        1px solid rgba(255,255,255,.10);

    background:
        rgba(255,255,255,.04);

    color:
        rgba(255,255,255,.75);

    text-decoration: none;

    font-size: 13px;

    font-weight: 650;

    transition: .3s ease;

}


.dashboard-secondary:hover {

    background:
        rgba(255,255,255,.08);

    color: #ffffff;

    transform: translateY(-2px);

}


/* =========================================================
   RIGHT SIDE - PREMIUM RESUME COMMAND CENTER
========================================================= */

.resume-page {

    position: relative;

    min-height: 100vh;

    overflow: hidden;

}


/* RIGHT SIDE BACKGROUND */

.resume-page::before {

    content: "";

    position: fixed;

    right: 0;

    top: 90px;

    width: 46%;

    height: calc(100vh - 90px);

    pointer-events: none;

    z-index: 0;

    background:

        radial-gradient(
            circle at 50% 45%,
            rgba(108,99,255,.16) 0%,
            rgba(0,212,255,.08) 22%,
            transparent 58%
        );

}


/* MAIN ORBIT */

.resume-page::after {

    content: "";

    position: fixed;

    right: 165px;

    top: 230px;

    width: 390px;

    height: 390px;

    border-radius: 50%;

    border:
        1px solid rgba(108,99,255,.16);

    box-shadow:

        0 0 70px
        rgba(108,99,255,.08),

        inset 0 0 70px
        rgba(0,212,255,.04);

    pointer-events: none;

    z-index: 0;

    animation:
        resumeOrbit
        16s linear infinite;

}


/* =========================================================
   RIGHT SIDE HUD
========================================================= */

.resume-title-area {

    position: relative;

    z-index: 10;

}


.resume-title-area::after {

    content:
        "RESUME CONTROL\A"
        "──────────────────\A"
        "PROFILE        ✓\A"
        "EDUCATION      ✓\A"
        "SKILLS         ✓\A"
        "EXPERIENCE     ✓\A"
        "PROJECTS       ✓\A"
        "ACHIEVEMENTS   ✓";

    white-space: pre;

    position: fixed;

    right: 70px;

    top: 240px;

    width: 270px;

    padding: 25px 28px;

    box-sizing: border-box;

    border:
        1px solid rgba(108,99,255,.28);

    border-radius: 22px;

    background:

        linear-gradient(
            145deg,
            rgba(18,20,32,.94),
            rgba(7,9,18,.90)
        );

    color: #c7cadb;

    font-family:
        "Courier New",
        monospace;

    font-size: 12px;

    line-height: 2;

    letter-spacing: 1px;

    box-shadow:

        0 25px 80px
        rgba(0,0,0,.45),

        0 0 35px
        rgba(108,99,255,.12),

        inset 0 1px 0
        rgba(255,255,255,.05);

    backdrop-filter: blur(18px);

    z-index: 50;

    animation:
        resumeHudFloat
        5s ease-in-out infinite;

}


/* CYAN STATUS DOT */

.resume-title-area::before {

    content: "";

    position: fixed;

    right: 390px;

    top: 265px;

    width: 9px;

    height: 9px;

    border-radius: 50%;

    background: #00e5ff;

    box-shadow:

        0 0 8px #00e5ff,

        0 0 20px
        rgba(0,229,255,.8);

    z-index: 60;

    animation:
        resumeStatusPulse
        1.8s ease-in-out infinite;

}


/* =========================================================
   EXTRA ORBITS
========================================================= */

.resume-page .resume-topbar::after {

    content: "";

    position: fixed;

    right: 210px;

    top: 275px;

    width: 300px;

    height: 300px;

    border-radius: 50%;

    border:
        1px dashed rgba(0,212,255,.15);

    pointer-events: none;

    z-index: 0;

    animation:
        resumeOrbitReverse
        12s linear infinite;

}


.resume-page .resume-topbar::before {

    content: "";

    position: fixed;

    right: 260px;

    top: 325px;

    width: 200px;

    height: 200px;

    border-radius: 50%;

    border:
        1px solid rgba(108,99,255,.12);

    pointer-events: none;

    z-index: 0;

    animation:
        resumeOrbit
        8s linear infinite;

}


/* =========================================================
   KEEP FORM ABOVE BACKGROUND
========================================================= */

.resume-editor {

    position: relative !important;

    z-index: 10 !important;

}


.resume-topbar {

    position: relative;

    z-index: 10;

}


/* =========================================================
   GRID BACKGROUND
========================================================= */

.resume-page {

    background-image:

        linear-gradient(
            rgba(108,99,255,.025) 1px,
            transparent 1px
        ),

        linear-gradient(
            90deg,
            rgba(108,99,255,.025) 1px,
            transparent 1px
        );

    background-size: 40px 40px;

}


/* =========================================================
   ANIMATIONS
========================================================= */

@keyframes resumeHudFloat {

    0%,
    100% {

        transform:
            translateY(0);

    }

    50% {

        transform:
            translateY(-8px);

    }

}


@keyframes resumeStatusPulse {

    0%,
    100% {

        opacity: .45;

        transform:
            scale(.8);

    }

    50% {

        opacity: 1;

        transform:
            scale(1.25);

    }

}


@keyframes resumeOrbit {

    from {

        transform:
            rotate(0deg);

    }

    to {

        transform:
            rotate(360deg);

    }

}


@keyframes resumeOrbitReverse {

    from {

        transform:
            rotate(360deg);

    }

    to {

        transform:
            rotate(0deg);

    }

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width:1100px) {

    .resume-title-area::after,
    .resume-title-area::before,
    .resume-page::after,
    .resume-page .resume-topbar::before,
    .resume-page .resume-topbar::after {

        display: none !important;

    }

    .resume-page::before {

        display: none;

    }

    .resume-editor {

        width: 100%;

        max-width: 850px;

    }

}


@media(max-width:900px) {

    .resume-page {

        padding: 25px;

    }

    .resume-editor {

        padding: 25px;

    }

}


@media(max-width:700px) {

    .resume-topbar {

        align-items: flex-start;

        flex-direction: column;

    }

    .form-grid {

        grid-template-columns: 1fr;

    }

    .form-group.full {

        grid-column: auto;

    }

}


@media(max-width:600px) {

    .resume-page {

        padding: 18px;

    }

    .resume-editor {

        padding: 20px;

        border-radius: 17px;

    }

    .resume-title-area h1 {

        font-size: 25px;

    }

    .action-buttons {

        flex-direction: column;

        align-items: stretch;

    }

    .save-btn,
    .view-resume-btn,
    .dashboard-secondary {

        width: 100%;

    }

    .resume-notification {

        margin-left: 0;

        margin-right: 0;

    }

}


/* =========================================================
   REDUCED MOTION
========================================================= */

@media(prefers-reduced-motion: reduce) {

    .dashboard-btn,
    .save-btn,
    .view-resume-btn,
    .dashboard-secondary,
    .form-group input,
    .form-group textarea {

        transition: none;

    }

}


/* =========================================================
   HIDE EXTRA FLOATING PROFILE CARD
========================================================= */

.resume-editor::before,
.resume-editor::after {

    display: none !important;

    content: none !important;

}

</style>

</head>


<body>


<!-- =======================================================
     MAIN RESUME PAGE
======================================================= -->

<div class="resume-page">


    <!-- ===================================================
         PAGE HEADER
    =================================================== -->

    <div class="resume-topbar">

        <div class="resume-title-area">

            <h1>
                Edit Resume
            </h1>

            <p>
                Manage and update your professional resume information.
            </p>

        </div>


        <a
            href="dashboard.php"
            class="dashboard-btn"
        >

            <i class="ri-dashboard-line"></i>

            Back to Dashboard

        </a>

    </div>


    <!-- ===================================================
         SAVE SUCCESS / ERROR MESSAGE
    =================================================== -->

    <?php if (!empty($success)): ?>

        <div class="resume-notification success">

            <div class="resume-notification-icon">

                <i class="ri-checkbox-circle-fill"></i>

            </div>

            <div class="resume-notification-content">

                <strong>
                    Resume Updated Successfully!
                </strong>

                <span>
                    Your resume information has been saved successfully.
                </span>

            </div>

        </div>

    <?php endif; ?>


    <?php if (!empty($error)): ?>

        <div class="resume-notification error">

            <div class="resume-notification-icon">

                <i class="ri-error-warning-fill"></i>

            </div>

            <div class="resume-notification-content">

                <strong>
                    Update Failed
                </strong>

                <span>
                    <?php echo htmlspecialchars($error); ?>
                </span>

            </div>

        </div>

    <?php endif; ?>


    <!-- ===================================================
         EDITOR
    =================================================== -->

    <div class="resume-editor">


        <form method="POST">


            <!-- =============================================
                 PERSONAL INFORMATION
            ============================================== -->

            <div class="resume-section">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="ri-user-line"></i>

                    </div>

                    <h2>
                        Personal Information
                    </h2>

                </div>


                <div class="form-grid">


                    <!-- NAME -->

                    <div class="form-group">

                        <label>
                            Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="<?php
                            echo htmlspecialchars(
                                $resume['name'] ?? 'MD Raja'
                            );
                            ?>"
                            required
                        >

                    </div>


                    <!-- EMAIL -->

                    <div class="form-group">

                        <label>
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="<?php
                            echo htmlspecialchars(
                                $resume['email'] ?? ''
                            );
                            ?>"
                        >

                    </div>


                    <!-- PHONE -->

                    <div class="form-group">

                        <label>
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="<?php
                            echo htmlspecialchars(
                                $resume['phone'] ?? ''
                            );
                            ?>"
                        >

                    </div>


                    <!-- LOCATION -->

                    <div class="form-group">

                        <label>
                            Location
                        </label>

                        <input
                            type="text"
                            name="location"
                            value="<?php
                            echo htmlspecialchars(
                                $resume['location']
                                ?? 'Delhi, India'
                            );
                            ?>"
                        >

                    </div>


                    <!-- ABOUT -->

                    <div class="form-group full">

                        <label>
                            Professional Profile
                        </label>

                        <textarea
                            name="about"
                            rows="5"
                        ><?php

                        echo htmlspecialchars(

                            $resume['about']

                            ??

                            'Fresher Full Stack Developer passionate about building modern and responsive websites.'

                        );

                        ?></textarea>

                    </div>


                </div>

            </div>


            <!-- =============================================
                 EDUCATION
            ============================================== -->

            <div class="resume-section">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="ri-graduation-cap-line"></i>

                    </div>

                    <h2>
                        Education
                    </h2>

                </div>


                <div class="form-group">

                    <label>
                        Education
                    </label>

                    <textarea
                        name="education"
                        rows="4"
                    ><?php

                    echo htmlspecialchars(

                        $resume['education']

                        ??

                        'B.A. — DU SOL, 2nd Year'

                    );

                    ?></textarea>

                </div>

            </div>


            <!-- =============================================
                 PROFESSIONAL COURSE
            ============================================== -->

            <div class="resume-section">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="ri-book-open-line"></i>

                    </div>

                    <h2>
                        Professional Course
                    </h2>

                </div>


                <div class="form-group">

                    <label>
                        Course
                    </label>

                    <textarea
                        name="course"
                        rows="4"
                    ><?php

                    echo htmlspecialchars(

                        $resume['course']

                        ??

                        'ADWE — Advanced Diploma in Website Engineering'

                    );

                    ?></textarea>

                </div>

            </div>


            <!-- =============================================
                 TECHNICAL SKILLS
            ============================================== -->

            <div class="resume-section">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="ri-code-box-line"></i>

                    </div>

                    <h2>
                        Technical Skills
                    </h2>

                </div>


                <div class="form-group">

                    <label>
                        Skills
                    </label>

                    <textarea
                        name="skills"
                        rows="5"
                    ><?php

                    echo htmlspecialchars(

                        $resume['skills']

                        ??

                        'HTML, CSS, JavaScript, PHP, Laravel, MySQL, WordPress, XAMPP, Git'

                    );

                    ?></textarea>


                    <small class="field-help">

                        Separate skills using commas.

                    </small>

                </div>

            </div>


            <!-- =============================================
                 EXPERIENCE
            ============================================== -->

            <div class="resume-section">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="ri-briefcase-line"></i>

                    </div>

                    <h2>
                        Experience
                    </h2>

                </div>


                <div class="form-group">

                    <label>
                        Experience
                    </label>

                    <textarea
                        name="experience"
                        rows="4"
                    ><?php

                    echo htmlspecialchars(

                        $resume['experience']

                        ??

                        'Fresher'

                    );

                    ?></textarea>

                </div>

            </div>


            <!-- =============================================
                 PROJECTS
            ============================================== -->

            <div class="resume-section">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="ri-folder-open-line"></i>

                    </div>

                    <h2>
                        Projects
                    </h2>

                </div>


                <div class="form-group">

                    <label>
                        Projects
                    </label>

                    <textarea
                        name="projects"
                        rows="5"
                    ><?php

                    echo htmlspecialchars(

                        $resume['projects']

                        ??

                        'Jatin Visuals — Photography & Videography Website'

                    );

                    ?></textarea>

                </div>

            </div>


            <!-- =============================================
                 ACHIEVEMENTS
            ============================================== -->

            <div class="resume-section achievement-box">

                <div class="section-heading">

                    <div class="section-icon">

                        <i class="ri-trophy-line"></i>

                    </div>

                    <h2>
                        Achievements
                    </h2>

                </div>


                <div class="form-group">

                    <label>
                        Achievements
                    </label>

                    <textarea
                        name="achievements"
                        rows="6"
                    ><?php

                    echo htmlspecialchars(

                        $resume['achievements']

                        ??

                        "3 Certificates of Achievement
Secured 1st Position in Laravel Competition
Won Web Development Competition"

                    );

                    ?></textarea>


                    <small class="field-help">

                        Write each achievement on a new line.

                    </small>

                </div>

            </div>


            <!-- =============================================
                 ACTION BUTTONS
            ============================================== -->

            <div class="action-buttons">


                <!-- SAVE -->

                <button
                    type="submit"
                    name="save"
                    class="save-btn"
                >

                    <i class="ri-save-line"></i>

                    Save Resume

                </button>


                <!-- VIEW -->

                <a
                    href="../resume.php"
                    target="_blank"
                    class="view-resume-btn"
                >

                    <i class="ri-eye-line"></i>

                    View Resume

                </a>


                <!-- DASHBOARD -->

                <a
                    href="dashboard.php"
                    class="dashboard-secondary"
                >

                    <i class="ri-arrow-left-line"></i>

                    Dashboard

                </a>


            </div>


        </form>

    </div>

</div>


<!-- =======================================================
     NOTIFICATION AUTO HIDE
======================================================= -->

<script>

setTimeout(function () {

    const notification =
        document.querySelector(
            ".resume-notification"
        );

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