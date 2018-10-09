<?php
/*
Customer Listing
*/
include ('../include.php');
?>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('incident_report');
$view_sql = (search_visible_function($dbc,'incident_report') > 0 ? '' : " AND (CONCAT(',',`contactid`,',',`clientid`,',') LIKE '%,{$_SESSION['contactid']},%' OR `completed_by` = '{$_SESSION['contactid']}')");
$current_type = $_GET['type'];
if($current_type == 'ALL') {
    $current_type = '';
}
if(!empty($current_type)) {
    $type_query = " AND `type` = '$current_type'";
}
if($current_type == 'SAVED') {
    $type_query = " AND `saved` = 1";
}
$project_tabs = get_config($dbc, 'project_tabs');
if($project_tabs == '') {
    $project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
}
$project_tabs = explode(',',$project_tabs);
$project_vars = [];
foreach($project_tabs as $item) {
    $project_vars[preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)))] = $item;
}
$quick_action_icons = explode(',',get_config($dbc, 'inc_rep_quick_action_icons'));
?>
<script type="text/javascript">
$(document).ready(function() {
    setQuickActions();

    $(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.tile-sidebar').offset().top;
        if(available_height > 200) {
            $('tile-container, .tile-sidebar, .tile-sidebar ul.sidebar, .tile-content').height(available_height);
            $('.tile-content .main-screen-white').height(available_height - 11);
        }
    }).resize();
});
function setQuickActions() {
    $('.manual-flag-icon').off('click').click(function() {
        var item = $(this).closest('tr');
        $(item).addClass('flag_target');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_flags.php?tile=incident_report&id='+$(item).data('id'), 'auto', false, true);
    });
    $('.flag-icon').off('click').click(function() {
        var item = $(this).closest('tr');
        var incidentreportid = $(this).closest('.action-icons').data('id');
        $.ajax({
            url: 'incident_report_ajax.php?action=quick_actions',
            method: 'POST',
            data: {
                field: 'flag_colour',
                value: item.data('colour'),
                table: item.data('table'),
                id: item.data('id'),
                id_field: item.data('id-field')
            },
            success: function(response) {
                item.data('colour',response.substr(0,6));
                item.css('background-color','#'+response.substr(0,6));
                item.find('.flag-label').html(response.substr(6));
            }
        });
    });
    $('.tagging-icon').off('click').click(function() {
        var item = $(this).closest('tr');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_tagging.php?tile=incident_report&id='+$(item).data('id'), 'auto', false, true);
    });
}
</script>
<div class="container">
    <div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe name="inc_rep_iframe" src=""></iframe>
        </div>
    </div>
    <div class='iframe_holder' style='display:none;'>
        <img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframe' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
        <span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
        <iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
    </div>
    <div class="row hide_on_iframe">
        <div class="main-screen">
            <div class="tile-header">
                <?php include('../Incident Report/tile_header.php'); ?>
            </div>

            <div class="tile-container" style="height: 100%;">
                <div class="collapsible tile-sidebar set-section-height hide-on-mobile">
                    <?php include('../Incident Report/tile_sidebar.php'); ?>
                </div>

                <div class="scale-to-fill tile-content set-section-height">
                    <div class="main-screen-white" style="height:calc(100vh - 20em); overflow-y: auto;">

                        <form name="form_sites" method="post" action="" class="form-inline" role="form">
                            <div id="no-more-tables">
								<?php
                                if($current_type == 'SAVED') {
                                    $search_from = '';
                                    $search_to = '';
                                } else {
                                    $search_from = date('Y-m-01');
    								$search_to = date('Y-m-d');
                                }
								if(isset($_POST['search_from'])) {
									$search_from = filter_var($_POST['search_from'],FILTER_SANITIZE_STRING);
								} else if(isset($_GET['search_from'])) {
                                    $search_from = filter_var($_GET['search_from'],FILTER_SANITIZE_STRING);
                                }
								if(isset($_POST['search_to'])) {
									$search_to = filter_var($_POST['search_to'],FILTER_SANITIZE_STRING);
								} else if(isset($_GET['search_to'])) {
                                    $search_to = filter_var($_GET['search_to'],FILTER_SANITIZE_STRING);
                                } ?>
                                <div class="preview-block">
                                    <div class="preview-block-header"><h4><?= empty($current_type) ? 'All '.INC_REP_TILE : ($current_type == 'SAVED' ? 'Saved '.INC_REP_TILE : $current_type) ?></h4></div>
									<br />
									<div class="col-sm-5"><label class="col-sm-4">From Date:</label>
										<div class="col-sm-8">
											<input type="text" class="form-control datepicker" name="search_from" value="<?= $search_from ?>">
										</div>
									</div>
									<div class="col-sm-5"><label class="col-sm-4">To Date:</label>
										<div class="col-sm-8">
											<input type="text" class="form-control datepicker" name="search_to" value="<?= $search_to ?>">
										</div>
									</div>
									<div class="col-sm-2">
										<button type="submit" class="btn brand-btn" value="search" name="search">Search</button>
									</div>
                                </div>
								<div class="clearfix"></div>
                            <?php
                            /* Pagination Counting */
                            $rowsPerPage = get_config($dbc, 'inc_rep_rows_per_page') > 0 ? get_config($dbc, 'inc_rep_rows_per_page') : 25;;
                            $pageNum = 1;

                            if(isset($_GET['page'])) {
                                $pageNum = $_GET['page'];
                            }

                            $offset = ($pageNum - 1) * $rowsPerPage;

                            if(!empty($_POST['search_incident_reports'])) {
                                $query_check_credentials = "SELECT * FROM incident_report WHERE (status = 'Done' OR status IS NULL) AND `deleted`=0 AND ((`date_of_happening` >= '$search_from' OR '$search_from' = '') OR (`date_of_report` >= '$search_from' OR '$search_from' = '')) AND ((`date_of_happening` <= '$search_to' OR '$search_to' = '') OR (`date_of_report` <= '$search_to' OR '$search_to' = '')) $view_sql $type_query";
                            } else {
                                $query_check_credentials = "SELECT * FROM incident_report WHERE (status = 'Done' OR status IS NULL) AND `deleted`=0 AND ((`date_of_happening` >= '$search_from' OR '$search_from' = '') OR (`date_of_report` >= '$search_from' OR '$search_from' = '')) AND ((`date_of_happening` <= '$search_to' OR '$search_to' = '') OR (`date_of_report` <= '$search_to' OR '$search_to' = '')) $view_sql $type_query LIMIT $offset, $rowsPerPage";
                                $query = "SELECT count(*) as numrows FROM incident_report WHERE (status = 'Done' OR status IS NULL) AND `deleted`=0 AND ((`date_of_happening` >= '$search_from' OR '$search_from' = '') OR (`date_of_report` >= '$search_from' OR '$search_from' = '')) AND ((`date_of_happening` <= '$search_to' OR '$search_to' = '') OR (`date_of_report` <= '$search_to' OR '$search_to' = '')) $view_sql $type_query";
                            }

                            $result = mysqli_query($dbc, $query_check_credentials);

                            $num_rows = mysqli_num_rows($result);
                            if($num_rows > 0) {

                                // Added Pagination //
                                if(empty($_POST['search_incident_reports'])) {
                                    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                                }
                                // Pagination Finish //

                                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT incident_report_dashboard FROM field_config_incident_report"));
                                $value_config = ','.$get_field_config['incident_report_dashboard'].',';

                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr class='hidden-xs hidden-sm'>";
                                    if (strpos($value_config, ','."Program".',') !== FALSE) {
                                        echo '<th>Program</th>';
                                    }
                                    if (strpos($value_config, ','."Project Type".',') !== FALSE) {
                                        echo '<th>'.PROJECT_NOUN.' Type</th>';
                                    }
                                    if (strpos($value_config, ','."Project".',') !== FALSE) {
                                        echo '<th>'.PROJECT_NOUN.'</th>';
                                    }
                                    if (strpos($value_config, ','."Ticket".',') !== FALSE) {
                                        echo '<th>'.TICKET_NOUN.'</th>';
                                    }
                                    if (strpos($value_config, ','."Member".',') !== FALSE) {
                                        echo '<th>Member</th>';
                                    }
                                    if (strpos($value_config, ','."Client".',') !== FALSE) {
                                        echo '<th>Client</th>';
                                    }
                                    if (strpos($value_config, ','."Type".',') !== FALSE) {
                                        echo '<th>Type</th>';
                                    }
                                    if (strpos($value_config, ','."Staff".',') !== FALSE) {
                                        echo '<th>Staff</th>';
                                    }
                                    if (strpos($value_config, ','."Follow Up".',') !== FALSE) {
                                        echo '<th>Follow Up</th>';
                                    }
                                    if (strpos($value_config, ','."Date of Happening".',') !== FALSE) {
                                        echo '<th>Date of Happening</th>';
                                    }
                                    if (strpos($value_config, ','."Date of Incident".',') !== FALSE) {
                                        echo '<th>Date of Incident</th>';
                                    }
                                    if (strpos($value_config, ','."Date Created".',') !== FALSE) {
                                        echo '<th>Date Created</th>';
                                    }
                                    if (strpos($value_config, ','."Location".',') !== FALSE) {
                                        echo '<th>Location</th>';
                                    }
                                    if (strpos($value_config, ','."PDF".',') !== FALSE) {
                                        echo '<th>View</th>';
                                    }
                                    if(vuaed_visible_function($dbc, 'incident_report') == 1) {
                                        echo '<th>Function</th>';
                                        if(!empty(array_filter($quick_action_icons))) {
                                            echo '<th>Quick Actions</th>';
                                        }
                                    }
                                echo "</tr>";
                                echo "</thead>";

                                while($row = mysqli_fetch_array( $result ))
                                {
                                    $flag_label = '';
                                    if($row['flag_colour'] != '' && $row['flag_colour'] != 'FFFFFF') {
                                        if(in_array('flag_manual',$quick_action_icons)) {
                                            if(time() < strtotime($row['flag_start']) || time() > strtotime(str_replace('9999',date('Y'),$row['flag_end']).' + 1 day')) {
                                                $row['flag_colour'] = '';
                                            } else {
                                                $flag_label = $row['flag_label'];
                                            }
                                        } else {
                                            $ticket_flag_names = [''=>''];
                                            $flag_names = explode('#*#', get_config($dbc, 'ticket_colour_flag_names'));
                                            foreach(explode(',',get_config($dbc, 'ticket_colour_flags')) as $i => $colour) {
                                                $ticket_flag_names[$colour] = $flag_names[$i];
                                            }
                                            $flag_label = $ticket_flag_names[$ticket['flag_colour']];
                                        }
                                    }
                                    $flag_styling = '';
                                    if(!empty($row['flag_colour']) && $row['flag_colour'] != 'FFFFFF') {
                                        $flag_styling = 'background-color: #'.$row['flag_colour'].';';
                                    }

                                    $contact_list = [];
                                    if ($row['contactid'] != '') {
                                        $contact_list[$row['contactid']] = get_staff($dbc, $row['contactid']);
                                    }
                                    $attendance_list = [];
                                    if ($row['attendance_staff'] != '') {
                                        $attendance_list = explode(',', $row['attendance_staff']);
                                    }
                                    foreach($attendance_list as $attendee) {
                                        $contact_list[] = $attendee;
                                    }
                                    if ($row['completed_by'] != '') {
                                        $contact_list[] = get_contact($dbc, $row['completed_by']);
                                    }
                                    $contact_list = array_unique($contact_list);
                                    if(empty($contact_list) && $current_type == 'SAVED') {
                                        $contact_list = [''];
                                    }

                                    foreach($contact_list as $contact_name) {
                                        $project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$row['projectid']."'"));
                                        $project = get_project_label($dbc, $project);
                                        $ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."'"));
                                        $ticket = get_ticket_label($dbc, $ticket);
                                        $program = (!empty(get_client($dbc, $row['programid'])) ? get_client($dbc, $row['programid']) : get_contact($dbc, $row['programid']));
                                        $member_list = [];
                                        foreach(explode(',',$row['memberid']) as $member) {
                                            if($member != '') {
                                                $member_list[] = !empty(get_client($dbc, $member)) ? get_client($dbc, $member) : get_contact($dbc, $member);
                                            }
                                        }
                                        $member_list = implode(', ',$member_list);
                                        $client_list = [];
                                        foreach(explode(',',$row['clientid']) as $client) {
                                            if($client != '') {
                                                $client_list[] = !empty(get_client($dbc, $client)) ? get_client($dbc, $client) : get_contact($dbc, $client);
                                            }
                                        }
                                        $client_list = implode(', ',$client_list);
                                        $project_type = $project_vars[$row['project_type']];

                                        $hidden_row = '';
                                        if(!empty($_POST['search_incident_reports'])) {
                                            $search_key = $_POST['search_incident_reports'];
                                            if(stripos($project, $search_key) === FALSE && stripos($ticket, $search_key) === FALSE && stripos($program, $search_key) === FALSE && stripos($member_list, $search_key) === FALSE && stripos($client_list, $search_key) === FALSE && stripos($contact_name, $search_key) === FALSE && stripos($project_type, $search_key) === FALSE) {
                                                $hidden_row = 'display: none;';
                                            }
                                        }
                                        echo '<tr data-id="'.$row['incidentreportid'].'" data-colour="'. $row['flag_colour'].'" data-table="incident_report" data-id-field="incidentreportid" style="'.$hidden_row.$flag_styling.'">';

                                        if (strpos($value_config, ','."Program".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="Program">'.$program.'</td>';
                                        }
                                        if (strpos($value_config, ','."Project Type".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="'.PROJECT_NOUN.' Type">'.$project_type.'</td>';
                                        }
                                        if (strpos($value_config, ','."Project".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="'.PROJECT_NOUN.'">'.$project.'</td>';
                                        }
                                        if (strpos($value_config, ','."Ticket".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="'.TICKET_NOUN.'">'.$ticket.'</td>';
                                        }
                                        if (strpos($value_config, ','."Member".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="Member">'.$member_list.'</td>';
                                        }
                                        if (strpos($value_config, ','."Client".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="Client">'.$client_list.'</td>';
                                        }
                                        if (strpos($value_config, ','."Type".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="Type">' . $row['type'] . '</td>';
                                        }
                                        if (strpos($value_config, ','."Staff".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="Staff">' . $contact_name . '</td>';
                                        }
                                        if (strpos($value_config, ','."Follow Up".',') !== FALSE) {
                                            if($row['type'] == 'Near Miss') {
                                                echo '<td style="'.$flag_styling.'" data-title="Follow Up">N/A</td>';
                                            } else {
                                                echo '<td style="'.$flag_styling.'" data-title="Follow Up">' . $row['ir14'] . '</td>';
                                            }
                                        }
                                        if (strpos($value_config, ','."Date of Happening".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="Date of Happening">' . $row['date_of_happening'] . '</td>';
                                        }
                                        if (strpos($value_config, ','."Date of Incident".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="Date of Incident">' . $row['incident_date'] . '</td>';
                                        }
                                        if (strpos($value_config, ','."Date Created".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="Date Created">' . $row['today_date'] . '</td>';
                                        }
                                        if (strpos($value_config, ','."Location".',') !== FALSE) {
                                            echo '<td style="'.$flag_styling.'" data-title="Location">' . $row['location'] . '</td>';
                                        }
                                        if (strpos($value_config, ','."PDF".',') !== FALSE) {
                                            $name_of_file = 'incident_report_'.$row['incidentreportid'].'.pdf';
                        					echo '<td style="'.$flag_styling.'" data-title="PDF">'.(file_exists('download/'.$name_of_file) ? '<a href="download/'.$name_of_file.'" target="_blank" ><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="View">View</a>' : '');
                                            if ($row['revision_number'] > 0) {
                                                $revision_dates = explode('*#*', $row['revision_date']);
                                                for ($i = 0; $i < $row['revision_number']; $i++) {
                                                    $name_of_file = 'incident_report_'.$row['incidentreportid'].'_'.($i+1).'.pdf';
													if(file_exists('download/'.$name_of_file)) {
														echo '<br /><a href="download/'.$name_of_file.'" target="_blank" ><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="view">View R'.($i+1).': '.$revision_dates[$i].'</a>';
													}
                                                }
                                            }
                                            echo '</td>';
                                        }
                                        if(vuaed_visible_function($dbc, 'incident_report') == 1) {
                                            echo '<td style="'.$flag_styling.'" data-title="Function">';
                    						echo '<a href=\'add_incident_report.php?type='.$row['type'].'&incidentreportid='.$row['incidentreportid'].'\'>Edit</a> | ';
                    						echo '<a href=\'../delete_restore.php?action=delete&incidentreportid='.$row['incidentreportid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                                            echo '</td>';

                                            if(!empty(array_filter($quick_action_icons))) {
                                                echo '<td style="'.$flag_styling.'" data-title="Quick Actions">';
                                                echo '<span class="flag-label">'.$flag_label.'</span><div class="clearfix"></div>';
                                                echo '<div class="action-icons pull-left">';
                                                echo (in_array('flag_manual',$quick_action_icons) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img manual-flag-icon no-toggle" title="Flag This!">' : '');
                                                echo (!in_array('flag_manual',$quick_action_icons) && in_array('flag',$quick_action_icons) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img flag-icon no-toggle" title="Flag This!">' : '');
                                                echo (in_array('tagging',$quick_action_icons) ? '<img src="'.WEBSITE_URL.'/img/icons/tagging.png" class="inline-img tagging-icon no-toggle" title="Tag Staff">' : '');
                                                echo '</div>';
                                                echo '</td>';
                                            }
                                        }

                                        echo "</tr>";
                                    }
                                }

                                echo '</table></div>';
                                // Added Pagination //
                                if(empty($_POST['search_incident_reports'])) {
                                    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                                }
                                // Pagination Finish //
                            } else {
                                echo "<h2>No Record Found.</h2>";
                            }

                            ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
