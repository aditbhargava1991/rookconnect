<?php 
$filter_date = '';
if(isset($_GET['filter_id']) && $_GET['filter_id']=='all' && !isset($_POST['expense_min_date'])){
	$filter_date .= " and submit_date >= '".date('Y-m-01')."' and submit_date <= '".date('Y-m-t')."'";
}
if(!isset($_GET['filter_id']) && !isset($_POST['expense_min_date'])){
	$filter_date = " and submit_date >= '".date('Y-m-01')."' and submit_date <= '".date('Y-m-t')."'";
}
if($_POST['expense_min_date']!=""){
    $filter_date .= " and submit_date >= '".$_POST['expense_min_date']."' ";
}
if($_POST['expense_max_date']!=""){
    $filter_date .= " and submit_date <= '".$_POST['expense_max_date']."' ";
}
if($_POST['filter_save'] == 'filter') {
	$filter_staff = implode(',',$_POST['filter_staff']);
	$filter_min_date = filter_var($_POST['filter_min_date'],FILTER_SANITIZE_STRING);
	$filter_max_date = filter_var($_POST['filter_max_date'],FILTER_SANITIZE_STRING);
	$filter_status = filter_var($_POST['filter_status'],FILTER_SANITIZE_STRING);
	$filter_amt_min = filter_var($_POST['filter_amt_min'],FILTER_SANITIZE_STRING);
	$filter_amt_max = filter_var($_POST['filter_amt_max'],FILTER_SANITIZE_STRING);
	$filter_merchants = filter_var(implode(',',$_POST['filter_merchants']),FILTER_SANITIZE_STRING);
	$filter_category = filter_var(implode(',',$_POST['filter_category']),FILTER_SANITIZE_STRING);
	$filter_receipt = filter_var($_POST['filter_receipt'],FILTER_SANITIZE_STRING);
	$filter_description = filter_var($_POST['filter_description'],FILTER_SANITIZE_STRING);
	$filter_warnings = filter_var(implode(',',$_POST['filter_warnings']),FILTER_SANITIZE_STRING);
	$filter_name = filter_var($_POST['filter_name'],FILTER_SANITIZE_STRING);
	$before_change = "";
	if($_GET['filter_id'] > 0) {
		$query = "UPDATE `expense_filters` SET `user`='$filter_staff', `date_start`='$filter_min_date', `date_end`='$filter_max_date', `status`='$filter_status', `amt_min`='$filter_amt_min', `amt_max`='$filter_amt_max', `merchant`='$filter_merchants', `category`='$filter_category', `receipt`='$filter_receipt', `warning`='$filter_warnings', `description`='$filter_description', `filter_name`='$filter_name' WHERE `filter_id`='{$_GET['filter_id']}'";
		$history = "Expense entry has been updated. <br />";
	} else {
		$query = "INSERT INTO `expense_filters` (`owner`, `user`, `date_start`, `date_end`, `status`, `amt_min`, `amt_max`, `merchant`, `category`, `receipt`, `description`, `warning`, `filter_name`)
			VALUES ('".$_SESSION['contactid']."', '$filter_staff', '$filter_min_date', '$filter_max_date', '$filter_status', '$filter_amt_min', '$filter_amt_max', '$filter_merchants', '$filter_category', '$filter_receipt', '$filter_description', '$filter_warnings', '$filter_name')";
		$history = "Expense entry has been added. <br />";
	}
	$result = mysqli_query($dbc, $query);
	add_update_history($dbc, 'expenses_history', $history, '', $before_change);
	$filter_id = ($_GET['filter_id'] > 0 ? $_GET['filter_id'] : mysqli_insert_id($dbc));
	echo "<script> window.location.replace('?filter_id=$filter_id'); </script>";
} else if($_GET['filter_id'] > 0) {
	$filter_data = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `expense_filters` WHERE `filter_id`='{$_GET['filter_id']}'"));
	$filter_staff = $filter_data['user'];
	$filter_min_date = $filter_data['date_start'] == '0000-00-00' ? '' : $filter_data['date_start'];
	$filter_max_date = $filter_data['date_end'] == '0000-00-00' ? '' : $filter_data['date_end'];
	$filter_status = $filter_data['status'];
	$filter_amt_min = $filter_data['amt_min'];
	$filter_amt_max = $filter_data['amt_max'] > 0 ? $filter_data['amt_max'] : '';
	$filter_merchants = $filter_data['merchant'];
	$filter_category = $filter_data['category'];
	$filter_receipt = $filter_data['receipt'];
	$filter_description = $filter_data['description'];
	$filter_warnings = $filter_data['warning'];
	$filter_name = $filter_data['filter_name'];
} else if($_GET['staff_id'] > 0) {
	$filter_staff = $_GET['staff_id'];
}
if(!empty($_GET['filter_cat'])) {
	$filter_cat = $dbc->query("SELECT `category` FROM `expense_categories` WHERE `EC`='".filter_var($_GET['filter_cat'],FILTER_SANITIZE_STRING)."'")->fetch_assoc()['category'];
} 
$last_app_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `expense_approval_role_id` FROM `expense_approval_levels` ORDER BY expense_role_sorting DESC limit 0,1"));
$lid = $last_app_id['expense_approval_role_id'];
?>
<script>
toggle_filter = function() {
	if($('.toggle-filter').text() == 'Filter Expenses') {
		$('.toggle-filter').text('Close Filter Options');
		$('.expense-list').addClass('col-sm-6 col-xs-12');
		$('.filter-div').show().addClass('col-sm-6 col-xs-12');
		$('.toggle_all').hide();
	} else {
		$('.toggle-filter').text('Filter Expenses');
		$('.expense-list').removeClass('col-sm-6 col-xs-12');
		$('.filter-div').hide().removeClass('col-sm-6 col-xs-12');
		$('.toggle_all').show();
	}
}
function filter_expenses() {
	$('ul.chained-list a').not('.export').hide();
	$('ul.chained-list a[data-visible=visible]').show();
	$('.expense-list .block-label').remove();
	var filter_labels = '';
	if($('[name="filter_staff[]"] option:selected').length > 0) {
		var staff_names = '';
		$('[name="filter_staff[]"] option:selected').each(function() {
			staff_names += $(this).text();
		});
		$('ul.chained-list a').each(function() {
			if($('[name="filter_staff[]"]').val().indexOf($(this).data('staff').toString()) == -1) {
				$(this).hide();
			}
		});
		filter_labels += '<span class="block-label">Filter: Staff: '+staff_names+'</span>';
	}
	if($('[name=filter_min_date]').val() != '') {
		$('ul.chained-list a').each(function() {
			if($(this).data('date') < $('[name=filter_min_date]').val()) {
				$(this).hide();
			}
		});
		filter_labels += '<span class="block-label">Filter: Start Date: '+$('[name=filter_min_date]').val()+'</span>';
	}
	if($('[name=filter_max_date]').val() != '') {
		$('ul.chained-list a').each(function() {
			if($(this).data('date') > $('[name=filter_max_date]').val()) {
				$(this).hide();
			}
		});
		filter_labels += '<span class="block-label">Filter: End Date: '+$('[name=filter_max_date]').val()+'</span>';
	}
	if($('[name=filter_status]').val() != '') {
		$('ul.chained-list a').each(function() {
			if($(this).data('status') != $('[name=filter_status]').val()) {
				$(this).hide();
			}
		});
		filter_labels += '<span class="block-label">Filter: Status: '+$('[name=filter_status]').val()+'</span>';
	}
	if($('[name=filter_amt_min]').val() != '') {
		$('ul.chained-list a').each(function() {
			if(parseFloat($(this).data('amt')) < parseFloat($('[name=filter_amt_min]').val())) {
				$(this).hide();
			}
		});
		filter_labels += '<span class="block-label">Filter: Minimum Amount: '+$('[name=filter_amt_min]').val()+'</span>';
	}
	if($('[name=filter_amt_max]').val() != '') {
		$('ul.chained-list a').each(function() {
			if(parseFloat($(this).data('amt')) > parseFloat($('[name=filter_amt_max]').val())) {
				$(this).hide();
			}
		});
		filter_labels += '<span class="block-label">Filter: Maximum Amount: '+$('[name=filter_amt_max]').val()+'</span>';
	}
	if($('[name="filter_category[]"] option:selected').length > 0) {
		var category_names = '';
		$('[name="filter_category[]"] option:selected').each(function() {
			category_names += $(this).text();
		});
		$('ul.chained-list a').each(function() {
			if($('[name="filter_category[]"]').val().indexOf($(this).data('category').toString()) == -1) {
				$(this).hide();
			}
		});
		filter_labels += '<span class="block-label">Filter: Categories: '+category_names+'</span>';
	}
	if($('[name=filter_receipt]:checked').val() != 'any') {
		$('ul.chained-list a').each(function() {
			if($(this).data('receipt') != $('[name=filter_receipt]:checked').val()) {
				$(this).hide();
			}
		});
		filter_labels += '<span class="block-label">Filter: '+($('[name=filter_receipt]:checked').val() == 'yes' ? 'Receipt Attached' : 'No Receipt Attached')+'</span>';
	}
	if($('[name=filter_description]').val() != '') {
		$('ul.chained-list a').each(function() {
			if(!$(this).data('description').includes($('[name=filter_description]').val().toLowerCase())) {
				$(this).hide();
			}
		});
	}
	$('[name=filter_warnings] option:selected').each(function() {
		warning = $(this).val();
		$('ul.chained-list a').each(function() {
			if(!$(this).data('description').includes(warning)) {
				$(this).hide();
			}
		});
	});

	//Hide empty lists
	$('ul.chained-list').each(function() {
		$(this).show();
		if($(this).find('a li:visible').length == 0) {
			$(this).hide();
		}
	});

	//Prepend filter labels
	if(filter_labels != '') {
		$('.expense-list').prepend(filter_labels+'<div class="clearfix"></div>')
	}
}
function toggle_all(status) {
	var switch_all = $(status).text();
	var status_expense = $(status).data('status');
	if(switch_all == 'Show All') {
		$(status).text('Show Top 5');
		$('a[data-status=Submitted][data-visible=visible]').show();
	} else {
		$(status).text('Show All');
		var i = 0;
		$('a[data-status=Submitted][data-visible=visible]').each(function() {
			if(++i > 5) {
				$(this).hide();
			}
		});
	}
}
$(document).ready(function() {
	filter_expenses();
	$('form input,select').change(filter_expenses);
	<?php if($_GET['filter_id'] > 0 || $_GET['staff_id'] > 0) { ?>
		$('li:contains("Show All")').hide();
	<?php } else { ?>
		var i = 0;
		var status = '';
		$('a[data-visible=visible]').each(function() {
			if($(this).data('status') != status) {
				i = 0;
				status = $(this).data('status');
			}
			if(++i > 5) {
				$(this).hide();
			}
		});
	<?php } ?>
});
</script>
<form action="" method="POST">
	<div class="filter-div pull-right panel-group block-panels double-gap-top" style="display:none; padding: 1em;" id="filter_accordions">
		<div class="panel panel-name">
			Filter Expenses
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_staff" >
						Staff<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_staff" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to select the staff whose expenses should appear. If you leave this empty, all staff will be displayed."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Staff:
						</label>
						<div class="col-sm-8">
							<select data-placeholder="Select a Staff..." name="filter_staff[]" multiple class="chosen-select-deselect form-control">
								<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0 AND `show_hide_user`=1"),MYSQLI_ASSOC));
								foreach($query as $query_contactid) {
									echo "<option ".(in_array($query_contactid,explode(',',$filter_staff)) ? 'selected' : '')." value='".$query_contactid."'>".get_contact($dbc, $query_contactid)."</option>";
								} ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_dates" >
						Date<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_dates" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set a date range for the expenses. Leaving the minimum date blank will allow you to select all expenses before a date."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							From:
						</label>
						<div class="col-sm-8">
							<input type="text" name="filter_min_date" class="datepicker form-control" value="<?= $filter_min_date ?>" />
						</div><div class="clearfix"></div>
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set a date range for the expenses. Leaving either the maximum date blank will allow you to select all expenses after a date."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Until:
						</label>
						<div class="col-sm-8">
							<input type="text" name="filter_max_date" class="datepicker form-control" value="<?= $filter_max_date ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_status" >
						Status<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_status" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Expense Status:
						</label>
						<div class="col-sm-8">
							<select data-placeholder="Select a status..." name="filter_status" class="chosen-select-deselect form-control">
								<option value=""></option>
								<option <?= $filter_status == 'Submitted' ? 'selected' : '' ?> value="Submitted">Pending</option>
								<option <?= $filter_status == 'Approved' ? 'selected' : '' ?> value="Approved">Approved</option>
								<option <?= $filter_status == 'Paid' ? 'selected' : '' ?> value="Paid">Paid</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_amount" >
						Amount<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_amount" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set an amount range for the expenses. Leaving the minimum amount blank will allow you to select all expenses below a certain amount."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Minimum Amount:
						</label>
						<div class="col-sm-8">
							<input type="number" name="filter_amt_min" class="form-control" step="0.01" min="0" value="<?= $filter_amt_min ?>" />
						</div><div class="clearfix"></div>
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set an amount range for the expenses. Leaving either the maximum amount blank will allow you to select all above a certain amount."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Maximum Amount:
						</label>
						<div class="col-sm-8">
							<input type="number" name="filter_amt_max" class="form-control" step="0.01" min="0" value="<?= $filter_amt_max ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_merchant" >
						Merchant<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_merchant" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Merchant:
						</label>
						<div class="col-sm-8">
							<select data-placeholder="Select a Merchant..." name="filter_merchants[]" multiple class="chosen-select-deselect form-control">
								<?php $query = mysqli_query($dbc, "SELECT `merchant` FROM `expense` WHERE `deleted`=0 ORDER BY `merchant`");
								while($row = mysqli_fetch_array($query)) {
									echo "<option ".(in_array($row['merchant'],explode(',',$filter_merchants)) ? 'selected' : '')." value='".$row['merchant']."'>".$row['merchant']."</option>";
								} ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_category" >
						Category<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_category" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Category:
						</label>
						<div class="col-sm-8">
							<select data-placeholder="Select a Category..." name="filter_category[]" multiple class="chosen-select-deselect form-control">
								<?php $query = mysqli_query($dbc, "SELECT DISTINCT(CONCAT(EC,': ',`category`)) cat, `category` FROM `expense_categories` ORDER BY cat");
								while($query_cat = mysqli_fetch_array($query)) {
									echo "<option ".($filter_category == $query_cat['cat'] ? 'selected' : '')." value='".$query_cat['category']."'>".$query_cat['cat']."</option>";
								} ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_receipt" >
						Receipt<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_receipt" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Receipt Attached:
						</label>
						<div class="col-sm-8">
							<label class="form-checkbox"><input type="radio" <?= $filter_receipt != 'yes' && $filter_receipt != 'no' ? 'checked' : '' ?> name="filter_receipt" value="any"> N/A</label>
							<label class="form-checkbox"><input type="radio" <?= $filter_receipt == 'yes' ? 'checked' : '' ?> name="filter_receipt" value="yes"> Yes</label>
							<label class="form-checkbox"><input type="radio" <?= $filter_receipt == 'no' ? 'checked' : '' ?> name="filter_receipt" value="no"> No</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_description" >
						Description<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_description" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Text in Description:
						</label>
						<div class="col-sm-8">
							<input type="text" name="filter_description" value="<?= $filter_description ?>" class="form-control" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_warning" >
						Warnings / Errors<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_warning" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Warnings / Errors:
						</label>
						<div class="col-sm-8">
							<select data-placeholder="Select Warnings..." name="filter_warnings[]" multiple class="chosen-select-deselect form-control">
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_save_filter" >
						Save Current Filter<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_save_filter" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Filter Name:
						</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="filter_name" value="<?= $filter_name ?>" />
						</div>
					</div>
					<button class="pull-right btn brand-btn" name="filter_save" value="filter">Save Filter</button>
				</div>
			</div>
		</div>
	</div>
