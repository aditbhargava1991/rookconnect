<div id="inv_summary">
    <h3>Summary</h3>
</div>
<script>
$(document).ready(function() {
	$('.form-control').change(function() {
		<?php if($patient != '') { ?>
			$('.detail_patient_name').html('<?= $patient ?>');
			$('.detail_patient_injury').html('<?= $injury ?>');
			if($('[name=treatment_plan]').is(':visible')) {
				$('.detail_patient_treatment').html('<?= $treatment_plan ?>').closest('h4').show();
			} else {
				$('.detail_patient_treatment').closest('h4').hide();
			}
			$('.detail_staff_name ').html('<?= $staff ?>');
		<?php } else { ?>
			if($('.non_patient_fields').is(':visible')) {
				$('.detail_patient_name').html($('[name=first_name]').val() + ' ' + $('[name=last_name]').val());
				$('.detail_patient_injury').closest('h4').hide();
			} else {
				$('.detail_patient_name').html($('[name=patientid] option:selected').text());
                if($('select[name=patientid]').val() > 0) {
                    $('#header_summary').load('../Contacts/contact_profile.php?summary=true&contactid='+$('select[name=patientid]').val());
                }
				if($('#injuryid_chosen').is(':visible')) {
					$('.detail_patient_injury').html($('[name=injuryid] option:selected').text() == '' ? 'Please Select' : $('[name=injuryid] option:selected').text()).closest('h4').show();
				}
			}
			if($('[name=treatment_plan]').is(':visible')) {
				$('.detail_patient_treatment').html($('[name=treatment_plan] option:selected').text()).closest('h4').show();
			} else {
				$('.detail_patient_treatment').closest('h4').hide();
			}
			$('.detail_staff_name ').html($('[name=therapistsid] option:selected').text() == '' ? 'N/A' : $('[name=therapistsid] option:selected').text());
		<?php } ?>
		$('.detail_promo_amt ').html($('[name=promotionid] option:selected').text() == '' ? 'N/A' : $('[name=promotionid] option:selected').text());
		if($('#paid_status').val() != '' && $('#paid_status').val() != 'Saved' && $('#paid_status').val() != 'Waiting on Insurer') {
			$('.detail_patient_amt').closest('h4').show();
		} else {
			$('.detail_patient_amt').closest('h4').hide();
		}
		if($('#paid_status').val() == 'No' || $('#paid_status').val() == 'Waiting on Insurer') {
			$('.detail_insurer_amt').closest('h4').show();
		} else {
			$('.detail_insurer_amt').closest('h4').hide();
		}
		$('[name="serviceid[]"]').each(function() {
			var label = $(this).find('option:selected').text();
			var fee = $(this).closest('.form-group').find('[name="fee[]"]').val();
		});
	});
	$('.form-control').first().change();
	<?php if($paid != '') {
		echo "pay_mode_selected('$paid');\n";
		if($paid == 'No' || $paid == 'Waiting on Insurer') {
			echo "var service_ins = '".$get_invoice['service_insurer']."';\n";
			echo "var inv_ins = '".$get_invoice['inventory_insurer']."';\n";
			echo "var package_ins = '".$get_invoice['package_insurer']."';\n";
		} else {
			echo "var service_ins = '0:0';\n";
			echo "var inv_ins = '0:0';\n";
			echo "var package_ins = '0:0';\n";
		}
	} else {
		echo "var service_ins = '0:0';\n";
		echo "var inv_ins = '0:0';\n";
		echo "var package_ins = '0:0';\n";
	} ?>

	var i = 1;
	$(service_ins.split(',')).each(function() {
		var j = 0;
		$(this.split('#*#')).each(function() {
			var info = this.split(':');
			var target = $('.service_option').find('.form-group').eq(i).find('.pay-div').eq(j);
			target.find('[name="insurerid[]"]').val(info[0]).trigger('change.select2');
			target.find('[name="insurer_payment_amt[]"]').val(info[1]);
			j++;
		});
		i++;
	});
	var i = 1;
	$(inv_ins.split(',')).each(function() {
		var j = 0;
		$(this.split('#*#')).each(function() {
			var info = this.split(':');
			var target = $('.product_option').find('.form-group').eq(i).find('.pay-div').eq(j);
			target.find('[name="insurerid[]"]').val(info[0]).trigger('change.select2');
			target.find('[name="insurer_payment_amt[]"]').val(info[1]);
			j++;
		});
		i++;
	});
	var i = 1;
	$(package_ins.split(',')).each(function() {
		var j = 0;
		$(this.split('#*#')).each(function() {
			var info = this.split(':');
			var target = $('.package_option').find('.form-group').eq(i).find('.pay-div').eq(j);
			target.find('[name="insurerid[]"]').val(info[0]).trigger('change.select2');
			target.find('[name="insurer_payment_amt[]"]').val(info[1]);
			j++;
		});
		i++;
	});
	countTotalPrice();
});
$(document).on('change', 'select[name="app_type"]', function() { changeApptType(this.value); });
$(document).on('change', 'select[name="pricing"]', function() {
    if ($('[name="pricing"] option:selected').val()=='admin_price') {
        $('[name="unit_price[]"]').attr('readonly', false);
    } else {
        $('[name="unit_price[]"]').attr('readonly', true);
    }
    updatePricing();
});
$(document).on('change', '[name="unit_price[]"]', function() {
    adminPrice(this);
});
$(document).on('change', 'select[name="linepricing[]"]', function() {
    var rowid = this.id.split('_')[1];
    if ($('#linepricing_'+rowid+' option:selected').val()=='admin_price') {
        $('#unitprice_'+rowid).attr('readonly', false);
    } else {
        $('#unitprice_'+rowid).attr('readonly', true);
    }
    updatePricing();
});
$(document).on('change', 'select[name="paid"]', function() { pay_mode_selected(this.value); });
$(document).on('change', 'select.service_category_onchange', function() { changeCategory(this); });
$(document).on('change', 'select[name="serviceid[]"]', function() { changeService(this); });
$(document).on('change', '[name="fee[]"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="inventorycat[]"]', function() { filterInventory(this); });
$(document).on('change', 'select[name="inventorypart[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="inventoryid[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="invtype[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="packagecat[]"]', function() { changePackage(this); });
$(document).on('change', 'select[name="packageid[]"]', function() { changePackage(this); });
$(document).on('change', 'select[name="promotionid"]', function() { changePromotion(this); });
$(document).on('change', 'select[name="delivery_type"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="contractorid"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="payment_type[]"]', function() { set_patient_payment_row(); });

