<?php
    require_once("../config.php");
    if(!empty($_POST))
    {
        // Add building to database
        if($_POST['is'] == null){
          $is = 0;
        }
        else{
          $is = 1;
        }

        $query = "
            INSERT INTO Indoor_Spot (
                Building_Name,
                Floor_Num,
                Room_Num,
                Is_Inside_Room,
                Coverage_Description
            ) VALUES (
                :name,
                :floor,
				        :room,
                :is,
                :coverage
            )
        ";

        $query_params = array(
            ':name' => $_POST['name'],
            ':floor' => $_POST['floor'],
            ':room' => $_POST['room'],
            ':is' => $is,
            ':coverage' => $_POST['coverage']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/spots.php");
        die("Redirecting to: ../pages/spots.php");
    }
?>
