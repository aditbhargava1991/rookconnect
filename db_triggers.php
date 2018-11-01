<?php include_once('database_connection.php');
// 2018-01-16 Ticket 5719 - Baldwin
if(!mysqli_query($dbc, "DROP TRIGGER IF EXISTS `sales_order_temp_status_date`;")) {
	echo "Error: ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "CREATE TRIGGER `sales_order_temp_status_date` BEFORE UPDATE ON `sales_order_temp`
	FOR EACH ROW
	BEGIN
		IF NEW.`status` != OLD.`status` THEN
			SET NEW.`status_date` = CURDATE();
		END IF;
	END;")) {
	echo "Error (sales_order_temp_status_date): ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "DROP TRIGGER IF EXISTS `sales_order_status_date`;")) {
	echo "Error: ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "CREATE TRIGGER `sales_order_status_date` BEFORE UPDATE ON `sales_order`
	FOR EACH ROW
	BEGIN
		IF NEW.`status` != OLD.`status` THEN
			SET NEW.`status_date` = CURDATE();
		END IF;
	END;")) {
	echo "Error (sales_order_status_date): ".mysqli_error($dbc)."<br />\n";
}

//2018-02-08 - Ticket #6097 - Task Auto Archive Completed Tasks - Baldwin
if(!mysqli_query($dbc, "DROP TRIGGER IF EXISTS `tasklist_status_date`;")) {
	echo "Error: ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "CREATE TRIGGER `tasklist_status_date` BEFORE UPDATE ON `tasklist`
	FOR EACH ROW
	BEGIN
		IF NEW.`status` != OLD.`status` THEN
			SET NEW.`status_date` = CURDATE();
		END IF;
	END;")) {
	echo "Error (tasklist_status_date): ".mysqli_error($dbc)."<br />\n";
}
//2018-02-21 - Ticket #6254 - Calendar Speed - Baldwin
if(!mysqli_query($dbc, "DROP TRIGGER IF EXISTS `tickets_last_updated`;")) {
	echo "Error: ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "CREATE TRIGGER `tickets_last_updated` BEFORE UPDATE ON `tickets`
	FOR EACH ROW
	BEGIN
		SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
	END;")) {
	echo "Error (tickets_last_updated): ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "DROP TRIGGER IF EXISTS `ticket_schedule_last_updated`;")) {
	echo "Error: ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "CREATE TRIGGER `ticket_schedule_last_updated` BEFORE UPDATE ON `ticket_schedule`
	FOR EACH ROW
	BEGIN
		SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
	END;")) {
	echo "Error (ticket_schedule_last_updated): ".mysqli_error($dbc)."<br />\n";
}
// 2018-02-28 - Jonathan
if(!mysqli_query($dbc, "DROP TRIGGER IF EXISTS `tasklist_status_date`;")) {
	echo "Error: ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "DROP TRIGGER IF EXISTS `tasklist_update`;")) {
	echo "Error: ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "CREATE TRIGGER `tasklist_update` BEFORE UPDATE ON `tasklist`
	FOR EACH ROW
	BEGIN
		IF NEW.`status` != OLD.`status` THEN
			SET NEW.`status_date` = CURDATE();
		END IF;
		SET NEW.`updated_date` = CURRENT_TIMESTAMP;
	END;")) {
	echo "Error (tasklist_update): ".mysqli_error($dbc)."<br />\n";
}
//2018-03-07 - Calendar Shift Changes - Baldwin
if(!mysqli_query($dbc, "DROP TRIGGER IF EXISTS `contacts_shifts_last_updated`;")) {
	echo "Error: ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "CREATE TRIGGER `contacts_shifts_last_updated` BEFORE UPDATE ON `contacts_shifts`
	FOR EACH ROW
	BEGIN
		SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
	END;")) {
	echo "Error (contacts_shifts_last_updated): ".mysqli_error($dbc)."<br />\n";
}
// 2018-10-23 - Jonathan
if(!mysqli_query($dbc, "DROP TRIGGER IF EXISTS `ticket_schedule_last_updated`;")) {
	echo "Error: ".mysqli_error($dbc)."<br />\n";
}
if(!mysqli_query($dbc, "CREATE TRIGGER `ticket_schedule_last_updated` BEFORE UPDATE ON `ticket_schedule`
	FOR EACH ROW
	BEGIN
		IF NEW.`status` != OLD.`status` THEN
			SET NEW.`status_date` = CURRENT_TIMESTAMP();
		END IF;
		SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
	END;")) {
	echo "Error (ticket_schedule_last_updated): ".mysqli_error($dbc)."<br />\n";
}