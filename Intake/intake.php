<?php
	/*
	 * Intake Form Submissions List
	 */
	include ('../include.php');
?>
</head>

<body><?php
include_once ('../navigation.php');
checkAuthorised('intake');

$tab = $_GET['tab'];
if(empty($tab)) {
	$_GET['tab'] = 'softwareforms';
	$tab = 'softwareforms';
}
$type = $_GET['type'];
if($tab == 'softwareforms') {
	if(!empty($type)) {
		$form_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `intakeformid` = '".$_GET['type']."'"))['form_name'];
		$note = 'These are all Forms that have been completed externally and can be attached to a Contact or '.PROJECT_NOUN.'.';
		$include_url = 'intake_completedsoftware.php';
		$include_search_icon = true;
	} else {
		$note = 'Software Intake Forms are created from the Form Builder within the software and can be added as an Intake Form. Forms added here will have the ability to generate a link to be filled out externally.';
		$include_url = 'intake_softwareforms.php';
		$include_search_icon = false;
	}
} else {
	$note = 'Web Intake Forms are used for admitting patients to the clinic. Any form submitted on the company website automatically submits to the Intake Form tile.';
	$include_url = 'intake_webforms.php';
	$include_search_icon = true;
}
?>

<script type="text/javascript">
$(document).ready(function() {
	$('#intake_accordions .panel-heading').click(loadPanel);
	if($(window).width() > 767) {
		resizeScreen();
		$(window).resize(function() {
			resizeScreen();
		});
	}
});
function resizeScreen() {
	var view_height = $(window).height() > 800 ? $(window).height() : 800;
	$('#intake_div .scale-to-fill .main-screen,#intake_div .tile-sidebar,#intake_div .scale-to-fill.tile-content').height(view_height - $('#intake_div .scale-to-fill').offset().top - $('#footer').outerHeight());
}
$(document).on('change', 'select[name="add_intakeform"]', function() { selectIntakeForm(this); });
function selectIntakeForm(sel) {
	window.location.href = '<?= WEBSITE_URL ?>/Intake/add_form.php?formid='+sel.value;
}
function addIntakeForm() {
	$('.dialog_addintake').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 600 ? $(window).width() : 600),
		modal: true,
		buttons: {
	        Cancel: function() {
	        	window.location.reload();
	        }
	    }
	});
}
function loadPanel() {
    $('#intake_accordions .panel-heading:not(.higher_level_heading)').closest('.panel').find('.panel-body').html('Loading...');
    if(!$(this).hasClass('higher_level_heading')) {
        var panel = $(this).closest('.panel').find('.panel-body');
        $(panel).html('Loading...');
        $.ajax({
            url: $(panel).data('file'),
            method: 'POST',
            response: 'html',
            success: function(response) {
                $(panel).html(response);
				$('.pagination_links a').click(pagination_load);
            }
        });   
    }
}
function pagination_load() {
	var target = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: this.href,
		method: 'POST',
		response: 'html',
		success: function(response) {
			target.html(response);
			$('.pagination_links a').click(pagination_load);
		}
	});
	return false;
}
function show_search_fields() {
	if($('.search-fields').is(':visible')) {
		$('.search-fields').hide();
	} else {
		$('.search-fields').show();
	}
	resize_blocks();
}
</script>

