<!-- Sales Order Details -->
<script type="text/javascript">
function editItemsIframe() {
	overlayIFrameSlider('<?= WEBSITE_URL ?>/Sales Order/details_category_order.php?sotid=<?= $sotid ?>&so_type=<?= $so_type ?>&from_type=iframe', '75%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20);

	$(document).on("overlayIFrameSliderLoad", function(e) {
		var no_confirm = e.originalEvent.detail.no_confirm;
		window.parent.$('.iframe_overlay').off('click').click(function() {
			if(no_confirm || confirm('Closing out of this window will discard your changes. Are you sure you want to close the window?')) {
				$('.iframe_overlay').hide();
				$('.iframe_overlay .iframe iframe').off('load').attr('src', '');
				$('html').prop('onclick',null).off('click');
				$('#save_order').click();
			}
		});
		window.parent.$('[name="sales_order_iframe"]').off('load').load(function() {
			$('.iframe_overlay').hide();
			$('.hide_on_iframe').show();
			$(this).off('load').contents().find('html').html('');
			$('#save_order').click();
		});
	});
}
</script>
<div class="accordion-block-details padded" id="nocat_order">
	<input type="hidden" name="has_details" value="1">
    <div class="accordion-block-details-heading"><h4 class="inline"><?= SALES_ORDER_NOUN ?> Details</h4><a href="" onclick="editItemsIframe(); return false;" class="pull-right"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img no-toggle" title="Edit"></a></div>
    <div class="row">
		<?php include('order_details_content_details.php');?>
		<div class="gap-left"><a href="" onclick="editItemsIframe(); return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img"></a></div>
	</div>
</div>