<?php include_once('../include.php');
checkAuthorised('equipment');

$security = get_security($dbc, 'equipment');
?>
<script type="text/javascript">
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').not('.show-on-mob .main-screen').is(':visible')) {
			var available_height = window.innerHeight;
			if($('#footer').is(':visible')) {
				available_height = available_height - $('#footer').outerHeight();
			}
			if($('.sidebar').is(':visible')) {
				available_height = available_height - $('.sidebar:visible').offset().top;
			}
			if(available_height > 200 && $(window).width() >= 768) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
			} else {
				$('.main-screen .main-screen').height('auto');
			}
		}
	}).resize();
});
</script>
</head>
<body>
<?php include_once ('../navigation.php'); ?>

<div id="equip_div" class="container">
	<div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
		<div class="iframe">
			<iframe name="equipment_iframe" src="../blank_loading_page.php"></iframe>
		</div>
	</div>
	<div class='iframe_holder' style='display:none;'>
		<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
    </div>
	<div class="row hide_on_iframe">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<div class="pull-right settings-block">
					<?php if($security['config'] > 0) {
						echo '<div class="pull-right gap-left"><a href="?settings=tab"><img src="'.WEBSITE_URL.'/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a></div>';
					}
					if($security['edit'] > 0) {
						echo '<div class="pull-right gap-left"><a href="?edit=&category='.($_GET['category'] != 'Top' ? $_GET['category'] : '').'" class="new-btn"><button class="btn brand-btn">New Equipment</button></a></div>';
						echo '<div class="pull-right gap-left"><a href="import_export.php?category='.$_GET['category'].'&action=import" class="no-toggle" title="Import CSV into Equipment List" onclick="overlayIFrameSlider(this.href,\'auto\',true,false,\'auto\',true); return false;"><img class="inline-img" src="../img/csv.png"></a></div>';
						echo '<div class="pull-right gap-left"><a href="import_export.php?category='.$_GET['category'].'&action=export" class="no-toggle" title="Export Equipment List as CSV" onclick="overlayIFrameSlider(this.href,\'auto\',true,true,\'auto\',true); return false;"><img class="inline-img" src="../img/icons/ROOK-download-icon.png"></a></div>';
					} ?>
				</div>
				<div class="scale-to-fill">
					<?php if($_GET['edit'] > 0) {
						$equipmentid = $_GET['edit'];
						$get_equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$equipmentid."'"));

						$unit_number = $get_equipment['unit_number'];
					} ?>
					<h1 class="gap-left"><a href="?">Equipment</a><?= $equipmentid > 0 ? ': Unit #'.$unit_number : '' ?></h1>
				</div>
                <?php $note = '';
                if(!isset($_GET['edit']) && !isset($_GET['edit_inspection']) && !isset($_GET['edit_assigned_equipment']) && !isset($_GET['edit_work_order']) && !isset($_GET['edit_service_request']) && !isset($_GET['edit_service_record']) && !isset($_GET['edit_checklist']) && (!isset($_GET['settings']) || $security['config'] < 1)) {
                    switch($_GET['tab']) {
                        case 'inspections':
                            $note = '';
                            break;
                        case 'assign_equipment':
                            $note = 'Here you can add and edit all equipment assignments.';
                            break;
                        case 'work_orders':
                            $note = '';
                            break;
                        case 'expenses':
                            $note = '';
                            break;
                        case 'balance':
                            $note = '';
                            break;
                        case 'service_schedules':
                            $note = '';
                            break;
                        case 'service_request':
                            $note = 'Whether your business maintains its own equipment and wishes to file service requests or work orders through this section or you\'re looking to track and record progress on all service requests being run through your company, this section has the ability to maintain and monitor all your equipment.';
                            break;
                        case 'service_record':
                            $note = 'Through this section, full services records and tracking can be done on all equipment. These records are ideal for reporting, sales, year end and tracking the profit and losses on all equipment.';
                            break;
                        case 'equipment_checklist':
                            $note = 'A checklist is defined for either an equipment tab, a type of equipment, or a specific piece of equipment. Once you have selected a piece of equipment, any checklists that match the piece of equipment will be displayed.';
                            break;
                        default:
                            $note = 'Tracking and maintaining equipment is an essential element for every business. Through this section you\'ll be able to Add/Edit/Archive equipment you wish to use throughout projects or capture essential data on.';
                            break;
                    }
                }
                if($note != '') { ?>
                    <div class="notice double-gap-top double-gap-bottom popover-examples">
                        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                        <div class="col-sm-11"><span class="notice-name">NOTE: </span><?= $note ?></div>
                        <div class="clearfix"></div>
                    </div>
                <?php } ?>
				<div class="clearfix"></div>
			</div>

			<div class="clearfix"></div>
            <?php $overview = get_config($dbc, 'show_equipment_overview');
			if(isset($_GET['edit_inspection'])) {
				include('edit_inspection.php');
			} else if(isset($_GET['edit_assigned_equipment'])) {
				include('edit_assigned_equipment.php');
			} else if(isset($_GET['edit_work_order'])) {
				include('edit_work_order.php');
			} else if(isset($_GET['edit_service_request'])) {
				include('edit_service_request.php');
			} else if(isset($_GET['edit_service_record'])) {
				include('edit_service_record.php');
			} else if(isset($_GET['edit_checklist'])) {
				include('edit_checklist.php');
			} else if(isset($_GET['edit'])) {
				include('edit_equipment_header.php');
				if($_GET['subtab'] == 'inspections') {
					include('edit_equipment_inspections.php');
				} else if($_GET['subtab'] == 'work_orders') {
					include('edit_equipment_work_order.php');
				} else if($_GET['subtab'] == 'service') {
					include('edit_equipment_service.php');
				} else if($_GET['subtab'] == 'expenses') {
					include('edit_equipment_expenses.php');
				} else if($_GET['subtab'] == 'balance') {
					include('edit_equipment_balance.php');
				} else if($_GET['subtab'] == 'equip_assign') {
					include('edit_equipment_assignment.php');
				} else {
                    if($overview > 0 && $_GET['subtab'] != 'edit' && $_GET['edit'] > 0) {
                        $_GET['view'] = 'readonly';
                        $_GET['subtab'] = 'overview';
                    }
                    include('edit_equipment.php');
				}
			} else if(isset($_GET['settings']) && $security['config'] > 0) {
				include('field_config.php');
			} else {
				include('equipment_dashboard.php');
			} ?>
		</div>
	</div>
</div>

<?php include_once('../footer.php'); ?>