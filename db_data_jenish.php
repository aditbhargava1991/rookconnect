<?php
/*
 * Jenish's DB changes
 */
echo "====== Jenish's db changes: ======\n";

/*if(!mysqli_query($dbc,"")) {
	echo "Error: ".mysqli_error($dbc)."\n";
}*/

/****************** Adding indexing for Ticket tables *********************/

if(!mysqli_query($dbc, "ALTER TABLE `security_privileges_log` ADD `type` TEXT(50) DEFAULT NULL")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "CREATE TABLE security_privileges_staff SELECT * FROM security_privileges LIMIT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `security_privileges_staff` ADD `staff` INT(15)")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `security_privileges_staff` ADD PRIMARY KEY(`privilegesid`)")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `security_privileges_staff` CHANGE `privilegesid` `privilegesid` INT(10) NOT NULL AUTO_INCREMENT")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `field_config_contacts` ADD `mandatory` BOOLEAN DEFAULT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "CREATE TABLE task_dashboard_mandatory SELECT * FROM task_dashboard LIMIT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `field_config_equipment` ADD `mandatory` BOOLEAN DEFAULT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `security_level_names` ADD `custom_order` INT(5)")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

$level_array = array('admin'=>'Admin','therapist'=>'Therapist','executive_front_staff'=>'Executive Front Staff','trainer'=>'Trainer','accounting'=>'Accounting','accmanager'=>'Accounting Manager','advocate'=>'Advocate','assembler'=>'Assembler','assist'=>'Assistant','businessdevmanager'=>'Business Dev Manager','businessdevcoo'=>'Business Dev Coordinator','contractor'=>'Contractor','chairman'=>'Chairman','ced'=>'Chief Executive Director','ceo'=>'Chief Executive Officer','cfd'=>'Chief Financial Director	','cfo'=>'Chief Financial Officer	','coo'=>'Chief Operating Officer	','cod'=>'Chief Operations Director	','client'=>'Client','commsalesdirector'=>'Commercial Sales Director	','customers'=>'Customers	','controller'=>'Controller	','daypass'=>'Day Pass	','executive'=>'Executive	','execassist'=>'Executive Assistant	','exdirect'=>'Executive Director	','fieldops'=>'Field Operations	','fieldopmanager'=>'Field Operations Manager	','fieldshop'=>'Field Shop	','fieldsup'=>'Field Supervisor	','findirect'=>'Financial Director	','fluidhaulingman'=>'Fluid Hauling Manager	','foreman'=>'Foreman	','genmanager'=>'General Manager	','hrmanager'=>'HR Manager	','humanres'=>'Human Resources	','invmanager'=>'Inventory Manager	','lead'=>'Lead','mainshop'=>'Main Shop	','manfmanager'=>'Manufacturing Manager	','manager'=>'Managers','marketing'=>'Marketing','marketingdirector'=>'Marketing Director	','mrkmanager'=>'Marketing Manager	','master'=>'Master','officemanager'=>'Office Manager	','office_admin'=>'Office Admin','operations'=>'Operations','opsmanager'=>'Operations Manager','opconsult'=>'Operations Consultant','operationslead'=>'Operations Lead','opcoord'=>'Operations Coordinator','paintshop'=>'Paint Shop	','president'=>'President	','prospect'=>'Prospect','regionalmanager'=>'Regional Manager	','sales'=>'Sales','safety'=>'Safety','safetysup'=>'Safety Supervisor	','salesmarketingdirect'=>'Sales & Marketing Director	','salesdirector'=>'Sales Director	','salesmanager'=>'Sales Manager','shopforeman'=>'Shop Foreman','shopworker'=>'Shop Worker','staff'=>'Staff','supervisor'=>'Supervisor','suppchainlogist'=>'Supply Chain & Logistics','supporter'=>'Supporter','teamcolead'=>'Team Co-Lead','teamlead'=>'Team Lead','teammember'=>'Team Member','vicepres'=>'Vice-President','vpcorpdev'=>'VP Corporate Development','vpsales'=>'VP Sales','waterspec'=>'Water Specialist');

$custom_count = 1;
foreach($level_array as $level => $label) {
  $security_level = mysqli_fetch_assoc(mysqli_query($dbc, "select identifier from security_level_names where identifier = '$level'"));
  if($security_level['identifier'] == '' || $security_level['identifier'] == null) {
    mysqli_query($dbc, "insert into security_level_names(`identifier`, `active`, `history`, `deleted`) VALUES('$level', 0, '', 0)");
  }
  else {
      $security_order = mysqli_fetch_assoc(mysqli_query($dbc, "select custom_order from security_level_names where identifier = '$level'"));
    if($security_order['custom_order'] == '' || $security_order['custom_order'] == null || $security_order['custom_order'] == NULL) {
      mysqli_query($dbc, "update security_level_names set custom_order = $custom_count where identifier = '$level'");
    }
  }

  $security_label = mysqli_fetch_assoc(mysqli_query($dbc, "select label from security_level_names where identifier = '$level'"));
  if($security_label['label'] == '' || $security_label['label'] == null) {
    //echo "update security_level_names set label = '$label' where identifier = '$level'";
    mysqli_query($dbc, "update security_level_names set label = '$label' where identifier = '$level'");
  }

  $custom_count++;
}

echo "<br> ======Jenish's db changes Done======<br>";
?>
