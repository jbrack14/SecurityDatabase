<?php
    require("../config.php");
    if(empty($_SESSION['user']))
    {
        header("Location: ../index.html");
        die("Redirecting to ../index.html");
    }

    //Get Unresolved Alarm Alerts
    if(!empty($_POST)){
        $query = "
            SELECT
                *
            FROM Alarm_Event
            WHERE
            Is_Resolved = false
        ";

        try{
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $num = $stmt->num_rows;
        
        if($num)
        {
          echo $num;
        } else {
          echo "0";
        }
    }
?>
