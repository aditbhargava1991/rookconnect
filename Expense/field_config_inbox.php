<?php /* Field Configuration for Expenses */
/*Comment Section*/

if (isset($_POST['submit'])) {
    $role_arr = $_POST['approval_role']; 
    mysqli_query($dbc, "TRUNCATE TABLE `expense_approval_levels`");
    if(!empty($role_arr)){
        $key = '1';
        foreach($role_arr as $ra){
            mysqli_query($dbc, "INSERT INTO `expense_approval_levels` (`expense_approval_role_id`,`expense_role_sorting`) values('".$ra."','".$key."') ");
            $key++;
        }
    }
    $expense = implode(',',$_POST['expense']);
    $expense_defaults = implode(',',$_POST['expense_defaults']);
    $expense_mode = $_POST['expense_mode'];
	mysqli_query($dbc, "INSERT INTO `field_config_expense` (`tab`) SELECT 'current_month' FROM (SELECT COUNT(*) rows FROM `field_config_expense` WHERE `tab`='current_month') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_expense` SET `expense_dashboard`='$expense_defaults', `expense_mode`='$expense_mode' WHERE `tab`='current_month'");

    $expense_category_field = implode(',',$_POST['expense_category_field']);
	$tab_category = $_POST['expense_category_field_name'];
	if($tab_category != '') {
		mysqli_query($dbc, "INSERT INTO `field_config_expense` (`tab`) SELECT 'category_".$tab_category."' FROM (SELECT COUNT(*) rows FROM `field_config_expense` WHERE `tab`='category_".$tab_category."') num WHERE num.rows=0");
		mysqli_query($dbc, "UPDATE `field_config_expense` SET `expense_dashboard`='$expense_category_field', `expense_mode`='$expense_mode' WHERE `tab`='category_".$tab_category."'");
	}

	$tab = '%';
    $expense_types = filter_var($_POST['expense_types'],FILTER_SANITIZE_STRING);
    $hst_name = filter_var($_POST['hst_name'],FILTER_SANITIZE_STRING);
    $pst_name = filter_var($_POST['pst_name'],FILTER_SANITIZE_STRING);
    $gst_name = filter_var($_POST['gst_name'],FILTER_SANITIZE_STRING);
    $hst_amt = filter_var($_POST['hst_amt'],FILTER_SANITIZE_STRING);
    $pst_amt = filter_var($_POST['pst_amt'],FILTER_SANITIZE_STRING);
    $gst_amt = filter_var($_POST['gst_amt'],FILTER_SANITIZE_STRING);
    $expense_rows = filter_var($_POST['expense_rows'],FILTER_SANITIZE_STRING);
	$exchange_buffer = filter_var($_POST['exchange_buffer'] / 100,FILTER_SANITIZE_STRING);

    $expense_tabs = implode(',',$_POST['expense_tabs']);
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config_expense WHERE `tab` LIKE '$tab'"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_config = "UPDATE `field_config_expense` SET expense = '$expense', `exchange_buffer`='$exchange_buffer', `gst_name`='$gst_name', `pst_name`='$pst_name', `hst_name`='$hst_name', `gst_amt`='$gst_amt', `pst_amt`='$pst_amt', `hst_amt`='$hst_amt', `expense_types`='$expense_types', `expense_rows`='$expense_rows' WHERE `tab` LIKE '$tab'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `field_config_expense` (`expense`, `exchange_buffer`, `gst_name`, `pst_name`, `hst_name`, `gst_amt`, `pst_amt`, `hst_amt`, `expense_types`, `expense_rows`, `tab`)
			VALUES ('$expense', '$exchange_buffer', '$gst_name', '$pst_name', '$hst_name', '$gst_amt', '$pst_amt', '$hst_amt', '$expense_types', '$expense_rows', 'current_month')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_tabs'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_tabs' WHERE name='expense_tabs'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_tabs', '$expense_tabs')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	set_config($dbc, 'expense_default_staff', $_POST['default_staff']);

	$default_country = filter_var($_POST['default_country'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='default_country'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$default_country' WHERE name='default_country'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('default_country', '$default_country')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$default_province = filter_var($_POST['default_province'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='default_province'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$default_province' WHERE name='default_province'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('default_province', '$default_province')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$expense_reminder_days = filter_var($_POST['expense_reminder_days'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_reminder_days'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_reminder_days' WHERE name='expense_reminder_days'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_reminder_days', '$expense_reminder_days')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$expense_reminder_sender = filter_var($_POST['expense_reminder_sender'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_reminder_sender'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_reminder_sender' WHERE name='expense_reminder_sender'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_reminder_sender', '$expense_reminder_sender')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$expense_reminder_subject = filter_var($_POST['expense_reminder_subject'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_reminder_subject'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_reminder_subject' WHERE name='expense_reminder_subject'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_reminder_subject', '$expense_reminder_subject')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$expense_reminder_body = filter_var(htmlentities($_POST['expense_reminder_body']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_reminder_body'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_reminder_body' WHERE name='expense_reminder_body'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_reminder_body', '$expense_reminder_body')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$expense_provinces = [];
	foreach($_POST['province_code'] as $row => $value) {
		if(trim($value) != '') {
			$expense_provinces[] = filter_var($value,FILTER_SANITIZE_STRING).'*'.
				filter_var($_POST['province_gst'][$row],FILTER_SANITIZE_STRING).'*'.
				filter_var($_POST['province_pst'][$row],FILTER_SANITIZE_STRING).'*'.
				filter_var($_POST['province_hst'][$row],FILTER_SANITIZE_STRING);
		}
	}
	$expense_provinces = implode('#*#',$expense_provinces);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_provinces'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_provinces' WHERE name='expense_provinces'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_provinces', '$expense_provinces')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    //Logo
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    $expense_logo = htmlspecialchars($_FILES["logo"]["name"], ENT_QUOTES);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_logo'"));
    if($get_config['configid'] > 0) {
		if($expense_logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $expense_logo;
		}
		move_uploaded_file($_FILES["logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='expense_logo'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
		move_uploaded_file($_FILES["logo"]["tmp_name"], "download/" . $_FILES["logo"]["name"]) ;
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_logo', '$expense_logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    //Logo

    //Header
    $survey = htmlentities($_POST['expense_header']);
    $expense_header = filter_var($survey,FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_header'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$expense_header' WHERE name='expense_header'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_header', '$expense_header')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Header

    //Footer
    $survey = htmlentities($_POST['expense_footer']);
    $expense_footer = filter_var($survey,FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_footer'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$expense_footer' WHERE name='expense_footer'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_footer', '$expense_footer')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Footer

	// Categories
	$all_ids = implode(',',array_filter($_POST['cat_id']));
    $date_of_archival = date('Y-m-d');
	$delete_rows = mysqli_query($dbc, "UPDATE `expense_categories` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `expense_tab` LIKE '$tab' AND `categoryid` NOT IN ($all_ids)");

	foreach($_POST['cat_id'] as $row => $id) {
		$category = $_POST['category'][$row];
		$heading = $_POST['cat_heading'][$row];
		$amount = $_POST['cat_amount'][$row];
		$gl = $_POST['heading_code'][$row];
		$ec = floor($gl / 1000) * 1000;

		if($heading != '' && $category != '') {
			if($id == '') {
				//$ec = mysqli_fetch_array(mysqli_query($dbc, "SELECT `EC` FROM `expense_categories` WHERE `category`='$category' AND `deleted`=0 AND `expense_tab` LIKE '$tab' UNION SELECT IFNULL(MAX(`EC`),0) + 1000 FROM `expense_categories` WHERE `expense_tab` LIKE '$tab' AND `deleted`=0"))['EC'];
				//$gl = mysqli_fetch_array(mysqli_query($dbc, "SELECT `GL` FROM `expense_categories` WHERE `categoryid`='$id' AND `deleted`=0 UNION SELECT IFNULL(MAX(`GL`),$ec) + 1 FROM `expense_categories` WHERE `expense_tab` LIKE '$tab' AND `category`='$category' AND `deleted`=0"))['GL'];
				$query = "INSERT INTO `expense_categories` (`expense_tab`, `category`, `EC`, `heading`, `GL`, `amount`)
					VALUES ('business', '$category', '$ec', '$heading', '$gl', '$amount')";
			} else {
				$old_cat = preg_replace('/[^a-z]/','_',strtolower(mysqli_fetch_array(mysqli_query($dbc, "SELECT `category` FROM `expense_categories` WHERE `categoryid`='$id'"))['category']));
				$new_cat = preg_replace('/[^a-z]/','_',strtolower($category));
				mysqli_query($dbc, "UPDATE `field_config_expense` SET `tab`='category_".$new_cat."' WHERE `tab`='category_".$old_cat."'");
				$query = "UPDATE `expense_categories` SET `category`='$category', `EC`='$ec', `heading`='$heading', `GL`='$gl', `amount`='$amount' WHERE `categoryid`='$id'";
			}
			mysqli_query($dbc, $query);
		}
	}
	// Categories
}

// Variables
$config_sql = "SELECT * FROM (SELECT expense, expense_dashboard, exchange_buffer, gst_name, gst_amt, pst_name, pst_amt, hst_name, hst_amt, expense_types, expense_rows, tab, expense_mode FROM field_config_expense
	WHERE `tab`='current_month' OR `tab` LIKE 'category_%' UNION SELECT
	'Flight,Hotel,Breakfast,Lunch,Dinner,Beverages,Transportation,Entertainment,Gas,Misc',
	'Description,Date,Receipt,Type,Day Expense,Amount,Tax,Total', '0', 'GST', '5', 'PST', '0', 'HST', '0', 'Meals,Tip', 1, '', '') settings ORDER BY `tab` DESC";
$get_expense_config = mysqli_fetch_assoc(mysqli_query($dbc,$config_sql));
$value_config = ','.$get_expense_config['expense'].',';
$db_config = ','.$get_expense_config['expense_dashboard'].',';
$gst_name = trim($get_expense_config['gst_name'],',');
$pst_name = trim($get_expense_config['pst_name'],',');
$hst_name = trim($get_expense_config['hst_name'],',');
$gst_amt = trim($get_expense_config['gst_amt'],',');
$pst_amt = trim($get_expense_config['pst_amt'],',');
$hst_amt = trim($get_expense_config['hst_amt'],',');
$expense_types = trim(','.$get_expense_config['expense_types'].',',',');
$expense_rows = $get_expense_config['expense_rows'];
$exchange_buffer = $get_expense_config['exchange_buffer'];
$expense_mode = $get_expense_config['expense_mode'];
?>
<div class="has-main-screen scale-to-fill hide-titles-mob">
<div class="main-screen standard-dashboard-body">
    <div class="standard-body-content full-height">
        <div class="dashboard-item dashboard-item2 full-height">
            <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="settingTabId" value="categorySettingTab">
            <div class="col-sm-12 setting-fields">
            	<div class="category_div" style="">
            		<script>
                    $(document).on('change', 'select[name="expense_category_field_name"]', function() { category_fields(); });
            		function set_category(input) {
            			$('input[name="category[]"][data-category="'+$(input).data('category')+'"]').val(input.value);
            		}
            		function set_category_code(input) {
            			var gl_code = input.value;
            			$('input[name="heading_code[]"][data-category="'+$(input).data('category')+'"]').each(function() {
            				var this_code = +this.value % 1000;
            				this.value = this_code + +gl_code;
            				this.min = +gl_code;
            			});
            		}
            		function add_category() {
            			$('.category_div table').append('<tr><td><input type="hidden" name="cat_id[]" value=""><input type="hidden" name="cat_heading[]" value="New Heading"><input type="hidden" name="cat_amount[]" value=0><input type="text" name="category[]" class="form-control"></td><td><input type="number" min=0 step=1000 name="heading_code[]" class="form-control"></td><td></td></tr>');
            		}
            		function remove_category(button) {
            			console.log(button);
            			$('input[data-category="'+button+'"]').closest('.panel').remove();
            			$('input[data-category="'+button+'"]').closest('tr').remove();
            			//$('input[name="src_category[]"][data-category="'+$(input).data('category')+'"]').closest('.panel').remove();
            			//$(button).closest('tr').remove();
            		}
            		</script>
            		<table class="table table-bordered">
            			<tr>
            				<th>Category</th>
            				<th>EC Code</th>
            				<th><button onclick="add_category(); return false;" class="btn brand-btn">Add Category</button></th>
            			</tr>
            		<?php $categories_result = mysqli_query($dbc, "SELECT `category`, `EC` FROM `expense_categories` WHERE `deleted`=0 GROUP BY `category`, `EC` ORDER BY `EC`");
            		do { ?>
            			<tr>
            				<td data-title="Category"><input type="text" name="src_category[]" data-category="cat_name_<?= $category['category'] ?>" value="<?= $category['category'] ?>" class="form-control" onchange="set_category(this);"></td>
            				<td data-title="EC Code"><input type="number" min="0" step="1000" data-category="cat_name_<?= $category['category'] ?>" value="<?= $category['EC'] ?>" class="form-control" onchange="set_category_code(this);"></td>
            				<td data-title=""><button data-category="cat_name_<?= $category['category'] ?>" onclick="remove_category('cat_name_<?= $category['category'] ?>'); return false;" class="btn brand-btn">Delete</button></td>
            			</tr>
            		<?php } while($category = mysqli_fetch_array($categories_result)); ?>
            		</table>
            	</div>
            	<div class="heading_div" style="display: none;">
            		<script>
            		var new_category = 0;
            		function add_heading(category) {
            			var row = $('input[name=\'category[]\'][data-category=\''+category+'\']').last().closest('tr');
            			var new_row = row.clone();
            			new_row.find('input[name!="category[]"]').val('');
            			var max_code = 0;
            			$('input[name=\'category[]\'][data-category=\''+category+'\']').each(function() {
            				var code = $(this).closest('tr').find('[name="heading_code[]"]').val();
            				if(code > max_code) {
            					max_code = code;
            				}
            			});
            			new_row.find('input[name="heading_code[]"]').val(+max_code + 1);
            			row.after(new_row);
            			row.nextAll('tr').find('input[type=text]').first().focus();
            		}
            		</script>
            		<h3>Heading</h3>
            		Below are the expense headings your staff will see on the web and in the app. Click on the category to edit and add subcategories for that category.
            		<div class="panel-group block-panels" id="category_div_panel">
            			<?php $categories_result = mysqli_query($dbc, "SELECT `categoryid`, `category`, `EC`, `heading`, `GL`, `amount` FROM `expense_categories` WHERE `deleted`=0 ORDER BY `EC`, `GL`");
            			$category_name = '';
            			$table_exists = false;
            			$collapse_id = 0;
            			while($row = mysqli_fetch_array($categories_result)) {
            				if($row['category'] != $category_name) {
            					if($table_exists) { ?>
            									</table>
            								</div>
            							</div>
            						</div>
            					<?php }
            					$table_exists = true;
            					$category_name = $row['category']; ?>
            					<div class="panel panel-default">
            						<div class="panel-heading">
            							<h4 class="panel-title">
            								<a data-toggle="collapse" data-parent="#category_div_panel" href="#collapse_<?= $collapse_id ?>" >
            									Category: <?= $category_name != '' ? 'EC '.$row['EC'].': '.$category_name : 'New Category' ?><span class="glyphicon glyphicon-plus"></span>
            								</a>
            							</h4>
            						</div>

            						<div id="collapse_<?= $collapse_id++ ?>" class="panel-collapse collapse">
            							<div class="panel-body">
            								<table class="table table-bordered" id="category_table">
            									<tr class="hidden-xs hidden-sm">
            										<th style="width: 50%;">Heading</th>
            										<th style="width: 20%;"><span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Setting a Per Diem amount will set a specific amount for expenses with this heading, and it will not be editable."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            											Per Diem Amount</th>
            										<th style="width: 15%;">GL Code</th>
            										<th style="width:1em;"><button class="btn brand-btn pull-right" onclick="add_heading('cat_name_<?= $category_name ?>'); return false;">Add Heading</button></th>
            									</tr>
            				<?php } ?>
            					<tr>
            						<input type="hidden" name="cat_id[]" value="<?php echo $row['categoryid']; ?>">
            						<input type="hidden" name="category[]" class="form-control" value="<?php echo $row['category']; ?>" data-category="cat_name_<?= $category_name ?>">
            						<td data-title="Heading"><input type="text" name="cat_heading[]" class="form-control" value="<?php echo $row['heading']; ?>"></td>
            						<td data-title="Per Diem Amount"><input type="number" min="0" step="0.01" name="cat_amount[]" class="form-control" value="<?php echo $row['amount']; ?>"></td>
            						<td data-title="GL Code"><input data-category="cat_name_<?= $category_name ?>" type="number" min=<?= $row['EC'] ?> max=<?= $row['EC'] + 1000 ?> step=1 name="heading_code[]" value="<?= $row['GL'] ?>" class="form-control"></td>
            						<td><a href="" onclick="$(this).closest('tr').remove(); return false;">Delete</a></td>
            					</tr>
            			<?php }
            			if($table_exists) { ?>
            							</table>
            							<!--<button onclick="add_category(); return false;" class="btn brand-btn pull-right">Add Category</button>-->
            						</div>
            					</div>
            				</div>
            			<?php } ?>
            		</div>
            	</div>
            	
            	<div class="manage_levels" style="display: none;">
            		<div class="standard-dashboard-body-content">
                    <div class="dashboard-item dashboard-item2 full-height">
                        <div class="form-horizontal block-group block-group-noborder full-height">
                            <!--<label class="hide-titles-mob col-sm-4 pull-right"><span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Select the Category to which you can attach other contacts, such as business."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                Active Level</label>--><div class="clearfix"></div>
                            <?php 
                            $approval_levels = mysqli_query($dbc, "SELECT expense_approval_role_id FROM `expense_approval_levels` ORDER BY expense_role_sorting");
                            $app_row = mysqli_fetch_all($approval_levels);
                            if(!empty($app_row)){
                            foreach($app_row as $app) {
                            ?>
                                <div class="form-group tab-group">
                                    <label class="col-sm-4">Levels:<br /></label>
                                    <div class="col-sm-5">
                                    	<select name="approval_role[]" class="form-control approval_role">
                                        <?php 
                                        $security_levels = mysqli_query($dbc, "SELECT * FROM `security_level_names` WHERE `active` > 0 AND `identifier` NOT LIKE 'FFMCUST_%'");
                                        while($row = mysqli_fetch_assoc($security_levels)) {
                                        ?>
                                        	<option <?= $app[0] == $row['id'] ? 'selected="selected"' : '' ?> value="<?= $row['id']?>"><?= $row['label']?></option>
                                        <?php
                                        }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-1">
                                        <label class="show-on-mob">Attach to approval level</label>
                                        <!--<input type="radio" name="business_category" <?= $app['expense_approval_active'] == '1' ? 'checked' : '' ?> value="<?= $app['expense_approval_role_id'] ?>"> -->
                                    </div>
                                    <div class="col-sm-2">
                                        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option();">
                                        <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this);">
                                        <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
                                    </div>
                                </div>
                            <?php }
                                }else{ ?>
                                <div class="form-group tab-group">
                                    <label class="col-sm-4">Levels:<br /></label>
                                    <div class="col-sm-5">
                                    <select name="approval_role[]" class="form-control approval_role">
                                    <?php
                                    $security_levels = mysqli_query($dbc, "SELECT * FROM `security_level_names` WHERE `active` > 0 AND `identifier` NOT LIKE 'FFMCUST_%'");
                                    while($row = mysqli_fetch_assoc($security_levels)) {
                                    ?>
                                    	<option value="<?= $row['id']?>"><?= $row['label']?></option>
                                    <?php
                                    }
                                    ?>
                                    </select>
                                    </div>
                                    <div class="col-sm-1">
                                        <label class="show-on-mob">Attach to approval level</label>
                                        <!--<input type="radio" name="business_category" value="">-->
                                    </div>
                                    <div class="col-sm-2">
                                        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option();">
                                        <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this);">
                                        <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
                                    </div>
                                </div>
                            <?php }?>
                        </div>
                    </div><!-- .dashboard-item -->
                </div><!-- .standard-dashboard-body-content -->
            	</div>
            	
            	<div class="style_div" style="display: none;">
            		<h3>Expense Tile Mode</h3>
            		<label class="form-checkbox"><input type="radio" name="expense_mode" value="tables" <?php if($expense_mode == 'tables'){ echo "checked";}?>> Table Mode</label>
            		<label class="form-checkbox"><input type="radio" name="expense_mode" value="inbox" <?php if($expense_mode == 'inbox'){ echo "checked";}?>> Inbox Mode</label>
            	</div>
            	<div class="expense_defaults_div" style="display: none;">
            		<h3>Expense Fields</h3>
            		<script>
            		$(document).ready(function() {
            			$('.expense_defaults_div').sortable({
            				connectWith: '.expense_defaults_div',
            				handle: '.sort-handle',
            				items: 'label:not(.no-sort)'
            			});

            			$('.approval_role').change(save_fields);
            			$('.block-group').sortable({
            				connectWith: '.block-group',
            				handle: '.drag-handle',
            				items: '.tab-group',
            				update: save_fields
            			});
            		});
            		function save_fields() {
            			var options = [];
            			var business_category = '';
            			$('.approval_role').each(function() {
            				this.value = this.value.replace(',','');
            				options.push(this.value);
            			});
            		}
            		function add_option() {
            			var row = $('.tab-group').last();
            			var clone = row.clone();
            			clone.find('input').val('');
            			row.after(clone);
            			$('.approval_role').off('change', save_fields).change(save_fields);
            		}
            		function remove_option(target) {
            			if($('.tab-group').length <= 0) {
            				add_option();
            			}
            			$(target).closest('.form-group').remove();
            			save_fields();
            		}
            		</script>
            		<?php $db_config = explode(',',trim($db_config,','));
            		$db_config_arr = array_filter(array_unique(array_merge($db_config,explode(',','Exchange,Tips,Third Tax,Local Tax,Tax,Description,Date,Work Order,Project,Province,Country,Vendor,Category,Heading,Reimburse,Receipt'))));
            		echo '<label class="form-checkbox no-sort"><input type="checkbox" '.(in_array('Exchange',$db_config) ? 'checked' : '').' value="Exchange" name="expense_defaults[]"> '.'Exchange Currency'.'</label>';
            		echo '<label class="form-checkbox no-sort"><input type="checkbox" '.(in_array('Tips',$db_config) ? 'checked' : '').' value="Tips" name="expense_defaults[]"> '.'Tips'.'</label>';
            		echo '<label class="form-checkbox no-sort"><input type="checkbox" '.(in_array('Third Tax',$db_config) ? 'checked' : '').' value="Third Tax" name="expense_defaults[]"> '.($hst_name == '' ? 'Third Tax' : $hst_name).'</label>';
            		echo '<label class="form-checkbox no-sort"><input type="checkbox" '.(in_array('Local Tax',$db_config) ? 'checked' : '').' value="Local Tax" name="expense_defaults[]"> '.($pst_name == '' ? 'Additional Tax' : $pst_name).'</label>';
            		echo '<label class="form-checkbox no-sort"><input type="checkbox" '.(in_array('Tax',$db_config) ? 'checked' : '').' value="Tax" name="expense_defaults[]"> '.($gst_name == '' ? 'Tax' : $gst_name).'</label>';
            		echo '<label class="form-checkbox no-sort"><input type="checkbox" '.(in_array('Total',$db_config) ? 'checked' : '').' value="Total" name="expense_defaults[]"> Total</label>';
            		foreach($db_config_arr as $field) {
            			if($field == 'Description') {
            				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_defaults[]"> Description<img class="inline-img sort-handle pull-right" src="../img/icons/drag_handle.png"></label>';
            			} else if($field == 'Date') {
            				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_defaults[]"> Expense Date<img class="inline-img sort-handle pull-right" src="../img/icons/drag_handle.png"></label>';
            			} else if($field == 'Work Order') {
            				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_defaults[]"> Work Order<img class="inline-img sort-handle pull-right" src="../img/icons/drag_handle.png"></label>';
            			} else if($field == 'Project') {
            				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_defaults[]"> '.PROJECT_TILE.'<img class="inline-img sort-handle pull-right" src="../img/icons/drag_handle.png"></label>';
            			} else if($field == 'Province') {
            				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_defaults[]"> Province of Expense<img class="inline-img sort-handle pull-right" src="../img/icons/drag_handle.png"></label>';
            			} else if($field == 'Country') {
            				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_defaults[]"> Country of Expense<img class="inline-img sort-handle pull-right" src="../img/icons/drag_handle.png"></label>';
            			} else if($field == 'Vendor') {
            				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_defaults[]"> Vendor<img class="inline-img sort-handle pull-right" src="../img/icons/drag_handle.png"></label>';
            			} else if($field == 'Category') {
            				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_defaults[]"> Category<img class="inline-img sort-handle pull-right" src="../img/icons/drag_handle.png"></label>';
            			} else if($field == 'Heading') {
            				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_defaults[]"> Heading<img class="inline-img sort-handle pull-right" src="../img/icons/drag_handle.png"></label>';
            			} else if($field == 'Reimburse') {
            				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_defaults[]"> Reimburse<img class="inline-img sort-handle pull-right" src="../img/icons/drag_handle.png"></label>';
            			}
            		}
            		echo '<label class="form-checkbox no-sort"><input type="checkbox" '.(in_array('Receipt',$db_config) ? 'checked' : '').' value="Receipt" name="expense_defaults[]"> Receipt</label>';
            		?>
            	</div>
            	<div class="expense_fields_div" style="display: none;">
            		<script>
            		function category_fields() {
            			var category = $('[name=expense_category_field_name]').val();
            			$('[name="expense_category_field[]"]').removeAttr('checked');
            			if(category == '') {
            				$('[name="expense_category_field[]"]').removeAttr('checked');
            			} else {
            				$.ajax({
            					url: 'inbox_ajax.php?action=category_fields',
            					method: 'POST',
            					data: { category: category },
            					success: function(response) {console.log(response);
            						response.split(',').forEach(function(field) {
            							$('[name="expense_category_field[]"][value="'+field+'"]').prop('checked','checked');
            						});
            					}
            				});
            			}
            		}
            		</script>
            		<h3>Expense Fields for</h3>
                    <select data-placeholder="Select a Category" name="expense_category_field_name" class="form-control chosen-select-deselect"><option></option>
            			<?php if($get_expense_config['tab'] == 'current_month') {
            				$categories = mysqli_query($dbc, "SELECT `category`, CONCAT('EC ',`EC`,': ',`category`) label FROM `expense_categories` WHERE `category` != '' AND `deleted`=0  GROUP BY `category` ORDER BY `EC`, `category`");
            				while($ex_category_name = mysqli_fetch_array($categories)) {
            					echo "<option value='".preg_replace('/[^a-z]/','_',strtolower($ex_category_name['category']))."'>".$ex_category_name['label']."</option>";
            				}
            			} else {
            				echo "<option>Please set the default fields for Expenses before setting the category fields.</option>";
            			} ?>
            			</select>
            		<?php if(in_array('Exchange',$db_config)) {
            			echo '<label class="form-checkbox"><input type="checkbox" value="Exchange" name="expense_category_field[]"> '.'Exchange Currency'.'</label>';
            		}
            		if(in_array('Tips',$db_config)) {
            			echo '<label class="form-checkbox"><input type="checkbox" value="Tips" name="expense_category_field[]"> '.'Tips'.'</label>';
            		}
            		if(in_array('Third Tax',$db_config)) {
            			echo '<label class="form-checkbox"><input type="checkbox" value="Third Tax" name="expense_category_field[]"> '.($hst_name == '' ? 'Third Tax' : $hst_name).'</label>';
            		}
            		if(in_array('Local Tax',$db_config)) {
            			echo '<label class="form-checkbox"><input type="checkbox" value="Local Tax" name="expense_category_field[]"> '.($pst_name == '' ? 'Additional Tax' : $pst_name).'</label>';
            		}
            		if(in_array('Tax',$db_config)) {
            			echo '<label class="form-checkbox"><input type="checkbox" value="Tax" name="expense_category_field[]"> '.($gst_name == '' ? 'Tax' : $gst_name).'</label>';
            		}
            		if(in_array('Total',$db_config)) {
            			echo '<label class="form-checkbox"><input type="checkbox" value="Total" name="expense_category_field[]"> Total</label>';
            		}
            		foreach($db_config as $field) {
            			if($field == 'Description') {
            				echo '<label class="form-checkbox"><input type="checkbox" value="'.$field.'" name="expense_category_field[]"> Description</label>';
            			} else if($field == 'Date') {
            				echo '<label class="form-checkbox"><input type="checkbox" value="'.$field.'" name="expense_category_field[]"> Expense Date</label>';
            			} else if($field == 'Work Order') {
            				echo '<label class="form-checkbox"><input type="checkbox" value="'.$field.'" name="expense_category_field[]"> Work Order</label>';
            			} else if($field == 'Project') {
            				echo '<label class="form-checkbox"><input type="checkbox" value="'.$field.'" name="expense_category_field[]"> '.PROJECT_TILE.'</label>';
            			} else if($field == 'Province') {
            				echo '<label class="form-checkbox"><input type="checkbox" value="'.$field.'" name="expense_category_field[]"> Province of Expense</label>';
            			} else if($field == 'Country') {
            				echo '<label class="form-checkbox"><input type="checkbox" value="'.$field.'" name="expense_category_field[]"> Country of Expense</label>';
            			} else if($field == 'Vendor') {
            				echo '<label class="form-checkbox"><input type="checkbox" value="'.$field.'" name="expense_category_field[]"> Vendor</label>';
            			} else if($field == 'Category') {
            				echo '<label class="form-checkbox"><input type="checkbox" value="'.$field.'" name="expense_category_field[]"> Category</label>';
            			} else if($field == 'Heading') {
            				echo '<label class="form-checkbox"><input type="checkbox" value="'.$field.'" name="expense_category_field[]"> Heading</label>';
            			} else if($field == 'Reimburse') {
            				echo '<label class="form-checkbox"><input type="checkbox" value="'.$field.'" name="expense_category_field[]"> Reimburse</label>';
            			}
            		}
            		if(in_array('Receipt',$db_config)) {
            			echo '<label class="form-checkbox"><input type="checkbox" value="Receipt" name="expense_category_field[]"> Receipt</label>';
            		} ?>
            	</div>
            	<div class="pdf_div" style="display: none;">
            		<div class="form-group">
            		<label for="file[]" class="col-sm-4 control-label">Header Logo
            		<span class="popover-examples list-inline">&nbsp;
            		<a  data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
            		</span>
            		:</label>
            		<div class="col-sm-8">
            		<?php
            			$logo = get_config($dbc, 'expense_logo');
            			if($logo != '') {
            			echo '<a href="download/'.$logo.'" target="_blank">View</a>';
            			?>
            			<input type="hidden" name="logo_file" value="<?php echo $logo; ?>" />
            			<input name="logo" type="file" data-filename-placement="inside" class="form-control" />
            		  <?php } else { ?>
            		  <input name="logo" type="file" data-filename-placement="inside" class="form-control" />
            		  <?php } ?>
            		</div>
            		</div>

            		<div class="form-group">
            			<label for="office_country" class="col-sm-4 control-label">Header Info:<br><em>(e.g. - company name, address, phone, etc.)</em></label>
            			<div class="col-sm-8">
            				<textarea name="expense_header" rows="3" cols="50" class="form-control"><?php echo get_config($dbc, 'expense_header'); ?></textarea>
            			</div>
            		</div>

            		<div class="form-group">
            			<label for="office_country" class="col-sm-4 control-label">Footer Info:<br><em>(e.g. - company name, address, phone, etc.)</em></label>
            			<div class="col-sm-8">
            				<textarea name="expense_footer" rows="3" cols="50" class="form-control"><?php echo get_config($dbc, 'expense_footer'); ?></textarea>
            			</div>
            		</div>
            	</div>
            	<div class="email_reminders_div" style="display: none;">
            		<h3>Expense Reminder</h3>
            		<div class="form-group">
            			<label class="col-sm-4 control-label">Days Before End of Month:<br /><em>To disable reminders, set this field to 0.<br />Any higher number will send a reminder that many days before the last day of the month.</em></label>
            			<div class="col-sm-8">
            				<input type="number" name="expense_reminder_days" value="<?= get_config($dbc, 'expense_reminder_days') ?>" class="form-control" min=0>
            			</div>
            		</div>
            		<div class="form-group">
            			<label class="col-sm-4 control-label">Email Sender:</label>
            			<div class="col-sm-8">
            				<input type="text" name="expense_reminder_sender" value="<?= get_config($dbc, 'expense_reminder_sender') ?>" class="form-control">
            			</div>
            		</div>
            		<div class="form-group">
            			<label class="col-sm-4 control-label">Email Subject:</label>
            			<div class="col-sm-8">
            				<input type="text" name="expense_reminder_subject" value="<?= get_config($dbc, 'expense_reminder_subject') ?>" class="form-control">
            			</div>
            		</div>
            		<div class="form-group">
            			<label class="col-sm-4 control-label">Email Body:</label>
            			<div class="col-sm-8">
            				<textarea name="expense_reminder_body" class="form-control"><?= get_config($dbc, 'expense_reminder_body') ?></textarea>
            			</div>
            		</div>
            	</div>
            	<div class="exchange_rates_div" style="display: none;">
            		<h3>Exchange Rates</h3>
            		<p><em>Exchange Rates are loaded from the Bank of Canada when the Expense is entered, and uses the exchange rate for the expense date, if available. The rates are available from
            			2017-01-02. All Bank of Canada exchange rates are indicative rates only, obtained from averages of aggregated price quotes from financial institutions. You can read their full
            			<a href="https://www.bankofcanada.ca/terms/#fx-rates">Terms and Conditions</a> for details.</em></p>
            		<div class="form-group">
            			<label class="col-sm-4 control-label">Exchange Rate Buffer Allowance:<br /><em>Exchange rates listed are the average exchange rate for the day. When exchanging currency, there
            				is an additional amount that is typically charged, often referred to as a currency conversion fee of two to three percent. If you wish to automatically add a percentage
            				allowance to cover this fee, enter the percent here.</em></label>
            			<div class="col-sm-8">
            				<input type="number" name="exchange_buffer" step="any" value="<?= $exchange_buffer * 100 ?>" class="form-control" min=0>
            			</div>
            		</div>
            	</div>
            	<div class="team_fields_div" style="display: none;">
            	</div>
            	<div class="tax_defaults_div" style="display: none;">
            		<h3>Tax Defaults</h3>

            		<div class="form-group">
            			<label for="office_zip" class="col-sm-4 control-label">Tax Name:<br><em>(e.g. - GST, HST, etc.)</em></label>
            			<div class="col-sm-8">
            				<input name="gst_name" value="<?php echo $gst_name; ?>" type="text" class="form-control" />
            			</div>
            		</div>
            		<div class="form-group">
            			<label for="office_zip" class="col-sm-4 control-label">Tax Amount %:<br><em>(e.g. - 5)</em></label>
            			<div class="col-sm-8">
            				<input name="gst_amt" value="<?php echo $gst_amt; ?>" type="text" class="form-control" />
            			</div>
            		</div>
            		<div class="form-group">
            			<label for="office_zip" class="col-sm-4 control-label">Additional Tax Name:<br><em>(e.g. - HST, PST, Sales Tax, etc.)</em></label>
            			<div class="col-sm-8">
            				<input name="pst_name" value="<?php echo $pst_name; ?>" type="text" class="form-control" />
            			</div>
            		</div>
            		<div class="form-group">
            			<label for="office_zip" class="col-sm-4 control-label">Additional Tax Amount %:<br><em>(e.g. - 5)</em></label>
            			<div class="col-sm-8">
            				<input name="pst_amt" value="<?php echo $pst_amt; ?>" type="text" class="form-control" />
            			</div>
            		</div>
            		<div class="form-group">
            			<label for="office_zip" class="col-sm-4 control-label">Additional Tax Name:<br><em>(e.g. - HST, PST, Sales Tax, etc.)</em></label>
            			<div class="col-sm-8">
            				<input name="hst_name" value="<?php echo $hst_name; ?>" type="text" class="form-control" />
            			</div>
            		</div>
            		<div class="form-group">
            			<label for="office_zip" class="col-sm-4 control-label">Additional Tax Amount %:<br><em>(e.g. - 5)</em></label>
            			<div class="col-sm-8">
            				<input name="hst_amt" value="<?php echo $hst_amt; ?>" type="text" class="form-control" />
            			</div>
            		</div>
            		<label class="col-sm-4 control-label">Default Assigned Staff:</label>
            		<div class="col-sm-8">
            			<?php $default_staff = get_config($dbc, 'expense_default_staff'); ?>
            			<select class="chosen-select-deselect" name="default_staff" data-placeholder="Select Staff"><option />
            				<option <?= $default_staff == 'NA' ? 'selected' : '' ?> value="NA">Current User</option>
            				<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `status`>=0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."")) as $staff_row) { ?>
            					<option <?= $default_staff == $staff_row['contactid'] ? 'selected' : '' ?> value="<?= $staff_row['contactid'] ?>"><?= $staff_row['full_name'] ?></option>
            				<?php } ?>
            			</select>
            		</div>
            		<label class="col-sm-4 control-label">Default Country:</label>
            		<div class="col-sm-8">
            			<input type="text" name="default_country" value="<?= get_config($dbc, 'default_country') ?>" class="form-control">
            		</div>
            		<label class="col-sm-4 control-label">Default Province:</label>
            		<div class="col-sm-8">
            			<input type="text" name="default_province" value="<?= get_config($dbc, 'default_province') ?>" class="form-control">
            		</div>
            		<label class="col-sm-4 control-label">Provinces:</label>
            		<div class="col-sm-8">
            			<script>
            			function add_province() {
            				var clone = $('.province_line').last().clone();
            				clone.find('input').val('');
            				$('.province_line').last().after(clone);
            			}
            			</script>
            			<div class="col-sm-4 text-center">Province</div><div class="col-sm-2 text-center"><?= $gst_name ?> Rate</div><div class="col-sm-2 text-center"><?= $pst_name ?> Rate</div><div class="col-sm-2 text-center"><?= $hst_name ?> Rate</div>
            			<?php $province_list = explode('#*#',get_config($dbc, 'expense_provinces'));
            			foreach($province_list as $province_data) {
            				$data = explode('*',$province_data); ?>
            				<div class="province_line">
            					<div class="col-sm-4"><input type="text" name="province_code[]" value="<?= $data[0] ?>" class="form-control"></div>
            					<div class="col-sm-2"><input type="text" name="province_gst[]" value="<?= $data[1] ?>" class="form-control"></div>
            					<div class="col-sm-2"><input type="text" name="province_pst[]" value="<?= $data[2] ?>" class="form-control"></div>
            					<div class="col-sm-2"><input type="text" name="province_hst[]" value="<?= $data[3] ?>" class="form-control"></div>
            					<div class="col-sm-2"><button class="btn brand-btn" onclick="$(this).closest('.province_line').remove();">Delete</button></div>
            				</div>
            			<?php } ?>
            			<button class="btn brand-btn pull-right" onclick="add_province(); return false;">Add Province</button>
            		</div>
            	</div>

            </div>

            <div class="form-group double-gap-top gap-right">
                <a href="?filter_id=all" class="btn brand-btn pull-left gap-left gap-top">Back</a>
                <button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right gap-top">Submit</button>
            </div>

            </form>
        </div>
    </div>
</div>
</div><!-- .expense-settings-container -->
</div>
</div>
<script type="text/javascript">
    var selectedSettingTab = '<?php echo $_POST['settingTabId'];?>';
    if(selectedSettingTab!=''){
        $("#"+selectedSettingTab)[0].click();
    }
</script>