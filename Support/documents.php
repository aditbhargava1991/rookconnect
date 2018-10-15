<?php include_once('config.php'); ?>
<div class="notice double-gap-bottom popover-examples">
	<img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" style="width:3em;">
	<div style="float:right; width:calc(100% - 4em);"><span class="notice-name">Note:</span>
	Organization is one of the secrets to success. To help you maintain and access your documents, all essential information that we design, configure or provide you with will be stored here for convenient access.</div>
	<div class="clearfix"></div>
</div>
<?php $document_list = mysqli_query($dbc_support, "SELECT docs.*, uploads.type, uploads.`document_link` FROM `client_documents` docs LEFT JOIN `client_documents_uploads` uploads ON docs.`client_documentsid`=uploads.`client_documentsid` WHERE `contactid`='$user' AND `deleted`=0");
if(mysqli_num_rows($document_list) > 0) { ?>
	<div id="no-more-tables">
		<table class="table table-bordered">
			<tr class="hidden-xs hidden-sm">
				<th>Category</th>
				<th>Type</th>
				<th>Link</th>
			</tr>
			<?php while($row = mysqli_fetch_array($document_list)) {
				echo "<tr>";
					echo "<td data-title='Category'>".$row['category']."</td>";
					echo "<td data-title='Type'>".$row['client_documents_type']."</td>";
					echo "<td data-title='Link'><a href='https://ffm.rookconnect.com/Client Documents/download/".$row['document_link']."'>".$row['title']."</a></td>";
				echo "</tr>";
			} ?>
		</table>
	</div>
<?php } else {
	echo "<h3>No Documents Found</h3>";
}