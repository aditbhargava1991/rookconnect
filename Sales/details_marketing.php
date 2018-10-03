<!-- Marketing Material -->
<?php include_once('../Sales/config.php'); ?>
<script type="text/javascript">
var add_new_m = 1;
$(document).ready(function() {
	$('.send_cancel').click(
    function() {
        var id = $(this).val();
        $('.approve-box-'+id).hide();
        $('.getemailsapprove').val('');
    });
});
$(document).on('change', 'select.mmat_type_onchange', selectMaterialMaterial);
$(document).on('change', 'select.mmat_cat_onchange', selectMaterialCat);
$(document).on('change', 'select[name="marketingmaterialid"]', selectMaterialHeading);
function selectMaterialHeading() {
    var line = $(this).closest('.row');
	var maketingid = this.value;
	$.ajax({
		type: "GET",
		url: "sales_ajax_all.php?fill=material_heading&maketingid="+maketingid,
		dataType: "html",
		success: function(response){
			line.find('[id^=pdfshow_]').html(response);
		}
	});
}

function selectMaterialMaterial() {
    var line = $(this).closest('.row');
	var stage = this.value;
	$.ajax({
		type: "GET",
		url: "sales_ajax_all.php?fill=m_material_config&value="+stage,
		dataType: "html",
		success: function(response){
            line.find(".mmat_cat_onchange").html(response);
			line.find(".mmat_cat_onchange").trigger("change.select2");
		}
	});
}

function selectMaterialCat() {
    var line = $(this).closest('.row');
	var stage = encodeURIComponent(this.value);

	$.ajax({
		type: "GET",
		url: "sales_ajax_all.php?fill=m_cat_config&value="+stage,
		dataType: "html",
		success: function(response){
            $("[name=marketingmaterialid]").html(response);
			$("[name=marketingmaterialid]").trigger("change.select2");
		}
	});
}

function approvebutton(sel) {
	var status = sel.id;
	$(".approve-box-"+status).show();
	return false;
}
</script>

<div class="accordion-block-details padded" id="marketing">
    <div class="accordion-block-details-heading"><h4>Marketing Material</h4></div>
    
    <?php
    if (strpos($value_config, ',Marketing Material Material Type,') !== false) { echo '<div class="col-sm-12 col-md-2 gap-md-left-15 gap-bottom"><b>Material Type</b></div>'; }
    if (strpos($value_config, ',Marketing Material Category,') !== false) { echo '<div class="col-sm-12 col-md-2 gap-md-left-15 gap-bottom"><b>Category</b></div>'; }
    if (strpos($value_config, ',Marketing Material Heading,') !== false) { echo '<div class="col-sm-12 col-md-3 gap-md-left-15 gap-bottom"><b>Heading</b></div>'; } ?>
    <div class="clearfix"></div><?php
    foreach(explode(',',$marketingmaterialid) as $marketing) { ?>
        <div class="row set-row-height" id="<?= 'mm_'.$id_loop; ?>"><?php
            if (strpos($value_config, ',Marketing Material Material Type,') !== false) { ?>
                <div class="col-sm-12 col-md-2 gap-md-left-15">
                    <select data-placeholder="Choose a Type..." class="chosen-select-deselect form-control mmat_type_onchange">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc, "SELECT DISTINCT(`marketing_material_type`) FROM `marketing_material` WHERE `deleted`=0 ORDER BY `marketing_material_type`");
                        while ( $row=mysqli_fetch_array($query) ) {
                            $selected = (get_marketing_material($dbc, $marketing, 'marketing_material_type') == $row['marketing_material_type']) ?'selected="selected"' : '';
                            echo '<option '. $selected .' value="'. $row['marketing_material_type'] .'">'. $row['marketing_material_type'] .'</option>';
                        } ?>
                    </select>
                </div><?php
            }
            
            if (strpos($value_config, ',Marketing Material Category,') !== false) { ?>
                <div class="col-sm-12 col-md-2 gap-md-left-15">
                    <select data-placeholder="Choose a Category..." class="chosen-select-deselect form-control mmat_cat_onchange">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc,"SELECT DISTINCT(`category`) FROM `marketing_material` WHERE `deleted`=0 ORDER BY `category`");
                        while($row = mysqli_fetch_array($query)) {
                            $selected = (get_marketing_material($dbc, $marketing, 'category') == $row['category']) ? 'selected="selected"' : '';
                            echo '<option '. $selected .' value="'. $row['category'] .'">'. $row['category'] .'</option>';
                        } ?>
                    </select>
                </div><?php
            }
            
            if (strpos($value_config, ',Marketing Material Heading,') !== false) { ?>
                <div class="col-sm-12 col-md-3 gap-md-left-15">
                    <select data-placeholder="Choose a Heading..." data-table="sales" data-concat="," name="marketingmaterialid" class="chosen-select-deselect form-control">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc,"SELECT `marketing_materialid`, IFNULL(NULLIF(`heading`,''),`title`) `heading` FROM `marketing_material` WHERE `deleted`=0 ORDER BY `heading`");
                        while($row = mysqli_fetch_array($query)) {
                            $selected = ($marketing == $row['marketing_materialid']) ? 'selected="selected"' : '';
                            echo '<option '. $selected .' value="'. $row['marketing_materialid'] .'">'. $row['heading'] .'</option>';
                        } ?>
                    </select>
                </div><?php
            }
            
            $query = mysqli_query($dbc, "SELECT `document_link` FROM `marketing_material_uploads` WHERE `marketing_materialid`='{$marketing}' AND `type`='Document'"); ?>
            
            <div class="col-sm-1 pad-5" id="pdfshow_<?= $id_loop; ?>"><?php
                while($row = mysqli_fetch_array($query)) {
                    echo '<a href="'. WEBSITE_URL .'/Documents/download/'. $row['document_link'] .'" title="'.$row['document_link'].'" target="_blank" class="no-toggle"><img class="inline-img" src="../img/person.PNG"></a>';
                } ?>
            </div>
            
            <div class="col-sm-12 col-md-1">
                <img src="<?= WEBSITE_URL; ?>/img/remove.png" class="inline-img cursor-hand pull-right" onclick="rem_row(this);" />
                <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" class="inline-img cursor-hand pull-right" onclick="add_row(this);" />
                <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-email-icon.png" onclick="email_doc(this);">
            </div>
            
        </div>
        <div class="clearfix"></div>
    <?php } ?>
    
</div><!-- .accordion-block-details -->