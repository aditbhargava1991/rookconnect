<?php /* Budgeting */
include ('../include.php');
checkAuthorised('budget');
$tab_config = get_config($dbc, 'budget_tabs');
if(empty(trim($tab_config,','))) {
	$tab_config = ',pending_budget,active_budget,expense_tracking,';
}
$tab_config = array_values(array_filter(explode(',',$tab_config)));
$asset_tabs = explode('#*#', get_config($dbc, 'chart_accts_assets'));
$liability_tabs = explode('#*#', get_config($dbc, 'chart_accts_liabilities'));
$expense_tabs = explode('#*#', get_config($dbc, 'chart_accts_expense'));
$_GET['tab'] = (empty($_GET['tab']) ? $tab_config[0] : $_GET['tab']);
$_GET['type'] = (empty($_GET['type']) ? $tab_config[0] : $_GET['type']); ?>
<script>
$(document).ready(function() {
	<?php if(!IFRAME_PAGE) { ?>
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
					$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				}
			}
			if($('.scrum_tickets ul').is(':visible')) {
				var height = $('.sidebar').offset().top + $('.sidebar').innerHeight() - $('.scrum_tickets').offset().top - 87;
				$('.scrum_tickets ul').css('display','inline-block').css('overflow-y','auto').outerHeight(height);
			}
		}).resize();
		$('.search_list').change(function() {
			window.location.replace('?tab=search&q='+encodeURIComponent(this.value));
		});
	<?php } ?>
	$('#mobile_tabs .panel-title').off('click',loadPanel).click(loadPanel);
});
function loadPanel() {
	$(this).off('click',loadPanel);
	var body = $(this).closest('.panel').find('.panel-body');
	body.load(body.data('url'));
}
function submitForm(thisForm) {
	if (!$('input[name="search_user_submit"]').length) {
		var input = $("<input>")
					.attr("type", "hidden")
					.attr("name", "search_user_submit").val("1");
		$('[name=form_sites]').append($(input));
	}

	$('[name=form_sites]').submit();
}
function selectStatus(sel) {
	var status = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
    var fname = $("#fname").val();
    var lname = $("#lname").val();
    var contactid = $("#session_contactid").val();

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "call_log_ajax_all.php?fill=sales_status&salesid="+arr[1]+'&status='+status,
		dataType: "html",   //expect html to be returned
		success: function(response){
            if(status == 'Won') {
                alert("Lead Won");
            }
            if(status == 'Lost') {
                alert("Lead Lost and Removed from Cold Call.");
            }
			    location.reload();
		}
	});
}

