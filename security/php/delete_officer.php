<?php
    require("../config.php");
    if(!empty($_POST))
    {
       // Add ticket to database
        $query = "
            DELETE FROM Security_Officer
            WHERE SSN = :ssn
        ";

        $query_params = array(
            ':ssn' => $_POST['delete']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/officers.php");
        die("Redirecting to: ../pages/officers.php");
    }
?>
