<?php
/* Expenses Listing */
include ('../include.php');
checkAuthorised('expense');
error_reporting(0); ?>
<style>
	ul.chained-list{
		padding: 0.3em 0 !important;
	}
	.standard-collapsible:before {
	    height: calc(100vh - 43em) !important;
	}
</style>
</head>
<body>
<?php include_once ('../navigation.php');
$tab_config = ','.get_config($dbc, 'expense_tabs').','; ?>
<div class="container">
<div class="iframe_overlay" style="display:none;">
	<div class="container">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe src=""></iframe>
		</div>
	</div>
</div>
	<div class="row">
		<div class="main-screen" style="padding-bottom: 0px;">
			<?php if(get_config($dbc, 'expense_mode') == 'inbox') {
				$approvals = approval_visible_function($dbc, 'expense');
				$tab_counts = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(*) all, SUM(IF(`status` NOT IN ('Approved','Paid'),1,0)) pending, SUM(IF(`status` IN ('Approved'),1,0)) approved, SUM(IF(`status` IN ('Paid'),1,0)) paid, SUM(IF(`status` IN ('Declined'),1,0)) declined
					FROM `expense` WHERE (`staff`='{$_SESSION['contactid']}' OR '$approvals'='1') AND `deleted`=0")); ?>
				<div class="tile-header">
	                <h1 class="pull-left"><a href="">Expense Tracking</a><?php
	                    if(strpos($tab_config, 'report') !== false && check_subtab_persmission($dbc, 'expense', ROLE, 'report')) { ?>
	                        <div class="show-on-mob">
	                            <a href="" onclick="$(this).find('img').toggleClass('counterclockwise'); $('.report_link').toggleClass('hide-titles-mob'); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/dropdown-arrow.png" style="height: 0.5em;" class="counterclockwise"></a>
	                        </div><?php
	                    } ?>
	                </h1>

	                <div class="pull-right settings-block">
	                <?php
	                if(config_visible_function($dbc, 'expense') == 1) {
	                    echo '<div class="pull-right not_filter">';
	                        echo '<span class="popover-examples list-inline" style="margin: 0.2em;line-height: 0;"></span>';
	                        echo '<a href="?tab=settings" class="mobile-block"><img title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>';
	                    echo '</div>';
	                }
	                if($_GET['edit'] == '') { ?>
	                    <div class="pull-right hide-titles-mob gap-left">
	                        <script> toggle_filter = function() { } </script>
	                        <a href="" class="btn brand-btn mobile-block gap-bottom pull-right toggle-filter" onclick="toggle_filter(); return false;">Filter Expenses</a>
	                        <span class="popover-examples list-inline pull-right" style="margin:-3px 3px 0 0;"></span>
	                    </div><?php
	                }
	                
	                if(vuaed_visible_function($dbc, 'expense')) { ?>
	                    <div class="pull-right not_filter hide-titles-mob">
	                        <a href="" onclick="overlayIFrame('edit_expense.php'); return false;" class="btn brand-btn mobile-block gap-bottom pull-right">New Expense</a>
	                        <span class="popover-examples list-inline pull-right" style="margin:-3px 3px 0 0;"></span>
	                    </div>
	                    <div class="pull-right not_filter show-on-mob" style="margin: 0 0.25em;">
	                        <span class="popover-examples list-inline" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Checklist."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>
	                        <a href="" onclick="overlayIFrame('edit_expense.php'); return false;"><img src="../img/icons/ROOK-add-icon.png" style="width:1em;"></a>
	                    </div><?php
	                }?>
	                </div>
	                <div class="clearfix"></div>
	            </div>
	            
				<div class="notice double-gap-bottom popover-examples">
					<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
					<div class="col-sm-11"><span class="notice-name">NOTE:</span>
					<?= (empty($_GET['tab']) ? (empty($_GET['edit']) ? "Add Expenses here to submit for reimbursement." : "Enter the details of the expense, and upload a receipt.") :
						($_GET['tab'] == 'reports' ? "View reports of the expenses that have been submitted." : ($_GET['tab'] == 'settings' ? "Configure the settings for Expenses." : ($_GET['tab'] == 'policy' ? "View and define the rules for submitting expenses." : "Please select a type of expense."))) ) ?></div>
					<div class="clearfix"></div>
				</div>
	            
				<div class="tile-sidebar standard-collapsible hide-titles-mob double-gap-top"  style="overflow-y:auto;">
					<ul>
						<?php if($_GET['tab'] == 'settings'){ ?>
							<a href="<?php echo WEBSITE_URL?>/Expense/expenses.php?filter_id=all"><li class="">Back to Dashboard</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.category_div').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue'); $('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="categorySettingTab"><li class="active blue">Categories</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.heading_div').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue');$('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="headingSettingTab"><li class="">Headings</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.manage_levels').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue');$('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="approvalSettingTab"><li class="">Level of Approval</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.expense_defaults_div').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue');$('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="defaultFieldsSettingTab"><li class="">Expense Default Fields</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.expense_fields_div').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue');$('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="expenseFieldsSettingTab"><li class="">Expense Fields</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.email_reminders_div').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue');$('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="emailReminderSettingTab"><li class="">Email Reminders</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.exchange_rates_div').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue');$('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="exchangeRatesSettingTab"><li class="">Exchange Rates</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.team_fields_div').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue');$('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="teamFieldsSettingTab"><li class="">Team Fields</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.tax_defaults_div').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue');$('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="taxDefaultsSettingTab"><li class="">Tax Defaults</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.style_div').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue');$('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="expenseStylingSettingTab"><li class="">Expense Styling</li></a>
							<a href="" onclick="$('.setting-fields>div:visible').hide(); $('.pdf_div').show(); $('.active.blue').removeClass('active blue'); $(this).find('li').addClass('active blue');$('#settingTitle').html($(this).text());$('[name=settingTabId]').val($(this).attr('id')); return false;" id="pdfSettingTab"><li class="">PDF Settings</li></a>
						<?php } else{?>
							<div>
								<li class="sidebar-higher-level highest-level"><a data-target="#all_cats" data-toggle="collapse" class="cursor-hand <?= $_GET['filter_id'] == 'all' ? 'active' : 'collapsed' ?>">My Expenses<span class="arrow"></span></a>
									<ul id="all_cats" class="collapse <?= $_GET['filter_id'] == 'all' ? 'in' : '' ?>">
										<a href="?filter_id=all"><li class="sidebar-higher-level <?= $_GET['filter_id'] == 'all' && empty($_GET['filter_cat']) ? 'active' : '' ?>">All Expenses<span class='pull-right'><?= $tab_counts['all'] ?></span></li></a>
									</ul>
								</li>
							</div>
							<div>
								<li class="sidebar-higher-level highest-level"><a data-target="#pending_cats" data-toggle="collapse" class="cursor-hand <?= $_GET['filter_id'] == 'pending' ? 'active' : 'collapsed' ?>">Pending<span class="arrow"></span></a>
									<ul id="pending_cats" class="collapse <?= $_GET['filter_id'] == 'pending' ? 'in' : '' ?>">
										<?php $pending_cats = $dbc->query("SELECT `categories`.`EC` `category`, CONCAT(`categories`.`EC`,': ',`expense`.`category`) `descript`, COUNT(*) `count` FROM `expense` LEFT JOIN (SELECT `EC`,`category` FROM `expense_categories` WHERE `deleted`=0 GROUP BY `category`) `categories` ON `expense`.`category`=`categories`.`category` WHERE `deleted`=0 AND `reimburse` > 0 AND IFNULL(`categories`.`category`,'') != '' AND `status` NOT IN ('Approved','Paid') ".($approvals > 0 ? '' : "AND `staff` IN ('{$_SESSION['contactid']}','".get_contact($dbc, $_SESSION['contactid'])."')")." GROUP BY `expense`.`category` ORDER BY `categories`.`EC`");
										while($pending_cat = $pending_cats->fetch_assoc()) { ?>
											<a href="?filter_id=pending&filter_cat=<?= $pending_cat['category'] ?>"><li class="sidebar-higher-level <?= $_GET['filter_id'] == 'pending' && $_GET['filter_cat'] == $pending_cat['category'] ? 'active' : '' ?>"><?= $pending_cat['descript'] ?><span class='pull-right'><?= $pending_cat['count'] ?></span></li></a>
										<?php } ?>
										<!-- <a href="?filter_id=pending"><li class="sidebar-higher-level <?= $_GET['filter_id'] == 'pending' && empty($_GET['filter_cat']) && empty($_GET['filter_tab']) ? 'active' : '' ?>">All Expenses<span class='pull-right'><?= $tab_counts['pending'] ?></span></li></a> -->
										<?php 
		            					$approval_levels = mysqli_query($dbc, "SELECT a.expense_approval_role_id, a.expense_role_sorting, b.label FROM `expense_approval_levels` a inner join `security_level_names` b on a.expense_approval_role_id = b.id ORDER BY expense_role_sorting");
		            					while($app_row = mysqli_fetch_assoc($approval_levels)){
		            					?>
		            					<a href="?filter_id=pending&filter_tab=<?= $app_row['expense_approval_role_id'].'_'.$app_row['expense_role_sorting']?>"><li class="sidebar-higher-level <?= $_GET['filter_tab'] == $app_row['expense_approval_role_id'].'_'.$app_row['expense_role_sorting'] && empty($_GET['filter_cat']) ? 'active' : '' ?>"><?= $app_row['label']?> Approval<span class='pull-right'><?= $tab_counts['pending'] ?></span></li></a>
		            					<?php } ?>
									</ul>
								</li>
							</div>
							
							<?php 
							/*$approval_levels = mysqli_query($dbc, "SELECT a.expense_role_sorting, b.label FROM `expense_approval_levels` a inner join `security_level_names` b on a.expense_approval_role_id = b.id ORDER BY expense_role_sorting");
							while($app_row = mysqli_fetch_assoc($approval_levels)){*/
							?>
							<!-- <div>
								<span class="popover-examples pull-left inline-img"><a data-toggle="tooltip" data-placement="top" title="View all Expenses that have been approved and are awaiting payment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<li class="sidebar-higher-level highest-level"><a data-target="#role_approved_<?= $app_row['expense_role_sorting']?>" data-toggle="collapse" class="cursor-hand <?= $_GET['filter_id'] == 'approved' ? 'active' : 'collapsed' ?>"><?=$app_row['label']?> <br> &nbsp;Approved<span class="arrow"></span></a>
									<ul id="role_approved_<?= $app_row['expense_role_sorting']?>" class="collapse <?= $_GET['filter_id'] == $app_row['label'].'_'.$app_row['expense_role_sorting'] ? 'in' : '' ?>">
										<?php $approved_cats = $dbc->query("SELECT `categories`.`EC` `category`, CONCAT(`categories`.`EC`,': ',`expense`.`category`) `descript`, COUNT(*) `count` FROM `expense` LEFT JOIN (SELECT `EC`,`category` FROM `expense_categories` WHERE `deleted`=0 GROUP BY `category`) `categories` ON `expense`.`category`=`categories`.`category` WHERE `deleted`=0 AND `reimburse` > 0 AND IFNULL(`categories`.`category`,'') != '' AND `status` IN ('Approved') ".($approvals > 0 ? '' : "AND `staff` IN ('{$_SESSION['contactid']}','".get_contact($dbc, $_SESSION['contactid'])."')")." GROUP BY `expense`.`category` ORDER BY `categories`.`EC`");
										while($approved_cat = $approved_cats->fetch_assoc()) { ?>
											<a href="?filter_id=<?= $app_row['label'].'_'.$app_row['expense_role_sorting']?>&filter_cat=<?= $approved_cat['category'] ?>"><li class="sidebar-higher-level <?= $_GET['filter_id'] == $app_row['label'].'_'.$app_row['expense_role_sorting'] && $_GET['filter_cat'] == $approved_cat['category'] ? 'active' : '' ?>"><?= $approved_cat['descript'] ?><span class='pull-right'><?= $approved_cat['count'] ?></span></li></a>
										<?php } ?>
										<a href="?filter_id=<?= $app_row['label'].'_'.$app_row['expense_role_sorting']?>"><li class="sidebar-higher-level <?= $_GET['filter_id'] == $app_row['label'].'_'.$app_row['expense_role_sorting'] && empty($_GET['filter_cat']) ? 'active' : '' ?>">All Expenses<span class='pull-right'><?= $tab_counts['approved'] ?></span></li></a>
									</ul>
								</li>
							</div> -->
							<?php
							//}
							?>
							
							<div>
								<li class="sidebar-higher-level highest-level"><a data-target="#approved_cats" data-toggle="collapse" class="cursor-hand <?= $_GET['filter_id'] == 'approved' ? 'active' : 'collapsed' ?>">Approved<span class="arrow"></span></a>
									<ul id="approved_cats" class="collapse <?= $_GET['filter_id'] == 'approved' ? 'in' : '' ?>">
										<?php $approved_cats = $dbc->query("SELECT `categories`.`EC` `category`, CONCAT(`categories`.`EC`,': ',`expense`.`category`) `descript`, COUNT(*) `count` FROM `expense` LEFT JOIN (SELECT `EC`,`category` FROM `expense_categories` WHERE `deleted`=0 GROUP BY `category`) `categories` ON `expense`.`category`=`categories`.`category` WHERE `deleted`=0 AND `reimburse` > 0 AND IFNULL(`categories`.`category`,'') != '' AND `status` IN ('Approved') ".($approvals > 0 ? '' : "AND `staff` IN ('{$_SESSION['contactid']}','".get_contact($dbc, $_SESSION['contactid'])."')")." GROUP BY `expense`.`category` ORDER BY `categories`.`EC`");
										while($approved_cat = $approved_cats->fetch_assoc()) { ?>
											<a href="?filter_id=approved&filter_cat=<?= $approved_cat['category'] ?>"><li class="sidebar-higher-level <?= $_GET['filter_id'] == 'approved' && $_GET['filter_cat'] == $approved_cat['category'] ? 'active' : '' ?>"><?= $approved_cat['descript'] ?><span class='pull-right'><?= $approved_cat['count'] ?></span></li></a>
										<?php } ?>
										<a href="?filter_id=approved"><li class="sidebar-higher-level <?= $_GET['filter_id'] == 'approved' && empty($_GET['filter_cat']) ? 'active' : '' ?>">All Expenses<span class='pull-right'><?= $tab_counts['approved'] ?></span></li></a>
									</ul>
								</li>
							</div>
							<div>
								<li class="sidebar-higher-level highest-level"><a data-target="#paid_cats" data-toggle="collapse" class="cursor-hand <?= $_GET['filter_id'] == 'paid' ? 'active' : 'collapsed' ?>">Paid<span class="arrow"></span></a>
									<ul id="paid_cats" class="collapse <?= $_GET['filter_id'] == 'paid' ? 'in' : '' ?>">
										<?php $paid_cats = $dbc->query("SELECT `categories`.`EC` `category`, CONCAT(`categories`.`EC`,': ',`expense`.`category`) `descript`, COUNT(*) `count` FROM `expense` LEFT JOIN (SELECT `EC`,`category` FROM `expense_categories` WHERE `deleted`=0 GROUP BY `category`) `categories` ON `expense`.`category`=`categories`.`category` WHERE `deleted`=0 AND `reimburse` > 0 AND IFNULL(`categories`.`category`,'') != '' AND `status` IN ('Paid') ".($approvals > 0 ? '' : "AND `staff` IN ('{$_SESSION['contactid']}','".get_contact($dbc, $_SESSION['contactid'])."')")." GROUP BY `expense`.`category` ORDER BY `categories`.`EC`");
										while($paid_cat = $paid_cats->fetch_assoc()) { ?>
											<a href="?filter_id=paid&filter_cat=<?= $paid_cat['category'] ?>"><li class="sidebar-higher-level <?= $_GET['filter_id'] == 'paid' && $_GET['filter_cat'] == $paid_cat['category'] ? 'active' : '' ?>"><?= $paid_cat['descript'] ?><span class='pull-right'><?= $paid_cat['count'] ?></span></li></a>
										<?php } ?>
										<a href="?filter_id=paid"><li class="sidebar-higher-level <?= $_GET['filter_id'] == 'paid' && empty($_GET['filter_cat']) ? 'active' : '' ?>">All Expenses<span class='pull-right'><?= $tab_counts['paid'] ?></span></li></a>
									</ul>
								</li>
							</div>
							<div>
								<li class="sidebar-higher-level highest-level"><a data-target="#declined_cats" data-toggle="collapse" class="cursor-hand <?= $_GET['filter_id'] == 'declined' ? 'active' : 'collapsed' ?>">Declined<span class="arrow"></span></a>
									<ul id="declined_cats" class="collapse <?= $_GET['filter_id'] == 'declined' ? 'in' : '' ?>">
										<?php $declined_cats = $dbc->query("SELECT `categories`.`EC` `category`, CONCAT(`categories`.`EC`,': ',`expense`.`category`) `descript`, COUNT(*) `count` FROM `expense` LEFT JOIN (SELECT `EC`,`category` FROM `expense_categories` WHERE `deleted`=0 GROUP BY `category`) `categories` ON `expense`.`category`=`categories`.`category` WHERE `deleted`=0 AND `reimburse` > 0 AND IFNULL(`categories`.`category`,'') != '' AND `status` IN ('Declined')  ".($approvals > 0 ? '' : "AND `staff` IN ('{$_SESSION['contactid']}','".get_contact($dbc, $_SESSION['contactid'])."')")." GROUP BY `expense`.`category` ORDER BY `categories`.`EC`");
										while($declined_cat = $declined_cats->fetch_assoc()) { ?>
											<a href="?filter_id=declined&filter_cat=<?= $declined_cat['category'] ?>"><li class="sidebar-higher-level <?= $_GET['filter_id'] == 'declined' && $_GET['filter_cat'] == $declined_cat['category'] ? 'active' : '' ?>"><?= $declined_cat['descript'] ?><span class='pull-right'><?= $declined_cat['count'] ?></span></li></a>
										<?php } ?>
										<a href="?filter_id=declined"><li class="sidebar-higher-level <?= $_GET['filter_id'] == 'declined' && empty($_GET['filter_cat']) ? 'active' : '' ?>">All Expenses<span class='pull-right'><?= $tab_counts['declined'] ?></span></li></a>
									</ul>
								</li>
							</div>
							<?php $query_retrieve_subtabs = mysqli_query($dbc, "SELECT * FROM `expense_filters` WHERE `owner` IN (".$_SESSION['contactid'].",0) AND `deleted`=0");
							while ($row = mysqli_fetch_array($query_retrieve_subtabs)) {
								echo "<a href='?filter_id={$row['filter_id']}'><li ".($_GET['filter_id'] == $row['filter_id'] ? 'class="active custom"' : 'class="custom"').">{$row['filter_name']}<span class='pull-right'></span></li></a>";
							} ?>
							<div>
								<li class="sidebar-higher-level highest-level <?= $_GET['tab'] == 'policy' ? 'active blue' : '' ?>">
									<a href="?tab=policy">Expense Policies</a>
								</li>
							</div>
							<?php if(strpos($tab_config, 'report') !== false && check_subtab_persmission($dbc, 'expense', ROLE, 'report')) { ?>
								<div>
									<li class="sidebar-higher-level highest-level <?= ($_GET['tab'] == 'reports' ? 'active blue' : '') ?>">
										<a href="?tab=reports">Reporting</a>
									</li>
								</div><?php
			                }
		                }?>
					</ul>
				</div>
	            
	            <div class="has-main-screen scale-to-fill hide-titles-mob">
					<div class="main-screen standard-dashboard-body">
					    <div class="standard-body-title">
					        <h3 class="pull-left">
								<?php
				                    if($_GET['tab'] == 'reports') {
				                        $selectedTabTitle = 'Reporting';
				                    } else if($_GET['tab'] == 'policy') {
				                        $selectedTabTitle = 'Expense Policies';
				                    } else if($_GET['tab'] == 'settings') {
				                        $selectedTabTitle = 'Settings - <span id="settingTitle">Categories</span>';
				                    } else if($_GET['filter_id'] == 'all') {
				                        $selectedTabTitle = 'My Expenses';
				                    } else if($_GET['filter_id'] == 'pending') {
				                        $selectedTabTitle = 'Pending';
				                    } else if($_GET['filter_id'] == 'approved') {
				                        $selectedTabTitle = 'Approved';
				                    } else if($_GET['filter_id'] == 'paid') {
				                        $selectedTabTitle = 'Paid';
				                    } else if($_GET['filter_id'] == 'declined') {
				                        $selectedTabTitle = 'Declined';
				                    } else {
				                        $selectedTabTitle = 'My Expenses';
				                    }
				                    echo $selectedTabTitle;
			                    ?>
					        </h3>
					        <?php
		                    if($_GET['tab'] == 'reports') { ?>
					        <a href="?<?= http_build_query($_GET) ?>&output=pdf"><img class="text-lg inline-img pull-right no-toggle" title="Download Report as PDF" src="../img/icons/ROOK-download-icon.png"></a>
					        <?php } ?>
					        <div class="clearfix"></div>
					    </div>
					    <div class="standard-body-content full-height">
				        	<?php
			                    if(!empty($_GET['edit'])) {
			                        include('edit_expense.php');
			                    } else if($_GET['tab'] == 'reports') {
			                        include('expense_reports.php');
			                    } else if($_GET['tab'] == 'settings') {
			                        include('field_config_inbox.php');
			                    } else if($_GET['tab'] == 'policy') {
			                        include('expense_policy.php');
			                    } else {
			                        include('expense_list.php');
			                    } 
		                    ?>
				        </div>
			        </div>
		        </div>
			<?php } else {
				if(empty($_GET['tab'])) {
					$current_tab = 'current_month';
				} else {
					$current_tab = $_GET['tab'];
				}
				if(strpos($tab_config,','.$current_tab.',') === false) {
					$current_tab = explode(',',trim($tab_config,','))[0];
				}

				switch($current_tab) {
					case 'current_month':
						$current_tab_name = 'Current Month Expenses';
						break;
					case 'budget':
						$current_tab_name = 'Budget Expense Tracking';
						break;
					case 'business':
						$current_tab_name = 'Business Expenses';
						break;
					case 'customers':
						$current_tab_name = 'Customer Expenses';
						break;
					case 'clients':
						$current_tab_name = 'Client Expenses';
						break;
					case 'staff':
						$current_tab_name = 'Staff Expenses';
						break;
					case 'sales':
						$current_tab_name = 'Sales Lead Expenses';
						break;
					case 'manager':
						$current_tab_name = 'Manager Approval';
						break;
					case 'payables':
						$current_tab_name = 'Payables';
						break;
					case 'report':
						$current_tab_name = 'Expense Reporting';
						break;
				}
				?>

				<h1 class="double-gap-bottom"><?php echo $current_tab_name; ?> Dashboard<?php if(config_visible_function($dbc, 'expense') == 1) {
					echo '<a href="field_config_expense.php?tab='.$current_tab.'" class="mobile-block pull-right"><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
				} ?></h1>
				<div class="clearfix"></div>
				
				<div class="tab-container mobile-100-container">
					<?php if ( check_subtab_persmission($dbc, 'expense', ROLE, 'budget') === TRUE && strpos($tab_config,',budget,') !== FALSE) { ?>
						<a href="expenses.php?tab=budget"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'budget' ? 'active_tab' : ''); ?>">Budget Expense Tracking</button></a>
					<?php }
					if ( check_subtab_persmission($dbc, 'expense', ROLE, 'current_month') === TRUE && strpos($tab_config,',current_month,') !== FALSE) { ?>
						<a href="expenses.php?tab=current_month"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'current_month' ? 'active_tab' : ''); ?>">Current Month</button></a>
					<?php }
					if ( check_subtab_persmission($dbc, 'expense', ROLE, 'business') === TRUE && strpos($tab_config,',business,') !== FALSE) { ?>
						<a href="expenses.php?tab=business"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'business' ? 'active_tab' : ''); ?>">Business</button></a>
					<?php }
					if ( check_subtab_persmission($dbc, 'expense', ROLE, 'customers') === TRUE && strpos($tab_config,',customers,') !== FALSE) { ?>
						<a href="expenses.php?tab=customers"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'customers' ? 'active_tab' : ''); ?>">Customers</button></a>
					<?php }
					if ( check_subtab_persmission($dbc, 'expense', ROLE, 'clients') === TRUE && strpos($tab_config,',clients,') !== FALSE) { ?>
						<a href="expenses.php?tab=clients"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'clients' ? 'active_tab' : ''); ?>">Clients</button></a>
					<?php }
					if ( check_subtab_persmission($dbc, 'expense', ROLE, 'staff') === TRUE && strpos($tab_config,',staff,') !== FALSE) { ?>
						<a href="expenses.php?tab=staff"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'staff' ? 'active_tab' : ''); ?>">Staff</button></a>
					<?php }
					if ( check_subtab_persmission($dbc, 'expense', ROLE, 'sales') === TRUE && strpos($tab_config,',sales,') !== FALSE) { ?>
						<a href="expenses.php?tab=sales"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'sales' ? 'active_tab' : ''); ?>">Sales Leads</button></a>
					<?php }
					if ( check_subtab_persmission($dbc, 'expense', ROLE, 'manager') === TRUE && strpos($tab_config,',manager,') !== FALSE) { ?>
						<a href="expenses.php?tab=manager"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'manager' ? 'active_tab' : ''); ?>">Manager Approval</button></a>
					<?php }
					if ( check_subtab_persmission($dbc, 'expense', ROLE, 'payables') === TRUE && strpos($tab_config,',payables,') !== FALSE) { ?>
						<a href="expenses.php?tab=payables"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'payables' ? 'active_tab' : ''); ?>">Payables</button></a>
					<?php }
					if ( check_subtab_persmission($dbc, 'expense', ROLE, 'report') === TRUE && strpos($tab_config,',report,') !== FALSE) { ?>
						<a href="expenses.php?tab=report"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'report' ? 'active_tab' : ''); ?>">Reporting</button></a>
					<?php } ?>
				</div>
					
				<div id="no-more-tables">
					<?php include($current_tab.'.php'); ?>
				</div>
			<?php } ?>
	        <div class="clearfix"></div>
	    </div>
	</div>
</div>
<?php include ('../footer.php'); ?>