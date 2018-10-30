var inc = 1;
var inc_pro = 1;
var inc_pay = 1;
var inc_pack = 1;
var inc_misc = 1;
var previous_payment = 0;
var sum_refund = 0;
var sum_adjustment = 0;
var submit_mode = true;

$(document).ready(function() {
	$('[name="ticketid[]"]').change();
	countTotalPrice();
	changeApptType($('[name="app_type"]').val());

	$("#insurance_payment").hide();

	$( "#printpdf" ).click(function() {
		if($("#search_user").val() == '') {
			alert("Select a customer from the dropdown, then click Search to view the item(s) you can Invoice to the Insurer.");
			return false;
		}
		if($("#insurerid").val() == '') {
			alert("You must select an Insurer before the report can print.");
			return false;
		}
		if($("[type='checkbox']:checked").length == 0) {
			alert("Click on the checkbox at the end of each row to select the item(s) you wish to print, then click the Print button.");
			return false;
		}
	});

	$("#injuryid").change(function() {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "../ajax_all.php?fill=invoice&injuryid="+this.value,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$(".mva_claim_price").text(response);
			}
		});
	});

	$("[name=patientid]").change(function() {
		var type = '';
		if($('[name="type"]').val() != undefined) {
			type = $('[name="type"]').val();
		}
		if ($(this).val()=='NEW') {
            overlayIFrameSlider('add_contact.php?type='+type, '50%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20);
        }
	});

	var patientid = getParameterByName('contactid');

	$(".next_appointment_fields").hide();
	$(".next_appointment").change(function(){
			if($(this).val() == 'Yes') {
				$(".next_appointment_fields").show();
			} else {
				$(".next_appointment_fields").hide();
			}
	});

	$(".patient_type_fields").show();
	$(".non_patient_fields").hide();
	$(".patient_type").change(function(){
			if($(this).val() == 'Patient') {
				$(".patient_type_fields").show();
				$(".non_patient_fields").hide().find('.form-control').change();
			} else {
				$(".patient_type_fields").hide();
				$(".non_patient_fields").show().find('.form-control').change();
			}
	});

	$(".block_booking_display").hide();
	$(".normal_booking_btn").hide();

	$(".block_booking_btn").click(function() {
		$(".block_booking_display").show();
		$(".normal_booking_display").hide();
		$(".normal_booking_btn").show();
		$(".block_booking_btn").hide();
	});

	$(".normal_booking_btn").click(function() {
		$(".block_booking_display").hide();
		$(".normal_booking_display").show();
		$(".block_booking_btn").show();
		$(".normal_booking_btn").hide();
	});

	$('#add_row_booking').on( 'click', function () {
		var clone = $('.additional_booking').clone();

		clone.find('.datetimepicker').val('');
		clone.find('.datetimepicker').val('');
		var numItems = ($('.datetimepicker').length/2);
		clone.find('.booking_head').html(numItems);

		clone.find('.form-control').val(0);
		clone.find('.datetimepicker').attr('id', 'appointdate_'+numItems);
		clone.find('.datetimepicker').attr('id', 'endappointdate_'+numItems);

		clone.removeClass("additional_booking");
		$('#add_here_new_booking').append(clone);

		clone.find('.datetimepicker').each(function() {
			$(this).removeAttr('id').removeClass('hasDatepicker');
			$('.datetimepicker').datetimepicker({dateFormat: 'yy-mm-dd', changeYear: true, changeMonth: true, yearRange: '1960:2025'});
		});

		clone.find('.datetimepicker').each(function() {
			$(this).removeAttr('id').removeClass('hasDatepicker');
			$('.datetimepicker').datetimepicker({dateFormat: 'yy-mm-dd', changeYear: true, changeMonth: true, yearRange: '1960:2025'});
		});

		return false;
	});

	$('#save').click(function() {
		submit_mode = false;
	});

	$("#form1").submit(function( event ) {
		if($('[name=invoice_type]:checked').val() == 'Patient') {
			if ($('#patientid').val() == '') {
				alert("Please select a Customer.");
				return false;
			}
			//if ($('#injuryid').closest('.form-group').is(':visible') && $('#injuryid').val() == '') {
			//	alert("Please select an Injury.");
			//	return false;
			//}
			if ($('#therapistsid').closest('.form-group').is(':visible') && $('[name="serviceid[]"]').filter(function() { return this.value != ''; }).length > 0 && $('#therapistsid').val() == '') {
				alert("Please select a Staff.");
				return false;
			}
			//if ((($("[name=total_price]").val() == '0.00' && !$('[name=add_credit]').is(':checked')) || $("[name=final_price]").val() == '0.00') && !$('.return_block').is(':visible') && !$('.adjust_block').is(':visible')) {
			//	alert("Please add items to the invoice.");
			//	return false;
			//}
			if(submit_mode) {
				var missing_payment_type = false;
				$('.form-group:visible').find('[name="payment_type[]"]').each(function() {
					if(this.value == '' && $(this).closest('.form-group').find('[name="payment_price[]"]').val() > 0) {
						missing_payment_type = true;
					}
				});
				if(missing_payment_type) {
					alert("Please complete the payment information.");
					return false;
				}
			}
			var refund_amts = 0;
			$('[name="refund_type_amount[]"]').each(function() {
				refund_amts += +$(this).val() || 0;
			});
			var payment_amts = 0;
			$('[name="payment_price[]"]').each(function() {
				payment_amts += +$(this).val() || 0;
			});
			var insurer_amts = 0;
			$('[name="insurer_payment_amt[]"]').each(function() {
				insurer_amts += +$(this).val() || 0;
				if(this.value != 0 && this.value != '' && $(this).closest('.insurer_line').find('[name="insurerid[]"]').val() == '') {
					alert("Please select an insurer for each payment.");
					return false;
				}
			});

			if(refund_amts > sum_refund + payment_amts + insurer_amts - sum_adjustment) {
				alert("You cannot refund more money than the value of the refund.");
				return false;
			}
		} else {
			if ($('#therapistsid').closest('.form-group').is(':visible') && $('[name="serviceid[]"]').filter(function() { return this.value != ''; }).length > 0 && $('#therapistsid').val() == '') {
				alert("Please select a Staff.");
				return false;
			}
			//if ((($("[name=total_price]").val() == '0.00' && !$('[name=add_credit]').is(':checked')) || $("[name=final_price]").val() == '0.00') && !$('.return_block').is(':visible') && !$('.adjust_block').is(':visible')) {
			//	alert("Please add items to the invoice.");
			//	return false;
			//}
			if(submit_mode) {
				var missing_payment_type = false;
				$('.form-group:visible').find('[name="payment_type[]"]').each(function() {
					if(this.value == '' && $(this).closest('.form-group').find('[name="payment_price[]"]').val() > 0) {
						missing_payment_type = true;
					}
				});
				if(missing_payment_type) {
					alert("Please complete the payment information.");
					return false;
				}
			}
			var refund_amts = 0;
			$('[name="refund_type_amount[]"]').each(function() {
				refund_amts += +$(this).val() || 0;
			});
			var payment_amts = 0;
			$('[name="payment_price[]"]').each(function() {
				payment_amts += +$(this).val() || 0;
			});
			var insurer_amts = 0;
			$('[name="insurer_payment_amt[]"]').each(function() {
				insurer_amts += +$(this).val() || 0;
				if(this.value != 0 && this.value != '' && $(this).closest('.insurer_line').find('[name="insurerid[]"]').val() == '') {
					alert("Please select an insurer for each payment.");
					return false;
				}
			});
            var total = Math.round((sum_refund + payment_amts + insurer_amts - sum_adjustment + 0.00001) * 100) / 100;
			//if(refund_amts > sum_refund + payment_amts + insurer_amts - sum_adjustment) {
            if(refund_amts > total) {
				alert(refund_amts+' | '+ (sum_refund + payment_amts + insurer_amts - sum_adjustment));
                alert("You cannot refund more money than the value of the refund.");
				return false;
			}
		}
	});

	//$("#submit").hide();
	//$("#save").hide();

	var paid_notpaid = $("#paid_notpaid").val();
	if($("#paid_notpaid").val() == 'Yes'){
		$(".ins_payment_option").hide();
		$(".payment_option").show();
		$('[name="payment_type[]"] option[value="Pro-Bono"]').show();
		$('[name="payment_type[]"]').trigger('change.select2');
		//$("#submit").show();
		//$("#save").hide();
		$("select[name='payment_type[]'] option").show();
	} else if($("#paid_notpaid").val() == 'Waiting on Insurer') {
		$(".ins_payment_option").show();
		$(".payment_option").hide().find('.payment_price').val(0);
		//$("#submit").show();
		//$("#save").hide();
		$("select[name='payment_type[]'] option").show();
	} else if($("#paid_notpaid").val() == 'No') {
		$(".ins_payment_option").show();
		$(".payment_option").show();
		//$('[name="payment_type[]"] option[value="Pro-Bono"]').removeAttr('selected').hide();
        $('[name="payment_type[]"] option[value="Pro-Bono"]').show();
		$('[name="payment_type[]"]').trigger('change.select2');
		//$("#submit").show();
		//$("#save").hide();
		$("select[name='payment_type[]'] option").show();
	} else if($("#paid_notpaid").val() == 'On Account') {
		//$(".payment_option").hide();
		$(".ins_payment_option").hide();
		$(".payment_option").show();
		//$("#submit").show();
		//$("#save").hide();
		$("select[name='payment_type[]'] option[value='Master Card']").hide();
		$("select[name='payment_type[]'] option[value='Visa']").hide();
		$("select[name='payment_type[]'] option[value='Debit Card']").hide();
		$("select[name='payment_type[]'] option[value='Cash']").hide();
		$("select[name='payment_type[]'] option[value='Cheque']").hide();
		$("select[name='payment_type[]'] option[value='Amex']").hide();
		$("select[name='payment_type[]'] option[value='Direct Deposit']").hide();
		$("select[name='payment_type[]'] option[value='Gift Certificate Redeem']").hide();
		$("select[name='payment_type[]'] option[value='Pro-Bono']").hide();
	} else if($("#paid_notpaid").val() == 'Credit On Account') {
		//$(".payment_option").hide();
		$(".ins_payment_option").hide();
		$(".payment_option").show();
		$('[name="payment_type[]"] option[value="Pro-Bono"]').removeAttr('selected').hide();
		$('[name="payment_type[]"]').trigger('change.select2');
		//$("#submit").show();
		//$("#save").hide();
		$("select[name='payment_type[]'] option").show();
	} else if($("#paid_notpaid").val() == 'Saved') {
		//$(".payment_option").hide();
		$(".ins_payment_option").hide();
		$(".payment_option").hide();
		//$("#submit").hide();
		//$("#save").show();
		$("select[name='payment_type[]'] option").show();
	}

	if(paid_notpaid == '') {
		$(".ins_payment_option").hide();
		$(".payment_option").hide();

		//$("#submit").hide();
		//$("#save").hide();
	}

	$('[name=paid]').change(function(){
		if($(this).val() == 'Yes'){
			//$(".payment_option").show();
			$(".ins_payment_option").hide();
			$(".payment_option").show();
			$('[name="payment_type[]"] option[value="Pro-Bono"]').show();
			$('[name="payment_type[]"]').trigger('change.select2');
			//$("#submit").show();
			//$("#save").hide();
			$("select[name='payment_type[]'] option").show();
		} else if($(this).val() == 'Waiting on Insurer') {
			$(".ins_payment_option").show();
			$(".payment_option").hide().find('.payment_price').val(0);
			//$("#submit").show();
			//$("#save").hide();
			$("select[name='payment_type[]'] option").show();
		} else if($(this).val() == 'No') {
			$(".ins_payment_option").show();
			$(".payment_option").show();
			//$('[name="payment_type[]"] option[value="Pro-Bono"]').removeAttr('selected').hide();
            $('[name="payment_type[]"] option[value="Pro-Bono"]').show();
			$('[name="payment_type[]"]').trigger('change.select2');
			//$("#submit").show();
			//$("#save").hide();
			$("select[name='payment_type[]'] option").show();
		} else if($(this).val() == 'On Account') {
			//$(".payment_option").hide();
			$(".ins_payment_option").hide();
			$(".payment_option").show();
			//$("#submit").show();
			//$("#save").hide();

			$("select[name='payment_type[]'] option[value='Master Card']").hide();
			$("select[name='payment_type[]'] option[value='Visa']").hide();
			$("select[name='payment_type[]'] option[value='Debit Card']").hide();
			$("select[name='payment_type[]'] option[value='Cash']").hide();
			$("select[name='payment_type[]'] option[value='Cheque']").hide();
			$("select[name='payment_type[]'] option[value='Amex']").hide();
			$("select[name='payment_type[]'] option[value='Direct Deposit']").hide();
			$("select[name='payment_type[]'] option[value='Gift Certificate Redeem']").hide();
			$("select[name='payment_type[]'] option[value='Pro-Bono']").hide();
		} else if($(this).val() == 'Credit On Account') {
		//$(".payment_option").hide();
			$(".ins_payment_option").hide();
			$(".payment_option").show();
			$('[name="payment_type[]"] option[value="Pro-Bono"]').removeAttr('selected').hide();
			$('[name="payment_type[]"]').trigger('change.select2');
			//$("#submit").show();
			//$("#save").hide();
			$("select[name='payment_type[]'] option").show();
		} else if($(this).val() == 'Saved') {
		//$(".payment_option").hide();
			$(".ins_payment_option").hide();
			$(".payment_option").hide();
			//$("#submit").hide();
			//$("#save").show();
			$("select[name='payment_type[]'] option").show();
		}
	});

	$('#ins_add_row_payment').on( 'click', function () {
		var clone = $('.ins_additional_payment').clone();
		clone.find('.form-control').val('');

		var inputs = $('.payment_price');
		var sum = 0;
		inputs.each(function () {
			sum += +$(this).val() || 0;
		});
		var final_price = $("#final_price").val();

		var insurance_payment = 0;
		$('[name="insurance_payment[]"]').each(function () {
			insurance_payment += +$(this).val() || 0;
		});

		//var insurance_payment = $('[name="insurance_payment"]').val();

		var remain_total = +final_price - +sum - +insurance_payment;

		clone.find('#payment_price_0').val(round2Fixed(remain_total));
		clone.find('#payment_price_0').attr('id', 'payment_price_'+inc);
		resetChosen(clone.find('[name="insurerid[]"]'));

		inc++;
		clone.removeClass("ins_additional_payment");
		$('#ins_add_here_new_payment').append(clone);
		return false;
	});

	var inc = 1;
	$('#add_row_payment').on( 'click', function () {
		var clone = $('.additional_payment').clone();
		clone.find('.form-control').val('');


		var inputs = $('.payment_price');
		var sum = 0;
		inputs.each(function () {
			sum += +$(this).val() || 0;
		});
		var final_price = $("#final_price").val();

		var insurance_payment = 0;
		$('[name="insurance_payment[]"]').each(function () {
			insurance_payment += +$(this).val() || 0;
		});

		var remain_total = +final_price - +sum - +insurance_payment;

		clone.find('#payment_price_0').val(round2Fixed(remain_total));
		clone.find('#payment_price_0').attr('id', 'payment_price_'+inc);
		resetChosen(clone.find('[name="payment_type[]"]'));

		inc++;
		clone.removeClass("additional_payment");
		$('#add_here_new_payment').append(clone);
		return false;
	});

	var inc = 1;
	$('#add_row_pay_payment').on( 'click', function () {
		var clone = $('.additional_pay_payment').clone();
		clone.find('.form-control').val('');

		var inputs = $('.payment_price');
		var sum = 0;
		inputs.each(function () {
			sum += +$(this).val() || 0;
		});
		var final_price = $("#final_price").val();

		var remain_total = +final_price - +sum;

		clone.find('#payment_price_0').val(round2Fixed(remain_total));
		clone.find('#payment_price_0').attr('id', 'payment_price_'+inc);

		inc++;
		clone.removeClass("additional_pay_payment");
		$('#add_here_new_pay_payment').append(clone);
		return false;
	});

	inc = 1;

	inc_pro = 1;

	$("#refund_btn").show();
	$(".refund_tb").hide();
	$(".treatment_plan").hide();

	/* $('.serviceid').each(function () {
		var service_id = this.id;
		var service = $("#"+service_id+" option:selected").text();

		if (service.toLowerCase().indexOf("assessment") > 0) {
			$(".treatment_plan").show();
		}
	}); */
});
$(document).on('change', 'select[name="type"]', function() { changeInvoiceType(); });
$(document).on('change', 'select[name="ticketid[]"]', function() { load_ticket_details(this); });

