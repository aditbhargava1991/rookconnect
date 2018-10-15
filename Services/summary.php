<?php
$query = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `services` WHERE `category`!='' AND `deleted`=0 ORDER BY `category`");
$all_service_count_array = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(`category`) as category_count FROM `services`"));
$all_service_count = $all_service_count_array['category_count'];

$i = 0;
while ($row=mysqli_fetch_assoc($query)) {
	$category = $row['category'];
	$service_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(`category`) as service_count FROM `services` WHERE `category`='$category'"));
	$dataPoints[$i]['label'] = $category;
	$dataPoints[$i]['y'] = ($service_count['service_count'] / $all_service_count) * 100;
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
$(".canvasjs-chart-credit").hide();
}
</script>
<div class="main-screen override-main-screen">
<div style="margin-top:150px" id="chartContainer" style="height: 50%; width: 100%;"></div>
</div>
<script src="/js/jquery.canvasjs.min.js"></script>
