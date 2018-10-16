<?php
include('../include.php');
?>
<script type="text/javascript">
$(document).on('change','#pay_settings select',function() { saveStatPay(this); });
function saveStatPay(sel) {
    var row = $(sel).closest('.stat_row');
    var staffid = $(row).find('[name="staffid"]').val();
    var stat_pay = $(row).find('[name="stat_pay"]').val();
    var vaca_pay = $(row).find('[name="vaca_pay"]').val();
    $.ajax({
        url: '../Timesheet/time_cards_ajax.php?action=set_stat_pay',
        method: 'POST',
        data: { staffid: staffid, stat_pay: stat_pay, vaca_pay: vaca_pay },
        success: function(response) {
        }
    });
}
</script>
</head>
<body>
<?php
include_once ('../navigation.php');
checkAuthorised('timesheet');
include 'config.php';

$value = $config['settings']['Choose Fields for Holidays Dashboard'];

?>
<div class="container triple-pad-bottom">
    <div class="row">
        <div class="col-md-12">

            <h1 class="">Stat Holidays Dashboard
            <?php
            if(config_visible_function_custom($dbc)) {
                echo '<a href="field_config.php?from_url=holidays.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
            }
            ?>
            </h1>

            <form id="form1" name="form1" method="get" enctype="multipart/form-data" class="form-horizontal" role="form">

            <?php echo get_tabs('Holidays', 'Stat Pay Calculation', array('db' => $dbc, 'field' => $value['config_field'])); ?>
            <br><br>

            <div id="no-more-tables">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="hidden-xs">
                            <th>Stat Pay</th>
                            <th>Calculation</th>
                        </tr>
                    </thead>
                    <tr>
                        <td data-title="Stat Pay">Alberta Standard 4%</td>
                        <td data-title="Calculation">
                            <ol>
                                <li>Go back 28 days from the date of the Stat Holiday.</li>
                                <li>Take the sum of the hours from the last 28 days.</li>
                                <li>Take the sum of Step 2 and multiply by 4% to get <b>Vacation Hours</b>.</li>
                                <li>Take the sum of Step 3 and the sum of Step 2 multiplied by 5% to get <b>Stat Hours</b>.</li>
                            </ol>
                        </td>
                    </tr>
                    <tr>
                        <td data-title="Stat Pay">Alberta Standard 6%</td>
                        <td data-title="Calculation">
                            <ol>
                                <li>Go back 28 days from the date of the Stat Holiday.</li>
                                <li>Take the sum of the hours from the last 28 days.</li>
                                <li>Take the sum of Step 2 and multiply by 6% to get <b>Vacation Hours</b>.</li>
                                <li>Take the sum of Step 3 and the sum of Step 2 multiplied by 5% to get <b>Stat Hours</b>.</li>
                            </ol>
                        </td>
                    </tr>

                    <tr>
                        <td data-title="Stat Pay">Alberta Standard Salary</td>
                        <td data-title="Calculation">
                            <ol>
                                <li># of Hours Per Week" / "# of Days Per Week</li>
                            </ol>
                        </td>
                    </tr>

                </table>

                <?php
                // Pagination Config
                $rowsPerPage = 25;
                $pageNum = 1;

                if(isset($_GET['page'])) {
                    $pageNum = $_GET['page'];
                }
                $offset = ($pageNum - 1) * $rowsPerPage;

                $sql = "SELECT `contactid`, `first_name`, `last_name`, `stat_pay`, `vaca_pay` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND CONCAT(IFNULL(`first_name`,''),IFNULL(`last_name`,'')) != ''";
                $result = mysqli_query($dbc, $sql);
                $sql_count = "SELECT COUNT(*) `numrows` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND CONCAT(IFNULL(`first_name`,''),IFNULL(`last_name`,'')) != ''";

                $contact_list = [];
                $contact_sort = [];
                $contact_list = array_merge($contact_list, mysqli_fetch_all($result, MYSQLI_ASSOC));
                $contact_sort = array_splice(sort_contacts_array($contact_list), $offset, $rowsPerPage);

                // Display pagination
                echo display_pagination($dbc, $sql_count, $pageNum, $rowsPerPage); ?>

                <table id="pay_settings" class="table table-bordered table-striped">
                    <thead>
                        <tr class="hidden-xs hidden-sm">
                            <th>Staff</th>
                            <th>Vacation Pay Settings</th>
                            <th>Stat Pay Setting</th>
                        </tr>
                    </thead>
                    <?php foreach($contact_sort as $id) {
                        $row = $contact_list[array_search($id, array_column($contact_list,'contactid'))]; ?>
                        <tr class="stat_row">
                            <input type="hidden" name="staffid" value="<?= $row['contactid'] ?>">
                            <td data-title="Staff"><?= decryptIt($row['first_name']).' '.decryptIt($row['last_name']) ?></td>
                            <td data-title="Vacation Pay Calculation">
                                <select name="vaca_pay" class="chosen-select-deselect">
                                    <option <?= empty($row['vaca_pay']) ? 'selected' : '' ?>>No Pay</option>
                                    <option value="Alberta Standard 4%" <?= $row['vaca_pay'] == 'Alberta Standard 4%' ? 'selected' : '' ?>>Alberta Standard 4%</option>
                                    <option value="Alberta Standard 6%" <?= $row['vaca_pay'] == 'Alberta Standard 6%' ? 'selected' : '' ?>>Alberta Standard 6%</option>
                                    <option value="Alberta Standard Salary" <?= $row['vaca_pay'] == 'Alberta Standard Salary' ? 'selected' : '' ?>>Alberta Standard Salary</option>
                                </select>
                            </td>
                            <td data-title="Stat Pay Calculation">
                                <select name="stat_pay" class="chosen-select-deselect">
                                    <option <?= empty($row['stat_pay']) ? 'selected' : '' ?>>No Pay</option>
                                    <option value="Alberta Standard 4%" <?= $row['stat_pay'] == 'Alberta Standard 4%' ? 'selected' : '' ?>>Alberta Standard 4%</option>
                                    <option value="Alberta Standard 6%" <?= $row['stat_pay'] == 'Alberta Standard 6%' ? 'selected' : '' ?>>Alberta Standard 6%</option>
                                    <option value="Alberta Standard Salary" <?= $row['stat_pay'] == 'Alberta Standard Salary' ? 'selected' : '' ?>>Alberta Standard Salary</option>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                </table>

                <?php // Display pagination
                echo display_pagination($dbc, $sql_count, $pageNum, $rowsPerPage); ?>

            </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>