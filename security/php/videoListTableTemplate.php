<?php
require_once("../basicFunctions.php");
doLogInCheck();

if(!isset($videoListQuery) || !isset($backAddress))
{
	header("Location: ../pages/home.php");
	die("This page cannot be displayed alone! Redirecting to ../pages/home.php");
}
?>

<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
    <thead>
        <tr>
            <th></th>
            <th>Start Time</th>
            <th>Duration</th>
            <th>Spot</th>
            <th>Resolution</th>
        </tr>
    </thead>
    <tbody>
        <?php while($videoListRow = $videoListQuery->fetch()) { ?>
        <tr>
            <td>
            <?php echo '<img height="50" width="50" src="data:image/png;base64,'.base64_encode($videoListRow['Thumbnail']).'"/>'; ?>
            </td>
            <td><?php echo $videoListRow['Start_Time']; ?></td>
            <td><?php echo formatDurationUS($videoListRow['Duration_us']); ?></td>
            <td><?php  $query = "
                SELECT
                    Coverage_Description
                FROM Spot
                WHERE
                    Spot_UUID = (
                    SELECT Spot_UUID
                    FROM Camera
                    WHERE Camera_UID = :camera
                    )
                ";

                $query_params = array(
                ':camera' => $videoListRow['Camera_UID']
                );

                try{
                $spot = $db->prepare($query);
                $result = $spot->execute($query_params);
                }
                catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                echo implode(', ', $spot->fetch())?>
            </td>
            <td><?php echo $videoListRow['Resolution_Width']; ?> x <?php echo $videoListRow['Resolution_Height']; ?></td>
            <td>
                <form action=<?php echo $backAddress; ?> method="post" role="form" data-toggle="validator">
                <div class="form-group">
                    <button type="submit" value="<?php echo $videoListRow['Record_UUID']; ?>" name="video_uuid" id="play" class="play-button btn btn-info btn-md"><i class="fa fa-play fa-fw"></i> Play Video</button>
                </div>
                </form>
            </td>
        </tr>

        <?php } ?>
    </tbody>
</table>
