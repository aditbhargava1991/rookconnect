<?php include_once('../include.php');
checkAuthorised('estimate');
error_reporting(0);
$us_exchange = json_decode(file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'), TRUE);
$us_rate = $us_exchange['observations'][count($us_exchange['observations']) - 1]['FXUSDCAD']['v'];
$us_rate_no_auto = get_config($dbc, 'disable_us_auto_convert');
$estimateid = filter_var($_GET['financials'],FILTER_SANITIZE_STRING);
$estimate = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));
$scope = mysqli_query($dbc, "SELECT * FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 ORDER BY `sort_order`");
$view = filter_var($_GET['view'],FILTER_VALIDATE_INT);
$total_cost = $total_price = $total_us_cost = $total_us_price = 0; ?>
<script>
$(document).ready(function() {
    $(window).resize(function() {
        available = Math.floor($(window).innerHeight() - $('.main-screen').offset().top - $('footer:visible').outerHeight() - 80);
		    if(available > 300) {
            $('.financials').each(function() { $(this).attr('style',$(this).attr('style')+';height:'+available+'px !important;'); })
		    }
	  }).resize();
      
    $('input[name=quantity]').on('change', function() {
        var arr = $(this).attr('id').split('_');
        var row = arr[1];
        var db_id = $(this).data('id');
        var quantity = $(this).val();
        var cost = $(this).closest('tr').find('#cost_'+row).val().replace(/[^\d.]/g,'');
        var price = $(this).closest('tr').find('#price_'+row).val().replace(/[^\d.]/g,'');
        calcTotals('quantity', quantity, quantity, cost, price, row, db_id);
    });
    
    $('input[name=margin]').on('change', function() {
        var arr = $(this).attr('id').split('_');
        var row = arr[1];
        var db_id = $(this).data('id');
        var margin = $(this).val();
        var quantity = $(this).closest('tr').find('#quantity_'+row).val();
        var cost = $(this).closest('tr').find('#cost_'+row).val().replace(/[^\d.]/g,'');
        var price = $(this).closest('tr').find('#price_'+row).val().replace(/[^\d.]/g,'');
        calcTotals('margin', margin, quantity, cost, price, row, db_id);
    });
    
    $('input[name=profit]').on('change', function() {
        var arr = $(this).attr('id').split('_');
        var row = arr[1];
        var db_id = $(this).data('id');
        var profit = $(this).val();
        var quantity = $(this).closest('tr').find('#quantity_'+row).val();
        var cost = $(this).closest('tr').find('#cost_'+row).val().replace(/[^\d.]/g,'');
        var price = $(this).closest('tr').find('#price_'+row).val().replace(/[^\d.]/g,'');
        calcTotals('profit', profit, quantity, cost, price, row, db_id);
    });
});