function selectAction(sel) {
	var action = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "call_log_ajax_all.php?fill=sales_action&salesid="+arr[1]+'&action='+action,
		dataType: "html",   //expect html to be returned
		success: function(response){
    	    location.reload();
		}
	});
}
function followupDate(sel) {
	var reminder = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "call_log_ajax_all.php?fill=sales_reminder&salesid="+arr[1]+'&reminder='+reminder,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
</script>
</head>
<body>
<?php include_once ('../navigation.php'); ?>
<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="ticket_iframe" src=""></iframe>
		</div>
	</div>
	<div class="row" id="no-more-tables">
		<div class="main-screen">
			<div class="tile-header standard-header" style="<?= IFRAME_PAGE ? 'display:none;' : '' ?>">
				<div class="pull-right settings-block">&nbsp;</div>
				<div class="scale-to-fill">
					<h1 class="gap-left"><a href="?">Budgets</a><img class="no-toggle statusIcon pull-right no-margin inline-img small" title="" src="" data-original-title="">
					<?php if(config_visible_function($dbc, 'budgets') == 1) { ?>
						<a href="?tab=settings" class="mobile-block pull-right gap-left"><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
					<?php } ?></h1>
				</div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

			<div class="clearfix"></div>
			<?php IF(!IFRAME_PAGE) { ?>
				<div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
					<ul>
						<?php if($_GET['tab'] == 'settings') { ?>
							<?php echo '<li class="sidebar-higher-level '.('tile' == $_GET['type'] ? 'active' : '').'"><a href="?tab=settings&type=tile">Tile Settings</a></li>';
							if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'pending_budget' ) === true ) {
								echo '<li class="sidebar-higher-level '.('pending_budget' == $_GET['type'] ? 'active' : '').'"><a href="?tab=settings&type=pending_budget">Pending Budgets</a></li>';
							}
							if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'active_budget' ) === true ) {
								echo '<li class="sidebar-higher-level '.('active_budget' == $_GET['type'] ? 'active' : '').'"><a href="?tab=settings&type=active_budget">Active Budgets</a></li>';
							}
							if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'expense_tracking' ) === true ) {
								echo '<li class="sidebar-higher-level '.('expense_tracking' == $_GET['type'] ? 'active' : '').'" ><a href="?tab=settings&type=expense_tracking">Budget Expense Tracking</a></li>';
							}
							if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'assets' ) === true ) {
								echo '<li class="sidebar-higher-level '.('assets' == $_GET['type'] ? 'active' : '').'" ><a href="?tab=settings&type=assets">Chart of Assets</a></li>';
							}
							if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'liabilities' ) === true ) {
								echo '<li class="sidebar-higher-level '.('liabilities' == $_GET['type'] ? 'active' : '').'" ><a href="?tab=settings&type=liabilities">Chart of Liabilities</a></li>';
							}
							if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'expense' ) === true ) {
								echo '<li class="sidebar-higher-level '.('expense' == $_GET['type'] ? 'active' : '').'" ><a href="?tab=settings&type=expense">Chart of Expenses</a></li>';
							} ?>
						<?php } else { ?>
							<li class="standard-sidebar-searchbox"><input type="text" class="form-control search_list" value="<?= $_GET['q'] ?>" placeholder="Search Scrum Notes"></li>
							<li class="sidebar-higher-level <?= $_GET['tab'] == 'howto' ? 'active' : '' ?>"><a href="?tab=howto">How To Guide</a></li>
							<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['pending_budget','active_budget','expense_tracking']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#budgets">Budgets<span class="arrow"></span></a>
								<ul class="collapse <?= in_array($_GET['tab'],['pending_budget','active_budget','expense_tracking']) ? 'in' : '' ?>" id="budgets">
									<?php if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'pending_budget' ) === true && in_array('pending_budget',$tab_config)) {
										echo '<li class="sidebar-lower-level '.('pending_budget' == $_GET['tab'] ? 'active' : '').'"><a href="?tab=pending_budget">Pending Budgets</a></li>';
									}
									if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'active_budget' ) === true && in_array('active_budget',$tab_config)) {
										echo '<li class="sidebar-lower-level '.('active_budget' == $_GET['tab'] ? 'active' : '').'"><a href="?tab=active_budget">Active Budgets</a></li>';
									}
									if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'expense_tracking' ) === true && in_array('expense_tracking',$tab_config)) {
										echo '<li class="sidebar-lower-level '.('expense_tracking' == $_GET['tab'] ? 'active' : '').'" ><a href="?tab=expense_tracking">Budget Expense Tracking</a></li>';
									} ?>
								</ul>
							</li>
							<?php if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'assets' ) === true && in_array('assets',$tab_config)) { ?>
								<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['assets']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#assets">Assets<span class="arrow"></span></a>
									<ul class="collapse <?= in_array($_GET['tab'],['assets']) ? 'in' : '' ?>" id="assets">
										<?php if(in_array('all_products',$asset_tabs) || in_array_starts('product_',$asset_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['product']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#assets_products">Products<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['product']) ? 'in' : '' ?>" id="assets_products">
													<?php $product_cats = $dbc->query("SELECT `category` FROM `products` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `category`");
													while($cat = $product_cats->fetch_assoc()) {
														if(in_array('all_products',$asset_tabs) || in_array('product_'.$cat['category'],$asset_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('assets' == $_GET['tab'] && $_GET['type'] == 'product' && $_GET['cat'] == $cat['category'] ? 'active' : '') ?>" ><a href="?tab=assets&type=product&cat=<?= $cat['category'] ?>"><?= $cat['category'] ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_materials',$asset_tabs) || in_array_starts('material_',$asset_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['material']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#assets_materials">Materials<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['material']) ? 'in' : '' ?>" id="assets_materials">
													<?php $material_cats = $dbc->query("SELECT `category` FROM `material` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `category`");
													while($cat = $material_cats->fetch_assoc()) {
														if(in_array('all_materials',$asset_tabs) || in_array('material_'.$cat['category'],$asset_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('assets' == $_GET['tab'] && $_GET['type'] == 'material' && $_GET['cat'] == $cat['category'] ? 'active' : '') ?>" ><a href="?tab=assets&type=material&cat=<?= $cat['category'] ?>"><?= $cat['category'] ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_invs',$asset_tabs) || in_array_starts('inventory_',$asset_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['inventory']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#assets_invs"><?= INVENTORY_TILE ?><span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['inventory']) ? 'in' : '' ?>" id="assets_invs">
													<?php foreach(explode('#*#', get_config($dbc, 'inventory_tabs')) as $cat) {
														if(in_array('all_invs',$asset_tabs) || in_array('inventory_'.$cat,$asset_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('assets' == $_GET['tab'] && $_GET['type'] == 'inventory' && $_GET['cat'] == $cat ? 'active' : '') ?>" ><a href="?tab=assets&type=inventory&cat=<?= $cat ?>"><?= $cat ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_assets',$asset_tabs) || in_array_starts('asset_',$asset_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['asset']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#assets_assets">Assets<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['asset']) ? 'in' : '' ?>" id="assets_assets">
													<?php foreach(explode(',', get_config($dbc, 'asset_tabs')) as $cat) {
														if(in_array('all_assets',$asset_tabs) || in_array('asset_'.$cat,$asset_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('assets' == $_GET['tab'] && $_GET['type'] == 'asset' && $_GET['cat'] == $cat ? 'active' : '') ?>" ><a href="?tab=assets&type=asset&cat=<?= $cat ?>"><?= $cat ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_equips',$asset_tabs) || in_array_starts('equip_',$asset_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['equip']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#assets_equips">Equipment<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['equip']) ? 'in' : '' ?>" id="assets_equips">
													<?php foreach(explode(',', get_config($dbc, 'equipment_tabs')) as $cat) {
														if(in_array('all_equips',$asset_tabs) || in_array('equip_'.$cat,$asset_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('assets' == $_GET['tab'] && $_GET['type'] == 'equip' && $_GET['cat'] == $cat ? 'active' : '') ?>" ><a href="?tab=assets&type=equip&cat=<?= $cat ?>"><?= $cat ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_customs',$asset_tabs) || in_array_starts('custom_',$asset_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['custom']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#assets_customs">Custom<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['custom']) ? 'in' : '' ?>" id="assets_customs">
													<?php $custom_cats = $dbc->query("SELECT `category` FROM `custom` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `category`");
													while($cat = $custom_cats->fetch_assoc()) {
														if(in_array('all_customs',$asset_tabs) || in_array('custom_'.$cat['category'],$asset_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('assets' == $_GET['tab'] && $_GET['type'] == 'custom' && $_GET['cat'] == $cat['category'] ? 'active' : '') ?>" ><a href="?tab=assets&type=custom&cat=<?= $cat['category'] ?>"><?= $cat['category'] ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_expenses',$asset_tabs) || in_array_starts('expense_',$asset_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['expense']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#assets_expenses">Expenses<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['assets']) && in_array($_GET['type'],['expense']) ? 'in' : '' ?>" id="assets_expenses">
													<?php $expense_cats = $dbc->query("SELECT `category`, `ec` FROM `expense_categories` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `ec`,`category`");
													while($cat = $expense_cats->fetch_assoc()) {
														if(in_array('all_expenses',$asset_tabs) || in_array('expense_'.$cat['category'],$asset_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('assets' == $_GET['tab'] && $_GET['type'] == 'expense' && $_GET['cat'] == $cat['category'] ? 'active' : '') ?>" ><a href="?tab=assets&type=expense&cat=<?= $cat['category'] ?>"><?= (empty($cat['ec']) ? '' : $cat['ec'].': ').$cat['category'] ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
									</ul>
								</li>
							<?php } ?>
							<?php if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'liabilities' ) === true && in_array('liabilities',$tab_config)) { ?>
								<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['liabilities']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#liabilities">Liabilities<span class="arrow"></span></a>
									<ul class="collapse <?= in_array($_GET['tab'],['liabilities']) ? 'in' : '' ?>" id="liabilities">
										<?php if(in_array('all_products',$liability_tabs) || in_array_starts('product_',$liability_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['product']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#liabilities_products">Products<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['product']) ? 'in' : '' ?>" id="liabilities_products">
													<?php $product_cats = $dbc->query("SELECT `category` FROM `products` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `category`");
													while($cat = $product_cats->fetch_assoc()) {
														if(in_array('all_products',$liability_tabs) || in_array('product_'.$cat['category'],$liability_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('liabilities' == $_GET['tab'] && $_GET['type'] == 'product' && $_GET['cat'] == $cat['category'] ? 'active' : '') ?>" ><a href="?tab=liabilities&type=product&cat=<?= $cat['category'] ?>"><?= $cat['category'] ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_materials',$liability_tabs) || in_array_starts('material_',$liability_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['material']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#liabilities_materials">Materials<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['material']) ? 'in' : '' ?>" id="liabilities_materials">
													<?php $material_cats = $dbc->query("SELECT `category` FROM `material` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `category`");
													while($cat = $material_cats->fetch_assoc()) {
														if(in_array('all_materials',$liability_tabs) || in_array('material_'.$cat['category'],$liability_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('liabilities' == $_GET['tab'] && $_GET['type'] == 'material' && $_GET['cat'] == $cat['category'] ? 'active' : '') ?>" ><a href="?tab=liabilities&type=material&cat=<?= $cat['category'] ?>"><?= $cat['category'] ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_invs',$liability_tabs) || in_array_starts('inventory_',$liability_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['inventory']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#liabilities_invs"><?= INVENTORY_TILE ?><span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['inventory']) ? 'in' : '' ?>" id="liabilities_invs">
													<?php foreach(explode('#*#', get_config($dbc, 'inventory_tabs')) as $cat) {
														if(in_array('all_invs',$liability_tabs) || in_array('inventory_'.$cat,$liability_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('liabilities' == $_GET['tab'] && $_GET['type'] == 'inventory' && $_GET['cat'] == $cat ? 'active' : '') ?>" ><a href="?tab=liabilities&type=inventory&cat=<?= $cat ?>"><?= $cat ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_assets',$liability_tabs) || in_array_starts('asset_',$liability_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['asset']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#liabilities_assets">Assets<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['asset']) ? 'in' : '' ?>" id="liabilities_assets">
													<?php foreach(explode(',', get_config($dbc, 'asset_tabs')) as $cat) {
														if(in_array('all_assets',$liability_tabs) || in_array('asset_'.$cat,$liability_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('liabilities' == $_GET['tab'] && $_GET['type'] == 'asset' && $_GET['cat'] == $cat ? 'active' : '') ?>" ><a href="?tab=liabilities&type=asset&cat=<?= $cat ?>"><?= $cat ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_equips',$liability_tabs) || in_array_starts('equip_',$liability_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['equip']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#liabilities_equips">Equipment<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['equip']) ? 'in' : '' ?>" id="liabilities_equips">
													<?php foreach(explode(',', get_config($dbc, 'equipment_tabs')) as $cat) {
														if(in_array('all_equips',$liability_tabs) || in_array('equip_'.$cat,$liability_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('liabilities' == $_GET['tab'] && $_GET['type'] == 'equip' && $_GET['cat'] == $cat ? 'active' : '') ?>" ><a href="?tab=liabilities&type=equip&cat=<?= $cat ?>"><?= $cat ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_customs',$liability_tabs) || in_array_starts('custom_',$liability_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['custom']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#liabilities_customs">Custom<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['custom']) ? 'in' : '' ?>" id="liabilities_customs">
													<?php $custom_cats = $dbc->query("SELECT `category` FROM `custom` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `category`");
													while($cat = $custom_cats->fetch_assoc()) {
														if(in_array('all_customs',$liability_tabs) || in_array('custom_'.$cat['category'],$liability_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('liabilities' == $_GET['tab'] && $_GET['type'] == 'custom' && $_GET['cat'] == $cat['category'] ? 'active' : '') ?>" ><a href="?tab=liabilities&type=custom&cat=<?= $cat['category'] ?>"><?= $cat['category'] ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if(in_array('all_expenses',$liability_tabs) || in_array_starts('expense_',$liability_tabs)) { ?>
											<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['expense']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#liabilities_expenses">Expenses<span class="arrow"></span></a>
												<ul class="collapse <?= in_array($_GET['tab'],['liabilities']) && in_array($_GET['type'],['expense']) ? 'in' : '' ?>" id="liabilities_expenses">
													<?php $expense_cats = $dbc->query("SELECT `category`, `ec` FROM `expense_categories` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `ec`,`category`");
													while($cat = $expense_cats->fetch_assoc()) {
														if(in_array('all_expenses',$liability_tabs) || in_array('expense_'.$cat['category'],$liability_tabs)) { ?>
															<li class="sidebar-lower-level <?= ('liabilities' == $_GET['tab'] && $_GET['type'] == 'expense' && $_GET['cat'] == $cat['category'] ? 'active' : '') ?>" ><a href="?tab=liabilities&type=expense&cat=<?= $cat['category'] ?>"><?= (empty($cat['ec']) ? '' : $cat['ec'].': ').$cat['category'] ?></a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
									</ul>
								</li>
							<?php } ?>
							<?php if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'expense' ) === true && in_array('expense',$tab_config)) { ?>
								<li class="sidebar-higher-level"><a class="<?= in_array($_GET['tab'],['expense']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#expense">Expenses<span class="arrow"></span></a>
									<ul class="collapse <?= in_array($_GET['tab'],['expense']) ? 'in' : '' ?>" id="expense">
										<?php $expense_cats = $dbc->query("SELECT `category`, `ec` FROM `expense_categories` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `ec`,`category`");
										while($cat = $expense_cats->fetch_assoc()) {
											if(in_array('all_expenses',$expense_tabs) || in_array('expense_'.$cat['category'],$expense_tabs)) { ?>
												<li class="sidebar-lower-level <?= ('expense' == $_GET['tab'] && $_GET['type'] == 'expense' && $_GET['cat'] == $cat['category'] ? 'active' : '') ?>" ><a href="?tab=expense&type=expense&cat=<?= $cat['category'] ?>"><?= (empty($cat['ec']) ? '' : $cat['ec'].': ').$cat['category'] ?></a></li>
											<?php } ?>
										<?php } ?>
									</ul>
								</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
			<div class="col-sm-12 form-horizontal show-on-mob panel-group block-panels full-width" id="mobile_tabs">
				<?php if($_GET['tab'] == 'settings') { ?>
					<div class="panel panel-default">
						<div class="panel-heading mobile_load">
							<h4 class="panel-title higher_level_clickable">
								<a data-toggle="collapse" data-parent="#mobile_tabs" href="#settings_tile">
									Tile Settings<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="settings_tile" class="panel-collapse collapse">
							<div class="panel-body" data-url="field_config_tile.php">
								Loading...
							</div>
						</div>
					</div>
					<?php if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'pending_budget' ) === true ) { ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title higher_level_clickable">
									<a data-toggle="collapse" data-parent="#mobile_tabs" href="#settings_pending">
										Pending Budgets<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="settings_pending" class="panel-collapse collapse">
								<div class="panel-body" data-url="field_config_pending.php">
									Loading...
								</div>
							</div>
						</div>
					<?php }
					if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'active_budget' ) === true ) { ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title higher_level_clickable">
									<a data-toggle="collapse" data-parent="#mobile_tabs" href="#settings_active">
										Active Budgets<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="settings_active" class="panel-collapse collapse">
								<div class="panel-body" data-url="field_config_active.php">
									Loading...
								</div>
							</div>
						</div>
					<?php }
					if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'expense_tracking' ) === true ) { ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title higher_level_clickable">
									<a data-toggle="collapse" data-parent="#mobile_tabs" href="#settings_expense_tracking">
										Budget Expense Tracking<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="settings_expense_tracking" class="panel-collapse collapse">
								<div class="panel-body" data-url="field_config_expense_tracking.php">
									Loading...
								</div>
							</div>
						</div>
					<?php }
					if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'assets' ) === true ) { ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title higher_level_clickable">
									<a data-toggle="collapse" data-parent="#mobile_tabs" href="#settings_assets">
										Chart of Assets<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="settings_assets" class="panel-collapse collapse">
								<div class="panel-body" data-url="field_config_chart_accounts.php?type=assets">
									Loading...
								</div>
							</div>
						</div>
					<?php }
					if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'liabilities' ) === true ) { ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title higher_level_clickable">
									<a data-toggle="collapse" data-parent="#mobile_tabs" href="#settings_liabilities">
										Chart of Liabilities<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="settings_liabilities" class="panel-collapse collapse">
								<div class="panel-body" data-url="field_config_chart_accounts.php?type=liabilities">
									Loading...
								</div>
							</div>
						</div>
					<?php }
					if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'expense' ) === true ) { ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title higher_level_clickable">
									<a data-toggle="collapse" data-parent="#mobile_tabs" href="#settings_expense">
										Chart of Expenses<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="settings_expense" class="panel-collapse collapse">
								<div class="panel-body" data-url="field_config_expenses.php">
									Loading...
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } else { ?>
					<div class="panel panel-default">
						<div class="panel-heading mobile_load">
							<h4 class="panel-title higher_level_clickable">
								<a data-toggle="collapse" data-parent="#mobile_tabs" href="#howtowork">
									How To Guide - Workflow<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="howtowork" class="panel-collapse collapse">
							<div class="panel-body" data-url="budget_howto.php?status=Workflow">
								Loading...
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading mobile_load">
							<h4 class="panel-title higher_level_clickable">
								<a data-toggle="collapse" data-parent="#mobile_tabs" href="#howtokey">
									How To Guide - Keywords<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="howtokey" class="panel-collapse collapse">
							<div class="panel-body" data-url="budget_howto.php?status=Keywords">
								Loading...
							</div>
						</div>
					</div>
					<?php if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'pending_budget' ) === true && in_array('pending_budget',$tab_config)) { ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title higher_level_clickable">
									<a data-toggle="collapse" data-parent="#mobile_tabs" href="#pending">
										Pending Budgets<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="pending" class="panel-collapse collapse">
								<div class="panel-body" data-url="budget_pending.php">
									Loading...
								</div>
							</div>
						</div>
					<?php }
					if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'active_budget' ) === true && in_array('active_budget',$tab_config)) { ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title higher_level_clickable">
									<a data-toggle="collapse" data-parent="#mobile_tabs" href="#active">
										Active Budgets<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="active" class="panel-collapse collapse">
								<div class="panel-body" data-url="budget_active.php">
									Loading...
								</div>
							</div>
						</div>
					<?php }
					if ( check_subtab_persmission( $dbc, 'budget', ROLE, 'expense_tracking' ) === true && in_array('expense_tracking',$tab_config)) { ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title higher_level_clickable">
									<a data-toggle="collapse" data-parent="#mobile_tabs" href="#expense">
										Budget Expense Tracking<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="expense" class="panel-collapse collapse">
								<div class="panel-body" data-url="expense_tracking.php">
									Loading...
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
			<div class="scale-to-fill has-main-screen hide-titles-mob">
				<div class="main-screen standard-dashboard-body form-horizontal pad-horizontal" id="no-more-tables">
					<div class="standard-dashboard-body-content">
						<?php switch($_GET['tab']) {
							case 'howto': include('budget_howto.php'); break;
							case 'pending_budget': include('budget_pending.php'); break;
							case 'active_budget': include('budget_active.php'); break;
							case 'expense_tracking': include('expense_tracking.php'); break;
							case 'expense':
							case 'liabilities':
							case 'assets': include('acct_chart.php'); break;
							case 'settings': include('field_config.php'); break;
						} ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<?php include_once('../footer.php'); ?>