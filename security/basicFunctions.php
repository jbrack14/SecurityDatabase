<?php
    function isLoggedIn()
	{
		if(empty($_SESSION['user']))
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
	
	function isSuperUser()
	{
		
	}
?>