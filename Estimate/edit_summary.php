<?php include_once('../include.php');
checkAuthorised('estimate');
error_reporting(0);
if(!isset($estimate)) {
	$estimateid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
	$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));
	$rates = [];
	$query = mysqli_query($dbc, "SELECT `heading` FROM `rate_card` WHERE `estimateid`='$estimateid' GROUP BY `rate_card`");
	if(mysqli_num_rows($query) > 0) {
		while($row = mysqli_fetch_array($query)) {
			$rates[bin2hex($row[0])] = explode(':',$row[0]);
		}
	} else {
		$rates[''] = '';
	}
	$current_rate = $_GET['rate'];
	$scope_list = [];
	$query = mysqli_query($dbc, "SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `rate_card`='".implode(':',$rates[$current_rate])."' AND `deleted`=0 GROUP BY `heading` ORDER BY MIN(`sort_order`)");
	if($query->num_rows > 0) {
		while($row = mysqli_fetch_array($query)) {
			$scope_list[config_safe_str($row[0])] = $row[0];
		}
	} else {
		$scope_list['scope_1'] = 'Scope 1';
	}
	$scope_id = filter_var($_GET['status'],FILTER_SANITIZE_STRING);
	$scope = $scope_list[$scope_id];
	$headings = [];
	$query = mysqli_query($dbc, "SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `scope_name`='$scope' AND `rate_card`='".implode(':',$rates[$current_rate])."' AND `deleted`=0 GROUP BY `heading` ORDER BY MIN(`sort_order`)");
	while($row = mysqli_fetch_array($query)) {
		$headings[preg_replace('/[^a-z]*/','',strtolower($row[0]))] = $row[0];
	}
	$us_exchange = json_decode(file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'), TRUE);
}
$us_rate = $us_exchange['observations'][count($us_exchange['observations']) - 1]['FXUSDCAD']['v'];
$summary_details = mysqli_query($dbc, "SELECT `cost`,`price`,`pricing`,`qty`,`retail` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `scope_name`='$scope' AND `rate_card`='".implode(':',$rates[$current_rate])."'");
$summary = mysqli_fetch_assoc(mysqli_query("SELECT `discount`, `discount_type` FROM `estimate` WHERE `estimateid`='$estimateid'"));
$total_price = 0;
$total_cost = 0;
while($scope_line_item = $summary_details->fetch_assoc()) {
	$line_cost = $line_price = 0;
	if($scope_line_item['pricing'] == 'usd_cpu') {
		$line_cost = ($scope_line_item['cost'] * $us_rate * $scope_line_item['qty']);
	} else {
		$line_cost = $scope_line_item['cost'] * $scope_line_item['qty'];
	}
	if(empty($scope_line_item['retail'])) {
		$line_price = $line_cost;
	} else {
		$line_price = $scope_line_item['retail'];
	}
	$total_cost += $line_cost;
	$total_price += $line_price;
}
$profit = $total_price - $total_cost;
$margin = $profit / $total_cost * 100; ?>
<script>
var total_cost = '0<?= $total_cost ?>';
var total_price = '0<?= $total_price - ($summary['discount_type'] == '%' ? ($summary['discount'] * $total_price / 100) : $summary['discount']) ?>';
</script>
<a name="summary"></a>
<div class="form-horizontal col-sm-12" data-tab-name="summary">
	<h3>Summary</h3>
	<div class="form-group">
		<label class="col-sm-4">Discount:</label>
		<div class="col-sm-4" id="discount_num"><input type="number" step="any" name="discount" value="<?= $estimate['discount'] ?>" data-table="estimate" data-id="<?= $estimate['estimateid'] ?>" data-id-field="estimateid" class="form-control discount" placeholder="Discount"></div>
		<div class="col-sm-4" id="discount_type">
			<select name="discount_type" class="chosen-select-deselect discount_type" data-table="estimate" data-id="<?= $estimate['estimateid'] ?>" data-id-field="estimateid" data-placeholder="Discount Type"><option />
				<option <?= $estimate['discount_type'] == '%' ? '' : 'selected' ?> value="$">$ Discount</option>
				<option <?= $estimate['discount_type'] == '%' ? 'selected' : '' ?> value="%">% Discount</option>
			</select></div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Total Cost:</label>
		<div class="col-sm-8" id="total_cost">$<?= number_format($total_cost,2) ?></div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Total $ Profit:</label>
		<div class="col-sm-8" id="total_profit">$<?= number_format($profit,2) ?></div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Total % Margin:</label>
		<div class="col-sm-8" id="total_margin"><?= number_format($margin,2) ?>%</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Total Price:</label>
		<div class="col-sm-8" id="total_price">$<?= number_format($total_price,2) ?></div>
	</div>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_summary.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>