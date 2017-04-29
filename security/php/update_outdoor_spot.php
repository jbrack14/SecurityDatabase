<?php
    require_once("../config.php");
    if(!empty($_POST))
    {
       // Add ticket to database
        $query = "
            UPDATE Outdoor_Spot
            SET Status = :status
            WHERE Spot_UUID = :uuid
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

        header("Location: ../pages/spots.php");
        die("Redirecting to: ../pages/spots.php");
    }
?>
