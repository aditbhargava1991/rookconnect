<?php
/*
Dashboard
*/
include_once ('../include.php');
checkAuthorised('sales');
?>
<style>
    div#salesLead_list{
        padding: 5px;
    }
    div#salesLead_list label{
        vertical-align: bottom;
    }
</style>
<script>
$(document).ready(function() {
    $('#salesLead_list input[type="checkbox"]').click(function(){
        var status = ($(this).prop("checked")==true ? 1 : 0);
        var id = $(this).val();
        if(id!=''){
            $.ajax({
                url:"sales_ajax_all.php?action=field_config_sales_lead",
                method:"POST",
                data:{'id': id, 'status' : status},
                success:function(data)
                {
                    
                }
            });
        }
    });
});
</script>
<div class="gap-top">
    <div class="form-group">
        <div class="row">
            <?php
            $query   = mysqli_query ( $dbc, "SELECT `sales_lead_is_active`,`contactid`,`first_name`, `last_name` FROM `contacts` where `category` = 'Staff' and `first_name` != ''" );
            while($row = mysqli_fetch_array($query)) { 
            ?>
                <div class="col-sm-4 ui-state-default" id="salesLead_list">
                    <input class="is_active" id="contact-status-<?php echo $row['contactid'];?>" type="checkbox" value="<?php echo $row['contactid'];?>" <?php echo $row['sales_lead_is_active']==1 ? 'checked' : '';?> style="height: 20px; width: 20px;" name="sales[]">
                    <label for="contact-status-<?php echo $row['contactid'];?>"><?php echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']); ?></label>
                </div>
            <?php
            }?>
        </div>
    </div>
</div>