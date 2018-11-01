<?php
include_once('../include.php');
ob_clean();

if(isset($_GET['category'])) {
    $category = $_GET['category'];
    if ($_GET['action'] == 'make') {
        echo '<option></option>';
        $list = mysqli_query($dbc, "SELECT `category`, `make` FROM `equipment` WHERE `deleted`=0 AND `category` = '$category' GROUP BY `make`");
        while($row = mysqli_fetch_array($list)) {
            if (!empty($row['make'])) {
                echo "<option value='".$row['make']."' data-category='".$row['category']."'>".$row['make']."</option>";
            }
        }
    } else if ($_GET['action'] == 'model') {
        echo '<option></option>';
        $list = mysqli_query($dbc, "SELECT `category`, `make`, `model` FROM `equipment` WHERE `deleted`=0 AND `category` = '$category' GROUP BY `model`");
        while($row = mysqli_fetch_array($list)) {
            if (!empty($row['model'])) {
                echo "<option value='".$row['model']."' data-category='".$row['category']."' data-make='".$row['make']."'>".$row['model']."</option>";
            }
        }
    } else if ($_GET['action'] == 'unit') {
        echo '<option></option>';
        $list = mysqli_query($dbc, "SELECT `category`, `make`, `model`, `unit_number`, `equipmentid` FROM `equipment` WHERE `deleted`=0 AND `category` = '$category'");
        while($row = mysqli_fetch_array($list)) {
            if (!empty($row['unit_number'])) {
                echo "<option value='".$row['equipmentid']."' data-category='".$row['category']."' data-make='".$row['make']."' data-model='".$row['model']."'>".$row['unit_number']."</option>";
            }
        }
    } else if($_GET['action'] == 'checklist') {
        $tab_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT `tab` FROM `field_config_equipment` WHERE CONCAT(',',`tab`,',') LIKE '%,$category,%' AND `inspection_list` IS NOT NULL"))['tab'];
		$inspection_list = mysqli_fetch_array(mysqli_query($dbc, "SELECT `inspection_list` FROM `field_config_equipment` WHERE CONCAT(',',`tab`,',') LIKE '%,$category,%' AND `inspection_list` IS NOT NULL"))['inspection_list'];
		if($inspection_list != '') {
			foreach(explode('#*#', $inspection_list) as $i => $item) {
                $inspection_checklist = '';
                $inspection_details = 0;
                $query = mysqli_query($dbc, "SELECT * FROM `field_config_equipment_inspection` WHERE `tab` = '".$category."' AND `inspection_name` = '".$item."'");
                if (mysqli_num_rows($query) > 0) {
                    $result = mysqli_fetch_array($query);
                    $inspection_checklist = $result['inspection_checklist'];
                    $inspection_details = $result['inspection_details'];
                }
				$details = false;
				$item_label = $item;
				if(strpos($item, 'Details') !== FALSE) {
					$item_label = str_replace('Details','',$item);
					$details = true;
				} ?>
                <div class="form-group">
                    <input type="hidden" checked name="checklist_item[<?= $i ?>]" value="<?= $item_label ?>">
                    <label class="col-sm-4 control-label"><?= $item_label ?></label>
                    <div class="col-sm-8">
                    <?php if (!empty($inspection_checklist)) {
                        $inspection_checklist = explode(',', $inspection_checklist);
                        foreach ($inspection_checklist as $row) { ?>
                            <input type="radio" name="checklist[<?= $i ?>]" value="<?= $row ?>"> <?= $row ?>
                    <?php }} else { ?>
                        <input type="radio" name="checklist[<?= $i ?>]" value="Good"> Good
                        <input type="radio" name="checklist[<?= $i ?>]" value="Needs Attention"> Needs Attention
                    <?php } ?>
                    </div>
                    <?php if ($inspection_details == 1) { ?>
                        <label class="col-sm-4 control-label"><?= $item_label?> Details</label>
                        <div class="col-sm-8">
                            <input type="text" name="checklist_details[<?= $i ?>]" class="form-control">
                        </div>
                    <?php } ?>
                </div>

				<!-- <div class="col-sm-6 col-md-4 col-lg-3" style="min-height: 8em;">
					<label class="col-sm-12 text-center"><b><?= $item_label ?></b></label>
					<label class="col-sm-5"><input type="radio" name="checklist[<?= $i ?>]" value="good"> Good</label>
					<label class="col-sm-7"><input type="radio" name="checklist[<?= $i ?>]" value="attention"> Needs Attention</label>
					<input type="hidden" checked name="checklist_item[]" value="<?= $item_label ?>"><br /><?php
					if($details) {
						echo '<label class="col-sm-4">Details:</label><div class="col-sm-8"><input type="text" checked name="checklist_detail['.$i.']" value="" class="form-control"></div>';
					} ?>
				</div> -->
			<?php }
		} else {
			echo "<h4>No checklist found for the current tab.</h4>";
		}
	}
} ?>