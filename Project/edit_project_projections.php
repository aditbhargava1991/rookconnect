<?php error_reporting(0);
include_once('../include.php');
if (isset($_POST['submit_general'])) {
    // $projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
    // $projection_service_heading = filter_var($_POST['projection_service_heading'],FILTER_SANITIZE_STRING);
    // $projection_service_price = filter_var($_POST['projection_service_price'],FILTER_SANITIZE_STRING);
    // $projection_product_heading = filter_var($_POST['projection_product_heading'],FILTER_SANITIZE_STRING);
    // $projection_product_price = filter_var($_POST['projection_product_price'],FILTER_SANITIZE_STRING);
    // $projection_task_heading = filter_var($_POST['projection_task_heading'],FILTER_SANITIZE_STRING);
    // $projection_task_price = filter_var($_POST['projection_task_price'],FILTER_SANITIZE_STRING);
    // $projection_inventory_heading = filter_var($_POST['projection_inventory_heading'],FILTER_SANITIZE_STRING);
    // $projection_inventory_price = filter_var($_POST['projection_inventory_price'],FILTER_SANITIZE_STRING);
    // $projection_admin_heading = filter_var($_POST['projection_admin_heading'],FILTER_SANITIZE_STRING);
    // $projection_admin_price = filter_var($_POST['projection_admin_price'],FILTER_SANITIZE_STRING);

    // $query_update_cal = "UPDATE `project` SET `projection_service_heading` = '$projection_service_heading', `projection_service_price` = '$projection_service_price', `projection_product_heading` = '$projection_product_heading', `projection_product_price` = '$projection_product_price', `projection_task_heading` = '$projection_task_heading', `projection_task_price` = '$projection_task_price', `projection_inventory_heading` = '$projection_inventory_heading', `projection_inventory_price` = '$projection_inventory_price', `projection_admin_heading` = '$projection_admin_heading', `projection_admin_price` = '$projection_admin_price' WHERE `projectid` = '$projectid'";
    // $result_update_cal = mysqli_query($dbc, $query_update_cal);
} ?>
<script>
function addRow(img) {
    var div = $(img).closest('.projection');
    var className = div.attr('class').split(' ')[div.attr('class').split(' ').length - 1];
    var clone = $('.projection.'+className).last().clone();
    clone.find('input').val('');
    $('.projection.'+className).last().after(clone);
    $('[data-table]').off('change',saveField).change(saveField);
}
function remRow(img) {
    var div = $(img).closest('.projection');
    var className = div.attr('class').split(' ')[div.attr('class').split(' ').length - 1];
    if($('.projection.'+className).length <= 1) {
        addRow(img);
    }
    $('.projection.'+className+' [data-table]').change();
}
</script>
<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
<?php
$projectid = $_GET['edit'];

$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project');

?>
<input type="hidden" id="projectid" name="projectid" value="<?php echo $projectid ?>" />
<h3 id="head_services">Services</h3>
<?php foreach(explode('#*#',$project['projection_service_heading']) as $i => $service_heading) { ?>
    <div class="projection service">
        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Heading:</label>
            <div class="col-sm-8">
                <input name="projection_service_heading" data-table="project" data-id="<?= $projectid ?>" data-id-field="projectid" data-concat="#*#" value="<?= $service_heading ?>" type="text" class="form-control"></p>
            </div>
        </div>
        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Price($):</label>
            <div class="col-sm-8">
                <input name="projection_service_price" data-table="project" data-id="<?= $projectid ?>" data-id-field="projectid" data-concat="#*#" value="<?= explode('#*#',$project['projection_service_price'])[$i] ?>" type="text" class="form-control"></p>
            </div>
        </div>
        <div class="form-group">
            <img src="../img/remove.png" class="pull-right cursor-hand inline-img" onclick="remRow(this);">
            <img src="../img/icons/ROOK-add-icon.png" class="pull-right cursor-hand inline-img" onclick="addRow(this);">
        </div>
        <div class="clearfix"></div><hr>
    </div>
<?php } ?>

