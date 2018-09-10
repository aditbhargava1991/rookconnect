<?php include_once('../include.php');
$active_add = 'active';
$active_edit = '';
$active_export = '';
$active_log = '';
$type_get = '';
$title = '';

if(isset($_GET['type'])) {
	$type_get = $_GET['type'];
	if($type_get == 'add' || $type_get == '') {
		$active_add = 'active';
		$title = 'Add Multiple Products';
	} else if($type_get == 'edit') {
		$active_edit = 'active';
		$active_add = '';
		$title = 'Edit Multiple Products';
	} else if($type_get == 'export') {
		$active_export = 'active';
		$active_add = '';
		$title = 'Export Inventory';
	} else if($type_get == 'exportpdf') {
		$active_exportpdf = 'active';
		$active_add = '';
		$title = 'Export PDF';
	} else if($type_get == 'log') {
		$active_log = 'active';
		$active_add = '';
		$title = 'History';
	}
} else {
	$title = 'Add Multiple Products';
} ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#category").change(function() {
            if($( "#category option:selected" ).text() == 'Other') {
                    $( "#category_name" ).show();
            } else {
                $( "#category_name" ).hide();
            }
        });
	});
	function generate_import_csv() {
		$.ajax({
			url: 'inventory_ajax.php?action=generate_import_csv',
			method: 'GET',
			result: 'html',
			success: function(response) {
				window.open(response, "_blank");
			}
		});
	}
