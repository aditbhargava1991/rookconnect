<?php
include('../include.php');
include_once('../Calendar/calendar_functions_inc.php');
include 'config.php';
?>
<script type="text/javascript" src="timesheet.js"></script>
<style>
    .ui-datepicker-current:empty { display:none; }
</style>
</head>
<body>
<?php
include_once ('../navigation.php');
checkAuthorised('timesheet');
?>

<div class="container triple-pad-bottom" id="timesheet_div">
	<div id="dialog-pdf-options" title="Select PDF Fields" style="display: none;">
		<?php echo get_pdf_options($dbc); ?>
	</div>
	<div id="dialog-signature" title="Signature Box" style="display: none;">
		<?php $output_name = 'time_cards_signature';
		include('../phpsign/sign_multiple.php'); ?>
	</div>
	<div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="timesheet_iframe" src=""></iframe>
		</div>
	</div>
    <div class="row timesheet_div">
    	<input type="hidden" name="timesheet_time_format" value="<?= get_config($dbc, 'timesheet_time_format') ?>">
        <div class="col-md-12">

        <h1 class="">Time Sheets Dashboard
        <?php $security = get_security($dbc, 'timesheet');
        if($security['config'] > 0) {
            echo '<a href="field_config.php?from_url=time_cards.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
        }
        ?>
        <img class="no-toggle statusIcon pull-right no-margin inline-img small" title="" src="" data-original-title=""></h1>
		<div class="clearfix"></div>

		<?php echo get_tabs('Time Sheets', $_GET['tab'], array('db' => $dbc, 'field' => $value['config_field'])); ?>
        <br><br>
        <?php include('../Timesheet/time_cards_content.php'); ?>
        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
