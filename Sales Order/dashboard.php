<!-- Sales Orders Dashboard -->
<script>
var keep_scrolling = '';
$(document).ready(function() {
	$('.main-screen-white').sortable({
		items: '.info-block-detail',
        sort: function(event) {
            var end_distance = window.innerWidth - event.clientX;
            var start_distance = event.clientX - $('.dashboard-container').offset().left;
            clearInterval(keep_scrolling);
            if(end_distance < 20) {
                keep_scrolling = setInterval(function() { $('.dashboard-container').scrollLeft($('.dashboard-container').scrollLeft() + 10); }, 10);
            } else if(start_distance < 20) {
                keep_scrolling = setInterval(function() { $('.dashboard-container').scrollLeft($('.dashboard-container').scrollLeft() - 10); }, 10);
            }
        },
		handle: '.lead-handle',
		update: function(event, element) {
			$.ajax({
				url: 'ajax.php?fill=changeStatus&type='+element.item.data('type')+'&soid='+element.item.data('id')+'&status='+element.item.closest('.info-block').data('status'),
				success: function() {
					window.location.reload();
				}
			});
		}
	});
    $(window).resize(function() {
        $('.double-scroller div').width($('.dashboard-container').get(0).scrollWidth);
        $('.double-scroller').off('scroll',doubleScroll).scroll(doubleScroll);
        $('.dashboard-container').off('scroll',setDoubleScroll).scroll(setDoubleScroll);
        if($(window).width() > 767 && $(window).innerHeight() - $($('div.info-block-details').first()).offset().top - 60 - ($('.dashboard-container').innerHeight() - $('.dashboard-container').prop('clientHeight')) > 250) {
            $('div.info-block-details').outerHeight($(window).innerHeight() - $($('div.info-block-details').first()).offset().top - 60 - ($('.dashboard-container').innerHeight() - $('.dashboard-container').prop('clientHeight')));
        } else {
            var height = 0;
            $('div.info-block-details').each(function() {
                height = $(this).height() > height ? $(this).height() : height;
            });
            $('div.info-block-details').outerHeight(height);
        }
    }).resize();
    $('.dashboard-container').css('height', 'calc(100% - '+$('.double-scroller').height()+'px)');
    $('.left_jump').off('click').click(function() {
        $('.dashboard-container').scrollLeft($('div.info-block-container').filter(function() { return $(this).position().left < 0 }).last().get(0).offsetLeft - 10);
    });
    $('.right_jump').off('click').click(function() {
        $('.dashboard-container').scrollLeft($('div.info-block-container').filter(function() { return $(this).position().left > 15 }).first().get(0).offsetLeft - 10);
    });
});
$(document).on('change', 'select[name="status"]', function() { changeStatus(this); });
$(document).on('change', 'select[name="next_action"]', function() { changeNextAction(this); });
function doubleScroll() {
    $('.dashboard-container').scrollLeft(this.scrollLeft).scroll();
}
function setDoubleScroll() {
    $('.double-scroller').scrollLeft(this.scrollLeft);
    if(this.scrollLeft < 25) {
        $('.left_jump').hide();
    } else {
        $('.left_jump').show();
    }
    if(this.scrollLeft > this.scrollWidth - this.clientWidth - 25) {
        $('.right_jump').hide();
    } else {
        $('.right_jump').show();
    }
}
</script>
<div class="clearfix"></div>
<div class="double-scroller"><div></div></div>
<div class="main-screen-white <?= (count(explode(',', $statuses)) > 3) ? 'horizontal-scroll' : 'no-scroll'; ?> dashboard-container">
    <img class="black-color clockwise inline-img stick-left text-lg left_jump" src="../img/icons/dropdown-arrow.png" style="display:none;z-index: 10;">
    <img class="black-color counterclockwise inline-img stick-right text-lg right_jump" src="../img/icons/dropdown-arrow.png" style="z-index: 10;"><?php
    foreach ( explode(',', $statuses) as $status ) { ?>
        <div class="col-xs-12 col-sm-6 col-md-4 gap-top info-block-container" style="height: inherit;">
            <div class="info-block" data-status="<?= $status ?>">
                <a href="?p=filter&s=<?= $status ?>"><div class="info-block-header">
                    <h4><?= $status; ?></h4><?php
                    $count = mysqli_fetch_assoc ( mysqli_query($dbc, "SELECT COUNT(`status`) AS `count` FROM `sales_order` WHERE `deleted` = 0 AND `status`='{$status}'".$security_query . $query_mod) )['count'] + mysqli_fetch_assoc ( mysqli_query($dbc, "SELECT COUNT(`status`) AS `count` FROM `sales_order_temp` WHERE `deleted` = 0 AND `status`='{$status}'".$security_query . $query_mod) )['count'];
                    echo '<div class="info-block-small">' . $count . '</div>'; ?>
                </div></a>
                <div class="info-block-details padded"><?php
					$counter = 0;
                    $result = mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `deleted` = 0 AND `status` = '{$status}'".$security_query.$query_mod." ORDER BY `sotid` DESC LIMIT 10");
                    if ( $result->num_rows > 0 ) {
                        while ( $row=mysqli_fetch_assoc($result) ) {
                            $counter++; ?>
                            <div class="sales-order-info info-block-detail" data-id="<?= $row['sotid'] ?>" style="<?= $counter > 10 ? 'display: none;' : '' ?>" data-searchable="<?= get_client($dbc, $row['customerid']); ?> <?= get_contact($dbc, $row['customerid']); ?>" data-type="sot">
                                <div class="row">
                                    <div class="col-sm-9"><a href="?p=preview&sotid=<?= $row['sotid'] ?>"><?= !empty(get_client($dbc, $row['customerid'])) ? get_client($dbc, $row['customerid']) : get_contact($dbc, $row['customerid']); ?><?= !empty($row['classification']) ? ': '.$row['classification'] : '' ?><?= !empty($row['name']) ? ' - '.$row['name'] : '' ?><img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit" /></a></div>
                                    <div class="col-sm-3 text-right">
                                        <!-- <b><a href="order.php?p=details&sotid=<?= $row['sotid'] ?>">$<?= ($row['total_price'] > 0) ? number_format($row['total_price'], 0) : '0.00' ; ?></a></b> -->
                                        <img src="../img/icons/drag_handle.png" class="inline-img pull-right lead-handle cursor-hand no-toggle" title="Drag" />
                                    </div>
                                </div>
                                
                                <div class="row set-row-height gap-top">
                                    <div class="col-sm-5">Status:</div>
                                    <div class="col-sm-7">
                                        <select name="status" class="chosen-select-deselect form-control" id="ssid_<?= $row['sotid'] ?>">
                                            <option value=""></option><?php
                                            foreach ( explode(',', $statuses) as $status_list ) {
                                                $selected = ($status_list==$status) ? 'selected="selected"' : '';
                                                echo '<option '. $selected .' value="'. $status_list .'">'. $status_list .'</li>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <?php if (strpos($value_config, ',Next Action,') !== FALSE) { ?>
                                    <div class="row set-row-height">
                                        <div class="col-sm-5">Next Action:</div>
                                        <div class="col-sm-7">
                                            <select name="next_action" class="chosen-select-deselect form-control" id="nsid_<?= $row['sotid'] ?>">
                                                <option value=""></option><?php
                                                foreach ( explode(',', $next_actions) as $next_action ) {
                                                    $selected = ($next_action==$row['next_action']) ? 'selected="selected"' : '';
                                                    echo '<option '. $selected .' value="'. $next_action .'">'. $next_action .'</li>';
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                
                                <?php if (strpos($value_config, ',Next Action Follow Up Date,') !== FALSE) { ?>
                                    <div class="row set-row-height">
                                        <div class="col-sm-5">Follow Up:</div>
                                        <div class="col-sm-7"><input type="text" name="next_action_date" value="<?= $row['next_action_date'] ?>" class="form-control datepicker" onchange="changeNextActionDate(this);" id="fsid_<?= $row['sotid'] ?>" /></div>
                                    </div>
                                <?php } ?>
                                
                                <div class="clearfix"></div>
                            </div><?php
                        } ?>
                    <?php }

                    $result = mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE `deleted` = 0 AND `status`='{$status}'".$security_query. $query_mod ." ORDER BY `invoice_date` DESC LIMIT 10");
                    if ( $result->num_rows > 0 ) {
                        while ( $row=mysqli_fetch_assoc($result) ) {
							$counter++; ?>
                            <div class="sales-order-info info-block-detail" data-id="<?= $row['posid'] ?>" style="<?= $counter > 10 ? 'display: none;' : '' ?>" data-searchable="<?= get_client($dbc, $row['contactid']); ?> <?= get_contact($dbc, $row['contactid']); ?>" data-type="so">
                                <div class="row">
                                    <div class="col-sm-9"><a href="index.php?p=preview&id=<?= $row['posid'] ?>"><?= !empty(get_client($dbc, $row['contactid'])) ? get_client($dbc, $row['contactid']) : get_contact($dbc, $row['contactid']); ?><?= !empty($row['classification']) ? ': '.$row['classification'] : '' ?><?= !empty($row['name']) ? ' - '.$row['name'] : '' ?><img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit" /></a></div>
                                    <div class="col-sm-3 text-right">
                                        <b><a href="index.php?p=preview&id=<?= $row['posid'] ?>">$<?= ($row['total_price'] > 0) ? number_format($row['total_price'], 0) : '0.00' ; ?></a></b>
                                        <img src="../img/icons/drag_handle.png" class="inline-img pull-right lead-handle cursor-hand no-toggle" title="Drag" />
                                    </div>
                                </div>
                                
                                <div class="row set-row-height gap-top">
                                    <div class="col-sm-5">Status:</div>
                                    <div class="col-sm-7">
                                        <select name="status" class="chosen-select-deselect form-control" id="ssid_<?= $row['posid'] ?>">
                                            <option value=""></option><?php
                                            foreach ( explode(',', $statuses) as $status_list ) {
                                                $selected = ($status_list==$status) ? 'selected="selected"' : '';
                                                echo '<option '. $selected .' value="'. $status_list .'">'. $status_list .'</li>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                                <?php if (strpos($value_config, ',Next Action,') !== FALSE) { ?>
                                    <div class="row set-row-height">
                                        <div class="col-sm-5">Next Action:</div>
                                        <div class="col-sm-7">
                                            <select name="next_action" class="chosen-select-deselect form-control" id="nsid_<?= $row['posid'] ?>">
                                                <option value=""></option><?php
                                                foreach ( explode(',', $next_actions) as $next_action ) {
                                                    $selected = ($next_action==$row['next_action']) ? 'selected="selected"' : '';
                                                    echo '<option '. $selected .' value="'. $next_action .'">'. $next_action .'</li>';
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                
                                <?php if (strpos($value_config, ',Next Action Follow Up Date,') !== FALSE) { ?>
                                    <div class="row set-row-height">
                                        <div class="col-sm-5">Follow Up:</div>
                                        <div class="col-sm-7"><input type="text" name="next_action_date" value="<?= $row['next_action_date'] ?>" class="form-control datepicker" onchange="changeNextActionDate(this);" id="fsid_<?= $row['posid'] ?>" /></div>
                                    </div>
                                <?php } ?>
                                
                                <div class="clearfix"></div>
                            </div><?php
                        } ?>
                    <?php }

                    if($counter == 0) { ?>
                        <div class="info-block-detail">No <?= strtolower($status); ?> <?= SALES_ORDER_TILE ?>.</div><?php
                    } ?>
                </div>
            </div>
        </div><?php
    } ?>
    <div class="clearfix"></div>
</div><!-- .main-screen-white -->