</form>

<?php 
if($_GET['filter_id'] == '' || $_GET['filter_id'] == 'all'){
?>
<div class="clearfix">&nbsp;</div>
<div class="row">
	<form name="filter_form" action="" method="POST">
		<label for="first_name" class="col-sm-2 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set a date range for the expenses. Leaving the minimum date blank will allow you to select all expenses before a date."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			From:
		</label>
		<div class="col-sm-3">
			<input type="text" name="expense_min_date" class="datepicker form-control" value="<?= (!isset($_POST['expense_min_date']) ? date('Y-m-01') : $_POST['expense_min_date']) ?>" />
		</div>
		<label for="first_name" class="col-sm-2 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set a date range for the expenses. Leaving either the maximum date blank will allow you to select all expenses after a date."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Until:
		</label>
		<div class="col-sm-3">
			<input type="text" name="expense_max_date" class="datepicker form-control" value="<?= (!isset($_POST['expense_max_date']) ? date('Y-m-t') : $_POST['expense_max_date']) ?>" />
		</div>
		<div class="col-sm-2">
			<input type="submit" name="filter_submit" class="btn brand-btn mobile-block" value="Apply">
		</div>
	</form>
</div>
<div class="clearfix">&nbsp;</div>
<?php
}
$appid = explode('_',$_GET['filter_tab']);
$pre = $appid[1]-1 ;
?>