function changeInvoiceType() {
	var invoiceid = $('[name="invoiceid"]').val() != undefined ? $('[name="invoiceid"]').val() : 0;
	var type = '';
	if($('[name="type"]').val() != undefined) {
		type = $('[name="type"]').val();
	}

	window.location.href = "?invoiceid="+invoiceid+"&type="+type;
}

function changeInsurance(sel) {
	var proValue = sel.value;

	if(proValue == '') {
		$("#insurance_payment").hide();
	} else {
		$("#insurance_payment").show();
	}
}


function changeRefund(sel) {
	//$(".refund_tb").show();

	var proValue = sel.value;
	var typeId = sel.id;
	var arr = typeId.split(' ');
	var arr1 = arr[1].split('_');
	$("#tb_"+arr1[1]).show();
	$("#btn_"+arr1[1]).show();
	$("#link_"+arr1[1]).hide();
	countTotalPrice();
}

function getParameterByName(name) {
	name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		results = regex.exec(location.search);
	return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function waiting_on_insurer(sel) {
	var action = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({
		type: "GET",
		url: "../ajax_all.php?fill=invoice&insurer="+action,
		dataType: "html",   //expect html to be returned
		success: function(response){
			alert("Invoice moved to Waiting on Insurer Invoice Tab.");
			location.reload();
		}
	});
}

function changeApptType(type) {
	$('[id^=category_] option').show()
	if(type != '') {
		$('[id^=category_] option[data-appt-type!=",,"]').filter(':not([data-appt-type*=",'+type+',"])').hide();
	}
	$('[id^=category_]').trigger('change.select2').change();
}

function changeCategory(sel) {
	var action = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var app_type = $("[name=app_type]").val();
	var invoiceid = $("#invoiceid").val();
	var serviceid = $(sel).closest('.form-group').find('[name="serviceid[]"]').val();

	$.ajax({
		type: "GET",
		url: "../ajax_all.php?fill=invoice&category="+action+"&app_type="+app_type+"&invoiceid="+invoiceid+"&sid="+serviceid,
		dataType: "html",   //expect html to be returned
		success: function(response){
			$("#serviceid_"+arr[1]).html('<option value=""></option>'+response);
			$("#serviceid_"+arr[1]).trigger("change.select2");
		}
	});
}

function changeService(sel) {
	var proValue = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var invoiceid = $("#invoiceid").val();

	//$('.serviceid').each(function () {
		var service_id = this.id;
		var service = $("#"+service_id+" option:selected").text();
		var editable = $(this).find('option:selected').data('editable');
		if(editable > 0) {
			$(this).closest('.form-group').find('.fee').removeAttr('readonly');
		} else {
			$(this).closest('.form-group').find('.fee').prop('readonly',true);
		}

		if (service.toLowerCase().indexOf("assessment") > 0) {
			$(".treatment_plan").show();
		}
	//});

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=invoice&serviceid="+proValue+"&invoiceid="+invoiceid,
		dataType: "html",   //expect html to be returned
		success: function(response){
			var arr2 = response.split('**');
			var total_with_gst = +arr2[0] + (arr2[1] == 1 ? 0 : Math.round(+arr2[0] * +$('#tax_rate').val()) / 100);
			$("#fee_"+arr[1]).val(arr2[0]).closest('.form-group').find('[name="insurer_payment_amt[]"]').first().val(total_with_gst);
			$("#gstexempt_"+arr[1]).val(arr2[1]);
			$(".invtype").html(arr2[2]);
			$(".invtype").trigger("change.select2");
			countTotalPrice();
		}
	});
}

