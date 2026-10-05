<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/db.php");

/* =========================
   CHECK PROJECT ID
========================= */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: project-list.php");
    exit();
}

$id = intval($_GET['id']);

$success = "";
$error = "";


/* =========================
   GET PROJECT
========================= */

$query = "SELECT * FROM projects WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Project not found.");
}

$project = mysqli_fetch_assoc($result);


/* =========================
   FLASH MESSAGE
========================= */

if (isset($_SESSION['project_success'])) {
    $success = $_SESSION['project_success'];
    unset($_SESSION['project_success']);
}

if (isset($_SESSION['project_error'])) {
    $error = $_SESSION['project_error'];
    unset($_SESSION['project_error']);
}


/* =========================
   UPDATE PROJECT
========================= */

if (isset($_POST['update'])) {

    $title = mysqli_real_escape_string(
        $conn,
        trim($_POST['title'] ?? '')
    );

    $description = mysqli_real_escape_string(
        $conn,
        trim($_POST['description'] ?? '')
    );

    $technologies = mysqli_real_escape_string(
        $conn,
        trim($_POST['technologies'] ?? '')
    );

    $github = mysqli_real_escape_string(
        $conn,
        trim($_POST['github'] ?? '')
    );

    $live_demo = mysqli_real_escape_string(
        $conn,
        trim($_POST['live_demo'] ?? '')
    );

    $status = mysqli_real_escape_string(
        $conn,
        $_POST['status'] ?? 'Completed'
    );


    /* =========================
       MAIN IMAGE
    ========================= */

    $image_name = $project['image'];

    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] == 0 &&
        !empty($_FILES['image']['name'])
    ) {

        $new_image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];

        $upload_dir = "uploads/projects/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $extension = strtolower(
            pathinfo($new_image, PATHINFO_EXTENSION)
        );

        $allowed_extensions = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];

        if (!in_array($extension, $allowed_extensions)) {

            $error = "Only JPG, JPEG, PNG or WEBP images are allowed.";

        } else {

            $new_file_name =
                time() . "_" .
                uniqid() . "." .
                $extension;

            $upload_path =
                $upload_dir . $new_file_name;

            if (
                move_uploaded_file(
                    $tmp_name,
                    $upload_path
                )
            ) {

                if (
                    !empty($project['image']) &&
                    file_exists(
                        $upload_dir . $project['image']
                    )
                ) {

                    unlink(
                        $upload_dir . $project['image']
                    );
                }

                $image_name = $new_file_name;

            } else {

                $error =
                    "The new project image could not be uploaded.";
            }
        }
    }


    /* =========================
       UPDATE PROJECT DATA
    ========================= */

    if (empty($error)) {

        $update_query = "UPDATE projects SET

            title = '$title',
            description = '$description',
            image = '$image_name',
            technologies = '$technologies',
            github = '$github',
            live_demo = '$live_demo',
            status = '$status'

            WHERE id = $id";

        if (mysqli_query($conn, $update_query)) {


            /* =========================
               ADDITIONAL PHOTOS
            ========================= */

            if (
                isset($_FILES['gallery_images']) &&
                !empty($_FILES['gallery_images']['name'][0])
            ) {

                $gallery_dir = "uploads/projects/";

                if (!is_dir($gallery_dir)) {
                    mkdir($gallery_dir, 0777, true);
                }

                $gallery_count =
                    count($_FILES['gallery_images']['name']);

                for ($i = 0; $i < $gallery_count; $i++) {

                    if (
                        $_FILES['gallery_images']['error'][$i] != 0
                    ) {
                        continue;
                    }

                    $gallery_original_name =
                        $_FILES['gallery_images']['name'][$i];

                    $gallery_tmp_name =
                        $_FILES['gallery_images']['tmp_name'][$i];

                    $gallery_extension = strtolower(
                        pathinfo(
                            $gallery_original_name,
                            PATHINFO_EXTENSION
                        )
                    );

                    $allowed_gallery_extensions = [
                        'jpg',
                        'jpeg',
                        'png',
                        'webp'
                    ];

                    if (
                        !in_array(
                            $gallery_extension,
                            $allowed_gallery_extensions
                        )
                    ) {
                        continue;
                    }

                    $gallery_file_name =
                        time() . "_" .
                        uniqid() . "." .
                        $gallery_extension;

                    $gallery_upload_path =
                        $gallery_dir . $gallery_file_name;

                    if (
                        move_uploaded_file(
                            $gallery_tmp_name,
                            $gallery_upload_path
                        )
                    ) {

                        $gallery_file_name_db =
                            mysqli_real_escape_string(
                                $conn,
                                $gallery_file_name
                            );

                        $gallery_insert_query = "
                            INSERT INTO project_images
                            (project_id, image)
                            VALUES
                            ($id, '$gallery_file_name_db')
                        ";

                        mysqli_query(
                            $conn,
                            $gallery_insert_query
                        );
                    }
                }
            }


            /* =========================
               REFRESH PROJECT
            ========================= */

            $refresh_query =
                "SELECT * FROM projects WHERE id = $id";

            $refresh_result =
                mysqli_query(
                    $conn,
                    $refresh_query
                );

            $project =
                mysqli_fetch_assoc(
                    $refresh_result
                );

            $success =
                "Project Updated Successfully!";

        } else {

            $error =
                "Something went wrong while updating the project.";
        }
    }
}


