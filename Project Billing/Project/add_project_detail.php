<?php
if(!empty($_GET['projectid'])) {
    $projectid = $_GET['projectid'];

    $get_detail = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM project_detail WHERE projectid='$projectid'"));

    $detail_issue = $get_detail['detail_issue'];
    $detail_problem = $get_detail['detail_problem'];
    $detail_technical_uncertainty = $get_detail['detail_technical_uncertainty'];
    $detail_base_knowledge = $get_detail['detail_base_knowledge'];
    $detail_do = $get_detail['detail_do'];
    $detail_already_known = $get_detail['detail_already_known'];
    $detail_sources = $get_detail['detail_sources'];
    $detail_current_designs = $get_detail['detail_current_designs'];
    $detail_known_techniques = $get_detail['detail_known_techniques'];
    $detail_review_needed = $get_detail['detail_review_needed'];
    $detail_looking_to_achieve = $get_detail['detail_looking_to_achieve'];
    $detail_plan = $get_detail['detail_plan'];
    $detail_next_steps = $get_detail['detail_next_steps'];
    $detail_learnt = $get_detail['detail_learnt'];
    $detail_discovered = $get_detail['detail_discovered'];
    $detail_tech_advancements = $get_detail['detail_tech_advancements'];
    $detail_work = $get_detail['detail_work'];
    $detail_adjustments_needed = $get_detail['detail_adjustments_needed'];
    $detail_future_designs = $get_detail['detail_future_designs'];

    $detail_objective = $get_detail['detail_objective'];
    $detail_targets = $get_detail['detail_targets'];
    $detail_audience = $get_detail['detail_audience'];
    $detail_plan = $get_detail['detail_plan'];
    $detail_strategy = $get_detail['detail_strategy'];
    $detail_desired_outcome = $get_detail['detail_desired_outcome'];
    $detail_actual_outcome = $get_detail['detail_actual_outcome'];
    $detail_check = $get_detail['detail_check'];
    $detail_gap = $get_detail['detail_gap'];

    $detail2_issue = $get_detail['detail2_issue'];
    $detail2_plan = $get_detail['detail2_plan'];
    $detail2_do = $get_detail['detail2_do'];
    $detail2_check = $get_detail['detail2_check'];
    $detail2_adjust = $get_detail['detail2_adjust'];

    $detail_procedureid = $get_detail['detail_procedure_id'];
    $detail_quote = $get_detail['detail_quote'];
    $detail_dwg = $get_detail['detail_dwg'];
    $detail_quantity = $get_detail['detail_quantity'];
    $detail_sn = $get_detail['detail_sn'];
    $detail_totalprojectbudget = $get_detail['detail_total_project_budget'];
}
?>
<?php if (strpos(','.$base_field_config.',', ','."Details Issue".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Issue:</label>
    <div class="col-sm-8">
        <textarea name="detail_issue" rows="5" cols="50" class="form-control"><?php echo $detail_issue; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Problem".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Problem:</label>
    <div class="col-sm-8">
        <textarea name="detail_problem" rows="5" cols="50" class="form-control"><?php echo $detail_problem; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details GAP".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">GAP:</label>
    <div class="col-sm-8">
        <textarea name="detail_gap" rows="5" cols="50" class="form-control"><?php echo $detail_gap; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Technical Uncertainty".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Technical Uncertainty:</label>
    <div class="col-sm-8">
        <textarea name="detail_technical_uncertainty" rows="5" cols="50" class="form-control"><?php echo $detail_technical_uncertainty; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Base Knowledge".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Base Knowledge:</label>
    <div class="col-sm-8">
        <textarea name="detail_base_knowledge" rows="5" cols="50" class="form-control"><?php echo $detail_base_knowledge; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Do".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Do:</label>
    <div class="col-sm-8">
        <textarea name="detail_do" rows="5" cols="50" class="form-control"><?php echo $detail_do; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Already Known".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Already Known:</label>
    <div class="col-sm-8">
        <textarea name="detail_already_known" rows="5" cols="50" class="form-control"><?php echo $detail_already_known; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Sources".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Sources:</label>
    <div class="col-sm-8">
        <textarea name="detail_sources" rows="5" cols="50" class="form-control"><?php echo $detail_sources; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Current Designs".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Current Designs:</label>
    <div class="col-sm-8">
        <textarea name="detail_current_designs" rows="5" cols="50" class="form-control"><?php echo $detail_current_designs; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Known Techniques".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Known Techniques:</label>
    <div class="col-sm-8">
        <textarea name="detail_known_techniques" rows="5" cols="50" class="form-control"><?php echo $detail_known_techniques; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Review Needed".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Review Needed:</label>
    <div class="col-sm-8">
        <textarea name="detail_review_needed" rows="5" cols="50" class="form-control"><?php echo $detail_review_needed; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Looking to Achieve".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Looking to Achieve:</label>
    <div class="col-sm-8">
        <textarea name="detail_looking_to_achieve" rows="5" cols="50" class="form-control"><?php echo $detail_looking_to_achieve; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Plan".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Plan:</label>
    <div class="col-sm-8">
        <textarea name="detail_plan" rows="5" cols="50" class="form-control"><?php echo $detail_plan; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Next Steps".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Next Steps:</label>
    <div class="col-sm-8">
        <textarea name="detail_next_steps" rows="5" cols="50" class="form-control"><?php echo $detail_next_steps; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Learnt".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Learned:</label>
    <div class="col-sm-8">
        <textarea name="detail_learnt" rows="5" cols="50" class="form-control"><?php echo $detail_learnt; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Discovered".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Discovered:</label>
    <div class="col-sm-8">
        <textarea name="detail_discovered" rows="5" cols="50" class="form-control"><?php echo $detail_discovered; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Tech Advancements".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Tech Advancements:</label>
    <div class="col-sm-8">
        <textarea name="detail_tech_advancements" rows="5" cols="50" class="form-control"><?php echo $detail_tech_advancements; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Work".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Work:</label>
    <div class="col-sm-8">
        <textarea name="detail_work" rows="5" cols="50" class="form-control"><?php echo $detail_work; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Adjustments Needed".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Adjustments Needed:</label>
    <div class="col-sm-8">
        <textarea name="detail_adjustments_needed" rows="5" cols="50" class="form-control"><?php echo $detail_adjustments_needed; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Future Designs".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Future Designs:</label>
    <div class="col-sm-8">
        <textarea name="detail_future_designs" rows="5" cols="50" class="form-control"><?php echo $detail_future_designs; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Objective".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Objective:</label>
    <div class="col-sm-8">
        <textarea name="detail_objective" rows="5" cols="50" class="form-control"><?php echo $detail_objective; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Details Targets".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Targets:</label>
    <div class="col-sm-8">
        <textarea name="detail_targets" rows="5" cols="50" class="form-control"><?php echo $detail_targets; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos(','.$base_field_config.',', ','."Details Audience".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Audience:</label>
    <div class="col-sm-8">
        <textarea name="detail_audience" rows="5" cols="50" class="form-control"><?php echo $detail_audience; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos(','.$base_field_config.',', ','."Details Strategy".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Strategy:</label>
    <div class="col-sm-8">
        <textarea name="detail_strategy" rows="5" cols="50" class="form-control"><?php echo $detail_strategy; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos(','.$base_field_config.',', ','."Details Desired Outcome".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Desired Outcome:</label>
    <div class="col-sm-8">
        <textarea name="detail_desired_outcome" rows="5" cols="50" class="form-control"><?php echo $detail_desired_outcome; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos(','.$base_field_config.',', ','."Details Actual Outcome".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Actual Outcome:</label>
    <div class="col-sm-8">
        <textarea name="detail_actual_outcome" rows="5" cols="50" class="form-control"><?php echo $detail_actual_outcome; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos(','.$base_field_config.',', ','."Details Check".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Check:</label>
    <div class="col-sm-8">
        <textarea name="detail_check" rows="5" cols="50" class="form-control"><?php echo $detail_check; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos(','.$base_field_config.',', ','."Project Detail".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Project Detail:</label>
    <div class="col-sm-8">
        <textarea name="detail2_issue" rows="5" cols="50" class="form-control"><?php echo $detail2_issue; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos(','.$base_field_config.',', ','."Details2 Adjust".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Adjust:</label>
    <div class="col-sm-8">
        <textarea name="detail2_adjust" rows="5" cols="50" class="form-control"><?php echo $detail2_adjust; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Details Procedure ID".',') !== FALSE) { ?>
<div class="form-group">
    <label for="procedureid" class="col-sm-4 control-label text-right">Procedure ID:</label>
    <div class="col-sm-8">
        <input name="procedureid" value="<?php echo $detail_procedureid; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Details Quote Num".',') !== FALSE) { ?>
<div class="form-group">
    <label for="quote" class="col-sm-4 control-label text-right">Quote#:</label>
    <div class="col-sm-8">
        <input name="quote" value="<?php echo $detail_quote; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Details DWG Num".',') !== FALSE) { ?>
<div class="form-group">
    <label for="dwg" class="col-sm-4 control-label text-right">DWG#:</label>
    <div class="col-sm-8">
        <input name="dwg" value="<?php echo $detail_dwg; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Details Quantity".',') !== FALSE) { ?>
<div class="form-group">
    <label for="quantity" class="col-sm-4 control-label text-right">Quantity:</label>
    <div class="col-sm-8">
        <input name="quantity" value="<?php echo $detail_quantity; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Details SN".',') !== FALSE) { ?>
<div class="form-group">
    <label for="sn" class="col-sm-4 control-label text-right">S/N:</label>
    <div class="col-sm-8">
        <input name="sn" value="<?php echo $detail_sn; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Details Customer PO AFE Num".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Customer PO/AFE#:</label>
    <div class="col-sm-8">
        <input name="afe_number" value="<?php echo $afe_number; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Details Total Project Budget".',') !== FALSE) { ?>
<div class="form-group">
    <label for="totalprojectbudget" class="col-sm-4 control-label text-right">Total Project Budget:</label>
    <div class="col-sm-8">
        <input name="totalprojectbudget" value="<?php echo $detail_totalprojectbudget; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Submit</button>