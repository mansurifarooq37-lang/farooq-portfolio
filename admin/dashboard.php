<?php

session_start();

/* =========================================================
   ADMIN AUTHENTICATION
========================================================= */

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}


/* =========================================================
   DATABASE
========================================================= */

include("../config/db.php");


/* =========================================================
   COUNTS
========================================================= */

/* Projects Count */

$project_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM projects"
);

$project_count = mysqli_fetch_assoc(
    $project_query
)['total'];


/* Skills Count */

$skill_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM skills"
);

$skill_count = mysqli_fetch_assoc(
    $skill_query
)['total'];


/* Messages Count */

$message_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM messages"
);

$message_count = mysqli_fetch_assoc(
    $message_query
)['total'];


/* =========================================================
   RECENT PROJECTS
========================================================= */

$recent_projects = mysqli_query(
    $conn,
    "SELECT id, title, status, created_at
     FROM projects
     ORDER BY id DESC
     LIMIT 5"
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

    <title>Admin Dashboard</title>


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


    <!-- =====================================================
         LOGOUT MODAL CSS
    ====================================================== -->

    <style>

        .logout-modal {
            position: fixed;
            inset: 0;

            display: none;

            align-items: center;
            justify-content: center;

            z-index: 999999;
        }


        .logout-modal.active {
            display: flex;
        }


        .logout-modal-overlay {
            position: absolute;

            inset: 0;

            background: rgba(5, 5, 15, 0.78);

            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }


        .logout-modal-box {

            position: relative;

            z-index: 2;

            width: min(420px, calc(100% - 30px));

            padding: 32px;

            border-radius: 22px;

            text-align: center;

            background:
                linear-gradient(
                    145deg,
                    #19172f,
                    #0c0b18
                );

            border: 1px solid rgba(108, 99, 255, 0.28);

            box-shadow:
                0 30px 90px rgba(0, 0, 0, 0.65),
                0 0 45px rgba(108, 99, 255, 0.08);

            animation: logoutModalShow 0.28s ease;
        }


        @keyframes logoutModalShow {

            from {
                opacity: 0;
                transform: translateY(18px) scale(0.94);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

        }


        /* MODAL ICON */

        .logout-modal-icon {

            width: 68px;
            height: 68px;

            margin: 0 auto 18px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: rgba(239, 68, 68, 0.11);

            border: 1px solid rgba(239, 68, 68, 0.22);

            color: #ff6b6b;

            font-size: 30px;

            box-shadow:
                0 0 30px rgba(239, 68, 68, 0.08);
        }


        /* TITLE */

        .logout-modal-box h3 {

            margin: 0 0 10px;

            color: #ffffff;

            font-size: 23px;

            font-weight: 800;
        }


        /* TEXT */

        .logout-modal-box p {

            margin: 0;

            color: rgba(255, 255, 255, 0.55);

            font-size: 13px;

            line-height: 1.7;
        }


        /* BUTTONS */

        .logout-modal-actions {

            display: flex;

            justify-content: center;

            gap: 12px;

            margin-top: 27px;
        }


        .logout-cancel,
        .logout-confirm {

            height: 44px;

            padding: 0 21px;

            border-radius: 11px;

            font-family: inherit;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.25s ease;
        }


        /* CANCEL */

        .logout-cancel {

            background: rgba(255, 255, 255, 0.06);

            color: rgba(255, 255, 255, 0.75);

            border: 1px solid rgba(255, 255, 255, 0.09);
        }


        .logout-cancel:hover {

            background: rgba(255, 255, 255, 0.11);

            transform: translateY(-1px);
        }


        /* CONFIRM */

        .logout-confirm {

            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 7px;

            border: none;

            background:
                linear-gradient(
                    135deg,
                    #ef4444,
                    #dc2626
                );

            color: #ffffff;

            box-shadow:
                0 9px 25px rgba(239, 68, 68, 0.22);
        }


        .logout-confirm:hover {

            transform: translateY(-2px);

            box-shadow:
                0 13px 30px rgba(239, 68, 68, 0.35);
        }


        .logout-confirm i {

            font-size: 16px;
        }


        /* MOBILE */

        @media (max-width: 500px) {

            .logout-modal-box {

                padding: 27px 20px;

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


<!-- =========================================================
     SIDEBAR
========================================================= -->

<div class="sidebar">


    <!-- LOGO -->

    <div class="logo">

        <h2>
            Farooq<span>.</span>
        </h2>

    </div>


    <!-- NAVIGATION -->

    <ul>


        <!-- DASHBOARD -->

        <li class="active">

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

        <li>

            <a href="messages.php">

                <i class="ri-message-3-fill"></i>

                Messages

            </a>

        </li>


        <!-- SETTINGS -->

        <li>

            <a href="settings.php">

                <i class="ri-settings-3-fill"></i>

                Settings

            </a>

        </li>


        <!-- ADMIN PROFILE -->

        <li>

            <a href="profile.php">

                <i class="ri-user-settings-fill"></i>

                Admin Profile

            </a>

        </li>


        <!-- LOGOUT -->

        <li>

            <a
                href="javascript:void(0);"
                onclick="openLogoutModal()"
            >

                <i class="ri-logout-box-r-fill"></i>

                Logout

            </a>

        </li>


    </ul>

</div>



<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<div class="main">


    <!-- =====================================================
         EPIC BACKGROUND
    ====================================================== -->

    <div class="epic-bg">

        <div class="energy-ring ring-1"></div>

        <div class="energy-ring ring-2"></div>

        <div class="energy-ring ring-3"></div>

        <div class="energy-core"></div>

        <div class="particle p1"></div>
        <div class="particle p2"></div>
        <div class="particle p3"></div>
        <div class="particle p4"></div>
        <div class="particle p5"></div>
        <div class="particle p6"></div>
        <div class="particle p7"></div>
        <div class="particle p8"></div>

        <div class="shooting-line"></div>

        <div class="shooting-line line-2"></div>

    </div>



    <!-- =====================================================
         TOPBAR
    ====================================================== -->

    <div class="topbar">

        <h2>
            Dashboard
        </h2>


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



    <!-- =====================================================
         WELCOME BANNER
    ====================================================== -->

    <div class="welcome-banner">

        <div class="welcome-content">

            <span class="welcome-tag">

                <i class="ri-code-s-slash-line"></i>

                ADMIN PANEL

            </span>


            <h1>

                Welcome back,

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $_SESSION['admin']
                    );

                    ?>

                </strong>

                👋

            </h1>


            <p>

                Manage your portfolio, projects,
                skills, resume and messages from one place.

            </p>

        </div>


        <div class="welcome-icon">

            <i class="ri-terminal-box-line"></i>

        </div>

    </div>



    <!-- =====================================================
         STAT CARDS
    ====================================================== -->

    <div class="cards">


        <!-- PROJECTS -->

        <div class="card">

            <div class="card-shine"></div>

            <i class="ri-folder-fill"></i>

            <h3>
                Projects
            </h3>

            <span>

                <?php

                echo $project_count;

                ?>

            </span>

        </div>



        <!-- SKILLS -->

        <div class="card">

            <div class="card-shine"></div>

            <i class="ri-code-box-fill"></i>

            <h3>
                Skills
            </h3>

            <span>

                <?php

                echo $skill_count;

                ?>

            </span>

        </div>



        <!-- MESSAGES -->

        <div class="card">

            <div class="card-shine"></div>

            <i class="ri-message-fill"></i>

            <h3>
                Messages
            </h3>

            <span>

                <?php

                echo $message_count;

                ?>

            </span>

        </div>



        <!-- RESUME -->

        <div class="card">

            <div class="card-shine"></div>

            <i class="ri-file-paper-2-fill"></i>

            <h3>
                Resume
            </h3>

            <span>
                Active
            </span>

        </div>

    </div>



    <!-- =====================================================
         QUICK ACTION
    ====================================================== -->

    <div class="quick-action">

        <a href="project-add.php">

            <i class="ri-add-circle-fill"></i>

            Add New Project

        </a>

    </div>



    <!-- =====================================================
         RECENT PROJECTS
    ====================================================== -->

    <div class="recent-projects">


        <!-- HEADER -->

        <div class="recent-header">

            <h3>
                Recent Projects
            </h3>


            <a href="project-list.php">
                View All →
            </a>

        </div>



        <?php

        if (
            mysqli_num_rows(
                $recent_projects
            ) > 0
        ) {

        ?>


            <!-- PROJECT TABLE -->

            <table class="recent-table">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Project
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Date
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php

                    while (
                        $project =
                        mysqli_fetch_assoc(
                            $recent_projects
                        )
                    ) {

                    ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $project['id']
                                );

                                ?>

                            </td>



                            <!-- PROJECT -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $project['title']
                                );

                                ?>

                            </td>



                            <!-- STATUS -->

                            <td>

                                <?php

                                if (
                                    $project['status']
                                    === "Completed"
                                ) {

                                ?>

                                    <span
                                        class="status-active"
                                    >

                                        ● Completed

                                    </span>

                                <?php

                                } else {

                                ?>

                                    <span
                                        class="status-inactive"
                                    >

                                        ● Under Development

                                    </span>

                                <?php

                                }

                                ?>

                            </td>



                            <!-- DATE -->

                            <td>

                                <?php

                                if (
                                    !empty(
                                        $project['created_at']
                                    )
                                ) {

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $project['created_at']
                                        )
                                    );

                                } else {

                                    echo "—";

                                }

                                ?>

                            </td>


                        </tr>


                    <?php

                    }

                    ?>


                </tbody>

            </table>


        <?php

        } else {

        ?>


            <!-- EMPTY PROJECT -->

            <div class="empty-project">

                <i
                    class="ri-folder-open-line"
                    style="font-size:35px;"
                >
                </i>


                <p>
                    No projects added yet.
                </p>


                <a href="project-add.php">

                    Add your first project

                </a>

            </div>


        <?php

        }

        ?>


    </div>


