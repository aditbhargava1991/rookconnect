/*
Dispatch Field Config - JS File
*/
$(document).ready(function() {
    $('input,select').change(save_config);
});
function save_config() {
    var field = this;
    var field_name = field.name;
    var value = '';
    if(field_name.substr(-2) == '[]') {
        if(field.type == 'checkbox') {
            array_values = [];
            $('[name="'+field_name+'"]:checked').each(function() {
                array_values.push(this.value);
            });
        } else {
            array_values = [];
            $('[name="'+field_name+'"]').each(function() {
                array_values.push(this.value);
            });
        }
        value = array_values.join(',');
        field_name = field_name.slice(0,-2);
    } else if(field.type == 'checkbox') {
        if($(field).is(':checked')) {
            value = field.value;
        } else {
            value = '';
        }
    } else {
        value = field.value;
    }
    $.ajax({
        url: '../Dispatch/ajax.php?action=save_config',
        method: 'POST',
        data: { field: field_name, value: value },
        success: function(response) {
        }
    });
}
function add_customer_role() {
    destroyInputs('.role_div');
    var block = $('.role_div').last();
    var clone = $(block).clone();
    clone.find('select').val('');
    $(block).after(clone);
    $('input,select').change(save_config);
    initInputs('.role_div');
}
function remove_customer_role(img) {
    if($('.role_div').length <= 1) {
        add_customer_role();
    }
    $(img).closest('.role_div').remove();
    $('[name="dispatch_tile_customer_roles[]"]').first().change();
}
function add_equipment_category() {
    destroyInputs('.equip_div');
    var block = $('.equip_div').last();
    var clone = $(block).clone();
    clone.find('select').val('');
    $(block).after(clone);
    $('input,select').change(save_config);
    initInputs('.equip_div');
}
function remove_equipment_category(img) {
    if($('.equip_div').length <= 1) {
        add_equipment_category();
    }
    $(img).closest('.equip_div').remove();
    $('[name="dispatch_tile_equipment_category[]"]').first().change();
}