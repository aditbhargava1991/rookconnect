<?php /* Field Configuration for Expenses */
include ('../include.php');
checkAuthorised('match');

if (isset($_POST['submit'])) {
	$match_exclude_security = filter_var(implode('#*#', $_POST['match_exclude_security']),FILTER_SANITIZE_STRING);
	set_config($dbc, 'match_exclude_security', $match_exclude_security);
}
?>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
    <ul>
        <a href="?settings=match"><li class="active blue">Match Settings</li></a>
    </ul>
</div>

<div class="scale-to-fill has-main-screen">
    <div class="main-screen standard-body form-horizontal">
        <div class="standard-body-title">
            <h3>Match Settings</h3>
        </div>
        <div class="standard-body-content" style="padding: 1em;">
		    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	        	<div class="form-group">
	        		<label class="col-sm-4 control-label">Exclude Hidden Contacts for Security Levels:</label>
	        		<div class="col-sm-8">
	        			<select name="match_exclude_security[]" multiple class="chosen-select-deselect form-control">
	        				<?php $on_security = get_security_levels($dbc);
	        				$match_exclude_security = explode('#*#',get_config($dbc, 'match_exclude_security'));
	        				foreach($on_security as $security_name => $value) {
	        					echo '<option value="'.$value.'" '.(in_array($value, $match_exclude_security) ? 'selected' : '').'>'.$security_name.'</option>';
	        				} ?>
	        			</select>
	        		</div>
	        	</div>
	        	<div class="form-group pull-right">
	        		<a href="?" class="btn brand-btn">Back</a>
	        		<button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
	        	</div>
	        </button>
        </div>
    </div>
</div>

<?php include('../footer.php'); ?>