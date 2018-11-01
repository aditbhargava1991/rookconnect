<script>
$(document).ready(function() {
	$('.panel-heading').click(loadPanel);
});
function loadPanel() {
	$('.panel-body').html('Loading...');
	body = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: $(body).data('file'),
		method: 'POST',
		response: 'html',
		success: function(response) {
			$(body).html(response);
		}
	});
}
function deleteStyle(img) {
	$.ajax({ url: 'estimates_ajax.php?action=deleteStyle&styleid='+$(img).data('id') });
	$(img).closest('a').hide();
}
</script>
<div id='settings_accordions' class='sidebar show-on-mob panel-group block-panels col-xs-12'>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_status">
					<?= ESTIMATE_TILE ?> Status<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_status" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_status.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_types">
					<?= rtrim(ESTIMATE_TILE, 's') ?> Types<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_types" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_types.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_groups">
					Staff Collaboration Groups<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_groups" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_groups.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_dashboard">
					Dashboard Settings<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_dashboard" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_dashboard.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_fields">
					Fields for <?= PROJECT_NOUN ?> and One Time <?= ESTIMATE_TILE ?><span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_fields" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_fields.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_ticket_fields">
					Fields for <?= TICKET_NOUN ?> <?= ESTIMATE_TILE ?><span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_fields" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_ticket_fields.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_reporting">
					Reporting<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_reporting" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_reporting.php">
				Loading...
			</div>
		</div>
	</div>
</div>
<div class='tile-sidebar hide-titles-mob standard-collapsible default-height'>
	<ul>
		<a href="?settings=status"><li class="<?= empty($_GET['settings']) || $_GET['settings'] == 'status' ? 'active blue' : '' ?>"><?= rtrim(ESTIMATE_TILE, 's') ?> Status</li></a>
		<a href="?settings=tile"><li class="<?= $_GET['settings'] == 'tile' ? 'active blue' : '' ?>">Tile Settings</li></a>
		<a href="?settings=types"><li class="<?= $_GET['settings'] == 'types' ? 'active blue' : '' ?>"><?= rtrim(ESTIMATE_TILE, 's') ?> Types</li></a>
		<a href="?settings=groups"><li class="<?= $_GET['settings'] == 'groups' ? 'active blue' : '' ?>">Staff Collaboration Groups</li></a>
		<a href="?settings=dashboard"><li class="<?= $_GET['settings'] == 'dashboard' ? 'active blue' : '' ?>">Dashboard Settings</li></a>
		<a href="?settings=fields"><li class="<?= $_GET['settings'] == 'fields' ? 'active blue' : '' ?>">Fields for <?= PROJECT_NOUN ?> and One Time <?= ESTIMATE_TILE ?></li></a>
		<a href="?settings=ticket_fields"><li class="<?= $_GET['settings'] == 'ticket_fields' ? 'active blue' : '' ?>">Fields for <?= TICKET_NOUN ?> <?= ESTIMATE_TILE ?></li></a>
		<a href="?settings=reporting"><li class="<?= $_GET['settings'] == 'reporting' ? 'active blue' : '' ?>">Reporting</li></a>
		<a href="?settings=pdf_options"><li class="<?= $_GET['settings'] == 'pdf_options' ? 'active blue' : '' ?>">PDF Options</li></a>
		<li><a class="<?= $_GET['settings'] == 'pdf' ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#collapse_estimate_designs"><?= ESTIMATE_TILE ?> Designs<span class="arrow"></span></a>
			<ul id="collapse_estimate_designs" class="collapse <?= $_GET['settings'] == 'pdf' ? 'in' : '' ?>">
				<?php if($_GET['settings'] == 'pdf') {
					include('field_config_pdf_save.php'); ?>
				<?php } ?>
				<?php $pdf_styles = mysqli_query($dbc, "SELECT * FROM (SELECT `pdfsettingid`,`style_name` FROM `estimate_pdf_setting` WHERE `estimateid` IS NULL AND `deleted`=0 ORDER BY `style_name`) `styles` UNION SELECT 'new' `pdfsettingid`, 'New Design' `style_name`");
				while($pdf_style = mysqli_fetch_assoc($pdf_styles)) { ?>
					<li><a href="?settings=pdf&style=<?= $pdf_style['pdfsettingid'] ?>&design=style"><?= $pdf_style['style_name'] == '' ? '(Untitled Template)' : $pdf_style['style_name'] ?><img data-id="<?= $pdf_style['pdfsettingid'] ?>" onclick="deleteStyle(this); return false;" class="inline-img pull-right" src="../img/remove.png"></a>
					<?php if($_GET['style'] == $pdf_style['pdfsettingid']) { ?>
						<ul>
							<a href="?settings=pdf&style=<?= $_GET['style'] ?>&design=style"><li class="<?= $_GET['design'] == 'style' ? 'active blue' : '' ?>"><?= ESTIMATE_TILE ?> Design</li></a>
							<a href="?settings=pdf&style=<?= $_GET['style'] ?>&design=cover"><li class="<?= $_GET['design'] == 'cover' ? 'active blue' : '' ?>">Cover Page</li></a>
							<a href="?settings=pdf&style=<?= $_GET['style'] ?>&design=toc"><li class="<?= $_GET['design'] == 'toc' ? 'active blue' : '' ?>">Table of Contents</li></a>
							<a href="?settings=pdf&style=<?= $_GET['style'] ?>&design=pages"><li class="<?= $_GET['design'] == 'pages' ? 'active blue' : '' ?>">Add Page</li></a>
							<a href="?settings=pdf&style=<?= $_GET['style'] ?>&design=pdf"><li class="<?= $_GET['design'] == 'pdf' ? 'active blue' : '' ?>">PDF Settings</li></a>
							<a href="?settings=pdf&style=<?= $_GET['style'] ?>&design=header"><li class="<?= $_GET['design'] == 'header' ? 'active blue' : '' ?>">Header</li></a>
							<a href="?settings=pdf&style=<?= $_GET['style'] ?>&design=content"><li class="<?= $_GET['design'] == 'content' ? 'active blue' : '' ?>">Main Content</li></a>
							<a href="?settings=pdf&style=<?= $_GET['style'] ?>&design=footer"><li class="<?= $_GET['design'] == 'footer' ? 'active blue' : '' ?>">Footer</li></a>
						</ul>
					<?php } ?>
					</li>
				<?php } ?>
			</ul>
		</li>
	</ul>
</div>
<div class='scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen default-height'>
		<?php switch($_GET['settings']) {
		case 'groups':
			include('field_config_groups.php');
			break;
		case 'types':
			include('field_config_types.php');
			break;
		case 'tile':
			include('field_config_tile.php');
			break;
		case 'reporting':
			include('field_config_reporting.php');
			break;
		case 'dashboard':
			include('field_config_dashboard.php');
			break;
		case 'fields':
			include('field_config_fields.php');
			break;
		case 'pdf_options':
			include('field_config_pdf_options.php');
			break;
		case 'pdf':
			include('field_config_pdf.php');
			break;
		case 'ticket_fields':
            include('field_config_ticket_fields.php');
            break;
		case 'status':
		default:
			include('field_config_status.php');
			break;
		} ?>
	</div>
</div>