function updatePricing() {
	$('[name="inventoryid[]"]').each(function() {
		changeProduct(this);
	});
}

function filterInventory(sel) {
	var cat = sel.value;
	var arr = sel.id.split('_');
	var part_no = $("#inventorypart_"+arr[1]).val();
	$("#inventorypart_"+arr[1]).empty();
	var inv_id = $("#inventoryid_"+arr[1]).val();
	$("#inventoryid_"+arr[1]).empty();
	
	$("#inventorypart_"+arr[1]).append('<option />');
	$("#inventoryid_"+arr[1]).append('<option />');
	inv_list.forEach(function(row) {
		if(row.category == cat) {
			$("#inventorypart_"+arr[1]).append('<option data-category="'+row.category+'" '+(row.part_no == part_no ? 'selected' : '')+' value="'+row.part_no+'">'+row.part_no+'</option>');
			$("#inventoryid_"+arr[1]).append('<option data-category="'+row.category+'" data-part="'+row.part_no+'" '+(row.inventoryid == inv_id ? 'selected' : '')+' value="'+row.inventoryid+'">'+row.name+'</option>');
		}
	});
	$("#inventorypart_"+arr[1]).trigger('change.select2');
	$("#inventoryid_"+arr[1]).trigger('change.select2').change();
    $("#inventorycat_"+arr[1]).val(cat);
	$("#inventorycat_"+arr[1]).trigger('change.select2');
}

