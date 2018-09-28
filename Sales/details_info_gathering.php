<?php include_once('../include.php');
include_once('../Sales/config.php');
include_once('../Information Gathering/manual_reporting_functions.php');
if(empty($salesid)) {
	$salesid = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
} ?>
<!-- Information Gathering -->
<script type="text/javascript">
$(document).ready(function() {
    $('.add_row_infodoc').on( 'click', add_info_doc);
});
$(document).ready(function() {
    init_page();
});
$(document).on('change', 'select[name="ig_tab"],select[name="ig_section"],select[name="ig_form"]', function() { filter_infogathering(); });
function filter_infogathering() {
    var category = $('[name="ig_tab"]').val();
    var section = $('[name="ig_section"]').val();

    if(category != undefined && category != '' && section != undefined && section != '') {
        $('[name="ig_section"] option').hide();
        $('[name="ig_section"] option[data-category="'+category+'"]').show();
        $('[name="ig_form"] option').hide();
        $('[name="ig_form"] option[data-category="'+category+'"][data-section="'+section+'"]').show();
    } else if(category != undefined && category != '') {
        $('[name="ig_section"] option').hide();
        $('[name="ig_section"] option[data-category="'+category+'"]').show();
        $('[name="ig_form"] option').hide();
        $('[name="ig_form"] option[data-category="'+category+'"]').show();
    } else if(section != undefined && section != '') {
        $('[name="ig_section"] option').show();
        $('[name="ig_form"] option').hide();
        $('[name="ig_form"] option[data-section="'+section+'"]').show();
    } else {
        $('[name="ig_section"] option').show();
        $('[name="ig_form"] option').show();
    }
    $('[name="ig_section"]').trigger('change.select2');
    $('[name="ig_form"]').trigger('change.select2');
}
function add_infogathering() {
    $('#dialog_add_info_gathering').dialog({
        resizable: true,
        height: "auto",
        width: ($(window).width() <= 800 ? $(window).width() : 800),
        modal: true,
        open: function() {
            $('[name="ig_tab"]').val('').change();
            $('[name="ig_section"]').val('').change();
            $('[name="ig_form"]').val('').change();
        },
        buttons: {
            "Add Info Gathering": function() {
                var salesid = $('[name="salesid"]').val();
                var infogatheringid = $('[name="ig_form"]').val();
                if(!(infogatheringid > 0)) {
                    alert('No Form Selected.');
                } else {
                    overlayIFrameSlider('<?= WEBSITE_URL ?>/Information Gathering/add_manual.php?action=view&infogatheringid='+infogatheringid+'&salesid='+salesid, '75%');
                    $(this).dialog('close');
                }
            },
            Cancel: function() {
                $(this).dialog('close');
            }
        }
    });
}
function add_info_doc() {
    var clone = $('.additional_infodoc').clone();
    clone.find('.form-control').val('');
    clone.removeClass("additional_infodoc");
    $('#add_here_new_infodoc').append(clone);
    $('.add_row_infodoc').off('click',add_info_doc).on( 'click', add_info_doc);
    return false;
}
var reload_info = function() {
	$.get('details_info_gathering.php?id=<?= $salesid ?>', function(response) {
		$('#infogathering').parents('div').first().html(response);
	});
}
</script>

<div id="dialog_add_info_gathering" title="Select an Information Gathering Form" style="display:none;">
    <div class="form-group">
        <label class="col-sm-4">Tab:</label>
        <div class="col-sm-8">
            <select name="ig_tab" data-placeholder="Select a Tab..." class="chosen-select-deselect">
                <option></option>
                <?php $ig_tabs = mysqli_fetch_all(mysqli_query($dbc, "SELECT distinct(category) FROM infogathering WHERE deleted=0 ORDER BY `category`"),MYSQLI_ASSOC);
                foreach($ig_tabs as $ig_tab) {
                    echo '<option value="'.$ig_tab['category'].'">'.$ig_tab['category'].'</option>';
                } ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4">Section:</label>
        <div class="col-sm-8">
            <select name="ig_section" data-placeholder="Select a Section..." class="chosen-select-deselect">
                <option></option>
                <?php $ig_sections = mysqli_fetch_all(mysqli_query($dbc, "SELECT distinct(CONCAT(`category`,`heading_number`)), `heading_number`, `heading`, `category` FROM infogathering WHERE deleted=0 ORDER BY CONCAT(`category`,`heading_number`)"),MYSQLI_ASSOC);
                foreach($ig_sections as $ig_section) {
                    echo '<option data-category="'.$ig_section['category'].'" value="'.$ig_section['heading_number'].'">'.$ig_section['heading'].'</option>';
                } ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4">Form:</label>
        <div class="col-sm-8">
            <select name="ig_form" data-placeholder="Select a Form..." class="chosen-select-deselect">
                <option></option>
                <?php $ig_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM infogathering WHERE deleted=0 ORDER BY `sub_heading`"),MYSQLI_ASSOC);
                foreach($ig_forms as $ig_form) {
                    echo '<option data-category="'.$ig_form['category'].'" data-section="'.$ig_form['heading_number'].'" value="'.$ig_form['infogatheringid'].'">'.$ig_form['sub_heading'].'</option>';
                } ?>
            </select>
        </div>
    </div>
