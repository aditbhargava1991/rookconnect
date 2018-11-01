<?php
/*
Add	Asset
*/
ini_set('session.cache_limiter','public');
session_cache_limiter(false);

include ('../include.php');
include_once('../function.php');
//include_once('saveAgenda.php');
error_reporting(0);
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_agendas_meetings"));
$value_config = ','.$get_field_config['field_config'].',';

?>
<script type="text/javascript">
$(document).ready(function () {
    $(".other_location").hide();
    $(".other_heading").hide();
    $(".location").change(function(){
    $(this).find("option:selected").each(function(){
                if($(this).attr("value")=="Other"){
                    $(".other_location").show();
                } else {
                    $(".other_location").hide();
                }
    });
    }).change();

    $(".heading").change(function(){
    $(this).find("option:selected").each(function(){
                if($(this).attr("value")=="Other"){
                    $(".other_heading").show();
                } else {
                    $(".other_heading").hide();
                }
    });
    }).change();

    $("#form1").submit(function( event ) {
        if ($("input[name=code]").val() == '') {
            alert("Please make sure you have provided a valid code.");
            return false;
        }
        else if ($("#category").val() == '') {
            alert("Please make sure you have selected a category.");
            return false;
        }
        else if ($("#sub_category").val() == '') {
            alert("Please make sure you have selected a sub category.");
            return false;
        }
        else if ($("input[name=name]").val() == '') {
            alert("Please make sure you have filled in a name.");
            return false;
        }
        else if($("#category").val() == 'Other' && $("input[name=category_name]").val() == '') {
            alert("Please make sure you have filled in a category name.");
            return false;
        }
        else if($("#sub_category").val() == 'Other' && $("input[name=sub_category_name]").val() == '') {
            alert("Please make sure you have filled in a sub category name.");
            return false;
        }
    });

});
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('agenda_meeting');
$back_url = (empty($_GET['from']) ? 'agenda.php' : urldecode($_GET['from']));
?>
<div class="container">
  <div class="row">

    <h3 class="gap-left pull-left">Agendas</h3>
    <?php if ( isset($_GET['projectid']) ) { ?>
        <div class="pull-right offset-top-15"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
    <?php } ?>
    <div class="clearfix"></div>

	<?php if ( !isset($_GET['projectid']) ) { ?>
        <div class="gap-top double-gap-bottom"><a href="<?php echo $back_url; ?>" class="btn config-btn">Back to Dashboard</a></div>
    <?php } ?>

    <form id="form1" name="form1" method="post"	action="saveAgenda.php" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
    $businessid = $get_field_config['default_business'] > 0 ? $get_field_config['default_business'] : '0';
    $businesscontactid = $get_field_config['default_contact'] > 0 ? $get_field_config['default_contact'] : '';
    $companycontactid = '';
    $new_contact = '';
    $date_of_meeting = '';
    $time_of_meeting = '';
    $end_time_of_meeting = '';
    $location = '';
     $heading = '';
   $meeting_requested_by = '';
    $meeting_objective = '';
    $items_to_bring = '';
    $projectid = (!empty($_GET['projectid']) ? $_GET['projectid'] : '');
    $servicecategory = '';
    $agenda_topic = '';
    $agenda_note = '';
    $qa_ticket = '';
    $agenda_email_business = '';
    $agenda_email_company = '';
    $agenda_additional_email = '';
    $status = 'Pending';
    $meeting_topic = '';
    $meeting_note = '';
    $client_deliverables = '';
    $company_deliverables = '';
    $subcommittee = '';

    $clientid = '';

    if(!empty($_GET['agendameetingid']))	{

        $agendameetingid = $_GET['agendameetingid'];
        $get_asset =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	agenda_meeting WHERE	agendameetingid='$agendameetingid'"));

        $businessid = ($get_asset['businessid'] == '' ? 0 : $get_asset['businessid']);
        $business_address = get_contact($dbc, $businessid, 'business_address').', '.get_contact($dbc, $businessid, 'city').', '.get_contact($dbc, $businessid, 'zip_code');

        $businesscontactid = $get_asset['businesscontactid'];
        $companycontactid = $get_asset['companycontactid'];
        $new_contact = $get_asset['new_contact'];
        $date_of_meeting = $get_asset['date_of_meeting'];
        $time_of_meeting = $get_asset['time_of_meeting'];
        $end_time_of_meeting = $get_asset['end_time_of_meeting'];
        $location = $get_asset['location'];
        $heading = $get_asset['heading'];
        $meeting_requested_by = $get_asset['meeting_requested_by'];
        $meeting_objective = $get_asset['meeting_objective'];
        $items_to_bring = $get_asset['items_to_bring'];
        $projectid = $get_asset['projectid'];
        $servicecategory = $get_asset['servicecategory'];
        $agenda_topic = $get_asset['agenda_topic'].'##FFM##';
        $agenda_note = $get_asset['agenda_note'].'##FFM##';
        $qa_ticket = $get_asset['qa_ticket'];
        $agenda_email_business = $get_asset['agenda_email_business'];
        $agenda_email_company = $get_asset['agenda_email_company'];
        $agenda_additional_email = $get_asset['agenda_additional_email'];
        $status = $get_asset['status'];
        $meeting_topic = $get_asset['meeting_topic'];
        $meeting_note = $get_asset['meeting_note'];
        $client_deliverables = $get_asset['client_deliverables'];
        $company_deliverables = $get_asset['company_deliverables'];
	    $subcommittee = $get_asset['subcommittee'];
		?>
		<input type="hidden" id="agendameetingid"	name="agendameetingid" value="<?php echo $agendameetingid ?>" />
    <?php } else {
		if(!empty($_GET['projectid'])) {
			$projectid = $_GET['projectid'];
			$_GET['bid'] = mysqli_fetch_array(mysqli_query($dbc, "SELECT `businessid` FROM `project` WHERE `projectid`='$projectid'"))['businessid'];
		}
		if(!empty($_GET['bid'])) {
			$businessid = $_GET['bid'];
			$business_address = get_contact($dbc, $businessid, 'business_address').', '.get_contact($dbc, $businessid, 'city').', '.get_contact($dbc, $businessid, 'zip_code');
		}
	} ?>
    <input type="hidden" name="new_status" value="<?php echo $status; ?>" />

