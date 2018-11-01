<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Delivery Summary</h3>') : '' ?>
<div id="no-more-tables">
    <table class="table table-bordered">
        <tr class="hidden-sm hidden-xs">
            <?php foreach($field_sort_order as $field_sort_field) { ?>
                <?php if (strpos($value_config, ','."Delivery Summary Stop".',') !== FALSE && $field_sort_field == 'Delivery Summary Stop') { ?>
                    <th>Stop</th>
                <?php } ?>
                <?php if (strpos($value_config, ','."Delivery Summary Client".',') !== FALSE && $field_sort_field == 'Delivery Summary Client') { ?>
                    <th>Name</th>
                <?php } ?>
                <?php if (strpos($value_config, ','."Delivery Summary Address".',') !== FALSE && $field_sort_field == 'Delivery Summary Address') { ?>
                    <th>Address</th>
                <?php } ?>
                <?php if (strpos($value_config, ','."Delivery Summary Services".',') !== FALSE && $field_sort_field == 'Delivery Summary Services') { ?>
                    <th>Services</th>
                <?php } ?>
                <?php if (strpos($value_config, ','."Delivery Summary Status".',') !== FALSE && $field_sort_field == 'Delivery Summary Status') { ?>
                    <th>Status</th>
                <?php } ?>
            <?php } ?>
        </tr>
        <?php $ticket_stops = mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `type` != 'origin' AND `type` != 'destination' $stop_id ORDER BY `sort`");
        $stop_i = 0;
        while($ticket_stop = $ticket_stops->fetch_assoc()) {
            $stop_i++; ?>
            <tr>
                <?php foreach($field_sort_order as $field_sort_field) { ?>
                    <?php if (strpos($value_config, ','."Delivery Summary Stop".',') !== FALSE && $field_sort_field == 'Delivery Summary Stop') { ?>
                        <td data-title="Stop">Stop <?= $stop_i ?> of <?= $ticket_stops->num_rows ?></td>
                    <?php } ?>
                    <?php if (strpos($value_config, ','."Delivery Summary Client".',') !== FALSE && $field_sort_field == 'Delivery Summary Client') { ?>
                        <td data-title="Name"><?= empty($ticket_stop['client_name']) ? $ticket_stop['location_name'] : $ticket_stop['client_name'] ?></td>
                    <?php } ?>
                    <?php if (strpos($value_config, ','."Delivery Summary Address".',') !== FALSE && $field_sort_field == 'Delivery Summary Address') { ?>
                        <td data-title="Address"><?= $ticket_stop['address'] ?></td>
                    <?php } ?>
                    <?php if (strpos($value_config, ','."Delivery Summary Services".',') !== FALSE && $field_sort_field == 'Delivery Summary Services') { ?>
                        <td data-title="Services">
                            <?php $service_list = [];
                            foreach(explode(',',$ticket_stop['serviceid']) as $delivery_service) {
                                if($delivery_service > 0) {
                                    $delivery_service = $dbc->query("SELECT `category`, `service_type`, `heading` FROM `services` WHERE `serviceid`='$delivery_service'")->fetch_assoc();
                                    $service_list[] = implode(': ',array_filter($delivery_service));
                                }
                            }
                            echo implode('<br />',$service_list); ?>
                        </td>
                    <?php } ?>
                    <?php if (strpos($value_config, ','."Delivery Summary Status".',') !== FALSE && $field_sort_field == 'Delivery Summary Status') { ?>
                        <td data-title="Status"><?= $ticket_stop['status'] ?></td>
                    <?php } ?>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</div>