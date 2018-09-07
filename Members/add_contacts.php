<?php
/*
Add	Inventory
*/
include ('../include.php');
include ('config_ss.php');
$rookconnect = get_software_name();
error_reporting(0);
//include('../Contacts/contact_field_arrays.php');

if (isset($_POST['submit']) || isset($_POST['submit_nosave'])) {
	$category		= $_POST['category'];
	$intakeid		= $_POST['intakeid'];
	$project_type	= $_POST['project_type'];
    if (isset($_POST['submit'])) {
        include('contacts_save.php');
    }
	?>
	<script>
		if(window.self === window.top) {
			<?php
				if(empty($_POST['subtab'])) {
					if(!empty($_GET['from_url'])) {
						$url = urldecode($_GET['from_url']);
					} else {
						$url = 'contacts.php?category='.$category.'&filter=Top';
					}

					/* Not creating a Project */
					if ( !empty($intakeid) && empty($project_type) ) {
						$url = '../Intake/intake.php';
					}
					/* Creating a Project */
					if ( !empty($intakeid) && !empty($project_type) ) {
						$url = '../Client Projects/add_project.php?clientid=' . $contactid . '&type=' . $project_type . '&intakeid=' . $intakeid;
					}
                    if($_GET['contacts_tabs'] == 1) {
                        $url .= '&contacts_tabs=1';
                    }

					echo 'window.location.replace("'.$url.'");';
				}
			?>
		}
		else if('<?php echo $category; ?>' == 'Business') {
			$('[name=new_business]', top.document).val('<?php echo $contactid; ?>');
		}
		else if(window.top.location.href.search('add_contacts.php') >= 0) {
			$('[name=iframe_related_contacts]', top.document).hide();
			var contacts = $('[name=related-contacts]', top.document).val();
			if(contacts != '')
				contacts = JSON.parse(contacts);
			else
				contacts = new Array();
			contacts.push('<?php echo $contactid; ?>');
			$('[name=related-contacts]', top.document).val(JSON.stringify(contacts));
			$('[name=submit]:contains("Submit")', top.document).click();
		}
		else if(window.top.location.href.search('add_agenda.php') >= 0) {
			$('[name=iframe_new_contacts]', top.document).hide();
			var contactid = '<?= $contactid ?>';
			var contact_name = '<?= $_POST['first_name'].' '.$_POST['last_name'] ?>';
			var email = '<?= $_POST['email_address'] ?>';
			$(window.top.document).find('[name="businesscontactid[]"]').find('option[value="New Contact"]').removeAttr('selected');
			$(window.top.document).find('[name="businesscontactid[]"]').append('<option selected value="'+contactid+'">'+contact_name+'</option>').trigger('change.select2');
			$(window.top.document).find('[name="agenda_email_business[]"]').append('<option data-id="'+contactid+'" value="'+email+'">'+contact_name+': '+email+'</option>').trigger('change.select2');
		}
	</script>
	<?php
   // mysqli_close($dbc); //Close the DB Connection
}

