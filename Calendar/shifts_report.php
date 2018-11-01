<?php include_once('../include.php');
checkAuthorised('calendar_rook');
include_once('../Calendar/calendar_functions_inc.php');
include_once('../Calendar/calendar_settings_inc.php');

if (isset($_POST['submit_report'])) {
    include('../tcpdf/tcpdf.php');
    $staff_include = $_POST['staff_include'];
    if(in_array('ALL', $staff_include)) {
        $staff_include = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query),MYSQLI_ASSOC));
    }

    $sort_by = $_POST['sort_by'];
    $start_date = date('Y-m-d', strtotime($_POST['start_date']));
    $end_date = date('Y-m-d', strtotime($_POST['end_date']));

    $report_list = [];
    if($sort_by == 'date') {
        for($current_date = $start_date; strtotime($current_date) <= strtotime($end_date); $current_date = date('Y-m-d', strtotime($current_date.' + 1 day'))) {
            $day_of_week = date('l', strtotime($current_date));
            foreach($staff_include as $staffid) {
                $shifts = checkShiftIntervals($dbc, $staffid, $day_of_week, $current_date);
                foreach($shifts as $shift) {
                    $report_list[$current_date][] = [get_contact($dbc, $staffid), $shift['starttime'], $shift['endtime']];
                }
            }
            // if(empty($report_list[$current_date])) {
            //     $report_list[$current_date][] = 'No Shifts Found.';
            // }
        }
    } else {
        foreach($staff_include as $staffid) {
            for($current_date = $start_date; strtotime($current_date) <= strtotime($end_date); $current_date = date('Y-m-d', strtotime($current_date.' + 1 day'))) {
                $day_of_week = date('l', strtotime($current_date));
                $shifts = checkShiftIntervals($dbc, $staffid, $day_of_week, $current_date);
                foreach($shifts as $shift) {
                    $report_list[$staffid][] = [$current_date, $shift['starttime'], $shift['endtime']];
                }
            }
            // if(empty($report_list[$staffid])) {
            //     $report_list[$staffid][] = 'No Shifts Found.';
            // }
        }
    }

    //PDF Settings
    $pdf_settings = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts_pdf`"));

    $header_logo = !empty($pdf_settings['header_logo']) ? $pdf_settings['header_logo'] : '';
    $header_logo_align = !empty($pdf_settings['header_logo_align']) ? $pdf_settings['header_logo_align'] : 'R';
    $header_text = !empty($pdf_settings['header_text']) ? $pdf_settings['header_text'] : '';
    $header_align = !empty($pdf_settings['header_align']) ? $pdf_settings['header_align'] : 'L';

    $footer_logo = !empty($pdf_settings['footer_logo']) ? $pdf_settings['footer_logo'] : '';
    $footer_logo_align = !empty($pdf_settings['footer_logo_align']) ? $pdf_settings['footer_logo_align'] : 'L';
    $footer_text = !empty($pdf_settings['footer_text']) ? $pdf_settings['footer_text'] : '';
    $footer_align = !empty($pdf_settings['footer_align']) ? $pdf_settings['footer_align'] : 'C';

    DEFINE(FORM_HEADER_LOGO, $header_logo);
    DEFINE(FORM_HEADER_LOGO_ALIGN, $header_logo_align);
    DEFINE(FORM_HEADER_TEXT, html_entity_decode($header_text));
    DEFINE(FORM_HEADER_ALIGN, $header_align);

    DEFINE(FORM_FOOTER_LOGO, $footer_logo);
    DEFINE(FORM_FOOTER_LOGO_ALIGN, $footer_logo_align);
    DEFINE(FORM_FOOTER_TEXT, html_entity_decode($footer_text));
    DEFINE(FORM_FOOTER_ALIGN, $footer_align);

    class MYPDF extends TCPDF {

        //Page header
        public function Header() {
            if(FORM_HEADER_LOGO != '') {
                $image_file = '../Calendar/download/'.FORM_HEADER_LOGO;
                $this->Image($image_file, 10, 5, 0, 25, '', '', 'T', false, 300, FORM_HEADER_LOGO_ALIGN, false, false, 0, false, false, false);
            }

            if(FORM_HEADER_TEXT != '') {
                $this->setCellHeightRatio(0.7);
                $this->writeHTMLCell(0, 0, 7.5, 5, FORM_HEADER_TEXT, 0, 0, false, true, FORM_HEADER_LOGO, true);
            }
        }

        //Page footer
        public function Footer() {
            $this->SetY(-10);
            $this->SetFont('helvetica', '', 6);
            $this->writeHTMLCell(0, 0, '', '', 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, 0, false, true, 'R', true);

            if(FORM_FOOTER_TEXT != '') {
                $this->SetY(-20);
                $this->setCellHeightRatio(0.7);
                $this->writeHTMLCell(0, 0, '' , '', FORM_FOOTER_TEXT, 0, 0, false, true, FORM_FOOTER_ALIGN, true);
            }

            if(FORM_FOOTER_LOGO != '') {
                $image_file = '../Calendar/download/'.FORM_FOOTER_LOGO;
                $this->Image($image_file, 0, 255, 0, 15, '', '', 'T', false, 300, FORM_FOOTER_LOGO_ALIGN, false, false, 0, false, false, false);
            }
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
    $pdf->SetMargins(PDF_MARGIN_LEFT, (FORM_HEADER_LOGO != '' ? 35 : (!empty($header_text) ? 20 : 10)), PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);
    $pdf->setCellHeightRatio(1);

    $html = '<style>
            th { background-color: #ccc; font-weight: bold; }
        </style>';
    $html .= '<p style="text-align: center"><h1>Shifts Report - '.$start_date.' to '.$end_date.'</h1></p>';

    foreach($report_list as $key => $report_rows) {
        $html .= '<h3>'.($sort_by == 'date' ? $key : get_contact($dbc, $key)).'</h3>';
        if($report_rows[0] == 'No Shifts Found.') {
            $html .= '<p>No Shifts Found.</p><br>';
        } else {
            $html .= '<table border="1" cellpadding="2">';
            $html .= '<tr>';
            $html .= '<th>'.($sort_by == 'date' ? 'Staff' : 'Date').'</th>';
            $html .= '<th>Start Time</th>';
            $html .= '<th>End Time</th>';
            $html .= '</tr>';
            foreach($report_rows as $report_row) {
                $html .= '<tr>';
                $html .= '<td>'.$report_row[0].'</td>';
                $html .= '<td>'.$report_row[1].'</td>';
                $html .= '<td>'.$report_row[2].'</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
            $html .= '<br>';
        }
    }

    $pdf->writeHTML(utf8_encode($html), true, false, true, false, '');

    if(!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    $today_date = date('Y-m-d_H-i-a', time());
    $file_name = 'shifts_report_'.$today_date.'.pdf';
    $pdf->Output('download/'.$file_name, 'F');

    echo '<script type="text/javascript">
            window.location.replace("download/'.$file_name.'", "_blank");
        </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
    $('[name="submit_report"]').click(function() {
        window.parent.$('[name="calendar_iframe"]').load();
    });
});
</script>
<div class="pad-left pad-right standard-body-content" style="background-color: #fff">
    <h3>Shifts Report</h3>
    <div class="block-group" style="height: calc(100% - 4.5em); overflow-y: auto;">
        <form name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form" id="shift_report_form" target="_blank">
            <div class="form-group">
                <label class="col-sm-4 control-label">Staff:</label>
                <div class="col-sm-8">
                    <select name="staff_include[]" multiple data-placeholder="Select Staff" class="chosen-select-deselect form-control">
                        <option value="ALL">All Staff</option>
                        <?php $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query),MYSQLI_ASSOC));
                        foreach($contact_list as $staffid) { ?>
                            <option value="<?= $staffid ?>"><?= get_contact($dbc, $staffid) ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Sort By:</label>
                <div class="col-sm-8">
                    <label class="form-checkbox"><input type="radio" name="sort_by" value="staff" checked> Staff</label>
                    <label class="form-checkbox"><input type="radio" name="sort_by" value="date"> Date</label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Start Date:</label>
                <div class="col-sm-8">
                    <input type="text" name="start_date" value="<?= date('Y-m-d') ?>" class="form-control datepicker">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">End Date:</label>
                <div class="col-sm-8">
                    <input type="text" name="end_date" value="<?= date('Y-m-d', strtotime(date('Y-m-d').'+ 1 month')) ?>" class="form-control datepicker">
                </div>
            </div>
            <div class="form-group pull-right">
                <a href="?" class="btn brand-btn" id="shift_report_cancel">Cancel</a>
                <button type="submit" name="submit_report" value="submit_report" class="btn brand-btn">Submit</button>
            </div>
        </form>
    </div>
</div>