<?php include_once('config.php'); ?>
<script>
$(document).ready(function() {
    $('.has-main-screen .main-screen').scroll(function() {
        $('.tile-sidebar li').removeClass('active');
        var top = $('.has-main-screen .main-screen').offset().top + 50;
        var bottom = top + $('.has-main-screen .main-screen').outerHeight() - 65;
        $('.has-main-screen .main-screen div[id]').each(function() {
            if(($(this).offset().top > top && $(this).offset().top < bottom) || ($(this).offset().top + $(this).outerHeight() < bottom && $(this).offset().top + $(this).outerHeight() > top)) {
                $('.tile-sidebar a[href=#'+this.id+'] li').addClass('active');
            }
        });
    }).scroll();
});
</script>
<a href="#inv_details"><li>Details</li></a>
<?php if(in_array('services',$field_config) || in_array('unbilled_tickets',$field_config)) { ?>
    <a href="#inv_services"><li>Services</li></a>
<?php } ?>
<?php if(in_array('inventory',$field_config)) { ?>
    <a href="#inv_inventory"><li><?= INVENTORY_TILE ?></li></a>
<?php } ?>
<?php if(in_array('products',$field_config)) { ?>
    <a href="#inv_products"><li>Products</li></a>
<?php } ?>
<?php if(in_array('packages',$field_config)) { ?>
    <a href="#inv_packages"><li>Packages</li></a>
<?php } ?>
<?php if(in_array('misc_items',$field_config) || in_array('unbilled_tickets',$field_config)) { ?>
    <a href="#inv_misc"><li>Miscellaneous</li></a>
<?php } ?>
<?php if(in_array('unbilled_tickets',$field_config)) { ?>
    <a href="#inv_tickets"><li><?= TICKET_TILE ?></li></a>
<?php } ?>
<a href="#inv_summary"><li>Summary</li></a>