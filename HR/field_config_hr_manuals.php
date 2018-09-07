<?php
include ('../include.php');
checkAuthorised('hr');
error_reporting(0);

if (isset($_POST['submit'])) {
    $send_email = filter_var($_POST['send_email'],FILTER_SANITIZE_STRING);
    $pdf_header = filter_var(htmlentities($_POST['pdf_header']),FILTER_SANITIZE_STRING);
    $pdf_footer = filter_var(htmlentities($_POST['pdf_footer']),FILTER_SANITIZE_STRING);

    $tab_field = filter_var($_POST['tab_field'],FILTER_SANITIZE_STRING);
    if(!empty($_POST['category_name_new'])) {
      $category = filter_var($_POST['category_name_new'],FILTER_SANITIZE_STRING);
    } else {
      $category = filter_var($_POST['category_name'],FILTER_SANITIZE_STRING);
    }
    $fields = implode(',',$_POST['fields']);

    $max_section = filter_var($_POST['max_section'],FILTER_SANITIZE_STRING);
    $max_subsection = filter_var($_POST['max_subsection'],FILTER_SANITIZE_STRING);
    $max_thirdsection = filter_var($_POST['max_thirdsection'],FILTER_SANITIZE_STRING);

    if (strpos(','.$fields.',', ','.'Topic (Sub Tab),Section #,Section Heading,Sub Section #,Sub Section Heading'.',') === false) {
        $fields = $fields.',Topic (Sub Tab),Section #,Section Heading,Sub Section #,Sub Section Heading';
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config_hr_manuals WHERE tab='$tab_field' AND category='$category'"));
    if($get_field_config['fieldconfigid'] > 0) {
      $query = "UPDATE `field_config_hr_manuals` SET `pdf_header` = '$pdf_header', `pdf_footer` = '$pdf_footer', `fields` = '$fields', `max_section` = '$max_section', `max_subsection` = '$max_subsection', `max_thirdsection` = '$max_thirdsection', `send_email` = '$send_email' WHERE `tab` = '$tab_field' AND `category` = '$category_name'";
      $result = mysqli_query($dbc, $query);
    } else {
      $query = "INSERT INTO `field_config_hr_manuals` (`tab`, `category`, `pdf_header`, `pdf_footer`, `fields`, `max_section`, `max_subsection`, `max_thirdsection`, `send_email`) VALUES ('$tab_field', '$category', '$pdf_header', '$pdf_footer', '$fields', '$max_section', '$max_subsection', '$max_thirdsection', '$send_email')";
      $result = mysqli_query($dbc, $query);
    }

    echo '<script type="text/javascript"> window.location.replace("field_config_hr_manuals.php?tab='.$tab_field.'&category='.$category.'"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#tab_field").change(function() {
        window.location = 'field_config_hr_manuals.php?tab='+this.value;
	});

	$("#category_name").change(function() {
        var tab = $("#tab_field").val();
        window.location = 'field_config_hr_manuals.php?tab='+tab+'&category='+this.value;
	});
});
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1>HR</h1>
<div class="gap-top double-gap-bottom"><a href="hr.php?tab=<?php echo $_GET['tab'];?>" class="btn config-btn">Back to Dashboard</a></div>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<?php
$tab = $_GET['tab'];
$category = $_GET['category'];

$tab_categories = mysqli_fetch_all(mysqli_query($dbc,"SELECT * FROM field_config_hr_manuals WHERE tab='$tab'"),MYSQLI_ASSOC);
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_hr_manuals WHERE tab='$tab' AND `category` = '$category'"));

$send_email = $get_field_config['send_email'];
$value_config = ','.$get_field_config['fields'].',';
$pdf_header = $get_field_config['pdf_header'];
$pdf_footer = $get_field_config['pdf_footer'];
$max_section = $get_field_config['max_section'];
$max_subsection = $get_field_config['max_subsection'];
$max_thirdsection = $get_field_config['max_thirdsection'];
if($max_section == '') {
    $max_section = 10;
}
if($max_subsection == '') {
    $max_subsection = 10;
}
if($max_thirdsection == '') {
    $max_thirdsection = 10;
}
$hr_tabs = get_config($dbc, 'hr_tabs');
?>

    <div class="tab-container">
        <div class="pull-left tab"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click this to configure custom tabs for the HR tile."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span><a href="field_config_hr.php"><button type="button" class="btn brand-btn mobile-block">Tabs</button></a></div>

        <div class="pull-left tab"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click this to configure your settings for HR Forms."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span><a href="field_config_hr.php?subtab=Forms"><button type="button" class="btn brand-btn mobile-block">Forms</button></a></div>

        <div class="pull-left tab"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click this to configure your settings for HR Manuals."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span><a href="field_config_hr_manuals.php"><button type="button" class="btn brand-btn mobile-block active_tab">Manuals</button></a></div>
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose your desired tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Tab:
		</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Tab..." id="tab_field" name="tab_field" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
              foreach (explode(',', $hr_tabs) as $hr_tab) {
                echo '<option '.($tab == $hr_tab ? 'selected' : '').' value="'.$hr_tab.'">'.$hr_tab.'</option>';
              } ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose your desired category."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Category:
		</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Category..." id="category_name" name="category_name" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                foreach ($tab_categories as $tab_category) {
                  echo '<option value="'.$tab_category['category'].'"'.($tab_category['category'] == $category ? ' selected' : '').'>'.$tab_category['category'].'</option>';
                }
              ?>
              <option value="ADD_NEW"<?= ($category == 'ADD_NEW' ? ' selected' : '') ?>>Add New Category</option>
            </select>
        </div>
    </div>
    <?php if ($category == 'ADD_NEW') { ?>
      <div class="form-group">
        <label for="category_name_new" class="col-sm-4 control-label">New Category:</label>
        <div class="col-sm-8">
          <input type="text" name="category_name_new" value="" class="form-control">
        </div>
      </div>
    <?php } ?>

    <div id='no-more-tables'>
