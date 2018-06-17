<?php
/*
Inventory Listing
*/
include ('../include.php');
$rookconnect = get_software_name();
?>
</head>
<body>
<?php
	include_once ('../navigation.php');
	checkAuthorised();
	error_reporting(0);

	$detect			= new Mobile_Detect;
	$is_mobile		= ( $detect->isMobile() ) ? true : false;
	$mobile_view	= false;

	/* Get logged in user's role */
	if ( !empty ( $_GET[ 'level' ] ) ) {
		$level_url = $_GET[ 'level' ];

	} else {
		$contacterid	= $_SESSION['contactid'];
		$result	= mysqli_query ( $dbc, "SELECT * FROM contacts WHERE contactid='$contacterid'" );
		/*if($_GET['category'] == 'Patient') {
			$result	= mysqli_query ( $dbc, "SELECT * FROM contacts WHERE contactid='$contacterid' and status = 1" );
		}
		else {
			$result	= mysqli_query ( $dbc, "SELECT * FROM contacts WHERE contactid='$contacterid'" );
		}*/

		while ( $row = mysqli_fetch_assoc( $result ) ) {
			$role = $row[ 'role' ];
		}

		$level_url = (strpos(','.ROLE.',',',super,') !== false) ? 'admin' : $role;
	}
//Status change
if(isset($_GET['status'])){
	$status=$_GET['status'];
	$contactidstatus=$_GET['contactid'];
	$query_update_status = "UPDATE contacts SET status = $status WHERE contactid='$contactidstatus'";
	$result_update_status = mysqli_query($dbc, $query_update_status);
}
?>

