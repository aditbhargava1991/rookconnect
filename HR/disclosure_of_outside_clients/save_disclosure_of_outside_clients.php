 <?php
    $today_date = date('Y-m-d');
    $contactid = $_SESSION['contactid'];

    $fields = '';
    for($i=0; $i<=1; $i++) {
        $fields .= $_POST['fields_'.$i].'**FFM**';
    }

    $fields = filter_var(htmlentities($fields),FILTER_SANITIZE_STRING);
	$all_task = '';
    $total_task = count($_POST['task']);
    for($i=0; $i<$total_task; $i++) {
        if($_POST['task'][$i] != '') {
            $all_task .= $_POST['task'][$i].'**'.$_POST['hazard'][$i].'**##**';
        }
    }
    $all_task = filter_var($all_task,FILTER_SANITIZE_STRING);

    if(empty($_POST['fieldlevelriskid'])) {
        $query_insert_site = "INSERT INTO `hr_disclosure_of_outside_clients` (`hrid`, `today_date`, `all_task`, `contactid`, `fields`) VALUES	('$hrid', '$today_date', '$all_task', '$contactid', '$fields')";
        $result_insert_site	= mysqli_query($dbc, $query_insert_site);
        $fieldlevelriskid = mysqli_insert_id($dbc);
    } else {
        $fieldlevelriskid = $_POST['fieldlevelriskid'];
        $query_update_employee = "UPDATE `hr_disclosure_of_outside_clients` SET `fields` = '$fields', `all_task` = CONCAT(all_task,'$all_task') WHERE fieldlevelriskid='$fieldlevelriskid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    }

    $img = sigJsonToImage($_POST['output']);
    imagepng($img, 'disclosure_of_outside_clients/download/hr_'.$_SESSION['contactid'].'.png');

    $tab = get_hr($dbc, $hrid, 'tab');
    if($tab == 'Form') {
        $assign_staff = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

        $query_insert_upload = "INSERT INTO `hr_attendance` (`hrid`, `fieldlevelriskid`, `assign_staff`, `done`, `assign_staffid`) VALUES ('$hrid', '$fieldlevelriskid', '$assign_staff', 1, '$contactid')";
        $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

        include ('disclosure_of_outside_clients_pdf.php');
        echo disclosure_of_outside_clients_pdf($dbc,$hrid, $fieldlevelriskid);
        $url_redirect = '?reports=view&tile_name='.$tile;
    }

    if($url_redirect == '') {
        $url_redirect = '?tile_name='.$tile.'&hr='.$hrid;
    }

    if($field_level_hazard == 'field_level_hazard_save') {
        echo '<script type="text/javascript"> window.location.replace("?tab='.config_safe_str($get_hr['category']).'"); </script>';
    } else {
        echo '<script type="text/javascript">
        window.location.replace("'.$url_redirect.'"); </script>';
    }