<?php include('../Rate Card/line_types.php'); ?>
<?php $estimateid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));
$config = explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM `field_config_estimate`"))[0]);

$_GET['rate'] = $current_rate;
$rates = [];
$query = mysqli_query($dbc, "SELECT `rate_card` FROM `estimate_scope` WHERE `estimateid`='$estimateid' GROUP BY `rate_card`");
if(mysqli_num_rows($query) > 0) {
	while($row = mysqli_fetch_array($query)) {
		$rates[bin2hex($row[0])] = explode(':',$row[0]);
	}
} else {
	$rates[''] = '';
}
$current_rate = (!empty($_GET['rate']) ? $_GET['rate'] : key($rates));
$_GET['rate'] = $current_rate;
//$query = mysqli_query($dbc, "SELECT `heading`, `scope_name` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `rate_card`='".implode(':',$rates[$current_rate])."' AND `src_table` != '' AND (`src_id` > 0 OR `description` != '') AND `deleted`=0 GROUP BY `heading` ORDER BY MIN(`sort_order`)");
$us_exchange = json_decode(file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'), TRUE);

$scope_list = [];
$query = mysqli_query($dbc, "SELECT `scope_name` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY IFNULL(`scope_name`,'') ORDER BY MIN(`sort_order`)");
$scope_name = '';
if(mysqli_num_rows($query) > 0) {
	while($row = mysqli_fetch_array($query)) {
		$scope_list[config_safe_str($row[0])] = $row[0];
	}
} else {
	$scope_list['scope_1'] = 'Scope 1';
}

$headings = [];
$heading_query = $dbc->query("SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 AND `scope_name`='".$scope_list[$_GET['status']]."' GROUP BY `heading` ORDER BY MIN(`sort_order`)");
while($heading_name = $heading_query->fetch_array()[0]) {
    $headings[config_safe_str($heading_name)] = $heading_name;
} ?>
<script>
profile_tab = [];
var lock_timeout;
var tile_options = [];
$(document).ready(function() {
	$('.panel-heading').click(loadPanel);
	$('[data-table]').change(saveField);
	$('.main-screen .main-screen').scroll(scrollScreen);
	<?php if($_GET['status'] != '') { ?>
		setTimeout(function() {
			$('.form-group:has(textarea)').css('height','18em');
			// $('.main-screen .main-screen').scrollTop($('[data-tab-name=<?= $_GET['status'] ?>]').offset().top - $('.main-screen .main-screen').offset().top);
			scrollScreen();
		}, 10);
	<?php } ?>
	$('#no-more-tables').sortable({
		connectWith: '.sort_table',
		handle: '.heading-handle',
		items: 'table',
		update: save_sort
	});
	$('.sort_table').sortable({
		connectWith: '.sort_table',
		handle: '.line-handle',
		items: 'tr',
		update: save_sort
	});
	$('[name=src_id]').each(function() { fill_selects($(this).closest('tr')); });
});
function save_sort() {
	set_headings();
	var i = 0;
	$('[name=sort_order]').each(function() {
		$(this).val(i++).change();
	});
}
function set_headings() {
	$('[name=heading][data-init]').each(function() {
		$(this).closest('table').find('[name=heading][data-table]').val(this.value).change();
		var heading_name = this.value;
		$(this).closest('table').find('a[href*=scope][href*=heading]').each(function() {
			this.href = this.href.replace(/&heading=[a-zA-Z0-9_]*/,'&heading='+heading_name.replace(/[^a-zA-Z0-9]/,'_'));
		});
	});
}
function set_scopes() {
	$('[name=scope_name][data-init]').each(function() {
		$(this).closest('.sort_table').find('[name=scope_name][data-table]').val(this.value).change();
		var scope_name = this.value;
		$(this).closest('.sort_table').find('a[href*=scope][href*=heading]').each(function() {
			this.href = this.href.replace(/&scope=[a-zA-Z0-9_]*/,'&scope='+scope_name.replace(/[^a-zA-Z0-9]/,'_'));
		});
	});
}
function scrollScreen() {
	var current_tab = [];
	$('[data-tab-name]:visible').each(function() {
		if(this.getBoundingClientRect().top < $('.main-screen .main-screen').offset().top + $('.main-screen .main-screen').height() &&
			this.getBoundingClientRect().bottom > $('.main-screen .main-screen').offset().top) {
			current_tab.push($(this).data('tab-name'));
		}
	});
	if(JSON.stringify(current_tab) != JSON.stringify(profile_tab)) {
		profile_tab.forEach(function(tab_name) {
			if(!current_tab.includes(tab_name)) {
				releaseLock([tab_name]);
			}
		});
		profile_tab = current_tab;
		getLock(profile_tab);
	}
}
function getLock(tab_name) {
	clearTimeout(lock_timeout);
	$.ajax({
		method: 'POST',
		url: 'estimates_ajax.php?action=table_locks',
		data: {
			contactid: '<?= $_GET['edit'] ?>',
			section: tab_name,
			session_id: '<?= $_SESSION['contactid'] ?>'
		},
		reponse: 'html',
		success: function(response) {
			var locked_tabs = [];
			if(response != '#*#') {
				locked_tabs = split(',',split('#*#',response)[0]);
				console.log(split('#*#',response)[1]);
			}
			$('div[data-tab-name]').data('locked','');
			tab_name.forEach(function(tab) {
				if(!locked_tabs.includes(tab)) {
					console.log(tab+' has been locked for editing.');
					$('div[data-tab-name="'+tab+'"]').data('locked','held');
				}
			});
			lock_timeout = setTimeout(function() { releaseLock(tab_name); }, 600000);
			lockTabs();
		}
	});
}
function releaseLock(tab_name) {
	tab_name.forEach(function(tab) {
		console.log(tab+' has been released.');
		$('div[data-tab-name="'+tab+'"]').data('locked','');
		lockTabs();
		$.ajax({
			method: 'POST',
			url: 'estimates_ajax.php?action=unlock_table',
			data: {
				contactid: '<?= $_GET['edit'] ?>',
				section: tab,
				session_id: '<?= $_SESSION['contactid'] ?>'
			}
		});
	});
}
function lockTabs() {
	$('.active.blue').removeClass('active').removeClass('blue');
	$('div[data-tab-name]').each(function() {
		if($(this).data('locked') != 'held') {
			$(this).find('input,select,a').off('click').click(function() { this.blur(); return false; }).off('keyup').keyup(function() { this.blur(); return false; }).off('keypress').keypress(function() { this.blur(); return false; });
		} else {
			$(this).find('input,select,a').off('click').off('keyup').off('keypress');
			$('a[href="#'+$(this).data('tab-name')+'"] li').addClass('active blue');
		}
	});
	if($('#view_profile').is(':visible')) {
		$('[href=#view_profile] li').addClass('active blue');
	}
}
function loadPanel() {
	$('.panel-body').html('Loading...');
	body = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: $(body).data('file'),
		method: 'POST',
		response: 'html',
		success: function(response) {
			$(body).html(response);
			$('input,select').off('change', saveField).change(saveField);
		}
	});
}
function add_heading() {
	$.ajax({
		url: 'estimates_ajax.php?action=estimate_add_heading',
		method: 'POST',
		data: {
			estimate: '<?= $estimateid ?>',
			ratecard: '<?= $current_rate ?>'
		},
		success: function(response) {
			console.log(response);
		}
	});
}
function saveFieldMethod() {
	if(this.value == 'CUSTOM') {
		$(this).closest('.form-group').find('div[class^=col-sm-]').show();
		$(this).closest('.col-sm-12').hide();
		$(this).closest('.form-group').find('input').focus();
		doneSaving();
	} else if($(this).is('[data-table]')) {
		var result = this.value;
		var name = this.name;
		if(name.substr(-2) == '[]') {
			result = [];
			$('[name="'+name+'"]').each(function() {
				result.push(this.value);
			});
		}
		$.ajax({
			url: 'estimates_ajax.php?action=estimate_fields',
			method: 'POST',
			dataType: 'html',
			data: {
				id: $(this).data('id'),
				id_field: $(this).data('id-field'),
				table: $(this).data('table'),
				field: name.replace('[]',''),
				value: result,
				estimate: $(this).data('estimate')
			},
			success: function(response) {
				console.log(response);
				if(name == 'quote_multiple') {
					window.location.replace('?edit=<?= $estimateid ?>&tab=scope&rate=<?= $current_rate ?>&status=quote');
				}
				doneSaving();
			}
		});
	}
}
function remove_line(img) {
	$(img).val(1).change();
	$(img).closest('tr').hide();
}
function add_line() {
	overlayIFrameSlider('estimate_scope_edit.php?estimateid=<?= $estimateid ?>&rate=<?= $_GET['rate'] ?>','75%');
}
</script>
<div id='estimate_accordions' class='sidebar show-on-mob panel-group block-panels col-xs-12'>
	<?php foreach($rates as $rate_id => $rate) {
		switch($rate[0]) {
			case 'SCOPE':
				$rate_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT `rate_card_name` FROM `rate_card_estimate_scopes` WHERE `id`='{$rate[1]}'"))[0];
				break;
			case 'COMPANY':
				$rate_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='{$rate[1]}'"))[0];
				break;
			default: break;
		}
		foreach($headings as $head_id => $heading) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#estimate_accordions" href="#collapse_<?= $rate_id.$head_id ?>">
							<?= $rate_id != '' ? $rate_name.': ' : '' ?><?= $heading ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_<?= $rate_id.$head_id ?>" class="panel-collapse collapse">
					<div class="panel-body" data-file="edit_headings.php?edit=<?= $estimateid ?>&tab=scope&rate=<?= $rate_id ?>&status=<?= $head_id ?>">
						Loading...
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#estimate_accordions" href="#collapse_<?= $rate_id ?>summary">
						<?= $rate_id != '' ? $rate_name.': ' : '' ?>Summary<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_<?= $rate_id ?>summary" class="panel-collapse collapse">
				<div class="panel-body" data-file="edit_summary.php?edit=<?= $estimateid ?>&tab=scope&$rate=<?= $rate_id ?>">
					Loading...
				</div>
			</div>
		</div>
	<?php } ?>
