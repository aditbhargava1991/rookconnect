<?php // Security Reporting
$subtab = isset($_GET['sub']) ? $_GET['sub'] : 'activated';
switch($subtab) {
	case 'login':
		$subtab = 'login';
		$heading = 'Login History';
		break;
	case 'users':
		$subtab = 'users';
		$heading = 'Account User Reports Dashboard';
		break;
	default:
		$subtab = 'activated';
		$heading = 'Activated Security Levels & Groups Dashboard';
		break;
}

// Pagination settings
$pageNum = isset($_GET['page']) ? $_GET['page'] : 1;
$rowsPerPage = 25;
?>
<script>
$(document).ready(function() {
	$('h1.single-pad-bottom').text('<?php echo $heading; ?>');

	<?php if($subtab == 'users'): ?>
		$('.iframe_open').click(function(){
				var id = $(this).data('option');
				var title = $(this).parents('tr').children(':first').text();
			   $('#iframe_instead_of_window').attr('src', 'login_history.php?user_id='+id+'&title='+title);
			   $('.iframe_title').text('Login History');
			   $('.iframe_holder').show();
			   $('.hide_on_iframe').hide();
			   $('#iframe_instead_of_window').on('load', function() {
				   $(this).height($(this).get(0).contentWindow.document.body.scrollHeight);
			   });
		});
	<?php elseif($subtab == 'activated'): ?>
			$('.iframe_open').click(function(){
				var level = $(this).data('option');
				var title = $(this).parents('tr').children(':first').text();
			   $('#iframe_instead_of_window').attr('src', 'privileges_history.php?level='+level+'&title='+title);
			   $('.iframe_title').text('Security Privileges History');
			   $('.iframe_holder').show();
			   $('.hide_on_iframe').hide();
			   $('#iframe_instead_of_window').on('load', function() {
				   $(this).height($(this).get(0).contentWindow.document.body.scrollHeight);
			   });
		});
	<?php endif; ?>

	$('.close_iframe').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});

	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		this.style.height = (this.contentWindow.document.body.offsetHeight + 80) + 'px';
	});
});
</script>
<div class="tab-container mobile-100-container">
	<a href='security.php?tab=reporting&sub=activated'><button type='button' class='btn brand-btn mobile-block mobile-100 <?php echo ($subtab == 'activated' ? 'active_tab' : ''); ?>' >Activated Security Levels & Groups</button></a>
	<a href='security.php?tab=reporting&sub=users'><button type='button' class='btn brand-btn mobile-block mobile-100 <?php echo ($subtab == 'users' ? 'active_tab' : ''); ?>' >Account User Reports</button></a>
</div>
<div class='iframe_holder' style='display:none;'>
	<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframe' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
	<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
	<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
