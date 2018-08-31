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

if ( !empty($url_newsid) ) {
    $news = mysqli_query($dbc, "SELECT `b`.`board_name`, `b`.`shared_staff`, `n`.`newsboardid`, `n`.`newsboard_type`, `n`.`tags`, `n`.`title`, `n`.`description`, `img`.`document_link` FROM `newsboard` `n` LEFT JOIN `newsboard_uploads` `img` ON (`n`.`newsboardid` = `img`.`newsboardid`) LEFT JOIN `newsboard_boards` `b` ON (`n`.`boardid` = `b`.`boardid`) WHERE `n`.`newsboardid`='$url_newsid' AND `n`.`deleted`=0");
    
    if ( $news->num_rows > 0 ) {
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
                    <span class="header-icon"><a class="cursor-hand" onclick="overlayIFrameSlider('add_news.php?news=<?= $url_newsid ?>', 'auto', true, false, 'auto', true);"><img src="../img/icons/ROOK-edit-icon.png" class="no-toggle" title="Edit" /></a></span>
                    <span class="header-icon"><a class="cursor-hand" onclick="overlayIFrameSlider('add_news.php?news=<?= $url_newsid ?>', 'auto', true, false, 'auto', true);"><img src="../img/icons/ROOK-reply-icon.png" class="no-toggle" title="Add Note" /></a></span>
                    <span class="header-icon"><img src="../img/icons/ROOK-trash-icon.png" class="no-toggle cursor-hand archive_newsitem" title="Archive" data-id="<?= $url_newsid ?>" data-type="<?= $row['newsboard_type']; ?>" /></span>
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