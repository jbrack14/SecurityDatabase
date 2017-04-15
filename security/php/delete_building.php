<?php
    require("../config.php");
    if(!empty($_POST))
    {
       // Add ticket to database
        $query = "
            DELETE FROM Building
            WHERE Name = :name
        ";

        $query_params = array(
            ':name' => $_POST['delete']
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
