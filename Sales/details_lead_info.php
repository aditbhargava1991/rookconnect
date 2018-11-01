<!-- Lead Information -->
<?php include_once('../Sales/config.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    $("#task_businessid").change(function() {
		var businessid = this.value;
		if(businessid > 0) {
			$.ajax({
				type: "GET",
				url: "sales_ajax_all.php?fill=assigncontact&businessid="+businessid,
				dataType: "html",
				success: function(response){
					var arr = response.split('**#**');
					$('#sales_contact').html(arr[0]);
					$("#sales_contact").trigger("change.select2");

					if(arr[4] != 'No' && arr[3] != 'No') {
						$(".estimate").html("<a href=\"#\" onclick=\"window.open('<?= WEBSITE_URL; ?>/Estimate/add_estimate.php?estimateid="+arr[3]+"', \'newwindow\', \'width=900, height=900\'); return false;\">Click to View Estimate</a>");

						var quote_html = "<a href=\"#\" onclick=\"window.open('<?= WEBSITE_URL; ?>/Quote/quotes.php?quoteid="+arr[4]+"', \'newwindow\', \'width=900, height=900\'); return false;\">Click to View Quote</a>";
						quote_html += "<br><a href=\"<?= WEBSITE_URL; ?>/Estimate/download/quote_"+arr[3]+".pdf\" target=\"_blank\"><img src=\"<?= WEBSITE_URL; ?>/img/pdf.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"View\">View PDF</a>";
						$(".quote").html(quote_html);
					} else if(arr[3] != 'No') {
						$(".estimate").html("<a href=\"#\" onclick=\"window.open('<?= WEBSITE_URL; ?>/Estimate/add_estimate.php?estimateid="+arr[3]+"', \'newwindow\', \'width=900, height=900\'); return false;\">Click to View Estimate</a>");

						var quote_html = "No Quote<br>";
						quote_html += "<a href=\"#\" onclick=\"window.open('<?= WEBSITE_URL; ?>/Estimate/estimate.php?businessid="+businessid+"&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']); ?>', \'newwindow\', \'width=900, height=900\'); return false;\">Click to View/Approve Estimate</a>";
						$(".quote").html(quote_html);
					} else {
						$(".estimate").html("<a href=\"#\" onclick=\"window.open('<?= WEBSITE_URL; ?>/Estimate/add_estimate.php?from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']); ?>', \'newwindow\', \'width=900, height=900\'); return false;\">Click to Add Estimate</a>");

						$(".quote").html("No Estimate or Quote");
					}
				}
			});
		}
	});

    $("#sales_number").change(function() {
        if($("#sales_number option:selected").text() == 'New Number') {
                $( "#new_number" ).show();
        } else {
            $( "#new_number" ).hide();
        }
    });

    $("#sales_email").change(function() {
        if($("#sales_email option:selected").text() == 'New Email') {
                $( "#new_email" ).show();
        } else {
            $( "#new_email" ).hide();
        }
    });
});
</script>

