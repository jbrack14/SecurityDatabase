<?php
    require_once("../config.php");
    $submitted_username = '';
    if(!empty($_POST)){
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
            ':username' => $_POST['username']
        );

        try{
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $login_ok = false;
        $user_row = $stmt->fetch();
        if($user_row){
            $check_password = hash('sha256', $_POST['password'] . $user_row['salt']);
            for($round = 0; $round < 65536; $round++){
                $check_password = hash('sha256', $check_password . $user_row['salt']);
            }
            if($check_password === $user_row['password']){
                $login_ok = true;
            }
        }

        if($login_ok){
            unset($user_row['salt']);
            unset($user_row['password']);
			      $_SESSION['User_UUID'] = $user_row['Account_UUID'];
            //print("logging in..." . $user_row['Account_UUID'] . '|||x');
			header("Location: ../pages/home.php");
			die("Redirecting to: ../pages/home.php");
        }
        else{
            print("Login Failed.");
            $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
        }
    }
?>
