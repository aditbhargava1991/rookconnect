<?php
/*
Dashboard
*/
include_once ('../include.php');
checkAuthorised('sales');

?>
<script>
$(document).ready(function() {
	$('input,select,textarea').change(saveFields);
});

function saveFields() {
	var this_field_name = this.name;
	var ticket_fields = [];
	$('[name="sales[]"]:checked').not(':disabled').each(function() {
		ticket_fields.push(this.value);
	});
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: 'sales_ajax_all.php?action=setting_fields&ticket_fields='+ticket_fields,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
</script>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <div class="gap-top">

        <h4>Choose Fields for Pipeline Sub Tab</h4>
        <div class="form-group">
            <?php
    			// Get Lead Statuses added in Settings->Lead Status accordion
    			$statuses = get_config ( $dbc, 'sales_lead_status' );

    			// Check if the array is empty and remove empty values
    			foreach ( $statuses as $key => $value ) {
    				if ( empty ( $value ) ) {
    				   unset ( $statuses[$key] );
    				}
    			}

    			$each_status	= explode ( ',', $statuses );
    			$count			= count( $each_status );
    		?>

            <table border="2" cellpadding="10" class="table">
                <?php
    				if ( $count>0 && !empty($statuses) ) {
    					for ( $i=0; $i<$count; $i++ ) {
    						echo ( $i%4 == 0 ) ? '<tr>' : '';
    							$checked = ( strpos ( $value_config, ',' . $each_status[$i] . ',' ) !== FALSE ) ? ' checked' : '';
    							echo '<td><input type="checkbox"' . $checked . ' value="' . $each_status[$i] . '" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;' . $each_status[$i];
    						echo ( $i%4 == 3 ) ? '</tr>' : '';
    					}
    				} else {
    					echo 'Please add the desired Lead Statuses first in Lead Status accordion below.';
    				}
    			?>
            </table>
        </div>

        <hr>

        <h4>Choose Fields for Schedule Sub Tab</h4>
        <div class="form-group">
            <table border="2" cellpadding="10" class="table">
                <tr>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','.'Today'.',') !== FALSE) { echo ' checked'; } ?> value="Today" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Today
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','.'This Week'.',') !== FALSE) { echo ' checked'; } ?> value="This Week" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;This Week
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','.'This Month'.',') !== FALSE) { echo ' checked'; } ?> value="This Month" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;This Month
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','.'Custom'.',') !== FALSE) { echo ' checked'; } ?> value="Custom" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Custom
                    </td>
                </tr>
            </table>
        </div>

    </div>
</form>