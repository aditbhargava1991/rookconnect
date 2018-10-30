<?php // Estimates View
error_reporting(0);
include_once('../include.php');
$rookconnect = get_software_name(); ?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.double-scroller').each(function() {
			$(this).find('div').width($('.has-dashboard').get(0).scrollWidth);
			$(this).off('scroll',doubleScroll).scroll(doubleScroll);
			$('.has-dashboard').off('scroll',setDoubleScroll).scroll(setDoubleScroll);
		});
		var available = 0;
        try {
            available = Math.floor($(window).innerHeight() - $('.main-screen .main-screen').offset().top - $('footer:visible').outerHeight());
        } catch(err) { }
		if(available > 300) {
			$('.tile-sidebar, .main-screen .main-screen, .preview-bar').each(function() {
				$(this).outerHeight(available);
			});
			$('.main-screen .has-dashboard').each(function() {
				$(this).outerHeight($(window).innerHeight() - $('.main-screen .has-dashboard').offset().top - $('footer:visible').outerHeight());
				var height = this.clientHeight;
				$(this).find('div.dashboard-list').each(function() {
					var offset = $(this).find('.info-block-header').outerHeight();
					$(this).find('ul').height('calc('+(height - offset)+'px - 1em)');
				});
			});
			$('body').css('overflow-y','hidden');
		}
	}).resize();

    $("input[name='estimate_name']").on("input", function() {
      var dInput = this.value;
      $('#estimate_name_fill').text(dInput);
    });
});

$(document).on('change', 'select.dashboard_select_onchange', function() { window.location.replace('?status=<?= $_GET['status'] ?>&dashboard='+this.value); });
var current_fields = [];
function syncUnsaved(name) {
	if(typeof(name) != 'string') {
		name = this.name;
	}
	name = name.replace('[]','');
	if(name.includes('prior_')) {
		return;
	}
	current_fields.push(name);
	syncIcon();
	if(this != window && this.value == this.defaultValue) {
		syncDone(name);
	}
}
function syncSaving() {
	$('.syncIcon').prop('src','../img/status_working.gif').prop('title','Saving Changes...').tooltip('destroy');
	initTooltips();
}
function syncDone(name) {
	name = name.replace('[]','');
	for(var i = current_fields.length - 1; i >= 0; i--) {
		if(current_fields[i] == name || current_fields[i] == '') {
			current_fields.splice(i, 1);
		}
	}
	syncIcon();
}
function syncIcon() {
	setTimeout(function() {
		if(current_fields.length > 0) {
			$('.syncIcon').prop('src','../img/status_incomplete.png').prop('title','Your page has unsaved changes...');
		} else {
			$('.syncIcon').prop('src','../img/status_complete.png').prop('title','All Changes Saved!');
		}
		initTooltips();
	}, 500);
}
</script>
</head>
<body>
<?php
checkAuthorised('estimate');
$edit_access = vuaed_visible_function($dbc, 'estimate');
$config_access = config_visible_function($dbc, 'estimate');
include_once ('../navigation.php');
$summary_view = explode(',',get_config($dbc, 'estimate_summary_view'));
$reminderIcon = false;
if( (isset($_GET['edit']) && $_GET['edit']!='') || (isset($_GET['view']) && $_GET['view']>0) ){
	$reminderIcon = true;
}
if(array_filter($summary_view) && (!isset($_GET['view']) || $_GET['view'] == 'summary') && !isset($_GET['status'])) {
	$_GET['status'] = 'summary_view';
	$_GET['view'] = 'summary';
} ?>
<div id="estimates_main" class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe src=""></iframe>
		</div>
	</div>
	<div class="row">
		<div class="main-screen">
            <div class="tile-header col-sm-12 icons_div" data-id="<?= $_GET['edit'] ?>">
				<h1><a href="?"><?= ESTIMATE_TILE ?></a><?php if(!empty($_GET['edit'])) {
						echo ': '.ESTIMATE_TILE.' #'.$_GET['edit'].' - <span id="estimate_name_fill">'.get_field_value('estimate_name','estimate','estimateid',$_GET['edit']).'</span>';
					}
					if($config_access > 0) {
						echo "<div class='pull-right gap-left'><a href='?settings=status'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30'></a></div>";
						echo "<div class='pull-right gap-left hide-titles-mob'><a href='?reports=statistics' style='font-size: 0.5em;'><button class='btn brand-btn icon-pie-chart'>Reporting</button></a></div>";
						echo "<div class='pull-right gap-left'><a href='?template=list' style='font-size: 0.5em;'><button class='btn brand-btn'>Templates</button></a></div>";
					}
                    if($edit_access > 0) {
                        echo "<div class='pull-right gap-left'><a href='?edit=new' style='font-size: 0.5em;'><button class='btn brand-btn hide-titles-mob'>New ".rtrim(ESTIMATE_TILE, 's')."</button>";
                        echo "<img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='show-on-mob' height='30'></a></div>";
                    }
                    if($reminderIcon==true){
						echo "<div class='pull-right'><a href='Add Reminder' onclick='return false;'><img src='".WEBSITE_URL."/img/icons/ROOK-reminder-icon.png' class='no-toggle reminder-icon' title='Schedule Reminder' style='width:1.5em;'></a></div>";
                    }
                    if(!isset($_GET['edit']) && !isset($_GET['view'])) { ?>
						<img class="inline-img pull-right btn-horizontal-collapse hide-titles-mob small no-toggle" src="../img/icons/pie-chart.png" title="View Summary">
                        <div class="pull-right top-dashboard"><img src="../img/icons/ROOK-Speedometer.png" class="cursor-hand" height="30" onclick="$('.dashboard_select').toggle();" /></div>
                        <div class="col-sm-3 pull-right small dashboard_select" style="display: none;">
                            <select class="chosen-select-deselect dashboard_select_onchange">
                                <option value="<?= $_SESSION['contactid'] ?>">My Dashboard</option>
                                <?php if($config_access > 0) {
                                    echo '<option '.($_GET['dashboard'] == 'company_dashboard' ? 'selected' : '').' value="company_dashboard">Company Dashboard</option>';
                                    foreach(sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC)) as $contactid) {
                                        if($contactid != $_SESSION['contactid']) {
                                            echo '<option '.($_GET['dashboard'] == $contactid ? 'selected' : '').' value="'.$contactid.'">'.get_contact($dbc, $contactid).'\'s Dashboard</option>';
                                        }
                                    }
                                } ?>
                            </select>
                        </div>
                    <?php } ?>
				</h1>
				<div class="clearfix"></div>
            </div>

			<?php if(($_GET['edit'] > 0 || $_GET['edit'] == 'new') && $edit_access > 0) {
				include('estimates_edit.php');
			} else if($_GET['settings'] == 'pdf_setting' || $_GET['settings'] == 'header' || $_GET['settings'] == 'footer' || !empty($_GET['style_settings'])) {
				include('field_config_style.php');
			} else if($_GET['view'] > 0) {
				include('estimates_overview.php');
			} else if(!empty($_GET['settings']) && $config_access > 0) {
				include('field_config.php');
			} else if(!empty($_GET['template']) && $config_access > 0) {
				include('template_list.php');
			} else if($_GET['reports']) {
				include('estimates_reporting.php');
			} else if($_GET['financials'] > 0) {
				include('estimate_financials.php');
			} else {
				include('estimates_main.php');
			} ?>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<script>
$(document).ready(function() {
	$('.icons_div .reminder-icon').off('click').click(function() {
        var item = $(this).closest('.icons_div');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=estimates&id='+item.data('id'), 'auto', false, true);
    });
})
</script>
<?php include('../footer.php'); ?>