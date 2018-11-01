<?php include_once('../include.php');
checkAuthorised('intake'); ?>
<div id="no-more-tables"><?php
	// Search
	$search_term = '';
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_submit']) ) {
		$search_term = ( !empty ($_POST['search_term']) ) ? filter_var ($_POST['search_term'], FILTER_SANITIZE_STRING) : '';
	} else {
		$search_term = '';
	}

	/* Pagination Counting */
	$rowsPerPage = 25;
	$pageNum = 1;

	if ( isset($_GET['page']) ) {
		$pageNum = $_GET['page'];
	}

	$offset = ($pageNum - 1) * $rowsPerPage;
	$intakeformid = $_GET['type'];

	$date_query = '';
	if(!empty($_POST['search_start_date'])) {
		$date_query .= " AND `received_date` >= '".$_POST['search_start_date']."'";
	}
	if(!empty($_POST['search_end_date'])) {
		$date_query .= " AND `received_date` <= '".$_POST['search_end_date']."'";
	}

	if ( $search_term == '' ) {
		$query_check_credentials = "SELECT * FROM `intake` WHERE `assigned_date` = '0000-00-00' AND `deleted` = 0 AND `intakeformid` != 0 AND `intakeformid` = '$intakeformid' $date_query ORDER BY `received_date` DESC LIMIT $offset, $rowsPerPage";
		$query = "SELECT COUNT(*) AS numrows FROM `intake` WHERE `assigned_date` = '0000-00-00' AND `deleted` = 0 AND `intakeformid` != 0 AND `intakeformid` = '$intakeformid' $date_query ORDER BY `received_date` DESC";
	} else {
		$query_check_credentials = "SELECT * FROM `intake` WHERE (`name` LIKE '%{$search_term}%' OR `email` LIKE '%{$search_term}%' OR `phone` LIKE '%{$search_term}%') AND `assigned_date` = '0000-00-00' AND `deleted` = 0 AND `intakeformid` != 0 AND `intakeformid` = '$intakeformid' $date_query ORDER BY `received_date` DESC LIMIT $offset, $rowsPerPage";
		$query = "SELECT COUNT(*) AS numrows FROM `intake` WHERE (`name` LIKE '%{$search_term}%' OR `email` LIKE '%{$search_term}%' OR `phone` LIKE '%{$search_term}%') AND `assigned_date` = '0000-00-00' AND `deleted` = 0 AND `intakeformid` != 0 AND `intakeformid` = '$intakeformid' $date_query ORDER BY `received_date` DESC";
	}

	$result		= mysqli_query($dbc, $query_check_credentials);
	$num_rows	= ($result) ? mysqli_num_rows($result) : 0;

	if ( $num_rows > 0 ) {

		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

		$get_field_config	= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `intake_software_dashboard` FROM `field_config` WHERE `fieldconfigid`=1" ) );
		$value_config		= ',' . $get_field_config['intake_software_dashboard'] . ',';

		echo '<table class="table table-bordered table-striped">';
            echo '<thead>';
                echo '<tr class="hidden-xs hidden-sm">';
                    if ( strpos($value_config, ',Form ID,') !== false ) {
                        echo '<th>Form ID</th>';
                    }
                    if ( strpos($value_config, ',Form Name,') !== false ) {
                        echo '<th>Form Name</th>';
                    }
                    if ( strpos($value_config, ',Name,') !== false ) {
                        echo '<th>Name</th>';
                    }
                    if ( strpos($value_config, ',Email,') !== false ) {
                        echo '<th>Email</th>';
                    }
                    if ( strpos($value_config, ',Phone,') !== false ) {
                        echo '<th>Phone</th>';
                    }
                    if ( strpos($value_config, ',Received Date,') !== false ) {
                        echo '<th>Received Date</th>';
                    }
                    if ( strpos($value_config, ',PDF Form,') !== false ) {
                        echo '<th>PDF Form</th>';
                    }
                    echo '<th>Function</th>';
                echo '</tr>';
            echo '</thead>';

			while ( $row = mysqli_fetch_array($result) ) {
				echo '<tr>';
					if ( strpos($value_config, ',Form ID,') !== false ) {
						echo '<td data-title="Form ID">' . $row['intakeid'] . '</td>';
					}
					if ( strpos($value_config, ',Form Name,') !== false ) {
						$form_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `form_name` FROM `intake_forms` WHERE `intakeformid` = '".$row['intakeformid']."'"))['form_name'];
						echo '<td data-title="Form Name">' . $form_name . '</td>';
					}
					if (strpos($value_config, ',Name,') !== false ) {
						echo '<td data-title="Name">' . $row['name'] . '</td>';
						//echo '<td data-title="Client">' . get_client($dbc, $row['clientid']) . '</td>';
					}
					if (strpos($value_config, ',Email,') !== false ) {
						echo '<td data-title="Email">' . $row['email'] . '</td>';
					}
					if (strpos($value_config, ',Phone,') !== false ) {
						echo '<td data-title="Phone">' . $row['phone'] . '</td>';
					}
					if (strpos($value_config, ',Received Date,') !== false ) {
						echo '<td data-title="Received Date">' . $row['received_date'] . '</td>';
					}
					if (strpos($value_config, ',PDF Form,') !== false ) {
						echo '<td data-title="PDF Form"><img src="' . WEBSITE_URL . '/img/pdf.png" width="16" height="16" border="0" alt="View"> <a href="' . $row['intake_file'] . '" target="_blank">View</a></td>';
					}
					//Prepare Function column
					$assign_data_subtitle = 'Choose a Contact category';
					$assign_title         = 'Assign to an Existing Contact';
					$assign_name          = 'Assign To A Profile';

					$create_data_subtitle = 'Choose a Contact category';
					$create_title         = 'Create a New Contact';
					$create_name          = 'Create A New Profile';

					$user_form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `user_form_id` FROM `intake_forms` WHERE `intakeformid` = '".$row['intakeformid']."'"))['user_form_id'];
					$user_field_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `intake_field` FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['intake_field'];
					$user_form_field = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `field_id` = '$user_field_id'"));
					$src_table = $user_form_field['source_table'];
					$contact_type = $user_form_field['source_conditions'];

					if ( strpos($value_config, ',Assign Project,') === false ) {
						$project_data_subtitle	= 'Choose a Contact category';
						$project_title			= 'Assign to '.PROJECT_NOUN;
						$project_name			= 'Assign to '.PROJECT_NOUN;
					} else {
						$project_data_subtitle	= 'Choose a Contact category';
						$project_title			= 'Create a new '.PROJECT_NOUN;
						$project_name			= 'New '.PROJECT_NOUN;
					}

					if ( strpos($value_config, ',Assign Ticket,') === false ) {
						$ticket_data_subtitle	= 'Choose a Contact category';
						$ticket_title			= 'Assign to '.TICKET_NOUN;
						$ticket_name			= 'Assign to '.TICKET_NOUN;
					} else {
						$ticket_data_subtitle	= 'Choose a Contact category';
						$ticket_title			= 'Create a new '.TICKET_NOUN;
						$ticket_name			= 'New '.TICKET_NOUN;
					}

					echo '<td data-title="Function" width="16%">';
					if ( vuaed_visible_function($dbc, 'intake') == 1 ) {
						echo '<a class="nowrap" href="add_form.php?intakeid='.$row['intakeid'].'">Edit Form</a><br />';
					}
					if ( strpos($value_config, ',Hide Assign,') === false ) {
						echo '<a data-title="'. $assign_title .'" data-action="assign" data-intakeid="'. $row['intakeid'] .'" data-subtitle="'. $assign_data_subtitle .'" data-type="'. $contact_type .'" data-table="'.$src_table.'" class="iframe_open nowrap" title="'. $assign_title .'" style="display:inline;">'. $assign_name .'</a><br />';
					}
					if ( strpos($value_config, ',Hide Create,') === false ) {
						echo '<a data-title="'. $create_title .'" data-action="create" data-intakeid="'. $row['intakeid'] .'" data-subtitle="'. $create_data_subtitle .'" data-type="'. $contact_type .'" data-table="'.$src_table.'" class="iframe_open nowrap" title="'. $create_title .'" style="display:inline;">'. $create_name .'</a><br />';
					}
					if ( strpos($value_config, ',Hide Project,') === false ) {
						echo '<a data-title="Create a New '.PROJECT_NOUN.'" data-action="project" data-intakeid="'.$row['intakeid'].'" data-subtitle="'. $project_data_subtitle.'" data-type="'. $contact_type .'" data-table="'.$src_table.'" class="iframe_open nowrap" title="'.$project_title.'" style="display:inline;">'.$project_name.'</a><br />';
					}
					if ( strpos($value_config, ',Hide Ticket,') === false ) {
						echo '<a data-title="Create a New '.TICKET_NOUN.'" data-action="ticket" data-intakeid="'.$row['intakeid'].'" data-subtitle="'. $ticket_data_subtitle.'" data-type="'. $contact_type .'" data-table="'.$src_table.'" class="iframe_open nowrap" title="'.$ticket_title.'" style="display:inline;">'.$ticket_name.'</a><br />';
					}
					if ( strpos($value_config, ',Hide Sales Lead,') === false ) {
						echo '<a data-title="Create a New '.SALES_NOUN.'" data-action="sales" data-intakeid="'.$row['intakeid'].'" data-subtitle="'. $project_data_subtitle.'" data-type="'. $contact_type .'" data-table="'.$src_table.'" class="iframe_open nowrap" title="Assign to '.SALES_NOUN.'" style="display:inline;">Assign to '.SALES_NOUN.'</a><br />';
					}
                    if ( strpos($value_config, ',Hide Archive,') === false ) {
                        echo '<a class="nowrap" href="../delete_restore.php?action=delete&intakeid='.$row['intakeid'].'" onclick="return confirm(\'Are you sure you want to archive this form?\')" title="Archive this submission">Archive</a>';
                    }
					echo '</td>';
				echo '</tr>';
			}
		echo '</table>';

		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

	} else {
		echo '<h2>No Records Found.</h2>';
	} ?>