<h3 id="head_products">Products</h3>
<?php foreach(explode('#*#',$project['projection_product_heading']) as $i => $product_heading) { ?>
    <div class="projection product">
        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Heading:</label>
            <div class="col-sm-8">
                <input name="projection_product_heading" data-table="project" data-id="<?= $projectid ?>" data-id-field="projectid" data-concat="#*#" value="<?= $product_heading ?>" type="text" class="form-control"></p>
            </div>
        </div>
        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Price($):</label>
            <div class="col-sm-8">
                <input name="projection_product_price" data-table="project" data-id="<?= $projectid ?>" data-id-field="projectid" data-concat="#*#" value="<?= explode('#*#',$project['projection_product_price'])[$i] ?>" type="text" class="form-control"></p>
            </div>
        </div>
        <div class="form-group">
            <img src="../img/remove.png" class="pull-right cursor-hand inline-img" onclick="remRow(this);">
            <img src="../img/icons/ROOK-add-icon.png" class="pull-right cursor-hand inline-img" onclick="addRow(this);">
        </div>
        <div class="clearfix"></div><hr>
    </div>
<?php } ?>

<h3 id="head_ptasks"><?= TASK_TILE ?></h3>
<?php foreach(explode('#*#',$project['projection_task_heading']) as $i => $task_heading) { ?>
    <div class="projection">
        <div class="form-group task">
            <label for="first_name" class="col-sm-4 control-label">Heading:</label>
            <div class="col-sm-8">
                <input name="projection_task_heading" data-table="project" data-id="<?= $projectid ?>" data-id-field="projectid" data-concat="#*#" value="<?= $task_heading ?>" type="text" class="form-control"></p>
            </div>
        </div>
        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Price($):</label>
            <div class="col-sm-8">
                <input name="projection_task_price" data-table="project" data-id="<?= $projectid ?>" data-id-field="projectid" data-concat="#*#" value="<?= explode('#*#',$project['projection_task_price'])[$i] ?>" type="text" class="form-control"></p>
            </div>
        </div>
        <div class="form-group">
            <img src="../img/remove.png" class="pull-right cursor-hand inline-img" onclick="remRow(this);">
            <img src="../img/icons/ROOK-add-icon.png" class="pull-right cursor-hand inline-img" onclick="addRow(this);">
        </div>
        <div class="clearfix"></div><hr>
    </div>
<?php } ?>

<h3 id="head_inventory">Inventory</h3>
<?php foreach(explode('#*#',$project['projection_inventory_heading']) as $i => $inventory_heading) { ?>
    <div class="projection inventory">
        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Heading:</label>
            <div class="col-sm-8">
                <input name="projection_inventory_heading" data-table="project" data-id="<?= $projectid ?>" data-id-field="projectid" data-concat="#*#" value="<?= $inventory_heading ?>" type="text" class="form-control"></p>
            </div>
        </div>
        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Price($):</label>
            <div class="col-sm-8">
                <input name="projection_inventory_price" data-table="project" data-id="<?= $projectid ?>" data-id-field="projectid" data-concat="#*#" value="<?= explode('#*#',$project['projection_inventory_price'])[$i] ?>" type="text" class="form-control"></p>
            </div>
        </div>
        <div class="form-group">
            <img src="../img/remove.png" class="pull-right cursor-hand inline-img" onclick="remRow(this);">
            <img src="../img/icons/ROOK-add-icon.png" class="pull-right cursor-hand inline-img" onclick="addRow(this);">
        </div>
        <div class="clearfix"></div><hr>
    </div>
<?php } ?>

<h3 id="head_admin">Admin</h3>
<?php foreach(explode('#*#',$project['projection_admin_heading']) as $i => $admin_heading) { ?>
    <div class="projection admin">
        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Heading:</label>
            <div class="col-sm-8">
                <input name="projection_admin_heading" data-table="project" data-id="<?= $projectid ?>" data-id-field="projectid" data-concat="#*#" value="<?= $admin_heading ?>" type="text" class="form-control"></p>
            </div>
        </div>
        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Price($):</label>
            <div class="col-sm-8">
                <input name="projection_admin_price" data-table="project" data-id="<?= $projectid ?>" data-id-field="projectid" data-concat="#*#" value="<?= explode('#*#',$project['projection_admin_price'])[$i] ?>" type="text" class="form-control"></p>
            </div>
        </div>
        <div class="form-group">
            <img src="../img/remove.png" class="pull-right cursor-hand inline-img" onclick="remRow(this);">
            <img src="../img/icons/ROOK-add-icon.png" class="pull-right cursor-hand inline-img" onclick="addRow(this);">
        </div>
        <div class="clearfix"></div><hr>
    </div>
<?php } ?>

<div class="form-group">
    <div class="col-sm-6">
    </div>
    <div class="col-sm-6">
        <button	type="submit" name="submit_general" value="Submit" class="btn brand-btn	pull-right">Submit</button>
    </div>
</div>

</form>
