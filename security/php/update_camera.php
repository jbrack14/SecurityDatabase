<?php
    require_once("../config.php");
    if(!empty($_POST))
    {
       // Add ticket to database
        $query = "
            UPDATE Camera
            SET Status = :status
            WHERE Camera_UID = :uuid
        ";

        $query_params = array(
            ':status' => $_POST['status'],
            ':uuid' => $_POST['uuid']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/cameras.php");
        die("Redirecting to: ../pages/cameras.php");
    }
?>
