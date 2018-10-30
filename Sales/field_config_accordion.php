<?php
/*
Dashboard
*/
include_once ('../include.php');
checkAuthorised('sales');
?>
<script>
$(document).ready(function(){
    $("#selectall").change(function(){
      $(".all_check").prop('checked', $(this).prop("checked"));
    });
	$('input,select,textarea').change(saveFields);
});

function saveFields() {
	var this_field_name = this.name;
	var accordion = [];
	$('[name="sales[]"]:checked').not(':disabled').each(function() {
		accordion.push(this.value);
	});
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: 'sales_ajax_all.php?action=setting_fields&ticket_fields='+accordion,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}

</script>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <div class="gap-top">

        <!--<h4>Pipeline &amp; Schedule Accordions</h4>-->
        <div class="form-group">
            <input type="checkbox" id="selectall"/> Select All
    		<div id='no-more-tables'>
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Sales Path".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Sales Path" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;<?= SALES_NOUN ?> Path
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Staff Information".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Staff Information" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Staff Information
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Lead Information".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Lead Information" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Information
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Service".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Service" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Service
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Products".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Products" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Products
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Lead Source".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Lead Source" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Source
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Reference Documents".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Reference Documents" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Reference Documents
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Marketing Material".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Marketing Material" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Marketing Material
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Information Gathering".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Information Gathering" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Information Gathering
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Estimate".',') !== FALSE) { echo " checked"; } ?> value="Estimate" class="all_check" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Estimate
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Quote".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Quote" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Quote
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Next Action".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Next Action" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Next Action
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Lead Notes".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Lead Notes" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Notes
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Lead Status".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Lead Status" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Status
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tasks".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Tasks" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Tasks
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Deliverable".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Deliverable" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Deliverable
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Time".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Time" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Time Tracking
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."History".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="History" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;History
                        </td>
                    </tr>

                </table>
    	    </div>
        </div>

        <hr>

        <!--<h4><?= SALES_TILE ?> Path Accordion</h4>
        <div class="form-group">
            <input type="checkbox" <?php if (strpos($value_config, ','."Sales Lead Path Intake".',') !== FALSE) { echo " checked"; } ?> value="Sales Lead Path Intake" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Intake Forms&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Sales Lead Path Checklists".',') !== FALSE) { echo " checked"; } ?> value="Sales Lead Path Checklists" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Checklists&nbsp;&nbsp;
        </div>

        <hr>

        <h4>Lead Information Accordion</h4>
        <div class="form-group">
            <input type="checkbox" <?php if (strpos($value_config, ','."Lead Information Lead Value".',') !== FALSE) { echo " checked"; } ?> value="Lead Information Lead Value" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Value&nbsp;&nbsp;
        </div>

        <hr>-->

        <h4>Services Fields</h4>
        <div class="form-group">
            <input type="checkbox" <?php if (strpos($value_config, ','."Services Service Type".',') !== FALSE) { echo " checked"; } ?> value="Services Service Type" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Services Category".',') !== FALSE) { echo " checked"; } ?> value="Services Category" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Services Heading".',') !== FALSE) { echo " checked"; } ?> value="Services Heading" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;
        </div>

        <hr>

        <h4>Products Fields</h4>
        <div class="form-group">
            <input type="checkbox" <?php if (strpos($value_config, ','."Products Product Type".',') !== FALSE) { echo " checked"; } ?> value="Products Product Type" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Product Type&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Products Category".',') !== FALSE) { echo " checked"; } ?> value="Products Category" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Products Heading".',') !== FALSE) { echo " checked"; } ?> value="Products Heading" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;
        </div>

        <hr>

        <h4>Marketing Material Fields</h4>
        <div class="form-group">
            <input type="checkbox" <?php if (strpos($value_config, ','."Marketing Material Material Type".',') !== FALSE) { echo " checked"; } ?> value="Marketing Material Material Type" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Material Type&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Marketing Material Category".',') !== FALSE) { echo " checked"; } ?> value="Marketing Material Category" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Marketing Material Heading".',') !== FALSE) { echo " checked"; } ?> value="Marketing Material Heading" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;
        </div>

    </div>
</form>