function adminPrice(sel) {
    arr = sel.id.split('_');
    id = arr[1];
    $('#sellprice_'+id).val(sel.value * $("#quantity_"+arr[1]).val());
    countTotalPrice();
}

function changeProduct(sel) {
	if($(sel).closest('.form-group').hasClass('refundable')) {
		var id = sel.id.split('_')[1];
		$("#sellprice_"+id).val(round2Fixed(sel.value*$("#unitprice_"+id).val()));
		countTotalPrice();
		return;
	}
	if(sel.name == 'inventorypart[]') {
		$("#inventoryid_"+sel.id.split('_')[1]).find('option:selected').removeAttr('selected');
		$("#inventoryid_"+sel.id.split('_')[1]).find('option[data-part="'+sel.value+'"]').prop('selected','selected');
		$("#inventoryid_"+sel.id.split('_')[1]).trigger('change.select2');
		sel = $("#inventoryid_"+sel.id.split('_')[1]).get(0);
		$('#inventorycat_'+sel.id.split('_')[1]).val($(sel).find('option:selected').data('category')).trigger('change.select2');
	} else {
		$('#inventorycat_'+sel.id.split('_')[1]).val($(sel).find('option:selected').data('category')).trigger('change.select2');
		$('#inventorypart_'+sel.id.split('_')[1]).val($(sel).find('option:selected').data('part')).trigger('change.select2');
	}
	var proValue = sel.value;
	var proId = sel.id;
	var arr = proId.split('_');
	var inventoryid = $("#inventoryid_"+arr[1]).val();
	var invtype = $("#invtype_"+arr[1]).val();
    var pricing = $('[name=pricing] option:selected').val();
    var linepricing = $('#linepricing_'+arr[1]).val();
    if (typeof linepricing !== 'undefined' && linepricing != '' && linepricing != null) {
        pricing = linepricing;
    }

	if(invtype == 'WCB') {
		invtype = 'wcb_price';
	} else if(invtype == 'MVA') {
		invtype = 'final_retail_price';
	} else if(pricing == '') {
		invtype = 'final_retail_price';
	} else {
		invtype = pricing;
	}

	if (pricing=='admin_price') {
        $("#sellprice_"+arr[1]).val($("#unitprice_"+arr[1]).val() * $("#quantity_"+arr[1]).val());
        countTotalPrice();
    } else {
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "../ajax_all.php?fill=invoice&inventoryid="+inventoryid+"&type="+invtype,
            dataType: "html",   //expect html to be returned
            success: function(response){
                response = response.split('#*#');
                var total_with_gst = +response[0] + (response[1] == 1 ? 0 : Math.round(+response[0] * +$('#tax_rate').val()) / 100);
                if ( response[0] != '' ) {
                    $("#unitprice_"+arr[1]).val(parseFloat(response[0]).toFixed(2)).closest('.form-group').find('[name="insurer_payment_amt[]"]').first().val(total_with_gst);
                } else {
                    $("#unitprice_"+arr[1]).val(0.00);
                }
                $("#sellprice_"+arr[1]).closest('div').find('[name="inventory_gst_exempt[]"]').val(response[1]);
                $("#sellprice_"+arr[1]).val(parseFloat($("#quantity_"+arr[1]).val()*response[0]).toFixed(2));
                countTotalPrice();
            }
        });
    }
}

function changePackage(sel) {
	var changed = $(sel).attr('name');
	var row = $(sel).closest('.form-group');
	var cat = row.find('.packagecat');
	var packageid = row.find('.packageid');
	var cost = row.find('.package_cost');
	if(changed == 'packagecat[]') {
		if(cat.val() == '') {
			packageid.find('option').show();
			packageid.trigger('change.select2');
		} else {
			packageid.find('option').hide();
			packageid.find('option[data-cat="'+cat.val()+'"]').show();
			packageid.trigger('change.select2');
		}
	} else if(packageid.val() != '') {
		cat.val(packageid.find('option:selected').data('cat')).trigger('change.select2');
		cost.val(round2Fixed(packageid.find('option:selected').data('cost')));
		var total_with_gst = +cost.val() + (Math.round(+cost.val() * +$('#tax_rate').val()) / 100);
		row.find('[name="insurer_payment_amt[]"]').first().val(total_with_gst);
	}
	countTotalPrice();
}

function setThirdPartyMisc(input) {
	var row = $(input).closest('.form-group');
	var total = +row.find('[name="misc_price[]"]').val() * +row.find('[name="misc_qty[]"]').val();
	var total_with_gst = total + (Math.round(total * +$('#tax_rate').val()) / 100);
	row.find('[name="insurer_payment_amt[]"]').first().val(total_with_gst);
}

/*function changeQuantity(sel) {
	var group = $(sel).closest('.form-group');
	var proValue = sel.value;
	var proId = sel.id;
	var arr = proId.split('_');
	var inventoryid = group.find('[name="inventoryid[]"]').val();
	var type = group.find('[name="invtype[]"]').val();
	if(type == 'General') {
		type = 'final_retail_price';
	}
	if(type == 'WCB') {
		type = 'wcb_price';
	}
	if(type == 'MVA') {
		type = 'final_retail_price';
	}
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=invoice&inventoryid="+inventoryid+"&type="+type,
		dataType: "html",   //expect html to be returned
		success: function(response){
			response = response.split('#*#');
			$("#sellprice_"+arr[1]).val(proValue*response[0]);
			$("#sellprice_"+arr[1]).closest('div').find('[name="inventory_gst_exempt[]"]').val(response[1]);
			countTotalPrice();
		}
	});
}*/

function changePromotion(sel) {
	var promotionid = sel.value;

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=invoice&promotionid="+promotionid,
		dataType: "html",   //expect html to be returned
		success: function(response){
			$('#set_promotion').val(response);
			countTotalPrice();
		}
	});
}

function changeGF(gf_number) {
	$.ajax({
		type: "GET",
		url: "../ajax_all.php?fill=posGF&gf_number="+gf_number,
		dataType: "html",   //expect html to be returned
		success: function(response) {
			if(gf_number == '') {
				gf_value = 0;
			} else if(response == 'na') {
				gf_value = 0;
				alert("Invalid Gift Card.")
			} else if(response == 'used') {
				gf_value = 0;
				alert("Gift Card has already been used.")
			}
			else {
				gf_value = response;
				var totals = 0;
				$('[name="payment_price[]"]').each(function() {
					totals += this.value * 1;
				});
				if(gf_value > totals) {
					gf_value = totals;
				}

				$('#set_gf').val(gf_value);
				setTotalPrice();
				$('#detail_gift_amount').html("$" + (gf_value * 1).toFixed(2));
			}
		}
	});
}

function countTotalPrice() {
	var gf_amt = $('#set_gf').val();
    if ( gf_amt > 0 ) {
        changeGF(gf_amt);
    }
	setTotalPrice();
}

