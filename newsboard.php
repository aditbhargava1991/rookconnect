<?php
/*
Newsbaord
FFM
*/
include_once ('include.php');
include ('database_connection_htg.php');
?>
<!--Masonry Layout-->
<script src="<?php echo WEBSITE_URL; ?>/js/imagesloaded.pkgd.min.js"></script>
<script src="<?php echo WEBSITE_URL; ?>/js/masonry.pkgd.min.js"></script>
<script>
$(document).ready(function() {
	$('.grid').each(function() {
		var grid = this;
		$(grid).imagesLoaded(function(){
			$(grid).masonry({
				itemSelector: '.grid-item',
				columnWidth: '.nb-block',
				percentPosition: true,
				gutter: 20
			});
		});
	});
});
</script>
	</head>
<body>
<?php include ('navigation.php'); ?>

<div class="container">
	<h1>News Board</h1>
	<div style="text-align:right;">
		<p>
			<a href="<?php echo WEBSITE_URL.'/News Board/newsboard.php'; ?>"> 
				<img src="<?php echo WEBSITE_URL; ?>/img/icons/switch-6.png" width="50px" style='display:none;' class="switch_info_off1">
				<img src="<?php echo WEBSITE_URL; ?>/img/icons/switch-7.png" class="switch_info_on1" width="50px">
			</a>
		</p>
	</div><?php
	
    $query_add = '';
    
    //$sea_software_dbc is set in SEA's software's Database_connection.php file.
    if ( isset($dbczen) && isset($sea_software_dbc) ) {
        $query_add  = 'news.cross_software_approval = 1 AND';
    }
    
	$query = "SELECT DISTINCT
            news.newsboardid AS nID, news.title AS title, news.description AS description, news.expiry_date,
			img.document_link AS image
		FROM
			newsboard AS news
		LEFT JOIN
			newsboard_uploads AS img ON (news.newsboardid = img.newsboardid)
		WHERE
			news.deleted = 0 AND
            ". $query_add ."
            (news.expiry_date > NOW() OR news.expiry_date='0000-00-00' OR news.issue_date > DATE_SUB(NOW(), INTERVAL 1 YEAR))
		GROUP BY
			news.newsboardid DESC";
    
    //Get softwarewide news boards
    $query_sw = "SELECT DISTINCT
            news.newsboardid AS nID, news.title AS title, news.description AS description, news.expiry_date,
			img.document_link AS image
		FROM
			newsboard AS news
		LEFT JOIN
			newsboard_uploads AS img ON (news.newsboardid = img.newsboardid)
		WHERE
			news.deleted = 0 AND
            news.newsboard_type = 'Softwarewide' AND
            (news.expiry_date > NOW() OR news.expiry_date='0000-00-00' OR news.issue_date > DATE_SUB(NOW(), INTERVAL 1 YEAR))
		GROUP BY
			news.newsboardid DESC";
			
    echo '<div class="rows grid">';
        
        // Softwarewide news boards added by FFM
        $results_sw = mysqli_query($dbc_htg, $query_sw);
        if ( $results_sw->num_rows > 0 ) {
            while ( $row_sw = mysqli_fetch_assoc ( $results_sw ) ) {
                $dbc->query("INSERT INTO `newsboard_seen` (`newsboardid`,`contactid`,`newsboard_src`) SELECT '".$row['nID']."','".$_SESSION['contactid']."','sw' FROM (SELECT COUNT(*) `rows` FROM `newsboard_seen` WHERE `newsboardid`='".$row['nID']."' AND `contactid`='".$_SESSION['contactid']."' AND `newsboard_src`='sw') `count` WHERE `count`.`rows`=0");
                $desc = html_entity_decode ( $row_sw[ 'description' ] ); ?>
                <div class="nb-block col-sm-3 col-md-3 grid-item">
                    <div class="nb-title"><h2><?= str_replace(['Rook Connect', 'Precision Work Flow'], ['ROOK Connect', 'Precision Workflow'], $row_sw[ 'title' ]); ?></h2></div>
                    <div class="nb-img"><?php if($row_sw[ 'image' ] !== NULL && $row_sw[ 'image' ] !== '') { ?><img src="https://ffm.rookconnect.com/News Board/download/<?= $row_sw[ 'image' ]; ?>" alt="<?= $row_sw[ 'title' ]; ?>" /><?php } ?></div>
                    <div class="nb-desc"><?= limit_text( str_replace(['Rook Connect', 'Precision Work Flow'], ['ROOK Connect', 'Precision Workflow'], $desc), 50 ); ?></div>
                    <div class="nb-more"><a href="newsitem.php?sw=yes&id=<?= $row_sw[ 'nID' ]; ?>" class="btn brand-btn mobile-block pull-right">Read More</a></div>
                    <div class="clearfix"></div>
                </div><?php
            }
        }
        
        if ( isset($dbczen) && isset($sea_software_dbc) ) {
            // START If cross_software is set START //
            $results = mysqli_query($dbczen, $query);
            $cross_num_rows = mysqli_num_rows($results);
            if ( $results->num_rows > 0 ) {
                while ( $row = mysqli_fetch_assoc ( $results ) ) {
                    $dbc->query("INSERT INTO `newsboard_seen` (`newsboardid`,`contactid`) SELECT '".$row['nID']."','".$_SESSION['contactid']."' FROM (SELECT COUNT(*) `rows` FROM `newsboard_seen` WHERE `newsboardid`='".$row['nID']."' AND `contactid`='".$_SESSION['contactid']."' AND `newsboard_src` IS NULL) `count` WHERE `count`.`rows`=0");
                    $desc = html_entity_decode ( $row[ 'description' ] ); ?>
                    <div class="nb-block col-sm-3 col-md-3 grid-item">
                        <div class="nb-title"><h2><?= $row[ 'title' ]; ?></h2></div>
                        <div class="nb-img"><?php if($row[ 'image' ] !== NULL && $row[ 'image' ] !== '') { ?><img src="https://sea.freshfocussoftware.com/News Board/download/<?= $row[ 'image' ]; ?>" alt="<?= $row[ 'title' ]; ?>" /><?php } ?></div>
                        <div class="nb-desc"><?= limit_text( $desc, 50 ); ?></div>
                        <div class="nb-more"><a href="newsitem.php?id=<?= $row[ 'nID' ]; ?>" class="btn brand-btn mobile-block pull-right">Read More</a></div>
                        <div class="clearfix"></div>
                    </div><?php
                }
            } else {
                echo '<div class="">No news to display at this time. Please check back later.</div>';
            }
            // DONE If cross_software is set DONE //
        
        } else {
            $results = mysqli_query($dbc, $query);
            if ( $results->num_rows > 0 ) {
                while ( $row = mysqli_fetch_assoc ( $results ) ) {
                    $dbc->query("INSERT INTO `newsboard_seen` (`newsboardid`,`contactid`) SELECT '".$row['nID']."','".$_SESSION['contactid']."' FROM (SELECT COUNT(*) `rows` FROM `newsboard_seen` WHERE `newsboardid`='".$row['nID']."' AND `contactid`='".$_SESSION['contactid']."' AND `newsboard_src` IS NULL) `count` WHERE `count`.`rows`=0");
                    $desc = html_entity_decode ( $row[ 'description' ] ); ?>
                    <div class="nb-block col-sm-3 col-md-3 grid-item">
                        <div class="nb-title"><h2><?= str_replace(['Rook Connect', 'Precision Work Flow'], ['ROOK Connect', 'Precision Workflow'], $row[ 'title' ]); ?></h2></div>
                        <div class="nb-img"><?php if($row[ 'image' ] !== NULL && $row[ 'image' ] !== '') { ?><img src="News Board/download/<?= $row[ 'image' ]; ?>" alt="<?= $row[ 'title' ]; ?>" /><?php } ?></div>
                        <div class="nb-desc"><?= limit_text( str_replace(['Rook Connect', 'Precision Work Flow'], ['ROOK Connect', 'Precision Workflow'], $desc), 50 ); ?></div>
                        <div class="nb-more"><a href="newsitem.php?id=<?= $row[ 'nID' ]; ?>" class="btn brand-btn mobile-block pull-right">Read More</a></div>
                        <div class="clearfix"></div>
                    </div><?php
                }
            } else {
                echo '<div class="">No news to display at this time. Please check back later.</div>';
            }
        }
    
	echo '</div><!-- .row .grid -->'; ?>
    
</div><!-- .container -->

<?php include ('footer.php'); ?>