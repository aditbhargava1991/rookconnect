<?php
	/*
	 * View Guide
	 */
	error_reporting(0);
	if(!isset($_GET['from_manual']) && $_GET['from_manual'] != 1)
	include ('../include.php');
    include ('check_security.php');
    include ('../database_connection_htg.php');
    $rookconnect = get_software_name();
    
    if( isset($_GET['guideid']) && !empty($_GET['guideid']) ) {
        $guideid = preg_replace('/[^0-9]/', '', $_GET['guideid']);
    }
    $result = mysqli_query($dbc_htg, "SELECT * FROM `how_to_guide` WHERE `guideid`='$guideid'");
    while ( $row=mysqli_fetch_assoc($result) ) {
        $tile = $row['tile'];
        $subtab = $row['subtab'];
        $description = html_entity_decode($row['description']);
    }
    
    $page = '';
    if ( isset($_GET['page']) && !empty($_GET['page']) ) {
        $page = preg_replace('/[^0-9]/', '', $_GET['page']);
    }
?>

</head>
<body>

<?php
	include_once ('../navigation.php');
    checkAuthorised('how_to_guide');
?>

<div class="container how-to-guide">
	<div class="row">

        <h1>Software Guide</h1>
        <div class="gap-left gap-top double-gap-bottom"><a href="guides_dashboard.php?page=<?= $page; ?>" class="btn config-btn">Back to Dashboard</a></div>
        
        <div class="gap-left">
            <div class=""><h2><?= $tile; ?> Tile</h2></div>
            <div class=""><h3><?= $subtab; ?></h3></div>
            <div class="double-gap-top"><?= $description; ?></div>
        </div>
        
        <div class="pad-left double-gap-top double-gap-bottom"><a href="guides_dashboard.php?page=<?= $page; ?>" class="btn brand-btn btn-lg">Back</a></div>
		
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>