<div class="container">
	<div class="row">
        <?php
        if(isset($_GET['from_url'])) {
            echo '<a href="'.urldecode($_GET['from_url']).'" class="btn brand-btn">Back</a>';
        } ?>
		<h1 class="single-pad-bottom">
			<span class="popover-examples list-inline hide-on-mobile"><a style="margin:0 0 0 15px;" data-toggle="tooltip" data-placement="top" title="This is where you will store all of your contact information pertaining to your business."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="30"></a></span>
			Contacts Dashboard<?php
			if(config_visible_function($dbc, 'contacts') == 1) {
				echo '<a href="field_config_contacts.php?category='. $_GET[ 'category' ] .'&type=tab" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right" style="margin:-5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span><br><br>';
			} ?>
		</h1>

		<form name="form_search" method="post" action="" class="form-inline" role="form">
			<!--<div class='mobile-100-container' >-->
			<div class="tab-container"><?php
				$tabs = str_replace(',,',',',str_replace('Staff','',get_config($dbc, FOLDER_NAME.'_tabs')));
				// If there is no configuration, as for new Software Installs, it should use the following configuration

                /*
                if(str_replace(',', '', $tabs) == '' && FOLDER_NAME == 'contacts') {
					$tabs = 'Business,Customers,Vendors,Contractors,Sales Leads';
					$sql_tabs_update = "UPDATE `general_configuration` SET `value`='$tabs' WHERE `name`='".FOLDER_NAME."_tabs'";
					$sql_tabs_insert = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('".FOLDER_NAME."_tabs', '$tabs')";
					$sql_tabs_num = "SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='".FOLDER_NAME."_tabs'";
					$tabs_num = mysqli_fetch_array(mysqli_query($dbc,$sql_tabs_num));
					if($tabs_num['rows'] == 0) {
						mysqli_query($dbc, $sql_tabs_insert);
					} else {
						mysqli_query($dbc, $sql_tabs_update);
					}
					$config_count = mysqli_fetch_array(mysqli_query($dbc,"SELECT COUNT(*) numrows FROM `field_config_contacts` WHERE tile_name = '".FOLDER_NAME."' AND ',$tabs,' LIKE CONCAT('%,',`tab`,',%')"));
					if($config_count['numrows'] == 0) {
						$sql_config = mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tab`, `subtab`, `accordion`, `contacts`, `order`, `contacts_dashboard`) VALUES
							('Vendors', NULL, NULL, NULL, NULL, 'Category,Business,First Name,Last Name,Division,Emergency Contact,Office Phone,Cell Phone,Email Address'),
							('Vendors', NULL, 'Business Information', 'Category,Business', 1, 'Category,Business,First Name,Last Name,Division,Emergency Contact,Office Phone,Cell Phone,Email Address'),
							('Contractors', NULL, NULL, NULL, NULL, 'Category,Business,First Name,Last Name,Division,Emergency Contact,Office Phone,Cell Phone,Email Address'),
							('Contractors', NULL, 'Business Information', 'Category,Business', 1, 'Category,Business,First Name,Last Name,Division,Emergency Contact,Office Phone,Cell Phone,Email Address'),
							('Vendors', NULL, 'Vendor Information', 'Category,Name,Division,Name on Account,Operating As,Emergency Contact,Office Phone,Fax,Email Address,Website,Contact Image,Description,Client Tax Exemption,Tax Exemption Number,DUNS,CAGE,Self Identification', 2, NULL),
							('Vendors', NULL, 'Vendor Address', 'Category,Mailing Address,Business Address,Zip Code,City,Province,State,Country,Google Maps Address', 3, NULL),
							('Vendors', NULL, 'Payment Information', 'Category,Account Number,Payment Type,Payment Name,Payment Address,Payment City,Payment State,Payment Zip Code,GST #,PST #,Payment Information', 4, NULL),
							('Vendors', NULL, 'Dates', 'Category,Start Date,Renewal Date,Option to Renew Date,Rate Increase Date,Insurance Expiry Date,Account Expiry Date', 5, NULL),
							('Vendors', NULL, 'Insurance', 'Category,Upload Commercial Insurance,WCB #', 6, NULL),
							('Vendors', NULL, 'Comments', 'Category,General Comments', 7, NULL),
							('Vendors', NULL, 'Status', 'Category,Status', 8, NULL),
							('Vendors', NULL, 'Social Media Links', 'Category,LinkedIn,Twitter', 9, NULL),
							('Vendors', NULL, 'Login Information', 'Category,User Name,Password', 10, 'Category,Business,First Name,Last Name,Division,Emergency Contact,Office Phone,Cell Phone,Email Address'),
							('Contractors', NULL, 'Contact Information', 'Category,First Name,Last Name,Email Address,BIO,Self Identification,Role,Name on Account,Emergency Contact,Occupation,Office Phone,Cell Phone,Home Phone,Fax,Email Address,Website,Contact Image,Description,Referred By,Position,Title,License #,Credential ,Tax Exemption Number,Division,Alberta Health Care No,', 2, NULL),
							('Contractors', NULL, 'Insurance', 'Category,Upload Commercial Insurance,WCB #,Upload WCB', 3, NULL),
							('Contractors', NULL, 'Social Media Links', 'Category,LinkedIn,Twitter', 4, NULL),
							('Contractors', NULL, 'Vehicle Description', 'Category,License Plate #', 5, NULL),
							('Contractors', NULL, 'Location', 'Mailing Address,Zip Code,City,Province,State,Country,Google Maps Address', 6, NULL),
							('Contractors', NULL, 'Payment Description', 'Category,Account Number,Payment Type,Payment Name,Payment Address,Payment City,Payment State,Payment Postal Code,Payment Zip Code,GST #,PST #,Payment Information,Total Monthly Rate,Total Annual Rate,Quote Description,Pricing Level,Cost,Minimum Billable,Estimated Hours,Actual Hours,Hourly Rate,Monthly Rate,Semi Monthly Rate,Daily Rate,HR Rate Work,HR Rate Travel,Field Day Cost,Field Day Billable', 7, NULL),
							('Contractors', NULL, 'Comments', 'General Comments', 10, 'Category,Business,First Name,Last Name,Division,Emergency Contact,Office Phone,Cell Phone,Email Address'),
							('Contractors', NULL, 'Dates', 'Start Date,Expiry Date,Renewal Date,Date Contract Signed,Option to Renew Date,Rate Increase Date,Insurance Expiry Date,Account Expiry Date,Birth Date', 8, 'Category,Business,First Name,Last Name,Division,Emergency Contact,Office Phone,Cell Phone,Email Address'),
							('Contractors', NULL, 'Insurance', 'Upload Commercial Insurance,WCB #', 9, NULL),
							('Contractors', NULL, 'Status', 'Status', 11, NULL),
							('Contractors', NULL, 'Login Information', 'Category,User Name,Password', 12, 'Category,Business,First Name,Last Name,Division,Emergency Contact,Office Phone,Cell Phone,Email Address'),
							('Business', NULL, 'Contact Information', 'Category,', 3, NULL),
							('Business', NULL, 'Business Address', 'Category,Business Address,Zip Code,City,Province,State,Country,Google Maps Address', 2, NULL),
							('Business', NULL, NULL, NULL, NULL, 'Category,Name,Office Phone,Email Address,Website'),
							('Customers', NULL, NULL, NULL, NULL, 'Category,Business,First Name,Last Name,Office Phone,Cell Phone,Email Address,Website,Position'),
							('Customers', NULL, 'Business Information', 'Category,Business', 1, NULL),
							('Customers', NULL, 'Contact Information', 'Category,First Name,Last Name,Office Phone,Cell Phone,Fax,Email Address,Website,Position', 2, NULL),
							('Customers', NULL, 'Contact Description', 'Category,Division', 3, NULL),
							('Business', NULL, 'Business Information', 'Category,Business,Name,Division,Office Phone,Fax,Email Address,Website,Description,Referred By', 0, NULL),
							('Business', NULL, '', 'Category,', 0, NULL),
							('Sales Leads', NULL, NULL, NULL, NULL, 'Category,Business,Name,Office Phone,Email Address')");
					}
                }

				if(str_replace(',', '', $tabs) == '' && FOLDER_NAME == 'contacts3') {
					$tabs = 'Business,Sites,Customer';
					$tabs_num = mysqli_fetch_array(mysqli_query($dbc,"SELECT COUNT(*) AS rows FROM `general_configuration` WHERE `name`='".FOLDER_NAME."_tabs'"));
					if($tabs_num['rows'] == 0) {
						mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('".FOLDER_NAME."_tabs', '$tabs')");
					} else {
						mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$tabs' WHERE `name`='".FOLDER_NAME."_tabs'");
					}
					$config_count = mysqli_fetch_array(mysqli_query($dbc,"SELECT COUNT(*) AS numrows FROM `field_config_contacts` WHERE tile_name = '".FOLDER_NAME."' AND ',$tabs,' LIKE CONCAT('%,',`tab`,',%')"));
					if($config_count['numrows'] == 0) {
						$sql_config = mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tile_name`, `tab`, `subtab`, `accordion`, `contacts`, `order`, `contacts_dashboard`) VALUES
							('contacts3', 'Business', NULL, 'Business Information', 'Category,Name,Classification,Name on Account,Operating As,Office Phone,Fax,Email Address,Website', 1, 'Category,Name,Total Sites,Total Customers'),
							('contacts3', 'Business', NULL, 'Business Address', 'Category,Business Address', 2, 'Category,Name,Total Sites,Total Customers'),
							('contacts3', 'Business', NULL, NULL, NULL, NULL, 'Category,Name,Total Sites,Total Customers'),
                            ('contacts3', 'Sites', NULL, 'Site Information', 'Category,Business,Site Name (Location),Classification,Name on Account,Operating As,Office Phone,Fax,Email Address,Website', 1, 'Category,Business,Site Name (Location),Total Customers'),
							('contacts3', 'Sites', NULL, 'Location', 'Category,Mailing Address,Zip Code,City,Province,State,Country', 2, 'Category,Business,Site Name (Location),Total Customers'),
							('contacts3', 'Sites', NULL, NULL, NULL, NULL, 'Category,Business,Site Name (Location),Total Customers'),
                            ('contacts3', 'Customer', NULL, 'Business Information', 'Category,Business', 1, 'Category,Business,First Name,Last Name,Office Phone,Contact Category,Site Name (Location)'),
                            ('contacts3', 'Customer', NULL, 'Site Information', 'Category,Business Sites', 2, 'Category,Business,First Name,Last Name,Office Phone,Contact Category,Site Name (Location)'),
                            ('contacts3', 'Customer', NULL, 'Contact Information', 'Category,First Name,Last Name,Classification,Name on Account,Operating As,Office Phone,Cell Phone,Home Phone,Fax,Email Address,Contact Category', 3, 'Category,Business,First Name,Last Name,Office Phone,Contact Category,Site Name (Location)'),
                            ('contacts3', 'Customer', NULL, 'Location', 'Category,Mailing Address,Zip Code,City,Province,State,Country', 4, 'Category,Business,First Name,Last Name,Office Phone,Contact Category,Site Name (Location)'),
							('contacts3', 'Customer', NULL, NULL, NULL, NULL, 'Category,Business,First Name,Last Name,Office Phone,Contact Category,Site Name (Location)')");
					}
				}

				if(str_replace(',', '', $tabs) == '' && FOLDER_NAME == 'contactsrolodex') {
					$tabs = 'Business,Corporation,Customer';
					$tabs_num = mysqli_fetch_array(mysqli_query($dbc,"SELECT COUNT(*) AS rows FROM `general_configuration` WHERE `name`='".FOLDER_NAME."_tabs'"));
					if($tabs_num['rows'] == 0) {
						mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('".FOLDER_NAME."_tabs', '$tabs')");
					} else {
						mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$tabs' WHERE `name`='".FOLDER_NAME."_tabs'");
					}
					$config_count = mysqli_fetch_array(mysqli_query($dbc,"SELECT COUNT(*) AS numrows FROM `field_config_contacts` WHERE tile_name = '".FOLDER_NAME."' AND ',$tabs,' LIKE CONCAT('%,',`tab`,',%')"));
					if($config_count['numrows'] == 0) {
						$sql_config = mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tile_name`, `tab`, `subtab`, `accordion`, `contacts`, `order`, `contacts_dashboard`) VALUES
							('contactsrolodex', 'Business', NULL, 'Business Information', 'Category,Name,Classification,Name on Account,Operating As,Office Phone,Fax,Email Address,Website', 1, 'Category,Name,Total Sites,Total Customers'),
							('contactsrolodex', 'Business', NULL, 'Business Address', 'Category,Business Address', 2, 'Category,Name,Total Sites,Total Customers'),
							('contactsrolodex', 'Business', NULL, NULL, NULL, NULL, 'Category,Name,Total Sites,Total Customers'),

                            ('contactsrolodex', 'Corporation', NULL, 'Site Information', 'Category,Business,Site Name (Location),Classification,Name on Account,Operating As,Office Phone,Fax,Email Address,Website', 1, 'Category,Business,Site Name (Location),Total Customers'),
							('contactsrolodex', 'Corporation', NULL, 'Location', 'Category,Mailing Address,Zip Code,City,Province,State,Country', 2, 'Category,Business,Site Name (Location),Total Customers'),
							('contactsrolodex', 'Corporation', NULL, NULL, NULL, NULL, 'Category,Business,Site Name (Location),Total Customers'),
                            ('contactsrolodex', 'Customer', NULL, 'Business Information', 'Category,Business', 1, 'Category,Business,First Name,Last Name,Office Phone,Contact Category,Site Name (Location)'),
                            ('contactsrolodex', 'Customer', NULL, 'Site Information', 'Category,Business Sites', 2, 'Category,Business,First Name,Last Name,Office Phone,Contact Category,Site Name (Location)'),
                            ('contactsrolodex', 'Customer', NULL, 'Contact Information', 'Category,First Name,Last Name,Classification,Name on Account,Operating As,Office Phone,Cell Phone,Home Phone,Fax,Email Address,Contact Category', 3, 'Category,Business,First Name,Last Name,Office Phone,Contact Category,Site Name (Location)'),
                            ('contactsrolodex', 'Customer', NULL, 'Location', 'Category,Mailing Address,Zip Code,City,Province,State,Country', 4, 'Category,Business,First Name,Last Name,Office Phone,Contact Category,Site Name (Location)'),
							('contactsrolodex', 'Customer', NULL, NULL, NULL, NULL, 'Category,Business,First Name,Last Name,Office Phone,Contact Category,Site Name (Location)')");
					}
				}

                */

				$each_tab = explode(',', $tabs);
				if(in_array('Patient',$each_tab)) {
					$key = array_search('Patient',$each_tab);
					array_splice($each_tab,$key,1,['Active Patient','Inactive Patient']);
				}
				$category = trim ( (empty($_GET['category']) ? $each_tab[0] : $_GET['category']) );
				$main_label = '';

				if($category == 'Active Patient' || $category == 'Inactive Patient') {
					$category = 'Patient';
				}

				$active_all = '';
				if($category == 'Top') {
					$active_all = 'active_tab';
				} else if($category == 'All') {
					$category = '%';
				}

				foreach ($each_tab as $cat_tab) {
					$active_daily = '';

					/*
					 * Check subtab settings
					 * Function check_subtab_persmission( database_connection, tile_name, security_level, subtab_name )
					 */
					 $subtab = (($cat_tab == 'Active Patient' || $cat_tab == 'Inactive Patient') ? 'Patient' : $cat_tab);
					$display = check_subtab_persmission( $dbc, 'contacts', $level_url, $subtab );

					if ( $category == $cat_tab || ($category == 'Patient' && ((empty($_GET['category']) && $cat_tab == $each_tab[0]) || ($cat_tab == $_GET['category']))) ) {
						$active_daily = 'active_tab';
					}

					if ( $cat_tab == 'Business' ) {
						$info_contact_type = 'These are contacts by '.(get_software_name() == 'breakthebarrier' ? 'program/site' : ($rookconnect == 'highland' ? 'customer' : 'business')).' name.';
					} elseif ( $cat_tab == 'Clients' ) {
						$info_contact_type = 'These are the clients who are linked to a '.(get_software_name() == 'breakthebarrier' ? 'program/site' : ($rookconnect == 'highland' ? 'customer' : 'business')).'.';
					} elseif ( $cat_tab == 'Customer' || $cat_tab == 'Customers' ) {
						$info_contact_type = 'These are '.($rookconnect == 'highland' ? 'contacts' : 'customers').' who are linked to a '.(get_software_name() == 'breakthebarrier' ? 'program/site' : ($rookconnect == 'highland' ? 'customer' : 'business')).'.';
					} elseif ( $cat_tab == 'Contractors' ) {
						$info_contact_type = 'These are the contractors who are utilized by your company.';
					} elseif ( $cat_tab == 'Vendors' ) {
						$info_contact_type = 'These are your vendors.';
					} elseif ( $cat_tab == 'Admin' ) {
						$info_contact_type = 'These are contacts that provide admin services to you.';
					} elseif ( $cat_tab == 'sales Leads' || $cat_tab == 'Sales Leads' ) {
						$info_contact_type = 'Potential sales leads.';
					} elseif ( $cat_tab == 'Donors' ) {
						$info_contact_type = 'These are all of your donors.';
					} else {
						$add_s = (substr($cat_tab, -1) == 's') ? '' : 's';
						$info_contact_type = 'These are all of your ' . strtolower($cat_tab) . $add_s . '.';
					}

					// Change Business to Customers and Customers to Contacts for Highland Projects
					if($rookconnect == 'highland' && $cat_tab == 'Business') {
						$cat_label = "Customers";
					} else if($rookconnect == 'highland' && ($cat_tab == 'Customer' || $category == 'Customers')) {
						$cat_label = "Contacts";
					}  else if($rookconnect == 'breakthebarrier' && $cat_tab == 'Business') {
						$cat_label = "Program/Site";
					} else {
						$cat_label = $cat_tab;
					}

					if($active_daily == 'active_tab') {
						$main_label = $cat_label;
					}

					$subtabs_nav = '';

					/* Display all subtabs to superadmin "super" (FFMAdmin) */
					if (strpos(','.ROLE.',',',super,') !== false) {
						$subtabs_nav .= "<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='". $info_contact_type ."'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>";
						$subtabs_nav .= "<a href='contacts.php?category=" . $cat_tab . "&filter=Top'><button type='button' class='btn brand-btn mobile-100 mobile-block ". $active_daily ."' >". $cat_label ."</button></a>";
					} else {
						if ( $display === true ) {
							$subtabs_nav .= "<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='". $info_contact_type ."'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>";
							$subtabs_nav .= "<a href='contacts.php?category=" . $cat_tab . "&filter=Top'><button type='button' class='btn brand-btn mobile-100 mobile-block ". $active_daily ."' >". $cat_label ."</button></a>";
						} else {
							$subtabs_nav .= "<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='". $info_contact_type ."'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>";
							$subtabs_nav .= "<button type='button' class='btn disabled-btn mobile-100 mobile-block ". $active_daily ."' >". $cat_label ."</button>";
						}
					}

					echo "<div class='pull-left tab'>" . $subtabs_nav . "</div>";

					$top_url = 'Top';

					/*echo "<div class='pull-left tab'><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='". $info_contact_type ."'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span><a href='contacts.php?category=" . $cat_tab . "&filter=".$top_url."'><button type='button' class='btn brand-btn mobile-100 mobile-block ". $active_daily ."' >". $cat_tab ."</button></a></div>";*/
				}


				// Other Tile tabs
				$get_tabs_tiles_config = mysqli_fetch_array(mysqli_query($dbc,"SELECT `value` FROM `general_configuration` WHERE  `name` = '".FOLDER_NAME."_tabs_tiles'"))['value'];
				if ($get_tabs_tiles_config != '') {
					$contacts_tabs_tiles = explode(',', $get_tabs_tiles_config);
					foreach ($contacts_tabs_tiles as $tile_tab) {
						$tile_name = substr($tile_tab, 0, strpos($tile_tab, '_'));
						$tab_name = substr($tile_tab, strpos($tile_tab, '_') + 1);
						if ($tile_name == 'members') {
							$tile_text = 'Members';
							$cat_url = WEBSITE_URL.'/Members/contacts.php?category='.$tab_name.'&filter=Top&contacts_tabs=1';
						}

						$display = check_subtab_persmission( $dbc, $tile_name, $level_url, $tab_name );
						$info_contact_type = 'These are all of your '.$tab_name.' from the '.$tile_text.' tile.';
						$subtabs_nav = '';

						/* Display all subtabs to superadmin "super" (FFMAdmin) */
						if (strpos(','.ROLE.',',',super,') !== false) {
							$subtabs_nav .= "<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='". $info_contact_type ."'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>";
							$subtabs_nav .= "<a href='".$cat_url."'><button type='button' class='btn brand-btn mobile-100 mobile-block ". $active_daily ."' >". $tab_name ."</button></a>";
						} else {
							if ( $display === true ) {
								$subtabs_nav .= "<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='". $info_contact_type ."'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>";
								$subtabs_nav .= "<a href='".$cat_url."'><button type='button' class='btn brand-btn mobile-100 mobile-block ". $active_daily ."' >". $tab_name ."</button></a>";
							} else {
								$subtabs_nav .= "<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='". $info_contact_type ."'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>";
								$subtabs_nav .= "<button type='button' class='btn disabled-btn mobile-100 mobile-block ". $active_daily ."' >". $tab_name ."</button>";
							}
						}

						echo "<div class='pull-left tab'>" . $subtabs_nav . "</div>";

						$top_url = 'Top';
					}
				}

				//echo display_filter('contacts.php'); ?>

				<div class="clearfix"></div>
			</div>

			<div class="tab-container2"><?php
				$region = '';
				if(!empty($_GET['region'])) {
					$region = $_GET['region'];
				}
				$tabs_region = get_config($dbc, FOLDER_NAME.'_region');
				$each_region = explode(',', $tabs_region);

				if ( !empty ( $tabs_region ) ) {
					echo "<div class='popover-examples list-inline pull-left info-single' style='margin:7px 5px 0 0;'><a style='margin:-5px 3px 0 0;' data-toggle='tooltip' data-placement='top' title='These are the different regions to help organize contacts within a certain region.'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></div>";
				}

				foreach ($each_region as $cat_region) {
					$active_region = '';
					if((!empty($_GET['region'])) && ($_GET['region'] == $cat_region)) {
						$active_region = 'active_tab';
					}
					if($cat_region !== NULL && $cat_region !== '') {
						$top_url = 'Top';
						echo "<div class='pull-left tab no-info-tab'><a href='contacts.php?category=".$category."&region=".$cat_region."&filter=".$top_url."'><button type='button' class='btn brand-btn mobile-100 mobile-block ".$active_region."' >".$cat_region."</button></a></div>";
					}
				} ?>

				<div class="clearfix"></div>
			</div><br />

			<div class="tab-container1"><?php
				$classification = '';
				if(!empty($_GET['classification'])) {
					$classification = $_GET['classification'];
				}
				$tabs_class = get_config($dbc, FOLDER_NAME.'_classification');
				$each_class = explode(',', $tabs_class);

				if ( !empty ( $tabs_class ) ) {
					echo "<div class='popover-examples list-inline pull-left info-single' style='margin:7px 5px 0 0;'><a style='margin:-5px 3px 0 0;' data-toggle='tooltip' data-placement='top' title='These help to organize contacts within a certain business.'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></div>";
				}

				foreach ($each_class as $cat_class) {
					$active_class = '';
					if((!empty($_GET['classification'])) && ($_GET['classification'] == $cat_class)) {
						$active_class = 'active_tab';
					}
					if($cat_class !== NULL && $cat_class !== '') {
						$top_url = 'Top';
						echo "<div class='pull-left tab no-info-tab'><a href='contacts.php?category=".$category."&classification=".$cat_class."&filter=".$top_url."'><button type='button' class='btn brand-btn mobile-100 mobile-block ".$active_class."' >".$cat_class."</button></a></div>";
					}
				} ?>

				<div class="clearfix"></div>
			</div>

			<div class="notice double-gap-bottom popover-examples">
				<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
				<div class="col-sm-11"><span class="notice-name">NOTE:</span>
				In this section you can maintain your active and inactive contacts. As a precaution, the software is preset to not allow name duplicates. If you have two contacts with the exact same name, simply add a location, middle initial or provide some sort of differentiating factor in order to add multiples. Contacts can be Viewed, Deactivated, Edited or Archived under their contact type sub tab.</div>
				<div class="clearfix"></div>
			</div>

			<div class="col-xs-12 col-sm-4 col-lg-2 pad-top" style="margin-top:7px;">
				<span class="popover-examples list-inline"><a style="margin:5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="This will search within the tab you have selected at the top."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<label for="search_contacts">Search Within Tab:</label>
			</div>
			<div class="col-sm-4 col-xs-12 col-lg-3 pad-top"><?php
				if ( isset ( $_POST[ 'search_contacts_submit' ] ) ) { ?>
					<input type="text" name="search_contacts" value="<?php echo $_POST['search_contacts']?>" class="form-control"><?php
				} else { ?>
					<input type="text" name="search_contacts" class="form-control"><?php
				} ?>
			</div>
			<div class="col-sm-4 col-xs-12 col-lg-3 pad-top pull-xs-right">
				<button type="submit" name="search_contacts_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
				<span class="popover-examples list-inline hide-on-mobile"><a style="margin:5px 0 0 15px;" data-toggle="tooltip" data-placement="top" title="Refreshes the page to display all contact information under the specific tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<button type="submit" name="display_all_contacts" value="Display All" class="btn brand-btn mobile-block hide-on-mobile">Display All</button>
			</div>

			<?php
				if ( $category == 'Business' ) {
					$info_add_contact = 'Click to add a '.(get_software_name() == 'breakthebarrier' ? 'Program/Site' : ($rookconnect == 'highland' ? 'Customer' : 'Business')).'; you must do this before attaching multiple clients.';
				} elseif ( $category == 'Customer' || $category == 'Customers' ) {
					$info_add_contact = 'Click here to add a '.($rookconnect == 'highland' ? 'Contact' : 'Customer').'.';
				} elseif ( $category == 'Clients' ) {
					$info_add_contact = 'Click to add a Client.';
				} elseif ( $category == 'Contractors' ) {
					$info_add_contact = 'Click here to add a Contractor.';
				} elseif ( $category == 'Vendors' ) {
					$info_add_contact = 'Click here to add a Vendor.';
				} elseif ( $category == 'sales Leads' ) {
					$info_add_contact = 'Click here to add a Sales Lead.';
				} else {
					$info_add_contact = 'No information available.';
				}

			$impexp_or_not ='';
			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_impexp_contact'"));
			if($get_config['configid'] > 0) {
				$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_impexp_contact'"));
				if($get_config['value'] == '1') {
					$impexp_or_not = 'true';
				}
			}

			if ( vuaed_visible_function ( $dbc, 'contacts' ) == 1 ) { ?>
				<div class="col-sm-12 col-xs-12 col-lg-4 pad-top offset-xs-top-20 pull-right">
					<a href="add_contacts.php?category=<?= $category; ?>" class="btn brand-btn mobile-block gap-bottom pull-right">Add <?= $main_label; ?></a>
					<span class="popover-examples list-inline"><a class="pull-right" style="margin:7px 5px 0 15px;" data-toggle="tooltip" data-placement="top" title="<?= $info_add_contact; ?>"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<?php if($impexp_or_not == 'true') { ?>
					<a href="add_contacts_multiple.php?category=<?= $category; ?>" class="btn brand-btn mobile-block gap-bottom pull-right">Import/Export</a>
					<span class="popover-examples list-inline"><a class="pull-right" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click to add multiple contacts at once, edit multiple contacts, or export a list of all of your contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<?php } ?>
				</div><?php
			}
			?>

		<div class="clearfix"></div>

		<div id="no-more-tables" class="triple-pad-top contacts-list">
			<?php
			// Display Pager
			$contacts = '';
			if (isset($_POST['search_contacts_submit'])) {
				$contacts = $_POST['search_contacts'];
				if (isset($_POST['search_contacts'])) {
					$contacts = $_POST['search_contacts'];
				}
				if (isset($_POST['search_category'])) {
					if ($_POST['search_category'] != '') {
						$contacts = $_POST['search_category'];
					}
				}
			}
			if (isset($_POST['display_all_contacts'])) {
				$contacts = '';
			}

			if ( $is_mobile === false ) {
                if($classification == '') {
                    echo display_filter_param('contacts.php?category='.$category);
                } else {
                    echo display_filter_param('contacts.php?category='.$category.'&classification='.$classification);
                }
            }

			//Exclude George & FFM from showing up on SEA Contacts
			if($rookconnect == 'sea') {
				$sea_constraint = " AND (user_name!='FFMAdmin' AND user_name!='georgev' AND user_name!='salimc' OR user_name IS NULL) ";
			} else {
				$sea_constraint = '';
			}

			include_once('contacts_search_function.php');
			if(isset($_GET['filter'])) { $url_search = $_GET['filter']; } else { $url_search = ''; }
			if($contacts != '') {
				$id_list = search_contacts_table($dbc, $contacts, $sea_constraint." AND `category` LIKE '$category'");
				$query_check_credentials = "SELECT * FROM contacts WHERE `contactid` IN ($id_list)";
				$query = "SELECT count(*) as numrows FROM contacts WHERE `contactid` IN ($id_list)";

				/* Pagination Counting */
				$rowsPerPage = mysqli_fetch_array(mysqli_query($dbc,$query))['numrows'];
				$pageNum = 1;
				$offset = 0;
			} else {
				$classification = '';
				if(!empty($_GET['classification'])) {
					$classification = $_GET['classification'];
				}

				$sitebusinessid = '';
				if(!empty($_GET['sitebusinessid'])) {
					$sitebusinessid = $_GET['sitebusinessid'];
				}

                $customersiteid = '';
				if(!empty($_GET['customersiteid'])) {
					$customersiteid = $_GET['customersiteid'];
				}

				/* Pagination Counting */
				$rowsPerPage = 25;
				$pageNum = 1;

				if(isset($_GET['page'])) {
					$pageNum = $_GET['page'];
				}

				$offset = ($pageNum - 1) * $rowsPerPage;

				$search = '';
				if(!empty($_GET['businessid'])) {
					$search = " AND `businessid` = '".$_GET['businessid']."' ";
				}

				if($region != '') {
					$query_check_credentials = "SELECT * FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE '$category' AND region='$region' $search $sea_constraint";
					$query = "SELECT count(*) as numrows FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE '$category' AND region='$region' $search $sea_constraint";
				} else if($classification != '') {
					$query_check_credentials = "SELECT * FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE '$category' AND classification='$classification' $search $sea_constraint";
					$query = "SELECT count(*) as numrows FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE '$category' AND classification='$classification' $search $sea_constraint";
				} else if($customersiteid != '') {
					$query_check_credentials = "SELECT * FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE '$category' AND siteid='$customersiteid' $search $sea_constraint";
 					$query = "SELECT count(*) as numrows FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE '$category' AND siteid='$customersiteid' $search $sea_constraint";
                } else if($sitebusinessid != '') {
					$query_check_credentials = "SELECT * FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE '$category' AND businessid='$sitebusinessid' $search $sea_constraint";
 					$query = "SELECT count(*) as numrows FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE '$category' AND businessid='$sitebusinessid' $search $sea_constraint";
                } else if($url_search == 'Top') {
					if ( $is_mobile === true ) { $mobile_view = true; }
					if($category == 'Patient') {
						$status = ($_GET['category'] == 'Inactive Patient' ? 0 : 1);
						$query_check_credentials = "SELECT * FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE 'Patient'  and status = $status $search $sea_constraint";
						$query = "SELECT count(*) as numrows FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE 'Patient'  and status = $status $search $sea_constraint";
					}
					else {
						$query_check_credentials = "SELECT * FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE '$category' $search $sea_constraint";
						$query = "SELECT count(*) as numrows FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted=0 AND category LIKE '$category' $search $sea_constraint";
					}
				} else if($url_search == 'All') {
					if($category == 'Patient') {
						$status = ($_GET['category'] == 'Inactive Patient' ? 0 : 1);
						$query_check_credentials = "SELECT * FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted = 0 AND category LIKE 'Patient' and status = $status $search $sea_constraint";
						$query = "SELECT count(*) as numrows FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted = 0 AND category LIKE 'Patient' and status = $status $search $sea_constraint";
					}
					else {
						$query_check_credentials = "SELECT * FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted = 0 AND category LIKE '$category' $search $sea_constraint";
						$query = "SELECT count(*) as numrows FROM contacts WHERE tile_name = '".FOLDER_NAME."' AND deleted = 0 AND category LIKE '$category' $search $sea_constraint";
					}
				} else if($url_search == 'Business') {
					$businessid = $_GET['businessid'];
				} else {
					$id_list = search_contacts_table($dbc, $url_search, $sea_constraint.$search." AND `category` LIKE '$category'", 'START');
					$query_check_credentials = "SELECT * FROM contacts WHERE `contactid` IN ($id_list)";
					$query = "SELECT count(*) as numrows FROM contacts WHERE `contactid` IN ($id_list)";
				}
			}
			$results = [];

			if(!isset($_GET['sortby'])) {
				$query_check_credentials .= ' ORDER BY `contactid`';
			}

			$rows = mysqli_fetch_array(mysqli_query($dbc,$query))['numrows'];
			if($rows > 2500) {
				$results[] = mysqli_query($dbc, $query_check_credentials.' LIMIT '.$offset.', '.($rowsPerPage * $pageNum));
			} else {
				for($i = 0; $i * 1000 < $rows; $i++) {
					$results[] = mysqli_query($dbc, $query_check_credentials.' LIMIT '.($i * 1000).', 1000');
				}
			}

			if ( $mobile_view === true ) { $num_rows = 0; }

			if($rows > 0) {

				if($category == 'Top' || $category == '%') {
					$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts_dashboard FROM field_config_contacts WHERE tile_name = '".FOLDER_NAME."' AND contacts_dashboard IS NOT NULL"));
					$value_config = ','.$get_field_config['contacts_dashboard'].',';
				} else {
					$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts_dashboard FROM field_config_contacts WHERE tile_name = '".FOLDER_NAME."' AND tab='$category' AND accordion IS NULL"));
					$value_config = ','.$get_field_config['contacts_dashboard'].',';
				}

				// Added Pagination //
				echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
				// Pagination Finish //

				echo "<table class='table table-bordered'>";
				echo "<tr class='hidden-xs hidden-sm'>";
				include ('contacts_header.php');
				echo '<th>Function</th>';
				echo "</tr>";

				$contact_list = [];
				$contact_sort = [];
				foreach($results as $result) {
					$contact_list = array_merge($contact_list, mysqli_fetch_all($result, MYSQLI_ASSOC));
				}
				if($rows > 2500) {
					$contact_sort = array_column($contact_list, 'contactid');
				} else {
					if(isset($_GET['sortby']))
						$contact_sort = array_splice(sort_contacts_array($contact_list, $_GET['sortby']), $offset, ($rowsPerPage * $pageNum));
					else
						$contact_sort = array_splice(sort_contacts_array($contact_list), $offset, ($rowsPerPage * $pageNum));
				}

				$i = 0;
				foreach($contact_sort as $sort => $id)
				{
					$row = $contact_list[array_search($id, array_column($contact_list,'contactid'))];
					$style = '';
					if (strpos($value_config, ','."Rating".',') !== FALSE) {
						$rating = $row['rating'];

						if ($rating == 'Bronze') {
							$style = 'style = "background-color:#9B886C"';
						}
						if ($rating == 'Silver') {
							$style = 'style = "background-color:silver"';
						}
						if ($rating == 'Gold') {
							$style = 'style = "background-color:#D1B85F"';
						}
						if ($rating == 'Platinum') {
							$style = 'style = "background-color:#ABA9AC"';
						}
						if ($rating == 'Diamond') {
							$style = 'style = "background-color:#b9f2ff"';
						}
						if ($rating == 'Green') {
							$style = 'style = "background-color:#228B22"';
						}
						if ($rating == 'Yellow') {
							$style = 'style = "background-color:#ffff00"';
						}
						if ($rating == 'Light blue') {
							$style = 'style = "background-color:#ADD8E6"';
						}
						if ($rating == 'Dark blue') {
							$style = 'style = "background-color:#1E90FF"';
						}
						if ($rating == 'Red') {
							$style = 'style = "background-color:#ff0000"';
						}
						if ($rating == 'Pink') {
							$style = 'style = "background-color:#FF69B4"';
						}
						if ($rating == 'Purple') {
							$style = 'style = "background-color:#BF00FE"';
						}
					}
					echo '<tr '.$style.'>';

					include ('contacts_data.php');

					echo '<td data-title="Function">';
					$query_check_credentialsx = "SELECT * FROM point_of_sell WHERE deleted = 0 AND contactid='".$row['contactid']."'";
					$resultx = mysqli_query($dbc, $query_check_credentialsx);
					$num_rowsx = mysqli_num_rows($resultx);
					if(vuaed_visible_function($dbc, 'contacts') == 1) {
                        /* Go to Overview sub tab if the Contact is a Patient */
                        if ( $category=='Patient' || $category=='Patients' ) {
                            echo '<a href=\'view_profile.php?category='.$category.'&contactid='.$row['contactid'].'\'>Edit</a> | ';
                        } else {
                            echo '<a href=\'add_contacts.php?category='.$category.'&contactid='.$row['contactid'].'\'>Edit</a> | ';
                        }
                        echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&contactid='.$row['contactid'].'&category='.$row['category'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a>';
                        if($num_rowsx > 0) {
                            echo ' | <a href="../Point of Sale/point_of_sell.php?contact_view_invoice='.$row['contactid'].'">View Invoices ('.$num_rowsx.')</a>';
                        }
					}
					echo '</td>';

					echo "</tr>";
					$i++;
				}

				echo '</table>';
				// Added Pagination //
				if(isset($query))
				echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
				// Pagination Finish //

			} else {
				echo "<h2>No Record Found.</h2>";
			}

			if ( $is_mobile === false ) {
                if($classification == '') {
                    echo display_filter_param('contacts.php?category='.$category);
                } else {
                    echo display_filter_param('contacts.php?category='.$category.'&classification='.$classification);
                }
				echo "<div class='clearfix'><div>";
            }

			if ( vuaed_visible_function ( $dbc, 'contacts' ) == 1 ) { ?>
				<div class="col-sm-12 col-xs-12 col-lg-4 pad-top offset-xs-top-20 pull-right">
					<a href="add_contacts.php?category=<?= $category; ?>" class="btn brand-btn mobile-block gap-bottom pull-right">Add <?= $main_label; ?></a>
					<span class="popover-examples list-inline"><a class="pull-right" style="margin:7px 5px 0 15px;" data-toggle="tooltip" data-placement="top" title="<?= $info_add_contact; ?>"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>

				</div><?php
			}

			?>

		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>
