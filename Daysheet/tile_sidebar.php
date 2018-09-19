<!-- Tile Sidebar -->
<?php if($_GET['settings'] == 'config') {
    $settings_type = 'user';
    if(!empty($_GET['settings_type'])) {
        $settings_type = $_GET['settings_type'];
    } ?>
    <ul class="sidebar">
        <a href="?settings=config&settings_type=user"><li <?= ($settings_type == 'user' ? 'class="active"' : '') ?>>User Settings</li></a>
        <?php if (config_visible_function($dbc, 'profile') == 1) { ?><a href="?settings=config&settings_type=software"><li <?= ($settings_type == 'software' ? 'class="active"' : '') ?>>Software Default Settings</li></a><?php } else { ?><li <?= ($settings_type == 'software' ? 'class="active"' : '') ?>>Software Default Settings</li><?php } ?>
    </ul>
<?php } else {
    $user_settings = get_user_settings();
    $daysheet_button_config = $user_settings['daysheet_button_config'];
    if(empty($daysheet_button_config)) {
        $daysheet_button_config = explode(',', get_config($dbc, 'daysheet_button_config'));
    } else {
        $daysheet_button_config = explode(',', $daysheet_button_config);
    } ?>
    <ul class="sidebar">
        <a href="?tab=daysheet"><li class="<?= $_GET['tab'] == 'daysheet' ? 'active' : '' ?>">Day Sheet</li></a>
        <a href="?tab=journals"><li class="<?= $_GET['tab'] == 'journals' ? 'active' : '' ?>">My Journal</li></a>
        <?php if (in_array('My Notes', $daysheet_button_config)) { ?>
            <a href="?tab=notes&daily_date=<?= date('Y-m-d') ?>&side_content=my_notes"><li class="<?= $_GET['tab'] == 'notes' ? 'active' : '' ?>">My Notes</li></a>
        <?php } ?>
        <?php if (in_array('Scrum Notes', $daysheet_button_config)) { ?>
            <a href="?tab=scrum_notes&daily_date=<?= date('Y-m-d') ?>&side_content=my_scrum_notes"><li class="<?= $_GET['tab'] == 'scrum_notes' ? 'active' : '' ?>">Scrum Notes</li></a>
        <?php } ?>
        <a href="?tab=alerts&daily_date=<?= date('Y-m-d') ?>&side_content=past_due"><li class="<?= $_GET['tab'] == 'alerts' ? 'active' : '' ?>">My Alerts</li></a>
        <?php if (in_array('My Projects', $daysheet_button_config)) { ?>
            <a href="?tab=projects&daily_date=<?= date('Y-m-d') ?>&side_content=my_projects"><li class="<?= $_GET['tab'] == 'projects' ? 'active' : '' ?>">My <?= PROJECT_TILE ?></li></a>
        <?php } ?>
        <?php if (in_array('My Tickets', $daysheet_button_config)) { ?>
            <a href="?tab=tickets&daily_date=<?= date('Y-m-d') ?>&side_content=my_tickets&date_display=daily"><li class="<?= $_GET['tab'] == 'tickets' ? 'active' : '' ?>">My <?= TICKET_TILE ?></li></a>
        <?php } ?>
        <?php if (in_array('My Tasks', $daysheet_button_config)) { ?>
            <a href="?tab=tasks&daily_date=<?= date('Y-m-d') ?>&side_content=my_tasks&date_display=daily"><li class="<?= $_GET['tab'] == 'tasks' ? 'active' : '' ?>">My <?= TASK_TILE ?></li></a>
        <?php } ?>
        <?php if (in_array('My Checklists', $daysheet_button_config)) { ?>
            <a href="?tab=checklists&daily_date=<?= date('Y-m-d') ?>&side_content=my_checklists&date_display=daily"><li class="<?= $_GET['tab'] == 'checklists' ? 'active' : '' ?>">My Checklists</li></a>
        <?php } ?>
        <?php if (in_array('My Communications', $daysheet_button_config)) { ?>
            <a href="?tab=communications&daily_date=<?= date('Y-m-d') ?>&side_content=my_communications&date_display=daily"><li class="<?= $_GET['tab'] == 'communications' ? 'active' : '' ?>">My Communications</li></a>
        <?php } ?>
        <?php if (in_array('My Sales', $daysheet_button_config)) { ?>
            <a href="?tab=sales&daily_date=<?= date('Y-m-d') ?>&side_content=my_sales&date_display=daily"><li class="<?= $_GET['tab'] == 'sales' ? 'active' : '' ?>">My <?= SALES_TILE ?></li></a>
        <?php } ?>
        <?php if (in_array('My Support', $daysheet_button_config)) { ?>
            <a href="?tab=support&daily_date=<?= date('Y-m-d') ?>&side_content=my_support&date_display=daily"><li class="<?= $_GET['tab'] == 'checklists' ? 'active' : '' ?>">My Support Requests</li></a>
        <?php } ?>
        <?php if (in_array('My Shifts', $daysheet_button_config)) { ?>
            <a href="?tab=shifts&daily_date=<?= date('Y-m-d') ?>&side_content=my_shifts&date_display=daily"><li class="<?= $_GET['tab'] == 'shifts' ? 'active' : '' ?>">My Shifts</li></a>
        <?php } ?>
        <?php if (in_array('My Time Sheets', $daysheet_button_config)) { ?>
            <a href="?tab=timesheets&daily_date=<?= date('Y-m-d') ?>&side_content=my_timesheets&date_display=daily"><li class="<?= $_GET['tab'] == 'timesheets' || ($_GET['side_content'] == 'my_timesheets') ? 'active' : '' ?>">My Time Sheet</li></a>

        <?php } ?>
        <?php if (in_array('My Tags', $daysheet_button_config)) { ?>
            <a href="?tab=tags&daily_date=<?= date('Y-m-d') ?>&side_content=my_tags&date_display=daily"><li class="<?= $_GET['tab'] == 'tags' ? 'active' : '' ?>">My Tags</li></a>

        <?php } ?>
        <?php if (in_array('Attached Contact Forms', $daysheet_button_config)) {
            $match_contacts = [];
            $match_query = mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE CONCAT(',',`staff_contact`,',') LIKE '%,".$_SESSION['contactid'].",%' AND `deleted` = 0 AND `match_date` <= '".date('Y-m-d')."' AND `end_date` >= '".date('Y-m-d')."' AND `status` = 'Active'");
            while($match_result = mysqli_fetch_assoc($match_query)) {
                $match_contacts = array_filter(array_merge($match_contacts, explode(',',$match_result['support_contact'])));
            }
            foreach($match_contacts as $match_contact) {
                $contact_cat = get_contact($dbc, $match_contact, 'category');
                $contact_forms = mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',',`assigned_tile`,',') LIKE '%,attach_contact,%' AND `deleted` = 0 AND (CONCAT(',',`attached_contacts`,',') LIKE '%,$match_contact,%' OR (CONCAT(',',`attached_contacts`,',') LIKE '%,ALL_CONTACTS%,' AND (CONCAT(',',`attached_contact_categories`,',') LIKE '%,$contact_cat,%' OR IFNULL(`attached_contact_categories`,'') = ''))) AND `is_template` = 0 ORDER BY `name`");
                while($contact_form = mysqli_fetch_assoc($contact_forms)) { ?>
                    <a href="?tab=contact_form&side_content=contact_form&attached_contactid=<?= $match_contact ?>&form_id=<?= $contact_form['form_id'] ?>"><li class="<?= $_GET['tab'] == 'contact_form' && $_GET['form_id'] == $contact_form['form_id'] && $_GET['attached_contactid'] == $match_contact ? 'active' : '' ?>"><?= $contact_form['name'] ?> - <?= !empty(get_client($dbc, $match_contact)) ? get_client($dbc, $match_contact) : get_contact($dbc, $match_contact) ?></li></a>
                <?php }
            }
        } ?>
    </ul>
<?php } ?>