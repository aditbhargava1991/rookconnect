<?php
/* Display search results
 * Included From: checklist.php
 */
 
$search_term = filter_var($_GET['search'], FILTER_SANITIZE_STRING);
?>

<div class="standard-body-title">
    <h3>Search Results <?= !empty($search_term) ? 'for '.$search_term : '' ?></h3>
</div>

<?php
    $query = '';
    if ( !empty($search_term) ) {
        $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE checklist_name LIKE '%$search_term%' AND deleted=0 AND (created_by = '{$_SESSION['contactid']}' OR assign_staff LIKE ',%{$_SESSION['contactid']}%,')");
    }

    if ($query->num_rows > 0) {
        echo '<div class="standard-dashboard-body-content">';
            while ( $row = mysqli_fetch_assoc($query) ) {
                echo '<div class="dashboard-item"><h4><a href="checklist.php?view='.$row['checklistid'].'">'. $row['checklist_name'] .'</a></h4></div>';
            }
        echo '</div>';
    }
?>