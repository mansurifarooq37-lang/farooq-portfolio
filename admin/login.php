<?php
session_start();

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit();
}

include("../config/db.php");

$error = "";
$success = "";
$old_username = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $old_username = $_POST['username'];

    $query = "SELECT * FROM admin WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {

            $_SESSION['admin'] = $row['username'];

            /*
             * Login successful
             * Green success screen will appear
             * before redirecting to dashboard
             */
            $success = "Admin Access Unlocked. Let’s Make Some Moves. 🚀";

        } else {

            $error = "That password was so wrong, even the database laughed. 💀";

        }

    } else {

        $error = "Username not found 💀 Even the database has no idea who you are.";

    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | Farooq.</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Remix Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css"
        rel="stylesheet"
    >

    <style>

        /* =========================================
           RESET
        ========================================= */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        /* =========================================
           BODY
        ========================================= */

        body {
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
            background: #070711;
            color: #ffffff;
            overflow: hidden;
        }


        /* =========================================
           LOGIN PAGE
        ========================================= */

        .login-page {
            min-height: 100vh;
            width: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 25px;

            position: relative;
            overflow: hidden;
        }


        /* =========================================
           ANIMATED BACKGROUND
        ========================================= */

        .background {
            position: fixed;
            inset: 0;

            overflow: hidden;
            pointer-events: none;

            z-index: 0;

            animation: backgroundBreathing 8s ease-in-out infinite alternate;
        }

        .background::before {
            content: "";

            position: absolute;
            inset: 0;

            background:
                radial-gradient(
                    circle at 20% 20%,
                    rgba(108, 99, 255, .18),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 85% 80%,
                    rgba(0, 212, 255, .13),
                    transparent 30%
                );
        }


        /* =========================================
           GRID
        ========================================= */

        .grid {
            position: absolute;
            inset: -50%;

            background-image:
                linear-gradient(
                    rgba(108, 99, 255, .07) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(108, 99, 255, .07) 1px,
                    transparent 1px
                );

            background-size: 55px 55px;

            transform:
                perspective(500px)
                rotateX(60deg);

            animation: gridMove 15s linear infinite;
        }

        @keyframes gridMove {

            from {
                transform:
                    perspective(500px)
                    rotateX(60deg)
                    translateY(0);
            }

            to {
                transform:
                    perspective(500px)
                    rotateX(60deg)
                    translateY(55px);
            }
        }


        /* =========================================
           GLOW ORBS
        ========================================= */

        .orb {
            position: absolute;

            border-radius: 50%;

            filter: blur(90px);

            opacity: .35;

            animation:
                orbFloat 8s ease-in-out infinite;
        }

        .orb-one {
            width: 420px;
            height: 420px;

            background: #6c63ff;

            left: -180px;
            top: -150px;
        }

        .orb-two {
            width: 380px;
            height: 380px;

            background: #00d4ff;

            right: -170px;
            bottom: -150px;

            animation-delay: -3s;
        }

        @keyframes orbFloat {

            0%,
            100% {
                transform:
                    translate(0, 0)
                    scale(1);
            }

            50% {
                transform:
                    translate(35px, -30px)
                    scale(1.15);
            }
        }


        /* =========================================
           ROTATING RING
        ========================================= */

        .ring {
            position: absolute;

            width: 550px;
            height: 550px;

            right: -210px;
            top: 50%;

            transform: translateY(-50%);

            border:
                1px solid
                rgba(108, 99, 255, .25);

            border-radius: 50%;

            box-shadow:
                0 0 30px rgba(108, 99, 255, .12),
                inset 0 0 30px rgba(0, 212, 255, .08);

            animation:
                ringRotate 20s linear infinite,
                ringGlow 4s ease-in-out infinite;
        }

        .ring::before,
        .ring::after {
            content: "";

            position: absolute;

            inset: 35px;

            border:
                1px solid
                rgba(0, 212, 255, .20);

            border-radius: 50%;
        }

        .ring::after {
            inset: 80px;

            border-color:
                rgba(108, 99, 255, .22);
        }

        @keyframes ringRotate {

            from {
                transform:
                    translateY(-50%)
                    rotate(0deg);
            }

            to {
                transform:
                    translateY(-50%)
                    rotate(360deg);
            }
        }

        @keyframes ringGlow {

            0%,
            100% {
                opacity: .45;
            }

            50% {
                opacity: .9;

                box-shadow:
                    0 0 45px rgba(108, 99, 255, .25),
                    inset 0 0 40px rgba(0, 212, 255, .15);
            }
        }


        /* =========================================
           PARTICLES
        ========================================= */

        .particle {
            position: absolute;

            width: 4px;
            height: 4px;

            border-radius: 50%;

            background: #8d83ff;

            box-shadow:
                0 0 10px #6c63ff,
                0 0 25px rgba(108, 99, 255, .8);

            animation:
                particleCinematic 7s ease-in-out infinite;
        }

        .p-one {
            left: 18%;
            top: 22%;
        }

        .p-two {
            left: 32%;
            top: 78%;

            animation-delay: -2s;
        }

        .p-three {
            left: 70%;
            top: 18%;

            background: #00d4ff;

            box-shadow:
                0 0 10px #00d4ff,
                0 0 25px rgba(0, 212, 255, .8);

            animation-delay: -3s;
        }

        .p-four {
            left: 82%;
            top: 58%;

            animation-delay: -4s;
        }

        @keyframes particleCinematic {

            0%,
            100% {
                transform:
                    translate(0, 0)
                    scale(.4);

                opacity: .15;
            }

            25% {
                transform:
                    translate(25px, -25px)
                    scale(1);

                opacity: .6;
            }

            50% {
                transform:
                    translate(50px, -70px)
                    scale(1.7);

                opacity: 1;
            }

            75% {
                transform:
                    translate(20px, -40px)
                    scale(.8);

                opacity: .5;
            }
        }


        /* =========================================
           SHOOTING LIGHT 1
        ========================================= */

        .background::after {
            content: "";

            position: absolute;

            width: 500px;
            height: 2px;

            left: -600px;
            top: 25%;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    rgba(108, 99, 255, 0),
                    #6c63ff,
                    #00d4ff,
                    transparent
                );

            box-shadow:
                0 0 10px #6c63ff,
                0 0 30px rgba(0, 212, 255, .7);

            transform: rotate(-18deg);

            animation:
                shootingLight 6s linear infinite;
        }

        @keyframes shootingLight {

            0% {
                left: -600px;
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            35% {
                opacity: .9;
            }

            50% {
                left: 120%;
                opacity: 0;
            }

            100% {
                left: 120%;
                opacity: 0;
            }
        }


        /* =========================================
           SHOOTING LIGHT 2
        ========================================= */

        .login-page::before {
            content: "";

            position: fixed;

            width: 350px;
            height: 1px;

            left: -400px;
            top: 70%;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    #00d4ff,
                    #6c63ff,
                    transparent
                );

            box-shadow:
                0 0 12px #00d4ff,
                0 0 30px rgba(108, 99, 255, .6);

            transform: rotate(15deg);

            animation:
                secondShootingLight 8s linear infinite;

            pointer-events: none;
        }

        @keyframes secondShootingLight {

            0% {
                left: -400px;
                opacity: 0;
            }

            15% {
                opacity: 1;
            }

            40% {
                left: 120%;
                opacity: 0;
            }

            100% {
                left: 120%;
                opacity: 0;
            }
        }


        /* =========================================
           LOGIN CARD
        ========================================= */

        .login-card {
            width: 100%;
            max-width: 430px;

            padding: 42px 38px 30px;

            position: relative;
            z-index: 5;

            background:
                rgba(15, 15, 30, .82);

            border:
                1px solid
                rgba(255, 255, 255, .12);

            border-radius: 25px;

            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);

            box-shadow:
                0 25px 80px rgba(0, 0, 0, .6),
                0 0 55px rgba(108, 99, 255, .13);

            overflow: hidden;

            isolation: isolate;

            animation:
                cardAppear .8s ease;
        }


        /* =========================================
           ANIMATED CARD BORDER
        ========================================= */

        .login-card::after {
            content: "";

            position: absolute;

            inset: -2px;

            border-radius: 27px;

            background:
                conic-gradient(
                    from 0deg,
                    transparent 0deg,
                    transparent 70deg,
                    rgba(108, 99, 255, .9) 110deg,
                    rgba(0, 212, 255, .9) 150deg,
                    transparent 190deg,
                    transparent 360deg
                );

            animation:
                cardBorderRotate 5s linear infinite;

            z-index: -2;

            filter: blur(2px);
        }

        .login-card::before {
            content: "";

            position: absolute;

            top: 0;
            left: 12%;

            width: 76%;
            height: 2px;

            border-radius: 20px;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    #6c63ff,
                    #00d4ff,
                    transparent
                );

            box-shadow:
                0 0 20px rgba(108, 99, 255, .8);

            z-index: 2;
        }

        @keyframes cardBorderRotate {

            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes cardAppear {

            from {
                opacity: 0;

                transform:
                    translateY(35px)
                    scale(.95);
            }

            to {
                opacity: 1;

                transform:
                    translateY(0)
                    scale(1);
            }
        }


        /* =========================================
           LOGO
        ========================================= */

        .logo {
            text-align: center;

            margin-bottom: 27px;
        }

        .logo h1 {
            font-size: 34px;

            font-weight: 700;

            letter-spacing: -1px;

            animation:
                logoGlow 3s ease-in-out infinite;
        }

        .logo h1 span {
            color: #6c63ff;
        }

        .logo p {
            color: #85859d;

            font-size: 11px;

            letter-spacing: 3px;

            margin-top: 5px;
        }

        .logo-line {
            width: 48px;
            height: 3px;

            margin: 10px auto 0;

            border-radius: 20px;

            background:
                linear-gradient(
                    90deg,
                    #6c63ff,
                    #00d4ff
                );
        }

        @keyframes logoGlow {

            0%,
            100% {
                text-shadow:
                    0 0 0 transparent;
            }

            50% {
                text-shadow:
                    0 0 18px
                    rgba(108, 99, 255, .35);
            }
        }


        /* =========================================
           LOGIN ICON
        ========================================= */

        .login-icon {
            width: 72px;
            height: 72px;

            margin: 0 auto 18px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 21px;

            color: #9b94ff;

            font-size: 32px;

            background:
                linear-gradient(
                    135deg,
                    rgba(108, 99, 255, .22),
                    rgba(0, 212, 255, .08)
                );

            border:
                1px solid
                rgba(108, 99, 255, .3);

            box-shadow:
                0 0 30px rgba(108, 99, 255, .18);

            animation:
                iconFloat 3s ease-in-out infinite,
                iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-7px);
            }
        }

        @keyframes iconPulse {

            0%,
            100% {
                box-shadow:
                    0 0 20px
                    rgba(108, 99, 255, .15);
            }

            50% {
                box-shadow:
                    0 0 35px
                    rgba(108, 99, 255, .4),

                    0 0 60px
                    rgba(0, 212, 255, .12);
            }
        }


        /* =========================================
           TEXT
        ========================================= */

        .login-card h2 {
            text-align: center;

            font-size: 25px;

            font-weight: 600;

            margin-bottom: 7px;
        }

        .subtitle {
            text-align: center;

            color: #9999b0;

            font-size: 13px;

            margin-bottom: 25px;
        }


        /* =========================================
           ERROR MESSAGE
        ========================================= */

        .login-error {
            display: flex;

            align-items: center;

            gap: 10px;

            margin-bottom: 18px;

            padding: 13px 14px;

            border:
                1px solid
                rgba(255, 70, 90, .45);

            border-radius: 13px;

            background:
                linear-gradient(
                    135deg,
                    rgba(255, 45, 70, .12),
                    rgba(255, 45, 70, .045)
                );

            color: #ff6577;

            font-size: 12px;

            font-weight: 500;

            line-height: 1.5;

            box-shadow:
                0 0 22px
                rgba(255, 45, 70, .08);

            animation:
                errorAppear .35s ease,
                errorShake .45s ease;
        }

        .login-error i {
            flex-shrink: 0;

            font-size: 21px;

            color: #ff4f67;

            filter:
                drop-shadow(
                    0 0 7px
                    rgba(255, 70, 90, .7)
                );
        }

        @keyframes errorAppear {

            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes errorShake {

            0%,
            100% {
                transform: translateX(0);
            }

            20% {
                transform: translateX(-5px);
            }

            40% {
                transform: translateX(5px);
            }

            60% {
                transform: translateX(-3px);
            }

            80% {
                transform: translateX(3px);
            }
        }


        /* =========================================
           INPUT GROUP
        ========================================= */

        .input-group {
            position: relative;

            margin-bottom: 17px;
        }

        .input-icon {
            position: absolute;

            left: 16px;
            top: 50%;

            transform:
                translateY(-50%);

            color: #77778f;

            font-size: 19px;

            pointer-events: none;

            transition: .3s ease;
        }

        .input-group input {
            width: 100%;
            height: 55px;

            padding:
                0 47px;

            color: #ffffff;

            background:
                rgba(255, 255, 255, .045);

            border:
                1px solid
                rgba(255, 255, 255, .11);

            border-radius: 13px;

            outline: none;

            font-family: inherit;

            font-size: 14px;

            transition: .3s ease;
        }

        .input-group input::placeholder {
            color: #77778c;
        }

        .input-group input:focus {
            border-color: #6c63ff;

            background:
                rgba(108, 99, 255, .08);

            box-shadow:
                0 0 0 3px
                rgba(108, 99, 255, .10),

                0 0 25px
                rgba(108, 99, 255, .10);
        }

        .input-group:focus-within .input-icon {
            color: #8d83ff;

            filter:
                drop-shadow(
                    0 0 7px
                    rgba(108, 99, 255, .8)
                );
        }


        /* =========================================
           ERROR INPUT STATE
        ========================================= */

        .input-error input {
            border-color:
                rgba(255, 70, 90, .55);

            box-shadow:
                0 0 0 3px
                rgba(255, 70, 90, .06);
        }

        .input-error .input-icon {
            color: #ff5b70;
        }


        /* =========================================
           PASSWORD TOGGLE
        ========================================= */

        .toggle-password {
            position: absolute;

            right: 14px;
            top: 50%;

            transform:
                translateY(-50%);

            border: none;

            background: transparent;

            color: #77778f;

            font-size: 19px;

            cursor: pointer;

            padding: 5px;

            transition: .3s ease;
        }

        .toggle-password:hover {
            color: #9b94ff;

            filter:
                drop-shadow(
                    0 0 7px
                    rgba(108, 99, 255, .7)
                );
        }


        /* =========================================
           LOGIN BUTTON
        ========================================= */

        .login-btn {
            width: 100%;
            height: 55px;

            margin-top: 7px;

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 10px;

            border: none;

            border-radius: 13px;

            color: #ffffff;

            background:
                linear-gradient(
                    135deg,
                    #6c63ff,
                    #5148d8
                );

            font-family: inherit;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;

            box-shadow:
                0 10px 30px
                rgba(108, 99, 255, .25);

            transition: .3s ease;

            position: relative;

            overflow: hidden;
        }

        .login-btn:hover {
            transform: translateY(-2px);

            box-shadow:
                0 15px 40px
                rgba(108, 99, 255, .45);
        }

        .login-btn i {
            transition: .3s ease;
        }

        .login-btn:hover i {
            transform: translateX(5px);
        }


        /* =========================================
           BUTTON SHINE
        ========================================= */

        .login-btn::before {
            content: "";

            position: absolute;

            width: 80px;
            height: 150%;

            top: -25%;
            left: -120px;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    rgba(255, 255, 255, .35),
                    transparent
                );

            transform: rotate(20deg);

            animation:
                buttonShine 3.5s ease-in-out infinite;
        }

        @keyframes buttonShine {

            0% {
                left: -120px;
            }

            45% {
                left: 120%;
            }

            100% {
                left: 120%;
            }
        }


        /* =========================================
           FOOTER
        ========================================= */

        .login-footer {
            margin-top: 25px;

            padding-top: 20px;

            border-top:
                1px solid
                rgba(255, 255, 255, .07);

            text-align: center;
        }

        .secure-access {
            display: flex;

            align-items: center;
            justify-content: center;

            gap: 6px;

            color: #68687e;

            font-size: 11px;

            margin-bottom: 18px;
        }

        .secure-access i {
            color: #6c63ff;

            animation:
                lockPulse 2s ease-in-out infinite;
        }

        @keyframes lockPulse {

            0%,
            100% {
                filter:
                    drop-shadow(
                        0 0 0 transparent
                    );
            }

            50% {
                filter:
                    drop-shadow(
                        0 0 7px
                        rgba(108, 99, 255, .9)
                    );
            }
        }


        /* =========================================
           CODE CHALLENGE FOOTER
        ========================================= */

        .code-challenge-footer {
            display: flex;

            align-items: center;
            justify-content: center;

            gap: 8px;

            color:
                rgba(255, 255, 255, .55);

            font-size: 12px;

            letter-spacing: .5px;

            position: relative;

            overflow: hidden;

            padding-bottom: 5px;

            animation:
                footerReveal 1.2s ease forwards;
        }

        .code-challenge-footer strong {
            color: #8b7cff;

            font-weight: 600;

            text-shadow:
                0 0 8px
                rgba(108, 99, 255, .7),

                0 0 18px
                rgba(108, 99, 255, .35);

            animation:
                codeGlow 2s ease-in-out infinite;
        }

        .code-line {
            color: #00d4ff;

            font-family: monospace;

            font-weight: bold;

            font-size: 14px;

            text-shadow:
                0 0 8px #00d4ff,

                0 0 18px
                rgba(0, 212, 255, .6);

            animation:
                codeFloat 2s ease-in-out infinite;
        }

        .code-cursor {
            color: #00d4ff;

            font-family: monospace;

            font-weight: bold;

            animation:
                cursorBlink .7s steps(1) infinite;
        }


        /* =========================================
           FOOTER SCAN
        ========================================= */

        .code-challenge-footer::after {
            content: "";

            position: absolute;

            left: -100%;
            bottom: 0;

            width: 45%;
            height: 1px;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    #6c63ff,
                    #00d4ff,
                    transparent
                );

            box-shadow:
                0 0 8px #6c63ff,
                0 0 18px #00d4ff;

            animation:
                footerScan 3s linear infinite;
        }

        @keyframes footerScan {

            0% {
                left: -50%;
            }

            100% {
                left: 110%;
            }
        }

        @keyframes codeGlow {

            0%,
            100% {
                opacity: .65;

                text-shadow:
                    0 0 5px
                    rgba(108, 99, 255, .4);
            }

            50% {
                opacity: 1;

                text-shadow:
                    0 0 8px #6c63ff,

                    0 0 20px
                    rgba(108, 99, 255, .7),

                    0 0 35px
                    rgba(0, 212, 255, .3);
            }
        }

        @keyframes codeFloat {

            0%,
            100% {
                transform:
                    translateY(0);
            }

            50% {
                transform:
                    translateY(-3px);
            }
        }

        @keyframes cursorBlink {

            0%,
            45% {
                opacity: 1;
            }

            46%,
            100% {
                opacity: 0;
            }
        }

        @keyframes footerReveal {

            from {
                opacity: 0;

                transform:
                    translateY(12px);
            }

            to {
                opacity: 1;

                transform:
                    translateY(0);
            }
        }


        /* =========================================
           MOBILE
        ========================================= */

        @media (max-width: 500px) {

            body {
                overflow: auto;
            }

            .login-page {
                padding: 18px;
            }

            .login-card {
                padding:
                    35px 24px 25px;

                border-radius: 21px;
            }

            .logo h1 {
                font-size: 29px;
            }

            .login-icon {
                width: 64px;
                height: 64px;

                font-size: 28px;
            }

            .ring {
                width: 350px;
                height: 350px;
            }

            .code-challenge-footer {
                font-size: 10.5px;

                gap: 6px;
            }

            .login-error {
                font-size: 11px;
            }
        }


        /* =========================================
           REDUCED MOTION
        ========================================= */

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation: none !important;

                transition: none !important;
            }
        }


        /* =========================================
   SUCCESS MESSAGE
========================================= */

