<?php $contact_regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
$region_colours = explode(',',get_config($dbc, '%_region_colour', true));
$contact_security = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_security` WHERE `contactid`='{$_SESSION['contactid']}'"));
$allowed_regions = array_filter(explode('#*#',$contact_security['region_access']));
if(count($allowed_regions) == 0) {
    $allowed_regions = $contact_regions;
}
$contact_locations = array_filter(explode(',', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `con_locations` FROM `field_config_contacts` WHERE `con_locations` IS NOT NULL"))['con_locations']));
$allowed_locations = array_filter(explode('#*#',$contact_security['location_access']));
if(count($allowed_locations) == 0) {
    $allowed_locations = $contact_locations;
}
$class_regions = explode(',',get_config($dbc, '%_class_regions', true, ','));
$contact_classifications = [];
$classification_regions = [];
$classification_logos = [];
$allowed_classifications = array_filter(explode('#*#',$contact_security['classification_access']));
foreach(explode(',',get_config($dbc, '%_classification', true, ',')) as $i => $contact_classification) {
    if(in_array($contact_classification, $allowed_classifications) || empty($allowed_classifications)) {
    	$row = array_search($contact_classification, $contact_classifications);
    	if($class_regions[$i] == 'ALL') {
    		$class_regions[$i] = '';
    	}
    	if($row !== FALSE && $class_regions[$i] != '') {
    		$classification_regions[$row][] = $class_regions[$i];
            $classification_logos[$row] = $class_logos[$i];
    	} else {
    		$contact_classifications[] = $contact_classification;
    		$classification_regions[] = array_filter([$class_regions[$i]]);
            $classification_logos[] = $class_logos[$i];
    	}
    }
}
$allowed_classifications = $contact_classifications; ?>
<script src="../Calendar/map_sorting.js"></script>
<script>
$(document).ready(function() {
	$('[name=region]').change(filterRegions);
	$('[name=location]').change(filterLocation);
	$('[name=classification]').change(filterClass);
	$('[name=date]').change(get_ticket_list);
	<?php if(!empty($_GET['date'])) { ?>
		get_ticket_list();
	<?php } ?>
});
var opt_region = '<?= $_GET['region'] ?>';
var opt_location = '<?= $_GET['location'] ?>';
var opt_classification = '<?= $_GET['classification'] ?>';
var opt_date = '<?= $_GET['date'] ?>';
var lock_timer = null;
var ticket_list = [];
var zoom = <?= $_GET['zoom'] > 0 ? $_GET['zoom'] : 10 ?>;
function filterRegions() {
	opt_region = $('[name=region]').val();
	$('[name=classification] option[data-region]').each(function() {
		if($(this).data('region').length > 0 && $(this).data('region').indexOf(opt_region) < 0) {
			$(this).hide();
		}
	});
	$('[name=classification]').trigger('change.select2');
	get_ticket_list();
}
function filterLocation() {
	opt_location = $('[name=location]').val();
	get_ticket_list();
}
function filterClass() {
	opt_classification = $('[name=classification]').val();
	get_ticket_list();
}
function get_ticket_list() {
	$('.draw_sort').empty();
	var equip_scroll = $('.equip_list').scrollTop();
	$('.equip_list').html('<h4>Loading Equipment...</h4>').load('assign_equipment_list.php?sorticons=true&date='+encodeURI($('[name=date]').val())+'&region='+encodeURI(opt_region)+'&location='+encodeURI(opt_location)+'&classification='+encodeURI(opt_classification), function() { setTicketSave(); $('.equip_list').scrollTop(equip_scroll); });
    get_map_view();
	$('.ticket_list').html('<h4>Loading <?= TICKET_TILE ?>...</h4>').load('assign_ticket_list.php?date='+encodeURI($('[name=date]').val())+'&region='+encodeURI(opt_region)+'&location='+encodeURI(opt_location)+'&classification='+encodeURI(opt_classification), setTicketSave);
	lockTickets();
	initOptions();
}
function get_map_view() {
    if(zoom > 16) {
        zoom = 16;
    } else if(zoom < 6) {
        zoom = 6;
    }
	$('.map_view').html('<h4>Loading Map...</h4>').load('assign_map_view.php?zoom='+zoom+'&x='+$('.map_view').width()+'&y='+$('.map_view').height()+'&date='+encodeURI($('[name=date]').val())+'&region='+encodeURI(opt_region)+'&location='+encodeURI(opt_location)+'&classification='+encodeURI(opt_classification), setTicketSave);
}
function lockTickets() {
	clearTimeout(lock_timer);
	lock_timer = setTimeout(lockTickets,30000);
	$.post('optimize_ajax.php?action=lock', {
		region: opt_region,
		location: opt_location,
		classification: opt_classification
	});
}
function setTicketSave() {
	$('.ticket_list select,.ticket_list input').change(function() {
		console.log($(this).data('id'));
		console.log($(this).data('table'));
		console.log(this.name);
		console.log(this.value);
	});
	initInputs();
    initTooltips();
}
function initOptions() {
    $('.ticket[data-table][data-id]').off('mouseenter');
    $('.ticket[data-table][data-id]').mouseenter(function() {
        $('.ticket[data-table='+$(this).data('table')+'][data-id='+$(this).data('id')+']').addClass('active').css('z-index',1);
        $('.map_view .ticket[data-table='+$(this).data('table')+'][data-id='+$(this).data('id')+'] img').css('height','60px').css('width','60px').css('margin-top','-40px').css('margin-left','-20px');
        if($(this).parents('.map_view').length > 0) {
            $('.ticket_list').scrollTop($('.ticket_list').scrollTop() + $('.ticket_list .ticket[data-table='+$(this).data('table')+'][data-id='+$(this).data('id')+']').closest('.block-item').offset().top - $('.ticket_list').offset().top);
        }
    });
    $('.ticket[data-table][data-id]').off('mouseleave');
    $('.ticket[data-table][data-id]').mouseleave(function() {
        $('.ticket[data-table='+$(this).data('table')+'][data-id='+$(this).data('id')+']').removeClass('active').css('z-index',0);
        $('.map_view .ticket[data-table='+$(this).data('table')+'][data-id='+$(this).data('id')+'] img').css('height','20px').css('width','20px').css('margin-top','0').css('margin-left','0');
    });
    try {
		$('.assign_list_box').sortable('destroy');
	} catch(e) { }
	$( ".assign_list_box" ).sortable({
		beforeStop: function(e, ticket) {
			$('.draw_sort').empty();
			var block = $('.block-item.equipment.active').first();
			if(block.length > 0) {
				$.ajax({
					async: false,
					url: 'optimize_ajax.php?action=assign_ticket',
					method: 'POST',
					data: {
						equipment: block.data('id'),
						table: ticket.item.data('table'),
						id_field: ticket.item.data('id-field'),
						id: ticket.item.data('id'),
						date: $('[name=date]').val()
					},
					success: function(response) {
						get_ticket_list();
					}
				});
			}
		},
		handle: ".drag-handle",
		items: "span.ticket",
		sort: function(e) {
			block = $(document.elementsFromPoint(e.clientX, e.clientY)).filter('.block-item.equipment').not('.ui-sortable-helper').first();
			$('.block-item.equipment.active').removeClass('active');
			block.addClass('active');
		}
	});
}
function initDraw() {
	try {
		$('.draw_sort').sortable('destroy');
	} catch(e) { }
	$( ".draw_sort" ).sortable({
		beforeStop: function(e) {
            $('.draw_sort').sortable('destroy');
			$('.draw_sort').empty();
			var block = $('.block-item.equipment.active').first();
			var delay_load = '';
			if(block.length > 0) {
				ticket_list.forEach(function(ticket) {
					$.ajax({
						async: false,
						url: 'optimize_ajax.php?action=assign_ticket',
						method: 'POST',
						data: {
							equipment: block.data('id'),
							table: $(ticket).closest('span').data('table'),
							id_field: $(ticket).closest('span').data('id-field'),
							id: $(ticket).closest('span').data('id'),
							date: $('[name=date]').val()
						},
						success: function(response) {
							clearTimeout(delay_load);
							delay_load = setTimeout(get_ticket_list(),1000);
						}
					});
				});
				ticket_list = [];
			}
		},
		sort: function(e) {
			block = $(document.elementsFromPoint(e.clientX, e.clientY)).filter('.block-item.equipment').not('.ui-sortable-helper').first();
			$('.block-item.equipment.active').removeClass('active');
			block.addClass('active');
		},
		items: "img.drag_handle"
	});
}
</script>
<div class="draw_sort" style="position:absolute;top:0;left:0;z-index:1;overflow:visible;height:0px;width:0px;"></div>
<div class="main-screen standard-body override-main-screen form-horizontal">
	<div class="standard-body-title">
		<h3>Assign <?= TICKET_TILE ?></h3>
	</div>
	<div class="standard-body-content pad-top">
		<div class="col-sm-12">
			<?php $columns = 1 + (count($allowed_regions) > 0 ? 1 : 0) + (count($allowed_locations) > 0 ? 1 : 0) + (count($contact_classifications) > 0 ? 1 : 0); ?>
			<?php if(count($allowed_regions) > 0) { ?>
				<div class="form-group col-sm-<?= 12 / $columns ?> col-xs-12">
					<label class="col-sm-4">Region:</label>
					<div class="col-sm-8">
						<select class="chosen-select-deselect" name="region" data-placeholder="Select Region"><option />
							<?php foreach($allowed_regions as $region_name) { ?>
								<option <?= $_GET['region'] == $region_name ? 'selected' : '' ?> value="<?= $region_name ?>"><?= $region_name ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<?php if(count($allowed_locations) > 0) { ?>
				<div class="form-group col-sm-<?= 12 / $columns ?> col-xs-12">
					<label class="col-sm-4">Location:</label>
					<div class="col-sm-8">
						<select class="chosen-select-deselect" name="location" data-placeholder="Select Location"><option />
							<?php foreach($allowed_locations as $location) { ?>
								<option <?= $_GET['location'] == $location ? 'selected' : '' ?> value="<?= $location ?>"><?= $location ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<?php if(count($contact_classifications) > 0) { ?>
				<div class="form-group col-sm-<?= 12 / $columns ?> col-xs-12">
					<label class="col-sm-4">Classification:</label>
					<div class="col-sm-8">
						<select class="chosen-select-deselect" name="classification" data-placeholder="Select Classification"><option />
							<?php foreach($contact_classifications as $i => $class) { ?>
								<option <?= $_GET['classification'] == $class ? 'selected' : '' ?> data-region='<?= json_encode($classification_regions[$i]) ?>' value="<?= $class ?>"><?= $class ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<div class="form-group col-sm-<?= 12 / $columns ?> col-xs-12">
				<label class="col-sm-4">Date:</label>
				<div class="col-sm-8">
					<input type="text" name="date" class="form-control datepicker" placeholder="<?= TICKET_NOUN ?> Date" value="<?= $_GET['date'] ?>">
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="assign_list_box" style="height: 20em;position:relative;overflow-x:hidden;overflow-y:hidden;">
			<div class="equip_list" style="display:inline-block; height:100%; width:20%; float:left; overflow-y:auto;"></div>
			<div class="map_view" style="display:inline-block; height:100%; width:60%; overflow:hidden;"></div>
			<div class="ticket_list" style="display:inline-block; height:100%; width:20%; overflow-y:auto; float:right;"></div>
		</div>
	</div>
</div>