<div id="intake_div" class="container" style="background-color: #fff;">
	<div class="dialog_addintake" title="Select an Intake Form" style="display: none;">
		<div class="form-group">
			<label class="col-sm-4 control-label">Intake Form:</label>
			<div class="col-sm-8">
				<select name="add_intakeform" class="chosen-select-deselect form-control">
					<option></option>
					<?php $form_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `deleted` = 0"),MYSQLI_ASSOC);
					foreach ($form_types as $form_type) {
						echo '<option value="'.$form_type['intakeformid'].'">'.$form_type['form_name'].'</option>';
					} ?>
				</select>
			</div>
		</div>
	</div>

	<div class="iframe_holder" style="display:none;">
		<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
		<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
		<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
	</div>

	<div class="row hide_on_iframe">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<?php include('../Intake/tile_header.php'); ?>
			</div>

			<div id="intake_accordions" class="sidebar show-on-mob panel-group block-panels col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading higher_level_heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#intake_accordions" href="#collapse_webforms">
								Website Forms<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_webforms" class="panel-collapse collapse">
						<div class="panel-body" style="padding: 0; margin: -1px;" id="collapse_webforms_body">
							<?php $form_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) num_rows FROM `intake` WHERE `intakeformid` = 0 AND `deleted` = 0 AND `assigned_date` = '0000-00-00'"))['num_rows']; ?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#collapse_webforms_body" href="#collapse_webforms_all" class="quadruple-pad-left">
											All Website Forms<span class="pull-right"><?= $form_count ?></span><span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_webforms_all" class="panel-collapse collapse">
									<div class="panel-body" data-file="intake_webforms.php">
										Loading...
									</div>
								</div>
							</div>
							<?php $intake_cats = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `intake` WHERE `intakeformid` = 0 AND `deleted` = 0 AND `assigned_date` = '0000-00-00' ORDER BY `category`"),MYSQLI_ASSOC),'category');
							foreach($intake_cats as $intake_cat) {
								$form_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) num_rows FROM `intake` WHERE `intakeformid` = 0 AND `category` = '$intake_cat' AND `deleted` = 0 AND `assigned_date` = '0000-00-00'"))['num_rows']; ?>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#collapse_webforms_body" href="#collapse_webforms_<?= config_safe_str($intake_cat) ?>" class="quadruple-pad-left">
												<?= $intake_cat ?><span class="pull-right"><?= $form_count ?></span><span class="glyphicon glyphicon-plus"></span>
											</a>
										</h4>
									</div>

									<div id="collapse_webforms_<?= config_safe_str($intake_cat) ?>" class="panel-collapse collapse">
										<div class="panel-body" data-file="intake_webforms.php?web_type=<?= $intake_cat ?>">
											Loading...
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading higher_level_heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#intake_accordions" href="#collapse_forms">
								Forms<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_forms" class="panel-collapse collapse">
						<div class="panel-body" style="padding: 0; margin: -1px;" id="collapse_forms_body">

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#collapse_forms_body" href="#collapse_forms_configure" class="double-pad-left">
											Configure All Forms<span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_forms_configure" class="panel-collapse collapse">
									<div class="panel-body" data-file="intake_softwareforms.php">
										Loading...
									</div>
								</div>
							</div>

							<?php $form_categories = get_config($dbc, 'intake_software_tabs');
							$form_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `deleted` = 0 ORDER BY `form_name` ASC"),MYSQLI_ASSOC);
							if(!empty($form_categories)) {
								$form_tabs = [];
								$form_categories = explode('*#*', $form_categories);
								foreach($form_categories as $form_cat) {
									foreach($form_types as $form_i => $form_type) {
										if($form_type['category'] == $form_cat) {
											$form_tabs[$form_cat][] = $form_type;
											unset($form_types[$form_i]);
										}
									}
								}
								ksort($form_categories);
								foreach($form_types as $form_i => $form_type) {
									$form_tabs['(Uncategorized)'][] = $form_type;
								}
								foreach($form_tabs as $form_cat => $form_tab) { ?>
									<div class="panel panel-default">
										<div class="panel-heading higher_level_heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#collapse_forms_body" href="#collapse_forms_<?= config_safe_str($form_cat) ?>" class="double-pad-left">
													<?= $form_cat ?><span class="glyphicon glyphicon-plus"></span>
												</a>
											</h4>
										</div>

										<div id="collapse_forms_<?= config_safe_str($form_cat) ?>" class="panel-collapse collapse">
											<div class="panel-body" style="padding: 0; margin: -1px;" id="collapse_forms_<?= config_safe_str($form_cat) ?>_body">

												<div class="panel panel-default">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a data-toggle="collapse" data-parent="#collapse_forms_<?= config_safe_str($form_cat) ?>_body" href="#collapse_forms_<?= config_safe_str($form_cat) ?>_configure" class="quadruple-pad-left">
																Configure <?= $form_cat ?> Forms<span class="glyphicon glyphicon-plus"></span>
															</a>
														</h4>
													</div>

													<div id="collapse_forms_<?= config_safe_str($form_cat) ?>_configure" class="panel-collapse collapse">
														<div class="panel-body" data-file="intake_softwareforms.php?cat=<?= $form_cat ?>">
															Loading...
														</div>
													</div>
												</div>

												<?php foreach ($form_tab as $form_type) {
													$form_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) num_rows FROM `intake` WHERE `assigned_date` = '0000-00-00' AND `deleted` = 0 AND `intakeformid` = '".$form_type['intakeformid']."'"))['num_rows']; ?>
													<div class="panel panel-default">
														<div class="panel-heading">
															<h4 class="panel-title">
																<a data-toggle="collapse" data-parent="#collapse_forms_<?= config_safe_str($form_cat) ?>_body" href="#collapse_forms_<?= config_safe_str($form_cat) ?>_<?= $form_type['intakeformid'] ?>" class="quadruple-pad-left">
																	<?= $form_type['form_name'] ?><span class="pull-right"><?= $form_count ?></span><span class="glyphicon glyphicon-plus"></span>
																</a>
															</h4>
														</div>

														<div id="collapse_forms_<?= config_safe_str($form_cat) ?>_<?= $form_type['intakeformid'] ?>" class="panel-collapse collapse">
															<div class="panel-body" data-file="intake_completedsoftware.php?type=<?= $form_type['intakeformid'] ?>">
																Loading...
															</div>
														</div>
													</div>
												<?php } ?>

											</div>
										</div>
									</div>
									<?php }
								} else { ?>
								<?php foreach ($form_types as $form_type) {
									$form_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) num_rows FROM `intake` WHERE `assigned_date` = '0000-00-00' AND `deleted` = 0 AND `intakeformid` = '".$form_type['intakeformid']."'"))['num_rows']; ?>
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#collapse_forms_body" href="#collapse_forms_<?= $form_type['intakeformid'] ?>" class="double-pad-left">
													<?= $form_type['form_name'] ?><span class="pull-right"><?= $form_count ?></span><span class="glyphicon glyphicon-plus"></span>
												</a>
											</h4>
										</div>

										<div id="collapse_forms_<?= $form_type['intakeformid'] ?>" class="panel-collapse collapse">
											<div class="panel-body" data-file="intake_completedsoftware.php?type=<?= $form_type['intakeformid'] ?>">
												Loading...
											</div>
										</div>
									</div>
								<?php }
							} ?>

						</div>
					</div>
				</div>

			</div>

			<form name="form_sites" method="post" action="" class="form-inline" role="form">
	            <div class="hide-titles-mob standard-collapsible tile-sidebar set-section-height">
	            	<?php include('../Intake/tile_sidebar.php'); ?>
	            </div>

	            <div class="hide-titles-mob scale-to-fill has-main-screen">
	            	<div class="main-screen standard-body form-horizontal">
		        		<div class="standard-body-title">
	        				<?php if(!empty($_GET['cat'])) {
	            				echo '<h3>'.$_GET['cat'].' Forms</h3>';
	        				} else if(!empty($_GET['type'])) {
	        					echo '<h3>'.$intake['form_name'].'</h3>';
	        				} else if($tab == 'webforms') {
	        					echo '<h3>Website Forms</h3>';
	        				} else {
	        					echo '<h3>Forms</h3>';
	        				} ?>
	            		</div>
	            		<div class="standard-body-content" style="padding: 0 0.5em;">
	            			<?php if($include_search_icon) {
	            				if($_POST['search_filters'] == 'Display Default') {
	            					$_POST['search_start_date'] = '';
	            					$_POST['search_end_date'] = '';
	            				} ?>
					            <div class="menu-bar" style="position: fixed; right: 20px; z-index: 1; top: 130px; display: block;">
					                <img src="../img/icons/ROOK-3dot-icon.png" width="30" class="no-toggle cursor-hand pull-right menu_button offset-right-10 theme-color-icon" title="" data-original-title="Search Filters" onclick="show_search_fields();">
					            </div>
					            <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
						            <div class="search-fields" style="padding: 1em; display: none;">
						                <h4>Search Filters</h4>
						                <div class="col-sm-6">
						                    <label class="col-sm-4">Start Date:</label>
						                    <div class="col-sm-8">
						                        <input type="text" name="search_start_date" value="<?= !empty($_POST['search_start_date']) ? $_POST['search_start_date'] : '' ?>" class="form-control datepicker">
						                    </div>
						                </div>
						                <div class="col-sm-6">
						                    <label class="col-sm-4">End Date:</label>
						                    <div class="col-sm-8">
						                        <input type="text" name="search_end_date" value="<?= !empty($_POST['search_end_date']) ? $_POST['search_end_date'] : '' ?>" class="form-control datepicker">
						                    </div>
						                </div>
						                <div class="clearfix"></div>
						                <div class="col-sm-12 pull-right pad-5">
							                <button type="submit" name="search_filters" value="Display Default" class="btn brand-btn pull-right">Display Default</button>
							                <button type="submit" name="search_filters" value="Submit" class="btn brand-btn pull-right">Submit</button>
							            </div>
						                <div class="clearfix"></div>
						            </div>
						        </form>
					        <?php } ?>

				        	<!-- Notice -->
				            <div class="notice gap-bottom gap-top popover-examples">
				                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
				                <div class="col-sm-11"><span class="notice-name">NOTE:</span>
								<?= $note ?></div>
				                <div class="clearfix"></div>
				            </div>

				            <?php 
				            	include('../Intake/'.$include_url); ?>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>