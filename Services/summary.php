<?php
$query = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `services` WHERE `category`!='' AND `deleted`=0 ORDER BY `category`");
$i = 0;
while ($row=mysqli_fetch_assoc($query)) {
	$category = $row['category'];
	$service_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(`category`) as service_count FROM `services` WHERE `category`='$category'"));
	$dataPoints[$i]['label'] = $category;
	$dataPoints[$i]['y'] = $service_count['service_count'];
	$i++;
}
?>
<head>
<script>
window.onload = function() {


var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	title: {
		text: "Service Category Pie Chart"
	},
	subtitles: [{
		text: ""
	}],
	data: [{
		type: "pie",
		yValueFormatString: "#,##0.00\"%\"",
		indexLabel: "{label} ({y})",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();

}
</script>

<div style="margin-top:115px" id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script>
$(document).ready(function() {
		$(".canvasjs-chart-credit").hide();
});
</script>