function calcTotals(changed, changed_val, qty, cost, price, row, db_id) {
    var view = '<?= filter_var($_GET['view'],FILTER_VALIDATE_INT) ?>';
    var estimateid = '<?= $_GET['edit']; ?>';
    var quantity = qty;
    var line_total_cost = quantity * cost;
    var line_profit = 0;
    var line_margin = 0;
    var line_total = 0;
    
    if (changed=='quantity') {
        line_total = (quantity * price).toFixed(2);
        line_profit = (line_total - line_total_cost).toFixed(2);
        if ( line_total_cost == 0 ) {
            line_margin = 100;
        } else {
            line_margin = (((line_total - line_total_cost) / line_total_cost) * 100).toFixed(2);
        }
    }
    
    if (changed=='margin') {
        line_margin = changed_val;
        line_total = (((quantity * cost) * (1 + (line_margin/100)))).toFixed(2);
        line_profit = (line_total - line_total_cost).toFixed(2);
    }
    
    if (changed=='profit') {
        profit = changed_val;
        line_profit = profit;
        line_total = ((parseFloat(quantity * cost)) + parseFloat(profit)).toFixed(2);
        if ( line_total_cost == 0 ) {
            line_margin = profit;
        } else {
            line_margin = (((line_total - line_total_cost) / line_total_cost) * 100).toFixed(2);
        }
    }
    
    $('#margin_'+row+', #marginscope_'+row).val(line_margin);
    $('#profit_'+row+', #profitscope_'+row).val(line_profit);
	if(quantity > 0) {
		$('#price_'+row+', #pricescope_'+row).val(round2Fixed(line_total / quantity));
	}
    $('#total_'+row+', #totalscope_'+row).val(line_total);
    
    $.ajax({
        url: 'estimates_ajax.php?action=cost_analysis',
        method: 'GET',
        data: {
            id: db_id,
            estimateid: estimateid,
            qty: quantity,
            profit: line_profit,
            margin: line_margin,
            price: line_total / quantity,
            retail: line_total,
        },
        success: function(response){
            arr = response.split('*#*');
            total_margin = arr[0];
            total_profit = arr[1];
            total_total = arr[2];
            $('.total_margin').text(total_margin);
            $('.total_profit').text(total_profit);
            $('.total_total').text(total_total);
            if ( view==2 ) {
                window.location.reload();
            }
        }
    });
}
</script>
<div id="no-more-tables" class="col-sm-12 fit-to-screen-full overflow-y">
	<div class='by-item' style="display:<?= empty($view) || $view=='1' ? 'block' : 'none' ?>;">
		<h4 class="pull-left">Profit & Loss by Item for <a href="?edit=<?= $estimate['estimateid'] ?>"><?= $estimate['estimate_name'] != '' ? $estimate['estimate_name'] : ESTIMATE_TILE.' #'.$estimateid ?><img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a></h4>
		<div class="pull-right gap-top">
            <a href="?edit=<?=$estimateid?>&tab=analysis&financials=<?=$estimateid?>&view=1" class="btn brand-btn pull-left active_tab">View by Item</a>
            <a href="?edit=<?=$estimateid?>&tab=analysis&financials=<?=$estimateid?>&view=2" class="btn brand-btn pull-left gap-left">View by Heading</a><?php
            $query = mysqli_query($dbc, "SELECT `scope_name` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY `scope_name`");
            if ( $query->num_rows > 1 ) {
                $i = 3;
                while ( $row=mysqli_fetch_assoc($query) ) {
                    echo '<a href="?edit='.$estimateid.'&tab=analysis&financials='.$estimateid.'&view='.$i.'&scope='.$row['scope_name'].'" class="btn brand-btn pull-left gap-left">View By '. $row['scope_name'] .'</a>';
                    $i++;
                }
            } ?>
            <div class="clearfix gap-bottom"></div>
        </div>
        
        <table class="table table-bordered">
			<tr class="hidden-xs hidden-sm">
                <th>Scope Name</th>
				<th>Heading</th>
				<th>Type</th>
				<!--<th>U of M</th>-->
				<th>Quantity</th>
				<th>Cost</th>
				<th>% Margin</th>
				<th>$ Profit</th>
				<th><?php //echo ESTIMATE_TILE ?> Unit Price</th>
				<th>Total</th>
			</tr>
			<?php
            $i = 1;
            while($scope_line = mysqli_fetch_assoc($scope)) {
				$scope_description = $scope_line['description'];
				if($scope_line['src_table'] == 'equipment' && $scope_line['src_id'] > 0) {
					$scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `equipmentid`='{$scope_line['src_id']}'"))['label'];
				} else if($scope_line['src_table'] == 'inventory' && $scope_line['src_id'] > 0) {
					$scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) label FROM `inventory` WHERE `inventoryid`='{$scope_line['src_id']}'"))['label'];
				} else if($scope_line['src_table'] == 'labour' && $scope_line['src_id'] > 0) {
					$scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`labour_type`,''),' ',IFNULL(`category`,''),' ',IFNULL(`heading`,''),' ',IFNULL(`name`,'')) label FROM `labour` WHERE `labourid`='{$scope_line['src_id']}'"))['label'];
				} else if($scope_line['src_table'] == 'material' && $scope_line['src_id'] > 0) {
					$scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`sub_category`,''),' ',IFNULL(`name`,'')) label FROM `material` WHERE `materialid`='{$scope_line['src_id']}'"))['label'];
				} else if($scope_line['src_table'] == 'position' && $scope_line['src_id'] > 0) {
					$scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` label FROM `positions` WHERE `position_id`='{$scope_line['src_id']}'"))['label'];
				} else if($scope_line['src_table'] == 'products' && $scope_line['src_id'] > 0) {
					$scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `products` WHERE `productid`='{$scope_line['src_id']}'"))['label'];
				} else if($scope_line['src_table'] == 'services' && $scope_line['src_id'] > 0) {
					$scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `services` WHERE `serviceid`='{$scope_line['src_id']}'"))['label'];
				} else if($scope_line['src_table'] == 'vpl' && $scope_line['src_id'] > 0) {
					$scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`product_name`,'')) label FROM `vendor_price_list` WHERE `inventoryid`='{$scope_line['src_id']}'"))['label'];
				} else if($scope_line['src_table'] != 'miscellaneous' && $scope_line['src_id'] > 0) {
					$scope_description = get_contact($dbc, $scope_line['src_id']);
				}
				if($us_rate_no_auto != 'true' && $scope_line['pricing'] == 'usd_cpu' && !($scope_line['price'] > 0)) {
					$scope_line['price'] = $scope_line['cost'] * $us_rate;
					$scope_line['retail'] = $scope_line['price'] * $scope_line['qty'];
				} else if(!($scope_line['retail'] > 0)) {
					$scope_line['retail'] = $scope_line['price'] * $scope_line['qty'];
				}
				if($us_rate_no_auto != 'true' && $scope_line['pricing'] == 'usd_cpu') {
					$scope_line['cost'] = $scope_line['cost'] * $us_rate;
				}
				if(!($scope_line['profit'] > 0)) {
					$scope_line['profit'] = ($scope_line['price'] - $scope_line['cost']) * $scope_line['qty'];
					$scope_line['margin'] = ($scope_line['price'] - $scope_line['cost']) / $scope_line['cost'] * 100;
				} ?>
				<tr>
                    <td data-title="Heading"><?= $scope_line['scope_name'] ?></td>
					<td data-title="Heading"><?= $scope_line['heading'] ?></td>
					<td data-title="Description" colspan="<?= $scope_line['src_table'] == 'notes' ? 7 : 1 ?>"><?= html_entity_decode($scope_description) ?></td>
					<!--<td data-title="U of M"><?php //echo $scope_line['uom'] ?></td>-->
					<?php if($scope_line['src_table'] != 'notes') { ?>
						<td data-title="Quantity" align="right"><input type="number" name="quantity" id="quantity_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right quantity" value="<?= number_format($scope_line['qty'],0, '.', '') ?>" min="1" /></td>
						<td data-title="Unit Cost" align="right"><input type="text" name="cost" id="cost_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right cost" readonly value="<?= number_format($scope_line['cost'], 2, '.', '').($scope_line['pricing'] == 'usd_cpu' ? ' CAD Approx' : '') ?>" /></td>
						<td data-title="Margin" align="right"><input type="text" name="margin" id="margin_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right margin" value="<?= number_format($scope_line['margin'], 2, '.', '') ?>" /></td>
						<td data-title="Profit" align="right"><input type="text" name="profit" id="profit_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right profit" value="<?= number_format($scope_line['profit'], 2, '.', '') ?>" /></td>
						<td data-title="Estimate Price" align="right"><input type="text" name="price" id="price_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right price" readonly value="<?= number_format($scope_line['price'], 2, '.', '') ?>" /></td>
						<td data-title="Total" align="right"><input type="text" name="total" id="totalscope_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right total" readonly value="<?= number_format($scope_line['retail'], 2, '.', '') ?>" /></td>
					<?php } ?>
				</tr>
				<?php if($us_rate_no_auto == 'true' && $scope_line['pricing'] == 'usd_cpue') {
					$total_us_cost += $scope_line['cost'] * $scope_line['qty'];
					$total_us_price += $scope_line['retail'];
				} else {
					$total_cost += $scope_line['cost'] * $scope_line['qty'];
					$total_price += $scope_line['retail'];
				}
                
                $i++;
			} ?>
			<tr style="font-weight:bold;">
				<td data-title="" colspan="4">Total <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</td>
				<td data-title="Total Cost" align="right">$<?= number_format($total_cost,2, '.', '') ?></td>
				<td data-title="Average Margin" align="right" class="total_margin"><?= number_format(($total_cost > 0 ? ($total_price - $total_cost) / $total_cost * 100 : 0),2, '.', '') ?>%</td>
				<td data-title="Total Profit" align="right" class="total_profit">$<?= number_format($total_price - $total_cost,2, '.', '') ?></td>
				<td data-title=""></td>
				<td data-title="Total" align="right" class="total_total">$<?= number_format($total_price,2, '.', '') ?></td>
			</tr>
			<?php if($total_us_cost + $total_us_price > 0) { ?>
				<tr style="font-weight:bold;">
					<td data-title="" colspan="4">Total USD <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</td>
					<td data-title="Total USD Cost" align="right">$<?= number_format($total_us_cost,2, '.', '') ?> USD</td>
					<td data-title="Average Margin" align="right" class="total_margin"><?= number_format(($total_us_cost > 0 ? ($total_us_price - $total_us_cost) / $total_us_cost * 100 : 0),2, '.', '') ?>%</td>
					<td data-title="Total Profit" align="right" class="total_profit">$<?= number_format($total_us_price - $total_us_cost,2, '.', '') ?></td>
					<td data-title=""></td>
					<td data-title="Total USD" align="right" class="total_total">$<?= number_format($total_us_price,2, '.', '') ?> USD</td>
				</tr>
			<?php } ?>
		</table>
	</div>
	
    
    <div class='by-heading' style="display:<?= !empty($view) && $view=='2' ? 'block' : 'none' ?>;">
		<h4 class="pull-left">Profit & Loss by Heading for <a href="?edit=<?= $estimate['estimateid'] ?>"><?= $estimate['estimate_name'] != '' ? $estimate['estimate_name'] : ESTIMATE_TILE.' #'.$estimateid ?><img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a></h4>
        <div class="pull-right gap-top">
            <a href="?edit=<?=$estimateid?>&tab=analysis&financials=<?=$estimateid?>&view=1" class="btn brand-btn pull-left">View by Item</a>
            <a href="?edit=<?=$estimateid?>&tab=analysis&financials=<?=$estimateid?>&view=2" class="btn brand-btn pull-left gap-left active_tab">View by Heading</a><?php
            $query = mysqli_query($dbc, "SELECT `scope_name` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY `scope_name`");
            if ( $query->num_rows > 1 ) {
                $i = 3;
                while ( $row=mysqli_fetch_assoc($query) ) {
                    echo '<a href="?edit='.$estimateid.'&tab=analysis&financials='.$estimateid.'&view='.$i.'&scope='.$row['scope_name'].'" class="btn brand-btn pull-left gap-left">View By '. $row['scope_name'] .'</a>';
                    $i++;
                }
            } ?>
            <div class="clearfix gap-bottom"></div>
        </div>
        
		<table class="table table-bordered">
			<tr class="hidden-xs hidden-sm">
				<th>Heading</th>
				<th>Cost</th>
				<th>% Margin</th>
				<th>$ Profit</th>
				<th>Total</th>
			</tr><?php
            $total_cost = 0;
			$total_price = 0;
            $scope_headings = mysqli_query($dbc, "SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY `heading` ORDER BY `sort_order`");
			while($scope_heading = mysqli_fetch_assoc($scope_headings)) {
				$cost = 0;
				$price = 0;
				$scope_details = mysqli_query($dbc, "SELECT `cost`,`qty`,`price`,`retail`,`pricing` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0");
				while($scope_line_item = $scope_details->fetch_assoc()) {
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
					$cost += $line_cost;
					$price += $line_price;
				} ?>
				<tr>
					<td data-title="Heading"><?= $scope_heading['heading'] ?></td>
					<td data-title="Cost" align="right">$<?= number_format($cost,2, '.', '') ?></td>
					<td data-title="% Margin" align="right"><?= number_format(($cost > 0 ? ($price - $cost) / $cost * 100 : 0),2, '.', '') ?>%</td>
					<td data-title="$ Profit" align="right">$<?= number_format($price - $cost,2, '.', '') ?></td>
					<td data-title="Total" align="right">$<?= number_format($price,2, '.', '') ?></td>
				</tr><?php
                $total_cost += $cost;
				$total_price += $price;
			} ?>
			<tr style="font-weight:bold;">
				<td data-title="">Total <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</td>
				<td data-title="Total Cost" align="right">$<?= number_format($total_cost,2, '.', '') ?></td>
				<td data-title="Average Margin" align="right"><?= number_format(($total_cost > 0 ? ($total_price - $total_cost) / $total_cost * 100 : 0),2, '.', '') ?>%</td>
				<td data-title="Total Profit" align="right">$<?= number_format($total_price - $total_cost,2, '.', '') ?></td>
				<td data-title="Total" align="right">$<?= number_format($total_price,2, '.', '') ?></td>
			</tr>
		</table><?php
        
        $query_heading = mysqli_query($dbc, "SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY `heading` ORDER BY `sort_order`");
        while ( $heading=mysqli_fetch_assoc($query_heading) ) {
            echo '<h4>'. $heading['heading'] .'</h4>';
            $query_items = mysqli_query($dbc, "SELECT * FROM `estimate_scope` WHERE `heading`='{$heading['heading']}' AND `estimateid`='$estimateid' AND `deleted`=0 ORDER BY `sort_order`");
            
            $i = 1;
            $total_cost = 0;
            $total_price = 0; ?>
            <table class="table table-bordered">
                <tr class="hidden-xs hidden-sm">
                    <th width="12%">Scope Name</th>
                    <th width="20%">Type</th>
                    <!--<th>U of M</th>-->
                    <th>Quantity</th>
                    <th>Cost</th>
                    <th>% Margin</th>
                    <th>$ Profit</th>
                    <th><?php //echo ESTIMATE_TILE ?> Unit Price</th>
                    <th>Total</th>
                </tr><?php
                while($scope_line = mysqli_fetch_assoc($query_items)) {
                    $scope_description = $scope_line['description'];
                    if($scope_line['src_table'] == 'equipment' && $scope_line['src_id'] > 0) {
                        $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `equipmentid`='{$scope_line['src_id']}'"))['label'];
                    } else if($scope_line['src_table'] == 'inventory' && $scope_line['src_id'] > 0) {
                        $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) label FROM `inventory` WHERE `inventoryid`='{$scope_line['src_id']}'"))['label'];
                    } else if($scope_line['src_table'] == 'labour' && $scope_line['src_id'] > 0) {
                        $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`labour_type`,''),' ',IFNULL(`category`,''),' ',IFNULL(`heading`,''),' ',IFNULL(`name`,'')) label FROM `labour` WHERE `labourid`='{$scope_line['src_id']}'"))['label'];
                    } else if($scope_line['src_table'] == 'material' && $scope_line['src_id'] > 0) {
                        $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`sub_category`,''),' ',IFNULL(`name`,'')) label FROM `material` WHERE `materialid`='{$scope_line['src_id']}'"))['label'];
                    } else if($scope_line['src_table'] == 'position' && $scope_line['src_id'] > 0) {
                        $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` label FROM `positions` WHERE `position_id`='{$scope_line['src_id']}'"))['label'];
                    } else if($scope_line['src_table'] == 'products' && $scope_line['src_id'] > 0) {
                        $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `products` WHERE `productid`='{$scope_line['src_id']}'"))['label'];
                    } else if($scope_line['src_table'] == 'services' && $scope_line['src_id'] > 0) {
                        $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `services` WHERE `serviceid`='{$scope_line['src_id']}'"))['label'];
                    } else if($scope_line['src_table'] == 'vpl' && $scope_line['src_id'] > 0) {
                        $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`product_name`,'')) label FROM `vendor_price_list` WHERE `inventoryid`='{$scope_line['src_id']}'"))['label'];
                    } else if($scope_line['src_table'] != 'miscellaneous' && $scope_line['src_id'] > 0) {
                        $scope_description = get_contact($dbc, $scope_line['src_id']);
                    }
					if($scope_line['pricing'] == 'usd_cpu' && !($scope_line['price'] > 0)) {
						$scope_line['price'] = $scope_line['cost'] * $us_rate;
						$scope_line['retail'] = $scope_line['price'] * $scope_line['qty'];
					} else if(!($scope_line['retail'] > 0)) {
						$scope_line['retail'] = $scope_line['price'] * $scope_line['qty'];
					}
					if($scope_line['pricing'] == 'usd_cpu') {
						$scope_line['cost'] = $scope_line['cost'] * $us_rate;
					}
					if(!($scope_line['profit'] > 0)) {
						$scope_line['profit'] = ($scope_line['price'] - $scope_line['cost']) * $scope_line['qty'];
						$scope_line['margin'] = ($scope_line['price'] - $scope_line['cost']) / $scope_line['cost'] * 100;
					} ?>
            
                    <tr>
                        <td data-title="Scope Name"><?= $scope_line['scope_name'] ?></td>
                        <td data-title="Description" colspan="<?= $scope_line['src_table'] == 'notes' ? 7 : 1 ?>"><?= html_entity_decode($scope_description) ?></td>
                        <!--<td data-title="U of M"><?php //echo $scope_line['uom'] ?></td>-->
						<?php if($scope_line['src_table'] != 'notes') { ?>
							<td data-title="Quantity" align="right"><input type="number" name="quantity" id="quantity_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right quantity" value="<?= number_format($scope_line['qty'],0, '.', '') ?>" min="1" /></td>
							<td data-title="Unit Cost" align="right"><input type="text" name="cost" id="cost_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right cost" readonly value="<?= number_format($scope_line['cost'], 2, '.', '').($scope_line['pricing'] == 'usd_cpu' ? ' CAD Approx' : '') ?>" /></td>
							<td data-title="Margin" align="right"><input type="text" name="margin" id="margin_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right margin" value="<?= number_format($scope_line['margin'], 2, '.', '') ?>" /></td>
							<td data-title="Profit" align="right"><input type="text" name="profit" id="profit_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right profit" value="<?= number_format($scope_line['profit'], 2, '.', '') ?>" /></td>
							<td data-title="Estimate Price" align="right"><input type="text" name="price" id="price_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right price" readonly value="<?= number_format($scope_line['price'], 2, '.', '') ?>" /></td>
							<td data-title="Total" align="right"><input type="text" name="total" id="totalscope_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right total" readonly value="<?= number_format($scope_line['retail'], 2, '.', '') ?>" /></td>
						<?php } ?>
                    </tr><?php
                    $total_cost += $scope_line['cost'] * $scope_line['qty'];
                    $total_price += $scope_line['retail'];
                    $i++;
                } ?>
                <tr style="font-weight:bold;">
                    <td data-title="" colspan="3">Total <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price by <?= $heading['heading'] ?></td>
                    <td data-title="Total Cost" align="right">$<?= number_format($total_cost,2, '.', '') ?></td>
                    <td data-title="Average Margin" align="right" class="total_margin"><?= number_format(($total_cost > 0 ? ($total_price - $total_cost) / $total_cost * 100 : 0),2, '.', '') ?>%</td>
                    <td data-title="Total Profit" align="right" class="total_profit">$<?= number_format($total_price - $total_cost,2, '.', '') ?></td>
                    <td data-title=""></td>
                    <td data-title="Total" align="right" class="total_total">$<?= number_format($total_price,2, '.', '') ?></td>
                </tr>
            </table><?php
        } ?>
	</div><!-- by-heading -->
    
    
    <?php $url_scope = filter_var($_GET['scope'], FILTER_SANITIZE_STRING); ?>
    <div class='by-scope' style="display:<?= !empty($url_scope) ? 'block' : 'none' ?>;">
        <h4 class="pull-left">Profit & Loss by <?= $url_scope ?> for <a href="?edit=<?= $estimate['estimateid'] ?>"><?= $estimate['estimate_name'] != '' ? $estimate['estimate_name'] : ESTIMATE_TILE.' #'.$estimateid ?><img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a></h4>
        <div class="pull-right gap-top">
            <a href="?edit=<?=$estimateid?>&tab=analysis&financials=<?=$estimateid?>&view=1" class="btn brand-btn pull-left">View by Item</a>
            <a href="?edit=<?=$estimateid?>&tab=analysis&financials=<?=$estimateid?>&view=2" class="btn brand-btn pull-left gap-left">View by Heading</a><?php
            $query = mysqli_query($dbc, "SELECT `scope_name` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY `scope_name`");
            if ( $query->num_rows > 1 ) {
                $i = 3;
                while ( $row=mysqli_fetch_assoc($query) ) {
                    echo '<a href="?edit='.$estimateid.'&tab=analysis&financials='.$estimateid.'&view='.$i.'&scope='.$row['scope_name'].'" class="btn brand-btn pull-left gap-left '.($row['scope_name']==$url_scope ? 'active_tab' : '').'">View By '. $row['scope_name'] .'</a>';
                    $i++;
                }
            } ?>
            <div class="clearfix gap-bottom"></div>
        </div>
        
        <table class="table table-bordered">
            <tr class="hidden-xs hidden-sm">
                <th>Heading</th>
                <th>Type</th>
                <!--<th>U of M</th>-->
                <th>Quantity</th>
                <th>Cost</th>
                <th>% Margin</th>
                <th>$ Profit</th>
                <th><?php //echo ESTIMATE_TILE ?> Unit Price</th>
                <th>Total</th>
            </tr>
            <?php
            $i = 1;
            $total_cost = 0;
			$total_price = 0;
            $scope = mysqli_query($dbc, "SELECT * FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 AND `scope_name`='$url_scope' ORDER BY `sort_order`");
            while($scope_line = mysqli_fetch_assoc($scope)) {
                $scope_description = $scope_line['description'];
                if($scope_line['src_table'] == 'equipment' && $scope_line['src_id'] > 0) {
                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `equipmentid`='{$scope_line['src_id']}'"))['label'];
                } else if($scope_line['src_table'] == 'inventory' && $scope_line['src_id'] > 0) {
                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) label FROM `inventory` WHERE `inventoryid`='{$scope_line['src_id']}'"))['label'];
                } else if($scope_line['src_table'] == 'labour' && $scope_line['src_id'] > 0) {
                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`labour_type`,''),' ',IFNULL(`category`,''),' ',IFNULL(`heading`,''),' ',IFNULL(`name`,'')) label FROM `labour` WHERE `labourid`='{$scope_line['src_id']}'"))['label'];
                } else if($scope_line['src_table'] == 'material' && $scope_line['src_id'] > 0) {
                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`sub_category`,''),' ',IFNULL(`name`,'')) label FROM `material` WHERE `materialid`='{$scope_line['src_id']}'"))['label'];
                } else if($scope_line['src_table'] == 'position' && $scope_line['src_id'] > 0) {
                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` label FROM `positions` WHERE `position_id`='{$scope_line['src_id']}'"))['label'];
                } else if($scope_line['src_table'] == 'products' && $scope_line['src_id'] > 0) {
                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `products` WHERE `productid`='{$scope_line['src_id']}'"))['label'];
                } else if($scope_line['src_table'] == 'services' && $scope_line['src_id'] > 0) {
                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `services` WHERE `serviceid`='{$scope_line['src_id']}'"))['label'];
                } else if($scope_line['src_table'] == 'vpl' && $scope_line['src_id'] > 0) {
                    $scope_description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`product_name`,'')) label FROM `vendor_price_list` WHERE `inventoryid`='{$scope_line['src_id']}'"))['label'];
                } else if($scope_line['src_table'] != 'miscellaneous' && $scope_line['src_id'] > 0) {
                    $scope_description = get_contact($dbc, $scope_line['src_id']);
                }
				if($scope_line['pricing'] == 'usd_cpu' && !($scope_line['price'] > 0)) {
					$scope_line['price'] = $scope_line['cost'] * $us_rate;
					$scope_line['retail'] = $scope_line['price'] * $scope_line['qty'];
				} else if(!($scope_line['retail'] > 0)) {
					$scope_line['retail'] = $scope_line['price'] * $scope_line['qty'];
				}
				if($scope_line['pricing'] == 'usd_cpu') {
					$scope_line['cost'] = $scope_line['cost'] * $us_rate;
				}
				if(!($scope_line['profit'] > 0)) {
					$scope_line['profit'] = ($scope_line['price'] - $scope_line['cost']) * $scope_line['qty'];
					$scope_line['margin'] = ($scope_line['price'] - $scope_line['cost']) / $scope_line['cost'] * 100;
				} ?>
                <tr>
                    <td data-title="Heading"><?= $scope_line['heading'] ?></td>
                    <td data-title="Description" colspan="<?= $scope_line['src_table'] == 'notes' ? 7 : 1 ?>"><?= html_entity_decode($scope_description) ?></td>
                    <!--<td data-title="U of M"><?php //echo $scope_line['uom'] ?></td>-->
					<?php if($scope_line['src_table'] != 'notes') { ?>
						<td data-title="Quantity" align="right"><input type="number" name="quantity" id="quantity_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right quantity" value="<?= number_format($scope_line['qty'],0, '.', '') ?>" min="1" /></td>
						<td data-title="Unit Cost" align="right"><input type="text" name="cost" id="cost_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right cost" readonly value="<?= number_format($scope_line['cost'], 2, '.', '').($scope_line['pricing'] == 'usd_cpu' ? ' CAD Approx' : '') ?>" /></td>
						<td data-title="Margin" align="right"><input type="text" name="margin" id="margin_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right margin" value="<?= number_format($scope_line['margin'], 2, '.', '') ?>" /></td>
						<td data-title="Profit" align="right"><input type="text" name="profit" id="profit_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right profit" value="<?= number_format($scope_line['profit'], 2, '.', '') ?>" /></td>
						<td data-title="Estimate Price" align="right"><input type="text" name="price" id="price_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right price" readonly value="<?= number_format($scope_line['price'], 2, '.', '') ?>" /></td>
						<td data-title="Total" align="right"><input type="text" name="total" id="total_<?= $i ?>" data-id="<?= $scope_line['id'] ?>" class="form-control text-right total" readonly value="<?= number_format($scope_line['retail'], 2, '.', '') ?>" /></td>
					<?php } ?>
                </tr>
                <?php $total_cost += $scope_line['cost'] * $scope_line['qty'];
                $total_price += $scope_line['retail'];
                
                $i++;
            } ?>
            <tr style="font-weight:bold;">
                <td data-title="" colspan="3">Total <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price for <?= $url_scope ?></td>
                <td data-title="Total Cost" align="right">$<?= number_format($total_cost,2, '.', '') ?></td>
                <td data-title="Average Margin" align="right" class="total_margin"><?= number_format(($total_cost > 0 ? ($total_price - $total_cost) / $total_cost * 100 : 0),2, '.', '') ?>%</td>
                <td data-title="Total Profit" align="right" class="total_profit">$<?= number_format($total_price - $total_cost,2, '.', '') ?></td>
                <td data-title=""></td>
                <td data-title="Total" align="right" class="total_total">$<?= number_format($total_price,2, '.', '') ?></td>
            </tr>
        </table>
    </div>
</div>
<div class="clearfix"></div>