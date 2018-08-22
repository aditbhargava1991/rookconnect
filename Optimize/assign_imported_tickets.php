<?php include('../include.php');
if(!empty($_GET['ids'])) {
	echo '<h3>'.TICKET_TILE.'</h3>';
	foreach(explode(',',$_GET['ids']) as $ticket) {
		if($ticket > 0) {
			$ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticket'")->fetch_assoc();
			$unbooked_html = '<span class="block-item ticket gap" style="position: relative; background-color: '.$ticket_colour.'; border: 1px solid rgba(0,0,0,0.5); color: #000; margin: 0.25em 0 0; display:block; float: left; width: 30em;" data-ticketid="'.$ticket['ticketid'].'" title="View '.TICKET_NOUN.'">
				<div class="drag-handle full-height" title="Drag Me!">
					<img class="drag-handle black-color inline-img pull-right" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" />
				</div>
                <img src="../img/icons/ROOK-trash-icon.png" class="inline-img cursor-hand pull-right margin-horiz-2" onclick="archive(this); return false;" title="Archive '.TICKET_NOUN.'">
				<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'" data-ticketid="'.$ticket['ticketid'].'" onclick=\'
				overlayIFrameSlider(this.href+"&calendar_view=true","auto",true,true); return false;\' style="text-decoration: none; display: block;">
				'.get_ticket_label($dbc, $ticket).($ticket['sub_label'] != '' ? '-'.$ticket['sub_label'] : '').($ticket['scheduled_lock'] > 0 ? '<img class="inline-img" title="Time has been Locked" src="../img/icons/lock.png">' : '').'<br />
				'.(in_array('project',$calendar_ticket_card_fields) ? PROJECT_NOUN.' #'.$ticket['projectid'].' '.$ticket['project_name'].'<br />' : '').'
				'.(in_array('customer',$calendar_ticket_card_fields) ? 'Customer: '.$customer.'<br />' : '').'
				'.(in_array('assigned',$calendar_ticket_card_fields) ? 'Assigned Staff: '.$assigned_staff.'<br />' : '').'
				'.(in_array('start_date',$calendar_ticket_card_fields) && !empty($ticket['to_do_date']) ? 'Date: '.$ticket['to_do_date'] : '');
			$unbooked_html .= '<h4>Addresses</h4>';
			$deliveries = $dbc->query("SELECT `address`, `city` FROM `ticket_schedule` WHERE `ticketid`='".$ticket['ticketid']."' ORDER BY `id`");
			while($delivery = $deliveries->fetch_assoc()) {
				$unbooked_html .= $delivery['address'].', '.$delivery['city'].'<br />';
			}
			$unbooked_html .= '</a></span>';
			echo $unbooked_html;
		}
	}
}
if(!empty($_GET['unassign_type'])) {
	$type = filter_var($_GET['unassign_type'],FILTER_SANITIZE_STRING);
	$date = filter_var($_GET['date'],FILTER_SANITIZE_STRING);
	$filter = '';
	$id_list = explode(',',filter_var($_GET['ids'],FILTER_SANITIZE_STRING));
	$filter = "`ticketid` NOT IN ('".implode("','",$id_list)."')";
	$sql = "SELECT * FROM `tickets` WHERE `ticket_type`='$type' AND `ticketid` NOT IN ('".implode("','",$_GET['ids'])."') AND `ticketid` NOT IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `deleted`=0 AND `equipmentid` > 0 GROUP BY `ticketid`) AND `ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `deleted`=0 AND IFNULL(`address`,'') != '' AND '$date' IN (`to_do_date`,'') GROUP BY `ticketid`) AND `deleted`=0 AND `status` NOT IN ('Archive')";
	$query = $dbc->query($sql);
	if($query->num_rows > 0) {
		echo '<div class="clearfix"></div><br /><h3>Unassigned '.TICKET_TILE.'</h3>';
		while($ticket = $query->fetch_assoc()) {
			$unbooked_html = '<span class="block-item ticket gap" style="position: relative; background-color: '.$ticket_colour.'; border: 1px solid rgba(0,0,0,0.5); color: #000; margin: 0.25em 0 0; display:block; float: left; width: 30em;" data-ticketid="'.$ticket['ticketid'].'" title="View '.TICKET_NOUN.'">
				<div class="drag-handle full-height" title="Drag Me!">
					<img class="drag-handle black-color inline-img pull-right" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" />
				</div>
				<img src="../img/icons/ROOK-trash-icon.png" class="inline-img cursor-hand pull-right margin-horiz-2" onclick="archive(this); return false;" title="Archive '.TICKET_NOUN.'">
				<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'" data-ticketid="'.$ticket['ticketid'].'" onclick=\'
				overlayIFrameSlider(this.href+"&calendar_view=true","auto",true,true); return false;\' style="text-decoration: none; display: block;">
				'.get_ticket_label($dbc, $ticket).($ticket['sub_label'] != '' ? '-'.$ticket['sub_label'] : '').($ticket['scheduled_lock'] > 0 ? '<img class="inline-img" title="Time has been Locked" src="../img/icons/lock.png">' : '').'<br />
				'.(in_array('project',$calendar_ticket_card_fields) ? PROJECT_NOUN.' #'.$ticket['projectid'].' '.$ticket['project_name'].'<br />' : '').'
				'.(in_array('customer',$calendar_ticket_card_fields) ? 'Customer: '.$customer.'<br />' : '').'
				'.(in_array('assigned',$calendar_ticket_card_fields) ? 'Assigned Staff: '.$assigned_staff.'<br />' : '').'
				'.(in_array('start_date',$calendar_ticket_card_fields) && !empty($ticket['to_do_date']) ? 'Date: '.$ticket['to_do_date'] : '');
			$unbooked_html .= '<h4>Addresses</h4>';
			$deliveries = $dbc->query("SELECT `address`, `city` FROM `ticket_schedule` WHERE `ticketid`='".$ticket['ticketid']."' ORDER BY `id`");
			while($delivery = $deliveries->fetch_assoc()) {
				$unbooked_html .= $delivery['address'].', '.$delivery['city'].'<br />';
			}
			$unbooked_html .= '</a></span>';
			echo $unbooked_html;
		}
	}
} ?>
<script>
function archive(img) {
    if(confirm('Are you sure you want to archive this <?= TICKET_NOUN ?>?')) {
        var block = $(img).closest('.block-item');
        var ticketid = block.data('ticketid');
        block.hide();
        $.post('optimize_ajax.php?action=archive', { id: ticketid, field: 'ticketid', table: 'tickets' });
    }
}
</script>