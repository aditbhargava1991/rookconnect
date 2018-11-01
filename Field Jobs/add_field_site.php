<?php
/*
Add	Site
*/
include ('../include.php');
checkAuthorised('field_job');
error_reporting(0);

if (isset($_POST['submit'])) {

	$same_address =	0;

	$site_name = filter_var($_POST['site_name'],FILTER_SANITIZE_STRING);
	$domain_name = filter_var($_POST['domain_name'],FILTER_SANITIZE_STRING);
	$display_name = filter_var($_POST['display_name'],FILTER_SANITIZE_STRING);
	$clientid = $_POST['clientid'];
	$phone_number =	filter_var($_POST['phone_number'],FILTER_SANITIZE_STRING);
	$fax_number	= filter_var($_POST['fax_number'],FILTER_SANITIZE_STRING);
	$photo = htmlspecialchars($_FILES["file"]["name"], ENT_QUOTES);
    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);

	$same_address =	$_POST['same_address'];

	$office_street = filter_var($_POST['office_street'],FILTER_SANITIZE_STRING);
	$office_country	= filter_var($_POST['office_country'],FILTER_SANITIZE_STRING);
	$office_city = filter_var($_POST['office_city'],FILTER_SANITIZE_STRING);
	$office_state =	filter_var($_POST['office_state'],FILTER_SANITIZE_STRING);
	$office_zip	= filter_var($_POST['office_zip'],FILTER_SANITIZE_STRING);

	if($same_address ==	1) {
		$mail_street = filter_var($_POST['office_street'],FILTER_SANITIZE_STRING);
		$mail_country =	filter_var($_POST['office_country'],FILTER_SANITIZE_STRING);
		$mail_city = filter_var($_POST['office_city'],FILTER_SANITIZE_STRING);
		$mail_state	= filter_var($_POST['office_state'],FILTER_SANITIZE_STRING);
		$mail_zip =	filter_var($_POST['office_zip'],FILTER_SANITIZE_STRING);
	} else {
		$mail_street = filter_var($_POST['mail_street'],FILTER_SANITIZE_STRING);
		$mail_country =	filter_var($_POST['mail_country'],FILTER_SANITIZE_STRING);
		$mail_city = filter_var($_POST['mail_city'],FILTER_SANITIZE_STRING);
		$mail_state	= filter_var($_POST['mail_state'],FILTER_SANITIZE_STRING);
		$mail_zip =	filter_var($_POST['mail_zip'],FILTER_SANITIZE_STRING);
	}

	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}

	if(empty($_POST['siteid'])) {
		move_uploaded_file($_FILES["file"]["tmp_name"],	"download/" . $photo);

		$query_insert_site = "INSERT INTO `field_sites` (`clientid`, `site_name`, `domain_name`, `display_name`, `phone_number`,	`fax_number`, `photo`, `description`, `same_address`, `mail_street`, `mail_country`, `mail_city`, `mail_state`, `mail_zip`, `office_street`, `office_country`,	`office_city`, `office_state`, `office_zip`) VALUES	('$clientid', '$site_name', '$domain_name', '$display_name', '$phone_number',	'$fax_number', '$photo', '$description', '$same_address', '$mail_street', '$mail_country', '$mail_city', '$mail_state',	'$mail_zip', '$office_street', '$office_country', '$office_city', '$office_state', '$office_zip')";
		$result_insert_site	= mysqli_query($dbc, $query_insert_site);
        $url = 'Added';
		$before_change = '';
    $history = "field_invoice entry has been added. <br />";
    add_update_history($dbc, 'field_sites', $history, '', $before_change);

	} else {
		$siteid = $_POST['siteid'];

		if($photo == '') {
			$photo_update =	$_POST['photo_file'];

		} else {
			$photo_update =	$photo;
		}

		move_uploaded_file($_FILES["file"]["tmp_name"],	"download/" . $photo_update);

		$query_update_site = "UPDATE `field_sites` SET `clientid` = '$clientid', `site_name` = '$site_name', `domain_name` = '$domain_name', `display_name` = '$display_name', `phone_number`	= '$phone_number',	`fax_number` = '$fax_number', `photo` =	'$photo_update', `description` = '$description', `same_address`	= '$same_address', `mail_street` = '$mail_street', `mail_country` =	'$mail_country', `mail_city` = '$mail_city', `mail_state` =	'$mail_state', `mail_zip` =	'$mail_zip', `office_street` = '$office_street', `office_country` =	'$office_country', `office_city` = '$office_city', `office_state` =	'$office_state', `office_zip` =	'$office_zip' WHERE	`siteid` = '$siteid'";

		$result_update_site	= mysqli_query($dbc, $query_update_site);
        $url = 'Updated';

		$before_change = '';
    $history = "field_sites entry has been updated for siteid -> $siteid. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
	}

    echo '<script type="text/javascript"> window.location.replace("field_sites.php"); </script>';

   // mysqli_close($dbc); //Close the DB Connection
}
$edit_result = mysqli_fetch_array(mysqli_query($dbc, "select field_list from field_config_field_jobs where tab='sites'"));
$edit_config = $edit_result['field_list'];
if(str_replace(',','',$edit_config) == '') {
	$edit_config = ',customer,site_name,website,display,phone,fax,photo,description,office_address,office_city,office_province,office_country,office_postal,address_sync,mail_address,mail_country,mail_city,mail_province,mail_postal,';
}
?>
<script	type="text/javascript">
  $(document).ready(function() {

    $("#form1").submit(function( event ) {
        var clientid = $("#clientid").val();
        var site_name = $("input[name=site_name]").val();
        if (clientid == '' || site_name == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });
	<?php if(strpos($edit_config,',address_sync,') !== false): ?>
	$("#same_address").prop('checked', true);
	$("#mail_addr").css("display", "none");
	$("#same_address").change(function(){
	  $("#mail_addr").toggle();
	});
	<?php endif; ?>
  });

</script>
</head>

<body>
<?php include_once ('../navigation.php');

?>
<div class="container">
  <div class="row">

		<h1>Site</h1>
		<div class="pad-left gap-top double-gap-bottom"><a href="field_sites.php" class="btn config-btn">Back to Dashboard</a></div>
		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<?php
		$site_name = '';
        $domain_name = '';
        $display_name = '';
		$phone_number =	'';
		$fax_number	= '';
		$description = '';
		$db_user_email = '';
		$same_address =	'';
		$clientid = '';
		$office_street = '';
		$office_country	= '';
		$office_city = '';
		$office_state =	'';
		$office_zip	= '';
		$mail_street = '';
		$mail_country =	'';
		$mail_city = '';
		$mail_state	= '';
		$mail_zip =	'';

		if(!empty($_GET['siteid']))	{

			$db_siteid = $_GET['siteid'];
			$get_site =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	field_sites WHERE	siteid='$db_siteid'"));

			$db_siteid = $get_site['siteid'];
			$site_name = $get_site['site_name'];
            $domain_name = $get_site['domain_name'];
            $display_name = $get_site['display_name'];
			$phone_number =	$get_site['phone_number'];
			$clientid = $get_site['clientid'];
			$fax_number	= $get_site['fax_number'];
			$doc_name =	$get_site['photo'];
			$description = $get_site['description'];

			$same_address =	$get_site['same_address'];

			$office_street = $get_site['office_street'];
			$office_country	= $get_site['office_country'];
			$office_city = $get_site['office_city'];
			$office_state =	$get_site['office_state'];
			$office_zip	= $get_site['office_zip'];

			$mail_street = $get_site['mail_street'];
			$mail_country =	$get_site['mail_country'];
			$mail_city = $get_site['mail_city'];
			$mail_state	= $get_site['mail_state'];
			$mail_zip =	$get_site['mail_zip'];
		?>
		<input type="hidden" id="siteid"	name="siteid" value="<?php echo $db_siteid ?>" />
		<?php	}	   ?>

        <div class="panel-group" id="accordion2">

            <?php if(strpos($edit_config,'customer') !== false) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
                            Customer<span class="glyphicon glyphicon-minus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_info" class="panel-collapse collapse in">
                    <div class="panel-body">

						<div class="form-group">
							<label for="site_name" class="col-sm-4 control-label">Customer<span class="text-red">*</span>:</label>
							<div class="col-sm-8">
							<select data-placeholder="Choose a Customer..." id="clientid" name="clientid" class="chosen-select-deselect form-control" width="380">
							  <option value=""></option>
							  <?php
								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Client' OR category='Customer' AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
								foreach($query as $id) {
									$selected = '';
									$selected = $id == $clientid ? 'selected = "selected"' : '';
									echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
								}
							  ?>
							</select>
							</div>
						</div>

    					<div class="form-group">
							<div class="col-sm-4 clearfix">
								<a href="field_sites.php" class="btn brand-btn pull-right">Back</a>
								<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
							</div>
							<div class="col-sm-8">
								<button type="submit" name="submit" value="Submit" class="btn brand-btn pull-right">Submit</button>
							</div>
						</div>

                    </div>
                </div>
            </div>
            <?php } ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_co_info" >
                            Site Information<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_co_info" class="panel-collapse collapse">
                    <div class="panel-body">

					  <?php if(strpos($edit_config,'site_name') !== false) { ?>
                      <div class="form-group">
						<label for="site_name" class="col-sm-4 control-label">Site Name	(Location)<span	class="text-red">*</span>:</label>
						<div class="col-sm-8">
						  <input name="site_name" type="text" value="<?php echo $site_name; ?>" id="site_name" class="form-control" />
						  <span class="text-red" id="site_fillup"></span>
						</div>
					  </div>
                      <?php } ?>

						<?php if(strpos($edit_config,',website,') !== false): ?>
						  <div class="form-group">
							<label for="site_name" class="col-sm-4 control-label">Website:<br>(e.g. - https://www.google.com)</label>
							<div class="col-sm-8">
							  <input name="domain_name" type="text" value="<?php echo $domain_name; ?>" class="form-control" />
							</div>
						  </div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',display,') !== false): ?>
						  <div class="form-group">
							<label for="site_name" class="col-sm-4 control-label">Display Name:</label>
							<div class="col-sm-8">
							  <input name="display_name" type="text" value="<?php echo $display_name; ?>" class="form-control" />
							</div>
						  </div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',phone,') !== false): ?>
						  <div class="form-group">
							<label for="phone_number" class="col-sm-4 control-label">Phone Number:</label>
							<div class="col-sm-8">
							  <input name="phone_number" type="text" value="<?php echo $phone_number; ?>" class="form-control"/>
							</div>
						  </div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',fax,') !== false): ?>
						  <div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Fax Number:</label>
							<div class="col-sm-8">
							  <input name="fax_number" type="text" value="<?php	echo $fax_number; ?>" class="form-control"/>
							</div>
						  </div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',photo,') !== false): ?>
						  <div class="form-group">
							<label for="file[]"	class="col-sm-4	control-label">Upload Photo:
							<span class="popover-examples list-inline">&nbsp;
							<a  data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
							</span>
							</label>
							<div class="col-sm-8">
							<?php if((!empty($_GET['siteid'])) && ($doc_name != '')) {
								echo "<a href='download/".$doc_name."' target='_blank'>View</a>";
								?>
								<input type="hidden" name="photo_file" value="<?php	echo $doc_name;	?>"	/>
							  <?php	} ?>
							  <input name="file" accept="image/*" type="file" id="file" data-filename-placement="inside"	class="form-control" />
							</div>
						  </div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',description,') !== false): ?>
						<div class="form-group">
							<label for="additional_note" class="col-sm-4 control-label">Description:</label>
							<div class="col-sm-8">
								<textarea name="description" rows="5" cols="50" class="form-control"><?php	echo $description; ?></textarea>
							</div>
						</div>
						<?php endif; ?>

					  <div class="form-group">
						<div class="col-sm-4 clearfix">
							<a href="field_sites.php"	class="btn brand-btn pull-right">Back</a>
							<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
						</div>
						<div class="col-sm-8">
							<button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Submit</button>
						</div>
					  </div>

                    </div>
                </div>
            </div>


            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_add" >
                            Address<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_add" class="panel-collapse collapse">
                    <div class="panel-body">

					<?php if(strpos($edit_config,',office_address,') !== false ||
						strpos($edit_config,',office_city,') !== false ||
						strpos($edit_config,',office_province,') !== false ||
						strpos($edit_config,',office_postal,') !== false ||
						strpos($edit_config,',office_country,') !== false): ?>
					  <h3>Office Address</h3>

						<?php if(strpos($edit_config,',office_address,') !== false): ?>
						  <div class="form-group">
							<label for="office_street" class="col-sm-4 control-label">Unit Number & Street:</label>
							<div class="col-sm-8">
							  <input name="office_street" type="text" value="<?php echo	$office_street;	?>"	class="form-control"/>
							</div>
						  </div>
						<?php endif; ?>
						<?php if(strpos($edit_config,',office_city,') !== false): ?>
						  <div class="form-group">
							<label for="office_city" class="col-sm-4 control-label">City:</label>
							<div class="col-sm-8">
							  <input name="office_city"	type="text"	value="<?php echo $office_city;	?>"	class="form-control"/>
							</div>
						  </div>
						<?php endif; ?>
						<?php if(strpos($edit_config,',office_province,') !== false): ?>
						  <div class="form-group">
							<label for="office_state" class="col-sm-4 control-label">State/Province:</label>
							<div class="col-sm-8">
							  <input name="office_state" type="text" value="<?php echo $office_state; ?>" class="form-control"/>
							</div>
						  </div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',office_country,') !== false): ?>
						  <div class="form-group">
							<label for="office_country"	class="col-sm-4	control-label">Country:</label>
							<div class="col-sm-8">
							  <input name="office_country" type="text" value="<?php	echo $office_country; ?>" class="form-control"/>
							</div>
						  </div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',office_postal,') !== false): ?>
						  <div class="form-group">
							<label for="office_zip"	class="col-sm-4	control-label">Zip/Postal Code:</label>
							<div class="col-sm-8">
							  <input name="office_zip" type="text" value="<?php	echo $office_zip; ?>" maxlength="10" class="form-control"/>
							</div>
						  </div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',address_sync,') !== false): ?>
						  <div class="form-group">
							<div class="col-sm-offset-4	col-sm-8">
							  <div class="checkbox">
								<label>
								  <input type="checkbox" value="1" name="same_address" value="<?php	echo $same_address;	?>"	id="same_address"> If mailing address is same as the office	address
								</label>
							  </div>
							</div>
						  </div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if(strpos($edit_config,',mail_address,') !== false ||
						strpos($edit_config,',mail_city,') !== false ||
						strpos($edit_config,',mail_province,') !== false ||
						strpos($edit_config,',mail_postal,') !== false ||
						strpos($edit_config,',mail_country,') !== false): ?>
					  <div id="mail_addr">

						<h3>Mailing	Address</h3>

						<?php if(strpos($edit_config,',mail_address,') !== false): ?>
							<div class="form-group">
							  <label for="mail_street" class="col-sm-4 control-label">Street / PO Box #:</label>
							  <div class="col-sm-8">
								<input name="mail_street" type="text" value="<?php echo	$mail_street; ?>"  class="form-control"/>
							  </div>
							</div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',mail_country,') !== false): ?>
							<div class="form-group">
							  <label for="mail_country"	class="col-sm-4	control-label">Country:</label>
							  <div class="col-sm-8">
								<input name="mail_country" type="text" value="<?php	echo $mail_country;	?>"	class="form-control"/>
							  </div>
							</div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',mail_city,') !== false): ?>
							<div class="form-group">
							  <label for="mail_city" class="col-sm-4 control-label">City:</label>
							  <div class="col-sm-8">
								<input name="mail_city"	type="text"	value="<?php echo $mail_city; ?>" class="form-control"/>
							  </div>
							</div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',mail_province,') !== false): ?>
							<div class="form-group">
							  <label for="mail_state" class="col-sm-4 control-label">State / Province:</label>
							  <div class="col-sm-8">
								<input name="mail_state" type="text" value="<?php echo $mail_state;	?>"	class="form-control"/>
							  </div>
							</div>
						<?php endif; ?>

						<?php if(strpos($edit_config,',mail_postal,') !== false): ?>
							<div class="form-group">
							  <label for="mail_zip"	class="col-sm-4	control-label">Zip / Postal	Code:</label>
							  <div class="col-sm-8">
								<input name="mail_zip" type="text" value="<?php	echo $mail_zip;	?>"	class="form-control"/>
							  </div>
							</div>
						<?php endif; ?>

					  </div> <!-- END mail_addr	-->
					<?php endif; ?>

					  <div class="form-group">
						<div class="col-sm-4 clearfix">
							<a href="field_sites.php"	class="btn brand-btn pull-right">Back</a>
							<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
						</div>
						<div class="col-sm-8">
							<button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Submit</button>
						</div>
					  </div>

                    </div>
                </div>
            </div>

        </div>

		<div class="form-group">
			<p><span class="text-red"><em>Required	Fields *</em></span></p>
		</div>

		  <div class="form-group">
			<div class="col-sm-6">
				<a href="field_sites.php"	class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
			</div>
		  </div>
		</form>

	</div>
  </div>

<?php include ('../footer.php'); ?>
