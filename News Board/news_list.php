<?php
/*
 * News List
 * Included From: index.php
 * Function: List all the news items for a selected News Board, Tag and Software Type (Softwarewide or Local)
 * Query Variables Accepted: board, tag, type
 * Scripts on index.php
 */

include('../include.php');
include('../database_connection_htg.php');

$url_boardid = preg_replace('/[^0-9]/', '', $_GET['board']);
$url_tag = filter_var($_GET['tag'], FILTER_SANITIZE_STRING);
$url_type = filter_var($_GET['type'], FILTER_SANITIZE_STRING);
$dbc_news = ($url_type == 'sw') ? $dbc_htg : $dbc;

/* if ( !empty($url_boardid) ) {
    $board = mysqli_query($dbc_news, "SELECT `board_name`, `shared_staff` FROM `newsboard_boards` WHERE `boardid`='$url_boardid' AND `deleted`=0");
} else {
    $board = mysqli_query($dbc_news, "SELECT `boardid`, `board_name`, `shared_staff` FROM `newsboard_boards` WHERE `deleted`=0");
} */

$board = mysqli_query($dbc_news, "SELECT `board_name`, `shared_staff` FROM `newsboard_boards` WHERE `boardid`='$url_boardid' AND `deleted`=0");

if ( $board->num_rows > 0 ) {
    while ( $row = mysqli_fetch_assoc($board) ) { ?>
        <div class="standard-body-title">
            <h3 class="pull-left"><?php
                echo $row['board_name'];
                foreach(array_filter(array_unique(explode(',', $row['shared_staff']))) as $shared_staff) {
                    if ( $shared_staff == 'ALL' || empty($shared_staff) ) {
                        echo '<div class="id-circle" style="background-color:#6DCFF6">All</div>';
                    } else {
                        profile_id($dbc, $shared_staff);
                    }
                } ?>
            </h3>
            <?php if ( vuaed_visible_function($dbc, 'newsboard') == 1 ) { ?>
                <div class="pull-right">
                  <span title="Tag Staff" onclick="quick_tag_staff('<?= $url_boardid ?>'); return false;">
                    <img src="../img/icons/ROOK-tag-icon.png" title="Tag Staff" style="width:30%" class="cursor-hand no-toggle double-gap-top gap-right" onclick="return false;">
                  </span>
                  <span class="header-icon"><img src="../img/icons/ROOK-trash-icon.png" alt="Archive" title="Archive News Board" class="cursor-hand no-toggle double-gap-top gap-right" /></span>
                </div>
            <?php } ?>
            <div class="clearfix"></div>
        </div><?php
        //$url_boardid = $row['boardid'];
    }
} ?>

<div class="standard-body-content double-padded"><?php
    if ( !empty($url_tag) ) {
        $newsboards = mysqli_query($dbc_news, "SELECT `n`.`newsboardid`, `n`.`newsboard_type`, `n`.`title`, `img`.`document_link` FROM `newsboard` `n` LEFT JOIN `newsboard_uploads` `img` ON (`n`.`newsboardid` = `img`.`newsboardid`) WHERE FIND_IN_SET ('$url_tag', `n`.`tags`) AND `n`.`boardid`='$url_boardid' AND `n`.`deleted`=0");
        if ( $newsboards->num_rows > 0 ) {
            while ( $row = mysqli_fetch_assoc($newsboards) ) {
                $url_type = ($row['newsboard_type'] == 'Softwarewide' ) ? 'sw' : ''; ?>
                <div class="col-sm-4 col-md-3">
                    <div class="nb-block">
                        <h4 class="no-gap-top"><a href="index.php?board=<?= $url_boardid ?>&tag=<?= $url_tag ?>&news=<?= $row['newsboardid'] ?>&type=<?= $url_type ?>"><?= ucwords($row['title']) ?></a></h4><?php
                        if ( !empty($row['document_link']) ) {
                            echo '<div class="nb-img"><a href="index.php?board='.$url_boardid.'&tag='.$url_tag.'&news='.$row['newsboardid'].'&type='.$url_type.'"><img src="download/'.$row['document_link'].'" /></a></div>';
                        } ?>
                        <?php
                        $roles = explode(",", $_SESSION['role']);
                        if(in_array('super', $roles) || in_array('admin', $roles)) {
                          echo '<span class="action-icons full-width">
                            <span title="Tag Staff" onclick="quick_tag_staff('.$url_boardid.','.$row["newsboardid"].'); return false;">
                              <img src="../img/icons/ROOK-tag-icon.png" title="Tag Staff" class="inline-img no-toggle" onclick="return false;">
                            </span>
                          </span>';
                        }
                        else {
                          $session_contact = $_SESSION['contactid'];
                          $boardid = $url_boardid;
                          $newsboardid = $row['newsboardid'];
                          $select_query = "SELECT staff from newsboard_tag where boardid=$boardid and newsboardid=$newsboardid";
                          $select_newsboard = explode(",", mysqli_fetch_assoc(mysqli_query($dbc, $select_query))['staff']);
                          if(in_array($session_contact, $select_newsboard)) {
                            echo '<span class="action-icons full-width">
                              <span title="Untag Yourself" onclick="untag_yourself('.$url_boardid.','.$row["newsboardid"].','.$session_contact.'); return false;">
                                <img src="../img/icons/ROOK-tag-icon.png" title="Untag Yourself" class="inline-img no-toggle" onclick="return false;">
                              </span>
                            </span>';
                          }
                        }
                        ?>
                    </div>
                </div><?php
            }
        } else {
            echo '<h4>No records found.</h4>';
        }

    } else {
        echo '<h4>Select a News Board to view news items.</h4>';
    } ?>
</div>

<?php if ( isset($_GET['mobile']) && $_GET['mobile'] == 'yes' ) {
    die();
} ?>
<script>
function quick_tag_staff(boardid, newsboardid = '') {
  if(newsboardid == '') {
    overlayIFrameSlider('tag_staff.php?tile=tasks&boardid='+boardid, 'auto', false, false);
  }
  else {
    overlayIFrameSlider('tag_staff.php?tile=tasks&boardid='+boardid+'&newsboardid='+newsboardid, 'auto', false, false);
  }
}

function untag_yourself(boardid, newsboardid, contactid) {
  confirm("Are you sure, you want to untag yourself from this post ?");
  $.ajax({
      type: 'GET',
      dataType: 'html',
      url: 'news_ajax_all.php?fill=untag&boardid='+boardid+'&newsboardid='+newsboardid+'&contactid='+contactid,
      success: function(response) {
          window.reload();
      }
  });
}
</script>
