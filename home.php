<?php
/*
Dashboard FFM
*/
error_reporting(0);
include ('include.php');
$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`,`email_address` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));
foreach($staff_list as $staff) {
	$tag_staff_array[] = $staff['contactid'];
	$tag_staff_email_array[] = $staff['email_address'];
}

//foreach($tag_staff_email_array as $tag_staff_email) {
try {
		$sender = '';
		$cc_emails = '';
		$bcc_emails = '';
		$title = "test";
		$user = $tag_staff_email_array;
		$subject = "New post -> $title has been added to the News Board.";
		$body = "New Post with Name $title has been added to the news Board. <br>";
		$body .= 'For more details <a href="'.'https://' . $_SERVER['SERVER_NAME'] . '/News Board/index.php">click here</a>';
		send_email($sender, $user, $cc_emails, $bcc_emails, $subject, html_entity_decode($body), '');
} catch (Exception $e) {
		$error .= "Unable to send email: ".$e->getMessage()."\n";
}
exit;
?>
<script>
	jQuery(document).ready(function($){
			$('.live-search-box').focus();
			$('.live-search-list div').each(function(){
			$(this).attr('data-search-term', $(this).text().toLowerCase());
			});

			$('.live-search-box').on('keyup', function(){

			var searchTerm = $(this).val().toLowerCase();

				$('.live-search-list div.dashboard').each(function(){

					if(searchTerm == '' && $(this).hasClass('searchOnly')) {
						$(this).hide();
					} else if(searchTerm == '') {
						$(this).show();
					} else if (($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) && $(this).hasClass('searchOnly')) {
						$(this).show();
					} else {
						if(!$(this).hasClass('dont-hide')) {
							$(this).hide();
						}
					}

				});

			});

});
$(document).ready(function() {
	var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
	/* if(isChrome !== true) {
		$('.alert_noChrome').show(251);
		$('.alert_noChrome').html('<span style="padding:10px; font-size:13px;"><img src="img/warning.png" style="width:25px;"> <span style="color:red; font-weight:bold;">NOTICE: </span>You are currently using a browser other than Google Chrome. It is <strong>strongly</strong> recommended that you use this software with the latest Google Chrome internet browser. Download Google Chrome <a href="https://www.google.com/chrome/browser/desktop/index.html?brand=CHBD&gclid=CPT0vf_Xss4CFYY0aQodcMkDfA" target="_BLANK" style="font-weight:bold; color:red; text-decoration:underline" >here</a>.</span>');
	} */
	if($('.ffmbtnpic.dashboard.link').length == 1) {
		$('.ffmbtnpic.dashboard.link').each(function() {window.location.href = $(this).find('a').attr('href');});
	}
});
function go_to_dashboard(target) {
	window.location.href = '?dashboard_id='+target;
}
</script>
</head>
<body class="home-page">

<?php include ('navigation.php');

?>

<div class="container">
<!-- <div class='alert_noChrome' style='padding:10px; display:none;margin:15px; border:2px solid grey; border-radius:10px; width:100%; background-color:rgba(155,155,155,0.5)'></div> -->
		<?php
		$contactidfortile = $_SESSION['contactid'];
		$tile_menu = '';
		$tie_size = '';
		$get_config_size = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT tile_size FROM user_settings WHERE contactid='$contactidfortile'"));
		$tile_size = $get_config_size['tile_size'];

		if($tile_size == '1') {
			?>
			<style>
				.dashboard {
					    padding: 5px;
						margin: 0 auto !important;
						position: relative !important;
						text-align: center !important;
						display: inline-table;
						float: none !important;
				}
				.dashboard.link {
					max-width:150px;
					font-size:11.5px;
					max-height:50px;
					vertical-align:top;
					min-height:50px;
				}
				.dashboard.link a {
					font-size: 11.5px;
					padding-left:5px;
					padding-right:5px;
					padding-top:2px;
					padding-bottom:2px;
					height:50px;
				}
				.dashboard.link a.support-btn {
					font-size: 11.5px;
					padding-left:5px;
					padding-right:5px;
					padding-top:2px;
					padding-bottom:2px;
					/*text-align: left;
					padding-left: 20px;*/
					height:50px;
				}
				.dashboard.link a.support-btn:hover {
					font-size: 11.5px;
					/*text-align: left;
					padding-left: 20px;*/
					height:50px;
				}
				.row {text-align:center;}
			</style>
			<?php
		} else if($tile_size == '2') {
			?>
			<style>
				.dashboard {
					    padding: 5px;
						margin: 0 auto !important;
						position: relative !important;
						text-align: center !important;
						display: inline-table;
						float: none !important;
				}
				.dashboard.link {
					max-width:200px;
					font-size:15px;
					max-height:60px;
					vertical-align:top;
					min-height:60px;
				}
				.dashboard.link a {
					font-size: 15px;
					padding:3px;
					height:60px;
				}
				.dashboard.link a.support-btn {
					font-size: 15px;
					padding:3px;
					text-align: left;
					text-align:center;
					/*text-align: left;
					padding-left: 30px;*/
					height:60px;
				}
				.dashboard.link a.support-btn:hover {
					font-size: 15px;
					padding:3px;
					/*text-align: left;
					padding-left: 30px;*/
					height:60px;
				}
				.row {text-align:center;}
			</style>
			<?php
		} else if($tile_size == '3' || $tile_size == '' || $tile_size == NULL) {
			?>
			<style>
				.dashboard {
					    padding: 5px;
						margin: 0 auto !important;
						position: relative !important;
						text-align: center !important;
						display: inline-table;
						float: none !important;
				}
				.dashboard.link {
					max-width:300px;
					vertical-align:top;
					font-size:17px;
					max-height:75px;
					min-height:20px;
				}
				.dashboard.link a {
					font-size: 17px;
					padding:3px;
					height:75px;
				}
				.dashboard.link a.support-btn {
					font-size: 17px;
					padding:3px;
					text-align:center;
					/*text-align: left;
					padding-left: 60px;*/
					height:75px;
				}
				.dashboard.link a.support-btn:hover {
					font-size: 17px;
					padding:3px;
					/*text-align: left;
					padding-left: 60px;*/
					height:75px;
				}
				.row {text-align:center;}

			</style>
			<?php
		} else if($tile_size == '4') {

		}
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT software_tile_menu_choice FROM contacts WHERE contactid='$contactidfortile'"));
        $tile_menu = $get_config['software_tile_menu_choice'];
		$contactid = $_SESSION['contactid'];
		$role = $_SESSION['role'];
		$therapistsid_booking = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT therapistsid FROM booking WHERE deleted=0 AND appoint_time IS NULL ORDER BY therapistsid LIMIT 1"));

		$therapistsid_checkin = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT therapistsid FROM booking WHERE (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = DATE(NOW()) AND appoint_time IS NULL ORDER BY therapistsid LIMIT 1"));

		if($tile_menu !== '2' && $tile_menu !== '3') {
			echo '<div class="row live-search-list"><div class="col-sm-4" style="font-size: 2em;">';
			if($_GET['dashboard_id'] != 'all') {
				$dashboard_name = '';
				foreach(array_filter(explode(',',ROLE)) as $temp_role_level) {
					$dashboard_result = mysqli_query($dbc, "SELECT td.`dashboard_id`, td.`tile_sort`, td.`name` FROM `tile_dashboards` td LEFT JOIN `contacts_tile_sort` cts ON cts.`default_dashboard`=td.`dashboard_id` WHERE (`dashboard_id`='".$_GET['dashboard_id']."' OR `contactid`='".$_SESSION['contactid']."' OR `default_levels` LIKE ',%".$temp_role_level."%,') AND (td.`assigned_users` IS NULL OR td.`assigned_users`='".$_SESSION['contactid']."') AND td.`deleted`=0 ORDER BY `dashboard_id`='".$_GET['dashboard_id']."' DESC, `contactid` DESC");
					if(mysqli_num_rows($dashboard_result)) {
						$dashboard = mysqli_fetch_array($dashboard_result);
						$dashboard_list = $dashboard['tile_sort'];
						$dashboard_name = $dashboard['name']." Dashboard";
						$_GET['dashboard_id'] = $dashboard['dashboard_id'];
					}
				}
				echo $dashboard_name;
			} else {

			}
			echo "</div>";
            // echo "<div class='col-sm-4'><center><input type='text' name='x' class='form-control live-search-box' placeholder='Search for a tile...' style='max-width:300px;'></center></div>";
			$show_dashboards = false;
			foreach(array_filter(explode(',',ROLE)) as $temp_role_level) {
				if(mysqli_num_rows(mysqli_query($dbc, "SELECT `tile_sort` FROM `tile_dashboards` WHERE CONCAT(',',`restrict_levels`,',') LIKE '%,$temp_role_level,%' AND `deleted`=0")) == 0) {
					$show_dashboards = true;
				}
			}
			if($show_dashboards) {
				$dashboards = mysqli_query($dbc, "SELECT `dashboard_id`, `name`, `tile_sort` FROM `tile_dashboards` WHERE `deleted`=0 AND (`assigned_users` IS NULL OR `assigned_users`='".$_SESSION['contactid']."')");
				if(mysqli_num_rows($dashboards) > 0) {
					echo '<div class="col-sm-8" style="text-align: right;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-Speedometer.png" class="no-toggle" title="Dashbaords" style="cursor: pointer; height: 2em;" title="Dashboards" class="dashboard_menu" onclick="$(\'#dashboard_menu\').toggle();">';
					echo "<div class='col-sm-8' id='dashboard_menu' style='display: none; margin: 0 1em; text-align: center;'>";
					echo "<select class='form-control' data-placeholder='Select a Dashboard' onchange='go_to_dashboard(this.value);'>";
					if(in_array_any(array_filter(explode(',',ROLE)),array_filter(explode(',',',super,'.get_config($dbc,'show_all_tiles_level'))))) {
						echo "<option ".($_GET['dashboard_id'] == 'all' ? 'selected' : '')." value='all'>Show All Tiles</option>";
					}
					while($db_row = mysqli_fetch_array($dashboards)) {
						$display_db = false;
						$db_tile_list = array_filter(explode('*#*',$db_row['tile_sort']));
						for($i = 0; $i < count($db_tile_list) && !$display_db; $i++) {
							if(tile_visible($dbc, $db_tile_list[$i])) {
								$display_db = true;
							}
						}
						if($display_db) {
							echo '<option '.($db_row['dashboard_id'] == $_GET['dashboard_id'] ? 'selected' : '').' value="'.$db_row['dashboard_id'].'">'.$db_row['name'].'</option>';
						}
					}
					echo "</select></div></div>";
				}
			}
			echo "<div class='clearfix'></div><br />";

			if(stripos(','.ROLE.',',',super,') !== false) {
				mysqli_query($dbc,"INSERT INTO `tile_security` (`tile_name`) SELECT 'software_config' FROM (SELECT COUNT(*) rows FROM `tile_security` WHERE `tile_name`='software_config') num WHERE num.rows=0");
				//if($get_config['total_id'] == 0) {
				//	$query_insert_customer = "INSERT INTO `admin_tile_config` (software_config) VALUES ('turn_on')";
				//	//$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
				//}
			}

			// Tiles have been moved to the tiles.php file to get a consistent order
			$item_start = '<div class="ffmbtnpic dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12">';
			$item_end = '</div>';
			include('tiles.php');
            $dashboard_list = '';
            $item_start = '<div class="ffmbtnpic searchOnly dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12" style="display:none;">';
            $item_end = '</div>';
            include('tiles.php');

			echo '</div>';
		} else {
			echo '<center><img src="img/logo.png" style="width:50%; margin-bottom:200px;"></center>';

		}
  	    ?>


</div>
<?php include ('footer.php'); ?>
