<?php $select_staff = $_GET['select_staff'];
$select_date = $_GET['select_date'];
$form_id = $_GET['form_id'];
$reviewid = $_GET['reviewid'];
$from_url = '../HR/index.php?performance_review=list';
?>
<script type="text/javascript">
$(document).ready(function() {
    $('.control-label').removeClass('control-label');
});
$(document).on('change', 'select[name="pr_form"]', function() { changeForm(this); });
function changeForm(sel) {
	window.location.href = '?performance_review=add&form_id='+sel.value;
}
</script>
<?php if($user_form_layout != 'Sidebar') { ?>
<div class='scale-to-fill has-main-screen background-white'>
	<div class='main-screen form-horizontal'>
        <div class="standard-body-title">
            <h3>Performance Review</h3>
        </div>
		<div class="standard-body-content col-sm-12">
<?php } ?>
			<?php if(empty($form_id)) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Select a Form:</label>
					<div class="col-sm-8">
						<select name="pr_form" class="chosen-select-deselect form-control">
							<option></option>
							<?php $pr_forms = implode(',',array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_performance_reviews` WHERE `enabled` = 1 AND (CONCAT(',',`limit_staff`,',') LIKE '%,".$_SESSION['contactid'].",%' OR IFNULL(`limit_staff`,'') = '')"),MYSQLI_ASSOC),'user_form_id'));
							$pr_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id` IN ($pr_forms) AND `deleted` = 0 ORDER BY `name`"),MYSQLI_ASSOC);
							foreach ($pr_forms as $pr_form) {
								echo '<option value="'.$pr_form['form_id'].'" '.($form_id == $pr_form['form_id'] ? 'selected' : '').'>'.$pr_form['name'].'</option>'; 
							} ?>
						</select>
					</div>
				</div>
			<?php } else { ?>
				<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
					<input type="hidden" name="reviewid" value="<?= $reviewid ?>">
					<?php include('../HR/user_forms_pr.php'); ?>
		            <div class="form-group">
		                <div class="col-sm-6">
		                    <a href="<?= $from_url ?>" class="btn brand-btn btn-lg">Back</a>
						</div>
						<div class="col-sm-6">
							<button type="submit" name="submit_pr_form" value="submit_pr_form" class="btn brand-btn btn-lg pull-right">Submit</button>
						</div>
					</div>
			<?php } ?>
<?php if($user_form_layout != 'Sidebar') { ?>
		</div>
	</div>
</div>
<?php } ?>