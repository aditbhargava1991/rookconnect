<?php $field_config = explode(',',get_config($dbc, 'hr_fields')); ?>
<script>
$(document).ready(function() {
	$('.form-group input').change(enableForm);
	$('.block-group input').change(saveFields);
});
function enableForm() {
	var i = $(this).data('i');
	if(this.checked) {
		$('#config_block_'+i).show();
	} else {
		$('#config_block_'+i).hide().find('[name=deleted]').val(1).change();
	}
}
function saveFields() {
	var value = this.value;
	if($(this).prop('type') == 'checkbox') {
		if(!$(this).is(':checked')) {
			value = '';
		}
	}
	$.ajax({
		url: 'hr_ajax.php?action=form_layout',
		method: 'POST',
		data: {
			hrid: $(this).closest('.block-group').data('hrid'),
			name: this.name,
			value: value
		}
	});
}
function get_form_config(block) {
	block.html('<h4><em>Loading...</em></h4>');
	if(!(block.data('hrid') > 0)) {
		$.ajax({
			url: 'hr_ajax.php?action=form_layout',
			method: 'POST',
			data: {
				form: block.data('form-name'),
				user_form_id: block.data('userformid')
			},
			success: function(response) {
				if(response > 0) {
					block.data('hrid',response);
				}
			}
		});
	}
	load_block(block);
}
function unload_block(link) {
	var block = $(link).closest('label').next('.block-group');
	$(link).remove();
	block.html('<a href="" onclick="get_form_config($(this).closest(\'.block-group\')); return false;">Configure Form</a>');
}
function load_block(block) {
	if(block.data('hrid') > 0) {
		$.ajax({
			url: 'hr_config.php?hrid='+block.data('hrid'),
			method: 'POST',
			data: {
				form_name: block.data('form-name')
			},
			success: function(response) {
				block.prev('label').append(' <small><em><a href="" onclick="unload_block(this); return false;">Hide Form Settings</a></em></small>');
				block.html(response);
				initInputs('#'+block.attr('id'));
				$('#'+block.attr('id')).find('input,select,textarea').change(saveFields);
			}
		});
	} else {
		setTimeout(function() { load_block(block); }, 250);
	}
}
function changeCategory(category) {
	$.ajax({
		url: 'hr_ajax.php?action=set_category',
		method: 'POST',
		data: {
			category: category
		},
		success: function(response) {
			$('[name=heading_number]').html(response).trigger('change.select2');
		}
	});
}
function changeSection(section) {
	$.ajax({
		url: 'hr_ajax.php?action=set_form_section',
		method: 'POST',
		data: {
			section: section,
			category: $('[name=category]').val()
		},
		success: function(response) {
			response = response.split('#*#');
			$('[name=heading]').val(response[0]);
			$('[name=sub_heading_number]').html(response[1]).trigger('change.select2');
		}
	});
}
function changeSubSection(subsection) {
	$.ajax({
		url: 'hr_ajax.php?action=set_form_subsection',
		method: 'POST',
		data: {
			subsection: subsection,
			category: $('[name=category]').val()
		},
		success: function(response) {
			response = response.split('#*#');
			$('[name=sub_heading]').val(response[0]);
			$('[name=third_heading_number]').html(response[1]).trigger('change.select2');
		}
	});
}
</script>
<div class="standard-body-title">
    <h3>Pre-Built Forms</h3>
