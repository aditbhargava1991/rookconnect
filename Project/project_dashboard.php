<?php $project_sort = get_config($dbc, "project_sorting");
if($project_sort == '') {
	$project_sort = 'newest';
}
$summ_config = explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL' AND '$projecttype' != 'ALL'"))['config_fields']);
$tab_list = [];
$search_list = [];
$project_list = [];
$business_list = [];
$contact_list = [];
$region_list = [];
$class_list = [];
$lead_list = [];
$colead_list = [];?>
<script>
var loadMore = true;
var project_list = [];
var loadResults = '';
var query = '';
var current_list = [];
var project_type = '<?= isset($_GET['tab']) ? '' : (!empty($_GET['type']) ? $_GET['type'] : $project_type) ?>';
var current_tile = '<?= $tile ?>';
var project_tile = '<?= PROJECT_TILE ?>';
<?php foreach($project_tabs as $type_name => $project_label) {
	if(check_subtab_persmission($dbc, 'project', ROLE, $type_name)) {
		$match_query = '';
		if(!empty(MATCH_CONTACTS)) {
			$match_query = ' AND (1=0';
			foreach(explode(',',MATCH_CONTACTS) as $contactid) {
				$match_query .= " OR `project`.`businessid`='$contactid' OR CONCAT(',',`project`.`clientid`,',') LIKE '%,$contactid,%'";
			}
			$match_query .= ')';
		}
		if($type_name == 'VIEW_ALL') {
			$project_query = mysqli_query($dbc, "SELECT `project`.`projectid`, `project`.`businessid`, `project`.`siteid`, `project`.`clientid`, `project`.`project_name`, `contacts`.`region`, `contacts`.`classification`, `project`.`project_lead`, `project`.`project_colead` FROM `project` LEFT JOIN `contacts` ON `project`.`businessid`=`contacts`.`contactid` OR CONCAT(',',`project`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,'%,') WHERE `project`.`deleted`=0 AND `project`.`status`!='Archive' AND (`project`.`status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project') $match_query ORDER BY REPLACE(`project`.`favourite`,',','') LIKE ',".$_SESSION['contactid'].",' DESC, `project`.`project_name` ASC, `project`.`projectid` DESC");
		} else {
			$project_query = mysqli_query($dbc, "SELECT `project`.`projectid`, `project`.`businessid`, `project`.`siteid`, `project`.`clientid`, `project`.`project_name`, `contacts`.`region`, `contacts`.`classification`, `project`.`project_lead`, `project`.`project_colead` FROM `project` LEFT JOIN `contacts` ON `project`.`businessid`=`contacts`.`contactid` OR CONCAT(',',`project`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,'%,') WHERE `project`.`deleted`=0 AND `project`.`status`!='Archive' AND ('$type_name' = 'pending' OR '$type_name' = 'favourite' OR `project`.`status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project' OR `project`.`projecttype`='$tile') AND (`project`.`projecttype`='$type_name' OR ('$type_name' = 'favourite' AND `project`.`favourite` LIKE '%,".$_SESSION['contactid'].",%') OR ('$type_name' = 'pending' AND `project`.`status`='Pending')) $match_query ORDER BY REPLACE(`project`.`favourite`,',','') LIKE ',".$_SESSION['contactid'].",' DESC, `project`.`project_name` ASC, `project`.`projectid` DESC");
		}
		while($project_line = mysqli_fetch_assoc($project_query)) {
			$key = $project_line['projectid'];
			if($project_sort == 'project') {
				$key = $project_line['project_name'].$project['projectid'];
			} else if($project_sort == 'business') {
				$key = get_client($dbc, $project_line['businessid']).$project_line['project_name'].$project['projectid'];
			} else if($project_sort == 'sites') {
				$key = get_contact($dbc, $project_line['siteid'],'CONCAT(`display_name`,`site_name`)').$project_line['project_name'].$project['projectid'];
			} else if($project_sort == 'contact') {
				$key = get_contact($dbc, array_filter(explode(',',$project_line['clientid']))[0],'last_name').get_contact($dbc, array_filter(explode(',',$project_line['clientid']))[0],'first_name').$project_line['project_name'].$project['projectid'];
			}
			$project_list[$type_name][$key] = $project_line['projectid'];
			$search_list[$project_line['projectid']] = $project_line['projectid'].' '.get_client($dbc, $project_line['businessid']).' '.get_contact($dbc, $project_line['clientid']).' '.$project_line['project_name'];
			if($project_line['businessid'] > 0) {
				$business_list[$project_line['businessid']][] = $project_line['projectid'];
			}
			foreach(explode(',',$project_line['clientid']) as $project_clientid) {
				if($project_clientid > 0) {
					$contact_list[$project_clientid][] = $project_line['projectid'];
				}
			}
			foreach(array_filter(explode(',',$project_line['region'])) as $project_region) {
				$region_list[$project_region][] = $project_line['projectid'];
			}
			foreach(array_filter(explode(',',$project_line['classification'])) as $project_class) {
				$class_list[$project_class][] = $project_line['projectid'];
			}
			$lead_list[$project_line['project_lead']][] = $project_line['projectid'];
			$colead_list[$project_line['project_colead']][] = $project_line['projectid'];
		}
		if($project_sort == 'newest') {
			krsort($project_list[$type_name]);
		} else {
			ksort($project_list[$type_name]);
		}
		foreach($project_list[$type_name] as $project) {
			$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '$project'")); ?>
			if(project_list['<?= $type_name ?>'] == undefined) {
				project_list['<?= $type_name ?>'] = [];
			}
			var project_exists = false;
			project_list['<?= $type_name ?>'].forEach(function(project) {
				if(project['projectid'] == '<?= $project['projectid'] ?>') {
					project_exists = true;
				}
			});
			if(!project_exists) {
				project_list['<?= $type_name ?>'].push({'projectid':'<?= $project['projectid'] ?>','businessid':'<?= $project['businessid'] ?>','siteid':'<?= $project['siteid'] ?>'});
			}<?php
		}
	} else {
		unset($project_tabs[$type_name]);
	}
	$tab_list[] = $type_name;
}
?>
// 	project_list['<?= $type_name ?>'] = [<?= implode(',',array_unique($project_list[$type_name])); ?>];
<?php
// }

