<?php
    require_once("../config.php");
    if(!empty($_POST))
    {
        // Resolve Ticket
        $query = "
            UPDATE Alarm_Event
            SET Resolved_Time = NOW()
            WHERE Alarm_Event_UUID = :uuid
        ";

        $query_params = array(
            ':uuid' => $_POST['resolve']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/alarms.php");
        die("Redirecting to: ../pages/alarms.php");
    }
?>
