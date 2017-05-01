<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    $query = "
        SELECT
            *
        FROM Security_Officer
        WHERE
            SSN = :ssn
    ";
    $query_params = array(
        ':ssn' => getUserSSN()
    );

    try{
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

    $profile = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Security Officer Terminal</title>

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
                    <h1 class="page-header">User Profile</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Welcome <?php echo getUsername() ?>
                        </div>
                        <br>
                        <div>
                            <form class="form-horizontal" action="../php/updateUser.php" method="post" role="form">

                                <div class="form-group">
                                  <label class="col-lg-2 control-label">First name:</label>
                                  <div class="col-lg-8">
                                    <input class="form-control" name="first" id="first"type="text" value="<?php echo $profile['First_Name'] ?>" disabled>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label class="col-lg-2 control-label">Last name:</label>
                                  <div class="col-lg-8">
                                    <input class="form-control" name="last" id="last" type="text" value="<?php echo $profile['Last_Name'] ?>" disabled>
                                  </div>
                                </div>
                                
                              <div class="form-group">
                                <label class="col-lg-2 control-label">Phone number:</label>
                                <div class="col-lg-8">
                                  <input class="form-control" name="phone" id="phone" type="text" value="<?php echo $profile['Phone_Number'] ?>">
                                </div>
                              </div>
                              
                              <div class="form-group">
                                <label class="col-lg-2 control-label">Email:</label>
                                <div class="col-lg-8">
                                  <input class="form-control" name="email" id="email" type="text" value="<?php echo $profile['Email'] ?>">
                                </div>
                              </div>
                              
                              <div class="form-group">
                                <label class="col-lg-2 control-label">Address:</label>
                                  <div class="col-lg-8">
                                    <input class="form-control" name="address" id="address" type="text" value="<?php echo $profile['Address'] ?>">
                                  </div>
                              </div>
                              
                              <div class="form-group">
                                <label class="col-md-2 control-label">Username:</label>
                                <div class="col-md-8">
                                  <input class="form-control" name="username" id="username" type="text" value="<?php echo getUsername() ?>">
                                </div>
                              </div>
                              
                              <div class="form-group">
                                <label class="col-md-2 control-label"></label>
                                <div class="col-md-8">
                                  <input type="submit" class="btn btn-primary" value="Save Changes">
                                </div>
                              </div>
                              
                            </form>
                          </div>
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

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
