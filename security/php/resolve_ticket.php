<?php
    require("../config.php");
    if(!empty($_POST))
    {
        // Resolve Ticket
        $query = "
            DELETE
            FROM Ticket
            WHERE Ticket_UUID = :uuid
        ";

        $query_params = array(
            ':uuid' => $_POST['resolve']
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
