<!-- Daysheet My Tags -->
<div class="col-xs-12">
    <div class="weekly-div" style="overflow-y: hidden;">
        <div class="clearfix"></div>
        <?php /* Pagination Counting */
        $rowsPerPage = 25;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        $query = "SELECT COUNT(*) as `numrows` FROM `contacts_tagging` WHERE `contactid` = '$contactid' AND `deleted` = 0 AND `last_updated_date` BETWEEN '0000-00-00' AND '9999-12-31' ORDER BY `last_updated_date` DESC";

        if(mysqli_fetch_assoc(mysqli_query($dbc, $query))['numrows'] > 0) {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            echo daysheet_get_tags($dbc, $_SESSION['contactid'], '0000-00-00', '9999-12-31', $offset, $rowsPerPage);
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        } else {
            echo '<h2>No Tags Found.</h2>';
        }

        ?>
    </div>
</div>