<?php $invoice_options = explode(',',(!empty($invoice_type) ? (!empty(get_config($dbc, 'invoice_custom_ticket_fields_'.config_safe_str($invoice_type))) ? get_config($dbc, 'invoice_custom_ticket_fields_'.config_safe_str($invoice_type)) : get_config($dbc, 'invoice_custom_ticket_fields')) : get_config($dbc, 'invoice_custom_ticket_fields')));
if($invoiceid > 0 && in_array('append_ticket',$invoice_options)) {
    $invoice_append_qty = get_config($dbc, 'invoice_ticket_append_qty'.(empty($invoice_type) ? '' : '_'.config_safe_str($invoice_type)));
    $ticket_pdf = [];
    foreach(explode(',',get_field_value('ticketid','invoice','invoiceid',$invoiceid)) as $ticketid) {
        if($ticketid > 0) {
            $get_ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
            $value_config .= get_config($dbc, 'ticket_fields_'.$get_ticket['ticket_type']).',';
            $sort_order = explode(',',get_config($dbc, 'ticket_sortorder_'.$get_ticket['ticket_type']));
            $stop_list = $dbc->query("SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 AND IFNULL(`type`,'') NOT IN ('origin','destination') ORDER BY `sort`");
            $invoice_stop = $stop_list->fetch_assoc();
            do {
                $_GET['stop'] = $invoice_stop['id'];
                $html = '';
                include('../Ticket/ticket_pdf_content.php');
                $ticket_pdf[] = $html;
            } while($invoice_stop = $stop_list->fetch_assoc());
        }
    }
    $ticket = $pdf;
    $pdf_list = [];
    foreach($ticket_pdf as $i => $html) {
        if($i % $invoice_append_qty == 0 && count($ticket_pdf) > $invoice_append_qty) {
            $pdf_list[] = $ticket;
            $ticket = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        }
        $ticket->AddPage();
        $ticket->SetFont('helvetica', '', 9);
        $ticket->writeHTML($html, true, false, true, false, '');
    }
    $pdf_list[] = $ticket;
    if(count($pdf_list) > 1) {
        foreach($pdf_list as $i => $tickets) {
            if($i > 0) {
                $tickets->Output('download/invoice_'.$invoiceid.'_'.config_safe_str(TICKET_NOUN).'_'.($i).'.pdf', 'F');
            }
        }
    }
}