<?php include_once('../include.php');
checkAuthorised('expense');
error_reporting(0);
if(isset($_POST['submit'])) {
	$expenseid = $_POST['expenseid'];
	if(empty($expenseid)) {
		mysqli_query($dbc, "INSERT INTO `expense` (`status`,`submit_by`,`submit_date`) VALUES ('Submitted','".$_SESSION['contactid']."','".date('Y-m-d')."')");
		$expenseid = mysqli_insert_id($dbc);
		$before_change = "";
		$history = "Expense entry has been added. <br />";
		add_update_history($dbc, 'expenses_history', $history, '', $before_change);
	}
	$fields = [];

	if(!empty($_FILES['ex_file']['name'])) {
		$file = $_FILES['ex_file']['tmp_name'];
		$filename = $_FILES['ex_file']['name'];
		if (!file_exists('download')) {
			mkdir('download', 0777, true);
		}
		$basefilename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$filename);
		$i = 0;
		while(file_exists('download/'.$filename) || file_exists('download/'.str_replace('.pdf','.png',$filename))) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basefilename);
		}
		move_uploaded_file($file, 'download/'.$filename);
		if(strtolower(pathinfo($filename, PATHINFO_EXTENSION)) == 'pdf') {
			try {
				exec('gs -sDEVICE=png16m -r600 -dDownScaleFactor=3 -o "download/'.$filename.'" "download/'.str_replace('.pdf','.png',$filename).'"');
				unlink('download/'.$filename);
				$filename = str_replace('.pdf', '.png', $filename);
			} catch(Exception $e) { }
			$fields[] = "`ex_file`='download/$filename'";
		} else {
			$image_info = getimagesize('download/'.$filename);
			if($image_info[0] > 0 && $image_info[1] > 0) {
				$fields[] = "`ex_file`='download/$filename'";
			} else {
				unlink('download/'.$filename);
			}
		}
	}
	foreach($_POST as $field_name => $value) {
		switch($field_name) {
			case 'exchange_rate': $fields[] = "`exchange_rate`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'amount': $fields[] = "`amount`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'total': $fields[] = "`total`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'currency': $fields[] = "`currency`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'tips': $fields[] = "`tips`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'gst': $fields[] = "`gst`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'pst': $fields[] = "`pst`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'hst': $fields[] = "`hst`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'description': $fields[] = "`description`='".filter_var(htmlentities($value), FILTER_SANITIZE_STRING)."'"; break;
			case 'ex_date': $fields[] = "`ex_date`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'province': $fields[] = "`province`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'country': $fields[] = "`country`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'type': $fields[] = "`type`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'reimburse': $fields[] = "`reimburse`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'category': $fields[] = "`category`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'heading': $fields[] = "`title`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'staff': $fields[] = "`staff`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'vendor': $fields[] = "`vendor`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'work_order': $fields[] = "`work_order`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'projectid': $fields[] = "`projectid`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
		}
	}
	mysqli_query($dbc,"UPDATE `expense` SET ".implode(',', $fields)." WHERE `expenseid`='$expenseid'");
	$before_change = "";
	$history = "Expense entry has been updated for expense id $expenseid. <br />";
	add_update_history($dbc, 'expenses_history', $history, '', $before_change);


	if($_POST['submit'] == 'approve') {
		$before_change = capture_before_change($dbc, 'expense', 'status', 'expenseid', $expenseid);
		$before_change .= capture_before_change($dbc, 'expense', 'approval_date', 'expenseid', $expenseid);
		$before_change .= capture_before_change($dbc, 'expense', 'approval_by', 'expenseid', $expenseid);
		mysqli_query($dbc,"UPDATE `expense` SET `status`='Approved', `approval_date`='".date('Y-m-d')."', `approval_by`='".$_SESSION['contactid']."' WHERE `expenseid`='$expenseid'");
		$history = capture_after_change('status', 'Approved');
		$history .= capture_after_change('approval_date', date('Y-m-d'));
		$history .= capture_after_change('approval_by', $_SESSION['contactid']);
		add_update_history($dbc, 'expenses_history', $history, '', $before_change);

	} else if($_POST['submit'] == 'decline') {
		$before_change = capture_before_change($dbc, 'expense', 'status', 'expenseid', $expenseid);
		mysqli_query($dbc,"UPDATE `expense` SET `status`='Declined' WHERE `expenseid`='$expenseid'");
		$history = capture_after_change('status', 'Declined');
		add_update_history($dbc, 'expenses_history', $history, '', $before_change);
	} else if($_POST['submit'] == 'pay') {
		$before_change = capture_before_change($dbc, 'expense', 'status', 'expenseid', $expenseid);
		$before_change .= capture_before_change($dbc, 'expense', 'paid_date', 'expenseid', $expenseid);
		$before_change .= capture_before_change($dbc, 'expense', 'paid_by', 'expenseid', $expenseid);
		mysqli_query($dbc,"UPDATE `expense` SET `status`='Paid', `paid_date`='".date('Y-m-d')."', `paid_by`='".$_SESSION['contactid']."' WHERE `expenseid`='$expenseid'");
		$history = capture_after_change('status', 'Approved');
		$history .= capture_after_change('paid_date', date('Y-m-d'));
		$history .= capture_after_change('paid_by', $_SESSION['contactid']);
		add_update_history($dbc, 'expenses_history', $history, '', $before_change);
	}

	echo "<script> window.location.replace('?'); </script>";
}

