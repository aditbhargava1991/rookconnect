<?php include_once('../include.php');
if($_POST['export']) {
  $category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
  $file_name = "report_equipment_".config_safe_str($category)."_" . date("Y_m_d") . '.csv';

  ob_end_clean();
  $fp = fopen('php://output','w');
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename='.$file_name);

  $total = 0;
  $data[] = 'ID,Category,Description,Type,Make,Model,Unit of Measure,Model Year,Equipment Label,Leased,Total Kilometres,Style,Vehicle Size,Color,Trim,Staff,Fuel Type,Tire Type,Drive Train,Serial #,Unit #,VIN #,Licence Plate,Nickname,Year Purchased,Mileage,Hours Operated,Cost,CDN Cost Per Unit,USD Cost Per Unit,Finance,Lease,Insurance Renewal Date,Insurance Company,Insurance Contact,Insurance Phone,Hourly Rate,Daily Rate,Semi Monthly Rate,Monthly Rate,Field Day Cost,Field Day Billable,HR Rate Work,HR Rate Travel,Next Service Date,Next Service Hours,Next Service Description,Service Location,Last Oil Filter Change (date),Last Oil Filter Change (km),Next Oil Filter Change (date),Next Oil Filter Change (km),Last Inspection & Tune Up (date),Last Inspection & Tune Up (km),Next Inspection & Tune Up (date),Next Inspection & Tune Up (km),Tire Condition,Last Tire Rotation (date),Last Tire Rotation (km),Next Tire Rotation (date),Next Tire Rotation (km),Registration Renewal Date,Location,LSD,Status,Ownership Status,Quote Description,Notes,CVIP Ticket Renewal Date,Vehicle Access Code,Cargo,Lessor,Group,Use';

  $report_validation = mysqli_query($dbc,"SELECT * FROM equipment WHERE `deleted`=0 AND '$category' IN (`category`,'ALL')");

  $num_rows = mysqli_num_rows($report_validation);
  while($row_report = mysqli_fetch_array($report_validation)) {
        $data[] = [$row_report['equipmentid'],$row_report['category'],$row_report['equ_description'],$row_report['type'],$row_report['make'],$row_report['model'],$row_report['submodel'],$row_report['model_year'],$row_report['label'],$row_report['total_kilometres'],$row_report['leased'],$row_report['style'],$row_report['vehicle_size'],$row_report['color'],$row_report['trim'],$row_report['staffid'],$row_report['fuel_type'],$row_report['tire_type'],$row_report['drive_train'],$row_report['serial_number'],$row_report['unit_number'],$row_report['vin_number'],$row_report['licence_plate'],$row_report['nickname'],$row_report['year_purchased'],$row_report['mileage'],$row_report['hours_operated'],$row_report['cost'],$row_report['cnd_cost_per_unit'],$row_report['usd_cost_per_unit'],$row_report['finance'],$row_report['lease'],$row_report['insurance_renewal'],$row_report['insurance'],$row_report['insurance_contact'],$row_report['insurance_phone'],$row_report['hourly_rate'],$row_report['daily_rate'],$row_report['semi_monthly_rate'],$row_report['monthly_rate'],$row_report['field_day_cost'],$row_report['field_day_billable'],$row_report['hr_rate_work'],$row_report['hr_rate_travel'],$row_report['next_service_date'],$row_report['next_service'],$row_report['next_serv_desc'],$row_report['service_location'],$row_report['last_oil_filter_change_date'],$row_report['last_oil_filter_change'],$row_report['next_oil_filter_change_date'],$row_report['next_oil_filter_change'],$row_report['last_insp_tune_up_date'],$row_report['last_insp_tune_up'],$row_report['next_insp_tune_up_date'],$row_report['next_insp_tune_up'],$row_report['tire_condition'],$row_report['last_tire_rotation_date'],$row_report['last_tire_rotation'],$row_report['next_tire_rotation_date'],$row_report['next_tire_rotation'],$row_report['reg_renewal_date'],$row_report['location'],$row_report['lsd'],$row_report['status'],$row_report['ownership_status'],$row_report['quote_description'],$row_report['notes'],$row_report['cvip_renewal_date'],$row_report['vehicle_access_code'],$row_report['cargo'],$row_report['lessor'],$row_report['group'],$row_report['use']];
  }

  foreach ($data as $line) {
      fputcsv($fp, $line);
  }
  exit();
}
if(!empty($_FILES['upload']['name'])) {
	include('upload_csv.php');
} ?>

<form name="form_sites" method="post" action="" class="form-horizontal col-sm-12" role="form" enctype="multipart/form-data">
<h2><?= $_GET['action'] == 'import' ? 'Import Equipment' : 'Export Equipment' ?><a href="../blank_loading_page.php" class="pull-right"><img src="../img/icons/cancel.png" class="inline-img"></a></h2>
<?php if(vuaed_visible_function($dbc, 'equipment') == 1) { ?>
    <div class="form-group">
        <label class="col-sm-4">
            <span class="popover-examples list-inline hide-on-mobile"><a style="margin:0 0 0 15px;" data-toggle="tooltip" data-placement="bottom" title="<?= $_GET['action'] == 'import' ? 'When importing a spreadsheet, this category will be used for any equipment with no category specified.' : 'Only equipment with this category will be exported.' ?>"><img src="<?= WEBSITE_URL ?>/img/info.png" style="width:1em;"></a></span>
            Equipment Tab:</label>
        <div class="col-sm-8">
            <select class="chosen-select-deselect" name="category" data-placeholder="Select Tab">
                <option value="ALL">All Equipment</option>
                <?php foreach (explode(',', get_config($dbc, 'equipment_tabs')) as $cat_tab) {
                    echo "<option ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'selected' : '').' value="'.$cat_tab.'">'.$cat_tab."</option>";
                } ?>
            </select>
        </div>
    </div>
    <?php if($_GET['action'] == 'import') { ?>
        <div class="form-group">
            <label class="col-sm-4">
                <span class="popover-examples list-inline hide-on-mobile"><a style="margin:0 0 0 15px;" data-toggle="tooltip" data-placement="bottom" title="You can upload several pieces of equipment at once by entering them into the Template, and then uploading them using the Upload CSV button to the right. Note that not all fields in the spreadsheet need to be filled in."><img src="<?= WEBSITE_URL ?>/img/info.png" style="width:1em;"></a></span>
                Equipment Spreadsheet:
            </label>
            <div class="col-sm-8">
                <a href="template.csv">Download Uploader Template</a>
                <input type="file" name="upload" />
            </div>
        </div>
        <button type="submit" name="send_upload" value="upload" class="btn brand-btn mobile-block gap-bottom pull-right">Upload CSV <img src="../img/csv.png" style="height:1em;"></button>
    <?php } else { ?>
        <button type="submit" name="export" value="export" class="btn brand-btn mobile-block gap-bottom pull-right">Export CSV <img src="../img/csv.png" style="height:1em;"></button>
    <?php }
} ?>
<a href="../blank_loading_page.php" class="btn brand-btn pull-left">Cancel</a>
</form>