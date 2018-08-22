<?php include_once('../include.php');
checkAuthorised('security');
ob_clean(); ?>
<thead>
    <tr class="hidden-sm hidden-xs">
        <th>Name</th>
        <th>User Name</th>
        <th>Email Address</th>
        <th>Password Status</th>
        <th>Function</th>
    </tr>
</thead>
<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name`, `email_address`, `user_name`, `password`, `password_date`, `password_update` FROM `contacts` WHERE IFNULL(`user_name`,'') != '' AND `deleted`=0 AND `status`>0")) as $user) { ?>
	<tr>
		<td data-title="Name"><?= $user['name'].($user['name'] != '' && $user['first_name'].$user['last_name'] != '' ? ': ' : '').$user['first_name'].' '.$user['last_name'] ?></td>
		<td data-title="User Name"><?= $user['user_name'] ?></td>
		<td data-title="Email Address"><?= $user['email_address'] ?></td>
		<td data-title="Password Status">
			Password Last Updated: <?= $user['password_date'] == '' ? 'Unknown' : $user['password_date'] ?>
			<?= $user['password_update'] > 0 ? '<br />Password Update Required at Next Login' : '' ?>
		</td>
		<td data-title="Function">
			<button class="btn brand-btn" onclick="generate_password(<?= $user['contactid'] ?>);">Generate Password</button>
			<button class="btn brand-btn" onclick="force_reset(<?= $user['contactid'] ?>);">Force Change</button>
		</td>
	</tr>
<?php } ?>