<?php
/*
 * News Search
 * Included From: index.php
 * Function: Search Softwarewide or Local news items
 * Query Variables Accepted: search
 * Scripts on index.php
 */
 
include('../include.php');
include('../database_connection_htg.php');

$url_search = filter_var($_GET['s'], FILTER_SANITIZE_STRING);

$board = mysqli_query($dbc_news, "SELECT `board_name`, `shared_staff` FROM `newsboard_boards` WHERE `boardid`='$url_boardid' AND `deleted`=0");

?>
<div class="standard-body-title">
    <h3>Search Results for '<?= $url_search ?>'</h3>
</div>

<div class="standard-body-content double-padded"><?php
    if ( !empty($url_search) ) {
        $query = "SELECT `n`.`newsboardid`, `n`.`boardid`, `n`.`newsboard_type`, `n`.`title`, `img`.`document_link` FROM `newsboard` `n` LEFT JOIN `newsboard_uploads` `img` ON (`n`.`newsboardid` = `img`.`newsboardid`) LEFT JOIN `newsboard_boards` `b` ON (`n`.`boardid` = `b`.`boardid`) WHERE (`n`.`title` LIKE '%$url_search%' OR `n`.`description` LIKE '%$url_search%') AND `n`.`deleted`=0 AND `n`.`boardid` <> 'NULL' AND (FIND_IN_SET('{$_SESSION['contactid']}', `b`.`shared_staff`) OR `b`.`shared_staff`='ALL' OR `b`.`shared_staff`='')";
        $newsboards_sw = mysqli_query($dbc_htg, $query);
        $sw_arr = array();
        while ( $sw_row = mysqli_fetch_assoc($newsboards_sw) ) {
            array_push($sw_arr, $sw_row);
        }
        $newsboards_local = mysqli_query($dbc, $query);
        $local_arr = array();
        while ( $local_row = mysqli_fetch_assoc($newsboards_local) ) {
            array_push($local_arr, $local_row);
        }
        $newsboards = array_merge($sw_arr, $local_arr);
        
        if ( !empty($newsboards) ) {
            foreach ($newsboards as $row ) {
                $url_type = ($row['newsboard_type'] == 'Softwarewide' ) ? 'sw' : ''; ?>
                <div class="col-sm-4 col-md-3">
                    <div class="nb-block">
                        <h4 class="no-gap-top"><a href="index.php?board=<?= $row['boardid'] ?>&tag=<?= $row['tag'] ?>&news=<?= $row['newsboardid'] ?>&type=<?= $url_type ?>"><?= ucwords($row['title']) ?></a></h4><?php
                        if ( !empty($row['document_link']) ) {
                            echo '<div class="nb-img"><a href="index.php?board='.$row['boardid'].'&tag='.$row['tag'].'&news='.$row['newsboardid'].'&type='.$url_type.'"><img src="download/'.$row['document_link'].'" /></a></div>';
                        } ?>
                    </div>
                </div><?php
            }
        } else {
            echo '<h4>No records found.</h4>';
        }
    
    } else {
        echo '<h4>No records found.</h4>';
    } ?>
</div>