function setTotalPrice() {
	var promotion = $('#set_promotion').val();
	var gf = $('#set_gf').val();
	var gratuity = $('#gratuity').val();
	var tax_rate = $('#tax_rate').val();
	sum_refund = 0;
	sum_adjustment = 0;

	var insurer_portions = 0;
	var sum_fee = 0;
	var j=0;
	var price_on_gst = 0;
	$('.detail_service_list').empty().prev('h4').hide();
	$('.fee').not(':disabled').each(function () {
		var fee_id = this.id;
		var arr = fee_id.split('_');
		var gstexempt = $(this).closest('.form-group').find('.gstexempt').val();

		var fee_row = +$(this).val() || 0;

		if(fee_row != 0) {
			var group = $(this).closest('.form-group');
			var cat = group.find('[id^=category_] option:selected').text();
			var label = group.find('[name="serviceid[]"] option:selected').text();
			if(group.hasClass('dis_service')) {
				cat = group.find('[name="service_cat[]"]').val();
				label = group.find('[name="service_name[]"]').val();
			}
			var info = cat+': '+label;
			if(label == '' || label == undefined) {
				info = group.find('[name=servicelabel]').val();
			}

            var qty = group.find('.qty').val();
            if(qty > 0) {
                info = info + ' x ' + qty;
                fee_row = fee_row * qty;
            } else {
                qty = 1;
            }
            sum_fee += fee_row;

			if(fee_row < 0) {
				$('.detail_service_list').append('<label class="pull-right">'+fee_row.toFixed(2)+'</label>Refund: '+info+'<br /><div class="clearfix"></div>').prev('h4').show();
            } else if(group.hasClass('adjust_block')) {
				sum_adjustment += fee_row + (gstexempt == 0 ? fee_row*tax_rate/100 : 0);
				$('.detail_service_list').append('<label class="pull-right">'+fee_row.toFixed(2)+'</label>Adjustment: '+info+'<br /><div class="clearfix"></div>').prev('h4').show();
			} else {
				$('.detail_service_list').append('<label class="pull-right">'+fee_row.toFixed(2)+'</label>'+info+'<br /><div class="clearfix"></div>').prev('h4').show();
			}
			if(group.find('[name="servicerow_refund[]"]').is(':checked')) {
				sum_fee -= fee_row;
				if(gstexempt == 0) {
					price_on_gst -= +fee_row || 0;
				}
				sum_refund += fee_row + (gstexempt == 0 ? fee_row*tax_rate/100 : 0);
				$('.detail_service_list').append('<label class="pull-right">'+(-fee_row).toFixed(2)+'</label>Refund: '+info+'<br /><div class="clearfix"></div>').prev('h4').show();
				//insurer_portions -= +this.value || 0;
			}
			group.find('[name="insurer_payment_amt[]"],[name="init_insurer_payment[]"]').each(function() {
				insurer_portions += +this.value || 0;
				//if(gstexempt != 1) {
				//	insurer_portions += ((+this.value || 0)*tax_rate)/100;
				//}
			});
		}

		if(gstexempt == 0) {
			price_on_gst += +fee_row || 0;
		} else {
			price_on_gst += 0;
		}
	});
    if($('.detail_service_list').text() != '') {
        $('.detail_service_list').append('<hr>');
    }

	var sum_price = 0;
	var sum_inv_gst = 0;
	$('.detail_inventory_list').empty().prev('h4').hide();
	$('[name="init_price[]"]').not(':disabled').each(function () {
		var fee_row = +$(this).val() || 0;

		if(fee_row != 0) {
			var group = $(this).closest('.form-group');
			var label = group.find('[name="inventoryid[]"] option:selected').text();
			var type = group.find('[name="invtype[]"] option:selected').text();
			var info = label+(group.find('[name="invtype[]"]').is(':visible') && type != '' ? ': '+type : '');
			if(label == '' || label == undefined) {
				info = group.find('[name=inventorylabel]').val();
			}
			info = info+' X '+group.find('[name="init_quantity[]"]').val();
			$('.detail_inventory_list').append('<label class="pull-right">'+fee_row.toFixed(2)+'</label>'+info+'<br /><div class="clearfix"></div>').prev('h4').show();
			sum_price += fee_row;
			var row_exempt = $(this).closest('.form-group').find('[name="inventory_gst_exempt[]"]').val();
			sum_inv_gst += (row_exempt == 1 ? 0 : fee_row);
			group.find('[name="init_insurer_payment[]"]').each(function() {
				insurer_portions += +this.value || 0;
				//insurer_portions += (row_exempt == 1 ? 0 : ((+this.value || 0)*tax_rate)/100);
			});
		}
	});
	$('.sellprice').not(':disabled').each(function () {
		var fee_row = +$(this).val() || 0;
		var row_exempt = $(this).closest('.form-group').find('[name="inventory_gst_exempt[]"]').val();
		sum_price += fee_row;
		sum_inv_gst += (row_exempt == 1 ? 0 : fee_row);

		if(fee_row != 0) {
			var group = $(this).closest('.form-group');
			var label = group.find('[name="inventoryid[]"] option:selected').text();
			var type = group.find('[name="invtype[]"] option:selected').text();
			var info = label+(group.find('[name="invtype[]"]').is(':visible') && type != '' ? ': '+type : '');
			if(label == '') {
				info = group.find('[name=inventorylabel]').val();
			}
			info = info+' X '+group.find('[name="quantity[]"]').val();
			group.find('[name="insurer_payment_amt[]"]').each(function() {
				insurer_portions += +this.value || 0;
				//insurer_portions += (row_exempt == 1 ? 0 : ((+this.value || 0)*tax_rate)/100);
			});
			if(fee_row < 0) {
				sum_refund -= fee_row + (row_exempt == 1 ? 0 : (fee_row*tax_rate/100));
				$('.detail_inventory_list').append('<label class="pull-right">'+fee_row.toFixed(2)+'</label>Return: '+info+'<br /><div class="clearfix"></div>').prev('h4').show();
			} else if(group.hasClass('adjust_block')) {
				sum_adjustment += fee_row + (row_exempt == 1 ? 0 : (fee_row*tax_rate/100));
				$('.detail_inventory_list').append('<label class="pull-right">'+fee_row.toFixed(2)+'</label>Adjustment: '+info+'<br /><div class="clearfix"></div>').prev('h4').show();
			} else {
				$('.detail_inventory_list').append('<label class="pull-right">'+fee_row.toFixed(2)+'</label>'+info+'<br /><div class="clearfix"></div>').prev('h4').show();
			}
		}
	});
    if($('.detail_inventory_list').text() != '') {
        $('.detail_inventory_list').append('<hr>');
    }

	var package_cost = 0;
	$('.detail_package_list').empty().prev('h4').hide();
	$('.package_cost').not(':disabled').each(function () {
		var fee_row = +$(this).val() || 0;
		package_cost += fee_row;
		if(fee_row != 0) {
			var group = $(this).closest('.form-group');
			var cat = group.find('[name="packagecat[]"] option:selected').text();
			var label = group.find('[name="packageid[]"] option:selected').text();
			var info = cat+': '+label;
			if(label == '' || label == undefined) {
				info = group.find('[name=package_label]').val();
			}
			if(group.hasClass('adjust_block')) {
				sum_adjustment += fee_row + (fee_row*tax_rate/100);
				$('.detail_package_list').append('<label class="pull-right">'+fee_row.toFixed(2)+'</label>Adjustment: '+info+'<br /><div class="clearfix"></div>').prev('h4').show();
			} else {
				$('.detail_package_list').append('<label class="pull-right">'+fee_row.toFixed(2)+'</label>'+info+'<br /><div class="clearfix"></div>').prev('h4').show();
			}
			if(group.find('[name="packagerow_refund[]"]').is(':checked')) {
				package_cost -= fee_row;
				sum_refund += fee_row + (fee_row*tax_rate/100);
				$('.detail_package_list').append('<label class="pull-right">'+(-fee_row).toFixed(2)+'</label>Refund: '+info+'<br /><div class="clearfix"></div>').prev('h4').show();
			}
			group.find('[name="insurer_payment_amt[]"],[name="init_insurer_payment[]"]').each(function() {
				insurer_portions += +this.value || 0;
				//insurer_portions += ((+this.value || 0)*tax_rate)/100;
			});
		}
	});
    if($('.detail_package_list').text() != '') {
        $('.detail_package_list').append('<hr>');
    }

	var misc_price = 0;
	$('.detail_misc_list').empty().prev('h4').hide();
	$('.misc_total').not(':disabled').each(function () {
		var group = $(this).closest('.form-group');
		var price = +group.find('.misc_price').val() || 0;
		var init_qty = +group.find('.init_qty').val() || 0;
		var qty = +group.find('.misc_qty').val() || 0;
		var init_total = price * init_qty;
		var total = price * qty;
		$(this).val(total);
		misc_price += total + init_total;
		if(init_total != 0 || total != 0) {
			var label = group.find('.misc_name').val() + ' X ';
			if(group.hasClass('adjust_block')) {
				sum_adjustment += total + (total*tax_rate/100);
				$('.detail_misc_list').append('<label class="pull-right">'+total.toFixed(2)+'</label>Adjustment: '+label+qty+'<br /><div class="clearfix"></div>').prev('h4').show();
			} else if(group.hasClass('refundable')) {
				$('.detail_misc_list').append('<label class="pull-right">'+init_total.toFixed(2)+'</label>'+label+init_qty+'<br /><div class="clearfix"></div>').prev('h4').show();
				if(total < 0) {
					sum_refund -= total + (total*tax_rate/100);
					$('.detail_misc_list').append('<label class="pull-right">'+(total).toFixed(2)+'</label>Return: '+label+qty+'<br /><div class="clearfix"></div>').prev('h4').show();
				}
			} else {
				$('.detail_misc_list').append('<label class="pull-right">'+total.toFixed(2)+'</label>'+label+qty+'<br /><div class="clearfix"></div>').prev('h4').show();
			}
			group.find('[name="insurer_payment_amt[]"],[name="init_insurer_payment[]"]').each(function() {
				insurer_portions += +this.value || 0;
			});
		}
	});
    if($('.detail_misc_list').text() != '') {
        $('.detail_misc_list').append('<hr>');
    }

	var ship_type = $('#delivery_type').val();
	if(ship_type == 'Company Delivery') {
		$('.confirm_delivery').show();
		$('.deliver_contractor').hide();
		$('.ship_amt').show();
	}
	if(ship_type == 'Drop Ship') {
		$('.confirm_delivery').show();
		$('.deliver_contractor').show();
		$('.ship_amt').show();
	}
	if(ship_type == 'Shipping') {
		$('.confirm_delivery').show();
		$('.deliver_contractor').show();
		$('.ship_amt').show();
	}
	if(ship_type == 'Shipping on Customer Account') {
		$('.confirm_delivery').hide();
		$('.deliver_contractor').hide();
		$('.ship_amt').show();
	}
	if(ship_type == 'Pick-Up') {
		$('.confirm_delivery').hide();
		$('.deliver_contractor').hide();
		$('.ship_amt').hide();
	}
	var shipping = $('#delivery').val();
    if(shipping == '' || typeof shipping === "undefined") {
		shipping = 0;
	}
	$('#delivery').val(round2Fixed(shipping));
    
	var assembly = $('#assembly').val();
    if(assembly == '' || typeof assembly === "undefined") {
		assembly = 0;
	}
	$('#assembly').val(round2Fixed(assembly));
    
    if(gf == '' || typeof gf === "undefined") {
		gf = 0;
	}
	$('#set_gf').val(round2Fixed(gf));

	var total_price = +sum_fee + +sum_price + +package_cost + +misc_price;
    
    var discount_amt = 0;
    var discount_type = $('input[name=discount_type]:checked').val();
    var discount_value = $('#discount_value').val();
    if (discount_value > 0) {
        if (discount_type=='%') {
            discount_amt = total_price * (discount_value/100);
        } else {
            discount_amt = discount_value;
        }
    }
    var total_after_discount = total_price - discount_amt;

	$("#total_price").val(round2Fixed(total_price));
	$(".detail_sub_total_amt").html('$'+round2Fixed(total_price));
    $(".detail_discount_amt").html('$'+round2Fixed(discount_amt));
	$(".detail_sub_total_after_discount").html('$'+round2Fixed(total_after_discount));

/*
	var total_price_for_gst = +price_on_gst + +sum_inv_gst + +package_cost + +misc_price;
    if (discount_type=='%') {
        total_price_for_gst -= total_price_for_gst * (discount_value/100);
    } else if(discount_value != undefined) {
        total_price_for_gst -= discount_value * (total_price_for_gst / total_price);
    }
    total_price_for_gst += +shipping + +assembly - +promotion;
    total_price_for_gst = Math.round(total_price_for_gst * 100) / 100;
*/
	var total_price_for_gst = Math.round(+price_on_gst + +sum_inv_gst + +package_cost + +misc_price + +shipping + +assembly - +promotion - +discount_amt);

	$(".detail_shipping_amt").html('$'+round2Fixed(shipping));
    $(".detail_assembly_amt").html('$'+round2Fixed(assembly));
	total_price = total_price + +shipping + +assembly - +promotion - +gf - +discount_amt;
	$(".detail_mid_total_amt").html('$'+round2Fixed(total_price));

	if(gratuity == '' || typeof gratuity === "undefined") {
		gratuity = 0;
	}

	if (!$("input[name='promotionid']:checked").val()) {
	   var promo_price = 0;
	} else {
		var promo = $('input[type=radio][name=promotionid]:checked').attr('id');
		var arr_promo = promo.split('_');
		var promo_price = arr_promo[1];
	}

	var payment_price = 0;
	$('[name="payment_price[]"]').each(function () {
		payment_price += +$(this).val() || 0;
	});
	var last_payment = +$('[name="payment_price[]"]').last().val() || 0;
	var credit_balance = 0;

	account_balance = 0;

    if(account_balance == '' || typeof account_balance === "undefined") {
        account_balance = 0;
    }

	if(tax_rate != 0) {
		var tax_rate_value = (total_price_for_gst*tax_rate)/100;
	} else {
		var tax_rate_value = 0;
	}

/*
	var total_after_gst = Math.round(parseFloat(total_price) * 100) / 100 + parseFloat(tax_rate_value);
*/
	var total_after_gst = Math.round(parseFloat(total_price)) + parseFloat(tax_rate_value);

	var total_count_cost = (payment_price+insurer_portions);
	var final_price_cost = +total_after_gst + +gratuity + +account_balance - +promo_price;
	if(total_count_cost > final_price_cost && $('[name=add_credit]').is(':checked')) {
		credit_balance = total_count_cost - final_price_cost;
	}
	// final_price_cost += credit_balance;


	var patient_owes = Math.round((+total_after_gst + +gratuity - +promo_price - +insurer_portions - previous_payment + credit_balance) * 100) / 100;
	var refund_owes = patient_owes * -1;
    if(refund_owes > Math.ceil(sum_refund)) {
        tax_rate_value += refund_owes - sum_refund;
        total_after_gst += refund_owes - sum_refund;
        final_price_cost += refund_owes - sum_refund;
        patient_owes += refund_owes - sum_refund;
        refund_owes = Math.round(sum_refund * 100) / 100;
    } else if(refund_owes == Math.ceil(sum_refund * 100) / 100) {
        sum_refund = Math.round(refund_owes * 100) / 100;
    }
    //var refund_owes = Math.round((patient_owes + 0.00001) * 100) / 100;
	$('[name="refund_type_amount[]"]').each(function() {
		var applied = (refund_owes > this.max ? this.max : (refund_owes < this.min ? this.min : refund_owes));
		if($(this).data('status') == 'auto') {
			refund_owes -= applied;
			$(this).val(round2Fixed(applied));
		}
	});

	if(sum_refund != 0) {
		$(".detail_refund_amt").html('$' + (Math.round(sum_refund * 100) / 100).toFixed(2)).closest('h4').show();
	}
	if(sum_adjustment != 0) {
		$(".detail_adjust_amt").html('$' + (sum_adjustment).toFixed(2)).closest('h4').show();
	}
	if(credit_balance > 0) {
		$(".detail_credit_balance").html('$' + (credit_balance).toFixed(2)).closest('h4').show();
	} else {
		$(".detail_credit_balance").closest('h4').hide()
	}


	var patient_owes = Math.round((+total_after_gst + +gratuity - +promo_price - +insurer_portions - previous_payment + credit_balance) * 100) / 100;
	var refund_owes = patient_owes * -1;
    if(refund_owes > Math.ceil(sum_refund)) {
        tax_rate_value += refund_owes - sum_refund;
        total_after_gst += refund_owes - sum_refund;
        final_price_cost += refund_owes - sum_refund;
        patient_owes += refund_owes - sum_refund;
        refund_owes = sum_refund;
    } else if(refund_owes == Math.ceil(sum_refund * 100) / 100) {
        sum_refund = refund_owes;
    }
    //var refund_owes = Math.round((patient_owes + 0.00001) * 100) / 100;
	$('[name="refund_type_amount[]"]').each(function() {
		var applied = (refund_owes > this.max ? this.max : (refund_owes < this.min ? this.min : refund_owes));
		if($(this).data('status') == 'auto') {
			refund_owes -= applied;
			$(this).val(round2Fixed(applied));
		}
	});
	if(sum_refund != 0) {
		$(".detail_refund_amt").html('$' + (sum_refund).toFixed(2)).closest('h4').show();
	}
	if(sum_adjustment != 0) {
		$(".detail_adjust_amt").html('$' + (sum_adjustment).toFixed(2)).closest('h4').show();
	}
	if(credit_balance > 0) {
		$(".detail_credit_balance").html('$' + (credit_balance).toFixed(2)).closest('h4').show();
	} else {
		$(".detail_credit_balance").closest('h4').hide()
	}

	$(".detail_gst_amt").html('$' + (+tax_rate_value).toFixed(2));
	$(".detail_gratuity_amt").html('$'+ (+gratuity).toFixed(2));
	$(".detail_total_amt").html('$' + (total_after_gst + +gratuity + credit_balance).toFixed(2));
	$("#final_price").val(round2Fixed(final_price_cost));
	$("[name=credit_balance]").val(round2Fixed(credit_balance));
    
	$('[name="refund_type_amount[]"]').each(function() {
		patient_owes += +$(this).val() || 0;
	});
    
	$('[name="payment_price[]"]').last().val(round2Fixed(patient_owes - payment_price + last_payment));
    if(patient_owes != 0) {
		$('.payment_option').show();
	} else {
		$('.payment_option').hide();
	}

	$('[name="init_insurer_payment[]"]').each(function() {
		insurer_portions -= +$(this).val() || 0;
	});

	$(".detail_patient_amt").html('$' + round2Fixed(patient_owes));
	$(".detail_insurer_amt").html('$' + round2Fixed(+insurer_portions));
}

