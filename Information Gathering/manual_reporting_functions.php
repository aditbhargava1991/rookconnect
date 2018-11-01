<?php include_once('../include.php');

function infogathering_pdf($dbc, $infogatheringid, $fieldlevelriskid) {
    $form = get_infogathering($dbc, $infogatheringid, 'form');
    $user_form_id = get_infogathering($dbc, $infogatheringid, 'user_form_id');

    if($user_form_id > 0) {
        $user_pdf = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `infogathering_pdf` WHERE `fieldlevelriskid` = '$fieldlevelriskid' AND `infogatheringid` = '$infogatheringid' ORDER BY `infopdfid` DESC"));
        $pdf_path = $user_pdf['pdf_path'];
        if(empty($pdf_path)) {
            $user_pdf = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_pdf` WHERE `pdf_id` = '$fieldlevelriskid'"));
            $pdf_path = 'download/'.$user_pdf['generated_file'];
        }
        return $pdf_path;
    } else {
        if($form == 'Client Business Introduction') {
            $pdf_path = 'client_business_introduction/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Branding Questionnaire') {
            $pdf_path = 'branding_questionnaire/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Website Information Gathering') {
            $pdf_path = 'website_information_gathering_form/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Blog') {
            $pdf_path = 'blog/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Marketing Strategies Review') {
            $pdf_path = 'marketing_strategies_review/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Social Media Info Gathering') {
            $pdf_path = 'social_media_info_gathering/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Social Media Start Up Questionnaire') {
            $pdf_path = 'social_media_start_up_questionnaire/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'Business Case Format') {
            $pdf_path = 'business_case_format/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Product-Service Outline') {
            $pdf_path = 'product_service_outline/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Client Reviews') {
            $pdf_path = 'client_reviews/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'SWOT') {
            $pdf_path = 'swot/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'GAP Analysis') {
            $pdf_path = 'gap_analysis/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Lesson Plan') {
            $pdf_path = 'lesson_plan/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Marketing Plan Information Gathering') {
            $pdf_path = 'marketing_plan_information_gathering/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Marketing Information') {
            $pdf_path = 'marketing_information/download/infogathering_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
    }
}