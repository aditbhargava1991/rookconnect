<script>
$(document).ready(function() {
	load_users();
});
function generate_password(userid) {
	$('.alert-success').show().text('Generating new password...');
	$.ajax({
		url: 'security_ajax_all.php?fill=password_generate&userid='+userid,
		success: function(response) {
			$('.alert-success').show().text(response);
			setTimeout(function() { $('.alert-success').fadeOut(); },10000);
			load_users();
		}
	});
}
function force_reset(userid) {
	$('.alert-success').show().text('Requiring passwords to be reset...');
	$.ajax({
		url: 'security_ajax_all.php?fill=password_require_update&userid='+userid,
		success: function(response) {
			$('.alert-success').show().text(response);
			setTimeout(function() { $('.alert-success').fadeOut(); },5000);
			load_users();
		}
	});
}
function load_users() {
	$.ajax({
		url: 'user_password_list.php',
		success: function(response) {
			$('.table').html(response);
		}
	});
}
</script><?php

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='security_password_reset'"));
$note = $notes['note'];
    
if ( !empty($note) ) { ?>
    <div class="notice double-gap-bottom popover-examples">
        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11">
            <span class="notice-name">NOTE:</span>
            <?= $note; ?>
        </div>
        <div class="clearfix"></div>
    </div><?php
} ?>

<div class="col-sm-12 text-center pad-bottom">
	<h2>All Users</h2>
	<button class="btn brand-btn" onclick="if(confirm('Are you sure you want to send this?')) { force_reset(); }">Force Password Change</button>
	<button class="btn brand-btn" onclick="if(confirm('Are you sure you want to send this?')) { generate_password(); }">Generate and Email New Passwords</button>
	<button class="btn brand-btn" onclick="if(confirm('Are you sure you want to send this?')) { force_reset(); generate_password(); }">Generate and Email New Passwords and Force Change</button>
</div>
<div class="clearfix"></div>
<div class="alert alert-success text-centre-25" style="display:none;"></div>
<h2>User Password Reset</h2>
<div id="no-more-tables">
	<table class="table table-bordered table-striped">
		<thead>
            <tr class="hidden-sm hidden-xs">
                <th>Name</th>
                <th>User Name</th>
                <th>Email Address</th>
                <th>Password Status</th>
                <th>Function</th>
            </tr>
        </thead>
		<tr>
			<td colspan="5"><em>Loading Users...</em></td>
		</tr>
	</table>
</div>