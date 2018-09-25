<?php
    /*
	 * iFrame content to select category (Inventory vs. Vendor Price List) to order from
	 */
	include ('../include.php');
checkAuthorised('sales_order');
	error_reporting(0);
?>
<script>
    $(document).ready(function() {
        var count = 1;
        $('#delete_item_0').hide();
        $('#add_item').on('click', function () {
            $('#delete_item_0').show();

            var clone = $('.copy-inventory-block').clone();
            clone.find('.form-control').val('');
            clone.find('.price').val('0');
            clone.find('.quantity').val('0');

            clone.find('.labourtype').attr('id', 'labourtype_'+count);
            clone.find('.labourcategory').attr('id', 'labourcategory_'+count);
            clone.find('.category').attr('id', 'category_'+count);
            clone.find('.subcategory').attr('id', 'subcategory_'+count);
            clone.find('.product').attr('id', 'product_'+count);
            clone.find('.price').attr('id', 'price_'+count);
            clone.find('.quantity').attr('id', 'quantity_'+count);

            clone.find('#inventory_0').attr('id', 'inventory_'+count);
            clone.find('#delete_item_0').attr('id', 'delete_item_'+count);
            clone.find('#inventory_details_0').html('');
            clone.find('#inventory_details_0').hide();
            clone.find('#inventory_details_0').attr('id', 'inventory_'+count);
            $('#delete_item_0').hide();

            clone.find('.form-control').trigger('change.select2');
            clone.removeClass('copy-inventory-block');
            $('#add_new_item').append(clone);
            resetChosen($('#category_'+count));
            resetChosen($('#subcategory_'+count));
            resetChosen($('#product_'+count));

            $('#subcategory_'+count).closest('div').hide();
            $('#product_'+count).closest('div').hide();

            count++;
            return false;
        });

        $('#cancel').on('click', function(){
            var from_type = $('#from_type').val();
            var redirect_url = '';
            if (from_type == 'load_template' || from_type == 'sot_iframe') {
                    window.parent.$('.close_iframer').click();
            } else if (from_type == 'template') {
                redirect_url = '<?= WEBSITE_URL; ?>/Sales Order/templates.php?templateid='+$('#templateid').val();
            } else {
                redirect_url = "<?= WEBSITE_URL; ?>/Sales Order/order.php?p=details&sotid="+$('#sotid').val()+"#";
            }
            if(redirect_url != '') {
                window.top.location.href = redirect_url;
            }
        });

        loadItems();
    });
	var allItems = [];
    $(document).on('change', 'select[name="heading_name"]', function() { changeHeading(this); });
    $(document).on('change', 'select[name="category[]"]', function() { changeCategory(this); });
    $(document).on('change', 'select[name="subcategory[]"]', function() { changeSubcategory(this); });
    $(document).on('change', 'select[name="inventory[]"]:not(.labour)', function() { changeItem(this); });
    $(document).on('change', 'select[name="labourtype[]"]', function() { changeLabourType(this); });
    $(document).on('change', 'select[name="labourcategory[]"]', function() { changeLabourCategory(this); });
    $(document).on('change', 'select[name="inventory[]"].labour', function() { changeLabourItem(this); });

    //Heading
    function changeHeading(sel) {
        if(sel.value == 'new') {
            $('#heading_name_new').show();
            $('#mandatory_quantity').val('0');
        } else {
            $('#heading_name_new').hide();
            var from_type = $('#from_type').val();
            var load_type = $('#load_type').val();
            var id = '';
            if (from_type == 'template' || (from_type == 'load_template' && load_type == 'template')) {
                id = $('#templateid').val();
            } else {
                id = $('#sotid').val();
            }
            var item_type = $('#item_type').val();
            var contact_category = $('#contact_category').val();
            var heading_name = sel.value;

            $.ajax({
                type: 'GET',
                url: 'ajax.php?fill=changeHeading&from_type='+from_type+'&id='+id+'&item_type='+item_type+'&contact_category='+contact_category+'&heading_name='+heading_name,
                dataType: 'html',
                success: function(response) {
                    var qty = parseInt(response);
                    $('#mandatory_quantity').val(qty);
                    if(qty > 0) {
                        $('#mandatory_checkbox').prop('checked', 'checked');
                        $('#mandatory_quantity').show();
                    } else {
                        $('#mandatory_checkbox').removeAttr('checked');
                        $('#mandatory_quantity').hide();
                    }
                }
            });
        }
    }

    //Mandatory Quantity
    function changeMandatory(sel) {
        if($(sel).is(':checked')) {
            $('#mandatory_quantity').show();
        } else {
            $('#mandatory_quantity').hide();
        }
    }

    //Category
    function changeCategory(sel) {
        var subcategory_sel = $(sel).closest('.inventory-block').find('[name="subcategory[]"]');
        var product_sel = $(sel).closest('.inventory-block').find('[name="inventory[]"]');
        subcategory_sel.val('');
        product_sel.val('');

        if(sel.value != '') {
            subcategory_sel.find('option').hide();
            if (subcategory_sel.find('option[data-category="'+sel.value+'"]').length > 0) {
                subcategory_sel.closest('div').show();
                subcategory_sel.find('option[data-category="'+sel.value+'"]').show();
            } else {
                subcategory_sel.closest('div').hide();
            }

            var items = filterItems(sel.value, '');
            product_sel.closest('div').show();
            product_sel.find('option').not('.always_visible').remove();
            product_sel.append(createItemOptions(items));

        } else {
            subcategory_sel.closest('div').hide();
            product_sel.closest('div').hide();
        }

        subcategory_sel.trigger('change.select2');
        product_sel.trigger('change.select2');

        changeItem(sel);
    }

    //Subcategory
    function changeSubcategory(sel) {
        var category = $(sel).closest('.inventory-block').find('[name="category[]"]').val();
        var product_sel = $(sel).closest('.inventory-block').find('[name="inventory[]"]');
        product_sel.val('');

        product_sel.find('option').hide();
        product_sel.find('option[data-category="'+category+'"][data-subcategory="'+sel.value+'"],option[value="**NEW_ITEM**"]').show();
        product_sel.trigger('change.select2');

        changeItem(sel);
    }

    //Item
    function changeItem(sel) {
        var category = $(sel).closest('.inventory-block').find('[name="category[]"]');
        var subcategory = $(sel).closest('.inventory-block').find('[name="subcategory[]"]');
        if(category != undefined && category != '') {
            loadItemDetails(sel);
        }
    }

    //Delete
    function deleteItem(sel, hide) {
        $(sel).closest('.inventory-block').remove();
    }

    //Retrieve items on page load
    function loadItems() {
        var table = '<?= strtolower($_GET['category']) == 'vendor' ? 'vendor_price_list' : strtolower($_GET['category']) ?>';
        $.ajax({
            type: "GET",
            url: "ajax.php?fill=loadItems&table="+table,
            dataType: "json",
            success: function(response) {
                allItems = response;
            }
        });
    }

    //Filter items
    function filterItems(category, subcategory) {
        var items = allItems.filter(function (item) {
            var filtered_item = true;
            if(category != '' && category != item.category) {
                filtered_item = false;
            }
            if(subcategory != '' && subcategory != item.subcategory) {
                filtered_item = false;
            }
            return filtered_item;
        });
        return items;
    }

    //Create options
    function createItemOptions(items) {
        var html = '';
        items.forEach(function(item) {
            html += '<option data-category="'+item.category+'" data-subcategory="'+item.subcategory+'" value="'+item.value+'">'+item.label+'</option>';
        });
        return html;
    }

    //Load Item Details
    function loadItemDetails(sel) {
        var block = $(sel).closest('.inventory-block');
        var category = block.find('[name="category[]"]').val();
        var subcategory = block.find('[name="subcategory[]"]').val();
        if(subcategory == undefined) {
            subcategory = '';
        }
        var labourtype = block.find('[name="labourtype[]"]').val();
        var labourcategory = block.find('[name="labourcategory[]"]').val();
        var product_name = block.find('[name="inventory[]"]').val();
        var item_type = $('#item_type').val();

        var data = { category: category, subcategory: subcategory, labourtype: labourtype, labourcategory: labourcategory, product_name: product_name, item_type: item_type, customer: $(window.top.document).find('[name=businessid]').val() };

        block.find('.inventory-details').html('Loading...');

        $.ajax({
            type: "POST",
            url: "ajax.php?fill=loadItemDetails",
            data: data,
            dataType: "html",
            success: function(response) {
                destroyInputs();
                block.find('.inventory-details').show();
                block.find('.inventory-details').html(response);
                initInputs();
            }
        });
    }

    //Set Price
    function setPrice(sel, price = '') {
        var price = $(sel).closest('tr').find('[name="price[]"]');
        var price_set = $(sel).closest('td').text().trim();
        if($(sel).data('price') != undefined) {
            price_set = parseFloat($(sel).data('price'));
        }
        price.val(price_set);
        $(sel).prop('checked', false);
        updatePrice(sel);
    }

    //Update Price
    function updatePrice(sel) {
        if(parseFloat($(sel).val()) < 0) {
            $(sel).val(0.00);
        }
        var price = $(sel).closest('tr').find('[name="price[]"]').val();
        var inventoryid = $(sel).closest('tr').find('[name="inventoryid[]"]').val();
        inventoryid = inventoryid.split('*#*')[0]+'*#*'+inventoryid.split('*#*')[1]+'*#*'+inventoryid.split('*#*')[2];
        inventoryid = inventoryid+'*#*'+price;
        var time_estimate = $(sel).closest('tr').find('[name="time_estimate[]"]').val();
        if(time_estimate == undefined) {
            time_estimate = '';
        }
        inventoryid = inventoryid+'*#*'+time_estimate;
        $(sel).closest('tr').find('[name="inventoryid[]"]').val(inventoryid);

        if(price != '') {
            $(sel).closest('tr').find('[name="inventoryid[]"]').prop('checked', true);
        }
    }

    //Time Estimate
    function updateTime(sel) {
        var row = $(sel).closest('tr');
        var time = $(sel).val();
        var previous_time = $(sel).data('initial');
        $(sel).data('initial', $(sel).val())

        var price = row.find('[name="price[]"]').val();
        var minutes = time.split(':');
        minutes = (parseInt(minutes[0])*60) + parseInt(minutes[1]);
        var previous_minutes = previous_time.split(':');
        previous_minutes = (parseInt(previous_minutes[0])*60) + parseInt(previous_minutes[1]);

        if(previous_minutes > 0 && minutes > 0 && previous_minutes != minutes) {
            price = minutes / previous_minutes * price;
            row.find('[name="price[]"]').val(price);
            updatePrice(row.find('[name="price[]"]'));
        }
    }

    //Set Price to 0 if Empty
    function setEmptyPrice(sel) {
        var price = $(sel).closest('tr').find('[name="price[]"]');
        if (price.val() == '') {
            price.val('0.00');
            updatePrice(sel);
        }
    }

    //Labour Type
    function changeLabourType(sel) {
        var category_sel = $(sel).closest('.inventory-block').find('[name="labourcategory[]"]');
        var product_sel = $(sel).closest('.inventory-block').find('[name="inventory[]"]');
        category_sel.val('');
        product_sel.val('');

        if(sel.value != '') {
            category_sel.find('option').hide();
            if (category_sel.find('option[data-labourtype="'+sel.value+'"]').length > 0) {
                category_sel.closest('div').show();
                category_sel.find('option[data-labourtype="'+sel.value+'"]').show();
            } else {
                category_sel.closest('div').hide();
            }

            var items = filterLabourItems(sel.value, '');
            product_sel.closest('div').show();
            product_sel.find('option').not('.always_visible').remove();
            product_sel.append(createLabourItemOptions(items));

        } else {
            category_sel.closest('div').hide();
            product_sel.closest('div').hide();
        }

        category_sel.trigger('change.select2');
        product_sel.trigger('change.select2');

        changeItem(sel);
    }

    //Labour Category
    function changeLabourCategory(sel) {
        var labourtype = $(sel).closest('.inventory-block').find('[name="labourtype[]"]').val();
        var product_sel = $(sel).closest('.inventory-block').find('[name="inventory[]"]');
        product_sel.val('');

        product_sel.find('option').hide();
        product_sel.find('option[data-labourtype="'+labourtype+'"][data-labourcategory="'+sel.value+'"],option[value="**NEW_ITEM**"]').show();
        product_sel.trigger('change.select2');

        changeItem(sel);
    }

    //Labour Item
    function changeLabourItem(sel) {
        var labourtype = $(sel).closest('.inventory-block').find('[name="labourtype[]"]');
        var category = $(sel).closest('.inventory-block').find('[name="labourcategory[]"]');
        if(labourtype != undefined && labourtype != '') {
            loadItemDetails(sel);
        }
    }

    //Filter labour items
    function filterLabourItems(labour_type, category) {
        var items = allItems.filter(function (item) {
            var filtered_item = true;
            if(labour_type != '' && labour_type != item.labour_type) {
                filtered_item = false;
            }
            if(category != '' && category != item.category) {
                filtered_item = false;
            }
            return filtered_item;
        });
        return items;
    }

    //Create labour options
    function createLabourItemOptions(items) {
        var html = '';
        items.forEach(function(item) {
            html += '<option data-labourtype="'+item.labour_type+'" data-category="'+item.category+'" value="'+item.value+'">'+item.label+'</option>';
        });
        return html;
    }
