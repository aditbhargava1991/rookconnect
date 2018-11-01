<script type="text/javascript">
$(document).ready(function() {
});
</script>

<div class="col-md-12">
       <div class="form-group">
        <label for="call_date" class="col-sm-4 control-label">Date of Call:</label>
        <div class="col-sm-8">
          <input type="text" name="doc" id="doc" value="<?php echo $doc; ?>" class="datepicker form-control">
        </div>
      </div>

      <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label">Comments:</label>
        <div class="col-sm-8">
          <textarea name="comments" rows="5" cols="50" class="form-control"><?php echo $comments; ?></textarea>
        </div>
      </div>

	  <div class="form-group">
        <label for="call_date" class="col-sm-4 control-label">Status:</label>
        <div class="col-sm-8">
			<select name='status' class="chosen-select-deselect1 form-control" width="380" id='status'>
				<?php if($status == 'To Do'): ?>
					<option selected value='To Do'>To Do</option>
				<?php else: ?>
					<option value='To Do'>To Do</option>
				<?php endif; ?>
				<?php if($status == 'Scheduled'): ?>
					<option value='Scheduled'>Scheduled</option>
				<?php else: ?>
					<option value='Scheduled'>Scheduled</option>
				<?php endif; ?>
				<?php if($status == 'Left Message'): ?>
					<option value='Left Message'>Left Message</option>
				<?php else: ?>
					<option value='Left Message'>Left Message</option>
				<?php endif; ?>
				<?php if($status == 'Missed'): ?>
					<option value='Missed'>Missed</option>
				<?php else: ?>
					<option value='Missed'>Missed</option>
				<?php endif; ?>
				<?php if($status == 'Rescheduled'): ?>
					<option value='Rescheduled'>Rescheduled</option>
				<?php else: ?>
					<option value='Rescheduled'>Rescheduled</option>
				<?php endif; ?>
				<?php if($status == 'Complete'): ?>
					<option value='Complete'>Complete</option>
				<?php else: ?>
					<option value='Complete'>Complete</option>
				<?php endif; ?>
			</select>
        </div>
      </div>

    <!--
    <div class="form-group">
        <div class="col-sm-4">
            <a href="<?php echo $back_url; ?>" class="btn brand-btn">Back</a>
        </div>
        <div class="col-sm-8">
            <button type="submit" name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
        </div>
    </div>
    -->

</div>