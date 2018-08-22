<?php
/*
Add Expense
*/
include ('../include.php');
checkAuthorised('expense');
error_reporting(0);

if (isset($_POST['expense_submit'])) {

    $expense_for = $_POST['expense_for'];
    $staff = $_POST['staff'];

    $contact = filter_var($_POST['contact'],FILTER_SANITIZE_STRING);
    $title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
    $description = filter_var(strip_tags($_POST['description']),FILTER_SANITIZE_STRING);
	$comments = filter_var($_POST['comments'],FILTER_SANITIZE_STRING);
	$comments = ($comments == '' ? '' : $comments.' (Comment added by '.get_contact($dbc, $_SESSION['contactid']).' on '.date('Y-m-d h:i:s').')');

    $ex_file = htmlspecialchars($_FILES["ex_file"]["name"], ENT_QUOTES);
	$status = ($_POST['expense_submit'] == 'Payable' ? 'Submitted' : '');

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    $N = count($_POST['amount']);
    for($i=0; $i < $N; $i++) {
        move_uploaded_file($_FILES["ex_file"]["tmp_name"][$i], "download/" . $_FILES["ex_file"]["name"][$i]) ;

        $ex_date = filter_var($_POST['ex_date'][$i],FILTER_SANITIZE_STRING);
        $type = filter_var($_POST['type'][$i],FILTER_SANITIZE_STRING);
        $day_expense = filter_var($_POST['day_expense'][$i],FILTER_SANITIZE_STRING);
        $amount = filter_var($_POST['amount'][$i],FILTER_SANITIZE_STRING);
        $balance = filter_var($_POST['balance'][$i],FILTER_SANITIZE_STRING);
        $pst = filter_var($_POST['pst'][$i],FILTER_SANITIZE_STRING);
        $gst = filter_var($_POST['gst'][$i],FILTER_SANITIZE_STRING);
        $total = filter_var($_POST['total'][$i],FILTER_SANITIZE_STRING);

		if(!empty($_GET['expenseid'])) {
			$sql_expense = "UPDATE `expense` SET `expense_for`='$expense_for', `contact`='$contact', `staff`='$staff', `category`='$category', `title`='$title', `description`='$description', `ex_date`='$ex_date', `ex_file`='".filter_var($ex_file[$i],FILTER_SANITIZE_STRING)."', `type`='$type',
				`day_expense`='$day_expense', `amount`='$amount', `balance`='$balance', `pst`='$pst', `gst`='$gst', `total`='$total', `status`='$status', `comments`=CONCAT(IF(IFNULL(`comments`,'') = '', '', CONCAT(`comments`,'<br />')),'$comments')
				WHERE `expenseid`='".$_GET['expenseid']."'";
        $before_change = "";
        $history = "Expense entry has been updated. <br />";
    	  add_update_history($dbc, 'expenses_history', $history, '', $before_change);
		} else {
			$sql_expense = "INSERT INTO `expense` (`expense_for`, `contact`, `staff`, `category`, `title`, `description`, `ex_date`, `ex_file`, `type`, `day_expense`, `amount`, `balance`, `pst`, `gst`, `total`, `status`, `comments`)
				VALUES ('$expense_for', '$contact', '$staff', '$category', '$title', '$description', '$ex_date', '".filter_var($ex_file[$i],FILTER_SANITIZE_STRING)."', '$type', '$day_expense', '$amount', '$balance', '$pst', '$gst', '$total', '$status', '$comments')";
        $before_change = "";
        $history = "Expense entry has been added. <br />";
    	  add_update_history($dbc, 'expenses_history', $history, '', $before_change);
		}
        $result_expense = mysqli_query($dbc, $sql_expense);
    }

    echo '<script type="text/javascript"> window.location.replace("'.$_POST['from'].'"); </script>';
    //mysqli_close($dbc);//Close the DB Connection
}
?>
<script type="text/javascript">
var contacts = '';
    $(document).ready(function() {
        var add_new_s = 1;
        $('#add_row_exp').on( 'click', function () {
            var clone = $('.additional_exp').clone();
            clone.find('.form-control').val('');
            clone.find('.set_pre').val(0);

            clone.find('.datepicker').val('');

            clone.find('#amount_0').attr('id', 'amount_'+add_new_s);
            clone.find('#gst_0').attr('id', 'gst_'+add_new_s);
            clone.find('#total_0').attr('id', 'total_'+add_new_s);

            clone.removeClass("additional_exp");

            $('#add_here_new_exp').append(clone);
            clone.find('.datepicker').each(function() {
                $(this).removeAttr('id').removeClass('hasDatepicker');
                $('.datepicker').datepicker({dateFormat: 'yy-mm-dd', changeYear: true, changeMonth: true, yearRange: '1920:2025'});
            });

            add_new_s++;
            return false;
        });

		contacts=$('[name=contact] option');
		$('[name=expense_for]').change(function() {
			$('[name=contact]').empty();
			contacts.each(function() {
				var type = $(this).data('type');
				var category = $('[name=expense_for]').val();
				if(type == '' || type == category || category == '') {
					$('[name=contact]').append($(this));
				}
				$('[name=contact]').val('').trigger('change.select2');
			});
		});
    });

  	function numericFilter(txb) {
	   txb.value = txb.value.replace(/[^\0-9]/ig, "");
	}

    function countTotal(sel) {
        var end = sel.value;
        var typeId = sel.id;
        var arr = typeId.split('_');

        var amount = $("#amount_"+arr[1]).val();
        var pst = +$("#pst_"+arr[1]).val() || 0;
        var gst = +$("#gst_"+arr[1]).val() || 0;

        var total = parseFloat(amount) + parseFloat(gst) + parseFloat(pst);
        //alert('ethy');

        $("#total_"+arr[1]).val((total).toFixed(2));

    }
