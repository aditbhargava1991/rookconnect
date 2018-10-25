<?php
// Define a function that will display a chosen tile_name
function tile_data($dbc, $tile_name, $is_mobile = FALSE) {
	// Call tile_visible to check if the user has access to the tile, and check_subtab_persmission for Project types or other sub tabs with dedicated tiles
	// tile_visible has a few tiles that it does manual role checks to verify
	$tile = $tile_name;
	$sub_tile = '';
    if(is_array($tile_name)) {
		$tile = $tile_name[0];
		$sub_tile = config_safe_str($tile_name[1]);
	}
	if(((tile_visible($dbc, $tile) || (is_array($tile_name) && tile_visible($dbc, $tile.'_'.$sub_tile, ROLE, $tile))) && ($sub_tile == '' || ($tile != 'project' && check_subtab_persmission($dbc, $tile, ROLE, $sub_tile)))) || ($tile == 'project' && $sub_tile != '' && tile_visible($dbc, 'project_type_'.$sub_tile, ROLE, 'project') && check_subtab_persmission($dbc, $tile, ROLE, $sub_tile)) || ($tile == 'ticket' && $sub_tile != '' && tile_visible($dbc, 'ticket_type_'.$sub_tile, ROLE, 'ticket') && check_subtab_persmission($dbc, $tile, ROLE, $sub_tile)) || ($tile == 'ticket_group' && $sub_tile != '' && tile_visible($dbc, 'ticket_tile_'.$sub_tile, ROLE, 'ticket'))) {
		switch($tile) {
			case 'start_day_button': return ['link'=>'Timesheet/start_day.php','name'=>START_DAY]; break;
			case 'admin_settings': return ['link'=>'admin_software_config.php','name'=>'Admin Settings']; break;
			case 'software_config': return ['link'=>'Settings/settings.php','name'=>'Settings']; break;
			case 'security': return ['link'=>"Security/security.php",'name'=>'Security']; break;
			case 'contacts': return ['link'=>"Contacts/contacts.php?filter=Top",'name'=>'Contacts (Tabbed View)']; break;
			case 'contacts_inbox': return ['link'=>"Contacts/contacts_inbox.php?list=summary&status=summary",'name'=>CONTACTS_TILE]; break;
			case 'contacts3': return ['link'=>"Contacts3/contacts_inbox.php?list=summary&status=summary",'name'=>'Contacts']; break;
			case 'client_info': return ['link'=>"ClientInfo/contacts_inbox.php?list=summary&status=summary",'name'=>'Client Information']; break;
			case 'contacts_rolodex': return ['link'=>"ContactsRolodex/contacts_inbox.php?list=summary&status=summary",'name'=>'Contacts Rolodex']; break;
			case 'staff': return ['link'=>"Staff/staff.php",'name'=>'Staff']; break;
			case 'orientation': return ['link'=>"Orientation/orientation.php",'name'=>'Orientation']; break;
			case 'infogathering': return ['link'=>"Information Gathering/infogathering.php",'name'=>'Information Gathering']; break;
			case 'agenda_meeting': return ['link'=>"Agenda Meetings/agenda.php",'name'=>'Agendas & Meetings']; break;
			case 'sales': return ['link'=>"Sales/index.php",'name'=>SALES_TILE]; break;
			case 'certificate': return ['link'=>"Certificate/index.php",'name'=>'Certificates']; break;
			case 'contracts': return ['link'=>"Contract/index.php",'name'=>'Contracts']; break;
			case 'driving_log': return ['link'=>"Driving Log/driving_log_tiles.php",'name'=>'Driving Log']; break;
			case 'package': return ['link'=>"Package/package.php",'name'=>'Packages']; break;
			case 'promotion': return ['link'=>"Promotion/promotion.php",'name'=>'Promotions & Coupons']; break;
			//case 'services': return ['link'=>"Services/services.php?category=3D Printing",'name'=>'Services']; break;
            case 'services': return ['link'=>"Services/index.php",'name'=>'Services']; break;
			case 'products': return ['link'=>"Products/products.php",'name'=>'Products']; break;
			case 'labour': return ['link'=>"Labour/index.php",'name'=>'Labour']; break;
			case 'material': return ['link'=>"Material/material.php?filter=Top",'name'=>'Materials']; break;
			case 'inventory': return ['link'=>"Inventory/inventory.php?category=Top",'name'=>INVENTORY_TILE]; break;
			// case 'vpl': return ['link'=>"Vendor Price List/inventory.php?category=Top",'name'=>get_tile_title_vpl($dbc).'']; break;
			case 'assets': return ['link'=>"Asset/asset.php?category=Top",'name'=>'Assets']; break;
			case 'equipment': return ['link'=>"Equipment/index.php?category=Top",'name'=>'Equipment']; break;
			case 'custom': return ['link'=>"Custom/custom.php",'name'=>'Custom']; break;
			case 'intake': return ['link'=>"Intake/intake.php",'name'=>'Intake Forms']; break;
			//case 'pos': return ['link'=>"Point of Sale/add_point_of_sell.php",'name'=>'Point of Sale<br /><small>Basic</small>']; break;
			case 'posadvanced': return ['link'=>"POSAdvanced/invoice_main.php",'name'=>POS_ADVANCE_TILE]; break;
			case 'invoicing': return ['link'=>"Invoicing/add_point_of_sell.php",'name'=>'Invoicing']; break;
			case 'service_queue': return ['link'=>"Service Queue/service_queue.php",'name'=>'Service Queue']; break;
			case 'incident_report': return ['link'=>"Incident Report/incident_report.php",'name'=>INC_REP_TILE]; break;
			case 'policy_procedure': return ['link'=>"Manuals/policy_procedures.php?category=".mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `manuals` WHERE `deleted`=0 AND `manual_type`='policy_procedures' UNION SELECT '0' `category` LIMIT 1"))['category'],'name'=>'Policies & Procedures']; break;
			case 'ops_manual': return ['link'=>"Manuals/operations_manual.php?category=".mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `manuals` WHERE `deleted`=0 AND `manual_type`='ops_manual' UNION SELECT '0' `category` LIMIT 1"))['category'],'name'=>'Operations Manual']; break;
			case 'emp_handbook': return ['link'=>"Manuals/emp_handbook.php?category=".mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `manuals` WHERE `deleted`=0 AND `manual_type`='emp_handbook' UNION SELECT '0' `category` LIMIT 1"))['category'],'name'=>'Employee Handbook']; break;
			case 'how_to_checklist': return ['link'=>"Manuals/guide.php?category=".mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `manuals` WHERE `deleted`=0 AND `manual_type`='how_to_checklist' UNION SELECT '0' `category` LIMIT 1"))['category'],'name'=>'How To Checklist']; break;
			case 'manual': return ['link'=>"Manuals/manual.php?filter=Top&maintype=pp",'name'=>'Manuals']; break;
			//case 'safety': return ['link'=>"Safety/safety.php?tab=".mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `tab` FROM `safety` WHERE `deleted`=0 UNION SELECT '0' `tab` LIMIT 1"))['tab'],'name'=>'Safety']; break;
			case 'safety':
                $get_safety_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) AS `configid`, `value` FROM `general_configuration` WHERE `name`='safety_dashboard'"));
                if ($get_safety_config['configid'] > 0) {
                    if ($get_safety_config['value'] == '' || $get_safety_config['value'] == ',,') {
                        mysqli_query($dbc, "UPDATE `general_configuration` SET `value`=',Toolbox,Tailgate,Forms,Manuals,' WHERE `name`='safety_dashboard'");
                    }
                } else {
                    mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('safety_dashboard', ',Toolbox,Tailgate,Forms,Manuals,')");
                }
                return ['link'=>"Safety/index.php",'name'=>'Safety'];
                break;
            case 'rate_card': return ['link'=>"Rate Card/ratecards.php",'name'=>'Rate Cards']; break;
			case 'estimate': return ['link'=>"Estimate/estimates.php",'name'=>ESTIMATE_TILE]; break;
			case 'field_ticket_estimates': return ['link'=>"Field Ticket Estimates/estimate.php",'name'=>'Field Ticket Estimates']; break;
			case 'site_work_orders': return ['link'=>"Site Work Orders/site_work_orders.php",'name'=>'Site Work Orders']; break;
			case 'shop_work_orders': return ['link'=>"Project Workflow/project_workflow_dashboard.php?tile=Shop Work Orders",'name'=>'Shop Work Orders']; break;
			case 'daysheet': return ['link'=>"Daysheet/daysheet.php",'name'=>'Planner']; break;
			case 'time_tracking': return ['link'=>"Time Tracking/time_tracking.php?type=TimeSheet",'name'=>'Time Tracking']; break;
			case 'calllog': return ['link'=>"Cold Call/call_log.php",'name'=>'Cold Call']; break;
			case 'budget': return ['link'=>"Budget/budget.php",'name'=>'Budget']; break;
			case 'profit_loss': return ['link'=>"ProfitLoss/profit_loss.php",'name'=>'Profit & Loss']; break;
			case 'gao': return ['link'=>"Gao/gao.php",'name'=>'Goals & Objectives']; break;

			case 'checklist':
            /* $get_checklist = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(checklistid) AS checklistid FROM checklist WHERE (`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `assign_staff`=',ALL,') AND `deleted`=0 AND checklist_tile=1"));
            if($get_checklist['checklistid'] > 0) {
               $checklist_url = 'checklist_tile.php';
            } else {
               $checklist_url = 'checklist.php';
            } */
            $checklist_url = 'checklist.php';
            return ['link'=>'Checklist/'.$checklist_url,'name'=>'Checklist']; break;

			case 'tasks': return ['link'=>"Tasks_Updated/index.php?category=All&tab=Summary",'name'=>TASK_TILE]; break;
			//case 'tasks_updated': return ['link'=>"Tasks_Updated/index.php?category=All&tab=Summary",'name'=>'Tasks (Updated)']; break;
			case 'scrum': return ['link'=>"Scrum/scrum.php?category=All",'name'=>'Scrum']; break;
			case 'communication': return ['link'=>"Communication/tasks.php?category=All",'name'=>'Communication Tasks']; break;
			case 'communication_schedule': return ['link'=>"Communication Schedule/communication.php",'name'=>'Communication']; break;
			//case 'email_communication': return ['link'=>"Email Communication/email_communication.php?type=Internal",'name'=>'Email Communication']; break;
			case 'email_communication': return ['link'=>"Email Communication/index.php",'name'=>'Email Communication']; break;
			case 'phone_communication': return ['link'=>"Phone Communication/phone_communication.php?type=Internal",'name'=>'Phone Communication']; break;
			case 'punch_card': return ['link'=>"Punch Card/punch_card.php?title=time_clock",'name'=>'Time Clock']; break;
			case 'sign_in_time': return ['link'=>"Punch Card/punch_card.php?title=sign_in",'name'=>'Sign In']; break;
			case 'payroll': return ['link'=>"Payroll/payroll.php",'name'=>'Payroll']; break;
			case 'purchase_order': return ['link'=>"Purchase Order/index.php",'name'=>get_tile_title_po($dbc).'']; break;
			case 'sales_order': return ['link'=>"Sales Order/index.php",'name'=>SALES_ORDER_TILE]; break;
			case 'newsboard': return ['link'=>"News Board/index.php",'name'=>'News Board']; break;
			case 'calendar_rook': return ['link'=>"Calendar/calendars.php",'name'=>'Calendar']; break;
			case 'field_job': return ['link'=>"Field Jobs/field_sites.php",'name'=>'Field Jobs']; break;
			case 'expense': return ['link'=>"Expense/expenses.php?filter_id=all",'name'=>'Expenses']; break;
			case 'payables': return ['link'=>"Payables/payables.php",'name'=>"Payables"]; break;
			case 'billing': return ['link'=>"Project Billing/project_billing.php",'name'=>"Project Billing & Invoices"]; break;
			case 'report':
				include('Reports/field_list.php');

                $reports_url = 'report_tiles.php';

                if ($is_mobile) {
                    $reports_landing_subtab_config = get_config($dbc, 'reports_mobile_landing_subtab');
                } else {
                    $reports_landing_subtab_config = get_config($dbc, 'reports_desktop_landing_subtab');
                }
                if(array_key_exists($reports_landing_subtab_config,$operations_reports) !== FALSE) {
                	$reports_url .= '?type=operations&report='.$reports_landing_subtab_config.'&landing=true';
                } else if(array_key_exists($reports_landing_subtab_config,$sales_reports) !== FALSE) {
                	$reports_url .= '?type=sales&report='.$reports_landing_subtab_config.'&landing=true';
                } else if(array_key_exists($reports_landing_subtab_config,$ar_reports) !== FALSE) {
                	$reports_url .= '?type=ar&report='.$reports_landing_subtab_config.'&landing=true';
                } else if(array_key_exists($reports_landing_subtab_config,$pnl_reports) !== FALSE) {
                	$reports_url .= '?type=pnl&report='.$reports_landing_subtab_config.'&landing=true';
                } else if(array_key_exists($reports_landing_subtab_config,$marketing_reports) !== FALSE) {
                	$reports_url .= '?type=marketing&report='.$reports_landing_subtab_config.'&landing=true';
                } else if(array_key_exists($reports_landing_subtab_config,$compensation_reports) !== FALSE) {
                	$reports_url .= '?type=compensation&report='.$reports_landing_subtab_config.'&landing=true';
                } else if(array_key_exists($reports_landing_subtab_config,$customer_reports) !== FALSE) {
                	$reports_url .= '?type=customer&report='.$reports_landing_subtab_config.'&landing=true';
                } else if(array_key_exists($reports_landing_subtab_config,$staff_reports) !== FALSE) {
                	$reports_url .= '?type=staff&report='.$reports_landing_subtab_config.'&landing=true';
                }

                return ['link'=>'Reports/'.$reports_url,'name'=>'Reports'];

                /* $detect = new Mobile_Detect;
                if ( $detect->isMobile() ) {
                    $report_value_config = get_config($dbc, 'reports_dashboard');
                    $configured_reports  = explode(',', $report_value_config);
                    $configured_reports  = array_filter($configured_reports);
                    $report_link = '';

                    $operations_reports = array('Daysheet'=>'report_daysheet.php', 'Therapist Stats'=>'report_stat.php', 'Block Booking vs Not Block Booking'=>'report_bb_vs_not_bb.php', 'Injury Type'=>'report_injury.php', 'Treatment Report'=>'report_treatment.php', 'Equipment List'=>'report_equipment_list.php', 'Equipment Transfer'=>'report_equip_transfer.php', 'Work Order'=>'report_work_order.php', 'Staff Tickets'=>'reports_staff_tickets.php', 'Day Sheet Report'=>'reports_daysheet_reports.php', 'Appointment Summary'=>'report_daily_appoint_summary.php', 'Patient Block Booking'=>'report_patient_block_booking.php', 'Assessment Tally Board'=>'report_tally_board.php', 'Assessment Follow Up'=>'report_assessment_followup.php', 'Field Jobs'=>'report_field_jobs.php', 'Shop Work Orders'=>'report_shop_work_orders.php', 'Shop Work Order Task Time'=>'report_operations_shop_task_time.php', 'Shop Work Order Time'=>'report_operations_shop_time.php', 'Site Work Orders'=>'report_site_work_orders.php', 'Scrum Business Productivity Summary'=>'reports_scrum_business_productivity_summary.php', 'Scrum Staff Productivity Summary'=>'reports_scrum_staff_productivity_summary.php', 'Scrum Status Report'=>'reports_scrum_status_report.php', 'Drop Off Analysis'=>'report_drop_off_analysis.php', 'Discharge Report'=>'report_discharge.php', 'Ticket Report'=>'report_ticket.php', 'Site Work Time'=>'report_site_work_time.php', 'Site Work Driving'=>'report_site_work_driving.php', 'Purchase Orders'=>'reports_purchase_orders.php', 'Inventory Log'=>'report_inventory_log.php', 'Point of Sale'=>'reports_pos.php', 'Credit Card on File'=>'report_cc_on_file.php', 'Checklist Time'=>'report_checklist_time.php');

                    $sales_reports = array('Validation by Therapist'=>'report_daily_validation.php', 'POS Validation'=>'report_pos_daily_validation.php', 'Daily Deposit Report'=>'report_daily_deposit.php', 'Monthly Sales by Injury Type'=>'report_review_sales.php', 'Invoice Sales Summary'=>'report_invoice_sales_summary.php', 'Sales by Customer Summary'=>'report_sales_by_customer_summary.php', 'Sales History by Customer'=>'report_sales_by_customer_detail.php', 'Sales by Service Summary'=>'report_sales_by_product_service_summary.php', 'Sales by Service Category'=>'report_sales_by_product_service_category.php', 'Sales by Inventory Summary'=>'report_sales_by_inventory_summary.php', 'Sales Summary by Injury Type'=>'report_daily_sales_summary.php', 'Inventory Analysis'=>'report_general_inventory.php', 'Unassigned/Error Invoices'=>'report_unassigned_invoices.php', 'Staff Revenue Report'=>'report_revenue.php', 'Expense Summary Report'=>'report_expenses.php', 'Phone Communication'=>'report_phone_communication.php', 'Sales by Inventory/Service Detail'=>'report_sales_by_product_service_detail.php', 'Payment Method List'=>'report_payment_method_list.php', 'Patient History'=>'report_patient_appoint_history.php', 'Receipts Summary Report'=>'report_receipt_summary.php', 'Gross Revenue by Staff'=>'report_gross_revenue_by_staff.php',  'Patient Invoices'=>'report_patient_unpaid_invoices.php', 'POS Sales Summary'=>'report_pos_daily_sales_summary.php', 'Profit-Loss'=>'report_profit_loss.php',  'Transaction List by Customer'=>'report_transaction_list_by_customer.php', 'Unbilled Invoices'=>'report_unbilled_charges.php', 'Deposit Detail'=>'report_deposit_detail.php');

                    $ar_reports = array('A/R Aging Summary'=>'report_ar_aging_summary.php', 'Patient Aging Receivable Summary'=>'report_receivables_patient_summary.php', 'Insurer Aging Receivable Summary'=>'report_receivables_summary.php', 'By Invoice#'=>'report_receivables.php', 'Customer Balance Summary'=>'report_account_receivable.php', 'Customer Balance by Invoice'=>'report_customer_balance_detail.php', 'Collections Report by Customer'=>'report_collections_report.php', 'Invoice List'=>'report_invoice_list.php', 'POS Receivables'=>'report_pos_receivables.php', 'UI Invoice Report'=>'ui_invoice_reports.php');

                    $pnl_reports = array('Revenue Receivables'=>'report_pnl_revenue_receivables.php', 'Staff Compensation'=>'report_pnl_staff_compensation.php', 'Expenses'=>'report_pnl_expenses.php', 'Costs'=>'report_pnl_costs.php', 'Summary'=>'report_pnl_summary.php');

                    $marketing_reports = array('Customer Contact List'=>'report_customer_contact_list.php', 'Customer Stats'=>'report_customer_stats.php', 'Demographics'=>'report_demographics.php', 'CRM Recommendations - By Date'=>'report_crm_recommend_date.php', 'CRM Recommendations - By Customer'=>'report_crm_recommend_customer.php', 'POS Coupons'=>'report_pos_coupons.php', 'Postal Code'=>'report_postalcode.php', 'Referral'=>'report_referral.php', 'Web Referrals Report'=>'report_web_referral.php', 'Pro Bono Report'=>'report_marketing_pro_bono.php', 'Net Promoter Score'=>'report_marketing_net_promoter_score.php');

                    $compensation_reports = array('Adjustment Compensation'=>'report_compensation_adjustments.php', 'Hourly Compensation'=>'report_hourly_compensation.php', 'Therapist Compensation'=>'report_compensation.php', 'Statutory Holiday Pay Breakdown'=>'report_stat_holiday_pay.php');

                    foreach ( $configured_reports as $config_val ) {
                        foreach($operations_reports as $key=>$file_name){
                            if ($config_val==$key) {
                                $report_link = 'Reports/'.$file_name.'?type=operations';
                            }
                            if (!empty($report_link)) {
                                break;
                            }
                        }
                    }

                    if (empty($report_link)) {
                        foreach ( $configured_reports as $config_val ) {
                            foreach($sales_reports as $key=>$file_name){
                                if ($config_val==$key) {
                                    $report_link = 'Reports/'.$file_name.'?type=sales';
                                }
                                if (!empty($report_link)) {
                                    break;
                                }
                            }
                        }
                    }

                    if (empty($report_link)) {
                        foreach ( $configured_reports as $config_val ) {
                            foreach($ar_reports as $key=>$file_name){
                                if ($config_val==$key) {
                                    $report_link = 'Reports/'.$file_name.'?type=ar';
                                }
                                if (!empty($report_link)) {
                                    break;
                                }
                            }
                        }
                    }

                    if (empty($report_link)) {
                        foreach ( $configured_reports as $config_val ) {
                            foreach($pnl_reports as $key=>$file_name){
                                if ($config_val==$key) {
                                    $report_link = 'Reports/'.$file_name.'?type=pnl';
                                }
                                if (!empty($report_link)) {
                                    break;
                                }
                            }
                        }
                    }

                    if (empty($report_link)) {
                        foreach ( $configured_reports as $config_val ) {
                            foreach($marketing_reports as $key=>$file_name){
                                if ($config_val==$key) {
                                    $report_link = 'Reports/'.$file_name.'?type=marketing';
                                }
                                if (!empty($report_link)) {
                                    break;
                                }
                            }
                        }
                    }

                    if (empty($report_link)) {
                        foreach ( $configured_reports as $config_val ) {
                            foreach($compensation_intersect as $key=>$file_name){
                                if ($config_val==$key) {
                                    $report_link = 'Reports/'.$file_name.'?type=compensation';
                                }
                                if (!empty($report_link)) {
                                    break;
                                }
                            }
                        }
                    }

                    return ['link'=>$report_link,'name'=>'Reports'];
                } else {
                    return ['link'=>'Reports/report_tiles.php','name'=>'Reports'];
                } */

                break;
			case 'passwords': return ['link'=>"Passwords/passwords.php?category=Website",'name'=>'Passwords']; break;
			case 'gantt_chart': return ['link'=>"Gantt Chart/estimated_gantt_chart.php",'name'=>'Gantt Chart']; break;
			case 'client_documentation': return ['link'=>"Client Documentation/client_documentation.php",'name'=>'Client Documentation']; break;
			case 'medication': return ['link'=>"Medication/medication.php",'name'=>'Medication']; break;
			case 'individual_support_plan': return ['link'=>"Individual Support Plan/individual_support_plan.php",'name'=>'Individual Service Plan (ISP)']; break;
			case 'social_story': return ['link'=>"Social Story/key_methodologies.php",'name'=>'Social Story']; break;
			case 'routine': return ['link'=>"Routine/routine.php",'name'=>'Routine Creator']; break;
			case 'day_program': return ['link'=>"Day Program/day_program.php",'name'=>'Day Program']; break;
			case 'match': return ['link'=>"Match/index.php",'name'=>'Match']; break;
			case 'fund_development': return ['link'=>"Fund Development/funders.php",'name'=>'Fund Development']; break;
			case 'how_to_guide': return ['link'=>"How To Guide/guides_dashboard.php",'name'=>'All Software Guide']; break;
			case 'software_guide': return ['link'=>"Software Guide/index.php",'name'=>'Software Guide']; break;
			case 'charts': return ['link'=>"Medical Charts/index.php",'name'=>'Charts']; break;
			case 'daily_log_notes': return ['link'=>"Daily Log Notes/index.php",'name'=>'Daily Log Notes']; break;
			case 'timesheet': return ['link'=>"Timesheet/index.php",'name'=>'Time Sheets']; break;
			case 'helpdesk': return ['link'=>"Helpdesk/helpdesk.php",'name'=>'Help Desk']; break;
			case 'appointment_calendar': return ['link'=>"mrbs/",'name'=>'Appointment Calendar','target'=>'_blank']; break;//return '<a target="_blank" id="do-login" href="mrbs/"></a>
			case 'booking': return ['link'=>"Booking/booking.php?contactid=0",'name'=>'Booking']; break;
			case 'check_in': return ['link'=>"Check In/checkin.php?contactid=0",'name'=>'Check In']; break;
			case 'check_out': return ['link'=>"Invoice/invoice_main.php",'name'=>'Check Out']; break;
			case 'treatment_charts': return ['link'=>"Treatment/index.php",'name'=>'Treatment Charts']; break;
			case 'accounts_receivables': return ['link'=>"Account Receivables/insurer_account_receivables.php",'name'=>'Accounts Receivable']; break;
			case 'therapist': return ['link'=>"Therapist/report_daysheet.php?type=Per",'name'=>'PT Day Sheet']; break;
			case 'exercise_library': return ['link'=>"Exercise Plan/exercise_config.php?view=master",'name'=>'Exercise Library']; break;
			case 'confirmation': return ['link'=>"Confirmation/email_confirmation.php",'name'=>'Notifications']; break;
			case 'reactivation': return ['link'=>"Reactivation/active_reactivation.php",'name'=>'Follow Up']; break;
			case 'goals_compensation': return ['link'=>"Compensation/compensation.php",'name'=>'Compensation']; break;
			case 'crm': return ['link'=>"CRM/referral.php",'name'=>'CRM']; break;
			case 'drop_off_analysis': return ['link'=>"Drop Off Analysis/drop_off_analysis.php",'name'=>'Drop Off Analysis']; break;
			case 'injury': return ['link'=>"Injury/injury.php?category=active",'name'=>'Injury']; break;
			case 'confirm': return ['link'=>"Confirmation/confirmation.php",'name'=>'Confirmation']; break;
			case 'archiveddata': return ['link'=>"Archived/archived_data.php",'name'=>'Archived Data']; break;
			case 'ffmsupport': return ['link'=>"Support/support.php",'name'=>'FFM Support']; break;
			case 'customer_support': return ['link'=>"Support/customer_support.php",'name'=>'Customer Support']; break;
			case 'demo': return ['link'=>"Demo/index.php",'name'=>'Demo']; break;
    		case 'interactive_calendar': return ['link'=>"Interactive Calendar/interactive_calendar.php",'name'=>'Interactive Calendar']; break;
    		case 'properties': return ['link'=>"Properties/properties.php",'name'=>'Properties']; break;
    		case 'training_quiz': return ['link'=>"TrainingQuizzes/orientation_training.php",'name'=>'Training & Quizzes']; break;
    		case 'preformance_review': return ['link'=>"HR/index.php?performance_review=list",'name'=>'Performance Reviews']; break;
			case 'client_projects': return ['link'=>"Client Projects/project.php",'name'=>'Client Projects']; break;
			case 'safety_manual': return ['link'=>"Manuals/safety_manual.php",'name'=>'Safety Manual']; break;
			case 'members': return ['link'=>"Members/contacts_inbox.php?list=summary&status=summary", 'name'=>'Members']; break;
			case 'form_builder': return ['link'=>"Form Builder/formbuilder.php", 'name'=>'Form Builder']; break;
			case 'hr':
				if(!is_array($tile_name)) {
					return ['link'=>"HR/index.php",'name'=>'HR'];
				} else {
					$hr_tabs = explode(',',get_config($dbc,'hr_tiles'));
					foreach($hr_tabs as $type) {
						$type_string = config_safe_str($type);
						if($type_string == $tile_name[1]) {
							return ['link'=>"HR/index.php?tile_name=".$type_string,'name'=>$type];
						}
					}
				} break;
			case 'project_workflow':
				if(!is_array($tile_name)) {
					return ['link'=>"Project Workflow/project_workflow.php?type=active",'name'=>'Project Workflow'];
				} else {
					if(strtolower($tile_name[1]) == 'call log') {
						return ['link'=>"Project Workflow/project_workflow_dashboard.php?tile=".$tile_name[1]."&tab=Cold Call Pipeline",'name'=>'Cold Call'];
					} else if($tile_name[1] != 'Shop Work Orders') {
						return ['link'=>"Project Workflow/project_workflow_dashboard.php?tile=".$tile_name[1],'name'=>$tile_name[1].''];
					}
					return ['link'=>"Project Workflow/project_workflow_dashboard.php?tile=".$tile_name[1],'name'=>$tile_name[1].''];
				} break;
			case 'project':
				if(!is_array($tile_name)) {
					return ['link'=>"Project/projects.php",'name'=>PROJECT_TILE];
				} else if(get_config($dbc, 'project_type_tiles') != 'HIDE') {
					$project_types = explode(',',get_config($dbc,'project_tabs'));
					foreach($project_types as $type) {
						$type_string = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($type)));
						if($type == $tile_name[1]) {
							return ['link'=>"Project/projects.php?tile_name=".$type_string."&type=".$type_string,'name'=>$type];
						}
					}
				} break;
			case 'ticket':
				if(!is_array($tile_name)) {
					return ['link'=>"Ticket/index.php",'name'=>TICKET_TILE];
				} else {
                    $ticket_type_tiles = get_config($dbc, 'ticket_type_tiles');
                    if($ticket_type_tiles == 'SHOW' || in_array($sub_tile,explode(',',$ticket_type_tiles))) {
                        foreach(explode(',',get_config($dbc,'ticket_tabs')) as $type) {
                            if(config_safe_str($type) == $sub_tile) {
                                return ['link'=>"Ticket/index.php?tile_name=".config_safe_str($type),'name'=>$type];
                            }
                        }
                    }
                }
                break;
			case 'ticket_group':
                $ticket_type_tile = explode('#*#',get_config($dbc, 'ticket_split_tiles_'.$sub_tile));
                return ['link'=>"Ticket/index.php?tile_group=".$sub_tile,'name'=>$ticket_type_tile[0]];
                break;
			case 'documents_all':
				if(!is_array($tile_name)) {
					return ['link'=>"Documents/index.php",'name'=>'Documents'];
				} else if(!empty(get_config($dbc, 'documents_all_tiles'))) {
					$documents_all_tiles = explode(',',get_config($dbc, 'documents_all_tiles'));
					foreach($documents_all_tiles as $documents_all_tile) {
						$type_string = config_safe_str($documents_all_tile);
						if($documents_all_tile == $tile_name[1] && strpos(get_privileges($dbc, 'documents_all_'.$type_string, ROLE),'*hide*') === FALSE) {
							return ['link'=>"Documents/index.php?tile_name=".$type_string,'name'=>$documents_all_tile];
						}
					}
				} break;
            case 'website': return ['link'=>"Website/website.php",'name'=>'Website']; break;
            case 'non_verbal_communication': return ['link'=>"Non Verbal Communication/index.php",'name'=>'Emoji Comm']; break;
            case 'vendors': return ['link'=>"Vendors/contacts_inbox.php?list=summary&status=summary",'name'=>VENDOR_TILE]; break;
			case 'quote': return ['link'=>"Quote/quotes.php",'name'=>'Quotes']; break;
			case 'cost_estimate': return ['link'=>"Cost Estimate/estimate.php",'name'=>'Cost Estimates']; break;
			case 'optimize': return ['link'=>"Optimize/index.php",'name'=>'Trip Optimizer']; break;
			case 'dispatch': return ['link'=>"Dispatch/index.php",'name'=>'Dispatch']; break;
		}
	}
	return ['link'=>false,'name'=>false];
}