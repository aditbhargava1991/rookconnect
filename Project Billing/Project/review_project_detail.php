<?php

if (isset($_POST['printtherapistpdf'])) {
    $projectid = $_POST['projectid'];

	class MYPDF extends TCPDF {

		public function Header() {
			$image_file = WEBSITE_URL.'/img/fresh-focus-logo-dark.png';
			$this->SetFont('helvetica', '', 13);
            $this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', '', 9);
			$footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
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
    $pdf->SetFont('helvetica', '', 9);

    $html .= review_project_detail($dbc, $projectid);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('download/projectdetail_'.$projectid.'.pdf', 'F');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('download/projectdetail_<?php echo $projectid;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    //$starttime = $starttimepdf;
}
        echo '
        <div class="pull-right"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to print, download or save the project details."><img src="../img/info.png" width="20"></a></span>';
		echo '<a id="'.$projectid.'" href="add_project.php?projectid='.$projectid.'&detail=add_view&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn mobile-block">Add Detail</a></div>';

?>

        <div class="pull-right"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to print, download or save the project details."><img src="../img/info.png" width="20"></a></span>
        <button type="submit" name="printtherapistpdf" value="Print Report" class="btn brand-btn">Print Detail</button>
        </div>

    <?php
    echo review_project_detail($dbc, $projectid);
    ?>

<?php
function review_project_detail($dbc, $projectid) {
    $review = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM project_detail WHERE projectid='$projectid'"));
    $review_project = '';

    if($review['detail_issue'] != '') {
        $review_project .= 'Issue:'.html_entity_decode($review['detail_issue']);
    }

    if($review['detail_problem'] != '') {
        $review_project .= 'Problem:'.html_entity_decode($review['detail_problem']);
    }

    if($review['detail_gap'] != ''){
        $review_project .= 'GAP:'.html_entity_decode($review['detail_gap']);
    }

    if($review['detail_technical_uncertainty'] != ''){
        $review_project .= 'Technical Uncertainty:'.html_entity_decode($review['detail_technical_uncertainty']);
    }

    if($review['detail_base_knowledge'] != ''){
        $review_project .= 'Base Knowledge:'.html_entity_decode($review['detail_base_knowledge']);
    }

    if($review['detail_do'] != ''){
        $review_project .= 'Do:'.html_entity_decode($review['detail_do']);
    }

    if($review['detail_already_known'] != ''){
        $review_project .= 'Already Known:'.html_entity_decode($review['detail_already_known']);
    }

    if($review['detail_sources'] != ''){
        $review_project .= 'Sources :'.html_entity_decode($review['detail_sources']);
    }

    if($review['detail_current_designs'] != ''){
        $review_project .= 'Current Designs :'.html_entity_decode($review['detail_current_designs']);
    }

    if($review['detail_known_techniques'] != ''){
        $review_project .= 'Known Techniques :'.html_entity_decode($review['detail_known_techniques']);
    }

    if($review['detail_review_needed'] != ''){
        $review_project .= 'Review Needed :'.html_entity_decode($review['detail_review_needed']);
    }

    if($review['detail_looking_to_achieve'] != ''){
        $review_project .= 'Looking to Achieve :'.html_entity_decode($review['detail_looking_to_achieve']);
    }

    if($review['detail_plan'] != ''){
        $review_project .= 'Plan : '.html_entity_decode($review['detail_plan']);
    }

    if($review['detail_next_steps'] != ''){
        $review_project .= 'Next Steps :'.html_entity_decode($review['detail_next_steps']);
    }

    if($review['detail_learnt'] != ''){
        $review_project .= 'Learned :'.html_entity_decode($review['detail_learnt']);
    }

    if($review['detail_discovered'] != ''){
        $review_project .= 'Discovered :'.html_entity_decode($review['detail_discovered']);
    }

    if($review['detail_tech_advancements'] != ''){
        $review_project .= 'Tech Advancements :'.html_entity_decode($review['detail_tech_advancements']);
    }

    if($review['detail_work'] != ''){
        $review_project .= 'Work :'.html_entity_decode($review['detail_work']);
    }

    if($review['detail_adjustments_needed'] != ''){
        $review_project .= 'Adjustments Needed :'.html_entity_decode($review['detail_adjustments_needed']);
    }

    if($review['detail_future_designs'] != ''){
        $review_project .= 'Future Designs :'.html_entity_decode($review['detail_future_designs']);
    }

    if($review['detail_objective'] != ''){
        $review_project .= 'Objective :'.html_entity_decode($review['detail_objective']);
    }

    if($review['detail_targets'] != ''){
        $review_project .= 'Targets :'.html_entity_decode($review['detail_targets']);
    }

    if($review['detail_audience'] != ''){
        $review_project .= 'Audience :'.html_entity_decode($review['detail_audience']);
    }

    if($review['detail_strategy'] != ''){
        $review_project .= 'Strategy :'.html_entity_decode($review['detail_strategy']);
    }

    if($review['detail_desired_outcome'] != ''){
        $review_project .= 'Desired Outcome :'.html_entity_decode($review['detail_desired_outcome']);
    }

    if($review['detail_actual_outcome'] != ''){
        $review_project .= 'Actual Outcome :'.html_entity_decode($review['detail_actual_outcome']);
    }

    if($review['detail_check'] != ''){
        $review_project .= 'Check :'.html_entity_decode($review['detail_check']);
    }

    if($review['detail2_issue'] != ''){
        $review_project .= 'Issue :'.html_entity_decode($review['detail2_issue']);
    }
    if($review['detail2_plan'] != ''){
        $review_project .= 'Plan :'.html_entity_decode($review['detail2_plan']);
    }
    if($review['detail2_do'] != ''){
        $review_project .= 'Do :'.html_entity_decode($review['detail2_do']);
    }
    if($review['detail2_check'] != ''){
        $review_project .= 'Check :'.html_entity_decode($review['detail2_check']);
    }
    if($review['detail2_adjust'] != ''){
        $review_project .= 'Adjust :'.html_entity_decode($review['detail2_adjust']);
    }

    return $review_project;
}