?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#form1").submit(function( event ) {
			tinyMCE.triggerSave();
            var category = $("#category").val();
            var sub_category = $("#sub_category").val();

            var code = $("input[name=code]").val();
            var name = $("input[name=name]").val();
            var category_name = $("input[name=category_name]").val();
            var sub_category_name = $("input[name=sub_category_name]").val();

            if (code == '' || category == '' || sub_category == '' || name == '') {
                //alert("Please make sure you have filled in all of the required fields.");
                //return false;
            }
            if(((category == 'Other') && (category_name == '')) || ((sub_category == 'Other') && (sub_category_name == ''))) {
                //alert("Please make sure you have filled in all of the required fields.");
                //return false;
            }
			if($('[name=iframe_related_contacts]').is(':visible')) {
				$('[name=iframe_related_contacts]').contents().find('form button[type=submit]:contains("Submit")').click();
				return false;
			}
        });

        $("#self_identification").change(function() {
            if($("#self_identification option:selected").text() == 'New Self Identification') {
                    $( "#new_self_identification" ).show();
            } else {
                $( "#new_self_identification" ).hide();
            }
        });

		if(window.self != window.top && window.top.location.href.search('add_contacts.php') >= 0) {
			var block = $('[name=businessid]').parent();
			block.empty();
			block.html("N/A");
			$('.panel').has(':contains("Add a Related Contact")').remove();
		}

        $("#contact_category").change(function() {
            if($("#contact_category option:selected").text() == 'New Category') {
                    $( "#new_category" ).show();
            } else {
                $( "#new_category" ).hide();
            }
        });

        $("#hour_0").hide();
        $("#hour_1").hide();
        $("#hour_2").hide();
        $("#hour_3").hide();
        $("#hour_4").hide();
        $("#hour_5").hide();
        $("#hour_6").hide();
        var contactid = $("#contactid").val();
        if(contactid != undefined) {
			$('[name="schedule_days[]"]').each(function() {
				if($(this).attr('checked')) {
					var id = this.id.split('_');
					$("#hour_"+id[1]).show();
				}
			});
        }

        $("#businesssiteid").change(function() {
            var businesssiteid = $("#businesssiteid").val();
            $.ajax({
                type: "GET",
                url: "../ajax_all.php?fill=business_sites&businesssiteid="+businesssiteid,
                dataType: "html",   //expect html to be returned
                success: function(response){
                    $('#siteid').html(response);
                    $("#siteid").trigger("change.select2");
                }
            });
        });

        var guardian_inc = 1;
        $('#add_row_guardian').on('click', function() {
            $(".hide_show_service").show();
            var clone = $('.additional_guardian').clone();
            clone.find(':input').val("");
            clone.removeClass("additional_guardian");
            $('#add_here_new_guardian').append(clone);
            guardian_inc++;
            return false;
        });

        var emergency_inc = 1;
        $('#add_row_emergency').on('click', function() {
            $(".hide_show_service").show();
            var clone = $('.additional_emergency').clone();
            clone.find(':input').val("");
            clone.removeClass("additional_emergency");
            $('#add_here_new_emergency').append(clone);
            emergency_inc++;
            return false;
        });

        var family_doctor_inc = 1;
        $('#add_row_family_doctor').on('click', function() {
            $(".hide_show_service").show();
            var clone = $('.additional_family_doctor').clone();
            clone.find(':input').val("");
            clone.removeClass("additional_family_doctor");
            $('#add_here_new_family_doctor').append(clone);
            family_doctor_inc++;
            return false;
        });

        var specialists_inc = 1;
        $('#add_row_specialist').on('click', function() {
            $(".hide_show_service").show();
            var clone = $('.additional_specialist').clone();
            clone.find(':input').val("");
            clone.removeClass("additional_specialist");
            $('#add_here_new_specialist').append(clone);
            specialists_inc++;
            return false;
        });

        //From contacts tile check and add contacts_tabs query string to urls
        var query_strings = window.location.search.substring(1);
        if(query_strings.indexOf('contacts_tabs=1') != -1) {
            $('a.btn').each (function () {
                var href = $(this).attr("href");
                if (href != undefined) {
                    if (href.indexOf('contacts_tabs=1') == -1) {
                        href = href.concat('&contacts_tabs=1');
                        $(this).attr("href", href);
                    }
                }
            });
        }

        //Disable inputs based on security
        <?php if($_POST['subtab'] == 'Medical Details' && !check_subtab_persmission($dbc, 'members', ROLE, 'members_medical_details')) { ?>
            disableInputs();
        <?php } ?>
        <?php if($_POST['subtab'] == 'Key Methodologies' && !check_subtab_persmission($dbc, 'members', ROLE, 'members_key_methodologies')) { ?>
            disableInputs();
        <?php } ?>
        <?php if($_POST['subtab'] == 'Activities' && !check_subtab_persmission($dbc, 'members', ROLE, 'members_activities')) { ?>
            disableInputs();
        <?php } ?>
        <?php if($_POST['subtab'] == 'Daily Log Notes' && !check_subtab_persmission($dbc, 'members', ROLE, 'members_daily_log_notes')) { ?>
            disableInputs();
        <?php } ?>
    });

function selectHour(obj){
    var day_id = obj.id;
    var arr = day_id.split('_');
    if (obj.checked) {
        $("#hour_"+arr[1]).show();
    }
    else {
        $("#hour_"+arr[1]).hide();
    }
}

