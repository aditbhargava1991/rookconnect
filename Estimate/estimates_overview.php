<?php include_once('../include.php');
$estimateid = ($estimateid > 0 ? $estimateid : filter_var($_GET['view'],FILTER_SANITIZE_STRING));
$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));
$approvals = approval_visible_function($dbc, 'estimate');
$config = explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM `field_config_estimate`"))[0]);
$us_rate_no_auto = get_config($dbc, 'disable_us_auto_convert'); ?>
<script>
$(document).ready(function() {
	$('input,select').change(saveField).keyup(syncUnsaved);
    /* var overviewHeight = $('#estimates_main').height() - $('.tile-header').height() + 18;
    $('.main-screen .fit-to-screen-full').each(function() { $(this).attr('style',$(this).attr('style')+';height:'+overviewHeight+'px !important;'); }) */
    $(window).resize(function() {
        available = Math.floor($(window).innerHeight() - $('.main-screen .main-screen').offset().top - $('footer:visible').outerHeight());
		if(available > 300) {
            $('.main-screen .fit-to-screen-full').each(function() { $(this).attr('style',$(this).attr('style')+';height:'+available+'px !important;'); })
		}
        var table_width = $('.table_resize').width();
        if($('.table_resize').width() > 800) {
            $('.table_resize').removeClass('no-more-tables-forced');
            $('.table_resize').find('.hidden-xs').css('display','');
        } else {
            $('.table_resize').addClass('no-more-tables-forced');
            $('.table_resize').find('.hidden-xs').css('display','none');
        }
	}).resize();
});
<?php if(empty($_GET['view'])) { ?>
	function saveField() {
		syncUnsaved(this.name);
		syncSaving();
		name = this.name;
		if($(this).is('[data-table]') != '' && (this.name != 'completed' || $(this).closest('div').find('[name=check_completed]').is(':checked'))) {
			$.ajax({
				url: 'estimates_ajax.php?action=estimate_fields',
				method: 'POST',
				data: {
					id: $(this).data('id'),
					id_field: $(this).data('id-field'),
					table: $(this).data('table'),
					field: this.name,
					value: this.value,
					estimate: '<?= $estimateid ?>'
				},
				success: function(response) {
					syncDone(name);
				}
			});
		}
	}
<?php } ?>
function add_follow_up() {
	$.ajax({
		url: 'estimates_ajax.php?action=estimate_fields',
		method: 'POST',
		data: {
			table: 'estimate_actions',
			id: '',
			id_field: 'id',
			estimate: '<?= $estimateid ?>',
			value: '',
			field: ''
		},
		success: function(response) {
			window.location.reload();
		}
	});
}
function remove_follow_up(elem) {
	var result = confirm("Are you sure you want to delete this follow up?");
	if (result) {
		$.ajax({
            url: 'estimates_ajax.php?action=estimate_fields',
            method: 'POST',
            data: {
                table: 'estimate_actions',
                id: $(elem).data('id'),
                id_field: 'id',
                estimate: '<?= $estimateid ?>',
                value: '',
                field: 'delete'
            },
            success: function(response) {
                window.location.reload();
            }
        });
	}
}
</script>
<div class="form-horizontal main-screen fit-to-screen-full full-grey-screen" style="padding:0;">
	<div class="main-item blue-border">
		<h3><?= rtrim(ESTIMATE_TILE, 's') ?> Name: <?= $estimate['estimate_name'] ?><?= $edit_access > 0 ? '<a href="?edit='.$estimateid.'" class="pad-left"><img src="../img/icons/ROOK-edit-icon.png" alt="Edit" width="25" class="no-toggle" title="Edit" /></a>' : '' ?></h3>
		<hr />
        <div class="row">
            <div class="col-sm-<?= empty($_GET['sideview']) ? '6' : '12' ?>">
                <h4><?= rtrim(ESTIMATE_TILE, 's') ?> Details:</h4>
                <div class="row form-group">
                    <label class="col-sm-4"><?= ESTIMATE_TILE ?> #:</label>
                    <div class="col-sm-8"><?= $estimate['estimateid'] ?></div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-4">Created By:</label>
                    <div class="col-sm-8"><?= get_contact($dbc, $estimate['created_by']) ?></div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-4">Date Created:</label>
                    <div class="col-sm-8"><?= $estimate['created_date'] ?></div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-4">Status:</label>
                    <div class="col-sm-8">
                        <?php if($approvals == 0 && ($estimate['status'] == 'Saved' || $estimate['status'] == 'Pending')) {
                            echo $estimate['status'];
                        } else { ?>
                            <select class="chosen-select-deselect" name="status" data-table="estimate" data-id="<?= $estimate['estimateid'] ?>" data-id-field="estimateid"><option></option>
                                <?php foreach(explode('#*#',get_config($dbc, 'estimate_status')) as $status_name) {
                                    $status_id = preg_replace('/[^a-z]/','',strtolower($status_name)); ?>
                                    <option <?= $status_id == $estimate['status'] ? 'selected' : '' ?> value="<?= $status_id ?>"><?= $status_name ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </div>
                </div>

                <?php $action_list = mysqli_query($dbc, "SELECT * FROM `estimate_actions` WHERE `estimateid`='$estimateid' AND `deleted`=0 AND `completed`=0 ORDER BY `due_date` ASC");
                while($action = mysqli_fetch_array($action_list)) { ?>
                    <hr />
                    <div class="action-group">
                        <div class="row form-group">
                            <label class="col-sm-4">Next Action:</label>
                            <div class="col-sm-8">
                                <select name="action" class="chosen-select-deselect" data-table="estimate_actions" data-id="<?= $action['id'] ?>" data-id-field="id" data-estimate="<?= $estimateid ?>">
                                    <option></option>
                                    <option <?= $action['action'] == 'phone' ? 'selected' : '' ?> value="phone">Phone Call</option>
                                    <option <?= $action['action'] == 'email' ? 'selected' : '' ?> value="email">Email</option>
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-4">Follow Up Date:</label>
                            <div class="col-sm-8">
                                <input type="text" name="due_date" class="form-control datepicker" value="<?= $action['due_date'] ?>" data-table="estimate_actions" data-id="<?= $action['id'] ?>" data-id-field="id" data-estimate="<?= $estimateid ?>">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-4">Mark Completed:</label>
                            <div class="col-sm-8">
                                <input type="checkbox" name="check_completed" class="form-checkbox" value="1" onchange="$(this).closest('div').find('[name=completed]').focus();" style="margin-left:0;" />
                                <input type="text" name="completed" class="checkbox-text form-control" data-table="estimate_actions" data-id="<?= $action['id'] ?>" data-estimate="<?= $estimateid ?>" data-id-field="id" placeholder="Follow Up Details" onblur="if(this.value == '') { $(this).closest('.form-group').find('[name=check_completed]').removeAttr('checked'); alert('You must provide details about the follow up.'); }">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="pull-right"><a href="javascript:void(0);" data-id="<?= $action['id'] ?>" onclick="remove_follow_up(this); return false;"><img src="../img/remove.png" class="inline-img" alt="Remove Folow Up" width="25" /></a>
                            <label class="pull-right"><a href="javascript:void(0);" onclick="add_follow_up(); return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img" alt="Add Folow Up" width="25" /></a>
                        </div>
                    </div>
                <?php } ?>

                <hr />

                <div class="row form-group">
                    <label class="col-sm-4">Business:</label>
                    <div class="col-sm-8">
                        <?= get_client($dbc, $estimate['businessid']) ?><br />
                        <?= get_address($dbc, $estimate['businessid']) ?>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-4">Contact:</label>
                    <div class="col-sm-8"><?php
                        $contact = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='{$estimate['clientid']}'"));
                        $name = decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']);
                        $home = decryptIt($contact['home_phone']);
                        $office = decryptIt($contact['office_phone']);
                        $cell = decryptIt($contact['cell_phone']);
                        $email = decryptIt($contact['email_address']); ?>
                        <img src="../img/person.PNG" height="15" class="pad-right"><?= get_contact($dbc, $estimate['clientid']) ?><br />
                        <?php if($email != '') { ?>
                            <a href="mailto:<?= $email ?>"><img src="../img/email.PNG" height="15" class="pad-right"><?= $email ?></a><br />
                        <?php } ?>
                        <?php if($home != '') { ?>
                            <a href="tel:<?= $home ?>"><img src="../img/home_phone.PNG" height="15" class="pad-right"><?= $home ?></a><br />
                        <?php } ?>
                        <?php if($office != '') { ?>
                            <a href="tel:<?= $office ?>"><img src="../img/office_phone.PNG" height="15" class="pad-right"><?= $office ?></a><br />
                        <?php } ?>
                        <?php if($cell != '') { ?>
                            <a href="tel:<?= $cell ?>"><img src="../img/cell_phone.PNG" height="15" class="pad-right"><?= $cell ?></a>
                        <?php } ?>
                    </div>
                </div>
                <hr />

                <?php if(in_array('Overview Hours',$config)) { ?>
                    <div class="row form-group">
                        <label class="col-sm-4">Total Hours</label>
                        <div class="col-sm-8">
                            <?= number_format(mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`qty`) FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `src_table` IN ('equipment', 'labour', 'staff', 'client', 'contractor', 'contacts')"))[0],2) ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if(in_array('Overview Items',$config)) { ?>
                    <div class="row form-group">
                        <label class="col-sm-4">Total Items</label>
                        <div class="col-sm-8">
                            <?= number_format(mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`qty`) FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `src_table` IN ('inventory', 'vpl')"))[0],2) ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if(in_array('Overview Services',$config)) { ?>
                    <div class="row form-group">
                        <label class="col-sm-4">Total Services</label>
                        <div class="col-sm-8">
                            <?= number_format(mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`qty`) FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `src_table` IN ('services')"))[0],2) ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if(in_array('Overview Other',$config)) { ?>
                    <div class="row form-group">
                        <label class="col-sm-4">Total Other</label>
                        <div class="col-sm-8">
                            <?= number_format(mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`qty`) FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `src_table` NOT IN ('equipment', 'labour', 'staff', 'client', 'contractor', 'contacts', 'inventory', 'vpl', 'services')"))[0],2) ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if(in_array('Overview All',$config)) { ?>
                    <div class="row form-group">
                        <label class="col-sm-4">Total Services</label>
                        <div class="col-sm-8">
                            <?= number_format(mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`qty`) FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `src_table`"))[0],2) ?>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <?php if(empty($_GET['sideview'])) { ?>
                <div class="col-sm-6">
                    <h4><?= ESTIMATE_TILE ?> Notes:</h4><?php
                    $notes = mysqli_query($dbc, "SELECT * FROM `estimate_notes` WHERE `deleted`=0 AND `estimateid`='$estimateid'");
                    if ( $notes->num_rows > 0 ) { ?>
                        <div id="no-more-tables">
                            <table class="table table-bordered">
                                <tr class="hidden-sm hidden-xs">
                                    <th>Heading</th>
                                    <th>Note</th>
                                    <th>Created</th>
                                    <th>Assigned</th>
                                </tr><?php
                                while($note = mysqli_fetch_array($notes)) { ?>
                                    <tr>
                                        <td data-title="Heading"><?= $note['heading'] ?></td>
                                        <td data-title="Note"><?= html_entity_decode($note['notes']) ?></td>
                                        <td data-title="Created"><?= $note['created_by'] > 0 ? get_contact($dbc, $note['created_by']).'<br />' : '' ?><?= date('Y-m-d', strtotime($note['note_date'])) ?></td>
                                        <td data-title="Assigned To"><?= $note['assigned'] > 0 ? get_contact($dbc, $note['assigned']) : '' ?></td>
                                    </tr><?php
                                } ?>
                            </table>
                        </div><?php
                    } else {
                        echo '-';
                    } ?>
                    <hr />
                </div>
                <div class="col-sm-6">
                    <h4><?= ESTIMATE_TILE ?> History:</h4>
                    <?= $estimate['history'] ?>
                </div>
                <hr>
            <?php } ?>
        </div><!-- .row -->
		<div class="col-sm-12">
			<h4><?= rtrim(ESTIMATE_TILE, 's') ?> Scope:</h4><?php
            $ticket_scope = false;
            foreach(explode(',',$estimate['estimatetype']) as $est_type) {
                if(strpos($est_type,'ticket_est_') === 0) {
                    $ticket_scope = true;
                }
            }
            if($ticket_scope) {
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
                $_GET['edit'] = $_GET['ticketid'] = $ticketid;
                include('../Ticket/edit_tickets.php');
                $_GET['edit'] = $estimateid;
                
            } else {
                $scope = mysqli_query($dbc, "SELECT * FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `src_table` != '' AND (`src_id` > 0 OR `description` != '') AND `deleted`=0 ORDER BY `rate_card`, `scope_name`, `heading`, `sort_order`");
                $heading_order = explode('#*#', get_config($dbc, 'estimate_field_order'));
                if(in_array('Scope Detail',$config) && !in_array_starts('Detail',$heading_order)) {
                    $heading_order[] = 'Detail***Scope Detail';
                }
                if(in_array('Scope Billing',$config) && !in_array_starts('Billing Frequency',$heading_order)) {
                    $heading_order[] = 'Billing Frequency***Billing Frequency';
                }
                $col_width = 0;
                foreach($heading_order as $order_info) {
                    $order_info = explode('***',$order_info);
                    // Count each column in use, and count description as four times wider
                    switch($order_info[0]) {
                        case 'Description':
                            $col_width += 3;
                        case 'UOM':
                        case 'Quantity':
                        case 'Estimate Price':
                        case 'Total':
                            $col_width += 1;
                            break;
                    }
                }
                $col_width = 1 / ($col_width + 1) * 100;

                $exchange_rate_list = json_decode(file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'), TRUE);
                $us_rate = $exchange_rate_list['observations'][count($exchange_rate_list['observations']) - 1]['FXUSDCAD']['v']; ?>
                <div id="no-more-tables" class="responsive-table table_resize">
                    <div style="overflow-x: auto;">
                        <table class="table table-bordered">
                            <tr class="hidden-xs hidden-sm">
                                <th>Heading</th>
                                <?php foreach($heading_order as $order_info) {
                                    $order_info = explode('***',$order_info);
                                    switch($order_info[0]) {
                                        case 'Description':
                                            echo "<th style='width:".($col_width * 4)."%;'>".(empty($order_info[1]) ? $order_info[0] : $order_info[1])."</th>";
                                            break;
                                        case 'UOM':
                                        case 'Quantity':
                                        case 'Estimate Price':
                                        case 'Total':
                                            echo "<th style='width:$col_width%;'>".(empty($order_info[1]) ? $order_info[0] : $order_info[1])."</th>";
                                            break;
                                    }
                                } ?>
                            </tr>
                            <?php while($scope_line = mysqli_fetch_assoc($scope)) {
                                $scope_description = $scope_line['description'];
                                if($scope_line['src_table'] == 'equipment' && $scope_line['src_id'] > 0) {
                                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `equipmentid`='{$scope_line['src_id']}'"))['label'];
                                } else if($scope_line['src_table'] == 'inventory' && $scope_line['src_id'] > 0) {
                                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) label FROM `inventory` WHERE `inventoryid`='{$scope_line['src_id']}'"))['label'];
                                } else if($scope_line['src_table'] == 'labour' && $scope_line['src_id'] > 0) {
                                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`labour_type`,''),' ',IFNULL(`category`,''),' ',IFNULL(`heading`,''),' ',IFNULL(`name`,'')) label FROM `labour` WHERE `labourid`='{$scope_line['src_id']}'"))['label'];
                                } else if($scope_line['src_table'] == 'material' && $scope_line['src_id'] > 0) {
                                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`sub_category`,''),' ',IFNULL(`name`,'')) label FROM `material` WHERE `materialid`='{$scope_line['src_id']}'"))['label'];
                                } else if($scope_line['src_table'] == 'position' && $scope_line['src_id'] > 0) {
                                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` label FROM `positions` WHERE `position_id`='{$scope_line['src_id']}'"))['label'];
                                } else if($scope_line['src_table'] == 'products' && $scope_line['src_id'] > 0) {
                                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `products` WHERE `productid`='{$scope_line['src_id']}'"))['label'];
                                } else if($scope_line['src_table'] == 'services' && $scope_line['src_id'] > 0) {
                                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `services` WHERE `serviceid`='{$scope_line['src_id']}'"))['label'];
                                } else if($scope_line['src_table'] == 'vpl' && $scope_line['src_id'] > 0) {
                                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`product_name`,'')) label FROM `vendor_price_list` WHERE `inventoryid`='{$scope_line['src_id']}'"))['label'];
                                } else if($scope_line['src_table'] != 'miscellaneous' && $scope_line['src_id'] > 0) {
                                    $scope_description = get_contact($dbc, $scope_line['src_id']);
                                }
                                if($scope_line['pricing'] == 'usd_cpu' && !($scope_line['price'] > 0)) {
                                    if($us_rate_no_auto == 'true') {
                                        $scope_line['price'] = $scope_line['cost'];
                                        $scope_line['retail'] = $scope_line['qty'] * $scope_line['price'];
                                    } else {
                                        $scope_line['price'] = $scope_line['cost'] * $us_rate;
                                        $scope_line['retail'] = $scope_line['qty'] * $scope_line['price'];
                                    }
                                }
                                if($scope_line['pricing'] == 'usd_cpu' && $us_rate_no_auto == 'true') {
                                    $scope_line['price'] .= ' USD';
                                    $scope_line['cost'] .= ' USD';
                                    $scope_line['retail'] .= ' USD';
                                }
                                ?>
                                <tr>
                                    <td data-title="Heading"><?= $scope_line['heading'] ?></td>
                                    <?php foreach($heading_order as $order_info) {
                                        $order_info = explode('***',$order_info);
                                        switch($order_info[0]) {
                                            case 'Description':
                                                echo '<td data-title="'.(empty($order_info[1]) ? $order_info[0] : $order_info[1]).'">'.html_entity_decode($scope_description).'</td>';
                                                break;
                                            case 'UOM':
                                                echo '<td data-title="'.(empty($order_info[1]) ? $order_info[0] : $order_info[1]).'">'.($scope_line['src_table'] != 'notes' ? $scope_line['uom'] : '').'</td>';
                                                break;
                                            case 'Quantity':
                                                echo '<td data-title="'.(empty($order_info[1]) ? $order_info[0] : $order_info[1]).'">'.($scope_line['src_table'] != 'notes' ? round($scope_line['qty'],2) : '').'</td>';
                                                break;
                                            case 'Estimate Price':
                                                echo '<td data-title="'.(empty($order_info[1]) ? $order_info[0] : $order_info[1]).'">'.($scope_line['src_table'] != 'notes' ? '$'.$scope_line['price'] : '').'</td>';
                                                break;
                                            case 'Total':
                                                echo '<td data-title="'.(empty($order_info[1]) ? $order_info[0] : $order_info[1]).'">'.($scope_line['src_table'] != 'notes' ? '$'.$scope_line['retail'] : '').'</td>';
                                                break;
                                        }
                                    } ?>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            <?php } ?>
		</div>
        <hr />

		<?php if(empty($_GET['sideview'])) { ?>
			<div class="col-sm-12">
				<h4>Cost Analysis: <a href="?financials=<?= $estimate['estimateid'] ?>"><img src="../img/icons/financials.png" class="inline-img no-toggle" title="View Estimate Financial Summary." width="20"></a></h4>
			</div>
		<?php } ?>
		<div class="clearfix"></div>
	</div>
	<div class="outer-btns">
		<script>
		function attach_to_project() {
			var projectid = $('[name=attach_to_project]').val();
			if(projectid > 0) {
				window.location.href = 'convert_to_project.php?estimate=<?= $estimateid ?>&projectid='+projectid;
			} else {
				if($('.select-project').is(':visible')) {
					alert('Please Select a <?= PROJECT_NOUN ?>.');
				} else {
					$('.select-project').show();
					$('.main-screen .main-screen').get(0).scrollTop = $('.main-screen .main-screen').get(0).scrollHeight;
				}
			}
		}
		function createTemplate() {
			$.post('estimates_ajax.php?action=copy_as_template', { estimate: '<?= $estimateid ?>' }, function(response) {
				window.location.href = 'estimates.php?template='+response;
			});
		}
		</script>
		<?php if($edit_access > 0) { ?>
			<a href="?edit=<?= $estimateid ?>" class="btn brand-btn">Edit</a>
			<?php if($estimate['projectid'] > 0) { ?>
				<a href="../Project/projects.php?edit=<?= $estimate['projectid'] ?>" class="btn brand-btn"><?= PROJECT_NOUN.' #'.$estimate['projectid'] ?></a>
			<?php } else if($estimate['estimatetype'] != 'one_time_est_type' && strpos($estimate['estimatetype'],'ticket_est_') !== 0) { ?>
				<a href="convert_to_project.php?estimate=<?= $estimateid ?>" class="btn brand-btn">Create <?= PROJECT_NOUN ?></a>
				<a href="convert_to_project.php?estimate=<?= $estimateid ?>" onclick="attach_to_project();$(this).hide();return false;" class="btn brand-btn">Attach to <?= PROJECT_NOUN ?></a>
			<?php } ?>
            <?php if($estimate['estimatetype'] != 'one_time_est_type' && strpos($estimate['estimatetype'],'ticket_est_') !== 0) { ?>
                <a href="?edit=<?= $estimateid ?>" class="btn brand-btn">Copy <?= ESTIMATE_TILE ?></a>
                <a onclick="createTemplate(); return false;" class="btn brand-btn">Create Template from <?= ESTIMATE_TILE ?></a>
            <?php } ?>
		<?php }
        if(strpos($estimate['estimatetype'],'ticket_est_') === 0 && vuaed_visible_function($dbc, 'tickets')) {
            $ticket = $dbc->query("SELECT `ticketid`, `status`, `ticket_type` FROM `tickets` WHERE `estimateid`='$estimateid' AND `deleted`=0 AND `estimateid` > 0")->fetch_assoc();
            if($ticket['ticketid'] > 0 && $ticket['status'] == 'ffm_est_ticket') { ?>
                <a href="../Ticket/index.php?edit=<?= $ticket['ticketid'] ?>" onclick="$.get('estimates_ajax.php', { action: 'activate_ticket', ticketid: '<?= $ticket['ticketid'] ?>', type: '<?= $ticket['ticket_type'] ?>' }, function() { overlayIFrameSlider('../Ticket/index.php?edit=<?= $ticket['ticketid'] ?>&calendar_view=true','auto',true,false); }); return false;" class="btn brand-btn">Create <?= TICKET_NOUN ?></a>
            <?php } else if($ticket['ticketid'] > 0) { ?>
                <a href="../Ticket/index.php?edit=<?= $ticket['ticketid'] ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,true); return false;" class="btn brand-btn">Edit <?= TICKET_NOUN ?></a>
            <?php } ?>
        <?php } ?>

        <?php $pdf_styles = mysqli_query($dbc, "SELECT `pdfsettingid`,`style_name`,`style` FROM `estimate_pdf_setting` WHERE `estimateid` IS NULL AND `deleted`=0 ORDER BY `style_name`");
        if(empty($_GET['style'])) {
            $_GET['style'] = $estimate['pdf_style'];
        }
        while($pdf_style = mysqli_fetch_assoc($pdf_styles)) {
            if(empty($_GET['style'])) {
                $_GET['style'] = $pdf_style['pdfsettingid'];
            }
        }
        ?>
		<a target="_blank" href="estimate_pdf_output.php?edit=<?= $estimateid ?>&style=<?= $_GET['style'] ?>" class="btn brand-btn pull-right">PDF</a>
		<a target="_blank" href="estimate_pdf_output.php?edit=<?= $estimateid ?>&style=<?= $_GET['style'] ?>&email=send" class="btn brand-btn pull-right">Email PDF</a>

        <!-- <a href="?edit=<?= $estimateid ?>" class="pull-right btn brand-btn">Email PDF</a> -->
		<?php if(!($estimate['projectid'] > 0) && $estimate['estimatetype'] != 'one_time_est_type' && strpos($estimate['estimatetype'],'ticket_est_') !== 0) { ?>
			<div class="select-project" style="display:none;">
				<select name="attach_to_project" data-placeholder="Select a <?= PROJECT_NOUN ?>" class="chosen-select-deselect"><option></option>
					<?php $projects = mysqli_query($dbc, "SELECT `projectid`, `project_name` FROM `project` WHERE `deleted`=0");
					while($project = mysqli_fetch_assoc($projects)) { ?>
						<option <?= $estimate['add_to_project'] == $project['projectid'] ? 'selected' : '' ?> value="<?= $project['projectid'] ?>"><?= PROJECT_NOUN.' #'.$project['projectid'].' '.$project['project_name'] ?></option>
					<?php } ?>
				</select>
				<button onclick="attach_to_project(); return false;" class="btn brand-btn pull-right">Confirm</button>
				<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
			</div>
		<?php }  ?>
	</div>
</div>