<?php
    require("../config.php");
    require("../basicFunctions.php");
	doLogInCheck();

    //Get Unresolved Alarm Alerts
    $query = "
        SELECT
            *
        FROM Ticket
        ORDER BY Time_Created
    ";

    try{
        $tickets = $db->prepare($query);
        $result = $tickets->execute();
        $tickets->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_tickets = $tickets->rowCount();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Ticket System</title>

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
                    <h1 class="page-header">Tickets</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            There are currently <b><?php echo $num_tickets?></b> tickets.
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Time Created</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $tickets->fetch()) { ?>
                                <tr>
                                  <td><?php echo $row['Time_Created']; ?></td>
                                  <td><?php echo $row['Name']; ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php echo $row['Description'];?></td>
                                  <td><form action="../php/resolve_ticket.php" method="post" role="form" data-toggle="validator">
                                    <div class="form-group">
                                      <input type="hidden" value="<?php echo $row['Ticket_UUID']; ?>" name="resolve" id="resolve">
                                      <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-primary" value="Resolve">
                                    </div>
                                  </td>
                                </tr>
                                <?php } ?>
                            <tbody>
                          </table>
                    </div>

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
