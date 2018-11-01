<?php
/*
Dashboard
*/
include ('../include.php');
include_once('../Information Gathering/manual_reporting_functions.php');
checkAuthorised('infogathering');
error_reporting(0);
?>
</head>
<script type="text/javascript" src="infogathering.js"></script>
<body>

<?php include_once ('../navigation.php'); ?>

<div class="container">
    <div class="row hide_on_iframe">
        <div class="main-screen">

            <!-- Tile Header -->
            <div class="tile-header">
                <div class="col-xs-12 col-sm-4">
                    <h1>
                        <span class="pull-left" style="margin-top: -5px;"><a href="infogathering.php?tab=Form" class="default-color">Information Gathering</a></span>
                        <span class="clearfix"></span>
                    </h1>
                </div>
                <div class="col-xs-12 col-sm-8 text-right settings-block">
                    <?php if ( config_visible_function ( $dbc, 'profile' ) == 1 ) { ?>
                        <div class="pull-right gap-left top-settings">
                            <a href="field_config_infogathering.php" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
                        </div>
                        <a href="field_config_style.php" class="btn brand-btn pull-right">PDF Style</a>
                    <?php } ?>
                    <?php if ( check_subtab_persmission($dbc, 'infogathering', ROLE, 'reporting') === TRUE ) { ?>
                    <a href="manual_reporting.php?type=infogathering" class="btn brand-btn pull-right">Reporting</a>
                    <?php } ?>
                    <?php if(vuaed_visible_function($dbc, 'infogathering') == 1) { ?>
                        <a href="add_manual.php?type=infogathering" class="btn brand-btn pull-right">Add Information Gathering</a>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

            <div class="tile-container">
                <!-- Notice --><?php
                $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='ig_reporting'"));
                $note = $notes['note'];
                    
                if ( !empty($note) ) { ?>
                    <div class="notice double-gap-bottom popover-examples">
                        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                        <div class="col-sm-11">
                            <span class="notice-name">NOTE:</span>
                            <?= $note; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div><?php
                } ?>

                <div class="collapsible tile-sidebar set-section-height">
                    <?php include('tile_sidebar.php'); ?>
                        </div>

                <div class="fill-to-gap tile-content set-section-height" style="padding: 0;">
                    <div class="col-sm-12 main-screen-details">
                        <div class="sidebar" style="padding: 1em; margin: 0 auto; overflow-y: auto;">
                    		<form name="form_sites" method="post" action="" class="form-inline" role="form">
                            <?php
                            $contactid = '';
                            $category = '';
                            $heading = '';
                            $status = '';
                            $s_start_date = '';
                            $s_end_date = '';

                            if(!empty($_POST['contactid'])) {
                                $contactid = $_POST['contactid'];
                            }
                            if(!empty($_POST['category'])) {
                                $category = $_POST['category'];
                            }
                            if(!empty($_POST['heading'])) {
                                $heading = $_POST['heading'];
                            }
                            if(!empty($_POST['status'])) {
                                $status = $_POST['status'];
                            }
                            if(!empty($_POST['s_start_date'])) {
                                $s_start_date = $_POST['s_start_date'];
                            }
                            if(!empty($_POST['s_end_date'])) {
                                $s_end_date = $_POST['s_end_date'];
                            }
                            if (isset($_POST['display_all_asset'])) {
                                $contactid = '';
                                $category = '';
                                $heading = '';
                                $status = '';
                                $s_start_date = '';
                                $s_end_date = '';
                            }
                            ?>

                    		<div class="search-group">
                    			<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    					<div class="col-sm-4">
                    						<label class="control-label">Staff:</label>
                    					</div>
                    					<div class="col-sm-8">
                    						<select data-placeholder="Select a Staff Member..." name="contactid" class="chosen-select-deselect form-control" style="width:502px;">
                    						  <option value=""></option>
                    							<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND `status`>0 AND `show_hide_user`=1 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""),MYSQLI_ASSOC));
                    							foreach($staff_list as $id) {
                    								$name = get_contact($dbc, $id);
                    								echo "<option ".($contactid == $name ? 'selected' : '')." value='$name' >$name</option>";
                    							} ?>
                    						</select>
                    					</div>
                    				</div>

                    				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    					<div class="col-sm-4">
                    						<label class="control-label">Topic:</label>
                    					</div>
                    					<div class="col-sm-8">
                    						<select data-placeholder="Select a Topic (Sub Tab)..." name="category" class="chosen-select-deselect form-control" style="width:200px;">
                    						  <option value=""></option>
                    						  <?php
                    							$query = mysqli_query($dbc,"SELECT distinct(category) FROM infogathering WHERE deleted=0");
                    							while($row = mysqli_fetch_array($query)) {
                    								if ($category == $row['category']) {
                    									$selected = 'selected="selected"';
                    								} else {
                    									$selected = '';
                    								}
                    								?>
                    								<option <?php echo $selected; ?> value='<?php echo $row['category']; ?>' ><?php echo $row['category']; ?></option>
                    							<?php }
                    						  ?>
                    						</select>
                    					</div>
                    				</div>
                    			</div>
                    			<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    					<div class="col-sm-4">
                    						<label class="control-label">Heading:</label>
                    					</div>
                    					<div class="col-sm-8">
                    						<select data-placeholder="Select a Heading..." name="heading" class="chosen-select-deselect form-control" style="width:200px;">
                    						  <option value=""></option>
                    						  <?php
                    							$query = mysqli_query($dbc,"SELECT distinct(heading) FROM infogathering WHERE deleted=0");
                    							while($row = mysqli_fetch_array($query)) {
                    								if ($heading == $row['heading']) {
                    									$selected = 'selected="selected"';
                    								} else {
                    									$selected = '';
                    								}
                    								?>
                    								<option <?php echo $selected; ?> value='<?php echo $row['heading']; ?>' ><?php echo $row['heading']; ?></option>
                    							<?php }
                    						  ?>
                    						</select>
                    					</div>
                    				</div>

                    				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    					<div class="col-sm-4">
                    						<label class="control-label">Created Date:</label>
                    					</div>
                    					<div class="col-sm-8">
                    						<input name="s_start_date" type="text" class="datepicker form-control" value="<?php echo $s_start_date; ?>">
                    					</div>
                    				</div>
                    			</div>
                    			<div class="form-group col-lg-3 col-md-4 col-sm-12 col-xs-12">
                    				<a href="" class="btn brand-btn mobile-block pull-right">Display All</a>
                    				<button type="submit" name="reporting_client" value="Submit" class="btn brand-btn mobile-block pull-right">Search</button>
                    			</div>
                    		</div>

                    		<div class="clearfix"></div>

                            <br><br>

                            <?php

                            if(isset($_POST['reporting_client'])) {
                                $contactid = $_POST['contactid'];
                                $category = $_POST['category'];
                                $heading = $_POST['heading'];
                                $s_start_date = $_POST['s_start_date'];

                                $query_check_credentials = "SELECT m.*, ms.*  FROM infogathering_pdf ms, infogathering m WHERE m.deleted=0 AND m.infogatheringid = ms.infogatheringid AND (ms.created_by = '$contactid' OR m.category='$category' OR m.heading='$heading' OR ms.today_date='$s_start_date')";
                            } else if(empty($_GET['action'])) {
                                $query_check_credentials = "SELECT i.*, p.*, GROUP_CONCAT(p.fieldlevelriskid SEPARATOR ',') AS all_order  FROM infogathering i, infogathering_pdf p WHERE i.deleted=0 AND i.infogatheringid = p.infogatheringid GROUP BY p.infogatheringid, p.company ORDER BY p.infopdfid DESC";
                                //$query_check_credentials = "SELECT *, GROUP_CONCAT(fieldlevelriskid SEPARATOR ',') AS all_order  FROM infogathering_pdf GROUP BY infogatheringid,company ORDER BY infopdfid DESC";
                            }

                            $result = mysqli_query($dbc, $query_check_credentials);

                            $num_rows = mysqli_num_rows($result);
                            if($num_rows > 0) {
                                echo "<div id='no-more-tables'><table class='table table-bordered'>";
                                echo '<tr class="hidden-xs hidden-sm">
                                    <th>Topic (Sub Tab)</th>
                                    <th>Heading</th>
                                    <th>Sub Section Heading</th>
                                    <th>Business</th>
                                    <th>Created By</th>
                                    <th>Created Date</th>
                                    <th>Function</th>
                                    </tr>';
                            } else {
                                echo "<h2>No Record Found.</h2>";
                            }

                            while($row = mysqli_fetch_array( $result ))
                            {
                                $infogatheringid = $row['infogatheringid'];
                                $fieldlevelriskid = $row['fieldlevelriskid'];
                                $done = $row['done'];
                                $staffid = $row['staffid'];
                                $today = date('Y-m-d');
                                $color = '';
                                $signed_off = $row['today_date'];

                                if($row['done'] == 0) {
                                    $color = 'style="background-color: lightgreen;"';
                                }

                                echo "<tr>";
                                //echo '<td data-title="Contact Person">' . $row['assign_staff'] . '</td>';
                                echo '<td data-title="Category">' . $row['category'] . '</td>';
                                echo '<td data-title="Heading">' . $row['heading'] . '</td>';
                                echo '<td data-title="Sub Heading">' . $row['sub_heading'] . '</td>';

                    			if(mysqli_num_rows(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `name`='".$row['company']."'")) > 0) {
                    				echo '<td data-title="Company">' . decryptIt($row['company']) . '</td>';
                    			} else {
                    				echo '<td data-title="Company">' . $row['company'] . '</td>';
                    			}
                                echo '<td data-title="Created By">' . $row['created_by'] . '</td>';
                                echo '<td data-title="Created Date">' . $row['today_date'] . '</td>';
                                //echo '<td data-title="Code">' . $row['deadline'] . '</td>';

                                $each_tab = explode(',', $row['all_order']);
                                echo '<td data-title="Function">';
                                foreach ($each_tab as $cat_tab) {
                                    $pdf_path = infogathering_pdf($dbc, $infogatheringid, $cat_tab);
                                    $pdf = '<a target="_blank" href="'.$pdf_path.'"><img src="'.WEBSITE_URL.'/img/pdf.png" width="'.$img_width.'" height="'.$img_height.'" border="0" alt=""></a>';
                                    echo $pdf;
                                }

                                $edit_path = infogathering_edit($dbc, $infogatheringid, $cat_tab);
                                echo $edit_path;
                                echo '</td>';

                                echo "</tr>";
                            }
                            if($num_rows > 0) {
                                echo '</table></div>';
                            }
                            ?>

                            </form>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<?php include ('../footer.php'); ?>

<?php

function infogathering_edit($dbc, $infogatheringid, $fieldlevelriskid) {
    $form = get_infogathering($dbc, $infogatheringid, 'form');

    //if($form == 'Client Business Introduction') {
        $edit_path = '
        <a href ="add_manual.php?infogatheringid='.$infogatheringid.'&action=view&formid='.$fieldlevelriskid.'">Edit</a>';
        return $edit_path;
   // }

}
?>