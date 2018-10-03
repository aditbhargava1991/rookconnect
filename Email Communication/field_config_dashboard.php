<?php
/* Settings - Dashboard */
?>
<?php
    error_reporting(0);
    include_once('../include.php');
?>

<script>
    $(document).ready(function() {
        $('[name="internal_communication_dashboard[]"]').change(save_settings_internal_dashboard);
        $('[name="external_communication_dashboard[]"]').change(save_settings_external_dashboard);
    });
    
    function save_settings_internal_dashboard() {
        var internal_communication_dashboard = [];
        $('[name="internal_communication_dashboard[]"]:checked').not(':disabled').each(function() {
            internal_communication_dashboard.push(this.value);
        });
        
        $.ajax ({
            url: 'communication_ajax_all.php?fill=save_settings_internal_dashboard',
            data: {internal_communication_dashboard:internal_communication_dashboard},
            method: 'GET',
            response: 'html',
            success: function(response) {}
        });
    }
    function save_settings_external_dashboard() {
        var external_communication_dashboard = [];
        $('[name="external_communication_dashboard[]"]:checked').not(':disabled').each(function() {
            external_communication_dashboard.push(this.value);
        });
        
        $.ajax ({
            url: 'communication_ajax_all.php?fill=save_settings_external_dashboard',
            data: {external_communication_dashboard:external_communication_dashboard},
            method: 'GET',
            response: 'html',
            success: function(response) {}
        });
    }
</script>

<div class="standard-dashboard-body-title hide-titles-mob">
    <h3>Dashboard</h3>
</div>

<div class="standard-dashboard-body-content full-height">
    <div class="dashboard-item full-height" style="border-left:0; margin:0;">
        <form class="form-horizontal full-height">
            <div class="form-group block-group block-group-noborder full-height" style="margin:0;">
                
                <?php
                    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT internal_communication_dashboard FROM field_config"));
                    $value_config = ','.$get_field_config['internal_communication_dashboard'].',';
                ?>
                <div class="row">
                    <label class="col-sm-4">Internal Communication</label>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Type
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Business
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Contact
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Project
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Subject".',') !== FALSE) { echo " checked"; } ?> value="Subject" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Subject
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Body".',') !== FALSE) { echo " checked"; } ?> value="Body" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Body
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Attachment".',') !== FALSE) { echo " checked"; } ?> value="Attachment" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Attachment
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."To Staff".',') !== FALSE) { echo " checked"; } ?> value="To Staff" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;To Staff
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."CC Staff".',') !== FALSE) { echo " checked"; } ?> value="CC Staff" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;CC Staff
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Additional Email".',') !== FALSE) { echo " checked"; } ?> value="Additional Email" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Additional Email
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Email#".',') !== FALSE) { echo " checked"; } ?> value="Email#" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Email#
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Email By".',') !== FALSE) { echo " checked"; } ?> value="Email By" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Staff
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Email Date".',') !== FALSE) { echo " checked"; } ?> value="Email Date" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Email Date
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Follow Up By".',') !== FALSE) { echo " checked"; } ?> value="Follow Up By" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Follow Up By
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Date" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Follow Up Date
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr />
                
                <?php
                    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT external_communication_dashboard FROM field_config"));
                    $value_config = ','.$get_field_config['external_communication_dashboard'].',';
                ?>
                <div class="row">
                    <label class="col-sm-4">External Communication</label>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Type
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Business
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Contact
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Project
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Subject".',') !== FALSE) { echo " checked"; } ?> value="Subject" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Subject
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Body".',') !== FALSE) { echo " checked"; } ?> value="Body" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Body
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Attachment".',') !== FALSE) { echo " checked"; } ?> value="Attachment" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Attachment
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."To Contact".',') !== FALSE) { echo " checked"; } ?> value="To Contact" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;To Contact
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."CC Contact".',') !== FALSE) { echo " checked"; } ?> value="CC Contact" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;CC Contact
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."CC Staff".',') !== FALSE) { echo " checked"; } ?> value="CC Staff" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;CC Staff
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Additional Email".',') !== FALSE) { echo " checked"; } ?> value="Additional Email" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Additional Email
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Email#".',') !== FALSE) { echo " checked"; } ?> value="Email#" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Email#
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Email By".',') !== FALSE) { echo " checked"; } ?> value="Email By" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Staff
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Email Date".',') !== FALSE) { echo " checked"; } ?> value="Email Date" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Email Date
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Follow Up By".',') !== FALSE) { echo " checked"; } ?> value="Follow Up By" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Follow Up By
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Date" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Follow Up Date
                            </div>
                            <div class="col-sm-4">
                                <input type="checkbox" <?php if (strpos($value_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Type
                            </div>
                        </div>
                    </div>
                </div>
                
            </div><!-- .block-group -->
        </form>
    </div><!-- .dashboard-item -->
</div><!-- .standard-dashboard-body-content -->