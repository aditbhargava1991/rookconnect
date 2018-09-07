<?php
/*
Add Vendor
*/
error_reporting(0);
$from_url = (!empty($_GET['from_url']) ? $_GET['from_url'] : 'individual_support_plan.php');

//Get the Contact Tile settings
$tile_contacts = 'clientinfo';
if(!tile_visible($dbc, 'client_info', 'super')) {
    if(tile_visible($dbc, 'contacts_rolodex', 'super')) {
        $tile_contacts = 'contactsrolodex';
    } else if(tile_visible($dbc, 'contact3', 'super')) {
        $tile_contacts = 'contacts3';
    } else {
        $tile_contacts = 'contacts';
    }
}
$contact_tabs = get_config($dbc, $tile_contacts.'_tabs');
if(get_software_name() == 'breakthebarrier') {
    str_replace('Business','Program/Site',$contact_tabs);
} else if($rookconnect == 'highland') {
    str_replace('Business','Customer',$contact_tabs);
}

if(!empty($_GET['meduploadid'])) {
    $meduploadid = $_GET['meduploadid'];
    $query = mysqli_query($dbc,"DELETE FROM medication_uploads WHERE meduploadid='$meduploadid'");
    $individualsupportplanid = $_GET['individualsupportplanid'];

    echo '<script type="text/javascript"> window.location.replace("add_medication.php?individualsupportplanid='.$individualsupportplanid.'&from_url='.$from_url.'"); </script>';
}

