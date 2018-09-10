<?php
include ('../include.php');
error_reporting(0);

//echo $_SERVER[REQUEST_URI];

if (isset($_POST['submit'])) {
	$created_by = $_SESSION['contactid'];
	$item_type = 'inventory';
	$category = $_POST['category'];
	$type = $_POST['type'];
	$unit_id = $_POST['inventoryid'];
	$checklist_name = filter_var($_POST['checklist_name']);
	$error_list = '';
	
	$lines = $_POST['checklist'];
	$existing = $_POST['checklist_update'];
	$documents = $_FILES['upload_document'];
	$links = $_POST['attach_link'];

	if(empty($_POST['checklistid'])) {
		$query_insert_ca = "INSERT INTO `item_checklist` (`checklist_item`, `item_category`, `item_type`, `item_id`, `checklist_name`, `created_by`) VALUES ('$item_type', '$category', '$type', '$unit_id', '$checklist_name', '$created_by')";
		$result_insert_ca = mysqli_query($dbc, $query_insert_ca);
		$checklistid = mysqli_insert_id($dbc);

	} else {
		$checklistid = $_POST['checklistid'];
		$query_update_vendor = "UPDATE `item_checklist` SET `item_category`='$category', `item_type`='$type', `item_id`='$unit_id', `checklist_name`='$checklist_name' WHERE `checklistid` = '$checklistid'";
		$result_update_vendor = mysqli_query($dbc, $query_update_vendor);
		foreach($existing as $i => $checklist) {
			$line_id = $_POST['checklistid_update'][$i];
			if(!mysqli_query($dbc, "UPDATE `item_checklist_line` SET `checklist` = CONCAT('$checklist',SUBSTRING(`checklist`,POSITION('".htmlentities("<p>")."' IN `checklist`))) WHERE `checklistlineid` = '$line_id'")) {
				$error_list .= mysqli_errno($dbc).': '.mysqli_error($dbc)."<br />\n";
			}
		}
	}

	$max_priority = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`priority`) FROM `item_checklist_line` WHERE `checklistid`='$checklistid'"))[0];
	for($i = 0; $i < count($lines); $i++) {
		$checklist = filter_var($lines[$i],FILTER_SANITIZE_STRING);
		$priority = ++$max_priority;

		if($checklist != '') {
			$query_insert_client_doc = "INSERT INTO `item_checklist_line` (`checklistid`, `checklist`, `priority`) VALUES ('$checklistid', '$checklist', '$priority')";
			if(!mysqli_query($dbc, $query_insert_client_doc)) {
				$error_list .= mysqli_errno($dbc).': '.mysqli_error($dbc)."<br />\n";
			}
		}
	}

	foreach($documents['name'] as $row => $filename) {
		if($filename != '') {
			if (!file_exists('download')) {
				mkdir('download', 0777, true);
			}
			$basefilename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$filename);
			$i = 0;
			while(file_exists('download/'.$filename)) {
				$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '('.++$i.')$1', $basefilename);
			}
			move_uploaded_file($documents['tmp_name'][$row], 'download/'.$filename);
			if(!mysqli_query($dbc, "INSERT INTO `item_checklist_document` (`checklistid`, `type`, `document`, `created_by`) VALUES ('$checklistid', 'Support Document', '$filename', '$created_by')")) {
				$error_list .= mysqli_errno($dbc).': '.mysqli_error($dbc)."<br />\n";
			}
		}
	}
	foreach($links as $link) {
		if($link != '') {
			if(!mysqli_query($dbc, "INSERT INTO `item_checklist_document` (`checklistid`, `type`, `link`, `created_by`) VALUES ('$checklistid', 'Web Link', '$link', '$created_by')")) {
				$error_list .= mysqli_errno($dbc).': '.mysqli_error($dbc)."<br />\n";
			}
		}
	}

	if($error_list != '') {
		echo $error_list;
	} else {
		echo '<script type="text/javascript"> window.location.replace("inventory_checklist.php"); </script>';
	}
}
?>

