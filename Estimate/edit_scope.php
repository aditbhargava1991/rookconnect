<?php include_once('../include.php');
checkAuthorised('estimate');
error_reporting(0);
if(!isset($estimate)) {
	$estimateid = filter_var($estimateid,FILTER_SANITIZE_STRING);
	$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));
} ?>
<script>
profile_tab = [];
var lock_timeout;
var tile_options = [];
$(document).ready(function() {
	$('[data-table]').change(saveField);
	$('#no-more-tables').sortable({
		handle: '.scope-handle',
		items: '.sort_table',
		update: save_sort
	});
	$('#no-more-tables').sortable({
		handle: '.heading-handle',
		items: 'table',
		update: save_sort
	});
	$('.sort_table').sortable({
		connectWith: '#no-more-tables',
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
			$('a[href$="status='+$(this).data('tab-name')+'"] li').addClass('active blue');
		}
	});
	if($('#view_profile').is(':visible')) {
		$('[href=#view_profile] li').addClass('active blue');
	}
}
function add_heading(scope) {
	$.ajax({
		url: 'estimates_ajax.php?action=estimate_add_heading',
		method: 'POST',
		data: {
			estimate: '<?= $estimateid ?>',
			scope: scope
		},
		success: function(response) {
			window.location.replace('?edit=<?= $estimateid ?>&status=est_scope');
		}
	});
}
function rem_heading(img) {
	var block = $(img).closest('table');
	block.find('[name=deleted]').val(1).change();
	block.hide();
}
function add_scope(scope) {
	$.ajax({
		url: 'estimates_ajax.php?action=estimate_add_scope',
		method: 'POST',
		data: {
			estimate: '<?= $estimateid ?>'
		},
		success: function(response) {
			window.location.replace('?edit=<?= $estimateid ?>&status=est_scope');
		}
	});
}
function rem_scope(img) {
	var block = $(img).closest('.sort_table');
	block.find('[name=deleted]').val(1).change();
	block.hide();
}
function saveFieldMethod(field) {
	if(field.value == 'CUSTOM') {
		$(field).closest('.form-group').find('div[class^=col-sm-]').show();
		$(field).closest('.col-sm-12').hide();
		$(field).closest('.form-group').find('input').focus();
		doneSaving();
	} else if($(field).is('[data-table]')) {
		var result = field.value;
		var name = field.name;
		if(name.substr(-2) == '[]') {
			result = [];
			$('[name="'+name+'"]').each(function() {
				result.push(field.value);
			});
		}
		$.ajax({
			url: 'estimates_ajax.php?action=estimate_fields',
			method: 'POST',
			dataType: 'html',
			data: {
				id: $(field).data('id'),
				id_field: $(field).data('id-field'),
				table: $(field).data('table'),
				field: name.replace('[]',''),
				value: result,
				estimate: $(field).data('estimate')
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
<div class="form-horizontal col-sm-12" data-tab-name="est_scope">
	<h3 class="pull-left"><?= rtrim(ESTIMATE_TILE, 's') ?> Scope</h3>
	<!--<button class="btn brand-btn" onclick="overlayIFrameSlider('estimate_scope_edit.php?estimateid=<?= $estimateid ?>&src=true', '75%', true, true, 'auto', true); return false;">Load Scope Details</button>-->
	<div class="pull-right gap-top"><a href="" onclick="overlayIFrameSlider('estimate_scope_edit.php?estimateid=<?= $estimateid ?>&src=true', '75%', true, true, 'auto', true); return false;"><img class="inline-img" src="../img/icons/ROOK-add-icon.png"></a></div>
	<div class="clearfix"></div>
    
    <div class="scale-to-fill has-main-screen hide-titles-mob"><?php
        $scope_list = [];
        $add_est_scope = false;
        foreach(explode(',',$estimate['estimatetype']) as $est_type) {
            if(strpos($est_type,'ticket_est_') === 0) {
                $scope_list[$est_type] = substr($est_type,11).' Scope';
            } else {
                $add_est_scope = true;
            }
        }
        if($add_est_scope) {
            $query = mysqli_query($dbc, "SELECT `scope_name` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY IFNULL(`scope_name`,'') ORDER BY MIN(`sort_order`)");
            $scope_name = '';
            if(mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_array($query)) {
                    $scope_list[config_safe_str($row[0])] = $row[0];
                }
            } else {
                $scope_list['scope_1'] = 'Scope 1';
            }
        }
        
        if(count($scope_list) < 2) {
            include('../Rate Card/line_types.php');
            $estimateid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
            $estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));
            $config = explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM `field_config_estimate`"))[0]);
            $us_exchange = json_decode(file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'), TRUE);

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
            $_GET['rate'] = $current_rate; ?>
            
            <!--<div class="standard-body-title"><h3>Estimate Scope <a href="estimate_scope_edit.php?estimateid=<?= $estimateid ?>&scope=<?= $_GET['status'] ?>" onclick="overlayIFrameSlider(this.href, '75%', true, false, 'auto', true); return false;"><img class="inline-img smaller" src="../img/icons/ROOK-edit-icon.png"></a></h3></div>-->
            <div id="no-more-tables"><?php
                foreach($scope_list as $scope_id => $scope) {
                    if(strpos($scope_id,'ticket_est_') === 0) {
                        $get_ticket = $dbc->query("SELECT * FROM `tickets` WHERE `deleted`=0 AND `estimateid`='$estimateid' AND `estimateid` > 0");
                        if($get_ticket->num_rows > 0) {
                            $get_ticket = $get_ticket->fetch_assoc();
                            $ticketid = $get_ticket['ticketid'];
                        } else {
                            $dbc->query("INSERT INTO `tickets` (`ticket_type`,`status`,`estimateid`,`businessid`,`clientid`,`siteid`,`afe_number`,`heading`) SELECT '".config_safe_str(substr($scope_id,11))."','ffm_est_ticket',`estimateid`,`businessid`,`clientid`,`siteid`,`afe_number`,`estimate_name` FROM `estimate` WHERE `estimateid` > 0 AND `estimateid`='$estimateid'");
                            $ticketid = $dbc->insert_id;
                            $get_ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
                        }
                        $force_readonly = true;
                        $_GET['calendar_view'] = 'true';
                        $_GET['estimate_mode'] = 1;
                        $_GET['ticket_type'] = config_safe_str(substr($scope_id,11));
                        $_GET['edit'] = $_GET['ticketid'] = $ticketid; ?>
                        <a href="../Ticket/edit_tickets.php?edit=<?= $ticketid ?>&estimate_mode=1" onclick="overlayIFrameSlider(this.href+'&calendar_view=true', 'auto', true, false); return false;"><h4><?= substr($scope_id,11) ?> Details<img class="inline-img" src="../img/icons/ROOK-edit-icon.png"></h4></a>
                        <?php include('../Ticket/edit_tickets.php');
                        $_GET['edit'] = $estimateid;
                    } else {
                        include('edit_headings.php');
                    }
                } ?>
            </div>
            <?php //include('edit_summary.php'); ?>
        <?php } else {
            foreach($scope_list as $scope_id => $scope) { ?>
                <a href="?edit=<?= $_GET['edit'] ?>&tab=scope&status=<?= $scope_id ?>" class="pull-left"><?= $scope ?></a><br />
            <?php }
        } ?>
        
    </div>
    
    <hr />
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_scope.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>