?>
<script type="text/javascript">
$(document).ready(function() {
    setTimeout(function() {
        $('[id^=contact_]').not('[id^=contact_category],[id$=chosen]').each(function() {
            var select = this;
            var category = $(this).data('category');
            var contacts = $(this).data('value');
            $.ajax({
                method: 'GET',
                url: '../Individual Support Plan/isp_ajax_all.php?fill=contact_category&category='+category+'&contacts='+contacts,
                success: function(response) {
                    $(select).empty().append(response).trigger('change.select2');
                }
            });
        });
    }, 1000);

    $("#form1").submit(function( event ) {
        var medication_type = $("#medication_type").val();
        var category = $("input[name=category]").val();
        var title = $("input[name=title]").val();
        if (medication_type == '' || category == '' || title == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });
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
    var clone = $('[name="isp_goals_name[]"]').first().clone();
    clone.val('');
    $(link).closest('.form-group').find('div.col-sm-8').append(clone);
}
</script>

<?php
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT individual_support_plan FROM field_config"));
    $value_config = ','.$get_field_config['individual_support_plan'].',';

    $support_contact_category = '';
    $support_contact = '';
    $dayprimary_contact_category = '';
    $dayprimary_contact = '';
    $daytl_contact_category = '';
    $daytl_contact = '';
    $daykey_contact_category = '';
    $daykey_contact = '';
    $resiprimary_contact_category = '';
    $resiprimary_contact = '';
    $resitl_contact_category = '';
    $resitl_contact = '';
    $resikey_contact_category = '';
    $resikey_contact = '';
    $guardianprimary_contact_category = '';
    $guardianprimary_contact = '';
    $guardiansecondary_contact_category = '';
    $guardiansecondary_contact = '';
    $guardianalt_contact_category = '';
    $guardianalt_contact = '';
    $eme_contact_category = '';
    $eme_contact = '';
    $isp_start_date = '';
    $isp_review_date = '';
    $isp_end_date = '';
    $isp_quality = '';
    $isp_goals = '';
    $isp_needs = '';
    $isp_strategies = '';
    $isp_objectives = '';
    $isp_sis = '';
    $isp_detail_responsible_contact_category = '';
    $isp_detail_responsible_contact = '';
    $isp_updates = '';
    $isp_notes = '';
    $acc_day_program = '';
    $acc_isp_detail = '';
    $acc_isp_notes = '';

    if($_GET['acc'] == 'day_program') {
        $acc_day_program = ' in';
    }
    if($_GET['acc'] == 'isp_detail') {
        $acc_isp_detail = ' in';
    }
    if($_GET['acc'] == 'isp_notes') {
        $acc_isp_notes = ' in';
    }

    if(!empty($_GET['contactid']) && !empty($_GET['category'])) {
        $support_contact = $_GET['contactid'];
        $support_contact_category = $_GET['category'];
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM individual_support_plan WHERE support_contact='$support_contact'"));
        $individualsupportplanid = $get_contact['individualsupportplanid'];

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

        ?>
        <input type="hidden" id="individualsupportplanid" name="individualsupportplanid" value="<?php echo $individualsupportplanid; ?>" />
        <input type="hidden" id="support_contact_category" name="support_contact_category" value="<?php echo $support_contact_category; ?>" />
        <input type="hidden" id="support_contact" name="support_contact" value="<?php echo $support_contact; ?>" />
    <?php } ?>
        <input type="hidden" id="submit_type" name="submit_type" value="individual_support_plan" />

<script>
var default_contact_list = '';
function contact_clone(btn) {
	var contact = $(btn).closest('.contact_group').clone();
	contact.find('select,input').val('');

	if(default_contact_list != '') {
		contact.find('select:not([name*=category])').html(default_contact_list)
	}
	contact.find("select").removeClass("chzn-done").css("display", "block").next().remove();
	contact.find("select").chosen({ allow_single_deselect:true });

	var group = $(btn).closest('.contact_group');
	while(group.next('.contact_group').length > 0) {
		group = group.next('.contact_group');
	}
	group.after(contact);
}
function contact_remove(btn) {
	if($(btn).closest('.contact_group').next('h3').length == 1 && $(btn).closest('.contact_group').prev('h3').length == 1) {
		contact_clone(btn);
	}
	$(btn).closest('.contact_group').remove();
}
</script>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse1" >
                Service Individual<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse1" class="panel-collapse collapse">
        <div class="panel-body">

            <?php
            echo contact_category_call($dbc, 'contact_category_0', 'support_contact_category', $support_contact_category, 'disabled="true"'); ?>

            <?php echo contact_call($dbc, 'contact_0', 'support_contact', $support_contact, '',$support_contact_category, 'disabled="true"'); ?>

        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse2" >
                Day Program Support Team<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse2" class="panel-collapse collapse <?php echo $acc_day_program; ?>">
        <div class="panel-body">

            <?php if (strpos($value_config, ',Day Program Support Team Primary Contact,') === FALSE) { ?>
                <h3>Primary Contact</h3>
                <?php foreach(explode(',',$dayprimary_contact) as $i => $multicontactid) { ?>
                    <div class="contact_group">
                        <?php echo contact_category_call($dbc, 'contact_category_1', 'dayprimary_contact_category[]', explode(',',$dayprimary_contact_category)[$i]); ?>

                        <?php echo contact_call($dbc, 'contact_1', 'dayprimary_contact[]', $multicontactid, '',explode(',',$dayprimary_contact_category)[$i]); ?>
                        <span class="pull-right">
                            <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
                            <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
                        </span>
                        <div class="clearfix"></div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if (strpos($value_config, ',Day Program Support Team Lead,') === FALSE) { ?>
                <h3>Team Lead</h3>
                <?php foreach(explode(',',$daytl_contact) as $i => $multicontactid) { ?>
                    <div class="contact_group">
                        <?php echo contact_category_call($dbc, 'contact_category_2', 'daytl_contact_category[]', explode(',',$daytl_contact_category)[$i]); ?>

                        <?php echo contact_call($dbc, 'contact_2', 'daytl_contact[]', $multicontactid, '',explode(',',$daytl_contact_category)[$i]); ?>
                        <span class="pull-right">
                            <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
                            <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
                        </span>
                        <div class="clearfix"></div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if (strpos($value_config, ',Day Program Support Team Key Supports,') === FALSE) { ?>
                <h3>Key Supports</h3>

                    <div class="contact_group">
                        <?php echo contact_category_call($dbc, 'contact_category_3', 'daykey_contact_category', $daykey_contact_category); ?>

                        <?php echo contact_call($dbc, 'contact_3', 'daykey_contact[]', $daykey_contact, '',$daykey_contact_category); ?>
                        <span class="pull-right">
                            <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
                            <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
                        </span>
                        <div class="clearfix"></div>
                    </div>
            <?php } ?>

        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse3" >
                Residential Support Team<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse3" class="panel-collapse collapse">
        <div class="panel-body">

            <?php if (strpos($value_config, ',Residential Support Team Primary Contact,') === FALSE) { ?>
                <h3>Primary Contact</h3>
                <?php foreach(explode(',',$resiprimary_contact) as $i => $multicontactid) { ?>
                    <div class="contact_group">
                        <?php echo contact_category_call($dbc, 'contact_category_4', 'resiprimary_contact_category[]', explode(',',$resiprimary_contact_category)[$i]); ?>

                        <?php echo contact_call($dbc, 'contact_4', 'resiprimary_contact[]', $multicontactid,'',explode(',',$resiprimary_contact_category)[$i]); ?>
                        <span class="pull-right">
                            <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
                            <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
                        </span>
                        <div class="clearfix"></div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if (strpos($value_config, ',Residential Support Team Lead,') === FALSE) { ?>
                <h3>Team Lead</h3>
                <?php foreach(explode(',',$resitl_contact) as $i => $multicontactid) { ?>
                    <div class="contact_group">
                        <?php echo contact_category_call($dbc, 'contact_category_5', 'resitl_contact_category[]', explode(',',$resitl_contact_category)[$i]); ?>

                        <?php echo contact_call($dbc, 'contact_5', 'resitl_contact[]', $multicontactid,'',explode(',',$resitl_contact_category)[$i]); ?>
                        <span class="pull-right">
                            <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
                            <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
                        </span>
                        <div class="clearfix"></div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if (strpos($value_config, ',Residential Support Team Key Supports,') === FALSE) { ?>
                <h3>Key Supports</h3>

                    <div class="contact_group">
                        <?php echo contact_category_call($dbc, 'contact_category_6', 'resikey_contact_category', $resikey_contact_category); ?>
                        <?php echo contact_call($dbc, 'contact_6', 'resikey_contact[]', $resikey_contact, '',$resikey_contact_category); ?>
                        <span class="pull-right">
                            <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
                            <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
                        </span>
                        <div class="clearfix"></div>
                    </div>
            <?php } ?>

        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse35" >
                Guardian<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse35" class="panel-collapse collapse">
        <div class="panel-body">

            <?php if (strpos($value_config, ',Guardian Primary Contact,') === FALSE) { ?>
                <h3>Primary Contact</h3>
                <?php foreach(explode(',',$guardianprimary_contact) as $i => $multicontactid) { ?>
                    <div class="contact_group">
                        <?php echo contact_category_call($dbc, 'contact_category_7', 'guardianprimary_contact_category[]', explode(',',$guardianprimary_contact_category)[$i]); ?>

                        <?php echo contact_call($dbc, 'contact_7', 'guardianprimary_contact[]', $multicontactid,'',explode(',',$guardianprimary_contact_category)[$i]); ?>
                        <span class="pull-right">
                            <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
                            <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
                        </span>
                        <div class="clearfix"></div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if (strpos($value_config, ',Guardian Secondary Contact,') === FALSE) { ?>
                <h3>Secondary Contact</h3>
                <?php foreach(explode(',',$guardiansecondary_contact) as $i => $multicontactid) { ?>
                    <div class="contact_group">
                        <?php echo contact_category_call($dbc, 'contact_category_8', 'guardiansecondary_contact_category[]', explode(',',$guardiansecondary_contact_category)[$i]); ?>

                        <?php echo contact_call($dbc, 'contact_8', 'guardiansecondary_contact[]', $multicontactid,'',explode(',',$guardiansecondary_contact_category)[$i]); ?>
                        <span class="pull-right">
                            <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
                            <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
                        </span>
                        <div class="clearfix"></div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if (strpos($value_config, ',Guardian Alternates,') === FALSE) { ?>
                <h3>Alternates</h3>

                    <div class="contact_group">
                        <?php echo contact_category_call($dbc, 'contact_category_9', 'guardianalt_contact_category', $guardianalt_contact_category); ?>

                        <?php echo contact_call($dbc, 'contact_9', 'guardianalt_contact[]', $guardianalt_contact, '',$guardianalt_contact_category); ?>
                        <span class="pull-right">
                            <a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
                            <a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
                        </span>
                        <div class="clearfix"></div>
                    </div>
            <?php } ?>

        </div>
    </div>
</div>
<?php if (strpos($value_config, ',Emergency Contacts,') === FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse4" >
                    Emergency Contacts<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse4" class="panel-collapse collapse">
            <div class="panel-body">

    			<div class="contact_group">
    				<?php echo contact_category_call($dbc, 'contact_category_10', 'eme_contact_category', $eme_contact_category); ?>

    				<?php echo contact_call($dbc, 'contact_10', 'eme_contact[]', $eme_contact, '',$eme_contact_category); ?>
    				<span class="pull-right">
    					<a href="" onclick="contact_remove(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
    					<a href="" onclick="contact_clone(this); return false;"><img src="<?= WEBSITE_URL ?>/img/plus.png"></a>
    				</span>
    				<div class="clearfix"></div>
    			</div>

            </div>
        </div>
    </div>
<?php } ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse5" >
                Dates & Timelines<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse5" class="panel-collapse collapse">
        <div class="panel-body">

            <?php if (strpos($value_config, ',ISP Start Date,') === FALSE) { ?>
                <div class="form-group clearfix">
                    <label for="first_name" class="col-sm-4 control-label text-right">ISP Start Date:</label>
                    <div class="col-sm-8">
                        <input name="isp_start_date" value="<?php echo $isp_start_date; ?>" type="text" class="datepicker">
                    </div>
                </div>
            <?php } ?>

            <?php if (strpos($value_config, ',ISP Review Date,') === FALSE) { ?>
                <div class="form-group clearfix">
                    <label for="first_name" class="col-sm-4 control-label text-right">ISP Review Date:</label>
                    <div class="col-sm-8">
                        <input name="isp_review_date" value="<?php echo $isp_review_date; ?>" type="text" class="datepicker">
                    </div>
                </div>
            <?php } ?>

            <?php if (strpos($value_config, ',ISP End Date,') === FALSE) { ?>
                <div class="form-group clearfix">
                    <label for="first_name" class="col-sm-4 control-label text-right">ISP End Date:</label>
                    <div class="col-sm-8">
                        <input name="isp_end_date" value="<?php echo $isp_end_date; ?>" type="text" class="datepicker">
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse6" >
                ISP Details<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse6" class="panel-collapse collapse <?php echo $acc_isp_detail; ?>">
        <div class="panel-body">

            <?php if (strpos($value_config, ',Quality of Life Outcomes,') === FALSE) { ?>
               <div class="form-group" id="isp_quality_name">
                <label for="travel_task" class="col-sm-4 control-label">Quality of Life Outcomes:</label>
                <div class="col-sm-8">
                    <input name="isp_quality_name" type="text" class="form-control" value="<?= $isp_quality ?>" />
                </div>
              </div>
            <?php } ?>

            <?php if (strpos($value_config, ',Goals,') === FALSE) { ?>
               <div class="form-group" id="isp_goals_name">
                <label for="travel_task" class="col-sm-4 control-label">Goals:</label>
                <div class="col-sm-8">
                    <?php if(!empty($isp_goals)) {
                        foreach ($isp_goals as $isp_goal) { ?>
                            <input name="isp_goals_name[]" type="text" class="form-control" value="<?= $isp_goal ?>" />
                        <?php }
                    } else { ?>
                        <input name="isp_goals_name[]" type="text" class="form-control" />
                    <?php } ?>
                </div>
                <button id="add_another_goal" onclick="addAnotherGoal(this); return false;" class="btn brand-btn mobile-block pull-right">Add Another Goal</button>
              </div>
            <?php } ?>

            <?php if (strpos($value_config, ',Assessed Service Needs,') === FALSE) { ?>
              <div class="form-group">
                <label for="first_name[]" class="col-sm-4 control-label">Assessed Service Needs:</label>
                <div class="col-sm-8">
                  <textarea name="isp_needs" rows="5" cols="50" class="form-control"><?php echo $isp_needs; ?></textarea>
                </div>
              </div>
            <?php } ?>

            <?php if (strpos($value_config, ',Support Strategies,') === FALSE) { ?>
              <div class="form-group">
                <label for="first_name[]" class="col-sm-4 control-label">Support Strategies:</label>
                <div class="col-sm-8">
                  <textarea name="isp_strategies" rows="5" cols="50" class="form-control"><?php echo $isp_strategies; ?></textarea>
                </div>
              </div>
            <?php } ?>

            <?php if (strpos($value_config, ',Support Objectives,') === FALSE) { ?>
              <div class="form-group">
                <label for="first_name[]" class="col-sm-4 control-label">Support Objectives:</label>
                <div class="col-sm-8">
                  <textarea name="isp_objectives" rows="5" cols="50" class="form-control"><?php echo $isp_objectives; ?></textarea>
                </div>
              </div>
            <?php } ?>

            <?php if (strpos($value_config, ',SIS Activity Areas,') === FALSE) { ?>
               <div class="form-group" id="isp_sis_name">
                <label for="travel_task" class="col-sm-4 control-label">SIS Activity Areas/Items:</label>
                <div class="col-sm-8">
                    <input name="isp_sis_name" type="text" class="form-control" value="<?= $isp_sis ?>" />
                </div>
              </div>
            <?php } ?>

            <?php if (strpos($value_config, ',Who is Responsible,') === FALSE) { ?>
                <h4>Who is Responsible</h4>
                <?php echo contact_category_call($dbc, 'contact_category_15', 'isp_detail_responsible_contact_category', $isp_detail_responsible_contact_category); ?>
                <?php echo contact_call($dbc, 'contact_15', 'isp_detail_responsible_contact[]', $isp_detail_responsible_contact, 'multiple',$isp_detail_responsible_contact_category); ?>
            <?php } ?>

            <?php if (strpos($value_config, ',Updates,') === FALSE) { ?>
              <div class="form-group">
                <label for="first_name[]" class="col-sm-4 control-label">Updates:</label>
                <div class="col-sm-8">
                  <textarea name="isp_updates" rows="5" cols="50" class="form-control"><?php echo $isp_updates; ?></textarea>
                </div>
              </div>
            <?php } ?>

        </div>
    </div>
</div>

<?php if (strpos($value_config, ',ISP Notes,') === FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse7" >
                    ISP Notes<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse7" class="panel-collapse collapse <?php echo $acc_isp_notes; ?>">
            <div class="panel-body">

              <div class="form-group">
                <label for="first_name[]" class="col-sm-4 control-label">ISP Notes:</label>
                <div class="col-sm-8">
                  <textarea name="isp_notes" rows="5" cols="50" class="form-control"><?php echo $isp_notes; ?></textarea>
                </div>
              </div>

            </div>
        </div>
    </div>
<?php } ?>

<script>
function checkContactChange(sel) {
    if(sel.value == 'NEW_CONTACT') {
        $(sel).closest('.form-group').find('input').show().focus();
    } else {
        $(sel).closest('.form-group').find('input').hide();
    }
}
</script>

<?php function contact_category_call($dbc, $select_id, $select_name, $contact_category_value, $disabled) {
    global $contact_tabs; ?>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">Contact Category:</label>
        <div class="col-sm-8">
            <select <?php echo $disabled; ?> data-placeholder="Choose a Category..." onChange='selectContactCategory(this)' id="<?php echo $select_id; ?>" name="<?php echo $select_name; ?>" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php $each_tab = explode(',', $contact_tabs);
                foreach ($each_tab as $cat_tab) {
                    ?>
                    <option <?php if (strpos($contact_category_value, $cat_tab) !== FALSE) {
                    echo " selected"; } ?> value='<?php echo $cat_tab; ?>'><?php echo $cat_tab; ?></option>
                <?php }
              ?>
            </select>
        </div>
    </div>
<?php } ?>

<?php
$all_contacts = [];
function contact_call($dbc, $select_id, $select_name, $contact_value,$multiple, $from_contact, $disabled) { ?>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">Contact:</label>
        <div class="col-sm-8">
            <select <?php echo $disabled; ?> <?php echo $multiple; ?> data-placeholder="Choose a Contact..." name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>" data-value="<?= $contact_value ?>" data-category="<?= $from_contact ?>" class="chosen-select-deselect form-control" width="380" onchange="checkContactChange(this);">
              <option value="NEW_CONTACT">Add New Contact</option>
            </select>
            <input type="text" name="<?= str_replace('[]','',$select_name) ?>_new_contact<?= preg_replace('/[^\[\]]/','',$select_name) ?>" class="form-control" style="display:none;">
        </div>
    </div>
<?php } ?>