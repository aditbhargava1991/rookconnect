<?php
/*
Inventory Listing
*/
if (isset($_POST['export_pdf'])) {
    $checklistid = $_POST['checklistid'];
    $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM checklist WHERE checklistid='$checklistid'"));

    $security = $get_contact['security'];
    $checklist_type = $get_contact['checklist_type'];
    $checklist_name = $get_contact['checklist_name'];

    DEFINE('CHECKLIST_NAME', $checklist_name);
    class MYPDF extends TCPDF {
        public function Header() {
			$this->SetFont('helvetica', '', 30);
			$footer_text = '<p style="text-align:center; background-color: #516371; color:white; height:100px; ">'.CHECKLIST_NAME.'</p>';
			$this->writeHTMLCell(0, 40, 15 , 15, $footer_text, 0, 0, false, "L", true);
        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    $pdf->SetMargins(PDF_MARGIN_LEFT, 45, PDF_MARGIN_RIGHT);

    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->AddPage();

    $pdf->SetFont('helvetica', '', 14);
    $pdf->SetTextColor(53,175,199);

    $html_weekly .= '<table cellpadding="4">';

    $query_check_credentials = "SELECT * FROM checklist_name WHERE checklistid='$checklistid' ORDER BY checked, priority";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);
    while($row = mysqli_fetch_array($result)) {
        $html_weekly .= '<tr>
                        <td width="5%">';
        $checked = '';
        if($row['checked'] == 1) {
            $html_weekly .= '<img src="../img/checkmark.png" width="15px">&nbsp;&nbsp;';
        } else {
            $html_weekly .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $html_weekly .= '</td><td width="95%">'.$row['checklist'].'</td></tr>';
    }

    $html_weekly .= '</table>';

    $pdf->writeHTML($html_weekly, true, false, true, false, '');

    $pdf->Output('download/Checklist_'.$checklistid.'.pdf', 'F');

    if($security == 'My Checklist') {
        $url = 'review_project.php?type=checklist&projectid='.$_POST['projectid'].'&category='.$checklist_type.'&checklistid='.$checklistid;
    } else {
        $url = 'review_project.php?type=company_checklist&projectid='.$_POST['projectid'].'&category='.$checklist_type.'&checklistid='.$checklistid;
    }

    echo '<script type="text/javascript">
    window.location.replace("'.$url.'");
    window.open("download/Checklist_'.$checklistid.'.pdf", "fullscreen=yes");
    </script>';
}
?>
<script type="text/javascript" src="checklist.js"></script>
<style type='text/css'>
.ui-state-disabled  { pointer-events: none !important; }
.display-field {
  display: inline-block;
  text-indent: 2px;
  vertical-align: top;
  width: 97%;
}
</style>
<script>
setTimeout(function() {

var maxWidth = Math.max.apply( null, $( '.ui-sortable' ).map( function () {
    return $( this ).outerWidth( true );
}).get() );

var maxHeight = -1;

$('.ui-sortable').each(function() {
  maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();

});

$(function() {
  $(".connectedChecklist").width(maxWidth).height(maxHeight);
});
$( '.connectedChecklist' ).each(function () {
    this.style.setProperty( 'height', maxHeight, 'important' );
	this.style.setProperty( 'width', maxWidth, 'important' );

	<?php if($check_table_orient == 1) { ?>
		$(this).attr('style', 'height:'+maxHeight+'px !important; width:'+maxWidth+'px !important');
	<?php } else { ?>
		$(this).attr('style', 'height:'+maxHeight+'px !important;');
	<?PHP } ?>
});

}, 200);

$(document).ready(function() {
});
</script>
    <?php
    $projectid = $_GET['projectid'];

	echo '<div class="clearfix"></div>';

	echo '<div class="tab-container">';

		echo "
			<div class='pull-left tab tab-nomargin'>
				<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='Click here to see your personal Checklists.'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>
				<a href='review_project.php?type=checklist&projectid=".$projectid."&category=ongoing'><button type='button' class='btn brand-btn mobile-block mobile-100'>My Checklists</button></a>
			</div>";

		echo "
			<div class='pull-left tab tab-nomargin'>
				<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='Click here to see Checklists from everyone in the company.'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>
				<a href='review_project.php?type=company_checklist&projectid=".$projectid."&category=ongoing'><button type='button' class='btn brand-btn mobile-block mobile-100 active_tab'>Company Checklists</button></a>
			</div>";

	echo '</div><div class="clearfix"></div><br />';

    $category = $_GET['category'];
    $active_on = '';
    $active_daily = '';
    $active_weekly = '';
    $active_monthly = '';
    if($category == 'ongoing') {
        $active_on = ' active_tab';
    } else if($category == 'daily') {
        $active_daily = ' active_tab';
    } else if($category == 'weekly') {
        $active_weekly = ' active_tab';
    } else {
        $active_monthly = ' active_tab';
    }

	echo '<div class="tab-container">';
		echo "
			<div class='pull-left tab tab-nomargin'>
				<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='Uncategorized/ongoing Checklists.'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>
				<a href='review_project.php?type=company_checklist&projectid=".$projectid."&category=ongoing'><button type='button' class='btn brand-btn mobile-block ".$active_on."'>Ongoing</button></a>
			</div>";
		echo "
			<div class='pull-left tab tab-nomargin'>
				<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='Checklists used every day.'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>
				<a href='review_project.php?type=company_checklist&projectid=".$projectid."&category=daily'><button type='button' class='btn brand-btn mobile-block ".$active_daily."'>Daily</button></a>
			</div>";
		echo "
			<div class='pull-left tab tab-nomargin'>
				<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='Checklists used for each week.'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>
				<a href='review_project.php?type=company_checklist&projectid=".$projectid."&category=weekly'><button type='button' class='btn brand-btn mobile-block ".$active_weekly."'>Weekly</button></a>
			</div>";
		echo "
			<div class='pull-left tab tab-nomargin'>
				<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='Checklists used for each month.'><img src='" . WEBSITE_URL . "/img/info.png' width='20'></a></span>
				<a href='review_project.php?type=company_checklist&projectid=".$projectid."&category=monthly'><button type='button' class='btn brand-btn mobile-block ".$active_monthly."'>Monthly</button></a>
			</div>";
	echo '</div><div class="clearfix"></div><br />';

    echo '
		<div class="mobile-100-container">
			<a href="add_checklist.php?projectid='.$projectid.'" class="btn brand-btn mobile-block gap-bottom pull-right">Add Checklist</a>
			<span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Checklist."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>
		</div>';
    ?>

	<br><br>

    <form name="form_sites" method="post" action="" class="form-inline" role="form">
        <?php

        $contactid = $_SESSION['contactid'];
        echo '<div class="mobile-100-container">';
        $result = mysqli_query($dbc, "SELECT * FROM checklist WHERE security = 'Company Checklist' AND checklist_type='$category' AND assign_staff LIKE '%," . $contactid . ",%' AND projectid='$projectid'");

        $checklistid_url = $_GET['checklistid'];

        while($row = mysqli_fetch_array($result)) {
            $active_daily = '';
            if(($checklistid_url == $row['checklistid'])) {
                $active_daily = 'active_tab';
            }

            echo "<a href='review_project.php?type=company_checklist&projectid=".$projectid."&category=".$category."&checklistid=".$row['checklistid']."'><button type='button' class='mobile-100 btn brand-btn mobile-block ".$active_daily."' >".$row['checklist_name']."</button></a>&nbsp;&nbsp;";
        }
        ?>
        <br><br>
		</div>
        <div>
        <?php
        if(!empty($_GET['checklistid'])) {
            $checklistid = $_GET['checklistid'];
            $checklist_name = get_checklist($dbc, $checklistid, 'checklist_name');

            echo '<a href="add_checklist.php?checklistid='.$checklistid.'" class="btn brand-btn mobile-block gap-bottom pull-right">Edit</a>';

            echo '<a href=\'../delete_restore.php?action=delete&remove_project_checklist=all&checklistid='.$checklistid.'&projectid='.$projectid.'\' onclick="return confirm(\'Are you sure?\')" class="btn brand-btn mobile-block gap-bottom pull-right">Delete</a>';

            echo '</form>';
            echo '<form name="form_sites1" method="post" action="" class="form-inline" role="form">';

            echo '<button type="submit" name="export_pdf" value="Submit" class="btn brand-btn pull-right">Export</button><br><br>';
            echo '<input type="hidden" name="checklistid" value="'.$checklistid.'" />';
            echo '<input type="hidden" name="projectid" value="'.$projectid.'" />';

            echo '<ul id="sortable'.$i.'" class="connectedChecklist">
            <li class="ui-state-default ui-state-disabled" style="cursor:pointer; font-size: 30px;">'.$checklist_name.'</li>';

            $result = mysqli_query($dbc, "SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND checked = 0 ORDER BY priority");

            while($row = mysqli_fetch_array( $result )) {

                echo '<li id="'.$row['checklistnameid'].'" class="ui-state-default"><span style="cursor:pointer; font-size: 25px;"><input type="checkbox" onclick="checklistChange(this);" value="'.$row['checklistnameid'].'" style="height: 30px; width: 30px;" name="checklistnameid[]">&nbsp;&nbsp;<span class="display-field">'.$row['checklist'].'</span></span>';

                echo '</li>';
            }

            echo '</form>';
            echo '<form name="form_sites2" method="post" action="" class="form-inline" role="form">';

            echo '<li class="new_task_box"><input type="checkbox" style="height: 30px; width: 30px;">&nbsp;&nbsp;&nbsp;<input onChange="changeEndAme(this)" name="add_checklist" placeholder="Add New Checklist Item" id="add_new_task '.$checklistid.'" type="text" class="form-control" /></li>';

            $result = mysqli_query($dbc, "SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND checked = 1");

            while($row = mysqli_fetch_array( $result )) {

                $info = ' : '.$row['updated_date']. ' : '.$row['updated_by'];
                echo '<li id="'.$row['checklistnameid'].'" class="ui-state-default"><span style="cursor:pointer; font-size: 20px;"><input type="checkbox" onclick="checklistChange(this);" checked value="'.$row['checklistnameid'].'" style="height: 30px; width: 30px;" name="checklistnameid[]">&nbsp;&nbsp;'.$row['checklist'].$info.'</span>';

                echo '</li>';
            }
            echo '</ul>';
            $i++;
        }
        ?>
        </div>

		</form>
