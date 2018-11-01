<!-- Customers -->
<script type="text/javascript">
$(document).ready(function() {
    $("#task_businessid").change(function() {
		var businessid = this.value;
        var sotid = $('#sotid').val();
		if(businessid > 0) {
            $.ajax({
                type: "GET",
                url: "ajax.php?fill=changeCustomer&businessid="+businessid+"&sotid="+sotid,
                dataType: "html",
                success: function(response){
                    // if (sotid > 0) {
                    //     location.reload();
                    // } else {
                    //     window.location.href = "?p=details&sotid="+response;
                    // }
                    if($('#sotid').val() == '' || $('#sotid').val() == undefined) {
                        $('#sotid').val(response);
                    }
                    $('#save_order').click();
                }
            })
		}
	});

    $("#task_businessid").change(function() {
        if($("#task_businessid option:selected").val() == 'New Business') {
                $("#new_business").show();
        } else {
            $("#new_business").hide();
        }
    });

    $('#customer_cat').change(function() {
        var cat = $(this).val();
        $('#task_businessid').find('option').not('option[value="New Business"]').hide();
        $('#task_businessid').find('option[data-category="'+cat+'"]').show();
        $('#task_businessid').trigger('change.select2');
    });

    // $("#sales_contact").change(function() {
    //     var contactid = this.value;

    //     if($("#sales_contact option:selected").text() == 'New Contact') {
    //             $( "#new_contact" ).show();
    //     } else {
    //         $( "#new_contact" ).hide();
    //     }

    //     $.ajax({
    //         type: "GET",
    //         url: "ajax.php?fill=assignphoneemail&contactid="+contactid,
    //         dataType: "html",
    //         success: function(response){
    //             var arr = response.split('**#**');
				// $('#sales_number').html(arr[0]);
				// $("#sales_number").trigger("change.select2");
				// $('#sales_email').html(arr[1]);
				// $("#sales_email").trigger("change.select2");
    //         }
    //     });

    // });

    // $("#sales_number").change(function() {
    //     if($("#sales_number option:selected").text() == 'New Number') {
    //             $( "#new_number" ).show();
    //     } else {
    //         $( "#new_number" ).hide();
    //     }
    // });

    // $("#sales_email").change(function() {
    //     if($("#sales_email option:selected").text() == 'New Email') {
    //             $( "#new_email" ).show();
    //     } else {
    //         $( "#new_email" ).hide();
    //     }
    // });

    $('#sales_classification').change(function() {
        var classification = this.value;
        var sotid = $('#sotid').val();
        if($('#sales_classification').val() == 'new_classification') {
            $('#new_classification').show();
        } else if($('#sales_classification').val() != '' && $('#sales_classification').val() != undefined) {
            $('#new_classification').hide();
            $.ajax({
                type: "GET",
                url: "ajax.php?fill=changeClassification&classification="+classification+"&sotid="+sotid,
                dataType: "html",
                success: function(response) {
                    $('#save_order').click();
                }
            });
        } else {
            $('#new_classification').hide();
        }
    });
});
$(document).on('change', 'select[name="business_contact[]"]', function() { newContact(this); });

