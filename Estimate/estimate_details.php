<?php $estimateid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'")); ?>
<script>
profile_tab = [];
var lock_timeout;
$(document).ready(function() {
	$('.panel-heading').click(loadPanel);
	$('[data-table]').change(saveField);
	$('.main-screen .main-screen').scroll(scrollScreen);
	$('.preview-bar').load('estimates_overview.php?view=<?= $estimateid ?>&sideview=true');
	<?php if($_GET['status'] != '') { ?>
		setTimeout(function() {
			$('.form-group:has(textarea)').css('height','18em');
			$('.main-screen .main-screen').scrollTop($('[data-tab-name=<?= $_GET['status'] ?>]').offset().top - $('.main-screen .main-screen').offset().top + 5);
			scrollScreen();
		}, 250);
	<?php } ?>
});
function moveToTab(tab) {
	$('.form-group:has(textarea)').css('height','18em');
	$('.main-screen .main-screen').scrollTop($('.main-screen .main-screen').scrollTop() + $('[data-tab-name='+tab+']').offset().top - $('.main-screen .main-screen').offset().top + 5);
	scrollScreen();
	return false;
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
			$(this).find('input,select,a').not('.chosen-container input').off('click').click(function() { this.blur(); return false; }).off('keyup').keyup(function() { this.blur(); return false; }).off('keypress').keypress(function() { this.blur(); return false; });
		} else {
			$(this).find('input,select,a').not('.chosen-container input').off('click').off('keyup').off('keypress');
            if($('.active.blue').length == 0) {
                $('a[href$="status='+$(this).data('tab-name')+'"] li').addClass('active blue');
            }
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
		url: $(body).data('file')+'?edit=<?= $_GET['edit'] ?>',
		method: 'POST',
		response: 'html',
		success: function(response) {
			$(body).html(response);
			$('[data-table]').off('change', saveField).change(saveField);
		}
	});
}
/*function saveField() {
	syncUnsaved(this.name);
	if(this.value == 'CUSTOM') {
		$(this).closest('.form-group').find('div[class^=col-sm-]').show().removeAttr('disabled');
		$(this).closest('.col-sm-12').hide();
		$(this).closest('.form-group').find('input').focus();
	} else if($(this).is('[data-table]')) {
		var result = this.value;
		var name = this.name;
		var table_name = $(this).data('table');
		var block = $(this).closest('.multi-block');
		if(name.substr(-2) == '[]') {
			result = [];
			$('[name="'+name+'"]').each(function() {
				result.push(this.value);
			});
		}
		if(this.type == 'checkbox' && !this.checked) {
			result = 0;
		}
		syncSaving();
		$.ajax({
			url: 'estimates_ajax.php?action=estimate_fields',
			method: 'POST',
			dataType: 'html',
			data: {
				id: $(this).data('id'),
				id_field: $(this).data('id-field'),
				table: table_name,
				field: name.replace('[]',''),
				value: result,
				estimate: $('[name=estimateid]').val()
			},
			success: function(response) {
				if(table_name == 'estimate' && '<?= $_GET['edit'] ?>' == 'new' && response > 0) {
					$('a.updateable');
					$('[name=estimateid]').val(response);
				} else if(block.length > 0 && response > 0) {
					block.find('[data-id]').not('[data-table=estimate]').data('id',response);
				}
				if(name == 'link') {
					window.location.replace('?edit='+$('[name=estimateid]').val()+'&status=documents');
				}
				$('.preview-bar').load('estimates_overview.php?view=<?= $estimateid ?>&sideview=true');
				syncDone(name);
			}
		});
	} else if(this.type == 'file') {
		var files = new FormData();
		for(var i = 0; i < this.files.length; i++) {
			files.append('files[]',this.files[i]);
		}
		files.append('table','estimate_document');
		files.append('estimate','<?= $estimateid ?>');
		$.ajax({
			url: 'estimates_ajax.php?action=estimate_uploads',
			method: 'POST',
			processData: false,
			contentType: false,
			data: files,
			success: function(response) {
				window.location.replace('?edit='+$('[name=estimateid]').val()+'&status=documents');
			}
		});
	}
}*/
</script>
<div id='estimate_accordions' class='sidebar show-on-mob panel-group block-panels col-xs-12'>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#estimate_accordions" href="#collapse_info">
					<?= rtrim(ESTIMATE_TILE, 's') ?> Information<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_info" class="panel-collapse collapse">
			<div class="panel-body" data-file="edit_information.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#estimate_accordions" href="#collapse_dates">
					Deliverables<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_dates" class="panel-collapse collapse">
			<div class="panel-body" data-file="edit_dates.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#estimate_accordions" href="#collapse_staff">
					Staff Collaboration<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_staff" class="panel-collapse collapse">
			<div class="panel-body" data-file="edit_staff.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#estimate_accordions" href="#collapse_templates">
					<?= rtrim(ESTIMATE_TILE, 's') ?> Scope<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_templates" class="panel-collapse collapse">
			<div class="panel-body" data-file="edit_templates.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#estimate_accordions" href="#collapse_details">
					Notes<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_details" class="panel-collapse collapse">
			<div class="panel-body" data-file="edit_details.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#estimate_accordions" href="#collapse_documents">
					Support Documents<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_documents" class="panel-collapse collapse">
			<div class="panel-body" data-file="edit_documents.php">
				Loading...
			</div>
		</div>
	</div>
