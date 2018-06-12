<?php
include('../include.php');
?>
</head>
<body>
<?php 
include_once ('../navigation.php');
checkAuthorised('charts');
include 'config.php';
$return_url = 'daily_dishwasher_temp.php';
?>

<div class="container triple-pad-bottom">
    <div class="row">
        <div class="col-md-12">

        <h1 class="">Daily Dishwasher Temp Dashboard
        <?php
        if(config_visible_function_custom($dbc)) {
            echo '<a href="field_config.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
        }
        ?>
        </h1>

        <?php echo get_tabs('Daily Dishwasher Temp'); ?>
		<?php include('daily_dishwasher_temp_list.php'); ?>

        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
