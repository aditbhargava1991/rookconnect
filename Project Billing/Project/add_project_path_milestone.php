<script>
$(document).ready(function(){
    var count = 1;

    $('#add_new_row').on( 'click', function () {
        $('#deleteservices_0').show();
        var clone = $('.additional_row').clone();
        clone.find('[name^=checklist_item]').closest('.form-group').remove();
        clone.find('[name^=ticket_item]').closest('.form-group').remove();
        clone.find('[name^=work_order_item]').closest('.form-group').remove();
        clone.find('.form-control').val('');
        clone.find('.mt').attr('id', 'mt_'+count);
        clone.find('.milestone').attr('id', 'milestone_'+count);
        clone.find('.timeline').attr('id', 'timeline_'+count);
        clone.find('.milestone').attr('name', 'milestone_'+count);
        clone.find('.timeline').attr('name', 'timeline_'+count);
        clone.find('#deleteservices_0').attr('id', 'deleteservices_'+count);
        clone.find('#add_checklist_item_0').attr('id', 'add_checklist_item_'+count);
        clone.find('#add_ticket_item_0').attr('id', 'add_ticket_item_'+count);
        clone.find('#add_work_order_item_0').attr('id', 'add_work_order_item_'+count);
        clone.removeClass("additional_row");
        $('#add_here_new_data').append(clone);
        count++;
        return false;
    });
    $('[name^=ticket_service_]').each(function() {
        var select = this;
        var id = $(select).data('service');
        $.ajax({
            method: 'GET',
            url: 'project_ajax_all.php?fill=ticket_template_service_list&id='+id,
            success: function(response) {
                $(select).empty().append(response).trigger('change.select2');
            }
        });
    });
    $('[name="project_path"]').change(function() {
        if($(this).val() == 'ADD_NEW_TEMPLATE') {
            $('#path_milestone_timeline').hide();
            $('#add_new_template').show();
        } else {
            $('#path_milestone_timeline').show();
            $('#add_new_template').hide();
        }
    });
});
function deleteService(sel, hide, blank) {
    var typeId = sel.id;
    var arr = typeId.split('_');

    $("#"+hide+arr[1]).hide();
    $("#"+blank+arr[1]).val('');
}
function addChecklistItem(sel) {
    var div = $(sel).closest('.form-group').find('[name^=timeline]').closest('div');
    var id = $(sel).attr('id').split('_')[3];
    var checklist_row = '<div class="form-group"><label class="control-label col-sm-2">Checklist Item:</label>'+
        '<div class="col-sm-9"><input type="text" name="checklist_item_'+id+'[]" class="form-control" placeholder="Enter a Checklist Item"></div>'+
        '<div class="col-sm-1"><button onclick="$(this).closest(\'.form-group\').remove(); return false;" class="btn brand-btn">X</button></div></div>';
    div.append(checklist_row);
}
function addTicket(sel) {
    var div = $(sel).closest('.form-group').find('[name^=timeline]').closest('div');
    var id = $(sel).attr('id').split('_')[3];
    var ticket_row = '<div class="form-group"><label class="control-label col-sm-2">Ticket Heading:</label>'+
        '<div class="col-sm-4"><input type="text" name="ticket_item_'+id+'[]" class="form-control" placeholder="Enter a Ticket Heading"></div>'+
        '<label class="control-label col-sm-1">Service:</label>'+
        '<div class="col-sm-4"><select name="ticket_service_'+id+'[]" class="form-control chosen-select-deselect" data-placeholder="Select a Service">[OPTIONS]</select></div>'+
        '<div class="col-sm-1"><button onclick="$(this).closest(\'.form-group\').remove(); return false;" class="btn brand-btn">X</button></div></div>';
    $.ajax({
        method: 'GET',
        url: 'project_ajax_all.php?fill=ticket_template_service_list&id=0',
        success: function(response) {
            ticket_row = ticket_row.replace('[OPTIONS]',response);
            div.append(ticket_row);
            resetChosen($('.chosen-select-deselect'));
        }
    });
}
function addWorkOrder(sel) {
    var div = $(sel).closest('.form-group').find('[name^=timeline]').closest('div');
    var id = $(sel).attr('id').split('_')[4];
    var work_order_row = '<div class="form-group"><label class="control-label col-sm-2">Work Order Heading:</label>'+
        '<div class="col-sm-9"><input type="text" name="work_order_item_'+id+'[]" class="form-control" placeholder="Enter Work Order Heading"></div>'+
        '<div class="col-sm-1"><button onclick="$(this).closest(\'.form-group\').remove(); return false;" class="btn brand-btn">X</button></div></div>';
    div.append(work_order_row);
}
</script>

