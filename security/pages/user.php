<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    if(empty($_SESSION['profile_page_set_password']))
    {
    	$_SESSION['profile_page_set_password'] = 'true';
    }

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
          <?php if($_SESSION['profile_page_set_password'] == 'false') { $_SESSION['profile_page_set_password'] = true; echo
            '<div class="alert alert-danger alert-dismissable">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <strong>Error!</strong> The current password you enetered was incorrect. Please try again.
            </div>';
          }?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">User Profile</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            
            <hr>
            
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
                                    <input class="form-control" name="first" id="first" type="text" value="<?php echo $profile['First_Name'] ?>" disabled>
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
                                  <input class="form-control" name="phone" id="phone" type="tel" value="<?php echo $profile['Phone_Number'] ?>">
                                </div>
                              </div>

                              <div class="form-group">
                                <label class="col-lg-2 control-label">Email:</label>
                                <div class="col-lg-8">
                                  <input class="form-control" name="email" id="email" type="email" value="<?php echo $profile['Email'] ?>">
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


                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Change Your Password
                        </div>
                        <br>
                        <div>
                            <form class="form-horizontal" action="../php/change_password.php" method="post" role="form" data-toggle="validator">

                                <div class="form-group">
                                  <label class="col-lg-2 control-label">Current Password:</label>
                                  <div class="col-lg-8">
                                    <input class="form-control" name="current" id="current" type="password">
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label class="col-lg-2 control-label">New Password:</label>
                                  <div class="col-lg-8">
                                    <input type="password" data-minlength="6" name="inputPassword" id="inputPassword" tabindex="2" class="form-control" placeholder="Password" required>
                                    <div class="help-block">Minimum of 6 characters</div>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label class="col-lg-2 control-label">Confirm New Password:</label>
                                  <div class="col-lg-8">
                                    <input type="password" name="confirm-password" id="confirm-password" data-match="#inputPassword" data-match-error="Passwords don't match." tabindex="2" class="form-control" placeholder="Confirm Password" required>
                                    <div class="help-block with-errors"></div>
                                  </div>
                                </div>

                              <div class="form-group">
                                <label class="col-md-2 control-label"></label>
                                <div class="col-md-8">
                                  <input type="submit" class="btn btn-primary" value="Change Password">
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


    <!-- Bootstrap Validator JavaScript -->
    <script src="../node_modules/bootstrap-validator/dist/validator.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
<?php
if(!empty($_SESSION['profile_page_set_password']))
{
	unset($_SESSION['profile_page_set_password']);
}
?>
