<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

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
                                    <th>Time Started</th>
                                    <th>Time Finished</th>
                                    <th>Related Videos</th>
                                    <th class="col-md-4">Result</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $unresolved->fetch()) { ?>
                                <tr>
                                  <form action="../php/resolve_ticket.php" method="post" role="form" data-toggle="validator">
                                  <td><?php echo $row['Time_Created']; ?></td>
                                  <td><?php echo $row['Name']; ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php echo $row['Phone_Num']; ?></td>
                                  <td><?php echo $row['Description'];?></td>
                                  <td><?php echo $row['Start_Time'];?></td>
                                  <td><?php echo $row['End_Time'];?></td>
                                  <td><?php echo GetRelatedVideosCount($row['Ticket_UUID'], $row['Start_Time'], $row['End_Time']) . " Video(s)."; ?>
                                <form action="../php/tickets.php" method="post" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <button type="submit" value="<?php echo $row['Ticket_UUID']; ?>" name="ticket_uuid" id="play" class="play-button btn btn-info btn-md">Show Videos</button>
                                    </div>
                                </form>
                                  </td>
                                  <td class="col-md-4">
                                    <textarea class="form-control" rows="4" name="result" id="result" required></textarea>
                                  </td>
                                  <td>
                                    <div class="form-group">
                                      <input type="hidden" value="<?php echo $row['Ticket_UUID']; ?>" name="resolve" id="resolve">
                                      <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-primary" value="Resolve">
                                    </div>
                                  </td>
                                </form>
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
                                        <th>Time Started</th>
                                        <th>Time Finished</th>
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
                                      <td><?php echo $row['Start_Time'];?></td>
                                      <td><?php echo $row['End_Time'];?></td>
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
