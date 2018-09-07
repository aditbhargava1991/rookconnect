<?php include_once('../include.php');
ob_clean();
if(get_config($dbc, 'ticket_exclude_archive') == 'true') {
	$status_clause = " AND `tickets`.`status` NOT IN ('Archive','Archived') ";
}
$ticket_type = filter_var($_POST['ticket_type'],FILTER_SANITIZE_STRING);
if($ticket_type == 'ticket' && $_POST['ticket_tile'] != '') {
	$ticket_type = 'ticket_'.filter_var($_POST['ticket_tile'],FILTER_SANITIZE_STRING);
}
$ticket_sort = ' ORDER BY `tickets`.`ticketid` DESC';
switch(get_config($dbc, 'ticket_sorting')) {
	case 'label': $ticket_sort = ' ORDER BY `tickets`.`ticket_label` ASC'; break;
	case 'oldest': $ticket_sort = ' ORDER BY `tickets`.`ticketid` ASC'; break;
	case 'project': $ticket_sort = ' ORDER BY `tickets`.`projectid` DESC'; break;
	case 'to_do_date_desc': $ticket_sort = ' ORDER BY `tickets`.`to_do_date` DESC'; break;
	case 'to_do_date_asc': $ticket_sort = ' ORDER BY `tickets`.`to_do_date` ASC'; break;
}
$ticket_fields = '';
$ticket_filter = '';
$ticket_join = '';
$ticket_group = '';
$revisions = 0;
$file_name = '';
if(strpos($ticket_type,'ticket_') !== FALSE) {
	$ticket_filter = " AND '".substr($ticket_type,7)."' IN (IFNULL(NULLIF(`tickets`.`ticket_type`,''),'other'),'')";
} else if(strpos($ticket_type,'form_') !== FALSE) {
	$ticket_filter = " AND `ticket_pdf_field_values`.`pdf_type`='".substr($ticket_type,5)."' AND `ticket_pdf_field_values`.`deleted`=0";
	$ticket_join = "LEFT JOIN `ticket_pdf_field_values` ON `tickets`.`ticketid`=`ticket_pdf_field_values`.`ticketid` LEFT JOIN (SELECT `ticketid`, `pdf_type`, MAX(`revision`) `last_revision` FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`) `revisions` ON `tickets`.`ticketid`=`revisions`.`ticketid` AND `ticket_pdf_field_values`.`pdf_type`=`revisions`.`pdf_type`";
	$form = $dbc->query("SELECT `pdf_name`, `revisions` FROM `ticket_pdf` WHERE `id`='".substr($ticket_type,5)."'")->fetch_assoc();
	$revisions = $form['revisions'];
	$file_name = config_safe_str($form['pdf_name']);
	if($revisions > 0) {
		$ticket_fields = ", `ticket_pdf_field_values`.`revision`, `revisions`.`last_revision`";
		$ticket_group = ", `ticket_pdf_field_values`.`revision` ";
	} else {
		$ticket_fields = ", MAX(`ticket_pdf_field_values`.`revision`) `revision`";
	}
	$ticket_sort = 'ORDER BY `ticket_pdf_field_values`.`id` DESC';
}
$match_business = '';
if(!empty(MATCH_CONTACTS)) {
	$match_business .= " AND `tickets`.`businessid` IN (".MATCH_CONTACTS.")";
}
$ticket_sql = "SELECT `tickets`.*, `ticket_attached`.`po_numbers`, `ticket_attached`.`customer_orders`, `project`.`projecttype`, `project`.`project_name` $ticket_fields FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` LEFT JOIN (SELECT GROUP_CONCAT(DISTINCT `position` SEPARATOR '#*#') `customer_orders`, GROUP_CONCAT(DISTINCT `po_num` SEPARATOR '#*#') `po_numbers`, `ticketid` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='inventory' GROUP BY `ticketid`) `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` ".$ticket_join." WHERE `tickets`.`deleted`=0 ".$ticket_filter.$status_clause.$match_business." GROUP BY `tickets`.`ticketid`".$ticket_group.$ticket_sort;
$ticket_list = mysqli_query($dbc, $ticket_sql);
$tickets = [];
$_SERVER['page_load_info'] .= 'Create List of Tickets: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
while($ticket = mysqli_fetch_assoc($ticket_list)) {
	$label = get_ticket_label($dbc,$ticket,$ticket['projecttype'],$ticket['project_name']).($revisions > 0 ? ' Revision #'.$ticket['revision'].' of '.$ticket['last_revision'] : '');
	$file = '';
	if($file_name != '') {
		$file = '../Ticket/ticket_pdf_custom.php?ticketid='.$ticket['ticketid'].'&form='.substr($ticket_type,5).'&revision='.$ticket['revision'];;
		// $file = '../Ticket/download/'.$file_name.'_'.$ticket['revision'].'_'.$ticket['ticketid'].'.pdf';
		// if(!file_exists($file)) {
			// $file = '../Ticket/download/'.$file_name.'_'.$ticket['ticketid'].'.pdf';
		// }
	}
	$tickets[] = ['id'=>$ticket['ticketid'],'staff'=>explode(',',$ticket['contactid'].','.$ticket['internal_qa_contactid'].','.$ticket['deliverable_contactid']),'internal_qa'=>explode(',',$ticket['internal_qa_contactid']),'deliverable_id'=>explode(',',$ticket['deliverable_contactid']),'file'=>$file,'revision'=>$ticket['revision'],'created_by'=>$ticket['created_by'],'po'=>$ticket['purchase_order'].'#*#'.$ticket['po_numbers'],'customer_orders'=>$ticket['customer_order_num'].'#*#'.$ticket['customer_orders'],'business'=>$ticket['businessid'],'contact'=>explode(',',$ticket['clientid']),'project'=>$ticket['projecttype'],'status'=>$ticket['status'],'key'=>$ticket['to_do_date'].'-'.$label.' '.$ticket['purchase_order'].' '.$ticket['po_numbers'].' '.$ticket['customer_order_num'].' '.$ticket['customer_orders'],'label'=>$label,'type'=>$ticket_type];
}
$_SERVER['page_load_info'] .= 'Output Ticket List: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
echo json_encode($tickets);
?>