</div>
<div class="standard-collapsible hide-titles-mob sidebar tile-sidebar sidebar-override inherit-height double-gap-top">
	<ul>
		<a href="?view=<?= $_GET['edit'] ?>"><li><img src="../img/icons/dropdown-arrow.png" class="smaller inline-img black-color clockwise">Back to Overview</li></a>
		<?php foreach($headings as $head_id => $heading) { ?>
			<a href="#<?= $head_id ?>"><li class="<?= $_GET['status'] == '' ? 'active blue' : '' ?>"><?= $heading ?></li></a>
		<?php } ?>
		<a href="#summary"><li class="<?= $_GET['status'] == '' ? 'active blue' : '' ?>">Summary</li></a>
	</ul>
</div>
<div class='scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen default_screen form-horizontal standard-body'>
		<div class="standard-body-title"><h3><?= rtrim(ESTIMATE_TILE, 's') ?> Scope <a href="estimate_scope_edit.php?estimateid=<?= $estimateid ?>&scope=<?= $_GET['status'] ?>" onclick="overlayIFrameSlider(this.href, '75%', true, false, 'auto', true); return false;"><img class="inline-img smaller no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a></h3></div>
		<div class="standard-dashboard-body-content pad-top pad-left pad-right">
			<div class="form-group" id="no-more-tables>
				<?php foreach($rates as $rate_id => $rate) {
					if($rate_id != '') { ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=scope&rate=<?= $rate_id ?>" class="folder-tab <?= $_GET['rate'] == $rate_id ? 'active' : '' ?>">
							<?php switch($rate[0]) {
								case 'SCOPE':
									echo mysqli_fetch_array(mysqli_query($dbc, "SELECT `rate_card_name` FROM `rate_card_estimate_scopes` WHERE `id`='{$rate[1]}'"))[0];
									break;
								case 'COMPANY':
									echo mysqli_fetch_array(mysqli_query($dbc, "SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='{$rate[1]}'"))[0];
									break;
								default: break;
							} ?>
						</a>
					<?php }
				} ?>
			</div>
            <?php foreach($scope_list as $scope_id => $scope) {
                if($scope_id == $_GET['status']) {
                    include('edit_headings.php');
                }
            } ?>
			<?php include('edit_summary.php'); ?>
			<a href="?edit=<?= $estimateid ?>&tab=preview" class="btn brand-btn pull-right">Go To PDF Preview</a>
			<a href="?edit=<?= $estimateid ?>" class="btn brand-btn pull-right">Back to <?= rtrim(ESTIMATE_TILE, 's') ?> Details</a>
		</div>
	</div>
</div>