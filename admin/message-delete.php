<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['message_error'] = "Invalid message request.";

    header("Location: messages.php");
    exit();
}

$id = intval($_GET['id']);

$query = "DELETE FROM messages WHERE id = $id";

if (mysqli_query($conn, $query)) {

    $_SESSION['message_success'] = "Message deleted successfully!";

} else {

    $_SESSION['message_error'] = "Failed to delete message.";

}

header("Location: messages.php");
exit();

?>