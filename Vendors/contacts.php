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
checkAuthorised('vendors');
	error_reporting(0);

	$detect			= new Mobile_Detect;
	$is_mobile		= ( $detect->isMobile() ) ? true : false;
	$mobile_view	= false;

	/* Get logged in user's role */
	if ( !empty ( $_GET[ 'level' ] ) ) {
		$level_url = $_GET[ 'level' ];

	} else {
		$contacterid = $_SESSION['contactid'];
		$result	= mysqli_query ( $dbc, "SELECT * FROM contacts WHERE contactid='$contacterid'" );

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
			<?= VENDOR_TILE ?> Dashboard<?php
			if(config_visible_function($dbc, 'contacts') == 1) {
				echo '<a href="field_config_contacts.php?category='. $_GET[ 'category' ] .'&type=tab" class="mobile-block pull-right"><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right gap-top" style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
			} ?>
		</h1>

        <div class="tab-container gap-left">
            <div class="pull-left tab"><button class="btn brand-btn mobile-100 mobile-block active_tab" type="button"><?= VENDOR_TILE ?></button></div>
        </div>

        <div class="clearfix gap-top"></div>

		<form name="form_search" method="post" action="" class="form-inline" role="form">

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
			</div><?php

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
					<a href="add_contacts.php?category=Vendor" class="btn brand-btn mobile-block gap-bottom pull-right">Add <?= VENDOR_TILE ?></a>
					<span class="popover-examples list-inline"><a class="pull-right" style="margin:7px 5px 0 15px;" data-toggle="tooltip" data-placement="top" title="Click to add a new Vendor."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
                        if ( $impexp_or_not == 'true' ) { ?>
                            <a href="add_contacts_multiple.php?category=<?= $category; ?>" class="btn brand-btn mobile-block gap-bottom pull-right">Import/Export</a>
                            <span class="popover-examples list-inline"><a class="pull-right" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click to add multiple contacts at once, edit multiple contacts, or export a list of all of your contacts."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
                        } ?>
				</div><?php
			} ?>

            <div class="clearfix"></div>

            <div id="no-more-tables" class="triple-pad-top contacts-list"><?php
                // Display Pager
                $contacts = '';
                $category = 'Vendor';
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

                include_once('contacts_search_function.php');

                $url_search = ( isset($_GET['filter']) ) ? $_GET['filter'] : '';

                if ( $contacts != '' ) {
                    $id_list = search_contacts_table($dbc, $contacts, $sea_constraint." AND `category` LIKE '$category'");
                    $query_check_credentials = "SELECT * FROM `contacts` WHERE `contactid` IN ($id_list)";
                    $query = "SELECT COUNT(*) AS `numrows` FROM `contacts` WHERE `contactid` IN ($id_list)";

                    /* Pagination Counting */
                    $rowsPerPage = mysqli_fetch_array(mysqli_query($dbc,$query))['numrows'];
                    $pageNum = 1;
                    $offset = 0;

                } else {
                    /* Pagination Counting */
                    $rowsPerPage = 25;
                    $pageNum = 1;

                    if(isset($_GET['page'])) {
                        $pageNum = $_GET['page'];
                    }

                    $offset = ($pageNum - 1) * $rowsPerPage;

                    $search = '';

                    if ( $url_search == 'Top' || $url_search == 'All' ) {
                        if ( $is_mobile === true ) { $mobile_view = true; }

                        $query_check_credentials = "SELECT * FROM `contacts` WHERE `deleted`=0 AND `category` LIKE '$category' $search $sea_constraint";
                        $query = "SELECT COUNT(*) AS `numrows` FROM `contacts` WHERE `deleted`=0 AND `category` LIKE '$category' $search $sea_constraint";
                    } else {
                        $id_list = search_contacts_table($dbc, $url_search, $sea_constraint.$search." AND `category` LIKE '$category'", 'START');
                        $query_check_credentials = "SELECT * FROM `contacts` WHERE `contactid` IN ($id_list)";
                        $query = "SELECT COUNT(*) AS `numrows` FROM `contacts` WHERE `contactid` IN ($id_list)";
                    }
                }

                $results = [];

                if ( !isset($_GET['sortby']) ) {
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
                        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts_dashboard` FROM `field_config_contacts` WHERE tab='$category' AND `contacts_dashboard` IS NOT NULL"));
                        $value_config = ','.$get_field_config['contacts_dashboard'].',';
                    } else {
                        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts_dashboard` FROM `field_config_contacts` WHERE tab='$category' AND `accordion` IS NULL"));
                        $value_config = ','.$get_field_config['contacts_dashboard'].',';
                    }

                    // Added Pagination //
                    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

                    echo '<table class="table table-bordered">';
                        echo '<tr class="hidden-xs hidden-sm">';
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
                        foreach($contact_sort as $sort => $id) {
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
                                    $query_check_credentialsx = "SELECT * FROM `purchase_orders` WHERE `deleted`=0 AND `contactid`='".$row['contactid']."'";
                                    $resultx = mysqli_query($dbc, $query_check_credentialsx);
                                    $num_rowsx = mysqli_num_rows($resultx);
                                    if ( vuaed_visible_function($dbc, 'contacts') == 1 ) {
                                        echo '<a href=\'add_contacts.php?category='.$category.'&contactid='.$row['contactid'].'\'>Edit</a> | ';
                                    }
                                    echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&contactid='.$row['contactid'].'&category='.$row['category'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a>';
                                    if($num_rowsx > 0) {
                                        echo ' | <a href="../Point of Sale/point_of_sell.php?contact_view_invoice='.$row['contactid'].'">View Invoices ('.$num_rowsx.')</a>';
                                    }
                                echo '</td>';

                            echo "</tr>";
                            $i++;
                        }

                    echo '</table>';
                    // Added Pagination //
                    if(isset($query))
                        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

                } else {
                    echo "<h2>No Record Found.</h2>";
                }

                if ( vuaed_visible_function ( $dbc, 'contacts' ) == 1 ) { ?>
                    <div class="col-sm-12 col-xs-12 col-lg-4 pad-top offset-xs-top-20 pull-right">
                        <a href="add_contacts.php?category=Vendor" class="btn brand-btn mobile-block gap-bottom pull-right">Add Vendor</a>
                        <span class="popover-examples list-inline"><a class="pull-right" style="margin:7px 5px 0 15px;" data-toggle="tooltip" data-placement="top" title="Click to add a new Vendor."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>

                    </div><?php
                } ?>

                <div class="clearfix"></div>
            </div><!-- #no-more-tables -->
        </form>

	</div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>
