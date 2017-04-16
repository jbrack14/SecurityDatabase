<?php
    require_once("../config.php");
    if(!empty($_POST))
    {
        // Add building to database
        $query = "
            INSERT INTO Building (
                Name,
                Street_Num,
                Street_Name,
                Zip_Code
            ) VALUES (
                :name,
                :street_num,
				        :street_name,
                :zip
            )
        ";

        $query_params = array(
            ':name' => $_POST['name'],
            ':street_num' => $_POST['street_num'],
            ':street_name' => $_POST['street_name'],
            ':zip' => $_POST['zip']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/buildings.php");
        die("Redirecting to: ../pages/buildings.php");
    }
?>
