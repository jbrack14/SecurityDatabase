<?php
    require("../config.php");
    require("../basicFunctions.php");
    if(!empty($_POST))
    {

        // Update Security_Officer Table
        $query = "
        UPDATE Security_Officer
          SET
            Super_SSN = :ssn
          WHERE
          SSN = :off_ssn
        ";

        $query_params = array(
            ':ssn' => $_POST['super'],
            ':off_ssn' => $_POST['off_ssn']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/officers.php");
        die("Redirecting to ../pages/officers.php");
    }
?>