</div>
<div class="standard-body-content">
    <?php $hr_forms = [];
    $hr_forms[] = ["Employee Information Form"];
    $hr_forms[] = ["Employee Driver Information Form"];
    $hr_forms[] = ["Time Off Request"];
    $hr_forms[] = ["Confidential Information"];
    $hr_forms[] = ["Work Hours Policy"];
    $hr_forms[] = ["Direct Deposit Information"];
    $hr_forms[] = ["Employee Substance Abuse Policy"];
    $hr_forms[] = ["Employee Right to Refuse Unsafe Work"];
    $hr_forms[] = ["Shop Yard and Office Orientation"];
    $hr_forms[] = ["Copy of Drivers Licence and Safety Tickets"];
    $hr_forms[] = ["PPE Requirements"];
    $hr_forms[] = ["Verbal Training in Emergency Response Plan"];
    $hr_forms[] = ["Eligibility for General Holidays and General Holiday Pay"];
    $hr_forms[] = ["Maternity Leave and Parental Leave"];
    $hr_forms[] = ["Employment Verification Letter"];
    $hr_forms[] = ["Background Check Authorization"];
    $hr_forms[] = ["Disclosure of Outside Clients"];
    $hr_forms[] = ["Employment Agreement"];
    $hr_forms[] = ["Independent Contractor Agreement"];
    $hr_forms[] = ["Letter of Offer"];
    $hr_forms[] = ["Employee Non-Disclosure Agreement"];
    $hr_forms[] = ["Employee Self Evaluation"];
    $hr_forms[] = ["HR Complaint"];
    $hr_forms[] = ["Exit Interview"];
    $hr_forms[] = ["Employee Expense Reimbursement"];
    $hr_forms[] = ["Absence Report"];
    $hr_forms[] = ["Employee Accident Report Form"];
    $hr_forms[] = ["Trucking Information"];
    $hr_forms[] = ["Contractor Orientation"];
    $hr_forms[] = ["Contract Welder Inspection Checklist"];
    $hr_forms[] = ["Contractor Pay Agreement"];
    $hr_forms[] = ["Employee Holiday Request Form"];
    $hr_forms[] = ["Employee Coaching Form"];
    $hr_forms[] = ["2016 Alberta Personal Tax Credits Return"];
    $hr_forms[] = ["2016 Personal Tax Credits Return"];
    $hr_forms[] = ["Driver Abstract Statement of Intent"];
    $hr_forms[] = ["PERSONAL PROTECTIVE EQUIPMENT POLICY"];
    $hr_forms[] = ["DRIVER CONSENT FORM"];
    $hr_forms[] = ["Policy and Procedure Notice of Understanding and Intent"];
    $hr_forms[] = ["Employee Personal and Emergency Information"];
    $hr_forms[] = ["Employment Agreement Evergreen"];
    $hr_forms[] = ["Police Information Check"];
    $query = mysqli_query ($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',',`assigned_tile`,',') LIKE '%,hr,%'");
    while ($row = mysqli_fetch_array($query)) {
        $hr_forms[] = [$row['form_id'],$row['name']];
    }

    foreach($hr_forms as $i => $form) {
        $form_name = isset($form[1]) ? $form[1] : $form[0];
        $user_form_id = isset($form[1]) ? $form[0] : '';
        $field_config = explode(',',get_config($dbc, 'hr_fields'));
        $get_hr = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `hr` WHERE `form`='$form_name' AND `deleted`=0"));
        $fields = explode(',',$get_hr['fields']); ?>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= $form_name ?></label>
            <label class="form-checkbox-any-width col-sm-8"><input <?= $get_hr['hrid'] > 0 ? 'checked' : '' ?> data-i="<?= $i ?>" type="checkbox">Enable<?= $get_hr['hrid'] > 0 ? ': '.($get_hr['third_heading_number'] != '' ? $get_hr['third_heading_number'].' '.$get_hr['third_heading'] : ($get_hr['sub_heading_number'] != '' ? $get_hr['sub_heading_number'].' '.$get_hr['sub_heading'] : $get_hr['heading_number'].' '.$get_hr['heading'])).' ('.$get_hr['category'].')' : '' ?></label>
            <div class="block-group col-sm-8 pull-right hr_form" id="config_block_<?= $i ?>" data-form-name="<?= $form_name ?>" data-userformid="<?= $user_form_id ?>" data-hrid="<?= $get_hr['hrid'] ?>" style="<?= $get_hr['hrid'] > 0 ? '' : 'display:none;' ?>">
                <a href="" onclick="get_form_config($(this).closest('.block-group')); return false;">Configure Form</a>
                <input type="hidden" name="deleted" value="0">
            </div>
        </div>
    <?php } ?>
</div>