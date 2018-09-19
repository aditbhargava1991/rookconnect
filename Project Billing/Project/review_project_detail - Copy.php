    Hello
    <button type="submit" name="printtherapistpdf" value="Print Report" class="btn brand-btn pull-right">Print Detail</button>

<?php
function review_project_detail($dbc, $projectid) {
    return 'hello';

    $review = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM project_detail WHERE projectid='$projectid'"));
    $review_project = '';

if($review['detail_issue'] != '') {

$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Issue:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_issue']).'
    </div>
</div>';
 }

if($review['detail_problem'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Problem:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_problem']).'
    </div>
</div>';
}


if($review['detail_gap'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">GAP:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_gap']).'
    </div>
</div>';
}


if($review['detail_technical_uncertainty'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Technical Uncertainty:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_technical_uncertainty']).'
    </div>
</div>';
}


if($review['detail_base_knowledge'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Base Knowledge:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_base_knowledge']).'
    </div>
</div>';
}


if($review['detail_do'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Do:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_do']).'
    </div>
</div>';
}


if($review['detail_already_known'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Already Known:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_already_known']).'
    </div>
</div>';
}


if($review['detail_sources'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Sources:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_sources']).'
    </div>
</div>';
}


if($review['detail_current_designs'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Current Designs:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_current_designs']).'
    </div>
</div>';
}


if($review['detail_known_techniques'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Known Techniques:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_known_techniques']).'
    </div>
</div>';
}


if($review['detail_review_needed'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Review Needed:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_review_needed']).'
    </div>
</div>';
}


if($review['detail_looking_to_achieve'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Looking to Achieve:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_looking_to_achieve']).'
    </div>
</div>';
}


if($review['detail_plan'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Plan:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_plan']).'
    </div>
</div>';
}


if($review['detail_next_steps'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Next Steps:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_next_steps']).'
    </div>
</div>';
}


if($review['detail_learnt'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Learned:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_learnt']).'
    </div>
</div>';
}


if($review['detail_discovered'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Discovered:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_discovered']).'
    </div>
</div>';
}


if($review['detail_tech_advancements'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Tech Advancements:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_tech_advancements']).'
    </div>
</div>';
}


if($review['detail_work'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Work:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_work']).'
    </div>
</div>';
}


if($review['detail_adjustments_needed'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Adjustments Needed:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_adjustments_needed']).'
    </div>
</div>';
}


if($review['detail_future_designs'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Future Designs:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_future_designs']).'
    </div>
</div>';
}


if($review['detail_objective'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Objective:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_objective']).'
    </div>
</div>';
}


if($review['detail_targets'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Targets:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_targets']).'
    </div>
</div>';
}


if($review['detail_audience'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Audience:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_audience']).'
    </div>
</div>';
}


if($review['detail_strategy'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Strategy:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_strategy']).'
    </div>
</div>';
}


if($review['detail_desired_outcome'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Desired Outcome:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_desired_outcome']).'
    </div>
</div>';
}


if($review['detail_actual_outcome'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Actual Outcome:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_actual_outcome']).'
    </div>
</div>';
}


if($review['detail_check'] != ''){
$review_project .= '<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Check:</label>
    <div class="col-sm-8">
        '.html_entity_decode($review['detail_check']).'
    </div>
</div>';
<?php }
    return $review_project;
 }

?>
