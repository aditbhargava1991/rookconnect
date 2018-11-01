<?php
/*
 * Ticket Preview for Event Calendar
 */
include_once ('../include.php');
error_reporting(0);
?>
<script type="text/javascript">
function editTicket() {
	var ticketid = $('#ticketid').val();
	parent.window.location.href = '<?= WEBSITE_URL ?>/Ticket/index.php?action=view&edit='+ticketid+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name);
}
function bookTicket() {
	var ticketid = $('#ticketid').val();
	var contactid = <?= $_SESSION['contactid'] ?>;
	var is_booked = $('#is_booked').val();
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?fill=book_ticket&ticketid='+ticketid+'&contactid='+contactid+'&is_booked='+is_booked,
		type: 'GET',
		dataType: 'html',
		success: function(response) {
			window.location.reload();
		}
	});
}
</script>
</head>

<body><?php
    include_once ('../navigation.php');
    checkAuthorised(); ?>

    <div class="main-screen full-width-screen" style="height: 100%;">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
			$value_config = get_field_config($dbc, 'tickets');
			$project_tabs = get_config($dbc, 'project_tabs');
			if($project_tabs == '') {
				$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
			}
			$project_tabs = explode(',',$project_tabs);
			$project_vars = [];
			foreach($project_tabs as $item) {
				$project_vars[preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)))] = $item;
			}

			$ticketid = $_GET['ticketid'];
			$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
			$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$ticket['projectid']."'"));

			$max_capacity = $ticket['max_capacity'];
			$cur_capacity = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `src_table` = 'Members' AND `deleted` = 0"))['num_rows'];
			$is_booked = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `item_id` = '".$_SESSION['contactid']."' AND `deleted` = 0"));
			if(!empty($is_booked)) {
				$is_booked = 1;
				$book_noun = 'Unbook '.TICKET_NOUN;
			} else {
				$is_booked = 0;
				$book_noun = 'Book '.TICKET_NOUN;
				if($max_capacity > 0 && $cur_capacity >= $max_capacity) {
					$book_noun = 'Capacity Reached';
				}
			}
			?>

			<div class="col-sm-12" style="background-color: rgb(58, 196, 242); margin:-20px 0 20px 0;">
			    <h1 style="color:#fff; padding-top:0.2em; padding-bottom:0.2em; margin:0;"><?= $project['project_name'] ?>
			    <a href="" onclick="window.location.reload();" class="brand-btn pull-right" style="background-color:#fff; color:rgb(58, 196, 242); padding-right:0.4em; padding-left:0.4em; text-decoration:none;">X</a></h1>
			</div>

            <input type="hidden" id="ticketid" name="ticketid" value="<?php echo $ticketid ?>" />
            <input type="hidden" id="contact_category" name="contact_category" value="<?= get_contact($dbc, $_SESSION['contactid'], 'category') ?>">
            <input type="hidden" id="is_booked" name="is_booked" value="<?= $is_booked ?>">

            <div class="padded double-gap-top">
				<?php if(!empty($ticket['attached_image'])) {
					if(file_exists('../Ticket/download/'.$ticket['attached_image'])) {
						echo '<img style="max-width: 50%; float: right;" src="../Ticket/download/'.$ticket['attached_image'].'">';
					} else if(file_exists('../Calendar/download/'.$ticket['attached_image'])) {
						echo '<img style="max-width: 50%; float: right;" src="../Calendar/download/'.$ticket['attached_image'].'">';
					}
				} ?>
            	<h3>Event Info</h3>
            	<div class="padded">
	            	<?= $project_vars[$project['projecttype']] ?><br><br>
            		<h4>Start and End Time</h4>
	            	<?= date('l, F d', strtotime($ticket['to_do_date'])).' '.date('h:i a', strtotime($ticket['member_start_time'])) ?><br>
	            	<?= date('l, F d', strtotime($ticket['to_do_date'])).' '.date('h:i a', strtotime($ticket['member_end_time'])) ?><br><br>
	            	<?php if (strtolower(get_contact($dbc, $_SESSION['contactid'], 'category')) == 'staff') { ?>
	            		<h4>Staff Start and End Time</h4>
		            	<?= date('l, F d', strtotime($ticket['to_do_date'])).' '.date('h:i a', strtotime($ticket['start_time'])) ?><br>
		            	<?= date('l, F d', strtotime($ticket['to_do_date'])).' '.date('h:i a', strtotime($ticket['end_time'])) ?><br><br>
	            	<?php } ?>
	            	<h4><?= TICKET_NOUN ?> Details</h4>
	            	<?= html_entity_decode($ticket['notes']) ?>
	            </div>
                <div class="gap-top double-gap-bottom pull-right">
                	<?php if (vuaed_visible_function($dbc, 'calendar_rook')) { ?>
	                	<a href="" class=" btn brand-btn" onclick="editTicket(); return false;">Edit <?= TICKET_NOUN ?></a>
                	<?php } ?>
                    <a href="" class=" btn brand-btn" onclick="window.location.reload();">Cancel</a>
                    <a href="" class="pull-right btn brand-btn" onclick="bookTicket()" <?= ($is_booked == 0 && $max_capacity > 0 && $cur_capacity >= $max_capacity) ? 'disabled' : '' ?>><?= $book_noun ?></a>
                    <div class="clearfix"></div>
                </div>
            </div>
        </form>
    </div>

<?php include_once('../footer.php'); ?>>
