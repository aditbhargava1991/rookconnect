<?php
/*
Field Purhase Order
*/
include_once ('../include.php');
include_once('../tcpdf/tcpdf.php');

if (isset($_POST['purchase_order'])) {
	$businessid = $_POST['businessid'];
	$projectid = $_POST['projectid'];
	$clientprojectid = '';
	if(substr($projectid,0,1) == 'C') {
		$clientprojectid = substr($projectid,1);
		$projectid = '';
	}
	$ticketid = $_POST['ticketid'];
	$workorderid = $_POST['workorderid'];
	$vendorid = $_POST['vendorid'];
    $issue_date = $_POST['issue_date'];
    $revision = $_POST['revision'];
    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);

	$qty = implode('#*#',$_POST['qty']);
	$desc = implode('#*#',$_POST['desc']);
	$grade = implode('#*#',$_POST['grade']);
	$tag = implode('#*#',$_POST['tag']);
	$detail = implode('#*#',$_POST['detail']);
	$price_per_unit = implode('#*#',$_POST['price_per_unit']);
	$each_cost = implode('#*#',$_POST['each_cost']);

	$desc = filter_var($desc,FILTER_SANITIZE_STRING);
	$grade = filter_var($grade,FILTER_SANITIZE_STRING);
	$tag = filter_var($tag,FILTER_SANITIZE_STRING);
	$detail = filter_var($detail,FILTER_SANITIZE_STRING);

    $cost = filter_var($_POST['cost'],FILTER_SANITIZE_STRING);
	$mark_up = filter_var($_POST['mark_up'],FILTER_SANITIZE_STRING);
	$total_cost = filter_var($_POST['total_cost'],FILTER_SANITIZE_STRING);
    $created_by = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

	if(empty($_POST['fieldpoid'])) {
		$query_insert_po = "INSERT INTO `purchase_order` (`businessid`, `projectid`, `client_projectid`, `ticketid`, `workorderid`, `vendorid`, `issue_date`, `description`, `qty`, `desc`, `grade`, `tag`, `detail`, `price_per_unit`, `each_cost`, `cost`, `mark_up`, `total_cost`, `created_by`) VALUES ('$businessid', '$projectid', '$clientprojectid', '$ticketid', '$workorderid', '$vendorid', '$issue_date', '$description', '$qty', '$desc', '$grade', '$tag', '$detail', '$price_per_unit', '$each_cost', '$cost', '$mark_up', '$total_cost', '$created_by')";
	    $result_insert_po = mysqli_query($dbc, $query_insert_po);
        $fieldpoid = mysqli_insert_id($dbc);
        $url = 'Added';
				$before_change = '';
		    $history = "Purchase Order entry has been added. <br />";
		    add_update_history($dbc, 'po_history', $history, '', $before_change);
	} else {
		$fieldpoid = $_POST['fieldpoid'];
		$query_update_site = "UPDATE `purchase_order` SET `businessid` = '$businessid', `projectid` = '$projectid', `client_projectid` = '$clientprojectid', `ticketid` = '$ticketid', `workorderid` = '$workorderid', `vendorid` = '$vendorid', `issue_date` = '$issue_date', `description`= '$description', `qty` = '$qty', `desc` = '$desc', `grade` = '$grade', `tag` = '$tag', `detail` = '$detail', `price_per_unit` = '$price_per_unit', `each_cost` = '$each_cost', `cost` = '$cost', `mark_up` = '$mark_up', `total_cost` = '$total_cost', `revision` = '$revision', `edited_by` = '$created_by' WHERE `fieldpoid` = '$fieldpoid'";
		$result_update_site	= mysqli_query($dbc, $query_update_site);
        $url = 'Updated';
				$before_change = "";
				$history = "Purchase order entry has been updated. <br />";
				add_update_history($dbc, 'po_history', $history, '', $before_change);
	}

    include ('purchase_order_pdf.php');

    echo '<script type="text/javascript"> window.location.replace("?"); </script>';

    mysqli_close($dbc);//Close the DB Connection
}

?>
<script type="text/javascript">