</script>
</head>

<body><?php
    //Get values when iFrame opens
    if(isset($_GET['templateid'])) {
        $templateid = $_GET['templateid'];
    } else {
        $sotid    = $_GET['sotid'];
    }
    $category = strtolower($_GET['category']);
    $title    = $_GET['action'];
    $pricing  = $_GET['pricing'];
    $contact_category = $_GET['contact_category'];
    $table = ($category == 'vendor') ? 'vendor_price_list' : $category;
    $config_table = ( $category=='vendor' ) ? 'field_config_vpl' : 'field_config_'.$category;
    switch ($category) {
        case 'vendor':
            $item_type_title = 'VENDOR PRICE LIST';
            break;
        case 'inventory':
            $item_type_title = 'INVENTORY';
            break;
        case 'services':
            $item_type_title = 'SERVICE';
            break;
        case 'labour':
            $item_type_title = 'LABOUR';
            break;
        case 'equipment':
            $item_type_title = 'EQUIPMENT';
            break;
    } ?>

	<div class="container triple-gap-top"><?php
        //Form submit. There's a Javascript check before getting here.
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $contactid = $_SESSION['contactid'];
            $heading_name = filter_var($_POST['heading_name'],FILTER_SANITIZE_STRING);
            if($heading_name == 'new') {
                $heading_name = filter_var($_POST['heading_name_new'],FILTER_SANITIZE_STRING);
            }
            if(isset($_POST['mandatory_checkbox'])) {
                $mandatory_quantity = $_POST['mandatory_quantity'];
            } else {
                $mandatory_quantity = 0;
            }
            $item_type = trim($_POST['item_type']);
            switch($item_type) {
                case 'labour':
                    $table = 'labour';
                    $tableid = 'labourid';
                    break;
                case 'services':
                    $table = 'services';
                    $tableid = 'serviceid';
                    break;
                case 'vendor':
                    $table = 'vendor_price_list';
                    $tableid = 'inventoryid';
                    break;
                case 'inventory':
                    $table = 'inventory';
                    $tableid = 'inventoryid';
                    break;
                case 'equipment':
                    $table = 'equipment';
                    $tableid = 'equipmentid';
                    break;
            }
            $contact_category = $_POST['contact_category'];

            $history = '';
            $from_type = $_POST['from_type'];

            foreach ($_POST['new_item_category'] as $key => $new_item) {
                if($item_type == 'equipment') {
                    include('../Equipment/field_list.php');
                    $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT equipment_dashboard FROM field_config_equipment WHERE tab='$new_item' AND accordion IS NULL UNION SELECT equipment_dashboard FROM field_config_equipment WHERE `tab` NOT IN ('service_record','service_request') AND equipment_dashboard IS NOT NULL"));
                    $field_config = explode(',',$field_config['equipment_dashboard']);
                } else if($item_type == 'labour') {
                    include('../Labour/field_list.php');
                    $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `labour_dashboard` FROM `field_config` WHERE `labour_dashboard` IS NOT NULL AND `labour_dashboard` != ''"));
                    $field_config = explode(',', $field_config['labour_dashboard']);
                } else if($item_type == 'services') {
                    include('../Services/field_list.php');
                    $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services_dashboard` FROM `field_config` WHERE `fieldconfigid` = 1"));
                    $field_config = explode(',',$field_config['services_dashboard']);
                } else {
                    include('../Inventory/field_list.php');
                    $config_table = ( $item_type=='vendor' ) ? 'field_config_vpl' : 'field_config_'.$item_type;
                    $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `inventory_dashboard` FROM `$config_table` WHERE `tab` = '$new_item' AND `inventory_dashboard` IS NOT NULL && `inventory_dashboard` != ''"));
                    $field_config = explode(',', $field_config['inventory_dashboard']);
                }

                mysqli_query($dbc, "INSERT INTO `$table` VALUES ()");
                $inventoryid = mysqli_insert_id($dbc);

                $item_category = $new_item;
                $subcategory = $_POST['new_item_subcategory'][$key];
                $new_item_name = $_POST['new_item_name'][$key];
                $new_item_heading = $_POST['new_item_heading'][$key];
                $query_update = [];
                if($item_type == 'labour') {
                    $query_update[] = "`labour_type` = '$item_category'";
                } else {
                    $query_update[] = "`category` = '$item_category'";
                }
                if($item_type == 'equipment') {
                } else if($item_type == 'labour') {
                    $query_update[] = "`category` = '$subcategory'";
                    $query_update[] = "`heading` = '$new_item_heading'";
                } else if ($item_type != 'services') {
                    $query_update[] = "`sub_category` = '$subcategory'";
                    $query_update[] = "`name` = '$new_item_name'";
                } else {
                    $query_update[] = "`heading` = '$new_item_heading'";
                }
                foreach ($field_config as $field) {
                    $field_key = array_search($field,$field_list);
                    if ($field != 'Labour Type' && $field != 'Rate Card' && $field != 'Rate Card Price' && $field != 'Category' && $field != 'Subcategory' && $field != 'Name' && $field != 'Heading' && $field != 'Staff' && !empty($field_key)) {
                        $query_update[] = "`$field_key` = '".$_POST['new_item_'.$field_key][$key]."'";
                    }
                }
                $query_update = implode(',', $query_update);
                mysqli_query($dbc, "UPDATE `$table` SET $query_update WHERE `$tableid` = '$inventoryid'");
                echo "UPDATE `$table` SET $query_update WHERE `$tableid` = '$inventoryid'";
                $item = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `$table` WHERE `$tableid` = '$inventoryid'"));
                if($item_type == 'equipment') {
                    $item_value = get_equipment_label($dbc, $item);
                } else if($item_type == 'services') {
                    $item_value = $item['heading'];
                } else {
                    $item_value = '';
                    if(in_array('Size', $field_config) || in_array('Color', $field_config)) {
                        $item_value .= '(';
                        if(in_array('Color', $field_config)) {
                            $item_value .= $item['color'].', ';
                        }
                        if(in_array('Size', $field_config)) {
                            $item_value .= $item['size'];
                        }
                        $item_value .= ')';
                    }
                    $item_value = trim($item['name'].' '.$item_value);
                }

                $_POST['inventoryid'][] = $inventoryid.'*#*'.$item_category.'*#*'.htmlspecialchars($item_value).'*#*'.$_POST['new_price'][$key];
            }

            if($from_type == 'load_template') {
                $service_fields = ','.get_field_config($dbc,'services').',';
                $include_hours = false;
                if(strpos($service_fields, ',Estimated Hours,') !== false && $item_type == 'services') {
                    $include_hours = true;
                }
                if($_POST['heading_name'] == 'new') {
                    $heading_html = '<div class="heading_block" data-heading="'.$heading_name.'"><img src="'.WEBSITE_URL.'/img/icons/drag_handle.png" class="inline-img heading_handle pull-right no-toggle" title="Drag">';
                    $heading_html .= '<div class="heading_row">';
                    $heading_html .= '<div class="row heading_row_text"><div class="col-sm-12 default-color"><b>'.$heading_name.($mandatory_quantity > 0 ? '(Mandatory Quantity: '.$mandatory_quantity.')' : '').'</b> <a href="" onclick="editHeading(this); return false;"><span style="font-size: x-small; color: #888;">EDIT HEADING</span></a></div></div>';
                    $heading_html .= '<div class="heading_row_edit" style="display: none;">';
                    $heading_html .= '<div class="row">';
                    $heading_html .= '<div class="col-sm-3"><b>Heading Name</b></div>';
                    $heading_html .= '<div class="col-sm-2" style="text-align: center;"><b>Mandatory?</b></div>';
                    $heading_html .= '<div class="col-sm-3 mandatory_quantity" '.($mandatory_quantity == 0 ? 'style="display: none;"' : '').'><b>Mandatory Quantity</b></div>';
                    $heading_html .= '<div class="col-sm-2"></div>';
                    $heading_html .= '</div>';
                    $heading_html .= '<div class="row heading_details">';
                    $heading_html .= '<div class="col-sm-3"><input type="text" name="heading_name" value="'.$heading_name.'" class="form-control"></div>';
                    $heading_html .= '<div class="col-sm-2" style="text-align: center;"><input type="checkbox" name="mandatory_checkbox" value="1" '.($mandatory_quantity > 0 ? 'checked="checked"' : '').' style="transform: scale(1.5);" onclick="displayMandatoryQuantity(this);"></div>';
                    $heading_html .= '<div class="col-sm-3 mandatory_quantity" '.($mandatory_quantity == 0 ? 'style="display: none;"' : '').'><input type="number" name="mandatory_quantity" value="'.$mandatory_quantity.'" class="form-control"></div>';
                    $heading_html .= '<div class="col-sm-2"><button onclick="saveHeading(this); return false;" class="btn brand-btn" data-category="'.$_GET['category'].'" data-contact-category="'.$contact_category.'" data-heading-name="'.$heading_name.'">Save</button></div>';
                    $heading_html .= '</div></div></div>';
                    $heading_html .= '<table id="no-more-tables" class="table table-bordered table-striped product_table">';
                    $heading_html .= '<thead>';
                    $heading_html .= '<tr class="hidden-xs">';
                    $heading_html .= '<th width="20%">Category</th>';
                    $heading_html .= '<th width="'.($include_hours ? '30' : '40').'%">Product</th>';
                    if($include_hours) {
                        $heading_html .= '<th width="10%">Time Estimate</th>';
                    }
                    $heading_html .= '<th width="10%">Price</th>';
                    $heading_html .= '<th width="5%"></th>';
                    $heading_html .= '</tr>';
                    $heading_html .= '</thead>';
                    $heading_html .= '</table>';
                    $heading_html .= '</div>'; ?>
                    <script type="text/javascript">
                    $(function() {
                        window.parent.$('.accordion-block-details[data-contact-category="<?= $contact_category ?>"] .row[data-item-type="<?= $_GET['category'] ?>"]').append('<?= $heading_html ?>');
                    });
                    </script>
                <?php }
                for ( $i=0; $i<count($_POST['inventoryid']); $i++ ) {
                    $product       = $_POST['inventoryid'][$i];
                    $arr           = explode('*#*', $product);
                    $inventoryid   = filter_var($arr[0],FILTER_SANITIZE_STRING);
                    $item_category = filter_var($arr[1],FILTER_SANITIZE_STRING);
                    $item_name     = filter_var($arr[2],FILTER_SANITIZE_STRING);
                    $item_price    = filter_var($arr[3],FILTER_SANITIZE_STRING);
                    $time_estimate = filter_var($arr[4],FILTER_SANITIZE_STRING);
                    // $quantity      = $_POST['quantity'][$i];

                    if ( !empty($inventoryid) ) {
                        $tr = '<tr>';
                        $tr .= '<input type="hidden" class="product_headingname" name="product_headingname[]" value="'.$heading_name.'">';
                        $tr .= '<input type="hidden" class="product_mandatory" name="product_mandatory[]" value="'.$mandatory_quantity.'">';
                        $tr .= '<input type="hidden" name="product_id[]" value="">';
                        $tr .= '<input type="hidden" name="product_item_type[]" value="'.$item_type.'">';
                        $tr .= '<input type="hidden" name="product_item_type_id[]" value="'.$inventoryid.'">';
                        $tr .= '<input type="hidden" name="product_item_category[]" value="'.$item_category.'">';
                        $tr .= '<input type="hidden" name="product_item_name[]" value="'.$item_name.'">';
                        $tr .= '<input type="hidden" name="product_contact_category[]" value="'.$contact_category.'">';
                        $tr .= '<td data-title="Category">'.$item_category.'</td>';
                        $tr .= '<td data-title="Product">'.$item_name.'</td>';
                        if($include_hours) {
                            $tr .= '<td data-title="Time Estimate"><input type="text" name="time_estimate[]" class="timepicker form-control" value="'.$time_estimate.'"></td>';
                        } else {
                            $tr .= '<input type="hidden" name="time_estimate[]" value="">';
                        }
                        $tr .= '<td data-title="Price"><input type="number" name="product_price[]" value="'.$item_price.'" class="form-control" step="0.01"></td>';
                        $tr .= '<td align="center"><img src="'.WEBSITE_URL.'/img/remove.png" height="20" onclick="removeItem(this);" /><img src="'.WEBSITE_URL.'/img/icons/drag_handle.png" class="inline-img sortable_handle pull-right no-toggle" title="Drag"></td>';
                        $tr .= '</tr>'; ?>
                        <script type="text/javascript">
                        $(function() {
                            window.parent.$('.accordion-block-details[data-contact-category="<?= $contact_category ?>"] .row[data-item-type="<?= $_GET['category'] ?>"] .heading_block[data-heading="<?= $heading_name ?>"] table').append('<?= $tr ?>');
                            window.parent.sortableRows();
                        });
                        </script>
                    <?php }
                }
                echo '<script type="text/javascript">
                    $(function() {
                        window.parent.destroyInputs();
                        window.parent.initInputs();
                        window.parent.$(\'.close_iframer\').click();
                    });
                </script>';
            } else if($from_type == 'template') {
                $templateid = $_POST['templateid'];
                if (empty($templateid) || $templateid == 'new') {
                    mysqli_query($dbc, "INSERT INTO `sales_order_template` VALUES ()");
                    $templateid = mysqli_insert_id($dbc);
                }

                mysqli_query($dbc, "UPDATE `sales_order_template_product` SET `mandatory_quantity` = '$mandatory_quantity' WHERE `template_id` = '$templateid' AND `item_type` = '$item_type' AND `contact_category` = '$contact_category' AND `heading_name` = '$heading_name'");

                for ( $i=0; $i<count($_POST['inventoryid']); $i++ ) {
                    $product       = $_POST['inventoryid'][$i];
                    $arr           = explode('*#*', $product);
                    $inventoryid   = filter_var($arr[0],FILTER_SANITIZE_STRING);
                    $item_category = filter_var($arr[1],FILTER_SANITIZE_STRING);
                    $item_name     = filter_var($arr[2],FILTER_SANITIZE_STRING);
                    $item_price    = filter_var($arr[3],FILTER_SANITIZE_STRING);
                    $time_estimate = filter_var($arr[4],FILTER_SANITIZE_STRING);
                    // $quantity      = $_POST['quantity'][$i];

                    if ( !empty($inventoryid) ) {
                        $heading_sortorder = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`heading_sortorder`) `sort` FROM `sales_order_template_product` WHERE `heading_name` = '$heading_name' AND `item_type` = '$item_type' AND `template_id` = '$templateid' AND `contact_category` = '$contact_category'"))['sort'];
                        if($heading_sortorder == 0) {
                            $heading_sortorder = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`heading_sortorder`) `sort` FROM `sales_order_template_product` WHERE `item_type` = '$item_type' AND `template_id` = '$templateid' AND `contact_category` = '$contact_category'"))['sort'] + 1;
                        }
                        $sortorder = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`sortorder`) `sort` FROM `sales_order_template_product` WHERE `heading_name` = '$heading_name' AND `item_type` = '$item_type' AND `template_id` = '$templateid' AND `contact_category` = '$contact_category'"))['sort'] + 1;
                        $query_insert = "INSERT INTO `sales_order_template_product` (`template_id`, `item_type`, `item_type_id`, `item_category`, `item_name`, `item_price`, `contact_category`, `heading_name`, `mandatory_quantity`, `heading_sortorder`, `sortorder`, `time_estimate`) VALUES ('$templateid', '$item_type', '$inventoryid', '$item_category', '$item_name', '$item_price', '$contact_category', '$heading_name', '$mandatory_quantity', '$heading_sortorder', '$sortorder', '$time_estimate')";
                        $result = mysqli_query($dbc, $query_insert);
                    }
                }

                echo '<script>
                        alert("Items saved successfully.");
                        window.top.location.href = "'. WEBSITE_URL .'/Sales Order/templates.php?templateid='.$templateid.'";
                        window.parent.$("#templateid").val("'.$templateid.'");
                        window.parent.$("#save_template").click();
                    </script>';
            } else {
                $sotid = $_POST['sotid'];
                if (empty($sotid)) {
                    mysqli_query($dbc, "INSERT INTO `sales_order_temp` VALUES ()");
                    $sotid = mysqli_insert_id($dbc);
                }

                mysqli_query($dbc, "UPDATE `sales_order_product_temp` SET `mandatory_quantity` = '$mandatory_quantity' WHERE `parentsotid` = '$sotid' AND `item_type` = '$item_type' AND `contact_category` = '$contact_category' AND `heading_name` = '$heading_name'");

                for ( $i=0; $i<count($_POST['inventoryid']); $i++ ) {
                    $product       = $_POST['inventoryid'][$i];
                    $arr           = explode('*#*', $product);
                    $inventoryid   = filter_var($arr[0],FILTER_SANITIZE_STRING);
                    $item_category = filter_var($arr[1],FILTER_SANITIZE_STRING);
                    $item_name     = filter_var($arr[2],FILTER_SANITIZE_STRING);
                    $item_price    = filter_var($arr[3],FILTER_SANITIZE_STRING);
                    $time_estimate = filter_var($arr[4],FILTER_SANITIZE_STRING);
                    // $quantity      = $_POST['quantity'][$i];

                    if ( !empty($inventoryid) ) {
                        $heading_sortorder = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`heading_sortorder`) `sort` FROM `sales_order_product_temp` WHERE `heading_name` = '$heading_name' AND `item_type` = '$item_type' AND `parentsotid` = '$sotid' AND `contact_category` = '$contact_category'"))['sort'];
                        if($heading_sortorder == 0) {
                            $heading_sortorder = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`heading_sortorder`) `sort` FROM `sales_order_product_temp` WHERE `item_type` = '$item_type' AND `parentsotid` = '$sotid' AND `contact_category` = '$contact_category'"))['sort'] + 1;
                        }
                        $sortorder = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`sortorder`) `sort` FROM `sales_order_product_temp` WHERE `heading_name` = '$heading_name' AND `item_type` = '$item_type' AND `parentsotid` = '$sotid' AND `contact_category` = '$contact_category'"))['sort'] + 1;
                        $query_insert = "INSERT INTO `sales_order_product_temp` (`parentsotid`, `contactid`, `item_type`, `item_type_id`, `item_category`, `item_name`, `item_price`, `contact_category`, `heading_name`, `mandatory_quantity`, `heading_sortorder`, `sortorder`, `time_estimate`) VALUES ('$sotid', '$contactid', '$item_type', '$inventoryid', '$item_category', '$item_name', '$item_price', '$contact_category', '$heading_name', '$mandatory_quantity', '$heading_sortorder', '$sortorder', '$time_estimate')";
                        $result = mysqli_query($dbc, $query_insert);
                        $history .= 'Added Product to '.$contact_category.' from '.$item_type.' - '.$item_category.': '.$item_name.' with a price of '.$item_price.'<br />';
                    }
                }

                //History
                if($history != '') {
                    $historyid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`id`) FROM `sales_order_history` WHERE `sales_order_id`='$sotid' AND `contactid`='".$_SESSION['contactid']."' AND `date` >= '".date('Y-m-d H:i:s',strtotime('-15min'))."'"))[0];
                    if($historyid > 0) {
                        mysqli_query($dbc, "UPDATE `sales_order_history` SET `history`=CONCAT(`history`,'".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."') WHERE `id`='$historyid'");
                    } else {
                        mysqli_query($dbc, "INSERT INTO `sales_order_history` (`sales_order_id`, `history`, `contactid`) VALUES ('$sotid', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '".$_SESSION['contactid']."')");
                    }
                }

                $url_has = '#'.$contact_category.'_order';
                if($from_type == 'sot_iframe') {
                    echo '<script>
                            alert("Items saved successfully.");
                            window.top.parent.$("#sotid").val("'.$sotid.'");
                            window.parent.$("#sotid").val("'.$sotid.'");
                            window.parent.reloadItemData();
                            window.parent.$(".close_iframer").click();
                        </script>';
                } else {
                    echo '<script>
                            alert("Items saved successfully.");
                            window.top.location.href = "'. WEBSITE_URL .'/Sales Order/order.php?p=details&sotid='.$sotid.$url_hash.'";
                            window.parent.$("#sotid").val("'.$sotid.'");
                            window.parent.$("#save_order").click();
                        </script>';
                }
            }
        } ?>

        <form name="form1" id="form1" method="post" action="" class="form-horizontal">
            <?php if ($_GET['from_type'] == 'load_template') { ?>
                <input type="hidden" name="sotid" id="sotid" value="<?= $sotid ?>" />
                <input type="hidden" name="templateid" id="templateid" value="<?= $templateid ?>" />
                <input type="hidden" id="from_type" name="from_type" value="<?= $_GET['from_type'] ?>">
                <input type="hidden" id="load_type" name="load_type" value="<?= $_GET['load_type'] ?>">
            <?php } else if (isset($_GET['templateid'])) { ?>
                <input type="hidden" name="templateid" id="templateid" value="<?= $templateid ?>" />
                <input type="hidden" id="from_type" name="from_type" value="template">
            <?php } else if ($_GET['from_type'] == 'sot_iframe') { ?>
                <input type="hidden" name="sotid" id="sotid" value="<?= $sotid ?>" />
                <input type="hidden" id="from_type" name="from_type" value="<?= $_GET['from_type'] ?>">
            <?php } else { ?>
                <input type="hidden" name="sotid" id="sotid" value="<?= $sotid ?>" />
                <input type="hidden" id="from_type" name="from_type" value="sot">
            <?php } ?>
            <input type="hidden" name="pricing" id="pricing" value="<?= $pricing; ?>" />
            <input type="hidden" name="item_type" id="item_type" value="<?= $category; ?>" />
            <input type="hidden" name="contact_category" id="contact_category" value="<?= $contact_category ?>" />

            <div id="dialog-newproduct" title="Add New <?= $title ?>">
            </div>

            <div class="row">
                <div class="form-group">
                    <label class="control-label col-sm-4">Heading:</label>
                    <div class="col-sm-4">
                        <select data-placeholder="Choose a Heading..." id="heading_name" name="heading_name" class="chosen-select-deselect form-control heading_name">
                            <option></option>
                            <option value="new">New Heading</option>
                            <?php if(!empty($templateid)) {
                                $query = mysqli_query($dbc, "SELECT DISTINCT `heading_name` FROM `sales_order_template_product` WHERE `template_id` = '$templateid' AND `contact_category` = '$contact_category' AND `item_type` = '$category'");
                            } else {
                                $query = mysqli_query($dbc, "SELECT DISTINCT `heading_name` FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `contact_category` = '$contact_category' AND `item_type` = '$category'");
                            }
                            while ( $row=mysqli_fetch_array($query) ) { ?>
                                <option value="<?= $row['heading_name'] ?>"><?= $row['heading_name'] ?></option><?php
                            } ?>
                        </select>
                        <div class="clearifx"></div>
                        <input type="text" id="heading_name_new" name="heading_name_new" value="" class="form-control" style="display:none;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">Mandatory Quantity:</label>
                    <div class="col-sm-4">
                        <input type="checkbox" id="mandatory_checkbox" name="mandatory_checkbox" value="1" style="transform: scale(1.5); position: relative; top: 0.5em; left: 0.5em;" onchange="changeMandatory(this);">
                        <div class="clearifx"></div>
                        <input type="number" id="mandatory_quantity" name="mandatory_quantity" value="0" class="form-control" style="display:none; margin-top: 0.5em;">
                    </div>
                </div>
            </div>
            <hr>
            <div class="row inventory-block copy-inventory-block">
                <div id="inventory_0">
                    <input type="hidden" name="inventoryids[]" value="" />

                    <?php if($category == 'labour') { ?>
                        <div class="col-xs-12 col-sm-3">
                            <b>Labour Type:</b><br />
                            <select data-placeholder="Choose a Labour Type..." id="labourtype_0" name="labourtype[]" class="chosen-select-deselect form-control labourtype">
                                <option value=""></option><?php
                                $query = mysqli_query($dbc, "SELECT DISTINCT `labour_type` FROM `$table` WHERE `labour_type` != '' ORDER BY `labour_type`");
                                while ( $row=mysqli_fetch_array($query) ) { ?>
                                    <option id="<?= $row['labour_type']; ?>" value="<?= $row['labour_type']; ?>"><?= $row['labour_type']; ?></option><?php
                                } ?>
                            </select>
                        </div>
                    <?php } else { ?>
                        <div class="col-xs-12 col-sm-3">
                            <b>Category:</b><br />
                            <select data-placeholder="Choose a Category..." id="category_0" name="category[]" class="chosen-select-deselect form-control category">
                                <option value=""></option><?php
                                $query = mysqli_query($dbc, "SELECT DISTINCT `category` FROM `$table` WHERE `category` != '' ORDER BY `category`");
                                while ( $row=mysqli_fetch_array($query) ) { ?>
                                    <option id="<?= $row['category']; ?>" value="<?= $row['category']; ?>"><?= $row['category']; ?></option><?php
                                } ?>
                            </select>
                        </div>
                    <?php } ?>

                    <?php if($category == 'equipment') { ?>
                        <div class="col-xs-12 col-sm-5" style="display:none;">
                            <b>Equipment:</b><br />
                            <select data-placeholder="Choose an Equipment..." id="product_0" name="inventory[]" class="chosen-select-deselect form-control product labour">
                                <option class="always_visible" value=""></option>
                                <option class="always_visible" value="**NEW_ITEM**">NEW <?= $item_type_title ?></option>
                            </select>
                        </div>
                    <?php } else if($category == 'labour') { ?>
                        <div class="col-xs-12 col-sm-3" style="display:none;">
                            <b>Category:</b><br />
                            <select data-placeholder="Choose a Category..." id="labourcategory_0" name="labourcategory[]" class="chosen-select-deselect form-control labourcategory">
                                <option value=""></option><?php
                                $query = mysqli_query($dbc, "SELECT DISTINCT CONCAT(`labour_type`,`category`), `labour_type`, `category` FROM `$table` WHERE `category` != '' ORDER BY `category`");
                                while ( $row=mysqli_fetch_array($query) ) { ?>
                                    <option data-labourtype="<?= $row['labour_type'] ?>" value="<?= $row['category'] ?>"><?= $row['category'] ?></option><?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-5" style="display:none;">
                            <b>Labour:</b><br />
                            <select data-placeholder="Choose a Labour..." id="product_0" name="inventory[]" class="chosen-select-deselect form-control product labour">
                                <option class="always_visible" value=""></option>
                                <option class="always_visible" value="**NEW_ITEM**">NEW <?= $item_type_title ?></option>
                            </select>
                        </div>
                    <?php } else if ($category == 'services') { ?>
                        <div class="col-xs-12 col-sm-3" style="display:none;">
                            <b>Service Type:</b><br />
                            <select data-placeholder="Choose a Subcategory..." id="subcategory_0" name="subcategory[]" class="chosen-select-deselect form-control subcategory">
                                <option value=""></option><?php
                                $query = mysqli_query($dbc, "SELECT DISTINCT CONCAT(`category`,`service_type`), `category`, `service_type` FROM `$table` WHERE `service_type` != '' ORDER BY `service_type`");
                                while ( $row=mysqli_fetch_array($query) ) { ?>
                                    <option data-category="<?= $row['category'] ?>" value="<?= $row['service_type'] ?>"><?= $row['service_type'] ?></option><?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-5" style="display:none;">
                            <b>Services:</b><br />
                            <select data-placeholder="Choose a Service..." id="product_0" name="inventory[]" class="chosen-select-deselect form-control product">
                                <option class="always_visible" value=""></option>
                                <option class="always_visible" value="**NEW_ITEM**">NEW <?= $item_type_title ?></option>
                            </select>
                        </div>
                    <?php } else { ?>
                        <div class="col-xs-12 col-sm-3" style="display:none;">
                            <b>Subcategory:</b><br />
                            <select data-placeholder="Choose a Subcategory..." id="subcategory_0" name="subcategory[]" class="chosen-select-deselect form-control subcategory">
                                <option value=""></option><?php
                                $query = mysqli_query($dbc, "SELECT DISTINCT CONCAT(`category`,`sub_category`), `category`, `sub_category` FROM `$table` WHERE `sub_category` != '' ORDER BY `sub_category`");
                                while ( $row=mysqli_fetch_array($query) ) { ?>
                                    <option data-category="<?= $row['category'] ?>" value="<?= $row['sub_category'] ?>"><?= $row['sub_category'] ?></option><?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-5" style="display:none;">
                            <b>Products:</b><br />
                            <select data-placeholder="Choose a Product..." id="product_0" name="inventory[]" class="chosen-select-deselect form-control product">
                                <option class="always_visible" value=""></option>
                                <option class="always_visible" value="**NEW_ITEM**">NEW <?= $item_type_title ?></option>
                            </select>
                        </div>
                    <?php } ?>
                    <a href="javascript:void(0);" onclick="deleteItem(this,'inventory_'); return false;" id="delete_item_0" class="col-sm-1 double-gap-top pad-top-5"><img src="<?= WEBSITE_URL; ?>/img/remove.png" height="20" /></a>
                </div><!-- #inventory_0 -->
                <div class="clearfix"></div>
                <div id="inventory_details_0" class="inventory-details" style="display:none;"></div>
            </div><!-- .row -->

            <div id="add_new_item"></div>

            <div class="row"><div class="col-sm-12 text-right"><a href="javascript:void(0);" id="add_item"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" height="20" /></a></div></div>

            <div class="row double-gap-top">
                <div class="col-sm-12 text-right">
                    <a href="javascript:void(0);" id="cancel" class="btn brand-btn">Cancel</a>
                    <input type="submit" id="save" name="save" value="Save" class="btn brand-btn" />
                </div>
            </div>
        </form>

	</div><!-- .container -->

</body>
</html>
<?php //include ('../footer.php'); ?>