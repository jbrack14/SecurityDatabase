<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    //Get All Videos
    $query = "
        SELECT
          Start_Time, Resolution_Height, Resolution_Width, Duration_us, Record_UUID
        FROM Surveillance_Video
        ORDER BY Start_Time
    ";

    try{
        $videos = $db->prepare($query);
        $result = $videos->execute();
        $videos->setFetchMode(PDO::FETCH_ASSOC);
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
                    <h1 class="page-header">Surveillance Video Terminal</h1>
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
                                    <th></th>
                                    <th>Start Time</th>
                                    <th>Duration</th>
                                    <th>Spot</th>
                                    <th>Resolution</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $videos->fetch()) { ?>
                                <tr>
                                  <td><?php echo $row['Thumbnail'];?></td>
                                  <td><?php echo $row['Start_Time']; ?></td>
                                  <td><?php echo $row['Duration_us']; ?></td>
                                  <td><?php  $query = "
                                        SELECT
                                          Coverage_Description
                                        FROM Spot
                                        WHERE
                                        Spot_UUID = (
                                          SELECT Spot_UUID
                                          FROM Camera
                                          WHERE Camera_UID = :camera
                                          )                                  ";

                                    $query_params = array(
                                        ':camera' => $row['Camera_UID']
                                    );

                                    try{
                                        $spot = $db->prepare($query);
                                        $result = $spot->execute($query_params);
                                    }
                                    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                    echo implode(', ', $spot->fetch())?>
                                  </td>
                                  <td><?php echo $row['Resolution_Height']; ?> x <?php echo $row['Resolution_Height']; ?></td>
                                  <td><form action="../php/delete_camera.php" method="post" role="form" data-toggle="validator">
                                    <div class="col-sm-8 col-sm-offset-2 video-link medium-paragraph">
                                        <a href="#" class="launch-modal" data-modal-id="modal-video">
                                            <span class="video-link-icon"><i class="fa fa-play"></i></span>
                                            <span class="video-link-text">Launch modal video</span>
                                        </a>
                                    </div>
                                  </form>
                                  </td>
                                </tr>
                                <div class="modal fade" id="modal-video" tabindex="-1" role="dialog" aria-labelledby="modal-video-label">
                                  <div class="modal-dialog" role="document">
                                      <div class="modal-content">
                                          <div class="modal-header">
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                  <span aria-hidden="true">&times;</span>
                                              </button>
                                          </div>
                                          <div class="modal-body">
                                              <div class="modal-video">
                                                <div content="Content-Type: video/mp4">
                                                    <video width="<?php echo $row['Resolution_Width']; ?>" height="<?php echo $row['Resolution_Height']; ?>" controls="controls" poster="image" preload="metadata">
                                                      <?php  $query = "
                                                            SELECT
                                                              Video_Data
                                                            FROM Surveillance_Video
                                                            WHERE
                                                            Record_UUID = :record                          ";

                                                        $query_params = array(
                                                            ':record' => $row['Record_UUID']
                                                        );

                                                        try{
                                                            $video = $db->prepare($query);
                                                            $result = $video->execute($query_params);
                                                        }
                                                        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                                        ?>
                                                        <source src="<?php echo $video->fetch(PDO::FETCH_COLUMN); ?>" type="video/mp4"/>;
                                                    </video>
                                                </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                </div>
                                <?php } ?>
                            <tbody>
                          </table>
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