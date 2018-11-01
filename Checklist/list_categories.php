<script>
function addSubtab(button) {
        var block = $(button).closest('.form-group');
        var clone = block.clone();
        block.after(clone);
        clone.find('input').val('');
        clone.find('input').attr('name', 'subtabnew[]');
    }
    function removeSubtab(button) {
        $(button).closest('.form-group').remove();
    }
</script>

<div class="standard-body-title">
    <h3>Tabs</h3>
</div>

<?php
    if (isset($_POST['submit'])) {
        foreach($_POST as $key => $value) {
            if (strpos($key, 'subtab_') === 0) {
                $subtab_id = explode('_', $key)[1];
                $subtab_name = filter_var($value, FILTER_SANITIZE_STRING);
                mysqli_query($dbc, "UPDATE checklist_subtab SET name='$subtab_name' WHERE subtabid='$subtab_id'");
            } elseif (strpos($key, 'subtabnew') === 0) {
                foreach ( $_POST['subtabnew'] as $subtabnew ) {
                    $subtab_name = filter_var($subtabnew, FILTER_SANITIZE_STRING);
                    mysqli_query($dbc, "INSERT INTO checklist_subtab (name, created_by) VALUES ('$subtab_name', '{$_SESSION['contactid']}')");
                }
            }
        }
        
    }
?>

<form method="post" action="" class="padded">
    <div class="form-group">
        <label class="col-sm-4 control-label">Add Tabs:</label>
        <div class="col-sm-8 tabs"><?php
            $subtabs = mysqli_query($dbc, "SELECT subtabid, name FROM checklist_subtab WHERE deleted=0 AND (created_by = '{$_SESSION['contactid']}' OR shared LIKE '%,{$_SESSION['contactid']},%')");
            while ( $row = mysqli_fetch_assoc($subtabs) ) { ?>
                <div class="form-group">
                    <div class="col-sm-10">
                        <input name="subtab_<?= $row['subtabid'] ?>" type="text" value="<?= $row['name'] ?>" class="form-control" />
                    </div>
                    <div class="col-sm-2">
                        <img src="../img/remove.png" class="pull-right inline-img cursor-hand" onclick="removeSubtab(this);" />
                        <img src="../img/icons/ROOK-add-icon.png" class="pull-right inline-img cursor-hand" onclick="addSubtab(this);" />
                    </div>
                </div><?php
            } ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group double-gap-top">
        <input type="submit" name="submit" value="Submit" class="btn brand-btn pull-right" />
        <a href="../Checklist/checklist.php?subtabid=categories&categories=all" class="btn brand-btn pull-right">Cancel</a>
    </div>
</form>