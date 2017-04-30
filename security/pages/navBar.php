<?php 
require_once("../basicFunctions.php");

if(!isset($isLoadingNavBar) || !$isLoadingNavBar)
{
	header("Location: ../index.html");
	die("This page cannot be displayed alone! Redirecting to ../index.html");
}

$navBarTitle="Security Officer Terminal";
if(isSysAdmin($_SESSION['User_UUID'])) 
{
	$navBarTitle="Security Officer Terminal (System Administrator)";
}
else if(isSuperUser($_SESSION['User_UUID'])) 
{
	$navBarTitle="Security Officer Terminal (Supervisor)";
}
?>
    
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="home.php"><?php echo $navBarTitle; ?></a>
    </div>
    <!-- /.navbar-header -->
    
    <ul class="nav navbar-top-links navbar-right">
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            	<i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="user.php"><i class="fa fa-user fa-fw"></i> User Profile</a>
                </li>
                <li class="divider"></li>
                <li><a href="../php/logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                </li>
            </ul>
        <!-- /.dropdown-user -->
        </li>
    	<!-- /.dropdown -->
    </ul>
    <!-- /.navbar-top-links -->
    
    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
            
                <li>
                	<a href="home.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                </li>
                
                <li>
                	<a href="user.php"><i class="fa fa-user fa-fw"></i> Profile</a>
                </li>
                
                <li>
                	<a href="alarms.php"><i class="fa fa-exclamation-triangle fa-fw"></i> Alarms</a>
                </li>
                
                <li>
                	<a href="tickets.php"><i class="fa fa-ticket fa-fw"></i> Tickets</a>
                </li>
                
                <li>
                	<a href="buildings.php"><i class="fa fa-building fa-fw"></i> Buildings</a>
                </li>
                
                <li>
                	<a href="spots.php"><i class="fa fa-map fa-fw"></i> Spots</a>
                </li>
                
                <li>
                	<a href="cameras.php"><i class="fa fa-camera fa-fw"></i> Cameras</a>
                </li>
                
                <li>
                	<a href="videos.php"><i class="fa fa-film fa-fw"></i> Videos</a>
                </li>
                
                <?php if(isSysAdmin($_SESSION['User_UUID'])) { ?>
                <li>
                	<a href="officers.php"><i class="fa fa-users fa-fw"></i> Officers</a>
                </li>
                <?php } ?>
                
                <?php if(isSuperUser($_SESSION['User_UUID'])) { ?>
                <li>
                	<a href="shifts.php"><i class="fa fa-calendar fa-fw"></i> Shifts</a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <!-- /.navbar-static-side -->