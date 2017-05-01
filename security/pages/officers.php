<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    //Get officers
    $query = "
        SELECT
          *
        FROM Security_Officer
        ORDER BY Status, Last_Name
    ";

    try{
        $officers = $db->prepare($query);
        $result = $officers->execute();
        $officers->setFetchMode(PDO::FETCH_ASSOC);
		$officerList = $officers->fetchAll();
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

    //Get Supervisors
    $query = "
        SELECT
            *
        FROM Security_Officer
        WHERE
         (Super_SSN IS NULL)
		OR
		 (SSN IN (SELECT DISTINCT Super_SSN FROM Security_Officer))
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

<?php 
	$isLoadingNavBar = true;
	require("navBar.php"); 
	$isLoadingNavBar = false;
?>
            
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
                            <h4><b>Officers</b></h4>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php foreach($officerList as $row) { ?>
                                <tr <?php if($row['Status'] == "INACTIVE") { echo 'class="warning"';} else if($row['Status'] == "RETIRED") { echo 'class="danger"';}?>>
                                  <td>
								  	<form action="../php/updateOfficer.php" method="post" role="form" data-toggle="validator">
                                    	<input type="hidden" value="<?php echo $row['SSN']; ?>" name="off_ssn" id="off_ssn">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Last Name: </label>
                                            <div class="col-md-9">
                                            	<input class="form-control" name="last" id="last" type="text" value="<?php echo $row['Last_Name']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">First Name: </label>
                                            <div class="col-md-9">
                                            	<input class="form-control" name="first" id="first" type="text" value="<?php echo $row['First_Name']; ?>" required>
                                            </div>
                                        </div>
                                        <button type="submit" class="form-control btn btn-xs btn-success"><i class="fa fa-check fa-fw"></i>Update</button>
                                    </form>
                                  </td>
                                  <td>(<?php echo substr($row['Phone_Number'], 0, 3); ?>) <?php echo substr($row['Phone_Number'], 3, 3); ?> - <?php echo substr($row['Phone_Number'], 6, 4); ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php echo $row['Address'];  ?></td>
                                  <td>
                                    <form action="../php/updateOfficer.php" method="post" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <input type="hidden" value="<?php echo $row['SSN']; ?>" name="off_ssn" id="off_ssn">
                                        <div>
                                        	<b>Supervisor:</b>
                                            <select style="font-size: 12px;" class="form-control" id="super" name="super">
                                                <?php
                                                foreach($officerList as $row3) { ?>
                                                <option value="<?php echo $row3['SSN']; ?>" <?php if($row3['SSN']==$row['Super_SSN']){echo "selected";} ?> ><?php echo $row3['Last_Name']; ?>, <?php echo $row3['First_Name']; ?></option>
                                                <?php } ?>
                                                <option value="" <?php if(empty($row['Super_SSN']) ){echo "selected";} ?> >None</option>
                                            </select>
                                        </div>

                                        <b>Status:</b>
                                        <select style="font-size: 12px;" class="form-control" id="status" name="status">
                                            <option value="ACTIVE" <?php if($row['Status']=="ACTIVE"){echo "selected";} ?> >ACTIVE</option>
                                            <option value="INACTIVE" <?php if($row['Status']=="INACTIVE"){echo "selected";} ?> >INACTIVE</option>
                                            <option value="RETIRED" <?php if($row['Status']=="RETIRED"){echo "selected";} ?> >RETIRED</option>
                                        </select>

                                        <button type="submit" tabindex="4" class="form-control btn btn-xs btn-success"><i class="fa fa-check fa-fw"></i>Update</button>
                                    </div>
                                    </form>
                                  </td>
                                  
                                </tr>

                                <?php } ?>
                            </tbody>
                          </table>
                    </div>

                    <hr>

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
                                  <td>(<?php echo substr($row['Phone_Number'], 0, 3); ?>) <?php echo substr($row['Phone_Number'], 3, 3); ?> - <?php echo substr($row['Phone_Number'], 6, 4); ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php echo $row['Address']; ?></td>
                                    <td>
                                        <ul><?php $query = "
                                            SELECT
                                              First_Name, Last_Name
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
                                 </tr>
                              <?php } ?>
                            </tbody>
                         </table>
                    </div>

                    <hr>

                    <div class="panel panel-info">
                        <div class="panel-success">
                          <div class="panel-heading">
                              <h4><b>Add New Officer</b></h4>
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
                                  <?php foreach($officerList as $row) { ?>
                                    <option value="<?php echo $row['SSN'] ?>"><?php echo $row['Last_Name'] ?>, <?php echo $row['First_Name'] ?></option>
                                  <?php } ?>
                                  <option value="" selected>None</option>
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
                    	</div>
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
