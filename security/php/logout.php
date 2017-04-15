<?php
    require("../config.php");
    unset($_SESSION['user']);
	session_destroy();
    header("Location: ../index.html");
    die("Redirecting to: ../index.html");
?>
