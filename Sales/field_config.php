<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('sales');

//$map_array = array("Leads"=>"Leads","Opportunities"=>"Opportunities","In_Negotiations"=>"In Negotiations","Closed_Successfully"=>"Closed Successfully","Lost_Abandoned"=>"Lost Abandoned","Pending"=>"Pending","Prospect"=>"Prospect","Qualification"=>"Qualification","Needs Analysis"=>"Needs Analysis","Propose Quote"=>"Propose Quote","Negotiations"=>"Negotiations","Won"=>"Won","Lost"=>"Lost","Abandoned"=>"Abandoned","Future_Review"=>"Future Review");

$sales_tile = SALES_TILE;
$sales_noun = SALES_NOUN;

switch($_GET['tab']) {
    case 'general':
    default:
        $_GET['tab'] = 'general';
        $page_title = SALES_TILE." Settings";
        $include_file = 'field_config_general.php';
}
?>
<script type="text/javascript">
$(document).ready(function(){
    if($(window).width() > 767) {
        resizeScreen();
        $(window).resize(function() {
            resizeScreen();
        });
    }
});

function resizeScreen() {
    var view_height = $(window).height() > 500 ? $(window).height() : 500;
    $('#sales_div .scale-to-fill,#sales_div .scale-to-fill .main-screen,#sales_div .tile-sidebar').height($('#sales_div .tile-container').height());
}
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div id="sales_div" class="container">
    <div class="row">
        <div class="main-screen"><?php
            include('tile_header.php'); ?>

            <div class="tile-container">
                <div class="standard-collapsible tile-sidebar tile-sidebar-noleftpad hide-on-mobile">
                    <ul>
                        <a href="index.php"><li>Back to Dashboard</li></a>
                        <a href="field_config.php?tab=general"><li <?= $_GET['tab'] == 'general' ? 'class="active"' : '' ?>><?= SALES_TILE ?> Settings</li></a>
                    </ul>
                </div>

                <div class="scale-to-fill" style="background-color: #fff">
                    <div class="main-screen-white standard-body" style="padding-left: 0; padding-right: 0; border: none;">
                        <div class="standard-body-title">
                            <h3><?= $page_title ?></h3>
                        </div>
                        <div class="standard-body-content pad-10">
                            <?php include($include_file); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include ('../footer.php'); ?>