/* =========================
   GET ADDITIONAL PHOTOS
========================= */

$gallery_query = "
    SELECT *
    FROM project_images
    WHERE project_id = $id
    ORDER BY id ASC
";

$gallery_result =
    mysqli_query(
        $conn,
        $gallery_query
    );

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Project | Admin Panel</title>

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }


        /* =========================
           PAGE
        ========================= */

        .page-content {
            padding: 35px;
            position: relative;
        }


        /* =========================
           PAGE HEADER
        ========================= */

        .page-header {
            margin-bottom: 28px;
        }

        .page-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .page-title-area {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .page-title-icon {
            width: 50px;
            height: 50px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 15px;

            color: #fff;
            font-size: 22px;

            background:
                linear-gradient(
                    135deg,
                    #7c5cff,
                    #00c6ff
                );

            box-shadow:
                0 8px 30px
                rgba(124,92,255,.25);

            animation:
                editIconPulse
                3s
                ease-in-out
                infinite;
        }

        @keyframes editIconPulse {

            0%, 100% {
                box-shadow:
                    0 8px 30px
                    rgba(124,92,255,.25);
            }

            50% {
                box-shadow:
                    0 8px 40px
                    rgba(0,229,255,.30);
            }

        }

        .page-title-area h2 {
            margin: 0 0 5px;

            color: #fff;

            font-size: 29px;
            font-weight: 700;
        }

        .page-title-area p {
            margin: 0;

            color: #8e91a8;

            font-size: 13px;
        }


        /* =========================
           HEADER BUTTONS
        ========================= */

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-action-btn {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 7px;

            padding: 11px 16px;

            border-radius: 11px;

            text-decoration: none;

            font-size: 12px;
            font-weight: 600;

            white-space: nowrap;

            transition: .3s ease;
        }

        .dashboard-btn {
            color: #dffcff;

            background:
                rgba(0,229,255,.06);

            border:
                1px solid
                rgba(0,229,255,.18);
        }

        .dashboard-btn i {
            color: #00dfff;
            font-size: 16px;
        }

        .dashboard-btn:hover {
            color: #fff;

            transform:
                translateY(-2px);

            background:
                rgba(0,229,255,.12);

            border-color:
                rgba(0,229,255,.50);

            box-shadow:
                0 8px 28px
                rgba(0,229,255,.15);
        }

        .projects-btn {
            color: #d9d9e8;

            background:
                rgba(255,255,255,.05);

            border:
                1px solid
                rgba(255,255,255,.09);
        }

        .projects-btn i {
            color: #8b72ff;
            font-size: 16px;
        }

        .projects-btn:hover {
            color: #fff;

            transform:
                translateY(-2px);

            background:
                rgba(124,92,255,.12);

            border-color:
                rgba(124,92,255,.40);

            box-shadow:
                0 8px 28px
                rgba(124,92,255,.15);
        }


        /* =========================
           MESSAGE
        ========================= */

        .project-message {
            display: flex;

            align-items: center;

            gap: 14px;

            width: 100%;
            max-width: 850px;

            margin-bottom: 24px;

            padding: 16px 18px;

            border-radius: 15px;

            backdrop-filter: blur(15px);

            animation:
                projectMessageIn
                .45s
                ease
                forwards;
        }

        .project-success {
            background:
                rgba(34,197,94,.08);

            border:
                1px solid
                rgba(34,197,94,.30);
        }

        .project-error {
            background:
                rgba(239,68,68,.08);

            border:
                1px solid
                rgba(239,68,68,.30);
        }

        .project-message-icon {
            width: 43px;
            height: 43px;

            flex-shrink: 0;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            font-size: 22px;
        }

        .project-success .project-message-icon {
            color: #22c55e;
            background: rgba(34,197,94,.14);
        }

        .project-error .project-message-icon {
            color: #ef4444;
            background: rgba(239,68,68,.14);
        }

        .project-message-content {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .project-message-content strong {
            font-size: 14px;
            font-weight: 700;
        }

        .project-success .project-message-content strong {
            color: #4ade80;
        }

        .project-error .project-message-content strong {
            color: #fb7185;
        }

        .project-message-content span {
            color: #9296aa;
            font-size: 12px;
            line-height: 1.5;
        }

        @keyframes projectMessageIn {

            from {
                opacity: 0;
                transform:
                    translateY(-12px)
                    scale(.98);
            }

            to {
                opacity: 1;
                transform:
                    translateY(0)
                    scale(1);
            }

        }


        /* =========================
           FORM CARD
        ========================= */

        .project-form {
            position: relative;

            max-width: 850px;

            padding: 32px;

            border-radius: 22px;

            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.065),
                    rgba(255,255,255,.025)
                );

            border:
                1px solid
                rgba(255,255,255,.09);

            backdrop-filter: blur(20px);

            box-shadow:
                0 25px 70px
                rgba(0,0,0,.35),

                inset
                0 1px 0
                rgba(255,255,255,.05);

            overflow: hidden;

            animation:
                formShow
                .6s
                ease
                forwards;
        }

        .project-form::before {
            content: "";

            position: absolute;

            top: 0;
            left: 10%;

            width: 80%;
            height: 1px;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    #7c5cff,
                    #00e5ff,
                    transparent
                );

            box-shadow:
                0 0 15px
                rgba(124,92,255,.6);

            animation:
                formLine
                4s
                ease-in-out
                infinite;
        }

        @keyframes formLine {

            0%, 100% {
                opacity: .45;
                transform: scaleX(.7);
            }

            50% {
                opacity: 1;
                transform: scaleX(1);
            }

        }

        @keyframes formShow {

            from {
                opacity: 0;
                transform:
                    translateY(18px)
                    scale(.99);
            }

            to {
                opacity: 1;
                transform:
                    translateY(0)
                    scale(1);
            }

        }


        /* =========================
           FORM GROUP
        ========================= */

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: flex;

            align-items: center;

            gap: 7px;

            margin-bottom: 9px;

            color: #d9d9e8;

            font-size: 13px;

            font-weight: 500;
        }

        .form-group label i {
            color: #8b72ff;
            font-size: 16px;
        }


        /* =========================
           INPUTS
        ========================= */

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;

            padding: 14px 16px;

            border:
                1px solid
                rgba(255,255,255,.09);

            border-radius: 12px;

            outline: none;

            color: #fff;

            background:
                rgba(5,5,15,.65);

            font-family:
                'Poppins',
                sans-serif;

            font-size: 13px;

            transition: .3s ease;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #62657a;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
            line-height: 1.7;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color:
                rgba(124,92,255,.75);

            background:
                rgba(10,10,25,.90);

            box-shadow:
                0 0 0 3px
                rgba(124,92,255,.08),

                0 0 25px
                rgba(124,92,255,.10);

            transform:
                translateY(-1px);
        }


        /* =========================
           SELECT
        ========================= */

        .form-group select {
            cursor: pointer;
        }

        .form-group select option {
            background: #10101c;
            color: #fff;
        }


        /* =========================
           CURRENT IMAGE
        ========================= */

        .current-image {
            display: inline-block;

            margin-bottom: 8px;

            padding: 5px;

            border-radius: 14px;

            background:
                linear-gradient(
                    135deg,
                    rgba(124,92,255,.20),
                    rgba(0,229,255,.12)
                );

            border:
                1px solid
                rgba(255,255,255,.08);
        }

        .current-image img {
            width: 180px;
            height: 115px;

            display: block;

            object-fit: cover;

            border-radius: 10px;
        }

        .no-image {
            margin: 0;
            padding: 18px;

            color: #777b91;

            font-size: 13px;
        }


        /* =========================
           ADDITIONAL PHOTOS
        ========================= */

        .gallery-section {
            margin-top: 10px;
            margin-bottom: 25px;
        }

        .gallery-grid {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 14px;

            margin-top: 12px;
        }

        .gallery-item {
            position: relative;

            overflow: hidden;

            border-radius: 15px;

            background: #090914;

            border:
                1px solid
                rgba(255,255,255,.10);

            box-shadow:
                0 12px 30px
                rgba(0,0,0,.25);

            transition:
                transform .3s ease,
                border-color .3s ease,
                box-shadow .3s ease;
        }

        .gallery-item:hover {
            transform:
                translateY(-4px);

            border-color:
                rgba(124,92,255,.45);

            box-shadow:
                0 18px 40px
                rgba(124,92,255,.14);
        }

        .gallery-item img {
            width: 100%;
            height: 145px;

            display: block;

            object-fit: cover;

            transition:
                transform .4s ease,
                filter .4s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);

            filter:
                brightness(.45);
        }


        /* =========================
           CUSTOM DELETE BUTTON
        ========================= */

        .gallery-delete {
            position: absolute;

            left: 50%;
            top: 50%;

            transform:
                translate(-50%, -50%)
                scale(.9);

            opacity: 0;

            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 6px;

            padding: 10px 14px;

            border:
                1px solid
                rgba(255,100,120,.35);

            border-radius: 10px;

            color: #fff;

            background:
                rgba(180,30,55,.88);

            backdrop-filter: blur(10px);

            font-family:
                'Poppins',
                sans-serif;

            font-size: 11px;

            font-weight: 600;

            cursor: pointer;

            transition:
                opacity .3s ease,
                transform .3s ease,
                background .3s ease;

            z-index: 5;
        }

        .gallery-item:hover .gallery-delete {
            opacity: 1;

            transform:
                translate(-50%, -50%)
                scale(1);
        }

        .gallery-delete:hover {
            background:
                #e53955;

            transform:
                translate(-50%, -50%)
                scale(1.05);
        }


        .gallery-empty {
            padding: 22px;

            border-radius: 14px;

            color: #777b91;

            background:
                rgba(255,255,255,.025);

            border:
                1px dashed
                rgba(255,255,255,.10);

            font-size: 12px;
        }

        .gallery-count {
            display: inline-flex;

            align-items: center;

            gap: 6px;

            margin-top: 8px;

            color: #8589a0;

            font-size: 11px;
        }

        .gallery-count i {
            color: #00dfff;
        }


        /* =========================
           FILE INPUT
        ========================= */

        .file-input {
            padding: 11px 12px !important;

            cursor: pointer;

            border-style: dashed !important;

            border-color:
                rgba(124,92,255,.40) !important;
        }

        .file-input:hover {
            border-color:
                rgba(0,229,255,.55) !important;

            background:
                rgba(124,92,255,.08) !important;
        }

        .form-group small {
            display: block;

            margin-top: 7px;

            color: #666a80;

            font-size: 11px;
        }


        /* =========================
           BUTTONS
        ========================= */

        .form-actions {
            display: flex;

            align-items: center;

            gap: 11px;

            margin-top: 30px;

            padding-top: 24px;

            border-top:
                1px solid
                rgba(255,255,255,.07);
        }

        .update-btn {
            position: relative;

            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 7px;

            border: none;

            padding: 13px 23px;

            border-radius: 11px;

            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    #7c5cff,
                    #00b7ff
                );

            font-family:
                'Poppins',
                sans-serif;

            font-size: 13px;

            font-weight: 600;

            cursor: pointer;

            transition:
                transform .3s ease,
                box-shadow .3s ease;
        }

        .update-btn:hover {
            transform:
                translateY(-3px);

            box-shadow:
                0 12px 40px
                rgba(124,92,255,.40);
        }

        .back-btn {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 7px;

            padding: 12px 20px;

            border:
                1px solid
                rgba(255,255,255,.10);

            border-radius: 11px;

            color: #c7c8d8;

            background:
                rgba(255,255,255,.05);

            text-decoration: none;

            font-size: 13px;

            font-weight: 600;

            transition: .3s ease;
        }

        .back-btn:hover {
            color: #fff;

            background:
                rgba(124,92,255,.12);

            border-color:
                rgba(124,92,255,.40);

            transform:
                translateY(-3px);
        }


        /* =========================
           DELETE MODAL
        ========================= */

        .gallery-delete-modal {
            position: fixed;

            inset: 0;

            display: none;

            align-items: center;
            justify-content: center;

            padding: 20px;

            z-index: 99999;
        }

        .gallery-delete-modal.active {
            display: flex;
        }

        .gallery-delete-overlay {
            position: absolute;

            inset: 0;

            background:
                rgba(0,0,0,.76);

            backdrop-filter:
                blur(9px);
        }

        .gallery-delete-box {
            position: relative;

            width: 100%;
            max-width: 420px;

            padding: 32px;

            text-align: center;

            border-radius: 23px;

            background:
                linear-gradient(
                    145deg,
                    rgba(31,31,50,.98),
                    rgba(10,10,21,.98)
                );

            border:
                1px solid
                rgba(255,255,255,.10);

            box-shadow:
                0 35px 100px
                rgba(0,0,0,.65);

            animation:
                galleryModalShow
                .25s
                ease
                forwards;
        }

        @keyframes galleryModalShow {

            from {
                opacity: 0;
                transform:
                    translateY(18px)
                    scale(.94);
            }

            to {
                opacity: 1;
                transform:
                    translateY(0)
                    scale(1);
            }
        }

        .gallery-delete-icon {
            width: 66px;
            height: 66px;

            margin:
                0 auto 18px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            color: #ff5b70;

            background:
                rgba(255,70,95,.12);

            border:
                1px solid
                rgba(255,70,95,.25);

            font-size: 28px;

            box-shadow:
                0 0 30px
                rgba(255,70,95,.12);

            animation:
                deleteIconPulse
                2s
                ease-in-out
                infinite;
        }

        @keyframes deleteIconPulse {

            0%,100% {
                transform: scale(1);
                box-shadow:
                    0 0 25px
                    rgba(255,70,95,.10);
            }

            50% {
                transform: scale(1.06);
                box-shadow:
                    0 0 35px
                    rgba(255,70,95,.22);
            }
        }

        .gallery-delete-box h3 {
            margin:
                0 0 9px;

            color: #fff;

            font-size: 20px;

            font-weight: 700;
        }

        .gallery-delete-box p {
            margin: 0;

            color: #9699ad;

            font-size: 12px;

            line-height: 1.7;
        }

        .gallery-delete-actions {
            display: flex;

            gap: 10px;

            margin-top: 25px;
        }

        .gallery-cancel-btn,
        .gallery-confirm-btn {

            flex: 1;

            height: 44px;

            border-radius: 11px;

            font-family:
                'Poppins',
                sans-serif;

            font-size: 12px;

            font-weight: 600;

            cursor: pointer;

            transition: .3s ease;
        }

        .gallery-cancel-btn {

            color: #d1d2df;

            background:
                rgba(255,255,255,.05);

            border:
                1px solid
                rgba(255,255,255,.10);
        }

        .gallery-cancel-btn:hover {

            color: #fff;

            background:
                rgba(255,255,255,.09);

            transform:
                translateY(-2px);
        }

        .gallery-confirm-btn {

            display: flex;

            align-items: center;
            justify-content: center;

            gap: 6px;

            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    #e53955,
                    #ff526b
                );

            border: none;

            box-shadow:
                0 8px 25px
                rgba(229,57,85,.22);
        }

        .gallery-confirm-btn:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 12px 32px
                rgba(229,57,85,.35);
        }

        body.gallery-modal-open {
            overflow: hidden;
        }


        /* =========================
           MOBILE
        ========================= */

        @media(max-width:768px) {

            .page-content {
                padding: 20px 16px 50px;
            }

            .page-header-top {
                align-items: flex-start;
                flex-direction: column;
            }

            .page-title-area {
                width: 100%;
            }

            .page-title-area h2 {
                font-size: 24px;
            }

            .header-actions {
                width: 100%;

                display: grid;

                grid-template-columns:
                    1fr 1fr;
            }

            .header-action-btn {
                width: 100%;
            }

            .project-form {
                padding: 22px 18px;
                border-radius: 18px;
            }

            .gallery-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .gallery-item img {
                height: 125px;
            }

            .form-actions {
                flex-direction: column;
            }

            .update-btn,
            .back-btn {
                width: 100%;
            }

            .current-image img {
                width: 100%;
                max-width: 300px;
                height: 170px;
            }

            .gallery-delete-box {
                padding: 28px 22px;
            }
        }

        @media(max-width:480px) {

            .gallery-grid {
                grid-template-columns: 1fr;
            }

            .gallery-item img {
                height: 190px;
            }

            .gallery-delete-actions {
                flex-direction: column;
            }
        }


        /* =========================
           REDUCED MOTION
        ========================= */

        @media(prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
            }
        }

    </style>