$expense = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `expense` WHERE `expenseid`='{$_GET['edit']}'"));
if(!($_GET['edit'] > 0)) {
	$default_staff = get_config($dbc, 'expense_default_staff');
	$expense['staff'] = ($default_staff > 0 ? $default_staff : $_SESSION['contactid']);
	$expense['ex_date'] = date('Y-m-d');
}
$warnings = mysqli_query($dbc, "SELECT ep.`type`, ep.`name` FROM `expense_policy` ep INNER JOIN `expense` e ON ep.`applies_to` IN (e.`staff`,'All') AND e.`amount` >= ep.`max_amt` AND ep.`reimburse` IN (e.`reimburse`,2) AND (IFNULL(e.`ex_file`,'') NOT LIKE ep.`receipt` OR IFNULL(e.`category`,'') NOT LIKE ep.`category` OR IFNULL(e.`description`,'') NOT LIKE ep.`description`) WHERE e.`expenseid`='{$_GET['edit']}' AND ep.`deleted`=0 ORDER BY 'Block','Warn'");
$categories_sql = "SELECT * FROM (SELECT CONCAT('EC ',`ec`,': ',`category`) `ec_code`, `category`, CONCAT('GL ',`gl`,': ',`heading`) `gl_code`, `heading`, `amount` FROM `expense_categories` WHERE `deleted`=0 ORDER BY `ec`, `gl`) `categories`
	UNION SELECT 'Uncategorized', '', 'Misc', '', 0";
$category_query = mysqli_query($dbc, $categories_sql);
$category_list = [];
$heading_list = [];
$sel_cat = '';
$cat_amount = 0;
while($cat_row = mysqli_fetch_array($category_query)) {
	if($sel_cat != $cat_row['category']) {
		$category_list[$cat_row['ec_code']] = $cat_row['category'];
	}
	$heading_list[$cat_row['gl_code']] = [$cat_row['heading'],$cat_row['amount'],$cat_row['category']];
	if($cat_row['heading'] == $expense['title']) {
		$cat_amount = $cat_row['heading'];
	}
}
$province_list = explode('#*#',get_config($dbc, 'expense_provinces'));

