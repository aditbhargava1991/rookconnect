<?php
	/*
	 * Add/Edit Software Guide
	 */

	include ('../include.php');
    include ('check_security.php');
    include ('../database_connection_htg.php');
    $force_load_list = true;
    include ('../tile_list.php');
	error_reporting(0);
    $type_url = ( isset($_GET['type']) ) ? trim($_GET['type']) : '';

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		$guideid		= $_POST['guideid'];
		$noteid		    = $_POST['noteid'];
		$type_url       = filter_var ( $_POST['type_url'], FILTER_SANITIZE_STRING );
        $tile			= filter_var ( $_POST['tile'], FILTER_SANITIZE_STRING );
        $note_subtab    = filter_var ( $_POST['note_subtab'], FILTER_SANITIZE_STRING );
        $type           = filter_var ( $_POST['type'], FILTER_SANITIZE_STRING );
		$subtab_input	= filter_var ( $_POST['subtab_input'], FILTER_SANITIZE_STRING );
		$subtab_new		= filter_var ( $_POST['subtab_new'], FILTER_SANITIZE_STRING );
		$subtab_select	= $_POST['subtab_select'];
		$sort_order		= ( !empty ( $_POST['sort_order'] ) ) ?  $_POST['sort_order'] : '0';
		$description	= filter_var ( htmlentities ( $_POST['description'] ), FILTER_SANITIZE_STRING );
		$image			= '';

		if ( !empty($subtab_new) ) {
			$subtab = $subtab_new;
		} else if ( !empty($subtab_input) ) {
			$subtab = $subtab_input;
		} else if ( !empty($subtab_select) ) {
			$subtab = $subtab_select;
		}

		if ( !file_exists ('download') ) {
			mkdir( 'download', 0777, true );
		}

		if ( count ( $_FILES['image']['name'] ) > 0 ) {
			$image = htmlspecialchars($_FILES['image']['name'], ENT_QUOTES);
			move_uploaded_file ( $_FILES['image']['tmp_name'], 'download/' . $_FILES['image']['name'] );
		}

        $image_full_path = 'https://'.$_SERVER['HTTP_HOST'].'/How%20To%20Guide/download/'.$_FILES['image']['name'];

		if ( $type_url=='note' ) {
            if ( empty($noteid) ) {
                // New Note
                $query_insert	= "INSERT INTO `notes` (`tile`, `subtab`, `description`) VALUES ('$tile', '$note_subtab', '$description')";
                $result_insert	= mysqli_query ( $dbc_htg, $query_insert ) or die( mysqli_error($dbc_htg) );
                $noteid = mysqli_insert_id($dbc_htg);
                $url = 'added';

            } else {
                $query_update	= "UPDATE `notes` SET `tile`='$tile', `subtab`='$note_subtab', `description`='$description' WHERE `noteid`='$noteid'";
                $result_update	= mysqli_query ( $dbc_htg, $query_update );
                $url = 'updated';
            }

        } else {
            if ( empty($guideid) ) {
                // New Software Guide
                $query_insert	= "INSERT INTO `how_to_guide` (`tile`, `subtab`, `sort_order`, `description`, `image`, `image_full_path`) VALUES ('$tile', '$subtab', '$sort_order', '$description', '$image', '$image_full_path')";
                $result_insert	= mysqli_query ( $dbc_htg, $query_insert ) or die( mysqli_error($dbc_htg) );
                $guideid = mysqli_insert_id($dbc_htg);
                $url = 'added';

            } else {
                $query_update	= "UPDATE `how_to_guide` SET `tile`='$tile', `subtab`='$subtab', `sort_order`='$sort_order', `description`='$description', `image`='$image', `image_full_path` = '$image_full_path' WHERE `guideid`='$guideid'";
                $result_update	= mysqli_query ( $dbc_htg, $query_update );
                $url = 'updated';
            }
        }

		if(isset($_GET['maintype'])) {
			$submit_url = WEBSITE_URL . '/Manuals/manual.php?maintype=htg';
			echo '<script type="text/javascript">alert("Software Guide '. $url .'"); window.location.replace('.$submit_url.');</script>';
		}
		else {
			echo '<script type="text/javascript">alert("Software Guide '. $url .'"); window.location.replace("guides_dashboard.php");</script>';
		}
	} ?>

	<script type="text/javascript">
		$(document).ready(function() {
			$("#subtab_new_heading").hide();

			$("#form1").submit(function( event ) {
				var tile		= $("#tile").val();
				var subtab 		= $("#subtab").val();

				if ( tile=='' || subtab=='' ) {
					alert("Please make sure you have filled all the fields.");
					return false;
				}
			});

			$("#subtab_select").change(function() {
				if($("#subtab_select option:selected").text() == 'Add New Accordion Heading') {
					var tile = $("#tile").val();
					$("#subtab_new_heading").show();
					$.ajax({
						type: "GET",
						url: "guides_ajax_all.php?fill=get_sort_order&tile="+tile,
						dataType: "html",
						success: function(response){
							console.log(response);
							$("#sort_order").html(response);
							$("#sort_order").trigger("change.select2");
						}
					});
					tinyMCE.activeEditor.setContent('');
				} else {
					$("#subtab_new_heading").hide();
				}
			});

            <?php if ( !empty($type_url) ) { ?>
                $('#heading, #subtab_new_heading, #heading_order, #htg_image').hide();
            <?php } else { ?>
                $('#note_subtab').hide();
            <?php } ?>
		});
		$(document).on('change', 'select[name="tile"]', function() { selectTile(this); });
		$(document).on('change', 'select[name="subtab_select"]', function() { selectSubtab(this); });

		function selectTile(sel) {
			var tile = $('#tile').val();
			$.ajax({
				type: "GET",
				url: "guides_ajax_all.php?fill=get_subtabs&tile="+tile,
				dataType: "html",
				success: function(response){
					$("#subtab_select").html(response);
					$("#subtab_select").trigger("change.select2");
				}
			});
		}

		function selectSubtab(sel) {
			var guideid = $('#subtab_select').val();
			$.ajax({
				type: "GET",
				url: "guides_ajax_all.php?fill=get_content&guideid="+guideid,
				dataType: "html",
				success: function(response){
					var result = response.split('**');
					$("#sort_order").val(result[0]);
					$("#sort_order").trigger("change.select2");
					tinyMCE.activeEditor.setContent(result[1]);
				}
			});
		}
	</script>
