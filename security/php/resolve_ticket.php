<?php
    require("../config.php");
    if(!empty($_POST))
    {
        // Resolve Ticket
        $query = "
            DELETE
            FROM Ticket
            WHERE Description = :desc
        ";

        $query_params = array(
            ':desc' => $_SESSION['desc']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/tickets.php");
        die("Redirecting to: ../pages/tickets.php");
    }
?>
