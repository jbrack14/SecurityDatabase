<?php
    require("../config.php");
    if(!empty($_POST))
    {
        // Resolve Ticket
        $query = "
            UPDATE Alarm_Event
            SET Resolved_Time = CURRENT_TIMESTAMP
            WHERE Alarm_Event_UUID = :uuid
        ";

        $query_params = array(
            ':uuid' => $_SESSION['alarm_uuid']
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