.login-success {
    display: flex;
    align-items: center;
    gap: 13px;

    margin-bottom: 20px;
    padding: 15px 17px;

    border: 1px solid rgba(34, 197, 94, .55);
    border-radius: 14px;

    background:
        linear-gradient(
            135deg,
            rgba(34, 197, 94, .16),
            rgba(34, 197, 94, .045)
        );

    color: #7dff9c;

    font-size: 13px;
    font-weight: 600;

    line-height: 1.5;

    box-shadow:
        0 0 25px rgba(34, 197, 94, .12);

    animation:
        successAppear .45s ease,
        successGlow 2s ease-in-out infinite;
}

.login-success i {
    flex-shrink: 0;

    font-size: 23px;
    color: #39ff72;

    filter:
        drop-shadow(
            0 0 8px rgba(57, 255, 114, .85)
        );
}

@keyframes successAppear {

    from {
        opacity: 0;
        transform:
            translateY(-12px)
            scale(.96);
    }

    to {
        opacity: 1;
        transform:
            translateY(0)
            scale(1);
    }
}

@keyframes successGlow {

    0%,
    100% {
        box-shadow:
            0 0 18px rgba(34, 197, 94, .08);
    }

    50% {
        box-shadow:
            0 0 32px rgba(34, 197, 94, .24);
    }
}


