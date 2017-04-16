<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
    if(!empty($_POST))
    {

        // Update Security_Officer Table
        $query = "
        UPDATE Security_Officer
          SET
            First_Name = :first,
            Last_Name = :last,
            Phone_Number = :phone,
            Email = :email,
            Address = :address
          WHERE SSN = :ssn
        ";

        $query_params = array(
            ':first' => $_POST['first'],
            ':last' => $_POST['last'],
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':address'=> $_POST['address'],
            ':ssn'=> getUserSSN()
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/user.php");
        die("Redirecting to ../pages/user.php");
    }
?>
