          <?php if($action == 'view') { ?>
                <?php $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM safety_upload WHERE type='document' AND safetyid='$safetyid'"));

				if((!empty($_GET['safetyid'])) && ($get_doc['total_id'] > 0)) {
					$result = mysqli_query($dbc, "SELECT upload, uploadid FROM safety_upload WHERE type='document' AND safetyid='$safetyid'");

					$i=0;
					while($row = mysqli_fetch_array($result)) {
						$document = $row['upload'];
						if($document != '') {
							echo '<li>Document: <a href="download/'.$document.'" target="_blank">'.$document.'</a></li>';
						}
					}
				} ?>
          <?php } else { ?>
            <div class="form-group">
                <label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
                        <span class="popover-examples list-inline">&nbsp;
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                        </span>
                </label>
                <div class="col-sm-8">

                <?php
                    if(!empty($_GET['safetyid'])) {
                    $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM safety_upload WHERE type='document' AND safetyid='$safetyid'"));

                    if($get_doc['total_id'] > 0) {
                        $result = mysqli_query($dbc, "SELECT upload, uploadid FROM safety_upload WHERE type='document' AND safetyid='$safetyid'");

                        echo '<ul>';
                        $i=0;
                        while($row = mysqli_fetch_array($result)) {
                            $document = $row['upload'];
                            if($document != '') {
                                echo '<li><a href="download/'.$document.'" target="_blank">'.$document.'</a> - <a href="'.(strpos($_SERVER['script_name'],'index.php') !== FALSE ? 'index.php' : 'add_manual.php').'?action=delete&uploadid='.$row['uploadid'].'&safetyid='.$safetyid.'&type='.$type.'" onclick="return confirm(\'Are you sure?\')">Delete</a></li>';
                            }
                        }
                        echo '</ul>';
                    }
                }
                ?>
                    <div class="enter_cost additional_doc clearfix">
                        <div class="clearfix"></div>

                        <div class="form-group clearfix">
                            <div class="col-sm-5">
                                <input name="document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                            </div>
                        </div>

                    </div>

                    <div id="add_here_new_doc"></div>

                    <div class="form-group triple-gapped clearfix">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button id="add_row_doc" class="btn brand-btn pull-left">Add Another Document</button>
                        </div>
                    </div>
                </div>
            </div>
          <?php } ?>