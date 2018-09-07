<?php $date = $_GET['date'];
$region = $_GET['region'];
$classification = $_GET['classification'];
$ticket_type = $_GET['ticket_type']; ?>
<div class="main-screen standard-body override-main-screen form-horizontal">
	<div class="standard-body-title">
		<h3>Assign <?= TICKET_TILE ?></h3>
	</div>
	<div class="standard-body-content pad-top">
		<div class="col-sm-12">
			<script>
			function get_details() {
				equip_scroll = $('.equip_list').scrollTop();
				$('.equip_list').html('<h4>Loading Equipment...</h4>').load(encodeURI('assign_equipment_list.php?date=<?= $date ?>&region=<?= $region ?>&classification=<?= $classification ?>'));
				$('.ticket_list').html('<h4>Loading <?= TICKET_TILE ?>...</h4>').load(encodeURI('assign_imported_tickets.php?date=<?= $date ?>&unassign_type=<?= $ticket_type ?>'), function() { setTicketAssign(); });
			}
			var ticketid = '';
			var equipment = '';
			function setTicketAssign() {
				$( ".ticket_list" ).sortable({
					beforeStop: function(e, ticket) {
						var block = $('.block-item.equipment.active').first();
						if(block.length > 0) {
							equipment = block.data('id');
							ticketid = ticket.item.data('ticketid');
							$('[name=day_start_time]').focus();
							$('.ui-datepicker-close').click(function() {
								$('.ui-datepicker-close').off('click');
								var day = this.value;
								this.value = '';
								$.post('optimize_ajax.php?action=assign_ticket_deliveries', {
									equipment: equipment,
									ticket: ticketid,
									start: $('[name=day_start_time]').val(),
									increment: '30 minutes'
								}, function(response) {
									$('.ticket_list').data('ids',$('.ticket_list').data('ids').filter(function(str) { return str != ticketid; }));
									get_details();
									$('[name=day_start_time]').val('');
									initInputs();
									ticketid = '';
									equipment = '';
								});
							});
						}
					},
					delay: 0,
					handle: ".drag-handle",
					items: "span.block-item.ticket",
					sort: function(e, ticket) {
						block = $(document.elementsFromPoint(e.clientX, e.clientY)).filter('.block-item.equipment').not('.ui-sortable-helper').first();
						$('.block-item.equipment.active').removeClass('active');
						block.addClass('active');
					},
					start: function(e, ticket) {
						ticket.helper.css('width','18em');
					}
				});
			}
			$(document).ready(function() {
				get_details();
			});
			</script>
			<input type="text" style="height:0;width:0;border:0; padding:0;" class="datetimepicker" name="day_start_time">
			<h4 class="no-gap"><?= !empty($date) ? 'Date: '.$date.' ' : ''?><?= !empty($region) ? 'Region: '.$region.' ' : ''?><?= !empty($classification) ? 'Classification: '.$classification.' ' : ''?></h4>
			<div class="assign_list_box" style="height: 20em;position:relative;width:calc(100% - 2px);">
				<div class="equip_list" style="display:inline-block; height:calc(100% - 7em); width:20%; float:left; overflow-y:auto;"></div>
				<div class="ticket_list" data-ids="<?= json_encode($ticket_list) ?>" style="display:inline-block; height:calc(100% - 7em); width:80%; float:right; overflow-y:auto;"></div>
			</div>
		</div>
	</div>
</div>