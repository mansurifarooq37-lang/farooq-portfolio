<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include("config/db.php");


/* =========================================================
   RESUME DATA
========================================================= */

$query = "SELECT * FROM resume LIMIT 1";
$result = mysqli_query($conn, $query);

$resume = mysqli_fetch_assoc($result);

if (!$resume) {
    die("Resume data not found.");
}


/* =========================================================
   HELPER
========================================================= */

function clean($value)
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}


/* =========================================================
   SKILLS
========================================================= */

$skills = [];

if (!empty($resume['skills'])) {

    $skills = array_filter(
        array_map(
            'trim',
            explode(',', $resume['skills'])
        )
    );
}


/* =========================================================
   ACHIEVEMENTS
========================================================= */

$achievements = [];

if (!empty($resume['achievements'])) {

    $achievements = preg_split(
        '/\r\n|\r|\n/',
        $resume['achievements']
    );

    $achievements = array_filter(
        array_map(
            'trim',
            $achievements
        )
    );
}


/* =========================================================
   PROJECTS
========================================================= */

$projects = [];

$projectQuery = "
    SELECT title, description, technologies, status
    FROM projects
    ORDER BY id DESC
    LIMIT 4
";

$projectResult = mysqli_query(
    $conn,
    $projectQuery
);

if ($projectResult) {

    while ($row = mysqli_fetch_assoc($projectResult)) {

        $projects[] = $row;

    }
}


/* =========================================================
   PDF HTML
========================================================= */

$html = '

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<style>

/* =========================================================
   A4 PAGE
========================================================= */

@page {
    size: A4 portrait;
    margin: 0;
}


/* =========================================================
   RESET
========================================================= */

html,
body {
    margin: 0;
    padding: 0;
}

* {
    box-sizing: border-box;
}


/* =========================================================
   BODY
========================================================= */

body {

    font-family: DejaVu Sans, Arial, sans-serif;

    background: #ffffff;

    color: #222222;

    font-size: 7.5px;

    line-height: 1.35;
}


/* =========================================================
   A4 SAFE CONTAINER
========================================================= */

.resume {

    width: 190mm;

    margin: 0 auto;

    padding-top: 9mm;

    padding-bottom: 5mm;

}


/* =========================================================
   HEADER
========================================================= */

.header {

    width: 190mm;

    background: #6C63FF;

    color: #ffffff;

    border-radius: 7px;

    padding: 13px 15px;

    margin-bottom: 9px;

}


/* Header table */

.header-table {

    width: 100%;

    border-collapse: collapse;

    table-layout: fixed;

}

.header-left {

    width: 62%;

    vertical-align: top;

    padding-right: 12px;

}

.header-right {

    width: 38%;

    vertical-align: top;

    text-align: right;

}


/* Name */

.name {

    font-size: 21px;

    font-weight: bold;

    line-height: 1.1;

    margin: 0 0 2px 0;

}


/* Role */

.role {

    font-size: 9.5px;

    margin: 0 0 4px 0;

}


/* Location */

.location {

    font-size: 7.2px;

    margin: 0;

}


/* Contact */

.contact {

    font-size: 6.8px;

    line-height: 1.65;

    word-wrap: break-word;

    overflow-wrap: break-word;

}


/* =========================================================
   MAIN COLUMNS
========================================================= */

.content-table {

    width: 190mm;

    border-collapse: collapse;

    table-layout: fixed;

}

.main-column {

    width: 124mm;

    vertical-align: top;

    padding-right: 8px;

}

.side-column {

    width: 66mm;

    vertical-align: top;

    padding-left: 8px;

}


/* =========================================================
   SECTIONS
========================================================= */

.section {

    width: 100%;

    margin-bottom: 7px;

}


/* =========================================================
   SECTION TITLE
========================================================= */

.section-title {

    color: #6C63FF;

    font-size: 8.6px;

    font-weight: bold;

    letter-spacing: 1.15px;

    border-bottom: 1px solid #dddddd;

    padding-bottom: 3px;

    margin-bottom: 4px;

}


/* =========================================================
   PROFILE
========================================================= */

.profile {

    font-size: 7.25px;

    color: #555555;

    line-height: 1.45;

}


/* =========================================================
   GENERAL ITEM
========================================================= */

.item {

    margin-bottom: 4px;

}


.item-title {

    font-size: 8.3px;

    font-weight: bold;

    color: #222222;

    margin-bottom: 1px;

}


.item-text {

    font-size: 7.25px;

    color: #555555;

    margin-bottom: 1px;

}


.item-sub {

    font-size: 6.6px;

    color: #777777;

}


/* =========================================================
   EXPERIENCE
========================================================= */

.experience-table {

    width: 100%;

    border-collapse: collapse;

    table-layout: fixed;

}


.experience-title {

    font-size: 8.3px;

    font-weight: bold;

}


.experience-badge-cell {

    width: 25%;

    text-align: right;

}