<?php if($approvals == 1) {
	$expense_list = mysqli_query($dbc, "SELECT IF(`status`='','Submitted',`status`) ex_status, `expense`.* FROM `expense` WHERE `deleted`=0 AND `reimburse` > 0 AND '$filter_cat' IN (`category`,'') ".$filter_date." ORDER BY IF(`status`='Declined',4,IF(`status`='Paid',3,IF(`status`='Approved',2,1))), `ex_date` DESC");
	$status_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(IF(`status`!='Approved' AND `status`!='Paid',1,0)) submitted, SUM(IF(`status`='Approved' and `approved_by`='$pre',1,0)) approved, SUM(IF(`status`='Paid',1,0)) paid, SUM(IF(`status`='Declined',1,0)) declined FROM `expense` WHERE `deleted`=0 AND `reimburse` > 0 AND '$filter_cat' IN (`category`,'') ".$filter_date." "));
} else {
	$expense_list = mysqli_query($dbc, "SELECT IF(`status`='','Submitted',`status`) ex_status, `expense`.* FROM `expense` WHERE `deleted`=0 AND `reimburse` > 0 AND '$filter_cat' IN (`category`,'') AND `staff` IN ('{$_SESSION['contactid']}','".get_contact($dbc, $_SESSION['contactid'])."') ".$filter_date." ORDER BY IF(`status`='Declined',4,IF(`status`='Paid',3,IF(`status`='Approved',2,1))), `ex_date` DESC");
	$status_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(IF(`status`!='Approved' AND `status`!='Paid',1,0)) submitted, SUM(IF(`status`='Approved' and `approved_by`='$pre',1,0)) approved, SUM(IF(`status`='Paid',1,0)) paid, SUM(IF(`status`='Declined',1,0)) declined FROM `expense` WHERE `deleted`=0 AND `reimburse` > 0 AND '$filter_cat' IN (`category`,'') AND `staff` IN ('{$_SESSION['contactid']}','".get_contact($dbc, $_SESSION['contactid'])."') ".$filter_date." "));
}

