<?php include_once('../include.php');
if($_GET['projectid'] > 0) {
	$projectid = $_GET['projectid'];
	$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
	$projecttype = $project['projecttype'];
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
	$base_config = array_filter(array_unique(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype' UNION
		SELECT `config_fields`  FROM `field_config_project` WHERE `fieldconfigprojectid` IN (SELECT MAX(`fieldconfigprojectid`) FROM `field_config_project` WHERE `type` IN ('".preg_replace('/[^a-z_,\']/','',str_replace(' ','_',str_replace(',',"','",strtolower(get_config($dbc,'project_tabs')))))."'))"))[0])));
	$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
	if(count($value_config) == 0) {
		$value_config = explode(',','Information Contact Region,Information Contact Location,Information Contact Classification,Information Business,Information Contact,Information Rate Card,Information Project Type,Information Project Short Name,Details Detail,Dates Project Created Date,Dates Project Start Date,Dates Estimate Completion Date,Dates Effective Date,Dates Time Clock Start Date');
	}
	$tab_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='$projecttype'"))['config_tabs']),explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='ALL'"))['config_tabs']))));
	if(count($tab_config) == 0) {
		$tab_config = explode(',','Path,Information,Details,Documents,Dates,Scope,Estimates,Tickets,Work Orders,Tasks,Checklists,Email,Phone,Reminders,Agendas,Meetings,Gantt,Profit,Report Checklist,Billing,Field Service Tickets,Purchase Orders,Invoices');
	}
} ?>
<?php $blocks = [];
$total_length = 0;
if(in_array('Summary Projections',$tab_config)) {
	$block_length = 68;
    $services = array_filter(explode('#*#',$project['projection_service_heading']));
    $service_fees = array_filter(explode('#*#',$project['projection_service_price']));
    $products = array_filter(explode('#*#',$project['projection_product_heading']));
    $product_fees = array_filter(explode('#*#',$project['projection_product_price']));
    $tasks = array_filter(explode('#*#',$project['projection_task_heading']));
    $task_fees = array_filter(explode('#*#',$project['projection_task_price']));
    $inventory = array_filter(explode('#*#',$project['projection_inventory_heading']));
    $inventory_fees = array_filter(explode('#*#',$project['projection_inventory_price']));
    $admin = array_filter(explode('#*#',$project['projection_admin_heading']));
    $admin_fees = array_filter(explode('#*#',$project['projection_admin_price']));
	$block = '<div class="overview-block">
		<h4>Projections</h4>';
    if(!empty($services)) {
        $block .= 'Service Headings: '.count($services).'<br />';
        $block .= 'Services Price: $'.number_format(array_sum($service_fees),2).'<br />';
        $block_length += 46;
    }
    if(!empty($products)) {
        $block .= 'Product Headings: '.count($products).'<br />';
        $block .= 'Products Price: $'.number_format(array_sum($product_fees),2).'<br />';
        $block_length += 46;
    }
    if(!empty($tasks)) {
        $block .= 'Task Headings: '.count($tasks).'<br />';
        $block .= 'Tasks Price: $'.number_format(array_sum($task_fees),2).'<br />';
        $block_length += 46;
    }
    if(!empty($inventory)) {
        $block .= INVENTORY_NOUN.' Headings: '.count($inventory).'<br />';
        $block .= INVENTORY_TILE.' Price: $'.number_format(array_sum($inventory_fees),2).'<br />';
        $block_length += 46;
    }
    if(!empty($admin)) {
        $block .= 'Admin Headings: '.count($admin).'<br />';
        $block .= 'Admin Price: $'.number_format(array_sum($admin_fees),2).'<br />';
        $block_length += 46;
    }
    $block .= 'Total Projection Headings: '.(count($services)+count($products)+count($tasks)+count($inventory)+count($admin)).'<br />';
    $block .= 'Total Projected Price: $'.number_format(array_sum($service_fees)+array_sum($product_fees)+array_sum($task_fees)+array_sum($inventory_fees)+array_sum($admin_fees),2).'<br />';
    $block_length += 46;
	$block .= '</div>';
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}

$display_column = 0;
$displayed_length = 0;
if($_GET['edit'] > 0) {
?>
<div class="col-sm-6">
	<?php $block_i = 0;
    foreach($blocks as $block) {
		if($block[0] == $displayed_length && $display_column == 0) {
            $block_i = 0;
			$displayed_length = 0;
			$total_length -= $block[0] + $displayed_length;
			echo '</div><div class="col-sm-6">'.$block[1].'</div><div class="col-sm-6">';
		} else if($block_i++ > 0 && $displayed_length + $block[0] - 25 > $total_length / 2) {
			$displayed_length = 0;
			$display_column = 1;
			echo '</div><div class="col-sm-6">'.$block[1];
		} else {
			$displayed_length += $block[0];
			echo $block[1];
		}
	} ?>
</div>
<?php } else {
	echo '<h2>Please add Project Details in order to see a Summary of the Project.</h2>';
} ?>