<script type="text/javascript">
$(document).ready(function () {
	$('#add_row_doc').on( 'click', function () {
		var clone = $('.additional_doc').clone();
		clone.find('input').val('').removeAttr('checked');
		clone.find('.popover-examples').css('display', 'none');
		clone.find('#add_row_doc').css('display', 'none');
		clone.removeClass("additional_doc");
		$('#add_here_new_doc').append(clone);
		$('#add_here_new_doc').find('input').last().focus();
		return false;
	});
});

function add_link(button) {
	var div = $(button).closest('.form-group');
	var link = div.clone();
	link.find('.form-control').val('');
	div.after(link).next('.form-group').find('.form-control').focus();
}

function changeSecurity(sel) {
	var stage = sel.value;
	if(stage == 'Company Checklist') {
		$('.assign_staff').show();
	} else {
		$('.assign_staff').hide();
	}
}
</script>

</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('inventory');
?>
<div class="container">
	<div class="row">

	<h1>Add Checklist</h1>
	<div class="gap-top double-gap-bottom"><a href="inventory_checklist.php" class="btn config-btn">Back to Dashboard</a></div>

	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php $userid = $_SESSION['contactid'];
	$category = '';
	$type = '';
	$unit_id = '';
	$name = '';
	if(!empty($_GET['checklistid'])) {
		$checklistid = $_GET['checklistid'];
		$checklist = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `item_checklist` WHERE `checklistid`='$checklistid'"));
		$category = $checklist['item_category'];
		$type = $checklist['item_type'];
		$unit_id = $checklist['item_id'];
		$name = $checklist['checklist_name'];

		$checklist = mysqli_query($dbc,"SELECT * FROM `item_checklist_line` WHERE `checklistid`='$checklistid' AND `deleted`=0 ORDER BY `priority`");

		echo '<input type="hidden" name="checklistid" value="'.$_GET['checklistid'].'" />';
	}
	$value_config = ','.mysqli_fetch_array(mysqli_query($dbc,"SELECT GROUP_CONCAT(`inventory`) FROM `field_config_inventory`"))[0].',';	?>

	<?php if(strpos($value_config,',Category,') !== FALSE): ?>
		<div class="form-group clearfix">
			<label for="first_name" class="col-sm-4 control-label text-right">
				<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select an inventory tab to attach the Checklist to it."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Inventory Tab:
			</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a Tab of Inventory" name="category" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php $tabs = get_config($dbc, 'inventory_tabs');
					$each_tab = explode('#*#', $tabs);
					foreach ($each_tab as $cat_tab) {
						echo "<option ".($category == $cat_tab ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
					} ?>
				</select>
			</div>
		</div>
	<?php endif; ?>

	<?php if(strpos($value_config,',Type,') !== FALSE): ?>
		<div class="form-group clearfix">
			<label for="first_name" class="col-sm-4 control-label text-right">
				<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select an inventory type to attach the Checklist to it."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Inventory Tab:
			</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a Tab of Inventory" name="type" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<option <?php if ($type=='Project Inventory') echo 'selected="selected"';?> value="Project Inventory">Project Inventory</option>
					<option <?php if ($type=='Consumables') echo 'selected="selected"';?> value="Consumables">Consumables</option>
					<option <?php if ($type=='Inventory') echo 'selected="selected"';?> value="Inventory" >Inventory</option>
				</select>
			</div>
		</div>
	<?php endif; ?>

	<div class="form-group clearfix">
		<label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select a specific piece of inventory to attach the Checklist to it."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Specific Inventory:
		</label>
		<div class="col-sm-8">
			<select data-placeholder="Select Inventory" name="inventoryid" class="chosen-select-deselect form-control" width="380">
				<option value=""></option>
				<?php $query = mysqli_query($dbc,"SELECT `inventoryid`, `part_no`, `name` FROM `inventory` ORDER BY `category`, `part_no`");
				$category = '';
				while($inventory = mysqli_fetch_array($query)) {
					if($inventory['category'] != $category) {
						$category = $inventory['category'];
						echo ($category == '' ? '' : '</optgroup>').'<optgroup label="'.$category.'">';
					} ?>
					<option <?= ($inventory['inventoryid'] == $unit_id ? " selected" : '') ?> value='<?=  $inventory['inventoryid'] ?>' ><?= $inventory['part_no'].': '.$inventory['name'] ?></option><?php
				}
				echo ($category == '' ? '' : '</optgroup>'); ?>
			</select>
		</div>
	</div>

	<div class="form-group clearfix">
		<label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="This will be the title of the Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Name:
		</label>
		<div class="col-sm-8">
			<input type="text" name="checklist_name" value="<?php echo $name; ?>" class="form-control" width="380" />
		</div>
	</div>

	<?php if(!empty($_GET['checklistid'])) {
		$query_check_credentials = "SELECT * FROM item_checklist_document WHERE checklistid='$checklistid' ORDER BY checklistdocid DESC";
		$result = mysqli_query($dbc, $query_check_credentials);
		$num_rows = mysqli_num_rows($result);
		if($num_rows > 0) {
			echo "<table class='table table-bordered table-striped' style='width:100%;'>
            <thead>
			<tr class='hidden-xs hidden-sm'>
			<th>Link / Document</th>
			<th>Date</th>
			<th>Attached By</th>
			</tr>
            </thead>";
			while($row = mysqli_fetch_array($result)) {
				echo '<tr>';
				if(empty($row['document'])) {
					echo '<td data-title="Link"><a href="'.$row['link'].'" target="_blank">'.$row['link'].'</a></td>';
				} else {
					echo '<td data-title="Document"><a href="download/'.(empty($row['document']) ? $row['link'] : $row['document']).'" target="_blank">'.$row['document'].'</a></td>';
				}
				echo '<td data-title="Date">'.$row['created_date'].'</td>';
				echo '<td data-title="Attached By">'.get_contact($dbc, $row['created_by']).'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	} ?>

	<div class="form-group">
		<label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
				<span class="popover-examples list-inline">&nbsp;
				<a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
				</span>
		</label>
		<div class="col-sm-8">
			<div class="form-group clearfix">
				<div class="col-sm-5">
					<input name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="additional_note" class="col-sm-4 control-label">Attach Link(s):</label>
		<div class="col-sm-7">
			<input name="attach_link[]" type="text" class="form-control" />
		</div>
		<div class="col-sm-1">
			<button onclick="add_link(this); $(this).hide(); return false;" class="btn brand-btn col-sm-12">Add Link</button>
		</div>
	</div>

	<div class="form-group">
		<label for="additional_note" class="col-sm-4 control-label">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add the content of the Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Checklist:
		</label>
		<div class="col-sm-8">
			<div class="form-group clearfix">
				<div class="col-sm-8">
					<?php if(!empty($_GET['checklistid']) && mysqli_num_rows($checklist) > 0) {
						while($row = mysqli_fetch_array($checklist)) {
							echo '<input disabled type="checkbox" '.($row['checked'] == 1 ? 'checked' : '').' value="" style="" >#'.$row['checklistlineid'].': <input type="text" name="checklist_update[]" class="form-control" value= "';
							echo explode('<p>',html_entity_decode($row['checklist']))[0];
							echo '" style="width: 80%; display:inline;" width="380" /><br>';
							echo '<input type="hidden" name="checklistid_update[]" value="'.$row['checklistlineid'].'" />';

						}
					} ?>
				</div>
			</div>
			<div class="enter_cost additional_doc clearfix">
				<div class="clearfix"></div>

				<div class="form-group clearfix">
					<div class="col-sm-8">
						<input type="text" name="checklist[]" class="form-control" style="width: 95%; display:inline;" width="380" />
					</div>
					<div class="col-sm-4">
						<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Checklist line."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						<button id="add_row_doc" class="btn brand-btn">Add</button>
					</div>
				</div>

			</div>
			<div id="add_here_new_doc"></div>
		</div>
	</div>

	<div class="form-group clearfix">
		<div class="col-sm-6">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="If you click this, the current Checklist will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="inventory_checklist.php" class="btn brand-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-6">
			<button name="submit" value="save_checklist" class="btn brand-btn btn-lg pull-right">Save Checklist</button>
			<span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save the Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		</div>
	</div>

	</form>

</div>
</div>
<?php include ('../footer.php'); ?>
