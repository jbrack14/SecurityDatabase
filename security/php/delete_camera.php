<?php
    require("../config.php");
    if(!empty($_POST))
    {
       // Add ticket to database
        $query = "
            DELETE FROM Camera
            WHERE Camera_UID = :cam
        ";

        $query_params = array(
            ':cam' => $_POST['delete']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/buildings.php");
        die("Redirecting to: ../pages/buildings.php");
    }
?>