</script>
</head>

<body>
<?php include_once ('../navigation.php');
$back_url = 'expenses.php';
if(!empty($_GET['from'])) {
	$back_url = urldecode($_GET['from']);
}

// Variables
$gst_name = get_config($dbc, 'gst_name');
if(empty($gst_name)) {
	$gst_name = 'GST';
}
$pst_name = get_config($dbc, 'pst_name');
if(empty($pst_name)) {
	$pst_name = 'PST';
}

$current_id = (empty($_GET['expenseid']) ? '' : $_GET['expenseid']);
$expense = mysqli_fetch_array(mysqli_query($dbc,"SELECT `expense_for`, `staff`, `contact`, `category`, `title`, `description`, `ex_file`, `ex_date`, `type`, `day_expense`, `amount`, `balance`, `pst`, `gst`, `total`, `comments` FROM `expense` WHERE `expenseid`='$current_id' UNION
	SELECT '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''"));
$expense_for = $expense['expense_for'];
$staff = $expense['staff'];
$contact = $expense['contact'];
$category = $expense['category'];
$title = $expense['title'];
$description = html_entity_decode($expense['description']);
$ex_file = $expense['ex_file'];
$ex_date = $expense['ex_date'];
$type = $expense['type'];
$day_expense = $expense['day_expense'];
$amount = $expense['amount'];
$budget = $expense['balance'];
$pst = $expense['pst'];
$gst = $expense['gst'];
$total = $expense['total'];
$comments = $expense['comments'];
?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
    <h1>Add Expense</h1>
	<div class="pad-left gap-top double-gap-bottom"><a href="<?php echo $back_url; ?>" class="btn config-btn">Back to Dashboard</a></div>

    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<input type="hidden" name="from" value="<?php echo $back_url; ?>">
    <?php
    $get_value_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`expense_dashboard`) expense FROM `field_config_expense`"));
    $value_config = ','.$get_value_config['expense'].',';
    ?>

    <div class="panel-group" id="accordion2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
                        Information<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_1" class="panel-collapse collapse">
                <div class="panel-body">

				<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label">Expense Tab<span class="text-red">*</span>:</label>
				  <div class="col-sm-8">
					<select data-placeholder="Choose a Expense For..." name="expense_for" class="chosen-select-deselect form-control" width="380">
					  <option value=""></option>
					  <option <?php echo ('business' == $expense_for ? 'selected' : ''); ?> value="business">Business Expense</option>
					  <option <?php echo ('customers' == $expense_for ? 'selected' : ''); ?> value="customers">Customer Expense</option>
					  <option <?php echo ('clients' == $expense_for ? 'selected' : ''); ?> value="clients">Client Expense</option>
					  <option <?php echo ('staff' == $expense_for ? 'selected' : ''); ?> value="staff">Staff Expense</option>
					  <option <?php echo ('sales' == $expense_for ? 'selected' : ''); ?> value="sales">Sales Expense</option>
					</select>
				  </div>
				</div>

				<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label">Contact<span class="text-red">*</span>:</label>
				  <div class="col-sm-8">
					<select data-placeholder="Choose a Contact..." name="contact" class="chosen-select-deselect form-control" width="380">
					  <option value=""></option>
					  <?php
						$query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE `category` NOT LIKE 'Cold Call%' AND deleted=0");
						while($row = mysqli_fetch_array($query)) {
							$name = (trim(decryptIt($row['name'])) != '' ? decryptIt($row['name']) : decryptIt($row['first_name']).' '.decryptIt($row['last_name']));
							echo "<option ".($name == $contact ? 'selected' : '')." data-type='".$row['category']."' value='". $name."'>".$name.'</option>';
						}
					  ?>
					</select>
				  </div>
				</div>

                <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
                    <div class="form-group">
                      <label for="site_name" class="col-sm-4 control-label">Staff<span class="text-red">*</span>:</label>
                      <div class="col-sm-8">
                        <select data-placeholder="Choose a Staff Member..." name="staff" class="chosen-select-deselect form-control" width="380">
                          <option value=""></option>
                          <?php
                            $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0");
                            while($row = mysqli_fetch_array($query)) {
								$name = get_contact($dbc, $row['contactid']);
                                echo "<option ".($row['contactid'] == $staff || $name == $staff ? 'selected' : '')." value='".$row['contactid']."'>".$name.'</option>';
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                <?php } ?>

                <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
                  <div class="form-group">
                    <label for="office_zip" class="col-sm-4 control-label">Expense Category:</label>
                    <div class="col-sm-8">
                      <input name="category" type="text" class="form-control office_zip" value="<?php echo $category; ?>" />
                    </div>
                  </div>
                <?php } ?>

                <?php if (strpos($value_config, ','."Expense Heading".',') !== FALSE) { ?>
                  <div class="form-group">
                    <label for="office_zip" class="col-sm-4 control-label">Expense Heading:</label>
                    <div class="col-sm-8">
                      <input name="title" type="text" class="form-control office_zip" value="<?php echo $title; ?>" />
                    </div>
                  </div>
                <?php } ?>

                <?php if (strpos($value_config, ','."Description".',') !== FALSE) { ?>
                  <div class="form-group">
                    <label for="first_name[]" class="col-sm-4 control-label">Description:</label>
                    <div class="col-sm-8">
                      <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
                    </div>
                  </div>
                <?php } ?>


                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
                        Expense Info<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_2" class="panel-collapse collapse">
                <div class="panel-body">

                <div class="form-group clearfix  hide-titles-mob">
                    <?php if (strpos($value_config, ','."Expense Date".',') !== FALSE) { ?>
                    <label class="col-sm-2 text-center">Date</label>
                    <?php } ?>
                    <?php if (strpos($value_config, ','."Receipt".',') !== FALSE) { ?>
                    <label class="col-sm-2 text-center">Receipt</label>
                    <?php } ?>
                    <label class="col-sm-2 text-center">Type</label>
                    <?php if (strpos($value_config, ','."Day Expense".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center">Day Expense</label>
                    <?php } ?>
                    <?php if (strpos($value_config, ','."Amount".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center">Amount</label>
                    <?php } ?>
                    <?php if (strpos($value_config, ','."Local Tax".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center"><?php echo $pst_name; ?></label>
                    <?php } ?>
                    <?php if (strpos($value_config, ','."Tax".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center"><?php echo $gst_name; ?></label>
                    <?php } ?>
                    <?php if (strpos($value_config, ','."Amount".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center">Total</label>
                    <?php } ?>
                    <?php if (strpos($value_config, ','."Budget".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center">Budget</label>
                    <?php } ?>

                </div>

                <div class="additional_exp clearfix">
                    <div class="clearfix"></div>

                    <div class="form-group clearfix">
                        <?php if (strpos($value_config, ','."Expense Date".',') !== FALSE) { ?>
                        <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Expense Date:</label>
                            <input name="ex_date[]" type="text" class="datepicker form-control" value="<?php echo $ex_date; ?>">
                        </div>
                        <?php } ?>

                        <?php if (strpos($value_config, ','."Receipt".',') !== FALSE) { ?>
                        <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Receipt:</label>
							<?php if($ex_file != '') {
								echo "<a href='download/".$ex_file."'>View</a>";
							} ?>
                            <input name="ex_file[]" type="file" data-filename-placement="inside" class="form-control" />
                        </div>
                        <?php } ?>
                        <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Type:</label>
                            <select data-placeholder="Choose a User..." name="type[]" class="chosen-select-deselect1 form-control" width="380">
                              <option value=""></option>
                              <?php if (strpos($value_config, ','."Flight".',') !== FALSE) { ?>
                              <option <?php if($type == 'Flight') { echo 'selected'; } ?> value="Flight">Flight</option>
                              <?php } ?>
                              <?php if (strpos($value_config, ','."Hotel".',') !== FALSE) { ?>
                              <option <?php if($type == 'Hotel') { echo 'selected'; } ?> value="Hotel">Hotel</option>
                              <?php } ?>
                              <?php if (strpos($value_config, ','."Breakfast".',') !== FALSE) { ?>
                              <option <?php if($type == 'Breakfast') { echo 'selected'; } ?> value="Breakfast">Breakfast</option>
                              <?php } ?>
                              <?php if (strpos($value_config, ','."Lunch".',') !== FALSE) { ?>
                              <option <?php if($type == 'Lunch') { echo 'selected'; } ?> value="Lunch">Lunch</option>
                              <?php } ?>
                              <?php if (strpos($value_config, ','."Dinner".',') !== FALSE) { ?>
                              <option <?php if($type == 'Dinner') { echo 'selected'; } ?> value="Dinner">Dinner</option>
                              <?php } ?>
                              <?php if (strpos($value_config, ','."Beverages".',') !== FALSE) { ?>
                              <option <?php if($type == 'Drink') { echo 'selected'; } ?> value="Drink">Beverages</option>
                              <?php } ?>
                              <?php if (strpos($value_config, ','."Transportation".',') !== FALSE) { ?>
                              <option <?php if($type == 'Transportation') { echo 'selected'; } ?> value="Transportation">Transportation</option>
                              <?php } ?>
                              <?php if (strpos($value_config, ','."Entertainment".',') !== FALSE) { ?>
                              <option <?php if($type == 'Entertainment') { echo 'selected'; } ?> value="Entertainment">Entertainment</option>
                              <?php } ?>
                              <?php if (strpos($value_config, ','."Gas".',') !== FALSE) { ?>
                              <option <?php if($type == 'Gas') { echo 'selected'; } ?> value="Gas">Gas</option>
                              <?php } ?>
                              <?php if (strpos($value_config, ','."Misc".',') !== FALSE) { ?>
                              <option <?php if($type == 'Misc') { echo 'selected'; } ?> value="Misc">Misc</option>
                              <?php } ?>
                              <?php
                                $expense_types = get_config($dbc, 'expense_types');
                                $w5 = explode(',', $expense_types);

                                foreach($w5 as $key=>$val) {
                                 echo '<option '.($val == $type ? 'selected' : '').' value="'.$val.'">'.$val.'</option>';
                                }
                              ?>
                            </select>
                        </div>
                        <?php if (strpos($value_config, ','."Day Expense".',') !== FALSE) { ?>
                        <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Day Expense:</label>
                            <input name="day_expense[]" type="text" class="form-control" value="<?php echo ($day_expense == '' ? 0 : $day_expense); ?>" />
                        </div>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Amount".',') !== FALSE) { ?>
                        <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Amount:</label>
                            <input name="amount[]" onchange="countTotal(this)" id="amount_0" type="text" class="form-control set_pre" value="<?php echo ($amount == '' ? 0 : $amount); ?>" />
                        </div>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Local Tax".',') !== FALSE) { ?>
                        <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php echo $pst_name; ?>:</label>
                            <input name="pst[]" onchange="countTotal(this)" id="pst_0" type="text" class="form-control set_pre" value="<?php echo ($pst == '' ? 0 : $pst); ?>" />
                        </div>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Tax".',') !== FALSE) { ?>
                        <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php echo $gst_name; ?>:</label>
                            <input name="gst[]" onchange="countTotal(this)" id="gst_0" type="text" class="form-control set_pre" value="<?php echo ($gst == '' ? 0 : $gst); ?>" />
                        </div>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Amount".',') !== FALSE) { ?>
                        <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                            <input name="total[]" id="total_0" type="text" class="form-control set_pre" value="<?php echo ($total == '' ? 0 : $total); ?>" />
                        </div>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Budget".',') !== FALSE) { ?>
                        <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Budget:</label>
                            <input name="balance[]" type="text" class="form-control set_pre" value="<?php echo ($budget == '' ? 0 : $budget); ?>" />
                        </div>
                        <?php } ?>
                    </div>

                </div>

				<?php if($current_id == '') { ?>
					<div id="add_here_new_exp"></div>
					<div class="form-group triple-gapped clearfix">
						<div class="col-sm-offset-4 col-sm-8">
							<button id="add_row_exp" class="btn brand-btn pull-left">Add Row</button>
						</div>
					</div>
				<?php } ?>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_comments" >
                        Comments<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_comments" class="panel-collapse collapse">
                <div class="panel-body">
					<div class="form-group"><?php echo $comments; ?></div>
					<div class="form-group">
						<label for="company_name" class="col-sm-4 control-label">Comments:</label>
						<div class="col-sm-8">
							<input name="comments" type="text" class="form-control" value="" />
						</div>
					</div>
                </div>
            </div>
        </div>

    </div>

      <div class="form-group">
		<p><span class="text-red"><em>Required Fields *</em></span></p>
      </div>

        <div class="form-group">
			<a href="<?php echo $back_url; ?>" class="btn brand-btn">Back</a>
			<?php if(strpos(','.get_config($dbc,'expense_tabs').',',',manager,') !== FALSE) {
				echo '<button type="submit" name="expense_submit" value="Payable" class="btn brand-btn mobile-block pull-right">Submit Expense for Approval</button>';
			} else {
				echo '<button type="submit" name="expense_submit" value="Payable" class="btn brand-btn mobile-block pull-right">Submit as a Payable</button>';
			} ?>
			<button type="submit" name="expense_submit" value="Submit" class="btn brand-btn mobile-block pull-right">Submit as an Expense</button>
      </div>

    </form>
    </div>
  </div>
</div>
<?php include ('../footer.php'); ?>
