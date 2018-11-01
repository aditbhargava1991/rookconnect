<?php include_once('../include.php');
include_once('../Sales/config.php');
if(empty($salesid) || empty($lead)) {
	$salesid = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
    if($salesid > 0) {
        $lead = $dbc->query("SELECT * FROM `sales` WHERE `salesid`='$salesid'")->fetch_assoc();
    }
} ?>
<!-- Sales Lead Contacts -->
<script type="text/javascript">
$(document).ready(function() {
    init_page();
});
var reload_contacts = function() {
	$.get('details_contacts.php?id='+$('[name=salesid]').val(), function(response) {
		$('[id^=contact_]').first().parents('div').first().html(response);
	});
}
</script>
<?php function contact_fields($contactid) {
    $dbc = $_SERVER['DBC'];
    include('../Contacts/edit_fields.php');
    $_POST['folder'] = 'Contacts';
    $_POST['tab_label'] = 'Contact Description';
    $_POST['tab_name'] = 'Contact Description';
    $_POST['type'] = get_contact($dbc, $contactid, 'category');
    $_GET['edit'] = $contactid;
    $tab_data = $tab_list['Contact Description']; ?>
    <div class="accordion-block-details padded row" id="contact_<?= $contactid ?>">
        <div class="col-xs-12 col-sm-4 gap-md-left-15">Sales Lead Contact:</div>
        <div class="col-xs-12 col-sm-5">
            <select data-placeholder="Select Sales Lead(s)..." id="sales_contact" data-table="sales" data-concat="," name="contactid" class="chosen-select-deselect form-control1">
                <option value=""></option>
                <option value="New Contact">New Contact</option><?php
                $dropdown_categories = explode(',',get_config($dbc, 'lead_all_contact_cat'));
                $dropdown_categories = array_filter(array_merge($dropdown_categories,['Sales Leads','Sales Lead']));
                $query = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE (`businessid`='$businessid' OR `contactid`='$contactid' OR `category` IN ('".implode("','",$dropdown_categories)."')) AND `deleted`=0 AND `status`>0"));
                foreach($query as $contact) {
                    if (!empty(trim($contact['full_name'],'- '))) {
                        echo '<option '. ($contactid==$contact['contactid'] ? "selected" : '') .' value="'. $contact['contactid'] .'">'.$contact['full_name'] .'</option>';
                    }
                } ?>
            </select>
        </div>
		<div class="col-xs-12 col-sm-1">
            <img class="inline-img cursor-hand pull-right no-toggle" title="Remove this <?= CONTACTS_NOUN ?> from this Sales Lead" src="../img/remove.png" onclick="rem_row(this);">
            <img class="inline-img cursor-hand pull-right no-toggle" title="Add another <?= CONTACTS_NOUN ?> to this Sales Lead" src="../img/icons/ROOK-add-icon.png" onclick="add_row(this);">
			<a href="../Contacts/contacts_inbox.php?fields=all_fields&edit=<?= $contactid ?>" class="no-toggle" title="<?= get_contact($dbc, $contactid) ?>" onclick="overlayIFrameSlider(this.href.replace(/edit=.*/,'edit='+$('#contacts_list').find('option:selected').first().val()),'auto',true,true); return false;"><img src="../img/person.PNG" class="inline-img"></a>
		</div>
        <div class="clearfix"></div>
    
        <div class="accordion-block-details-heading sub_details">
            <?php if(get_contact($dbc, $contactid, 'name_company')!=''){
            ?>
            <script type="text/javascript">
                $('#salesLeadContact-tab-label').html("<?php echo get_contact($dbc, $contactid, 'name_company') ?>");
            </script>
            <?php } ?>
            <h4><?= get_contact($dbc, $contactid, 'name_company') ?></h4>
        </div>
        
        <div class="row sub_details">
            <div class="col-xs-12 col-sm-11 gap-md-left-15">
                <input type="hidden" name="contactid" value="<?= $contactid ?>">
                <div data-tab-name='<?= $tab_data[0] ?>' data-locked='' id="<?= $tab_data[0] ?>" class="scroll-section">
                    <?php $hide_section_heading = true;
                    include('../Contacts/edit_section.php'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <hr />
<?php }

foreach(explode(',', trim($lead['contactid'],',')) as $row_contact) {
    contact_fields($row_contact);
} ?>