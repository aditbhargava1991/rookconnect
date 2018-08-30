<?php
include_once('../include.php');

if(empty($_GET['tasklistid'])) {

    $taskboardid = $_GET['taskboardid'];
    $label = $_GET['label'];
    echo '<div class="clearfix gap-top gap-left"><h3 class="inline">History For Tasklists for ' . $label . '</h3>';
    echo '<div class="pull-right gap-right gap-top"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img"></a></div>';
    if($label == 'Tasks') {
      $label = 'To Do';
    }
    $taskboard_query = mysqli_query($dbc, "SELECT `tasklistid` FROM `tasklist` WHERE `task_board`='$taskboardid' and task_milestone_timeline = '$label'");
    while($tasklists = mysqli_fetch_assoc($taskboard_query)) {
      $tasklistid = $tasklists['tasklistid'];
      $documents = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `document` FROM `task_document` WHERE `tasklistid`='$tasklistid' ORDER BY `taskdocid` DESC");
      if ( $documents->num_rows > 0 ) { ?>
          <div class="form-group clearfix full-width"><b>Tasklist - <?php echo $tasklistid; ?></b>
              <div class="updates_<?= $row['tasklistid'] ?> col-sm-12 gap-top"><?php
                  $odd_even = 0;
                  while ( $row_doc=mysqli_fetch_assoc($documents) ) {
                      $odd_even_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
                      <div class="note_block row <?= $odd_even_class ?>">
                          <div class="col-xs-1"><?= profile_id($dbc, $row_doc['created_by']); ?></div>
                          <div class="col-xs-11" style="<?= $style_strikethrough ?>">
                              <div><a href="../Tasks_Updated/download/<?= $row_doc['document'] ?>"><?= $row_doc['document'] ?></a></div>
                              <div><em>Added by <?= get_contact($dbc, $row_doc['created_by']); ?> on <?= $row_doc['created_date']; ?></em></div>
                          </div>
                          <div class="clearfix"></div>
                      </div>
                      <hr class="margin-vertical" /><?php
                      $odd_even++;
                  } ?>
              </div>
              <div class="clearfix"></div>
          </div><?php
      }
      $comments = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `comment` FROM `task_comments` WHERE `tasklistid`='$tasklistid' AND `deleted`=0 ORDER BY `taskcommid` DESC");
      if ( $comments->num_rows > 0 ) { ?>
          <div class="form-group clearfix full-width"><b>Tasklist - <?php echo $tasklistid; ?></b>
              <div class="updates_<?= $row['tasklistid'] ?> col-sm-12 gap-top"><?php
                  $odd_even = 0;
                  while ( $row_comment=mysqli_fetch_assoc($comments) ) {
                      $odd_even_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
                      <div class="note_block row <?= $odd_even_class ?>">
                          <div class="col-xs-1"><?= profile_id($dbc, $row_comment['created_by']); ?></div>
                          <div class="col-xs-11" style="<?= $style_strikethrough ?>">
                              <div><?= html_entity_decode($row_comment['comment']); ?></div>
                              <div><em>Added by <?= get_contact($dbc, $row_comment['created_by']); ?> on <?= $row_comment['created_date']; ?></em></div>
                          </div>
                          <div class="clearfix"></div>
                      </div><?php
                    $odd_even++;
                  } ?>
              </div>
              <hr class="margin-vertical" />
              <div class="clearfix"></div>
          </div><?php
      }
      else {
        $tasks = mysqli_query($dbc, "SELECT `heading`,`updated_date`,`contactid`,`tasklistid` FROM `tasklist` WHERE `tasklistid`='$tasklistid' ORDER BY `tasklistid` DESC");
        if ( $tasks->num_rows > 0 ) { ?>
            <div class="form-group clearfix full-width"><b>Tasklist - <?php echo $tasklistid; ?></b>
                <div class="updates_<?= $row['tasklistid'] ?> col-sm-12 gap-top"><?php
                    $odd_even = 0;
                    while ( $row_doc=mysqli_fetch_assoc($tasks) ) {
                        $odd_even_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
                        <?php //if($row_doc['created_by'] != ''): ?>
                          <div class="note_block row <?= $odd_even_class ?>">
                              <div class="col-xs-1"><?= profile_id($dbc, $row_doc['contactid']); ?></div>
                              <div class="col-xs-11" style="<?= $style_strikethrough ?>">
                                  <div><a href="../Tasks_Updated/download/<?= $row_doc['heading'] ?>"><?= $row_doc['heading'] ?></a></div>
                                  <div><em>Added by <?= get_contact($dbc, $row_doc['contactid']); ?> on <?= $row_doc['updated_date']; ?></em></div>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                        <?php //endif; ?><?php
                        $odd_even++;
                    } ?>
                </div>
                <hr class="margin-vertical" />
                <div class="clearfix"></div>
            </div><?php
        }
      }
    }
    echo '</div>';

}

