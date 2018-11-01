<?php include_once('config.php');
if($user_category == 'Staff') {
	echo "<a href='add_services.php' class='btn brand-btn pull-right'>Add Service Information</a>";
} ?><h2 class="double-gap-top">Services</h2>
<?php $query = mysqli_query($dbc_support, "SELECT * FROM `support_services` WHERE `type`='service' AND `deleted`=0 ORDER BY `priority`");
echo '<div class="panel-group" id="accordion2">';
if(mysqli_num_rows($query) > 0) {
	while($row = mysqli_fetch_array($query)) {
		echo '<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_prep_'.$row['serviceid'].'" >
						'.$row['heading'].'<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_prep_'.$row['serviceid'].'" class="panel-collapse collapse">
				<div class="panel-body">';
					echo "<h3>".(!empty($row['image']) ? '<img src="'.$row['image'].'" style="height: 3em;">' : '').$row['heading']."</h3>";
					echo html_entity_decode($row['description']);
					if(!empty($row['link'])) {
						echo '<a href="'.$row['link'].'">Learn more here.</a>';
					}
					if($user_category == 'Staff') {
						echo "<br /><a href='add_services.php?serviceid=".$row['serviceid']."' class='btn brand-btn pull-right'>Edit Service Information</a>";
						echo "<a href='add_services.php?serviceid=".$row['serviceid']."&delete=true' class='btn brand-btn pull-right'>Delete</a>";
					}
				echo '</div>
			</div>
		</div>';
	}
	//echo '</div>';
} else {
	echo "Coming Soon!";
} ?>
<h2 class="double-gap-top">Products</h2>
<?php $query = mysqli_query($dbc_support, "SELECT * FROM `support_services` WHERE `type`='product' AND `deleted`=0 ORDER BY `priority`");
if(mysqli_num_rows($query) > 0) {
	//echo '<div class="panel-group" id="accordion2">';
	while($row = mysqli_fetch_array($query)) {
		echo '<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_prep_'.$row['serviceid'].'" >
						'.$row['heading'].'<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_prep_'.$row['serviceid'].'" class="panel-collapse collapse">
				<div class="panel-body">';
					echo "<h3>".(!empty($row['image']) ? '<img src="'.$row['image'].'" style="height: 3em;">' : '').$row['heading']."</h3>";
					echo html_entity_decode($row['description']);
					if(!empty($row['link'])) {
						echo '<a href="'.$row['link'].'">Learn more here.</a>';
					}
					if($user_category == 'Staff') {
						echo "<br /><a href='add_services.php?serviceid=".$row['serviceid']."' class='btn brand-btn pull-right'>Edit Service Information</a>";
						echo "<a href='add_services.php?serviceid=".$row['serviceid']."&delete=true' class='btn brand-btn pull-right'>Delete</a>";
					}
				echo '</div>
			</div>
		</div>';
	}
	//echo '</div>';
} else {
	echo "Coming Soon!";
} ?>
<h2 class="double-gap-top">Service Plans</h2>
<?php $query = mysqli_query($dbc_support, "SELECT * FROM `support_services` WHERE `type`='plan' AND `deleted`=0 ORDER BY `priority`");
if(mysqli_num_rows($query) > 0) {
	//echo '<div class="panel-group" id="accordion3">';
	while($row = mysqli_fetch_array($query)) {
		echo '<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_prep_'.$row['serviceid'].'" >
						'.$row['heading'].'<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_prep_'.$row['serviceid'].'" class="panel-collapse collapse">
				<div class="panel-body">';
					echo "<h3>".(!empty($row['image']) ? '<img src="'.$row['image'].'" style="height: 3em;">' : '').$row['heading']."</h3>";
					echo html_entity_decode($row['description']);
					if(!empty($row['link'])) {
						echo '<a href="'.$row['link'].'">Learn more here.</a>';
					}
					if($user_category == 'Staff') {
						echo "<br /><a href='add_services.php?serviceid=".$row['serviceid']."' class='btn brand-btn pull-right'>Edit Service Information</a>";
						echo "<a href='add_services.php?serviceid=".$row['serviceid']."&delete=true' class='btn brand-btn pull-right'>Delete</a>";
					}
				echo '</div>
			</div>
		</div>';
	}
	//echo '</div>';
} else {
	echo "Coming Soon!";
}
echo '</div>' ?>