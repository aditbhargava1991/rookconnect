<?php
/*
 * News List
 * Included From: index.php
 * Function: List all the news items for a selected News Board and Tag
 * Query Variables Accepted: board, tag
 * Scripts on index.php
 */
 
include('../include.php');
include('../database_connection_htg.php');

$url_boardid = preg_replace('/[^0-9]/', '', $_GET['board']);
$url_tag = filter_var($_GET['tag'], FILTER_SANITIZE_STRING);

if ( !empty($url_boardid) ) {
    $board = mysqli_query($dbc, "SELECT `board_name`, `shared_staff` FROM `newsboard_boards` WHERE `boardid`='$url_boardid' AND `deleted`=0");
} else {
    $board = mysqli_query($dbc_htg, "SELECT `boardid`, `board_name`, `shared_staff` FROM `newsboard_boards` WHERE `deleted`=0");
}
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
            <div class="pull-right">
                <span class="header-icon"><img src="../img/icons/ROOK-trash-icon.png" alt="Archive" title="Archive News Board" class="cursor-hand no-toggle double-gap-top gap-right archive_board" /></span>
            </div>
            <div class="clearfix"></div>
        </div><?php
        $url_boardid = $row['boardid'];
    }
} ?>

<div class="standard-body-content double-padded"><?php
    if ( !empty($url_tag) ) {
        $newsboards = mysqli_query($dbc, "SELECT `n`.`newsboardid`, `n`.`title`, `img`.`document_link` FROM `newsboard` `n` LEFT JOIN `newsboard_uploads` `img` ON (`n`.`newsboardid` = `img`.`newsboardid`) WHERE FIND_IN_SET ('$url_tag', `n`.`tags`) AND `n`.`boardid`='$url_boardid' AND `n`.`deleted`=0");
        if ( $newsboards->num_rows > 0 ) {
            while ( $row = mysqli_fetch_assoc($newsboards) ) { ?>
                <div class="col-sm-3 nb-block">
                    <h4 class="no-gap-top"><a href="index.php?board=<?= $url_boardid ?>&tag=<?= $url_tag ?>&news=<?= $row['newsboardid'] ?>"><?= ucwords($row['title']) ?></a></h4><?php
                    if ( !empty($row['document_link']) ) {
                        echo '<div class="nb-img"><a href="index.php?board='.$url_boardid.'&tag='.$url_tag.'&news='.$row['newsboardid'].'"><img src="download/'.$row['document_link'].'" /></a></div>';
                    } ?>
                </div><?php
            }
        } else {
            echo '<h4>No records found.</h4>';
        }
    
    } else {
        $newsboards = mysqli_query($dbc_htg, "SELECT `n`.`newsboardid`, `n`.`title`, `img`.`document_link` FROM `newsboard` `n` LEFT JOIN `newsboard_uploads` `img` ON (`n`.`newsboardid` = `img`.`newsboardid`) WHERE `n`.`boardid`='$url_boardid' AND `n`.`deleted`=0");
        if ( $newsboards->num_rows > 0 ) {
            while ( $row = mysqli_fetch_assoc($newsboards) ) { ?>
                <div class="col-sm-3 nb-block">
                    <h4 class="no-gap-top"><a href="index.php?board=<?= $url_boardid ?>&tag=<?= $url_tag ?>&news=<?= $row['newsboardid'] ?>"><?= ucwords($row['title']) ?></a></h4><?php
                    if ( !empty($row['document_link']) ) {
                        echo '<div class="nb-img"><a href="index.php?board='.$url_boardid.'&tag='.$url_tag.'&news='.$row['newsboardid'].'"><img src="download/'.$row['document_link'].'" /></a></div>';
                    } ?>
                </div><?php
            }
        } else {
            echo '<h4>No records found.</h4>';
        }
    } ?>
</div>