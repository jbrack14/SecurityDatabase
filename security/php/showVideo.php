<?php
    require_once("../config.php");
    if(!empty($_POST) && !empty($_POST['video_uuid']))
    {
        $_SESSION['video_page_video_uuid'] = $_POST['video_uuid'];
    }
	header("Location: ../pages/videos.php");
	die("Redirecting to: ../pages/videos.php");
?>
