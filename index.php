<?php

include("config/db.php");

/* =========================================================
   SETTINGS FROM DATABASE
========================================================= */

$settings_query = "SELECT * FROM settings LIMIT 1";
$settings_result = mysqli_query($conn, $settings_query);
$settings = mysqli_fetch_assoc($settings_result);

$email = trim($settings['email'] ?? '');
$instagram = trim($settings['instagram'] ?? '');
$location = trim($settings['location'] ?? '');

/*
|--------------------------------------------------------------------------
| CLEAN EMAIL
|--------------------------------------------------------------------------
| Database me agar mailto: laga hua ho tab bhi correct link banega.
*/

$email = preg_replace('/^mailto:/i', '', $email);
$email = trim($email);


/*
|--------------------------------------------------------------------------
| CLEAN INSTAGRAM
|--------------------------------------------------------------------------
*/

$instagram_username = $instagram;

if (preg_match('/instagram\.com\/([^\/\?\#]+)/i', $instagram, $matches)) {
    $instagram_username = $matches[1];
}

$instagram_username = ltrim($instagram_username, '@');

$instagram_url = '';

if ($instagram_username !== '') {
    $instagram_url =
        'https://www.instagram.com/' .
        rawurlencode($instagram_username) .
        '/';
}


/* =========================================================
   ADMIN PROFILE PHOTO
========================================================= */

$admin_query = "SELECT profile_photo FROM admin LIMIT 1";
$admin_result = mysqli_query($conn, $admin_query);
$admin_data = mysqli_fetch_assoc($admin_result);

$profile_photo = $admin_data['profile_photo'] ?? '';

if (!empty($profile_photo)) {

    if (strpos($profile_photo, 'uploads/') === 0) {
        $profile_photo = 'admin/' . $profile_photo;
    }

} else {

    $profile_photo = 'assets/images/profile.jpeg';

}


/* =========================================================
   ABOUT FROM DATABASE
========================================================= */

$about_query = "SELECT * FROM about LIMIT 1";
$about_result = mysqli_query($conn, $about_query);
$about = mysqli_fetch_assoc($about_result);


/* =========================================================
   SKILLS FROM DATABASE
========================================================= */

$skills_query = "SELECT * FROM skills ORDER BY category, id DESC";
$skills_result = mysqli_query($conn, $skills_query);


/* =========================================================
   PROJECT GALLERY DATA
========================================================= */

$gallery_map = [];

$gallery_query = mysqli_query(
    $conn,
    "SELECT project_id, image
     FROM project_images
     ORDER BY id ASC"
);

if ($gallery_query) {

    while ($gallery = mysqli_fetch_assoc($gallery_query)) {

        $pid = (int)$gallery['project_id'];

        if (!isset($gallery_map[$pid])) {
            $gallery_map[$pid] = [];
        }

        $gallery_map[$pid][] =
            'admin/uploads/projects/' . $gallery['image'];
    }
}


/* =========================================================
   CONTACT FORM VALIDATION
========================================================= */

$contact_success = false;
$contact_error = false;
$contact_errors = [];

$name = '';
$email_input = '';
$phone = '';
$subject = '';
$message = '';


