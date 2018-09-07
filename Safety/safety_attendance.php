<?php
    $attendance_staff = '';
    $attendance_extra = 0;
    if(!empty($_GET['formid'])) {
        $formid = $_GET['formid'];

        $get_att = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT attendance_staff, attendance_extra FROM safety_field_level_risk_assessment WHERE fieldlevelriskid='$formid'"));
        $attendance_staff = $get_att['attendance_staff'];
        $attendance_extra = $get_att['attendance_extra'];
    }
?>
    <?php if($user_form_layout == 'Sidebar') { ?>
        <div id="user_form_div_safety_1" class="tab-section">
        <h4>Attendance</h4>
    <?php } else { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_att" >
                        Attendance<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_att" class="panel-collapse collapse">
                <div class="panel-body">
    <?php } ?>

                <?php if(empty($_GET['formid'])) { ?>
                  <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Staff:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Select a Staff Member..." multiple name="attendance_staff[]" class="chosen-select-deselect form-control" width="380">
                            <?php $sorted_staff = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
                            foreach($sorted_staff as $row) {
								$row_staff = get_contact($dbc, $row); ?>
                                <option <?php if (strpos($attendance_staff, $row_staff) !== FALSE) { echo " selected"; } ?> value='<?= $row_staff ?>'><?= $row_staff ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Extras:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Select an Extra..." name="attendance_extra" class="chosen-select-deselect form-control" width="380">
                            <option value=""></option>
                            <?php
                            for($i=0;$i<=50;$i++) {
                                if ($attendance_extra == $i) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='". $i."'>".$i.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                  </div>
                  <?php } else { ?>
                  <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Staff:</label>
                    <div class="col-sm-8">
                        <?php echo $attendance_staff;?>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Extras:</label>
                    <div class="col-sm-8">
                        <?php echo $attendance_extra;?>
                    </div>
                  </div>
                  <?php } ?>

    <?php if($user_form_layout == 'Sidebar') { ?>
        </div>
        <hr>
    <?php } else { ?>
                </div>
            </div>
        </div>
    <?php } ?>