<table border='2' cellpadding='10' class='table'>
    <tr>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Topic (Sub Tab)".',') !== FALSE) { echo " checked"; } ?> value="Topic (Sub Tab)" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Topic (Sub Tab)
        </td>
        <td>
            <input disabled type="checkbox" <?php if (strpos($value_config, ','."Section #".',') !== FALSE) { echo " checked"; } ?> value="Section #" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Section #
        </td>
        <td>
            <input disabled type="checkbox" <?php if (strpos($value_config, ','."Section Heading".',') !== FALSE) { echo " checked"; } ?> value="Section Heading" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Section Heading
        </td>
        <td>
            <input disabled type="checkbox" <?php if (strpos($value_config, ','."Sub Section #".',') !== FALSE) { echo " checked"; } ?> value="Sub Section #" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Sub Section #
        </td>
        <td>
            <input disabled type="checkbox" <?php if (strpos($value_config, ','."Sub Section Heading".',') !== FALSE) { echo " checked"; } ?> value="Sub Section Heading" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Sub Section Heading
        </td>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Third Tier Section #".',') !== FALSE) { echo " checked"; } ?> value="Third Tier Section #" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Third Tier Section #
        </td>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Third Tier Heading".',') !== FALSE) { echo " checked"; } ?> value="Third Tier Heading" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Third Tier Heading
        </td>
    </tr>
    <tr>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Detail".',') !== FALSE) { echo " checked"; } ?> value="Detail" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Detail
        </td>

        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Document".',') !== FALSE) { echo " checked"; } ?> value="Document" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Document
        </td>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Link".',') !== FALSE) { echo " checked"; } ?> value="Link" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Link
        </td>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Videos".',') !== FALSE) { echo " checked"; } ?> value="Videos" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Videos
        </td>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Signature box".',') !== FALSE) { echo " checked"; } ?> value="Signature box" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Signature box
        </td>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Comments".',') !== FALSE) { echo " checked"; } ?> value="Comments" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Comments
        </td>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Staff
        </td>
    </tr>
    <tr>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Review Deadline".',') !== FALSE) { echo " checked"; } ?> value="Review Deadline" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Review Deadline
        </td>
        <td>
            <input type="checkbox" <?php if (strpos($value_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="fields[]">&nbsp;&nbsp;Status
        </td>
        <td>
            <input type="checkbox" <?php if ( strpos ( $value_config, ',' . "Configure Email" . ',' ) !== FALSE) { echo " checked"; } ?> value="Configure Email" name="fields[]">&nbsp;&nbsp;Configure Email
        </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    </tr>
</table>
</div>

<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Send Email on Comment:</label>
<div class="col-sm-8">
  <input name="send_email" value="<?php echo $send_email; ?>" type="text" class="form-control">
</div>
</div>

<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Max Section #:<br><em>(add only digits)</em></label>
<div class="col-sm-8">
  <input name="max_section" value="<?php echo $max_section; ?>" type="text" class="form-control">
</div>
</div>

<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Max Sub Section #:<br><em>(add only digits)</em></label>
<div class="col-sm-8">
  <input name="max_subsection" value="<?php echo $max_subsection; ?>" type="text" class="form-control">
</div>
</div>

<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Max Third Tier Section #:<br><em>(add only digits)</em></label>
<div class="col-sm-8">
  <input name="max_thirdsection" value="<?php echo $max_thirdsection; ?>" type="text" class="form-control">
</div>
</div>

<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">PDF Header:</label>
<div class="col-sm-8">
  <textarea name="pdf_header" class="form-control"><?= html_entity_decode($pdf_header) ?></textarea>
</div>
</div>

<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">PDF Footer:</label>
<div class="col-sm-8">
  <textarea name="pdf_footer" class="form-control"><?= html_entity_decode($pdf_footer) ?></textarea>
</div>
</div>

<div class="form-group">
    <div class="col-sm-6">
        <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your HR settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href="hr.php?tab=<?php echo $tab; ?>" class="btn config-btn btn-lg">Back</a>
		<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
	</div>
	<div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
		<span class="popover-examples list-inline pull-right" style="margin:15px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click this to finalize your HR settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
    </div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>