.experience-badge {

    display: inline-block;

    background: #F0EFFF;

    color: #5B54D6;

    border: 1px solid #D8D5FF;

    border-radius: 7px;

    padding: 2px 5px;

    font-size: 6px;

}


.experience-text {

    font-size: 7.2px;

    color: #555555;

    margin-top: 2px;

}


/* =========================================================
   PROJECTS
========================================================= */

.project {

    width: 100%;

    background: #FAFAFF;

    border-left: 2px solid #6C63FF;

    padding: 5px 7px;

    margin-bottom: 4px;

}


.project-table {

    width: 100%;

    border-collapse: collapse;

    table-layout: fixed;

}


.project-title-cell {

    width: 78%;

    vertical-align: top;

}


.project-status-cell {

    width: 22%;

    text-align: right;

    vertical-align: top;

}


.project-title {

    font-size: 7.9px;

    font-weight: bold;

    color: #222222;

}


.project-status {

    display: inline-block;

    background: #F0EFFF;

    color: #5B54D6;

    border: 1px solid #D8D5FF;

    border-radius: 6px;

    padding: 1px 4px;

    font-size: 5.6px;

}


.project-description {

    font-size: 6.7px;

    color: #555555;

    line-height: 1.35;

    margin-top: 2px;

    word-wrap: break-word;

    overflow-wrap: break-word;

}


.project-tech {

    font-size: 6.2px;

    color: #777777;

    margin-top: 2px;

    word-wrap: break-word;

    overflow-wrap: break-word;

}


/* =========================================================
   SKILLS
========================================================= */

.skills-table {

    width: 100%;

    border-collapse: separate;

    border-spacing: 3px;

    table-layout: fixed;

}


.skill {

    width: 50%;

    background: #F0EFFF;

    border: 1px solid #D8D5FF;

    color: #5B54D6;

    text-align: center;

    padding: 4px 2px;

    font-size: 6.2px;

    font-weight: bold;

    border-radius: 6px;

    word-wrap: break-word;

}


/* =========================================================
   ACHIEVEMENTS
========================================================= */

.achievement {

    width: 100%;

    background: #FAFAFA;

    border-left: 2px solid #6C63FF;

    padding: 5px 6px;

    margin-bottom: 4px;

}


.achievement-title {

    font-size: 6.7px;

    font-weight: bold;

    color: #5148D8;

    margin-bottom: 2px;

}


.achievement-text {

    font-size: 6.5px;

    color: #555555;

    line-height: 1.4;

    word-wrap: break-word;

    overflow-wrap: break-word;

}


/* =========================================================
   HIGHLIGHT
========================================================= */

.highlight {

    width: 100%;

    background: #6C63FF;

    color: #ffffff;

    border-radius: 6px;

    padding: 7px;

    margin-top: 7px;

}


.highlight-title {

    font-size: 7.5px;

    font-weight: bold;

    margin-bottom: 2px;

}


.highlight-text {

    font-size: 6.2px;

    line-height: 1.4;

}


/* =========================================================
   FOOTER
========================================================= */

.footer {

    width: 100%;

    border-top: 1px solid #dddddd;

    margin-top: 5px;

    padding-top: 4px;

    text-align: center;

    color: #888888;

    font-size: 6px;

}


/* =========================================================
   KEEP CONTENT TOGETHER
========================================================= */

.project,
.achievement,
.highlight {

    page-break-inside: avoid;

}

</style>

</head>


<body>

<div class="resume">


<!-- =====================================================
     HEADER
===================================================== -->

<div class="header">

    <table class="header-table">

        <tr>

            <td class="header-left">

                <div class="name">
                    ' . clean($resume['name']) . '
                </div>

                <div class="role">
                    Full Stack Developer
                </div>

                <div class="location">
                    ' . clean($resume['location']) . '
                </div>

            </td>


            <td class="header-right">

                <div class="contact">

                    <strong>Email:</strong><br>
                    ' . clean($resume['email']) . '

                    <br><br>

                    <strong>Phone:</strong><br>
                    ' . clean($resume['phone']) . '

                </div>

            </td>

        </tr>

    </table>

</div>


<!-- =====================================================
     CONTENT
===================================================== -->

<table class="content-table">

<tr>


<!-- =====================================================
     LEFT
===================================================== -->

<td class="main-column">


<!-- PROFILE -->

<div class="section">

    <div class="section-title">
        PROFILE
    </div>

    <div class="profile">
        ' . nl2br(clean($resume['about'])) . '
    </div>

</div>


<!-- EDUCATION -->

<div class="section">

    <div class="section-title">
        EDUCATION
    </div>

    <div class="item">

        <div class="item-title">
            Bachelor of Arts (B.A.)
        </div>

        <div class="item-text">
            Delhi University – School of Open Learning (DU SOL)
        </div>

        <div class="item-sub">
            2nd Year – Pursuing
        </div>

    </div>

