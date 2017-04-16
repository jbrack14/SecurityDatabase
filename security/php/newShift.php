<?php
    require_once("../config.php");
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

        $sql = "SELECT @last_uuid";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute();
        $shift_id = implode(',', $stmt->fetch());

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
              ':shift' => $shift_id,
              ':spot' => $spot
          );

          try {
              $stmt = $db->prepare($query);
              $result = $stmt->execute($query_params);
          }
          catch(PDOException $ex){echo $shift_id; echo ' --- '; echo $spot; die("Failed to run query: " . $ex->getMessage()); }
        }

        header("Location: ../pages/shifts.php");
        die("Redirecting to: ../pages/shifts.php");
    }
?>
