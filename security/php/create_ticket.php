<?php
    require_once("../config.php");
    if(!empty($_POST))
    {
        // Ensure that the user fills out fields
        if(empty($_POST['ticket_name']))
        { die("Please enter a name."); }
        if(empty($_POST['ticket_email']))
        { die("Please enter an email."); }
		    if(empty($_POST['ticket_message']))
		    { die("Please enter your ticket message."); }

        // Add ticket to database
        $query = "
            INSERT INTO Ticket (
                Name,
                Email,
                Phone_Num,
                Description,
                Start_Time,
                End_Time
            ) VALUES (
                :name,
                :email,
                :phone,
				        :description,
                :start,
                :end
            )
        ";

        $query_params = array(
            ':name' => $_POST['ticket_name'],
            ':email' => $_POST['ticket_email'],
            ':phone' => $_POST['ticket_phone'],
            ':description' => $_POST['ticket_message'],
            'start' => $_POST['start'],
            'end' => $_POST['end']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        //Add spots to ticket
        $sql = "SELECT @last_uuid";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute();
        $ticket_id = implode(',', $stmt->fetch());

        foreach ($_POST['spots'] as $spot) {
          $query = "
              INSERT INTO Ticket_Spots (
                  Ticket_UUID,
                  Spot_UUID
              ) VALUES (
                  :ticket,
                  :spot
              )
          ";

          $query_params = array(
              ':ticket' => $ticket_id,
              ':spot' => $spot
          );

          try {
              $stmt = $db->prepare($query);
              $result = $stmt->execute($query_params);
          }
          catch(PDOException $ex){echo $shift_id; echo ' --- '; echo $spot; die("Failed to run query: " . $ex->getMessage()); }
        }

        header("Location: ../pages/ticket.php");
        die("Redirecting to: ../pages/ticket.php");
    }
?>