</head>


<body>


<!-- ================= SIDEBAR ================= -->

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

        <li class="active">
            <a href="project-list.php">
                <i class="ri-folder-fill"></i>
                Projects
            </a>
        </li>

        <li>
            <a href="about.php">
                <i class="ri-user-3-fill"></i>
                About
            </a>
        </li>

        <li>
            <a href="skills.php">
                <i class="ri-code-box-fill"></i>
                Skills
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


<!-- ================= MAIN ================= -->

<div class="main">


    <div class="topbar">

        <h2>
            Edit Project
        </h2>

    </div>


    <div class="page-content">


        <!-- ================= PAGE HEADER ================= -->

        <div class="page-header">

            <div class="page-header-top">


                <div class="page-title-area">

                    <div class="page-title-icon">

                        <i class="ri-edit-2-line"></i>

                    </div>

                    <div>

                        <h2>
                            Edit Project
                        </h2>

                        <p>
                            Update your project information.
                        </p>

                    </div>

                </div>


                <div class="header-actions">

                    <a
                        href="project-list.php"
                        class="header-action-btn projects-btn"
                    >

                        <i class="ri-folder-line"></i>

                        Projects

                    </a>


                    <a
                        href="dashboard.php"
                        class="header-action-btn dashboard-btn"
                    >

                        <i class="ri-dashboard-line"></i>

                        Dashboard

                    </a>

                </div>

            </div>

        </div>


        <!-- ================= SUCCESS ================= -->

        <?php if (!empty($success)): ?>

            <div class="project-message project-success">

                <div class="project-message-icon">

                    <i class="ri-checkbox-circle-fill"></i>

                </div>

                <div class="project-message-content">

                    <strong>
                        <?php echo htmlspecialchars($success); ?>
                    </strong>

                    <span>
                        Your project details have been updated successfully.
                    </span>

                </div>

            </div>

        <?php endif; ?>


        <!-- ================= ERROR ================= -->

        <?php if (!empty($error)): ?>

            <div class="project-message project-error">

                <div class="project-message-icon">

                    <i class="ri-error-warning-fill"></i>

                </div>

                <div class="project-message-content">

                    <strong>
                        Update Failed
                    </strong>

                    <span>
                        <?php echo htmlspecialchars($error); ?>
                    </span>

                </div>

            </div>

        <?php endif; ?>


        <!-- ================= FORM ================= -->

        <form
            method="POST"
            enctype="multipart/form-data"
            class="project-form"
        >


            <!-- TITLE -->

            <div class="form-group">

                <label>
                    <i class="ri-price-tag-3-line"></i>
                    Project Title
                </label>

                <input
                    type="text"
                    name="title"
                    value="<?php echo htmlspecialchars($project['title']); ?>"
                    required
                >

            </div>


            <!-- DESCRIPTION -->

            <div class="form-group">

                <label>
                    <i class="ri-file-text-line"></i>
                    Description
                </label>

                <textarea
                    name="description"
                    rows="6"
                    required
                ><?php echo htmlspecialchars($project['description']); ?></textarea>

            </div>


            <!-- TECHNOLOGIES -->

            <div class="form-group">

                <label>
                    <i class="ri-code-s-slash-line"></i>
                    Technologies
                </label>

                <input
                    type="text"
                    name="technologies"
                    value="<?php echo htmlspecialchars($project['technologies']); ?>"
                    placeholder="HTML, CSS, PHP, Laravel"
                >

            </div>


            <!-- GITHUB -->

            <div class="form-group">

                <label>
                    <i class="ri-github-line"></i>
                    GitHub Link
                </label>

                <input
                    type="text"
                    name="github"
                    value="<?php echo htmlspecialchars($project['github']); ?>"
                    placeholder="https://github.com/..."
                >

            </div>


            <!-- LIVE DEMO -->

            <div class="form-group">

                <label>
                    <i class="ri-external-link-line"></i>
                    Live Demo Link
                </label>

                <input
                    type="text"
                    name="demo"
                    value="<?php echo htmlspecialchars($project['live_demo']); ?>"
                    placeholder="https://example.com/..."
                >

            </div>


            <!-- CURRENT MAIN IMAGE -->

            <div class="form-group">

                <label>

                    <i class="ri-image-line"></i>

                    Current Project Image

                </label>


                <div class="current-image">

                    <?php if (!empty($project['image'])): ?>

                        <img
                            src="uploads/projects/<?php echo htmlspecialchars($project['image']); ?>"
                            alt="Project Image"
                        >

                    <?php else: ?>

                        <p class="no-image">
                            No image uploaded.
                        </p>

                    <?php endif; ?>

                </div>

            </div>


            <!-- CHANGE MAIN IMAGE -->

            <div class="form-group">

                <label>

                    <i class="ri-image-add-line"></i>

                    Change Project Image

                </label>


                <input
                    class="file-input"
                    type="file"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >


                <small>
                    Leave empty if you want to keep the current image.
                </small>

            </div>


            <!-- =========================
                 ADDITIONAL PHOTOS
            ========================= -->

            <div class="form-group gallery-section">

                <label>

                    <i class="ri-gallery-line"></i>

                    Additional Project Photos

                </label>


                <?php if (
                    $gallery_result &&
                    mysqli_num_rows($gallery_result) > 0
                ): ?>


                    <div class="gallery-grid">

                        <?php while (
                            $gallery =
                            mysqli_fetch_assoc($gallery_result)
                        ): ?>

                            <div class="gallery-item">


                                <img
                                    src="uploads/projects/<?php echo htmlspecialchars($gallery['image']); ?>"
                                    alt="Additional Project Photo"
                                >


                                <!-- CUSTOM MODAL BUTTON -->

                                <button
                                    type="button"
                                    class="gallery-delete"
                                    onclick="openGalleryDeleteModal(<?php echo intval($gallery['id']); ?>)"
                                >

                                    <i class="ri-delete-bin-line"></i>

                                    Delete

                                </button>


                            </div>

                        <?php endwhile; ?>

                    </div>


                    <div class="gallery-count">

                        <i class="ri-image-line"></i>

                        Existing additional photos

                    </div>


                <?php else: ?>


                    <div class="gallery-empty">

                        <i class="ri-image-line"></i>

                        No additional photos uploaded yet.

                    </div>


                <?php endif; ?>

            </div>


            <!-- =========================
                 UPLOAD ADDITIONAL PHOTOS
            ========================= -->

            <div class="form-group">

                <label>

                    <i class="ri-image-add-line"></i>

                    Add More Project Photos

                </label>


                <input
                    class="file-input"
                    type="file"
                    name="gallery_images[]"
                    accept=".jpg,.jpeg,.png,.webp"
                    multiple
                >


                <small>

                    You can select multiple photos.
                    Hold CTRL while selecting files.

                </small>

            </div>


            <!-- STATUS -->

            <div class="form-group">

                <label>

                    <i class="ri-checkbox-circle-line"></i>

                    Project Status

                </label>


                <select name="status">

                    <option
                        value="Completed"
                        <?php
                        if ($project['status'] == 'Completed') {
                            echo 'selected';
                        }
                        ?>
                    >
                        Completed
                    </option>


                    <option
                        value="Under Development"
                        <?php
                        if ($project['status'] == 'Under Development') {
                            echo 'selected';
                        }
                        ?>
                    >
                        Under Development
                    </option>

                </select>

            </div>


            <!-- BUTTONS -->

            <div class="form-actions">

                <button
                    type="submit"
                    name="update"
                    class="update-btn"
                >

                    <i class="ri-save-line"></i>

                    Update Project

                </button>


                <a
                    href="project-list.php"
                    class="back-btn"
                >

                    <i class="ri-folder-line"></i>

                    Back to Projects

                </a>

            </div>


        </form>


    </div>

