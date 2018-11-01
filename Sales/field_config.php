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

	/*case 'fields':
        $page_title = "Fields";
        $include_file = 'field_config_fields.php';
		break;
	case 'dashboards':
        $page_title = "Dashboards";
        $include_file = 'field_config_dashboards.php';
		break;*/
	case 'accordion':
        $page_title = "accordion";
        $include_file = 'field_config_accordion.php';
		break;
	case 'lead_source':
        $page_title = "Lead Source";
        $include_file = 'field_config_lead_source.php';
		break;
	case 'next_action':
        $page_title = "Next Action";
        $include_file = 'field_config_next_action.php';
		break;
	case 'lead_status':
        $page_title = "Lead Status";
        $include_file = 'field_config_lead_status.php';
		break;

	case 'auto_archive':
        $page_title = "Auto Archive";
        $include_file = 'field_config_auto_archive.php';
		break;

    case 'actions':
        $_GET['tab'] = 'actions';
        $page_title = "Quick Action Flags";
        $include_file = '../Ticket/field_config_flags.php';
		break;

    case 'tile':
    default:
        $_GET['tab'] = 'tile';
        $page_title = "Tile Settings";
        $include_file = 'field_config_tile.php';
    /*
    case 'general':
    default:
        $_GET['tab'] = 'general';
        $page_title = SALES_TILE." Settings";
        $include_file = 'field_config_general.php';
    */
}
?>
<script type="text/javascript">
$(document).ready(function(){
    /* if($(window).width() > 767) {
        resizeScreen();
        $(window).resize(function() {
            resizeScreen();
        });
    } */
    $(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('#sales_div .tile-container').offset().top - 1;
        if(available_height > 200) {
            $('#sales_div .tile-sidebar, #sales_div .scale-to-fill').height(available_height);
        }
    }).resize();
});

function resizeScreen() {
    /* var view_height = $(window).height() > 500 ? $(window).height() : 500;
    $('#sales_div .scale-to-fill, #sales_div .tile-sidebar').height($('#sales_div').height() - $('.tile-header').height() + 15); */
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
                        <!--
                        <a href="?tab=general"><li <?= $_GET['tab'] == 'general' ? 'class="active"' : '' ?>><?= SALES_TILE ?> Settings</li></a>
                        -->
                        <a href="?tab=tile"><li <?= $_GET['tab'] == 'tile' ? 'class="active"' : '' ?>>Tile Settings</li></a>
                        <!--<a href="?tab=fields"><li <?= $_GET['tab'] == 'fields' ? 'class="active"' : '' ?>>Fields</li></a>
                        <a href="?tab=dashboards"><li <?= $_GET['tab'] == 'dashboards' ? 'class="active"' : '' ?>> Dashboards</li></a>-->
                        <a href="?tab=actions"><li <?= $_GET['tab'] == 'actions' ? 'class="active"' : '' ?>>Quick Action Icons</li></a>
                       <a href="?tab=accordion"><li <?= $_GET['tab'] == 'accordion' ? 'class="active"' : '' ?>>Fields</li></a>
                        <a href="?tab=lead_source"><li <?= $_GET['tab'] == 'lead_source' ? 'class="active"' : '' ?>>Lead Source</li></a>
                        <a href="?tab=next_action"><li <?= $_GET['tab'] == 'next_action' ? 'class="active"' : '' ?>>Next Action</li></a>
                        <a href="?tab=lead_status"><li <?= $_GET['tab'] == 'lead_status' ? 'class="active"' : '' ?>>Lead Status</li></a>
                        <a href="?tab=auto_archive"><li <?= $_GET['tab'] == 'auto_archive' ? 'class="active"' : '' ?>>Auto Archive</li></a>
                    </ul>
                </div>
                <?php
                    $get_field_config	= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `sales` FROM `field_config`" ) );
                    $value_config		= ',' . $get_field_config['sales'] . ',';
                ?>
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
