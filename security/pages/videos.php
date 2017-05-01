<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    //Get All Videos
    $query = "
        SELECT
          Thumbnail, Start_Time, End_Time, Duration_us, Resolution_Height, Resolution_Width, Video_Format, Record_UUID, Camera_UID
        FROM Surveillance_Video
        ORDER BY Start_Time
    ";

    try{
        $videoListQuery = $db->prepare($query);
        $result = $videoListQuery->execute();
        $videoListQuery->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
	
	if(!empty($_POST['video_uuid']))
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

    <title>Surveillance Video Terminal</title>

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
    
    <?php if(!empty($_POST['video_uuid'])) {?>
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
                    <h1 class="page-header">Surveillance Video Terminal</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Surveillance Video</b></h4>
                        </div>
						<?php 
							$backAddress = "videos.php";
							require_once("../php/videoListTableTemplate.php"); 
							unset($backAddress);
						?>
                    </div>
                    <!-- /.panel-info -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    
    <?php if(!empty($_POST['video_uuid'])) { ?>
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
if(!empty($_POST['video_uuid'])) 
{
	unset($_POST['video_uuid']);
}
?>