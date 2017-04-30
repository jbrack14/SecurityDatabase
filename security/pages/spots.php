<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    //Get Indoor Spots
    $query = "
        SELECT
          *
        FROM Indoor_Spot
        ORDER BY Status, Building_Name
    ";

    try{
        $indoor_spots = $db->prepare($query);
        $result = $indoor_spots->execute();
        $indoor_spots->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

    //Get Outdoor Spots
    $query = "
        SELECT
            *
        FROM Outdoor_Spot
        ORDER BY Status
    ";

    try{
        $outdoor_spots = $db->prepare($query);
        $result = $outdoor_spots->execute();
        $outdoor_spots->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

    //Get Buildings
    $query = "
        SELECT
          *
        FROM Building
    ";

    try{
        $buildings = $db->prepare($query);
        $result = $buildings->execute();
        $buildings->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Spot Management System</title>

    <!-- Bootstrap Core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">

<?php 
	$isLoadingNavBar = true;
	require("navBar.php"); 
	$isLoadingNavBar = false;
?>
            
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Spot Management</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Indoor Spots</h4><b>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Building</th>
                                    <th>Floor Number</th>
                                    <th>Room Number</th>
                                    <th>Description</th>
                                    <th>Shifts</th>
                                    <th>Cameras<th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $indoor_spots->fetch()) { ?>
                                <tr <?php if($row['Status'] == "INACTIVE"){ echo 'class="danger"'; } ?>>
                                  <td><?php echo $row['Building_Name']; ?></td>
                                  <td><?php echo $row['Floor_Num']; ?></td>
                                  <td><?php echo $row['Room_Num']; ?></td>
                                  <td><?php echo $row['Coverage_Description']; ?></td>
                                  <td><ul><?php
                                  $query = "
                                      SELECT
                                        *
                                      FROM Shift_Assignment
                                      WHERE
                                      Shift_UUID IN (
                                        SELECT
                                        Shift_UUID
                                        FROM Spot_Assignment
                                        WHERE Spot_UUID = :spot_id
                                        )
                                  ";

                                  $query_params = array(
                                      ':spot_id' => $row['Spot_UUID']
                                  );

                                  try{
                                      $spot_shifts = $db->prepare($query);
                                      $result = $spot_shifts->execute($query_params);
                                      $spot_shifts->setFetchMode(PDO::FETCH_ASSOC);
                                  }
                                  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

                                  while($row2 = $spot_shifts->fetch()) {?>
                                    <li><?php echo $row2['Start_Time']; ?> - <?php echo $row2['End_Time'];   $query = "
                                          SELECT
                                            Last_Name, First_Name
                                          FROM Security_Officer
                                          WHERE
                                          SSN = :ssn
                                      ";

                                      $query_params = array(
                                          ':ssn' => $row2['Officer_SSN']
                                      );

                                      try{
                                          $officer = $db->prepare($query);
                                          $result = $officer->execute($query_params);
                                      }
                                      catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                      ?> : <?php echo implode(', ', $officer->fetch())?>
                                    </li>
                                  <?php } ?>
                                  </td>
                                  <td><?php
                                  $query = "
                                      SELECT
                                        Brand, Model, Camera_UID
                                      FROM Camera
                                      WHERE
                                      Spot_UUID = :spot_id
                                  ";

                                  $query_params = array(
                                      ':spot_id' => $row['Spot_UUID']
                                  );

                                  try{
                                      $cameras = $db->prepare($query);
                                      $result = $cameras->execute($query_params);
                                      $cameras->setFetchMode(PDO::FETCH_ASSOC);
                                  }
                                  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }?>
                                  <ul>
                                  <?php while($row2 = $cameras->fetch()) {?>
                                    <li>
                                    <?php echo $row2['Brand']; ?> - <?php echo $row2['Model'];?> (<?php echo substr($row2['Camera_UID'], -4)?>)
                                    </li>
                                  <?php } ?>
                                  </ul>
                                  </td>
                                  <td class="col-md-2"><form action="../php/update_spot.php" method="post" role="form" data-toggle="validator">
                                    <b>Status:</b>
                                    <select style="font-size: 12px;" class="form-control" id="status" name="status" <?php
                                    $query = "
                                        SELECT
                                          Status
                                        FROM Building
                                        WHERE
                                        Name = :name
                                    ";

                                    $query_params = array(
                                        ':name' => $row['Building_Name']
                                    );

                                    try{
                                        $building_status = $db->prepare($query);
                                        $result = $building_status->execute($query_params);
                                    }
                                    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                    $b_stat = $building_status->fetch();
                                    if($b_stat['Status'] == "INACTIVE"){ echo "disabled";}?>
                                    >
                                        <option value="ACTIVE" <?php if($row['Status']=="ACTIVE"){echo "selected";} ?> >ACTIVE</option>
                                        <option value="INACTIVE" <?php if($row['Status']=="INACTIVE"){echo "selected";} ?> >INACTIVE</option>
                                    </select>
                                    <div class="form-group">
                                      <input type="hidden" value="<?php echo $row['Spot_UUID']; ?>" name="uuid" id="uuid">
                                      <button type="submit" tabindex="4" class="form-control btn btn-xs btn-success"><i class="fa fa-check fa-fw"></i></button>
                                    </div>
                                  </form>
                                  </td>
                                </tr>
                                <?php } ?>
                            <tbody>
                          </table>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Outdoor Spots</h4><b>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Street</th>
                                    <th>Coverage Description</th>
                                    <th>Shifts</th>
                                    <th>Cameras</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $outdoor_spots->fetch()) { ?>
                                <tr <?php if($row['Status'] == "INACTIVE") { echo 'class="danger"';}?>>
                                  <td><?php echo $row['Location']; ?></td>
                                  <td><?php echo $row['Street']; ?></td>
                                  <td><?php echo $row['Coverage_Description']; ?></td>
                                  <td><ul><?php

                                  $query = "
                                      SELECT
                                        *
                                      FROM Shift_Assignment
                                      WHERE
                                      Shift_UUID IN (
                                        SELECT
                                        Shift_UUID
                                        FROM Spot_Assignment
                                        WHERE Spot_UUID = :spot_id
                                        )
                                  ";

                                  $query_params = array(
                                      ':spot_id' => $row['Spot_UUID']
                                  );

                                  try{
                                      $spot_shifts = $db->prepare($query);
                                      $result = $spot_shifts->execute($query_params);
                                      $spot_shifts->setFetchMode(PDO::FETCH_ASSOC);
                                  }
                                  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

                                  while($row2 = $spot_shifts->fetch()) {?>
                                    <li><?php echo $row2['Start_Time']; ?> - <?php echo $row2['End_Time'];   $query = "
                                          SELECT
                                            Last_Name, First_Name
                                          FROM Security_Officer
                                          WHERE
                                          SSN = :ssn
                                      ";

                                      $query_params = array(
                                          ':ssn' => $row2['Officer_SSN']
                                      );

                                      try{
                                          $officer = $db->prepare($query);
                                          $result = $officer->execute($query_params);
                                      }
                                      catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                      ?> : <?php echo implode(', ', $officer->fetch())?>
                                    </li>
                                  <?php } ?>
                                </ul>
                                  </td>
                                  <td><?php
                                  $query = "
                                      SELECT
                                        Brand, Model, Camera_UID
                                      FROM Camera
                                      WHERE
                                      Spot_UUID = :spot_id
                                  ";

                                  $query_params = array(
                                      ':spot_id' => $row['Spot_UUID']
                                  );

                                  try{
                                      $cameras = $db->prepare($query);
                                      $result = $cameras->execute($query_params);
                                      $cameras->setFetchMode(PDO::FETCH_ASSOC);
                                  }
                                  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }?>
                                  <ul>
                                  <?php while($row2 = $cameras->fetch()) {?>
                                  <li>
                                    <?php echo $row2['Brand']; ?> - <?php echo $row2['Model'];?> (<?php echo substr($row2['Camera_UID'], -4)?>)
                                  </li>
                                  <?php } ?>
                                  </ul>
                                  </td>
                                  <td class="col-md-2"><form action="../php/update_outdoor_spot.php" method="post" role="form" data-toggle="validator">
                                    <b>Status:</b>
                                    <select style="font-size: 12px;" class="form-control" id="status" name="status">
                                        <option value="ACTIVE" <?php if($row['Status']=="ACTIVE"){echo "selected";} ?> >ACTIVE</option>
                                        <option value="INACTIVE" <?php if($row['Status']=="INACTIVE"){echo "selected";} ?> >INACTIVE</option>
                                    </select>
                                    <div class="form-group">
                                      <input type="hidden" value="<?php echo $row['Spot_UUID']; ?>" name="uuid" id="uuid">
                                      <button type="submit" tabindex="4" class="form-control btn btn-xs btn-success"><i class="fa fa-check fa-fw"></i></button>
                                    </div>
                                  </form>
                                  </td>
                                </tr>
                                <?php } ?>
                            <tbody>
                          </table>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-success">
                          <div class="panel-heading">
                              <h4><b>Create New <b>Indoor</b> Spot</b></h4>
                          </div>
                          <br>
                        <form class="form-horizontal" action="../php/newIndoorSpot.php" method="post" role="form">
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Building:</label>
                            <div class="col-lg-8">
                              <label for="name">Select a Building:</label>
                                <select class="form-control" id="name" name="name">
                                  <?php while($row = $buildings->fetch()) { ?>
                                    <option value="<?php echo $row['Name'] ?>" <?php if($row['Status'] == "INACTIVE"){ echo 'select disabled';}?>><?php echo $row['Name'] ?></option>
                                  <?php } ?>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Floor Number:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="floor" id="floor" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Room Number:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="room" id="room" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Hallway / Room:</label>
                            <div class="col-lg-8">
                              <label><input name="is" id="is" type="checkbox" value="">This spot is inside a room.</label>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Coverage Description:</label>
                            <div class="col-lg-8">
                              <textarea class="form-control" rows="4" name="coverage" id="coverage"></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-8">
                              <input type="submit" class="btn btn-primary" value="Create Spot">
                            </div>
                          </div>
                        </form>
                    <hr>
                  </div>
                    <!-- /.panel -->
                </div>

                <div class="panel panel-info">
                    <div class="panel-success">
                      <div class="panel-heading">
                          <h4><b>Create New <b>Outdoor</b> Spot</b></h4>
                      </div>
                      <br>
                    <form class="form-horizontal" action="../php/newOutdoorSpot.php" method="post" role="form">
                      <div class="form-group">
                        <label class="col-lg-2 control-label">Location:</label>
                        <div class="col-lg-8">
                          <input class="form-control" name="location" id="location" required>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label">Street:</label>
                        <div class="col-lg-8">
                          <input class="form-control" name="street" id="street" required>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label">Coverage Description:</label>
                        <div class="col-lg-8">
                          <textarea class="form-control" rows="4" name="coverage" id="coverage"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-8">
                          <input type="submit" class="btn btn-primary" value="Create Spot">
                        </div>
                      </div>
                    </form>
                <hr>
              </div>
                <!-- /.panel -->
            </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
