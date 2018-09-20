<?php
/*
Dashboard
*/
include_once ('../include.php');
checkAuthorised('sales');
?>
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    ul#salesTab_list{
        list-style: none;
    }
    ul#salesTab_list li{
        padding: 10px;
        margin: 5px;
    }
    ul#salesTab_list li i{
        padding-right: 10px;
    }
</style>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(document).ready(function() {
	$( "#salesTab_list" ).sortable({
        axis: 'y',
        update  : function(event, ui)
        {
            var tabOrderArray = [];
            $('#salesTab_list li').each(function(){
                tabOrderArray.push({"title": $(this).attr('data-title'), "segment": $(this).attr('data-segment')});
            });
            if(tabOrderArray!=''){
                var storeOrderObj = JSON.stringify(tabOrderArray);
                $.ajax({
                    url:"sales_ajax_all.php?action=sales_field_config_tab_order",
                    method:"POST",
                    data:{tab_order_array:storeOrderObj},
                    success:function(data)
                    {
                        
                    }
                });
            }
        }
    }).disableSelection();
});
</script>
<div class="gap-top">
    <div class="form-group">
        <label for="company_name" class="col-sm-2 col-sm-offset-2 control-label">Manage Positions:</label>
        <div class="col-sm-5">
            <ul id="salesTab_list">
                <?php
                $get_field_config_tabs_order   = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `value` FROM `general_configuration` where `name` = 'sales_sub_tabs_order'" ) );
                $tab_order = stripslashes(html_entity_decode($get_field_config_tabs_order['value']));
                $tab_order = json_decode($tab_order, true);
                foreach ($tab_order as $key => $value) { 
                ?>
                    <li class="ui-state-default" data-title="<?php echo $value['title'];?>" data-segment="<?php echo $value['segment'];?>">
                        <i class="fa fa-arrows"></i><?php echo $value['title'];?>
                    </li>
                <?php
                }?>
            </ul>
        </div>
    </div>
</div>