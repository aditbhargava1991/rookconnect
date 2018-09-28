<?php
/*
Archived Data Listing
*/
include ('../include.php');
error_reporting(0);

/* Pagination Counting */
$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
    $pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

if($_GET['searching'] == 'true') {
	$rowsPerPage = 9999999999999;
	$offset = 0;
}
?>
<!-- <script src="js/jquery.cookie.js"></script> -->
<script>
var loadSearchResults = '';
$(document).ready(function() {
	$('.live-search-list2 tr').each(function(){
		$(this).attr('data-search-term', $(this).text().toLowerCase());
	});
	$('.live-search-box2').on('keyup', function() {
		var searchTerm = $(this).val().toLowerCase();
		var searching = '';
		if(searchTerm != '') {
			searching = '&searching=true';
		}
		clearTimeout(loadSearchResults);
		loadSearchResults = setTimeout(function() {
			$.ajax({
				url: '../Archived/archived_data.php?archive_type=<?= $_GET['archive_type'] ?>'+searching,
				method: 'GET',
				dataType: 'html',
				success: function(response) {
					var html = $(response).find('#no-more-tables');
					$(html).find('tr').each(function(){
						$(this).attr('data-search-term', $(this).text().toLowerCase());
					});
					$(html).find('tr').each(function(){

						if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
							$(this).show();
						} else if($(this).parents('thead').length <= 0) {
							if($(this).hasClass('dont-hide')) {
							} else { $(this).remove(); }
						}

					});

					$('#no-more-tables').html(html);
				}
			});
		}, 500);
	});
});
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('archiveddata');
$edit_access = vuaed_visible_function($dbc, 'archiveddata');
$archive = $_GET['archive_type'] = isset($_GET['archive_type']) ? $_GET['archive_type'] : 'contacts';
switch($archive) {
	case "inventory" : $current_tab = "inventory"; break;
	case "sales" : $current_tab = "sales"; break;
	case "equipment" : $current_tab = "equipment"; break;
	case "bid" : $current_tab = "bid"; break;
	case "staff" : $current_tab = "staff"; break;
	case "shop_work_order" : $current_tab = "shop_work_order"; break;
	case "certificate" : $current_tab = "certificate"; break;
	case "guide" : $current_tab = "guide"; break;
	case "hr" : $current_tab = "hr"; break;
	case "eh" : $current_tab = "eh"; break;
	case "manual" : $current_tab = "manual"; break;
	case "pp" : $current_tab = "pp"; break;
	case "checklist" : $current_tab = "checklist"; break;
	case "cd" : $current_tab = "cd"; break;
	case "id" : $current_tab = "id"; break;
	case "passwords" : $current_tab = "passwords"; break;
	case "documents" : $current_tab = "documents"; break;
	case "helpdesk" : $current_tab = "helpdesk"; break;
	case "field_jobs" : $current_tab = "field_jobs"; break;
	case "field_sites" : $current_tab = "field_sites"; break;
	case "field_foreman" : $current_tab = "field_foreman"; break;
	case "field_po" : $current_tab = "field_po"; break;
	case "field_work_ticket" : $current_tab = "field_work_ticket"; break;
	case "field_invoices" : $current_tab = "field_invoices"; break;
	case "project_workflow" : $current_tab = "project_workflow"; break;
	case "asset" : $current_tab = "asset"; break;
	case "equipment" : $current_tab = "equipment"; break;
	case "inventory" : $current_tab = "inventory"; break;
	case "material" : $current_tab = "material"; break;
	case "safety" : $current_tab = "safety"; break;
	case "tickets" : $current_tab = "tickets"; break;
	case "daysheet" : $current_tab = "daysheet"; break;
	case "time_tracking" : $current_tab = "time_tracking"; break;
	case "estimate" : $current_tab = "estimate"; break;
	case "quote" : $current_tab = "quote"; break;
	case "email_communication" : $current_tab = "email_communication"; break;
	case "newsboard" : $current_tab = "newsboard"; break;
	case "check_out" : $current_tab = "check_out"; break;
    case "match" : $current_tab = "match"; break;
	case "expense" : $current_tab = "expense"; break;
	case "pos" : $current_tab = "pos"; break;
	case "sales_order" : $current_tab = "sales_order"; break;
	case "purchase_order" : $current_tab = "purchase_order"; break;
	case "vpl" : $current_tab = "vpl"; break;
	case "budget" : $current_tab = "budget"; break;
	case "infogathering" : $current_tab = "infogathering"; break;
	case "marketing_material" : $current_tab = "marketing_material"; break;
	case "newsboard" : $current_tab = "newsboard"; break;
    case "staff_documents" : $current_tab = "staff_documents"; break;
	case "treatment" : $current_tab = "treatment"; break;
	case "reminder" : $current_tab = "reminder"; break;
	case "custom" : $current_tab = "custom"; break;
	case "labour" : $current_tab = "labour"; break;
	case "package" : $current_tab = "package"; break;
	case "products" : $current_tab = "products"; break;
	case "promotion" : $current_tab = "promotion"; break;
	case "service" : $current_tab = "service"; break;
	case "charts" : $current_tab = "charts"; break;
	case "day_program" : $current_tab = "day_program"; break;
	case "fund_development" : $current_tab = "fund_development"; break;
	case "medication" : $current_tab = "medication"; break;
	case "individual_support_plan" : $current_tab = "individual_support_plan"; break;
	case "social_story" : $current_tab = "social_story"; break;
	case "preformance_review" : $current_tab = "preformance_review"; break;
	default: $current_tab = "contacts"; break;
}
?>
<div class="container">
    <div class="row">
		<div class="">
		    <ul id="" class="tab-links nav nav-pills">
				<?php if(tile_visible($dbc, 'contacts') == 1 || tile_visible($dbc, 'contacts_inbox')):
					if(empty($_GET['type'])) {
						$_GET['type'] = 'contacts';
					} ?>
					<li <?= $_GET['archive_type'] == 'contacts' && $_GET['type'] == 'contacts' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=contacts&type=contacts">Contacts</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'contacts3') == 1):
					if(empty($_GET['type'])) {
						$_GET['type'] = 'contacts3';
					} ?>
					<li <?= $_GET['archive_type'] == 'contacts' && $_GET['type'] == 'contacts3' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=contacts&type=contacts3">Contacts</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'client_info') == 1):
					if(empty($_GET['type'])) {
						$_GET['type'] = 'clientinfo';
					} ?>
					<li <?= $_GET['archive_type'] == 'contacts' && $_GET['type'] == 'clientinfo' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=contacts&type=clientinfo">Client Information</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'contacts_rolodex') == 1):
					if(empty($_GET['type'])) {
						$_GET['type'] = 'contactsrolodex';
					} ?>
					<li <?= $_GET['archive_type'] == 'contacts' && $_GET['type'] == 'contactsrolodex' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=contacts&type=contactsrolodex">Contacts Rolodex</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'members') == 1):
					if(empty($_GET['type'])) {
						$_GET['type'] = 'members';
					} ?>
					<li <?= $_GET['archive_type'] == 'contacts' && $_GET['type'] == 'members' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=contacts&type=members">Members</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'vendors') == 1):
					if(empty($_GET['type'])) {
						$_GET['type'] = 'vendors';
					} ?>
					<li <?= $_GET['archive_type'] == 'contacts' && $_GET['type'] == 'vendors' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=contacts&type=vendors"><?= VENDOR_TILE ?></a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'staff') == 1): ?>
					<li <?= $_GET['archive_type'] == 'staff' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=staff">Staff</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'field_job') == 1):
					$field_job_tabs = get_config($dbc, 'field_job_tabs');
					if(strpos($field_job_tabs,',sites,') !== false) { ?>
						<li <?= $_GET['archive_type'] == 'field_sites' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=field_sites">Field Sites</a></li>
					<?php }
					if(strpos($field_job_tabs,',jobs,') !== false) { ?>
						<li <?= $_GET['archive_type'] == 'field_jobs' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=field_jobs">Field Jobs</a></li>
					<?php }
					if(strpos($field_job_tabs,',foreman,') !== false) { ?>
						<li <?= $_GET['archive_type'] == 'field_foreman' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=field_foreman">Field Foreman Sheets</a></li>
					<?php }
					if(strpos($field_job_tabs,',po,') !== false) { ?>
						<li <?= $_GET['archive_type'] == 'field_po' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=field_po">Field PO</a></li>
					<?php }
					if(strpos($field_job_tabs,',work,') !== false) { ?>
						<li <?= $_GET['archive_type'] == 'field_work_ticket' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=field_work_ticket">Field Work Tickets</a></li>
					<?php }
					if(strpos($field_job_tabs,',invoice,') !== false) { ?>
						<li <?= $_GET['archive_type'] == 'field_invoices' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=field_invoices">Field Invoices</a></li>
					<?php } ?>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'shop_work_orders') == 1): ?>
					<li <?= $_GET['archive_type'] == 'shop_work_order' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=shop_work_order">Shop Work Orders</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'certificate') == 1): ?>
					<li <?= $_GET['archive_type'] == 'certificate' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=certificate">Certificates</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'how_to_guide') == 1): ?>
					<li <?= $_GET['archive_type'] == 'guide' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=guide">How to Guide</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'hr') == 1): ?>
					<li <?= $_GET['archive_type'] == 'hr' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=hr">HR</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'emp_handbook') == 1): ?>
					<li <?= $_GET['archive_type'] == 'eh' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=eh">Employee Handbook</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'ops_manual') == 1): ?>
					<li <?= $_GET['archive_type'] == 'manual' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=manual">Operational Manual</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'policy_procedure') == 1): ?>
					<li <?= $_GET['archive_type'] == 'pp' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=pp">Policy & Procedure</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'checklist') == 1): ?>
					<li <?= $_GET['archive_type'] == 'checklist' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=checklist">Checklist</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'client_documents') == 1): ?>
					<li <?= $_GET['archive_type'] == 'cd' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=cd">Client Documents</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'internal_documents') == 1): ?>
					<li <?= $_GET['archive_type'] == 'id' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=id">Internal Documents</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'passwords') == 1): ?>
					<li <?= $_GET['archive_type'] == 'passwords' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=passwords">Passwords</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'documents') == 1): ?>
					<li <?= $_GET['archive_type'] == 'documents' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=documents">Documents</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'helpdesk') == 1): ?>
					<li <?= $_GET['archive_type'] == 'helpdesk' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=helpdesk">Help Desk</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'project_workflow') == 1): ?>
					<li <?= $_GET['archive_type'] == 'project_workflow' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=project_workflow">Project Workflow</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'assets') == 1): ?>
					<li <?= $_GET['archive_type'] == 'asset' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=asset">Assets</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'equipment') == 1): ?>
					<li <?= $_GET['archive_type'] == 'equipment' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=equipment">Equipment</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'inventory') == 1): ?>
					<li <?= $_GET['archive_type'] == 'inventory' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=inventory">Inventory</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'material') == 1): ?>
					<li <?= $_GET['archive_type'] == 'material' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=material">Materials</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'safety') == 1): ?>
					<li <?= $_GET['archive_type'] == 'safety' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=safety">Safety Checklist</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'ticket') == 1): ?>
					<li <?= $_GET['archive_type'] == 'tickets' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=tickets"><?= TICKET_TILE ?></a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'daysheet') == 1): ?>
					<li <?= $_GET['archive_type'] == 'daysheet' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=daysheet">Planner</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'time_tracking') == 1): ?>
					<li <?= $_GET['archive_type'] == 'time_tracking' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=time_tracking">Time Tracking</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'estimate') == 1): ?>
					<li <?= $_GET['archive_type'] == 'estimate' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=estimate"><?= ESTIMATE_TILE ?></a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'quote') == 1): ?>
					<li <?= $_GET['archive_type'] == 'quote' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=quote">Quote</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'email_communication') == 1): ?>
					<li <?= $_GET['archive_type'] == 'email_communication' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=email_communication">Email Communication</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'newsboard') == 1): ?>
					<li <?= $_GET['archive_type'] == 'newsboard' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=newsboard">News Board</a></li>
				<?php endif; ?>
				<?php if(tile_visible($dbc, 'check_out') == 1): ?>
					<li <?= $_GET['archive_type'] == 'check_out' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=check_out">Checkout</a></li>
				<?php endif; ?>
                <?php if(tile_visible($dbc, 'match') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'match' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=match">Match</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'expense') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'expense' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=expense">Expenses</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'pos') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'pos' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=pos"><?= POS_ADVANCE_TILE ?></a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'purchase_order') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'purchase_order' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=purchase_order">Purchase Orders</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'vpl') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'vpl' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=vpl">Vendors Price List</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'budget') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'budget' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=budget">Budget</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'infogathering') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'infogathering' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=infogathering">Information Gathering</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'marketing_material') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'marketing_material' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=marketing_material">Marketing Material</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'sales_order') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'sales_order' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=sales_order"><?= SALES_ORDER_TILE ?></a></li>
                <?php endif; ?>
                <?php if(tile_visible($dbc, 'staff_documents') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'staff_documents' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=staff_documents">Staff Documents</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'treatment_charts') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'treatment_charts' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=treatment">Treatment</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'staff') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'reminder' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=reminder">Reminders</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'custom') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'custom' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=custom">Custom</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'labour') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'labour' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=labour">Labour</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'package') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'package' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=package">Package</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'products') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'products' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=products">Products</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'promotion') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'promotion' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=promotion">Promotion</a></li>
                <?php endif; ?>
				<?php if(tile_visible($dbc, 'services') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'service' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=service">Services</a></li>
                <?php endif; ?>
				<?php //if(tile_visible($dbc, 'charts') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'charts' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=charts">Charts</a></li>
                <?php //endif; ?>
				<?php if(tile_visible($dbc, 'day_program') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'day_program' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=day_program">Day Program</a></li>
                <?php endif; ?>
				<?php //if(tile_visible($dbc, 'fund_development') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'fund_development' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=fund_development">Fund Development</a></li>
                <?php //endif; ?>
				<?php //if(tile_visible($dbc, 'medication') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'medication' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=medication">Medication</a></li>
                <?php //endif; ?>
				<?php //if(tile_visible($dbc, 'individual_support_plan') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'individual_support_plan' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=individual_support_plan">Individual Support Plan</a></li>
                <?php //endif; ?>
				<?php //if(tile_visible($dbc, 'social_story') == 1): ?>
                    <li <?= $_GET['archive_type'] == 'social_story' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=social_story">Social Story</a></li>
                <?php //endif; ?>
                	<li <?= $_GET['archive_type'] == 'performance_review' ? 'class="active"' : '' ?>><a href="archived_data.php?archive_type=preformance_review">Performance Reviews</a></li>

				<!-- currently not working -> <li><a href="archived_data.php?archive_type=sales">Sales</a></li> -->
				<!-- currently not working -> <li><a href="archived_data.php?archive_type=bid">Businesses</a></li> -->
				<!-- currently not working -> <li><a href="archived_data.php?archive_type=equipment">Equipment</a></li> -->
				<!-- currently not working -> <li><a href="archived_data.php?archive_type=staff">Staff</a></li> -->
			</ul>
		</div>

        <div class="live-search-list2">
			<?php if($current_tab =='contacts') { ?>
             <!-- Archived Contacts -->
            <div id="tab1" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
					<center><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search..." style='max-width:300px; margin-bottom:20px;'></center>
                    <div id="no-more-tables">
                        <?php
                        $tile_query = '';
                        if(!empty($_GET['type'])) {
                        	$tile_query = " AND `tile_name` = '".$_GET['type']."'";
                        }
                        $result = mysqli_query($dbc, "SELECT * FROM contacts WHERE category != 'Staff' and deleted = 1".$tile_query." ORDER BY date_of_archival DESC, contactid ASC LIMIT $offset, $rowsPerPage");
                        $query = "SELECT count(*) as numrows FROM contacts WHERE category != 'Staff' and deleted = 1".$tile_query." ORDER BY date_of_archival DESC, contactid ASC";

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        // Added Pagination //
                        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                        // Pagination Finish //
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr class='dont-hide'>
                                <th>Contact ID</th>
								<th>Category</th>
                                <th>Name</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
							if(decryptIt($row['name']) == NULL || decryptIt($row['name']) == '') {
								$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']);
							} else { $name = decryptIt($row['name']); }
                            echo '<tr>';
                            $contactid = $row['contactid'];
                            echo '<td data-title="Contact ID">' . $row['contactid'] . '</td>';
							echo '<td data-title="Category">' . $row['category'] . '</td>';
                            echo '<td data-title="Name">' . $name . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
	                        if($edit_access > 0) {
							echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&contactid='.$row['contactid'].'&category=contacts\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&contactid='.$row['contactid'].'&category=contacts\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
							}
                            echo '</tr>';
                        }

                        echo '</table>';
                        // Added Pagination //
                        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                        // Pagination Finish //
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived contacts -->
			<?php } ?>

			<?php if($current_tab =='staff') { ?>
             <!-- Archived Staff -->
            <div id="tab1" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
					<center><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search..." style='max-width:300px; margin-bottom:20px;'></center>
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM contacts WHERE category = 'Staff' and deleted = 1 ORDER BY date_of_archival DESC LIMIT $offset, $rowsPerPage");
                        $query = "SELECT count(*) as numrows FROM contacts WHERE category = 'Staff' and deleted = 1 ORDER BY date_of_archival DESC";

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        // Added Pagination //
                        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                        // Pagination Finish //
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr class='dont-hide'>
                                <th>Contact ID</th>
								<th>Category</th>
                                <th>Name</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
							if(decryptIt($row['name']) == NULL || decryptIt($row['name']) == '') {
								$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']);
							} else { $name = decryptIt($row['name']); }
                            echo '<tr>';
                            $contactid = $row['contactid'];
                            echo '<td data-title="Contact ID">' . $row['contactid'] . '</td>';
							echo '<td data-title="Category">' . $row['category'] . '</td>';
                            echo '<td data-title="Name">' . $name . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
							echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&contactid='.$row['contactid'].'&category=staff\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&contactid='.$row['contactid'].'&category=staff\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
							}
                            echo '</tr>';
                        }

                        echo '</table>';
                        // Added Pagination //
                        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                        // Pagination Finish //
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived staff -->
			<?php } ?>

			<?php if($current_tab =='sales') { ?>
            <!-- Archived Sales -->
            <div id="tab4" class="tab-pane triple-gap-top">
            <form name="form_sales" method="post" action="archived_data.php" class="form-inline" role="form">
				<center><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search..." style='max-width:300px; margin-bottom:20px;'></center>
                <div id="no-more-tables">

                <?php
                $query_check_credentials = "SELECT * FROM sales WHERE deleted = 1 ORDER BY date_of_archival DESC LIMIT $offset, $rowsPerPage";
                $query = "SELECT count(*) as numrows FROM sales WHERE deleted = 1 ORDER BY date_of_archival DESC";

                $result = mysqli_query($dbc, $query_check_credentials);

                $num_rows = mysqli_num_rows($result);
                if($num_rows > 0) {

                    // Added Pagination //
                    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                    // Pagination Finish //

                    echo "<table class='table table-bordered table-striped'>";
                    echo "<thead><tr class='hidden-xs hidden-sm'>
                    <th>ID#</th>
                    <th>Business</th>
                    <th>Contact</th>
                    <th>Lead Stage</th>
                    <th>Next Action</th>
                    <th>Reminder</th>
                    <th>Status</th>
                    <th>Date of Archival</th>";
                    if($edit_access > 0) {
                        echo "<th>Restore</th>";
                    }
					echo "</tr></thead>";
                } else{
                    echo "<div class='clearfix'><h2>No Record Found.</h2></div>";
                }

                while($row = mysqli_fetch_array( $result ))
                {
                    $salesleadid = $row['salesleadid'];
                    $clientid = $row['contactid'];
                    $contactid = $row['contactid'];
                    $get_client = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT c.name, cci.first_name, cci.last_name FROM clients c, contacts cci WHERE cci.clientid='$clientid' AND c.clientid = cci.clientid AND cci.contactid = '$contactid'"));
                    $client_name = $get_client['client_name'];
                    $contact = $get_client['first_name'].' '.$get_client['last_name'];

                    echo "<tr>";
                    echo '<td data-title="ID#"><a href=\'add_sales_lead.php?salesleadid='.$salesleadid.'\' >'.$row['sales_lead_number'].'</a></td>';

                    echo '<td data-title="Business"><a href="add_client.php?clientid='.$clientid.'" target="_blank">'.$client_name.'</a></td>';

                    echo '<td data-title="Contact"><a href="add_contact.php?contactid='.$contactid.'" target="_blank">'.$contact.'</a></td>';

					echo '<td data-title="Lead Stage">' . $row['lead_stage'] . '</td>';
					echo '<td data-title="Product / Services">' . $row['action'] . '</td>';
					echo '<td data-title="Action">' . $row['action_date'] . '</td>';
					echo '<td data-title="Est. Approx ROI">' . $row['status'] . '</td>';
                     echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

		            if($edit_access > 0) {
                    echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&sales_lead_number='.$row['sales_lead_number'].'&category=sales\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&sales_lead_number='.$row['sales_lead_number'].'&category=sales\' onclick="return confirm(\'By deleting this item, you may never be able to gain access this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
                    }
                    echo "</tr>";
                }

                echo '</table>';

                // Added Pagination //
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                // Pagination Finish //

                ?>
                </div>
            </form>

            </div>
            <!-- Archived Sales -->
			<?php } ?>
			<?php if($current_tab =='inventory') { ?>
             <!-- Archived inventory -->
            <div id="tab5" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
					<center><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search..." style='max-width:300px; margin-bottom:20px;'></center>
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM inventory WHERE deleted = 1 ORDER BY date_of_archival DESC LIMIT $offset, $rowsPerPage");
                        $query = "SELECT COUNT(*) as numrows FROM inventory WHERE deleted = 1 ORDER BY date_of_archival";

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        // Added Pagination //
                        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                        // Pagination Finish //
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Inventory ID</th>
                                <th>Category</th>
                                <th>Name</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Inventory ID">' . $row['inventoryid'] . '</td>';
                            echo '<td data-title="Category">' . $row['category'] . '</td>';
                            echo '<td data-title="Name"><a href=\'../Inventory/add_inventory.php?inventoryid='.$row['inventoryid'].'\'>' . $row['name'] . '</a></td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&inventoryid='.$row['inventoryid'].'&category=inventory\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&inventoryid='.$row['inventoryid'].'&category=inventory\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        // Added Pagination //
                        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                        // Pagination Finish //
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived inventory -->
			<?php } ?>
			<?php if($current_tab =='equipment') { ?>
             <!-- Archived equipment -->
            <div id="tab7" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM equipment WHERE deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Serial Number</th>
                                <th>Unit Number</th>
                                <th>Type</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Model Year</th>
                                <th>Year Purchased</th>
                                <th>Next Service</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Serial Number">' . $row['serial_number'] . '</td>';
                            echo '<td data-title="Unit Number">' . $row['unit_number'] . '</td>';
                            echo '<td data-title="Type">' . $row['type'] . '</td>';
                            echo '<td data-title="Make">' . $row['make'] . '</td>';
                            echo '<td data-title="Model">' . $row['model'] . '</td>';
                            echo '<td data-title="Model Year">' . $row['model_year'] . '</td>';
                            echo '<td data-title="Year Purchased">' . $row['year_purchased'] . '</td>';
                            echo '<td data-title="Next Service">' . $row['next_service'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&category=equipment&equipmentid='.$row['equipmentid'].'\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> |
								<a href=\'../delete_restore.php?action=delete_2&category=equipment&equipmentid='.$row['equipmentid'].'\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
							}
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived equipment -->
			<?php } ?>
			<?php if($current_tab =='bid') { ?>
             <!-- Archived bid -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT b.*, c.customer_name FROM bid_section b, customer c WHERE b.customerid = c.customerid AND b.deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Bid#</th>
								<th>Client</th>
								<th>Created Date</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Category">#' . $row['sectionid'] . '</td>';
							echo '<td data-title="Category">' . $row['customer_name'] . '</td>';
							echo '<td data-title="Category">' . $row['today_date'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'delete_restore.php?action=restore&sectionid='.$row['sectionid'].'\' onclick="return confirm(\'Are you sure?\')">Restore</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived bid -->
			<?php }
			else if($current_tab == 'field_sites') { ?>
			<!-- Archived Field Sites -->
			<div id="tab_field_jobs" class="tab-pane triple-gap-top">
				<h1>Archived Field Sites</h1>
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
					<center><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search..." style='max-width:300px; margin-bottom:20px;'></center>
                    <div id="no-more-tables">
                        <?php
						$dashboards = mysqli_query($dbc, "SELECT `tab`, `dashboard_list` FROM `field_config_field_jobs` WHERE `tab`='sites'");
						$sites_db = ',site_name,';
						while($row = mysqli_fetch_array($dashboards)) {
							switch($row['tab']) {
								case 'sites': $sites_db = $row['dashboard_list']; break;
							}
						}
						$sites_info = "CONCAT(";
						if(strpos($sites_db,',site_name,') !== false)
							$sites_info .= "'Site Name: ',`FS`.`site_name`,'<br />\n',";
						if(strpos($sites_db,',customer,') !== false)
							$sites_info .= "'Customer: [ENCRYPTED]<br />\n',";
						if(strpos($sites_db,',website,') !== false)
							$sites_info .= "'Website: ',`FS`.`domain_name`,'<br />\n',";
						if(strpos($sites_db,',display,') !== false)
							$sites_info .= "'Display Name: ',`FS`.`display_name`,'<br />\n',";
						if(strpos($sites_db,',address,') !== false)
							$sites_info .= "'Full Address: ',`FS`.`office_street`,', ',`FS`.`office_city`,', ',`FS`.`office_state`,'  ',`FS`.`office_zip`,', ',`FS`.`office_country`,'<br />\n',";
						if(strpos($sites_db,',phone,') !== false)
							$sites_info .= "'Phone Number: ',FS.`phone_number`,'<br />\n',";
						if(strpos($sites_db,',fax,') !== false)
							$sites_info .= "'Fax Number: ',FS.`fax_number`,'<br />\n',";
						$sites_info .= "'')";
						$sql = "SELECT 'Sites' job_tab, FS.`siteid` job_id, $sites_info info, C.`name` `encrypted`, FS.`deleted`, FS.date_of_archival, FS.site_name FROM `field_sites` FS LEFT JOIN `contacts` C ON FS.`clientid`=C.`contactid`
							WHERE FS.deleted = 1 ORDER BY job_tab, job_id";
                        $result = mysqli_query($dbc, $sql);
						$num_rows = mysqli_num_rows($result);
                        $query = "SELECT '$num_rows' as numrows";
                        if($num_rows > 0) {
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
							echo "<table border='2' cellpadding='10' class='table table-striped'>";
							echo "<thead><tr class='hidden-xs hidden-sm'>
								<th>Information</th>
                                <th>Date of Archival</th>";
	                        if($edit_access > 0) {
	                            echo "<th>Restore / Delete</th>";
	                        }
							echo "</tr></thead><tbody>";
							mysqli_data_seek($result, $offset);
							$row_num = 0;
							while($row_num++ < $rowsPerPage && $row = mysqli_fetch_array( $result ))
							{
								echo '<tr>';
								//echo '<td data-title="Information">' . str_replace('[ENCRYPTED]',($row['encrypted'] == '' ? 'N/A' : decryptIt($row['encrypted'])),$row['info']) . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['site_name'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
	                            if($edit_access > 0) {
								echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&category=field_jobs&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> |
									<a href=\'../delete_restore.php?action=delete_2&category=field_jobs&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
								}
								echo '</tr>';
							}

							echo '</tbody></table>';
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        ?>
					</div>
				</form>
			</div>
			<!-- Archived Field Sites -->
			<?php }
			else if($current_tab == 'field_jobs') { ?>
			<!-- Archived Field Jobs -->
			<div id="tab_field_jobs" class="tab-pane triple-gap-top">
				<h1>Archived Field Jobs</h1>
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
					<center><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search..." style='max-width:300px; margin-bottom:20px;'></center>
                    <div id="no-more-tables">
                        <?php
						$dashboards = mysqli_query($dbc, "SELECT `tab`, `dashboard_list` FROM `field_config_field_jobs` WHERE `tab`='jobs'");
						$jobs_db = ',job,contact,foreman,';
						while($row = mysqli_fetch_array($dashboards)) {
							switch($row['tab']) {
								case 'jobs': $jobs_db = $row['dashboard_list']; break;
							}
						}
						$jobs_info = "CONCAT(";
						if(strpos($jobs_db,',job,') !== false)
							$jobs_info .= "'Job #: ',FJ.`job_number`,'<br />\n',";
						if(strpos($jobs_db,',site,') !== false)
							$jobs_info .= "'Site: ',FS.`site_name`,'<br />\n',";
						$jobs_info .= "'')";
						$sql = "SELECT 'Jobs' job_tab, FJ.`jobid` job_id, $jobs_info info, '' `encrypted`, FJ.`deleted`, FJ.date_of_archival FROM `field_jobs` FJ LEFT JOIN `field_sites` FS ON FJ.`siteid` = FS.`siteid`
							WHERE FJ.deleted = 1 ORDER BY job_tab, job_id";
                        $result = mysqli_query($dbc, $sql);
						$num_rows = mysqli_num_rows($result);
                        $query = "SELECT '$num_rows' as numrows";
                        if($num_rows > 0) {
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
							echo "<table border='2' cellpadding='10' class='table table-striped'>";
							echo "<thead><tr class='hidden-xs hidden-sm'>
								<th>Information</th>
                                <th>Date of Archival</th>";
	                        if($edit_access > 0) {
	                            echo "<th>Restore / Delete</th>";
	                        }
							echo "</tr></thead><tbody>";
							mysqli_data_seek($result, $offset);
							$row_num = 0;
							while($row_num++ < $rowsPerPage && $row = mysqli_fetch_array( $result ))
							{
								echo '<tr>';
								echo '<td data-title="Information">' . str_replace('[ENCRYPTED]',($row['encrypted'] == '' ? 'N/A' : decryptIt($row['encrypted'])),$row['info']) . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

	                            if($edit_access > 0) {
								echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&category=field_jobs&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> |
									<a href=\'../delete_restore.php?action=delete_2&category=field_jobs&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
								}
								echo '</tr>';
							}

							echo '</tbody></table>';
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        ?>
					</div>
				</form>
			</div>
			<!-- Archived Field Jobs -->
			<?php }
			else if($current_tab == 'field_foreman') { ?>
			<!-- Archived Field Foreman Sheets -->
			<div id="tab_field_jobs" class="tab-pane triple-gap-top">
				<h1>Archived Field Foreman Sheets</h1>
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
					<center><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search..." style='max-width:300px; margin-bottom:20px;'></center>
                    <div id="no-more-tables">
                        <?php
						$dashboards = mysqli_query($dbc, "SELECT `tab`, `dashboard_list` FROM `field_config_field_jobs` WHERE `tab`='foreman'");
						$foreman_db = ',job,crew,';
						while($row = mysqli_fetch_array($dashboards)) {
							switch($row['tab']) {
								case 'foreman': $foreman_db = $row['dashboard_list']; break;
							}
						}
						$foreman_info = "CONCAT(";
						if(strpos($foreman_db,',job,') !== false)
							$foreman_info .= "'Job #: ',FJ.`job_number`,'<br />\n',";
						if(strpos($foreman_db,',date,') !== false)
							$foreman_info .= "'Sheet Date: ',FS.`today_date`,'<br />\n',";
						$foreman_info .= "'')";
						$sql = "SELECT 'Foreman Sheet' job_tab, `fsid` job_id, $foreman_info info, '' `encrypted`, FS.`deleted`, FS.date_of_archival FROM `field_foreman_sheet` FS LEFT JOIN field_jobs FJ ON FS.jobid=FJ.jobid
							WHERE FS.deleted = 1 ORDER BY job_tab, job_id";
                        $result = mysqli_query($dbc, $sql);
						$num_rows = mysqli_num_rows($result);
                        $query = "SELECT '$num_rows' as numrows";
                        if($num_rows > 0) {
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
							echo "<table border='2' cellpadding='10' class='table table-striped'>";
							echo "<thead><tr class='hidden-xs hidden-sm'>
								<th>Information</th>
                                <th>Date of Archival</th>";
	                        if($edit_access > 0) {
	                            echo "<th>Restore / Delete</th>";
	                        }
							echo "</tr></thead><tbody>";
							mysqli_data_seek($result, $offset);
							$row_num = 0;
							while($row_num++ < $rowsPerPage && $row = mysqli_fetch_array( $result ))
							{
								echo '<tr>';
								echo '<td data-title="Information">' . str_replace('[ENCRYPTED]',($row['encrypted'] == '' ? 'N/A' : decryptIt($row['encrypted'])),$row['info']) . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

	                            if($edit_access > 0) {
								echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&category=field_jobs&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> |
									<a href=\'../delete_restore.php?action=delete_2&category=field_jobs&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
								}
								echo '</tr>';
							}

							echo '</tbody></table>';
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        ?>
					</div>
				</form>
			</div>
			<!-- Archived Field Foreman Sheets -->
			<?php }
			else if($current_tab == 'field_po') { ?>
			<!-- Archived Field POs -->
			<div id="tab_field_jobs" class="tab-pane triple-gap-top">
				<h1>Archived Field POs</h1>
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
					<center><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search..." style='max-width:300px; margin-bottom:20px;'></center>
                    <div id="no-more-tables">
                        <?php
						$dashboards = mysqli_query($dbc, "SELECT `tab`, `dashboard_list` FROM `field_config_field_jobs` WHERE `tab`='po'");
						$po_db = ',po,job,';
						while($row = mysqli_fetch_array($dashboards)) {
							switch($row['tab']) {
								case 'po': $po_db = $row['dashboard_list']; break;
							}
						}
						$po_info = "CONCAT(";
						if(strpos($po_db,',po,') !== false)
							$po_info .= "'PO #: ',PO.`po_number`,'<br />\n',";
						if(strpos($po_db,',job,') !== false)
							$po_info .= "'Job #: ',FJ.`job_number`,'<br />\n',";
						if(strpos($po_db,',vendor,') !== false)
							$po_info .= "'Vendor: [ENCRYPTED]<br />\n',";
						$po_info .= "'')";
						$sql = "SELECT IF(PO.`deleted`=1, 'Archived', 'Attached to Work Ticket') job_tab, PO.`fieldpoid` job_id, $po_info info, C.`name` `encrypted`, PO.`deleted`, PO.date_of_archival FROM `field_po` PO LEFT JOIN field_jobs FJ ON PO.jobid=FJ.jobid LEFT JOIN `contacts` C ON PO.`vendorid`=C.`contactid`
							WHERE PO.deleted = 1 OR (PO.`attach_workticket`=1 AND PO.`deleted`=0) ORDER BY job_tab, job_id";
                        $result = mysqli_query($dbc, $sql);
						$num_rows = mysqli_num_rows($result);
                        $query = "SELECT '$num_rows' as numrows";
                        if($num_rows > 0) {
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
							echo "<table border='2' cellpadding='10' class='table table-striped'>";
							echo "<thead><tr class='hidden-xs hidden-sm'>
								<th>PO Status</th>
								<th>Information</th>
                                <th>Date of Archival</th>";
	                        if($edit_access > 0) {
	                            echo "<th>Restore / Delete</th>";
	                        }
							echo "</tr></thead><tbody>";
							mysqli_data_seek($result, $offset);
							$row_num = 0;
							while($row_num++ < $rowsPerPage && $row = mysqli_fetch_array( $result ))
							{
								echo '<tr>';
								echo '<td data-title="Status">'.$row['job_tab'].'<br /><a href="../Field Jobs/download/field_po_'.$row['job_id'].'.pdf">View</a></td>';
								echo '<td data-title="Information">' . str_replace('[ENCRYPTED]',($row['encrypted'] == '' ? 'N/A' : decryptIt($row['encrypted'])),$row['info']) . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

	                            if($edit_access > 0) {
								echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&category=field_po&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> |
									<a href=\'../delete_restore.php?action=delete_2&category=field_po&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
								}
								echo '</tr>';
							}

							echo '</tbody></table>';
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        ?>
					</div>
				</form>
			</div>
			<!-- Archived Field POs -->
			<?php }
			else if($current_tab == 'field_work_ticket') { ?>
			<!-- Archived Field Work Tickets -->
			<div id="tab_field_jobs" class="tab-pane triple-gap-top">
				<h1>Archived Field Work Tickets</h1>
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
					<center><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search..." style='max-width:300px; margin-bottom:20px;'></center>
                    <div id="no-more-tables">
                        <?php
						$dashboards = mysqli_query($dbc, "SELECT `tab`, `dashboard_list` FROM `field_config_field_jobs` WHERE `tab`='work'");
						$work_db = ',ticket,job,date,description,mod_reg,mod_ot,';
						$rowsPerPage = 50;
						while($row = mysqli_fetch_array($dashboards)) {
							switch($row['tab']) {
								case 'work': $work_db = $row['dashboard_list']; break;
							}
						}
						$work_info = "CONCAT(";
						if(strpos($work_db,',work_ticket,') !== false)
							$work_info .= "'Work Ticket #: ',FW.`workticketid`,'<br />\n',";
						if(strpos($work_db,',date,') !== false)
							$work_info .= "'Date: ',FW.`wt_date`,'<br />\n',";
						if(strpos($work_db,',job,') !== false)
							$work_info .= "'Job #: ',FJ.`job_number`,'<br />\n',";
						$work_info .= "'')";
						$sql = "SELECT 'Work Ticket' job_tab, FW.`workticketid` job_id, $work_info info, '' `encrypted`, FW.`attach_invoice`, FW.`jobid` job_num, FW.`fsid`, FW.`deleted`, FW.date_of_archival FROM `field_work_ticket` FW LEFT JOIN field_jobs FJ ON FW.jobid=FJ.jobid
							WHERE FW.deleted = 1 OR FW.attach_invoice > 0 ORDER BY job_tab, job_id DESC";
                        $result = mysqli_query($dbc, $sql);
						$num_rows = mysqli_num_rows($result);
                        $query = "SELECT '$num_rows' as numrows";
                        if($num_rows > 0) {
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
							echo "<table border='2' cellpadding='10' class='table table-striped'>";
							echo "<thead><tr class='hidden-xs hidden-sm'>
								<th>Work Ticket Status</th>
								<th>Information</th>
                                <th>Date of Archival</th>";
	                        if($edit_access > 0) {
	                            echo "<th>Restore / Delete</th>";
	                        }
							echo "</tr></thead><tbody>";
							mysqli_data_seek($result, $offset);
							$row_num = 0;
							while($row_num++ < $rowsPerPage && $row = mysqli_fetch_array( $result ))
							{
								echo '<tr>';
								echo '<td data-title="Status">' . ($row['deleted'] == 1 ? 'Archived' : 'Attached to Invoice #'.$row['attach_invoice'].'<br /><a href="../Field Jobs/download/field_invoice_'.$row['attach_invoice'].'.pdf">View Invoice #'.$row['attach_invoice'].'</a>');
								echo '<br /><a href="../Field Jobs/download/field_work_ticket_'.$row['job_id'].'.pdf">View Work Ticket</a>' . '</td>';
								echo '<td data-title="Information">' . str_replace('[ENCRYPTED]',($row['encrypted'] == '' ? 'N/A' : decryptIt($row['encrypted'])),$row['info']) . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

	                            if($edit_access > 0) {
								echo '<td data-title="Restore">'.($row['deleted'] == 1 ? '<a href=\'../delete_restore.php?action=restore&category=field_jobs&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> |
									<a href=\'../delete_restore.php?action=delete_2&category=field_jobs&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a>'
									: '<a href="../Field Jobs/add_field_work_ticket.php?fsid='.$row['fsid'].'&jobid='.$row['job_num'].'&workticketid='.$row['job_id'].'&from_url='.urlencode(WEBSITE_URL.'/Archived/archived_data.php?archive_type=field_work_ticket').'&mode=view">Review Invoiced Work Ticket</a>') .'</td>';
								}
								echo '</tr>';
							}

							echo '</tbody></table>';
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        ?>
					</div>
				</form>
			</div>
			<!-- Archived Field Work Tickets -->
			<?php }
			else if($current_tab == 'field_invoices') { ?>
			<!-- Archived Field Invoices -->
			<div id="tab_field_jobs" class="tab-pane triple-gap-top">
				<h1>Archived Field Invoices</h1>
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
					<center><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search..." style='max-width:300px; margin-bottom:20px;'></center>
                    <div id="no-more-tables">
                        <?php
						$dashboards = mysqli_query($dbc, "SELECT `tab`, `dashboard_list` FROM `field_config_field_jobs` WHERE `tab`='invoice'");
						$invoice_db = ',invoice,job,customer,';
						while($row = mysqli_fetch_array($dashboards)) {
							switch($row['tab']) {
								case 'invoice': $invoice_db = $row['dashboard_list']; break;
							}
						}
						$invoice_info = "CONCAT(";
						if(strpos($invoice_db,',invoice,') !== false)
							$invoice_info .= "'Invoice #: ',FI.`invoiceid`,'<br />\n',";
						if(strpos($invoice_db,',job,') !== false)
							$invoice_info .= "'Job #: ',FJ.`job_number`,'<br />\n',";
						if(strpos($invoice_db,',date,') !== false)
							$invoice_info .= "'Invoice Date: ',FI.`invoice_date`,'<br />\n',";
						$invoice_info .= "'')";
						$sql = "SELECT 'Invoice' job_tab, FI.`invoiceid` job_id, $invoice_info info, '' `encrypted`, FI.`deleted`, FI.date_of_archival FROM `field_invoice` FI LEFT JOIN field_jobs FJ ON FI.jobid=FJ.jobid
							WHERE FI.deleted = 1 ORDER BY job_tab, job_id";
                        $result = mysqli_query($dbc, $sql);
						$num_rows = mysqli_num_rows($result);
                        $query = "SELECT '$num_rows' as numrows";
                        if($num_rows > 0) {
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
							echo "<table border='2' cellpadding='10' class='table table-striped'>";
							echo "<thead><tr class='hidden-xs hidden-sm'>
								<th>Information</th>
                                <th>Date of Archival</th>";
	                        if($edit_access > 0) {
	                            echo "<th>Restore / Delete</th>";
	                        }
							echo "</tr></thead><tbody>";
							mysqli_data_seek($result, $offset);
							$row_num = 0;
							while($row_num++ < $rowsPerPage && $row = mysqli_fetch_array( $result ))
							{
								echo '<tr>';
								echo '<td data-title="Information">' . str_replace('[ENCRYPTED]',($row['encrypted'] == '' ? 'N/A' : decryptIt($row['encrypted'])),$row['info']) . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

	                            if($edit_access > 0) {
								echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&category=field_jobs&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> |
									<a href=\'../delete_restore.php?action=delete_2&category=field_jobs&field_job='.$row['job_id'].'&job_tab='.$row['job_tab'].'\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
								}
								echo '</tr>';
							}

							echo '</tbody></table>';
							// Added Pagination //
							echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
							// Pagination Finish //
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        ?>
					</div>
				</form>
			</div>
			<!-- Archived Field Invoices -->
			<?php } ?>
			<?php if($current_tab =='shop_work_order') { ?>
             <!-- Archived Shop Work Orders -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT p.*, c.`name` FROM `project_manage` p LEFT JOIN `contacts` c ON p.`businessid`=c.`contactid` WHERE p.`tile`='Shop Work Orders' AND p.`status` IN ('Deleted','Rejected')");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Work Order #</th>
								<th>Business</th>
								<th>Status</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Category"># ' . $row['projectmanageid'] . '</td>';
							echo '<td data-title="Category">' . decryptIt($row['name']) . '</td>';
							echo '<td data-title="Category">' . $row['status'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&projectmanageid='.$row['projectmanageid'].'&category=shop_work_order\' onclick="return confirm(\'Are you sure?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&projectmanageid='.$row['projectmanageid'].'&category=shop_work_order\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived bid -->
			<?php } ?>
			<?php if($current_tab =='certificate') { ?>
             <!-- Archived bid -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from certificate where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Certificate Type</th>
								<th>Title</th>
								<th>Name</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Certificate-Type">#' . $row['certificate_type'] . '</td>';
							echo '<td data-title="Title">' . $row['heading'] . '</td>';
							echo '<td data-title="Name">' . $row['name'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&certificateid='.$row['certificateid'].'&category=certificate\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&certificateid='.$row['certificateid'].'&category=certificate\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived certificate -->
			<?php } ?>
			<?php if($current_tab =='guide') { ?>
             <!-- Archived guide -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from how_to_guide where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Tile</th>
								<th>Sub Tab</th>
								<th>Description</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">#' . $row['tile'] . '</td>';
							echo '<td data-title="Sub Tab">' . $row['subtab'] . '</td>';
							echo '<td data-title="Description">' . $row['description'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&guideid='.$row['guideid'].'&category=guide\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&guideid='.$row['guideid'].'&category=guide\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived guide -->
			<?php } ?>
			<?php if($current_tab =='hr') { ?>
             <!-- Archived hr -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from hr where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Description</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['sub_heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&hrid='.$row['hrid'].'&category=hr\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&hrid='.$row['hrid'].'&category=hr\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived hr -->
			<?php } ?>

			<?php if($current_tab =='eh') { ?>
             <!-- Archived hr -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from manuals where deleted=1 and manual_type='emp_handbook' ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Heading</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&manualtypeid='.$row['manualtypeid'].'&category=eh\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&manualtypeid='.$row['manualtypeid'].'&category=eh\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived hr -->
			<?php } ?>

			<?php if($current_tab =='manual') { ?>
             <!-- Archived hr -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from manuals where deleted=1 and manual_type='operations_manual' ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Heading</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&manualtypeid='.$row['manualtypeid'].'&category=manual\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&manualtypeid='.$row['manualtypeid'].'&category=manual\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived hr -->
			<?php } ?>

			<?php if($current_tab =='pp') { ?>
             <!-- Archived hr -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from manuals where deleted=1 and manual_type='policy_procedures' ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Heading</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&manualtypeid='.$row['manualtypeid'].'&category=pp\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&manualtypeid='.$row['manualtypeid'].'&category=pp\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived hr -->
			<?php } ?>

			<?php if($current_tab =='checklist') { ?>
             <!-- Archived Checklist -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from checklist where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Checklist Name</th>
								<th>Security</th>
								<th>Checklist Type</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['checklist_name'] . '</td>';
							echo '<td data-title="Tile">' . $row['security'] . '</td>';
							echo '<td data-title="Tile">' . $row['checklist_type'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href="../delete_restore.php?action=restore&archive_checklistid='.$row['checklistid'].'&category=checklist" onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href="../delete_restore.php?action=delete_2&archive_checklistid='.$row['checklistid'].'&category=checklist" onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
                            }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Checklist -->
			<?php } ?>

			<?php if($current_tab =='cd') { ?>
             <!-- Archived Client Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from client_documents where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Client Document Type</th>
								<th>Category</th>
								<th>Title</th>
								<th>Name</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['client_documents_type'] . '</td>';
							echo '<td data-title="Tile">' . $row['category'] . '</td>';
							echo '<td data-title="Tile">' . $row['title'] . '</td>';
							echo '<td data-title="Tile">' . $row['name'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&client_documentsid='.$row['client_documentsid'].'&category=cd\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&client_documentsid='.$row['client_documentsid'].'&category=cd\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Client Documents -->
			<?php } ?>

			<?php if($current_tab =='id') { ?>
             <!-- Archived Client Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from internal_documents where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Internal Document Type</th>
								<th>Category</th>
								<th>Title</th>
								<th>Name</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['internal_documents_type'] . '</td>';
							echo '<td data-title="Tile">' . $row['category'] . '</td>';
							echo '<td data-title="Tile">' . $row['title'] . '</td>';
							echo '<td data-title="Tile">' . $row['name'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&internal_documentsid='.$row['internal_documentsid'].'&category=id\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&internal_documentsid='.$row['internal_documentsid'].'&category=id\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Client Documents -->
			<?php } ?>

			<?php if($current_tab =='passwords') { ?>
             <!-- Archived Passwords -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from passwords where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Password Type</th>
								<th>Category</th>
								<th>Heading</th>
								<th>Description</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['password_type'] . '</td>';
							echo '<td data-title="Tile">' . $row['category'] . '</td>';
							echo '<td data-title="Tile">' . $row['heading'] . '</td>';
							echo '<td data-title="Tile">' . html_entity_decode($row['description']) . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&passwordid='.$row['passwordid'].'&category=passwords\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&passwordid='.$row['passwordid'].'&category=passwords\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Password -->
			<?php } ?>

			<?php if($current_tab =='documents') { ?>
             <!-- Archived Documents  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from documents where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Tile Heading</th>
								<th>Document</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['tile_heading'] . '</td>';
							echo '<td data-title="Tile">' . $row['document'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&documentid='.$row['documentid'].'&category=documents\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&documentid='.$row['documentid'].'&category=documents\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Client Documents -->
			<?php } ?>

			<?php if($current_tab =='helpdesk') { ?>
             <!-- Archived Documents  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from support where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Type Heading</th>
								<th>Support Type</th>
								<th>Client Info</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['heading'] . '</td>';
							echo '<td data-title="Tile">' . $row['company_name'] . '</td>';
							echo '<td data-title="Tile">' . $row['support_type'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&supportid='.$row['supportid'].'&category=helpdesk\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&supportid='.$row['supportid'].'&category=helpdesk\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Client Documents -->
			<?php } ?>

			<?php if($current_tab =='project_workflow') { ?>
             <!-- Archived Project Workflow  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from project_workflow where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Tile Name</th>
								<th>Project Path</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['tile_name'] . '</td>';
							echo '<td data-title="Tile">' . $row['project_path'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&project_workflow_id='.$row['project_workflow_id'].'&category=project_workflow\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&project_workflow_id='.$row['project_workflow_id'].'&category=project_workflow\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Project Workflow -->
			<?php } ?>

			<?php if($current_tab =='asset') { ?>
             <!-- Archived Asset  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from asset where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Category</th>
								<th>Sub Category</th>
								<th>Name</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['name'] . '</td>';
							echo '<td data-title="Tile">' . $row['sub_category'] . '</td>';
							echo '<td data-title="Tile">' . $row['name'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&assetid='.$row['assetid'].'&category=asset\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&assetid='.$row['assetid'].'&category=asset\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Asset -->
			<?php } ?>

			<?php if($current_tab =='material') { ?>
             <!-- Archived Asset  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from material where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Category</th>
								<th>Sub Category</th>
								<th>Name</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['name'] . '</td>';
							echo '<td data-title="Tile">' . $row['sub_category'] . '</td>';
							echo '<td data-title="Tile">' . $row['name'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&materialid='.$row['materialid'].'&category=material\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&materialid='.$row['materialid'].'&category=material\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Asset -->
			<?php } ?>

			<?php if($current_tab =='safety') { ?>
             <!-- Archived Asset  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from safety where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Tab</th>
								<th>Category</th>
								<th>Form</th>
								<th>Heading</th>
								<th>Sub Heading</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['tab'] . '</td>';
							echo '<td data-title="Tile">' . $row['category'] . '</td>';
							echo '<td data-title="Tile">' . $row['form'] . '</td>';
							echo '<td data-title="Tile">' . $row['heading'] . '</td>';
							echo '<td data-title="Tile">' . $row['sub_heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&safetyid='.$row['safetyid'].'&category=safety\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&safetyid='.$row['safetyid'].'&category=safety\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Asset -->
			<?php } ?>
			<?php if($current_tab =='tickets') { ?>
             <!-- Archived Tickets  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php $pageNum = $_GET['page'] ?: 1;
						$rowsPerPage = 25;
						$offset = ($pageNum - 1) * $rowsPerPage;
                        $result = mysqli_query($dbc, "SELECT * FROM tickets WHERE (deleted=1 OR status='Archive') ORDER BY date_of_archival DESC LIMIT $offset, $rowsPerPage");
						$page_count = "SELECT COUNT(*) `numrows` FROM `tickets` WHERE (deleted=1 OR status='Archive') ORDER BY date_of_archival DESC";

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
							display_pagination($dbc, $page_count, $pageNum, $rowsPerPage);
							$db_config = explode(',',get_field_config($dbc, 'tickets_dashboard'));
							include('../Ticket/ticket_table.php');
							display_pagination($dbc, $page_count, $pageNum, $rowsPerPage);
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Tickets -->
			<?php } ?>

			<?php if($current_tab =='daysheet') { ?>
             <!-- Archived Day Sheet  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT *
														FROM `tickets`
														WHERE
															(`internal_qa_date`=DATE(NOW()) AND `internal_qa_contactid` LIKE '%," . $search_user . ",%') OR
															(`deliverable_date`=DATE(NOW()) AND `deliverable_contactid` LIKE '%," . $search_user . ",%') OR
															((DATE(NOW()) BETWEEN `to_do_date` AND `to_do_end_date`) AND `contactid` LIKE '%," . $search_user . ",%') AND deleted = 1 AND
															`status` IN('Archived', 'Done')
														ORDER BY date_of_archival DESC, `ticketid`");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Service Type</th>
								<th>".TICKET_NOUN." Heading</th>
								<th>Sub Heading</th>
								<th>Service</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['service_type'] . '</td>';
							echo '<td data-title="Tile">' . $row['heading'] . '</td>';
							echo '<td data-title="Tile">' . $row['sub_heading'] . '</td>';
							echo '<td data-title="Tile">' . $row['service'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&ticketid='.$row['ticketid'].'&category=tickets\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&ticketid='.$row['ticketid'].'&category=tickets\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Day Sheet -->
			<?php } ?>

			<?php if($current_tab =='time_tracking') { ?>
             <!-- Archived Time Tracking  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from time_tracking where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Location</th>
								<th>Short Description</th>
								<th>Work Performed</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['location'] . '</td>';
							echo '<td data-title="Tile">' . $row['short_desc'] . '</td>';
							echo '<td data-title="Tile">' . $row['work_preformed'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&timetrackingid='.$row['timetrackingid'].'&category=time_tracking\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&timetrackingid='.$row['timetrackingid'].'&category=time_tracking\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Time Tracking -->
			<?php } ?>

			<?php if($current_tab =='estimate') { ?>
             <!-- Archived Estimates  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from estimate where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Business</th>
								<th>".ESTIMATE_TILE." Name</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . get_contact($dbc, $row['businessid'], 'name') . '</td>';
							echo '<td data-title="Tile">' . $row['estimate_name'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&estimateid='.$row['estimateid'].'&category=estimate\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&estimateid='.$row['estimateid'].'&category=estimate\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Estimates -->
			<?php } ?>

			<?php if($current_tab =='quote') { ?>
             <!-- Archived Estimates  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT r.*, c.name FROM estimate r, contacts c WHERE r.businessid = c.contactid AND (r.deleted = 1 OR r.status='Delete') AND (r.status!='Saved' AND r.status!='Submitted' AND r.status!='Approved Quote') ORDER BY date_of_archival DESC, estimateid DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Quote Name</th>
								<th>Total Cost</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['estimate_name'] . '</td>';
							echo '<td data-title="Tile">' . $row['financial_cost'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&estimateid='.$row['estimateid'].'&category=quote\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&estimateid='.$row['estimateid'].'&category=quote\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Estimates -->
			<?php } ?>

			<?php if($current_tab =='email_communication') { ?>
             <!-- Archived Estimates  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * from email_communication where deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Communication Id</th>
								<th>Subject</th>
								<th>Status</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['email_communicationid'] . '</td>';
							echo '<td data-title="Tile">' . $row['subject'] . '</td>';
							echo '<td data-title="Tile">' . $row['status'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&email_communicationid='.$row['email_communicationid'].'&category=email_communication\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&email_communicationid='.$row['email_communicationid'].'&category=email_communication\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Estimates -->
			<?php } ?>

			<?php if($current_tab =='newsboard') { ?>
             <!-- Archived newsboard  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * from newsboard where deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
								<th>Title</th>
								<th>Description</th>
								<th>Newsboard Type</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['title'] . '</td>';
							echo '<td data-title="Tile">' . $row['description'] . '</td>';
							echo '<td data-title="Tile">' . $row['newsboard_type'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&newsboardid='.$row['newsboardid'].'&category=newsboard\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&newsboardid='.$row['newsboardid'].'&category=newsboard\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived newsboard -->
			<?php } ?>

			<?php if($current_tab =='check_out') { ?>
             <!-- Archived check_out  -->
            <div id="tab9" class="tab-pane triple-gap-top">
                <form name="form_checkout" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                    <?php
                    if (isset($_POST['display_all_inventory'])) {
                        $search_user = '';
                        $search_invoiceid = '';
                        $search_date = '';
                    } else if(isset($_POST['search_user_submit'])) {
                        $search_user = $_POST['search_user'];
                        $search_invoiceid = $_POST['search_invoiceid'];
                        $search_date = $_POST['search_date'];
                    } else if(!empty($_GET['search_user'])) {
                        $search_user = $_GET['search_user'];
                        $search_invoiceid = '';
                        $search_date = '';
                    } else if(!empty($_GET['search_invoice'])) {
                        $search_invoiceid = $_GET['search_invoice'];
                        $search_user = '';
                        $search_date = '';
                    } else if(!empty($_GET['search_date'])) {
                        $search_date = $_GET['search_date'];
                        $search_user = '';
                        $search_invoiceid = '';
                    } else {
                        $search_user = '';
                        $search_invoiceid = '';
                        $search_date = '';
                    }
                    ?>

                    <div class="form-group">
                      <label for="site_name" class="col-sm-2 control-label">Search by Customer:</label>
                      <div class="col-sm-4" style="width:auto">
                          <select data-placeholder="Pick a User" name="search_user" id="search_user" class="chosen-select-deselect form-control" width="380">
                          <option value=""></option>
                            <?php
                            $query = mysqli_query($dbc,"SELECT distinct(patientid) FROM invoice WHERE deleted = 1 AND patientid != 0");
                            while($row = mysqli_fetch_array($query)) {
                            ?><option <?php if ($row['patientid'] == $search_user) { echo " selected"; } ?> value='<?php echo  $row['patientid']; ?>' ><?php echo get_contact($dbc, $row['patientid']); ?></option>
                            <?php	}
                            ?>
                        </select>
                      </div>
                        Invoice#:
                            <input name="search_invoiceid" type="text" class="form-control1" value="<?php echo $search_invoiceid; ?>">
                        Invoice Date
                            <input name="search_date" type="text" class="datepicker" value="<?php echo $search_date; ?>">

                        <button type="submit" name="search_user_submit" id="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                        <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
                    </div>

                        <?php
                         /* Pagination Counting */
                        $rowsPerPage = 25;
                        $pageNum = 1;

                        if(isset($_GET['page'])) {
                            $pageNum = $_GET['page'];
                        }

                        $offset = ($pageNum - 1) * $rowsPerPage;

                        if($search_user != '') {
                            $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 1 AND patientid='$search_user' ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                        } else if($search_invoiceid != '') {
                            $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 1 AND invoiceid='$search_invoiceid' ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                        }  else if($search_date != '') {
                            $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 1 AND invoice_date='$search_date' ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                        } else {
                            $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 1 ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC LIMIT $offset, $rowsPerPage";
                            $query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 1 ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                        }

                        $num_rows = 0;
                        $result = mysqli_query($dbc, $query_check_credentials);
                        $num_rows = mysqli_num_rows($result);

                        if($num_rows > 0) {

                            if($search_user == '' && $search_invoiceid == '' && $search_date == '') {
                                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                            }
                            echo "<table border='2' cellpadding='10' class='table table-striped'>";
                            echo "<thead><tr>";
                            echo "<th>Invoice#</th>
                            <th>Invoice Date</th>
                            <th>Patient</th>
                            <th>Service Date</th>
                            <th>Service</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Patient Invoice</th>
                            <th>Patient Receipt</th>
                            <th>Date of Archival</th>
                            <th>Restore / Delete</th>
                            </tr>";
                        } else {
                            echo "<h2>No Record Found.</h2>";
                        }

                        while($row = mysqli_fetch_array( $result ))
                        {
                            $patientid = $row['patientid'];

                            echo '<tr>';
                            if(!empty($_GET['patientid'])) {
                                echo '<td><input type="checkbox" name="invoice[]" value="'.$row['invoiceid'].'" class="privileges_view_'.$patientid.'" ></td>';
                            }

                            echo '<td>' . $row['invoiceid'] . '</td>';
                            echo '<td>' . $row['invoice_date'] . '</td>';

                            if($row['patientid'] != 0) {
                                //echo '<td><a class="iframe_open" id="'.$row['patientid'].'">'.get_contact($dbc, $row['patientid']). '</a></td>';
                                echo '<td><a href="#"  onclick=" window.open(\''.WEBSITE_URL.'/Contacts/add_contacts.php?category=Patient&contactid='.$row['patientid'].'\', \'newwindow\', \'width=900, height=900\'); return false;">'.get_contact($dbc, $row['patientid']). '</a></td>';
                                //echo '<td>'.get_contact($dbc, $row['patientid']). '</td>';
                            } else {
                                echo '<td>Non Patient</td>';
                            }

                            echo '<td>' . $row['service_date'] . '</td>';

                            $serviceid = $row['serviceid'];
                            echo '<td>'. get_all_from_service($dbc, $serviceid, 'service_code').' : '.get_all_from_service($dbc, $serviceid, 'service_type') . '</td>';
                            echo '<td>$' . ($row['final_price']) . '</td>';

                            $paid = $row['paid'];
                            if($row['paid'] == 'Yes') {
                                $paid = 'Patient Invoice';
                            } else if($row['paid'] == 'No') {
                                $paid = 'Partially Paid';
                            }

                            echo '<td>' . $paid . '</td>';

                            if($row['final_price'] != '' && $row['paid'] != 'Saved') {
                                $name_of_file = 'Download/invoice_'.$row['invoiceid'].'.pdf';
                                if(file_exists($name_of_file)) {
                                    $md5 = md5_file($name_of_file);
                                    if($md5 == $row['invoice_md5']) {
                                        echo '<td><a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a></td>';
                                    } else {
                                        echo '<td>(Error : File Change)</td>';
                                    }
                                } else {
                                    echo '<td>-</td>';
                                }
                            } else {
                                echo '<td>-</td>';
                            }

                            if($row['patient_payment_receipt'] == 1) {
                                $name_of_file = 'Download/patientreceipt_'.$row['invoiceid'].'.pdf';
                                if(file_exists($name_of_file)) {
                                    echo '<td><a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a></td>';
                                } else {
                                    echo '<td>-</td>';
                                }
                            } else {
                                echo '<td>-</td>';
                            }
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            echo '<td data-title="Restore / Delete"><a href="../delete_restore.php?action=restore&checkoutid='.$row['invoiceid'].'&category=check_out" onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href="../delete_restore.php?action=delete_2&checkoutid='.$row['invoiceid'].'&category=check_out" onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';

                            echo "</tr>";
                        }

                        echo '</table>';

                        if($search_user == '' && $search_invoiceid == '' && $search_date == '') {
                            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                        }

                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived check_out -->
			<?php } ?>

            <?php if($current_tab =='match') { ?>
             <!-- Archived match  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM match_contact WHERE deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Staff</th>
                                <th>Contacts</th>
                                <th>Timeline</th>
                                <th>Follow Up</th>
                                <th>End Date</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            $matchid = $row['matchid'];

                            echo '<tr>';
                            $staff_contacts_arr = explode(',', $row['staff_contact']);
                            $staff_contacts = [];

                            foreach($staff_contacts_arr as $value){
                                array_push($staff_contacts, get_staff($dbc, $value).''.get_client($dbc, $value));
                            }

                            echo '<td data-title="Staff">' . implode(', ', $staff_contacts) . '</td>';

                            $support_contacts_arr = explode(',', $row['support_contact']);
                            $support_contacts = [];

                            foreach($support_contacts_arr as $value){
                                array_push($support_contacts, get_staff($dbc, $value).''.get_client($dbc, $value));
                            }

                            echo '<td data-title="Contacts">' . implode(', ', $support_contacts) . '</td>';

                            echo '<td data-title="Timeline">'. $row['match_date']. '</td>';
                            echo '<td data-title="Follow Up">'. $row['follow_up_date']. '</td>';
                            echo '<td data-title="End Date">'. $row['end_date']. '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';

                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&matchid='.$row['matchid'].'&category=match\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&matchid='.$row['matchid'].'&category=match\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived Expenses -->
            <?php } ?>

			<?php if($current_tab =='expense') { ?>
             <!-- Archived match  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM expense WHERE deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Heading</th>
                                <th>Expense Date</th>
                                <th>Description</th>
                                <th>Total Amount</th>
                                <th>Expense Type</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }

                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['title'] . '</td>';
							echo '<td data-title="Tile">' . $row['ex_date'] . '</td>';
							echo '<td data-title="Tile">' . $row['description'] . '</td>';
							echo '<td data-title="Tile">' . $row['total'] . '</td>';
							echo '<td data-title="Tile">' . $row['type'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&expenseid='.$row['expenseid'].'&category=expense\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&expenseid='.$row['expenseid'].'&category=expense\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived expenses -->
            <?php } ?>

			<?php if($current_tab =='pos') { ?>
             <!-- Archived match  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM point_of_sell WHERE deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Invoice Date</th>
                                <th>Invoice Name</th>
                                <th>Total Amount</th>
                                <th>Product Pricing</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }

                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['invoice_date'] . '</td>';
							echo '<td data-title="Tile">' . get_contact($dbc, $row['contactid'], 'name') . '</td>';
							echo '<td data-title="Tile">' . $row['total_price'] . '</td>';
							echo '<td data-title="Tile">' . $row['productpricing'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&posid='.$row['posid'].'&category=pos\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&posid='.$row['posid'].'&category=pos\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived expenses -->
            <?php } ?>

			<?php if($current_tab =='sales_order') { ?>
             <!-- Archived match  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM sales_order WHERE deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Invoice Date</th>
                                <th>Invoice Name</th>
                                <th>Total Amount</th>
                                <th>Product Pricing</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }

                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['invoice_date'] . '</td>';
							echo '<td data-title="Tile">' . get_contact($dbc, $row['contactid'], 'name') . '</td>';
							echo '<td data-title="Tile">' . $row['total_price'] . '</td>';
							echo '<td data-title="Tile">' . $row['productpricing'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&sales_orderid='.$row['posid'].'&category=sales_order\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&sales_orderid='.$row['posid'].'&category=sales_order\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived expenses -->
            <?php } ?>

			<?php if($current_tab =='purchase_order') { ?>
             <!-- Archived match  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM purchase_orders WHERE deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Invoice Date</th>
                                <th>Invoice Name</th>
                                <th>Total Amount</th>
                                <th>Product Pricing</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }

                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['invoice_date'] . '</td>';
							echo '<td data-title="Tile">' . get_contact($dbc, $row['contactid'], 'name') . '</td>';
							echo '<td data-title="Tile">' . $row['total_price'] . '</td>';
							echo '<td data-title="Tile">' . $row['productpricing'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&poid='.$row['posid'].'&category=purchase_order\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&poid='.$row['posid'].'&category=purchase_order\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived expenses -->
            <?php } ?>

			<?php if($current_tab =='vpl') { ?>
             <!-- Archived match  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM vendor_price_list WHERE deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Code</th>
                                <th>Part Number</th>
                                <th>Category</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }

                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['code'] . '</td>';
							echo '<td data-title="Tile">' . $row['part_number'] . '</td>';
							echo '<td data-title="Tile">' . $row['category'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&vplid='.$row['vplid'].'&category=vpl\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&vpl='.$row['vplid'].'&category=vpl\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived expenses -->
            <?php } ?>

			<?php if($current_tab =='budget') { ?>
             <!-- Archived match  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM budget WHERE status = 3 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Budget Name</th>
                                <th>Staff Lead</th>
                                <th>Business</th>
								<th>Budget Creation Date</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }

                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['budget_name'] . '</td>';
							echo '<td data-title="Tile">' . get_contact($dbc, $row['staff_lead']) . '</td>';
							echo '<td data-title="Tile">' . get_contact($dbc, $row['business'],'name') . '</td>';
							echo '<td data-title="Tile">' . $row['budget_created'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&status=2&budgetid='.$row['budgetid'].'&category=budget\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&budgetid='.$row['budgetid'].'&category=budget\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived expenses -->
            <?php } ?>

			<?php if($current_tab =='infogathering') { ?>
             <!-- Archived match  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM infogathering WHERE deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Title</th>
								<th>Category</th>
                                <th>Revised Date</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }

                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['form'] . '</td>';
							echo '<td data-title="Tile">' . $row['category'] . '</td>';
							echo '<td data-title="Tile">' . $row['last_edited'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&infogatheringid='.$row['infogatheringid'].'&category=infogathering\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&infogatheringid='.$row['infogatheringid'].'&category=infogathering\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived expenses -->
            <?php } ?>

			<?php if($current_tab =='marketing_material') { ?>
             <!-- Archived match  -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM marketing_material WHERE deleted = 1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Title</th>
								<th>Category</th>
                                <th>Description</th>
								<th>Heading</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }

                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td data-title="Tile">' . $row['title'] . '</td>';
							echo '<td data-title="Tile">' . $row['category'] . '</td>';
							echo '<td data-title="Tile">' . $row['description'] . '</td>';
							echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&marketing_materialid='.$row['marketing_materialid'].'&category=marketing_material\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&marketing_materialid='.$row['marketing_materialid'].'&category=marketing_material\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>


                    </div>
                </form>
            </div>
            <!-- Archived expenses -->
            <?php } ?>

            <?php if($current_tab =='staff_documents') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from staff_documents where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Staff Document Type</th>
                                <th>Category</th>
                                <th>Title</th>
                                <th>Name</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['staff_documents_type'] . '</td>';
                            echo '<td data-title="Tile">' . $row['category'] . '</td>';
                            echo '<td data-title="Tile">' . $row['title'] . '</td>';
                            echo '<td data-title="Tile">' . $row['name'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&staff_documentsid='.$row['staff_documentsid'].'&category=staff_documents\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&staff_documentsid='.$row['staff_documentsid'].'&category=staff_documents\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Staff Documents -->
            <?php } ?>

            <?php if($current_tab =='treatment') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from patientform where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Form</th>
                                <th>Category</th>
                                <th>Heading</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['form'] . '</td>';
                            echo '<td data-title="Tile">' . $row['category'] . '</td>';
                            echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&patientformid='.$row['patientformid'].'&category=treatment\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&patientformid='.$row['patientformid'].'&category=treatment\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Staff Documents -->
            <?php } ?>

			<?php if($current_tab =='reminder') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from reminders where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Reminder Type</th>
                                <th>Reminder By</th>
                                <th>Subject</th>
								<th>Sender</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['reminder_type'] . '</td>';
                            echo '<td data-title="Tile">' . get_contact($dbc, $row['contactid']) . '</td>';
                            echo '<td data-title="Tile">' . $row['subject'] . '</td>';
							echo '<td data-title="Tile">' . $row['sender'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&reminderid='.$row['reminderid'].'&category=reminder\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&reminderid='.$row['reminderid'].'&category=reminder\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='custom') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from custom where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Service Type</th>
                                <th>Category</th>
                                <th>Heading</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['service_type'] . '</td>';
                            echo '<td data-title="Tile">' . $row['category'] . '</td>';
                            echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&customid='.$row['customid'].'&category=custom\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&customid='.$row['customid'].'&category=custom\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='labour') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from labour where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Labour Type</th>
                                <th>Heading</th>
                                <th>Cost</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['labour_type'] . '</td>';
                            echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Tile">' . $row['cost'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&labourid='.$row['labourid'].'&category=labour\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&labourid='.$row['labourid'].'&category=labour\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='package') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from package where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Service Type</th>
                                <th>Category</th>
                                <th>Headig</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['service_type'] . '</td>';
                            echo '<td data-title="Tile">' . $row['category'] . '</td>';
                            echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&packageid='.$row['packageid'].'&category=package\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&packageid='.$row['packageid'].'&category=package\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='products') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from products where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Product Type</th>
                                <th>Category</th>
                                <th>Headig</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['product_type'] . '</td>';
                            echo '<td data-title="Tile">' . $row['category'] . '</td>';
                            echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&productid='.$row['productid'].'&category=products\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&productid='.$row['productid'].'&category=products\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='promotion') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from promotion where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Service Type</th>
                                <th>Category</th>
                                <th>Headig</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['service_type'] . '</td>';
                            echo '<td data-title="Tile">' . $row['category'] . '</td>';
                            echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&promotionid='.$row['promotionid'].'&category=promotion\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&promotionid='.$row['promotionid'].'&category=products\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='service') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from services where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Service Type</th>
                                <th>Category</th>
                                <th>Headig</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['service_type'] . '</td>';
                            echo '<td data-title="Tile">' . $row['category'] . '</td>';
                            echo '<td data-title="Tile">' . $row['heading'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&serviceid='.$row['serviceid'].'&category=service\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&serviceid='.$row['serviceid'].'&category=service\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='charts') { ?>
             <!-- Archived Charts -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
						$num_rows = 0;
                        $result1 = mysqli_query($dbc, "select * from seizure_record where deleted=1 ORDER BY date_of_archival DESC");
                        $num_rows += mysqli_num_rows($result1);
						$result2 = mysqli_query($dbc, "select * from blood_glucose where deleted=1 ORDER BY date_of_archival DESC");
                        $num_rows += mysqli_num_rows($result2);
						$result3 = mysqli_query($dbc, "select * from bowel_movement where deleted=1 ORDER BY date_of_archival DESC");
                        $num_rows += mysqli_num_rows($result3);
						$result4 = mysqli_query($dbc, "select * from daily_water_temp where deleted=1 ORDER BY date_of_archival DESC");
                        $num_rows += mysqli_num_rows($result4);
                        $result5 = mysqli_query($dbc, "select * from daily_water_temp_bus where deleted=1 ORDER BY date_of_archival DESC");
                        $num_rows += mysqli_num_rows($result5);
                        $result6 = mysqli_query($dbc, "select * from daily_fridge_temp where deleted=1 ORDER BY date_of_archival DESC");
                        $num_rows += mysqli_num_rows($result6);
                        $result7 = mysqli_query($dbc, "select * from daily_freezer_temp where deleted=1 ORDER BY date_of_archival DESC");
                        $num_rows += mysqli_num_rows($result7);
                        $result8 = mysqli_query($dbc, "select * from daily_dishwasher_temp where deleted=1 ORDER BY date_of_archival DESC");
                        $num_rows += mysqli_num_rows($result8);

                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Chart Type</th>
                                <th>Client/Business</th>
                                <th>Time</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result1 ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">Seizure Record</td>';
                            echo '<td data-title="Tile">' . (!empty(get_client($dbc, $row['client'])) ? get_client($dbc, $row['client']) : get_contact($dbc, $row['client'])) . '</td>';
                            echo '<td data-title="Tile">' . $row['time'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&seizure_record_id='.$row['seizure_record_id'].'&category=charts\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&seizure_record_id='.$row['seizure_record_id'].'&category=charts\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }
						while($row = mysqli_fetch_array( $result2 ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">Blood Glucose</td>';
                            echo '<td data-title="Tile">' . (!empty(get_client($dbc, $row['client'])) ? get_client($dbc, $row['client']) : get_contact($dbc, $row['client'])) . '</td>';
                            echo '<td data-title="Tile">' . $row['time'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
	                        if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&blood_glucose_id='.$row['blood_glucose_id'].'&category=charts\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&blood_glucose_id='.$row['blood_glucose_id'].'&category=charts\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }
						while($row = mysqli_fetch_array( $result3 ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">Bowel Movement</td>';
                            echo '<td data-title="Tile">' . (!empty(get_client($dbc, $row['client'])) ? get_client($dbc, $row['client']) : get_contact($dbc, $row['client'])) . '</td>';
                            echo '<td data-title="Tile">' . $row['time'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&bowel_movement_id='.$row['bowel_movement_id'].'&category=charts\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&bowel_movement_id='.$row['bowel_movement_id'].'&category=charts\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }
						while($row = mysqli_fetch_array( $result4 ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">Daily Water Temp (Client)</td>';
                            echo '<td data-title="Tile">' . (!empty(get_client($dbc, $row['client'])) ? get_client($dbc, $row['client']) : get_contact($dbc, $row['client'])) . '</td>';
                            echo '<td data-title="Tile">' . $row['time'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&daily_water_temp_id='.$row['daily_water_temp_id'].'&category=charts\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&daily_water_temp_id='.$row['daily_water_temp_id'].'&category=charts\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }
						while($row = mysqli_fetch_array( $result5 ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">Daily Water Temp (Business)</td>';
                            echo '<td data-title="Tile">' . (!empty(get_client($dbc, $row['business'])) ? get_client($dbc, $row['business']) : get_contact($dbc, $row['business'])) . '</td>';
                            echo '<td data-title="Tile">' . $row['time'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&daily_water_temp_bus_id='.$row['daily_water_temp_bus_id'].'&category=charts\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&daily_water_temp_bus_id='.$row['daily_water_temp_bus_id'].'&category=charts\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }
						while($row = mysqli_fetch_array( $result6 ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">Daily Fridge Temp</td>';
                            echo '<td data-title="Tile">' . (!empty(get_client($dbc, $row['business'])) ? get_client($dbc, $row['business']) : get_contact($dbc, $row['business'])) . '</td>';
                            echo '<td data-title="Tile">' . $row['time'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&daily_fridge_temp_id='.$row['daily_fridge_temp_id'].'&category=charts\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&daily_fridge_temp_id='.$row['daily_fridge_temp_id'].'&category=charts\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }
						while($row = mysqli_fetch_array( $result7 ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">Daily Freezer Temp</td>';
                            echo '<td data-title="Tile">' . (!empty(get_client($dbc, $row['business'])) ? get_client($dbc, $row['business']) : get_contact($dbc, $row['business'])) . '</td>';
                            echo '<td data-title="Tile">' . $row['time'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&daily_freezer_temp_id='.$row['daily_freezer_temp_id'].'&category=charts\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&daily_freezer_temp_id='.$row['daily_freezer_temp_id'].'&category=charts\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }
						while($row = mysqli_fetch_array( $result8 ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">Daily Dishwasher Temp</td>';
                            echo '<td data-title="Tile">' . (!empty(get_client($dbc, $row['business'])) ? get_client($dbc, $row['business']) : get_contact($dbc, $row['business'])) . '</td>';
                            echo '<td data-title="Tile">' . $row['time'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&daily_dishwasher_temp_id='.$row['daily_dishwasher_temp_id'].'&category=charts\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&daily_dishwasher_temp_id='.$row['daily_dishwasher_temp_id'].'&category=charts\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='day_program') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from day_program where deleted=1");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Support Contact Category</th>
                                <th>Category</th>
                                <th>Planned Activity</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['support_contact_category'] . '</td>';
                            echo '<td data-title="Tile">' . get_contact($dbc, $row['support_contact']) . '</td>';
                            echo '<td data-title="Tile">' . $row['planned_activity'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&dayprogramid='.$row['dayprogramid'].'&category=day_program\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&dayprogramid='.$row['dayprogramid'].'&category=day_program\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='fund_development') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
						$num_rows = 0;
                        $result1 = mysqli_query($dbc, "select * from fund_development_funding where deleted=1 ORDER BY date_of_archival DESC");
						$num_rows += mysqli_num_rows($result1);

                        if($num_rows > 0) {
                            echo "<table border='2' cellpadding='10' class='table table-striped'>";
                            echo "<thead><tr>
                                    <th>Type</th>
                                    <th>Funding For</th>
                                    <th>Staff</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Date of Archival</th>";
                                    if($edit_access > 0) {
                                        echo "<th>Restore / Delete</th>";
                                    }
                            echo "</tr>";

                            while($row = mysqli_fetch_array( $result1 ))
                            {
                                echo '<tr>';
                                    echo '<td data-title="Tile">Funding</td>';
                                    echo '<td data-title="Tile">' . $row['funding_for'] . '</td>';
                                    echo '<td data-title="Tile">' . $row['first_name'] . '</td>';
                                    echo '<td data-title="Tile">' . $row['last_name'] . '</td>';
                                    echo '<td data-title="Tile">' . get_contact($dbc, $row['client_id']) . '</td>';
                                    echo '<td data-title="Tile">' . $row['email_address'] . '</td>';
                                    echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                                    if($edit_access > 0) {
                                    echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&fundingid='.$row['fundingid'].'&category=fund_development\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&fundingid='.$row['fundingid'].'&category=fund_development\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
                                    }
                                echo "</tr>";
                            }
                        }

						$result2 = mysqli_query($dbc, "select * from fund_development_funder where deleted=1");
						$num_rows2 += mysqli_num_rows($result2);
						if($num_rows2 > 0) {
                            echo "<table border='2' cellpadding='10' class='table table-striped'>";
                            echo "<thead><tr>
                                    <th>Type</th>
                                    <th>Funding For</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Client</th>
                                    <th>Email</th>
                                    <th>Date of Archival</th>";
                                if($edit_access > 0) {
                                    echo "<th>Restore / Delete</th>";
                                }
                            echo "</tr>";

                            while($row = mysqli_fetch_array( $result2 ))
                            {
                                echo '<tr>';
                                echo '<td data-title="Tile">Funders</td>';
                                echo '<td data-title="Tile">' . $row['funding_for'] . '</td>';
                                echo '<td data-title="Tile">' . get_contact($dbc, $row['staff']) . '</td>';
                                echo '<td data-title="Tile">' . $row['title'] . '</td>';
                                echo '<td data-title="Tile">' . $row['description'] . '</td>';
                                echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                                if($edit_access > 0) {
                                echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&fundingid='.$row['fundingid'].'&category=fund_development\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&fundingid='.$row['fundingid'].'&category=fund_development\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
                                }
                                echo "</tr>";
                            }
                        }

                        echo '</table>';

                        if ( $num_rows<=0 && $num_rows2<=0 ) {
                            echo "<h2>No Record Found.</h2>";
                        }
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='medication') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from medication where deleted=1");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Contact</th>
                                <th>Category</th>
                                <th>Heading</th>
								<th>Name</th>
								<th>Title</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . get_contact($dbc, $row['contactid']) . '</td>';
                            echo '<td data-title="Tile">' . $row['category'] . '</td>';
                            echo '<td data-title="Tile">' . $row['heading'] . '</td>';
							echo '<td data-title="Tile">' . $row['name'] . '</td>';
							echo '<td data-title="Tile">' . $row['title'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&medicationid='.$row['medicationid'].'&category=medication\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&medicationid='.$row['medicationid'].'&category=medication\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='individual_support_plan') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from individual_support_plan where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Category</th>
                                <th>Support Contact</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['support_contact_category'] . '</td>';
                            echo '<td data-title="Tile">' . get_contact($dbc, $row['contactid']) . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&individualsupportplanid='.$row['individualsupportplanid'].'&category=individual_support_plan\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&individualsupportplanid='.$row['individualsupportplanid'].'&category=individual_support_plan\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='social_story') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from key_methodologies where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Contact Category</th>
                                <th>Contact</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Tile">' . $row['contact_category'] . '</td>';
                            echo '<td data-title="Tile">' . get_contact($dbc, $row['contact']) . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&keymethodologiesid='.$row['keymethodologiesid'].'&category=social_story\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&keymethodologiesid='.$row['keymethodologiesid'].'&category=social_story\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>

			<?php if($current_tab =='preformance_review') { ?>
             <!-- Archived Staff Documents -->
            <div id="tab8" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "select * from performance_review where deleted=1 ORDER BY date_of_archival DESC");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table table-striped'>";
                        echo "<thead><tr>
                                <th>Staff</th>
                                <th>Position</th>
                                <th>Date Created</th>
                                <th>Date of Archival</th>";
                        if($edit_access > 0) {
                            echo "<th>Restore / Delete</th>";
                        }
						echo "</tr></thead>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td data-title="Staff">' . get_contact($dbc, $row['userid']) . '</td>';
                            echo '<td data-title="Position">' . $row['position'] . '</td>';
                            echo '<td data-title="Created Date">' . $row['today_date'] . '</td>';
                            echo '<td data-title="Date of Archival">' . $row['date_of_archival'] . '</td>';
                            if($edit_access > 0) {
                            echo '<td data-title="Restore"><a href=\'../delete_restore.php?action=restore&reviewid='.$row['reviewid'].'&category=social_story\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a> | <a href=\'../delete_restore.php?action=delete_2&reviewid='.$row['reviewid'].'&category=social_story\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a></td>';
	                        }
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Custom -->
            <?php } ?>
		</div>

    </div>

</div>

<script>
	// $('#myTab a').click(function (e) {
	//   e.preventDefault()
	//   $(this).tab('show')
	// })

 //    $('#myTab a').on('shown.bs.tab', function(e){
 //      //save the latest tab using a cookie:
 //      $.cookie('last_tab', $(e.target).attr('href'));
 //    });

 //    //activate latest tab, if it exists:
 //    var lastTab = $.cookie('last_tab');
 //    if (lastTab) {
 //        $('ul.nav-pills').children().removeClass('active');
 //        $('a[href='+ lastTab +']').parents('li:first').addClass('active');
 //        $('div.tab-content').children().removeClass('active');
 //        $(lastTab).addClass('active');
 //    }
</script>
<?php include ('../footer.php'); ?>
