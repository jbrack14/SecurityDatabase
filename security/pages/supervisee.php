<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doSuperUserLogInCheck();

    //Get Supervisees
    $query = "
        SELECT
            *
        FROM Security_Officer
        WHERE Super_SSN = :ssn
        ORDER BY Last_Name
    ";

	$query_params = array(
        ':ssn' => getUserSSN()
    );

    try{
        $superve = $db->prepare($query);
        $result = $superve->execute($query_params);
        $superve->setFetchMode(PDO::FETCH_ASSOC);
		$superveList = $superve->fetchAll();
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
                              <?php foreach($superveList as $row) { ?>
                                <tr <?php if($row['Status'] == "INACTIVE") { echo 'class="warning"';} else if($row['Status'] == "RETIRED") { echo 'class="danger"';}?>>
                                  <td><?php echo $row['Last_Name'] . ", " . $row['First_Name']; ?></td>
                                  <td>(<?php echo substr($row['Phone_Number'], 0, 3); ?>) <?php echo substr($row['Phone_Number'], 3, 3); ?> - <?php echo substr($row['Phone_Number'], 6, 4); ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php echo $row['Address'];  ?></td>
                                  <td>
                                    <form action="../php/updateSupervisee.php" method="post" role="form" data-toggle="validator">
                                    	<input type="hidden" value="<?php echo $row['SSN']; ?>" name="off_ssn" id="off_ssn">
                                        <div class="form-group">
                                            <label for="last" class="control-label">Last Name: </label>
                                              <input class="form-control" name="last" id="last" type="text" placeholder="<?php echo $row['Last_Name']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="first" class="control-label">First Name: </label>
                                            	<input class="form-control" name="first" id="first" type="text" placeholder="<?php echo $row['First_Name']; ?>" required>
                                        </div>
                                        <button type="submit" tabindex="4" class="form-control btn btn-xs btn-success"><i class="fa fa-check fa-fw"></i>Update</button>
                                    </form>
                                  </td>
                                </tr>

                                <?php } ?>
                            </tbody>
                          </table>
                    </div>

                    <hr>

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
