<?php
    require("../config.php");
    if(!empty($_POST))
    {
        // Add building to database
        $query = "
            INSERT INTO Camera (
                Brand,
                Model,
                Serial_Num,
                Resolution_Width,
                Resolution_Height,
                Spot_UUID
            ) VALUES (
                :brand,
                :model,
				        :sn,
                :width,
                :height,
                :spot
            )
        ";

        $query_params = array(
            ':brand' => $_POST['brand'],
            ':model' => $_POST['model'],
            ':sn' => $_POST['sn'],
            ':width' => $_POST['width'],
            ':height' => $_POST['height'],
            ':spot' => $_POST['spot']
        );

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

        header("Location: ../pages/cameras.php");
        die("Redirecting to: ../pages/cameras.php");
    }
?>