$(document).ready(function() {

	$("#businessid").change(function() {
        var businessid = $("#businessid").val();
		$.ajax({
			type: "GET",
			url: "purchase_order_ajax_all.php?fill=projectname&businessid="+businessid,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#projectid').html(response);
				$("#projectid").trigger("change.select2");

				$('#ticketid').html(response);
				$("#ticketid").trigger("change.select2");
			}
		});
	});

    $("#form1").submit(function( event ) {
        var jobid = $("#jobid").val();

        if (jobid === null) {
        } else {
            var cost = $("input[name=cost]").val();
            var total_cost = $("input[name=total_cost]").val();

            if(cost == '0.00') {
                alert("Please fill up Cost.");
                return false;
            }
            if(total_cost == '0.00' || total_cost == '0' || total_cost == '' ) {
                alert("Please fill up Total Cost.");
                return false;
            }
        }

    });

        $("#cost").keyup(function() {
            var cost = parseFloat($('#cost').val());
            var mark_up = parseFloat($('#mark_up').val());
            var gst = parseFloat(cost*mark_up)/100;
            var cost_gst = parseFloat(cost+gst);
            document.getElementById('total_cost').value = round2Fixed(cost_gst);
        });

        var inc_material = $('.qty').length;
        $('.hide_additional').hide();
        $('#add_new_row').on( 'click', function () {
         if ($('.hide_additional').is(":hidden")) {
            $('.hide_additional').show();
         } else {
            var clone = $('.additional_row').clone();
            clone.find('.form-control').val('');
            //clone.find('#qty_1').attr('id', 'qty_'+inc_material);
            //clone.find('#up_1').attr('id', 'up_'+inc_material);
            //clone.find('#amount_1').attr('id', 'amount_'+inc_material);

            clone.find('.qty').attr('id', 'qty_'+inc_material);
            clone.find('.up').attr('id', 'up_'+inc_material);
            clone.find('.amount').attr('id', 'amount_'+inc_material);

            //$('div').attr('name', 'newName');
            clone.removeClass("additional_row");
            $('#add_here_new_data').append(clone);
          }
          inc_material++;
            return false;
        });
    });

	function multiplyCost(txb) {
        var get_id = txb.id;
        var split_id = get_id.split('_');
        var amount = parseFloat($('#qty_'+split_id[1]).val() * $('#up_'+split_id[1]).val());
        document.getElementById('amount_'+split_id[1]).value = round2Fixed(amount);
        materialStock();
	}

	function materialStock() {
        var sum = 0;
        $('input[name="each_cost[]"]').each(function(){
            if(!isNaN(this.value) && this.value.length!=0) {
                sum += parseFloat($(this).val());
            }
        });
        document.getElementById('cost').value = sum;
		var cost = parseFloat($('#cost').val());
		var mark_up = parseFloat($('#mark_up').val());
		var gst = parseFloat(cost*0.05);
        var cost_gst = parseFloat(cost+gst);
		document.getElementById('total_cost').value = round2Fixed(cost_gst);
	}

	function totalCost() {
		var cost = parseFloat($('#cost').val());
		var mark_up = parseFloat($('#mark_up').val());
		var gst = parseFloat(cost*0.05);
        var cost_gst = parseFloat(cost+gst);
		document.getElementById('total_cost').value = round2Fixed(cost_gst);
	}

	function numericFilter(txb) {
	   txb.value = txb.value.replace(/[^\0-9]/ig, "");
	}
</script>
<form id="form1" action="" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
<?php
$businessid = '';
$projectid = '';
$ticketid = '';
$workorderid = '';
$vendorid = '';
$description = '';
$cost = '';
$qty = '';
$desc = '';
$grade = '';
$tag = '';
$detail = '';
$price_per_unit = '';
$each_cost = '';
$issue_date = '';
$revision = 0;
$cost = '';
$mark_up = '';
$total_cost = '';
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT purchase_order FROM field_config"));
$value_config = ','.$get_field_config['purchase_order'].',';

if(!empty($_GET['fieldpoid'])) {

	$fieldpoid = $_GET['fieldpoid'];
	$get_job =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	purchase_order WHERE fieldpoid='$fieldpoid'"));

	$businessid = $get_job['businessid'];
	$projectid = (empty($get_job['projectid']) ? 'C'.$get_job['client_projectid'] : $get_job['projectid']);
	$ticketid = $get_job['ticketid'];
	$workorderid = $get_job['workorderid'];
	$mark_up = $get_job['mark_up'];
	$bill_to = $get_job['bill_to'];
	$total_cost = $get_job['total_cost'];
	$vendorid = $get_job['vendorid'];
	$cost = $get_job['cost'];
	$description = $get_job['description'];

	$cost = $get_job['cost'];
	$qty = $get_job['qty'];
	$desc = $get_job['desc'];
	$grade = $get_job['grade'];
	$tag = $get_job['tag'];
	$detail = $get_job['detail'];
	$price_per_unit = $get_job['price_per_unit'];
	$each_cost = $get_job['each_cost'];
	$issue_date = $get_job['issue_date'];

	$cost = $get_job['cost'];
	$mark_up = $get_job['mark_up'];
	$total_cost = $get_job['total_cost'];
	$revision = $get_job['revision']+1;
?>
<input type="hidden" id="fieldpoid"	name="fieldpoid" value="<?php echo $fieldpoid ?>" />
<?php	}	   ?>
<input type="hidden" id="revision"	name="revision" value="<?php echo $revision ?>" />

<?php include ('add_purchase_order_basic_info.php'); ?>

<?php include ('add_purchase_order_items.php'); ?>

<?php include ('add_purchase_order_cost.php'); ?>

<div class="form-group">
	<div class="col-sm-4 clearfix">
		<a href="purchase_order.php" class="btn brand-btn pull-right">Back</a>
		<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
	</div>
	<div class="col-sm-8">
			<!-- <input type="submit" name="purchase_order" value="Save Changes" class="btn brand-btn btn-lg pull-right" style="margin-right:20px"/> -->
			<input type="submit" name="purchase_order" value="Issue PO" class="btn brand-btn btn-lg pull-right" style="margin-right:20px"/>
	</div>
</div>

</form>
