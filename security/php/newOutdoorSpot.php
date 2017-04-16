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
            INSERT INTO Outdoor_Spot (
                Coverage_Description,
                Street,
                Location
            ) VALUES (
                :coverage,
                :street,
				        :location
            )
        ";

        $query_params = array(
            ':coverage' => $_POST['coverage'],
            ':street' => $_POST['street'],
            ':location' => $_POST['location']
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
