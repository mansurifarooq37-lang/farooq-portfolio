<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: project-list.php");
    exit();
}

$project_id = intval($_GET['id']);


/* =========================================================
   GET PROJECT
========================================================= */

$query = "SELECT * FROM projects WHERE id = $project_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {

    $_SESSION['project_error'] = "Project not found.";

    header("Location: project-list.php");
    exit();
}

$project = mysqli_fetch_assoc($result);


/* =========================================================
   DELETE MAIN PROJECT IMAGE
========================================================= */

if (!empty($project['image'])) {

    $main_image_path =
        "uploads/projects/" . $project['image'];

    if (file_exists($main_image_path)) {
        unlink($main_image_path);
    }
}


/* =========================================================
   GET GALLERY IMAGES
========================================================= */

$gallery_query = "
    SELECT *
    FROM project_images
    WHERE project_id = $project_id
";

$gallery_result = mysqli_query($conn, $gallery_query);


/* =========================================================
   DELETE GALLERY PHYSICAL IMAGES
========================================================= */

if ($gallery_result) {

    while ($gallery = mysqli_fetch_assoc($gallery_result)) {

        if (!empty($gallery['image'])) {

            $gallery_image_path =
                "uploads/projects/" . $gallery['image'];

            if (file_exists($gallery_image_path)) {
                unlink($gallery_image_path);
            }
        }
    }
}


/* =========================================================
   DELETE GALLERY DATABASE RECORDS
========================================================= */

$delete_gallery_query = "
    DELETE FROM project_images
    WHERE project_id = $project_id
";

mysqli_query($conn, $delete_gallery_query);


/* =========================================================
   DELETE PROJECT
========================================================= */

$delete_project_query = "
    DELETE FROM projects
    WHERE id = $project_id
";

if (mysqli_query($conn, $delete_project_query)) {

    $_SESSION['project_success'] =
        "Project deleted successfully!";

} else {

    $_SESSION['project_error'] =
        "Failed to delete project.";
}


/* =========================================================
   RETURN TO PROJECT LIST
========================================================= */

header("Location: project-list.php");
exit();

?>