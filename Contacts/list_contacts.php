<script>
var contact_type = '';
$(document).ready(function() {
	retrieveContacts();
	<?php if(empty($_GET['vpl']) && empty($_GET['orderform']) && empty($_GET['vpl_name']) && FOLDER_NAME != 'vendors') { ?>
		loadContacts();
	<?php } ?>

	$('.search_list').keyup(function() {
		if(current_contact_search_key != this.value.toLowerCase()) {
			$('#summary_tab li').removeClass('active blue');
			loadContacts();
		}
	});

	$('.contact_anchor').off('click').click(function() {
		current_contact_search_key = '';
		$('.search_list').val('');
		var parent = $(this).closest('.sidebar-higher-level.highest-level');
		$('.sidebar-higher-level.highest-level').not(parent).find('.active').removeClass('active blue');
		$('#summary_tab li').removeClass('active blue');
		$(this).find('li').toggleClass('active blue');
		$('.sidebar-higher-level.highest-level').each(function() {
			if($(this).find('.active').length > 0) {
				$(this).find('a.cursor-hand').first().addClass('active');
			}
		});
		$('.sidebar-higher-level:not(.highest-level)').each(function() {
			if($(this).find('.active:not(.cursor-hand)').length > 0) {
				$(this).find('a.cursor-hand').first().addClass('active');
			} else {
				$(this).find('a.cursor-hand').first().removeClass('active');
			}
		});
		loadContacts();
		return false;
	});
	$('#summary_tab').off('click').click(function() {
		$('.tile-sidebar').find('.active').removeClass('active blue');
		$(this).find('li').addClass('active blue');
		loadContacts();
		return false;
	});

	$('.panel-heading').off('click').click(function() { $('.contact_panel').html(''); loadPanel(this) });
	$(window).resize(function() {
		var available_height = window.innerHeight - $('footer:visible').outerHeight() - ($('.main-screen .main-screen').offset() == undefined ? 0 : $('.main-screen .main-screen').offset().top);
		if(available_height > 200) {
			$('.main-screen .main-screen').outerHeight(available_height).css({'overflow-y':'auto', 'max-width':'100%', 'margin-left':'0'});
			$('.tile-sidebar').outerHeight(available_height).css('overflow-y','auto');
		}
	}).resize();

});
var ajax_loads = [];
var contact_list = [];
var result_list = [];
var current_contact_search_key = '';
function retrieveContacts() {
	$.ajax({
		url: '../Contacts/contacts_load_list.php',
		data: { folder: '<?= FOLDER_NAME ?>' },
		method: 'POST',
		success: function(response) {
			contact_list = JSON.parse(response);
		}
	});
}
function loadContacts() {
	$('.vpl_content').hide();
	$('.contact_content').show();
	$('.non_contact_tab').removeClass('active blue');
	$('.standard-body').removeClass('standard-body').addClass('standard-dashboard-body');
	$('.standard-body-title').removeClass('standard-body-title').addClass('standard-dashboard-body-title');
	clearTimeout(continue_loading);
	ajax_loads.forEach(function(call) { call.abort(); });
	$('.contact_list').empty().html('<h4 class="col-sm-12">Enter a search term to display Contacts here<h4>');
	if(contact_list.length == 0 || contact_list == undefined) {
		setTimeout(function() { loadContacts(); }, 500);
	} else {
		var target = $('.contact_list');

		var category = [];
		$('.active[data-category]').each(function() {
			if($(this).data('category') != '' && $(this).data('category') != 'ALL') {
				category.push($(this).data('category'));
			}
		});
		var regions = [];
		$('.active[data-region]').each(function() {
			if($(this).data('region') != '') {
				regions.push($(this).data('region'));
			}
		});
		var locations = [];
		$('.active[data-location]').each(function() {
			if($(this).data('location') != '') {
				locations.push($(this).data('location'));
			}
		});
		var classifications = [];
		$('.active[data-classification]').each(function() {
			if($(this).data('classification') != '') {
				classifications.push($(this).data('classification'));
			}
		});
		var titles = [];
		$('.active[data-title]').each(function() {
			if($(this).data('title') != '') {
				titles.push($(this).data('title'));
			}
		});
		var status = [];
		$('.active[data-status]').each(function() {
			if($(this).data('status') >= 0) {
				status.push($(this).data('status'));
			}
		});
		var match_staffs = [];
		$('.active[data-match-staff]').each(function() {
			if($(this).data('match-staff') != '') {
				match_contacts.push($(this).data('match-staff'));
			}
		});

		target.html('');

		result_list = [];
		var filter_list = [];
		contact_list.forEach(function(contact) {
			if(
				(category.indexOf(contact.category) >= 0 || category.length == 0) &&
				(contact.region.some(function(element) { return this.indexOf(element) >= 0; }, regions) || regions.length == 0) &&
				(contact.location.some(function(element) { return this.indexOf(element) >= 0; }, locations) || locations.length == 0) &&
				(contact.classification.some(function(element) { return this.indexOf(element) >= 0; }, classifications) || classifications.length == 0) &&
				(titles.indexOf(contact.title) >= 0 || titles.length == 0) &&
				(status.indexOf(parseInt(contact.status)) >= 0 || status.length == 0) &&
				(contact.match_staffs.some(function(element) { return this.indexOf(parseInt(element)) >= 0; }, match_staffs) || match_staffs.length == 0)
			) {
				filter_list.push(contact);
			}
		});
		var key = $('.search_list:visible').val();
		if(key != undefined) {
			key = key.toLowerCase();
		}
		current_contact_search_key = key;
		filter_list.forEach(function(contact) {
			if(key == '' || key == undefined || contact.search_string.toLowerCase().search(key) >= 0) {
				result_list.push(contact);
			}
		});
		
		if($('#summary_tab li').hasClass('active')) {
			$('.title_label').text('Summary');
			$('.summary_list').show();
			$('.title_options').hide();
			$('.contact_list').hide();
		} else if(result_list.length > 0) {
			if($('.active[data-category]').length == 0) {
				$('.title_label').text('All');
			} else {
				$('.title_label').text($('.active[data-category]').first().text());
			}
			$('.summary_list').hide();
			$('.contact_list').show();
			$('.title_options').show();
			$('.title_options').find('[name="export_contacts"]').val($('.active[data-category]').first().text());
			$('.title_options').find('.new_contact_url').prop('href', '?edit=new&category='+$('.active[data-category]').first().text()).text('New '+$('.active[data-category]').first().text());
			showContacts(result_list, target);
		} else {
			$('.contact_list').empty().html('<h4 class="col-sm-12">No Contacts Found.<h4>');
		}
	}
}
var continue_loading = '';
function showContacts(result_list, target) {
	clearTimeout(continue_loading);
	if($('.contact_list').text() != 'No Contacts Found.') {
		if($('.dashboard-item').length == 0 || $('.dashboard-item').last().offset().top - $(window).scrollTop() < $(window).innerHeight() + 500) {
			var contact = result_list.shift();
			if(contact != undefined && contact.id > 0) {
				ajax_loads.push($.ajax({
					url: '../Contacts/contacts_load.php?contactid='+contact.id,
					data: { folder: '<?= FOLDER_NAME ?>' },
					method: 'POST',
					success: function(response) {
						target.append(response);
						continue_loading = showContacts(result_list, target);
						initTooltips();
						initIconColors();
					}
				}));
			}
		} else if(result_list.length > 0) {
			continue_loading = setTimeout(function() { showContacts(result_list, target) },1000);
		}
	}
}
function loadPanel(a) {
	var panel = $(a).closest('.panel').find('.panel-body');
	if($(panel).hasClass('contact_panel')) {
		if(contact_list.length == 0 || contact_list == undefined) {
			setTimeout(function() { loadPanel(a); }, 500);
		} else {
			var category = [$(panel).data('category')];
			var status = [$(panel).data('status')];
			result_list = [];

			contact_list.forEach(function(contact) {
				if(
					(category.indexOf(contact.category) >= 0 || category.length == 0) &&
					(status.indexOf(parseInt(contact.status)) >= 0 || status.length == 0)
				) {
					result_list.push(contact);
				}
			});

			$(panel).empty();
			if(result_list.length > 0) {
				showContacts(result_list, panel);
			} else {
				$(panel).html('<h4>No Contacts Found.</h4>');
			}
		}
	} else {
		$.ajax({
			url: '../Contacts/'+panel.data('file'),
			data: { folder: '<?= FOLDER_NAME ?>', type: contact_type },
			method: 'POST',
			response: 'html',
			success: function(response) {
				panel.html(response);
				$('.pagination_links a').click(pagination_load);
			}
		});
	}
}
function pagination_load() {
	var target = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: this.href,
		data: { folder: '<?= FOLDER_NAME ?>', type: contact_type },
		method: 'POST',
		response: 'html',
		success: function(response) {
			target.html(response);
			$('.pagination_links a').click(pagination_load);
		}
	});
	return false;
}
function deleteContact(link) {
	if(confirm("Are you sure you want to archive this contact?")) {
		var startBlock = $(link).closest('tr').prevAll('tr').filter(function() { return $(this).find('td[colspan=2]').length > 0; }).first();
		var end = false;
		startBlock.nextAll('tr').each(function() {
			if(!end) {
				if($(this).find('td[colspan=2]').length > 0) {
					end = true;
				}
				$(this).remove();
			}
		});
		$.ajax({
			url: '../Contacts/contacts_ajax.php?action=archive&contactid='+$(link).data('contactid'),
			method: 'POST'
		});
	}
}
function statusChange(link) {
	var change_status = $(link).data('status') == "0" ? 'Activate' : 'Deactivate';
	if(confirm("Are you sure you want to "+change_status+" this contact?")) {
		$(link).text($(link).data('status') == "0" ? 'Deactivate' : 'Activate').data('status',$(link).data('status') == "0" ? '1' : '0');
		$.ajax({
			url: '../Contacts/contacts_ajax.php?action=status_change&contactid='+$(link).data('contactid')+'&new_status='+$(link).data('status'),
			method: 'POST'
		});
	}
}
</script>
<?php $lists = array_filter(explode(',',get_config($dbc, FOLDER_NAME.'_tabs')));
foreach($lists as $i => $list_name) {
	if($list_name == 'Staff' || !check_subtab_persmission($dbc, $security_folder, ROLE, $list_name)) {
		unset($lists[$i]);
	}
}
$lists = array_values($lists);
if(mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category` NOT IN ('".implode("','",$lists)."','Staff')"))[0]) {
	$lists[] = 'Uncategorized';
}
$category = empty($_GET['list']) ? $lists[0] : filter_var($_GET['list'],FILTER_SANITIZE_STRING);
$status = (empty($_GET['status']) ? 'active' : $_GET['status']); ?>
<?php $list = ''; ?>
<?php if(!isset($_POST['search_contacts_submit'])): ?>
	<?php if(empty($_GET['list'])): ?>
		<?php $list = $lists[0]; ?>
	<?php else: ?>
		<?php $list = $_GET['list']; ?>
	<?php endif; ?>
<?php else: ?>
	<?php $category = ''; ?>
<?php endif; ?>

<div class="tile-container hide-on-mobile">
    <div class="tile-sidebar standard-collapsible hide-titles-mob double-gap-top" style="overflow-y:auto;">
        <ul>
            <li class="standard-sidebar-searchbox">
                <input name="search_list" type="text" value="" placeholder="Search Contacts" class="form-control search_list" />
            </li>

			<?php if(check_subtab_persmission($dbc, $security_folder, ROLE, 'summary')) { ?>
				<a id="summary_tab" href="?list=summary&status=summary">
					<li class="<?= ((empty($_GET['list']) && empty($_GET['vpl']) && empty($_GET['orderform']) && empty($_GET['vpl_name'])) || ($_GET['list']=='summary' && $_GET['status']=='summary')) ? 'active blue' : '' ?>"><b>Summary</b></li>
				</a>
			<?php } ?>

            <?php foreach($lists as $list_name) {
                $heading = $list_name;
                if($list_name == 'Vendors') {
                    $heading = VENDOR_TILE;
                }

				$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts_dashboard FROM field_config_contacts WHERE tile_name = '".$folder_name."' AND tab='$list_name' AND accordion IS NULL UNION SELECT contacts_dashboard FROM `field_config_contacts` WHERE tile_name='".$folder_name."'"));
				$field_display = explode(",",$get_field_config['contacts_dashboard']);
				//$contact_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) count FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$list_name' AND `status`=1")); ?>
				<!--
				<a href="?list=<?= $list_name ?>&status=<?= $status ?>"><li class="<?= $category == $list_name ? 'active blue' : '' ?>"><b><?= $status == 'inactive' ? 'Inactive ' : ($status == 'archive' ? 'Archived ' : '') ?><?= $list_name ?></b><span class="pull-right"><?= $contact_count['count']; ?></span></li></a>
				-->
				<li class="sidebar-higher-level highest-level">
					<a data-category="<?= $list_name ?>" class="cursor-hand <?= $_GET['list']==$list_name ? 'active blue' : 'collapsed' ?>" onclick="$(this).toggleClass('collapsed'); $(this).closest('li').find('ul').first().toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= $heading ?> <span class="arrow"></span></a>
					<ul style="<?= $_GET['list']==$list_name ? '' : 'display:none;' ?>">
			            <?php $con_regions = array_filter(array_unique(explode(',', get_config($dbc, '%_region', true))));
			            if(count($con_regions) > 0) { ?>
			                <li class="sidebar-higher-level">
			                    <a class="cursor-hand collapsed" onclick="$(this).toggleClass('collapsed'); $(this).closest('li').find('ul').toggle(); $(this).find('img').toggleClass('counterclockwise');">Regions <span class="arrow"></span></a>
			                    <ul style="display: none;">
			                        <?php foreach($con_regions as $con_region):
			                            $region_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) count FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$heading' AND CONCAT(',',`region`,',') LIKE '%,$con_region,%'")); ?>
			                            <a class="contact_anchor" href="?">
			                                <li data-region="<?= $con_region ?>"><b><?php echo $con_region; ?></b><span class="pull-right"><?= $region_count['count']; ?></span></li>
			                            </a>
			                        <?php endforeach; ?>
			                    </ul>
			                </li>
			            <?php } ?>
			            <?php $con_locations = array_filter(explode(",", mysqli_fetch_assoc(mysqli_query($dbc,"SELECT con_locations FROM field_config_contacts where con_locations is not null AND `tile_name`='".FOLDER_NAME."'"))['con_locations']));
			            if(count($con_locations) > 0) { ?>
			                <li class="sidebar-higher-level">
			                    <a class="cursor-hand collapsed" onclick="$(this).toggleClass('collapsed'); $(this).closest('li').find('ul').toggle(); $(this).find('img').toggleClass('counterclockwise');">Locations <span class="arrow"></span></a>
			                    <ul style="display: none;">
			                        <?php foreach($con_locations as $location):
			                            $location_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) count FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$heading' AND CONCAT(',',`con_locations`,',') LIKE '%,$location,%'")); ?>
			                            <a class="contact_anchor" href="?">
			                                <li data-location="<?= $location ?>"><b><?php echo $location; ?></b><span class="pull-right"><?= $location_count['count']; ?></span></li>
			                            </a>
			                        <?php endforeach; ?>
			                    </ul>
			                </li>
			            <?php } ?>
			            <?php $con_classifications = array_filter(explode(",", get_config($dbc, FOLDER_NAME.'_classification')));
			            if(count($con_classifications) > 0) { ?>
			                <li class="sidebar-higher-level">
			                    <a class="cursor-hand collapsed" onclick="$(this).toggleClass('collapsed'); $(this).closest('li').find('ul').toggle(); $(this).find('img').toggleClass('counterclockwise');">Classifications <span class="arrow"></span></a>
			                    <ul style="display: none;">
			                        <?php foreach($con_classifications as $con_classification):
			                            $classification_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) count FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$heading' AND CONCAT(',',`classification`,',') LIKE '%,$con_classification,%'")); ?>
			                            <a class="contact_anchor" href="?">
			                                <li data-classification="<?= $con_classification ?>"><b><?php echo $con_classification; ?></b><span class="pull-right"><?= $classification_count['count']; ?></span></li>
			                            </a>
			                        <?php endforeach; ?>
			                    </ul>
			                </li>
			            <?php } ?>
			            <?php $con_titles = array_filter(explode(",", mysqli_fetch_assoc(mysqli_query($dbc,"SELECT con_title FROM field_config_contacts where con_title is not null AND `tile_name`='".FOLDER_NAME."'"))['con_title']));
			            if(count($con_titles) > 0) { ?>
			                <li class="sidebar-higher-level">
			                    <a class="cursor-hand collapsed" onclick="$(this).toggleClass('collapsed'); $(this).closest('li').find('ul').toggle(); $(this).find('img').toggleClass('counterclockwise');">Titles <span class="arrow"></span></a>
			                    <ul style="display: none;">
			                        <?php foreach($con_titles as $con_title):
			                            $title_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) count FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$heading' AND `title`='$con_title'")); ?>
			                            <a class="contact_anchor" href="?">
			                                <li data-title="<?= $con_title ?>"><b><?php echo $con_title; ?></b><span class="pull-right"><?= $title_count['count']; ?></span></li>
			                            </a>
			                        <?php endforeach; ?>
			                    </ul>
			                </li>
			            <?php } ?>
						<?php if(in_array("Sort Match Staff",$field_display)) { ?>
							<li class="sidebar-higher-level">
								<a class="cursor-hand collapsed" onclick="$(this).toggleClass('collapsed'); $(this).closest('li').find('ul').toggle(); $(this).find('img').toggleClass('counterclockwise');">Matched Staff <span class="arrow"></span></a>
								<ul style="display: none;">
									<?php $match_contacts = [];
									$sorted_match_contacts = [];
									$match_contacts_query = mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE `deleted` = 0 AND `support_contact_category` = '$list_name'");
									while($match_contacts_result = mysqli_fetch_assoc($match_contacts_query)) {
										foreach(explode(',', $match_contacts_result['staff_contact']) as $staff_contact) {
											foreach(explode(',', $match_contacts_result['support_contact']) as $support_contact) {
												if(!in_array($staff_contact,
													$sorted_match_contacts)) {
													$sorted_match_contacts[] = $staff_contact;
												}
												if(!in_array($support_contact, $match_contacts[$staff_contact])) {
													$match_contacts[$staff_contact][] = $support_contact;
												}
											}
										}
									}
									$sorted_match_contacts = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` IN (".implode(',', $sorted_match_contacts).")"));
									foreach($sorted_match_contacts as $matched_staff) { ?>
										<a class="contact_anchor" href="?list=<?= $list_name; ?>&match_staff=<?= $matched_staff['contactid'] ?>">
											<li data-match-staff="<?= $matched_staff['contactid'] ?>"><b><?= $matched_staff['full_name'] ?></b><span class="pull-right"><?= count($match_contacts[$matched_staff['contactid']]); ?></span></li>
										</a>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>
						<?php $active_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) `count` FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$list_name' AND `status`=1")); ?>
						<a class="contact_anchor" href="?list=<?= $list_name; ?>&status=active">
							<li data-status="1" class="<?= ($_GET['list']==$list_name && $_GET['status']=='active') ? 'active blue' : '' ?>"><b>Active</b><span class="pull-right"><?= $active_count['count']; ?></span></li>
						</a>
						<?php $inactive_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) `count` FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$list_name' AND `status`=0")); ?>
						<a class="contact_anchor" href="?list=<?= $list_name; ?>&status=inactive">
							<li data-status="0" class="<?= ($_GET['list']==$list_name && $_GET['status']=='inactive') ? 'active blue' : '' ?>"><b>Inactive</b><span class="pull-right"><?= $inactive_count['count']; ?></span></li>
						</a>
					</ul>
				</li>
            <?php } ?>

            <?php if(tile_visible($dbc, 'vpl') && FOLDER_NAME == 'vendors') { ?>
                <li class="sidebar-higher-level highest-level">
                    <a class="non_contact_tab cursor-hand <?= !empty($_GET['vpl']) ? 'active blue' : 'collapsed' ?>" onclick="$(this).toggleClass('collapsed'); $(this).closest('li').find('ul').first().toggle(); $(this).find('img').toggleClass('counterclockwise');">Vendor Price List <span class="arrow"></span></a>
                    <ul style="<?= !empty($_GET['vpl']) ? '' : 'display:none;' ?>">
                        <a href="?vpl=1">
                            <li class="non_contact_tab <?= !empty($_GET['vpl']) && empty($_GET['vendorid']) ? 'active blue' : '' ?>">All Vendor Price List Items</li>
                        </a>
                        <?php $vendor_lists = sort_contacts_query(mysqli_query($dbc, "SELECT DISTINCT `vendor_price_list`.`vendorid`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`name` FROM `vendor_price_list` LEFT JOIN `contacts` ON `vendor_price_list`.`vendorid` = `contacts`.`contactid` WHERE `vendor_price_list`.`vendorid` > 0 AND `vendor_price_list`.`deleted` = 0 AND `contacts`.`deleted` = 0"));
                        foreach($vendor_lists as $row) { ?>
                            <li class="sidebar-higher-level">
                                <a class="non_contact_tab cursor-hand <?= !empty($_GET['vpl']) && $_GET['vendorid'] == $row['vendorid'] ? 'active blue' : 'collapsed' ?>" onclick="$(this).toggleClass('collapsed'); $(this).closest('li').find('ul').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= $row['full_name'] ?> <span class="arrow"></span></a>
                                <ul style="<?= !empty($_GET['vpl']) && $_GET['vendorid'] == $row['vendorid'] ? '' : 'display:none;' ?>">
                                    <a href="?vpl=1&vendorid=<?= $row['vendorid'] ?>">
                                        <li class="non_contact_tab <?= !empty($_GET['vpl']) && $_GET['vendorid'] == $row['vendorid'] && empty($_GET['vpl_name']) ? 'active blue' : '' ?>">All Items</li>
                                    </a>
                                    <?php $vpl_names = mysqli_query($dbc, "SELECT DISTINCT `vpl_name` FROM `vendor_price_list` WHERE `deleted` = 0 AND `vendorid` = '{$row['vendorid']}' AND IFNULL(`vpl_name`,'') != '' ORDER BY `vpl_name`");
                                    while($vpl_name = mysqli_fetch_assoc($vpl_names)) { ?>
                                        <a href="?vpl=1&vendorid=<?= $row['vendorid'] ?>&vpl_name=<?= $vpl_name['vpl_name'] ?>">
                                            <li class="<?= !empty($_GET['vpl']) && $_GET['vendorid'] == $row['vendorid'] && $_GET['vpl_name'] == $vpl_name['vpl_name'] ? 'active blue' : '' ?>"><?= $vpl_name['vpl_name'] ?></li>
                                        </a>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>

            <?php if(tile_visible($dbc, 'vpl') && FOLDER_NAME == 'vendors' && get_config($dbc, 'show_orderforms_vpl') == 1) { ?>
                <li class="sidebar-higher-level highest-level">
                    <a class="non_contact_tab cursor-hand <?= !empty($_GET['orderform']) ? 'active blue' : 'collapsed' ?>" onclick="$(this).toggleClass('collapsed'); $(this).closest('li').find('ul').first().toggle(); $(this).find('img').toggleClass('counterclockwise');">Order Forms <span class="arrow"></span></a>
                    <ul style="<?= !empty($_GET['orderform']) ? '' : 'display:none;' ?>">
                        <?php $vendor_lists = sort_contacts_query(mysqli_query($dbc, "SELECT DISTINCT `vendor_price_list`.`vendorid`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`name` FROM `vendor_price_list` LEFT JOIN `contacts` ON `vendor_price_list`.`vendorid` = `contacts`.`contactid` WHERE `vendor_price_list`.`vendorid` > 0 AND `vendor_price_list`.`deleted` = 0 AND `contacts`.`deleted` = 0"));
                        foreach($vendor_lists as $row) { ?>
                            <li class="sidebar-higher-level">
                                <a class="non_contact_tab cursor-hand <?= !empty($_GET['orderform']) && $_GET['vendorid'] == $row['vendorid'] ? 'active blue' : 'collapsed' ?>" onclick="$(this).toggleClass('collapsed'); $(this).closest('li').find('ul').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= $row['full_name'] ?> <span class="arrow"></span></a>
                                <ul style="<?= !empty($_GET['orderform']) && $_GET['vendorid'] == $row['vendorid'] ? '' : 'display:none;' ?>">
                                    <?php $vpl_names = mysqli_query($dbc, "SELECT DISTINCT `vpl_name` FROM `vendor_price_list` WHERE `deleted` = 0 AND `vendorid` = '{$row['vendorid']}' AND IFNULL(`vpl_name`,'') != '' ORDER BY `vpl_name`");
                                    while($vpl_name = mysqli_fetch_assoc($vpl_names)) { ?>
                                        <a href="?orderform=1&vendorid=<?= $row['vendorid'] ?>&vpl_name=<?= $vpl_name['vpl_name'] ?>">
                                            <li class="non_contact_tab <?= !empty($_GET['orderform']) && $_GET['vendorid'] == $row['vendorid'] && $_GET['vpl_name'] == $vpl_name['vpl_name'] ? 'active blue' : '' ?>"><?= $vpl_name['vpl_name'] ?></li>
                                        </a>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </div><!-- .tile-sidebar -->

    <div class='scale-to-fill has-main-screen hide-titles-mob'>
        <div class='main-screen <?= (!empty($_GET['vpl']) || !empty($_GET['orderform']) || !empty($_GET['vpl_name'])) && FOLDER_NAME == 'vendors' ? 'standard-body form-horizontal' : '' ?>'>
            <?php $hide_contacts = false;
                if(!empty($_GET['vpl']) && FOLDER_NAME == 'vendors') {
                	$hide_contacts = true;
                	echo '<div class="vpl_content">';
                    if(isset($_GET['inventoryid'])) {
                        include('../Vendor Price List/edit_vpl.php');
                    } else if($_GET['impexp'] == 1) {
                        include('../Vendor Price List/import_export_vpl.php');
                    } else {
                        include('../Vendor Price List/vpl_dashboard.php');
                    }
                    echo '</div>';
                } else if(!empty($_GET['orderform']) && FOLDER_NAME == 'vendors') {
                	$hide_contacts = true;
                	echo '<div class="vpl_content">';
                    include('../Vendor Price List/order_form.php');
                    echo '</div>';
                }
                $category = $list;
                include('list_common.php');
            ?>
        </div>
    </div>

    <div class="clearfix"></div>
</div><!-- .tile-container -->

<?php if ( isset($_GET['category']) ) {
    $category = filter_var($_GET['category'], FILTER_SANITIZE_STRING);
}
if(!empty($_GET['search_contacts']) || !empty($_POST['search_'.$category])) { ?>
    <div class="show-on-mob">
	<?php include('list_common.php'); ?>
    </div>
<?php } else { ?>
	<div id="type_accordions" class="gap-top gap-left show-on-mob panel-group block-panels" style="width:95%;"><?php
		$counter = 1; ?>
		<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#type_accordions" href="#collapse_summary">
                        Summary<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>
            <div id="collapse_summary" class="panel-collapse collapse">
                <div class="panel-body" data-file="list_common.php?list=summary&status=summary">
                    Loading...
                </div>
            </div>
        </div><?php
        foreach($lists as $list_name) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#type_accordions" href="#collapse_<?= $counter; ?>">
                            <?= $list_name; ?><span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_<?= $counter; ?>" class="panel-collapse collapse">
                    <div class="panel-body contact_panel" data-category="<?= $list_name ?>" data-status="1">
                        Loading...
                    </div>
                </div>
            </div><?php
            $counter++;
        } ?>
	</div>
<?php } ?> 