function disableInputs() {
    $("#form1 input").prop("disabled", true);
    $("#form1 input[type=hidden]").prop("disabled", false);
    $("#form1 select").prop("disabled", true);
    $("#form1 select").trigger("change.select2");
    $("#form1 textarea").addClass("noMceEditor");
    $("#form1 textarea").prop("disabled", true);
    $('#form1 textarea').val(function(i,val) {
        return $('<div>').html(val).text();
    });
    $("#form1 button").prop("name", "submit_nosave");
}
</script>
</head>

<body>
<?php
	if(!IFRAME_PAGE) {
		include_once ('../navigation.php');
	}
	checkAuthorised('members');
	$url_category	= $_GET['category'];
	$intakeid		= ( isset($_GET['intakeid']) ) ? trim($_GET['intakeid']) : '';
	$project_type	= ( isset($_GET['project_type']) ) ? trim($_GET['project_type']) : '';

	// Change Business to Customers and Customers to Contacts for Highland Projects
	if(trim($url_category == '')) {
		$cat_label = "Contact";
	} else if($rookconnect == 'highland' && $url_category == 'Business') {
		$cat_label = "Customer";
	} else if($rookconnect == 'breakthebarrier' && $url_category == 'Business') {
		$cat_label = "Program/Site";
	} else if($rookconnect == 'highland' && $url_category == 'Customers') {
		$cat_label = "Contact";
	} else if($url_category == 'Customers') {
		$cat_label = 'Customer';
	} else if($url_category == 'Vendors') {
		$cat_label = VENDOR_TILE;
	} else if($url_category == 'Contractors') {
		$cat_label = 'Contractor';
	} else if($url_category == 'Sales Leads') {
		$cat_label = 'Sales Lead';
	} else if($url_category == 'Sites') {
		$cat_label = 'Site';
	} else if($url_category == 'Clients') {
		$cat_label = 'Client';
	} else if($url_category == 'Members') {
        $cat_label = 'Member';
    } else {
		$cat_label = trim($url_category);
	}
	$subtab = (!empty($_POST['subtab']) ? $_POST['subtab'] : (!empty($_GET['subtab']) ? $_GET['subtab'] : ''));
	if($subtab == '' && !empty($_GET['target'])) {
		$subtab = mysqli_fetch_array(mysqli_query($dbc, "SELECT `subtab` FROM `field_config_contacts` WHERE CONCAT(',',`contacts`,',') LIKE '%,".$_GET['target'].",%' AND `tab`='$url_category' UNION (SELECT `subtab` FROM `field_config_contacts` WHERE `tab`='$url_category' AND `contacts` IS NOT NULL ORDER BY `order`)"))['subtab'];
	}
	include('../Contacts/contacts_fields.php');
