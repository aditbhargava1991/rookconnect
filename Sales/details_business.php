<div class="accordion-block-details padded" id="business">
    <div class="accordion-block-details-heading"><h4><?= get_contact($dbc, $businessid, 'name_company') ?></h4></div>
    
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
                    <hr>
                    <?php include('../Contacts/edit_section.php'); ?>
                </div>
            <?php }

            business_fields($businessid); ?>
        </div>
    </div>
</div>