if (isset($_POST['send_message'])) {

    /* ================= GET FORM DATA ================= */

    $name = trim($_POST['name'] ?? '');
    $email_input = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');


    /* ================= NAME VALIDATION ================= */

    if ($name === '') {

        $contact_errors['name'] =
            "Name is required.";

    } elseif (strlen($name) < 2) {

        $contact_errors['name'] =
            "Name must contain at least 2 characters.";

    } elseif (strlen($name) > 50) {

        $contact_errors['name'] =
            "Name must not exceed 50 characters.";

    } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {

        $contact_errors['name'] =
            "Name can contain only letters and spaces.";

    }


    /* ================= PHONE VALIDATION ================= */

    if ($phone === '') {

        $contact_errors['phone'] =
            "Phone number is required.";

    } elseif (!preg_match("/^[0-9]+$/", $phone)) {

        $contact_errors['phone'] =
            "Phone number can contain only numbers.";

    } elseif (strlen($phone) !== 10) {

        $contact_errors['phone'] =
            "Phone number must contain exactly 10 digits.";

    }


    /* ================= EMAIL VALIDATION ================= */

    if ($email_input === '') {

        $contact_errors['email'] =
            "Email is required.";

    } elseif (strlen($email_input) > 100) {

        $contact_errors['email'] =
            "Email must not exceed 100 characters.";

    } elseif (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {

        $contact_errors['email'] =
            "Please enter a valid email address.";

    }


    /* ================= SUBJECT VALIDATION ================= */

    if ($subject === '') {

        $contact_errors['subject'] =
            "Subject is required.";

    } elseif (strlen($subject) < 3) {

        $contact_errors['subject'] =
            "Subject must contain at least 3 characters.";

    } elseif (strlen($subject) > 100) {

        $contact_errors['subject'] =
            "Subject must not exceed 100 characters.";

    } elseif (
        preg_match(
            '/<[^>]*>|<script|<\/script>|javascript:|<\?php|<\?=|<\?|\?>|eval\s*\(|alert\s*\(|document\.|window\.|onerror\s*=|onclick\s*=|onload\s*=/i',
            $subject
        )
    ) {

        $contact_errors['subject'] =
            "HTML, JavaScript or PHP code is not allowed in subject.";

    }


    /* ================= MESSAGE VALIDATION ================= */

    if ($message === '') {

        $contact_errors['message'] =
            "Message is required.";

    } elseif (strlen($message) < 5) {

        $contact_errors['message'] =
            "Message must contain at least 5 characters.";

    } elseif (strlen($message) > 1000) {

        $contact_errors['message'] =
            "Message must not exceed 1000 characters.";

    } elseif (
        preg_match(
            '/<[^>]*>|<script|<\/script>|javascript:|<\?php|<\?=|<\?|\?>|eval\s*\(|alert\s*\(|document\.|window\.|onerror\s*=|onclick\s*=|onload\s*=/i',
            $message
        )
    ) {

        $contact_errors['message'] =
            "HTML, JavaScript or PHP code is not allowed in message.";

    }


    /* =========================================================
       INSERT MESSAGE
    ========================================================= */

    if (empty($contact_errors)) {

        $name_safe =
            mysqli_real_escape_string($conn, $name);

        $email_safe =
            mysqli_real_escape_string($conn, $email_input);

        $phone_safe =
            mysqli_real_escape_string($conn, $phone);

        $subject_safe =
            mysqli_real_escape_string($conn, $subject);

        $message_safe =
            mysqli_real_escape_string($conn, $message);


        $query = "INSERT INTO messages
            (name, email, phone, subject, message)
            VALUES
            ('$name_safe',
             '$email_safe',
             '$phone_safe',
             '$subject_safe',
             '$message_safe')";


        if (mysqli_query($conn, $query)) {

            $contact_success = true;

            $name = '';
            $email_input = '';
            $phone = '';
            $subject = '';
            $message = '';

        } else {

            $contact_error = true;

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

    <title>
        Farooq Mansuri | Full Stack Developer
    </title>


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <!-- Google Font -->

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- Remix Icons -->

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
        rel="stylesheet"
    >

</head>


<body>


<!-- =========================================================
     ANIMATED BACKGROUND
========================================================= -->

<div class="animated-bg">

    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>
    <div class="glow glow-3"></div>

    <div class="code-symbol symbol-1">&lt;/&gt;</div>
    <div class="code-symbol symbol-2">{ }</div>
    <div class="code-symbol symbol-3">01</div>
    <div class="code-symbol symbol-4">PHP</div>
    <div class="code-symbol symbol-5">CSS</div>
    <div class="code-symbol symbol-6">&lt;?&gt;</div>

</div>


<canvas id="particles-canvas"></canvas>

<div class="mouse-glow"></div>


<!-- =========================================================
     NAVBAR
========================================================= -->

<header class="header">

    <div class="container navbar">

        <a href="#" class="logo">

            <img
                src="assets/images/logo.png"
                alt="FP Logo"
            >

        </a>


        <nav>

            <ul class="nav-links">

                <li>
                    <a href="#" class="active">Home</a>
                </li>

                <li>
                    <a href="#about">About</a>
                </li>

                <li>
                    <a href="#skills">Skills</a>
                </li>

                <li>
                    <a href="#projects">Projects</a>
                </li>

                <li>
                    <a href="#contact">Contact</a>
                </li>

            </ul>

        </nav>

    </div>

</header>


<!-- =========================================================
     HERO
========================================================= -->

<section class="hero">

    <div class="hero-bg">

        <span class="circle circle1"></span>
        <span class="circle circle2"></span>

    </div>


    <div class="container hero-container">

        <div class="hero-content">

            <h1>

                Hi, I'm

                <span>Farooq Mansuri</span>

            </h1>


            <h2>

                <span class="typing"></span>

            </h2>


            <p>

                Passionate Full Stack Developer focused on creating modern,
                responsive and user-friendly websites with clean code and elegant design.

            </p>


            <div class="hero-buttons">

                <a
                    href="#projects"
                    class="btn primary-btn"
                >

                    View Projects

                </a>


                <a
                    href="download-cv.php"
                    class="btn secondary-btn"
                >

                    <i class="ri-download-2-line"></i>

                    Download CV

                </a>

            </div>


            <!-- ================= SOCIAL ICONS ================= -->

            <div class="social-icons">

                <?php if (!empty($instagram_url)): ?>

                    <a
                        href="<?php echo htmlspecialchars($instagram_url, ENT_QUOTES); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Instagram"
                    >

                        <i class="ri-instagram-line"></i>

                    </a>

                <?php endif; ?>


                <?php if (!empty($email)): ?>

                    <a
                        href="mailto:<?php echo htmlspecialchars($email, ENT_QUOTES); ?>"
                        aria-label="Email"
                    >

                        <i class="ri-mail-line"></i>

                    </a>

                <?php endif; ?>

            </div>

        </div>


        <div class="hero-image">

            <div class="image-box">

                <span class="ring"></span>

                <img
                    src="<?php echo htmlspecialchars($profile_photo); ?>"
                    alt="Farooq Mansuri"
                >

            </div>

        </div>

    </div>


    <div class="scroll-down">

        <span>Scroll Down</span>

        <i class="ri-arrow-down-line"></i>

    </div>

</section>


<!-- =========================================================
     ABOUT
========================================================= -->

<section
    class="about reveal"
    id="about"
>

    <div class="container about-container">

        <div class="about-image">

            <div class="about-img-box reveal-left">

                <img
                    src="<?php echo htmlspecialchars($profile_photo); ?>"
                    alt="Farooq Mansuri"
                >

            </div>

        </div>


        <div class="about-content reveal-right">

            <span class="section-tag">
                ABOUT ME
            </span>


            <h2>

                <?php
                echo htmlspecialchars(
                    $about['heading'] ?? ''
                );
                ?>

            </h2>


            <p>

                <?php

                echo nl2br(
                    htmlspecialchars(
                        $about['description'] ?? ''
                    )
                );

                ?>

            </p>


            <div class="about-info">

                <div class="info-card">

                    <h3>Education</h3>

                    <p>

                        <?php
                        echo htmlspecialchars(
                            $about['education'] ?? ''
                        );
                        ?>

                    </p>

                </div>


                <div class="info-card">

                    <h3>Course</h3>

                    <p>

                        <?php
                        echo htmlspecialchars(
                            $about['course'] ?? ''
                        );
                        ?>

                    </p>

                </div>


                <div class="info-card">

                    <h3>Goal</h3>

                    <p>

                        <?php
                        echo htmlspecialchars(
                            $about['goal'] ?? ''
                        );
                        ?>

                    </p>

                </div>

            </div>


            <a
                href="download-cv.php"
                class="btn primary-btn"
            >

                <i class="ri-download-2-line"></i>

                Download Resume

            </a>

        </div>

    </div>

</section>


<!-- =========================================================
     SKILLS
========================================================= -->

<section
    class="skills reveal-card"
    id="skills"
>

    <div class="container">

        <div class="skills-heading">

            <h1 class="bg-text">
                SKILLS
            </h1>

            <span>
                MY SKILLS
            </span>

            <h2>
                Tech Stack & Tools
            </h2>

            <p>

                Technologies and tools I use to build modern,
                responsive and high-performance web applications.

            </p>

        </div>


        <div class="skills-wrapper">

            <?php

            $categories = [

                "Frontend" => "ri-code-s-slash-line",

                "Backend" => "ri-server-line",

                "Tools" => "ri-tools-line"

            ];


            foreach (
                $categories as $category => $category_icon
            ) {

            ?>

                <div class="skills-card">

                    <i class="<?php echo $category_icon; ?>"></i>

                    <h3>
                        <?php echo htmlspecialchars($category); ?>
                    </h3>


                    <div class="skills-list">

                        <?php

                        mysqli_data_seek(
                            $skills_result,
                            0
                        );


                        while (
                            $skill =
                            mysqli_fetch_assoc($skills_result)
                        ) {

                            if (
                                $skill['category'] == $category
                            ) {

                        ?>

                            <span>

                                <?php
                                echo htmlspecialchars(
                                    $skill['skill_name']
                                );
                                ?>

                            </span>

                        <?php

                            }

                        }

                        ?>

                    </div>

                </div>

            <?php

            }

            ?>

        </div>

    </div>

</section>


<!-- =========================================================
     PROJECTS
========================================================= -->

<section
    class="projects reveal-card"
    id="projects"
>

    <div class="container">

        <div class="projects-heading">

            <span>
                MY WORK
            </span>

            <h2>
                Recent Projects
            </h2>

            <p>

                Crafting modern, responsive and high-performance web experiences
                with clean code and premium UI.

            </p>

        </div>


        <div class="project-grid">

            <?php

            $query =
                "SELECT * FROM projects ORDER BY id DESC";

            $result =
                mysqli_query($conn, $query);


            while (
                $row =
                mysqli_fetch_assoc($result)
            ) {

                $project_id =
                    (int)$row['id'];


                $github_link =
                    trim($row['github'] ?? '');


                $demo_link =
                    trim($row['live_demo'] ?? '');


                /* ================= MAIN IMAGE ================= */

                $main_image = '';

                if (!empty($row['image'])) {

                    $main_image =
                        'admin/uploads/projects/' .
                        $row['image'];

                }


                /* ================= GALLERY ================= */

                $additional_images =
                    $gallery_map[$project_id] ?? [];

            ?>

                <div class="project-card">


                    <!-- ================= PROJECT IMAGE ================= -->

                    <div class="project-img">

                        <?php if (!empty($main_image)): ?>

                            <div class="project-image-box">

                                <img
                                    src="<?php echo htmlspecialchars($main_image, ENT_QUOTES); ?>"
                                    alt="<?php echo htmlspecialchars($row['title'] ?? 'Project'); ?>"
                                    class="project-main-image"
                                >


                                <div class="project-image-overlay">

                                    <button
                                        type="button"
                                        class="view-project-btn"
                                        onclick="openProjectGallery(<?php echo $project_id; ?>)"
                                    >

                                        <i class="ri-eye-line"></i>

                                        <span>
                                            View Photos
                                        </span>

                                    </button>

                                </div>

                            </div>

                        <?php else: ?>

                            <div class="project-no-image">

                                <i class="ri-image-line"></i>

                            </div>

                        <?php endif; ?>

                    </div>


                    <div class="project-content">


                        <h3>

                            <?php

                            echo htmlspecialchars(
                                $row['title'] ?? ''
                            );

                            ?>

                        </h3>


                        <p>

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $row['description'] ?? ''
                                )
                            );

                            ?>

                        </p>


                        <div class="project-tech">

                            <?php

                            $techs =
                                explode(
                                    ",",
                                    $row['technologies'] ?? ''
                                );


                            foreach (
                                $techs as $tech
                            ) {

                                $tech =
                                    trim($tech);

                                if ($tech !== '') {

                                    echo "<span>"
                                        .
                                        htmlspecialchars($tech)
                                        .
                                        "</span>";

                                }

                            }

                            ?>

                        </div>


                        <div class="project-footer">

                            <span>

                                <i class="ri-calendar-line"></i>

                                <?php

                                echo !empty($row['created_at'])
                                    ? date(
                                        "M Y",
                                        strtotime(
                                            $row['created_at']
                                        )
                                    )
                                    : '';

                                ?>

                            </span>


                            <span class="complete">

                                <i class="ri-checkbox-circle-fill"></i>

                                <?php

                                echo htmlspecialchars(
                                    $row['status'] ?? ''
                                );

                                ?>

                            </span>


                            <span class="private">

                                <i class="ri-lock-2-fill"></i>

                                Private

                            </span>

                        </div>


                        <!-- ================= PROJECT LINKS ================= -->

                        <?php if (
                            !empty($github_link)
                            ||
                            !empty($demo_link)
                        ): ?>

                            <div class="project-links">


                                <!-- GITHUB -->

                                <?php if (!empty($github_link)): ?>

                                    <div class="project-link-group">

                                        <a
                                            href="<?php echo htmlspecialchars($github_link, ENT_QUOTES); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="project-link-btn github-link"
                                        >

                                            <i class="ri-github-fill"></i>

                                            GitHub

                                            <i class="ri-external-link-line"></i>

                                        </a>


                                        <button
                                            type="button"
                                            class="project-copy-btn"
                                            data-copy-url="<?php echo htmlspecialchars($github_link, ENT_QUOTES); ?>"
                                            title="Copy GitHub link"
                                        >

                                            <i class="ri-file-copy-line"></i>

                                            Copy

                                        </button>

                                    </div>

                                <?php endif; ?>


                                <!-- LIVE DEMO -->

                                <?php if (!empty($demo_link)): ?>

                                    <div class="project-link-group">

                                        <a
                                            href="<?php echo htmlspecialchars($demo_link, ENT_QUOTES); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="project-link-btn demo-link"
                                        >

                                            <i class="ri-external-link-line"></i>

                                            Live Demo

                                        </a>


                                        <button
                                            type="button"
                                            class="project-copy-btn"
                                            data-copy-url="<?php echo htmlspecialchars($demo_link, ENT_QUOTES); ?>"
                                            title="Copy Live Demo link"
                                        >

                                            <i class="ri-file-copy-line"></i>

                                            Copy

                                        </button>

                                    </div>

                                <?php endif; ?>


                            </div>

                        <?php endif; ?>


                    </div>

                </div>


            <?php

            }

            ?>

        </div>

    </div>

</section>


<!-- =========================================================
     CONTACT
========================================================= -->

<section
    class="contact reveal-left"
    id="contact"
>

    <div class="container">

        <div class="contact-heading">

            <h1 class="bg-text">
                CONTACT
            </h1>

            <span>
                GET IN TOUCH
            </span>

            <h2>
                Let's Work Together
            </h2>

            <p>

                Have a project in mind or want to collaborate?
                Feel free to contact me anytime.

            </p>

        </div>


        <div class="contact-wrapper">


            <!-- ================= CONTACT INFORMATION ================= -->

            <div class="contact-info">

                <h3>
                    Contact Information
                </h3>


                <p>

                    I'm always open to discussing new projects,
                    creative ideas and opportunities.

                </p>


                <!-- EMAIL -->

                <div class="info-item">

                    <i class="ri-mail-line"></i>

                    <div>

                        <h4>
                            Email
                        </h4>

                        <?php if (!empty($email)): ?>

                            <a
                                href="mailto:<?php echo htmlspecialchars($email, ENT_QUOTES); ?>"
                                class="contact-email-link"
                            >

                                <?php
                                echo htmlspecialchars($email);
                                ?>

                            </a>

                        <?php else: ?>

                            <span>
                                Email not available
                            </span>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- LOCATION -->

                <div class="info-item">

                    <i class="ri-map-pin-line"></i>

                    <div>

                        <h4>
                            Location
                        </h4>

                        <span>

                            <?php
                            echo htmlspecialchars($location);
                            ?>

                        </span>

                    </div>

                </div>


                <!-- INSTAGRAM -->

                <div class="info-item">

                    <i class="ri-instagram-line"></i>

                    <div>

                        <h4>
                            Instagram
                        </h4>

                        <span>

                            @<?php
                            echo htmlspecialchars(
                                $instagram_username
                            );
                            ?>

                        </span>

                    </div>

                </div>

            </div>


            <!-- ================= CONTACT FORM ================= -->

            <div class="contact-form reveal-right">


                <!-- SUCCESS -->

                <?php if ($contact_success): ?>

                    <div class="contact-success">

                        <div class="success-icon">

                            <i class="ri-checkbox-circle-fill"></i>

                        </div>


                        <div class="success-content">

                            <strong>
                                Message Sent Successfully!
                            </strong>


                            <span>

                                Thanks for reaching out.
                                I'll get back to you soon.

                            </span>

                        </div>


                        <button
                            type="button"
                            class="success-close"
                            onclick="this.parentElement.remove();"
                        >

                            <i class="ri-close-line"></i>

                        </button>

                    </div>

                <?php endif; ?>


                <!-- DATABASE ERROR -->

                <?php if ($contact_error): ?>

                    <div class="contact-error">

                        <div class="success-icon">

                            <i class="ri-error-warning-fill"></i>

                        </div>


                        <div class="success-content">

                            <strong>
                                Something went wrong!
                            </strong>


                            <span>

                                Your message could not be sent.
                                Please try again.

                            </span>

                        </div>


                        <button
                            type="button"
                            class="success-close"
                            onclick="this.parentElement.remove();"
                        >

                            <i class="ri-close-line"></i>

                        </button>

                    </div>

                <?php endif; ?>


                <form
                    method="POST"
                    novalidate
                >


                    <!-- NAME -->

                    <input
                        type="text"
                        name="name"
                        placeholder="Your Name"
                        value="<?php echo htmlspecialchars($name); ?>"
                        required
                        maxlength="50"
                    >


                    <?php if (isset($contact_errors['name'])): ?>

                        <small class="form-error">

                            <?php
                            echo htmlspecialchars(
                                $contact_errors['name']
                            );
                            ?>

                        </small>

                    <?php endif; ?>


                    <!-- EMAIL -->

                    <input
                        type="email"
                        name="email"
                        placeholder="Your Email"
                        value="<?php echo htmlspecialchars($email_input); ?>"
                        required
                        maxlength="100"
                    >


                    <?php if (isset($contact_errors['email'])): ?>

                        <small class="form-error">

                            <?php
                            echo htmlspecialchars(
                                $contact_errors['email']
                            );
                            ?>

                        </small>

                    <?php endif; ?>


                    <!-- PHONE -->

                    <div class="form-group">

                        <input
                            type="tel"
                            name="phone"
                            placeholder="Your Phone Number"
                            value="<?php echo htmlspecialchars($phone); ?>"
                            required
                            maxlength="10"
                            minlength="10"
                            inputmode="numeric"
                            pattern="[0-9]{10}"
                        >


                        <?php if (isset($contact_errors['phone'])): ?>

                            <small class="form-error">

                                <?php
                                echo htmlspecialchars(
                                    $contact_errors['phone']
                                );
                                ?>

                            </small>

                        <?php endif; ?>

                    </div>


                    <!-- SUBJECT -->

                    <input
                        type="text"
                        name="subject"
                        placeholder="Subject"
                        value="<?php echo htmlspecialchars($subject); ?>"
                        required
                        maxlength="100"
                    >


                    <?php if (isset($contact_errors['subject'])): ?>

                        <small class="form-error">

                            <?php
                            echo htmlspecialchars(
                                $contact_errors['subject']
                            );
                            ?>

                        </small>

                    <?php endif; ?>


                    <!-- MESSAGE -->

                    <textarea
                        name="message"
                        rows="6"
                        placeholder="Your Message"
                        required
                        maxlength="1000"
                    ><?php echo htmlspecialchars($message); ?></textarea>


                    <?php if (isset($contact_errors['message'])): ?>

                        <small class="form-error">

                            <?php
                            echo htmlspecialchars(
                                $contact_errors['message']
                            );
                            ?>

                        </small>

                    <?php endif; ?>


                    <!-- SUBMIT -->

                    <button
                        type="submit"
                        name="send_message"
                        class="btn primary-btn"
                    >

                        Send Message

                        <i class="ri-send-plane-fill"></i>

                    </button>

                </form>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     FOOTER
========================================================= -->

<footer class="footer">

    <div class="container footer-container">


        <div class="footer-logo">

            <h2>
                Farooq<span>.</span>
            </h2>


            <p>

                Passionate Full Stack Developer creating modern,
                responsive and professional websites.

            </p>

        </div>


        <div class="footer-links">

            <h3>
                Quick Links
            </h3>


            <a href="#">
                Home
            </a>


            <a href="#about">
                About
            </a>


            <a href="#skills">
                Skills
            </a>


            <a href="#projects">
                Projects
            </a>


            <a href="#contact">
                Contact
            </a>

        </div>


        <div class="footer-social">

            <h3>
                Follow Me
            </h3>


            <a
                href="https://github.com/"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="GitHub"
            >

                <i class="ri-github-fill"></i>

            </a>


            <a
                href="https://linkedin.com/"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="LinkedIn"
            >

                <i class="ri-linkedin-box-fill"></i>

            </a>


            <?php if (!empty($instagram_url)): ?>

                <a
                    href="<?php echo htmlspecialchars($instagram_url, ENT_QUOTES); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Instagram"
                >

                    <i class="ri-instagram-line"></i>

                </a>

            <?php endif; ?>


            <?php if (!empty($email)): ?>

                <a
                    href="mailto:<?php echo htmlspecialchars($email, ENT_QUOTES); ?>"
                    aria-label="Email"
                >

                    <i class="ri-mail-fill"></i>

                </a>

            <?php endif; ?>

        </div>

    </div>


    <div class="footer-bottom">

        © 2026 Farooq Mansuri | Made with ❤️

    </div>

</footer>


<!-- =========================================================
     PROJECT GALLERY MODAL
========================================================= -->

<div
    class="project-gallery-modal"
    id="projectGalleryModal"
>

    <div class="project-gallery-content">


        <!-- HEADER -->

        <div class="project-gallery-header">

            <h3
                class="project-gallery-title"
                id="galleryProjectTitle"
            >
                Project Photos
            </h3>


            <button
                type="button"
                class="project-gallery-close"
                onclick="closeProjectGallery()"
            >

                <i class="ri-close-line"></i>

            </button>

        </div>


        <!-- MAIN / COVER PHOTO -->

        <div class="gallery-cover">

            <h4 class="gallery-section-title">
                Main / Cover Photo
            </h4>


            <img
                id="galleryMainImage"
                src=""
                alt="Main Project Photo"
            >

        </div>


        <!-- ADDITIONAL PHOTOS -->

        <div id="additionalGallerySection">

            <h4 class="gallery-section-title">
                Additional Photos
            </h4>


            <div
                class="gallery-additional-grid"
                id="galleryAdditionalImages"
            ></div>

        </div>

    </div>

</div>


<!-- =========================================================
     MAIN JS
========================================================= -->

<script src="assets/js/script.js"></script>


<!-- =========================================================
     PROJECT GALLERY + COPY SCRIPT
========================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {


    /* =====================================================
       PROJECT GALLERY DATA
    ===================================================== */

    const projectGalleryData = <?php

        echo json_encode(
            $gallery_map,
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_AMP |
            JSON_HEX_QUOT
        );

    ?>;


    const projectMainImages = {};


    <?php

    $project_data_query =
        mysqli_query(
            $conn,
            "SELECT id, title, image FROM projects"
        );


    if ($project_data_query) {

        while (
            $project_data =
            mysqli_fetch_assoc(
                $project_data_query
            )
        ) {

            $pid =
                (int)$project_data['id'];


            $mainImage = '';

            if (!empty($project_data['image'])) {

                $mainImage =
                    'admin/uploads/projects/' .
                    $project_data['image'];

            }

    ?>

        projectMainImages[<?php echo $pid; ?>] = {

            title:
                <?php
                echo json_encode(
                    $project_data['title'] ?? ''
                );
                ?>,

            image:
                <?php
                echo json_encode(
                    $mainImage
                );
                ?>

        };

    <?php

        }

    }

    ?>


    /* =====================================================
       OPEN PROJECT GALLERY
    ===================================================== */

    window.openProjectGallery = function (projectId) {

        const modal =
            document.getElementById(
                "projectGalleryModal"
            );


        const mainImage =
            document.getElementById(
                "galleryMainImage"
            );


        const title =
            document.getElementById(
                "galleryProjectTitle"
            );


        const additionalContainer =
            document.getElementById(
                "galleryAdditionalImages"
            );


        const additionalSection =
            document.getElementById(
                "additionalGallerySection"
            );


        const project =
            projectMainImages[projectId];


        if (!project) {
            return;
        }


        /* PROJECT TITLE */

        title.textContent =
            project.title ||
            "Project Photos";


        /* MAIN IMAGE */

        mainImage.src =
            project.image || "";


        mainImage.alt =
            project.title ||
            "Main Project Photo";


        /* ADDITIONAL PHOTOS */

        additionalContainer.innerHTML = "";


        const additionalImages =
            projectGalleryData[projectId] || [];


        if (additionalImages.length === 0) {

            additionalSection.style.display =
                "none";

        } else {

            additionalSection.style.display =
                "block";


            additionalImages.forEach(
                function (image) {

                    const item =
                        document.createElement(
                            "div"
                        );


                    item.className =
                        "gallery-additional-item";


                    const img =
                        document.createElement(
                            "img"
                        );


                    img.src = image;

                    img.alt =
                        project.title ||
                        "Project Photo";


                    item.appendChild(img);


                    additionalContainer.appendChild(
                        item
                    );

                }
            );

        }


        modal.classList.add("show");

        document.body.style.overflow =
            "hidden";

    };


    /* =====================================================
       CLOSE PROJECT GALLERY
    ===================================================== */

    window.closeProjectGallery = function () {

        const modal =
            document.getElementById(
                "projectGalleryModal"
            );


        if (!modal) {
            return;
        }


        modal.classList.remove("show");

        document.body.style.overflow = "";

    };


    /* =====================================================
       OUTSIDE CLICK
    ===================================================== */

    const galleryModal =
        document.getElementById(
            "projectGalleryModal"
        );


    if (galleryModal) {

        galleryModal.addEventListener(
            "click",
            function (event) {

                if (event.target === this) {

                    closeProjectGallery();

                }

            }
        );

    }


    /* =====================================================
       ESC KEY
    ===================================================== */

    document.addEventListener(
        "keydown",
        function (event) {

            if (event.key === "Escape") {

                closeProjectGallery();

            }

        }
    );


    /* =====================================================
       COPY PROJECT LINK
    ===================================================== */

    const copyButtons =
        document.querySelectorAll(
            ".project-copy-btn"
        );


    copyButtons.forEach(
        function (button) {

            button.addEventListener(
                "click",
                async function () {

                    const url =
                        this.getAttribute(
                            "data-copy-url"
                        );


                    if (!url) {
                        return;
                    }


                    try {

                        await navigator.clipboard.writeText(
                            url
                        );


                        showCopyToast(
                            "Link copied successfully!"
                        );


                    } catch (error) {

                        const tempInput =
                            document.createElement(
                                "input"
                            );


                        tempInput.value = url;


                        document.body.appendChild(
                            tempInput
                        );


                        tempInput.select();


                        tempInput.setSelectionRange(
                            0,
                            99999
                        );


                        document.execCommand(
                            "copy"
                        );


                        tempInput.remove();


                        showCopyToast(
                            "Link copied successfully!"
                        );

                    }

                }
            );

        }
    );


    /* =====================================================
       COPY TOAST
    ===================================================== */

    function showCopyToast(message) {

        let toast =
            document.querySelector(
                ".copy-toast"
            );


        if (!toast) {

            toast =
                document.createElement(
                    "div"
                );


            toast.className =
                "copy-toast";


            toast.innerHTML = `
                <i class="ri-checkbox-circle-fill"></i>
                <span></span>
            `;


            document.body.appendChild(
                toast
            );

        }


        toast.querySelector(
            "span"
        ).textContent =
            message;


        toast.classList.add(
            "show"
        );


        clearTimeout(
            window.copyToastTimer
        );


        window.copyToastTimer =
            setTimeout(
                function () {

                    toast.classList.remove(
                        "show"
                    );

                },
                2200
            );

    }

});

</script>


</body>

</html>