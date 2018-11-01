<?php if($field_option == 'Order Lists Addition') {
$order_lists = mysqli_query($dbc,"SELECT * FROM `order_lists` WHERE `contactid`='$contactid' AND `deleted`='0'");

if ( $order_lists->num_rows>0 ) { ?>
    <div id="no-more-tables">
        <table class="table table-bordered">
            <tr class="hidden-xs">
                <th>List Name</th>
                <th>Include in Purchase Orders</th>
                <th>Include in <?= SALES_ORDER_TILE ?></th>
                <th>Function</th>
            </tr><?php
            while ( $row=mysqli_fetch_array($order_lists)) { ?>
                <tr>
                    <td data-title="List Name"><?= $row['order_title'] ?></td>
                    <td data-title="Incl. in PO"><?= $row['include_in_po'] ?></td>
                    <td data-title="Incl. in SO"><?= $row['include_in_so'] ?></td>
                    <td data-title="Function">
                        <a href="javascript:void(0);" onclick="overlayIFrameSlider('../Contacts/edit_order_lists.php?order_id=<?= $row['order_id'] ?>&contactid=<?= $contactid ?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">Edit</a>
                        |
                        <a href="<?= WEBSITE_URL ?>/delete_restore.php?action=delete&order_list_id=<?= $row['order_id'] ?>&contactid=<?= $contactid ?>" onclick="return confirm('Are you sure you want to delete this Order List?')">Delete</a>
                    </td>
                </tr><?php
            } ?>
        </table>
    </div><?php
} else {
    echo '<label class="col-sm-12 control-label">No Records Found.</label>';
} ?>

<div class="pull-right"><a href="javascript:void(0);" onclick="overlayIFrameSlider('../Contacts/edit_order_lists.php?contactid=<?= $contactid ?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img" alt="Add New Order List" /></a></div>
<div class="clearfix"></div>

<script>
$(document).ready(function() {
    setTimeout(function() {
        $('div[data-tab-name="individual_service_plan"] [id^=contact_]').not('[id^=contact_category],[id$=chosen]').each(function() {
            var select = this;
            var category = $(this).data('category');
            var contacts = $(this).data('value');
            $.ajax({
                method: 'GET',
                url: '../Individual Support Plan/isp_ajax_all.php?fill=contact_category&category='+category+'&contacts='+contacts,
                success: function(response) {
                    if($(select).find('option:selected').val() == '') {
                        $(select).empty().append(response).trigger('change.select2');
                    }
                }
            });
        });
    }, 1000);
});

function selectContactCategory(sel) {
    if(default_contact_list == '') {
        default_contact_list = $(sel).closest('.contact_group').find('select:not([name*=category])').html();
    }
    $.ajax({
        type: "GET",
        url: "../Individual Support Plan/isp_ajax_all.php?fill=contact_category&category="+sel.value,
        dataType: "html",   //expect html to be returned
        success: function(response){
            $(sel).closest('.contact_group').find('select:not([name*=category])').html(response).change().trigger('change.select2');
        }
    });
}

function addAnotherGoal(link) {
    var clone = $('[name="isp_goals_name[]"]').first().clone();
    clone.val('');
    $(link).closest('.form-group').find('div.col-sm-8').append(clone);
    $('[data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
}
var default_contact_list = '';
function contact_clone(btn) {
    var contact = $(btn).closest('.contact_group').clone();
    contact.find('select,input').val('');
    
    if(default_contact_list != '') {
        contact.find('select:not([name*=category])').html(default_contact_list)
    }
    resetChosen(contact.find("select"));
    
    var group = $(btn).closest('.contact_group');
    while(group.next('.contact_group').length > 0) {
        group = group.next('.contact_group');
    }
    group.after(contact);
    $('[data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
}
function contact_remove(btn) {
    if($(btn).closest('.contact_group').next('h3').length == 1 && $(btn).closest('.contact_group').prev('h3').length == 1) {
        contact_clone(btn);
    }
    $(btn).closest('.contact_group').remove();
}
function checkContactChange(sel) {
    if(sel.value == 'NEW_CONTACT') {
        $(sel).closest('.form-group').find('input').show().focus();
    } else {
        $(sel).closest('.form-group').find('input').hide();
    }
}
function submitSignature(div, btn) {
    var block = $('.'+div);

    var ispid = $(btn).data('row-id');
    var sig = $(block).find('[name="'+div+'[]"]').val();
    var sig_name = $(block).find('[name="'+div+'_name[]"]').val();
    var sig_date = $(block).find('[name="'+div+'_date[]"]').val();

    $('.'+div+' .form-control').val('');
    $('.'+div+' .clearButton a').click();
    
    $.ajax({
        url: '../Contacts/contacts_ajax.php?action=isp_submit_signature',
        method: 'POST',
        data: { ispid: ispid, field: div, sig: sig, sig_name: sig_name, sig_date: sig_date },
        success: function(response) {
            $('.'+div+'_existing .col-sm-8').append(response);
        }
    });
}
</script>

<?php } ?>