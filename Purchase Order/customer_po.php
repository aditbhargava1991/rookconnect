<?php if(empty($_GET['po'])) { ?>
	<script>
	$(document).ready(function() {
		save_po_num();
	});
	function save_po_num() {
		$('[name=po_num]').off('change',save_po).change(save_po);
		$('[name=contactid]').off('change',save_contact).change(save_contact);
	}
	function save_contact() {
		var field = this;
		$.post('pos_ajax_all.php?action=contact_po_number_contacts', { id: $(this).data('id'), po: $(this).closest('.block-group').find('[name=po_num]').val(), contact: this.value }, function(response) {console.log(response);
			if(response > 0) {
				$(field).data('id',response);
			}
		});
	}
	function save_po() {
		$.post('pos_ajax_all.php?action=contact_po_numbers', { new_po: this.value, old_po: $(this).data('po') });
		$(this).data('po',this.value);
	}
	function add_po(img) {
		
	}
	function rem_po(img) {
		
	}
	function add_contact(img) {
		
	}
	function rem_contact(img) {
		
	}
	</script>
	<?php $po_numbers = $dbc->query("SELECT `detail` FROM `contact_order_numbers` WHERE `category`='po_number' AND `deleted`=0 AND `contactid` > 0 GROUP BY `detail`");
	$po_num = $po_numbers->fetch_array()[0];
	$contact_list = sort_contacts_query($dbc->query("SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `category`='".BUSINESS_CAT."'"));
	do { ?>
		<div class="block-group form-horizontal">
			<div class="form-group">
				<label class="col-sm-4">Purchase Order #:</label>
				<div class="col-sm-7">
					<input type="text" name="po_num" data-po="<?= $po_num ?>" value="<?= $po_num ?>" class="form-control">
				</div>
				<div class="col-sm-1">
					<img class="inline-img" src="../img/remove.png">
					<img class="inline-img" src="../img/icons/ROOK-add-icon.png">
				</div>
			</div>
			<hr />
			<?php $po_contacts = $dbc->query("SELECT `id`, `contactid` FROM `contact_order_numbers` WHERE `deleted`=0 AND `detail`='$po_num'");
			$po_contact = $po_contacts->fetch_assoc();
			do { ?>
				<div class="form-group">
					<label class="col-sm-4">Contacts:</label>
					<div class="col-sm-7">
						<select class="chosen-select-deselect" data-id="<?= $po_contact['id'] ?>" data-placeholder="Select Contacts" name="contactid"><option />
							<?php foreach($contact_list as $contact) { ?>
								<option <?= $contact['contactid'] == $po_contact['contactid'] ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['full_name'] ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-sm-1">
						<img class="inline-img" src="../img/remove.png">
						<img class="inline-img" src="../img/icons/ROOK-add-icon.png">
					</div>
				</div>
			<?php } while($po_contact = $po_contacts->fetch_assoc()); ?>
		</div>
	<?php } while($po_num = $po_numbers->fetch_array()[0]);
} else {
	$po_num = filter_var($_GET['po'],FILTER_SANITIZE_STRING);
	$form_list = []; ?>
	<div id="no-more-tables">
		<table class="table table-bordered table-striped">
            <thead>
                <tr class="hidden-xs hidden-sm">
                    <th>Purchase Order #</th>
                    <th>PO Line Item #</th>
                    <th>Contacts</th>
                    <th><?= TICKET_NOUN ?></th>
                    <th>Customer Order #</th>
                    <th>Cross Reference #</th>
                </tr>
            </thead>
			<?php $query = $dbc->query("SELECT `tickets`.`businessid`, `tickets`.`clientid`, `tickets`.`notes`, `tickets`.`ticket_label`, `ticket_attached`.`ticketid`, `ticket_attached`.`po_line`, `ticket_attached`.`position` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`deleted`=0 AND `tickets`.`deleted`=0 AND `ticket_attached`.`po_num`='$po_num' ORDER BY LPAD(`po_num`,100,0), LPAD(`po_line`,100,0)");
			while($row = $query->fetch_assoc()) { ?>
				<tr>
					<td data-title="Purchase Order #"><?= $po_num ?></td>
					<td data-title="PO Line Item #"><?= $row['po_line'] ?></td>
					<td data-title="Contacts">
						<?php $contact_list = [];
						foreach(array_filter(explode(',',$row['businessid'].','.$row['clientid'])) as $contactid) {
							$contact_list[] = '<a href="../Contacts/contacts_inbox.php?fields=all_fields&edit='.$contactid.'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;">'.get_contact($dbc, $contactid, 'name_company').'</a>';
						}
						echo implode('<br />',$contact_list); ?>
					</td>
					<td data-title="<?= TICKET_NOUN ?>"><a href="../Ticket/index.php?edit=<?= $row['ticketid'] ?>&ticketid=<?= $row['ticketid'] ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,true); return false;"><?= $row['ticket_label'] ?></a></td>
					<td data-title="Customer Order #"><?= $row['position'] ?></td>
					<td data-title="Cross Reference #"><?= $row['notes'] ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
<?php }