<?php
/*
Customer Listing
*/
include ('../include.php');
?>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('custom');
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

        <div class="col-sm-10">
			<h1>Custom</h1>
		</div>
		<div class="col-sm-2 double-gap-top">
			<?php
				if(config_visible_function($dbc, 'custom') == 1) {
					echo '<a href="field_config_custom.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
				}
			?>
        </div>

		<div class="clearfix"></div>

        <form name="form_sites" method="post" action="" class="form-inline" role="form">

            <center>
            <div class="form-group">
                <label for="site_name" class="col-sm-5 control-label">Search By Any:</label>
                <div class="col-sm-6">
                    <?php if(isset($_POST['search_vendor_submit'])) { ?>
                        <input type="text" name="search_vendor" class="form-control" value="<?php echo $_POST['search_vendor']?>">
                    <?php } else { ?>
                        <input type="text" name="search_vendor" class="form-control">
                    <?php } ?>
                </div>
            </div>
            &nbsp;
				<button type="submit" name="search_vendor_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
				<button type="submit" name="display_all_vendor" value="Display All" class="btn brand-btn mobile-block">Display All</button>
            </center>

            <?php
            if(vuaed_visible_function($dbc, 'custom') == 1) {
                echo '<a href="add_custom.php" class="btn brand-btn mobile-block pull-right double-gap-top">Add Custom</a>';
            }
            ?>

            <div id="no-more-tables">

            <?php
            //Search
            $vendor = '';
            if (isset($_POST['search_vendor_submit'])) {
                if (isset($_POST['search_vendor'])) {
                    $vendor = $_POST['search_vendor'];
                }
            }
            if (isset($_POST['display_all_vendor'])) {
                $vendor = '';
            }

            include('custom_table.php');

            if(vuaed_visible_function($dbc, 'custom') == 1) {
            echo '<a href="add_custom.php" class="btn brand-btn mobile-block pull-right">Add Custom</a>';
            }

            ?>
        </form>

        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