</div>


<!-- PROFESSIONAL COURSE -->

<div class="section">

    <div class="section-title">
        PROFESSIONAL COURSE
    </div>

    <div class="item">

        <div class="item-title">
            ' . clean($resume['course']) . '
        </div>

        <div class="item-text">
            Advanced Diploma in Website Engineering
        </div>

        <div class="item-sub">
            Pursuing
        </div>

    </div>

</div>


<!-- EXPERIENCE -->

<div class="section">

    <div class="section-title">
        EXPERIENCE
    </div>

    <div class="item">

        <table class="experience-table">

            <tr>

                <td class="experience-title">
                    Fresher
                </td>

                <td class="experience-badge-cell">

                    <span class="experience-badge">
                        Entry Level
                    </span>

                </td>

            </tr>

        </table>

        <div class="experience-text">

            Currently seeking an opportunity to start a
            professional career as a Full Stack Web Developer.

        </div>

    </div>

</div>


<!-- PROJECTS -->

<div class="section">

    <div class="section-title">
        PROJECTS
    </div>
';


if (!empty($projects)) {

    foreach ($projects as $project) {

        $html .= '

        <div class="project">

            <table class="project-table">

                <tr>

                    <td class="project-title-cell">

                        <div class="project-title">
                            ' . clean($project['title']) . '
                        </div>

                    </td>

                    <td class="project-status-cell">
        ';

        if (!empty($project['status'])) {

            $html .= '

                        <span class="project-status">
                            ' . clean($project['status']) . '
                        </span>

            ';

        }

        $html .= '

                    </td>

                </tr>

            </table>


            <div class="project-description">

                ' . nl2br(
                    clean($project['description'])
                ) . '

            </div>


            <div class="project-tech">

                Technologies:
                ' . clean($project['technologies']) . '

            </div>

        </div>

        ';

    }

} else {

    $html .= '

        <div class="item">
            No projects added yet.
        </div>

    ';

}


$html .= '

</div>


</td>


<!-- =====================================================
     RIGHT
===================================================== -->

<td class="side-column">


<!-- TECHNICAL SKILLS -->

<div class="section">

    <div class="section-title">
        TECHNICAL SKILLS
    </div>

    <table class="skills-table">

        <tr>
';


$skillCount = 0;

foreach ($skills as $skill) {

    $html .= '

            <td class="skill">
                ' . clean($skill) . '
            </td>

    ';

    $skillCount++;

    if ($skillCount % 2 === 0) {

        $html .= '

        </tr>
        <tr>

        ';

    }

}


/*
   Close incomplete row correctly
*/

if ($skillCount % 2 !== 0) {

    $html .= '

            <td class="skill">
                &nbsp;
            </td>

    ';

}


$html .= '

        </tr>

    </table>

</div>


<!-- ACHIEVEMENTS -->

<div class="section">

    <div class="section-title">
        ACHIEVEMENTS
    </div>

';


if (!empty($achievements)) {

    foreach ($achievements as $achievement) {

        $html .= '

        <div class="achievement">

            <div class="achievement-title">
                Achievement
            </div>

            <div class="achievement-text">
                ' . clean($achievement) . '
            </div>

        </div>

        ';

    }

} else {

    $html .= '

        <div class="item">
            No achievements added yet.
        </div>

    ';

}


$html .= '

</div>


<!-- HIGHLIGHT -->

<div class="highlight">

    <div class="highlight-title">
        Full Stack Developer
    </div>

    <div class="highlight-text">
        Building modern, responsive and dynamic
        web applications.
    </div>

</div>


</td>

</tr>

</table>


<!-- FOOTER -->

<div class="footer">

    ' . clean($resume['name']) . '
    &nbsp;•&nbsp;
    Full Stack Developer
    &nbsp;•&nbsp;
    Resume

</div>


</div>

</body>

</html>
';


/* =========================================================
   DOMPDF OPTIONS
========================================================= */

$options = new Options();

$options->set(
    'isHtml5ParserEnabled',
    true
);

$options->set(
    'isRemoteEnabled',
    true
);

$options->set(
    'defaultFont',
    'DejaVu Sans'
);


/* =========================================================
   CREATE DOMPDF
========================================================= */

$dompdf = new Dompdf($options);


/* =========================================================
   LOAD
========================================================= */

$dompdf->loadHtml($html);


/* =========================================================
   A4
========================================================= */

$dompdf->setPaper(
    'A4',
    'portrait'
);


/* =========================================================
   RENDER
========================================================= */

$dompdf->render();


/* =========================================================
   FILE NAME
========================================================= */

$fileName = preg_replace(
    '/[^A-Za-z0-9_-]/',
    '_',
    $resume['name']
);

$fileName .= '_Resume.pdf';


/* =========================================================
   DOWNLOAD
========================================================= */

$dompdf->stream(
    $fileName,
    [
        "Attachment" => true
    ]
);

exit();

?>