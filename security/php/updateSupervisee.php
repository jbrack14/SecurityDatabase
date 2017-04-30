<?php 
	require_once("../config.php");
	require_once("../basicFunctions.php");
	
	if(!empty($_POST)
	&& !empty($_POST['first']) 
	&& !empty($_POST['last']) 
	&& !empty($_POST['off_ssn']) )
	{
	$query = "
		UPDATE Security_Officer
		SET
			First_Name = :first,
			Last_Name = :last
		WHERE SSN = :ssn
	";
	
	$query_params = array(
		':first' => $_POST['first'],
		':last' => $_POST['last'],
		':ssn'=> $_POST['off_ssn']
	);
	
	try {
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}
	catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
	}
	header("Location: ../pages/supervisee.php");
	die("Redirecting to ../pages/supervisee.php");
?>