<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
    $security = get_security($dbc, $tile);
    $strict_view = strictview_visible_function($dbc, 'project');
    if($strict_view > 0) {
        $security['edit'] = 0;
        $security['config'] = 0;
    }
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project');
$infogathering_count = 0; ?>
<script type="text/javascript">
$(document).ready(function() {
	$('[name="infogathering_form"]').on('change', function() {
		window.location.href = '../Information Gathering/add_manual.php?infogatheringid='+this.value+'&action=view&projectid=<?= $_GET['edit'] ?>';
	});
});
function addInfoGathering() {
	$('#dialog-infogathering').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 500 ? $(window).width() : 500),
		modal: true,
		buttons: {
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	})
}
</script>
<div id="dialog-infogathering" title="Select a Form" style="display:none;">
	<div class="form-group">
		<label class="col-sm-4 control-label">Form:</label>
		<div class="col-sm-8">
			<select name="infogathering_form" class="chosen-select-deselect">
				<option></option>
				<?php $info_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `infogathering` WHERE `deleted` = 0 ORDER BY `sub_heading`"),MYSQLI_ASSOC);
				foreach ($info_forms as $info_form) {
					echo '<option value="'.$info_form['infogatheringid'].'">'.$info_form['sub_heading'].'</option>';
				} ?>
			</select>
		</div>
	</div>
</div>
<!-- <h3 class="inline">Information Gathering</h3> -->
<?php if(vuaed_visible_function($dbc, 'infogathering')) { ?>
    <button type="submit" name="add_infogathering" class="btn brand-btn pull-right gap-top" onclick="addInfoGathering(); return false;">Add Information Gathering</button>
<?php } ?>
<ul>
<?php $infogatherings = mysqli_query($dbc, "SELECT info.sub_heading, info.category, pdf.fieldlevelriskid, pdf.infogatheringid, pdf.today_date FROM infogathering_pdf pdf LEFT JOIN infogathering info ON pdf.infogatheringid=info.infogatheringid WHERE info.`deleted`=0 AND (pdf.`company`='".get_client($dbc, $project['businessid'])."' OR pdf.`projectid` = '$projectid')");
$infogathering_count += mysqli_num_rows($infogatherings);
if($infogathering_count > 0) {
	while($infogathering = mysqli_fetch_assoc($infogatherings)) { ?>
		<li><a href="../Information Gathering/<?= infogathering_pdf($dbc, $infogathering['infogatheringid'], $infogathering['fieldlevelriskid']) ?>"><?= $infogathering['sub_heading'] ?> - <?= !empty($client) ? get_contact($dbc, $client).': ' : '' ?><?= $infogathering['today_date'] ?></a></li>
	<?php } ?>
	</ul>
<?php } else {
	echo "<h2>No Information Gathering Found</h2>";
} ?>
<?php include('next_buttons.php'); ?>
<?php
function infogathering_pdf($dbc, $infogatheringid, $fieldlevelriskid) {
    $form = get_infogathering($dbc, $infogatheringid, 'form');
    $user_form_id = get_infogathering($dbc, $infogatheringid, 'user_form_id');

    if($user_form_id > 0) {
        $user_pdf = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `infogathering_pdf` WHERE `fieldlevelriskid` = '$fieldlevelriskid' AND `infogatheringid` = '$infogatheringid' ORDER BY `infopdfid` DESC"));
        $pdf_path = $user_pdf['pdf_path'];
        return $pdf_path;
    } else {
        if($form == 'Client Business Introduction') {
            $pdf_path = 'client_business_introduction/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Branding Questionnaire') {
            $pdf_path = 'branding_questionnaire/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Website Information Gathering') {
            $pdf_path = 'website_information_gathering_form/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Blog') {
            $pdf_path = 'blog/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Marketing Strategies Review') {
            $pdf_path = 'marketing_strategies_review/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Social Media Info Gathering') {
            $pdf_path = 'social_media_info_gathering/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Social Media Start Up Questionnaire') {
            $pdf_path = 'social_media_start_up_questionnaire/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'Business Case Format') {
            $pdf_path = 'business_case_format/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Product-Service Outline') {
            $pdf_path = 'product_service_outline/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Client Reviews') {
            $pdf_path = 'client_reviews/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'SWOT') {
            $pdf_path = 'swot/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'GAP Analysis') {
            $pdf_path = 'gap_analysis/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Lesson Plan') {
            $pdf_path = 'lesson_plan/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Marketing Plan Information Gathering') {
            $pdf_path = 'marketing_plan_information_gathering/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Marketing Information') {
            $pdf_path = 'marketing_information/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
    }
}
?>