</head>

<body>
	<?php
		include_once ('../navigation.php');
		checkAuthorised('how_to_guide');
	?>

	<div class="container">
		<div class="row">
			<h1><?php
                if ( empty($type_url) ) {
                    echo ( isset($_GET['guideid']) ) ? 'Edit' : 'Add' ?> Software Guide<?php
                } else {
                    echo ( isset($_GET['noteid']) ) ? 'Edit' : 'Add' ?> Note<?php
                } ?>
            </h1>
			<?php if(isset($_GET['maintype'])): ?>
				<div class="gap-left gap-top double-gap-bottom"><a href="<?php WEBSITE_URL ?>/Manuals/manual.php?maintype=htg" class="btn config-btn">Back to Dashboard</a></div>
			<?php elseif ( $type_url=='note' ): ?>
                <div class="gap-left gap-top double-gap-bottom"><a href="notes.php" class="btn config-btn">Back to Dashboard</a></div>
            <?php else: ?>
				<div class="gap-left gap-top double-gap-bottom"><a href="guides_dashboard.php" class="btn config-btn">Back to Dashboard</a></div>
			<?php endif; ?>
			<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
				if ( isset ( $_GET['guideid']) ) {
					$guideid = preg_replace ( '/[^0-9]+/', '', trim($_GET['guideid']) );

					$row = mysqli_fetch_assoc ( mysqli_query ( $dbc_htg, "SELECT * FROM `how_to_guide` WHERE `guideid`='$guideid'"));

					$tile			= $row['tile'];
					$subtab			= $row['subtab'];
					$sort_order		= $row['sort_order'];
					$image			= $row['image'];
					$description	= $row['description']; ?>

					<input type="hidden" id="guideid" name="guideid" value="<?= $guideid; ?>" /><?php
				}
                if ( isset ( $_GET['noteid']) ) {
					$noteid = preg_replace ( '/[^0-9]+/', '', trim($_GET['noteid']) );

					$row = mysqli_fetch_assoc ( mysqli_query ( $dbc_htg, "SELECT * FROM `notes` WHERE `noteid`='$noteid'"));

					$tile			= $row['tile'];
					$subtab			= $row['subtab'];
					$description	= $row['description']; ?>

					<input type="hidden" id="noteid" name="noteid" value="<?= $noteid; ?>" /><?php
				} ?>

                <input type="hidden" id="type_url" name="type_url" value="<?= $type_url; ?>" />

				<div class="form-group">
					<label for="tile" class="col-sm-4 control-label">
						<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Select the tile you want to add the Software Guide to."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Tile: <span class="hp-red">*</span>
					</label>
					<div class="col-sm-8">
                        <select data-placeholder="Choose the Tile..." name="tile" id="tile" class="chosen-select-deselect form-control" width="380">
							<option value=""></option><?php
                            $tiles = get_tile_names($all_tiles_list);
							foreach ( $tiles as $tile_name ) {
								$selected = ( $tile_name == $tile ) ? 'selected="selected"' : '';
								echo '<option value="'. $tile_name .'" '. $selected .'>'. $tile_name .'</option>';
							} ?>
						</select>
					</div>
				</div><!-- .form-group -->

				<div class="form-group" id="note_subtab">
					<label for="tile" class="col-sm-4 control-label">
						<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Enter the sub tab name on which this note should appear. Leave blank, if this is for the whole tile."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Sub Tab:
					</label>
					<div class="col-sm-8">
						<input type="text" name="note_subtab" class="form-control" value="<?php echo $subtab; ?>" />
					</div>
				</div><!-- .form-group -->

				<div class="form-group" id="heading">
					<label for="company_name" class="col-sm-4 control-label">
						<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Enter the accordion heading within the selected tile above which you want to add to the Software Guide."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Accordion Heading: <span class="hp-red">*</span>
					</label>
					<div class="col-sm-8"><?php
						if ( isset($_GET['guideid']) ) { ?>
							<input type="text" name="subtab_input" id="subtab" class="form-control" value="<?php echo $subtab; ?>" /><?php
						} else { ?>
							<select name="subtab_select" id="subtab_select" class="chosen-select-deselect form-control">
							</select><?php
						} ?>
					</div>
				</div><!-- .form-group -->

				<div class="form-group" id="subtab_new_heading">
					<label for="company_name" class="col-sm-4 control-label">
						<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Enter the accordion heading within the selected tile above which you want to add to the Software Guide."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						New Accordion Heading: <span class="hp-red">*</span>
					</label>
					<div class="col-sm-8">
						<input type="text" name="subtab_new" id="subtab_new" class="form-control" />
					</div>
				</div><!-- .form-group -->

				<div class="form-group" id="heading_order">
					<label for="company_name" class="col-sm-4 control-label">
						<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Select the accordion heading order."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Accordion Heading Order:
					</label>
					<div class="col-sm-8">
						<select name="sort_order" id="sort_order" class="chosen-select-deselect form-control">
							<option value=""></option><?php
							for ( $i=1; $i<=15; $i++ ) {
								$selected = ( $sort_order == $i ) ? 'selected="selected"' : ''; ?>
								<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option><?php
							} ?>
						</select>
					</div>
				</div><!-- .form-group -->

				<div class="form-group" id="htg_image">
					<label for="company_name" class="col-sm-4 control-label">
						<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Add an image/infographic you want to display on the Software Guide."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Image:
					</label>
					<div class="col-sm-8">
						<input type="file" name="image" id="image" class="form-control" data-filename-placement="inside" value="<?php echo $image; ?>" />
					</div>
				</div><!-- .form-group -->

				<div class="form-group">
					<label for="description" class="col-sm-4 control-label">
						<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Description of the Software Guide or Note."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Description:<?php if($type_url == 'note') { ?><br />
							<em>You can use the following text in your note:<br />
							[PROJECT TILE]: The name that the project tile is labelled as.<br />
							[PROJECT]: The name that the individual project is labelled as.<br />
							[AFTER_PROJECT]: The next step after a project has been created.<br />
							[TICKET TILE]: The name that the ticket tile is labelled as.<br />
							[TICKET]: The name that the individual ticket is labelled as.<br />
							[COMPANY]: The name of the company using the software.<br />
							[SOFTWARE URL]: The url of the software currently in use.<br />
							[BUSINESS]: The category to which items are attached.<br />
							[SITE]: The category between Businesses and Contacts.</em>
						<?php } ?>
					</label>
					<div class="col-sm-8">
						<textarea name="description" id="description" rows="10" cols="50" class="form-control"><?php echo $description; ?></textarea>
					</div>
				</div><!-- .form-group -->

				<?php if ($_GET['noteid'] > 0) {
					echo "<h4>Current Note</h4>";
					echo software_notes($tile, $subtab);
				} ?>

				<div class="form-group">
					<span class="hp-red"><em>Required Fields *</em></span>
				</div>

				<div class="form-group">
					<div class="col-sm-6">
						<?php if(isset($_GET['maintype'])): ?>
							<a href="<?php WEBSITE_URL ?>/Manuals/manual.php?maintype=htg" class="btn brand-btn btn-lg">Back</a>
						<?php elseif ($type_url=='note'): ?>
							<a href="notes.php" class="btn brand-btn btn-lg">Back</a>
						<?php else: ?>
							<a href="guides_dashboard.php" class="btn brand-btn btn-lg">Back</a>
						<?php endif; ?>
					</div>
					<div class="col-sm-6">
						<button type="submit" name="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
					</div>
				</div><!-- .form-group -->
			</form>

		</div><!-- .row -->
	</div><!-- .container -->

<?php include ('../footer.php'); ?>