</div>
<div class="row hide_on_iframe">
	<?php if($subtab == 'activated'):
		$level_list = get_security_levels($dbc);
		$page_query = "SELECT ".count($level_list)." numrows";
		
        $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='security_reporting'"));
        $note = $notes['note'];
            
        if ( !empty($note) ) { ?>
            <div class="notice double-gap-bottom popover-examples">
                <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11">
                    <span class="notice-name">NOTE:</span>
                    <?= $note; ?>
                </div>
                <div class="clearfix"></div>
            </div><?php
        } ?>
        
		<center><input type='text' name='x' class='form-control live-search-box2' placeholder='Search for a level or group...' style='max-width:300px; margin-bottom:20px;'></center>
		<?php // Added Pagination //
		echo display_pagination($dbc, $page_query, $pageNum, $rowsPerPage);
		// Pagination Finish // ?>
		<table class='table table-bordered table-striped'>
			<thead>
                <tr class='hidden-xs hidden-sm'>
                    <th>Activated Security Levels & Groups</th>
                    <th># Accounts</th>
                    <th>Security Privileges</th>
                    <th>History</th>
                </tr>
            </thead>
			<?php $i = 0;
			foreach($level_list as $level_name => $security_level) {
				if($i > ($pageNum - 1) * $rowsPerPage && $i < $rowsPerPage * $pageNum) {
					$level_count = mysqli_fetch_array(mysqli_query($dbc,"SELECT COUNT(*) FROM `contacts` WHERE `role`='$security_level'"));
					$level_staff_result = mysqli_query($dbc,"SELECT `first_name`, `last_name` FROM `contacts` WHERE `role`='$security_level'");
					$level_priv_result = mysqli_query($dbc, "SELECT `tile`, `privileges` FROM `security_privileges` WHERE `level`='$security_level'");

					$level_staff = '';
					while($row = mysqli_fetch_array($level_staff_result)) {
						$level_staff .= decryptIt($row['first_name']) . ' ' . decryptIt($row['last_name']) . '<br />';
					}

					$level_priv = '';
					while($row = mysqli_fetch_array($level_priv_result)) {
						$tile_name = get_tile_names([$row['tile']])[0];
						if(strpos($row['privileges'],'*hide*') !== false) {
							$level_priv .= ($level_priv == '' ? '' : '<br />').$tile_name . " disabled.";
						}
						if(strpos($row['privileges'],'*view_use_add_edit_delete*') !== false) {
							$level_priv .= ($level_priv == '' ? '' : '<br />').$tile_name . " use activated.";
						}
						if(strpos($row['privileges'],'*configure*') !== false) {
							$level_priv .= ($level_priv == '' ? '' : '<br />').$tile_name . " settings enabled.";
						}
					}
					?>
					<tr>
						<td data-title="Activated Security Level"><?php echo $level_name; ?></td>
						<td data-title="# Accounts"><?php echo $level_count[0] . ' user(s)<br />' . $level_staff; ?></td>
						<td data-title="Privileges"><?php echo $level_priv; ?></td>
						<td data-title="History"><a><span data-option="<?php echo $level_list[$i]; ?>" class="iframe_open">View All</span></a></td>
					</tr>
				<?php }
				$i++;
			} ?>
		</table>
		<?php // Added Pagination //
		echo display_pagination($dbc, $page_query, $pageNum, $rowsPerPage);
		// Pagination Finish // ?>
	<?php elseif($subtab == 'users'):
		// Exclude George & FFM from showing up on SEA Contacts
		$query_clause = '';
		if ( get_software_name() == 'SEA' ) {
			$query_clause .= " AND (user_name!='FFMAdmin' AND user_name!='georgev' AND user_name!='salimc')";
		}
		$offset = ($pageNum - 1) * $rowsPerPage;
		$page_query = "SELECT COUNT(*) numrows FROM `contacts` WHERE `user_name` != '' $query_clause";
		
        $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='security_account_user_reports'"));
        $note = $notes['note'];
            
        if ( !empty($note) ) { ?>
            <div class="notice double-gap-bottom popover-examples">
                <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11">
                    <span class="notice-name">NOTE:</span>
                    <?= $note; ?>
                </div>
                <div class="clearfix"></div>
            </div><?php
        } ?>
        
		<center><input type='text' name='x' class='form-control live-search-box2' placeholder='Search for a user...' style='max-width:300px; margin-bottom:20px;'></center>
		<a href="?tab=reporting&sub=login&user_id=ALL" class="pull-right">View All Logins</a>
		<?php // Added Pagination //
		echo display_pagination($dbc, $page_query, $pageNum, $rowsPerPage);
		// Pagination Finish // ?>
		<div id="no-more-tables">
			<table class='table table-bordered table-striped'>
				<thead>
                    <tr class='hidden-xs hidden-sm'>
                        <th>User Accounts</th>
                        <th>Status</th>
                        <th>Security Level</th>
                        <th>Last Login</th>
                        <th>Login History</th>
                    </tr>
                </thead>
				<?php $user_sql = "SELECT `contacts`.`contactid`, `category`, `name`, `first_name`, `last_name`, `logins`.`last_login`, `role`, `status`, `deleted` FROM `contacts` LEFT JOIN (SELECT MAX(`login_at`) `last_login`, `contactid` FROM `login_history` GROUP BY `contactid`) `logins` ON `contacts`.`contactid`=`logins`.`contactid` WHERE `user_name` != '' $query_clause LIMIT $offset, $rowsPerPage";
				$user_list = sort_contacts_query(mysqli_query($dbc, $user_sql));
				foreach($user_list as $user) {
					$level_list = [];
					foreach(array_filter(explode(',',$user['role'])) as $role) {
						$level_list[] = get_securitylevel($dbc, $role);
					} ?>
					<tr>
						<td data-title="User"><?php echo $user['first_name'].' '.$user['last_name']; ?></td>
						<td data-title="Status"><?php echo ($user['deleted'] == 1 ? 'Archived' : ($user['status'] == 0 ? 'Suspended' : ($user['status'] == 2 ? 'On Probation' : 'Active'))); ?></td>
						<td data-title="Security Level"><?php echo implode(', ', $level_list); ?></td>
						<td data-title="Last Login"><?= $user['last_login'] != '' ? date('Y-m-d g:i a', strtotime($user['last_login'])) : 'N/A' ?></td>
						<td data-title="Login History"><a href="?tab=reporting&sub=login&user_id=<?= $user['contactid'] ?>">View All</a></td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<?php // Added Pagination //
		echo display_pagination($dbc, $page_query, $pageNum, $rowsPerPage);
		// Pagination Finish // ?>
	<?php elseif($subtab == 'login'):
		include('login_history.php');
	endif; ?>
</div>
