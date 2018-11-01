// AJAX Saving Functionality
function saveFieldMethod(field) {
    saveSalesMethod(field);
}
function saveSalesMethod(field) {
    if(field.type == 'file') {
        var uploaded = 0;
        var filecount = field.files.length;
        for(var i = 0; i < filecount; i++) {
            var file = new FormData();
            var file_data = field.files[i];
            file.append('file',field.files[i]);
            file.append('table',$(field).data('table'));
            file.append('type',$(field).data('type'));
            file.append('salesid',$('[name=salesid]').val());
            $.ajax({
                url: '../Sales/sales_ajax_all.php?action=upload_files',
                method: 'POST',
                processData: false,
                contentType: false,
                data: file,
                xhr: function() {
                    var num_label = i;
                    var filename = this.data.get('file').name;
                    $(field).hide().after('<div style="background-color:#000;height:1.5em;padding:0;position:relative;width:100%;"><div style="background-color:#444;height:1.5em;left:0;position:absolute;top:0;" id="progress_'+num_label+'"></div><span id="label_'+num_label+'" style="color:#fff;left:0;position:absolute;text-align:center;top:0;width:100%;z-index:1;">'+filename+': 0%</span></div><div class="clearfix"></div>');
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(e){
                        var percentComplete = Math.round(e.loaded / e.total * 100);
                        $('#label_'+num_label).text(filename+': '+percentComplete+'%');
                        $('#progress_'+num_label).css('width',percentComplete+'%');
                    }, false);

                    return xhr;
                },
                success: function(response) {
                    if(++uploaded == filecount && $(field).data('after') != undefined) {
                        try {
                            window[$(field).data('after')]();
                        } catch(err) { }
                        doneSaving();
                    }
                }
            });
        }
    } else {
        var save_value = field.value;
        if($(field).data('table') == 'sales' && field.name == 'businessid' && save_value == 'New Business') {
            $.post('../Sales/sales_ajax_all.php?action=new_business', { }, function(response) {
                $('[name=businessid][data-table=sales]').append('<option selected value="'+response+'">New Business</option>').val(response).change();
            });
            doneSaving();
            return;
        } else if($(field).data('table') == 'sales' && field.name == 'contactid' && save_value == 'New Contact') {
            $.post('../Sales/sales_ajax_all.php?action=new_lead', {
                    businessid: $('[name=businessid][data-table=sales]').val()
                }, function(response) {
                $(field).append('<option selected value="'+response+'">New Sales Lead Contact</option>').val(response).change();
            });
            doneSaving();
            return;
        }
        if($(field).data('concat') != undefined) {
            var save_value = [];
            $('[data-table][name="'+field.name+'"]').each(function() {
                if(this.value != '') {
                    save_value.push(this.value);
                }
            });
            save_value = save_value.join($(field).data('concat'));
        }
        var id = $(field).data('id') != undefined ? $(field).data('id') : $('[name=salesid]').val();
        var field_name = field.name;
        if($(field).data('field') != undefined) {
            field_name = $(field).data('field');
            id = $(field).closest('.accordion-block-details').find('[name=contactid]').val();
        }
        $.ajax({
            url: '../Sales/sales_ajax_all.php?action=update_fields',
            method: 'POST',
            data: {
                salesid: $('[name=salesid]').val(),
                id: id,
                table: $(field).data('table'),
                field: field_name,
                value: save_value,
                target: $(field).data('target'),
                business: $('[name=businessid]').val()
            },
            success: function(response) {
                if(response > 0 && $(field).data('table') == 'sales') {
                    $('[name=salesid]').val(response);
                    $('[name=primary_staff]').change();
                    $('[name=status][data-table=sales]').change();
                } else if(response > 0 && $(field).data('table') == 'contacts') {
                    $(field).closest('.row').find('[data-table="contacts"]').data('id',response);
                    $(field).closest('.row').parents('.row').first().find('select[name=contactid],select[name=businessid]').first().append('<option value="'+response+'">New Record</option>').trigger('select2.change').val(response).change();
                } else if(response > 0 && $(field).data('after') != undefined) {
                    try {
                        window[$(field).data('after')]();
                    } catch(err) { }
                } else if(response > 0) {
                    $('[data-table="'+$(field).data('table')+'"]').data('id',response);
                }
                if($(field).data('table') == 'sales' && field_name == 'businessid') {
                    reload_business();
                } else if ($(field).data('table') == 'sales' && field_name == 'contactid') {
                    reload_contacts();
                }
                doneSaving();
            }
        });
    }
}

// Add and Remove rows of fields for a sales lead
function add_row(img) {
    var line = $(img).closest('.row');
    var clone = line.clone();
    clone.find('input,select').val('').find('option').show();
    clone.find('.sub_details').remove();
    line.after(clone);
    if(clone.offset().top < $('.standard-body-title').offset().top + $('.standard-body-title').outerHeight() || clone.offset().top > window.innerHeight - 200) {
        try {
            $('.standard-body-content').scrollTop($('.standard-body-content').scrollTop() + clone.offset().top - $('.standard-body-title').offset().top - $('.standard-body-title').outerHeight());
        } catch(err) { }
    }
    init_page();
}
function rem_row(img) {
    var line = $(img).closest('.row');
    var field_name = line.find('[data-table]').attr('name');
    if($('[data-table][name="'+field_name+'"]').length == 1) {
        add_row(img);
    }
    line.remove();
    $('[data-table][name="'+field_name+'"]').first().change();
}
function add_doc(img) {
    var line = $(img).closest('.row');
    line.closest('.accordion-block-details').find('.add_doc').show();
}
function rem_doc(img) {
    var line = $(img).closest('tr');
    line.hide();
    if(line.closest('.row').find('[data-table][name="deleted"]').filter(function() { return $(this).closest('tr').is(':visible'); }).length < 1) {
        line.closest('.accordion-block-details').find('table').hide();
        line.closest('.accordion-block-details').find('.add_doc').show();
    }
    line.find('[data-table][name="deleted"]').val(1).change();
}
function rem_infogathering(img) {
    var line = $(img).closest('tr');
    line.find('[data-table][name="deleted"]').val(1).change();
    line.remove();
}
function add_note() {
    overlayIFrameSlider('../Sales/add_sales_comment.php?salesid='+$('[name=salesid]').val(),'auto',true,true);
}

// Open the profile for the nearest contact
function load_profile(img, tile) {
    var contactid = $(img).closest('.row').find('select').val();
    if(contactid > 0) {
        overlayIFrameSlider('../'+tile+contactid,'auto',true,true);
    } else {
        alert('Please select a contact before attempting to view the Profile.');
    }
}