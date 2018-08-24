<?php include_once('../include.php');
checkAuthorised('intake'); ?>
<script type="text/javascript">
function generateLink(link) {
	$(link).closest('td').find('.external_link_div').html('Generating...');
	var intakeformid = $(link).closest('td').data('id');
	$.ajax({
		url: '../Intake/intake_ajax_all.php?fill=generateLink&intakeformid='+intakeformid,
		type: 'GET',
		dataType: 'html',
		success: function(response) {
			$(link).closest('td').find('.external_link_div').html(response);
			$(link).closest('td').find('.remove_link').show();
			$(link).closest('td').find('.generate_link').hide();
		}
	});
}
function removeLink(link) {
	if(confirm('Are you sure you want to remove this link?')) {
		var intakeformid = $(link).closest('td').data('id');
		$.ajax({
			url: '../Intake/intake_ajax_all.php?fill=removeLink&intakeformid='+intakeformid,
			type: 'GET',
			dataType: 'html',
			success: function(response) {
				$(link).closest('td').find('.external_link_div').html('');
				$(link).closest('td').find('.remove_link').hide();
				$(link).closest('td').find('.generate_link').show();
			}
		});
	}
}
</script>
<div id="no-more-tables"><?php
	// Search
	$search_term = '';
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_submit']) ) {
		$search_term = ( !empty ($_POST['search_term']) ) ? filter_var ($_POST['search_term'], FILTER_SANITIZE_STRING) : '';
	} else {
		$search_term = '';
	}

	/* Pagination Counting */
	$rowsPerPage = 25;
	$pageNum = 1;

	if ( isset($_GET['page']) ) {
		$pageNum = $_GET['page'];
	}

	$offset = ($pageNum - 1) * $rowsPerPage;

	if(!empty($_GET['cat'])) {
		$form_categories = explode('*#*', get_config($dbc, 'intake_software_tabs'));
		$intake_cat = $_GET['cat'];
		if($intake_cat == '(Uncategorized)') {
			$cat_query = " AND `category` NOT IN ('".implode("','", $form_categories)."')";
		} else {
			$cat_query = " AND `category` = '$intake_cat'";
		}
	}

	if ( $search_term == '' ) {
		$query_check_credentials = "SELECT * FROM `intake_forms` WHERE `deleted` = 0 $cat_query LIMIT $offset, $rowsPerPage";
		$query = "SELECT COUNT(*) AS numrows FROM `intake_forms` WHERE `deleted` = 0 $cat_query";
	} else {
		$query_check_credentials = "SELECT * FROM `intake_forms` WHERE `form_name` LIKE '%{$search_term}%' AND `deleted` = 0 $cat_query LIMIT $offset, $rowsPerPage";
		$query = "SELECT COUNT(*) AS numrows FROM `intake_forms` WHERE `form_name` LIKE '%{$search_term}%' AND `deleted` = 0 $cat_query";
	}

	$result		= mysqli_query($dbc, $query_check_credentials);
	$num_rows	= ($result) ? mysqli_num_rows($result) : 0;

	if ( $num_rows > 0 ) {

		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage); ?>

		<table class="table table-bordered table-striped">
            <thead>
                <tr class="hidden-xs hidden-sm">
                    <th>Form Name</th>
                    <th>External Link</th>
                    <th>Expiry Date</th>
                    <th>Function</th>
                </tr>
            </thead>
			<?php while ($row = mysqli_fetch_array($result)) { ?>
				<tr>
					<td data-title="Form Name"><?= $row['form_name'] ?></td>
					<td data-title="External Link" data-id="<?= $row['intakeformid'] ?>">
						<div class="external_link_div">
							<?php if(!empty($row['access_code'])) {
								echo '<a href="'.WEBSITE_URL.'/Intake/add_form.php?formid='.$row['intakeformid'].'&access_code='.$row['access_code'].'" target="_blank">'.WEBSITE_URL.'/Intake/add_form.php?formid='.$row['intakeformid'].'&access_code='.$row['access_code'].'</a>';
							} ?>
						</div>
						<?php if(config_visible_function($dbc, 'intake') == 1) { ?>
							<a href="" onclick="removeLink(this); return false;" <?= empty($row['access_code']) ? 'style="display: none;"' : '' ?> class="remove_link">Remove Link</a>
							<a href="" onclick="generateLink(this); return false;" <?= !empty($row['access_code']) ? 'style="display: none;"' : '' ?> class="generate_link">Generate Link</a>
						<?php } ?>
					</td>
					<td data-title="Expiry Date"><?= $row['expiry_date'] ?></td>
					<td data-title="Function">
						<a href="email_form.php?intakeformid=<?= $row['intakeformid'] ?>">Email Form</a><br />
						<?php if(vuaed_visible_function($dbc, 'intake') == 1) { ?>
							<a href="add_form.php?formid=<?= $row['intakeformid'] ?>">Add Form</a><br />
						<?php } ?>
						<?php if(config_visible_function($dbc, 'intake') == 1) { ?>
							<a href="add_intake.php?edit=<?= $row['intakeformid'] ?>">Edit Settings</a><br /><a href="../delete_restore.php?action=delete&intakeformid=<?= $row['intakeformid'] ?>" onclick="return confirm('Are you sure you want to delete this form?');">Delete</a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</table>

		<?php echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

	} else {
		echo '<h2>No Records Found.</h2>';
	} ?>

</div><!-- #no-more-tables -->