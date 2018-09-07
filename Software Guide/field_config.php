<?php
/*
 * Software Guide Config
 */
error_reporting(0);
include ('../include.php');
include ('../database_connection_htg.php');
?>

<script type="text/javascript">
    $(document).ready(function() {
         $('.panel-heading.mobile_load').click(loadPanel);
        
        $(window).resize(function() {
            var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('#sg_div .tile-container').offset().top - 1;
            if(available_height > 200) {
                $('#sg_div .tile-sidebar, #sg_div .scale-to-fill').height(available_height);
            }
        }).resize();
        
        $('.delete-additional-guide').click(function() {
            var result = confirm("Are you sure you want to delete this?");
            if (result) {
                var guideid = $('[name=guideid]').val();
                $.ajax({
                    url: 'guide_ajax_all.php?fill=delete_additional_guide&guideid='+guideid,
                    method: 'GET',
                    response: 'html',
                    success: function(response) {
                        alert('Additional software guide deleted.');
                        window.location.reload();
                    }
                });
            }
        });
    });

    function loadPanel() {
        var panel = $(this).closest('.panel').find('.panel-body');
        var guide = panel.data('guide'); alert(guide);
        panel.html('');
        
        $.ajax({
            url: 'guide_ajax_all.php?fill=load_panel_config&guide='+guide,
            method: 'GET',
            response: 'html',
            success: function(response) {
                panel.html(response);
            }
        });
    }
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
    checkAuthorised('software_guide');

    $tab_list = [
        'guides' => 'Software Guides',
        'notes' => 'Notes'
    ];

    $tab = empty($_GET['tab']) ? '' : $_GET['tab'];
    $url_guideid = preg_replace('/[^0-9]/', '', $_GET['guide']);
    
    if ( isset($_POST['submit_guide']) ) {
        $guideid = preg_replace('/[^0-9]/', '', $_POST['guideid']);
        $additional_guide = filter_var($_POST['additional_guide'], FILTER_SANITIZE_STRING);
        $local_guideid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `guideid` FROM `local_software_guide` WHERE `guideid`='$guideid'"))['guideid'];
        if ( empty($local_guideid) ) {
            $query = "INSERT INTO `local_software_guide` (`guideid`, `additional_guide`) VALUES ('$guideid', '$additional_guide')";
        } else {
            $query = "UPDATE `local_software_guide` SET `additional_guide`='$additional_guide' WHERE `guideid`='$guideid'";
        }
        mysqli_query($dbc, $query);
    }
?>

<div id="sg_div" class="container">
    
    <div class="row hide_on_iframe">
		<div class="main-screen">
            <!-- Tile Header -->
            <div class="tile-header standard-header">
                <div class="row">
                    <div class="col-xs-12"><h1><a href="index.php" class="default-color">Software Guide</a></h1></div>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->
            
            <div class="tile-container" style="height: 100%;">
                <!-- Sidebar -->
                <!-- Mobile -->
                <div id="guide_accordions" class="sidebar show-on-mob panel-group block-panels col-xs-12 form-horizontal"><?php
                    $tiles = mysqli_query($dbc_htg, "SELECT `tile` FROM `how_to_guide` GROUP BY `tile` ORDER BY `tile`");
                    if ( $tiles->num_rows > 0 ) {
                        foreach ( $tiles as $tile ) {
                            $guide = mysqli_query($dbc_htg, "SELECT `guideid`, `tile`, `subtab` FROM `how_to_guide` WHERE `tile`='{$tile['tile']}' AND `deleted`=0 ORDER BY `sort_order`");
                            if ( $guide->num_rows > 0 ) {
                                while ( $row=mysqli_fetch_assoc($guide) ) {
                                    $tab_id = $row['guideid']; ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading mobile_load">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#guide_accordions" href="#collapse_<?= $tab_id ?>">
                                                    <?= $row['tile'] . ': ' . $row['subtab']; ?><span class="glyphicon glyphicon-plus"></span>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_<?= $tab_id ?>" class="panel-collapse collapse">
                                            <div class="panel-body" data-guide="<?= $row['guideid'] ?>">
                                                Loading...
                                            </div>
                                        </div>
                                    </div><?php
                                }
                            }
                        }
                    } else {
                        echo '<li>No software guides available</li>';
                    } ?>
                </div>
                
                <!-- Desktop -->
                <div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
                    <ul>
                        <li><a href="index.php">Dashboard</a></li>
                        <li class="sidebar-higher-level highest-level">
                            <a class="cursor-hand <?= $tab=='guides' ? 'active' : 'collapsed' ?>" data-toggle="collapse" data-target="#guides">Guides<span class="arrow"></span></a>
                            <ul id="guides" class="collapse <?= $tab=='guides' ? 'in' : '' ?>"><?php
                                $tiles = mysqli_query($dbc_htg, "SELECT `tile` FROM `how_to_guide` GROUP BY `tile` ORDER BY `tile`");
                                if ( $tiles->num_rows > 0 ) {
                                    foreach ( $tiles as $tile ) {
                                        $guide = mysqli_query($dbc_htg, "SELECT `guideid`, `tile`, `subtab` FROM `how_to_guide` WHERE `tile`='{$tile['tile']}' AND `deleted`=0 ORDER BY `sort_order`"); ?>
                                        <li class="sidebar-higher-level">
                                            <a class="cursor-hand <?= $_GET['tile'] == $tile['tile'] ? 'active' : 'collapsed' ?>" data-toggle="collapse" data-target="#collapse_<?= strtolower(str_replace(' ', '_', $tile['tile'])) ?>"><?= $tile['tile'] ?><span class="arrow"></span></a><?php
                                            if ( $guide->num_rows > 0 ) { ?>
                                                <ul id="collapse_<?= strtolower(str_replace(' ', '_', $tile['tile'])) ?>" class="collapse <?= $_GET['tile'] == $tile['tile'] ? 'in' : '' ?>"><?php
                                                    while ( $row=mysqli_fetch_assoc($guide) ) { ?>
                                                        <li class="<?= $url_guideid==$row['guideid'] ? 'active' : '' ?>"><a href="?tab=guides&guide=<?= $row['guideid'] ?>&tile=<?= $row['tile'] ?>"><?= $row['subtab'] ?></a></li><?php
                                                    } ?>
                                                </ul><?php
                                            } ?>
                                        </li><?php
                                    }
                                } ?>
                            </ul>
                        </li>
                    </ul>
                </div><!-- .tile-sidebar -->
                
                <!-- Main Screen -->
                <div class="scale-to-fill has-main-screen hide-titles-mob" style="margin-bottom:-20px;">
                    <div class="main-screen standard-body form-horizontal full-height">
                        <?php include('field_config_guides.php'); ?>
                    </div>
                </div>
                
                <div class="clearfix"></div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>