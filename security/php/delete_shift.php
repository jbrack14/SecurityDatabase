<?php
    require("../config.php");
    if(!empty($_POST))
    {
       // Add ticket to database
        $query = "
            DELETE FROM Shift_Assignment
            WHERE Shift_UUID = :uuid
        ";

        $query_params = array(
            ':uuid' => $_POST['delete']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/shifts.php");
        die("Redirecting to: ../pages/shifts.php");
    }
?>