</div>


<!-- =====================================================
     CUSTOM GALLERY DELETE MODAL
===================================================== -->

<div
    class="gallery-delete-modal"
    id="galleryDeleteModal"
>

    <div
        class="gallery-delete-overlay"
        onclick="closeGalleryDeleteModal()"
    ></div>


    <div class="gallery-delete-box">


        <div class="gallery-delete-icon">

            <i class="ri-delete-bin-line"></i>

        </div>


        <h3>
            Delete Photo?
        </h3>


        <p>
            Are you sure you want to delete this additional photo?
            This action cannot be undone.
        </p>


        <div class="gallery-delete-actions">

            <button
                type="button"
                class="gallery-cancel-btn"
                onclick="closeGalleryDeleteModal()"
            >

                Cancel

            </button>


            <button
                type="button"
                class="gallery-confirm-btn"
                onclick="confirmGalleryDelete()"
            >

                <i class="ri-delete-bin-line"></i>

                Yes, Delete

            </button>

        </div>


    </div>

</div>


<!-- =====================================================
     CUSTOM MODAL JAVASCRIPT
===================================================== -->

<script>

    let galleryDeleteId = null;


    function openGalleryDeleteModal(id) {

        galleryDeleteId = id;

        const modal =
            document.getElementById("galleryDeleteModal");

        modal.classList.add("active");

        document.body.classList.add(
            "gallery-modal-open"
        );
    }


    function closeGalleryDeleteModal() {

        galleryDeleteId = null;

        const modal =
            document.getElementById("galleryDeleteModal");

        modal.classList.remove("active");

        document.body.classList.remove(
            "gallery-modal-open"
        );
    }


    function confirmGalleryDelete() {

        if (!galleryDeleteId) {
            return;
        }

        window.location.href =
            "project-image-delete.php?id=" +
            galleryDeleteId;
    }


    document.addEventListener(
        "keydown",
        function(event) {

            if (event.key === "Escape") {

                closeGalleryDeleteModal();

            }

        }
    );

</script>


</body>

</html>