<h1 class="triple-pad-bottom">Pay Invoice</h1>

<form id="form_pay_invoice" name="form_pay_invoice" method="post" action="add_invoice.php" enctype="multipart/form-data" class="form-horizontal" role="form">

<input type="hidden" name="invoiceid" value=<?php echo $_GET['invoiceid']; ?> >
<input type="hidden" name="from" value=<?php echo $_GET['from']; ?> >

<?php
$tags = explode(',',$_GET['invoiceid']);
$final_price = 0;
foreach($tags as $invoiceid) {
    $get_inventory =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT final_price FROM invoice WHERE	invoiceid='$invoiceid'"));
    $final_price += $get_inventory['final_price'];
}
?>
<input type="hidden" id="final_price" name="final_price" value=<?php echo $final_price; ?> >

  <div class="form-group">
    <label for="site_name" class="col-sm-4 control-label">Payment Total:</label>
    <div class="col-sm-8">
      <?php echo '$'.$final_price; ?>
    </div>
  </div>

    <div class="form-group payment_option">
        <label for="additional_note" class="col-sm-4 control-label">Payment:</label>
        <div class="col-sm-8">
            <div class="form-group clearfix">
                <label class="col-sm-2 text-center"></label>
                <label class="col-sm-3 text-center">Type</label>
                <label class="col-sm-3 text-center">Sub Price</label>
            </div>

            <div class="additional_pay_payment clearfix">
                <div class="clearfix"></div>
                <div class="form-group clearfix">
                    <div class="col-sm-2">
                    </div>
                    <div class="col-sm-3">
                      <select id="payment_type" name="payment_type[]" data-placeholder="Choose a Type..." class="chosen-select-deselect1 form-control" width="380">
                            <option value=''></option>
                            <option value = 'Master Card'>Master Card</option>
                            <option value = 'Visa'>Visa</option>
                            <option value = 'Debit'>Debit</option>
                            <option value = 'Cash'>Cash</option>
                            <option value = 'Check'>Check</option>
                            <option value = 'Cash'>Gift Certificate Redeem</option>
                            <option value = 'Pro-Bono'>Pro-Bono</option>
                      </select>
                    </div> <!-- Quantity -->
                    <div class="col-sm-3">
                        <input name="payment_price[]" type="text" value="<?php echo $final_price; ?>" id="payment_price_0" class="form-control payment_price" />
                    </div> <!-- Quantity -->
                </div>
            </div>

            <div id="add_here_new_pay_payment"></div>

            <div class="form-group triple-gapped clearfix">
                <div class="col-sm-offset-4 col-sm-8">
                    <button id="add_row_pay_payment" class="btn brand-btn pull-left">Add Payment Option</button>
                </div>
            </div>
        </div>
    </div>

  <!-- <div class="form-group">
    <label for="site_name" class="col-sm-4 control-label">Payment Type:</label>
    <div class="col-sm-8">
      <select id="payment_type" name="payment_type" data-placeholder="Choose a Type..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <option value = 'Master Card'>Master Card</option>
            <option value = 'Visa'>Visa</option>
            <option value = 'Debit'>Debit</option>
            <option value = 'Cash'>Cash</option>
            <option value = 'Check'>Check</option>
            <option value = 'Cash'>Gift Certificate Redeem</option>
      </select>
    </div>
  </div>
  -->

  <div class="form-group">
    <div class="col-sm-4 clearfix">
        <a href="index.php?tab=today" class="btn brand-btn pull-right">Back</a>
    </div>
    <div class="col-sm-8">
        <button type="submit" name="submit_pay" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
    </div>
  </div>

</form>