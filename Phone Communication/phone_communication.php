<?php

/*
Equipment Listing
*/
include ('../include.php');
error_reporting(0);
?>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('phone_communication');
?>
<div class="container">
    <div class="row">

    <div class="col-sm-10">
		<h1>Phone Communication</h1>
	</div>
	<div class="col-sm-2 double-gap-top">
		<?php
			if(config_visible_function($dbc, 'phone_communication') == 1) {
				echo '<a href="field_config.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
			}
		?>
	</div>
	
	<div class="clearfix double-gap-bottom"></div>
	
	<?php
    $internal_active_tab = '';
    $external_active_tab = '';
    if($_GET['type'] == 'Internal') {
        $internal_active_tab = 'active_tab';
    }
    if($_GET['type'] == 'External') {
        $external_active_tab = 'active_tab';
    }
	$fax_db = ','.get_config($dbc,'fax_communication_db').',';
	$fax_fields = ','.get_config($dbc,'fax_communication').',';
	?>
	
	<div class="tab-container mobile-100-container">
		<?php if ( check_subtab_persmission($dbc, 'phone_communication', ROLE, 'internal') === TRUE ) { ?>
			<a href="phone_communication.php?type=Internal"><button type="button" class="mobile-100 btn brand-btn mobile-block <?php echo $internal_active_tab; ?>">Internal</button></a>&nbsp;&nbsp;
		<?php } else { ?>
			<button type="button" class="btn disabled-btn mobile-block mobile-100">Internal</button>&nbsp;&nbsp;
		<?php } ?>
		
		<?php if ( check_subtab_persmission($dbc, 'phone_communication', ROLE, 'external') === TRUE ) { ?>
			<a href="phone_communication.php?type=External"><button type="button" class="mobile-100 btn brand-btn mobile-block <?php echo $external_active_tab; ?>">External</button></a>&nbsp;&nbsp;
		<?php } else { ?>
			<button type="button" class="btn disabled-btn mobile-block mobile-100">External</button>&nbsp;&nbsp;
		<?php } ?>
		
		<?php if ( check_subtab_persmission($dbc, 'phone_communication', ROLE, 'fax') === TRUE && str_replace(',','',$fax_db) != '' || str_replace(',','',$fax_db) != '') { ?>
			<a href="phone_communication.php?type=Fax"><button type="button" class="mobile-100 btn brand-btn mobile-block <?= $_GET['type'] == 'Fax' ? 'active_tab' : '' ?>">Fax</button></a>&nbsp;&nbsp;
		<?php } ?>
		
		<?php if ( check_subtab_persmission($dbc, 'phone_communication', ROLE, 'log') === TRUE ) { ?>
			<a href="log_phone_communication.php"><button type="button" class="btn mobile-100 brand-btn mobile-block">Log</button></a>&nbsp;&nbsp;
		<?php } else { ?>
			<button type="button" class="btn disabled-btn mobile-block mobile-100">Log</button>&nbsp;&nbsp;
		<?php } ?>
	</div>

    <?php include('phone_list.php'); ?>
</div>
</div>

<?php include ('../footer.php'); ?>