?>
<div class="container">
  <div class="row">

		<h1><?php echo (!empty($_GET['contactid']) ? 'Edit '.$cat_label.' - '.(empty($first_name) ? get_client($dbc, $contactid) : get_contact($dbc, $contactid)) : 'Add A New '.$cat_label) ?></h1>

		<?php
			if ( !empty($intakeid) ) { ?>
				<div class="pad-left gap-top double-gap-bottom"><a href="../Intake/intake.php" class="btn config-btn">Back to Dashboard</a></div><?php
			} else if(!empty($_GET['from_url'])) { ?>
				<div class="pad-left gap-top double-gap-bottom"><a href="<?php echo urldecode($_GET['from_url']); ?>" class="btn config-btn">Back to Dashboard</a></div><?php
			} else { ?>
				<div class="pad-left gap-top double-gap-bottom"><a href="contacts.php?category=<?php echo $url_category; ?>&filter=Top" class="btn config-btn">Back to Dashboard</a></div><?php
			}
		?>

		<?php
			if ( !empty($_GET['contactid']) && !empty($intakeid) ) { ?>
				<div class="notice">
					<img src="<?= WEBSITE_URL; ?>/img/info.png" width="30">&nbsp;&nbsp;Simply click <strong>Submit</strong> button to attach the Intake Form to this <?php echo $cat_label; ?>.
				</div><?php
			} else if ( !empty($intakeid) && empty($project_type) ) { ?>
				<div class="notice">
					<img src="<?= WEBSITE_URL; ?>/img/info.png" width="30">&nbsp;&nbsp;Add a new <?php echo $cat_label; ?> to attach the Intake Form to.
				</div><?php
			} else if ( !empty($intakeid) && !empty($project_type) ) { ?>
				<div class="notice">
					<img src="<?= WEBSITE_URL; ?>/img/info.png" width="30">&nbsp;&nbsp;Add a new <?php echo $cat_label; ?> to create a new <?php echo ucwords($project_type); ?> Project with the Intake Form.
				</div><?php
			}
		?>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
		<input type="hidden" name="intakeid" value="<?php echo $intakeid; ?>" />
		<input type="hidden" name="project_type" value="<?php echo $project_type; ?>" /><?php

        if(!empty($_GET['contactid']))	{ ?>
			<script>
			if('<?php echo $url_category; ?>' == 'Staff') {
				alert('Staff can only be edited in the Staff Tile.');
				window.location.replace('<?php echo WEBSITE_URL; ?>/home.php');
			}
			</script>
			<input type="hidden" id="contactid"	name="contactid" value="<?php echo $contactid ?>" />
		<?php }
		$subtab_list = get_config($dbc, FOLDER_NAME.'_field_subtabs');
		if($subtab_list != '') {
			$used_subtabs = [];
			$used_subtab_result = mysqli_query($dbc, "SELECT DISTINCT `subtab` FROM `field_config_contacts` WHERE `tile_name`='".FOLDER_NAME."' AND `tab`='".$url_category."' AND `contacts` IS NOT NULL");
			while($subtab_row = mysqli_fetch_array($used_subtab_result)) {
				$subtab_name = $subtab_row['subtab'];
				if($subtab_name == null && strpos(','.$subtab_list.',',',Main,') === FALSE) {
					$subtab_list = 'Main,'.$subtab_list;
					$subtab_name = 'Main';
				}
				$used_subtabs[] = $subtab_name;
			}
            if (empty($_GET['contactid'])) {
                if(($key = array_search("Summary", $used_subtabs)) !== false) {
                    unset($used_subtabs[$key]);
                }
            }
			$subtab_list = explode(',',$subtab_list);
			if(!in_array($subtab, $subtab_list)) {
                for ($i = 0; $i < count($subtab_list); $i++) {
                    if (in_array($subtab_list[$i], $used_subtabs)) {
                        $subtab = $subtab_list[$i];
                        break;
                    }
                }
				// $subtab = $subtab_list[0];
			}

            // Check security permissions
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_details_limited') && !check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_details_full')) {
            //     if(($key = array_search('Details', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_isp')) {
            //     if(($key = array_search('Individual Service Plan (ISP)', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_medical_details')) {
            //     if(($key = array_search('Medical Details', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_medications')) {
            //     if(($key = array_search('Medications', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_key_methodologies')) {
            //     if(($key = array_search('Key Methodologies', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_protocols')) {
            //     if(($key = array_search('Protocols', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_routine')) {
            //     if(($key = array_search('Routine', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_communication')) {
            //     if(($key = array_search('Communication', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_activities')) {
            //     if(($key = array_search('Activities', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_medical_charts')) {
            //     if(($key = array_search('Medical Charts', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }
            // if(!check_subtab_persmission($dbc, 'client_info', ROLE, 'clients_daily_log_notes')) {
            //     if(($key = array_search('Daily Log Notes', $subtab_list)) !== FALSE) {
            //         unset($subtab_list[$key]);
            //     }
            // }

			foreach($subtab_list as $subtab_name) {
				if($subtab == '') {
					$subtab = $subtab_name;
				}
				if(in_array($subtab_name, $used_subtabs)) { ?>
					<button type='submit' value='submit' name='submit' onclick="$('[name=subtab]').val('<?php echo $subtab_name; ?>');" class='btn brand-btn <?php echo ($subtab_name == $subtab ? 'active_tab' : ''); ?>'><?php echo $subtab_name; ?></button>
				<?php }
			}
			echo "<div class='clearfix'></div><br />";
		} ?>
		<input type="hidden" name="subtab" value="">
		<input type="hidden" id="url_category"	name="category" value="<?php echo $url_category ?>" />
		<?php if($url_category != 'Business') : ?>
			<div id="business-add-auto" style="display:none;">
				<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframe_business' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
				<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
				<iframe name="iframe_business" id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
			</div>
			<input type="hidden" name="new_business">
		<?php endif; ?>
        <div class="panel-group" id="accordion2<?php echo (IFRAME_PAGE ? '_IF' : ''); ?>">

        <?php
        $query_main = mysqli_query($dbc,"SELECT accordion, IFNULL(subtab,'Main') subtab, contacts FROM field_config_contacts WHERE tile_name = '".FOLDER_NAME."' AND tab='$url_category' AND contacts != 'Category,' AND `accordion` IS NOT NULL AND `order` IS NOT NULL ORDER BY IFNULL(`subtab`,'Main')='$subtab', `order`");

        $j=0;
		if(IFRAME_PAGE) {
			$j = 100;
		}
        while($row_main = mysqli_fetch_array($query_main)) {
            $accordion = $row_main['accordion'];
            $value_config = ','.$row_main['contacts'].',';
            $edit_config = $value_config;

            if ($subtab == 'Member Details') {
                if(check_subtab_persmission($dbc, 'members', ROLE, 'members_details_limited') && !check_subtab_persmission($dbc, 'members', ROLE, 'members_details_full')) {
                    if($accordion != 'Members Profile' && $accordion != 'Address') {
                        $accordion = '';
                    }
                } else if(!check_subtab_persmission($dbc, 'members', ROLE, 'members_details_limited') && !check_subtab_persmission($dbc, 'members', ROLE, 'members_details_full')) {
                    $accordion = '';
                }
            }

            if($subtab == 'Key Methodologies') {
                include_once('add_key_methodologies.php');
            }
            else if($subtab == 'Activities') {
                include_once('add_activities.php');
            }
            else if (strpos($value_config, ','."Injury".',') === FALSE && $accordion != '') {
                $collapse_accordion = '';
                if ($subtab == 'Summary' && !IFRAME_PAGE) {
                    $collapse_accordion = ' in';
                }
            ?>
			<div class="panel panel-default" <?php echo ($subtab == $row_main['subtab'] || count($subtab_list) == 1 ? '' : 'style="display:none;"'); ?>>
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2<?php echo (IFRAME_PAGE ? '_IF' : ''); ?>" href="#collapse_<?php echo $j;?>" >
							<?php if ($row_main['accordion'] == 'Parental Guardian Family Contact Information') { echo 'Parental/Guardian & Family Contact'; } else { echo $row_main['accordion']; } ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_<?php echo $j;?>" class="panel-collapse collapse<?php echo ($subtab == 'Summary' ? $collapse_accordion : ''); $collapse_accordion = ''; ?>">
					<div class="panel-body">
						<?php
						include ('../Contacts/add_contacts_basic_info.php');
						include ('../Contacts/add_contacts_dates.php');
						include ('../Contacts/add_contacts_cost.php');
						include ('../Contacts/add_contacts_description.php');
						include ('../Contacts/add_contacts_upload.php');
						include ('../Contacts/add_contacts_tile_data.php');
						?>

					</div>
				</div>
			</div>

            <?php } ?>

            <?php
            if($subtab == 'Injury') {
            if (strpos($value_config, ','."Injury".',') !== FALSE) {
            ?>
            <?php
                $injury_q = "SELECT * FROM patient_injury WHERE deleted = 0 AND contactid ='$contactid'";
                $injury = mysqli_query($dbc, $injury_q);
                $num_rows = mysqli_num_rows($injury);
                if($num_rows > 0) {

                while($row_injury = mysqli_fetch_array( $injury )) {
                    $injuryid = $row_injury['injuryid'];
                    $it = 'Active';
                    if($row_injury['discharge_date'] != '') {
                        $it = 'Discharged';
                    }
            ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_<?php echo $injuryid;?>" >
                                    <?php echo $row_injury['injury_type']. ' : '. $row_injury['injury_name']. ' : '. $it; ?><span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>

                        <div id="collapse_<?php echo $injuryid;?>" class="panel-collapse collapse">
                            <div class="panel-body">

                        <?php
                            echo "<table class='table table-bordered'>";
                            echo "<tr class='hidden-xs hidden-sm'>
                                <th>Therapist</th>
                                <th>Injury Date</th>
                                <th>Treatment Plan</th>
                                <th>Invoice(s)</th>
                                <th>Treatment Chart(s)</th>
                            ";

                            $back = '';
                            if($row_injury['discharge_date'] != '') {
                                $back = 'style="background-color: #ffa64d;"';
                            }
                            //echo '<tr '.$back.'>';
                            echo '<tr>';
                            echo '<td data-title="Therapist">' .get_contact($dbc, $row_injury['injury_therapistsid']) . '</td>';
                            echo '<td data-title="Injury Date">' . $row_injury['injury_date'] . '</td>';
                            echo '<td data-title="Treatment Plan">';
                            $appoint_date = mysqli_query($dbc,"SELECT bookingid, appoint_date, follow_up_call_status FROM booking WHERE injuryid='$injuryid' ORDER BY appoint_date DESC");
                            echo $row_injury['treatment_plan'].'<br>';
                            while($appoint_date1 = mysqli_fetch_array($appoint_date)) {
                                echo substr($appoint_date1['appoint_date'],0,10).' : '.$appoint_date1['follow_up_call_status'].'<br>';
                            }
                            echo '</td>';

                            echo '<td data-title="Invoice(s)">';
                            $appoint_date = mysqli_query($dbc,"SELECT invoiceid, invoice_date, final_price FROM invoice WHERE injuryid='$injuryid' ORDER BY invoiceid DESC");
                            while($appoint_date1 = mysqli_fetch_array($appoint_date)) {
                                $name_of_file = '../Invoice/Download/invoice_'.$appoint_date1['invoiceid'].'.pdf';
                                echo '#'.$appoint_date1['invoiceid'].' : '.$appoint_date1['invoice_date'].' : $'.$appoint_date1['final_price'].'&nbsp;&nbsp;<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a><br>';
                            }
                            echo '</td>';

                            echo '<td data-title="Treatment Chart(s)">';
                            $chart_list = mysqli_query($dbc,"SELECT `form_name`, `pdf_path`, `today_date` FROM `patientform_pdf` WHERE `patientid`='$contactid' AND `injuryid` IN ('$injuryid','0') ORDER BY `infopdfid`");
                            while($chart = mysqli_fetch_array($chart_list)) {
                                $name_of_file = '../Treatment/'.$chart['pdf_path'];
                                echo '<a href="'.$name_of_file.'" target="_blank">'.$chart['form_name'].': ('.$chart['today_date'].') <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a><br>';
                            }
                            echo '</td>';

                            echo "</tr>";
                        echo '</table>';

                        ?>

                            </div>
                        </div>
                    </div>

        <?php } }  } } ?>

			<?php $j++;
		} ?>

		<?php if($url_category == 'Business' || $url_category == 'Sites') : ?>
			<div class="panel panel-default hide_in_iframe">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_<?php echo $j;?>" >
							Add a Related Contact<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_<?php echo $j;?>" class="panel-collapse collapse">
					<div class="panel-body">
						<select name="contact-category"  data-placeholder="Choose a Category..." class="chosen-select-deselect form-control"><option></option></select>
						<div id="related_contacts_iframe" style="display:none">
							<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' onclick="close_related_contacts();" class='close_iframe_related_contacts' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
							<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
							<iframe name="iframe_related_contacts" id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="related-contacts">
			<?php endif; ?>
        </div>

		<div class="form-group double-gap-top">
			<p><span class="brand-color"><em>Required Fields *</em></span></p>
		</div>

		<div class="form-group">
			<div class="col-sm-6">
				<?php if ( !empty($intakeid) ) { ?>
					<a href="../Intake/intake.php" class="btn brand-btn btn-lg">Back</a><?php
				} else if(!empty($_GET['from_url'])) { ?>
					<div class="pad-left gap-top double-gap-bottom"><a href="<?php echo urldecode($_GET['from_url']); ?>" class="btn brand-btn">Back</a></div><?php
				} else { ?>
					<a href="contacts.php?category=<?php echo $url_category; ?>&filter=Top" class="btn brand-btn btn-lg">Back</a><?php
				} ?>
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
			</div>
			<div class="clearfix"></div>
		</div>

        <script type="text/javascript">
		$.ajax({
			url: 'get_list.php',
			method: 'POST',
			data: { target: 'categories' },
			success: function(result) {
				var categories = JSON.parse(result);
				categories.forEach(function(cat) {
					var option = document.createElement("option");
					if("<?php echo $rookconnect; ?>" == "highland" && cat == 'Business') {
						option.text = "Customer";
					} else if("<?php echo $rookconnect; ?>" == "highland" && (cat == 'Customer' || cat == 'Customers')) {
						option.text = "Contact";
					} else {
						option.text = cat;
					}

					option.value = cat;
					if(cat != 'Business' && cat != 'Staff' && cat != '<?= $url_category ?>') {
						$('[name=contact-category]').append(option);
					}
				});
				$('[name=contact-category]').trigger('change.select2');
			}
		});
		$(document).on('click','.no-results:contains("Add a Business")',add_business);
		function add_business() {
			var business = $(this).text().replace('Add a Business: "','').slice(0,-1);
			$('#accordion2').hide();
			$('[name=iframe_business]').attr('src', '<?php echo WEBSITE_URL; ?>/Contacts/add_contacts.php?category=Business&name='+encodeURI(business));
			$('[name=iframe_business]').load(function() {
			  $('#business-add-auto').show();
			  $('[name=iframe_business]').unbind('load');
			  $('[name=iframe_business]').load(close_business_iframe);
			});
		}
		$('.close_iframe_business').click(close_business_iframe);
		$(document).on('mouseenter','.no-results:contains("Add a Business")',function() {
			$(this).css('cursor','pointer');
			$(this).css('background-color','#3875d7');
			$(this).css('color','#fff');
		});
		$(document).on('mouseleave','.no-results:contains("Add a Business")',function() {
			$(this).css('cursor','');
			$(this).css('background-color','');
			$(this).css('color','');
		});
		function toggle_related_contacts() {
			var contacts = $('[name=related-contacts]').val();
			if(contacts == '')
				contacts = new Array();
			else
				contacts = JSON.parse(contacts);
			$('[name=related-contacts]').val(JSON.stringify(contacts));
			$('#contact-add-auto').toggle();
			if($('#add_contact_btn').text() == 'Cancel') {
				$('#add_contact_btn').text("Add Related Contact");
				$('[name=contact-category]').val('').trigger('change.select2');
				$('#related_contacts_iframe').hide();
				$('#contact-add-auto').hide();
				$('#accordion2').show();
				$('[name=iframe_related_contacts]').unbind('load');
			}
			else {
				$('#add_contact_btn').text("Cancel");
			}
		}
		function close_related_contacts() {
			$('[name=iframe_related_contacts]').attr('src', '<?php echo WEBSITE_URL; ?>/Contacts/contacts.php');
		}
		$('[name=contact-category]').change(function() {
			var category = $('[name=contact-category]').val();
			$('[name=iframe_related_contacts]').off('load');
			$('#related_contacts_iframe').show();
			$('[name=iframe_related_contacts]').attr('src', '<?php echo WEBSITE_URL; ?>/Contacts/add_contacts.php?category='+category);
			$('[name=iframe_related_contacts]').load(function() {
				$('[name=iframe_related_contacts]').load(function() {
					$('[name=contact-category]').val('').trigger('change.select2');
					$('#related_contacts_iframe').hide();
					$('[name=iframe_related_contacts]').off('load');
					$('[name=iframe_related_contacts]').attr('src', '');
				});
			});
		});
		function close_business_iframe() {
			$('#business-add-auto').hide();
			$('#accordion2').show();
			var id = $('[name=new_business]').val();
			if(id != '') {
				update_business_list(id);
			}
		}
		function update_business_list(id = '') {
			$.ajax({
				url: 'get_list.php',
				method: 'POST',
				data: { target: 'businessid' },
				success: function(result) {
					var option = document.createElement("option");
					option.text = "Add a new Business";
					option.value = "[add-business]";
					$('[name=businessid]').append(option);
					$('[name=businessid]').append(result);
					$('[name=businessid]').val(id).trigger('change.select2');
					$('[name=businessid]').change(function() {
						if($(this).val() == '[add-business]')
							add_business();
					});
					document.body.click();
				}
			});
		}
		$(document).ready(function() {
			$('#add_contact_btn').click(toggle_related_contacts);
			update_business_list(<?php echo $businessid; ?>);
		});
        </script>
        <!-- Chosen JS -->

		</form>

	</div>
  </div>

<?php include ('../footer.php'); ?>