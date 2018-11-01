<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Checklists</h3>') ?>
<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Checklist/edit_checklist.php?edit=NEW&ticketid=<?= $ticketid ?>&from_url=<?= urlencode(WEBSITE_URL.'/Ticket/index.php?edit='.$ticketid.(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group'])) ?>&reload_ticket_checklists=1', 'auto', false, true); return false;" class="btn brand-btn pull-right">Add Checklist</a>
<div class="clearfix"></div>
<?php $attached_checklists = mysqli_query($dbc,"SELECT * FROM `checklist` WHERE (CONCAT(',',`ticketid`,',') LIKE '%,$ticketid,%' OR `ticketid` LIKE '%ALL%') AND `ticketid` != '' AND `ticketid` IS NOT NULL AND `deleted`=0");
if(mysqli_num_rows($attached_checklists) > 0) {
	while($checklist = mysqli_fetch_assoc($attached_checklists)) {
        if($generate_pdf) {
            ob_clean();
        }
		if(in_array($_SESSION['contactid'],explode(',',$checklist['assign_staff'])) || $checklist['assign_staff'] == ',ALL,') {
			$_GET['view'] = $checklist['checklistid'];
            $_GET['from_tile'] = 'tickets';
            echo '<div class="checklist_screen" data-querystring="view='.$checklist['checklistid'].'&from_tile=tickets">';
			include('../Checklist/view_checklist.php');
            echo '</div>';
		} else {
			echo "<h3>".$checklist['checklist_name'].' - Restricted</h3>';
		}
        if($generate_pdf) {
            $pdf_contents[] = ['', ob_get_contents()];
        }
	}
} else {
	echo "<h3>No Checklists Found.";
} ?>
<div class="clearfix"></div>
<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Checklist/edit_checklist.php?edit=NEW&ticketid=<?= $ticketid ?>&from_url=<?= urlencode(WEBSITE_URL.'/Ticket/index.php?edit='.$ticketid) ?>&reload_ticket_checklists=1', 'auto', false, true); return false;" data-history-label="New Checklist" class="btn brand-btn pull-right">Add Checklist</a>
<script>
function checklistChange(sel) {
	var stage = sel.value;
    if($(sel).is(':checked')){
        var checked = 1;
    } else {
        var checked = 0;
    }
    $.ajax({
        type: "GET",
        url: "../Checklist/checklist_ajax.php?fill=checklist&ticketid="+$(sel).data('ticket')+"&checklistid="+stage+"&checked="+checked,
        dataType: "html",
        success: function(response){
            location.reload();
        }
    });
}
</script>