$approvals = approval_visible_function($dbc, 'expense');
$ex_category = preg_replace('/[^a-z]/','_',strtolower($expense['category']));
$config_row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM (SELECT expense, expense_dashboard, exchange_buffer, gst_name, gst_amt, pst_name, pst_amt, hst_name, hst_amt, expense_types, expense_rows, tab FROM field_config_expense
	WHERE `tab` IN ('current_month') UNION SELECT
	'Flight,Hotel,Breakfast,Lunch,Dinner,Beverages,Transportation,Entertainment,Gas,Misc',
	'Description,Date,Receipt,Type,Day Expense,Amount,Tax,Total', '0', 'GST', '5', 'PST', '0', 'HST', '0', 'Meals,Tip', 1, 'defaults') settings ORDER BY `tab` ASC"));
$field_config = array_filter(array_unique(explode(',',$config_row['expense_dashboard'])));
$value_config = $config_row['expense'];
$gst_name = trim($config_row['gst_name'],',');
$pst_name = trim($config_row['pst_name'],',');
$hst_name = trim($config_row['hst_name'],',');
$gst_amt = trim($config_row['gst_amt'],',');
$pst_amt = trim($config_row['pst_amt'],',');
$hst_amt = trim($config_row['hst_amt'],',');
$expense_types = trim(','.$config_row['expense_types'].',',',');
$default_country = get_config($dbc, 'default_country');
$default_province = get_config($dbc, 'default_province');
$province_list = explode('#*#',get_config($dbc, 'expense_provinces'));
$amt_label = [];
$currency_json = file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json');
$currency_data = json_decode($currency_json, TRUE);
if(count(array_intersect($field_config, ['Tax','Local Tax','Third Tax'])) > 0) {
	$amt_label[] = 'Tax';
}
if(in_array('Tips',$field_config)) {
	$amt_label[] = 'Tips';
} ?>
<script>
$(document).ready(function() {
	$('[name=category]').change();
	$('[name=currency],[name=province]').change(calcTotal);
});
var exchange_data = <?= $currency_json ?>;
var exchange_buffer = <?= $config_row['exchange_buffer'] ?>;
function calcTotal() {
	var amt = +$('[name=amount]').val() || 0;
	$('[name=amount]').val(amt.toFixed(2));
	var tips = +$('[name=tips]').val() || 0;
	$('[name=tips]').val(tips.toFixed(2));
	var gst_rate = <?= ($gst_amt > 0 ? $gst_amt : 0) ?>;
	var pst_rate = <?= ($pst_amt > 0 ? $pst_amt : 0) ?>;
	var hst_rate = <?= ($hst_amt > 0 ? $hst_amt : 0) ?>;
	var province = $('[name=province] option:selected');
	if($('[name=amount]').prop('readonly')) {
		gst_rate = 0;
		pst_rate = 0;
		hst_rate = 0;
	} else if(province.val() != '' && province.val() != undefined) {
		gst_rate = province.data('gst');
		pst_rate = province.data('pst');
		hst_rate = province.data('hst');
	}
	$('[name=gst]').val((amt * gst_rate / 100).toFixed(2));
	$('[name=pst]').val((amt * pst_rate / 100).toFixed(2));
	$('[name=hst]').val((amt * hst_rate / 100).toFixed(2));

	var currency = $('[name=currency]').val();
	var ex_date = $('[name=ex_date]').val() == '' || $('[name=ex_date]').val() == undefined  ? '<?= date('Y-m-d') ?>' : $('[name=ex_date]').val();
	var exchange_rate = 1;
	if(currency != 'CAD/CAD' && currency != undefined) {
		var exchange_rate = $(exchange_data.observations).filter(function() { return this.d == ex_date; });
		if(exchange_rate.length == 0 && ex_date > exchange_data.observations[exchange_data.observations.length - 1].d) {
			exchange_rate = exchange_data.observations[exchange_data.observations.length - 1][$('[name=currency] option:selected').data('currency')].v + exchange_buffer;
		} else if(exchange_rate.length == 0) {
			exchange_rate = 1;
		} else {
			exchange_rate = exchange_rate[0][$('[name=currency] option:selected').data('currency')].v + exchange_buffer;
		}
		$('[name=exchange_rate]').val(exchange_rate);
	}

	$('[name=total]').val((amt * (100 + gst_rate + pst_rate + hst_rate) / 100 * exchange_rate).toFixed(2));
}
function format_money(input) {
	var regex = /.*\.[0-9]{2}$/;
	if(input.value.match(regex) === null) {
		input.value = (parseInt(input.value.replace('.','')) / 100).toFixed(2);
	}
}
</script>
<div class="main-screen full-width-screen">
<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
<input type="hidden" name="expenseid" value="<?= $expense['expenseid'] ?>">
<input type="hidden" name="exchange_rate" value="<?= $expense['exchange_rate'] ?>">
<input type="hidden" name="total" value="<?= $expense['total'] ?>">
<div class="col-sm-6 no-gap-pad">
	<ul class="chained-field">
		<li id="amt-fields">
			<div class="pull-left" style="font-size:1.65em;">$</div>
            <div class="pull-left"><input type="number" min="0" step="0.01" name="amount" onkeyup="format_money(this);" onchange="calcTotal();" value="<?= $expense['amount'] ?>" class="form-control expense-amount" id="amt-box" /></div>
			<?php if(in_array('Exchange',$field_config)) { ?><label for="currency-box" class="sub-label" style="position:absolute; overflow:hidden; right:0; width:6em;">Currency
				<select name="currency"class="form-control above-label" id="currency-box">
				<option value="CAD/CAD">CAD</option>
				<?php foreach($currency_data['seriesDetail'] as $id => $info) {
					echo "<option data-currency='".$id."' ".($expense['currency'] == $info['label'] ? 'selected' : '');
					echo " value='".$info['label']."' title='".str_replace(' to Canadian dollar daily exchange rate','',$info['description'])."'>".str_replace('/CAD','',$info['label'])."</option>";
				} ?>
				</select></label><?php } ?>
            <div class="clearfix"></div>
            <div for="amt-box" class="sub-label separator-dark text-uppercase font-medium">Amount<?= (count($amt_label) > 0 ? ' Before ' : '').implode(' And/Or ',$amt_label) ?></div>
            <!-- <label for="amt-box" class="sub-label" style="padding-right:6.5em; margin-right:-6.5em;">Amount<?= (count($amt_label) > 0 ? ' Before ' : '').implode(' And/Or ',$amt_label) ?>
				<span style="float:left; font-size: 2em; margin: -1.75em 0 0 0;">$</span><input type="number" min="0" step="0.01" name="amount" onkeyup="format_money(this);" onchange="calcTotal();" value="<?= $expense['amount'] ?>" class="clear-control above-label" id="amt-box" style="padding-left:1em;"></label> -->
			<?php if(in_array('Tax',$field_config)) { ?><label class="super-label text-center" id="gst-box" style="max-width: 25%;">GST<input type="number" min="0" step="0.01" name="gst" value="<?= $expense['gst'] ?>" placeholder="GST" class="form-control"></label><?php } ?>
			<?php if(in_array('Total',$field_config)) { ?><label class="super-label text-center" style="float:right; max-width: 25%;">Total<input type="number" name="total" readonly value="<?= $expense['total'] ?>" placeholder="Total" class="form-control" id="gst-box"></label><?php } ?>
			<?php if(in_array('Tips',$field_config)) { ?><label class="super-label text-center" style="float:right; max-width: 25%;">Tips<input type="number" min="0" step="0.01" name="tips" value="<?= $expense['tips'] ?>" placeholder="Tips" class="form-control" id="gst-box"></label><?php } ?>
			<?php if(in_array('Third Tax',$field_config)) { ?><label class="super-label text-center" style="float:right; max-width: 25%;">HST<input type="number" min="0" step="0.01" name="hst" value="<?= $expense['hst'] ?>" placeholder="HST" class="form-control" id="gst-box"></label><?php } ?>
			<?php if(in_array('Local Tax',$field_config)) { ?><label class="super-label text-center" style="float:right; max-width: 25%;">PST<input type="number" min="0" step="0.01" name="pst" value="<?= $expense['pst'] ?>" placeholder="PST" class="form-control" id="gst-box"></label><?php } ?>
			<div class="clearfix"></div>
		</li>
		<li><label for="staff-box" class="super-label">Staff
				<select name="staff" data-placeholder="Select Staff" class="chosen-select-deselect" id="staff-box">
				<option></option>
				<?php $staff_query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE (`category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0 AND `show_hide_user`=1) OR `contactid`='".$expense['staff']."'"),MYSQLI_ASSOC));
				foreach($staff_query as $staffid) {
					echo "<option ".($expense['staff'] == $staffid ? 'selected' : '')." value='".$staffid."'>".get_contact($dbc, $staffid)."</option>";
				}
				if(!is_numeric($expense['staff'])) {
					echo "<option selected value='".$expense['staff']."'>".$expense['staff']."</option>";
				} ?>
				</select></label></li>
		<?php foreach($field_config as $field_name) { ?>
			<?php if($field_name == 'Description') { ?><li><label class="super-label" style="overflow: visible; padding-top: 0;">Description <textarea name="description"><?= $expense['description'] ?></textarea></label></li><?php } ?>
			<?php if($field_name == 'Date') { ?><li><label class="super-label">Date <input name="ex_date" value="<?= $expense['ex_date'] ?>" class="form-control datepicker"></label></li><?php } else { ?><input type="hidden" name="ex_date" value="<?= ($expense['ex_date'] == '' ? date('Y-m-d') : $expense['ex_date']) ?>"><?php } ?>
			<?php if($field_name == 'Work Order') { ?><li><label class="super-label"><?= TICKET_NOUN ?> <select name="work_order" data-placeholder="Select <?= TICKET_NOUN ?>" class="form-control chosen-select-deselect"><option></option>
					<?php $ticket_list = $dbc->query("SELECT * FROM `tickets` WHERE `deleted`=0 AND `status` != 'Archived' ORDER BY `ticketid` DESC");
					while($ticket = $ticket_list->fetch_assoc()) { ?>
						<option <?= $ticket['ticketid'] == $expense['work_order'] ? 'selected' : '' ?> <?= $ticket['projectid'] != $expense['projectid'] && $expense['projectid'] > 0 ? 'style="display:none;"' : '' ?> data-project="<?= $ticket['projectid'] ?>" value="<?= $ticket['ticketid'] ?>"><?= get_ticket_label($dbc, $ticket) ?></option>
					<?php } ?>
				</select></label></li><?php } ?>
			<?php if($field_name == 'Project') { ?><li>
					<script>
					function filter_tickets(projectid) {
						$('[name=work_order] option').each(function() {
							$(this).show();
							if($(this).data('project') != projectid && projectid > 0) {
								$(this).hide();
							}
						});
						$('[name=work_order]').trigger('change.select2');
					}
					$(document).ready(function() {
						$('[name=projectid]').change(function() {
							filter_tickets(this.value);
						});
					});
					</script>
					<label class="super-label"><?= PROJECT_TILE ?> <select name="projectid" data-placeholder="Select <?= PROJECT_TILE ?>" class="form-control chosen-select-deselect"><option></option>
					<?php $project_list = mysqli_query($dbc, "SELECT `projectid`, `projecttype`, `project_name` FROM `project` WHERE `deleted`=0 AND `status` NOT IN ('Pending','Archive')");
					while($project = mysqli_fetch_assoc($project_list)) { ?>
						<option <?= $project['projectid'] == $expense['projectid'] ? 'selected' : '' ?> value="<?= $project['projectid'] ?>"><?= PROJECT_TILE ?> #<?= $project['projectid'] ?>: <?= $project['project_name'] ?></option>
					<?php } ?>
				</select></label></li><?php } ?>
			<?php if($field_name == 'Vendor') { ?><li><label class="super-label">Vendor Name <input name="vendor" value="<?= $expense['vendor'] ?>" class="form-control"></label></li><?php } ?>
			<?php if($field_name == 'Province') { ?><li>
				<label class="super-label">Province <select name="province" class="chosen-select-deselect"><?= ($cat_amount == 0 ? '<option></option>' : '') ?>
					<option <?= ($province == '--' ? 'selected' : '') ?> data-gst="0" data-pst="0" data-hst="0" value="--">N/A</option>
					<?php if($cat_amount == 0) {
						foreach($province_list as $province_data) {
							$data = explode('*',$province_data);
							echo "<option data-gst='".$data[1]."' data-pst='".$data[2]."' data-hst='".$data[3]."' ".($expense['province'] == $data[0] ? 'selected' : '');
							echo " value='".$data[0]."'>".$data[0]."</option>";
						}
					} ?>
					</select></label>
			</li><?php } ?>
			<?php if($field_name == 'Country') { ?><li><label class="super-label">Country <input type="text" name="country" value="<?= $expense['country'] ?>" class="form-control"></label></li><?php } ?>
			<?php if($field_name == 'Category') { ?>
				<li>
					<script>
					function set_category_fields(category) {
						$('li,#receipt_field,#amt-fields input').show();
						if(category != '') {
							$.ajax({
								url: 'inbox_ajax.php?action=category_fields',
								method: 'POST',
								data: { category: category },
								success: function(response) {
									var field_list = response.split(',');
									if(!field_list.includes('Description')) {
										$('[name=description]').closest('li').hide();
									}
									if(!field_list.includes('Country')) {
										$('[name=country]').closest('li').hide();
									}
									if(!field_list.includes('Province')) {
										$('[name=province]').closest('li').hide();
									}
									if(!field_list.includes('Exchange')) {
										$('[name=currency]').closest('label').hide();
									}
									if(!field_list.includes('Date')) {
										$('[name=ex_date]').closest('li').hide();
									}
									if(!field_list.includes('Work Order')) {
										$('[name=work_order]').closest('li').hide();
									}
									if(!field_list.includes('Vendor')) {
										$('[name=vendor]').closest('li').hide();
									}
									if(!field_list.includes('Tips')) {
										$('[name=tips]').hide();
									}
									if(!field_list.includes('Third Tax')) {
										$('[name=hst').hide();
									}
									if(!field_list.includes('Local Tax')) {
										$('[name=pst').hide();
									}
									if(!field_list.includes('Tax')) {
										$('[name=tax').hide();
									}
									if(!field_list.includes('Reimburse')) {
										$('[name=reimburse]').closest('li').hide();
									}
									if(!field_list.includes('Receipt')) {
										$('#receipt_field').hide();
									}
								}
							});
						}
					}
					$(document).ready(function() {
						$('[name=category]').change(filter_categories);
						$('[name=heading]').change(heading_set);
					});
					function filter_categories() {
						var category = this.value;
						$('[name=heading] option').hide().filter(function() { return $(this).data('category') == category || category == ''; }).show();
						$('[name=heading]').trigger('change.select2');
						set_category_fields(category);
					}
					function heading_set(option) {
						var option = $(this).find('option:selected');
						if(option.data('amt') > 0) {
							$('[name=amount]').prop('readonly','readonly').val(option.data('amt')).change();
						}
						if(option.data('catgory') != '') {
							$('[name=category]').val(option.data('category')).trigger('change.select2');
						}
						set_category_fields(option.data('catgory'));
					}
					</script>
					<label for="category-box" class="super-label">Category
						<select name="category"class="chosen-select-deselect above-label" data-placeholder="Select a Category" id="category-box">
						<option></option>
						<?php foreach($category_list as $ec_code => $category) {
							echo "<option ".($expense['category'] == $category ? 'selected' : '')." value='".$category."'>$ec_code</option>";
						} ?>
						</select></label>
				</li><?php } ?>
			<?php if($field_name == 'Heading') { ?><li>
					<label for="heading-box" class="super-label">Heading
						<select name="heading"class="chosen-select-deselect above-label" data-placeholder="Select a Heading" id="heading-box">
						<option></option>
						<?php foreach($heading_list as $gl_code => $heading) {
							echo "<option data-category='".$heading[2]."' data-amt='".$heading[1]."' ".($expense['title'] == $heading[0] ? 'selected' : '')." value='".$heading[0]."'>$gl_code</option>";
						} ?>
						</select></label></li>
				<!--<?php if(in_array('Type',$field_config)) { ?><li><label class="super-label">Expense Type <select data-placeholder="Select a Type" name="type" class="chosen-select-deselect form-control">
					<option value=""></option>
					<?php if (strpos($value_config, ','."Flight".',') !== FALSE) {
						echo '<option '.($expense['type'] == 'Flight'?'selected':'').' value="Flight">Flight</option>';
					}
					if (strpos($value_config, ','."Hotel".',') !== FALSE) { ?>
						<option <?php if($expense['type'] == 'Hotel') { echo 'selected'; } ?> value="Hotel">Hotel</option>
					<?php }
					if (strpos($value_config, ','."Breakfast".',') !== FALSE) { ?>
						<option <?php if($expense['type'] == 'Breakfast') { echo 'selected'; } ?> value="Breakfast">Breakfast</option>
					<?php }
					if (strpos($value_config, ','."Lunch".',') !== FALSE) { ?>
						<option <?php if($expense['type'] == 'Lunch') { echo 'selected'; } ?> value="Lunch">Lunch</option>
					<?php }
					if (strpos($value_config, ','."Dinner".',') !== FALSE) { ?>
						<option <?php if($expense['type'] == 'Dinner') { echo 'selected'; } ?> value="Dinner">Dinner</option>
					<?php }
					if (strpos($value_config, ','."Beverages".',') !== FALSE) { ?>
						<option <?php if($expense['type'] == 'Drink') { echo 'selected'; } ?> value="Drink">Beverages</option>
					<?php }
					if (strpos($value_config, ','."Transportation".',') !== FALSE) { ?>
						<option <?php if($expense['type'] == 'Transportation') { echo 'selected'; } ?> value="Transportation">Transportation</option>
					<?php }
					if (strpos($value_config, ','."Entertainment".',') !== FALSE) { ?>
						<option <?php if($expense['type'] == 'Entertainment') { echo 'selected'; } ?> value="Entertainment">Entertainment</option>
					<?php }
					if (strpos($value_config, ','."Gas".',') !== FALSE) { ?>
						<option <?php if($expense['type'] == 'Gas') { echo 'selected'; } ?> value="Gas">Gas</option>
					<?php }
					if (strpos($value_config, ','."Misc".',') !== FALSE) { ?>
						<option <?php if($expense['type'] == 'Misc') { echo 'selected'; } ?> value="Misc">Misc</option>
					<?php }
					$w5 = explode(',', $expense_types);
					foreach($w5 as $key=>$val) {
						echo '<option '.($val == $expense['type'] ? 'selected' : '').' value="'.$val.'">'.$val.'</option>';
					} ?>
					</select></label></li><?php } ?>-->
			<?php } ?>
			<?php if($field_name == 'Reimburse') { ?><li><label class="checkbox-control" style="font-size: 0.8em;"><input type="checkbox" name="reimburse" value=1> Eligible for Reimbursement</label></li><?php } ?>
		<?php } ?>
	</ul>
</div>
<?php if(in_array('Receipt',$field_config)) { ?>
	<div class="col-sm-6" style="text-align: center;" id="receipt_field">
		<img id="receipt_view" class="expense-receipt-view" src="<?= ($expense['ex_file'] == '' ? WEBSITE_URL.'/img/defaults/no_receipt.png' : $expense['ex_file']) ?>" alt="Your Receipt" />
		<input type="file" class="button-only" name="ex_file" style="display:none;" onchange="receipt_chosen(this);" accept="image/*,application/pdf">
		<div class="receipt_name"><?= $receipt ?></div>
		<span class="popover-examples list-inline">&nbsp;
		<a  data-toggle="tooltip" data-placement="top" title="The receipt file should be an image. You may also upload a pdf file, however, it will be converted to an image when you upload it. If it is not an image or a pdf, it will not be saved."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
		</span>
		<button class="btn brand-btn" onclick="$('[name=ex_file]').click(); return false;">Click here to upload a receipt image</button>
		<script>
		function receipt_chosen(input) {
			if (input.files && input.files[0]) {
				if(input.files[0].name.substring(input.files[0].name.length - 4) != '.pdf') {
					var reader = new FileReader();

					reader.onload = function (e) {
						$('#receipt_view')
							.attr('src', e.target.result)
							.width('75%');
					};

					reader.readAsDataURL(input.files[0]);
				} else {
					$('#receipt_view').attr('src', '<?= WEBSITE_URL ?>/img/defaults/no_receipt.png');
				}
				$('.receipt_name').html("Attached: "+input.files[0].name);
			} else {
				$('#receipt_view').attr('src', '<?= WEBSITE_URL ?>/img/defaults/no_receipt.png');
				$('.receipt_name').html("No file selected.");
			}
		}
		</script>
	</div>
<?php } ?>
<div class="clearfix"></div>
<hr>
<div class='form-group' style='padding: 1em;'>
<?php if($approvals == 1) { ?>
	<?php if($_GET['edit'] > 0 && mysqli_num_rows($warnings) == 0) { ?>
		<button name="submit" value="pay" class="btn brand-btn pull-right offset-left-5">Mark as Paid</button>
		<button name="submit" value="decline" class="btn brand-btn pull-right offset-left-5">Decline</button>
		<button name="submit" value="approve" class="btn brand-btn pull-right offset-left-5">Approve</button>
	<?php } else {
		$block = false;
		$warn = false;
		$warning_list = [];
		while($warning = mysqli_fetch_array($warnings)) {
			if($warning['type'] == 'Block') {
				$block = true;
				$warning_list[] = '<img src="'.WEBSITE_URL.'/img/icons/ROOK-status-error.png" style="height: 2em; margin: 0.5em;"> '.$warning['name'];
			} else {
				$warn = true;
				$warning_list[] = '<img src="'.WEBSITE_URL.'/img/icons/ROOK-status-warning.png" style="height: 2em; margin: 0.5em;"> '.$warning['name'];
			}
		}
		if($block) {
			echo "This expense cannot be appoved or paid until the errors have been resolved:<br />";
			echo implode('<br />',$warning_list).'';
		} else if ($warn) { ?>
			This expense may be approved and paid, however, it has the following warnings:<br />
			<?php echo implode('<br />',$warning_list); ?>
			<div class="pull-right offset-left-5">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to mark this Expense as paid."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <button name="submit" value="pay" class="btn brand-btn">Mark as Paid</button>
            </div>
            <div class="pull-right offset-left-5">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to approve the Expense."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <button name="submit" value="approve" class="btn brand-btn">Approve</button>
            </div>
		<?php }
	} ?>
    <div class="pull-right offset-left-5">
        <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save the Expense (this will not approve the expense)."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        <button name="submit" value="send" class="btn brand-btn">Save</button>
    </div>
<?php } else { ?>
	<button name="submit" value="send" class="btn brand-btn pull-right">Send</button>
<?php } ?>
    <div class="pull-right">
        <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to return to the Expense dashboard (your changes will not be saved)"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        <a href="?filter_id=pending" class="btn brand-btn">Cancel</a>
    </div>

</div>
<div class="clearfix" style="margin-bottom: 1em;"></div>
</form>
</div>
<div style="display:none;"><?php include('../footer.php'); ?></div>
