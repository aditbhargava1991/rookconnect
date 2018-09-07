<?php
	/*
	 * Software Guide Tile AJAX Calls
	 */
    
     include_once('../include.php');
    include ('../database_connection_htg.php');
    ob_clean();
    
    /* Load panel on mobile. Called from guide_index.php */
    if ( $_GET['fill'] == 'load_panel' ) {
        $guide = preg_replace('/[^0-9]/', '', $_GET['guide']);
        $html = '';
        
        $result = mysqli_query($dbc_htg, "SELECT `description` FROM `how_to_guide` WHERE `guideid`='$guide' AND `deleted`=0");
        if ( $result->num_rows > 0 ) {
            while ( $row=mysqli_fetch_assoc($result) ) {
                $html .= html_entity_decode($row['description']);
            }
        }
        
        $local_guide = mysqli_query($dbc, "SELECT `additional_guide` FROM `local_software_guide` WHERE `guideid`='$guide'");
        if ( $local_guide->num_rows > 0 ) {
            while ( $row=mysqli_fetch_assoc($local_guide) ) {
                $html .= html_entity_decode($row['additional_guide']);
            }
        }
        
        if ( $result->num_rows == 0 && $local_guide->num_rows == 0 ) {
            $html = 'Requested software guide is not available at this time. Please check back later for updates.';
        }
        
        echo $html;
    }
    
    /* Load config panel on mobile. Called from guide_index.php */
    if ( $_GET['fill'] == 'load_panel_config' ) {
        $guide = preg_replace('/[^0-9]/', '', $_GET['guide']);
        $additional_guide = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `additional_guide` FROM `local_software_guide` WHERE `guideid`='$guide' AND `deleted`=0"))['additional_guide'];
        
        $html = '
            <form method="post" action="">
                <input type="hidden" name="guideid" value="'. $guide .'" />
                <div class="row">
                    <div class="col-sm-12 gap-top"><label>Additional Software Guide:</label></div>
                    <div class="col-sm-12"><textarea name="additional_guide" style="height:150px; width:100%;">'. html_entity_decode($additional_guide) .'</textarea></div>
                    <div class="col-sm-12 gap-top">
                        <div class="row">
                            <div class="col-xs-6"><a class="cursor-hand"><img src="../img/icons/ROOK-trash-icon.png" width="30" alt="Delete" /></a></div>
                            <div class="col-xs-6 text-right"><input type="submit" name="submit_guide" value="Submit" class="btn brand-btn" /></div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </form>';
        
        echo $html;
    }
    
    if ( $_GET['fill'] == 'delete_additional_guide' ) {
        $guideid = preg_replace('/[^0-9]/', '', $_GET['guideid']);
        mysqli_query($dbc, "DELETE FROM `local_software_guide` WHERE `guideid`='$guideid'");
    }
?>