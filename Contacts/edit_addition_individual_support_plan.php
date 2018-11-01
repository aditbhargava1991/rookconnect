<?php if($field_option == 'Client Support Plan') {
$get_contact = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `individual_support_plan` WHERE `support_contact`='$contactid'"));
$ispid = $get_contact['individualsupportplanid'];
$dayprimary_contact_category = $get_contact['dayprimary_contact_category'];
$dayprimary_contact = $get_contact['dayprimary_contact'];
$daytl_contact_category = $get_contact['daytl_contact_category'];
$daytl_contact = $get_contact['daytl_contact'];
$daykey_contact_category = $get_contact['daykey_contact_category'];
$daykey_contact = $get_contact['daykey_contact'];
$resiprimary_contact_category = $get_contact['resiprimary_contact_category'];
$resiprimary_contact = $get_contact['resiprimary_contact'];
$resitl_contact_category = $get_contact['resitl_contact_category'];
$resitl_contact = $get_contact['resitl_contact'];
$resikey_contact_category = $get_contact['resikey_contact_category'];
$resikey_contact = $get_contact['resikey_contact'];
$guardianprimary_contact_category = $get_contact['guardianprimary_contact_category'];
$guardianprimary_contact = $get_contact['guardianprimary_contact'];
$guardiansecondary_contact_category = $get_contact['guardiansecondary_contact_category'];
$guardiansecondary_contact = $get_contact['guardiansecondary_contact'];
$guardianalt_contact_category = $get_contact['guardianalt_contact_category'];
$guardianalt_contact = $get_contact['guardianalt_contact'];
$eme_contact_category = $get_contact['eme_contact_category'];
$eme_contact = $get_contact['eme_contact'];
$isp_start_date = $get_contact['isp_start_date'];
$isp_review_date = $get_contact['isp_review_date'];
$isp_end_date = $get_contact['isp_end_date'];
$isp_quality = $get_contact['isp_quality'];
$isp_goals = explode('*#*', $get_contact['isp_goals']);
$isp_needs = $get_contact['isp_needs'];
$isp_strategies = $get_contact['isp_strategies'];
$isp_objectives = $get_contact['isp_objectives'];
$isp_sis = $get_contact['isp_sis'];
$isp_detail_responsible_contact_category = $get_contact['isp_detail_responsible_contact_category'];
$isp_detail_responsible_contact = $get_contact['isp_detail_responsible_contact'];
$isp_updates = $get_contact['isp_updates'];
$isp_notes = $get_contact['isp_notes'];

$support_contact_gender = $get_contact['support_contact_gender'];
$support_contact_school = $get_contact['support_contact_school'];
$support_contact_grade = $get_contact['support_contact_grade'];
$support_contact_diagnosis = $get_contact['support_contact_diagnosis'];
$support_contact_date_of_birth = $get_contact['support_contact_date_of_birth'];
$support_contact_other_supports = $get_contact['support_contact_other_supports'];

$daycoordinator_contact_category = $get_contact['daycoordinator_contact_category'];
$daycoordinator_contact = $get_contact['daycoordinator_contact'];
$daycoordinator_contact_hours = $get_contact['daycoordinator_contact_hours'];
$daycoordinator_contact_phone = $get_contact['daycoordinator_contact_phone'];
$daycoordinator_contact_email = $get_contact['daycoordinator_contact_email'];

$daysl_contact_category = $get_contact['daysl_contact_category'];
$daysl_contact = $get_contact['daysl_contact'];
$daysl_contact_hours = $get_contact['daysl_contact_hours'];
$daysl_contact_phone = $get_contact['daysl_contact_phone'];
$daysl_contact_email = $get_contact['daysl_contact_email'];

$dayot_contact_category = $get_contact['dayot_contact_category'];
$dayot_contact = $get_contact['dayot_contact'];
$dayot_contact_hours = $get_contact['dayot_contact_hours'];
$dayot_contact_phone = $get_contact['dayot_contact_phone'];
$dayot_contact_email = $get_contact['dayot_contact_email'];

$daypp_contact_category = $get_contact['daypp_contact_category'];
$daypp_contact = $get_contact['daypp_contact'];
$daypp_contact_hours = $get_contact['daypp_contact_hours'];
$daypp_contact_phone = $get_contact['daypp_contact_phone'];
$daypp_contact_email = $get_contact['daypp_contact_email'];

$dayphysio_contact_category = $get_contact['dayphysio_contact_category'];
$dayphysio_contact = $get_contact['dayphysio_contact'];
$dayphysio_contact_hours = $get_contact['dayphysio_contact_hours'];
$dayphysio_contact_phone = $get_contact['dayphysio_contact_phone'];
$dayphysio_contact_email = $get_contact['dayphysio_contact_email'];

$dayaide_contact_category = $get_contact['dayaide_contact_category'];
$dayaide_contact = $get_contact['dayaide_contact'];
$dayaide_contact_hours = $get_contact['dayaide_contact_hours'];
$dayaide_contact_phone = $get_contact['dayaide_contact_phone'];
$dayaide_contact_email = $get_contact['dayaide_contact_email'];

$dayfscd_contact_category = $get_contact['dayfscd_contact_category'];
$dayfscd_contact = $get_contact['dayfscd_contact'];
$dayfscd_contact_hours = $get_contact['dayfscd_contact_hours'];
$dayfscd_contact_phone = $get_contact['dayfscd_contact_phone'];
$dayfscd_contact_email = $get_contact['dayfscd_contact_email'];

$goal1_date = $get_contact['goal1_date'];
$goal1_outcomes = $get_contact['goal1_outcomes'];
$goal2_date = $get_contact['goal2_date'];
$goal2_outcomes = $get_contact['goal2_outcomes'];
$goal3_date = $get_contact['goal3_date'];
$goal3_outcomes = $get_contact['goal3_outcomes'];
$goal4_date = $get_contact['goal4_date'];
$goal4_outcomes = $get_contact['goal4_outcomes'];
$longterm_goal1_notes = $get_contact['longterm_goal1_notes'];

$rating_behaviour_objective = $get_contact['rating_behaviour_objective'];
$rating_behaviour_child = $get_contact['rating_behaviour_child'];
$rating_behaviour_child_date = $get_contact['rating_behaviour_child_date'];
$rating_behaviour_child_rating = $get_contact['rating_behaviour_child_rating'];
$rating_behaviour_family = $get_contact['rating_behaviour_family'];
$rating_behaviour_family_date = $get_contact['rating_behaviour_family_date'];
$rating_behaviour_family_rating = $get_contact['rating_behaviour_family_rating'];
$rating_behaviour_targeted = $get_contact['rating_behaviour_targeted'];
$rating_behaviour_targeted_date = $get_contact['rating_behaviour_targeted_date'];
$rating_behaviour_targeted_rating = $get_contact['rating_behaviour_targeted_rating'];
$rating_behaviour_strategies_individual = $get_contact['rating_behaviour_strategies_individual'];
$rating_behaviour_strategies_family = $get_contact['rating_behaviour_strategies_family'];
$rating_behaviour_review_date = $get_contact['rating_behaviour_review_date'];
$rating_behaviour_parent_update = $get_contact['rating_behaviour_parent_update'];
$rating_behaviour_therapist_update = $get_contact['rating_behaviour_therapist_update'];
$rating_behaviour_aide_update = $get_contact['rating_behaviour_aide_update'];
$rating_behaviour_next_step = $get_contact['rating_behaviour_next_step'];

$rating_comm_objective = $get_contact['rating_comm_objective'];
$rating_comm_child = $get_contact['rating_comm_child'];
$rating_comm_child_date = $get_contact['rating_comm_child_date'];
$rating_comm_child_rating = $get_contact['rating_comm_child_rating'];
$rating_comm_family = $get_contact['rating_comm_family'];
$rating_comm_family_date = $get_contact['rating_comm_family_date'];
$rating_comm_family_rating = $get_contact['rating_comm_family_rating'];
$rating_comm_targeted = $get_contact['rating_comm_targeted'];
$rating_comm_targeted_date = $get_contact['rating_comm_targeted_date'];
$rating_comm_targeted_rating = $get_contact['rating_comm_targeted_rating'];
$rating_comm_strategies_individual = $get_contact['rating_comm_strategies_individual'];
$rating_comm_strategies_family = $get_contact['rating_comm_strategies_family'];
$rating_comm_review_date = $get_contact['rating_comm_review_date'];
$rating_comm_parent_update = $get_contact['rating_comm_parent_update'];
$rating_comm_therapist_update = $get_contact['rating_comm_therapist_update'];
$rating_comm_aide_update = $get_contact['rating_comm_aide_update'];
$rating_comm_next_step = $get_contact['rating_comm_next_step'];

$rating_physical_objective = $get_contact['rating_physical_objective'];
$rating_physical_child = $get_contact['rating_physical_child'];
$rating_physical_child_date = $get_contact['rating_physical_child_date'];
$rating_physical_child_rating = $get_contact['rating_physical_child_rating'];
$rating_physical_family = $get_contact['rating_physical_family'];
$rating_physical_family_date = $get_contact['rating_physical_family_date'];
$rating_physical_family_rating = $get_contact['rating_physical_family_rating'];
$rating_physical_targeted = $get_contact['rating_physical_targeted'];
$rating_physical_targeted_date = $get_contact['rating_physical_targeted_date'];
$rating_physical_targeted_rating = $get_contact['rating_physical_targeted_rating'];
$rating_physical_strategies_individual = $get_contact['rating_physical_strategies_individual'];
$rating_physical_strategies_family = $get_contact['rating_physical_strategies_family'];
$rating_physical_review_date = $get_contact['rating_physical_review_date'];
$rating_physical_parent_update = $get_contact['rating_physical_parent_update'];
$rating_physical_therapist_update = $get_contact['rating_physical_therapist_update'];
$rating_physical_aide_update = $get_contact['rating_physical_aide_update'];
$rating_physical_next_step = $get_contact['rating_physical_next_step'];

$rating_cognitive_objective = $get_contact['rating_cognitive_objective'];
$rating_cognitive_child = $get_contact['rating_cognitive_child'];
$rating_cognitive_child_date = $get_contact['rating_cognitive_child_date'];
$rating_cognitive_child_rating = $get_contact['rating_cognitive_child_rating'];
$rating_cognitive_family = $get_contact['rating_cognitive_family'];
$rating_cognitive_family_date = $get_contact['rating_cognitive_family_date'];
$rating_cognitive_family_rating = $get_contact['rating_cognitive_family_rating'];
$rating_cognitive_targeted = $get_contact['rating_cognitive_targeted'];
$rating_cognitive_targeted_date = $get_contact['rating_cognitive_targeted_date'];
$rating_cognitive_targeted_rating = $get_contact['rating_cognitive_targeted_rating'];
$rating_cognitive_strategies_individual = $get_contact['rating_cognitive_strategies_individual'];
$rating_cognitive_strategies_family = $get_contact['rating_cognitive_strategies_family'];
$rating_cognitive_review_date = $get_contact['rating_cognitive_review_date'];
$rating_cognitive_parent_update = $get_contact['rating_cognitive_parent_update'];
$rating_cognitive_therapist_update = $get_contact['rating_cognitive_therapist_update'];
$rating_cognitive_aide_update = $get_contact['rating_cognitive_aide_update'];
$rating_cognitive_next_step = $get_contact['ratin_cognitive_next_step'];

$rating_safety_objective = $get_contact['rating_safety_objective'];
$rating_safety_child = $get_contact['rating_safety_child'];
$rating_safety_child_date = $get_contact['rating_safety_child_date'];
$rating_safety_child_rating = $get_contact['rating_safety_child_rating'];
$rating_safety_family = $get_contact['rating_safety_family'];
$rating_safety_family_date = $get_contact['rating_safety_family_date'];
$rating_safety_family_rating = $get_contact['rating_safety_family_rating'];
$rating_safety_targeted = $get_contact['rating_safety_targeted'];
$rating_safety_targeted_date = $get_contact['rating_safety_targeted_date'];
$rating_safety_targeted_rating = $get_contact['rating_safety_targeted_rating'];
$rating_safety_strategies_individual = $get_contact['rating_safety_strategies_individual'];
$rating_safety_strategies_family = $get_contact['rating_safety_strategies_family'];
$rating_safety_review_date = $get_contact['rating_safety_review_date'];
$rating_safety_parent_update = $get_contact['rating_safety_parent_update'];
$rating_safety_therapist_update = $get_contact['rating_safety_therapist_update'];
$rating_safety_aide_update = $get_contact['rating_safety_aide_update'];
$rating_safety_next_step = $get_contact['rating_safety_next_step'];

$signatures_parent = $get_contact['signatures_parent'];
$signatures_parent_name = $get_contact['signatures_parent_name'];
$signatures_parent_date = $get_contact['signatures_parent_date'];
$signatures_coordinator = $get_contact['signatures_coordinator'];
$signatures_coordinator_name = $get_contact['signatures_coordinator_name'];
$signatures_coordinator_date = $get_contact['signatures_coordinator_date'];
$signatures_sl = $get_contact['signatures_sl'];
$signatures_sl_name = $get_contact['signatures_sl_name'];
$signatures_sl_date = $get_contact['signatures_sl_date'];
$signatures_ot = $get_contact['signatures_ot'];
$signatures_ot_name = $get_contact['signatures_ot_name'];
$signatures_ot_date = $get_contact['signatures_ot_date'];
$signatures_pp = $get_contact['signatures_pp'];
$signatures_pp_name = $get_contact['signatures_pp_name'];
$signatures_pp_date = $get_contact['signatures_pp_date'];
$signatures_physio = $get_contact['signatures_physio'];
$signatures_physio_name = $get_contact['signatures_physio_name'];
$signatures_physio_date = $get_contact['signatures_physio_date'];
$signatures_aide = $get_contact['signatures_aide'];
$signatures_aide_name = $get_contact['signatures_aide_name'];
$signatures_aide_date = $get_contact['signatures_aide_date'];

$value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT individual_support_plan FROM field_config"))['individual_support_plan'].',';
?>

<?php if (strpos($value_config, ',Service Individual Gender,') !== FALSE) { ?>
    <div class="form-group">
        <label class="col-sm-4 control-label">Gender</label>
        <div class="col-sm-8">
            <input type="text" name="support_contact_gender" class="form-control" value="<?= $support_contact_gender ?>" data-field="support_contact_gender" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" data-contactid-category-field="support_contact_category">
        </div>
    </div>
<?php } ?>
<?php if (strpos($value_config, ',Service Individual School,') !== FALSE) { ?>
    <div class="form-group">
        <label class="col-sm-4 control-label">School</label>
        <div class="col-sm-8">
            <input type="text" name="support_contact_school" class="form-control" value="<?= $support_contact_school ?>" data-field="support_contact_school" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact">
        </div>
    </div>
<?php } ?>
<?php if (strpos($value_config, ',Service Individual Grade/Class,') !== FALSE) { ?>
    <div class="form-group">
        <label class="col-sm-4 control-label">Grade/Class</label>
        <div class="col-sm-8">
            <input type="text" name="support_contact_grade" class="form-control" value="<?= $support_contact_grade ?>" data-field="support_contact_grade" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact">
        </div>
    </div>
<?php } ?>
<?php if (strpos($value_config, ',Service Individual Diagnosis,') !== FALSE) { ?>
    <div class="form-group">
        <label class="col-sm-4 control-label">Diagnosis</label>
        <div class="col-sm-8">
            <input type="text" name="support_contact_diagnosis" class="form-control" value="<?= $support_contact_diagnosis ?>" data-field="support_contact_diagnosis" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact">
        </div>
    </div>
<?php } ?>
<?php if (strpos($value_config, ',Service Individual Date of Birth,') !== FALSE) { ?>
    <div class="form-group">
        <label class="col-sm-4 control-label">Date of Birth</label>
        <div class="col-sm-8">
            <input type="text" name="support_contact_date_of_birth" class="form-control datepicker" value="<?= $support_contact_date_of_birth ?>" data-field="support_contact_date_of_birth" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact">
        </div>
    </div>
<?php } ?>
<?php if (strpos($value_config, ',Service Individual Other Supports,') !== FALSE) { ?>
    <div class="form-group">
        <label class="col-sm-4 control-label">Other Supports</label>
        <div class="col-sm-8">
            <input type="text" name="support_contact_other_supports" class="form-control" value="<?= $support_contact_other_supports ?>" data-field="support_contact_other_supports" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact">
        </div>
    </div>
<?php } ?>

<?php if (strpos($value_config, ',Day Program Support Team,') !== FALSE) { ?>
    <h4>Day Program Support Team</h4>
    <?php if (strpos($value_config, ',Day Program Support Team Primary Contact,') !== FALSE) { ?>
        <h5>Primary Contact</h5>
        <?php foreach(explode(',',$dayprimary_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_1', 'dayprimary_contact_category[]', explode(',',$dayprimary_contact_category)[$i], 'dayprimary_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_1', 'dayprimary_contact[]', $multicontactid, '',explode(',',$dayprimary_contact_category)[$i], 'dayprimary_contact', $ispid); ?>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (strpos($value_config, ',Day Program Support Team Lead,') !== FALSE) { ?>
        <h5>Team Lead</h5>
        <?php foreach(explode(',',$daytl_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_2', 'daytl_contact_category[]', explode(',',$daytl_contact_category)[$i], 'daytl_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_2', 'daytl_contact[]', $multicontactid, '',explode(',',$daytl_contact_category)[$i], 'daytl_contact', $ispid); ?>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (strpos($value_config, ',Day Program Support Team Key Supports,') !== FALSE) { ?>
        <h5>Key Supports</h5>

            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_3', 'daykey_contact_category', $daykey_contact_category, 'daykey_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_3', 'daykey_contact[]', $daykey_contact, '',$daykey_contact_category, 'daykey_contact', $ispid); ?>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Day Program Support Team Coordinator,') !== FALSE) { ?>
        <h5>Coordinator</h5>
        <?php foreach(explode(',',$daycoordinator_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_4', 'daycoordinator_contact_category[]', explode(',',$daycoordinator_contact_category)[$i], 'daycoordinator_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_4', 'daycoordinator_contact[]', $multicontactid, '',explode(',',$daycoordinator_contact_category)[$i], 'daycoordinator_contact', $ispid); ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Hours</label>
                    <div class="col-sm-8">
                        <input type="text" name="daycoordinator_contact_hours[]" class="form-control" value="<?= explode('*#*', $daycoordinator_contact_hours)[$i] ?>" data-field="daycoordinator_contact_hours" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Phone #</label>
                    <div class="col-sm-8">
                        <input type="text" name="daycoordinator_contact_phone[]" class="form-control" value="<?= explode('*#*', $daycoordinator_contact_phone)[$i] ?>" data-field="daycoordinator_contact_phone" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Email Address</label>
                    <div class="col-sm-8">
                        <input type="text" name="daycoordinator_contact_email[]" class="form-control" value="<?= explode('*#*', $daycoordinator_contact_email)[$i] ?>" data-field="daycoordinator_contact_email" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (strpos($value_config, ',Day Program Support Team Speech-Language Pathologist,') !== FALSE) { ?>
        <h5>Speech-Language Pathologist</h5>
        <?php foreach(explode(',',$daysl_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_5', 'daysl_contact_category[]', explode(',',$daysl_contact_category)[$i], 'daysl_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_5', 'daysl_contact[]', $multicontactid, '',explode(',',$daysl_contact_category)[$i], 'daysl_contact', $ispid); ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Hours</label>
                    <div class="col-sm-8">
                        <input type="text" name="daysl_contact_hours[]" class="form-control" value="<?= explode('*#*', $daysl_contact_hours)[$i] ?>" data-field="daysl_contact_hours" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Phone #</label>
                    <div class="col-sm-8">
                        <input type="text" name="daysl_contact_phone[]" class="form-control" value="<?= explode('*#*', $daysl_contact_phone)[$i] ?>" data-field="daysl_contact_phone" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Email Address</label>
                    <div class="col-sm-8">
                        <input type="text" name="daysl_contact_email[]" class="form-control" value="<?= explode('*#*', $daysl_contact_email)[$i] ?>" data-field="daysl_contact_email" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>

    <?php if (strpos($value_config, ',Day Program Support Team Speech-Language Pathologist,') !== FALSE) { ?>
        <h5>Speech-Language Pathologist</h5>
        <?php foreach(explode(',',$dayot_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_6', 'dayot_contact_category[]', explode(',',$dayot_contact_category)[$i], 'dayot_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_6', 'dayot_contact[]', $multicontactid, '',explode(',',$dayot_contact_category)[$i], 'dayot_contact', $ispid); ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Hours</label>
                    <div class="col-sm-8">
                        <input type="text" name="dayot_contact_hours[]" class="form-control" value="<?= explode('*#*', $dayot_contact_hours)[$i] ?>" data-field="daysl_contact_email" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Phone #</label>
                    <div class="col-sm-8">
                        <input type="text" name="dayot_contact_phone[]" class="form-control" value="<?= explode('*#*', $dayot_contact_phone)[$i] ?>" data-field="daysl_contact_email" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Email Address</label>
                    <div class="col-sm-8">
                        <input type="text" name="dayot_contact_email[]" class="form-control" value="<?= explode('*#*', $dayot_contact_email)[$i] ?>" data-field="daysl_contact_email" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (strpos($value_config, ',Day Program Support Team Provisional Psychologist,') !== FALSE) { ?>
        <h5>Provisional Psychologist</h5>
        <?php foreach(explode(',',$daypp_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_7', 'daypp_contact_category[]', explode(',',$daypp_contact_category)[$i], 'daypp_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_7', 'daypp_contact[]', $multicontactid, '',explode(',',$daypp_contact_category)[$i], 'daypp_contact', $ispid); ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Hours</label>
                    <div class="col-sm-8">
                        <input type="text" name="daypp_contact_hours[]" class="form-control" value="<?= explode('*#*', $daypp_contact_hours)[$i] ?>" data-field="daypp_contact_hours" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Phone #</label>
                    <div class="col-sm-8">
                        <input type="text" name="daypp_contact_phone[]" class="form-control" value="<?= explode('*#*', $daypp_contact_phone)[$i] ?>" data-field="daypp_contact_phone" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Email Address</label>
                    <div class="col-sm-8">
                        <input type="text" name="daypp_contact_email[]" class="form-control" value="<?= explode('*#*', $daypp_contact_email)[$i] ?>" data-field="daypp_contact_email" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (strpos($value_config, ',Day Program Support Team Aides,') !== FALSE) { ?>
        <h5>Aide(s)</h5>
        <?php foreach(explode(',',$dayaide_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_8', 'dayaide_contact_category[]', explode(',',$dayaide_contact_category)[$i], 'dayaide_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_8', 'dayaide_contact[]', $multicontactid, '',explode(',',$dayaide_contact_category)[$i], 'dayaide_contact', $ispid); ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Hours</label>
                    <div class="col-sm-8">
                        <input type="text" name="dayaide_contact_hours[]" class="form-control" value="<?= explode('*#*', $dayaide_contact_hours)[$i] ?>" data-field="dayaide_contact_hours" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Phone #</label>
                    <div class="col-sm-8">
                        <input type="text" name="dayaide_contact_phone[]" class="form-control" value="<?= explode('*#*', $dayaide_contact_phone)[$i] ?>" data-field="dayaide_contact_phone" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Email Address</label>
                    <div class="col-sm-8">
                        <input type="text" name="dayaide_contact_email[]" class="form-control" value="<?= explode('*#*', $dayaide_contact_email)[$i] ?>" data-field="dayaide_contact_email" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (strpos($value_config, ',Day Program Support Team FSCD Worker,') !== FALSE) { ?>
        <h5>FSCD Worker</h5>
        <?php foreach(explode(',',$dayfscd_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_9', 'dayfscd_contact_category[]', explode(',',$dayfscd_contact_category)[$i], 'dayfscd_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_9', 'dayfscd_contact[]', $multicontactid, '',explode(',',$dayfscd_contact_category)[$i], 'dayfscd_contact', $ispid); ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Hours</label>
                    <div class="col-sm-8">
                        <input type="text" name="dayfscd_contact_hours[]" class="form-control" value="<?= explode('*#*', $dayfscd_contact_hours)[$i] ?>" data-field="dayfscd_contact_hours" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Phone #</label>
                    <div class="col-sm-8">
                        <input type="text" name="dayfscd_contact_phone[]" class="form-control" value="<?= explode('*#*', $dayfscd_contact_phone)[$i] ?>" data-field="dayfscd_contact_phone" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Email Address</label>
                    <div class="col-sm-8">
                        <input type="text" name="dayfscd_contact_email[]" class="form-control" value="<?= explode('*#*', $dayfscd_contact_email)[$i] ?>" data-field="dayfscd_contact_email" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-delimiter="*#*" data-contactid-field="support_contact">
                    </div>
                </div>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
<?php } ?>

<?php if (strpos($value_config, ',Residential Support Team,') !== FALSE) { ?>
    <h4>Residential Support Team</h4>
    <?php if (strpos($value_config, ',Residential Support Team Primary Contact,') !== FALSE) { ?>
        <h5>Primary Contact</h5>
        <?php foreach(explode(',',$resiprimary_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_4', 'resiprimary_contact_category[]', explode(',',$resiprimary_contact_category)[$i], 'resiprimary_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_4', 'resiprimary_contact[]', $multicontactid,'',explode(',',$resiprimary_contact_category)[$i], 'resiprimary_contact', $ispid); ?>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (strpos($value_config, ',Residential Support Team Lead,') !== FALSE) { ?>
        <h5>Team Lead</h5>
        <?php foreach(explode(',',$resitl_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_5', 'resitl_contact_category[]', explode(',',$resitl_contact_category)[$i], 'resitl_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_5', 'resitl_contact[]', $multicontactid,'',explode(',',$resitl_contact_category)[$i], 'resitl_contact', $ispid); ?>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (strpos($value_config, ',Residential Support Team Key Supports,') !== FALSE) { ?>
        <h5>Key Supports</h5>

            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_6', 'resikey_contact_category', $resikey_contact_category, 'resikey_contact_category', $ispid); ?>
                <?php echo contact_call($dbc, 'contact_6', 'resikey_contact[]', $resikey_contact, '',$resikey_contact_category, 'resikey_contact', $ispid); ?>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
    <?php } ?>
<?php } ?>


<?php if (strpos($value_config, ',Guardian,') !== FALSE) { ?>
    <h4>Guardian</h4>
    <?php if (strpos($value_config, ',Guardian Primary Contact,') !== FALSE) { ?>
        <h5>Primary Contact</h5>
        <?php foreach(explode(',',$guardianprimary_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_4', 'guardianprimary_contact_category[]', explode(',',$guardianprimary_contact_category)[$i], 'guardianprimary_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_4', 'guardianprimary_contact[]', $multicontactid,'',explode(',',$guardianprimary_contact_category)[$i], 'guardianprimary_contact', $ispid); ?>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (strpos($value_config, ',Guardian Secondary Contact,') !== FALSE) { ?>
        <h5>Secondary Contact</h5>
        <?php foreach(explode(',',$guardiansecondary_contact) as $i => $multicontactid) { ?>
            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_5', 'guardiansecondary_contact_category[]', explode(',',$guardiansecondary_contact_category)[$i], 'guardiansecondary_contact_category', $ispid); ?>

                <?php echo contact_call($dbc, 'contact_5', 'guardiansecondary_contact_category[]', $multicontactid,'',explode(',',$guardiansecondary_contact_category)[$i], 'guardiansecondary_contact_category', $ispid); ?>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (strpos($value_config, ',Guardian Alternates,') !== FALSE) { ?>
        <h5>Alternates</h5>

            <div class="contact_group">
                <?php echo contact_category_call($dbc, 'contact_category_6', 'guardianalt_contact_category', $guardianalt_contact_category, 'guardianalt_contact_category', $ispid); ?>
                <?php echo contact_call($dbc, 'contact_6', 'guardianalt_contact[]', $guardianalt_contact, '',$guardianalt_contact_category, 'guardianalt_contact', $ispid); ?>
                <span class="pull-right">
                    <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
                    <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
                </span>
                <div class="clearfix"></div>
            </div>
    <?php } ?>
<?php } ?>

<?php if (strpos($value_config, ',Family Support Goals,') !== FALSE) { ?>
    <h4>Family Support Goals</h4>
    <?php if (strpos($value_config, ',Family Support Goals Goal 1,') !== FALSE) { ?>
        <h5>Goal #1: Connect family to appropriate community resources</h5>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date Discussed:</label>
            <div class="col-sm-8">
                <input type="text" name="goal1_date" class="form-control datepicker" value="<?= $goal1_date ?>" data-field="goal1_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Outcomes:</label>
            <div class="col-sm-8">
                <textarea name="goal1_outcomes" rows="5" cols="50" class="form-control" data-field="goal1_outcomes" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"><?php echo html_entity_decode($goal1_outcomes); ?></textarea>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Family Support Goals Goal 2,') !== FALSE) { ?>
        <h5>Goal #2: Provide information about safety for children: a) Options for carrying identification b) Vulnerable persons registry c) Environmental adaptations</h5>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date Discussed:</label>
            <div class="col-sm-8">
                <input type="text" name="goal2_date" class="form-control datepicker" value="<?= $goal2_date ?>" data-field="goal2_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Outcomes:</label>
            <div class="col-sm-8">
                <textarea name="goal2_outcomes" rows="5" cols="50" class="form-control" data-field="goal2_outcomes" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"><?php echo html_entity_decode($goal2_outcomes); ?></textarea>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Family Support Goals Goal 3,') !== FALSE) { ?>
        <h5>Goal #3: Create a one-page profile that can easily be shared with others involved in the child's care</h5>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date Discussed:</label>
            <div class="col-sm-8">
                <input type="text" name="goal3_date" class="form-control datepicker" value="<?= $goal3_date ?>" data-field="goal3_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Outcomes:</label>
            <div class="col-sm-8">
                <textarea name="goal3_outcomes" rows="5" cols="50" class="form-control" data-field="goal3_outcomes" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"><?php echo html_entity_decode($goal3_outcomes); ?></textarea>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Family Support Goals Goal 4,') !== FALSE) { ?>
        <h5>Goal #4: Provide information about typical development and what parents can expect across the lifespan</h5>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date Discussed:</label>
            <div class="col-sm-8">
                <input type="text" name="goal4_date" class="form-control datepicker" value="<?= $goal4_date ?>" data-field="goal4_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Outcomes:</label>
            <div class="col-sm-8">
                <textarea name="goal4_outcomes" rows="5" cols="50" class="form-control" data-field="goal4_outcomes" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"><?php echo html_entity_decode($goal4_outcomes); ?></textarea>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Family Support Goals Long Term Goal 1,') !== FALSE) { ?>
        <h5>Long Term Goal #1: In order to meet this goal that has been identified by the family, the following objectives need to be achieved</h5>
        <div class="form-group">
            <label class="col-sm-4 control-label">Notes:</label>
            <div class="col-sm-8">
                <textarea name="longterm_goal1_notes" rows="5" cols="50" class="form-control" data-field="longterm_goal1_notes" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"><?php echo html_entity_decode($longterm_goal1_notes); ?></textarea>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<?php if (strpos($value_config, ',Emergency Contacts,') !== FALSE) { ?>
    <h4>Emergency Contacts</h4>
	<div class="contact_group">
		<?php echo contact_category_call($dbc, 'contact_category_10', 'eme_contact_category', $eme_contact_category, 'eme_contact_category', $ispid); ?>

		<?php echo contact_call($dbc, 'contact_10', 'eme_contact[]', $eme_contact, '',$eme_contact_category, 'eme_contact', $ispid); ?>
		<span class="pull-right">
			<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
			<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
		</span>
		<div class="clearfix"></div>
	</div>
<?php } ?>


<?php if (strpos($value_config, ',Parent Rating,') !== FALSE) { ?>
    <h4>Parent Rating</h4>
    <?php if (strpos($value_config, ',Parent Rating Note,') !== FALSE) { ?>
        <h5>NOTE: Parent rating refers to parents satisfaction with their own knowledge and skills in this area. Based on a scale of 1 to 5, 1 being not satisfied at all (no knowledge or skills), 5 being very satisfied (self-reported to be knowledgeable enough and able to use skills in their environments).</h5>
    <?php } ?>
    <?php if (strpos($value_config, ',Parent Rating Behaviour,') !== FALSE) { ?>
        <h5>Behaviour</h5>
        <div class="form-group">
            <label class="col-sm-4 control-label">Objective #:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_behaviour_objective" data-field="rating_behaviour_objective" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_behaviour_objective ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Child Baseline:</label>
            <div class="col-sm-8">
                <textarea name="rating_behaviour_child" rows="5" cols="50" data-field="rating_behaviour_child" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_behaviour_child); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_behaviour_child_date" data-field="rating_behaviour_child_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_behaviour_child_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_behaviour_child_rating" data-field="rating_behaviour_child_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_behaviour_child_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Family Baseline:</label>
            <div class="col-sm-8">
                <textarea name="rating_behaviour_family" rows="5" cols="50" data-field="rating_behaviour_family" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_behaviour_family); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_behaviour_family_date" data-field="rating_behaviour_family_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_behaviour_family_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_behaviour_family_rating" data-field="rating_behaviour_family_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_behaviour_family_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Targeted Outcomes:</label>
            <div class="col-sm-8">
                <textarea name="rating_behaviour_targeted" rows="5" cols="50" data-field="rating_behaviour_targeted" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_behaviour_targeted); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_behaviour_targeted_date" data-field="rating_behaviour_targeted_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_behaviour_targeted_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_behaviour_targeted_rating" data-field="rating_behaviour_targeted_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_behaviour_targeted_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Strategies to support the individual:</label>
            <div class="col-sm-8">
                <textarea name="rating_behaviour_strategies_individual" rows="5" cols="50" data-field="rating_behaviour_strategies_individual" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_behaviour_strategies_individual); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Strategies to support the family:</label>
            <div class="col-sm-8">
                <textarea name="rating_behaviour_strategies_family" rows="5" cols="50" data-field="rating_behaviour_strategies_family" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_behaviour_strategies_family); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Review Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_behaviour_review_date" data-field="rating_behaviour_review_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_behaviour_review_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Parent Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_behaviour_parent_update" rows="5" cols="50" data-field="rating_behaviour_parent_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_behaviour_parent_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Therapist Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_behaviour_therapist_update" rows="5" cols="50" data-field="rating_behaviour_therapist_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_behaviour_therapist_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Aide Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_behaviour_aide_update" rows="5" cols="50" data-field="rating_behaviour_aide_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_behaviour_aide_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Next Step:</label>
            <div class="col-sm-8">
                <textarea name="rating_behaviour_next_step" rows="5" cols="50" data-field="rating_behaviour_next_step" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_behaviour_next_step); ?></textarea>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Parent Rating Communication & Social Skills,') !== FALSE) { ?>
        <h5>Communication & Social Skills</h5>
        <div class="form-group">
            <label class="col-sm-4 control-label">Objective #:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_comm_objective" data-field="rating_comm_objective" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_comm_objective ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Child Baseline:</label>
            <div class="col-sm-8">
                <textarea name="rating_comm_child" rows="5" cols="50" data-field="rating_comm_child" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_comm_child); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_comm_child_date" data-field="rating_comm_child_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_comm_child_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_comm_child_rating" data-field="rating_comm_child_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_comm_child_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Family Baseline:</label>
            <div class="col-sm-8">
                <textarea name="rating_comm_family" rows="5" cols="50" data-field="rating_comm_family" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_comm_family); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_comm_family_date" data-field="rating_comm_family_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_comm_family_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_comm_family_rating" data-field="rating_comm_family_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_comm_family_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Targeted Outcomes:</label>
            <div class="col-sm-8">
                <textarea name="rating_comm_targeted" rows="5" cols="50" data-field="rating_comm_targeted" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_comm_targeted); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_comm_targeted_date" data-field="rating_comm_targeted_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_comm_targeted_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_comm_targeted_rating" data-field="rating_comm_targeted_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_comm_targeted_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Strategies to support the individual:</label>
            <div class="col-sm-8">
                <textarea name="rating_comm_strategies_individual" rows="5" cols="50" data-field="rating_comm_strategies_individual" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_comm_strategies_individual); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Strategies to support the family:</label>
            <div class="col-sm-8">
                <textarea name="rating_comm_strategies_family" rows="5" cols="50" data-field="rating_comm_strategies_family" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_comm_strategies_family); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Review Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_comm_review_date" data-field="rating_comm_review_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_comm_review_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Parent Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_comm_parent_update" rows="5" cols="50" data-field="rating_comm_parent_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_comm_parent_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Therapist Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_comm_therapist_update" rows="5" cols="50" data-field="rating_comm_therapist_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_comm_therapist_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Aide Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_comm_aide_update" rows="5" cols="50" data-field="rating_comm_aide_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_comm_aide_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Next Step:</label>
            <div class="col-sm-8">
                <textarea name="rating_comm_next_step" rows="5" cols="50" data-field="rating_comm_next_step" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_comm_next_step); ?></textarea>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Parent Rating Physical Abilities,') !== FALSE) { ?>
        <h5>Physical Abilities</h5>
        <div class="form-group">
            <label class="col-sm-4 control-label">Objective #:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_physical_objective" data-field="rating_physical_objective" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_physical_objective ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Child Baseline:</label>
            <div class="col-sm-8">
                <textarea name="rating_physical_child" rows="5" cols="50" data-field="rating_physical_child" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_physical_child); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_physical_child_date" data-field="rating_physical_child_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_physical_child_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_physical_child_rating" data-field="rating_physical_child_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_physical_child_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Family Baseline:</label>
            <div class="col-sm-8">
                <textarea name="rating_physical_family" rows="5" cols="50" data-field="rating_physical_family" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_physical_family); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_physical_family_date" data-field="rating_physical_family_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_physical_family_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_physical_family_rating" data-field="rating_physical_family_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_physical_family_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Targeted Outcomes:</label>
            <div class="col-sm-8">
                <textarea name="rating_physical_targeted" rows="5" cols="50" data-field="rating_physical_targeted" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_physical_targeted); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_physical_targeted_date" data-field="rating_physical_targeted_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_physical_targeted_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_physical_targeted_rating" data-field="rating_physical_targeted_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_physical_targeted_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Strategies to support the individual:</label>
            <div class="col-sm-8">
                <textarea name="rating_physical_strategies_individual" rows="5" cols="50" data-field="rating_physical_strategies_individual" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_physical_strategies_individual); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Strategies to support the family:</label>
            <div class="col-sm-8">
                <textarea name="rating_physical_strategies_family" rows="5" cols="50" data-field="rating_physical_strategies_family" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_physical_strategies_family); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Review Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_physical_review_date" data-field="rating_physical_review_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_physical_review_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Parent Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_physical_parent_update" rows="5" cols="50" data-field="rating_physical_parent_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_physical_parent_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Therapist Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_physical_therapist_update" rows="5" cols="50" data-field="rating_physical_therapist_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_physical_therapist_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Aide Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_physical_aide_update" rows="5" cols="50" data-field="rating_physical_aide_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_physical_aide_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Next Step:</label>
            <div class="col-sm-8">
                <textarea name="rating_physical_next_step" rows="5" cols="50" data-field="rating_physical_next_step" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_physical_next_step); ?></textarea>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Parent Rating Cognitive Abilities,') !== FALSE) { ?>
        <h5>Cognitive Abilities</h5>
        <div class="form-group">
            <label class="col-sm-4 control-label">Objective #:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_cognitive_objective" data-field="rating_cognitive_objective" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_cognitive_objective ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Child Baseline:</label>
            <div class="col-sm-8">
                <textarea name="rating_cognitive_child" rows="5" cols="50" data-field="rating_cognitive_child" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_cognitive_child); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_cognitive_child_date" data-field="rating_cognitive_child_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_cognitive_child_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_cognitive_child_rating" data-field="rating_cognitive_child_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_cognitive_child_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Family Baseline:</label>
            <div class="col-sm-8">
                <textarea name="rating_cognitive_family" rows="5" cols="50" data-field="rating_cognitive_family" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_cognitive_family); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_cognitive_family_date" data-field="rating_cognitive_family_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_cognitive_family_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_cognitive_family_rating" data-field="rating_cognitive_family_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_cognitive_family_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Targeted Outcomes:</label>
            <div class="col-sm-8">
                <textarea name="rating_cognitive_targeted" rows="5" cols="50" data-field="rating_cognitive_targeted" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_cognitive_targeted); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_cognitive_targeted_date" data-field="rating_cognitive_targeted_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_cognitive_targeted_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Rating:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_cognitive_targeted_rating" data-field="rating_cognitive_targeted_rating" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control" value="<?= $rating_cognitive_targeted_rating ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Strategies to support the individual:</label>
            <div class="col-sm-8">
                <textarea name="rating_cognitive_strategies_individual" rows="5" cols="50" data-field="rating_cognitive_strategies_individual" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_cognitive_strategies_individual); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Strategies to support the family:</label>
            <div class="col-sm-8">
                <textarea name="rating_cognitive_strategies_family" rows="5" cols="50" data-field="rating_cognitive_strategies_family" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_cognitive_strategies_family); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Review Date:</label>
            <div class="col-sm-8">
                <input type="text" name="rating_cognitive_review_date" data-field="rating_cognitive_review_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker" value="<?= $rating_cognitive_review_date ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Parent Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_cognitive_parent_update" rows="5" cols="50" data-field="rating_cognitive_parent_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_cognitive_parent_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Therapist Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_cognitive_therapist_update" rows="5" cols="50" data-field="rating_cognitive_therapist_update" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_cognitive_therapist_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Aide Update:</label>
            <div class="col-sm-8">
                <textarea name="rating_cognitive_aide_update" rows="5" cols="50" data-field="rating_cognitive_aide_update" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_cognitive_aide_update); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Next Step:</label>
            <div class="col-sm-8">
                <textarea name="rating_cognitive_next_step" rows="5" cols="50" data-field="rating_cognitive_next_step" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control"><?php echo html_entity_decode($rating_cognitive_next_step); ?></textarea>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<?php if (strpos($value_config, ',Dates & Timelines,') !== FALSE) { ?>
	<h4>Dates &amp; Timelines</h4>
	<?php if (strpos($value_config, ',ISP Start Date,') !== FALSE) { ?>
		<div class="form-group clearfix">
			<label for="first_name" class="col-sm-4 control-label text-right">ISP Start Date:</label>
			<div class="col-sm-8">
				<input name="isp_start_date" value="<?php echo $isp_start_date; ?>" type="text" data-field="isp_start_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"  class="form-control datepicker">
			</div>
		</div>
	<?php } ?>

	<?php if (strpos($value_config, ',ISP Review Date,') !== FALSE) { ?>
		<div class="form-group clearfix">
			<label for="first_name" class="col-sm-4 control-label text-right">ISP Review Date:</label>
			<div class="col-sm-8">
				<input name="isp_review_date" value="<?php echo $isp_review_date; ?>" type="text" data-field="isp_review_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"  class="form-control datepicker">
			</div>
		</div>
	<?php } ?>

	<?php if (strpos($value_config, ',ISP End Date,') !== FALSE) { ?>
		<div class="form-group clearfix">
			<label for="first_name" class="col-sm-4 control-label text-right">ISP End Date:</label>
			<div class="col-sm-8">
				<input name="isp_end_date" value="<?php echo $isp_end_date; ?>" type="text" data-field="isp_end_date" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="form-control datepicker">
			</div>
		</div>
	<?php } ?>
<?php } ?>

<?php if (strpos($value_config, ',ISP Details,') !== FALSE) { ?>
	<h4>ISP Details</h4>
	<?php if (strpos($value_config, ',Quality of Life Outcomes,') !== FALSE) { ?>
	   <div class="form-group" id="isp_quality_name">
		<label for="travel_task" class="col-sm-4 control-label">Quality of Life Outcomes:</label>
		<div class="col-sm-8">
			<input name="isp_quality" type="text" class="form-control" value="<?= $isp_quality ?>" data-field="isp_quality" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" />
		</div>
	  </div>
	<?php } ?>

	<?php if (strpos($value_config, ',Goals,') !== FALSE) { ?>
	   <div class="form-group" id="isp_goals_name">
		<label for="travel_task" class="col-sm-4 control-label">Goals:</label>
		<div class="col-sm-8">
			<?php if(!empty($isp_goals)) {
				foreach ($isp_goals as $isp_goal) { ?>
					<input name="isp_goals[]" type="text" class="form-control" value="<?= $isp_goal ?>" data-field="isp_goals" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" data-delimiter="*#*" />
				<?php }
			} else { ?>
				<input name="isp_goals[]" type="text" class="form-control" data-field="isp_goals" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" data-delimiter="*#*" />
			<?php } ?>
		</div>
		<button id="add_another_goal" onclick="addAnotherGoal(this); return false;" class="btn brand-btn mobile-block pull-right">Add Another Goal</button>
	  </div>
	<?php } ?>

	<?php if (strpos($value_config, ',Assessed Service Needs,') !== FALSE) { ?>
	  <div class="form-group">
		<label for="first_name[]" class="col-sm-4 control-label">Assessed Service Needs:</label>
		<div class="col-sm-8">
		  <textarea name="isp_needs" rows="5" cols="50" class="form-control" data-field="isp_needs" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"><?php echo $isp_needs; ?></textarea>
		</div>
	  </div>
	<?php } ?>

	<?php if (strpos($value_config, ',Support Strategies,') !== FALSE) { ?>
	  <div class="form-group">
		<label for="first_name[]" class="col-sm-4 control-label">Support Strategies:</label>
		<div class="col-sm-8">
		  <textarea name="isp_strategies" rows="5" cols="50" class="form-control" data-field="isp_strategies" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"><?php echo $isp_strategies; ?></textarea>
		</div>
	  </div>
	<?php } ?>

	<?php if (strpos($value_config, ',Support Objectives,') !== FALSE) { ?>
	  <div class="form-group">
		<label for="first_name[]" class="col-sm-4 control-label">Support Objectives:</label>
		<div class="col-sm-8">
		  <textarea name="isp_objectives" rows="5" cols="50" class="form-control" data-field="isp_objectives" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"><?php echo $isp_objectives; ?></textarea>
		</div>
	  </div>
	<?php } ?>

	<?php if (strpos($value_config, ',SIS Activity Areas,') !== FALSE) { ?>
	   <div class="form-group" id="isp_sis_name">
		<label for="travel_task" class="col-sm-4 control-label">SIS Activity Areas/Items:</label>
		<div class="col-sm-8">
			<input name="isp_sis_name" type="text" class="form-control" value="<?= $isp_sis ?>" data-field="isp_sis_name" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" />
		</div>
	  </div>
	<?php } ?>

	<?php if (strpos($value_config, ',Who is Responsible,') !== FALSE) { ?>
		<h4>Who is Responsible</h4>
		<?php foreach(explode(',',$isp_detail_responsible_contact) as $i => $multicontactid) { ?>
			<div class="contact_group">
				<?php echo contact_category_call($dbc, 'contact_category_5', 'isp_detail_responsible_contact_category[]', explode(',',$isp_detail_responsible_contact_category)[$i], 'isp_detail_responsible_contact_category', $ispid); ?>

				<?php echo contact_call($dbc, 'contact_5', 'isp_detail_responsible_contact[]', $multicontactid,'',explode(',',$isp_detail_responsible_contact_category)[$i], 'isp_detail_responsible_contact', $ispid); ?>
				<span class="pull-right">
					<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img pull-right"></a>
					<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img pull-right"></a>
				</span>
				<div class="clearfix"></div>
			</div>
		<?php } ?>
	<?php } ?>

	<?php if (strpos($value_config, ',Updates,') !== FALSE) { ?>
	  <div class="form-group">
		<label for="first_name[]" class="col-sm-4 control-label">Updates:</label>
		<div class="col-sm-8">
		  <textarea name="isp_updates" rows="5" cols="50" class="form-control" data-field="isp_updates" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"><?php echo $isp_updates; ?></textarea>
		</div>
	  </div>
	<?php } ?>
<?php } ?>

<?php if (strpos($value_config, ',ISP Notes,') !== FALSE) { ?>
    <h4>ISP Notes</h4>
	<div class="form-group">
		<label for="first_name[]" class="col-sm-4 control-label">ISP Notes:</label>
		<div class="col-sm-8">
		  <textarea name="isp_notes" rows="5" cols="50" class="form-control" data-field="isp_notes" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact"><?php echo $isp_notes; ?></textarea>
		</div>
	</div>
<?php } ?>

<?php if (strpos($value_config, ',Signatures,') !== FALSE) { ?>
    <h4>Signatures</h4>
    <?php if (strpos($value_config, ',Signatures Parents,') !== FALSE) { ?>
        <h5>Parents</h5>
        <div class="form-group signatures_parent_existing">
            <div class="col-sm-8 col-sm-offset-4">
                <?php if(!empty($signatures_parent)) {
                    $signatures_parent = explode('*#*', $signatures_parent);
                    for ($sig_i = 0; $sig_i < count($signatures_parent); $sig_i++) { ?>
                        <img src="../Individual Support Plan/download/<?= $signatures_parent[$sig_i] ?>"><br>
                        Name: <?= explode('*#*', $signatures_parent_name)[$sig_i] ?><br>
                        Date: <?= explode('*#*', $signatures_parent_date)[$sig_i] ?><br><br>
                    <?php }
                } ?>
            </div>
        </div>
        <div class="form-group signatures_parent">
            <label class="col-sm-4 control-label">Signature:</label>
            <div class="col-sm-8">
                <?php $output_name = 'signatures_parent[]';
                include('../phpsign/sign_multiple.php'); ?>
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Name:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_parent_name[]" class="form-control" value="">
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_parent_date[]" class="form-control datepicker" value="">
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-8 col-sm-offset-4">
                <button type="submit" name="isp_submit_signature" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="btn brand-btn" onclick="submitSignature('signatures_parent', this); return false;">Submit Signature</button>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Signatures Coordinator,') !== FALSE) { ?>
        <h5>Coordinator</h5>
        <div class="form-group signatures_coordinator_existing">
            <div class="col-sm-8 col-sm-offset-4">
                <?php if(!empty($signatures_coordinator)) {
                    $signatures_coordinator = explode('*#*', $signatures_coordinator);
                    for ($sig_i = 0; $sig_i < count($signatures_coordinator); $sig_i++) { ?>
                        <img src="../Individual Support Plan/download/<?= $signatures_coordinator[$sig_i] ?>"><br>
                        Name: <?= explode('*#*', $signatures_coordinator_name)[$sig_i] ?><br>
                        Date: <?= explode('*#*', $signatures_coordinator_date)[$sig_i] ?><br><br>
                    <?php }
                } ?>
            </div>
        </div>
        <div class="form-group signatures_coordinator">
            <label class="col-sm-4 control-label">Signature:</label>
            <div class="col-sm-8">
                <?php $output_name = 'signatures_coordinator[]';
                include('../phpsign/sign_multiple.php'); ?>
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Name:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_coordinator_name[]" class="form-control" value="">
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_coordinator_date[]" class="form-control datepicker" value="">
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-8 col-sm-offset-4">
                <button type="submit" name="isp_submit_signature" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="btn brand-btn" onclick="submitSignature('signatures_coordinator', this); return false;">Submit Signature</button>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Signatures Speech-Language,') !== FALSE) { ?>
        <h5>Speech-Language</h5>
        <div class="form-group signatures_sl_existing">
            <div class="col-sm-8 col-sm-offset-4">
                <?php if(!empty($signatures_sl)) {
                    $signatures_sl = explode('*#*', $signatures_sl);
                    for ($sig_i = 0; $sig_i < count($signatures_sl); $sig_i++) { ?>
                        <img src="../Individual Support Plan/download/<?= $signatures_sl[$sig_i] ?>"><br>
                        Name: <?= explode('*#*', $signatures_sl_name)[$sig_i] ?><br>
                        Date: <?= explode('*#*', $signatures_sl_date)[$sig_i] ?><br><br>
                    <?php }
                } ?>
            </div>
        </div>
        <div class="form-group signatures_sl">
            <label class="col-sm-4 control-label">Signature:</label>
            <div class="col-sm-8">
                <?php $output_name = 'signatures_sl[]';
                include('../phpsign/sign_multiple.php'); ?>
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Name:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_sl_name[]" class="form-control" value="">
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_sl_date[]" class="form-control datepicker" value="">
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-8 col-sm-offset-4">
                <button type="submit" name="isp_submit_signature" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="btn brand-btn" onclick="submitSignature('signatures_sl', this); return false;">Submit Signature</button>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Signatures Occupational Therapist,') !== FALSE) { ?>
        <h5>Occupational Therapist</h5>
        <div class="form-group signatures_ot_existing">
            <div class="col-sm-8 col-sm-offset-4">
                <?php if(!empty($signatures_ot)) {
                    $signatures_ot = explode('*#*', $signatures_ot);
                    for ($sig_i = 0; $sig_i < count($signatures_ot); $sig_i++) { ?>
                        <img src="../Individual Support Plan/download/<?= $signatures_ot[$sig_i] ?>"><br>
                        Name: <?= explode('*#*', $signatures_ot_name)[$sig_i] ?><br>
                        Date: <?= explode('*#*', $signatures_ot_date)[$sig_i] ?><br><br>
                    <?php }
                } ?>
            </div>
        </div>
        <div class="form-group signatures_ot">
            <label class="col-sm-4 control-label">Signature:</label>
            <div class="col-sm-8">
                <?php $output_name = 'signatures_ot[]';
                include('../phpsign/sign_multiple.php'); ?>
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Name:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_ot_name[]" class="form-control" value="">
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_ot_date[]" class="form-control datepicker" value="">
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-8 col-sm-offset-4">
                <button type="submit" name="isp_submit_signature" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="btn brand-btn" onclick="submitSignature('signatures_ot', this); return false;">Submit Signature</button>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Signatures Provisional Psychologist,') !== FALSE) { ?>
        <h5>Provisional Psychologist</h5>
        <div class="form-group signatures_pp_existing">
            <div class="col-sm-8 col-sm-offset-4">
                <?php if(!empty($signatures_pp)) {
                    $signatures_pp = explode('*#*', $signatures_pp);
                    for ($sig_i = 0; $sig_i < count($signatures_pp); $sig_i++) { ?>
                        <img src="../Individual Support Plan/download/<?= $signatures_pp[$sig_i] ?>"><br>
                        Name: <?= explode('*#*', $signatures_pp_name)[$sig_i] ?><br>
                        Date: <?= explode('*#*', $signatures_pp_date)[$sig_i] ?><br><br>
                    <?php }
                } ?>
            </div>
        </div>
        <div class="form-group signatures_pp">
            <label class="col-sm-4 control-label">Signature:</label>
            <div class="col-sm-8">
                <?php $output_name = 'signatures_pp[]';
                include('../phpsign/sign_multiple.php'); ?>
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Name:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_pp_name[]" class="form-control" value="">
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_pp_date[]" class="form-control datepicker" value="">
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-8 col-sm-offset-4">
                <button type="submit" name="isp_submit_signature" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="btn brand-btn" onclick="submitSignature('signatures_pp', this); return false;">Submit Signature</button>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Signatures Physiotherapist,') !== FALSE) { ?>
        <h5>Physiotherapist</h5>
        <div class="form-group signatures_physio_existing">
            <div class="col-sm-8 col-sm-offset-4">
                <?php if(!empty($signatures_physio)) {
                    $signatures_physio = explode('*#*', $signatures_physio);
                    for ($sig_i = 0; $sig_i < count($signatures_physio); $sig_i++) { ?>
                        <img src="../Individual Support Plan/download/<?= $signatures_physio[$sig_i] ?>"><br>
                        Name: <?= explode('*#*', $signatures_physio_name)[$sig_i] ?><br>
                        Date: <?= explode('*#*', $signatures_physio_date)[$sig_i] ?><br><br>
                    <?php }
                } ?>
            </div>
        </div>
        <div class="form-group signatures_physio">
            <label class="col-sm-4 control-label">Signature:</label>
            <div class="col-sm-8">
                <?php $output_name = 'signatures_physio[]';
                include('../phpsign/sign_multiple.php'); ?>
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Name:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_physio_name[]" class="form-control" value="">
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_physio_date[]" class="form-control datepicker" value="">
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-8 col-sm-offset-4">
                <button type="submit" name="isp_submit_signature" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="btn brand-btn" onclick="submitSignature('signatures_physio', this); return false;">Submit Signature</button>
            </div>
        </div>
    <?php } ?>
    <?php if (strpos($value_config, ',Signatures Aides,') !== FALSE) { ?>
        <h5>Aide(s)</h5>
        <div class="form-group signatures_aide_existing">
            <div class="col-sm-8 col-sm-offset-4">
                <?php if(!empty($signatures_aide)) {
                    $signatures_aide = explode('*#*', $signatures_aide);
                    for ($sig_i = 0; $sig_i < count($signatures_aide); $sig_i++) { ?>
                        <img src="../Individual Support Plan/download/<?= $signatures_aide[$sig_i] ?>"><br>
                        Name: <?= explode('*#*', $signatures_aide_name)[$sig_i] ?><br>
                        Date: <?= explode('*#*', $signatures_aide_date)[$sig_i] ?><br><br>
                    <?php }
                } ?>
            </div>
        </div>
        <div class="form-group signatures_aide">
            <label class="col-sm-4 control-label">Signature:</label>
            <div class="col-sm-8">
                <?php $output_name = 'signatures_aide[]';
                include('../phpsign/sign_multiple.php'); ?>
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Name:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_aide_name[]" class="form-control" value="">
            </div>
            <div class="clearfix"></div>
            <label class="col-sm-4 control-label">Date:</label>
            <div class="col-sm-8">
                <input type="text" name="signatures_parent[]" class="form-control datepicker" value="">
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-8 col-sm-offset-4">
                <button type="submit" name="isp_submit_signature" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?= $ispid ?>" data-contactid-field="support_contact" class="btn brand-btn" onclick="submitSignature('signatures_parent', this); return false;">Submit Signature</button>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<script>
$(document).ready(function() {
    setTimeout(function() {
        $('div[data-tab-name="individual_service_plan"] [id^=contact_]').not('[id^=contact_category],[id$=chosen]').each(function() {
            var select = this;
            var category = $(this).data('category');
            var contacts = $(this).data('value');
            $.ajax({
                method: 'GET',
                url: '../Individual Support Plan/isp_ajax_all.php?fill=contact_category&category='+category+'&contacts='+contacts,
                success: function(response) {
                    if($(select).find('option:selected').val() == '') {
                        $(select).empty().append(response).trigger('change.select2');
                    }
                }
            });
        });
    }, 1000);
});

function selectContactCategory(sel) {
    if(default_contact_list == '') {
        default_contact_list = $(sel).closest('.contact_group').find('select:not([name*=category])').html();
    }
    $.ajax({
        type: "GET",
        url: "../Individual Support Plan/isp_ajax_all.php?fill=contact_category&category="+sel.value,
        dataType: "html",   //expect html to be returned
        success: function(response){
            $(sel).closest('.contact_group').find('select:not([name*=category])').html(response).change().trigger('change.select2');
        }
    });
}

function addAnotherGoal(link) {
    var clone = $('[name="isp_goals[]"]').first().clone(true);
    clone.val('');
    $(link).closest('.form-group').find('div.col-sm-8').append(clone);
    $('[data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
}
var default_contact_list = '';
function contact_clone(btn) {
    var contact = $(btn).closest('.contact_group').clone();
    contact.find('select,input').val('');
    
    if(default_contact_list != '') {
        contact.find('select:not([name*=category])').html(default_contact_list)
    }
    resetChosen(contact.find("select"));
    
    var group = $(btn).closest('.contact_group');
    while(group.next('.contact_group').length > 0) {
        group = group.next('.contact_group');
    }
    group.after(contact);
    $('[data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
}
function contact_remove(btn) {
    if($(btn).closest('.contact_group').next('h3').length == 1 && $(btn).closest('.contact_group').prev('h3').length == 1) {
        contact_clone(btn);
    }
    var select_name = $(btn).closest('.contact_group').find('select').last().prop('name');
    $(btn).closest('.contact_group').remove();
    if(select_name != undefined && select_name != '') {
        $('[name="'+select_name+'"]').first().change();
    }
}
function checkContactChange(sel) {
    if(sel.value == 'NEW_CONTACT') {
        $(sel).closest('.form-group').find('input').show().focus();
    } else {
        $(sel).closest('.form-group').find('input').hide();
    }
}
function submitSignature(div, btn) {
    var block = $('.'+div);

    var ispid = $(btn).data('row-id');
    var sig = $(block).find('[name="'+div+'[]"]').val();
    var sig_name = $(block).find('[name="'+div+'_name[]"]').val();
    var sig_date = $(block).find('[name="'+div+'_date[]"]').val();

    $('.'+div+' .form-control').val('');
    $('.'+div+' .clearButton a').click();
    
    $.ajax({
        url: '../Contacts/contacts_ajax.php?action=isp_submit_signature',
        method: 'POST',
        data: { ispid: ispid, field: div, sig: sig, sig_name: sig_name, sig_date: sig_date },
        success: function(response) {
            $('.'+div+'_existing .col-sm-8').append(response);
        }
    });
}
</script>

<?php } ?>