</script>
<?php if($type_get == '' || $type_get == 'add') { ?>
	<div class="row add">
		<form action="add_inventory_multiple.php" name="import" method="post" enctype="multipart/form-data">
			<div class="notice">Steps to Upload Multiple Items into the Inventory tile:<br><Br>
				<b>1.</b> Please download the following Excel(CSV) file to use as a template: <a href='' onclick="generate_import_csv(); return false;" style='color:white !important; text-decoration:underline !important;'>Add_multiple_inventory.csv</a>.<br><br>
				<b>2.</b> Fill in the rows (starting from row 2). Please note that each row you fill out will become a separate inventory item in the Inventory tile.<br>
				<span style='color:pink;'><img src='../img/warning.png' style='width:25px;'> NOTE</span>: Do not change/move/delete any of the column titles in the first row.<br> <span style='color:lightgreen'><b>Hint</b>:</span> press CTRL+F on your keyboard to find the fields you would like to populate; this will help you locate them faster. <br><br>
				<b>3.</b> After you are done filling out your data, save the Excel (CSV) file, upload the CSV file below, and hit submit.<br><br>
				<b>4.</b> Please look for your newly added inventory in the Inventory dashboard!<br><br>
				<input class="form-control" type="file" name="file" />
			</div>
			<div class="row gap-left gap-right pull-right">
				<div class="col-sm-6">
					<a href="inventory.php?category=Top" class="btn brand-btn">Back</a>
					<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<input class="btn brand-btn" type="submit" name="submitty" value="Submit" />
				</div>
				<div class="clearfix"></div>
			</div>
		</form>
	</div>

<?php } else if ($type_get == 'edit') { ?>
	<div class="row edit">
		<form action="add_inventory_multiple.php" name="import" method="post" enctype="multipart/form-data">
			<div class="notice">Steps to Edit Multiple Items into the Inventory tile:<br><Br>
				<b>1.</b> Please export the Excel (CSV) file from this page: <a href='add_inventory_multiple.php?type=export' target="_BLANK" style='color:white; text-decoration:underline;'>Export Inventory</a>.<br><br>
				<b>2.</b> Make your desired changes inside of the Excel file.<br>
				<span style='color:pink;'><img src='../img/warning.png' style='width:25px;'> NOTE</span>: Do not change/move/delete any of the column titles in the first row. Also, do not change the data in the first column (<em>inventoryid</em>), or else the edits may not go through properly. <br><span style='color:lightgreen'><b>Hint:</b></span> press CTRL+F on your keyboard to find the fields you would like to populate; this will help you locate them faster.<br><br>
				<b>3.</b> After you are done editing the data, save your Excel (CSV) file, upload the CSV file below, and hit submit.<br><br>
				<b>4.</b> Please look for your edited Inventory in the Inventory dashboard!<br><br>
				<input class="form-control" type="file" name="file" />
			</div>
			<div class="row gap-left gap-right pull-right">
				<div class="col-sm-6">
					<a href="inventory.php?category=Top" class="btn brand-btn">Back</a>
					<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<input class="btn brand-btn" type="submit" name="submitty2" value="Submit" />
				</div>
				<div class="clearfix"></div>
			</div>
		</form>
	</div>

<?php }  else if ($type_get == 'export') { ?>
	<div class="row export">
		<form action="add_inventory_multiple.php" name="import" method="post" enctype="multipart/form-data">
			<div class="notice">
				<div class="col-sm-3 gap-top"><?php
					$sql = mysqli_query($dbc, 'SELECT * FROM inventory WHERE deleted = 0 GROUP BY category');  ?>
					<label for="travel_task" class="col-sm-2" style='width:120px;'>
						<span class="popover-examples list-inline hide-on-mobile" style='display:inline-block;'><a style="margin:5px 0 0 15px;" data-toggle="tooltip" data-placement="top" title="Select which tab you would like to export, or select All Tabs to export every inventory item that you have."><img src="../img/info.png" width="20"></a></span>
						Tab:
					</label>
				</div>
				<div class="col-sm-9">
					<select name="category_export" class="chosen-select-deselect form-control" width="380">
						<option value="3456780123456971230">All Tabs</option><?php
						while($row = mysqli_fetch_assoc($sql)){
							echo '<option value="'.$row['category'].'">'.$row['category'].'</option>';
						} ?>
					</select>
				</div>
				<div class="clearfix"></div>

		        <div class="form-group">
					<div class="col-sm-3 gap-top">
						<label for="travel_task" class="col-sm-2" style='width:120px;'>
							<span class="popover-examples list-inline hide-on-mobile" style='display:inline-block;'><a style="margin:5px 0 0 15px;" data-toggle="tooltip" data-placement="top" title="Select the Template for your Exported CSV."><img src="../img/info.png" width="20"></a></span>
							Template:
						</label>
					</div>
		            <div class="col-sm-9">
		                <select name="template" class="chosen-select-deselect">
		                    <option></option>
		                    <?php $template_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `inventory_templates` WHERE `deleted` = 0 ORDER BY `template_name`"),MYSQLI_ASSOC);
		                        foreach ($template_list as $template) {
		                            echo '<option value="'.$template['id'].'">'.$template['template_name'].'</option>';
		                        }
		                    ?>
		                </select>
		            </div>
		        </div>
		        <div class="clearfix"></div>
		    </div>

			<div class="form-group pull-right gap-right">
				<a href="contacts.php?category=Top" class="btn brand-btn">Back</a>
				<!--<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>-->
				<button class="btn brand-btn" type="submit" name="exporter" value="Export" />Export Inventory</button>
			</div>
		</form>
	</div>

<?php }  else if ($type_get == 'exportpdf') { ?>
	<div class="row export">
		<div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11">
                <span class="notice-name">NOTE:</span>
                Please note, when exporting an extensive amount of records (i.e. over 1000 records), it will cause the PDF to process slower.
            </div>
            <div class="clearfix"></div>
        </div>
        <form action="add_inventory_multiple.php" name="import" method="post" enctype="multipart/form-data">
			<div class="notice">
				<div class="col-sm-3 gap-top"><?php
					$sql = mysqli_query($dbc, 'SELECT * FROM inventory WHERE deleted = 0 GROUP BY category');  ?>
					<label for="travel_task" class="control-label" style='width:120px;'>
						<span class="popover-examples list-inline hide-on-mobile" style='display:inline-block;'><a style="margin:5px 0 0 15px;" data-toggle="tooltip" data-placement="top" title="Select which tab you would like to export, or select All Tabs to export every inventory item that you have."><img src="../img/info.png" width="20"></a></span>
						Tab:
					</label>
				</div>
				<div class="col-sm-9">
					<select name="category_export" class="chosen-select-deselect form-control" width="380">
						<option value="3456780123456971230">All Tabs</option><?php
						while($row = mysqli_fetch_assoc($sql)){
							echo '<option value="'.$row['category'].'">'.$row['category'].'</option>';
						} ?>
					</select>
				</div>
				<div class="clearfix"></div>

				<?php $template_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `inventory_templates` WHERE `deleted` = 0 ORDER BY `template_name`"),MYSQLI_ASSOC);
				if(!empty($template_list)) { ?>
			        <div class="form-group">
						<div class="col-sm-3 gap-top">
							<label for="travel_task" class="control-label" style='width:120px;'>
								<span class="popover-examples list-inline hide-on-mobile" style='display:inline-block;'><a style="margin:5px 0 0 15px;" data-toggle="tooltip" data-placement="top" title="Select the Template for your Exported PDF. Leave empty to export Dashboard fields."><img src="../img/info.png" width="20"></a></span>
								Template:
							</label>
						</div>
			            <div class="col-sm-9">
			                <select name="template" class="chosen-select-deselect">
			                    <option></option>
			                    <?php 
			                        foreach ($template_list as $template) {
			                            echo '<option value="'.$template['id'].'">'.$template['template_name'].'</option>';
			                        }
			                    ?>
			                </select>
			            </div>
			        </div>
			        <div class="clearfix"></div>
			    <?php } ?>

                <?php $pdf_styles = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `inventory_pdf_setting`"),MYSQLI_ASSOC);
                $style_options = [];
                foreach($pdf_styles as $pdf_style) {
                	if(!in_array($pdf_style['style'], $style_options)) {
                		switch($pdf_style['style']) {
                			case 'design_styleA':
                				$style_options['design_styleA'] = 'Design Style A';
                				break;
                			case 'design_styleB':
                				$style_options['design_styleB'] = 'Design Style B';
                				break;
                			case 'design_styleC':
                				$style_options['design_styleC'] = 'Design Style C';
                				break;
                		}
                	}
                }
                if(!empty($style_options)) { ?>
			        <div class="form-group">
						<div class="col-sm-3 gap-top">
							<label for="travel_task" class="control-label" style='width:120px;'>
								<span class="popover-examples list-inline hide-on-mobile" style='display:inline-block;'><a style="margin:5px 0 0 15px;" data-toggle="tooltip" data-placement="top" title="Select the PDF Styling for your Exported PDF."><img src="../img/info.png" width="20"></a></span>
								PDF Styling:
							</label>
						</div>
			            <div class="col-sm-9">
			                <select name="pdf_styling" class="chosen-select-deselect">
	                            <?php foreach($style_options as $key => $value) {
	                            	if(!empty($value)) {
		                            	echo '<option value="'.$key.'">'.$value.'</option>';
	                            	}
	                            } ?>
			                </select>
			            </div>
			        </div>
			        <div class="clearfix"></div>
		        <?php } ?>

		        <div class="form-group">
					<div class="col-sm-3 gap-top">
						<label for="travel_task" class="control-label" style='width:120px;'>
							<span class="popover-examples list-inline hide-on-mobile" style='display:inline-block;'><a style="margin:5px 0 0 15px;" data-toggle="tooltip" data-placement="top" title="Limit the number of rows to generate in the PDF. Leave blank or at 0 to export all items."><img src="../img/info.png" width="20"></a></span>
							Limit Rows:
						</label>
					</div>
		            <div class="col-sm-9">
		            	<input type="number" name="limit_rows" class="form-control" min="0" value="">
		            </div>
		        </div>
		        <div class="clearfix"></div>
		    </div>

			<div class="form-group pull-right gap-right">
				<a href="contacts.php?category=Top" class="btn brand-btn">Back</a>
				<!--<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>-->
				<button class="btn brand-btn" type="submit" name="exportpdf" value="Export" />Export PDF</button>
			</div>
		</form>
	</div>

<?php }  else if ($type_get == 'log') {
    echo '<div class="row">';
		$query_check_credentials = "SELECT * FROM import_export_log WHERE deleted = 0 AND table_name = 'Inventory' ORDER BY date_time DESC LIMIT 10000";
		$gettotalrows = "SELECT * FROM import_export_log WHERE deleted = 0 AND table_name = 'Inventory'";
		$result = mysqli_query($dbc, $query_check_credentials);
		$xxres = mysqli_query($dbc, $gettotalrows);
		$num_rows = mysqli_num_rows($result);
		$num_rowst = mysqli_num_rows($xxres);

		if($num_rows > 0) {
			echo "<br>Currently displaying the last $num_rows rows (out of a total of $num_rowst rows).<br><br>";
			echo "<table class='table table-bordered table-striped'>";
                echo "<thead>";
                    echo "<tr class='hidden-xs hidden-sm'>";
                        echo '<th>Tab</th>';
                        echo '<th>Description</th>';
                        echo '<th>Date/Time</th>';
                        echo '<th>Author</th>';
                    echo "</tr>";
                echo "</thead>";
		} else {
			echo "<h2 class ='list_dashboard'>No Record Found.</h2>";
		}

		while($row = mysqli_fetch_array( $result )) {
			echo "<tr>";
				echo '<td data-title="Tab">' . $row['type'] . '</td>';
				echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
				$time = substr($row['date_time'], strpos($row['date_time'], ' '));
				$time = date("g:i a", strtotime($time));
				$arr = explode(' ',trim($row['date_time']));
				echo '<td data-title="Date & Time">'.$arr[0].' at '.$time. '</td>';
				echo '<td data-title="Author">' . $row['contact'] . '</td>';
			echo "</tr>";
		}

		echo '</table>'; ?>

		<a href="inventory.php?category=Top" class="btn brand-btn pull-right">Back</a>
		<!--<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>-->
	</div>
<?php } ?>