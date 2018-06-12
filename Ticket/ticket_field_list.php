<?php // This function is used to convert field names or configurations to headings. These should be in the order they should appear in the import / export spreadsheet, and on the page.
function ticket_field_name($string = '', $col = 0) {
	$ticket_field_list = [ [ 'REQUIRED', 'Ticket ID', 'ticketid' ],
		'ticket_type' => [ 'REQUIRED', TICKET_NOUN.' Type', 'ticket_type' ],
		[ 'REQUIRED', 'Status', 'status' ],
		[ 'PI Business', BUSINESS_CAT.' ID', 'businessid' ],
		[ 'PI Business', BUSINESS_CAT.' Name', 'businessid_name' ],
		[ 'PI Name', 'Customer ID', 'clientid' ],
		[ 'PI Name', 'Customer Name', 'clientid_name' ],
		[ 'PI AFE', 'AFE #', 'afe_number' ],
		[ 'PI Project', PROJECT_NOUN.' ID', 'projectid' ],
		[ 'PI Pieces', 'Piece Work', 'piece_work' ],
		[ 'PI Sites', 'Site ID', 'siteid' ],
		[ 'PI Sales Order', 'Sales Order #', 'salesorderid' ],
		[ 'PI Invoice', 'Invoice #', 'salesorderid' ],
		[ 'PI Order', 'Order #', 'salesorderid' ],
		[ 'PI WTS Order', 'WTS Order #', 'salesorderid' ],
		[ 'PI Purchase Order', 'Purchase Order #', 'purchase_order' ],
		[ 'PI Customer Order', 'Customer Order #', 'customer_order_num' ],
		[ 'PI Work Order', 'Work Order #', 'heading' ],
		[ 'PI Invoiced Out', 'Invoiced (1/0)', 'invoiced' ],
		[ 'PI Cross Ref', 'Cross Reference #', 'notes' ],
		[ 'PI Date of Entry', 'Date of Entry', 'last_updated_date' ],
		[ 'PI Date of Entry', 'Time of Entry', 'last_updated_time' ],
		[ 'PI Agent', 'Additional Project Contact ID', 'agentid' ],
		[ 'PI Agent', 'Additional Project Contact Name', 'agentid_name' ],
		[ 'Detail Business', BUSINESS_CAT.' ID', 'businessid' ],
		[ 'Detail Business', BUSINESS_CAT.' Name', 'businessid_name' ],
		[ 'Detail Contact', 'Contact ID', 'clientid' ],
		[ 'Detail Contact', 'Contact Name', 'clientid_name' ],
		[ 'Details', PROJECT_NOUN.' ID', 'projectid' ],
		[ 'Detail Heading', TICKET_NOUN.' Name', 'heading' ],
		[ 'Detail Date', 'Date', 'to_do_date' ],
		[ 'Detail Staff', 'Staff ID', 'contactid' ],
		[ 'Detail Staff', 'Staff Name', 'contactid_name' ],
		[ 'Detail Staff Times', 'Start Time', 'start_time' ],
		[ 'Detail Staff Times', 'End Time', 'end_time' ],
		[ 'Detail Member Times', 'Member Drop Off', 'member_start_time' ],
		[ 'Detail Member Times', 'Member Pick Up', 'member_end_time' ],
		[ 'Detail Times', 'Start Time', 'to_do_start_time' ],
		[ 'Detail Times', 'End Time', 'to_do_end_time' ],
		[ 'Detail Times', 'Duration', 'max_time' ],
		[ 'Detail Notes', 'Notes', 'notes' ],
		[ 'Detail Max Capacity', 'Max Capacity', 'max_capacity' ],
		[ 'Path & Milestone', PROJECT_NOUN.' Milestone', 'milestone_timeline' ],
		[ 'Fees', 'Fee Name (comma separated)', 'fee_name' ],
		[ 'Fees', 'Fee Amount (comma separated)', 'fee_amt' ],
		[ 'Fees', 'Fee Description (comma separated)', 'fee_details' ],
		[ 'Location', 'Location Name', 'location' ],
		[ 'Task Extra Billing', 'Assigned Tasks (comma separated)', 'task_available' ],
		[ 'Ticket Details', 'Service ID', 'serviceid' ],
		[ 'Ticket Details', TICKET_NOUN.' Heading', 'heading' ],
		[ 'Ticket Details', TICKET_NOUN.' Details', 'assign_work' ],
		[ 'Services', 'Service ID', 'serviceid' ],
		[ 'Services', 'Service Heading', 'heading' ],
		[ 'Services', 'Service Details', 'assign_work' ],
		[ 'Inventory General PO Item', 'Inventory PO Item', 'po_line', 'ticket_attached' ],
		[ 'Inventory General PO Line Item', 'Inventory PO Line Item', 'po_line', 'ticket_attached' ],
		[ 'Inventory General PO Line Read', 'Inventory PO Line Item', 'po_line', 'ticket_attached' ],
		[ 'Inventory Detail Customer Order', 'Detailed Inventory Customer Order', 'position_detail', 'ticket_attached' ],
		[ 'Inventory Detail PO Num', 'Detailed Inventory PO#', 'po_num', 'ticket_attached' ],
		[ 'Inventory Detail PO Item', 'Detailed Inventory PO Item', 'po_line_detail', 'ticket_attached' ],
		[ 'Inventory Detail PO Read', 'Detailed Inventory PO Item', 'po_line_detail', 'ticket_attached' ],
		[ 'Inventory Detail Quantity', 'Inventory Quantity', 'qty', 'ticket_attached' ],
		[ 'Inventory Detail Received', 'Inventory Received', 'received', 'ticket_attached' ],
		[ 'Inventory Detail Unique', 'Inventory Name', 'name', 'ticket_attached' ],
		[ 'Delivery Pickup', 'Delivery Name', 'location_name', 'ticket_schedule' ],
		[ 'Delivery Pickup', 'Delivery Client Name', 'client_name', 'ticket_schedule' ],
		[ 'Delivery Pickup', 'Delivery Address', 'address', 'ticket_schedule' ],
		[ 'Delivery Pickup', 'Delivery City', 'city', 'ticket_schedule' ],
		[ 'Delivery Pickup', 'Delivery Postal Code', 'postal_code', 'ticket_schedule' ],
		[ 'Delivery Pickup', 'Delivery Google Link', 'map_link', 'ticket_schedule' ],
		[ 'Delivery Pickup Phone', 'Delivery Phone', 'details', 'ticket_schedule' ],
		[ 'Delivery Pickup Type', 'Delivery Type', 'type', 'ticket_schedule' ],
		[ 'Delivery Pickup Date', 'Delivery Date', 'to_do_date', 'ticket_schedule' ],
		[ 'Delivery Pickup Date', 'Delivery Time', 'to_do_start_time', 'ticket_schedule' ],
		[ 'Delivery Pickup Timeframe', 'Delivery Available Start', 'start_available', 'ticket_schedule' ],
		[ 'Delivery Pickup Timeframe', 'Delivery Available End', 'end_available', 'ticket_schedule' ],
		[ 'Deliverable To Do', 'Assigned User ID (comma separated)', 'contactid' ],
		[ 'Deliverable To Do', 'Assigned User Name (comma separated)', 'contactid_name' ],
		[ 'Deliverables', 'Start Date', 'to_do_date' ],
		[ 'Deliverables', 'End Date', 'to_do_end_date' ],
		[ 'Deliverables', 'Start Time', 'to_do_start_time' ],
		[ 'Deliverables', 'End Time', 'to_do_end_time' ],
		[ 'Deliverable Internal', 'Internal QA User ID (comma separated)', 'internal_qa_contactid' ],
		[ 'Deliverable Internal', 'Internal QA User Name (comma separated)', 'internal_qa_contactid_name' ],
		[ 'Deliverable Internal', 'Internal QA Date', 'internal_qa_date' ],
		[ 'Deliverable Internal', 'Internal QA Start Time', 'internal_qa_start_time' ],
		[ 'Deliverable Internal', 'Internal QA End Time', 'internal_qa_end_time' ],
		[ 'Deliverable Customer', 'Customer QA Date', 'deliverable_date' ],
		[ 'Deliverable Customer', 'Customer QA Staff ID (comma separated)', 'deliverable_contactid' ],
		[ 'Deliverable Customer', 'Customer QA Staff Name (comma separated)', 'deliverable_contactid_name' ],
		[ 'Deliverable Customer', 'Customer QA Start Time', 'deliverable_start_time' ],
		[ 'Deliverable Customer', 'Customer QA End Time', 'deliverable_end_time' ],
		[ 'Timer', 'Estimated Time', 'max_time' ],
		[ 'Timer', 'Estimated QA Time', 'max_qa_time' ] ];
	
	$return_arr = [];
	if($string != '') {
		foreach($ticket_field_list as $arr) {
			if($arr[$col] == $string) {
				$return_arr[] = $arr;
			}
		}
	}
	return $return_arr;
}