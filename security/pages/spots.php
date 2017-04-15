<?php
    require("../config.php");
    require("../basicFunctions.php");
	doLogInCheck();

    //Get Indoor Spots
    $query = "
        SELECT
          *
        FROM Indoor_Spot
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
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home.php">Security Officer Terminal</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="user.php"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="../php/logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        <li>
                            <a href="home.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="user.php"><i class="fa fa-user fa-fw"></i> Profile</a>
                        </li>
                        <li>
                            <a href="alarms.php"><i class="fa fa-exclamation-triangle fa-fw"></i> Alarms</a>
                        </li>
                        <li>
                            <a href="tickets.php"><i class="fa fa-ticket fa-fw"></i> Tickets</a>
                        </li>
                        <li>
                            <a href="shifts.php"><i class="fa fa-users fa-fw"></i> Shifts</a>
                        </li>
                        <li>
                            <a href="buildings.php"><i class="fa fa-building fa-fw"></i> Buildings</a>
                        </li>
                        <li>
                            <a href="spots.php"><i class="fa fa-map fa-fw"></i> Spots</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
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
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $indoor_spots->fetch()) { ?>
                                <tr>
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
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $outdoor_spots->fetch()) { ?>
                                <tr>
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
                                    <option value="<?php echo $row['Name'] ?>"><?php echo $row['Name'] ?></option>
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
