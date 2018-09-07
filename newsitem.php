<?php
/*
Newsbaord
FFM
*/
if ( isset ( $_GET[ 'id'] ) ) {
	$nid = intval ( trim ( $_GET[ 'id' ] ) );
    $is_sw = ( isset($_GET['sw']) && $_GET['sw']=='yes' ) ? true : false;
} else {
	header( 'Location: newsboard.php' );
	exit;
}
include_once ('include.php');
if ($is_sw) {
    include ('database_connection_htg.php');
}
?>
</head>
<body>

<?php include ('navigation.php'); ?>

<div class="container"><?php
    $query = '
		SELECT DISTINCT
			news.newsboardid AS nID, news.title AS title, news.description AS description,
			img.document_link AS image
		FROM
			newsboard AS news
		LEFT JOIN
			newsboard_uploads AS img ON (news.newsboardid = img.newsboardid)
		WHERE
			deleted = 0 AND news.newsboardid = ' . $nid . '
		GROUP BY
			news.newsboardid DESC';
	
	$results = $is_sw ? mysqli_query($dbc_htg, $query) : mysqli_query($dbc, $query);
	
	if ( $results->num_rows > 0 ) { ?>
		<div class="rows grid"><?php
			while ( $row = mysqli_fetch_assoc ( $results ) ) { ?>
				<div class="col-sm-12 col-md-12">
					<div class="nb-title">
						<h1><?= str_replace(['Rook Connect', 'Precision Work Flow'], ['ROOK Connect', 'Precision Workflow'], $row[ 'title' ]); ?></h1>
						<div class="gap-left gap-top double-gap-bottom"><a href="newsboard.php" class="btn config-btn">Back to Dashboard</a></div>
					</div>
					<div class="nb-img nb-img-single"><img src="https://ffm.rookconnect.com/News Board/download/<?= $row[ 'image' ]; ?>" alt="<?= $row[ 'title' ]; ?>" /></div>
					<div class="nb-desc"><?= html_entity_decode ( str_replace(['Rook Connect', 'Precision Work Flow'], ['ROOK Connect', 'Precision Workflow'], $row[ 'description' ]) ); ?></div>
					<div class="nb-more triple-gap-bottom"><a href="newsboard.php" class="btn brand-btn btn-lg mobile-block">Back</a></div>
				</div>
                <input type="hidden" name="nb_title" value="<?= $row['title'] ?>" /><?php
			}
			
			$software_url = WEBSITE_URL; ?>
			
			<div class="clearfix"></div>
			
			<div class="row gap-top triple-gap-bottom">
                <div class="col-sm-12">
                    <h4>Comments</h4>
                    <input type="text" name="nb_comment" class="nb_comment form-control" />
                    <input type="hidden" name="nb_newsboardid" value="<?= $nid ?>" />
                    <input type="hidden" name="nb_contactid" value="<?= $_SESSION['contactid'] ?>" />
                </div>
                <?php
                    $sw_query = '';
                    $software_name = $_SERVER['SERVER_NAME'];
                    if ($is_sw) {
                        $sw_query = "AND `software_name`='$software_name'";
                        ?><input type="hidden" name="nb_software_name" value="<?= $software_name ?>" /><?php
                    } else {
                        ?><input type="hidden" name="nb_software_name" value="" /><?php
                    }
                    $comments_query = "SELECT `nbcommentid`, `contactid`, `created_date`, `comment` FROM `newsboard_comments` WHERE `newsboardid`=$nid AND `deleted`=0 $sw_query ORDER BY `nbcommentid` DESC";
                    $comments = $is_sw ? mysqli_query($dbc_htg, $comments_query) : mysqli_query($dbc, $comments_query);
                    
                    if ( $comments->num_rows > 0 ) { ?>
                        <div class="form-group clearfix full-width">
                            <div class="updates_<?= $row['tasklistid'] ?> col-sm-12"><?php
                                while ( $row_comment=mysqli_fetch_assoc($comments) ) { ?>
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
                    }
                ?>
            </div>
			
		</div><!-- .row .grid --><?php
	} ?>
</div>

<script>
    $('.nb_comment').keyup(function(e) {
		if(e.which == 13) {
            newsboardid = $('[name="nb_newsboardid"]').val();
            contactid = $('[name="nb_contactid"]').val();
            software_name = $('[name="nb_software_name"]').val();
            title = $('[name="nb_title"]').val();
            comment = $(this).val();
			$(this).val('');
			$(this).blur();
            
            $.ajax({
				method: 'POST',
				url: 'News Board/news_ajax_all.php?fill=comment_reply',
				data: {
                    newsboardid: newsboardid,
                    contactid: contactid,
                    software_name: software_name,
                    title: title,
                    comment: comment,
                    },
				complete: function(result) {
                    //console.log(result.responseText);
                    console.log(result);
                    //$('.updates_'+task_id).append(result);
                    window.location.reload();
                }
			});
		}
	});
</script>

<?php include ('footer.php'); ?>