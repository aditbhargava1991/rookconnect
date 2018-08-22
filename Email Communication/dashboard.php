<?php
/*
 * Email Communication Dashboard
 * Included In: index.php
 * Included Files: dashboard_email.php
 */
?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#mobile_tabs .panel-heading').click(loadPanel);
    });
    function loadPanel() {
        var panel = $(this).closest('.panel').find('.panel-body');
        panel.html('Loading...');
        $.ajax({
            url: panel.data('file-name'),
            method: 'POST',
            response: 'html',
            success: function(response) {
                panel.html(response);
            }
        });
    }
</script>

<div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
    <div class="panel panel-default">
        <div class="panel-heading mobile_load">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_internal">
                    Internal<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>
        <div id="collapse_internal" class="panel-collapse collapse">
            <div class="panel-body" data-file-name="dashboard_email.php?type=internal">
                Loading...
            </div>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading mobile_load">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_external">
                    External<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>
        <div id="collapse_external" class="panel-collapse collapse">
            <div class="panel-body" data-file-name="dashboard_email.php?type=external">
                Loading...
            </div>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading mobile_load">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_log">
                    Log<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>
        <div id="collapse_log" class="panel-collapse collapse">
            <div class="panel-body" data-file-name="dashboard_log.php">
                Loading...
            </div>
        </div>
    </div>
</div>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
	<ul>
        <form action="" method="POST">
			<li class="standard-sidebar-searchbox">
				<input type="text" name="search_term" class="form-control" placeholder="Search <?= $tab_title ?>" value="<?= $_POST['search_term'] ?>">
	            <input type="submit" name="search" value="Search" class="btn brand-btn" style="display:none;" />
	        </li>
		</form>
        <?php if ( check_subtab_persmission($dbc, 'email_communication', ROLE, 'internal') === true ) { ?>
            <li class="sidebar-higher-level <?= $type == 'internal' ? 'active' : '' ?>"><a href="?type=internal">Internal</a>
        <?php } ?>
        <?php if ( check_subtab_persmission($dbc, 'email_communication', ROLE, 'external') === true ) { ?>
            <li class="sidebar-higher-level <?= $type == 'external' ? 'active' : '' ?>"><a href="?type=external">External</a>
        <?php } ?>
        <?php if ( check_subtab_persmission($dbc, 'email_communication', ROLE, 'log') === TRUE ) { ?>
            <li class="sidebar-higher-level <?= $type == 'log' ? 'active' : '' ?>"><a href="?type=log">Log</a>
        <?php } ?>
	</ul>
</div>

<div class="scale-to-fill has-main-screen hide-titles-mob no-overflow">
	<div class="main-screen standard-body form-horizontal">
		<div class="standard-body-title">
			<h3><?= $tab_title ?></h3>
		</div>

		<div class="standard-body-content" style="padding:1em;"><?php
            if($type == 'log') {
				include('dashboard_log.php');
			} else {
                include('dashboard_email.php');
			} ?>
		</div>
	</div>
</div>