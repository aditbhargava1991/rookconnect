<?php $field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_user_forms`"));
$subtab_list = $field_config['subtabs'];
$use_templates = $field_config['use_templates'];
if(!empty($subtab_list) || $use_templates == 1) {
	echo '<div class="tab gap-bottom">';
	$subtab = $_GET['subtab'];
	$type = $_GET['type'];
	if(!empty($subtab)) {
		$subtab_query = " AND `subtab` = '$subtab'";
	}
	echo '<a href="?tab=form_list&subtab=" class="btn brand-btn'.(empty($subtab) && $type != 'templates' ? ' active_tab' : '').'">All Forms</a>';
	$subtab_list = explode(',', $subtab_list);
	foreach ($subtab_list as $subtab_btn) {
		echo '<a href="?tab=form_list&subtab='.$subtab_btn.'" class="btn brand-btn'.($subtab == $subtab_btn ? ' active_tab' : '').'">'.$subtab_btn.'</a>';
	}
	if($use_templates == 1) {
		echo '<a href="?tab=form_list&type=templates" class="btn brand-btn pull-right'.($type == 'templates' ? ' active_tab' : '').'">Templates</a>';
	}
	echo '</div>';
}
if(!empty($type)) {
	$type_query = " AND `is_template` = 1";
} else {
	$type_query = " AND `is_template` = 0";
}
?>

<?php if($edit_access == 1) { ?>
	<a href="edit_form.php?edit=<?= $type == 'templates' ? '&type=template' : '' ?>" class="btn brand-btn mobile-block pull-right">Add New <?= $type == 'templates' ? 'Template' : 'Form' ?></a>
	<span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new <?= $type == 'templates' ? 'Template' : 'Form' ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>
<?php }

$rowsPerPage = 25;
$pageNum = (empty($_GET['page']) ? 1 : $_GET['page']);
$offset = ($pageNum - 1) * $rowsPerPage;

$form_list = mysqli_query($dbc, "SELECT `form_id`, `name`, `assigned_tile` FROM `user_forms` WHERE `deleted`=0 $subtab_query $type_query ORDER BY `name`, `created_date` LIMIT $offset, $rowsPerPage");
if(mysqli_num_rows($form_list) > 0) {
	$form_count = "SELECT COUNT(*) numrows FROM `user_forms` WHERE `deleted`=0 $subtab_query $type_query";
	echo display_pagination($dbc, $form_count, $pageNum, $rowsPerPage); ?>
	<table class="table table-bordered table-striped">
        <thead>
            <tr class="hidden-sm hidden-xs">
                <th>Name</th>
                <?php if ($type != 'templates') { ?>
                    <th>Assigned Tiles</th>
                <?php } ?>
                <!-- <th>History</th> -->
                <th>Function</th>
            </tr>
        </thead>
		<?php while($form = mysqli_fetch_array($form_list)) { ?>
			<tr>
				<td data-title="Form Name"><?= $form['name'] ?></td>
				<?php if ($type != 'templates') { ?>
					<td data-title="Assigned Tiles">
						<?php $tile_list = ['contracts' => 'Contracts', 'hr' => 'HR', 'infogathering' => 'Information Gathering', 'safety'=> 'Safety', 'treatment' => 'Treatment Charts', 'intake' => 'Intake Forms', 'performance_review' => 'Performance Reviews', 'project' => 'Projects', 'attach_contact' => 'Attach to Contact', 'incident_report' => INC_REP_TILE];
						$assigned_tiles = explode(',', $form['assigned_tile']);
						$tile_text = '';
						foreach ($assigned_tiles as $assigned_tile) {
							$tile_text .= $tile_list[$assigned_tile].', ';
						}
						$tile_text = rtrim($tile_text, ', ');
						echo $tile_text; ?>
					</td>
				<?php } ?>
				<!-- <td data-title="History"><a href="" onclick="show_history(<?= $form['form_id'] ?>); return false;">View All</a></td> -->
				<td data-title="Function">
				<?php if ($type != 'templates') { ?>
					<a href="?tab=generate_form&id=<?= $form['form_id'] ?>">Complete</a> | <a href="?tab=assign_form&id=<?= $form['form_id'] ?>">Assign</a> | <a data-id="<?= $form['form_id'] ?>" href="" onclick="export_printable_pdf(this); return false">Printable PDF</a>
				<?php } ?>
				<?= ($edit_access == 1 ? ($type != 'templates' ? '| ' : '')."<a href='edit_form.php?edit={$form['form_id']}'>Edit</a> | <a href='' data-id='{$form['form_id']}' onclick='return copy_form(this);'>Copy</a> | <a href='' data-id='{$form['form_id']}' onclick='return archive_form(this);'>Archive</a>" : '') ?>
				</td>
			</tr>
		<?php } ?>
	</table>
	<?php echo display_pagination($dbc, $form_count, $pageNum, $rowsPerPage);
} else { ?>
	<h2>No <?= $type == 'templates' ? 'Templates' : 'Forms' ?> Found</h2>
<?php } ?>
<?php if($edit_access == 1) { ?>
	<a href="edit_form.php?edit=<?= $type == 'templates' ? '&type=template' : '' ?>" class="btn brand-btn mobile-block pull-right">Add New <?= $type == 'templates' ? 'Template' : 'Form' ?></a>
	<span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new <?= $type == 'templates' ? 'Template' : 'Form' ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>
	<script>
	function archive_form(btn) {
		if(confirm("Are you sure you want to archive this form?")) {
			$.ajax({
				method: 'POST',
				url: 'form_ajax.php?action=archive',
				data: { form: $(btn).data('id') },
				dataType: 'html',
				success: function(response) {
					$(btn).closest('tr').hide();
				}
			});
		}
		return false;
	}
	function copy_form(btn) {
		if(confirm("Are you sure you want to copy this form?")) {
			$.ajax({
				method: 'POST',
				url: 'form_ajax.php?fill=copy_form',
				data: { form: $(btn).data('id') },
				dataType: 'html',
				success: function(response) {
					window.location.href = '<?= WEBSITE_URL ?>/Form Builder/edit_form.php?edit='+response;
				}
			});
		}
		return false;
	}
	function show_history(id) {
		console.log(id);
	}
	function export_printable_pdf(a) {
		$(a).text('Generating PDF...');
		form_id = $(a).data('id');
		$.ajax({
			method: 'POST',
			url: '../Form Builder/generate_form.php',
			data: { complete_form: 'complete_form', form_id: form_id, printable_pdf: 'true' },
			success: function(response) {
				window.location.href = response;
				$(a).text('Printable PDF');
			}
		});
	}
	</script>
<?php } ?>