<script>
$(document).ready(function() {
	//Expenses
    $('#deletepackage_0').hide();
    $('#add_row_exp').on( 'click', function () {
        var clone = $('.additional_exp').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_exp");
        $('#add_here_new_exp').append(clone);
        return false;
    });
});
function countExpense() {
    var sum_fee = 0;
    $('[name="expprojectprice[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="expense_total"]').val(round2Fixed(sum_fee));
    $('[name="expense_summary"]').val(round2Fixed(sum_fee));

    var expense_budget = $('[name="expense_budget"]').val();
    if(expense_budget >= sum_fee) {
        $('[name="expense_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="expense_total"]').css("background-color", "#ff9999"); // Green
    }
}
</script>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <label class="col-sm-2 text-center">Type of Expense</label>
            <label class="col-sm-2 text-center">Category of Expense</label>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</label>
        </div>

        <div class="additional_exp clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix">
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Type of Expense:</label>
                    <select data-placeholder="Choose a Type..." name="expensetype[]" class="chosen-select-deselect1 form-control equipmentid" width="380">
                        <option value=''></option>
                        <option value='Clients'>Clients</option>
                        <option value='Packages & Promotions'>Packages & Promotions</option>
                        <option value='Vendor'>Vendor</option>
                        <option value='Equipment'>Equipment</option>
                        <option value='Services'>Services</option>
                        <option value='Staff'>Staff</option>
                        <option value='Contractors'>Contractors</option>
                        <option value='Expenses'>Expenses</option>
                        <option value='Custom'>Custom</option>
                    </select>
                </div>

                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category of Expense:</label>
                    <select data-placeholder="Choose a Category..." name="expensecategory[]" class="chosen-select-deselect1 form-control equipmentid" width="380">
                        <option value=''></option>
                        <option value='Hotel'>Hotel</option>
                        <option value='Breakfast'>Breakfast</option>
                        <option value='Lunch'>Lunch</option>
                        <option value='Dinner'>Dinner</option>
                        <option value='Per Deum'>Per Deum</option>
                        <option value='Tickets'>Tickets</option>
                        <option value='Transportation'>Transportation</option>
                        <option value='Entertainment'>Entertainment</option>
                        <option value='Misc'>Misc</option>
                    </select>
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="expfinalprice[]" readonly id="expfinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="expprojectprice[]" onchange="countExpense()" type="text" class="form-control" />
                </div>
            </div>

        </div>

        <div id="add_here_new_exp"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_exp" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="expense_budget" value="<?php echo $budget_price[11]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="expense_total" type="text" class="form-control">
    </div>
</div>