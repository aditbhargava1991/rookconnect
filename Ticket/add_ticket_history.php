<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>History</h3>') ?>
<a class="btn brand-btn" href="ticket_view_history.php?ticketid=<?= $ticketid ?>" onclick="overlayIFrameSlider(this.href.replace('ticketid=0','ticketid='+ticketid),'auto',true,true,'auto'); return false;">View History</a>