/* =========================================
   SUCCESS OVERLAY
========================================= */

.success-screen {
    position: fixed;
    inset: 0;

    z-index: 9999;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 25px;

    background:
        rgba(4, 8, 7, .88);

    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);

    animation:
        successScreenAppear .35s ease;
}

.success-box {
    width: 100%;
    max-width: 450px;

    padding: 38px 30px;

    text-align: center;

    border-radius: 24px;

    border:
        1px solid
        rgba(57, 255, 114, .45);

    background:
        rgba(10, 25, 16, .92);

    box-shadow:
        0 0 45px rgba(34, 197, 94, .16),
        0 25px 80px rgba(0, 0, 0, .6);

    animation:
        successBoxAppear .55s cubic-bezier(.2,.8,.2,1);
}

.success-icon {
    width: 78px;
    height: 78px;

    margin: 0 auto 20px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    color: #39ff72;

    font-size: 39px;

    border:
        1px solid
        rgba(57, 255, 114, .45);

    background:
        rgba(34, 197, 94, .10);

    box-shadow:
        0 0 25px rgba(57, 255, 114, .18);

    animation:
        successIconPulse 1.5s ease-in-out infinite;
}

.success-box h3 {
    margin-bottom: 9px;

    color: #ffffff;

    font-size: 23px;
    font-weight: 600;
}

