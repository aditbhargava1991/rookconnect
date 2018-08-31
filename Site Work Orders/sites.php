<?php $add_site_tile = mysqli_fetch_array(mysqli_query($dbc, "SELECT `tile_name` FROM `contacts` WHERE `category`='Sites' GROUP BY `tile_name` ORDER BY COUNT(*) DESC"))['tile_name'];
if ( $edit_access == 1 ) { ?>
	<div class="col-sm-12 col-xs-12 col-lg-4 pad-top offset-xs-top-20 pull-right">
		<a href="../<?= ($add_site_tile == 'contacts3' ? 'Contacts3' : ($add_site_tile == 'contacts2' ? 'Contacts2' : 'Contacts')) ?>/add_contacts.php?category=Sites&from_url=<?= WEBSITE_URL ?>/Site Work Orders/site_work_orders.php?tab=sites" class="btn brand-btn mobile-block gap-bottom pull-right">Add Site</a>
		<a href="add_contacts_multiple.php?category=<?= $category; ?>" class="btn brand-btn mobile-block gap-bottom pull-right">Import</a>
		<span class="popover-examples list-inline"><a class="pull-right" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click to add multiple sites at once."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	</div>
<?php } ?>
<div class="clearfix"></div>
<?php $site_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT sites.`contactid`, sites.`site_name` status, bus.`name` first_name FROM `contacts` sites LEFT JOIN `contacts` bus on sites.`contactid`=bus.`siteid` WHERE sites.`category`='Sites' AND sites.`deleted`=0 AND sites.`status`=1 AND sites.`show_hide_user`=1"), MYSQLI_ASSOC));
$site_number_enabled = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts` WHERE `tab` = 'Sites' AND `accordion` = 'Site Information'"))['contacts'];
if(count($site_list) > 0): ?>
<div id="no-more-tables">
	<table class="table table-bordered table-striped">
        <thead>
		<tr class="hidden-sm hidden-xs">
			<?php if (strpos(','.$site_number_enabled.',', ',Site Number,')) { ?>
				<th>Work Site #</th>
			<?php } ?>
			<th>Site Name</th>
			<th>Customer</th>
			<th>Address</th>
			<th>Google Maps</th>
			<th>Phone Number</th>
			<th>Function</th>
		</tr>
        </thead>
		<?php foreach($site_list as $site_id)
		{
			$site = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$site_id'")); ?>
			<tr>
				<?php if (strpos(','.$site_number_enabled.',', ',Site Number,')) { ?>
					<td data-title="Work Site #:"><?= $site['site_number']; ?></td>
				<?php } ?>
				<td data-title="Site Name:"><?= $site['site_name']; ?></td>
				<td data-title="Customer:"><?= implode(":<br />\n", array_filter([get_client($dbc, $site['businessid'])])); ?></td>
				<td data-title="Address:"><?= implode("<br />\n", array_filter([decryptIt($site['business_address']), decryptIt($site['business_street']), decryptIt($site['business_city']).', '.decryptIt($site['business_state']).'  '.decryptIt($site['business_zip']), decryptIt($site['business_country'])])); ?></td>
				<td data-title="Google Map:"><?= (!empty($site['google_maps_address']) ? '<a href="'.$site['google_maps_address'].'">View Map</a>' : ''); ?></td>
				<td data-title="Phone Number:"><?= decryptIt($site['office_phone']); ?></td>
				<td data-title="Function:">
				<?php if($edit_access == 1)
				{ ?>
					<a href="../<?= ($site['tile_name'] == 'contacts3' ? 'Contacts3' : ($site['tile_name'] == 'contacts2' ? 'Contacts2' : 'Contacts')) ?>/add_contacts.php?category=Sites&contactid=<?= $site['contactid'] ?>&from_url=<?= WEBSITE_URL ?>/Site Work Orders/site_work_orders.php?tab=sites">Edit</a> |
					<a href="../delete_restore.php?action=delete&contactid=<?= $site['contactid'] ?>&from_url=<?= WEBSITE_URL ?>/Site Work Orders/site_work_orders.php?tab=sites">Archive</a>
				<?php } ?>
				</td>
			</tr>
		<?php } ?>
	</table>
</div>
<?php else:
	echo "<h2>No Sites Found</h2>";
endif; ?>
<?php if ( $edit_access == 1 ) { ?>
	<div class="col-sm-12 col-xs-12 col-lg-4 pad-top offset-xs-top-20 pull-right">
		<a href="../<?= ($add_site_tile == 'contacts3' ? 'Contacts3' : ($add_site_tile == 'contacts2' ? 'Contacts2' : 'Contacts')) ?>/add_contacts.php?category=Sites&from_url=<?= WEBSITE_URL ?>/Site Work Orders/site_work_orders.php?tab=sites" class="btn brand-btn mobile-block gap-bottom pull-right">Add Site</a>
	</div>
<?php } ?>