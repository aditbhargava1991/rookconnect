<?php
// Privileges History
include ('../include.php');
checkAuthorised('security');
error_reporting(0);
?>
<script>
</script>
</head>
<body style="min-height:0px;">
	<?php
	echo "<h1>{$_GET['title']} Changes</h1>";
	$sql = "SELECT privileges, contact, date_time, `tile` FROM security_privileges_log WHERE tile='{$_GET['tile_name']}' OR '{$_GET['tile_name']}'='' AND level='{$_GET['level']}' AND type='staff'";
	$history = mysqli_query($dbc, $sql);
	echo "<p>";
	while($row = mysqli_fetch_array($history)) {
		switch($row['tile']) {
			case 'archiveddata': $tile_name = 'Archived Data'; break;
			case 'ffmsupport': $tile_name = 'FFM Support'; break;
			case 'helpdesk': $tile_name = 'Helpdesk'; break;
			case 'security': $tile_name = 'Security'; break;
			case 'software_config': $tile_name = 'Software Settings'; break;
			case 'staff': $tile_name = 'Staff'; break;
			case 'agenda_meeting': $tile_name = 'Agenda & Meetings'; break;
			case 'checklist': $tile_name = 'Checklist'; break;
			case 'client_documents': $tile_name = 'Client Documents'; break;
			case 'contacts': $tile_name = 'Contacts'; break;
			case 'documents': $tile_name = 'Documents'; break;
			case 'internal_documents': $tile_name = 'Internal Documents'; break;
			case 'passwords': $tile_name = 'Passwords'; break;
			case 'profile': $tile_name = 'Profile'; break;
			case 'emp_handbook': $tile_name = 'Employee Handbook'; break;
			case 'how_to_guide': $tile_name = 'How to Guide'; break;
			case 'hr': $tile_name = 'HR'; break;
			case 'incident_report': $tile_name = INC_REP_TILE; break;
			case 'ops_manual': $tile_name = 'Operations Manual'; break;
			case 'orientation': $tile_name = 'Orientation'; break;
			case 'policy_procedure': $tile_name = 'Policy & Procedure'; break;
			case 'calllog': $tile_name = 'Cold Call'; break;
			case 'budget': $tile_name = 'Budget'; break;
			case 'gao': $tile_name = 'Goals & Objectives'; break;

			//For Clinicace
			case 'appointment_calendar': $tile_name = 'Appointment Calendar'; break;
			case 'booking': $tile_name = 'Booking'; break;
			case 'check_in': $tile_name = 'Check In'; break;
			case 'reactivation': $tile_name = 'Reactivation'; break;
			case 'check_out': $tile_name = 'Check Out'; break;
			case 'treatment_charts': $tile_name = 'Treatment Charts'; break;
			case 'accounts_receivables': $tile_name = 'Accounts Receivable'; break;
			case 'therapist': $tile_name = 'Therapists'; break;
			case 'treatment': $tile_name = 'Treatment'; break;
			case 'exercise_library': $tile_name = 'Exercise Library'; break;
			case 'confirmation': $tile_name = 'Appt. Confirmation'; break;
			case 'goals_compensation': $tile_name = 'Goals & Compensation'; break;
			case 'crm': $tile_name = 'Crm'; break;
			case 'policies': $tile_name = 'Policies'; break;
			case 'employee_handbook': $tile_name = 'Employee Handbook'; break;
			//End for Clinicace

			case 'infogathering': $tile_name = 'Information Gathering'; break;
			case 'marketing_material': $tile_name = 'Marketing Material'; break;
			case 'sales': $tile_name = 'Sales'; break;
			case 'sales_order': $tile_name = SALES_ORDER_NOUN; break;
			case 'assets': $tile_name = 'Assets'; break;
			case 'equipment': $tile_name = 'Equipment'; break;
			case 'inventory': $tile_name = 'Inventory'; break;
			case 'material': $tile_name = 'Material'; break;
			case 'communication': $tile_name = 'Communication'; break;
			case 'email_communication': $tile_name = 'Email Communication'; break;
			case 'newsboard': $tile_name = 'News Board'; break;
			case 'scrum': $tile_name = 'Scrum'; break;
			case 'tasks': $tile_name =TASK_TILE; break;
			//case 'tasks_updated': $tile_name = 'Tasks (Updated)'; break;
			case 'estimate': $tile_name = ESTIMATE_TILE; break;
			case 'quote': $tile_name = 'Quote'; break;
			case 'field_ticket_estimates': $tile_name = 'Field Ticket Estimates'; break;
			case 'driving_log': $tile_name = 'Driving Log'; break;
			case 'safety': $tile_name = 'Safety'; break;
			case 'addendum': $tile_name = 'Addendum Projects'; break;
			case 'addition': $tile_name = 'Addition Projects'; break;
			case 'field_job': $tile_name = 'Field Jobs'; break;
			case 'project': $tile_name = (PROJECT_TILE=='Projects' ? "Project" : PROJECT_TILE); break;
			case 'custom': $tile_name = 'Custom'; break;
			case 'labour': $tile_name = 'Labour'; break;
			case 'package': $tile_name = 'Packages'; break;
			case 'products': $tile_name = 'Products'; break;
			case 'promotion': $tile_name = 'Promotions'; break;
			case 'rate_card': $tile_name = 'Rate Cards'; break;
			case 'services': $tile_name = 'Services'; break;
			case 'assembly': $tile_name = 'Assembly Projects'; break;
			case 'business_development': $tile_name = 'Business Development Projects'; break;
			case 'internal': $tile_name = 'Internal Projects'; break;
			case 'manufacturing': $tile_name = 'Manufacturing Projects'; break;
			case 'marketing': $tile_name = 'Marketing Projects'; break;
			case 'process_development': $tile_name = 'Process Development Projects'; break;
			case 'rd': $tile_name = 'R&D Projects'; break;
			case 'sred': $tile_name = 'SR&ED Projects'; break;
			case 'daysheet': $tile_name = 'Daysheet'; break;
			case 'punch_card': $tile_name = 'Punch Card'; break;
			case 'ticket': $tile_name = TICKET_TILE; break;
			case 'time_tracking': $tile_name = 'Time Tracking'; break;
			case 'work_order': $tile_name = 'Work Order'; break;
			case 'expense': $tile_name = 'Expense'; break;
			case 'pos': $tile_name = POS_ADVANCE_TILE; break;
			case 'purchase_order': $tile_name = 'Purchase Order'; break;
			case 'vpl': $tile_name = 'Vendor Price List'; break;
			case 'gantt_chart': $tile_name = 'Gantt Chart'; break;
			case 'report': $tile_name = 'Report'; break;
			case 'charts': $tile_name = 'Charts'; break;
			case 'daily_log_notes': $tile_name = 'Daily Log Notes'; break;
			case 'timesheet': $tile_name = 'Timesheet'; break;
			default: $tile_name = ''; break;
		}
		$change = "$tile_name Tile ";
		if(strpos($row['privileges'], '*hide*') !== false) {
			$change .= "hidden";
		}
		else if($row['privileges'] == '*view_use_add_edit_delete*') {
			$change .= "granted use";
		}
		else if($row['privileges'] == '*view_use_add_edit_delete*configure*') {
			$change .= "granted use and settings access";
		}
		else if($row['privileges'] == '*configure*') {
			$change .= "granted setting access";
		}
		else if($row['privileges'] == '*') {
			$change .= "shown";
		}
		$change .= " by {$row['contact']} on {$row['date_time']}<br />\n";
		echo $change;
	}
	echo "</p>";
	?>
</body>