if(!empty($_GET['tasklistid'])) {

    $tasklistid = $_GET['tasklistid'];
    $label = $_GET['label'];
    echo '<div class="clearfix gap-top gap-left"><h3 class="inline">History For Tasklists for ' . $label . '</h3>';
    echo '<div class="pull-right gap-right gap-top"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img"></a></div>';
    if($label == 'Tasks') {
      $label = 'To Do';
    }
      $documents = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `document` FROM `task_document` WHERE `tasklistid`='$tasklistid' ORDER BY `taskdocid` DESC");
      if ( $documents->num_rows > 0 ) { ?>
          <div class="form-group clearfix full-width"><b>Tasklist - <?php echo $tasklistid; ?></b>
              <div class="updates_<?= $row['tasklistid'] ?> col-sm-12 gap-top"><?php
                  $odd_even = 0;
                  while ( $row_doc=mysqli_fetch_assoc($documents) ) {
                      $odd_even_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
                      <div class="note_block row <?= $odd_even_class ?>">
                          <div class="col-xs-1"><?= profile_id($dbc, $row_doc['created_by']); ?></div>
                          <div class="col-xs-11" style="<?= $style_strikethrough ?>">
                              <div><a href="../Tasks_Updated/download/<?= $row_doc['document'] ?>"><?= $row_doc['document'] ?></a></div>
                              <div><em>Added by <?= get_contact($dbc, $row_doc['created_by']); ?> on <?= $row_doc['created_date']; ?></em></div>
                          </div>
                          <div class="clearfix"></div>
                      </div><?php
                    $odd_even++;
                  } ?>
              </div>
              <hr class="margin-vertical" />
              <div class="clearfix"></div>
          </div><?php
      }
      $comments = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `comment` FROM `task_comments` WHERE `tasklistid`='$tasklistid' AND `deleted`=0 ORDER BY `taskcommid` DESC");
      if ( $comments->num_rows > 0 ) { ?>
          <div class="form-group clearfix full-width"><b>Tasklist - <?php echo $tasklistid; ?></b>
              <div class="updates_<?= $row['tasklistid'] ?> col-sm-12 gap-top"><?php
                  $odd_even = 0;
                  while ( $row_comment=mysqli_fetch_assoc($comments) ) {
                      $odd_even_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
                      <div class="note_block row <?= $odd_even_class ?>">
                          <div class="col-xs-1"><?= profile_id($dbc, $row_comment['created_by']); ?></div>
                          <div class="col-xs-11" style="<?= $style_strikethrough ?>">
                              <div><?= html_entity_decode($row_comment['comment']); ?></div>
                              <div><em>Added by <?= get_contact($dbc, $row_comment['created_by']); ?> on <?= $row_comment['created_date']; ?></em></div>
                          </div>
                          <div class="clearfix"></div>
                      </div><?php
                      $odd_even++;
                  } ?>
              </div>
              <hr class="margin-vertical" />
              <div class="clearfix"></div>
          </div><?php
      }
      else {
        $tasks = mysqli_query($dbc, "SELECT `heading`,`updated_date`,`contactid`,`tasklistid` FROM `tasklist` WHERE `tasklistid`='$tasklistid' ORDER BY `tasklistid` DESC");
        if ( $tasks->num_rows > 0 ) { ?>
            <div class="form-group clearfix full-width"><b>Tasklist - <?php echo $tasklistid; ?></b>
                <div class="updates_<?= $row['tasklistid'] ?> col-sm-12 gap-top"><?php
                    $odd_even = 0;
                    while ( $row_doc=mysqli_fetch_assoc($tasks) ) {
                        $odd_even_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
                        <?php //if($row_doc['created_by'] != ''): ?>
                          <div class="note_block row <?= $odd_even_class ?>">
                              <div class="col-xs-1"><?= profile_id($dbc, $row_doc['contactid']); ?></div>
                              <div class="col-xs-11" style="<?= $style_strikethrough ?>">
                                  <div><a href="../Tasks_Updated/download/<?= $row_doc['heading'] ?>"><?= $row_doc['heading'] ?></a></div>
                                  <div><em>Added by <?= get_contact($dbc, $row_doc['contactid']); ?> on <?= $row_doc['updated_date']; ?></em></div>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                        <?php //endif; ?><?php
                        $odd_even++;
                    } ?>
                </div>
                <hr class="margin-vertical" />
                <div class="clearfix"></div>
            </div><?php
        }
      }
    echo '</div>';

}
?>
