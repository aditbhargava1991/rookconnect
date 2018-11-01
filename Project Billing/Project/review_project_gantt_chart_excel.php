<?php
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
require('../gantti-master/lib/gantti.php');

$projectid = $_GET['projectid'];

$a_date = date("Y-m-t");
$d=date("t", strtotime($a_date));

?>
<table>
    <tr>
		<td colspan="<?php echo $d+2; ?>" rowspan="2">
			<img src='<?= WEBSITE_URL ?>/Ticket/download/our logo-use in software.jpg'/>
		</td>
	</tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
	<tr>
		<td colspan="<?php echo $d+2; ?>" rowspan="2">
			<?php echo '<h3 style="color:33B0B0;">'.get_project($dbc, $projectid, 'project_name').'</h3>'; ?>
		</td>
	</tr>
	<tr></tr>
	<tr>
		<td colspan="2" rowspan="2"><h2>#<?php echo $projectid; ?></h2></td>
        <?php
        $result_ticket = mysqli_query($dbc, "SELECT distinct(to_do_date) FROM tickets WHERE projectid = '$projectid' order by to_do_date");
        $dayscount = array();
        $existingValues = array();
        while($row_ticket = mysqli_fetch_array($result_ticket)) {
            $rows[] = $row_ticket['to_do_date'];
        }


        if(in_array('', $rows)) {
            $rows = array_filter($rows);
            array_push($rows, '');
        }

        if(in_array('0000-00-00', $rows)) {
            $key = array_search('0000-00-00', $rows);
            $rows[$key] = '';
            $rows = array_filter($rows);
            array_push($rows, '0000-00-00');
        }

        foreach($rows as $row) {
            $rowMonth = explode("-", $row);
            if(isset($rowMonth[1])) {
                $month = $rowMonth[1];
            }

            if(!in_array($row, $existingValues)) {
                if(isset($month) && !in_array($month, $existingValues)) {
                    if($row == null || $row == '0000-00-00') {
                        $dateValue = date('Y-m-d');
                        $existingValues[] = 'null';
                        $existingValues[] = '0000-00-00';
                    }
                    else {
                        $dateValue = $row;
                    }

                    $dateData = explode("-", $dateValue);
                    $month = $dateData[1];
                    $year = $dateData[0];
                    $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    $existingValues[] = $month;
                    $dayscount[$month] = $number;
                    echo "<td colspan=".$number."><h4 style='color:black'><b>" . date('F', strtotime($dateValue)) . "</b></h4></td>";
                }
            }
        }
        ?>
	</tr>
	<tr>
		<?php
        foreach($dayscount as $days) {
			for($i=01;$i<=$days;$i++)
			{
				echo '<td align="center">'.sprintf("%02d", $i).'</td>';
			}
        }
		?>
	</tr>
<?php
	$result_ticket = mysqli_query($dbc, "SELECT * FROM tickets WHERE projectid = '$projectid'");
	while($row_ticket = mysqli_fetch_array( $result_ticket )) {
?>
	<tr>
		<td colspan="2">#<?php echo $row_ticket['ticketid'].' : '.limit_text($row_ticket['heading'], 5 ); ?></td>
		<?php
        foreach($dayscount as $month=>$days) {
			for($i=1;$i<=$days;$i++)
			{
                if($row_ticket['to_do_date'] == null || $row_ticket['to_do_date'] == '0000-00-00') {
                    $compareValues = date('Y-m-d');
                }
                else {
                    $compareValues = $row_ticket['to_do_date'];
                }

                $compareValue = explode("-", $compareValues);
				if($month==$compareValue[1] && $i==$compareValue[2])
					echo '<td align="center"><b>1</b></td>';
				else
					echo '<td></td>';
			}
        }
		?>
	</tr>
<?php
	}
?>
</table>
<?php
header('Content-type: application/excel');
header("Content-Disposition: attachment; filename=GanttChart".date('d-m-Y').".xls");
?>
