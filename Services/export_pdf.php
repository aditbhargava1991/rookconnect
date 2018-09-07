<?php
/*
 * Services Tile Main Page
 */
error_reporting(0);
include ('../include.php');
include ('field_list.php');

if(isset($_POST['submit_pdf'])) {
    include('services_pdf.php');
} else if(isset($_POST['submit_csv_export'])) {
    $category = $_POST['category'];
    $template = $_POST['template'];
    $today_date = date('Y-m-d_h-i-s-a', time());
    if(!file_exists('exports')) {
        mkdir('exports', 0777, true);
    }
    $FileName = "exports/services_export_".$today_date.".csv";
    $file = fopen($FileName, "w");
    $HeadingsArray = ['serviceid'];
    $query_fields = '`serviceid`,';

    if(!empty($template)) {
        $query_template = "SELECT GROUP_CONCAT(`heading_name`) as field_list FROM `services_templates_headings` WHERE `template_id` = '$template' AND `deleted` = 0 ORDER BY `sort_order` ASC";
        $result_template = mysqli_fetch_assoc(mysqli_query($dbc, $query_template));
        $fields_config = $result_template['field_list'];
    } else {
        $fields_config = '';
    }

    foreach($field_list as $key => $field) {
        if(strpos(','.$fields_config.',', ','.$key.',') || empty($fields_config)) {
            $HeadingsArray[] = $key;
            $query_fields .= '`'.$key.'`,';
        }
    }
    $query_fields = rtrim($query_fields, ',');
    fputcsv($file,$HeadingsArray);

    if(empty($category)) {
        $sql = mysqli_query($dbc, 'SELECT '.$query_fields.' FROM `services` WHERE `deleted` = 0');
    } else {
        $sql = mysqli_query($dbc, 'SELECT '.$query_fields.' FROM `services` WHERE `deleted` = 0 AND category = "'.$category.'"');
    }

    // Save all records without headings
    while($row = mysqli_fetch_assoc($sql)){
        $valuesArray=array();
        foreach($row as $name => $value){
            $valuesArray[]=$value;
        }
        fputcsv($file,$valuesArray);
    }
    fclose($file);

    header("Location: $FileName");
    if(empty($category)) {
        $update_log = 'All services were exported.';
    } else {
        $update_log = 'All services under the '.$category.' category was exported.';
    }

    $today_date = date('Y-m-d H:i:s', time());
    $contactid = $_SESSION['contactid'];
    $result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
    while($row = mysqli_fetch_assoc($result)) {
        $name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
    }
    $query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Services', 'Export', '$update_log', '$today_date', '$name')";
    $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
    echo '<script type="text/javascript"> alert("Successfully exported CSV file."); </script>';
} else if(isset($_POST['submit_csv_import'])) {
    $i = 0;
    $file = htmlspecialchars($_FILES['file']['tmp_name'], ENT_QUOTES);
    $handle = fopen($file, "r");
    $headers = fgetcsv($handle, 0, ',');
    $c = 0;
    while(($row = fgetcsv($handle, 1000, ",")) !== false)
    {
        $values = [];
        foreach($headers as $i => $col) {
            $values[filter_var($col,FILTER_SANITIZE_STRING)] = filter_var(htmlentities($row[$i],FILTER_SANITIZE_STRING));
        }

        $serviceid = $values['serviceid'];
        $new_service = false;
        if(empty($serviceid)) {
            mysqli_query($dbc, "INSERT INTO `services` VALUES ()");
            $serviceid = mysqli_insert_id($dbc);
            $values['serviceid'] = $serviceid;
            $new_service = true;

            $update_log = 'Service Added (ID: '.$serviceid.')';
            $today_date = date('Y-m-d H:i:s', time());
            $contactid = $_SESSION['contactid'];
            $result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
            while($row = mysqli_fetch_assoc($result)) {
                $name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
            }
            $query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Services', 'Add', '$update_log', '$today_date', '$name')";
            $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
        }

        if($serviceid > 0) {
            $original = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$serviceid'"));
            $updates = [];
            foreach($values as $field => $value) {
                if($field != '' && $field != 'serviceid') {
                    $updates[] = "`$field`='$value'";
                    if($value != $original[$field] && !empty($value) && !empty($original[$field]) && !$new_service) {
                        $update_log = $field.' was changed from "'.$original[$field].'" to "'.$value.'" where service ID = '.$serviceid;
                        $today_date = date('Y-m-d H:i:s', time());
                        $contactid = $_SESSION['contactid'];
                        $result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
                        while($row = mysqli_fetch_assoc($result)) {
                            $name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
                        }
                        $query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Services', 'Edit', '$update_log', '$today_date', '$name')";
                        $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
                    }
                }
            }
        }
        $updates = implode(',',$updates);
        $sql = "UPDATE `services` SET $updates WHERE `serviceid` = '$serviceid'";
        mysqli_query($dbc, $sql);
        $before_change = "";
				$history = "Service with service id $serviceid has been updated. <br />";
	  		add_update_history($dbc, 'service_history', $history, '', $before_change);
    }

    echo '<script type="text/javascript"> alert("Successfully imported CSV file. Please check the Services dashboard to view your newly added items."); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
    $(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').is(':visible') && $('.sidebar').is(':visible')) {
			var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
			if(available_height > 300) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
			}
            var sidebar_height = $('.tile-sidebar').outerHeight(true);
            $('.has-main-screen, .has-main-screen .main-screen').css('min-height', sidebar_height);
		} else {
			$('.main-screen .main-screen').css('height','auto');
		}
	}).resize();
});
function generate_import_csv() {
    $.ajax({
        url: 'services_ajax.php?action=generate_import_csv',
        method: 'GET',
        result: 'html',
        success: function(response) {
            window.open(response, "_blank");
        }
    });
}
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
	checkAuthorised();
    if(empty($_GET['type'])) {
        $_GET['type'] = 'exportpdf';
    }
    $type = $_GET['type'];
