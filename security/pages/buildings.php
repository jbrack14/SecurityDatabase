<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    //Get Buildings
    $query = "
        SELECT
          *
        FROM Building
        ORDER BY Status, Name
    ";

    try{
        $buildings = $db->prepare($query);
        $result = $buildings->execute();
        $buildings->setFetchMode(PDO::FETCH_ASSOC);
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
                    <h1 class="page-header">Building Management</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Buildings</h4><b>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Number</th>
                                    <th>Street</th>
                                    <th>Zip Code</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $buildings->fetch()) { ?>
                                <tr <?php if($row['Status'] == "INACTIVE"){ echo 'class="danger"'; } ?>>
                                  <td><?php echo $row['Name']; ?></td>
                                  <td><?php echo $row['Street_Num']; ?></td>
                                  <td><?php echo $row['Street_Name']; ?></td>
                                  <td><?php echo $row['Zip_Code']; ?></td>
                                  <td><form action="../php/delete_building.php" method="post" role="form" data-toggle="validator">
                                    <b>Status:</b>
                                    <select style="font-size: 12px;" class="form-control" id="status" name="status">
                                        <option value="ACTIVE" <?php if($row['Status']=="ACTIVE"){echo "selected";} ?> >ACTIVE</option>
                                        <option value="INACTIVE" <?php if($row['Status']=="INACTIVE"){echo "selected";} ?> >INACTIVE</option>
                                    </select>
                                    <div class="form-group">
                                      <input type="hidden" value="<?php echo $row['Name']; ?>" name="delete" id="delete">
                                      <button type="submit" tabindex="4" class="form-control btn btn-xs btn-success"><i class="fa fa-check fa-fw"></i></button>
                                    </div>
                                  </form></td>
                                </tr>
                                <?php } ?>
                            <tbody>
                          </table>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-success">
                          <div class="panel-heading">
                              <h4><b>Add New Building</b></h4>
                          </div>
                          <br>
                        <form class="form-horizontal" action="../php/newBuilding.php" method="post" role="form">
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Name:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="name" id="name" type="text" value="" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Street Number:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="street_num" id="street_num" type="text" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Steet Name:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="street_name" id="street_num" type="text" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Zip Code:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="zip" id="zip" type="text" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-8">
                              <input type="submit" class="btn btn-primary" value="Create Building">
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
