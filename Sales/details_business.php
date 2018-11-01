<?php include_once('../include.php');
include_once('../Sales/config.php');
if(empty($salesid) || empty($lead)) {
	$salesid = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
    if($salesid > 0) {
        $businessid = $dbc->query("SELECT `businessid` FROM `sales` WHERE `salesid`='$salesid'")->fetch_assoc()['businessid'];
    }
} ?>
<!-- Sales Lead Business -->
<script type="text/javascript">
$(document).ready(function() {
    init_page();
});
var reload_business = function() {
	$.get('details_business.php?id='+$('[name=salesid]').val(), function(response) {
        $('#business-tab-label').show();
		$('[id^=business]').parents('div').first().html(response);
	});
}
</script>
<div class="accordion-block-details padded" id="business">
    <div class="row set-row-height">
        <div class="col-xs-12 col-sm-4 gap-md-left-15"><?= BUSINESS_CAT ?>:</div>
        <div class="col-xs-12 col-sm-5">
            <select data-placeholder="Select <?= BUSINESS_CAT ?>..." data-table="sales" name="businessid" id="task_businessid" class="chosen-select-deselect form-control1">
                <option value=""></option><?php
                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `name`, `contactid` FROM `contacts` WHERE (category IN ('".BUSINESS_CAT."','Sales Lead','Sales Leads') AND deleted=0 AND `status`>0 AND IFNULL(`name`,'') != '') OR `contactid`='$businessid'"), MYSQLI_ASSOC));
                echo '<option value="New '.BUSINESS_CAT.'">New '.BUSINESS_CAT.'</option>';
                foreach($query as $id) {
                    echo '<option '. ($businessid==$id ? 'selected' : '') .' value="'. $id .'">'. get_client($dbc, $id) .'</option>';
                } ?>
            </select>
        </div>
		<div class="col-xs-12 col-sm-1">
            <a href="../Contacts/contacts_inbox.php?fields=all_fields&edit=<?= $businessid ?>" class="no-toggle" title="<?= get_contact($dbc, $businessid, 'name_company') ?>" onclick="overlayIFrameSlider(this.href.replace(/edit=.*/,'edit='+$('#task_businessid').find('option:selected').first().val()),'auto',true,true); return false;"><img src="../img/person.PNG" class="inline-img"></a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="accordion-block-details-heading">
        <?php if(get_contact($dbc, $businessid, 'name_company')!=''){
            ?>
            <script type="text/javascript">
                $('#business-tab-label').html("<?php echo get_contact($dbc, $businessid, 'name_company') ?>");
            </script>
        <?php } ?>
        <h4><?= get_contact($dbc, $businessid, 'name_company') ?></h4>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-11 gap-md-left-15">
            <?php function business_fields($contactid) {
                $dbc = $_SERVER['DBC'];
                include('../Contacts/edit_fields.php');
                $_POST['folder'] = 'Contacts';
                $_POST['tab_label'] = 'Contact Description';
                $_POST['tab_name'] = 'Contact Description';
                $_POST['type'] = BUSINESS_CAT;
                $_GET['edit'] = $contactid;
                $tab_data = $tab_list['Contact Description']; ?>
                <input type="hidden" name="contactid" value="<?= $contactid ?>">
                <div data-tab-name='<?= $tab_data[0] ?>' data-locked='' id="<?= $tab_data[0] ?>" class="scroll-section">
                    <?php $hide_section_heading = true;
                    include('../Contacts/edit_section.php'); ?>
                </div>
            <?php }

            if($businessid > 0) {
                business_fields($businessid);
            } ?>
        </div>
    </div>
</div>