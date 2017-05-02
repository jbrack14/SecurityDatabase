<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Security Officer Account Registration</title>
    <script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>
    <script src="../js/ticket.js"></script>

    <!-- Bootstrap Validator-->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.0/css/bootstrapValidator.min.css"/>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.0/js/bootstrapValidator.min.js"> </script>

    <!-- Bootstrap Core CSS -->
    <link type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="../vendor/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link type="text/css" href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link type="text/css" href="../dist/css/login.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link type="text/css" href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery -->
    <script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script type="text/javascript" src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../vendor/moment/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="../vendor/bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <!-- Bootstrap Validator JavaScript -->
    <script type="text/javascript" src="../node_modules/bootstrap-validator/dist/validator.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script type="text/javascript" src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script type="text/javascript" src="../dist/js/sb-admin-2.js"></script>
</head>

<body>
    <div class="page-wrapper">
        <div class="container-fluid" height="100%" style="height: 100%;">
            <div class="row" style="height:80%; overflow: hidden;">
                <div class="col-md-6 col-md-offset-3">
                    <div class="alert alert-success alert-dismissable" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            Success! Ticket submitted.
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                <h4><b>Create A Ticket</b></h4>
                            </div>
                                <div class="panel-body" height="100%" style="height: 100%;">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <form id="ticket-form" class="form-horizontal" action="../php/create_ticket.php" method="post" role="form" data-toggle="validator" >
                                                <fieldset>
                                                <!-- Name input-->
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label" for="ticket_name">Name</label>
                                                    <div class="col-md-9">
                                                        <input id="ticket_name" name="ticket_name" type="text" placeholder="Your name" class="form-control" required>
                                                    </div>
                                                </div>
                                                
                                                <!-- Email input-->
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label" for="ticket_email">Your E-mail</label>
                                                    <div class="col-md-9">
                                                        <input id="ticket_email" name="ticket_email" type="text" placeholder="Your email" class="form-control" required>
                                                    </div>
                                                </div>
                                                
                                                <!-- Phone input-->
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label" for="ticket_phone">Your Phone Number</label>
                                                    <div class="col-md-9">
                                                        <input id="ticket_phone" name="ticket_phone" type="text" placeholder="Your phone number" class="form-control" required>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Spots:</label>
                                                    <div class="col-md-9">
                                                        <label for="spots">Select Spots:</label>
                                                        <select multiple="multiple" class="form-control" name="spots[]" id="spots">
                                                            <?php
                                                            $query = "
                                                                SELECT
                                                                    *
                                                                FROM Spot
                                                            ";
                                                            
                                                            try{
                                                                $spots = $db->prepare($query);
                                                                $result = $spots->execute();
                                                                $spots->setFetchMode(PDO::FETCH_ASSOC);
                                                            }
                                                            catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                                            while($row = $spots->fetch()) { ?>
                                                            <option value="<?php echo $row['Spot_UUID']?>"><?php echo $row['Coverage_Description'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <!-- Start Time -->
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label">Start Time:</label>
                                                    <div class="col-lg-9">
                                                        <input class="form-control" name="start" id="datetimepicker_start" type="text" required>
                                                        <script type="text/javascript">
                                                        $(function () {
                                                            $('#datetimepicker_start').datetimepicker({format:'MM/DD/YYYY HH:mm'});
                                                        });
                                                        </script>
                                                    </div>
                                                </div>
                                                
                                                <!-- End Time -->
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label">End Time:</label>
                                                    <div class="col-lg-9">
                                                        <input class="form-control" name="end" id="datetimepicker_end" type="text" required>
                                                        <script type="text/javascript">
                                                        $(function () {
                                                            $('#datetimepicker_end').datetimepicker({format:'MM/DD/YYYY HH:mm'});
                                                        });
                                                        </script>
                                                    </div>
                                                </div>
                                                
                                                <!-- Message body -->
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label" for="ticket_message">Describe your scurity request</label>
                                                    <div class="col-md-9">
                                                        <textarea class="form-control" id="ticket_message" name="ticket_message" placeholder="Please enter your message here..." rows="5" required></textarea>
                                                    </div>
                                                </div>
                                                
                                                <!-- Form actions -->
                                                <div class="form-group">
                                                    <div class="col-md-12 text-right">
                                                        <a href="login.html" class="btn btn-primary btn-lg">
                                                            <i class="fa fa-arrow-left"></i>
                                                        </a>
                                                        <button type="submit" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#success-alert">Submit</button>
                                                    </div>
                                                </div>
                                                
                                                <br>
                                                <br>
                                                <br>
                                                
                                            </fieldset>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /row -->
                </div>
                <!-- /col-md-6 -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container-fluid -->
    </div>
    <!-- /page-wrapper -->

</body>

</html>