?>

<div class="container">
    <div class="row">
		<div class="main-screen"><?php
            include('tile_header.php'); ?>

            <div class="tile-container">
                <ul class='sidebar hide-titles-mob col-sm-3' style='padding-left: 15px;'>
                    <a href="?type=exportpdf"><li <?= ($type == 'exportpdf' ? 'class="active blue"' : '') ?>>Export PDF</li></a>
                    <a href="?type=exportcsv"><li <?= ($type == 'exportcsv' ? 'class="active blue"' : '') ?>>Export CSV</li></a>
                    <?php if (vuaed_visible_function($dbc, 'services') == 1) { ?>
                        <a href="?type=importcsv"><li <?= ($type == 'importcsv' ? 'class="active blue"' : '') ?>>Import CSV</li></a>
                    <?php } ?>
                </ul>

                <div class='col-sm-9 has-main-screen'>
                    <div class='main-screen'>
                        <form id="form1" name="form1" method="post" action="export_pdf.php" enctype="multipart/form-data" class="form-horizontal" role="form">
                            <div class="col-sm-12">
                                <?php if($type == 'exportpdf') { ?>
                                    <h3>Export PDF</h3>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Category:</label>
                                        <div class="col-sm-8">
                                            <select name="category" class="chosen-select-deselect">
                                                <option value="">All Categories</option>
                                                <?php $category_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `services` WHERE `deleted` = 0 ORDER BY `category`"),MYSQLI_ASSOC);
                                                    foreach ($category_list as $category) {
                                                        echo '<option value="'.$category['category'].'">'.$category['category'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Template:</label>
                                        <div class="col-sm-8">
                                            <select name="template" class="chosen-select-deselect">
                                                <option></option>
                                                <?php $template_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `services_templates` WHERE `deleted` = 0 ORDER BY `template_name`"),MYSQLI_ASSOC);
                                                    foreach ($template_list as $template) {
                                                        echo '<option value="'.$template['id'].'">'.$template['template_name'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">PDF Styling:</label>
                                        <div class="col-sm-8">
                                            <select name="pdf_styling" class="chosen-select-deselect">
                                                <option></option>
                                                <option value="design_styleA">Design Style A</option>
                                                <option value="design_styleB">Design Style B</option>
                                                <option value="design_styleC">Design Style C</option>
                                            </select>
                                        </div>
                                    </div>

                                    <button type="submit" name="submit_pdf" value="Submit" class="btn brand-btn pull-right">Export</button>
                                    <a href="index.php" class="btn brand-btn pull-right">Back</a>
                                <?php } else if($type == 'exportcsv') { ?>
                                    <h3>Export CSV</h3>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Category:</label>
                                        <div class="col-sm-8">
                                            <select name="category" class="chosen-select-deselect">
                                                <option value="">All Categories</option>
                                                <?php $category_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `services` WHERE `deleted` = 0 ORDER BY `category`"),MYSQLI_ASSOC);
                                                    foreach ($category_list as $category) {
                                                        echo '<option value="'.$category['category'].'">'.$category['category'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Template:</label>
                                        <div class="col-sm-8">
                                            <select name="template" class="chosen-select-deselect">
                                                <option></option>
                                                <?php $template_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `services_templates` WHERE `deleted` = 0 ORDER BY `template_name`"),MYSQLI_ASSOC);
                                                    foreach ($template_list as $template) {
                                                        echo '<option value="'.$template['id'].'">'.$template['template_name'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <button type="submit" name="submit_csv_export" value="Submit" class="btn brand-btn pull-right">Export</button>
                                    <a href="index.php" class="btn brand-btn pull-right">Back</a>
                                <?php } else if($type == 'importcsv' && vuaed_visible_function($dbc, 'services' == 1)) { ?>
                                    <h3>Import CSV</h3>

                                    <div class="notice">Steps to Import into the Services tile:<br><Br>
                                        <b>1.</b> Please export the Excel (CSV) file from this page: <a href='?type=exportcsv' target="_BLANK" style='color:white; text-decoration:underline;'>Export CSV</a> or download the following file to add new Services: <a href='' onclick="generate_import_csv(); return false;" target="_BLANK" style='color:white; text-decoration:underline;'>Add_multiple_services.csv</a>.<br><br>
                                        <b>2.</b> Make your desired changes inside of the Excel file.<br>
                                        <span style='color:pink;'><img src='../img/warning.png' style='width:25px;'> NOTE</span>: Do not change/move/delete any of the column titles in the first row. Also, do not change the data in the first column (<em>serviceid</em>), or else the edits may not go through properly. <br><span style='color:lightgreen'><b>Hint:</b></span> press CTRL+F on your keyboard to find the fields you would like to populate; this will help you locate them faster.<br><br>
                                        <b>3.</b> After you are done editing the data, save your Excel (CSV) file, upload the CSV file below, and hit submit.<br><br>
                                        <b>4.</b> Please look for your edited Services in the Services dashboard!<br><br>
                                        <input class="form-control" type="file" name="file" />
                                    </div>

                                    <button type="submit" name="submit_csv_import" value="Submit" class="btn brand-btn pull-right">Import</button>
                                    <a href="index.php" class="btn brand-btn pull-right">Back</a>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>
