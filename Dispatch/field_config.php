<?php include_once ('../include.php');
checkAuthorised('dispatch');
if(empty($_GET['settings'])) {
    $_GET['settings'] = 'tile';
}
switch($_GET['settings']) {
    case 'tile':
        $field_title = 'Tile Settings';
        break;
    case 'fields':
        $field_title = TICKET_NOUN.' Fields';
        break;
    case 'status_colors':
        $field_title = 'Status Colors';
        break;
} ?>

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
                <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_dashboard">
                    Tile Settings<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_dashboard" class="panel-collapse collapse">
            <div class="panel-body" data-file-name="field_config_tile.php">
                Loading...
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading mobile_load">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_fields">
                    <?= TICKET_NOUN ?> Fields<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_fields" class="panel-collapse collapse">
            <div class="panel-body" data-file-name="field_config_fields.php">
                Loading...
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading mobile_load">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_summary_blocks">
                    Summary Blocks<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_summary_blocks" class="panel-collapse collapse">
            <div class="panel-body" data-file-name="field_config_tile.php">
                Loading...
            </div>
        </div>
    </div>


</div>

<div class="sidebar standard-collapsible tile-sidebar hide-titles-mob">
    <ul>
        <a href="?"><li>Back to Dashboard</li></a>
        <a href="?settings=tile"><li <?= $_GET['settings'] == 'tile' ? 'class="active"' : '' ?>>Tile Settings</li></a>
        <a href="?settings=fields"><li <?= $_GET['settings'] == 'fields' ? 'class="active"' : '' ?>><?= TICKET_NOUN ?> Fields</li></a>
        <a href="?settings=status_colors"><li <?= $_GET['settings'] == 'status_colors' ? 'class="active"' : '' ?>>Status Colors</li></a>
        <a href="?settings=summary_blocks"><li <?= $_GET['settings'] == 'summary_blocks' ? 'class="active"' : '' ?>>Summary Blocks</li></a>
    </ul>
</div>

<div class="scale-to-fill has-main-screen hide-titles-mob">
    <div class="main-screen standard-body form-horizontal">
        <div class="standard-body-title">
            <h3><?= $field_title ?></h3>
        </div>
        <div class="standard-body-content" style="padding: 1em;">
            <?php if($_GET['settings'] == 'summary_blocks') {
                include('field_config_summary_blocks.php');
            } else if($_GET['settings'] == 'status_colors') {
                include('field_config_status_colors.php');
            } else if($_GET['settings'] == 'fields') {
                include('field_config_fields.php');
            } else {
                include('field_config_tile.php');
            } ?>
        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->