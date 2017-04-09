<?php
    require("../config.php");
    $submitted_username = '';
    if(!empty($_POST)){
        $query = "
            SELECT
                username,
                password,
                salt,
                email,
                Officer_SSN
            FROM users
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
            $check_ssn = $user_row['Officer_SSN'];
        }

        $query = "
            SELECT
                1
            FROM Supervisor
            WHERE
                ssn = :ssn
        ";
        $query_params = array(
            ':ssn' => $check_ssn
        );

        try{
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
        if($row){
          $is_super = true;
        }

        if($login_ok){
            unset($user_row['salt']);
            unset($user_row['password']);
            $_SESSION['user'] = $user_row;
            if($is_super)
            {
              header("Location: ../pages/super_home.php");
              die("Redirecting to: ../pages/super_home.php");
            }
            else {
              header("Location: ../pages/home.php");
              die("Redirecting to: ../pages/home.php");
            }

        }
        else{
            print("Login Failed.");
            $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
        }
    }
?>
