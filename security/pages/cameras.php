<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    //Get Buildings
    $query = "
        SELECT
          *
        FROM Camera
        ORDER BY Status, Spot_UUID
    ";

    try{
        $cameras = $db->prepare($query);
        $result = $cameras->execute();
        $cameras->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

    //Get Spots
    $query = "
        SELECT
          *
        FROM Spot
    ";

    try{
        $spots = $db->prepare($query);
        $result = $spots->execute();
        $spots->setFetchMode(PDO::FETCH_ASSOC);
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

    <title>Camera Management System</title>

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
                        <li>
                            <a href="home.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
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
                        <li>
                            <a href="videos.php"><i class="fa fa-film fa-fw"></i> Videos</a>
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
                    <h1 class="page-header">Camera Management</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Cameras</h4><b>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>ID Number</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Serial Number</th>
                                    <th>Resolution Width</th>
                                    <th>Resolution Height</th>
                                    <th>Spot</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $cameras->fetch()) { ?>
                                <tr <?php if($row['Status'] == "INACTIVE") { echo 'class="danger"';}?>>
                                  <td><?php echo $row['Camera_UID']; ?></td>
                                  <td><?php echo $row['Brand']; ?></td>
                                  <td><?php echo $row['Model']; ?></td>
                                  <td><?php echo $row['Serial_Num']; ?></td>
                                  <td><?php echo $row['Resolution_Width']; ?></td>
                                  <td><?php echo $row['Resolution_Height']; ?></td>
                                  <td><?php  $query = "
                                        SELECT
                                          Coverage_Description
                                        FROM Spot
                                        WHERE
                                        Spot_UUID = :spot                                    ";

                                    $query_params = array(
                                        ':spot' => $row['Spot_UUID']
                                    );

                                    try{
                                        $spot = $db->prepare($query);
                                        $result = $spot->execute($query_params);
                                    }
                                    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                    echo implode(', ', $spot->fetch())?>
                                  </td>
                                  <td class="col-md-2"><form action="../php/update_camera.php" method="post" role="form" data-toggle="validator">
                                    <b>Status:</b>
                                    <select style="font-size: 12px;" class="form-control" id="status" name="status" <?php
                                    $query = "
                                        SELECT
                                          Status
                                        FROM Spot
                                        WHERE
                                        Spot_UUID = :uuid
                                    ";

                                    $query_params = array(
                                        ':uuid' => $row['Spot_UUID']
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
                                      <input type="hidden" value="<?php echo $row['Camera_UID']; ?>" name="uuid" id="uuid">
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
                              <h4><b>Add New Camera</b></h4>
                          </div>
                          <br>
                        <form class="form-horizontal" action="../php/newCamera.php" method="post" role="form">
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Brand:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="brand" id="brand" type="text" value="" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Model:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="model" id="model" type="text" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">S/N:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="sn" id="sn" type="text" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Resolution:</label>
                            <div class="col-lg-3">
                              <div class="input-group">
                                  <input class="form-control" name="width" id="width" type="text" placeholder="Width" required>
                                  <div class="input-group-addon">X</div>
                                  <input class="form-control" name="height" id="height" type="text"  placeholder="Height" required>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Spot:</label>
                            <div class="col-lg-8">
                              <label for="spot">Select a Spot:</label>
                                <select class="form-control" id="spot" name="spot">
                                  <?php while($row = $spots->fetch()) { ?>
                                    <option value="<?php echo $row['Spot_UUID'] ?>"><?php echo $row['Coverage_Description'] ?></option>
                                  <?php } ?>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-8">
                              <input type="submit" class="btn btn-primary" value="Create Camera">
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
