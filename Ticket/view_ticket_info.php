<div class="col-md-12">
    <div class="form-group">
        <label for="site_name" class="col-sm-4 control-label">Service Tab:</label>
        <div class="col-sm-8">
            <?php echo $service_type; ?>
        </div>
    </div>

    <!--
    <div class="form-group">
      <label for="site_name" class="col-sm-4 control-label">Service Heading:</label>
      <div class="col-sm-8">
        <?php echo $sub_heading; ?>
      </div>
    </div>
    -->

    <div class="form-group">
        <label for="first_name" class="col-sm-4 control-label">Service Tab:</label>
        <div class="col-sm-8">
            <?php echo $service; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="first_name" class="col-sm-4 control-label">Service Heading:</label>
        <div class="col-sm-8">
            <?php echo $sub_heading; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="first_name" class="col-sm-4 control-label">Heading:</label>
        <div class="col-sm-8">
            <?php echo $heading; ?>
        </div>
    </div>

    <!--
    <div class="form-group">
        <label for="first_name" class="col-sm-4 control-label">Scrum Board:</label>
        <div class="col-sm-8">
            <?php echo $category; ?>
        </div>
    </div>
    -->

  <div class="form-group">
    <label for="site_name" class="col-sm-4 control-label">Description:</label>
    <div class="col-sm-8">
      <?php echo html_entity_decode($assign_work); ?>
    </div>
  </div>

    <div class="form-group">
        <label for="first_name" class="col-sm-4 control-label">Current <?= TICKET_NOUN ?> Status:</label>
        <div class="col-sm-8">
            <?php echo $status; ?>
        </div>
    </div>

</div>