</div><!-- #no-more-tables -->


<!-- iFrame -->
<script type="text/javascript">
	$(document).ready(function() {
		$('.iframe_open').click(function(){
			var title		 = $(this).data('title');
			var action		 = $(this).data('action');
			var intakeid	 = $(this).data('intakeid');
			var subtitle	 = $(this).data('subtitle');
            var contact_type = $(this).data('type');
            var src_table 	 = $(this).data('table');
			$('#iframe_instead_of_window').attr('src', 'get_contact_categories_software.php?subtitle='+subtitle+'&action='+action+'&contact_type='+contact_type+'&src_table='+src_table+'&intakeid='+intakeid);
			$('.iframe_title').text(title);
			$('.iframe_holder').show();
			$('.hide_on_iframe').hide();
		});

		$('.close_iframer').click(function(){
			$('.iframe_holder').hide();
			$('.hide_on_iframe').show();
		});

		$('iframe').load(function() {
			this.contentWindow.document.body.style.overflow = 'hidden';
			this.contentWindow.document.body.style.minHeight = '0';
			this.contentWindow.document.body.style.paddingBottom = '15em';
			this.style.height = (this.contentWindow.document.body.offsetHeight + 180) + 'px';
		});
	});

	window.onpopstate = function() {
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	}
</script>