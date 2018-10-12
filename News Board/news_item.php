<?php
/*
 * News Item
 * Included From: index.php
 * Function: Display the news item
 * Query Variables Accepted: news
 * Scripts on index.php
 */

include('../include.php');
include('../database_connection_htg.php');

$url_boardid = preg_replace('/[^0-9]/', '', $_GET['board']);
$url_tag = trim($_GET['tag']);
$url_newsid = preg_replace('/[^0-9]/', '', $_GET['news']);
$url_type = filter_var($_GET['type'], FILTER_SANITIZE_STRING);
$dbc_news = ($url_type == 'sw') ? $dbc_htg : $dbc;

if ( !empty($url_newsid) ) {
    $news = mysqli_query($dbc_news, "SELECT `b`.`board_name`, `b`.`shared_staff`, `n`.`newsboardid`, `n`.`newsboard_type`, `n`.`tags`, `n`.`title`, `n`.`description`, `img`.`document_link` FROM `newsboard` `n` LEFT JOIN `newsboard_uploads` `img` ON (`n`.`newsboardid` = `img`.`newsboardid`) LEFT JOIN `newsboard_boards` `b` ON (`n`.`boardid` = `b`.`boardid`) WHERE `n`.`newsboardid`='$url_newsid' AND `n`.`deleted`=0");

    if ( $news->num_rows > 0 ) {
        // Insert to newsboard_seen on the local software as this news item is seen by the user now
        $query_mod_insert = $url_type=='sw' ? ", `newsboard_src`" : '';
        $query_mod_select = $url_type=='sw' ? ", 'sw'" : '';
        $query_mod_from = $url_type=='sw' ? "AND `newsboard_src`='sw'" : '';
        mysqli_query($dbc, "INSERT INTO `newsboard_seen` (`newsboardid`, `contactid`".$query_mod_insert.") SELECT '".$url_newsid."', '".$_SESSION['contactid']."'".$query_mod_select." FROM (SELECT COUNT(*) `rows` FROM `newsboard_seen` WHERE `newsboardid`='".$url_newsid."' AND `contactid`='".$_SESSION['contactid']."' ".$query_mod_from.") `count` WHERE `count`.`rows`=0");

        while ( $row = mysqli_fetch_assoc($news) ) { ?>
            <div class="standard-body-title">
                <div class="col-xs-9">
                    <h3 class="no-gap-bottom"><?= ucwords($row['title']) ?></h3>
                    <div class="double-gap-left offset-bottom-15">
                        <small><?php
                            $tags = '';
                            foreach ( explode(',', $row['tags']) as $tag ) {
                                $tags .= $tag .', ';
                            }
                            echo !empty($tags) ? rtrim($tags, ', ') : ''; ?>
                        </small>
                    </div>
                </div>
                <div class="col-xs-3 text-right pad-top-15 double-pad-right">
                    <?php if ( vuaed_visible_function($dbc, 'newsboard') == 1 ) { ?>
                        <span class="header-icon"><a class="cursor-hand" onclick="overlayIFrameSlider('add_news.php?news=<?= $url_newsid ?>', 'auto', true, false, 'auto', true);"><img src="../img/icons/ROOK-edit-icon.png" class="no-toggle" title="Edit" /></a></span>
                    <?php } ?>
                    <span class="header-icon"><a class="cursor-hand" onclick="overlayIFrameSlider('../quick_action_notes.php?tile=newsboard&id=<?= $url_newsid ?>&type=<?= $url_type ?>', 'auto', true, false, 'auto', false);"><img src="../img/icons/ROOK-reply-icon.png" class="no-toggle" title="Add Note" /></a></span>
                    <?php if ( vuaed_visible_function($dbc, 'newsboard') == 1 ) { ?>
                        <span class="header-icon"><img src="../img/icons/ROOK-trash-icon.png" class="no-toggle cursor-hand archive_newsitem" title="Archive" data-id="<?= $url_newsid ?>" data-type="<?= $row['newsboard_type']; ?>" /></span>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="standard-body-content double-padded">
                <div><?php
                    if ( !empty($row['document_link']) ) { ?>
                        <div class="nb-img no-gap-top"><img src="download/<?= $row['document_link'] ?>" /></div><?php
                    } ?>
                    <div class="gap-top"><?= html_entity_decode($row['description']) ?></div>
                </div>

                <div class="row gap-top">
                    <div class="col-sm-12">
                        <h4>Comments</h4>
                        <!-- <input type="text" name="nb_comment" class="nb_comment form-control" /> -->
                        <input type="hidden" name="nb_newsboardid" value="<?= $url_newsid ?>" />
                        <input type="hidden" name="nb_contactid" value="<?= $_SESSION['contactid'] ?>" />
                    </div><?php
                    $sw_query = '';
                    $software_name = $_SERVER['SERVER_NAME'];
                    if ( $url_type == 'sw' ) {
                        $sw_query = "AND `software_name`='$software_name'";
                        ?><input type="hidden" name="nb_software_name" value="<?= $software_name ?>" /><?php
                    } else {
                        ?><input type="hidden" name="nb_software_name" value="" /><?php
                    }
                    $comments = mysqli_query($dbc_news, "SELECT `nbcommentid`, `contactid`, `created_date`, `comment` FROM `newsboard_comments` WHERE `newsboardid`='$url_newsid' AND `deleted`=0 $sw_query ORDER BY `nbcommentid` DESC");

                    if ( $comments->num_rows > 0 ) { ?>
                        <div class="form-group clearfix full-width">
                            <div class="col-sm-12"><?php
                                while ( $row_comment = mysqli_fetch_assoc($comments) ) { ?>
                                    <div class="note_block row gap-top">
                                        <div class="pull-left"><?= profile_id($dbc, $row_comment['contactid']); ?></div>
                                        <div class="pull-left gap-left">
                                            <div><?= html_entity_decode($row_comment['comment']); ?></div>
                                            <div><small><em>Added by <?= get_contact($dbc, $row_comment['contactid']); ?> on <?= $row_comment['created_date']; ?></em></small></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <hr class="margin-vertical" /><?php
                                } ?>
                            </div>
                            <div class="clearfix"></div>
                        </div><?php
                    } ?>
                </div>
            </div><?php
        }

    } else { ?>
        <div class="standard-body-title">
            <h3>Not Found</h3>
        </div>
        <div class="standard-body-content double-padded">
            <h4>News item you are looking for is either moved or removed.</h4>
        </div><?php
    }
}
?>

<?php if ( isset($_GET['mobile']) && $_GET['mobile'] == 'yes' ) {
    die();
} ?>