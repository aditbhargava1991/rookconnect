<script>
function countBudget(txb) {
    var sum_fee = 0;
    $('.budget_price').each(function () {
        sum_fee += +$(this).val() || 0;
    });
    $('.total_budget').val(round2Fixed(sum_fee));
}
</script>
<div class="form-group">
    <div class="col-sm-12">
      <?php if (strpos($value_config, ','."Package".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Package:</label>
        <div class="col-sm-8">
          <input name="budget_price_0" value="<?php echo $budget_price[0]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Promotion".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Promotion:</label>
        <div class="col-sm-8">
          <input name="budget_price_1" value="<?php echo $budget_price[1]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Custom".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Custom:</label>
        <div class="col-sm-8">
          <input name="budget_price_2" value="<?php echo $budget_price[2]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Material".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Material:</label>
        <div class="col-sm-8">
          <input name="budget_price_14" value="<?php echo $budget_price[14]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Labour".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Labour:</label>
        <div class="col-sm-8">
          <input name="budget_price_13" value="<?php echo $budget_price[13]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Services".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Services:</label>
        <div class="col-sm-8">
          <input name="budget_price_3" value="<?php echo $budget_price[3]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Products".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Products:</label>
        <div class="col-sm-8">
          <input name="budget_price_16" value="<?php echo $budget_price[16]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."SRED".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">SR&ED:</label>
        <div class="col-sm-8">
          <input name="budget_price_15" value="<?php echo $budget_price[15]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Staff:</label>
        <div class="col-sm-8">
          <input name="budget_price_4" value="<?php echo $budget_price[4]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Contractor".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Contractor:</label>
        <div class="col-sm-8">
          <input name="budget_price_5" value="<?php echo $budget_price[5]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Clients".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Clients:</label>
        <div class="col-sm-8">
          <input name="budget_price_6" value="<?php echo $budget_price[6]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Vendor Pricelist".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Vendor Pricelist:</label>
        <div class="col-sm-8">
          <input name="budget_price_7" value="<?php echo $budget_price[7]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Customer:</label>
        <div class="col-sm-8">
          <input name="budget_price_8" value="<?php echo $budget_price[8]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Inventory".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Inventory:</label>
        <div class="col-sm-8">
          <input name="budget_price_9" value="<?php echo $budget_price[9]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Equipment:</label>
        <div class="col-sm-8">
          <input name="budget_price_10" value="<?php echo $budget_price[10]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Expenses".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Expenses:</label>
        <div class="col-sm-8">
          <input name="budget_price_11" value="<?php echo $budget_price[11]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Other".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Other:</label>
        <div class="col-sm-8">
          <input name="budget_price_12" value="<?php echo $budget_price[12]; ?>" onchange="countBudget(this)" type="text" class="form-control budget_price">
        </div>
      </div>
      <?php } ?>

    <div class="form-group">
        <label for="first_name" class="col-sm-4 control-label text-right">Total Budget Dollars:</label>
        <div class="col-sm-8">
            <input name="total_budget" value="<?php echo $budget_price[17]; ?>" type="text" class="form-control total_budget"></p>
        </div>
    </div>

    </div>
</div>
