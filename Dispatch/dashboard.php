<?php include_once ('../include.php');
checkAuthorised('dispatch');
?>
<script type="text/javascript" src="../Dispatch/dashboard.js"></script>

<div class="scale-to-fill has-main-screen" style="padding: 0;">
    <div class="main-screen standard-body form-horizontal">

        <div class="standard-body-content">
            <div class="dispatch-body">
                <div class="dispatch-equipment-summary">
                </div>
                <div class="dashboard-equipment-buttons-group" style="padding: 1em;">
                    <h4 style="margin: 0;">Equipment</h4>
                    <div class="dispatch-equipment-buttons">
                    </div>
                    <label class="form-checkbox"><input type="checkbox" onclick="select_all_buttons(this);" checked> Select All</label>
                </div>
                <div class="double-scroller"><div></div></div>
                <div class="dispatch-equipment-list"></div>
            </div>
        </div>
    </div>
</div>