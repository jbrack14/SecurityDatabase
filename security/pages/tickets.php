<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();
	$isSuperOrSysAdmin = isSuperUserOrSysAdmin($_SESSION['User_UUID']);

    //Get Unresolved Tickets
    $query = "
        SELECT
            *
        FROM Ticket
        WHERE Result IS NULL
    ";

    try{
        $unresolved = $db->prepare($query);
        $result = $unresolved->execute();
        $unresolved->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_unresolved = $unresolved->rowCount();

    //Get Resolved Tickets
    $query = "
        SELECT
            *
        FROM Ticket
        WHERE Result IS NOT NULL
    ";

    try{
        $resolved = $db->prepare($query);
        $result = $resolved->execute();
        $resolved->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

	function GetRelatedVideosCount($ticketUUID, $startTime, $endTime)
	{
		global $db;

		$query = "
		SELECT
			COUNT(*) AS Video_Num
		FROM Surveillance_Video AS S inner join (Camera natural join Spot) on Camera.Camera_UID = S.Camera_UID
		WHERE
			CASE
				WHEN EXISTS(SELECT 1 FROM Ticket_Spots WHERE Ticket_Spots.Ticket_UUID = :TicketUUID) THEN
					Spot.Spot_UUID IN (SELECT Spot_UUID FROM Ticket_Spots WHERE Ticket_Spots.Ticket_UUID = :TicketUUID)
				ELSE
					TRUE
			END
				AND
				(
					(timestampdiff(SECOND, :TicketStartTime, S.Start_Time) >= 0
						AND timestampdiff(SECOND, S.Start_Time, :TicketEndTime) >= 0)
					OR (timestampdiff(SECOND, :TicketStartTime, S.End_Time) >= 0
						AND timestampdiff(SECOND, S.End_Time, :TicketEndTime) >= 0)
					OR (timestampdiff(SECOND, S.Start_Time, :TicketStartTime) >= 0
						AND timestampdiff(SECOND, :TicketEndTime, S.End_Time) >= 0)
				)
		;
		";

		$query_params = array(
		  ':TicketUUID' => $ticketUUID,
		  ':TicketStartTime' => $startTime,
		  ':TicketEndTime' => $endTime
		);

		try{
			$relatedVideoCount = $db->prepare($query);
			$result = $relatedVideoCount->execute($query_params);
			$relatedVideoCount->setFetchMode(PDO::FETCH_ASSOC);
		}
		catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
		$relatedVideoCountRow = $relatedVideoCount->fetch();
		return $relatedVideoCountRow['Video_Num'];
	}

	if(!empty($_POST['ticket_uuid']))
	{
		$query = "
			SELECT
				*
			FROM Ticket
			WHERE Ticket_UUID = :ticketUUID
		";

		$query_params = array(
		  ':ticketUUID' => $_POST['ticket_uuid']
		);
		try{
			$ticketInfoQuery = $db->prepare($query);
			$result = $ticketInfoQuery->execute($query_params);
			$ticketInfoQuery->setFetchMode(PDO::FETCH_ASSOC);
		}
		catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

		$ticketInfoRow = $ticketInfoQuery->fetch();

		$query = "
		SELECT
			S.Thumbnail, S.Start_Time, S.End_Time, S.Duration_us, S.Resolution_Height, S.Resolution_Width, S.Video_Format, S.Record_UUID, S.Camera_UID
		FROM Surveillance_Video AS S inner join (Camera natural join Spot) on Camera.Camera_UID = S.Camera_UID
		WHERE
			CASE
				WHEN EXISTS(SELECT 1 FROM Ticket_Spots WHERE Ticket_Spots.Ticket_UUID = :TicketUUID) THEN
					Spot.Spot_UUID IN (SELECT Spot_UUID FROM Ticket_Spots WHERE Ticket_Spots.Ticket_UUID = :TicketUUID)
				ELSE
					TRUE
			END
				AND
				(
					(timestampdiff(SECOND, :TicketStartTime, S.Start_Time) >= 0
						AND timestampdiff(SECOND, S.Start_Time, :TicketEndTime) >= 0)
					OR (timestampdiff(SECOND, :TicketStartTime, S.End_Time) >= 0
						AND timestampdiff(SECOND, S.End_Time, :TicketEndTime) >= 0)
					OR (timestampdiff(SECOND, S.Start_Time, :TicketStartTime) >= 0
						AND timestampdiff(SECOND, :TicketEndTime, S.End_Time) >= 0)
				)
		;
		";

		$query_params = array(
		  ':TicketUUID' => $ticketInfoRow['Ticket_UUID'],
		  ':TicketStartTime' => $ticketInfoRow['Start_Time'],
		  ':TicketEndTime' => $ticketInfoRow['End_Time']
		);

		try{
			$videoListQuery = $db->prepare($query);
			$result = $videoListQuery->execute($query_params);
			$videoListQuery->setFetchMode(PDO::FETCH_ASSOC);
		}
		catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

	}
	else if(!empty($_POST['video_uuid']))
	{
		$query = "
		SELECT
			Video_Data, Video_Format, Resolution_Height, Resolution_Width
		FROM Surveillance_Video
		WHERE
			Record_UUID = :record
		";

		$query_params = array(
			':record' => $_POST['video_uuid']
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

    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

    <?php if(!empty($_POST['ticket_uuid'])) {?>
    <script>
	$(window).on('load',function()
	{
		$('#relatedVideoModal').modal('show');
	});
	</script>
    <?php } else if(!empty($_POST['video_uuid'])) {?>
    <script>
	$(window).on('load',function()
	{
		$('#playingVideoModal').modal('show');
	});
	</script>
    <?php } ?>

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
                    <h1 class="page-header">Tickets</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            There are currently <b><?php echo $num_unresolved?></b> unresolved tickets.
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Time Created</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Description</th>
                                    <th>Spots</th>
                                    <th>Time</th>
                                    <th>Related Videos</th>
                                    <?php if($isSuperOrSysAdmin){ ?><th class="col-md-4">Result</th><?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $unresolved->fetch()) { ?>
                                <tr>

                                  <td class="col-md-1"><?php echo $row['Time_Created']; ?></td>
                                  <td class="col-md-1"><?php echo $row['Name']; ?></td>
                                  <td class="col-md-1"><?php echo $row['Email']; ?></td>
                                  <td class="col-md-1">(<?php echo substr($row['Phone_Num'], 0, 3); ?>) <?php echo substr($row['Phone_Num'], 3, 3); ?> - <?php echo substr($row['Phone_Num'], 6, 4); ?></td>
                                  <td class="col-md-2"><?php echo $row['Description'];?></td>
                                  <td class="col-md-1">
                                  <ul><?php $query = "
                                        SELECT
                                          Coverage_Description
                                        FROM Spot AS sp NATURAL JOIN Ticket_Spots AS sa
                                        WHERE sa.Ticket_UUID = :ticket_uuid
                                    ";

                                    $query_params = array(
                                      ':ticket_uuid' => $row['Ticket_UUID']
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
                                  <td class="col-md-1"><?php echo "<b>Start: </b>" . $row['Start_Time'];?><br><?php echo "<b>End: </b>" . $row['End_Time'];?></td>
                                  <td class="col-md-1"><?php $relatedVideoCountNum = GetRelatedVideosCount($row['Ticket_UUID'], $row['Start_Time'], $row['End_Time']) . " Video(s)."; echo $relatedVideoCountNum;?>
                                <form action="tickets.php" method="post" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <button type="submit" value="<?php echo $row['Ticket_UUID']; ?>" name="ticket_uuid" id="play" class="play-button btn btn-info btn-md" <?php if($relatedVideoCountNum == 0){ echo 'disabled="true"'; } ?>>Show Videos</button>
                                    </div>
                                </form>
                                  </td>
                                  
                                  <?php if($isSuperOrSysAdmin){ ?>
                                  <td class="col-md-2">
                                      <form action="../php/resolve_ticket.php" method="post" role="form" data-toggle="validator">
                                        <textarea class="form-control" rows="4" name="result" id="result" required></textarea>
                                        <input type="hidden" value="<?php echo $row['Ticket_UUID']; ?>" name="resolve" id="resolve">
                                        <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-primary" value="Resolve">
                                      </form>
                                  </td>
								  <?php } ?>

                                </tr>
                                <?php } ?>
                            <tbody>
                          </table>
                    </div>

                    <hr>
                  </div>
                    <!-- /.panel -->
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Resolved Tickets
                            </div>
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Time Created</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Description</th>
                                        <th>Spots</th>
                                        <th>Time</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php while($row = $resolved->fetch()) { ?>
                                    <tr>
                                      <td><?php echo $row['Time_Created']; ?></td>
                                      <td><?php echo $row['Name']; ?></td>
                                      <td><?php echo $row['Email']; ?></td>
                                      <td><?php echo $row['Phone_Num'];?></td>
                                      <td><?php echo $row['Description'];?></td>
                                      <td>
                                      <ul><?php $query = "
                                        SELECT
                                          Coverage_Description
                                        FROM Spot AS sp NATURAL JOIN Ticket_Spots AS sa
                                        WHERE sa.Ticket_UUID = :ticket_uuid
                                    ";

                                    $query_params = array(
                                      ':ticket_uuid' => $row['Ticket_UUID']
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
                                      <td><?php echo "<b>Start: </b>" . $row['Start_Time'];?><br><?php echo "<b>End: </b>" . $row['End_Time'];?></td>
                                      <td><?php echo $row['Result'];?></td>
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

    <?php if(!empty($_POST['ticket_uuid'])){ ?>
    <!-- Modal content 1-->
    <div class="modal fade" id="relatedVideoModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Related Videos</h4>
                </div>
                <div class="modal-body">
					<?php
						$backAddress = "../pages/tickets.php";
						require_once("../php/videoListTableTemplate.php");
						unset($backAddress);
					?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Modal content 1-->
	<?php }	else if(!empty($_POST['video_uuid'])) { ?>
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

</body>

</html>
<?php
if(!empty($_POST['ticket_uuid']))
{
	unset($_POST['ticket_uuid']);
}

if(!empty($_POST['video_uuid']))
{
	unset($_POST['video_uuid']);
}
?>
