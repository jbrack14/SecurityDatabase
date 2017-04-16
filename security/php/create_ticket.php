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
                Description
            ) VALUES (
                :name,
                :email,
				        :description
            )
        ";

        $query_params = array(
            ':name' => $_POST['ticket_name'],
            ':email' => $_POST['ticket_email'],
            ':description' => $_POST['ticket_message']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/ticket.html");
        die("Redirecting to: ../pages/ticket.html");
    }
?>
