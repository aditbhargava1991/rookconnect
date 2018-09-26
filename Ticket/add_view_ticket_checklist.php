<script type="text/javascript">
$(document).ready(function() {
    $('.displayTable').parent().parent().each(function(){$(this).hide()});
    $("#add_checklist").off('click').on("click", function () {
        var task_other_name = $("#task_other").val();
        $("ul.add-task").append('<ul><li>'+task_other_name+'</li></ul>');
        $("#task_other").val('');
		var ticketid = $('#ticketid').val();
        var checklist = escape(task_other_name);

		if(checklist != '') {
			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "ticket_ajax_all.php?fill=gochecklist&checklist="+checklist+"&ticketid="+ticketid,
				dataType: "html",   //expect html to be returned
				success: function(response) {
					$("#checklist").val('');
                    $('.displayTable').parent().parent().each(function(){$(this).show()});
					$("#table2").append('<tr><td><input type="checkbox" class="rowLinkOther" value="'+task_other_name+'" name="travel_task[]"></td><td><label style="padding-left:20px" for="data_patient-satisfaction">'+task_other_name+'</label></td></tr>');
				}
			});
		}
		$("#task_other").focus();
        return false;
    });
});

function checkedItem(sel) {
    //if(this.checked) {
	<?php if($access_all == TRUE) { ?>
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "ticket_ajax_all.php?fill=checklistdone&ticketchecklistid="+sel.id,
            dataType: "html",   //expect html to be returned
            success: function(response){
                location.reload();
            }
        });
	<?php } else {
		echo "return false;";
	} ?>
    //}
}

</script>
<?php if(strpos($value_config, ',Details Checklist,') !== FALSE) { ?>
	<div class="col-md-12">

		  <div class="form-group">
			<label for="task_other" class="col-sm-4 control-label">Checklist of Items:</label>
			<?php if($access_all == TRUE) { ?>
				<div class="col-sm-7">
				  <input type="text" name="task_other" id="task_other" class="form-control" />
				</div>
				<div class="col-sm-1">
				  <img class="inline-img" id="add_checklist" title="Add To Checklist" data-history-label="Checklist Item" src="../img/icons/ROOK-add-icon.png">
				</div>
			<?php } ?>
		  </div>

			<?php
			$query_check_credentials = "SELECT * FROM ticket_checklist WHERE ticketid='$ticketid' AND `ticketid` > 0";
			$result = mysqli_query($dbc, $query_check_credentials);

				echo '<div class="col-sm-offset-4 col-sm-8 double-pad-top triple-pad-bottom">
				<div class="col-sm-6">
				  <table id="table2" class="displayTable pad-top">
					<tbody>';

				while($row = mysqli_fetch_array($result)) {
					$ticketchecklistid = $row['ticketchecklistid'];
					$key = $row['checklist'];
					$done = $row['checked'];
					if($key != '') {
						$disable = '';
						$checked = '';
						$style = '';
						if($done == 1) {
							$disable = ' disabled';
							$checked = ' checked';
							$style='text-decoration:line-through; padding-left:20px;';
						} else {
							$style='padding-left:20px;';
						}
						echo "<tr><td><input onchange='checkedItem(this)' type='checkbox' ".$disable." ".$checked." id='".$ticketchecklistid."' class='rowLinkOther' value='".$key."'></td><td><label for='data_patient-satisfaction' class='cl-text' style='".$style."' >".$key."</label> </td></tr>
						";
					}
				}
				echo '</tbody>
				  </table>
				</div>
				</div>
				';
				 ?>


		  <div class="col-sm-offset-4 col-sm-8 double-pad-top triple-pad-bottom">
			<div class="col-sm-6">
			  <table id="table2" class="displayTable pad-top">
				<tbody></tbody>
			  </table>
			</div>
			</div>

		<!--<div class="form-group">
			<div class="col-sm-4">
				<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
			</div>
			<div class="col-sm-8">
				<span class="popover-examples list-inline pull-right" style="margin-left:-6em;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
					<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed." class="btn brand-btn">Submit</button></a></span>
				<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed." class="btn brand-btn pull-right">Submit</button>
			</div>
		</div>-->
	</div>
<?php } ?>
