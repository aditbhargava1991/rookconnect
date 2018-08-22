<?php function contact_fields($contactid) {
    $dbc = $_SERVER['DBC'];
    include('../Contacts/edit_fields.php');
    $_POST['folder'] = 'Contacts';
    $_POST['tab_label'] = 'Contact Description';
    $_POST['tab_name'] = 'Contact Description';
    $_POST['type'] = get_contact($dbc, $contactid, 'category');
    $_GET['edit'] = $contactid;
    $tab_data = $tab_list['Contact Description']; ?>
    <div class="accordion-block-details padded" id="contact_<?= $contactid ?>">
        <div class="accordion-block-details-heading"><h4><?= get_contact($dbc, $contactid, 'name_company') ?></h4></div>
        
        <div class="row">
            <div class="col-xs-12 col-sm-11 gap-md-left-15">
                <input type="hidden" name="contactid" value="<?= $contactid ?>">
                <div data-tab-name='<?= $tab_data[0] ?>' data-locked='' id="<?= $tab_data[0] ?>" class="scroll-section">
                    <hr>
                    <?php include('../Contacts/edit_section.php'); ?>
                </div>
            </div>
        </div>
    </div>
    <hr />
<?php }

foreach(explode(',', $contactid) as $row_contact) {
    contact_fields($row_contact);
} ?>