function pay_mode_selected(paid) {
	if(paid == 'No' || paid == 'Waiting on Insurer') {
		if($('.pay-div').html() == '') {
			$('.pay-div').html('<div class="insurer_line"><label class="col-sm-2 control-label"><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Name:</label>'+
				'<div class="col-sm-4"><select name="insurerid[]" class="chosen-select-deselect form-control" width="380">'+
                    '<option value=""></option><?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category IN ('".implode("','",$payer_config)."') AND deleted=0 ORDER BY name"),MYSQLI_ASSOC));
					foreach($query as $row) {
						echo '<option value="'. $row.'">'.htmlentities(get_client($dbc, $row), ENT_QUOTES).'</option>';
					}
					?></select></div>'+
				'<label class="col-sm-2 control-label"><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Portion: <span class="popover-examples list-inline">'+
					'<a href="#job_file" data-toggle="tooltip" data-placement="top" title="The portion that the <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> will pay."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span></label>'+
				'<div class="col-sm-2"><input type="number" step="any" name="insurer_payment_amt[]" class="form-control" value="0" onchange="countTotalPrice();">'+
					'<input type="hidden" name="insurer_row_applied[]" value=""></div>'+
				'<div class="col-sm-2"><img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_insurer_row(this);">'+
					'<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_insurer_row(this);"></div></div>');
			$('[name="insurerid[]"]').select2({
                width: '100%'
            });
			$('.pay-div').each(function() {
				$(this).find('[name="insurer_row_applied[]"]').val($(this).closest('.form-group').find('.insurer_row_id').val());
			});
		}
		if(paid == 'Waiting on Insurer') {
			$('[name="serviceid[]"]').change();
			$('[name="quantity[]"]').change();
			$('[name="packageid[]"]').change();
			$('[name="misc_qty[]"]').change();
		}
	} else {
		$('.pay-div').empty();
	}
	$('[name=paid]').val(paid).trigger('change.select2');
	$('[name="payment_price[]"]').last().attr('readonly','readonly');
	countTotalPrice();
}

