<div class="col-md-12">
	<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Documents</h3>') ?>
	<?php if($access_all === TRUE) { ?>
		<?php foreach ($field_sort_order as $field_sort_field) { ?>
			<?php if(strpos($value_config, ',Project Docs,') !== FALSE && $field_sort_field == 'Project Docs') {
				$doc_type = 'project'; ?>
				<div class="project_doc_table" id="no-more-tables"><?php include('add_ticket_view_documents.php'); ?></div>
			<?php } ?>
			
			<?php if(strpos($value_config, ',Contact Docs,') !== FALSE && $field_sort_field == 'Contact Docs') {
				$doc_type = 'contact'; ?>
				<div class="contact_doc_table" id="no-more-tables"><?php include('add_ticket_view_documents.php'); ?></div>
			<?php } ?>
			
			<?php if(strpos($value_config, ',Documents Docs,') !== FALSE && $field_sort_field == 'Documents Docs') {
				unset($doc_type); ?>
				<div class="document_table" id="no-more-tables"><?php include('add_ticket_view_documents.php'); ?></div>
				<div class="form-group">
					<label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
							<span class="popover-examples list-inline">&nbsp;
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
							</span>
					</label>
					<div class="col-sm-8">
						<input name="upload_document[]" multiple type="file" data-table="ticket_document" data-id="" data-id-field="ticketdocid" data-filename-placement="inside" class="form-control" />
					</div>
				</div>
			<?php } ?>

			<?php if(strpos($value_config, ',Documents Links,') !== FALSE && $field_sort_field == 'Documents Links') { ?>
				<div class="multi-block">
					<input name="deleted" type="hidden" data-table="ticket_document" data-id="" data-id-field="ticketdocid">
					<div class="form-group">
						<label for="additional_note" class="col-sm-4 control-label">Attach Link(s):<br><em>(e.g. - https://www.google.com)</em></label>
						<div class="col-sm-8">
							<input name="link" type="text" data-table="ticket_document" data-id="" data-id-field="ticketdocid" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="additional_note" class="col-sm-4 control-label">Label:</label>
						<div class="col-sm-8">
							<input name="label" type="text" data-table="ticket_document" data-id="" data-id-field="ticketdocid" class="form-control" placeholder="Detail name of the attached file...">
						</div>
					</div>
					<div class="form-group">
						<label for="additional_note" class="col-sm-4 control-label">Tab:</label>
						<div class="col-sm-7">
							<select name="type" data-table="ticket_document" data-id="" data-id-field="ticketdocid" class="chosen-select">
								<option value="Support">Support</option>
								<option value="Review">Review</option>
							</select>
						</div>
						<div class="col-sm-1">
							<img class="inline-img pull-right" data-history-label="Document" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
							<img class="inline-img pull-right" data-history-label="Document" onclick="remMulti(this);" src="../img/remove.png">
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	<?php } ?>
</div>