<div class="panel-group <?= !empty($projectid) ? 'block-panels main-screen' : '' ?>" id="accordion2" <?= !empty($projectid) ? 'style="background-color: #fff; padding: 0; margin-left: 0.5em; width: calc(100% - 1em);"' : '' ?>>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_bc" >
                        <?php echo (strpos($value_config, ','."Business".',') === FALSE ? 'Contacts' : BUSINESS_CAT); ?> & Attendees<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_bc" class="panel-collapse collapse in">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_meeting_business_contact.php');
                    ?>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_mi" >
                        Meeting Basic Info<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_mi" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_meeting_basic_info.php');
                    ?>

                </div>
            </div>
        </div>

        <?php if (strpos($value_config, ','."Project".',') !== FALSE || strpos($value_config, ','."Service".',') !== FALSE) { ?>
	        <div class="panel panel-default">
	            <div class="panel-heading">
	                <h4 class="panel-title">
	                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ps" >
	                        Project & Services<span class="glyphicon glyphicon-plus"></span>
	                    </a>
	                </h4>
	            </div>

	            <div id="collapse_ps" class="panel-collapse collapse">
	                <div class="panel-body">

	                    <?php
	                    include ('add_agenda_meeting_project_services.php');
	                    ?>

	                </div>
	            </div>
	        </div>
	    <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ai" >
                        Agendas Information<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_ai" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_basic_info.php');
                    ?>

                </div>
            </div>
        </div>

        <?php if (strpos($value_config, ','."Documents".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_au" >
                        Agendas Uploader<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_au" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_uploader.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Tickets Waiting for QA".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_qa" >
                        <?= TICKET_TILE ?> Waiting for QA<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_qa" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_tickets.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($status == 'Pending') { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ea" >
                        Email Agendas<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_ea" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_agenda_email.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($status != 'Pending') { ?>
        <h1	class="triple-pad-bottom">Meeting</h1>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_mi2" >
                        Meeting Information<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_mi2" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_meeting_basic_info.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Documents".',') !== FALSE) { ?>
        <?php if($status != 'Pending') { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_meeu" >
                        Meeting Uploader<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_meeu" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_meeting_uploader.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>
        <?php } ?>

        <?php if($status == 'Approve') { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_em" >
                        Email Meeting<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_em" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_meeting_email.php');
                    ?>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($status == 'Approve') { ?>
        <!--
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tt" >
                        Ticket(s) & Task(s)<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_tt" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_meeting_ticket_task.php');
                    ?>

                </div>
            </div>
        </div>
        -->
        <?php } ?>

    </div>

		<!--
        <div class="form-group">
			<p><span class="brand-color"><em>Required Fields *</em></span></p>
		</div>
        -->

		<?php if ( isset($_GET['projectid']) ) { ?>
            <div class="form-group">
                <div class="pull-right">
                    <?php if($status == 'Pending') { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Approve and Email Agendas</button>
                        <span class="popover-examples pull-right" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } else if($status == 'Approve') { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Create Meeting and Email</button>
                        <span class="popover-examples pull-right" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } else { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Update Agendas</button>
                        <span class="popover-examples pull-right" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } ?>
                </div>
                <div class="pull-right">
                    <button	type="submit" name="submit"	value="Save" class="btn brand-btn pull-right">Save<?= ($status == 'Pending' || $status == 'Approve' ? ' and Email' : '') ?> Agendas</button>
                    <span class="popover-examples pull-right" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to save, in order to make changes later on."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                </div>
                <div class="pull-right">
                    <button	type="submit" name="submit"	value="temp_save" class="btn brand-btn pull-right">Save</button>
                    <span class="popover-examples pull-right" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to save Agenda without sending an email."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                </div>
                <div class="pull-right">
                    <span class="popover-examples" style="margin:5px 5px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="" class="btn brand-btn">Cancel</a>
                </div>
            </div>
        <?php } else { ?>
            <div class="form-group">
                <div class="col-sm-4">
                    <span class="popover-examples" style="margin:15px 0 0 0;"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="<?php echo $back_url; ?>" class="btn brand-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-8">
                    <?php if($status == 'Pending') { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Approve and Email Agendas</button>
                        <span class="popover-examples pull-right" style="margin:15px 5px 0 15px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } else if($status == 'Approve') { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Create Meeting and Email</button>
                        <span class="popover-examples pull-right" style="margin:15px 5px 0 15px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } else { ?>
                        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Update Agendas</button>
                        <span class="popover-examples pull-right" style="margin:15px 5px 0 15px;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit changes and email any added contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <?php } ?>

                    <button	type="submit" name="submit"	value="Save" class="btn brand-btn btn-lg pull-right">Save<?= ($status == 'Pending' || $status == 'Approve' ? ' and Email' : '') ?> Agendas</button>
                    <span class="popover-examples pull-right" style="margin:15px 5px 0 15px;"><a data-toggle="tooltip" data-placement="top" title="Click here to save, in order to make changes later on."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>

                    <button	type="submit" name="submit"	value="temp_save" class="btn brand-btn btn-lg pull-right">Save</button>
                    <span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save Agenda without sending an email."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                </div>
            </div>
        <?php } ?>

		</form>

	</div>
  </div>
  <script type="text/javascript">
  	var Autosave = (function(w, $){
	let $public = {};
	$public.xhr = false
	$public.submitForm = function(props = {}) {
        if (this.xhr !== false) {
        	this.xhr.abort();
        }
		let elem = $('form'),
			that = this,
			action = elem.attr('action'),
			form = new FormData(elem[0]);
		if(typeof props.file === 'undefined') {
			form.delete('upload_agenda_document[]')
		}
		form.append('submit', 'Submit');
		this.xhr = $.ajax({
			processData:false,
			contentType:false,
			method:'POST',
			header:{
				'HTTP_X_REQUESTED_WITH':"ajax"
			},
			url:'saveAgenda.php',
			data: form,
			success:function(response) {
				var response = $.parseJSON(response);
            	if(response.status ==  true){
            		if($('#agendameetingid').length==0){
						$(elem).append('<input type="hidden" id="agendameetingid" name="agendameetingid" value="'+response.agendameetingid+'" />')
            		}
            	}
			}
		});
	}
	$public.init = function(){
		let that = this;
		$('form').on('keypress change','input, select, textarea', function(event){
			that.submitForm();
		})

		$('form').on('change', 'input[type="file"]', function(e){
			that.submitForm({
				file:true
			})
		})
	}
	return $public
})(window, $);
Autosave.init();
  </script>
<?php include ('../footer.php'); ?>