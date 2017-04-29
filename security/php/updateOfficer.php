<?php
    require("../config.php");
    require("../basicFunctions.php");
    if(!empty($_POST) && (!empty($_POST['status']) && !empty($_POST['off_ssn'])))
    {
		// Update Security_Officer Table
        $query = "
        UPDATE Security_Officer
          SET
            Super_SSN = :ssn,
            Status = :status
          WHERE
          SSN = :off_ssn
        ";
		
		$superSSN = NULL;
		if(empty($_POST['super']))
		{
			$superSSN = $_POST['super'];
		}
        
        $query_params = array(
            ':ssn' => $superSSN,
            ':status' => $_POST['status'],
            ':off_ssn' => $_POST['off_ssn']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

    }
	
	header("Location: ../pages/officers.php");
	die("Redirecting to ../pages/officers.php");
?>
