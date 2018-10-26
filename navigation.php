<?php if (strpos($_SERVER['REQUEST_URI'],'forgot_pwd.php') === false) {
	include_once('include.php');
}
$_SERVER['page_load_info'] .= 'Nav Bar Start: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n"; ?>
<?php if(!IFRAME_PAGE) :
	// Done defining the Company's software name.
	// Check for running tickets for the current user
	$ticket_mode = get_config($dbc, 'daysheet_ticket_default_mode');
	$ticket_action_mode = 0;
	if($ticket_mode == 'action') {
	    $ticket_action_mode = 1;
	}

	$active_ticket_buttons = '';

    $today_date = date('Y-m-d');
	$active_tasks = mysqli_query($dbc, "SELECT time_id, tasklistid FROM `tasklist_time` WHERE `start_time` IS NOT NULL AND end_time IS NULL AND src='A' AND contactid='{$_SESSION['contactid']}' AND timer_date='$today_date' ORDER BY time_id DESC LIMIT 1");

	$active_tickets = mysqli_query($dbc, "SELECT `tickets`.`ticketid`, `tickets`.`heading`, `tickets`.`businessid`, `tickets`.`clientid`, `tickets`.`contactid`, `tickets`.`ticket_type`, `tickets`.`to_do_date`, `tickets`.`status`, `tickets`.`projectid`, `tickets`.`main_ticketid`, `tickets`.`sub_ticket`, `ticket_label` FROM `tickets` LEFT JOIN `ticket_timer` ON `tickets`.`ticketid`=`ticket_timer`.`ticketid` AND `ticket_timer`.`deleted`=0 WHERE `tickets`.`deleted`=0 AND `tickets`.`status` != 'Archive' AND `ticket_timer`.`created_by`='{$_SESSION['contactid']}' AND `start_timer_time` > 0 GROUP BY `tickets`.`ticketid` UNION SELECT `tickets`.`ticketid`, `tickets`.`heading`, `tickets`.`businessid`, `tickets`.`clientid`, `tickets`.`contactid`, `tickets`.`ticket_type`, `tickets`.`to_do_date`, `tickets`.`status`, `tickets`.`projectid`, `tickets`.`main_ticketid`, `tickets`.`sub_ticket`, `tickets`.`ticket_label` FROM `tickets` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` WHERE `tickets`.`deleted`=0 AND `tickets`.`status` != 'Archive' AND `ticket_attached`.`arrived` > `ticket_attached`.`completed` AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`src_table` IN ('Staff','Staff_Tasks') AND `ticket_attached`.`item_id`='{$_SESSION['contactid']}' GROUP BY `tickets`.`ticketid`");
	if($active_tickets->num_rows > 0 && ACTIVE_TICKET_BUTTON != 'disable_active_ticket') {
		$ticket_tile_visible = tile_visible($dbc, 'ticket');
		$ticket_shown = [];
		while($active_ticket = mysqli_fetch_assoc($active_tickets)) {
			if(!in_array($active_ticket['ticketid'],$ticket_shown)) {
				$ticket_shown[] = $active_ticket['ticketid'];
				if(ACTIVE_TICKET_BUTTON == 'ticket_label') {
					$label = $active_ticket['ticket_label'];
				} else {
					$label = 'Running '.TICKET_NOUN.' #'.$active_ticket['ticketid'];
				}
				$active_ticket_buttons .= '<a class="btn brand-btn active-ticket" href="'.WEBSITE_URL.'/Ticket/index.php?'.($ticket_tile_visible ? '' : 'tile_name='.$active_ticket['ticket_type'].'&').'edit='.$active_ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&action_mode='.$ticket_action_mode.'">'.$label.'</a>';
			}
		}
		if(SHOW_SIGN_IN == '1' && strpos(get_privileges($dbc, 'start_day_button', ROLE),'*hide*') === FALSE) {
			$active_ticket_buttons .= '<a class="btn brand-btn active-ticket" href="'.WEBSITE_URL.'/Timesheet/start_day.php">'.END_DAY.'</a>';
		}
	} else if($active_tasks->num_rows > 0 && ACTIVE_TICKET_BUTTON != 'disable_active_ticket') {
		$task_shown = [];
		while($active_task = mysqli_fetch_assoc($active_tasks)) {
			if(!in_array($active_task['tasklistid'],$task_shown)) {
				$task_shown[] = $active_task['tasklistid'];
                $task_label = get_tasklist_tabledata($dbc, $active_task['tasklistid'], 'heading');
				if(ACTIVE_TICKET_BUTTON == 'ticket_label') {
					$label = $task_label;
				} else {
					$label = 'Running '.TASK_NOUN.' #'.$active_task['tasklistid'];
				}
				$active_ticket_buttons .= '<a class="btn brand-btn active-ticket" href="'.WEBSITE_URL.'/Tasks_Updated/add_task_full_view.php?tasklistid='.$active_task['tasklistid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&action_mode='.$ticket_action_mode.'">'.$label.'</a>';
			}
		}
    } else if((SHOW_SIGN_IN == '1' || ACTIVE_DAY_BANNER != '') && strpos(get_privileges($dbc, 'start_day_button', ROLE),'*hide*') === FALSE) {
		$timer_running = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `timer_start` FROM `time_cards` WHERE `type_of_time` IN ('day_tracking','day_break') AND `timer_start` > 0 AND `staff`='".$_SESSION['contactid']."'"))['timer_start'];
		if(ACTIVE_DAY_BANNER != '' && $timer_running > 0) {
			$active_ticket_buttons .= '<a class="btn brand-btn active-ticket" href="'.WEBSITE_URL.'/Timesheet/start_day.php">'.\ACTIVE_DAY_BANNER.'</a>';
		} else if(SHOW_SIGN_IN == '1' && $timer_running > 0) {
			$active_ticket_buttons .= '<a class="btn brand-btn active-ticket" href="'.WEBSITE_URL.'/Timesheet/start_day.php">'.END_DAY.'</a>';
		} else if(SHOW_SIGN_IN == '1') {
			$active_ticket_buttons .= '<a class="btn brand-btn active-ticket" href="'.WEBSITE_URL.'/Timesheet/start_day.php">'.START_DAY.'</a>';
		}
	}
	$_SERVER['page_load_time'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
	$_SERVER['page_load_info'] .= 'Ticket Banners Loaded: '.number_format($_SERVER['page_load_time'],5)."\n";
endif; ?>
<script>
$(document).ready(function() {
	// Modify ENTER key for all forms to move focus to the next input
	$('form').find('input,select').not('[name^=search_],.no_tab').keypress(function(e) {
		if(e.which == 13) {
			$(this).blur();
			var next = $(this).nextAll('input,select,textarea');
			if(next.length > 0) {
				next.first().focus();
				return false;
			}
			var next = $(this).parents('.form-group').nextAll().find('input,select,textarea').filter(':visible');
			if(next.length == 0) {
				var nextPanel = $(this).parents('.panel').next('.panel');
				if(nextPanel.length == 0) {
					next = $('[type=submit]');
				}
				else {
					nextPanel.find('.panel-title a').click().focus();
					next = nextPanel.find('input,select,textarea').filter(':visible');
				}
			}
			next.first().focus();
			return false;
		}
	});

	// Turn info i off and on
	var toggleState = $('#info_toggle_state').val();
	if(toggleState == 1) {
		$('.switch_info_on').show();
		$('.switch_info_off').hide();
		$('#info_toggle_state').val(0);
	} else {
		$('.switch_info_off').show();
		$('.switch_info_on').hide();
		$('#info_toggle_state').val(1);
	}

    var fullscreen = $('#fullscreen').val();
    if ( fullscreen==1 ) {
        $('#main-header, #nav, #footer').hide();
        $('.pullup').addClass('rotate');
        $('.hide-header-footer-down').show();
        $('.main-screen').addClass('double-pad-top');
        $(window).resize();
    } else {
        $('#main-header, #nav, #footer').show();
        $('.pullup').removeClass('rotate');
        $('.hide-header-footer-down').hide();
        $('.main-screen').removeClass('double-pad-top');
    }

    if ( $(window).width() < 768 ) {
        var runningTicket = $('.active-ticket');
        var container = $('.container').offset().top + 20;

        $(window).scroll(function(){
            if ( $(window).scrollTop()>container && runningTicket.is(':visible') ){
                runningTicket.stop().hide();
            }
            else if ( $(window).scrollTop()<container && runningTicket.is(':hidden') ){
                runningTicket.stop().show();
            }
        });
    }
    
    $('.nav_more_options').click(function() {
        $('.nav_icons_mobile').toggle();
    });
    $(window).scroll(function() {
        $('.nav_icons_mobile').hide();
    });
});

var software_search_shifted = false;
var delay_results_load = false;
var search_categories = [];
var current_search_categories = [];
var current_category = '';
var search_ajax;
var search_key = '';
var current_key = '';
var i = 0;
var completed = 0;
var completed_searches = 0;
function software_search() {
	$('.navbar-nav.navbar-right,.navbar-nav.scale-to-fill>li>a,.navbar-nav.scale-to-fill>li>p').addClass('hidden');
	$('.software-search-results').show();
	$('.software_search').off('change').off('keyup').off('keydown').keydown(function(e) {
		if(e.which == 16) {
			software_search_shifted = true;
		} else if(e.which == 13) {
			var link = $('.software-search-results .active.blue').prop('href');
			if(link == undefined) {
				$(this).blur();
			} else if(link != '') {
				window.location.href = link;
			}
		} else if(e.which == 38 || (e.which == 9 && software_search_shifted)) {
			var link = $('.software-search-results .active.blue').prevAll('a');
			if(link.length > 0) {
				$('.software-search-results .active.blue').removeClass('active blue');
				link.first().addClass('active blue');
			}
			return false;
		} else if(e.which == 40 || e.which == 9) {
			var link = $('.software-search-results .active.blue').nextAll('a');
			if(link.length > 0) {
				$('.software-search-results .active.blue').removeClass('active blue');
				link.first().addClass('active blue');
			}
			return false;
		}
	}).keyup(function(e) {
		if(e.which == 16) {
			software_search_shifted = false;
			return false;
		} else if(e.which == 27) {
			$(this).blur();
		} else if(e.which != 38 && e.which != 40 && e.which != 9) {
			// search_ajax.abort();
			search_key = this.value;
			if(this.value != '' && search_key != current_key) {
				clearTimeout(delay_results_load);
				$('.software-search-results').html('<em class="search_note"><img class="inline-img" src="<?= WEBSITE_URL ?>/img/status_working.gif">Loading results for '+search_key+'...</em>');
				current_category = '';
				delay_results_load = setTimeout(loadSearchBarResults, 250);
			} else if(this.value == '') {
				$('.software-search-results').html('<em>Type to display results.</em>');
			}
		}
	});
}
function software_search_end() {
	setTimeout(function() {
		if($('.software-search-results *:hover').length > 0) {
			software_search_end();

		} else {
			$('.software-search-results').fadeOut(250, function() {
				$('.navbar-nav.navbar-right,.navbar-nav.scale-to-fill>li>a,.navbar-nav.scale-to-fill>li>p').removeClass('hidden');
			});
            $('input.software_search').addClass('hide-titles-mob');
            $('img.software_search').removeClass('hide-titles-mob').addClass('show-on-mob');
		}
	}, 250);
}
function loadSearchBarResults() {
	$('.software-search-results').find('span.no_results').remove();
	$('.software-search-results').find('.search_note').remove();
	if(current_category == '') {
		current_search_categories = search_categories.slice(0);
	}
	var category = current_search_categories.shift();
	if(search_key+category != current_key+current_category && category != undefined) {
		$('.software-search-results').append('<em class="search_note"><img class="inline-img" src="<?= WEBSITE_URL ?>/img/status_working.gif">Loading results for '+search_key+'...</em>');
		current_key = search_key;
		current_category = category;
		var local_key = search_key;
		var current_search = ++completed_searches;
		search_ajax = $.ajax({
			url: '<?= WEBSITE_URL ?>/search.php?search_query='+search_key.toLowerCase()+'&category='+category+'&rows='+(11 - $('.software-search-results').find('a').length)+'&return=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>',
			success: function(response) {
				if(local_key == search_key && current_search == completed_searches) {
					$('.software-search-results').find('.display_all').remove();
					$('.software-search-results').append(response);
					$('.software-search-results a').mouseover(function() {
						$('.software-search-results .active.blue').removeClass('active blue');
						$(this).addClass('active blue');
					});
					$('.software-search-results a').first().addClass('active blue');
					loadSearchBarResults();
				}
			}
		});
	}
}
function checkIsMobile() {
    var width = screen.width;
    var availWidth = screen.availWidth;
    alert(width+'|'+availWidth);
}
</script>

<?php // Set the status of information i toggles
$info_toggle_state = @$_SESSION['info_toggle'];
if(!isset($_SESSION['info_toggle'])) {
	$info_toggle_state = 1;
}

$fullscreen = $_SESSION['fullscreen'];
if(!isset($_SESSION['fullscreen'])) {
	$fullscreen = 0;
} ?>

<!-- Static navbar -->
<?php if(!IFRAME_PAGE) : ?>
    <div>
        <input type="hidden" id="info_toggle_state" name="info_toggle_state" value="<?= $info_toggle_state ?>">
        <input type="hidden" id="fullscreen" name="fullscreen" value="<?= $fullscreen ?>">
        <div id="nav" class="navbar navbar-default navbar-static-top brand-nav" role="navigation">
            <div class="container no-pad-mobile">
            <?php include('tile_menu.php'); ?>
                <div class="navbar-collapse">
                    <?php if($_SESSION['contactid'] > 0) { ?>
                        <ul class="nav navbar-nav navbar-right pad-right pull-right">
                            <?php //include('Navigation/social_media_links.php'); ?>
                            <li class="nav-active-ticket"><?= $active_ticket_buttons ?></li><?php
                            $contact_category = $_SESSION['category'];
                            /*
                            if(tile_enabled($dbc, 'contacts_rolodex')) {
                                $contacts_folder = 'ContactsRolodex';
                            } else {
                                $contacts_folder = 'Contacts';
                            }
                            */
                            $contacts_folder = 'Contacts';
                            if(strtolower($contact_category) != 'staff') {
                                $profile_access = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_security` WHERE `category` = '$contact_category' AND `security_level` = '".ROLE."'"))['profile_access'];
                                if($profile_access == 'disable') {
                                    $profile_html = profile_id($dbc, $_SESSION['contactid'], false);
                                } else {
                                    $profile_html = '<a href="'.WEBSITE_URL.'/'.$contacts_folder.'/contacts_inbox.php?edit='.$_SESSION['contactid'].'" title="My Profile">'.profile_id($dbc, $_SESSION['contactid'], false).'</a>';
                                }
                            } else {
                                $profile_html = '<a href="'.WEBSITE_URL.'/Profile/my_profile.php" title="My Profile">'.profile_id($dbc, $_SESSION['contactid'], false).'</a>';
                            }
                            echo '<li>'.$profile_html .'</li>'; ?>
                            <li class="hide-header-footer">
                                <div class="pullup"><img src="<?= WEBSITE_URL;?>/img/pullup.png" alt="" class="no-toggle" title="Hide Header &amp; Footer" data-placement="bottom" /></div>
                            </li>
                            <li><a class="logout-button" href="<?= WEBSITE_URL; ?>/logout.php"><img src="<?= WEBSITE_URL; ?>/img/logout-icon.png" class="no-toggle" title="Logout" data-placement="bottom" /></a></li>
                            <li class="show-on-mob"><img title="More Options" data-placement="bottom" src="<?= WEBSITE_URL; ?>/img/icons/ROOK-3dot-icon.png" width="30p" class="nav_more_options offset-top-13 offset-right-15 no-toggle cursor-hand" /></li>
                        </ul>
                    <?php } ?>
                    <ul class="nav navbar-nav scale-to-fill" style="overflow: hidden;">
                        <?php if (strpos($site_url,'forgot_pwd.php') == false) { ?>
                            <li class="pull-left home-button">
                                <a style="padding-left: 17px;" href="<?php echo WEBSITE_URL;?>/home.php" title="Home"><?php
                                    $logo_upload = get_config($dbc, 'logo_upload');
                                    $logo_upload_icon = get_config($dbc, 'logo_upload_icon');
                                    if($logo_upload_icon == '') {
                                        if($logo_upload == '') {
                                            echo '<img src="'.WEBSITE_URL.'/img/logo.png" height="30" alt="Home" class="no-toggle" title="Home" data-placement="bottom" />';
                                        } else {
                                            echo '<img src="'.WEBSITE_URL.'/Settings/download/'.$logo_upload.'" height="30" alt="Home" class="no-toggle" title="Home" data-placement="bottom" />';
                                        }
                                    } else {
                                        echo '<img src="'.WEBSITE_URL.'/Settings/download/'.$logo_upload_icon.'" height="30" alt="Home" class="no-toggle" title="Home" data-placement="bottom" />';
                                    } ?>
                                </a>
                                <img title="" src="" class="status_loader statusIcon no-margin">
                            </li>
                            <?php if ( isset($_SESSION[ 'newsboard_menu_choice' ]) && $_SESSION[ 'newsboard_menu_choice' ] != NULL ) { ?>
                                <li class="pull-left"><?php include('Notification/newsboard.php'); ?></li>
                            <?php } ?>
                            <?php if(tile_visible($dbc, 'calendar_rook')): ?>
                                <li class="pull-left"><a href="<?php echo WEBSITE_URL;?>/Calendar/calendars.php" title="Calendar" class="calendar-button"><img src="<?= WEBSITE_URL ?>/img/month-overview-blue.png" class="white-color no-toggle" title="Calendar" data-placement="bottom"></a></li>
                            <?php endif; ?>
                            <?php if($_SESSION['contactid'] > 0) { ?>
                                <li class="pull-left"><?php include('Notification/alert_software.php'); ?></li>
                                <li class="pull-left"><p class="no-pad-right no-pad-horiz-mobile offset-right-5"><a id="info_toggle" class="no-toggle" title="Info i Toggle" data-placement="bottom"><img src="<?php echo WEBSITE_URL; ?>/img/icons/switch-off.png" style='display:none; position: relative; top: 5px;' width="50px" class="switch_info_off"><img src="<?php echo WEBSITE_URL; ?>/img/icons/switch-on.png" class="switch_info_on" style='display:none; position: relative; top: 5px;'  width="50px"></a></p></li>
                                <li class="scale-to-fill">
                                    <script>
                                    <?php // Get Search Categories
                                    $search_cats = ['Tiles'];
                                    echo "search_categories.push('tiles');\n";
                                    if(in_array('contacts_inbox',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = CONTACTS_TILE;
                                        echo "search_categories.push('contacts');\n";
                                    } else if(in_array('contacts',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = 'Contacts';
                                        echo "search_categories.push('contacts');\n";
                                    } else if(in_array('contacts3',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = 'Contacts';
                                        echo "search_categories.push('contacts3');\n";
                                    } else if(in_array('contacts_rolodex',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = 'Contacts';
                                        echo "search_categories.push('contactsrolodex');\n";
                                    }
                                    if(in_array('members',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = 'Members';
                                        echo "search_categories.push('members');\n";
                                    }
                                    if(in_array('client_info',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = 'Clients';
                                        echo "search_categories.push('clients');\n";
                                    }
                                    if(in_array('staff',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = 'Staff';
                                        echo "search_categories.push('staff');\n";
                                    }
                                    if(in_array('project',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = PROJECT_TILE;
                                        echo "search_categories.push('projects');\n";
                                    }
                                    if(in_array('ticket',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = TICKET_TILE;
                                        echo "search_categories.push('tickets');\n";
                                    }
                                    if(in_array('checklist',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = 'Checklists';
                                        echo "search_categories.push('checklists');\n";
                                    }

                                    if(in_array('tasks',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = 'Tasks';
                                        echo "search_categories.push('tasks');\n";
                                    }

                                    if(in_array('demo',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = 'Demo';
                                        echo "search_categories.push('demo');\n";
                                    }

                                    /*if(in_array('tasks_updated',array_column($_SESSION['tile_list'],'tile'))) {
                                        $search_cats[] = 'Tasks (Updated)';
                                        echo "search_categories.push('tasks_updated');\n";
                                    } */
                                    ?>
                                    </script>
                                    <img class="software_search cursor-hand white-color show-on-mob" src="<?= WEBSITE_URL ?>/img/Magnifying_glass_icon.png" height="20" alt="Search All <?= implode(', ',$search_cats) ?>" tabindex="1" onclick="software_search(); $('input.search-text').removeClass('hide-titles-mob'); $('input.search-text').focus(); $(this).removeClass('show-on-mob').addClass('hide-titles-mob');" />
                                    <input type="text" class="hide-titles-mob form-control software_search search-text pad-top-5" placeholder="Search All <?= implode(', ',$search_cats) ?>" onfocus="software_search();" onblur="software_search_end();">
                                    <div class="main-screen-white software-search-results" style="display:none;"><em>Type to display results.</em></div>
                                </li>
                            <?php } ?>
                        <?php } else { ?>
                            <li><a href="<?php echo WEBSITE_URL;?>/index.php">Login</a></li>
                        <?php } ?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div><!--/.container-fluid -->
        </div>
        
        <?php
            /*
             * Show on mobile when the user click the 3 dots icons
             * Duplicate the nav li items
             */
        ?>
        <div class="nav_icons_mobile">
            <div class="pull-left"><?php
                if ( isset($_SESSION[ 'newsboard_menu_choice' ]) && $_SESSION[ 'newsboard_menu_choice' ] != NULL ) { ?>
                    <div class="pull-left"><?php include('Notification/newsboard.php'); ?></div><?php
                }
                if ( tile_visible($dbc, 'calendar_rook') ) { ?>
                    <div class="pull-left"><a href="<?php echo WEBSITE_URL;?>/Calendar/calendars.php" title="Calendar" class="calendar-button"><img src="<?= WEBSITE_URL ?>/img/month-overview-blue.png" class="inline-img white-color no-toggle" title="Calendar" data-placement="bottom"></a></div><?php
                }
                if ( $_SESSION['contactid'] > 0 ) { ?>
                    <div class="pull-left"><?php include('Notification/alert_software.php'); ?></div>
                    <div class="pull-left"><p class="no-pad-right no-pad-horiz-mobile offset-right-5"><a id="info_toggle" title="Info i Toggle"><img src="<?php echo WEBSITE_URL; ?>/img/icons/switch-off.png" style='display:none; position: relative; top: 5px;' width="50px" class="switch_info_off"><img src="<?php echo WEBSITE_URL; ?>/img/icons/switch-on.png" class="switch_info_on"  style='display:none; position: relative; top: 5px;'  width="50px"></a></p></div><?php
                } ?>
            </div>
            <div class="pull-right"><?php
                $contact_category = $_SESSION['category'];
                $contacts_folder = 'Contacts';
                if ( strtolower($contact_category) != 'staff' ) {
                    $profile_access = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_security` WHERE `category` = '$contact_category' AND `security_level` = '".ROLE."'"))['profile_access'];
                    if($profile_access == 'disable') {
                        $profile_html = profile_id($dbc, $_SESSION['contactid'], false);
                    } else {
                        $profile_html = '<a href="'.WEBSITE_URL.'/'.$contacts_folder.'/contacts_inbox.php?edit='.$_SESSION['contactid'].'" title="My Profile">'.profile_id($dbc, $_SESSION['contactid'], false).'</a>';
                    }
                } else {
                    $profile_html = '<a href="'.WEBSITE_URL.'/Profile/my_profile.php" title="My Profile">'.profile_id($dbc, $_SESSION['contactid'], false).'</a>';
                }
                echo '<div class="pull-left">'.$profile_html .'</div>'; ?>
                <div class="pull-left"><a href="<?= WEBSITE_URL; ?>/logout.php"><img src="<?= WEBSITE_URL; ?>/img/logout-icon.png" class="offset-top-15 no-toggle offset-right-15" title="Logout" data-placement="bottom" /></a></div>
            </div>
            <div class="clearfix"></div>
        </div><!-- .nav_icons_mobile -->

        <div class="hide-header-footer-down">
            <div class="pullup down"><img src="<?= WEBSITE_URL;?>/img/pullup.png" alt="" /></div>
        </div>
        <?php include('sticky_tile_menu.php'); ?>
    </div>
<?php endif; ?>
<?php
$_SERVER['page_load_time'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
$_SERVER['page_load_info'] .= 'Nav Bar Loaded: '.number_format($_SERVER['page_load_time'],5)."\n"; ?>
<?php if($_SESSION['password_update'] > 0) {
	include_once('password_reset.php');
	exit();
} ?>