var clone = $('.book-validate-cal').clone();
clone.find('.datetimepicker').val('');
clone.find('.datetimepicker').each(function() {
$(this).removeAttr('id').removeClass('hasDatepicker');
	$('.datetimepicker').datetimepicker({
		controlType: 'select',
		changeMonth: true,
		changeYear: true,
		yearRange: '<?= date('Y') - 10 ?>:<?= date('Y') + 5 ?>',
		dateFormat: 'yy-mm-dd',
		timeFormat: "hh:mm tt",
		minuteGrid: 15,
		hourMin: 6,
		hourMax: 20,
		//minDate: 0
	});
});
function addmore()
{
	var classname = $('.book-calendar [class^=book_]').last().attr('class');
	var classes = classname.split("_");
	var value = parseInt(classes[1]) + 1;
	var currentclass = 'book_' + value;

	var insertstring = '<div class="'+ currentclass +'">'+
							'<span class="col-sm-3">'+
								'<input name="block_appoint_date[]" id="appointdate_'+value+'" type="text" placeholder="Click for Datepicker" class="datetimepicker form-control"></p>'+
							'</span>'+
							'<span class="col-sm-3">'+
								'<input name="block_end_appoint_date[]" id="endappointdate_'+value+'" type="text" placeholder="Click for Datepicker" class="datetimepicker form-control"></p>'+
							'</span>'+
							'<span class="col-sm-5">'+
								'<select data-placeholder="Select a Type..." id="appointtype_'+value+'" name="appointtype[]" class="chosen-select-deselect form-control input-sm"><option value=""></option>'+
								'<option value="A">Private-PT-Assessment</option>'+
							'<option value="B">Private-PT-Treatment</option>'+
							'<option value="C">MVC-IN-PT-Assessment</option>'+
							'<option value="D">MVC-IN-PT-Treatment</option>'+
							'<option value="F">MVC-OUT-PT-Assessment</option>'+
							'<option value="G">MVC-OUT-PT-Treatment</option>'+
							'<option value="H">WCB-PT-Assessment</option>'+
							'<option value="J">WCB-PT-Treatment</option>'+
							'<option value="K">Private-MT</option>'+
							'<option value="L">MVC-IN-MT</option>'+
							'<option value="M">MVC-OUT-MT</option>'+
							'<option value="N">AHS-PT-Assessment</option>'+
							'<option value="O">AHS-PT-Treatment</option>'+
							'<option value="S">Reassessment</option>'+
							'<option value="T">Post-Reassessment</option>'+
							'<option value="U">Private-MT-Assessment</option>'+
							'<option value="V">Orthotics</option>'+
							'</select></p>'+
							'</span>'+
							'<span class="col-sm-1">'+
							'<img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="removeclass(this);" title="Remove this Row">'+
							'<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="addmore();" title="Add Additional Appointment">'+
							'</span><div class="clearfix"></div>'+
						'</div>';
	jQuery(insertstring).insertAfter('.' + classname);
	resetChosen($('.'+currentclass).find('.chosen-select-deselect'));
	var clone = $('.book-validate-cal').clone();
	clone.find('.datetimepicker').val('');
	clone.find('.datetimepicker').each(function() {
		$(this).removeAttr('id').removeClass('hasDatepicker');
		$('.datetimepicker').datetimepicker({
			controlType: 'select',
			changeMonth: true,
			changeYear: true,
			yearRange: '<?= date('Y') - 10 ?>:<?= date('Y') + 5 ?>',
			dateFormat: 'yy-mm-dd',
			timeFormat: "hh:mm tt",
			minuteGrid: 15,
			hourMin: 7,
			hourMax: 19
		});
	});
}

function removeclass(remove)
{
	if($('[class^=book_]').length == 1) {
		addmore();
	}
	$(remove).closest('[class^=book_]').remove();
}

function validateappo()
{
	if(jQuery("input[name='next_appointment']:checked").val() == 'Yes' || jQuery("input[name='next_appointment']:checked").val() == 'yes')
	{
		var count = 0;
		//alert(jQuery(".book-validate-cal > div"));
		var therapiststemid = "<?php echo $get_invoice['therapistsid']; ?>";
		jQuery(".book-validate-cal").children().each(function(n, i) {
			if(typeof this.className !== 'object') {
				var classname = this.className;
				var splitclass = classname.split("_");
				var i = parseInt(splitclass[1]);
				var appdate = jQuery('#appointdate_' + i).val();
				var endappdate = jQuery('#endappointdate_' + i).val();
				$.ajax({
				  type: "GET",
				  url: "../Invoice/appointment_ajax.php",
				  data: 'appdate=' + appdate + '&endappdate=' + endappdate + '&therapistid=' + therapiststemid,
				  cache: false,
				  success: function(data){
					  if(data == 1) {
						  jQuery('#appointdate_' + i)
						  jQuery('#appointdate_' + i).addClass('borderClass');
						  jQuery('#endappointdate_' + i).addClass('borderClass');
						  count = 1;
					  }
					  else {
						  jQuery('#appointdate_' + i)
						  jQuery('#appointdate_' + i).removeClass('borderClass');
						  jQuery('#endappointdate_' + i).removeClass('borderClass');
					  }

				  },

				  error: function(data) {
					  alert("Something Wrong in Appointment");
				  },

				  async:false
				});
			}
		});

		if(count > 0) {
			alert("There are some clashes in Appointment dates marked with Red Border");
			return false;
		}

		return true;
	}
}

function billTicket(input) {
	var block = $(input).closest('label');
	if(input.checked) {
		block.find('[disabled]').removeAttr('disabled');
	} else {
		block.find('[type=hidden]').prop('disabled',true);
	}
	setTotalPrice();
}
</script>