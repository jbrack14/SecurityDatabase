<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    //Get Number Unresolved Tickets
    $query = "
        SELECT
            *
        FROM Ticket
        WHERE Result IS NULL
    ";

    try{
        $tickets = $db->prepare($query);
        $result = $tickets->execute();
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_tickets = $tickets->rowCount();

    //Get Unresolved Alarm Alerts
    $query = "
        SELECT
            *
        FROM Alarm_Event
        WHERE
        Resolved_Time = null
    ";

    try{
        $stmt = $db->prepare($query);
        $result = $stmt->execute();
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_alarms = $stmt->rowCount();

    //Get Shifts

    $query = "
        SELECT
            *
        FROM Shift_Assignment
        WHERE
        Officer_SSN = :ssn
    ";

    $query_params = array(
        ':ssn' => getUserSSN()
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

    <title>Security Supervisor Terminal</title>

    <!-- Bootstrap Core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="../vendor/morrisjs/morris.css" rel="stylesheet">

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
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-exclamation-triangle fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div id="alarms" class="huge"><?php echo $num_alarms?></div>

                                    <div>Unresolved Alarms!</div>
                                </div>
                            </div>
                        </div>
                        <a href="alarms.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-ticket fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $num_tickets?></div>
                                    <div>Support Tickets!</div>
                                </div>
                            </div>
                        </div>
                        <a href="tickets.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-film fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div>Surveillance Videos</div>
                                </div>
                            </div>
                        </div>
                        <a href="videos.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php if(isSuperUser($_SESSION['User_UUID'])) { ?>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-group fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div>Manage Shifts</div>
                                </div>
                            </div>
                        </div>
                        <a href="shifts.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php } ?>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Your Shifts
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                          <table width="100%" class="table table-striped table-bordered table-hover" id="shifts_table">
                              <thead>
                                  <tr class="warning">
                                      <th>Start Time</th>
                                      <th>End Time</th>
                                      <th>Spots</th>
                                      <th>Duration</th>
                                      <th>Date Created</th>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php while($row = $shifts->fetch()) { ?>
                                  <tr class="warning">
                                    <td><?php echo $row['Start_Time']; ?></td>
                                    <td><?php echo $row['End_Time']; ?></td>
                                    <td><?php
                                    $query = "
                                        SELECT
                                            Coverage_Description
                                        FROM Spot as S
                                        WHERE S.Spot_UUID IN
                                        (SELECT Spot_UUID
                                        FROM Spot_Assignment
                                        WHERE Shift_UUID = :shift)
                                    ";

                                    $query_params = array(
                                        ':shift' => $row['Shift_UUID']
                                    );

                                    try{
                                        $spots = $db->prepare($query);
                                        $result = $spots->execute($query_params);
                                        $spots->setFetchMode(PDO::FETCH_ASSOC);
                                    }
                                    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }?>
                                    <ul>
                                    <?php while($spot_row = $spots->fetch()) { ?>
                                    <li>
                                    <?php echo $spot_row['Coverage_Description']; ?>
                                    </li>
                                    <?php }?>
                                    </ul>
                                    </td>
                                    <td><?php echo ($row['Duration_s']/3600); ?></td>
                                    <td><?php echo $row['Created_Time']; ?></td>
                                  </tr>
                                  <?php } ?>
                              <tbody>
                            </table>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-4 -->
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

    <!-- Morris Charts JavaScript -->
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../data/morris-data.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
