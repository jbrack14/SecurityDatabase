<?php
    require("../config.php");
    if(empty($_SESSION['user']))
    {
        header("Location: ../index.html");
        die("Redirecting to ../index.html");
    }

    //Get Supervisees
    $query = "
        SELECT
          *
        FROM Security_Officer
        WHERE
        Super_SSN = :ssn
        ORDER BY Last_Name
    ";

    $query_params = array(
        ':ssn' => $_SESSION['user']['Officer_SSN']
    );

    try{
        $supervisees = $db->prepare($query);
        $result = $supervisees->execute($query_params);
        $supervisees->setFetchMode(PDO::FETCH_ASSOC);
        $supervisees2 = $db->prepare($query);
        $result = $supervisees2->execute($query_params);
        $supervisees2->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_supervisees = $supervisees->rowCount();

    //Get Shifts
    $query = "
        SELECT
            *
        FROM Shift_Assignment AS S INNER JOIN Security_Officer AS O ON S.Officer_SSN = O.SSN
        WHERE
        S.Officer_SSN IN
        (SELECT
            SSN
        FROM Security_Officer
        WHERE Super_SSN = :ssn)
    ";

    $query_params = array(
        ':ssn' => $_SESSION['user']['Officer_SSN']
    );

    try{
        $shifts = $db->prepare($query);
        $result = $shifts->execute($query_params);
        $shifts->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_shifts = $shifts->rowCount();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Shift Management System</title>

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
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Shift Management</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Supervisees</h4><b>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Shifts</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $supervisees->fetch()) { ?>
                                <tr>
                                  <td><?php echo $row['Last_Name']; ?></td>
                                  <td><?php echo $row['First_Name']; ?></td>
                                  <td><?php echo $row['Phone_Number']; ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php

                                  $query = "
                                      SELECT
                                        *
                                      FROM Shift_Assignment
                                      WHERE
                                      Officer_SSN = :ssn
                                  ";

                                  $query_params = array(
                                      ':ssn' => $row['SSN']
                                  );

                                  try{
                                      $supervisee_shifts = $db->prepare($query);
                                      $result = $supervisee_shifts->execute($query_params);
                                      $supervisee_shifts->setFetchMode(PDO::FETCH_ASSOC);
                                  }
                                  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                  $num_supervisee_shifts = $supervisee_shifts->rowCount();
                                  echo $num_supervisee_shifts; ?></td>
                                </tr>
                                <?php } ?>
                            <tbody>
                          </table>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>All Shifts</b></h4>
                            There are currently <b> <?php echo $num_shifts ?> </b> shifts under your management.
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Spots</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $shifts->fetch()) { ?>
                                <tr>
                                  <td><?php echo $row['Start_Time']; ?></td>
                                  <td><?php echo $row['End_Time']; ?></td>
                                  <td><?php echo $row['Last_Name']; ?></td>
                                  <td><?php echo $row['First_Name']; ?></td>
                                  <td><ul><?php $query = "
                                        SELECT
                                          *
                                        FROM Spot AS sp NATURAL JOIN Spot_Assignment AS sa
                                        WHERE sa.Shift_UUID = :shift_uuid
                                    ";

                                    $query_params = array(
                                      ':shift_uuid' => $row['Shift_UUID']
                                    );

                                    try{
                                        $spots = $db->prepare($query);
                                        $result = $spots->execute($query_params);
                                        $spots->setFetchMode(PDO::FETCH_ASSOC);
                                    }
                                    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                    while($row2 = $spots->fetch()) { ?>
                                      <li><?php echo $row2['Coverage_Description']; ?></li>
                                    <?php } ?>
                                  </ul>
                                </td>
                                <td>
                                  <form action="../php/delete_shift.php" method="post" role="form" data-toggle="validator">
                                    <div class="form-group">
                                      <input type="hidden" value="<?php echo $row['Shift_UUID']; ?>" name="delete" id="delete">
                                      <button type="submit" tabindex="4" class="form-control btn btn-danger"> Delete </button>
                                    </div>
                                  </form>
                                </td><
                              <?php } ?>
                            <tbody>
                          </table>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-success">
                          <div class="panel-heading">
                              <h4><b>Create New Shift</b></h4>
                          </div>
                          <br>
                        <form class="form-horizontal" action="../php/newShift.php" method="post" role="form">
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Start Time:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="start" id="start" type="time" value="<?php echo $profile['First_Name'] ?>" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">End Time:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="end" id="end" type="time" value="<?php echo $profile['Last_Name'] ?>" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Officer:</label>
                            <div class="col-lg-8">
                              <label for="ssn">Select an Officer:</label>
                                <select class="form-control" id="ssn" name="ssn">
                                  <?php while($row = $supervisees2->fetch()) { ?>
                                    <option value="<?php echo $row['SSN'] ?>"><?php echo $row['Last_Name'] ?>, <?php echo $row['First_Name'] ?></option>
                                  <?php } ?>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Spots:</label>
                            <div class="col-lg-8">
                              <label for="spots">Select Spots:</label>
                                <select multiple="multiple" class="form-control" name="spots[]" id="spots">
                                  <?php
                                  $query = "
                                      SELECT
                                        *
                                      FROM Spot
                                  ";

                                  try{
                                      $spots = $db->prepare($query);
                                      $result = $spots->execute($query_params);
                                      $spots->setFetchMode(PDO::FETCH_ASSOC);
                                  }
                                  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                  while($row = $spots->fetch()) { ?>
                                    <option value="<?php echo $row['Spot_UUID']?>"><?php echo $row['Coverage_Description'] ?></option>
                                  <?php } ?>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-8">
                              <input type="submit" class="btn btn-primary" value="Create Shift">
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
