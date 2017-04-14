<?php
    require("../config.php");
    if(!empty($_POST))
    {
        // Add ticket to database
        $query = "
            INSERT INTO Shift_Assignment (
                Start_Time,
                End_Time,
                Officer_SSN
            ) VALUES (
                :start,
                :end,
				        :ssn
            )
        ";

        $query_params = array(
            ':start' => $_POST['start'],
            ':end' => $_POST['end'],
            ':ssn' => $_POST['ssn']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        $query = "
              SELECT Shift_UUID
              FROM Shift_Assignment
              WHERE (Start_Time = :start) AND (Officer_SSN = :ssn) AND (End_Time = :end)
        ";

        $query_params = array(
            ':start' => $_POST['start'],
            ':ssn' => $_POST['ssn'],
            ':end' => $_POST['end']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $shift_uuid = $stmt->fetch();

        foreach ($_POST['spots'] as $spot) {
          $query = "
              INSERT INTO Spot_Assignment (
                  Shift_UUID,
                  Spot_UUID
              ) VALUES (
                  :shift,
                  :spot
              )
          ";

          $query_params = array(
              ':shift' => $shift_uuid,
              ':spot' => $spot
          );

          try {
              $stmt = $db->prepare($query);
              $result = $stmt->execute($query_params);
          }
          catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        }

        header("Location: ../pages/shifts.php");
        die("Redirecting to: ../pages/shifts.php");
    }
?>