.success-box p {
    color: #83ff9d;

    font-size: 13px;
}

.success-progress {
    width: 100%;
    height: 3px;

    margin-top: 25px;

    overflow: hidden;

    border-radius: 20px;

    background:
        rgba(255, 255, 255, .08);
}

.success-progress span {
    display: block;

    width: 0%;
    height: 100%;

    border-radius: inherit;

    background:
        linear-gradient(
            90deg,
            #22c55e,
            #39ff72,
            #00d4ff
        );

    box-shadow:
        0 0 12px #39ff72;

    animation:
        successProgress 1.7s linear forwards;
}

@keyframes successScreenAppear {

    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

@keyframes successBoxAppear {

    from {
        opacity: 0;
        transform:
            translateY(25px)
            scale(.92);
    }

    to {
        opacity: 1;
        transform:
            translateY(0)
            scale(1);
    }
}

@keyframes successIconPulse {

    0%,
    100% {
        transform: scale(1);

        box-shadow:
            0 0 20px
            rgba(57, 255, 114, .12);
    }

    50% {
        transform: scale(1.07);

        box-shadow:
            0 0 35px
            rgba(57, 255, 114, .35);
    }
}

@keyframes successProgress {

    from {
        width: 0%;
    }

    to {
        width: 100%;
    }
}

    </style>

</head>


<body>


    <!-- =========================================
         ANIMATED BACKGROUND
    ========================================= -->

    <div class="background">

        <div class="grid"></div>

        <div class="orb orb-one"></div>

        <div class="orb orb-two"></div>

        <div class="ring"></div>

        <div class="particle p-one"></div>

        <div class="particle p-two"></div>

        <div class="particle p-three"></div>

        <div class="particle p-four"></div>

    </div>

    <?php if (!empty($success)): ?>

    <div class="success-screen">

        <div class="success-box">

            <div class="success-icon">
                <i class="ri-checkbox-circle-line"></i>
            </div>

            <h3>
                Access Granted
            </h3>

            <p>
                <?php echo htmlspecialchars($success); ?>
            </p>

            <div class="success-progress">
                <span></span>
            </div>

        </div>

    </div>

<?php endif; ?>


    <!-- =========================================
         LOGIN PAGE
    ========================================= -->

    <main class="login-page">

        <section class="login-card">


            <!-- LOGO -->

            <div class="logo">

                <h1>
                    Farooq<span>.</span>
                </h1>

                <p>
                    ADMIN PANEL
                </p>

                <div class="logo-line"></div>

            </div>


            <!-- LOGIN ICON -->

            <div class="login-icon">

                <i class="ri-shield-user-line"></i>

            </div>


            <!-- TITLE -->

            <h2>
                Welcome Back
            </h2>

            <p class="subtitle">
                Sign in to manage your portfolio
            </p>


            <!-- =====================================
                 ERROR MESSAGE
            ====================================== -->

            <?php if (!empty($error)): ?>

                <div class="login-error">

                    <i class="ri-error-warning-line"></i>

                    <span>
                        <?php echo htmlspecialchars($error); ?>
                    </span>

                </div>

            <?php endif; ?>


            <!-- LOGIN FORM -->

            <form action="" method="POST">


                <!-- USERNAME -->

                <div class="input-group <?php echo !empty($error) ? 'input-error' : ''; ?>">

                    <i class="ri-user-3-line input-icon"></i>

                    <input
                        type="text"
                        name="username"
                        placeholder="Enter username"
                        value="<?php echo htmlspecialchars($old_username); ?>"
                        autocomplete="username"
                        required
                    >

                </div>


                <!-- PASSWORD -->

                <div class="input-group <?php echo !empty($error) ? 'input-error' : ''; ?>">

                    <i class="ri-lock-2-line input-icon"></i>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        id="togglePassword"
                        aria-label="Show password"
                    >

                        <i class="ri-eye-line"></i>

                    </button>

                </div>


                <!-- LOGIN BUTTON -->

                <button
                    type="submit"
                    name="login"
                    class="login-btn"
                >

                    <span>
                        Login to Dashboard
                    </span>

                    <i class="ri-arrow-right-line"></i>

                </button>


            </form>


            <!-- FOOTER -->

            <div class="login-footer">


                <!-- SECURE ACCESS -->

                <div class="secure-access">

                    <i class="ri-lock-line"></i>

                    <span>
                        Secure Admin Access
                    </span>

                </div>


                <!-- CODING CHALLENGE -->

                <div class="code-challenge-footer">

                    <span class="code-line">
                        &lt;/&gt;
                    </span>

                    <span>
                        Wanna login?
                        <strong>
                            Prove you can code.
                        </strong>
                    </span>

                    <span class="code-cursor">
                        _
                    </span>

                </div>


            </div>


        </section>


    </main>


    <!-- =========================================
         PASSWORD SHOW / HIDE
    ========================================= -->

    <script>

        const togglePassword =
            document.getElementById("togglePassword");

        const password =
            document.getElementById("password");


        togglePassword.addEventListener(
            "click",
            function () {

                const icon =
                    this.querySelector("i");


                if (password.type === "password") {

                    password.type = "text";

                    icon.className =
                        "ri-eye-off-line";

                    this.setAttribute(
                        "aria-label",
                        "Hide password"
                    );

                } else {

                    password.type = "password";

                    icon.className =
                        "ri-eye-line";

                    this.setAttribute(
                        "aria-label",
                        "Show password"
                    );

                }

            }
        );

        /* =========================================
       SUCCESS → DASHBOARD
    ========================================= */

    <?php if (!empty($success)): ?>

        setTimeout(function () {

            window.location.href = "dashboard.php";

        }, 1800);

    <?php endif; ?>

    </script>


</body>

</html>