<div class="accordion-block-details padded" id="leadinfo">
    <div class="accordion-block-details-heading"><h4>Lead Information</h4></div>

    <?php if (strpos($value_config, ',Lead Information Lead Value,') !== false) { ?>
        <div class="row set-row-height">
            <div class="col-xs-12 col-sm-4 gap-md-left-15">Lead Value:</div>
            <div class="col-xs-12 col-sm-5"><input data-table="sales" name="lead_value" value="<?= $lead_value; ?>" type="text" class="form-control" /></div>
            <div class="clearfix"></div>
        </div>
    <?php } ?>

    <div class="row set-row-height">
        <div class="col-xs-12 col-sm-4 gap-md-left-15">Estimated Close Date:</div>
        <div class="col-xs-12 col-sm-5"><input data-table="sales" name="estimated_close_date" value="<?= $estimated_close_date; ?>" type="text" class="datepicker form-control" /></div>
        <div class="clearfix"></div>
    </div>

    <!-- Lead Source -->
    <?php $lead_source_tabs = array_filter(explode(',',get_config($dbc, 'sales_lead_source')));
    $lead_source = explode('#*#', $lead_source);
    $lead_sources = [];
    foreach($lead_source as $lead_source_type) {
        if(is_numeric($lead_source_type)) {
            if(get_contact($dbc, $lead_source_type, 'category') == 'Business') {
                $lead_sources['Business'][] = $lead_source_type;
            } else {
                $lead_sources['Contact'][] = $lead_source_type;
            }
        } else if(in_array($lead_source_type, $lead_source_tabs)) {
            $lead_sources['Dropdown'][] = $lead_source_type;
        } else {
            $lead_sources['Other'][] = $lead_source_type;
        }
    }
    if(empty($lead_sources['Business'])) {
        $lead_sources['Business'] = [''];
    }
    if(empty($lead_sources['Contact'])) {
        $lead_sources['Contact'] = [''];
    }
    if(empty($lead_sources['Dropdown'])) {
        $lead_sources['Dropdown'] = [''];
    }
    if(empty($lead_sources['Other'])) {
        $lead_sources['Other'] = [''];
    }
    if(strpos($value_config, ','."Lead Source Dropdown".',') === FALSE && strpos($value_config, ','."Lead Source Business".',') === FALSE && strpos($value_config, ','."Lead Source Contact".',') === FALSE && strpos($value_config, ','."Lead Source Other".',') === FALSE) {
        $value_config .= ",Lead Source Dropdown,Lead Source Business,Lead Source Contact,Lead Source Other,";
    } ?>
    <?php if(strpos($value_config, ','."Lead Source Dropdown".',') !== FALSE) {
        foreach($lead_sources['Dropdown'] as $lead_source) { ?>
            <div class="row lead_source_dropdown">
                <div class="col-xs-12 col-sm-4 gap-md-left-15">Lead Source:</div>
                <div class="col-xs-12 col-sm-5">
                    <select data-placeholder="Choose a Lead Source..." data-table="sales" data-concat="#*#" name="lead_source" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <?php
                        foreach ($lead_source_tabs as $cat_tab) {
                            $selected = ($lead_source == $cat_tab) ? 'selected="selected"' : '';
                            echo '<option '. $selected .' value="'. $cat_tab .'">'. $cat_tab .'</option>';
                        } ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-1">
                    <img src="<?= WEBSITE_URL; ?>/img/remove.png" class="inline-img cursor-hand pull-right" onclick="rem_row(this);" />
                    <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" class="inline-img cursor-hand pull-right" onclick="add_row(this);" />
                </div>
                <div class="clearfix"></div>
            </div><!-- .row -->
        <?php }
    } ?>

    <?php if(strpos($value_config, ','."Lead Source Business".',') !== FALSE) {
        foreach($lead_sources['Business'] as $lead_source) { ?>
            <div class="row lead_source_business">
                <div class="col-xs-12 col-sm-4 gap-md-left-15">Lead Source - Business:</div>
                <div class="col-xs-12 col-sm-5">
                    <select data-placeholder="Select a Business..." data-table="sales" data-concat="#*#" name="lead_source" class="chosen-select-deselect form-control">
                        <option value=""></option><?php
                        $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.",'Sites') AND `category` = 'Business' AND `deleted`=0 AND `status` > 0"), MYSQLI_ASSOC));
                        foreach($query as $id) {
                            echo '<option '. ($id==$lead_source ? 'selected' : '') .' value="'. $id .'">'. get_client($dbc, $id) .'</option>';
                        } ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-1">
                    <img class="inline-img cursor-hand pull-left no-toggle" title="View this contact's profile" src="../img/person.PNG" onclick="load_profile(this,'Contacts/contacts_inbox.php?fields=all_fields&edit=');">
                    <img src="<?= WEBSITE_URL; ?>/img/remove.png" class="inline-img cursor-hand pull-right" onclick="rem_row(this);" />
                    <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" class="inline-img cursor-hand pull-right" onclick="add_row(this);" />
                </div>
                <div class="clearfix"></div>
            </div><!-- .row -->
        <?php }
    } ?>

    <?php if(strpos($value_config, ','."Lead Source Contact".',') !== FALSE) {
        foreach($lead_sources['Contact'] as $lead_source) { ?>
            <div class="row lead_source_contact">
                <div class="col-xs-12 col-sm-4 gap-md-left-15">Lead Source - Contact:</div>
                <div class="col-xs-12 col-sm-5">
                    <select data-placeholder="Select a Contact..." data-table="sales" data-concat="#*#" name="lead_source" class="chosen-select-deselect form-control">
                        <option value=""></option><?php
                        $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.",'Business') AND `deleted`=0 AND `status`>0"), MYSQLI_ASSOC));
                        foreach($query as $id) {
                            if ( get_contact($dbc, $id) != '-' ) {
                                echo '<option '. ($id==$lead_source ? 'selected' : '') .' value="'. $id .'">'. get_contact($dbc, $id) .'</option>';
                            }
                        } ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-1">
                    <img class="inline-img cursor-hand pull-left no-toggle" title="View this contact's profile" src="../img/person.PNG" onclick="load_profile(this,'Contacts/contacts_inbox.php?fields=all_fields&edit=');">
                    <img src="<?= WEBSITE_URL; ?>/img/remove.png" class="inline-img cursor-hand pull-right" onclick="rem_row(this);" />
                    <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" class="inline-img cursor-hand pull-right" onclick="add_row(this);" />
                </div>
                <div class="clearfix"></div>
            </div><!-- .row -->
        <?php }
    } ?>

    <?php if(strpos($value_config, ','."Lead Source Other".',') !== FALSE) {
        foreach($lead_sources['Other'] as $lead_source) { ?>
            <div class="row lead_source_other">
                <div class="col-xs-12 col-sm-4 gap-md-left-15">Lead Source - Other:</div>
                <div class="col-xs-12 col-sm-5">
                    <input type="text" name="lead_source" data-table="sales" data-concat="#*#" value="<?= $lead_source; ?>" placeholder="Enter Lead Source..." class="form-control" />
                </div>
                <div class="col-xs-12 col-sm-1">
                    <img src="<?= WEBSITE_URL; ?>/img/remove.png" class="inline-img cursor-hand pull-right" onclick="rem_row(this);" />
                    <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" class="inline-img cursor-hand pull-right" onclick="add_row(this);" />
                </div>
                <div class="clearfix"></div>
            </div><!-- .row -->
        <?php }
    } ?>

    <div class="clearfix"></div>
</div>