function get_max_insurer_row() {
	var rowid = 0;
	$('.insurer_row_id').each(function() {
		if($(this).val() > rowid) {
			rowid = $(this).val();
		}
	});
	return +rowid + 1;
}

function add_service_row() {
	$(".hide_show_service").show();
    if ( $('.service_labels').is(':hidden') ) {
        $('.service_labels').show();
    }
	var clone = $('.service_option .form-group').last().clone();
    clone.show();
	clone.find('.form-control').val(0);
	clone.find('.qty').val(1);
	clone.find('[id^=serviceid]').attr('id', 'serviceid_'+inc);
	resetChosen(clone.find('[id^=serviceid]'));
	clone.find('[id^=category]').attr('id', 'category_'+inc);
	resetChosen(clone.find('[id^=category]'));
	resetChosen(clone.find('[name^=insurerid]'));
	clone.find('[id^=fee]').attr('id', 'fee_'+inc);
	clone.find('[id^=gstexempt]').attr('id', 'gstexempt_'+inc);
	var max_row = get_max_insurer_row();
	clone.find('.insurer_row_id').val(max_row);
	clone.find('[name="insurer_row_applied[]"]').val(max_row);
	$('#add_here_new_service').append(clone);
	inc++;
	return false;
}
function rem_service_row(btn) {
	if($('.service_option .form-group').not('.hide-titles-mob,.refundable').length == 1) {
		add_service_row();
	}
	$(btn).closest('.form-group').remove();
	countTotalPrice();
}
function add_product_row() {
	$(".hide_show_product").show();
    if ( $('.product_labels').is(':hidden') ) {
        $('.product_labels').show();
    }
	var clone = $('.additional_product').last().clone();
    clone.show();
	clone.find('.form-control').val(0);
	clone.find('.inventorycat').attr('id', 'inventorycat_'+inc_pro);
	clone.find('.inventorypart').attr('id', 'inventorypart_'+inc_pro);
	clone.find('.inventoryid').attr('id', 'inventoryid_'+inc_pro);
	clone.find('.invtype').attr('id', 'invtype_'+inc_pro).val('General');
	clone.find('.invunitprice').attr('id', 'unitprice_'+inc_pro).val(1);
	clone.find('.quantity').attr('id', 'quantity_'+inc_pro).val(1);
	clone.find('.adjust').attr('id', 'adjust_'+inc_pro);
	clone.find('.sellprice').attr('id', 'sellprice_'+inc_pro);
	clone.find('.linepricing').attr('id', 'linepricing_'+inc_pro);
	resetChosen(clone.find("[id^=inventoryid]"));
	resetChosen(clone.find("[id^=inventorycat]"));
	resetChosen(clone.find("[id^=inventorypart]"));
	resetChosen(clone.find("[id^=invtype]"));
	resetChosen(clone.find("[id^=linepricing]"));
	var max_row = get_max_insurer_row();
	clone.find('.insurer_row_id').val(max_row);
	clone.find('[name="insurer_row_applied[]"]').val(max_row);
	$('#add_here_new_product').append(clone);
	inc_pro++;
	return false;
}
function rem_product_row(btn) {
	if($('.product_option .form-group').not('.hide-titles-mob,.refundable').length == 1) {
		add_product_row();
	}
	$(btn).closest('.form-group').remove();
	countTotalPrice();
}
function add_insurer_row(btn) {
	var clone = $(btn).closest('.pay-div').find('.insurer_line').last().clone();
	clone.find('.form-control').val(0);
	resetChosen(clone.find('[name^=insurerid]'));
	$(btn).closest('.pay-div').append(clone);
}
function rem_insurer_row(btn) {
	if($(btn).closest('.pay-div').find('.insurer_line').length == 1) {
		add_insurer_row(btn);
	}
	$(btn).closest('.insurer_line').remove();
	countTotalPrice();
}
function set_patient_payment_row() {
	if($('[name="payment_type[]"] option[value="Pro-Bono"]:selected').length > 0) {
		$('[name="payment_type[]"] option[value="Pro-Bono"]').removeAttr('selected');
		$('[name="payment_type[]"]').each(function() {
			rem_patient_payment_row(this);
		});
		$('[name="payment_type[]"] option[value="Pro-Bono"]').prop('selected','selected');
		$('[name="payment_type[]"]').trigger('change.select2');
	}
}
function add_patient_payment_row() {
	if($('[name="payment_type[]"] option[value="Pro-Bono"]:selected').length > 0) {
		alert('Pro-Bono Payment selected, no other payment method is allowed.');
		return false;
	}
	var clone = $('.additional_payment').last().clone();

	clone.find('.form-control').val('');
	clone.find('#payment_price_0').attr('id', 'payment_price_'+inc_pay).val('0.00');
	resetChosen(clone.find('[name="payment_type[]"]'));

	inc_pay++;
	$('#add_here_new_payment').append(clone);
	$('.additional_payment').find('[name="payment_price[]"]').removeAttr('readonly');
	if(!$('[name=add_credit]').is(':checked')) {
		$('.additional_payment').last().find('[name="payment_price[]"]').attr('readonly','readonly');
	}
	return false;
}
function rem_patient_payment_row(btn) {
	if($(btn).closest('.payment_option').find('.form-group').not('.hide-titles-mob').length == 1) {
		add_patient_payment_row(btn);
	}
	$(btn).closest('.form-group').remove();
	$('.additional_payment').find('[name="payment_price[]"]').removeAttr('readonly');
	if(!$('[name=add_credit]').is(':checked')) {
		$('.additional_payment').last().find('[name="payment_price[]"]').attr('readonly','readonly');
	}
	countTotalPrice();
}
function add_package_row() {
	$(".hide_show_package").show();
    if ( $('.package_labels').is(':hidden') ) {
        $('.package_labels').show();
    }
	var clone = $('.additional_package').last().clone();
    clone.show();
	clone.find('.form-control').val(0);
	clone.find('.packagecat').attr('id', 'packagecat_'+inc_pack);
	clone.find('.packageid').attr('id', 'packageid_'+inc_pack);
	clone.find('.package_cost').attr('id', 'package_cost_'+inc_pack);
	resetChosen(clone.find("[id^=packagecat]"));
	resetChosen(clone.find("[id^=packageid]"));
	var max_row = get_max_insurer_row();
	clone.find('.insurer_row_id').val(max_row);
	clone.find('[name="insurer_row_applied[]"]').val(max_row);
	$('#add_here_new_package').append(clone);
	inc_pack++;
	return false;
}
function rem_package_row(btn) {
	if($('.package_option .form-group').not('.hide-titles-mob,.refundable').length == 1) {
		add_package_row();
	}
	$(btn).closest('.form-group').remove();
	countTotalPrice();
}
function add_misc_row() {
	$(".hide_show_package").show();
    if ( $('.misc_labels').is(':hidden') ) {
        $('.misc_labels').show();
    }
	var clone = $('.additional_misc').last().clone();
    clone.show();
	clone.find('.form-control').val('');
	var max_row = get_max_insurer_row();
	clone.find('.insurer_row_id').val(max_row);
	clone.find('[name="insurer_row_applied[]"]').val(max_row);
	$('#add_here_new_misc').append(clone);
	inc_misc++;
	return false;
}
function rem_misc_row(btn) {
	if($('.misc_option .form-group').not('.hide-titles-mob,.refundable').length == 1) {
		add_misc_row();
	}
	$(btn).closest('.form-group').remove();
	countTotalPrice();
}
function allow_edit_amount() {
	if($('[name=add_credit]').is(':checked')) {
		$('.additional_payment').find('[name="payment_price[]"]').removeAttr('readonly');
	} else {
		$('.additional_payment').last().find('[name="payment_price[]"]').attr('readonly','readonly');
	}
}
function load_ticket_details(sel) {
	var block = $(sel).closest('.invoice_ticket');
	var ticketid = $(sel).find('option:selected').val();
	if(ticketid != undefined && ticketid > 0) {
		$.ajax({
			url: '../Invoice/invoice_ajax.php?action=load_ticket_details',
			method: 'POST',
			data: { ticketid: ticketid },
			dataType: 'html',
			success: function(response) {
				$(block).find('.ticket_details').html(response);
				setTotalPrice();
			}
		});
	} else {
		$(block).find('.ticket_details').html('');
		setTotalPrice();
	}
}
function add_ticket_row() {
	destroyInputs('.invoice_ticket');
	var block = $('.invoice_ticket').last();
	var clone = $(block).clone();

	$(clone).find('.ticket_details').html('');
	$(clone).find('input,select').val('');

	$(block).after(clone);
	initInputs('.invoice_ticket');
}
function rem_ticket_row(btn) {
	if($('.ticket_option .invoice_ticket').length == 1) {
		add_ticket_row();
	}
	$(btn).closest('.invoice_ticket').remove();
	countTotalPrice();
}
function view_tabs() {
    $('.view_tabs').toggle();
}
function view_summary() {
    $('.view_summary').toggle();
}
function view_search() {
    $('.search-group').toggleClass('hidden');
}
function void_invoice(invoiceid) {
    var ans = confirm('Are you sure you want to void this invoice?');
    if ( ans == true ) {
        $.ajax({
            url: '../Invoice/invoice_ajax.php?action=void_invoice',
            type: 'POST',
            data: { invoiceid: invoiceid },
            success: function(response) {
                alert('Invoice #'+invoiceid+' voided successfully.');
                window.location.reload();
            }
        });
    }
}
function email_doc(pdf, folder_name){
    var documents=[];
    if ( pdf != '' ){
        var invoice_pdf = '../'+folder_name+'/'+pdf;
        documents.push(invoice_pdf);
        overlayIFrameSlider('../Email Communication/add_email.php?type=external&attach_docs='+documents.join('#*#'),'auto',false,true);
    }
}