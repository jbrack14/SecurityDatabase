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
                                    <th class="col-md-4">Description</th>
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
                                  <td class="col-md-4"><?php echo $row['Description'];?></td>
                                  <td><?php echo $row['Start_Time'];?></td>
                                  <td><?php echo $row['End_Time'];?></td>
                                  <td>    </td>
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