function newContact(sel) {
    if($(sel).find('option:selected').text() == 'New Contact') {
        var businessid = $('[name="businessid"]').val();
        var sotid = $('#sotid').val();
        $(sel).prop('id','current_business_contact');
        overlayIFrameSlider('../Sales Order/add_contact.php?sotid='+sotid+'&businessid='+businessid, '75%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20);
    } else {
        $(sel).closest('.row_contact').find('.new_contact').hide();
    }
}

function addContact() {
    var contact_block = $('.row_contact').first();
    var clone = contact_block.clone();

    clone.find('.form-control').val('');
    clone.find('.form-control').trigger('change.select2');
    resetChosen(clone.find('select'));
    clone.find('.new_contact').hide();
    contact_block.after(clone);
}

function deleteContact(sel) {
    if($('.row_contact').length <= 1) {
        addContact();
    }

    $(sel).closest('.row_contact').remove();
}

function addCustomer() {
    var customer_cat = $('[name="customer_cat"]').val();
    var sotid = $('#sotid').val();
    overlayIFrameSlider('../Sales Order/add_customer.php?sotid='+sotid+'&category='+customer_cat, '75%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20);
}

</script>

<div class="accordion-block-details padded" id="customers">
    <div class="accordion-block-details-heading"><h4>Customer</h4></div>

    <?php if(count($customer_cat) > 1) { ?>
    <div class="row set-row-height gap-md-left-15 ">
        <div class="col-sm-3">Customer Category:*</div>
        <div class="col-sm-7">
            <select data-placeholder="Select a Customer Category..." name="customer_cat" id="customer_cat" class="chosen-select-deselect form-control">
                <?php
                $customerid_cat = get_contact($dbc, $customerid, 'category');
                if(empty($customerid_cat)) {
                    $customerid_cat = $customer_cat[0];
                }
                foreach($customer_cat as $cat_name) {
                    echo '<option '. ($customerid_cat==$cat_name ? 'selected' : '') .' value="'. $cat_name .'">'. $cat_name .'</option>';
                } ?>
            </select>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php } else {
        $customerid_cat = $customer_cat[0]; ?>
        <input type="hidden" name="customer_cat" id="customer_cat" value="<?= $customer_cat[0] ?>">
    <?php } ?>

    <div class="row set-row-height gap-md-left-15 customer_block">
        <div class="col-sm-3 pad-5">Customer:*</div>
        <div class="col-sm-7">
            <select data-placeholder="Select a Customer..." name="businessid" id="task_businessid" class="chosen-select-deselect form-control">
                <option value=""></option><?php
                $businessid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `customerid` FROM `sales_order_temp` WHERE `sotid` = '$sotid'"))['customerid'];
                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `name`, `contactid` FROM `contacts` WHERE `category` IN ('".implode("','", $customer_cat)."') AND `deleted` = 0 AND `status` > 0"), MYSQLI_ASSOC));
                // echo '<option value="New Business">New Customer</option>';
                foreach($query as $id) {
                    echo '<option data-category="'.get_contact($dbc, $id, 'category').'" '. ($businessid==$id ? 'selected' : '') .' value="'. $id .'" '.($customerid_cat != get_contact($dbc, $id, 'category') ? 'style="display:none;"' : '').'>'. (!empty(get_client($dbc, $id)) ? get_client($dbc, $id) : get_contact($dbc, $id)) .'</option>';
                } ?>
            </select>
        </div>
        <div class="col-sm-1 pull-right">
            <a href="#" class="add_staff" onclick="addCustomer(); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" height="20" /></a>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php if (isset($businessid)) {
        if (strpos($value_config, ',Business Contact,') !== FALSE) {
            $business_contact = explode(',',$business_contact);
            foreach ($business_contact as $contact) { ?>
                <div class="row_contact">
                    <div class="row set-row-height business_contact gap-md-left-15">
                        <div class="col-sm-3 pad-5">Contact:</div>
                        <div class="col-sm-7">
                            <select data-placeholder="Select a Contact..." name="business_contact[]" class="chosen-select-deselect form-control business_contact">
                                <option value=""></option><?php
                                $businessid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `customerid` FROM `sales_order_temp` WHERE `sotid` = '$sotid'"))['customerid'];
                                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE (`category`<>'Business' AND `deleted`=0 AND `status`>0 AND `businessid`='$businessid') OR `contactid` = '$contact'"), MYSQLI_ASSOC));
                                echo '<option value="New Contact">New Contact</option>';
                                foreach($query as $id) {
                                    echo '<option '. ($contact==$id ? 'selected' : '') .' value="'. $id .'">'. get_contact($dbc, $id) .'</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="col-sm-1 pull-right">
                        <a href="#" onclick="deleteContact(this); return false;"><img src="<?= WEBSITE_URL; ?>/img/remove.png" height="20" /></a>&nbsp;&nbsp;<a href="#" class="add_contact" onclick="addContact(); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" height="20" /></a></div>
                        <div class="clearfix"></div>
                    </div>

                </div>
            <?php }
        }

        if (strpos($value_config, ',Classification,') !== FALSE) { ?>
            <div class="row set-row-height gap-md-left-15">
                <div class="col-sm-3 pad-5">Classification:</div>
                <div class="col-sm-7">
                    <select data-placeholder="Select a Classification..." name="classification" class="chosen-select-deselect" id="sales_classification">
                        <option value=""></option>
                        <option value="new_classification">New Classification</option><?php
                        foreach ($get_classifications as $cat_tab) {
                            $selected = ( $classification==$cat_tab ) ? 'selected="selected"' : '';
                            echo '<option '. $selected .' value="'. $cat_tab .'">'. $cat_tab .'</option>';
                        } ?>
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>

            <div id="new_classification" style="display:none;">
                <div class="row set-row-height gap-md-left-15">
                    <div class="col-sm-4 pad-5">New Classification:</div>
                    <div class="col-sm-5"><input name="new_classification" type="text" class="form-control" /></div>
                    <div class="clearfix"></div>
                </div>
            </div>
        <?php }
    } ?>
</div><!-- .accordion-block-details -->