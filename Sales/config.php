<?php include_once('../include.php');
$lead_created_by        = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
$primary_staff          = $_SESSION['contactid'];
$share_lead             = '';
$businessid             = '';
$contactid              = '';
$primary_number         = '';
$email_address          = '';
$lead_value             = '';
$estimated_close_date   = '';
$serviceid              = '';
$productid              = '';
$marketingmaterialid    = '';
$lead_source            = '';
$next_action            = '';
$new_reminder           = '';
$status                 = '';
$number_of_days                 = '';
$number_of_days_start_date = '';
$flag_colour = $flag_label = '';
$flag_colours = explode(',', get_config($dbc, "ticket_colour_flags"));
$flag_labels = explode('#*#', get_config($dbc, "ticket_colour_flag_names"));

if ( !empty($_GET['businessid']) ) {
    $businessid = $_GET['businessid'];
}
if($_GET['id'] > 0) {
    $salesid = $_GET['id'];

    if ( !empty($salesid) ) {
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales` WHERE `salesid`='{$salesid}'"));

        $lead_created_by        = $get_contact['lead_created_by'];
        $primary_staff          = $get_contact['primary_staff'];
        $share_lead             = $get_contact['share_lead'];
        $businessid             = $get_contact['businessid'];
        $contactid              = $get_contact['contactid'];
        $primary_number         = $get_contact['primary_number'];
        $email_address          = $get_contact['email_address'];
        $lead_value             = $get_contact['lead_value'];
        $estimated_close_date   = $get_contact['estimated_close_date'];
        $serviceid              = $get_contact['serviceid'];
        $productid              = $get_contact['productid'];
        $marketingmaterialid    = $get_contact['marketingmaterialid'];
        $lead_source            = $get_contact['lead_source'];
        $next_action            = $get_contact['next_action'];
        $new_reminder           = $get_contact['new_reminder'];
        $status                 = $get_contact['status'];
        $region                 = $get_contact['region'];
        $location               = $get_contact['location'];
        $classification         = $get_contact['classification'];
		if(!empty($get_contact['flag_label'])) {
			$flag_colour = $get_contact['flag_colour'];
			$flag_label = $get_contact['flag_label'];
		} else if(!empty($get_contact['flag_colour'])) {
			$flag_colour = $get_contact['flag_colour'];
			$flag_label = $flag_labels[array_search($get_contact['flag_colour'], $flag_colours)];
		}

        $get_lead_source = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `businessid`, `referred_contactid` FROM `contacts` WHERE (`referred_contactid` IN ($contactid) OR `referred_contactid` IN ($businessid))"));
        $lead_source_cid = $get_lead_source['contactid'];
        $lead_source_bid = $get_lead_source['businessid'];
    }
}
$statuses     = get_config($dbc, 'sales_lead_status');
$next_actions = get_config($dbc, 'sales_next_action');
$value_config = get_field_config($dbc, 'sales'); ?>
<script>
$(document).ready(function() {
    init_page();
});
function init_page() {
    destroyInputs();
    $('[data-table]').off('change',saveField).change(saveField);
    initInputs();
}
function email_doc(img) {
    var documents = [];
    var line = $(img).closest('tr,.row');
    line.find('a[href*=download]').each(function() { documents.push(this.href.replace('<?= WEBSITE_URL ?>','..')); });
    var business = $('[name=businessid][data-table=sales]').val();
    var contact = '<?= array_values(array_filter(explode(',',$contactid)))[0] ?>';
    var sales = $('[name=salesid]').val();
    var subject = encodeURIComponent(line.find('[name=marketingmaterialid] option:selected,a').first().text());
    var body = encodeURIComponent('');
    overlayIFrameSlider('../Email Communication/add_email.php?type=external&subject='+subject+'&body='+body+'&bid='+business+'&cid='+contact+'&salesid='+sales+'&attach_docs='+encodeURIComponent(documents.join('#*#')), 'auto', false, true);
}
function email_infogathering(img) {
    var documents = [];
    var line = $(img).closest('tr');
    line.find('a[href*=download]').each(function() { documents.push(this.href.replace('<?= WEBSITE_URL ?>','..')); });
    var business = $('[name=businessid][data-table=sales]').val();
    var contact = '<?= array_values(array_filter(explode(',',$contactid)))[0] ?>';
    var sales = $('[name=salesid]').val();
    var subject = encodeURIComponent('');
    var body = encodeURIComponent('');
    overlayIFrameSlider('../Email Communication/add_email.php?type=external&subject='+subject+'&body='+body+'&bid='+business+'&cid='+contact+'&salesid='+sales+'&attach_docs='+encodeURIComponent(documents.join('#*#')), 'auto', false, true);
}
</script>