</div>

<div class="accordion-block-details padded" id="infogathering">
    <div class="accordion-block-details-heading"><h4>Information Gathering</h4></div>
    
    <div class="row">
        <div class="col-xs-12"><?php
            $result = mysqli_query($dbc, "SELECT * FROM `sales_document` WHERE `salesid`='{$salesid}' AND `document_type`='Information Gathering' AND `deleted`=0 AND `salesid` > 0 ORDER BY `salesdocid` DESC");
            if ( $result->num_rows > 0 ) {
                echo '
                    <br />
                    <table class="table table-bordered">
                        <tr class="hidden-xs hidden-sm">
                            <th>Document</th>
                            <th>Date</th>
                            <th>Uploaded By</th>
                            <th></th>
                        </tr>';
                
                while ( $row=mysqli_fetch_array($result) ) {
                    echo '<tr>';
                        $by = $row['created_by'];
                        $label = (empty($row['label']) ? $row['document'] : $row['label']);
                        echo '<td data-title="Document"><a href="download/'.$row['document'].'" target="_blank">'.$label.'</a>
                            <input type="text" class="form-control" data-table="sales_document" data-id="'.$row['salesdocid'].'" name="label" value="'.$label.'" onblur="$(this).hide(); $(this).closest(\'td\').find(\'a\').text(this.value).show(); $(this).closest(\'td\').find(\'img\').show();" style="display:none;">
                            <img src="../img/icons/ROOK-edit-icon.png" class="inline-img cursor-hand no-toggle" onclick="$(this).closest(\'td\').find(\'a,img\').hide();$(this).closest(\'td\').find(\'[name=label]\').show().focus();" title="Edit">
                        </td>';
                        echo '<td data-title="Date">'.$row['created_date'].'</td>';
                        echo '<td data-title="Uploaded By">'.get_staff($dbc, $by).'</td>';
                        echo '<td data-title="Function">
                            <input type="hidden" data-table="sales_document" data-id="'.$row['salesdocid'].'" name="deleted">
                            <img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="rem_doc(this);">
                            <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="add_doc(this);">
                            <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-email-icon.png" onclick="email_doc(this);">
                        </td>';
                    echo '</tr>';
                }
                
                echo '</table><br /><br />';
            } ?>
        </div>
    </div><!-- .row -->
    
    <div class="row add_doc" style="<?= $result->num_rows > 0 ? 'display:none;' : '' ?>">
        <div class="col-xs-12 col-sm-4">
            <span class="popover-examples list-inline"><a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span>
            <b>Upload Document(s):</b>
        </div>
        
        <div class="col-xs-12 col-sm-5">
            <input name="document" multiple data-table="sales_document" data-id="" data-after="reload_info" data-type="Information Gathering" type="file" data-filename-placement="inside" class="form-control" />
        </div>
        <div class="clearfix"></div>
    </div><!-- .row -->

    <div class="row">
        <div class="col-xs-12"><?php
            $result = mysqli_query($dbc, "SELECT * FROM `infogathering_pdf` WHERE `salesid`='{$salesid}' AND '$salesid' > 0 AND `deleted`=0");
            if ( $result->num_rows > 0 ) {
                echo '
                    <br />
                    <table class="table table-bordered">
                        <tr class="hidden-xs hidden-sm">
                            <th>Tab</th>
                            <th>Section</th>
                            <th>Form</th>
                            <th>PDF</th>
                            <th></th>
                        </tr>';
                
                while ( $row=mysqli_fetch_array($result) ) {
                    $infogathering = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `infogathering` WHERE `infogatheringid` = '".$row['infogatheringid']."'"));
                    echo '<tr>';
                        echo '<td data-title="Tab">'.$infogathering['category'].'</td>';
                        echo '<td data-title="Section">'.$infogathering['heading'].'</td>';
                        echo '<td data-title="Form">'.$infogathering['sub_heading'].'</td>';
                        echo '<td data-title="PDF"><a href="'.WEBSITE_URL.'/Information Gathering/'.infogathering_pdf($dbc, $row['infogatheringid'], $row['fieldlevelriskid']).'" target="_blank">View PDF</a></td>';
                        echo '<td data-title="Function">
                            <input type="hidden" data-table="infogathering_pdf" data-id="'.$row['infopdfid'].'" name="deleted">
                            <img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="rem_infogathering(this);">
                            <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="add_infogathering();">
                            <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-email-icon.png" onclick="email_infogathering(this);">
                        </td>';
                    echo '</tr>';
                }
                
                echo '</table><br /><br />';
            } ?>
        </div>
    </div><!-- .row -->
    
    <div class="row double-gap-top">
        <a href="<?= WEBSITE_URL; ?>/Information Gathering/infogathering.php?from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']); ?>" onclick="add_infogathering(); return false;" class="btn brand-btn">Add Information Gathering</a>
        <div class="clearfix"></div>
    </div><!-- .row -->
</div>