$view_sql = "SELECT * FROM (SELECT expense, expense_dashboard, exchange_buffer, gst_name, gst_amt, pst_name, pst_amt, hst_name, hst_amt, expense_types, expense_rows, tab, expense_mode FROM field_config_expense
	WHERE `tab`='current_month' OR `tab` LIKE 'category_%' UNION SELECT
	'Flight,Hotel,Breakfast,Lunch,Dinner,Beverages,Transportation,Entertainment,Gas,Misc',
	'Description,Date,Receipt,Type,Day Expense,Amount,Tax,Total', '0', 'GST', '5', 'PST', '0', 'HST', '0', 'Meals,Tip', 1, '', '') settings ORDER BY `tab` DESC";
$get_view_expense = mysqli_fetch_assoc(mysqli_query($dbc,$view_sql));

$app_role = mysqli_fetch_array(mysqli_query($dbc, "SELECT * from expense_approval_levels where expense_role_sorting = '".$pre."' "));

if($get_view_expense['expense_mode'] == 'inbox'){
echo "<div class='expense-list' style='text-align:center;'>";
$no_list = true;
$status = '';
while($expense = mysqli_fetch_array($expense_list)) {
	if($expense['ex_status'] != $status) {
	    if($no_list) {
			$no_list = false;
		} else {
			echo "<li class='toggle_all' onclick='toggle_all(this);' data-status='$status' style='";
			if($status == 'Submitted' && $status_count['submitted'] <= 5) {
				echo "display: none;";
			} else if($status == 'Approved' && $status_count['approved'] <= 5) {
				echo "display: none;";
			} else if($status == 'Paid' && $status_count['paid'] <= 5) {
				echo "display: none;";
			} else if($status == 'Declined' && $status_count['declined'] <= 5) {
				echo "display: none;";
			}
			echo "'>Show All</li></ul>";
		}
		$status = $expense['ex_status'];
		echo "<ul class='chained-list'>";
		if($status == 'Submitted') {
			echo "<li ".($_GET['filter_id'] == 'approved' || $_GET['filter_id'] == 'paid' ? 'style="display:none;"' : '').">Expenses Awaiting Approval (".$status_count['submitted'].")";
			echo "<a href='expense_pdf.php?min_date=".$filter_min_date."&max_date=".$filter_max_date."&status=Submitted' class='export'><img src='../img/icons/ROOK-download-icon.png' class='inline-img' title='Export PDF'></a></li>";
		} else if($status == 'Approved') {
		    echo "<li ".( ($_GET['filter_id'] == 'pending' || $_GET['filter_id'] == 'paid') && $expense['approved_by'] != $app_role['expense_approval_role_id'] ? 'style="display:none;"' : '').">Approved &amp; Awaiting Payment (".$status_count['approved'].")";
			echo "<a href='expense_pdf.php?min_date=".$filter_min_date."&max_date=".$filter_max_date."&status=Approved' class='export'><img src='../img/icons/ROOK-download-icon.png' class='inline-img' title='Export PDF'></a></li>";
		} else if($status == 'Paid') {
			echo "<li ".($_GET['filter_id'] == 'approved' || $_GET['filter_id'] == 'pending' ? 'style="display:none;"' : '').">Completed &amp; Paid (".$status_count['paid'].")";
			echo "<a href='expense_pdf.php?min_date=".$filter_min_date."&max_date=".$filter_max_date."&status=Paid' class='export'><img src='../img/icons/ROOK-download-icon.png' class='inline-img' title='Export PDF'></a></li>";
		} else if($status == 'Declined') {
			echo "<li ".($_GET['filter_id'] == 'declined' || $_GET['filter_id'] == 'declined' ? 'style="display:none;"' : '').">Declined (".$status_count['declined'].")";
			echo "<a href='expense_pdf.php?min_date=".$filter_min_date."&max_date=".$filter_max_date."&status=Declined' class='export'><img src='../img/icons/ROOK-download-icon.png' class='inline-img' title='Export PDF'></a></li>";
		} else {
			echo "<li ".($_GET['filter_id'] == 'all' ? '' : 'style="display:none;"').">Unknown Status</li>";
		}
	}
	
	$visibility = "hidden style='display:none;'";
	switch($_GET['filter_id']) {
	    case 'pending':
	        if($expense['ex_status'] == 'Submitted' && $appid[1] == '1') {
	            $visibility = "visible";
	        }else if($expense['ex_status'] == 'Approved' && $expense['approved_by'] == $app_role['expense_approval_role_id'] ){
	            $visibility = "visible";
	        }
	        break;
	    case 'approved':
	        if($expense['ex_status'] == 'Approved' && $expense['approved_by'] == $lid) {
	            $visibility = "visible";
	        }
	        break;
	    case 'paid':
	        if($expense['ex_status'] == 'Paid') {
	            $visibility = "visible";
	        }
	        break;
	    case 'declined':
	        if($expense['ex_status'] == 'Declined') {
	            $visibility = "visible";
	        }
	        break;
	    default:
	        $visibility = "visible";
	        break;
	}
	
	$staff = mysqli_fetch_array(mysqli_query($dbc, "SELECT `initials`, `first_name`, `last_name`, `calendar_color` FROM `contacts` WHERE `contactid`='{$expense['staff']}'"));
	$warnings = '';
	echo "<a href='' onclick='overlayIFrame(\"edit_expense.php?edit={$expense['expenseid']}&app_id={$_GET['filter_tab']}\"); return false;' data-visible='$visibility' data-staff='{$expense['staff']}' data-date='{$expense['ex_date']}' data-status='{$expense['ex_status']}' ";
	echo "data-amt='{$expense['total']}' data-merchant='' data-category='{$expense['category']}' data-receipt='".($expense['ex_file'] != '' ? 'yes' : 'no')."' data-description='".strtolower($expense['description'])."' data-warning='$warnings'>";
	
	echo '<li>';
	echo "<div class='middle-valign col-sm-2 col-xs-12 expense-col-1'>";
	if($expense['staff'] > 0) {
	    profile_id($dbc, $expense['staff']);
	} else {
	    echo '<span class="id-circle" style="background-color:#6DCFF6;">';
	    foreach(explode(' ',$expense['staff']) as $name) {
	        echo substr($name,0,1);
	    }
	    echo '</span>';
	}
	echo '</div>';
	
	echo '<div class="middle-valign col-sm-7 col-xs-12 expense-col-2">';
	echo '<p style="font-weight:bold;">'.($expense['staff'] > 0 ? get_contact($dbc, $expense['staff']) : $expense['staff'])."</p>";
	echo '<p style="font-size:0.7em;">'.html_entity_decode($expense['description']).'</p>';
	echo '<p style="font-size:0.7em; color:#888;">'.date('F j, Y', strtotime($expense['ex_date'])).'</p>';
	echo '</div>';
	
	echo "<div class='middle-valign col-sm-3 col-xs-12 expense-col-3'>$";
	echo number_format($expense['total'],2)."<br />";
	if($expense['status'] == 'Approved') {
	    echo '<img src="'.WEBSITE_URL.'/img/icons/ROOK-status-approved.png" title="This expense was approved on '.$expense['approval_date'].'.">';
	} else if($expense['status'] == 'Paid') {
	    echo '<img src="'.WEBSITE_URL.'/img/icons/ROOK-status-paid.png" title="This expense was paid out on '.$expense['paid_date'].'.">';
	} else if($expense['status'] == 'Declined') {
	    echo '<img src="'.WEBSITE_URL.'/img/icons/ROOK-status-rejected.jpg" title="This expense has been declined. Please contact the individual responsible for reimbursing expenses for further details.">';
	} else {
	    $warnings = mysqli_query($dbc, "SELECT ep.`type`, ep.`name` FROM `expense_policy` ep INNER JOIN `expense` e ON ep.`applies_to` IN (e.`staff`,'All') AND e.`amount` >= ep.`max_amt` AND ep.`reimburse` IN (e.`reimburse`,2) AND (IFNULL(e.`ex_file`,'') NOT LIKE ep.`receipt` OR IFNULL(e.`category`,'') NOT LIKE ep.`category` OR IFNULL(e.`description`,'') NOT LIKE ep.`description`) WHERE e.`expenseid`='{$expense['expenseid']}' AND ep.`deleted`=0 ORDER BY 'Block','Warn'");
	    $block = false;
	    while($warning = mysqli_fetch_array($warnings)) {
	        if($warning['type'] == 'Block') {
	            $block = true;
	            echo '<img src="'.WEBSITE_URL.'/img/icons/ROOK-status-error.png" title="'.$warning['name'].'.">';
	        } else {
	            echo '<img src="'.WEBSITE_URL.'/img/icons/ROOK-status-warning.png" title="'.$warning['name'].'.">';
	        }
	    }
	    if(!$block) {
	        echo '<img src="'.WEBSITE_URL.'/img/icons/ROOK-status-completed.png" title="This expense was submitted for approval on '.$expense['submitted_date'].'.">';
	    }
	}
	$warning = false;
	if($warning) {
	    echo '<img src="'.WEBSITE_URL.'/img/icons/ROOK-status-warning.png" style="height:1em;">';
	}
	$error = false;
	if($error) {
	    echo '<img src="'.WEBSITE_URL.'/img/icons/ROOK-status-error.png" style="height:1em;">';
	}
    echo '<img src="'.WEBSITE_URL.'/img/icons/edit.png" style="height:1em;">';
            
    echo "</div>";
    echo "<div class='clearfix'></div>";
    echo "</li></a>";
}
    
    if($_GET['filter_id']!='' && $_GET['filter_id']!='all'){
        echo "<li class='toggle_all font-medium border-bottom-ddd cursor-hand' onclick='toggle_all(this);' data-status='$status' style='";
        if($status == 'Submitted' && $status_count['submitted'] <= 5) {
        	echo "display: none;";
        } else if($status == 'Approved' && $status_count['approved'] <= 5) {
        	echo "display: none;";
        } else if($status == 'Paid' && $status_count['paid'] <= 5) {
        	echo "display: none;";
        } else if($status == 'Declined' && $status_count['declined'] <= 5) {
        	echo "display: none;";
        }
        echo "'>Show All</li>"; 
    }
    echo "</ul></div>";

}else if($get_view_expense['expense_mode'] == 'tables'){
    echo "<table class='table table-bordered'>";
        echo "<tr class='hidden-xs hidden-sm'>";
            echo "<th>Title</th>";
            echo "<th>Description</th>";
            echo "<th>Date</th>";
            //echo "<th>Projects</th>";
            //echo "<th>Category</th>";
            echo "<th>Status</th>";
            echo "<th>Function</th>";
        echo "</tr>";
        
        while($expense = mysqli_fetch_array($expense_list)) {
            
            $visibility = "style='display:none;'";
            switch($_GET['filter_id']) {
                case 'pending':
                    if($expense['ex_status'] == 'Submitted' && $appid[1] == '1') {
                        $visibility = "";
                    }else if($expense['ex_status'] == 'Approved' && $expense['approved_by'] == $app_role['expense_approval_role_id'] ){
                        $visibility = "";
                    }
                    break;
                case 'approved':
                    if($expense['ex_status'] == 'Approved' && $expense['approved_by'] == $lid) {
                        $visibility = "";
                    }
                    break;
                case 'paid':
                    if($expense['ex_status'] == 'Paid') {
                        $visibility = "";
                    }
                    break;
                case 'declined':
                    if($expense['ex_status'] == 'Declined') {
                        $visibility = "";
                    }
                    break;
                default:
                    $visibility = "";
                    break;
            }
            
            echo "<tr ".$visibility.">";
            echo "<td data-title='Title'>".($expense['staff'] > 0 ? get_contact($dbc, $expense['staff']) : $expense['staff'])."</td>";
            echo "<td data-title='Description'>".html_entity_decode($expense['description'])."</td>";
            echo "<td data-title='Date'>".date('F j, Y', strtotime($expense['ex_date']))."</td>";
            //echo "<td data-title='Projects'>".$expense['projectid']."</td>";
            //echo "<td data-title='Category'>".$expense['category']."</td>";
            echo "<td data-title='Status'>".$expense['ex_status']."</td>";
            echo "<td data-title='Function'><a href='' onclick='overlayIFrame(\"edit_expense.php?edit={$expense['expenseid']}&app_id={$_GET['filter_tab']}\"); return false;'>Edit</a></td>";
            echo "</tr>";
        }
        
    echo "</table>";
}
?>
