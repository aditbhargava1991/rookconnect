<div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
	<ul>
		<?php if(isset($_GET['settings'])) { ?>
			<li class="sidebar-higher-level <?= $_GET['settings'] == 'tile' ? 'active' : '' ?>"><a href="?settings=tile">Tile Settings</a></li>
			<li class="sidebar-higher-level <?= $_GET['settings'] == 'macros' ? 'active' : '' ?>"><a href="?settings=macros">Add Macros</a></li>
			<li class="sidebar-higher-level bb_warehouses <?= $_GET['settings'] == 'bb_warehouses' ? 'active' : '' ?>" <?= strpos('|'.get_config($dbc, 'upload_macros').'|', '|macro_import_bb.php|') !== FALSE ? '' : 'style="display:none;"' ?>><a href="?settings=bb_warehouses">Best Buy Warehouses</a></li>
		<?php } else {
			if(in_array('upload',$tab_list)) { ?>
				<li class="sidebar-higher-level <?= $_GET['tab'] == 'upload' ? 'active' : '' ?>"><a href="?tab=upload">Upload CSV</a></li>
			<?php }
			if(in_array('macros',$tab_list)) { ?>
				<li class="sidebar-higher-level <?= $_GET['tab'] == 'macros' ? 'active' : '' ?>"><a class="<?= $_GET['tab'] == 'macros' ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#macro_list">Macros<span class="arrow"></span></a></li>
				<ul class="collapse <?= $_GET['tab'] == 'macros' ? 'in' : '' ?>" id="macro_list">
					<?php if(count($macro_list) == 0) {
						echo '<li>No Macros Found</li>';
					} else {
						foreach($macro_list as $macro_label => $macro) {
							echo '<li class="sidebar-lower-level"><a class="'.($macro[0] == $_GET['macro'] ? 'active' : '').'" href="?tab=macros&macro='.$macro[0].'">'.$macro_label.'</a></li>';
						}
					} ?>
				</ul>
			<?php }
			if(in_array('assign',$tab_list)) { ?>
				<li class="sidebar-higher-level <?= $_GET['tab'] == 'assign' ? 'active' : '' ?>"><a href="?tab=assign">Assign <?= TICKET_TILE ?></a></li>
			<?php }
			if(in_array('history',$tab_list)) { ?>
				<li class="sidebar-higher-level <?= $_GET['tab'] == 'history' ? 'active' : '' ?>"><a href="?tab=history">History</a></li>
			<?php }
		} ?>
	</ul>
</div>