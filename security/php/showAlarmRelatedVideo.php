<?php
    require_once("../config.php");
    if(!empty($_POST) && !empty($_POST['alarm_uuid']))
    {
        $_SESSION['alarms_page_alarm_uuid'] = $_POST['alarm_uuid'];
    }
	else if(!empty($_POST) && !empty($_POST['video_uuid']))
    {
        $_SESSION['alarms_page_video_uuid'] = $_POST['video_uuid'];
    }
	header("Location: ../pages/alarms.php");
	die("Redirecting to: ../pages/alarms.php");
?>
