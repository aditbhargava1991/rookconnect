<?php // Checkout View
error_reporting(0);
include_once('../include.php'); ?>
</head>
<body>
<?php 
if(FOLDER_NAME == 'accountreceivables') {
    checkAuthorised('accounts_receivables');
    $security = get_security($dbc, 'accounts_receivables');
    $tab_list = ['insurer_ar','patient_ar','ui_invoice_report','insurer_ar_report','insurer_ar_cm'];
} else if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
    $security = get_security($dbc, 'posadvanced');
    $tab_list = explode(',', get_config($dbc, 'invoice_tabs'));
} else {
    checkAuthorised('check_out');
    $security = get_security($dbc, 'check_out');
    $tab_list = explode(',', get_config($dbc, 'invoice_tabs'));
}
$edit_access = vuaed_visible_function($dbc, 'check_out');
$config_access = config_visible_function($dbc, 'check_out');
$ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));
include_once ('../navigation.php'); ?>
<div class="iframe_overlay" style="display:none;">
	<div class="container">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe src=""></iframe>
		</div>
	</div>
</div>
<div class="container">
    <div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header" style="<?= IFRAME_PAGE ? 'display:none;' : '' ?>">
                <div class="pull-right settings-block"><?php
                    if($security['config'] > 0) {
                        echo "<div class='pull-right gap-left'><a href='?".$current_tile."settings=fields'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>";
                    }
					if(in_array('PDF',$db_config)) {
						echo '<a href="../Ticket/ticket_pdf.php?'.$current_tile.'ticketid=&ticket_type='.$ticket_type.'" onclick="blankPDFForm(); return false;" class="btn brand-btn pull-right hide-titles-mob">Blank '.$ticket_noun.' Form <img src="../img/pdf.png" class="inline-img smaller"></a>';
						if($_GET['edit'] > 0) {
							echo '<a href="../Ticket/ticket_pdf.php?'.$current_tile.'ticketid='.$_GET['edit'].'&ticket_type='.$ticket_type.'" class="btn brand-btn pull-right hide-titles-mob">Print Current '.$ticket_noun.' <img src="../img/pdf.png" class="inline-img smaller"></a>';
						}
					}
					if(in_array('Export Ticket Log',$db_config)) {
						$ticket_log_template = !empty(get_config($dbc, 'ticket_log_template')) ? get_config($dbc, 'ticket_log_template') : 'template_a';
						echo '<a href="../Ticket/ticket_log_templates/'.$ticket_log_template.'_pdf.php?'.$current_tile.'ticketid=" class="btn brand-btn pull-right hide-titles-mob">Blank '.$ticket_noun.' Log <img src="../img/pdf.png" class="inline-img smaller"></a>';
					}
                    if($security['edit'] > 0) {
						echo "<div class='pull-right gap-left'><a href='?".$current_tile."edit=0&type=".$ticket_type."' class='new-btn'><button class='btn brand-btn hide-titles-mob'>New ".$ticket_noun."</button>";
						echo "<img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='show-on-mob' style='height: 2.5em;'></a></div>";
                    } ?>
                </div>
                <div class="scale-to-fill">
					<h1 class="gap-left"><a href="?tile_name=<?= $_GET['tile_name'] ?>"><?= TICKET_TILE.(!empty($_GET['tile_name']) ? ': '.$ticket_tabs[$_GET['tile_name']] : '') ?></a><?= isset($_GET['edit']) ? ($ticketid > 0 && $_GET['new_ticket'] != 'true' ? ': <span class="ticketid_span">'.get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"))).'</span>' : ': <span class="ticketid_span">New '.TICKET_NOUN.'</span>') : '' ?>
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
        <div class="main-screen" style="background-color: #fff; border-width: 0; height: auto; margin-top: -20px;">
            <h3 style="margin-top: 0; padding: 0.25em;"><a href="?"><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?></a><?php if($config_access > 0) {
                echo "<div class='pull-right' style='height: 1.35em; width: 1.35em;'><a href='?settings='><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' style='height: 100%;'></a></div>";
            } ?>
            <?php
            if($edit_access > 0) {
                echo "<div class='pull-right' style='height: 1em; padding: 0 0.25em;'><a href='?invoiceid=new' style='font-size: 0.5em;'><button class='btn brand-btn hide-titles-mob'>New Invoice</button>";
                echo "<img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='show-on-mob' style='height: 2.5em;'></a></div>";
            } ?>
            </h3>
            <div class="clearfix"></div>
			<?php if($_GET['invoiceid'] == 'new' || $_GET['invoiceid'] > 0) {
				if(!in_array('touch',$ux_options) || $_GET['ux'] != 'touch') {
					include('../Invoice/edit_invoice.php');
				} else if(in_array('touch',$ux_options) && (!in_array('standard',$ux_options) || $_GET['ux'] == 'touch')) {
					include('../Invoice/touch_main.php');
				}
			} else if(isset($_GET['settings'])) {
				include('../Invoice/field_config.php');
			} else {
				include('../Invoice/list_invoices.php');
			} ?>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<?php include('../footer.php'); ?>