<?php
    require_once("../config.php");
    if(!empty($_POST))
    {
        // Resolve Ticket
        $query = "
            UPDATE Ticket
            SET
              Start_Time = :start,
              End_Time = :end,
              Result = :result
            WHERE Ticket_UUID = :uuid
        ";

        $query_params = array(
            ':start' => $_POST['start'],
            ':end' => $_POST['end'],
            ':result' => $_POST['result'],
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