</div>



<!-- =========================================================
     LOGOUT CONFIRMATION MODAL
========================================================= -->

<div
    class="logout-modal"
    id="logoutModal"
>

    <!-- Overlay -->

    <div
        class="logout-modal-overlay"
        onclick="closeLogoutModal()"
    ></div>


    <!-- Modal Box -->

    <div class="logout-modal-box">


        <!-- Icon -->

        <div class="logout-modal-icon">

            <i class="ri-logout-box-r-line"></i>

        </div>


        <!-- Title -->

        <h3>
            Logout?
        </h3>


        <!-- Message -->

        <p>

            Are you sure you want to logout
            from the admin panel?

        </p>


        <!-- Buttons -->

        <div class="logout-modal-actions">


            <!-- CANCEL -->

            <button
                type="button"
                class="logout-cancel"
                onclick="closeLogoutModal()"
            >

                Cancel

            </button>


            <!-- CONFIRM -->

            <button
                type="button"
                class="logout-confirm"
                onclick="confirmLogout()"
            >

                <i class="ri-logout-box-r-line"></i>

                Yes, Logout

            </button>


        </div>


    </div>

</div>



<!-- =========================================================
     LOGOUT MODAL JAVASCRIPT
========================================================= -->

<script>

function openLogoutModal() {

    const modal =
        document.getElementById("logoutModal");

    modal.classList.add("active");

}


function closeLogoutModal() {

    const modal =
        document.getElementById("logoutModal");

    modal.classList.remove("active");

}


function confirmLogout() {

    window.location.href = "logout.php";

}


/* ESC KEY */

document.addEventListener(
    "keydown",
    function(event) {

        if (event.key === "Escape") {

            closeLogoutModal();

        }

    }
);

</script>


</body>

</html>