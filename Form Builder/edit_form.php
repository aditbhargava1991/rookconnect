<?php // Form Builder
include_once('../include.php');
$rookconnect = get_software_name(); ?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('#form_div .formbuilder_view').offset().top - 1;
		if(available_height > 200) {
            $('#form_div .tile-sidebar .sidebar, #form_div .formbuilder_view .main-screen').height(available_height);
		}
	}).resize();
});
function switchTab(tab) {
	var formid = $('#formid').val();
	window.location.href = '<?= WEBSITE_URL ?>/Form Builder/edit_form.php?edit='+formid+'&tab='+tab;
}
function switchPageTab(subtab) {
	var formid = $('#formid').val();
	window.location.href = '<?= WEBSITE_URL ?>/Form Builder/edit_form.php?edit='+formid+'&tab=pagebypage&subtab='+subtab;
}
function previewFormPdf() {
	$('#preview_form_pdf').submit();
}
</script>
</head>
<body>
<?php 
checkAuthorised('form_builder');
$config_access = config_visible_function($dbc, 'form_builder');
$edit_access = vuaed_visible_function($dbc, 'form_builder');
include_once ('../navigation.php');

$formid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
$form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id` = '$formid'"));
$advanced_styling = $form['advanced_styling'];
$page_by_page = $form['page_by_page'];
$is_template = $_GET['type'] == 'template' ? '1' : '0';
if(!empty($form)) {
	$is_template = $form['is_template'];
} ?>
<div class="container" id="form_div">
	<div class="iframe_overlay" style="display:none; margin-top: -20px; margin-left: -15px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe src=""></iframe>
		</div>
	</div>
	<div class="row">
		<div class="main-screen" style="margin-top: -20px;">
			<input type="hidden" name="formid" id="formid" value="<?= $_GET['edit'] ?>">
			<input type="hidden" name="is_template" id="is_template" value="<?= $is_template ?>">
            <div class="tile-header" style="position: relative; top: 19px; margin-bottom: 20px;">
                <div class="col-xs-12 col-sm-8"><h1><a href="<?= WEBSITE_URL ?>/Form Builder/formbuilder.php"><?= $is_template == 1 ? 'Template' : 'Form Builder' ?><?= !empty($form) ? ' - '.$form['name'] : '' ?></a></h1></div>
                <div class="col-xs-12 col-sm-4 gap-top"><?php
                    if($config_access > 0) {
                        echo "<div class='pull-right gap-left'><a href='".WEBSITE_URL."/Form Builder/formbuilder.php?tab=field_config'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30'></a></div>";
                    } ?>
                    <img class="no-toggle syncIcon pull-right no-margin inline-img" title="" src="" />
                </div>
                <div class="clearfix"></div>
            </div>

			<div class="blue formbuilder_tabs">
				<a href="" onclick="switchTab(''); return false;"><span class="block-clear <?= $_GET['tab'] == '' ? 'active' : '' ?>">Name</span></a>
				<a href="" onclick="switchTab('fields'); return false;"><span class="block-clear <?= $_GET['tab'] == 'fields' ? 'active' : '' ?>">Fields</span></a>
				<a href="" onclick="switchTab('styling'); return false;"><span class="block-clear <?= $_GET['tab'] == 'styling' ? 'active' : '' ?>">Styling</span></a>
				<a href="" class="page_by_page_styling" onclick="switchTab('pagebypage'); return false;" <?= $page_by_page == 1 && $advanced_styling != 1 ? '' : 'style="display:none;"' ?>><span class="block-clear <?= $_GET['tab'] == 'pagebypage' ? 'active' : '' ?>">Page-by-Page Styling</span></a>
				<a href="" onclick="switchTab('config'); return false;"><span class="block-clear <?= $_GET['tab'] == 'config' ? 'active' : '' ?>">Configuration</span></a>
				<?php if(!empty($formid)) { ?>
					<a href="<?= WEBSITE_URL ?>/Form Builder/formbuilder.php?tab=generate_form&id=<?= $formid ?>" target="_blank" class="pull-right"><span class="block-clear">Preview Form</span></a>
					<form id="preview_form_pdf" action="<?= WEBSITE_URL ?>/Form Builder/formbuilder.php?tab=generate_form&id=<?= $formid ?>" method="post" target="_blank" style="display: inline;">
						<input type="hidden" name="complete_form" value="true">
						<input type="hidden" name="preview_form" value="true">
						<input type="hidden" name="form_id" value="<?= $formid ?>">
						<a href="" onclick="previewFormPdf(); return false;" class="pull-right"><span class="block-clear">Preview PDF</span></a>
					</form>
				<?php } ?>
				<div class="clearfix"></div>
				<!-- <?php if($_GET['tab'] == 'pagebypage') { ?>
					<div class="blue">
						<a href="" onclick="switchPageTab(''); return false;"><span class="block-clear <?= $_GET['subtab'] == '' ? 'active' : '' ?>">Settings</span></a>
						<a href="" onclick="switchPageTab('styling'); return false;"><span class="block-clear <?= $_GET['subtab'] == 'styling' ? 'active' : '' ?>">Styling</span></a>
					</div>
				<?php } ?> -->
			</div>
			
			<div class="formbuilder_view" data-tab="<?= $_GET['tab'] ?>">
	            <?php
	            if($_GET['tab'] == 'fields') {
	            	include('edit_form_fields.php');
	            } else if($_GET['tab'] == 'styling') {
	            	include('edit_form_styling.php');
	            } else if($_GET['tab'] == 'config') {
	            	include('edit_form_config.php');
	            } else if($_GET['tab'] == 'pagebypage') {
	            	// if($_GET['subtab'] == 'styling') {
	            		include('edit_form_pagebypage.php');
	            	// } else {
		            // 	include('edit_form_pagebypage_settings.php');
	            	// }
	            } else {
	            	include('edit_form_main.php');
	            }
	            ?>
	        </div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<?php include('../footer.php'); ?>