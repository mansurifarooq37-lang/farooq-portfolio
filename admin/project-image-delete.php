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

$image_id = intval($_GET['id']);

/* Get additional photo */
$query = "SELECT * FROM project_images WHERE id = $image_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {

    $_SESSION['project_error'] = "Additional photo not found.";

    header("Location: project-list.php");
    exit();
}

$image = mysqli_fetch_assoc($result);

$project_id = intval($image['project_id']);
$image_name = $image['image'];

/* Physical image path */
$image_path = __DIR__ . "/uploads/projects/" . $image_name;

/* Delete physical image */
if (!empty($image_name) && file_exists($image_path)) {
    unlink($image_path);
}

/* Delete database record */
$delete_query = "
    DELETE FROM project_images
    WHERE id = $image_id
";

if (mysqli_query($conn, $delete_query)) {

    $_SESSION['project_success'] =
        "Additional photo deleted successfully!";

} else {

    $_SESSION['project_error'] =
        "Failed to delete additional photo.";
}

/* Return to same project edit page */
header(
    "Location: project-edit.php?id=" . $project_id
);

exit();

?>