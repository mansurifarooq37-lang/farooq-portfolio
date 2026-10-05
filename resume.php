<?php

include("config/db.php");

/* ================= RESUME DATA ================= */

$query = "SELECT * FROM resume LIMIT 1";
$result = mysqli_query($conn, $query);

$resume = mysqli_fetch_assoc($result);

if (!$resume) {
    die("Resume data not found.");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($resume['name']); ?> | Resume
    </title>

    <!-- Remix Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet"
    >

    <!-- Resume CSS -->
    <link rel="stylesheet" href="assets/css/resume.css">

</head>

<body>

<div class="resume-container">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <header class="resume-header">

        <div class="header-left">

            <div class="header-badge">
                <i class="ri-code-s-slash-line"></i>
            </div>

            <div>

                <h1>
                    <?php echo htmlspecialchars($resume['name']); ?>
                </h1>

                <h2>
                    Full Stack Developer
                </h2>

                <p class="location">
                    <i class="ri-map-pin-line"></i>
                    <?php echo htmlspecialchars($resume['location']); ?>
                </p>

            </div>

        </div>


        <div class="contact-details">

            <p>
                <i class="ri-mail-line"></i>
                <?php echo htmlspecialchars($resume['email']); ?>
            </p>

            <p>
                <i class="ri-phone-line"></i>
                <?php echo htmlspecialchars($resume['phone']); ?>
            </p>

        </div>

    </header>



    <!-- =====================================================
         MAIN RESUME CONTENT
    ====================================================== -->

    <div class="resume-content">


        <!-- =================================================
             LEFT COLUMN
        ================================================== -->

        <main class="resume-main">


            <!-- ================= PROFILE ================= -->

            <section class="resume-section">

                <div class="section-title">

                    <span class="section-icon">
                        <i class="ri-user-line"></i>
                    </span>

                    <h2>
                        Profile
                    </h2>

                </div>

                <p class="profile-text">
                    <?php
                    echo nl2br(
                        htmlspecialchars($resume['about'])
                    );
                    ?>
                </p>

            </section>



            <!-- ================= EDUCATION ================= -->

            <section class="resume-section">

                <div class="section-title">

                    <span class="section-icon">
                        <i class="ri-graduation-cap-line"></i>
                    </span>

                    <h2>
                        Education
                    </h2>

                </div>


                <div class="resume-item">

                    <div class="item-dot"></div>

                    <div class="item-content">

                        <h3>
                            Bachelor of Arts (B.A.)
                        </h3>

                        <p>
                            Delhi University – School of Open Learning (DU SOL)
                        </p>

                        <span class="item-status">
                            2nd Year – Pursuing
                        </span>

                    </div>

                </div>

            </section>



            <!-- ================= PROFESSIONAL COURSE ================= -->

            <section class="resume-section">

                <div class="section-title">

                    <span class="section-icon">
                        <i class="ri-computer-line"></i>
                    </span>

                    <h2>
                        Professional Course
                    </h2>

                </div>


                <div class="resume-item">

                    <div class="item-dot"></div>

                    <div class="item-content">

                        <h3>
                            <?php
                            echo htmlspecialchars(
                                $resume['course']
                            );
                            ?>
                        </h3>

                        <p>
                            Advanced Diploma in Website Engineering
                        </p>

                        <span class="item-status">
                            Pursuing
                        </span>

                    </div>

                </div>

            </section>



            <!-- ================= EXPERIENCE ================= -->

            <section class="resume-section">

                <div class="section-title">

                    <span class="section-icon">
                        <i class="ri-briefcase-line"></i>
                    </span>

                    <h2>
                        Experience
                    </h2>

                </div>


                <div class="resume-item">

                    <div class="item-dot"></div>

                    <div class="item-content">

                        <div class="item-heading">

                            <h3>
                                Fresher
                            </h3>

                            <span class="experience-badge">
                                Entry Level
                            </span>

                        </div>

                        <p>
                            Currently seeking an opportunity to start a
                            professional career as a Full Stack Web Developer.
                        </p>

                    </div>

                </div>

            </section>



            <!-- ================= PROJECTS ================= -->

            <section class="resume-section">

                <div class="section-title">

                    <span class="section-icon">
                        <i class="ri-folder-3-line"></i>
                    </span>

                    <h2>
                        Projects
                    </h2>

                </div>


                <div class="projects-list">

                    <?php

                    $project_query = "
                        SELECT *
                        FROM projects
                        ORDER BY id DESC
                        LIMIT 4
                    ";

                    $project_result = mysqli_query(
                        $conn,
                        $project_query
                    );


                    if (
                        $project_result &&
                        mysqli_num_rows($project_result) > 0
                    ) {

                        while (
                            $project =
                            mysqli_fetch_assoc($project_result)
                        ) {

                    ?>

                        <div class="project-card">

                            <div class="project-top">

                                <div class="project-icon">

                                    <i class="ri-code-box-line"></i>

                                </div>

                                <div>

                                    <h3>
                                        <?php
                                        echo htmlspecialchars(
                                            $project['title']
                                        );
                                        ?>
                                    </h3>

                                    <?php if (!empty($project['status'])) { ?>

                                        <span class="project-status">

                                            <?php
                                            echo htmlspecialchars(
                                                $project['status']
                                            );
                                            ?>

                                        </span>

                                    <?php } ?>

                                </div>

                            </div>


                            <p class="project-description">

                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $project['description']
                                    )
                                );
                                ?>

                            </p>


                            <?php if (!empty($project['technologies'])) { ?>

                                <div class="project-tech">

                                    <?php

                                    $technologies =
                                        explode(
                                            ",",
                                            $project['technologies']
                                        );

                                    foreach (
                                        $technologies
                                        as $technology
                                    ) {

                                        $technology =
                                            trim($technology);

                                        if (
                                            !empty($technology)
                                        ) {

                                    ?>

                                        <span>
                                            <?php
                                            echo htmlspecialchars(
                                                $technology
                                            );
                                            ?>
                                        </span>

                                    <?php

                                        }

                                    }

                                    ?>

                                </div>

                            <?php } ?>

                        </div>

                    <?php

                        }

                    } else {

                    ?>

                        <p class="empty-projects">
                            No projects added yet.
                        </p>

                    <?php

                    }

                    ?>

                </div>

            </section>

        </main>



        <!-- =================================================
             RIGHT COLUMN
        ================================================== -->

        <aside class="resume-sidebar">


            <!-- ================= TECHNICAL SKILLS ================= -->

            <section class="resume-section">

                <div class="section-title">

                    <span class="section-icon">
                        <i class="ri-tools-line"></i>
                    </span>

                    <h2>
                        Technical Skills
                    </h2>

                </div>


                <div class="skills">

                    <?php

                    $skills =
                        explode(
                            ",",
                            $resume['skills']
                        );

                    foreach (
                        $skills
                        as $skill
                    ) {

                        $skill =
                            trim($skill);

                        if (!empty($skill)) {

                    ?>

                        <span class="skill-pill">

                            <i class="ri-check-line"></i>

                            <?php
                            echo htmlspecialchars($skill);
                            ?>

                        </span>

                    <?php

                        }

                    }

                    ?>

                </div>

            </section>



            <!-- ================= ACHIEVEMENTS ================= -->

            <section class="resume-section achievements-section">

                <div class="section-title">

                    <span class="section-icon">
                        <i class="ri-trophy-fill"></i>
                    </span>

                    <h2>
                        Achievements
                    </h2>

                </div>


                <div class="achievements-list">

                    <?php

                    $achievements =
                        $resume['achievements'] ?? '';


                    /*
                     * IMPORTANT:
                     * Correct line-break separator
                     */

                    $achievement_list =
                        preg_split(
                            '/\r\n|\r|\n/',
                            $achievements
                        );


                    foreach (
                        $achievement_list
                        as $achievement
                    ) {

                        $achievement =
                            trim($achievement);


                        if (
                            !empty($achievement)
                        ) {

                    ?>

                        <div class="achievement-card">

                            <div class="achievement-icon">

                                <i class="ri-award-fill"></i>

                            </div>


                            <div class="achievement-content">

                                <h3>
                                    Achievement
                                </h3>

                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $achievement
                                    );
                                    ?>
                                </p>

                            </div>

                        </div>

                    <?php

                        }

                    }

                    ?>

                </div>

            </section>



            <!-- ================= RESUME HIGHLIGHT ================= -->

            <div class="resume-highlight">

                <div class="highlight-icon">

                    <i class="ri-code-s-slash-line"></i>

                </div>

                <div>

                    <h3>
                        Full Stack Developer
                    </h3>

                    <p>
                        Building modern,
                        responsive and
                        dynamic web applications.
                    </p>

                </div>

            </div>


        </aside>

    </div>



    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer class="resume-footer">

        <div class="footer-line"></div>

        <div class="resume-actions">

            <button onclick="window.print()">

                <i class="ri-printer-line"></i>

                Print / Save PDF

            </button>


            <a href="index.php">

                <i class="ri-arrow-left-line"></i>

                Back to Portfolio

            </a>

        </div>


        <p class="footer-note">

            <?php
            echo htmlspecialchars(
                $resume['name']
            );
            ?>
            • Full Stack Developer

        </p>

    </footer>


</div>

</body>

</html>