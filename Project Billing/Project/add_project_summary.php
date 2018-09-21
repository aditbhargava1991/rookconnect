<div class="form-group">
    <div class="col-sm-12">
      <?php if (strpos($value_config, ','."Package".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Package:</label>
        <div class="col-sm-8">
          <input name="package_summary" value="<?php echo $final_total_package;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Promotion".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Promotion:</label>
        <div class="col-sm-8">
          <input name="promotion_summary" value="<?php echo $final_total_promotion;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Custom".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Custom:</label>
        <div class="col-sm-8">
          <input name="custom_summary" value="<?php echo $final_total_custom;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Material".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Material:</label>
        <div class="col-sm-8">
          <input name="material_summary" value="<?php echo $final_total_material;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Labour".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Labour:</label>
        <div class="col-sm-8">
          <input name="labour_summary" value="<?php echo $final_total_labour;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Services".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Services:</label>
        <div class="col-sm-8">
          <input name="service_summary" value="<?php echo $final_total_services;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Products".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Products:</label>
        <div class="col-sm-8">
          <input name="product_summary" value="<?php echo $final_total_products;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."SRED".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">SR&ED:</label>
        <div class="col-sm-8">
          <input name="sred_summary" value="<?php echo $final_total_sred;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Staff:</label>
        <div class="col-sm-8">
          <input name="staff_summary" value="<?php echo $final_total_staff;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Contractor".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Contractor:</label>
        <div class="col-sm-8">
          <input name="contractor_summary" value="<?php echo $final_total_contractor;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Clients".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Clients:</label>
        <div class="col-sm-8">
          <input name="client_summary" value="<?php echo $final_total_clients;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Vendor Pricelist".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Vendor Pricelist:</label>
        <div class="col-sm-8">
          <input name="vendorpl_summary" value="<?php echo $final_total_vendor;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Customer:</label>
        <div class="col-sm-8">
          <input name="customer_summary" value="<?php echo $final_total_customer;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Inventory".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Inventory:</label>
        <div class="col-sm-8">
          <input name="inventory_summary" value="<?php echo $final_total_inventory;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Equipment:</label>
        <div class="col-sm-8">
          <input name="equipment_summary" value="<?php echo $final_total_equipment;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Expenses".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Expenses:</label>
        <div class="col-sm-8">
          <input name="expense_summary" value="<?php echo $final_total_expense;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Other".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Other:</label>
        <div class="col-sm-8">
          <input name="other_summary" value="<?php echo $final_total_other;?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

    </div>
</div>
