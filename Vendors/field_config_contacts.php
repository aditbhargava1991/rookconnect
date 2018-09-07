<?php
/*
 * Vendor Tile Settings
 */
include ('../include.php');
checkAuthorised('vendors');

error_reporting(0);

$cat_page = $_GET[ 'category' ];

/* Sub Tabs sub tab submit */
if (isset($_POST['add_general'])) {
	$contacts_classification = filter_var($_POST['contacts_classification'],FILTER_SANITIZE_STRING);
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='vendor_classification'"));
	if($get_config['configid'] > 0) {
		$query_update_config = "UPDATE `general_configuration` SET `value`='$contacts_classification' WHERE `name`='vendor_classification'";
		$result_update_config = mysqli_query($dbc, $query_update_config);
	} else {
		$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('vendor_classification', '$contacts_classification')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

	$contacts_region = filter_var($_POST['contacts_region'], FILTER_SANITIZE_STRING);
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='vendor_region'"));
	if($get_config['configid'] > 0) {
		$query_update_config = "UPDATE `general_configuration` SET `value`='$contacts_region' WHERE `name`='vendor_region'";
		$result_update_config = mysqli_query($dbc, $query_update_config);
	} else {
		$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('vendor_region', '$contacts_region')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

	$field_category = filter_var($_POST['contact_field_category'],FILTER_SANITIZE_STRING);
    $field_category_lower = strtolower($field_category);
    $field_tabs = filter_var($_POST['contact_field_subtabs'],FILTER_SANITIZE_STRING);
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE name='vendor_field_subtabs'"));
	if($get_config['configid'] > 0) {
		$query_update_config = "UPDATE `general_configuration` SET `value`='$field_tabs' WHERE `name=`='vendor_field_subtabs'";
		$result_update_config = mysqli_query($dbc, $query_update_config);
	} else {
		$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('vendor_field_subtabs', '$field_tabs')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

	echo '<script type="text/javascript">window.location.replace("field_config_contacts.php?type=general&category='.$field_category.'");</script>';
}


/* Dashboard sub tab submit */
if (isset($_POST['inv_dashboard'])) {
	//$tab_dashboard = filter_var($_POST['tab_dashboard'],FILTER_SANITIZE_STRING);
    $tab_dashboard = 'vendor';
	$dashboard = implode(',', $_POST['dashboard']);
    $dashboard_subtab_field = $_POST['dashboard_subtab_field'];
	if (strpos(','.$dashboard.',', ','.'Category'.',') === false) {
		$dashboard = 'Category,'.$dashboard;
	}

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configvendorid`) AS `configvendorid` FROM `field_config_vendors` WHERE `tab`='$tab_dashboard' AND `subtab`='$dashboard_subtab_field'"));
	if($get_field_config['configvendorid'] > 0) {
		$query_update_config = "UPDATE `field_config_vendors` SET `dashboard`='$dashboard' WHERE `tab`='$tab_dashboard' AND `subtab`='$dashboard_subtab_field'";
		$result_update_config = mysqli_query($dbc, $query_update_config);
	} else {
		$query_insert_config = "INSERT INTO `field_config_vendors` (`tab`, `subtab`, `dashboard`) VALUES ('$tab_dashboard', 'dashboard_subtab_field', '$dashboard')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}
	echo '<script type="text/javascript">window.location.replace("field_config_contacts.php?type=dashboard&tab='.$tab_dashboard.'&subtab='.$dashboard_subtab_field.'");</script>';
}


/* Fields sub tab submit */
if (isset($_POST['inv_field'])) {
	//$tab_field = filter_var($_POST['tab_field'],FILTER_SANITIZE_STRING);
    $tab_field      = 'vendor';
	$subtab_field   = filter_var($_POST['subtab_field'],FILTER_SANITIZE_STRING);
	$accordion      = filter_var($_POST['accordion'],FILTER_SANITIZE_STRING);
	$order          = filter_var($_POST['order'],FILTER_SANITIZE_STRING);

	$add_order  = ( $order != '' ) ? ", `order` = '$order'" : "";
	$add_subtab = ( $subtab_field != '' ) ? ", `subtab` = '$subtab_field'" : "";

	$vendors = implode(',', $_POST['vendors']);

	if (strpos(','.$vendors.',', ','.'Category'.',') === false) {
		$vendors = 'Category,'.$vendors;
	}

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configvendorid`) AS `configvendorid` FROM `field_config_vendors` WHERE `tab`='$tab_field' AND `accordion`='$accordion'"));
    if($get_field_config['configvendorid'] > 0) {
        $query_update_config = "UPDATE `field_config_vendors` SET `fields`='$vendors' $add_order $add_subtab WHERE `tab`='$tab_field' AND `accordion`='$accordion'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `field_config_vendors` (`tab`, `subtab`, `accordion`, `fields`, `order`) VALUES ('$tab_field', '$subtab_field', '$accordion', '$vendors', '$order')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	echo '<script type="text/javascript">window.location.replace("field_config_contacts.php?type=field&tab='.$tab_field.'&subtab='.$subtab_field.'&accr='.$accordion.'");</script>';
}
?>

<script type="text/javascript">
$(document).ready(function() {
	$("#acc").change(function() {
		var tab = $("#tab_field").val();
		if(tab == undefined) {
			tab = 'vendor';
		}
        var subtab = $("#subtab_field").val();
		if(subtab == undefined) {
			subtab = '';
		}
		window.location = 'field_config_contacts.php?type=field&tab='+tab+'&subtab='+subtab+'&accr='+this.value;
	});

    $('#contact_field_category').on('change', function() {
        var category = $(this).val();
        var folder_name = 'vendor';
        $.ajax({
            type: "GET",
            url: "../ajax_all.php?fill=contact_field_category&category="+category+"&folder_name="+folder_name,
            dataType: "html",
            success: function(response){
                $('#contact_field_subtabs').val(response);
            }
		});
    });

    $('#dashboard_subtab_field').on('change', function() {
        var subtab = $("#dashboard_subtab_field").val();
		if(subtab == undefined) {
			subtab = '';
		}
		window.location = 'field_config_contacts.php?type=dashboard&tab=vendor&subtab='+subtab;
    });

	$('input.show_impexp_contact').on('change', function() {
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({	//create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=show_impexp_contact&value="+value,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});
	var selected = $('[name=accordion]').val();
	var options = $('[name=accordion]').find('option');
	options.sort(function(a,b) {
		if(b.text == '')
			return 0;
		if(a.text.substring(0,1) == ':')
			return 0;

		aValue = parseInt(a.text.split(':')[0].trim());
		bValue = parseInt(b.text.split(':')[0].trim());
		if(aValue > bValue) {
			return 1;
		} else {
			return -1;
		}
		return 0;
	});
	$('[name=accordion]').empty().append(options);
	$('[name=accordion]').val(selected).trigger('change.select2');

	if($('#acc').val() == '') {
		$('input[type=checkbox]').attr('disabled','disabled');
		$('button[type=submit]').attr('disabled','disabled');
	}
});
</script>

</head>
<body>
<?php include ('../navigation.php'); ?>

<div class="container">
    <div class="row">
        <h1><?= VENDOR_TILE ?></h1>
        <a href="contacts.php?category=<?= $cat_page; ?>&filter=Top" class="gap-top btn config-btn">Back to Dashboard</a>

        <form id="form1" name="form1" method="post"	action="field_config_contacts.php" enctype="multipart/form-data" class="form-horizontal">
            <div class="panel-group" id="accordion2"><?php
                $contype    = ( isset($_GET['tab']) && !empty($_GET['tab']) ) ? $_GET['tab'] : 'vendor';
                $accr       = $_GET['accr'];
                $type       = $_GET['type'];
                $category   = $_GET['category'];
                $url_subtab = ( isset($_GET['subtab']) && !empty($_GET['subtab']) ) ? $_GET['subtab'] : '';

                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `fields` FROM `field_config_vendors` WHERE `tab`='$contype' AND `accordion`='$accr'"));
                $fields_config = ','.$get_field_config['fields'].',';

                if ( !empty($url_subtab) ) {
                    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `dashboard` FROM `field_config_vendors` WHERE `tab`='$contype' AND `subtab`='$url_subtab'"));
                } else {
                    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `dashboard` FROM `field_config_vendors` WHERE `tab`='$contype' AND `accordion` IS NULL"));
                }
                $dashboard_config = ','.$get_field_config['dashboard'].',';

                $get_field_order = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT GROUP_CONCAT(`order` SEPARATOR ',') AS `all_order` FROM `field_config_vendors` WHERE `tab`='$contype'"));

                $active_general     = ($_GET['type'] == 'general') ? 'active_tab' : '';
                $active_dashboard   = ($_GET['type'] == 'dashboard') ? 'active_tab' : '';
                $active_field       = ($_GET['type'] == 'field') ? 'active_tab' : '';
                $impexp             = ($_GET['type'] == 'impexp') ? 'active_tab' : ''; ?>

                <div class="tab-container">
                    <div class="pull-left tab"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click this to add the sub tabs in Vendors. This will help to organize your Vendors by a specific business."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><a href="field_config_contacts.php?type=general&category=<?= $category; ?>"><button type="button" class="btn brand-btn mobile-block <?= $active_general; ?>">Sub Tabs</button></a></div>

                    <div class="pull-left tab"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click this to add or remove so you can organize your own tabs."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><a href="field_config_contacts.php?type=dashboard&category=<?= $category; ?>"><button type="button" class="btn brand-btn mobile-block <?= $active_dashboard; ?>">Dashboard</button></a></div>

                    <div class="pull-left tab"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to add fields within the tabs, you must add a tab before adding fields. These determine which fields you want when creating a new type of contact."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><a href="field_config_contacts.php?type=field&category=<?= $category; ?>&tab=vendor"><button type="button" class="btn brand-btn mobile-block <?= $active_field; ?>">Fields</button></a></div>

                    <!--
                    <div class="pull-left tab"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click this to enable/disable the ability to export a spreadsheet of your Contacts. This functionality also allows you to edit/add multiple contacts at a time by importing a spreadsheet into the software."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><a href="field_config_contacts.php?type=impexp&category=<?= $category; ?>"><button type="button" class="btn brand-btn mobile-block <?= $impexp; ?>">Import/Export</button></a></div>
                    -->
                </div>

                <div class="clearfix"></div><?php

                /* ----- Sub Tabs ----- */
                if($_GET['type'] == 'general') { ?>
                    <h3>Tabs</h3>
                    <div class="panel-group" id="accordion_tabs">
                        <!-- How To -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here first for the Step by Step Guide on how to organize which classifications you would like to use for each sub tab."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                    <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_1">
                                        How To<span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse_1" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <strong>Step 1:</strong><br />
                                    Think of the sub tabs you would like to organize your Contacts Dashboard. If you do not wish to add any sub tabs at this point, simply skip this section.<br />
                                    <br />
                                    <strong>Step 2:</strong><br />
                                    Simply click to the next Add sub tabs section to fill out which sub tabs will be displayed on the Contacts Dashboard.<br />
                                    <br />
                                    <strong>Step 3:</strong><br />
                                    Click Submit to make sure your changes are captured. If you click Back, it will not save your changes.<br />
                                    <br />
                                    <strong>Reminder:</strong><br />
                                    Separate these tabs with a comma, with no spaces between the comma and the next entry. The order in which you enter them into the Add Divisions bar will be the order they appear in your Contacts Dashboard.
                                </div>
                            </div>
                        </div><!-- .panel .panel-default -->

                        <!-- Add Regions -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to add your own regions."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                    <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_region">
                                        Add Regions<span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse_region" class="panel-collapse collapse">
                                <div class="panel-body">
                                    Add Region separated by a comma in the order you want them on the dashboard:<br />
                                    <br />
                                    <input name="contacts_region" type="text" value="<?php echo get_config($dbc, FOLDER_NAME.'_region'); ?>" class="form-control"/><br />

                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                                            <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                                        </div>
                                        <div class="col-sm-6">
                                            <button	type="submit" name="add_general" value="add_general" class="btn config-btn btn-lg pull-right">Submit</button>
                                        </div>
                                    </div>

                                    <!--
                                    <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Company Name:</label>
                                        <div class="col-sm-8">
                                            <input name="company_name" type="text" value="<?php echo get_config($dbc, 'company_name'); ?>" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg pull-left">Back</a>
                                        </div>
                                        <div class="col-sm-6">
                                            <button	type="submit" name="add_general" value="add_general" class="btn config-btn btn-lg pull-right">Submit</button>
                                        </div>
                                    </div>
                                    -->

                                </div>
                            </div>
                        </div><!-- .panel .panel-default -->

                        <!-- Add Divisions -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to add your own sub tabs."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                    <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_2">
                                        Add Divisions<span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse_2" class="panel-collapse collapse">
                                <div class="panel-body">
                                    Add Division separated by a comma in the order you want them on the dashboard:<br />
                                    <br />
                                    <input name="contacts_classification" type="text" value="<?php echo get_config($dbc, 'vendor_classification'); ?>" class="form-control"/><br />

                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                                            <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                                        </div>
                                        <div class="col-sm-6">
                                            <button	type="submit" name="add_general" value="add_general" class="btn config-btn btn-lg pull-right">Submit</button>
                                        </div>
                                    </div>

                                    <!--
                                    <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Company Name:</label>
                                        <div class="col-sm-8">
                                            <input name="company_name" type="text" value="<?php echo get_config($dbc, 'company_name'); ?>" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg pull-left">Back</a>
                                        </div>
                                        <div class="col-sm-6">
                                            <button	type="submit" name="add_general" value="add_general" class="btn config-btn btn-lg pull-right">Submit</button>
                                        </div>
                                    </div>
                                    -->

                                </div>
                            </div>
                        </div><!-- .panel .panel-default -->

                        <!-- Add Contact Subtabs -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to add your own sub tabs."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                    <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_3">
                                        Add Contact Field Tabs<span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse_3" class="panel-collapse collapse">
                                <div class="panel-body">
                                    Add Field sub tab separated by a comma in the order you want them on the Edit page:<br />
                                    <small><em>Unused tabs will not show up, you will need to apply these tabs to accordions before they will appear. This will control the order of the selected tabs.</em></small>
                                    <br />
                                    <input name="contact_field_subtabs" id="contact_field_subtabs" type="text" value="<?php echo get_config($dbc, 'vendor_field_subtabs'); ?>" class="form-control"/><br />

                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg pull-left">Back</a>
                                        </div>
                                        <div class="col-sm-6">
                                            <button	type="submit" name="add_general" value="add_general" class="btn config-btn btn-lg pull-right">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .panel .panel-default -->

                    </div><!-- #accordion_tabs --><?php
                }


                /* ----- Dashboard ----- */
                if($_GET['type'] == 'dashboard') { ?>
                    <div class="form-group triple-gap-top"><?php
                        $url_subtab = ( isset($_GET['subtab']) && !empty($_GET['subtab']) ) ? $_GET['subtab'] : '';
                        $category = strtolower ( preg_replace('/\PL/u', '', $_GET['tab']) );
                        $subtab_config = get_config($dbc, 'vendor_field_subtabs');

                        if($subtab_config != '') { ?>
                            <label for="fax_number"	class="col-sm-4	control-label">Field Sub Tab:</label>
                            <div class="col-sm-8">
                                <select data-placeholder="Select a Sub Tab..." id="dashboard_subtab_field" name="dashboard_subtab_field" class="chosen-select-deselect form-control" width="380">
                                    <option></option>
                                    <?php
                                    if($subtab == '') {
                                        $subtab = get_field_config_vendors($dbc, $accr, 'subtab', $contype);
                                    }
                                    $subtabs = explode(',',$subtab_config);
                                    foreach($subtabs as $this_tab) {
                                        if ( $this_tab=='Profile' || $this_tab=='Price Lists' ) {
                                            echo "<option ".($this_tab == $url_subtab ? 'selected' : '')." value='$this_tab'>$this_tab</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div><?php
                        } else {
                            $sql_clear_subtabs = "UPDATE `field_config_vendors` SET `subtab`='' WHERE `tab` NOT IN ('Staff','Profile')";
                            mysqli_query($dbc, $sql_clear_subtabs);
                        } ?>
                    </div>

                    <h3>Dashboard</h3>
                    <div class="panel-group" id="accordion2">
                        <!-- How To -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here first for the Step by Step guide on how to personalize fields shown on the Contacts Dashboard view."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_0">
                                        How To<span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse_0" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <strong>Step 1:</strong><br />
                                    Make sure you click on the dropdown menu beside Tabs. This allows you to add or remove fields in the tab. This dropdown menu is populated from the tabs you have created.<br />
                                    <br />
                                    <strong>Step 2:</strong><br />
                                    Once you are in the desired tab, go through each accordion heading to check off which fields you would like to appear in that tab.<br />
                                    <br />
                                    <strong>Step 3:</strong><br />
                                    Click Submit to make sure your changes are captured. If you click Back, it will not save your changes.
                                </div>
                            </div>
                        </div><!-- .panel .panel-default -->

                        <?php include('config_dashboard_list.php'); ?>

                    </div>

                    <div class="form-group">
                        <div class="col-sm-6 clearfix">
                            <a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
                            <!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
                        </div>
                        <div class="col-sm-6">
                            <button	type="submit" name="inv_dashboard"	value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
                        </div>
                    </div>

                <?php }


                /* ----- Fields ----- */
                if($_GET['type'] == 'field') { ?>
                    <div class="form-group triple-gap-top">
                        <label for="fax_number"	class="col-sm-4	control-label"><span class="popover-examples list-inline" style="margin:0 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="This is the vertically stacked list of items seen below. Click to determine which list of items you would like to make changes to. This must be selected before you can select fields."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Accordion:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Choose an Accordion..." id="acc" name="accordion" class="chosen-select-deselect form-control" width="380">
                                <?php include('config_accordion_list.php'); ?>
                            </select>
                        </div><?php

                        $category = strtolower ( preg_replace('/\PL/u', '', $_GET['tab']) );
                        $subtab_config = get_config($dbc, 'vendor_field_subtabs');

                        if($subtab_config != '') { ?>
                            <label for="fax_number"	class="col-sm-4	control-label">Field Sub Tab:</label>
                            <div class="col-sm-8">
                                <select data-placeholder="Select a Sub Tab..." id="subtab_field" name="subtab_field" class="chosen-select-deselect form-control" width="380">
                                    <option></option>
                                    <?php
                                    if($subtab == '') {
                                        $subtab = get_field_config_vendors($dbc, $accr, 'subtab', $contype);
                                    }
                                    $subtabs = explode(',',$subtab_config);
                                    foreach($subtabs as $this_tab) {
                                        if ( $this_tab=='Profile' || $this_tab=='Price Lists' ) {
                                            echo "<option ".($this_tab == $subtab ? 'selected' : '')." value='$this_tab'>$this_tab</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div><?php
                        } else {
                            $sql_clear_subtabs = "UPDATE `field_config_vendors` SET `subtab`='' WHERE `tab` NOT IN ('Staff','Profile')";
                            mysqli_query($dbc, $sql_clear_subtabs);
                        } ?>

                        <label for="fax_number"	class="col-sm-4	control-label">Sort Order:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Choose an Order..." name="order" class="chosen-select-deselect form-control" width="380">
                                <option value=""></option><?php
                                $accr_order = get_field_config_vendors($dbc, $accr, 'order', $contype, $subtab);
                                for ( $m=1; $m<=40; $m++ ) { ?>
                                    <option <?php if ($accr_order == $m) { echo 'selected="selected"'; } else if (strpos(','.$get_field_order['all_order'].',', ','.$m.',') !== FALSE) { echo " disabled"; } ?>
                                        value="<?= $m; ?>"><?= $m; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                    </div>

                    <h3>Fields</h3>
                    <div class="panel-group" id="accordion2">
                        <!-- How To -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here first for the Step by Step guide on how to personalize fields within your Add Contacts view."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_0">
                                        How To<span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse_0" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <strong>Step 1:</strong><br />
                                    Make sure you click on the dropdown menu beside Tabs. This dropdown menu is populated from the tabs you have created.<br />
                                    <br />
                                    <strong>Step 2:</strong><br />
                                    Once you are in the desired tab, click on the next drop down menu labelled Accordion. This allows to to add whichever Accordion you would like to the selected tab.<br />
                                    <br />
                                    <strong>Step 3:</strong><br />
                                    You can then choose the order in which it appears in the Choose an Order (drop down menu).<br />
                                    <br />
                                    <strong>Step 4:</strong><br />
                                    Go through each Accordion heading to check off which fields you would like to appear in that tab.<br />
                                    <br />
                                    <strong>Step 5:</strong><br />
                                    Click Submit to make sure your changes are captured. If you click Back, it will not save your changes.
                                </div>
                            </div>
                        </div><!-- .panel .panel-default -->

                        <?php include('config_field_list.php'); ?>

                    </div>

                    <div class="form-group">
                        <div class="col-sm-6 clearfix">
                            <a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
                        </div>
                        <div class="col-sm-6">
                            <button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                        </div>
                    </div><?php
                }


                /* ----- Import/Export ----- */
                if($_GET['type'] == 'impexp') { ?>
                    <br><br>
                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="The Import/Export functionality allows users to export a full spreadsheet of the tile's data, as well as add or edit multiple row items at once."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Enable Import/Export:</label>
                        <div class="col-sm-8">
                        <?php
                        $checked = '';
                        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_impexp_contact'"));
                        if($get_config['configid'] > 0) {
                            $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_impexp_contact'"));
                            if($get_config['value'] == '1') {
                                $checked = 'checked';
                            }
                        }
                        ?>
                          <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='' class='show_impexp_contact' value='1'>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                            <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                        </div>
                        <div class="col-sm-6">
                            <button	type="submit" name="imp_exp_en" value="" class="btn config-btn btn-lg pull-right">Submit</button>
                        </div>
                    </div><?php
                } ?>

</form>
</div>
</div>
</div>

<?php include ('../footer.php'); ?>