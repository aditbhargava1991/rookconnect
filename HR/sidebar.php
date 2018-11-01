<?php if(!IFRAME_PAGE) { ?>
    <script>
    $(document).ready(function() {
        $('.search_list').change(function() {
            window.location.href = '?tile_name=<?= $_GET['tile_name'] ?>&tab=search&key='+encodeURIComponent(this.value);
        });
    });
    </script>
    <div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible <?= $tile == 'hr' ? '' : 'collapsed' ?>">
        <ul id="desktop_accordions" class="panel-group">
            <li class="standard-sidebar-searchbox"><input class="form-control search_list" placeholder="Search HR" type="text" value="<?= $_GET['key'] ?>"></li>
            <?php if(get_config($dbc, 'hr_include_profile') == 1) {
                include('../Staff/field_list.php');
                $contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` LEFT JOIN `contacts_upload` ON `contacts`.`contactid`=`contacts_upload`.`contactid` WHERE `contacts`.`contactid`='".$_SESSION['contactid']."'"));
                $field_config_contacts = mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab` = 'Staff' AND `subtab` != 'hidden'");
                $completed_fields = 0;
                $all_fields = 0;
                $contact_fields = [];
                while($field_config_contact = mysqli_fetch_assoc($field_config_contacts)) {
                    $contact_fields[] = $field_config_contact['contacts'];
                }
                $contact_fields = ','.implode(',', $contact_fields).',';
                $contact_tabs = ','.get_config($dbc, 'staff_field_subtabs').',';
                foreach($field_list as $staff_tab => $tab_list) {
                    if(strpos($contact_tabs, ','.$staff_tab.',') !== FALSE) {
                        foreach($tab_list as $staff_subtab => $subtab_list) {
                            foreach($subtab_list as $field_key => $field_value) {
                                $field_key = explode('#',$field_key)[0];
                                if(strpos($contact_fields, ','.$field_value.',') !== FALSE && isset($contact[$field_key])) {
                                    $all_fields++;
                                    if(!empty(str_replace(['0000-00-00','0'],'',$contact[$field_key]))) {
                                        $completed_fields++;
                                    }
                                }
                            }
                        }
                    }
                }
                $percent_completed = round($completed_fields/$all_fields*100); ?>
                <a href="<?= WEBSITE_URL ?>/Profile/my_profile.php?edit_contact=true"><li class="">Profile (<?= $percent_completed ?>% Completed)</li></a>
            <?php } ?>
            <?php if(count($hr_summary) > 0) { ?>
                <a href="?tile_name=<?= $tile ?>&tab=summary"><li class="<?= 'summary' == $tab ? 'active blue' : '' ?>">Summary</li></a>
            <?php } ?>
            <?php foreach($categories as $cat_id => $label) {
                if($tab == $cat_id) {
                    $tab_cat = $label;
                }
                if(($tile == 'hr' || $tile == $cat_id) && check_subtab_persmission($dbc, 'hr', ROLE, $label)) {
                    $count = $dbc->query("SELECT COUNT(*) `rows` FROM (SELECT `hrid` `id` FROM `hr` WHERE `category`='$label' AND `deleted`=0 UNION SELECT `manualtypeid` `id` FROM `manuals` WHERE `category`='$label' AND `deleted`=0) `hr`")->fetch_assoc()['rows']; ?>
                    <a href="?tile_name=<?= $tile ?>&tab=<?= $cat_id ?>"><li class="<?= $cat_id == $tab ? 'active blue' : '' ?>"><?= $label ?><span class="pull-right"><?= $count > 0 ? $count : '' ?></span></li></a>
                <?php }
            } ?>
            <?php $pr_fields = ','.get_config($dbc, 'performance_review_fields').',';
            if(strpos($pr_fields, ',Enable Performance Reviews,') !== FALSE) {
                if(tile_visible($dbc, 'preformance_review')) { ?>
                    <li class="sidebar-higher-level highest-level <?= $_GET['performance_review'] == 'list' ? 'active blue' : '' ?>"><a class="cursor-hand <?= $_GET['performance_review'] == 'list' ? '' : 'collapsed' ?>" data-toggle="collapse" data-target="#list_pr" data-parent="#desktop_accordions">Performance Reviews<span class="arrow"></span></a></li>
                    <ul id="list_pr" class="collapse <?= $_GET['performance_review'] == 'list' ? 'in' : '' ?>">
                        <?php $pr_tab = $_GET['pr_tab'];
                        $pr_positions = explode(',', get_config($dbc, 'performance_review_positions'));
                        if(!empty(get_config($dbc, 'performance_review_positions'))) {
                            foreach ($pr_positions as $pr_position) {
                                if(check_subtab_persmission($dbc, 'preformance_review', ROLE, $pr_position)) {
                                    $count = $dbc->query("SELECT COUNT(*) as numrows FROM `performance_review` WHERE `deleted` = 0 AND `position` = '$pr_position'")->fetch_assoc()['numrows']; ?>
                                    <a href="?performance_review=list&pr_tab=<?= $pr_position ?>"><li class="sidebar-lower-level <?= $pr_tab == $pr_position ? 'active' : '' ?>"><?= $pr_position ?><span class="pull-right"><?= $count ?></span></li></a>
                                <?php }
                            }
                        } ?>
                        <a href="?performance_review=list"><li class="<?= $_GET['performance_review'] == 'list' && empty($pr_tab) ? 'active' : '' ?>">View All</li></a>
                    </ul>
                <?php }
                if(check_subtab_persmission($dbc, 'hr', ROLE, 'reporting')) { ?>
                    <a href='?reports=view&tile_name=<?= $tile ?>'><li class='<?= ($_GET['reports'] == 'view' ? "active blue" : "") ?>'>Reporting</li></a>
                <?php }
                if(check_subtab_persmission($dbc, 'hr', ROLE, 'request_an_update') && get_config($dbc, 'hr_include_request_update') == 1) { ?>
                    <a href='?request_update=1&tile_name=<?= $tile ?>'><li class='<?= ($_GET['request_update'] == 1 ? "active blue" : "") ?>'>Request an Update</li></a>
                <?php }
            } ?>
        </ul>
    </div>
<?php } ?>