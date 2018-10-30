<?php // Projects View
include_once('../include.php');
include_once('../Ticket/field_list.php'); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php if(!IFRAME_PAGE) { ?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').not('.show-on-mob .main-screen').is(':visible')) {
			<?php if(isset($_GET['edit']) && $ticket_layout == 'Accordions') { ?>
				var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.standard-body').offset().top;
			<?php } else { ?>
				var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
			<?php } ?>
			if(available_height > 200) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				/* Sticky header
                $('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','hidden');
				$('.main-screen .main-screen .standard-body-content').outerHeight(available_height - $('.standard-body-title').height()).css('overflow-y','auto');
                */
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
			}
		}
	}).resize();
});
</script>
<?php } ?>
<?php if(isset($_GET['intake_key']) && !isset($_SESSION['contactid'])) { ?>
	<style>
	body>.container {
		min-height: calc(100vh - 124px + 75px);
	}
	</style>
<?php } ?>
</head>
<body>
<?php
if(empty($_GET['intake_key']) || !empty($_SESSION['contactid'])) {
	include_once ('../navigation.php');
}
include_once('config.php'); ?>
<script type="text/javascript">
function blankPDFForm() {
	$('#dialog-blank-pdf').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 800 ? $(window).width() : 800),
		modal: true,
		buttons: {
			"No <?= TICKET_NOUN ?> Tab": function() {
				window.open('<?= WEBSITE_URL ?>/Ticket/ticket_pdf.php?ticketid=&ticket_type=', '_blank');
				$(this).dialog('close');
			},
			<?php foreach($ticket_tabs as $type_key => $type_label) { ?>
				"<?= $type_label ?>": function() {
					window.open('<?= WEBSITE_URL ?>/Ticket/ticket_pdf.php?ticketid=&ticket_type=<?= $type_key ?>', '_blank');
					$(this).dialog('close');
				},
			<?php } ?>
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
}
</script>
<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="ticket_iframe" src=""></iframe>
		</div>
	</div>
	<div id="dialog-blank-pdf" title="Select <?= TICKET_NOUN ?> Tab" style="display: none;">
		Please choose a <?= TICKET_NOUN ?> Tab for your Blank PDF Form.
	</div>
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header" style="<?= IFRAME_PAGE ? 'display:none;' : '' ?>">
                <div class="pull-right settings-block"><?php
                	if(!isset($_GET['intake_key'])) {
	                    if($security['config'] > 0) {
	                        echo "<div class='pull-right gap-left'><a href='?".$current_tile."settings=fields'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>";
	                    }
						if(in_array('PDF',$db_config)) {
							echo '<a href="../Ticket/ticket_pdf.php?'.$current_tile.'ticketid=&ticket_type='.$ticket_type.'" onclick="blankPDFForm(); return false;" class="btn brand-btn pull-right hide-titles-mob">Blank '.$ticket_noun.' Form <img src="../img/pdf.png" class="inline-img smaller"></a>';
							if($_GET['edit'] > 0) {
								echo '<a href="../Ticket/ticket_pdf.php?'.$current_tile.'ticketid='.$_GET['edit'].'&ticket_type='.$ticket_type.'" target="_blank" class="btn brand-btn pull-right hide-titles-mob">Print Current '.$ticket_noun.' <img src="../img/pdf.png" class="inline-img smaller"></a>';
							}
						}
						if(in_array('Export Ticket Log',$db_config)) {
							$ticket_log_template = !empty(get_config($dbc, 'ticket_log_template')) ? get_config($dbc, 'ticket_log_template') : 'template_a';
							echo '<a href="../Ticket/ticket_log_templates/'.$ticket_log_template.'_pdf.php?'.$current_tile.'ticketid=" target="_blank" class="btn brand-btn pull-right hide-titles-mob">Blank '.$ticket_noun.' Log <img src="../img/pdf.png" class="inline-img smaller"></a>';
						}
	                    if($security['edit'] > 0) {
							echo "<div class='pull-right gap-left'><a href='?".$current_tile."edit=0&type=".$ticket_type."' class='new-btn'><button class='btn brand-btn hide-titles-mob'>New ".$ticket_noun."</button>";
							echo "<img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='show-on-mob' style='height: 2.5em;'></a></div>";
	                    }
                    } ?>
                </div>
                <div class="scale-to-fill" <?= (!empty($_GET['intake_key']) && empty($_SESSION['contactid']) ? 'style="padding-top: 20px;"' : '') ?>><?php
					if(isset($_GET['intake_key']) && !($_SESSION['contactid'] > 0)) { ?>
                        <div class="pull-left" style="margin: 1em;">
                            <?php
                            $logo_upload = get_config($dbc, 'logo_upload');
                            $logo_upload_icon = get_config($dbc, 'logo_upload_icon');
                            if($logo_upload_icon == '') {
                                if($logo_upload == '') {
                                    echo '<img src="'.WEBSITE_URL.'/img/logo.png" height="30" alt="'.get_config($dbc, 'company_name').'" class="no-toggle" title="'.get_config($dbc, 'company_name').'" data-placement="bottom" style="margin:0; border: 1px solid #ddd; border-radius: 50%;" />';
                                } else {
                                    echo '<img src="'.WEBSITE_URL.'/Settings/download/'.$logo_upload.'" height="30" alt="'.get_config($dbc, 'company_name').'" class="no-toggle" title="'.get_config($dbc, 'company_name').'" data-placement="bottom" style="margin:0; border: 1px solid #ddd; border-radius: 50%;" />';
                                }
                            } else {
                                echo '<img src="'.WEBSITE_URL.'/Settings/download/'.$logo_upload_icon.'" height="30" alt="'.get_config($dbc, 'company_name').'" class="no-toggle" title="'.get_config($dbc, 'company_name').'" data-placement="bottom" style="margin:0; border: 1px solid #ddd; border-radius: 50%;" />';
                            } ?>
                        </div>
                	<?php } ?>
					<h1 class="gap-left"><a href="?<?= $current_tile ?>"><?= $ticket_tile ?></a><?= isset($_GET['edit']) ? ($ticketid > 0 && $_GET['new_ticket'] != 'true' ? ': <span class="ticketid_span">'.get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"))).'</span>' : ': <span class="ticketid_span">New '.TICKET_NOUN.'</span>') : '' ?>
						<!-- <img class="no-toggle statusIcon pull-right no-margin inline-img small" title="" src="" data-original-title=""> --></h1>
				</div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

			<div class="clearfix"></div>
			<?php if(isset($_GET['edit'])) {
				echo '<input type="hidden" name="global_ticket_noun" value="'.$ticket_noun.'">';
				include('edit_tickets.php');
				if(empty($ticketid) && $calendar_ticket_slider == 'accordion') {
					$include_hidden = 'true'; ?>
					<div style="display:none;"><?php include('edit_tickets.php'); ?></div>
				<?php }
			} else if(!empty($_GET['settings']) && $security['config'] > 0) {
				include('field_config.php');
			} else if(!empty($_GET['custom_form'])) {
				include('ticket_pdf_build.php');
			} else {
				include('ticket_dashboard.php');
			} ?>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<?php include_once('../footer.php'); ?>
