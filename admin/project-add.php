<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/db.php");

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title        = trim($_POST['title'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $github       = trim($_POST['github'] ?? '');
    $demo         = trim($_POST['demo'] ?? '');
    $status       = trim($_POST['status'] ?? 'Completed');

    /* =========================
       MAIN IMAGE
    ========================= */

    $main_image = "";

    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] === UPLOAD_ERR_OK
    ) {

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

        $extension = strtolower(
            pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowed_extensions)) {

            $error = "Invalid main image format.";

        } else {

            $upload_dir = "uploads/projects/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $main_image =
                time() . "_" .
                uniqid() . "." .
                $extension;

            $main_path = $upload_dir . $main_image;

            if (
                !move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    $main_path
                )
            ) {
                $error = "Failed to upload main image.";
                $main_image = "";
            }
        }

    } else {

        $error = "Please select a main project image.";
    }


    /* =========================
       INSERT PROJECT
    ========================= */

    if (empty($error)) {

        $title        = mysqli_real_escape_string($conn, $title);
        $description  = mysqli_real_escape_string($conn, $description);
        $technologies = mysqli_real_escape_string($conn, $technologies);
        $github       = mysqli_real_escape_string($conn, $github);
        $demo         = mysqli_real_escape_string($conn, $demo);
        $main_image   = mysqli_real_escape_string($conn, $main_image);
        $status       = mysqli_real_escape_string($conn, $status);

        $query = "
            INSERT INTO projects
            (
                title,
                description,
                image,
                technologies,
                github,
                live_demo,
                status
            )
            VALUES
            (
                '$title',
                '$description',
                '$main_image',
                '$technologies',
                '$github',
                '$demo',
                '$status'
            )
        ";

        if (mysqli_query($conn, $query)) {

            $project_id = mysqli_insert_id($conn);


            /* =========================
               GALLERY IMAGES
            ========================= */

            if (
                isset($_FILES['gallery_images']) &&
                !empty($_FILES['gallery_images']['name'][0])
            ) {

                $gallery_count =
                    count($_FILES['gallery_images']['name']);

                $allowed_extensions =
                    ['jpg', 'jpeg', 'png', 'webp'];

                $gallery_dir =
                    "uploads/projects/";

                for ($i = 0; $i < $gallery_count; $i++) {

                    if (
                        $_FILES['gallery_images']['error'][$i]
                        !== UPLOAD_ERR_OK
                    ) {
                        continue;
                    }

                    $original_name =
                        $_FILES['gallery_images']['name'][$i];

                    $tmp_name =
                        $_FILES['gallery_images']['tmp_name'][$i];

                    $extension =
                        strtolower(
                            pathinfo(
                                $original_name,
                                PATHINFO_EXTENSION
                            )
                        );

                    if (
                        !in_array(
                            $extension,
                            $allowed_extensions
                        )
                    ) {
                        continue;
                    }

                    $gallery_image =
                        time() . "_" .
                        uniqid() . "_" .
                        $i . "." .
                        $extension;

                    $gallery_path =
                        $gallery_dir . $gallery_image;

                    if (
                        move_uploaded_file(
                            $tmp_name,
                            $gallery_path
                        )
                    ) {

                        $gallery_image_db =
                            mysqli_real_escape_string(
                                $conn,
                                $gallery_image
                            );

                        $gallery_query = "
                            INSERT INTO project_images
                            (
                                project_id,
                                image
                            )
                            VALUES
                            (
                                '$project_id',
                                '$gallery_image_db'
                            )
                        ";

                        mysqli_query(
                            $conn,
                            $gallery_query
                        );
                    }
                }
            }


            /* =========================
               SUCCESS
            ========================= */

            $_SESSION['project_success'] =
                "Project added successfully!";

            header("Location: project-list.php");
            exit();

        } else {

            /* Delete uploaded main image
               if database insertion fails */

            if (
                !empty($main_image) &&
                file_exists(
                    "uploads/projects/" . $main_image
                )
            ) {

                unlink(
                    "uploads/projects/" . $main_image
                );
            }

            $error =
                "Failed to add project.";
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

    <title>Add Project | Farooq Admin</title>

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css"
        rel="stylesheet"
    >

    <style>

        .project-form-page {
            position: relative;
            z-index: 2;
            max-width: 1100px;
        }

        .project-form-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }

        .project-form-heading h2 {
            margin: 0 0 6px;
            color: #fff;
            font-size: 28px;
        }

        .project-form-heading p {
            margin: 0;
            color: #8f96aa;
            font-size: 13px;
        }

        .project-form-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .top-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 0 16px;
            border-radius: 11px;
            text-decoration: none;
            color: #dfe3ef;
            background: rgba(255,255,255,.045);
            border: 1px solid rgba(255,255,255,.08);
            font-size: 12px;
            font-weight: 700;
            transition: .25s ease;
        }

        .top-btn:hover {
            color: #fff;
            background: rgba(124,92,255,.14);
            border-color: rgba(124,92,255,.30);
            transform: translateY(-2px);
        }

        .project-form-card {
            padding: 30px;
            border-radius: 22px;
            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.055),
                    rgba(255,255,255,.018)
                );
            border: 1px solid rgba(255,255,255,.07);
            box-shadow: 0 25px 70px rgba(0,0,0,.25);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            color: #cfd4e2;
            font-size: 12px;
            font-weight: 700;
        }

        .form-group label i {
            margin-right: 5px;
            color: #9d88ff;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 12px;
            padding: 13px 14px;
            outline: none;
            color: #fff;
            background: rgba(8,10,18,.72);
            font-family: inherit;
            font-size: 13px;
            transition: .25s ease;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: rgba(124,92,255,.55);
            box-shadow:
                0 0 0 3px rgba(124,92,255,.08);
        }

        .form-group input::file-selector-button {
            margin-right: 12px;
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            color: #fff;
            background: linear-gradient(
                135deg,
                #7657ff,
                #5639db
            );
            cursor: pointer;
        }

        .file-help {
            color: #70788d;
            font-size: 11px;
        }

        .gallery-box {
            padding: 18px;
            border-radius: 15px;
            border: 1px dashed rgba(124,92,255,.30);
            background: rgba(124,92,255,.035);
        }

        .gallery-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 7px;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }

        .gallery-title i {
            color: #9d88ff;
            font-size: 18px;
        }

        .gallery-help {
            margin-bottom: 14px;
            color: #737b91;
            font-size: 11px;
            line-height: 1.6;
        }

        .form-error {
            margin-bottom: 22px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 12px;
            color: #ff7181;
            background: rgba(239,68,68,.08);
            border: 1px solid rgba(239,68,68,.20);
            font-size: 13px;
        }

        .form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 28px;
        }

        .save-btn,
        .cancel-btn {
            min-height: 46px;
            padding: 0 20px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: .25s ease;
        }

        .save-btn {
            color: #fff;
            border: 1px solid rgba(124,92,255,.35);
            background: linear-gradient(
                135deg,
                #7657ff,
                #5639db
            );
            box-shadow:
                0 10px 30px rgba(100,70,255,.18);
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow:
                0 15px 38px rgba(100,70,255,.30);
        }

        .cancel-btn {
            color: #c7cbd6;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.045);
        }

        .cancel-btn:hover {
            color: #fff;
            background: rgba(255,255,255,.09);
            transform: translateY(-2px);
        }

        @media(max-width:768px) {

            .project-form-topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .project-form-actions {
                width: 100%;
            }

            .top-btn {
                flex: 1;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .project-form-card {
                padding: 20px;
            }

            .form-buttons {
                flex-direction: column;
            }

            .save-btn,
            .cancel-btn {
                width: 100%;
            }
        }


        /* =====================================================
   LOGOUT MODAL FIX
===================================================== */

body.modal-open {
    overflow: hidden;
}

.logout-modal-overlay {
    position: fixed !important;
    inset: 0 !important;
    width: 100vw !important;
    height: 100vh !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    padding: 20px;

    background: rgba(3, 5, 12, 0.78) !important;
    backdrop-filter: blur(10px);

    opacity: 0;
    visibility: hidden;
    pointer-events: none;

    z-index: 99999 !important;

    transition:
        opacity .25s ease,
        visibility .25s ease;
}

.logout-modal-overlay.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.logout-modal {
    position: relative;

    width: min(420px, 100%);

    padding: 32px;

    border-radius: 22px;

    background:
        linear-gradient(
            145deg,
            rgba(20, 22, 35, 0.98),
            rgba(10, 12, 22, 0.98)
        ) !important;

    border: 1px solid rgba(124, 92, 255, 0.28);

    box-shadow:
        0 30px 100px rgba(0, 0, 0, .65),
        0 0 50px rgba(124, 92, 255, .10);

    text-align: center;

    transform: translateY(20px) scale(.96);

    transition:
        transform .3s ease,
        opacity .3s ease;
}

.logout-modal-overlay.active .logout-modal {
    transform: translateY(0) scale(1);
}

.logout-modal-close {
    position: absolute;

    top: 14px;
    right: 14px;

    width: 34px;
    height: 34px;

    border: 1px solid rgba(255,255,255,.08);
    border-radius: 9px;

    background: rgba(255,255,255,.05);

    color: #9da4b7;

    display: flex;
    align-items: center;
    justify-content: center;

    cursor: pointer;

    transition: .25s ease;
}

.logout-modal-close:hover {
    color: #fff;
    background: rgba(255,255,255,.10);
    transform: rotate(90deg);
}

.logout-modal-icon {
    width: 70px;
    height: 70px;

    margin: 0 auto 20px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 20px;

    color: #a98cff;

    background: rgba(124, 92, 255, .10);

    border: 1px solid rgba(124, 92, 255, .22);

    box-shadow:
        0 0 35px rgba(124, 92, 255, .12);

    font-size: 30px;
}

.logout-modal h2 {
    margin: 0 0 8px;

    color: #fff;

    font-size: 24px;
    font-weight: 800;
}

.logout-modal p {
    margin: 0 auto 26px;

    max-width: 320px;

    color: #8f96aa;

    font-size: 13px;
    line-height: 1.6;
}

.logout-modal-actions {
    display: flex;

    justify-content: center;

    gap: 12px;
}

.logout-cancel,
.logout-confirm {
    min-height: 44px;

    padding: 0 18px;

    border-radius: 11px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    font-family: inherit;
    font-size: 12px;
    font-weight: 700;

    cursor: pointer;

    text-decoration: none;

    transition: .25s ease;
}

.logout-cancel {
    color: #c8cdd9;

    background: rgba(255,255,255,.05);

    border: 1px solid rgba(255,255,255,.08);
}

.logout-cancel:hover {
    color: #fff;
    background: rgba(255,255,255,.10);

    transform: translateY(-2px);
}

.logout-confirm {
    color: #fff;

    background: linear-gradient(
        135deg,
        #7657ff,
        #5639db
    );

    border: 1px solid rgba(124,92,255,.35);

    box-shadow:
        0 10px 25px rgba(100,70,255,.20);
}

.logout-confirm:hover {
    transform: translateY(-2px);

    box-shadow:
        0 15px 35px rgba(100,70,255,.35);
}


/* MOBILE */

@media(max-width:500px) {

    .logout-modal {
        padding: 28px 20px;
    }

    .logout-modal-actions {
        flex-direction: column;
    }

    .logout-cancel,
    .logout-confirm {
        width: 100%;
    }

}

    </style>

</head>

<body>

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
            <a href="project-list.php">
                <i class="ri-folder-fill"></i>
                <span>Projects</span>
            </a>
        </li>

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


<div class="main project-form-page">

    <div class="project-form-topbar">

        <div class="project-form-heading">

            <h2>Add New Project</h2>

            <p>
                Add your project details and showcase your work.
            </p>

        </div>

        <div class="project-form-actions">

            <a
                href="dashboard.php"
                class="top-btn"
            >
                <i class="ri-dashboard-line"></i>
                Dashboard
            </a>

            <a
                href="project-list.php"
                class="top-btn"
            >
                <i class="ri-folder-line"></i>
                Projects
            </a>

        </div>

    </div>


    <?php if (!empty($error)): ?>

        <div class="form-error">

            <i class="ri-error-warning-fill"></i>

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <div class="project-form-card">

        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="form-grid">


                <!-- TITLE -->

                <div class="form-group">

                    <label>
                        <i class="ri-text"></i>
                        Project Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        placeholder="Enter project title"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['title'] ?? ''
                            );
                        ?>"
                        required
                    >

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label>
                        <i class="ri-checkbox-circle-line"></i>
                        Status
                    </label>

                    <select name="status">

                        <option
                            value="Completed"
                            <?php
                            echo (
                                ($_POST['status'] ?? 'Completed')
                                === 'Completed'
                            )
                            ? 'selected'
                            : '';
                            ?>
                        >
                            Completed
                        </option>

                        <option
                            value="Under Development"
                            <?php
                            echo (
                                ($_POST['status'] ?? '')
                                === 'Under Development'
                            )
                            ? 'selected'
                            : '';
                            ?>
                        >
                            Under Development
                        </option>

                    </select>

                </div>


                <!-- DESCRIPTION -->

                <div class="form-group full">

                    <label>
                        <i class="ri-file-text-line"></i>
                        Description
                    </label>

                    <textarea
                        name="description"
                        placeholder="Describe your project..."
                        required
                    ><?php
                        echo htmlspecialchars(
                            $_POST['description'] ?? ''
                        );
                    ?></textarea>

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
                        placeholder="HTML, CSS, JavaScript, Laravel..."
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['technologies'] ?? ''
                            );
                        ?>"
                    >

                </div>


                <!-- GITHUB -->

                <div class="form-group">

                    <label>
                        <i class="ri-github-line"></i>
                        GitHub URL
                    </label>

                    <input
                        type="url"
                        name="github"
                        placeholder="https://github.com/..."
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['github'] ?? ''
                            );
                        ?>"
                    >

                </div>


                <!-- LIVE DEMO -->

                <div class="form-group">

                    <label>
                        <i class="ri-global-line"></i>
                        Live Demo URL
                    </label>

                    <input
                        type="url"
                        name="demo"
                        placeholder="https://..."
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['demo'] ?? ''
                            );
                        ?>"
                    >

                </div>


                <!-- MAIN IMAGE -->

                <div class="form-group">

                    <label>
                        <i class="ri-image-line"></i>
                        Main Project Image
                    </label>

                    <input
                        type="file"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp"
                        required
                    >

                    <span class="file-help">
                        This will be the main/cover image.
                    </span>

                </div>


                <!-- GALLERY -->

                <div class="form-group full">

                    <div class="gallery-box">

                        <div class="gallery-title">

                            <i class="ri-gallery-line"></i>

                            Additional Project Photos

                        </div>

                        <div class="gallery-help">

                            Select multiple images for your project gallery.
                            Visitors will be able to view these photos
                            through the View Photos button.

                        </div>

                        <input
                            type="file"
                            name="gallery_images[]"
                            accept=".jpg,.jpeg,.png,.webp"
                            multiple
                        >

                    </div>

                </div>


            </div>


            <div class="form-buttons">

                <a
                    href="project-list.php"
                    class="cancel-btn"
                >
                    <i class="ri-arrow-left-line"></i>
                    Back to Projects
                </a>

                <button
                    type="submit"
                    class="save-btn"
                >
                    <i class="ri-add-line"></i>
                    Add Project
                </button>

            </div>

        </form>

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

        <h2>Logout?</h2>

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


<script>

function openLogoutModal() {

    const modal =
        document.getElementById("logoutModal");

    if (!modal) return;

    modal.classList.add("active");

    document.body.classList.add("modal-open");
}


function closeLogoutModal() {

    const modal =
        document.getElementById("logoutModal");

    if (!modal) return;

    modal.classList.remove("active");

    document.body.classList.remove("modal-open");
}


document.addEventListener(
    "keydown",
    function(e) {

        if (e.key === "Escape") {
            closeLogoutModal();
        }

    }
);


document.addEventListener(
    "click",
    function(e) {

        const modal =
            document.getElementById("logoutModal");

        if (
            modal &&
            e.target === modal
        ) {
            closeLogoutModal();
        }

    }
);

</script>

</body>
</html>