<?php if (strpos($value_config, ','."Path Path".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Project Path:</label>
    <div class="col-sm-8">
        <select name="project_path" id="project_path" data-placeholder="Select a Template..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $query = mysqli_query($dbc,"SELECT project_path_milestone, project_path FROM project_path_milestone ORDER BY `project_path`");
            while($row = mysqli_fetch_array($query)) {
                if ($project_path== $row['project_path_milestone']) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='". $row['project_path_milestone']."'>".$row['project_path'].'</option>';
            }
            if (strpos($value_config, ','."Path Add New Template".',') !== FALSE) {
                echo "<option value='ADD_NEW_TEMPLATE'>Add New Template</option>";
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Path Milestone Timeline".',') !== FALSE) { ?>
<div class="form-group" id="path_milestone_timeline">
    <label for="site_name" class="col-sm-4 control-label">Milestone & Timeline:</label>
    <div class="col-sm-8 template-target"></div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Path Add New Template".',') !== FALSE) { ?>
<div id="add_new_template" style="display: none;">
    <div class="form-group">
        <label for="site_name" class="col-sm-4 control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Template Name:</label>
        <div class="col-sm-8">
            <input name="new_project_path" type="text" class="form-control">
        </div>
    </div>
    <div class="form-group clearfix">
        <label class="col-sm-3 text-center">Milestone</label>
        <label class="col-sm-5 text-center">Timeline</label>
    </div>
    <?php
        $new_project_path = '';
        $milestone = '';
        $timeline = '';
        $checklist = '';
        $ticket = '';
        $workorder = '';
        $each_milestone = explode('#*#',$milestone);
        $each_timeline = explode('#*#',$timeline);
        $each_ticket = explode('#*#',$ticket);
        $each_workorder = explode('#*#',$workorder);
        $each_checklist = explode('#*#',$checklist);

        $total_count = mb_substr_count($milestone,'#*#');
        $mt_id = 500;
        for($emp_loop=0; $emp_loop<=$total_count; $emp_loop++) {
            $ms = '';
            $tl = '';
            $tk = '';
            $wo = '';
            $cl = '';

            if(isset($each_milestone[$emp_loop])) {
                $ms = $each_milestone[$emp_loop];
            }
            if(isset($each_timeline[$emp_loop])) {
                $tl = $each_timeline[$emp_loop];
            }
            if(isset($each_ticket[$emp_loop])) {
                $tk = $each_ticket[$emp_loop];
            }
            if(isset($each_workorder[$emp_loop])) {
                $wo = $each_workorder[$emp_loop];
            }
            if(isset($each_checklist[$emp_loop])) {
                $cl = $each_checklist[$emp_loop];
            }
            if($ms != '') {
            ?>

            <div class="form-group clearfix mt" id="mt_<?php echo $mt_id; ?>">
                <div class="col-sm-3">
                    <input name="milestone_<?php echo $mt_id; ?>" id="milestone_<?php echo $mt_id; ?>" value = "<?php echo $ms; ?>" type="text" class="form-control milestone">
                </div>
                <div class="col-sm-7">
                    <input name="timeline_<?php echo $mt_id; ?>" id="timeline_<?php echo $mt_id; ?>" value="<?php echo $tl; ?>" type="text" class="form-control timeline">
                    <?php $items = explode('*#*',$cl);
                    foreach($items as $checklist_item):
                        if($checklist_item != ''): ?>
                            <div class="form-group">
                                <label class="control-label col-sm-2">Checklist Item:</label>
                                <div class="col-sm-9"><input type="text" name="checklist_item_<?php echo $mt_id; ?>[]" class="form-control" placeholder="Enter a Checklist Item" value="<?php echo $checklist_item; ?>"></div>
                                <div class="col-sm-1"><button onclick="$(this).closest('.form-group').remove(); return false;" class="btn brand-btn">X</button></div>
                            </div>
                        <?php endif;
                    endforeach;
                    $items = explode('*#*',$tk);
                    foreach($items as $ticket):
                        if($ticket != ''):
                            $ticket = explode('FFMSPLIT', $ticket);
                            $heading = $ticket[0];
                            $service = $ticket[1]; ?>
                            <div class="form-group">
                                <label class="control-label col-sm-2">Ticket Heading:</label>
                                <div class="col-sm-4"><input type="text" name="ticket_item_<?php echo $mt_id; ?>[]" class="form-control" placeholder="Enter a Ticket Heading" value="<?php echo $heading; ?>"></div>
                                <label class="control-label col-sm-1">Service:</label>
                                <div class="col-sm-4"><select name="ticket_service_<?php echo $mt_id; ?>[]" class="form-control chosen-select-deselect" data-placeholder="Select a Service" data-service="<?php echo $service; ?>"></select></div>
                                <div class="col-sm-1"><button onclick="$(this).closest('.form-group').remove(); return false;" class="btn brand-btn">X</button></div>
                            </div>
                        <?php endif;
                    endforeach;
                    $items = explode('*#*',$wo);
                    foreach($items as $work_order):
                        if($work_order != ''): ?>
                            <div class="form-group">
                                <label class="control-label col-sm-2">Work Order Heading:</label>
                                <div class="col-sm-9"><input type="text" name="work_order_item_<?php echo $mt_id; ?>[]" class="form-control" placeholder="Enter a Work Order Heading" value="<?php echo $work_order; ?>"></div>
                                <div class="col-sm-1"><button onclick="$(this).closest('.form-group').remove(); return false;" class="btn brand-btn">X</button></div>
                            </div>
                        <?php endif;
                    endforeach; ?>
                </div>
                <div class="col-sm-2 m-top-mbl" >
                    <a href="#" onclick="deleteService(this,'mt_','milestone_'); return false;" id="deleteservices_<?php echo $mt_id; ?>" class="btn brand-btn">Delete</a>
                    <a href="#" onclick="addChecklistItem(this); return false;" id="add_checklist_item_<?php echo $mt_id; ?>" class="btn brand-btn">Checklist</a>
                    <a href="#" onclick="addTicket(this); return false;" id="add_ticket_item_<?php echo $mt_id; ?>" class="btn brand-btn">Ticket</a>
                    <?php if(tile_visible($dbc, 'work_order')) { ?>
                        <a href="#" onclick="addWorkOrder(this); return false;" id="add_work_order_item_<?php echo $mt_id; ?>" class="btn brand-btn">Work Order</a>
                    <?php } ?>
                </div>
            </div>
    <?php
            }
            $mt_id++; } ?>

    <div class="additional_row">
        <div class="clearfix"></div>
        <div class="form-group clearfix mt" id="mt_0">
            <div class="col-sm-3">
                <input name="milestone_0" id="milestone_0" type="text" class="form-control milestone">
            </div>
            <div class="col-sm-7">
                <input name="timeline_0" type="text" class="form-control timeline">
            </div>
            <div class="col-sm-2 m-top-mbl" >
                <a href="#" onclick="deleteService(this,'mt_','milestone_'); return false;" id="deleteservices_0" class="btn brand-btn">Delete</a>
                <a href="#" onclick="addChecklistItem(this); return false;" id="add_checklist_item_0" class="btn brand-btn">Checklist</a>
                <a href="#" onclick="addTicket(this); return false;" id="add_ticket_item_0" class="btn brand-btn">Ticket</a>
                <?php if(tile_visible($dbc, 'work_order')) { ?>
                    <a href="#" onclick="addWorkOrder(this); return false;" id="add_work_order_item_0" class="btn brand-btn">Work Order</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div id="add_here_new_data"></div>
    <button id="add_new_row" class="btn brand-btn">Add Another Milestone</button>
</div>
<?php } ?>