</div>
<div class="standard-collapsible hide-titles-mob sidebar tile-sidebar sidebar-override inherit-height double-gap-top">
	<ul>
		<a href="?view=<?= $_GET['edit'] ?>"><li><img src="../img/icons/dropdown-arrow.png" class="smaller inline-img black-color clockwise">Back to Overview</li></a>
		<a href="?edit=<?= $_GET['edit'] ?>&status=information" onclick="return moveToTab('information')"><li class="<?= $_GET['status'] == '' ? 'active blue' : '' ?>"><?= rtrim(ESTIMATE_TILE, 's') ?> Information</li></a>
		<a href="?edit=<?= $_GET['edit'] ?>&status=dates" onclick="return moveToTab('dates')"><li class="<?= $_GET['status'] == 'dates' ? 'active blue' : '' ?>">Deliverables</li></a>
		<a href="?edit=<?= $_GET['edit'] ?>&status=staff" onclick="return moveToTab('staff')"><li class="<?= $_GET['status'] == 'staff' ? 'active blue' : '' ?>">Staff Collaboration</li></a>
		<a href="?edit=<?= $_GET['edit'] ?>&status=est_scope" onclick="return moveToTab('est_scope')"><li class="<?= $_GET['status'] == 'est_scope' ? 'active blue' : '' ?>"><?= rtrim(ESTIMATE_TILE, 's') ?> Scope</li></a>
		<a href="?edit=<?= $_GET['edit'] ?>&status=details" onclick="return moveToTab('details')"><li class="<?= $_GET['status'] == 'details' ? 'active blue' : '' ?>">Notes</li></a>
		<!--
        <a href="?edit=<?= $_GET['edit'] ?>&status=display_options" onclick="return moveToTab('display_options')"><li class="<?= $_GET['status'] == 'display_options' ? 'active blue' : '' ?>">Options</li></a>
        -->
		<a href="?edit=<?= $_GET['edit'] ?>&status=documents" onclick="return moveToTab('documents')"><li class="<?= $_GET['status'] == 'documents' ? 'active blue' : '' ?>">Reference Documents</li></a>
	</ul>
</div>
<div class='scalable preview-bar hide-titles-mob ui-resizable' style="padding:0;"></div>
<div class='scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen default_screen form-horizontal standard-body'>
		<div class="standard-body-title"><h3><?= rtrim(ESTIMATE_TILE, 's') ?> Details</h3></div>
		<div class="standard-dashboard-body-content pad-top pad-left pad-right">
			<input type="hidden" name="estimateid" value="<?= $_GET['edit'] ?>">
			<?php $config = explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM `field_config_estimate`"))[0]);
			include('edit_information.php');
			include('edit_dates.php');
			include('edit_staff.php');
			include('edit_scope.php');
			include('edit_details.php');
			//include('edit_display_options.php');
			include('edit_documents.php'); ?>
			<a href="?edit=<?= $estimateid ?>&tab=scope" class="btn brand-btn pull-right">Go To Scope</a>
			<a href="?view=<?= $estimateid ?>" class="btn brand-btn pull-right">Back to Overview</a>
		</div>
	</div>
</div>