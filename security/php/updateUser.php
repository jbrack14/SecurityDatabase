<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	
	$userSSN = getUserSSN();
    if(!empty($_POST)
	&& !empty($_POST['phone']) 
	&& !empty($_POST['email'])  
	&& !empty($_POST['address'])  
	&& !empty($_POST['username']) )
    {
        // Update Security_Officer Table
        $query = "
        UPDATE Security_Officer
          SET
            Phone_Number = :phone,
            Email = :email,
            Address = :address
          WHERE SSN = :ssn
        ";

        $query_params = array(
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':address'=> $_POST['address'],
            ':ssn'=> $userSSN
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
		
		// Update Security_Officer Table
        $query = "
        UPDATE User_Accounts
        SET Username = :username
        WHERE Officer_SSN = :ssn
        ";

        $query_params = array(
            ':username' => $_POST['username'],
            ':ssn'=> $userSSN
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    }
	header("Location: ../pages/user.php");
	die("Redirecting to ../pages/user.php");
?>
