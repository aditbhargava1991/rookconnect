<?php

$projectid = $_POST['projectid'];

class MYPDF extends TCPDF {

    public function Header() {
        $this->setCellHeightRatio(0.7);
        $this->SetFont('helvetica', '', 9);
        $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', '', 11);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);

$report_data = 'Hello';

$result_ticket = mysqli_query($dbc, "SELECT * FROM tickets WHERE projectid = '$projectid'");
while($row_ticket = mysqli_fetch_array( $result_ticket )) {
    if($row_ticket['to_do_date'] != '0000-00-00' && $row_ticket['to_do_date'] != '') {

        $data[] = array(
          'label' => '#'.$row_ticket['ticketid'],
          'start' => $row_ticket['to_do_date'],
          'end'   => date('Y-m-d', strtotime($row_ticket['to_do_end_date'] . ' +1 day')),
          'class' => $class,
        );
    }
}

$gantti = new Gantti($data, array(
  'title'      => get_project($dbc, $projectid, 'project_name'),
  'cellwidth'  => 25,
  'cellheight' => 35,
  'today'      => true
));

$report_data .= $gantti;

$today_date = date('Y-m-d');

$test_data = 'Hello<figure class="gantt"><figcaption>17 6 Evening</figcaption><aside><ul class="gantt-labels"
style="margin-top: 71px"><li class="gantt-label"><strong style="line-height: 35px; height:
35px">#207</strong></li><li class="gantt-label"><strong style="line-height: 35px; height:
35px">#208</strong></li></ul></aside><section class="gantt-data" id="gantt-data"><header><ul class="gantt-months"
style="width: 1550px"><li class="gantt-month" style="width: 775px"><strong style="line-height: 35px;
height: 35px">July</strong></li><li class="gantt-month" style="width: 775px"><strong style="line-height:
35px; height: 35px">August</strong></li></ul><ul class="gantt-days" style="width: 1550px"><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">01</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">02</span></li><li class="gantt-day weekend" style="width: 25px"><span style="line-height:
35px; height: 35px">03</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">04</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">05</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">06</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">07</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">08</span></li><li class="gantt-day weekend" style="width: 25px"><span
style="line-height: 35px; height: 35px">09</span></li><li class="gantt-day weekend" style="width:
25px"><span style="line-height: 35px; height: 35px">10</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">11</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">12</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">13</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">14</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">15</span></li><li class="gantt-day weekend"
style="width: 25px"><span style="line-height: 35px; height: 35px">16</span></li><li class="gantt-day
weekend" style="width: 25px"><span style="line-height: 35px; height: 35px">17</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">18</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">19</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">20</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">21</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">22</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">23</span></li><li class="gantt-day weekend" style="width: 25px"><span style="line-height:
35px; height: 35px">24</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">25</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">26</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">27</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">28</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">29</span></li><li class="gantt-day weekend" style="width: 25px"><span
style="line-height: 35px; height: 35px">30</span></li><li class="gantt-day weekend today" style="width:
25px"><span style="line-height: 35px; height: 35px">31</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">01</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">02</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">03</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">04</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">05</span></li><li class="gantt-day weekend"
style="width: 25px"><span style="line-height: 35px; height: 35px">06</span></li><li class="gantt-day
weekend" style="width: 25px"><span style="line-height: 35px; height: 35px">07</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">08</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">09</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">10</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">11</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">12</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">13</span></li><li class="gantt-day weekend" style="width: 25px"><span style="line-height:
35px; height: 35px">14</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">15</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">16</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">17</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">18</span></li><li class="gantt-day" style="width: 25px"><span style="line-height:
35px; height: 35px">19</span></li><li class="gantt-day weekend" style="width: 25px"><span
style="line-height: 35px; height: 35px">20</span></li><li class="gantt-day weekend" style="width:
25px"><span style="line-height: 35px; height: 35px">21</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">22</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">23</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">24</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">25</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">26</span></li><li class="gantt-day weekend"
style="width: 25px"><span style="line-height: 35px; height: 35px">27</span></li><li class="gantt-day
weekend" style="width: 25px"><span style="line-height: 35px; height: 35px">28</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">29</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height: 35px">30</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">31</span></li></ul></header><ul class="gantt-items" style="width: 1550px"><li class="ganttitem"><ul
class="gantt-days"><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-07-01</span></li><li class="gantt-day weekend" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-02</span></li><li class="gantt-day weekend"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-03</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-04</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-07-05</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-07-06</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-07</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-07-08</span></li><li class="gantt-day
weekend" style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-09</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-10</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-07-11</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-07-12</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-13</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-07-14</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-15</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-16</span></li><li class="gantt-day weekend" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-07-17</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-18</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-07-19</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-20</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-21</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-07-22</span></li><li class="gantt-day weekend" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-23</span></li><li class="gantt-day weekend"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-24</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-25</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-07-26</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-07-27</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-28</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-07-29</span></li><li class="gantt-day
weekend" style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-30</span></li><li
class="gantt-day weekend today" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-31</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-08-01</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-08-02</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-03</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-08-04</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-05</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-06</span></li><li class="gantt-day weekend" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-08-07</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-08</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-08-09</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-10</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-11</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-08-12</span></li><li class="gantt-day weekend" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-13</span></li><li class="gantt-day weekend"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-14</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-15</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-08-16</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-08-17</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-18</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-08-19</span></li><li class="gantt-day
weekend" style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-20</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-21</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-08-22</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-08-23</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-24</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-08-25</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-26</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-27</span></li><li class="gantt-day weekend" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-08-28</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-29</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-08-30</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-31</span></li></ul><span
class="gantt-block" style="left: 422px; width: 23px; height: 27px"><strong class="gantt-blocklabel">1</strong></span></li><li
class="gantt-item"><ul class="gantt-days"><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-01</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-02</span></li><li class="gantt-day weekend" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-07-03</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-04</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-07-05</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-06</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-07</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-07-08</span></li><li class="gantt-day weekend" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-09</span></li><li class="gantt-day weekend"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-10</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-11</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-07-12</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-07-13</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-14</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-07-15</span></li><li class="gantt-day
weekend" style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-16</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-17</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-07-18</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-07-19</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-20</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-07-21</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-22</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-23</span></li><li class="gantt-day weekend" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-07-24</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-25</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-07-26</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-27</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-07-28</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-07-29</span></li><li class="gantt-day weekend" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-07-30</span></li><li class="gantt-day weekend today"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-07-31</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-01</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-08-02</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-08-03</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-04</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-08-05</span></li><li class="gantt-day
weekend" style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-06</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-07</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-08-08</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-08-09</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-10</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-08-11</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-12</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-13</span></li><li class="gantt-day weekend" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-08-14</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-15</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-08-16</span></li><li class="gantt-day"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-17</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-18</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-08-19</span></li><li class="gantt-day weekend" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-20</span></li><li class="gantt-day weekend"
style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-21</span></li><li
class="gantt-day" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-22</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-08-23</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-08-24</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-25</span></li><li class="gantt-day" style="width:
25px"><span style="line-height: 35px; height: 35px">2016-08-26</span></li><li class="gantt-day
weekend" style="width: 25px"><span style="line-height: 35px; height: 35px">2016-08-27</span></li><li
class="gantt-day weekend" style="width: 25px"><span style="line-height: 35px; height:
35px">2016-08-28</span></li><li class="gantt-day" style="width: 25px"><span style="line-height: 35px;
height: 35px">2016-08-29</span></li><li class="gantt-day" style="width: 25px"><span style="lineheight:
35px; height: 35px">2016-08-30</span></li><li class="gantt-day" style="width: 25px"><span
style="line-height: 35px; height: 35px">2016-08-31</span></li></ul><span class="gantt-block"
style="left: 1047px; width: 273px; height: 27px"><strong class="gantt-blocklabel">11</strong></span></li></ul><time
style="top: 70px; left: 762px"
datetime="2016-07-31">Today</time></section></figure>
Powered by TCPDF (www.tcpdf.org)';

$pdf->writeHTML($test_data, true, 0, true, 0);

$pdf->Output('Download/patientunpaid_1.pdf', 'F');

echo '<script type="text/javascript" language="Javascript">
    window.open("Download/patientunpaid_1.pdf", "fullscreen=yes");
    </script>';
