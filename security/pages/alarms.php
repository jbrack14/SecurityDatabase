<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    //Get Unresolved Alarm Alerts
    $query = "
        SELECT
          *
        FROM Alarm_Event NATURAL JOIN Spot
        WHERE
        Resolved_Time IS NULL
        ORDER BY Start_Time
    ";

    try{
        $unresolved = $db->prepare($query);
        $result = $unresolved->execute();
        $unresolved->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_unresolved_alarms = $unresolved->rowCount();

    //Get resolved Alarm Alerts
    $query = "
        SELECT
          *
        FROM Alarm_Event NATURAL JOIN Spot
        WHERE
        NOT Resolved_Time IS NULL
        ORDER BY Start_Time
    ";

    try{
        $resolved = $db->prepare($query);
        $result = $resolved->execute();
        $resolved->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_resolved_alarms = $resolved->rowCount();
	
	if(!empty($_SESSION['alarms_page_alarm_uuid']))
	{
		$query = "
			SELECT
			  Start_Time, End_Time, Spot_UUID
			FROM Alarm_Event
			WHERE
			  Alarm_Event_UUID = :alarm_uuid
		";
		$query_params = array(
		  ':alarm_uuid' => $_SESSION['alarms_page_alarm_uuid']
		);
		try{
			$selectedAlarm = $db->prepare($query);
			$result = $selectedAlarm->execute($query_params);
			$selectedAlarm->setFetchMode(PDO::FETCH_ASSOC);
		}
		catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
		
		if(!($selectedAlarmRow = $selectedAlarm->fetch()))
		{
			unset($_SESSION['video_page_alarm_uuid']);
			header("Location: ../pages/alarms.php");
			die("Redirecting to: ../pages/alarms.php");
		}
		
		$query = "
		SELECT
				S.Thumbnail, S.Start_Time, S.End_Time, S.Duration_us, S.Resolution_Height, S.Resolution_Width, S.Video_Format, S.Record_UUID, S.Camera_UID
		FROM Surveillance_Video AS S inner join (Camera natural join Spot) on Camera.Camera_UID = S.Camera_UID
		WHERE
		Spot.Spot_UUID = :uuid
		AND ( 
			(timestampdiff(SECOND, :AlarmStartTime, S.Start_Time) >= 0
				AND timestampdiff(SECOND, S.Start_Time, :AlarmEndTime) >= 0)
			OR (timestampdiff(SECOND, :AlarmStartTime, S.End_Time) >= 0
				AND timestampdiff(SECOND, S.End_Time, :AlarmEndTime) >= 0)
			OR (timestampdiff(SECOND, S.Start_Time, :AlarmStartTime) >= 0
				AND timestampdiff(SECOND, :AlarmEndTime, S.End_Time) >= 0)
		);
		";

		$query_params = array(
		  ':uuid' => $selectedAlarmRow['Spot_UUID'],
		  ':AlarmStartTime' => $selectedAlarmRow["Start_Time"],
		  ':AlarmEndTime' => $selectedAlarmRow['End_Time']
		);
		
		try{
			$relatedVideo = $db->prepare($query);
			$result = $relatedVideo->execute($query_params);
			$relatedVideo->setFetchMode(PDO::FETCH_ASSOC);
		}
		catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
		
	}
	else if(!empty($_SESSION['alarms_page_video_uuid']))
	{
		$query = "
		SELECT
			Video_Data, Video_Format, Resolution_Height, Resolution_Width
		FROM Surveillance_Video
		WHERE
			Record_UUID = :record
		";
		
		$query_params = array(
			':record' => $_SESSION['alarms_page_video_uuid']
		);
		
		try{
			$video = $db->prepare($query);
			$result = $video->execute($query_params);
		}
		catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
		$videoRow = $video->fetch();
	}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Alarm System</title>

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
                    <h1 class="page-header">Alarms</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            There are currently <b><?php echo $num_unresolved_alarms?></b> unresolved alarms.
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr class="danger">
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Spot Description</th>
                                    <th>Related Videos</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $unresolved->fetch()) { ?>
                                <tr class="danger">
                                  <td><?php echo $row['Start_Time']; ?></td>
                                  <td><?php echo $row['End_Time']; ?></td>
                                  <td><?php echo $row['Coverage_Description']; ?></td>
								  <td>
									<?php
                                      $query = "
									SELECT
											COUNT(*) AS Video_Num
									FROM Surveillance_Video inner join (Camera natural join Spot) on Camera.Camera_UID = Surveillance_Video.Camera_UID
									WHERE
									Spot.Spot_UUID = :uuid
									AND ( 
										(timestampdiff(SECOND, :AlarmStartTime, Surveillance_Video.Start_Time) >= 0
											AND timestampdiff(SECOND, Surveillance_Video.Start_Time, :AlarmEndTime) >= 0)
										OR (timestampdiff(SECOND, :AlarmStartTime, Surveillance_Video.End_Time) >= 0
											AND timestampdiff(SECOND, Surveillance_Video.End_Time, :AlarmEndTime) >= 0)
										OR (timestampdiff(SECOND, Surveillance_Video.Start_Time, :AlarmStartTime) >= 0
											AND timestampdiff(SECOND, :AlarmEndTime, Surveillance_Video.End_Time) >= 0)
									);
									";

									$query_params = array(
									  ':uuid' => $row['Spot_UUID'],
									  ':AlarmStartTime' => $row["Start_Time"],
									  ':AlarmEndTime' => $row['End_Time']
									);

									try
									{
									  $relatedVideoCount = $db->prepare($query);
									  $result = $relatedVideoCount->execute($query_params);
									  $relatedVideoCount->setFetchMode(PDO::FETCH_ASSOC);
									}
									catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
									$relatedVideoCountRow = $relatedVideoCount->fetch();
									echo $relatedVideoCountRow['Video_Num'] . " Video(s).";
									?>
                                    <form action="../php/showAlarmRelatedVideo.php" method="post" role="form" data-toggle="validator">
                                        <div class="form-group">
                                        	<button type="submit" value="<?php echo $row['Alarm_Event_UUID']; ?>" name="alarm_uuid" id="play" class="play-button btn btn-info btn-md">Show Videos</button>
                                        </div>
                                    </form>
								</td>
                                <td>
                                	<form action="../php/resolve_alarm.php" method="post" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <input type="hidden" value="<?php echo $row['Alarm_Event_UUID']; ?>" name="resolve" id="resolve">
                                        <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-sm btn-success" value="Resolve">
                                    </div>
                                    </form>
                                </td>
                                </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                    </div>

                    <hr>
                    
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            There are currently <b><?php echo $num_resolved_alarms?></b> resolved alarms.
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr class="success">
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Resolved Time</th>
                                    <th>Spot Description</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $resolved->fetch()) { ?>
                                <tr class="success">
                                  <td><?php echo $row['Start_Time']; ?></td>
                                  <td><?php echo $row['End_Time']; ?></td>
                                  <td><?php echo $row['Resolved_Time']; ?></td>
                                  <td><?php echo $row['Coverage_Description']; ?></td>
                                </tr>
                                <?php } ?>
                            <tbody>
                          </table>
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
    <?php if(!empty($_SESSION['alarms_page_alarm_uuid'])) { ?>
    <!-- Modal content 1-->
    <div class="modal fade" id="relatedVideoModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Related Videos</h4>
                </div>
                <div class="modal-body">
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
							<?php while($relatedVideoRow = $relatedVideo->fetch()) { ?>
                            <tr>
                                <td>
                                <?php echo '<img height="50" width="50" src="data:image/png;base64,'.base64_encode($relatedVideoRow['Thumbnail']).'"/>'; ?>
                                </td>
                                <td><?php echo $relatedVideoRow['Start_Time']; ?></td>
                                <td><?php echo $relatedVideoRow['Duration_us']; ?></td>
                                <td><?php  $query = "
									SELECT
										Coverage_Description
									FROM Spot
									WHERE
										Spot_UUID = (
										SELECT Spot_UUID
										FROM Camera
										WHERE Camera_UID = :camera
										)
									";
									
									$query_params = array(
									':camera' => $relatedVideoRow['Camera_UID']
									);
									
									try{
									$spot = $db->prepare($query);
									$result = $spot->execute($query_params);
									}
									catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
									echo implode(', ', $spot->fetch())?>
                                </td>
                                <td><?php echo $relatedVideoRow['Resolution_Width']; ?> x <?php echo $relatedVideoRow['Resolution_Height']; ?></td>
                                <td>
                                    <form action="../php/showAlarmRelatedVideo.php" method="post" role="form" data-toggle="validator">
                                    <div class="form-group">
                                    	<button type="submit" value="<?php echo $relatedVideoRow['Record_UUID']; ?>" name="video_uuid" id="play" class="play-button btn btn-info btn-md"><i class="fa fa-play fa-fw"></i> Play Video</button>
                                    </div>
                                    </form>
                                </td>
                            </tr>
                            
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Modal content 1-->
	<?php } else if(!empty($_SESSION['alarms_page_video_uuid'])) { ?>
    <!-- Modal content 2-->
    <div class="modal fade" id="playingVideoModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Playing Video</h4>
                </div>
                <div class="modal-body">
                    <video width="100%" height="" controls>
                    	<?php echo '<source src="data:image/png;base64,'.base64_encode($videoRow['Video_Data']).'" type="video/'.$videoRow['Video_Format'].'"  />'; ?>
                    	Your browser does not support HTML5 video.
                    </video>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Modal content 2-->
	<?php } ?>
    
    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>
    
	<?php if(!empty($_SESSION['alarms_page_alarm_uuid'])) {?>
    <script> 
	$(window).on('load',function()
	{
		$('#relatedVideoModal').modal('show');
	});
	</script>
    <?php } else if(!empty($_SESSION['alarms_page_video_uuid'])) {?>
    <script> 
	$(window).on('load',function()
	{
		$('#playingVideoModal').modal('show');
	});
	</script>
    <?php } ?>
</body>

</html>

<?php 
if(!empty($_SESSION['alarms_page_alarm_uuid'])) 
{
	unset($_SESSION['alarms_page_alarm_uuid']);
}

if(!empty($_SESSION['alarms_page_video_uuid'])) 
{
	unset($_SESSION['alarms_page_video_uuid']);
}
?>