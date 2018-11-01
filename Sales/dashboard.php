<script>
$(document).ready(function() {
	allow_sort();
});
$(document).on('change', 'select[name="status"]', function() { changeLeadStatus(this); });
$(document).on('change', 'select[name="next_action"]', function() { changeLeadNextAction(this); });

// Status Edit Functions
function edit_status(img) {
    var item = $(img).closest('.info-block');
    var prior_status = item.find('input[name=status_name]').val();
    item.find('h4').hide();
    item.find('input[name=status_name]').show().focus().off('blur').blur(function() {
        if(this.value != prior_status) {
            var status_list = [];
            $('input[name=status_name]').each(function() {
                status_list.push(this.value);
            });
            item.find('h4 span').text(this.value);
            item.find('a').attr('href','?p=filter&s='+this.value);
            item.data('status',this.value);
            $.post('sales_ajax_all.php?action=dashboard_lead_statuses', {
                action: 'rename',
                prior_status: prior_status,
                post_status: this.value,
                sales_lead_status: status_list
            });
        }
        $(this).hide();
        item.find('h4').show();
    });
}
function add_status(img) {
    $('.dashboard-container').sortable('destroy');
	$('.main-screen-white').sortable('destroy');
    var item = $(img).closest('.info-block-container');
    var clone = item.clone();
    clone.find('.info-block-detail').remove();
    clone.find('h4 span').text('New Status');
    clone.find('input[name=status_name]').val('New Status');
    clone.find('a').attr('href','?p=filter&s='+'New Status');
    clone.data('status','New Status');
    item.after(clone);
    allow_sort();
}
function rem_status(img) {
    if(confirm("Are you sure you want to remove this status? All <?= SALES_TILE ?> with this status will be archived.")) {
        $(img).closest('.info-block-container').remove();
        var status_list = [];
        $('input[name=status_name]').each(function() {
            status_list.push(this.value);
        });
        $.post('sales_ajax_all.php?action=dashboard_lead_statuses', {
            action: 'remove',
            prior_status: $(img).closest('.info-block').find('input[name=status_name]').val(),
            sales_lead_status: status_list
        });
    }
}
// Allow drag and dropping
function allow_sort() {
    $('.dashboard-container').sortable({
        handle: '.status_handle',
        items: '.info-block-container',
        update: function(event, element) {
            var status_list = [];
            $('input[name=status_name]').each(function() {
                status_list.push(this.value);
            });
            $.post('sales_ajax_all.php?action=dashboard_lead_statuses', {
                sales_lead_status: status_list
            });
        }
    });
	$('.info-block-details').sortable({
        connectWith: '.info-block-details',
		items: '.info-block-detail:not(.no-sort)',
		handle: '.lead-handle',
		update: function(event, element) {
			$.ajax({
				url: 'sales_ajax_all.php?fill=changeLeadStatus&salesid='+element.item.data('id')+'&status='+element.item.closest('.info-block').data('status'),
				success: function() {
					window.location.reload();
				}
			});
		}
	});
}
</script>
<!-- Sales Dashboard -->
<div class="main-screen-white horizontal-scroll no-overflow-y dashboard-container" style="height:95%"><?php
	$project_security = get_security($dbc, 'project');
    $get_config_won_status = get_config($dbc, 'lead_status_won');
    $get_config_lost_status = get_config($dbc, 'lead_status_lost');
    $get_config_retained = get_config($dbc, 'lead_status_retained');
	$estimates_active = tile_enabled($dbc, 'estimate')['user_enabled'];
	$flag_colours = explode(',', get_config($dbc, "ticket_colour_flags"));
	$flag_labels = explode('#*#', get_config($dbc, "ticket_colour_flag_names"));
	$staff_list = sort_contacts_query($dbc->query("SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
    foreach ( explode(',', $statuses) as $status ) { ?>
        <div class="col-xs-12 col-sm-6 col-md-4 gap-top info-block-container">
            <div class="info-block" data-status="<?= $status ?>">
                <?php if(!in_array($status, [$get_config_won_status,$get_config_lost_status,$get_config_retained])) { ?>
                    <input type="text" class="form-control pull-left" name="status_name" value="<?= $status ?>" style="display:none;">
                <?php } ?>
                <a href="?p=filter&s=<?= $status ?>"><div class="info-block-header">
                    <h4><span><?= $status; ?></span>
                        <img src="../img/icons/drag_handle.png" class="inline-img small pull-right status_handle no-toggle" onclick="return false;" title="Drag">
                        <?php if(!in_array($status, [$get_config_won_status,$get_config_lost_status,$get_config_retained])) { ?>
                            <img src="../img/icons/ROOK-edit-icon.png" class="inline-img small no-toggle" onclick="edit_status(this); return false;" title="Edit">
                            <img src="../img/remove.png" class="inline-img small pull-right" onclick="rem_status(this); return false;">
                            <img src="../img/icons/ROOK-add-icon.png" class="inline-img small pull-right" onclick="add_status(this); return false;">
                        <?php } ?>
                    </h4>
                    <?php $count = mysqli_fetch_assoc ( mysqli_query($dbc, "SELECT COUNT(`status`) AS `count` FROM `sales` WHERE `status`='{$status}' AND `deleted`=0" . $query_mod) );
                    echo '<div class="info-block-small">Lead : ' . $count['count'] . '</div>'; ?>
                </div></a>
                <div class="info-block-details padded" style="max-height: calc(100% - 4.5em);"><?php
                    $result = mysqli_query($dbc, "SELECT * FROM `sales` WHERE `status`='{$status}' AND `deleted`=0" . $query_mod.' LIMIT 0,10');
					$lead_count = 0;
                    if ( $result->num_rows > 0 ) {
                        while ( $row=mysqli_fetch_assoc($result) ) {
							$flag_colour = $flag_label = '';
							if(!empty($row['flag_label'])) {
								$flag_colour = $row['flag_colour'];
								$flag_label = $row['flag_label'];
							} else if(!empty($row['flag_colour'])) {
								$flag_colour = $row['flag_colour'];
                                $flag_label_row = array_search($row['flag_colour'], $flag_colours);
                                if($flag_label_row !== FALSE) {
                                    $flag_label = $flag_labels[$flag_label_row];
                                }
							}
                            $lead_colour = get_contact($dbc, $row['primary_staff'],'calendar_color');
							$lead_count++; ?>
                            <div class="info-block-detail <?= $approvals > 0 || $status != 'Pending' ? '' : 'no-sort' ?>" data-id="<?= $row['salesid'] ?>" style="<?= $lead_count > 10 ? 'display: none;' : '' ?> <?= empty($flag_colour) ? '' : 'background-color:#'.$flag_colour.';' ?> <?= empty($lead_colour) ? '' : 'border: 3px solid '.$lead_colour.' !important;' ?>" data-searchable="<?= get_client($dbc, $row['businessid']); ?> <?= get_contact($dbc, $row['contactid']); ?> <?= $row['next_action']; ?><?= $row['lead_value']; ?><?= $row['new_reminder']; ?>" data-colour="<?= $flag_colour ?>">
                                <span class="flag-label"><?= $flag_label ?></span>
                                <?php if($approvals > 0 || $status != 'Pending') { ?>
                                    <img src="../img/icons/drag_handle.png" class="inline-img pull-right lead-handle no-toggle" title="Drag" />
                                <?php } ?>
                                <?php if($row['primary_staff'] > 0) { ?>
                                    <div class="pull-right"><?= profile_id($dbc, $row['primary_staff']); ?></div>
                                <?php } ?>

                                <?php
                                if($row['number_of_days'] > 0) { ?>
                                <div class="row set-row-height"><div class="col-sm-12">
                                    <?php
                                        $now = time(); // or your date as well
                                        $your_date = strtotime($row['number_of_days_start_date']);
                                        $datediff = $now - $your_date;

                                        echo '<b class="pull-right">'.round($datediff / (60 * 60 * 24)).'/'.$row['number_of_days'].' Days'.'</b>';
                                    ?>
                                </div></div>
                                <?php } ?>

								<a href="sale.php?p=preview&id=<?= $row['salesid'] ?>">
                                <div class="row set-row-height" style="<?= empty($row['status']) || empty($row['next_action']) || empty($row['new_reminder']) ? 'color: red;' : '' ?>">
                                    <div class="col-sm-12"><?= get_client($dbc, $row['businessid']); ?><img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit">
										<b class="pull-right"><?= '$' . ($row['lead_value'] > 0) ? number_format($row['lead_value'], 2) : '0:00' ; ?></b></div>
                                </div>

                                <div class="row set-row-height">
                                    <div class="col-sm-12"><?php
                                        $contacts = '';
                                        foreach ( explode(',', $row['contactid']) as $contact ) {
                                            if ( get_contact($dbc, $contact) != '-' ) {
                                                $contacts .= get_contact($dbc, $contact) . ', ';
                                            }
                                        }
                                        echo rtrim($contacts, ', '); ?>
                                    </div>
                                </div></a>

                                <div class="clearfix"></div>
                                <?php include('quick_actions.php'); ?>


                                <div class="row set-row-height">
                                    <div class="col-sm-5">Status:</div>
                                    <div class="col-sm-7">
										<?php if($approvals > 0 || $status != 'Pending') { ?>
											<select name="status" class="chosen-select-deselect form-control" id="ssid_<?= $row['salesid'] ?>">
												<option value=""></option><?php
												foreach ( explode(',', $statuses) as $status_list ) {
													$selected = ($status_list==$status) ? 'selected="selected"' : '';
													echo '<option '. $selected .' value="'. $status_list .'">'. $status_list .'</li>';
												} ?>
											</select>
										<?php } else {
											echo $status;
										} ?>
                                    </div>
                                </div>

                                <div class="row set-row-height">
                                    <div class="col-sm-5">Next Action:</div>
                                    <div class="col-sm-7">
                                        <select name="next_action" class="chosen-select-deselect form-control" id="nsid_<?= $row['salesid'] ?>">
                                            <option value=""></option><?php
                                            foreach ( explode(',', $next_actions) as $next_action ) {
                                                $selected = ($next_action==$row['next_action']) ? 'selected="selected"' : '';
                                                echo '<option '. $selected .' value="'. $next_action .'">'. $next_action .'</li>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row set-row-height">
                                    <div class="col-sm-5">Follow Up:</div>
                                    <div class="col-sm-7"><input type="text" name="follow_up" value="<?= $row['new_reminder'] ?>" class="form-control datepicker" onchange="changeLeadFollowUpDate(this);" id="fsid_<?= $row['salesid'] ?>" /></div>
                                </div>
                            </div><?php
                        } ?>
                    <?php } else { ?>
                        <div class="info-block-detail">No <?= strtolower($status); ?> sales leads.</div><?php
                    } ?>
                </div>
            </div>
        </div><?php
    } ?>
    <div class="clearfix"></div>
</div><!-- .main-screen-white -->