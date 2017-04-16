<?php
    require("../config.php");
    require("../basicFunctions.php");
	doLogInCheck();

    //Get Supervisees
    $query = "
        SELECT
          *
        FROM Security_Officer
        WHERE Super_SSN IS NOT NULL
        ORDER BY Last_Name
    ";

    try{
        $supervisees = $db->prepare($query);
        $result = $supervisees->execute();
        $supervisees->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

    //Get officers
    $query = "
        SELECT
          *
        FROM Security_Officer
        ORDER BY Last_Name
    ";

    try{
        $officers = $db->prepare($query);
        $result = $officers->execute();
        $officers->setFetchMode(PDO::FETCH_ASSOC);
        $officers2 = $db->prepare($query);
        $result2 = $officers2->execute();
        $officers2->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

    //Get Supervisors
    $query = "
        SELECT
            *
        FROM Security_Officer
        WHERE
        Super_SSN IS NULL
    ";

    try{
        $supers = $db->prepare($query);
        $result = $supers->execute();
        $supers->setFetchMode(PDO::FETCH_ASSOC);
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

    <title>Shift Management System</title>

    <!-- Bootstrap Validator-->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.0/css/bootstrapValidator.min.css"/>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.0/js/bootstrapValidator.min.js"> </script>


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
                <a class="navbar-brand" href="home.php">System Administrator Terminal</a>
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
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <?php if(isSysAdmin($_SESSION['User_UUID'])) { ?>
                        <li>
                            <a href="officers.php"><i class="fa fa-users fa-fw"></i> Officers</a>
                        </li>
                        <?php } ?>
                        <?php if(isSuperUser($_SESSION['User_UUID'])) { ?>
                        <li>
                            <a href="user.php"><i class="fa fa-user fa-fw"></i> Profile</a>
                        </li>
                        <?php } else { ?>
                          <li>
                              <a href="user.php"><i class="fa fa-user fa-fw"></i> Profile</a>
                          </li>
                        <?php } ?>
                        <li>
                            <a href="alarms.php"><i class="fa fa-exclamation-triangle fa-fw"></i> Alarms</a>
                        </li>
                        <li>
                            <a href="tickets.php"><i class="fa fa-ticket fa-fw"></i> Tickets</a>
                        </li>
                        <?php if(isSuperUser($_SESSION['User_UUID'])) { ?>
                        <li>
                            <a href="shifts.php"><i class="fa fa-calendar fa-fw"></i> Shifts</a>
                        </li>
                        <?php } ?>
                        <li>
                            <a href="buildings.php"><i class="fa fa-building fa-fw"></i> Buildings</a>
                        </li>
                        <li>
                            <a href="spots.php"><i class="fa fa-map fa-fw"></i> Spots</a>
                        </li>
                        <li>
                            <a href="cameras.php"><i class="fa fa-camera fa-fw"></i> Cameras</a>
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
                    <h1 class="page-header">Officer Management</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Officers</h4><b>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Supervisor</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $officers->fetch()) { ?>
                                <tr>
                                  <td><?php echo $row['Last_Name']; ?></td>
                                  <td><?php echo $row['First_Name']; ?></td>
                                  <td>(<?php echo substr($row['Phone_Number'], 0, 3); ?>) <?php echo substr($row['Phone_Number'], 3, 3); ?> - <?php echo substr($row['Phone_Number'], 6, 4); ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php echo $row['Address'];  ?></td>
                                  <td><?php

                                  $query = "
                                      SELECT
                                        Last_Name, First_Name, SSN
                                      FROM Security_Officer
                                      WHERE
                                      SSN = :ssn
                                  ";

                                  $query_params = array(
                                      ':ssn' => $row['Super_SSN']
                                  );

                                  try{
                                      $supervisor = $db->prepare($query);
                                      $result = $supervisor->execute($query_params);
                                  }
                                  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                  $row2 = $supervisor->fetch();
                                  ?>
                                  <form action="../php/updateOfficer.php" method="post" role="form" data-toggle="validator">
                                    <div class="form-group">
                                      <input type="hidden" value="<?php echo $row['SSN']; ?>" name="off_ssn" id="off_ssn">
                                        <div>
                                            <select class="form-control" id="super" name="super">
                                              <?php if($row2) { ?>
                                              <option value="<?php echo $row2['SSN']; ?>" selected disabled><?php echo $row2['Last_Name']; ?>, <?php echo $row2['First_Name'] ?></option>
                                              <?php } else {?>
                                                <option value="" selected disabled>None</option>
                                              <?php } ?>
                                              <?php $result2 = $officers2->execute();
                                                $officers2->setFetchMode(PDO::FETCH_ASSOC);
                                                while($row3 = $officers2->fetch()) { ?>
                                                <option value="<?php echo $row3['SSN']; ?>"><?php echo $row3['Last_Name']; ?>, <?php echo $row3['First_Name']; ?></option>
                                              <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                  </td>
                                  <td>
                                      <div class="form-group">
                                        <button type="submit" tabindex="4" class="form-control btn btn-xs btn-success"><i class="fa fa-check fa-fw"></i></button>
                                      </div>
                                  </form>
                                  </td>
                                  <td>
                                    <form action="../php/delete_officer.php" method="post" role="form" data-toggle="validator">
                                      <div class="form-group">
                                        <input type="hidden" value="<?php echo $row['SSN']; ?>" name="delete" id="delete">
                                        <button type="submit" tabindex="4" class="form-control btn btn-xs btn-danger"><i class="fa fa-trash fa-fw"></i></button>
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
                            <h4><b>Supervisors</b></h4>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Supervisees</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $supers->fetch()) { ?>
                                <tr>
                                  <td><?php echo $row['Last_Name']; ?></td>
                                  <td><?php echo $row['First_Name']; ?></td>
                                  <td><?php echo $row['Phone_Number']; ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php echo $row['Address']; ?></td>
                                  <td><ul><?php $query = "
                                        SELECT
                                          *
                                        FROM Security_Officer
                                        WHERE Super_SSN = :ssn
                                    ";

                                    $query_params = array(
                                      ':ssn' => $row['SSN']
                                    );

                                    try{
                                        $supervs = $db->prepare($query);
                                        $result = $supervs->execute($query_params);
                                        $supervs->setFetchMode(PDO::FETCH_ASSOC);
                                    }
                                    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                    while($row2 = $supervs->fetch()) { ?>
                                      <li><?php echo $row2['First_Name']; ?> <?php echo $row2['Last_Name']; ?></li>
                                    <?php } ?>
                                  </ul>
                                </td>
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
                        <form class="form-horizontal" data-toggle="validator" action="../php/newOfficer.php" method="post" role="form">
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Last Name:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="last" id="last" type="text" placeholder="Last Name" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">First Name:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="first" id="first" type="text" placeholder="First Name" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">SSN:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="ssn" id="ssn" type="text" data-minlength="9" data-minlength-error="Invalid Social Security Number" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Phone Number:</label>
                            <div class="col-lg-8">
                              <input class="form-control" data-minlength="10" data-minlength-error="Invalid Phone Number" name="phone" id="phone" type="text" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Email:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="email" id="email" type="text" placeholder="you@email.com" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Address:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="address" id="address" type="text" placeholder="Address" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Supervisor:</label>
                            <div class="col-lg-8">
                              <label for="super">Select an Officer:</label>
                                <select class="form-control" id="super" name="super">
                                  <?php $result = $officers->execute();
                                  $officers->setFetchMode(PDO::FETCH_ASSOC);
                                  while($row = $officers->fetch()) { ?>
                                    <option value="<?php echo $row['SSN'] ?>"><?php echo $row['Last_Name'] ?>, <?php echo $row['First_Name'] ?></option>
                                  <?php } ?>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-8">
                              <input type="submit" class="btn btn-primary" value="Add Officer">
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

    <!-- Bootstrap Validator JavaScript -->
    <script src="../node_modules/bootstrap-validator/dist/validator.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
