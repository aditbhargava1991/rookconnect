<!-- Sales Lead Details / Add/Edit Sales Lead -->
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
    $service_type = '';
    $category = '';
    $service_code = '';
    $quantity = '';
    $heading = '';
    $cost = '';
    $description = '';
    $quote_description = '';
    $invoice_description = '';
    $ticket_description = '';
    $service_image = '';

    $final_retail_price = '';
    $name = '';
    $fee = '';
    $admin_price = '';
    $wholesale_price = '';
    $commercial_price = '';
    $client_price = '';
    $purchase_order_price = '';
    $sales_order_price = '';
    $minimum_billable = '';
    $hourly_rate = '';
    $estimated_hours = '';
    $actual_hours = '';
    $msrp = '';

    $unit_price = '';
    $unit_cost = '';
    $rent_price = '';
    $rental_days = '';
    $rental_weeks = '';
    $rental_months = '';
    $rental_years = '';
    $reminder_alert = '';
    $daily = '';
    $weekly = '';
    $monthly = '';
    $annually = '';
    $total_days = '';
    $total_hours = '';
    $total_km = '';
    $total_miles = '';
    $include_in_po = '';
    $include_in_so = '';
    $include_in_pos = '';
    $gst_exempt = '';
    $appointment_type = '';
    $checklist = [''];

    if ( !empty($serviceid) ) {
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='$serviceid'"));

        $service_type = $get_contact['service_type'];
        $category = $get_contact['category'];
        $service_code = $get_contact['service_code'];
        $quantity = $get_contact['quantity'];
        $heading = $get_contact['heading'];
        $cost = $get_contact['cost'];
		if(!($cost > 0)) {
			$cost = $dbc->query("SELECT `cost` FROM `company_rate_card` WHERE `deleted`=0 AND `cost` > 0 AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) AND `item_id`='$serviceid' AND `tile_name`='Services' ORDER BY `start_date`")->fetch_assoc()['cost'];
		}
        $description = $get_contact['description'];
        $quote_description = $get_contact['quote_description'];
        $invoice_description = $get_contact['invoice_description'];
        $ticket_description = $get_contact['ticket_description'];
		$service_image = $get_contact['service_image'];
        $name = $get_contact['name'];
        $fee = $get_contact['fee'];

        $final_retail_price = $get_contact['final_retail_price'];
        $admin_price = $get_contact['admin_price'];
        $wholesale_price = $get_contact['wholesale_price'];
        $commercial_price = $get_contact['commercial_price'];
        $client_price = $get_contact['client_price'];
        $purchase_order_price = $get_contact['purchase_order_price'];
        $sales_order_price = $get_contact['sales_order_price'];
        $minimum_billable = $get_contact['minimum_billable'];
        $hourly_rate = $get_contact['hourly_rate'];
        $estimated_hours = $get_contact['estimated_hours'];
        $actual_hours = $get_contact['actual_hours'];
        $msrp = $get_contact['msrp'];

        $unit_price = $get_contact['unit_price'];
        $unit_cost = $get_contact['unit_cost'];
        $rent_price = $get_contact['rent_price'];
        $rental_days = $get_contact['rental_days'];
        $rental_weeks = $get_contact['rental_weeks'];
        $rental_months = $get_contact['rental_months'];
        $rental_years = $get_contact['rental_years'];
        $reminder_alert = $get_contact['reminder_alert'];
        $daily = $get_contact['daily'];
        $weekly = $get_contact['weekly'];
        $monthly = $get_contact['monthly'];
        $annually = $get_contact['annually'];
        $total_days = $get_contact['total_days'];
        $total_hours = $get_contact['total_hours'];
        $total_km = $get_contact['total_km'];
        $total_miles = $get_contact['total_miles'];
        $include_in_po = $get_contact['include_in_po'];
        $include_in_so = $get_contact['include_in_so'];
        $include_in_pos = $get_contact['include_in_pos'];
        $gst_exempt = $get_contact['gst_exempt'];
        $appointment_type = $get_contact['appointment_type'];
        $checklist = explode('#*#', $get_contact['checklist']); ?>
        <input type="hidden" id="serviceid" name="serviceid" value="<?= $serviceid ?>" /><?php
    } ?>

    <div class="standard-body full-height">
        <div class="standard-body-title hide-on-mobile" style="padding-top:0 !important;">
            <h3 class="double-pad-top"><?= ( !empty($serviceid) ) ? 'Edit' : 'Add'; ?> Service</h3>
        </div>

        <div class="standard-body-content">
            <!-- Service Information -->
            <div class="accordion-block-details padded" id="serviceinfo">
                <div class="accordion-block-details-heading"><h4>Service Information</h4></div><?php

                //Category
                if (strpos($value_config, ',Category,') !== false) { ?>
                    <div class="row set-row-height">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Category:</div>
                        <div class="col-xs-12 col-sm-5">
                            <select id="category" name="category" class="chosen-select-deselect form-control" width="380">
                                <option value=""></option><?php
                                $query = mysqli_query($dbc, "SELECT distinct(`category`) FROM `services` WHERE `deleted` = 0 order by `category`");
                                while($row = mysqli_fetch_array($query)) {
                                    $selected = ( $category==$row['category']) ? 'selected="selected"' : '';
                                    echo '<option '. $selected .' value="'. $row['category'] .'">'. $row['category'] .'</option>';
                                }
                                echo '<option value="Other">New Category</option>'; ?>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row set-row-height" id="new_category" style="display:none;">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">New Category Name:</div>
                        <div class="col-xs-12 col-sm-5"><input name="new_category" type="text" class="form-control" /></div>
                        <div class="clearfix"></div>
                    </div><?php
                }

                //Service Type
                if (strpos($value_config, ',Service Type,') !== false) { ?>
                    <div class="row set-row-height">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Service Type:</div>
                        <div class="col-xs-12 col-sm-5">
                            <select id="service_type" name="service_type" class="chosen-select-deselect form-control" width="380">
                                <option value=""></option><?php
                                $query = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`service_type`) `service_type` FROM `services` WHERE `deleted` = 0 ORDER BY `service_type`"),MYSQLI_ASSOC),'service_type');
                                $query = array_filter(array_unique(array_merge($query, explode(',', get_config($dbc, 'service_types')))));
                                asort($query);
                                foreach($query as $row) {
                                    $selected = ( $service_type==$row ) ? 'selected="selected"' : '';
                                    echo '<option '. $selected .' value="'. $row .'">'. $row .'</option>';
                                }
                                echo '<option value="Other">New Service</option>'; ?>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row set-row-height" id="new_service" style="display:none;">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">New Service Name:</div>
                        <div class="col-xs-12 col-sm-5"><input name="new_service" type="text" class="form-control" /></div>
                        <div class="clearfix"></div>
                    </div><?php
                }

                //Heading
                if (strpos($value_config, ',Heading,') !== false) { ?>
                    <div class="row set-row-height">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Heading:</div>
                        <div class="col-xs-12 col-sm-5"><input name="heading" value="<?= $heading; ?>" type="text" id="name" class="form-control" /></div>
                        <div class="clearfix"></div>
                    </div><?php
                }

                //Name
                if (strpos($value_config, ',Name,') !== false) { ?>
                    <div class="row set-row-height">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Name:</div>
                        <div class="col-xs-12 col-sm-5"><input name="name" value="<?= $name; ?>" type="text" id="name" class="form-control" /></div>
                        <div class="clearfix"></div>
                    </div><?php
                }

                //Service Code
                if (strpos($value_config, ',Service Code,') !== false) { ?>
                    <div class="row set-row-height">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Service Code:</div>
                        <input name="service_code" value="<?= $service_code; ?>" type="text" id="name" class="form-control" />
                        <div class="clearfix"></div>
                    </div><?php
                } ?>

            </div><!-- #serviceinfo -->

            <hr />

            <!-- Quantity -->
            <?php if (strpos($value_config, ',Quantity,') !== false) { ?>
                <div class="accordion-block-details padded" id="quantity">
                    <div class="accordion-block-details-heading"><h4>Quantity</h4></div>
                    <div class="row set-row-height">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Quantity:</div>
                        <div class="col-xs-12 col-sm-5"><input name="quantity" value="<?= $quantity; ?>" type="text" id="name" class="form-control" /></div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <hr />
            <?php } ?><!-- #quantity -->

            <!-- Descriptions -->
            <div class="accordion-block-details padded" id="descriptions">
                <div class="accordion-block-details-heading"><h4>Descriptions</h4></div><?php

                //Descriptions
                if (strpos($value_config, ',Description,') !== false) { ?>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Description:</div>
                        <div class="col-xs-12 col-sm-7"><textarea name="description" rows="5" cols="50" class="form-control"><?= $description; ?></textarea></div>
                        <div class="clearfix"></div>
                    </div><?php
                }

                //Quote Description
                if (strpos($value_config, ',Quote Description,') !== false) { ?>
                    <div class="row set-row-height">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Same Description:</div>
                        <div class="col-xs-12 col-sm-7"><input type="checkbox" value="1" name="same_desc" /> Check this if Quote Description is same as Description.</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row`">
                        <div class="col-xs-12 col-sm-4 gap-md-left-10">Quote Description:</div>
                        <div class="col-xs-12 col-sm-7"><textarea name="quote_description" rows="5" cols="50" class="form-control"><?= $quote_description; ?></textarea></div>
                        <div class="clearfix"></div>
                    </div><?php
                }

                //Invoice Description
                if (strpos($value_config, ',Invoice Description,') !== false) { ?>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Invoice Description:</div>
                        <div class="col-xs-12 col-sm-7"><textarea name="invoice_description" rows="5" cols="50" class="form-control"><?= $invoice_description; ?></textarea></div>
                        <div class="clearfix"></div>
                    </div><?php
                }

                //Ticket Description
                if (strpos($value_config, ',Ticket Description,') !== false) { ?>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15"><?= TICKET_NOUN ?> Description:</div>
                        <div class="col-xs-12 col-sm-7"><textarea name="ticket_description" rows="5" cols="50" class="form-control"><?= $ticket_description; ?></textarea></div>
                        <div class="clearfix"></div>
                    </div><?php
                }

                //Service Image
                if (strpos($value_config, ',Service Image,') !== false) { ?>
                    <div class="row double-gap-top">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Service Image:</div>
                        <div class="col-xs-12 col-sm-7">
                            <?php if($service_image != '' && file_exists('download/'.$service_image)) { ?>
                                <img src="download/<?= $service_image ?>" style="max-width: 100%; max-height: 20em;" />
                            <?php } else { ?>
                                <?php $db_service_image = get_config($dbc, 'services_default_image', false, ''); ?>
                                <img src="download/<?= $db_service_image ?>" style="max-width: 100%; max-height: 20em;" />
                            <?php } ?>
                            <input type="file" name="service_image">
                        </div>
                        <div class="clearfix"></div>
                    </div><?php
                } ?>

            </div><!-- #descriptions -->

            <hr />

            <!-- Checklist -->
            <?php if (strpos($value_config, ',Checklist,') !== false) { ?>
                <div class="accordion-block-details padded" id="checklist">
                    <div class="accordion-block-details-heading"><h4>Checklist</h4></div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Checklist:</div>
                        <div class="col-xs-12 col-sm-7"><?php
                            foreach ( $checklist as $item ) { ?>
                                <div name="checklist_row">
                                    <div class="col-sm-10"><input type="text" class="form-control" value="<?= $item ?>" name="checklist[]"></div>
                                    <div class="col-sm-2"><a class="btn brand-btn pull-right" onclick="$(this).closest('[name=checklist_row]').remove(); return false;" tabindex="-1"><img src="<?= WEBSITE_URL; ?>/img/remove.png" height="20" /></button></a></div>
                                </div><?php
                            } ?>
                            <a class="pull-right" onclick="$(this).before($('[name=checklist_row]').last().clone()); $('[name^=checklist]').last().val('').focus(); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" height="20" /></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <hr />
            <?php } ?><!-- #checklist -->

            <!-- Fee -->
            <?php if (strpos($value_config, ',Fee,') !== false) { ?>
                <div class="accordion-block-details padded" id="fee">
                    <div class="accordion-block-details-heading"><h4>Fee</h4></div>
                    <div class="row set-row-height">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Fee:</div>
                        <div class="col-xs-12 col-sm-5"><input name="fee" value="<?= $fee; ?>" type="text" id="name" class="form-control" /></div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <hr />
            <?php } ?><!-- #fee -->

            <!-- Costs -->
            <?php if (strpos($value_config, ',Cost,') !== false || strpos($value_config, ',Unit Cost,') !== false) { ?>
                <div class="accordion-block-details padded" id="costs"><?php
                    //Cost
                    if (strpos($value_config, ',Cost,') !== false) { ?>
                        <div class="accordion-block-details-heading"><h4>Cost</h4></div>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Cost:</div>
                            <div class="col-xs-12 col-sm-5"><input name="cost" value="<?= $cost; ?>" type="text" id="name" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Unit Cost
                    if (strpos($value_config, ',Unit Cost,') !== false) { ?>
                        <div class="accordion-block-details-heading"><h4>Unit Cost</h4></div>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Unit Cost:</div>
                            <div class="col-xs-12 col-sm-5"><input name="unit_cost" value="<?= $unit_cost; ?>" type="text" id="name" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    if (strpos($value_config, ',Cost,') === false && strpos($value_config, ',Unit Cost,') === false) { ?>
                        <div class="row"><div class="col-xs-12 gap-md-left-15">Please configure costs from Settings first.</div></div><?php
                    } ?>
                </div>

                <hr />
            <?php } ?><!-- #costs -->

            <!-- Price Points -->
            <?php if (strpos($value_config, ',Final Retail Price,') !== false || strpos($value_config, ',Admin Price,') !== false || strpos($value_config, ',Wholesale Price,') !== false || strpos($value_config, ',Commercial Price,') !== false || strpos($value_config, ',Client Price,') !== false || strpos($value_config, ',Purchase Order Price,') !== false || strpos($value_config, ',Sales Order Price,') !== false || strpos($value_config, ',MSRP,') !== false || strpos($value_config, ',Unit Price,') !== false) { ?>
                <div class="accordion-block-details padded" id="pricepoints">
                    <div class="accordion-block-details-heading"><h4>Price Points</h4></div><?php

                    //Final Retail Price
                    if (strpos($value_config, ',Final Retail Price,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Final Retail Price:</div>
                            <div class="col-xs-12 col-sm-5"><input name="final_retail_price" value="<?= $final_retail_price; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Admin Price
                    if (strpos($value_config, ',Admin Price,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Admin Price:</div>
                            <div class="col-xs-12 col-sm-5"><input name="admin_price" value="<?= $admin_price; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Wholesale Price
                    if (strpos($value_config, ',Wholesale Price,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Wholesale Price:</div>
                            <div class="col-xs-12 col-sm-5"><input name="wholesale_price" value="<?= $wholesale_price; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Commercial Price
                    if (strpos($value_config, ',Commercial Price,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Commercial Price:</div>
                            <div class="col-xs-12 col-sm-5"><input name="commercial_price" value="<?= $commercial_price; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Client Price
                    if (strpos($value_config, ',Client Price,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Client Price:</div>
                            <div class="col-xs-12 col-sm-5"><input name="client_price" value="<?= $client_price; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Purchase Order Price
                    if (strpos($value_config, ',Purchase Order Price,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Purchase Order Price:</div>
                            <div class="col-xs-12 col-sm-5"><input name="purchase_order_price" value="<?= $purchase_order_price; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Sales Order Price
                    if (strpos($value_config, ',Sales Order Price,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15"><?= SALES_ORDER_NOUN ?> Price:</div>
                            <div class="col-xs-12 col-sm-5"><input name="sales_order_price" value="<?= $sales_order_price; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //MSRP
                    if (strpos($value_config, ',MSRP,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">MSRP:</div>
                            <div class="col-xs-12 col-sm-5"><input name="msrp" value="<?= $msrp; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Unit Price
                    if (strpos($value_config, ',Unit Price,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Unit Price:</div>
                            <div class="col-xs-12 col-sm-5"><input name="unit_price" value="<?= $unit_price; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Rate Cards
                    if (strpos($value_config, ',Rate Card,') !== false && vuaed_visible_function($dbc, 'rate_card') > 0) { ?>
                        <div class="row">
							<?php include('../Services/edit_rate_card.php'); ?>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    if (strpos($value_config, ',Final Retail Price,') === false && strpos($value_config, ',Admin Price,') === false && strpos($value_config, ',Wholesale Price,') === false && strpos($value_config, ',Commercial Price,') === false && strpos($value_config, ',Client Price,') === false && strpos($value_config, ',Purchase Order Price,') === false && strpos($value_config, ',Sales Order Price,') === false && strpos($value_config, ',MSRP,') === false && strpos($value_config, ',Unit Price,') === false && strpos($value_config, ',Rate Card,') === false) { ?>
                        <div class="row"><div class="col-xs-12 gap-md-left-15">Please configure price points from Settings first.</div></div><?php
                    } ?>
                </div>

                <hr />
            <?php } ?><!-- #pricepoints -->

            <!-- Inclusions -->
            <?php if (strpos($value_config, ',Include in Sales Orders,') !== false || strpos($value_config, ',Include in Purchase Orders,') !== false || strpos($value_config, ',Include in P.O.S.,') !== false) { ?>
                <div class="accordion-block-details padded" id="inclusions">
                    <div class="accordion-block-details-heading"><h4>Inclusions</h4></div><?php

                    //Include in Sales Orders
                    if (strpos($value_config, ',Include in Sales Orders,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-11 gap-md-left-15">
                                <input type="checkbox" <?php if ( $include_in_so!=='' && $include_in_so!==NULL ) { echo "checked"; } ?> name="include_in_so" value="1" /> Include in <?= SALES_ORDER_TILE ?>
                            </div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Include in Purchase Orders
                    if (strpos($value_config, ',Include in Purchase Orders,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-11 gap-md-left-15">
                                <input type="checkbox" <?php if ( $include_in_po!=='' && $include_in_po!==NULL ) { echo "checked"; } ?> name="include_in_po" value="1" /> Include in Purchase Orders
                            </div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Include in POS
                    if (strpos($value_config, ',Include in P.O.S.,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-11 gap-md-left-15">
                                <input type="checkbox" <?php if ( $include_in_pos!=='' && $include_in_pos!==NULL ) { echo "checked"; } ?> name="include_in_pos" value="1" /> Include in <?= POS_ADVANCE_TILE ?>
                            </div>
                            <div class="clearfix"></div>
                        </div><?php
                    } ?>
                </div>

                <hr />
            <?php } ?><!-- #inclusions -->

            <!-- Rental Information -->
            <?php if (strpos($value_config, ',Rent Price,') !== false || strpos($value_config, ',Rental Days,') !== false || strpos($value_config, ',Rental Weeks,') !== false || strpos($value_config, ',Rental Months,') !== false || strpos($value_config, ',Rental Years,') !== false) { ?>
                <div class="accordion-block-details padded" id="rentalinfo">
                    <div class="accordion-block-details-heading"><h4>Rental Information</h4></div><?php

                    //Rent Price
                    if (strpos($value_config, ',Rent Price,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Rent Price:</div>
                            <div class="col-xs-12 col-sm-5"><input name="rent_price" value="<?= $rent_price; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Rental Days
                    if (strpos($value_config, ',Rental Days,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Rental Days:</div>
                            <div class="col-xs-12 col-sm-5"><input name="rental_days" value="<?= $rental_days; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Rental Weeks
                    if (strpos($value_config, ',Rental Weeks,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Rental Weeks:</div>
                            <div class="col-xs-12 col-sm-5"><input name="rental_weeks" value="<?= $rental_weeks; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Rental Months
                    if (strpos($value_config, ',Rental Months,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Rental Months:</div>
                            <div class="col-xs-12 col-sm-5"><input name="rental_months" value="<?= $rental_months; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Rental Years
                    if (strpos($value_config, ',Rental Years,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Rental Years:</div>
                            <div class="col-xs-12 col-sm-5"><input name="rental_years" value="<?= $rental_years; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    } ?>
                </div>

                <hr />
            <?php } ?><!-- #rentalinfo -->

            <!-- Reminder/Alert -->
            <?php if (strpos($value_config, ',Reminder/Alert,') !== false || strpos($value_config, ',Daily,') !== false || strpos($value_config, ',Weekly,') !== false || strpos($value_config, ',Monthly,') !== false || strpos($value_config, ',Annually,') !== false) { ?>
                <div class="accordion-block-details padded" id="reminderalert">
                    <div class="accordion-block-details-heading"><h4>Reminder/Alert</h4></div><?php

                    //Reminder/Alert
                    if (strpos($value_config, ',Reminder/Alert,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Reminder/Alert:</div>
                            <div class="col-xs-12 col-sm-5"><input name="reminder_alert" value="<?= $reminder_alert; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Daily
                    if (strpos($value_config, ',Daily,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Daily:</div>
                            <div class="col-xs-12 col-sm-5"><input name="daily" value="<?= $daily; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Weekly
                    if (strpos($value_config, ',Weekly,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Weekly:</div>
                            <div class="col-xs-12 col-sm-5"><input name="weekly" value="<?= $weekly; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Monthly
                    if (strpos($value_config, ',Monthly,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Monthly:</div>
                            <div class="col-xs-12 col-sm-5"><input name="monthly" value="<?= $monthly; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //Annually
                    if (strpos($value_config, ',Annually,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Annually:</div>
                            <div class="col-xs-12 col-sm-5"><input name="annually" value="<?= $annually; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    if (strpos($value_config, ',Reminder/Alert,') === false && strpos($value_config, ',Daily,') === false && strpos($value_config, ',Weekly,') === false && strpos($value_config, ',Monthly,') === false && strpos($value_config, ',Annually,') === false) { ?>
                        <div class="row"><div class="col-xs-12 gap-md-left-15">Please configure reminder/alert from Settings first.</div></div><?php
                    } ?>
                </div>

                <hr />
            <?php } ?><!-- #reminderalert -->

            <!-- Unit Information -->
            <?php if (strpos($value_config, ',#Of Days,') !== false || strpos($value_config, ',#Of Hours,') !== false || strpos($value_config, ',#Of Kilometers,') !== false || strpos($value_config, ',#Of Miles,') !== false || strpos($value_config, ',Estimated Hours,') !== false) { ?>
                <div class="accordion-block-details padded" id="unitinfo">
                    <div class="accordion-block-details-heading"><h4>Unit Information</h4></div><?php

                    //Estimated Hours
                    if (strpos($value_config, ',Estimated Hours,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15">Estimated Hours:</div>
                            <div class="col-xs-12 col-sm-5"><input name="estimated_hours" value="<?= empty($estimated_hours) ? '00:00' : $estimated_hours ?>" type="text" class="timepicker form-control"  /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //#Of Days
                    if (strpos($value_config, ',#Of Days,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15"># Of Days:</div>
                            <div class="col-xs-12 col-sm-5"><input name="total_days" value="<?= $total_days; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //#Of Hours
                    if (strpos($value_config, ',#Of Hours,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15"># Of Hours:</div>
                            <div class="col-xs-12 col-sm-5"><input name="total_hours" value="<?= $total_hours; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //#Of Kilometers
                    if (strpos($value_config, ',#Of Kilometers,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15"># Of Kilometers:</div>
                            <div class="col-xs-12 col-sm-5"><input name="total_km" value="<?= $total_km; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    //#Of Miles
                    if (strpos($value_config, ',#Of Miles,') !== false) { ?>
                        <div class="row set-row-height">
                            <div class="col-xs-12 col-sm-4 gap-md-left-15"># Of Miles:</div>
                            <div class="col-xs-12 col-sm-5"><input name="total_miles" value="<?= $total_miles; ?>" type="text" class="form-control" /></div>
                            <div class="clearfix"></div>
                        </div><?php
                    }

                    if (strpos($value_config, ',#Of Days,') === false && strpos($value_config, ',#Of Hours,') === false && strpos($value_config, ',#Of Kilometers,') === false && strpos($value_config, ',#Of Miles,') === false && strpos($value_config, ',Estimated Hours,') === false) { ?>
                        <div class="row"><div class="col-xs-12 gap-md-left-15">Please configure units from Settings first.</div></div><?php
                    } ?>
                </div>

                <hr />
            <?php } ?><!-- #unitinfo -->

            <!-- GST Exempt -->
            <?php if (strpos($value_config, ',GST exempt,') !== false) { ?>
                <div class="accordion-block-details padded" id="gstexempt">
                    <div class="accordion-block-details-heading"><h4>GST Exempt</h4></div>
                    <div class="row set-row-height">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">GST Exempt:</div>
                        <div class="col-xs-12 col-sm-5"><input type="checkbox" <?php if ( $gst_exempt=='1') { echo "checked"; } ?> value="1" name="gst_exempt" /></div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <hr />
            <?php } ?><!-- #gstexempt -->

            <!-- Appointment Type -->
            <?php if (strpos($value_config, ',Appointment Type,') !== false) { ?>
                <div class="accordion-block-details padded" id="appttype">
                    <div class="accordion-block-details-heading"><h4>Appointment Type</h4></div>
                    <div class="row set-row-height">
                        <div class="col-xs-12 col-sm-4 gap-md-left-15">Appointment Type:</div>
                        <div class="col-xs-12 col-sm-5">
                            <select name="appointment_type" class="chosen-select-deselect form-control" width="380">
                                <option value=""></option>
                                <?php $appointment_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                                foreach ($appointment_types as $this_appointment_type) {
                                    echo '<option '.($appointment_type == $this_appointment_type['id'] ? 'selected' : '').' value="'.$this_appointment_type['id'].'">'.$this_appointment_type['name'].'</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php } ?><!-- #fee -->

            <div class="pull-right gap-top gap-right">
                <a href="index.php" class="btn brand-btn">Cancel</a>
                <button type="submit" name="add_service" value="Submit" class="btn brand-btn">Save</button>
            </div>
            <div class="clearfix"></div><br />

        </div><!-- .standard-body-content -->
    </div><!-- .preview-block-container -->

    <div class="clearfix"></div>

</form>