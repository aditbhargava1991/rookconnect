<!-- Products -->
<?php include_once('../Sales/config.php'); ?>
<script type="text/javascript">
$(document).on('change', '.prod_prod_onchange', selectProductProduct);
$(document).on('change', '.prod_cat_onchange', selectProductCat);
$(document).on('change', '[name="productid"]', selectProductHeading);

function selectProductProduct() {
    var line = $(this).closest('.row');
    line.find('[name=productid] option,.prod_cat_onchange option').hide();
    line.find('[name=productid] option[data-product-type="'+this.value+'"],.prod_cat_onchange option[data-product-type="'+this.value+'"]').show();
    line.find('[name=productid],.prod_cat_onchange').trigger('change.select2');
}

function selectProductCat() {
    var line = $(this).closest('.row');
    var cat_filter = $(this).find('option:selected').data('product-type');
    line.find('.prod_prod_onchange').val(cat_filter).trigger("change.select2");
    line.find('[name=productid] option').hide();
    line.find('[name=productid] option'+(this.value != '' ? '[data-category="'+this.value+'"]' : '')+(cat_filter != '' && cat_filter != undefined ? '[data-product-type="'+cat_filter+'"]' : '')).show();
    line.find('[name=productid]').trigger('change.select2');
}

function selectProductHeading() {
    var line = $(this).closest('.row');
    line.find('.prod_cat_onchange').val($(this).find('option:selected').data('category')).trigger("change.select2");
    line.find('.prod_prod_onchange').val($(this).find('option:selected').data('product-type')).trigger("change.select2");
}
</script>

<div class="accordion-block-details padded" id="products">
    <div class="accordion-block-details-heading"><h4>Products</h4></div>
    <?php if (strpos($value_config, ',Products Product Type,') !== false) { echo '<div class="col-sm-3 gap-md-left-15 gap-bottom"><b>Product Type</b></div>'; }
    if (strpos($value_config, ',Products Category,') !== false) { echo '<div class="col-sm-3 gap-md-left-15 gap-bottom"><b>Category</b></div>'; }
    if (strpos($value_config, ',Products Heading,') !== false) { echo '<div class="col-sm-3 gap-md-left-15 gap-bottom"><b>Heading</b></div>'; } ?>
    <div class="clearfix"></div><?php
    foreach(explode(',',$productid) as $product) {
        $product_type = get_field_value('product_type','products','productid',$product);
        $category = get_field_value('category','products','productid',$product); ?>
        <div class="row set-row-height"><?php
            if (strpos($value_config, ',Products Product Type,') !== false) { ?>
                <div class="col-sm-3 gap-md-left-15">
                    <select data-placeholder="Choose a Type..." class="chosen-select-deselect form-control prod_prod_onchange">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc,"SELECT DISTINCT(`product_type`) FROM `products` WHERE `deleted`=0 ORDER BY `product_type`");
                        while($row = mysqli_fetch_array($query)) {
                            $selected = ($product_type == $row['product_type']) ? 'selected="selected"' : '';
                            echo '<option '. $selected .' value="'. $row['product_type'] .'">'. $row['product_type'] .'</option>';
                        } ?>
                    </select>
                </div><?php
            } ?>
            <?php if (strpos($value_config, ',Products Category,') !== false) { ?>
                <div class="col-sm-3 gap-md-left-15">
                    <select data-placeholder="Choose a Category..." class="chosen-select-deselect form-control prod_cat_onchange">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc,"SELECT `category`, `product_type` FROM `products` WHERE `deleted`=0 GROUP BY `category`, `product_type` ORDER BY `category`");
                        while($row = mysqli_fetch_array($query)) {
                            $selected = ($category == $row['category']) ? 'selected="selected"' : '';
                            echo '<option '. $selected .' data-product-type="'.$row['product_type'].'" value="'. $row['category'] .'">'. $row['category'] .'</option>';
                        } ?>
                    </select>
                </div><?php
            } ?>
            <?php if (strpos($value_config, ',Products Heading,') !== false) { ?>
                <div class="col-sm-3 gap-md-left-15">
                    <select data-placeholder="Choose a Heading..." data-table="sales" data-concat="," name="productid" class="chosen-select-deselect form-control">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc,"SELECT `productid`, `heading`, `category`, `product_type` FROM `products` WHERE `deleted`=0 ORDER BY `heading`");
                        while($row = mysqli_fetch_array($query)) {
                            $selected = ($productid == $row['productid']) ? 'selected="selected"' : '';
                            echo '<option '. $selected .' data-product-type="'.$row['product_type'].'" data-category="'.$row['category'].'" value="'. $row['productid'] .'">'. $row['heading'] .'</option>';
                        } ?>
                    </select>
                </div><?php
            } ?>
            <div class="col-sm-1" >
                <img src="<?= WEBSITE_URL; ?>/img/remove.png" class="cursor-hand inline-img pull-right" onclick="rem_row(this);"/>
                <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" class="cursor-hand inline-img pull-right" onclick="add_row(this);"/>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php } ?>    
</div><!-- .accordion-block-details -->