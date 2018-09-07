<script>
function addSite() {
	var src = $('.agenda_topic:visible').last().closest('.agenda_info');
	destroyInputs($('.agenda_info'));
	var clone = src.clone();
	clone.find('input').val('');
	clone.find('.agenda_note').val('');
	src.after(clone);
	initInputs('.agenda_info');
	//clone.find('.agenda_note').focus();
}
function remSite(field) {
	if($('.agenda_topic:visible').length == 1) {
		addSite();
	}
	$(field).closest('.agenda_info').remove();
}

</script>
<?php
if(!empty($_GET['agendameetingid']))	 {
	$topic = explode('##FFM##', $agenda_topic);
	$note = explode('##FFM##', $agenda_note);

    $m = 0;
	foreach ($topic as $a_topic) {
    if($a_topic != '') {
    ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label" style="text-align:left;">Agendas Topic(s):</label>
    <div class="clearfix"></div>
    <div><input name="agenda_topic[]" value="<?php echo $a_topic; ?>" type="text" class="form-control agenda_topic" /></div>
</div>

<div class="form-group">
    <label for="site_name" class="col-sm-4 control-label" style="text-align:left;">Agendas Note:</label>
    <div class="clearfix"></div>
    <div><textarea name="agenda_note[]" rows="3" cols="50" class="form-control agenda_note"><?php echo html_entity_decode($note[$m]); ?></textarea></div>
</div>

<?php
    }

$m++;
}
} else {
?>

<div class="agenda_info">
<?php if (strpos($value_config, ','."Agenda Topic".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label" style="text-align:left;">Agendas Topic(s):</label>
    <div class="clearfix"></div>
    <div><input name="agenda_topic[]" type="text" class="form-control agenda_topic" /></div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Agenda Notes".',') !== FALSE) { ?>
<div class="form-group">
    <label for="site_name" class="col-sm-4 control-label" style="text-align:left;">Agendas Note:</label>
    <div class="clearfix"></div>
    <div><textarea name="agenda_note[]" rows="3" cols="50" class="form-control agenda_note"></textarea></div>
</div>

<div class="col-sm-1">
    <img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addSite();">
    <img class="inline-img pull-right" src="../img/remove.png" onclick="remSite(this);">
</div>
<?php } ?>
</div>

<?php } ?>
