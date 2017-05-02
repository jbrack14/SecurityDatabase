<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
    if(!empty($_POST))
    {

        // Check old password
        $query = "
            SELECT
				        Account_UUID,
                username,
                password,
                salt
            FROM User_Accounts
            WHERE
                username = :username
        ";
        $query_params = array(
            ':username' => getUsername()
        );

        try{
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $login_ok = false;
        $user_row = $stmt->fetch();
        if($user_row){
            $check_password = hash('sha256', $_POST['current'] . $user_row['salt']);
            for($round = 0; $round < 65536; $round++){
                $check_password = hash('sha256', $check_password . $user_row['salt']);
            }
            if($check_password === $user_row['password']){
                $login_ok = true;
            }
        }

        if(!$login_ok){
          $_SESSION['profile_page_set_password'] = false;
          header("Location: ../pages/user.php");
          die("Error incorrect password. Redirecting to ../pages/user.php");
        }

        // Add account to database
        $query = "
            UPDATE User_Accounts
            SET password = :password,
                salt = :salt
  				  WHERE Username = :username
        ";

        // Security measures
        $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
        $password = hash('sha256', $_POST['inputPassword'] . $salt);
        for($round = 0; $round < 65536; $round++){ $password = hash('sha256', $password . $salt); }
        $query_params = array(
            ':password' => $password,
            ':salt' => $salt,
            ':username'=> getUsername()
        );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/user.php");
        die("Redirecting to ../pages/user.php");
    }
?>
