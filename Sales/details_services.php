<!-- Services --><?php include_once('../Sales/config.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    $('#deleteservices_0').hide();
});
$(document).on('change', '.serv_serv_onchange', selectServiceService);
$(document).on('change', '.serv_cat_onchange', selectServiceCat);
$(document).on('change', '[name="serviceid"]', selectServiceHeading);

function selectServiceCat() {
    var line = $(this).closest('.row');
    line.find('[name=serviceid] option,.serv_serv_onchange option').hide();
    line.find('[name=serviceid] option[data-category="'+this.value+'"],.serv_serv_onchange option[data-category="'+this.value+'"]').show();
    line.find('[name=serviceid],.serv_serv_onchange').trigger('change.select2');
}

function selectServiceService() {
    var line = $(this).closest('.row');
    var cat_filter = $(this).find('option:selected').data('category');
    line.find('.serv_cat_onchange').val(cat_filter).trigger("change.select2");
    line.find('[name=serviceid] option').hide();
    line.find('[name=serviceid] option[data-service-type="'+this.value+'"]'+(cat_filter != '' && cat_filter != undefined ? '[data-category="'+cat_filter+'"]' : '')).show();
    line.find('[name=serviceid]').trigger('change.select2');
}

function selectServiceHeading() {
    var line = $(this).closest('.row');
    line.find('.serv_cat_onchange').val($(this).find('option:selected').data('category')).trigger("change.select2");
    line.find('.serv_serv_onchange').val($(this).find('option:selected').data('service-type')).trigger("change.select2");
}
</script>

<div class="accordion-block-details padded" id="services">
    <div class="accordion-block-details-heading"><h4>Services</h4></div>
    <?php if (strpos($value_config, ',Services Category,') !== false) { echo '<div class="col-sm-3 gap-md-left-15 gap-bottom"><b>Category</b></div>'; }
    if (strpos($value_config, ',Services Service Type,') !== false) { echo '<div class="col-sm-3 gap-md-left-15 gap-bottom"><b>Service Type</b></div>'; }
    if (strpos($value_config, ',Services Heading,') !== false) { echo '<div class="col-sm-3 gap-md-left-15 gap-bottom"><b>Heading</b></div>'; } ?>
    <div class="clearfix"></div><?php
    foreach(explode(',',$serviceid) as $service) {
        $current_category = get_services($dbc, $service, 'category');
        $current_service_type = get_services($dbc, $service, 'service_type'); ?>
        <div class="row set-row-height" id="<?= 'services_'.$id_loop; ?>"><?php
            if (strpos($value_config, ',Services Category,') !== false) { ?>
                <div class="col-sm-3 gap-md-left-15">
                    <select data-placeholder="Choose a Category..." id="<?php echo 'scategory_'.$id_loop; ?>" class="chosen-select-deselect form-control serv_cat_onchange">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc,"SELECT DISTINCT(`category`) FROM `services` WHERE `deleted`=0 ORDER BY `category`");
                        while($row = mysqli_fetch_array($query)) {
                            $selected = ($current_category == $row['category']) ? 'selected="selected"' : '';
                            echo '<option '. $selected .' value="'. $row['category'] .'">'. $row['category'] .'</option>';
                        } ?>
                    </select>
                </div><?php
            }
            
            if (strpos($value_config, ',Services Service Type,') !== false) { ?>
                <div class="col-sm-3 gap-md-left-15">
                    <select data-placeholder="Choose a Type..." id="<?php echo 'sservice_'.$id_loop; ?>" class="chosen-select-deselect form-control serv_serv_onchange">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc,"SELECT DISTINCT(CONCAT(`service_type`,`category`)), `service_type`, `category` FROM `services` WHERE `deleted`=0 ORDER BY `service_type`");
                        while($row = mysqli_fetch_array($query)) {
                            $selected = ($current_service_type == $row['service_type']) ? 'selected="selected"' : '';
                            echo '<option data-category="'.$row['category'].'" '.($row['category'] != $current_category && !empty($current_category) ? 'style="display: none;"' : '').' '. $selected .' value="'. $row['service_type'] .'">'. $row['service_type'] .'</option>';
                        } ?>
                    </select>
                </div><?php
            }
            
            if (strpos($value_config, ',Services Heading,') !== false) { ?>
                <div class="col-sm-3 gap-md-left-15">
                    <select data-placeholder="Choose a Heading..." data-table="sales" data-concat="," name="serviceid" class="chosen-select-deselect form-control">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc,"SELECT `serviceid`, `heading`, `category`, `service_type` FROM `services` WHERE `deleted`=0 ORDER BY `heading`");
                        while($row = mysqli_fetch_array($query)) {
                            $selected = ($service == $row['serviceid']) ? 'selected="selected"' : '';
                            echo '<option data-category="'.$row['category'].'" data-service-type="'.$row['service_type'].'" '.(($row['category'] != $current_category && !empty($current_category) || ($row['service_type'] != $current_service_type && !empty($current_service_type))) ? 'style="display: none;"' : '').' '. $selected .' value="'. $row['serviceid'] .'">'. $row['heading'] .'</option>';
                        } ?>
                    </select>
                </div><?php
            } ?>

            <div class="col-sm-1" >
                <img src="<?= WEBSITE_URL; ?>/img/remove.png" class="cursor-hand inline-img pull-right" onclick="rem_row(this);"/>
                <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" class="cursor-hand inline-img pull-right" onclick="add_row(this);"/>
            </div>
            
            <div class="clearfix"></div>
        </div><?php
    } ?>
    
    <div class="clearfix"></div>
</div><!-- .accordion-block-details -->