function convert_format($time){
    $time_str = '';
    $t = explode(':',$time);
    if($t[0]>0){
        $time_str .= intval($t[0]).' h ';
    }
    if($t[1]>0){
        $time_str .= intval($t[1]).' m ';
    }
    return $time_str;
}

$contacts = sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `project` LEFT JOIN `contacts` ON CONCAT(',',`project`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') AND `contacts`.`deleted`=0 AND `contacts`.`status`=1 AND `project`.`projectid` IS NOT NULL"));
$businesses = sort_contacts_query(mysqli_query($dbc, "SELECT `name`, `contactid` FROM `contacts` WHERE `contactid` IN (SELECT `businessid` FROM `project` WHERE `deleted`=0) AND `deleted`=0"));
$leads = sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `project` LEFT JOIN `contacts` ON `project`.`project_lead`=`contacts`.`contactid` AND `contacts`.`deleted`=0 AND `contacts`.`status` > 0 AND `project`.`projectid` IS NOT NULL"));
$coleads = sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `project` LEFT JOIN `contacts` ON `project`.`project_colead`=`contacts`.`contactid` AND `contacts`.`deleted`=0 AND `contacts`.`status` > 0 AND `project`.`projectid` IS NOT NULL"));

$uncategorized_sql = "SELECT `projectid`, `businessid`, `clientid`, `project_name` FROM `project` WHERE `deleted`=0 AND `status` NOT IN ('Archive'".($pending_projects == 'disable' ? '' : ",'Pending'").") AND '$tile' = 'project' AND `projecttype` NOT IN ('".implode("','",$tab_list)."') ORDER BY REPLACE(`favourite`,',','') LIKE ',".$_SESSION['contactid'].",' DESC, `project_name` ASC, `projectid` DESC";
$project_list = mysqli_query($dbc, $uncategorized_sql); ?>
project_list['uncategorized'] = [<?php while($project_line = mysqli_fetch_assoc($project_list)) {
	echo $project_line['projectid'].',';
	$search_list[] = $project_line['projectid'].' '.get_client($dbc, $project_line['businessid']).' '.get_contact($dbc, $project_line['clientid']).' '.$project_line['project_name'];
}
krsort($search_list); ?>];
var search_list = ["<?= implode('","',$search_list) ?>"];
<?php foreach($business_list as $businessid => $business_projects) { ?>
	project_list['business_<?= $businessid ?>'] = [<?= implode(',',array_unique($business_projects)) ?>];
<?php } ?>
<?php ksort($region_list);
foreach($region_list as $region_name => $region_projects) { ?>
	project_list['region_<?= config_safe_str($region_name) ?>'] = [<?= implode(',',array_unique($region_projects)) ?>];
<?php } ?>
<?php ksort($class_list);
foreach($class_list as $class_name => $class_projects) { ?>
	project_list['class_<?= config_safe_str($class_name) ?>'] = [<?= implode(',',array_unique($class_projects)) ?>];
<?php } ?>
<?php foreach($contact_list as $project_clientid => $contact_projects) { ?>
	project_list['contact_<?= $project_clientid ?>'] = [<?= implode(',',array_unique($contact_projects)) ?>];
<?php } ?>
<?php foreach($lead_list as $project_lead => $led_projects) { ?>
	project_list['lead_<?= $project_lead ?>'] = [<?= implode(',',array_unique($led_projects)) ?>];
<?php } ?>
<?php foreach($colead_list as $project_colead => $coled_projects) { ?>
	project_list['lead_<?= $project_colead ?>'] = [<?= implode(',',array_unique($coled_projects)) ?>];
<?php } ?>
$(document).ready(function() {
	if($('#display_screen').is(':visible')) {
		selectType(project_type);
	}
	$('[data-table]').change(saveDBField);
    $('.tile-sidebar>ul').prepend('<li class="standard-sidebar-searchbox"><input type="text" class="form-control search_list" placeholder="Search '+project_tile+'"></li>');
	$('.search_list').off('focus').focus(function() {
		if(this.value != '') {
			$(this).keyup();
		}
	}).keyup(function() {
		if(this.value == '') {
			$('.search-results').addClass('hidden');
			$('.main-content-screen, #project_accordions').removeClass('hidden');
			selectType(project_type);
		} else {
			ajaxCalls.forEach(function(call) { call.abort(); });
			var item = document.createElement('div');
			item.innerHTML = this.value;
			query = item.innerText.toLowerCase();
			clearTimeout(loadResults);
			loadResults = setTimeout(function() {
				current_list = [];
				search_list.forEach(function(project_string) {
					item.innerHTML = project_string;
					if(item.innerText.toLowerCase().indexOf(query) !== -1) {
						current_list.push({'projectid':project_string.split(' ')[0]});
					}
				});
				$('.search-results').removeClass('hidden');
				$('.main-content-screen, #project_accordions').addClass('hidden');
                $('.search-results .main-screen').html('');
				loadProjects($('.search-results .main-screen'));
			}, 250);
		}
	});
	$('.panel-heading').click(loadDBPanel);
	if($('#display_screen').is(':visible')) {
		loadProjects($('#display_screen'));
	}
	$('.search-results').scroll(function() {
		loadProjects($('.search-results'));
	});
	$(window).scroll(function() {
		var panel = $('.panel-body:visible,.search-results').first();
		loadProjects(panel);
	});
});
</script>

<!-- Search on mobile -->
<div class="tile-sidebar show-on-mob gap-top" style="width:98%;">
    <ul></ul>
</div>

<div id="project_accordions" class="sidebar show-on-mob panel-group block-panels col-xs-12">
	<?php if(in_array('Types',$project_classify) || in_array('All',$project_classify)) {
		foreach($project_tabs as $type_name => $project_label) { ?>
			<?php if($type_name == 'VIEW_ALL') {
				$count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM `project` WHERE `deleted`=0 AND `status`!='Archive' AND (`status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project')"))[0];
			} else {
				$count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM `project` WHERE `deleted`=0 AND `status`!='Archive' AND ('$type_name' = 'pending' OR '$type_name' = 'favourite' OR `status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project' OR `projecttype`='$tile') AND (`projecttype`='$type_name' OR ('$type_name' = 'favourite' AND `favourite` LIKE '%,".$_SESSION['contactid'].",%') OR ('$type_name' = 'pending' AND `status`='Pending'))"))[0];
			} ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_<?= $type_name ?>">
							<?= $project_label ?><span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= $count ?></span>
						</a>
					</h4>
				</div>

				<div id="collapse_<?= $type_name ?>" class="panel-collapse collapse">
					<div class="panel-body" data-project-type="<?= $type_name ?>">
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(in_array('Region',$project_classify)) {
		foreach($region_list as $region => $region_projects) {
			$region_string = config_safe_str($region); ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_<?= $region_string ?>">
							<?= $region == '' ? 'No Region' : $region ?><span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= count(array_unique($region_projects)) ?></span>
						</a>
					</h4>
				</div>

				<div id="collapse_<?= $region_string ?>" class="panel-collapse collapse">
					<div class="panel-body" data-project-project-type="region_<?= $region_string ?>">
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(in_array('Classifications',$project_classify)) {
		foreach($class_list as $class_name => $class_projects) {
			$class_string = config_safe_str($class_name); ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_<?= $class_string ?>"">
							<?= $class_name == '' ? 'No Classification' : $class_name ?><span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= count(array_unique($class_projects)) ?></span>
						</a>
					</h4>
				</div>

				<div id="collapse_<?= $class_string ?>" class="panel-collapse collapse">
					<div class="panel-body" data-project-project-type="class_<?= $class_string ?>">
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(in_array('Business',$project_classify)) {
		foreach($businesses as $business) {
			if(isset($business_list[$business['contactid']])) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_<?= $business['contactid'] ?>">
								<?= $business['name'] == '' ? '(Unknown)' : $business['name'] ?><span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= count(array_unique($business_list[$business['contactid']])) ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_<?= $business['contactid'] ?>" class="panel-collapse collapse">
						<div class="panel-body" data-project-project-type="business_<?= $business['contactid'] ?>">
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	<?php } ?>
	<?php if(in_array('Contact',$project_classify)) {
		foreach($contacts as $contact) {
			if(isset($contact_list[$contact['contactid']])) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_contact_<?= $contact['contactid'] ?>">
								<?= $contact['first_name'].$contact['last_name'] == '' ? '(Unknown)' : $contact['first_name'].' '.$contact['last_name'] ?><span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= count(array_unique($contact_list[$contact['contactid']])) ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_contact_<?= $contact['contactid'] ?>" class="panel-collapse collapse">
						<div class="panel-body" data-project-project-type="contact_<?= $contact['contactid'] ?>">
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	<?php } ?>
	<?php if(in_array('Lead',$project_classify)) {
		foreach($leads as $contact) {
			if(isset($lead_list[$contact['contactid']])) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_lead_<?= $contact['contactid'] ?>">
								<?= $contact['first_name'].$contact['last_name'] == '' ? '(Unknown)' : $contact['first_name'].' '.$contact['last_name'] ?><span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= count(array_unique($contact_list[$contact['contactid']])) ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_lead_<?= $contact['contactid'] ?>" class="panel-collapse collapse">
						<div class="panel-body" data-project-project-type="lead_<?= $contact['contactid'] ?>">
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	<?php } ?>

	<?php if(in_array('Colead',$project_classify)) {
		foreach($coleads as $contact) {
			if(isset($colead_list[$contact['contactid']])) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_colead_<?= $contact['contactid'] ?>">
								<?= $contact['first_name'].$contact['last_name'] == '' ? '(Unknown)' : $contact['first_name'].' '.$contact['last_name'] ?><span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= count(array_unique($contact_list[$contact['contactid']])) ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_colead_<?= $contact['contactid'] ?>" class="panel-collapse collapse">
						<div class="panel-body" data-project-project-type="lead_<?= $contact['contactid'] ?>">
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	<?php } ?>
</div>


<div class="tile-sidebar inherit-height sidebar sidebar-override double-gap-top hide-titles-mob  standard-collapsible">
    <ul>
		<?php if(in_array_starts('SUMM',$summ_config)) { ?>
			<a href="?tile_name=<?= $tile ?>&tab=summary" onclick="$('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue'); $('#project_admin,#display_screen').hide(); $('#project_summary').show(); return false;"><li class="<?= empty($_GET['tab']) || $_GET['tab'] == 'summary' ? 'active blue' : '' ?>">Summary</li></a>
			<?php $project_type = '';
		} ?>
		<?php if(check_subtab_persmission($dbc, 'project', ROLE, 'administration')) {
			$admin_groups = $dbc->query("SELECT `id`, `name`,`region`,`classification`,`location`,`customer` FROM `field_config_project_admin` WHERE CONCAT(',',`contactid`,',') LIKE '%".$_SESSION['contactid']."%' AND `deleted`=0");
			if($admin_groups->num_rows > 0) { ?>
				<li class="sidebar-higher-level"><a class="cursor-hand <?= isset($_GET['tab']) ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#tab_admin">Administration<span class="arrow"></span></a>
					<ul id="tab_admin" class="collapse <?= isset($_GET['tab']) ? 'in' : '' ?>">
						<?php while($admin_group = $admin_groups->fetch_assoc()) {
							$other_groups = $dbc->query("SELECT GROUP_CONCAT(`region` SEPARATOR ''',''') `regions`, GROUP_CONCAT(`classification` SEPARATOR ''',''') `classifications` FROM `field_config_project_admin` WHERE `id`!='{$admin_group['id']}' AND `deleted`=0")->fetch_assoc(); ?>
							<h4><?= $admin_group['name'].
								($admin_group['region'] != '' ? '<br /><em><small>'.$admin_group['region'].'</small></em>' : '').
								($admin_group['classification'] != '' ? '<br /><em><small>'.$admin_group['classification'].'</small></em>' : '').
								($admin_group['location'] != '' ? '<br /><em><small>'.$admin_group['location'].'</small></em>' : '').
								($admin_group['customer'] > 0 ? '<br /><em><small>'.get_contact($dbc,$admin_group['customer'],'full_name').'</small></em>' : '') ?></h4>
							<?php $admin_regions = $admin_classes = [''];
							if($admin_group['region'] == '') {
								$admin_regions = mysqli_fetch_all($dbc->query("SELECT IFNULL(`region`,'') FROM `tickets` WHERE `deleted`=0 ".($other_groups['regions'] != "','" && $other_groups['regions'] != "" ? " AND ((`region` IN ('{$admin_group['region']}','') AND `region` NOT IN ('{$other_groups['regions']}')) OR ('{$admin_group['region']}'='' AND `region` NOT IN ('{$other_groups['regions']}')))" : "")." GROUP BY IFNULL(`region`,'')"));
							}
							if($admin_group['classification'] == '') {
								$admin_classes = mysqli_fetch_all($dbc->query("SELECT IFNULL(`classification`,'') FROM `tickets` WHERE `deleted`=0 ".($other_groups['classifications'] != "','" && $other_groups['classifications'] != "" ? " AND (`classification` IN ('{$admin_group['classification']}','') OR ('{$admin_group['classification']}'='' AND `classification` NOT IN ('{$other_groups['classifications']}')))" : "")." GROUP BY IFNULL(`classification`,'')"));
							}
							foreach($admin_regions as $region_i => $admin_region) {
								foreach($admin_classes as $class_i => $admin_class) { ?>
									<?php if($admin_region[0].$admin_class[0] != '') { ?>
										<li><a class="cursor-hand <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && strpos($_GET['tab'], '_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0]))) !== FALSE ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#tab_admin_<?= $admin_group['id'] ?>_<?= $region_i ?>_<?= $class_i ?>"><?= $admin_region[0] != '' ? 'Region: '.$admin_region[0] : '' ?><?= $admin_region[0] != '' && $admin_class[0] != '' ? '<br />' : '' ?><?= $admin_class[0] != '' ? 'Classification: '.$admin_class[0] : '' ?><span class="arrow"></span></a>
											<ul id="tab_admin_<?= $admin_group['id'] ?>_<?= $region_i ?>_<?= $class_i ?>" class="collapse <?= strpos($_GET['tab'], 'administration_'.$admin_group['id'].'_') !== FALSE && strpos($_GET['tab'], '_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0]))) !== FALSE ? 'in' : '' ?>">
									<?php } ?>
									<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_approved_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0])) ? 'active blue' : '' ?>"><a href="?tile_name=<?= $_GET['tile_name'] ?>&tab=administration_<?= $admin_group['id'] ?>_approved_<?= str_replace('_','',config_safe_str($admin_region[0])) ?>_<?= str_replace('_','',config_safe_str($admin_class[0])) ?>">Approved</a></li>
									<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_revision_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0])) ? 'active blue' : '' ?>"><a href="?tile_name=<?= $_GET['tile_name'] ?>&tab=administration_<?= $admin_group['id'] ?>_revision_<?= str_replace('_','',config_safe_str($admin_region[0])) ?>_<?= str_replace('_','',config_safe_str($admin_class[0])) ?>">In Revision</a></li>
									<li class="sidebar-lower-level <?= $_GET['tab'] == 'administration_'.$admin_group['id'].'_pending_'.str_replace('_','',config_safe_str($admin_region[0])).'_'.str_replace('_','',config_safe_str($admin_class[0])) ? 'active blue' : '' ?>"><a href="?tile_name=<?= $_GET['tile_name'] ?>&tab=administration_<?= $admin_group['id'] ?>_pending_<?= str_replace('_','',config_safe_str($admin_region[0])) ?>_<?= str_replace('_','',config_safe_str($admin_class[0])) ?>">Pending</a></li>
									<?php if($admin_region[0].$admin_class[0] != '') { ?>
										</ul></li>
									<?php } ?>
								<?php }
							} ?>
						<?php } ?>
					</ul>
				</li>
			<?php }
		} ?>
		<?php $sort_fields = array_filter(explode(',',get_config($dbc, 'project_sort_fields'))); ?>
		<?php if(in_array('Business',$project_classify)) { ?>
            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#Business">Business<span class="arrow"></span></a>
                <ul class="collapse" id="Business" style="overflow: hidden;">
			<?php asort($businesses); ?>
			<?php foreach($businesses as $business) {
				if(isset($business_list[$business['contactid']])) { ?>
					<a href="?tile_name=<?= $tile ?>&type=business_<?= $business['contactid'] ?>" onclick="selectType('business_<?= $business['contactid'] ?>', undefined, '<?= $business['name'] == '' ? '(Unknown)' : htmlentities($business['name'], ENT_QUOTES) ?>'); return false;"><li class="<?= 'business_'.$business['contactid'] == $project_type ? 'active blue' : '' ?>"><?= $business['name'] == '' ? '(Unknown)' : $business['name'] ?><span class="pull-right"><?= count(array_unique($business_list[$business['contactid']])) ?></span></li></a>
				<?php } ?>
			<?php } ?>
            </ul></li>
		<?php } ?>
		<?php if(in_array('Classifications',$project_classify)) { ?>
            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#Classifications">Classifications<span class="arrow"></span></a>
                <ul class="collapse" id="Classifications" style="overflow: hidden;">
			<?php asort($class_list); ?>
			<?php foreach($class_list as $class_name => $class_projects) { ?>
				<?php $class_string = config_safe_str($class_name); ?>
				<a href="?tile_name=<?= $tile ?>&type=class_<?= $class_string ?>" onclick="selectType('class_<?= $class_string ?>', undefined, '<?= $class_name == '' ? 'No Classification' : htmlentities($class_name,ENT_QUOTES) ?>'); return false;"><li class="<?= $project_type == 'class_'.$class_string && isset($_GET['classification']) ? 'active blue' : '' ?>"><?= $class_name == '' ? 'No Classification' : $class_name ?><span class="pull-right"><?= count(array_unique($class_projects)) ?></span></li></a>
			<?php } ?>
            </ul></li>
		<?php } ?>
		<?php if(in_array('Contact',$project_classify)) { ?>
            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#Contact">Contact<span class="arrow"></span></a>
                <ul class="collapse" id="Contact" style="overflow: hidden;">
			<?php asort($contacts); ?>
			<?php foreach($contacts as $contact) {
				if(isset($contact_list[$contact['contactid']])) { ?>
					<a href="?tile_name=<?= $tile ?>&type=contact_<?= $contact['contactid'] ?>" onclick="selectType('contact_<?= $contact['contactid'] ?>', undefined, '<?= $contact['first_name'].$contact['last_name'] == '' ? '(Unknown)' : htmlentities($contact['first_name'].' '.$contact['last_name'], ENT_QUOTES) ?>'); return false;"><li class="<?= 'contact_'.$contact['contactid'] == $project_type ? 'active blue' : '' ?>"><?= $contact['first_name'].$contact['last_name'] == '' ? '(Unknown)' : $contact['first_name'].' '.$contact['last_name'] ?><span class="pull-right"><?= count(array_unique($contact_list[$contact['contactid']])) ?></span></li></a>
				<?php }
			}
            echo '</ul></li>';
		} ?>
		<?php if(in_array('Lead',$project_classify)) { ?>
            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#Lead">Lead<span class="arrow"></span></a>
                <ul class="collapse" id="Lead" style="overflow: hidden;">
			<?php asort($leads); ?>
			<?php foreach($leads as $contact) {
				if(isset($lead_list[$contact['contactid']])) { ?>
					<a href="?tile_name=<?= $tile ?>&type=lead_<?= $contact['contactid'] ?>" onclick="selectType('lead_<?= $contact['contactid'] ?>', undefined, '<?= $contact['first_name'].$contact['last_name'] == '' ? '(Unknown)' : htmlentities($contact['first_name'].' '.$contact['last_name'], ENT_QUOTES) ?>'); return false;"><li class="<?= 'lead_'.$contact['contactid'] == $project_type ? 'active blue' : '' ?>"><?= $contact['first_name'].$contact['last_name'] == '' ? '(Unknown)' : $contact['first_name'].' '.$contact['last_name'] ?><span class="pull-right"><?= count(array_unique($lead_list[$contact['contactid']])) ?></span></li></a>
				<?php }
			}
            echo '</ul></li>';
		} ?>
		<?php if(in_array('Regions',$project_classify)) { ?>
            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#Regions">Regions<span class="arrow"></span></a>
                <ul class="collapse" id="Regions" style="overflow: hidden;">
			<?php asort($region_list); ?>
			<?php foreach($region_list as $region_name => $region_projects) { ?>
				<?php $region_string = config_safe_str($region_name); ?>
				<a href="?tile_name=<?= $tile ?>&type=region_<?= $region_string ?>" onclick="selectType('region_<?= $region_string ?>', undefined, '<?= $region_name == '' ? 'No Region' : htmlentities($region_name, ENT_QUOTES) ?>'); return false;"><li class="<?= $project_type == 'region_'.$region_string && isset($_GET['region_name']) ? 'active blue' : '' ?>"><?= $region_name == '' ? 'No Region' : $region_name ?><span class="pull-right"><?= count(array_unique($region_projects)) ?></span></li></a>
			<?php } ?>
            </ul></li>
		<?php } ?>
		<?php
		if(in_array('Types',$project_classify) || in_array('All',$project_classify)) { ?>

            <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#types">Types<span class="arrow"></span></a>
                <ul class="collapse" id="types" style="overflow: hidden;">
			<?php asort($project_tabs); ?>
			<?php foreach($project_tabs as $type_name => $project_label) {

                $project_tabs_for_color = get_config($dbc, "project_tabs");
                $project_type_color_for_color = get_config($dbc, "project_type_color");

                $var_count = explode($project_label, $project_tabs_for_color);
                $occr = substr_count($var_count[0], ",");
                $color_apply = '';
                $var_count_loop = explode(',', $project_type_color_for_color);
                if (strpos($project_tabs_for_color, $project_label) !== false) {
                    $color_apply = $var_count_loop[$occr];
                }

                $c_a = 'style="background-color:'.$color_apply.';height: 20px;width: 20px;margin-top: 5px;"';

                ?>
				<?php if($type_name == 'VIEW_ALL') {
					$count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM `project` WHERE `deleted`=0 AND `status`!='Archive' AND (`status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project')"))[0];
				} else {
					$count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM `project` WHERE `deleted`=0 AND `status`!='Archive' AND ('$type_name' = 'pending' OR '$type_name' = 'favourite' OR `status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project' OR `projecttype`='$tile') AND (`projecttype`='$type_name' OR ('$type_name' = 'favourite' AND `favourite` LIKE '%,".$_SESSION['contactid'].",%') OR ('$type_name' = 'pending' AND `status`='Pending'))"))[0];
				} ?>
				<!-- <div class="row"><div class="col-sm-2"><div <?= $c_a ?>></div></div><div class="col-sm-10"><a href="?tile_name=<?= $tile ?>&type=<?= $type_name ?>" onclick="selectType('<?= $type_name ?>', undefined, '<?= $project_label ?>'); return false;"><li class="<?= $type_name == $project_type && !isset($_GET['tab']) ? 'active blue' : '' ?>"><?= $project_label ?><span class="pull-right"><?= $count ?></span></li></a></div></div> -->

                <div class="row"><div><?= in_array('SUMM Colors', $summ_config) ? '<div class="col-sm-2"><div '.$c_a.'></div></div>' : '' ?><a <?= !empty($sort_fields) ? 'class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_type_'.$type_name.'"' : 'href="?tile_name='.$tile.'&type='.$type_name.'" onclick="selectType(\''.$type_name.'\', undefined, \''.htmlentities($project_label, ENT_QUOTES).'\'); return false;"' ?>><li class="<?= !empty($sort_fields) ? 'sidebar-higher-level highest_level' : ($type_name == $project_type && !isset($_GET['tab']) ? 'active blue' : '') ?>"><?= $project_label ?><?= !empty($sort_fields) ? '<span class="arrow"></span>' : '' ?><span class="pull-right"><?= $count ?></span>
                	<?php if(!empty($sort_fields)) { ?>
                		<ul class="collapse" id="filter_type_<?= $type_name ?>" style="margin-top: 0px !important;">
                			<?php if(in_array('Business',$sort_fields)) { ?>
                				<a class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_type_business_<?= $type_name ?>" style="margin-left: 0px;"><li class="sidebar-higher-level"><?= BUSINESS_CAT ?><span class="arrow"></span>
                					<ul class="collapse" id="filter_type_business_<?= $type_name ?>">
                						<?php
                						if($type_name == 'VIEW_ALL') {
	                						$business_list = sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`name`, `contacts`.`contactid`, COUNT(*) `count` FROM `contacts` LEFT JOIN `project` ON `project`.`businessid` = `contacts`.`contactid` AND `project`.`deleted` = 0 WHERE `contacts`.`deleted` = 0 AND `project`.`status`!='Archive' AND (`project`.`status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project') GROUP BY `contacts`.`contactid`"));
                						} else {
	                						$business_list = sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`name`, `contacts`.`contactid`, COUNT(*) `count` FROM `contacts` LEFT JOIN `project` ON `project`.`businessid` = `contacts`.`contactid` AND `project`.`deleted` = 0 WHERE `contacts`.`deleted` = 0 AND `project`.`status`!='Archive' AND ('$type_name' = 'pending' OR '$type_name' = 'favourite' OR `project`.`status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project' OR `project`.`projecttype`='$tile') AND (`project`.`projecttype`='$type_name' OR ('$type_name' = 'favourite' AND `project`.`favourite` LIKE '%,".$_SESSION['contactid'].",%') OR ('$type_name' = 'pending' AND `project`.`status`='Pending')) GROUP BY `contacts`.`contactid`"));
	                					}
	                					foreach($business_list as $business) {
	                						if($business['contactid'] > 0) { ?>
		                						<a href="?tile_name=<?= $tile ?>&type=<?= $type_name ?>&businessid=<?= $business['contactid'] ?>" onclick="$(this).find('li').toggleClass('active blue'); $('.highest_level').not($(this).closest('.highest_level')).find('.active.blue').removeClass('active').removeClass('blue'); selectType('<?= $type_name ?>', undefined, '<?= htmlentities($project_label, ENT_QUOTES) ?>'); return false;"><li class="sidebar-lower-level" style="padding-left: 25px; margin-left: 0px;" data-businessid="<?= $business['contactid'] ?>"><?= $business['name'] ?><span class="pull-right"><?= $business['count'] ?></span></li></a>
		                					<?php }
	                					} ?>
                					</ul>
                				</li></a>
                			<?php } ?>
                			<?php if(in_array('Site',$sort_fields)) { ?>
                				<a class="collapsed cursor-hand" data-toggle="collapse" data-target="#filter_type_site_<?= $type_name ?>" style="margin-left: 0px;"><li class="sidebar-higher-level">Site<span class="arrow"></span>
                					<ul class="collapse" id="filter_type_site_<?= $type_name ?>">
                						<?php
                						if($type_name == 'VIEW_ALL') {
	                						$site_list = sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`name`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`site_name`, `contacts`.`contactid`, COUNT(*) `count` FROM `contacts` LEFT JOIN `project` ON `project`.`siteid` = `contacts`.`contactid` AND `project`.`deleted` = 0 WHERE `contacts`.`deleted` = 0 AND `project`.`status`!='Archive' AND (`project`.`status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project') GROUP BY `contacts`.`contactid."));
                						} else {
	                						$site_list = sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`name`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`site_name`, `contacts`.`contactid`, COUNT(*) `count` FROM `contacts` LEFT JOIN `project` ON `project`.`siteid` = `contacts`.`contactid` AND `project`.`deleted` = 0 WHERE `contacts`.`deleted` = 0 AND `project`.`status`!='Archive' AND ('$type_name' = 'pending' OR '$type_name' = 'favourite' OR `project`.`status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project' OR `project`.`projecttype`='$tile') AND (`project`.`projecttype`='$type_name' OR ('$type_name' = 'favourite' AND `project`.`favourite` LIKE '%,".$_SESSION['contactid'].",%') OR ('$type_name' = 'pending' AND `project`.`status`='Pending')) GROUP BY `contacts`.`contactid`"));
	                					}
	                					foreach($site_list as $site) {
	                						if($site['contactid'] > 0) { ?>
		                						<a href="?tile_name=<?= $tile ?>&type=<?= $type_name ?>&siteid=<?= $site['contactid'] ?>" onclick="$(this).find('li').toggleClass('active blue'); $('.highest_level').not($(this).closest('.highest_level')).find('.active.blue').removeClass('active').removeClass('blue'); selectType('<?= $type_name ?>', undefined, '<?= htmlentities($project_label, ENT_QUOTES) ?>'); return false;"><li class="sidebar-lower-level" style="padding-left: 25px; margin-left: 0px;" data-siteid="<?= $site['contactid'] ?>"><?= !empty($site['site_name']) ? $site['site_name'] : $site['full_name'] ?><span class="pull-right"><?= $site['count'] ?></span></li></a>
		                					<?php }
	                					} ?>
                					</ul>
                				</li></a>
                			<?php } ?>
    						<a href="?tile_name=<?= $tile ?>&type=<?= $type_name ?>&view_all=ALL" onclick="$(this).find('li').toggleClass('active blue'); $(this).closest('.highest_level').find('.active.blue').not('.view_all').removeClass('active').removeClass('blue'); $('.highest_level').not($(this).closest('.highest_level')).find('.active.blue').removeClass('active').removeClass('blue'); selectType('<?= $type_name ?>', undefined, '<?= htmlentities($project_label, ENT_QUOTES) ?>'); return false;"><li class="sidebar-lower-level view_all" style="padding-left: 25px; margin-left: 0px;">View All<span class="pull-right"><?= $count ?></span></li></a>
                		</ul>
                	<?php } ?>
                </li></a></div></div>

			<?php }
			$other_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM `project` WHERE `deleted`=0 AND `status` NOT IN ('Archive'".($pending_projects == 'disable' ? '' : ",'Pending'").") AND '$tile' = 'project' AND `projecttype` NOT IN ('".implode("','",$tab_list)."')"))[0];
			if($other_count > 0 && in_array('Types',$project_classify)) { ?>
				<a href="?tile_name=<?= $tile ?>&type=uncategorized" onclick="selectType('uncategorized', undefined, 'Uncategorized'); return false;"><li class="<?= 'uncategorized' == $project_type ? 'active blue' : '' ?>">(Uncategorized)<span class="pull-right"><?= $other_count ?></span></li></a>
			<?php } ?>
            </ul></li>
		<?php } ?>
    </ul>
</div>

<div class='main-content-screen scale-to-fill has-main-screen hide-titles-mob standard-body'>
	<?php if($_GET['tab'] == 'summary' || in_array_starts('SUMM',$summ_config) && !isset($_GET['tab'])) { ?>
		<div class='main-screen override-main-screen form-horizontal' id="project_summary">
			<div class='standard-body-title'>
				<h3>Summary</h3>
			</div>
			<div class='standard-body-content pad-top pad-left pad-right pad-bottom'>
				<?php $blocks = [];
				$total_length = 0;
				$favourites = $dbc->query("SELECT * FROM `project` WHERE `deleted`=0 AND `status`!='Archive' AND ('$tile' = 'project' OR `projecttype`='$tile') AND `favourite` LIKE '%,".$_SESSION['contactid'].",%'");
				if(in_array('SUMM Favourite', $summ_config) && !empty($favourites->fetch_assoc())) {
					$block_length = 68;
					$block = '<div class="overview-block">
						<h4>Favourite '.PROJECT_TILE.'</h4>';
						while($fave = $favourites->fetch_assoc()) {
							$block .= '<a href="?edit='.$fave['projectid'].'&tile_name='.$_GET['tile_name'].'">'.get_project_label($dbc, $fave).'</a><br />';
							$block_length += 23;
						}
					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}
				if(in_array('SUMM Types', $summ_config) && !empty($project_tabs)) {
					$block_length = 68;
					$block = '<div class="overview-block">
						<h4>'.PROJECT_TILE.' by Type</h4>';
						foreach($project_tabs as $type_name => $project_label) {

                            $project_tabs_for_color = get_config($dbc, "project_tabs");
                            $project_type_color_for_color = get_config($dbc, "project_type_color");

                            $var_count = explode($project_label, $project_tabs_for_color);
                            $occr = substr_count($var_count[0], ",");
                            $color_apply = '';
                            $var_count_loop = explode(',', $project_type_color_for_color);
                            if (strpos($project_tabs_for_color, $project_label) !== false) {
                                $color_apply = $var_count_loop[$occr];
                            }

							$count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM `project` WHERE `deleted`=0 AND `status`!='Archive' AND ('$type_name' = 'pending' OR '$type_name' = 'favourite' OR `status` != 'Pending' OR '$pending_projects' = 'disable') AND ('$tile' = 'project' OR `projecttype`='$tile') AND (`projecttype`='$type_name' OR ('$type_name' = 'favourite' AND `favourite` LIKE '%,".$_SESSION['contactid'].",%') OR ('$type_name' = 'pending' AND `status`='Pending'))"))[0];

                            if($count > 0) {
                                if(in_array('SUMM Colors', $summ_config)) {
                                    $c_a = 'style="background-color:'.$color_apply.';height: 20px;width: 20px;margin-top: 5px;"';
                                    $block .= '<div class="row"><div class="col-sm-2"><div '. $c_a .'></div></div><div class="col-sm-8"><a href="?tile_name='.$tile.'&type='.$type_name.'" onclick="selectType(\''.$type_name.'\'); return false;"><label class="control-label cursor-hand">'.$project_label.':</label> '.$count.'</a></div></div><br />';
                                } else {
                                    $c_a = '';
                                    $block .= '<a href="?tile_name='.$tile.'&type='.$type_name.'" onclick="selectType(\''.$type_name.'\'); return false;"><label class="control-label cursor-hand">'.$project_label.':</label> '.$count.'</a><br />';
                                }
                            }

							$block_length += 23;
						}
						$other_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM `project` WHERE `deleted`=0 AND `status` NOT IN ('Archive','Pending') AND '$tile' = 'project' AND `projecttype` NOT IN ('".implode("','",$tab_list)."')"))[0];
						if($other_count > 0) {
							$block .= '<a href="?tile_name='.$tile.'&type=uncategorized" onclick="selectType(\'uncategorized\'); return false;"><label class="control-label cursor-hand">Uncategorized:</label> '.$other_count.'</a>';
							$block_length += 23;
						}
					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}
				if(in_array('SUMM Region', $summ_config) && !empty($region_list)) {
					$block_length = 68;
					$block = '<div class="overview-block">
						<h4>'.PROJECT_TILE.' by Region</h4>';
						foreach($region_list as $region_name => $region_projects) {
                            if(count(array_unique($region_projects)) > 0) {
                                $region_string = config_safe_str($region_name);
                                $block .= '<a href="?tile_name='.$tile.'&type=region_'.$region_string.'" onclick="selectType(\'region_'.$region_string.'\'); return false;"><label class="cursor-hand control-label">'.($region_name == '' ? 'No Region' : $region_name).':</label> '.count(array_unique($region_projects)).'</a><br />';
                                $block_length += 23;
                            }
						}
					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}
				if(in_array('SUMM Status', $summ_config)) {
					$status_list = explode('#*#',get_config($dbc, 'project_status'));
					if(get_config($dbc, 'project_status_pending') != 'disable') {
						$status_list = array_merge(['Pending'],$status_list);
					}
					$status_list[] = 'Archive';
					$block_length = 68;
					$block = '<div class="overview-block">
						<h4>'.PROJECT_TILE.' by Status</h4>';
						foreach($status_list as $status_name) {
							$status_count = $dbc->query("SELECT `status`, COUNT(*) `count` FROM `project` WHERE `deleted`=0 AND `status`='$status_name'")->fetch_assoc()['count'];
                            if($status_count > 0) {
							    $block .= '<a href="?tile_name'.$tile.'&tab=administration_'.$admin_group['id'].'_'.strtolower($status_name).'__"><label class="control-label">'.$status_name.':</label> '.$status_count.'</a><br />';
							    $block_length += 23;
                            }
						}
					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}
				echo '<style>.hidethisblock{display:none;;}</style>';
				if(in_array('SUMM Business', $summ_config) && !empty($businesses)) {
					$block_length = 68;
					$block = '<div class="overview-block hidethisblock">
						<h4>'.PROJECT_TILE.' by '.BUSINESS_CAT.'</h4>';
						foreach($businesses as $contact) {
							if(isset($business_list[$contact['contactid']])) {
							    echo '<style>.hidethisblock{display:block !important;}</style>';
								$block .= '<a href="?tile_name='.$tile.'&type=business_'.$contact['contactid'].'" onclick="selectType(\'business_'.$contact['contactid'].'\'); return false;"><label class="cursor-hand control-label">'.$contact['name'].':</label> '.count(array_unique($business_list[$contact['contactid']])).'</a><br />';
								$block_length += 23;
							}
						}
					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}
				if(in_array('SUMM Contacts', $summ_config) && !empty($contacts)) {
					$block_length = 68;
					$block = '<div class="overview-block">
						<h4>'.PROJECT_TILE.' by Contact</h4>';
						foreach($contacts as $contact) {
							if(isset($contact_list[$contact['contactid']])) {
								$block .= '<a href="?tile_name='.$tile.'&type=contact_'.$contact['contactid'].'" onclick="selectType(\'contact_'.$contact['contactid'].'\'); return false;"><label class="cursor-hand control-label">'.$contact['first_name'].' '.$contact['last_name'].':</label> '.count(array_unique($contact_list[$contact['contactid']])).'</a><br />';
								$block_length += 23;
							}
						}
					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}
				if(in_array('SUMM Leads', $summ_config) && !empty($leads)) {
					$block_length = 68;
					$block = '<div class="overview-block">
						<h4>'.PROJECT_TILE.' by Lead</h4>';
						foreach($leads as $contact) {
							if(isset($lead_list[$contact['contactid']])) {
								$block .= '<a href="?tile_name='.$tile.'&type=lead_'.$contact['contactid'].'" onclick="selectType(\'lead_'.$contact['contactid'].'\'); return false;"><label class="cursor-hand control-label">'.$contact['first_name'].' '.$contact['last_name'].':</label> '.count(array_unique($lead_list[$contact['contactid']])).'</a><br />';
								$block_length += 23;
							}
						}
					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}
				echo '<style>.hidethisblock1{display:none;;}</style>';
				if(in_array('SUMM Colead', $summ_config) && !empty($coleads)) {
					$block_length = 68;
					$block = '<div class="overview-block hidethisblock1">
						<h4>'.PROJECT_TILE.' by Co-Lead</h4>';
						foreach($coleads as $contact) {
							if(isset($colead_list[$contact['contactid']])) {
							    echo '<style>.hidethisblock1{display:block !important;}</style>';
								$block .= '<a href="?tile_name='.$tile.'&type=lead_'.$contact['contactid'].'" onclick="selectType(\'lead_'.$contact['contactid'].'\'); return false;"><label class="cursor-hand control-label">'.$contact['first_name'].' '.$contact['last_name'].':</label> '.count(array_unique($colead_list[$contact['contactid']])).'</a><br />';
								$block_length += 23;
							}
						}
					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}

				$total_estimated_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time`, `project`.* FROM `ticket_time_list` LEFT JOIN `tickets` ON `ticket_time_list`.`ticketid`=`tickets`.`ticketid` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `project`.`deleted`=0 AND `ticket_time_list`.`deleted`=0 AND `time_type` IN ('Completion Estimate','QA Estimate') AND `tickets`.`deleted`=0 GROUP BY `project`.`projectid`");
				if(in_array('SUMM Estimated', $summ_config) && !empty($total_estimated_time->fetch_assoc())) {
					$block_length = 68;
					$block = '<div class="overview-block">
						<h4>'.PROJECT_NOUN.' Estimated Time</h4>';
						while($time = $total_estimated_time->fetch_assoc()) {
						    $block .= '<label class="control-label"><a href="?tile_name='.$_GET['tile_name'].'&edit='.$time['projectid'].'">'.get_project_label($dbc, $time).':</a></label> '.convert_format($time['time']).'<br />';
							$block_length += 23;
						}
					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}
				$total_tracked_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) `time`, `project`.* FROM (SELECT `time_length` `time`, `ticketid` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type`='Manual Time' UNION SELECT `timer` `time`, `ticketid` FROM `ticket_timer` WHERE `deleted` = 0) `time_list` LEFT JOIN `tickets` ON `time_list`.`ticketid`=`tickets`.`ticketid` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`deleted`=0 AND `project`.`deleted`=0 GROUP BY `project`.`projectid`");
				if(in_array('SUMM Tracked', $summ_config) && !empty($total_tracked_time->fetch_assoc())) {
					$block_length = 68;
					$block = '<div class="overview-block">
						<h4>'.PROJECT_NOUN.' Actual Time</h4>';
						while($time = $total_tracked_time->fetch_assoc()) {
                            if($time['time'] > 0) {
                                $block .= '<label class="control-label"><a href="?tile_name='.$_GET['tile_name'].'&edit='.$time['projectid'].'">'.get_project_label($dbc, $time).':</a></label> '.convert_format($time['time']).'<br />';
							    $block_length += 23;
                            }
						}
					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}

				$piece_work = $dbc->query("SELECT `ticketid`, `piece_work` FROM `tickets` WHERE `deleted`=0 AND `status` NOT IN ('Archive','Archived','Done') AND piece_work != '' AND piece_work IS NOT NULL");
				if(in_array('SUMM Piece', $summ_config) && !empty($piece_work->fetch_assoc())) {
					$block_length = 68;
					$block = '<div class="overview-block">
						<h4>'.TICKET_TILE.' by Piece Work</h4>';

                        while($piece = $piece_work->fetch_assoc()) {
                                $block .= '<label class="control-label"><a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$piece['ticketid'].'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">#'.$piece['ticketid'].'</a></label> : '.$piece['piece_work'].'<br />';
							    $block_length += 23;
                        }

					$block .= '</div>';
					$blocks[] = [$block_length, $block];
					$total_length += $block_length;
				}


				$display_column = 0;
				$displayed_length = 0; ?>
				<div class="col-sm-6">
					<?php foreach($blocks as $block) {
						if($block[0] == $displayed_length && $display_column == 0) {
							$displayed_length = 0;
							$total_length -= $block[0] + $displayed_length;
							echo '</div><div class="col-sm-6">'.$block[1].'</div><div class="col-sm-6">';
						} else if($displayed_length > $total_length / 2) {
							$displayed_length = 0;
							$display_column = 1;
							echo '</div><div class="col-sm-6">'.$block[1];
						} else {
							$displayed_length += $block[0];
							echo $block[1];
						}
					} ?>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	<?php } else if(isset($_GET['tab'])) { ?>
		<div class='main-screen override-main-screen form-horizontal' id="project_admin">
			<div class='standard-body-title'>
				<h3>Administration</h3>
			</div>
			<div class='standard-body-content pad-top pad-left pad-right pad-bottom'>
				<?php include('project_administration.php'); ?>
			</div>
		</div>
	<?php } ?>
    <div class='main-screen override-main-screen form-horizontal standard-dashboard-body-content' id='display_screen' style='<?= isset($_GET['tab']) || in_array_starts('SUMM',$summ_config) ? 'display:none;' : '' ?>'></div>
</div>

<div class='scale-to-fill has-main-screen hidden search-results'>
    <div class='main-screen override-main-screen form-horizontal standard-dashboard-body-content' id='display_screen'></div>
</div>

<div class="clearfix"></div>
