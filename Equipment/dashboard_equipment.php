<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment'); ?>
<script>
$(document).on('change', 'select[name="search_category"]', function() { changeCategory(this); });
function changeCategory(sel) {
	var value = sel.value;
	<?php if($_GET['mobile_view'] == 1) { ?>
		var panel = $(sel).closest('.panel').find('.panel-body');
		panel.html('Loading...');
		$.ajax({
			url: 'dashboard_equipment.php'+value+'&mobile_view=1',
			method: 'GET',
			response: 'html',
			success: function(response) {
				panel.html(response);
				$('.pagination_links a').click(pagination_load);
			}
		});
	<?php } else { ?>
		location = value;
	<?php } ?>
}
function send_csv() {
	$('[name=upload]').change(function() {
		$('form').submit();
	});
	$('[name=upload]').click();
}
</script>

<?php $status = (empty($_GET['status']) ? 'Active' : $_GET['status']);
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));

include_once('../Equipment/region_location_access.php'); ?>

<?php
$category = $_GET['category'];
$each_tab = explode(',', get_config($dbc, 'equipment_tabs')); ?>

<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
    <div class="gap-left tab-container col-sm-10">
        <div class="row">
			<label class="control-label col-sm-2">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Filter equipment by Tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                Tab:
            </label>
			<div class="col-sm-4">
				<select name="search_category" class="chosen-select-deselect form-control mobile-100-pull-right category_actual">
					<option value="?category=Top">Last 25 Added</option>
					<?php
						foreach ($each_tab as $cat_tab) {
							echo "<option ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'selected' : '')." value='?status=".$_GET['status']."&category=".$cat_tab."'>".$cat_tab."</option>";
						}
					?>
				</select>
			</div>
        </div>
	</div>
	<div class="clearfix"></div>
<?php } ?>

<div id="no-more-tables"> <?php

// Display Pager

$equipment = '';

if (isset($_GET['search_equipment_submit'])) {
    $equipment = $_GET['search_equipment'];

    if (!empty($_GET['search_equipment'])) {
        $equipment = $_GET['search_equipment'];
    }
    if (!empty($_GET['search_category'])) {
        $equipment = $_GET['search_category'];
    }
}

if (isset($_GET['display_all_equipment'])) {
    $equipment = '';
}
include('dashboard_equipment_list.php');
?>

</div>
<script type="text/javascript">
function export_csv()
{
	//var hreflocation = window.location.href;
	window.location.href += "&export=yes";
}
</script>