<?php
    require_once("config.php");

    function isLoggedIn()
	{
		if(empty($_SESSION['User_UUID']))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function isSessionExpired()
	{
		//TODO: In future, we will limit the time for each session.
		return false;
	}

  function getUserSSN()
  {
  	global $db;
    //Get Officer SSN
    $query = "
        SELECT
            Officer_SSN
        FROM User_Accounts
        WHERE
        Account_UUID = :uuid
    ";

    $query_params = array(
        ':uuid' => $_SESSION['User_UUID']
    );
    try{
        $account_officer = $db->prepare($query);
        $result = $account_officer->execute($query_params);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $officer_ssn = $account_officer->fetch();
    return implode(',', $officer_ssn);
  }

  function getUsername()
  {
  	global $db;
    //Get Officer SSN
    $query = "
        SELECT
        Username
        FROM User_Accounts
        WHERE
        Account_UUID = :uuid
    ";

    $query_params = array(
        ':uuid' => $_SESSION['User_UUID']
    );
    try{
        $account_officer = $db->prepare($query);
        $result = $account_officer->execute($query_params);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $officer_ssn = $account_officer->fetch();
    return implode(',', $officer_ssn);
  }


	function doLogInCheck()
	{
		if(isLoggedIn())
		{
			if(isSessionExpired())
			{
				header("Location: ../index.html");
        		die("Your session has been expired! Redirecting to ../index.html");
			}
			else
			{
				//return;
			}
		}
		else
		{
			header("Location: ../index.html");
        	die("Please login first! Redirecting to ../index.html");
		}
	}

	function isSuperUser($UserUUID)
	{
		global $db;

		$query = "
		SELECT 1
		FROM Security_Officer JOIN User_Accounts ON Security_Officer.SSN = User_Accounts.Officer_SSN
		WHERE
			User_Accounts.Account_UUID = :user_UUID
			AND
			(
				(Security_Officer.Super_SSN IS NULL)
				OR
				(Security_Officer.SSN IN (SELECT DISTINCT Super_SSN FROM Security_Officer))
			)
		";

		$query_params = array(
			':user_UUID' => $UserUUID
		);

		try
		{
			$stmt = $db->prepare($query);
			$qResult = $stmt->execute($query_params);
		}
    	catch(PDOException $ex)
		{
			die("Failed to run query: " . $ex->getMessage());
		}

		$result = false;
		if($stmt->fetch())
		{
		  $result = true;
		}

		return $result;
	}

	function isSysAdmin($UserUUID)
	{
		global $db;

    $query = "
		SELECT 1
		FROM System_Administrator
    WHERE Officer_SSN = :ssn
		";

    $query_params = array(
			':ssn' => getUserSSN()
		);

    try
		{
			$stmt = $db->prepare($query);
			$qResult = $stmt->execute($query_params);
		}
    	catch(PDOException $ex)
		{
			die("Failed to run query: " . $ex->getMessage());
		}

    $result = false;
		if($stmt->fetch())
		{
		  $result = true;
		}

		return $result;

	}

  function formatDurationUS($microseconds ) {
    return formatDurationS(($microseconds/1000000));
  }

  function formatDurationS($durationInSeconds) {
    $duration = '';
    $days = floor($durationInSeconds / 86400);
    $durationInSeconds -= $days * 86400;
    $hours = floor($durationInSeconds / 3600);
    $durationInSeconds -= $hours * 3600;
    $minutes = floor($durationInSeconds / 60);
    $seconds = $durationInSeconds - $minutes * 60;

    if($days > 0) {
      $duration .= $days . 'd';
    }
    if($hours > 0) {
      $duration .= ' ' . $hours . 'h';
    }
    if($minutes > 0) {
      $duration .= ' ' . $minutes . 'm';
    }
    if($seconds > 0) {
      $duration .= ' ' . $seconds . 's';
    }
    return $duration;
  }
?>
