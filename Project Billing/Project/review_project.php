<?php
/*
Dashboard
*/
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
require('../gantti-master/lib/gantti.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {
    include_once ('print_project_tickets.php');
}

?>
<script type="text/javascript" src="project_path.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('#scrum_tickets').each(function() { DoubleScroll(this); });

	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
});

function exportExcel(id)
{
	var projectid=id;
	var testid = $("#testid").val();
	window.location="review_project_gantt_chart_excel.php?projectid="+projectid;
	$('.close').click();
}

function DoubleScroll(element) {
        var scrollbar= document.createElement('div');
        scrollbar.appendChild(document.createElement('div'));
        scrollbar.style.overflow= 'auto';
        scrollbar.style.overflowY= 'hidden';
        scrollbar.style.width= '';
        scrollbar.firstChild.style.width= element.scrollWidth+'px';
        scrollbar.firstChild.style.height= '0px';
        scrollbar.firstChild.style.paddingTop= '1px';
        scrollbar.firstChild.appendChild(document.createTextNode('\xA0'));
        scrollbar.onscroll= function() {
            element.scrollLeft= scrollbar.scrollLeft;
        };
        element.onscroll= function() {
            scrollbar.scrollLeft= element.scrollLeft;
        };
        element.parentNode.insertBefore(scrollbar, element);
}
</script>
<style>
.active_tab {
    color: #000000;background: #ffffff;background: -webkit-linear-gradient(top, #FFF, #9A9A9A);
    background: linear-gradient(to bottom, #FFF, #9A9A9A);
    border: 2px solid #FFFFFF;cursor:default;
}
.ui-state-disabled  { pointer-events: none !important; }
</style>
</head>
<body>

<?php include_once('../navigation.php'); ?>

<div class="container">
	<div class="iframe_holder" style="display:none;">
		<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
		<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
		<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
	</div>
	<div class="row hide_on_iframe">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <?php
            $projectid = $_GET['projectid'];
			$project_name = get_project($dbc, $projectid, 'project_name');
            $businessid = get_project($dbc, $projectid, 'businessid');
            $b_name = get_client($dbc, $businessid);
			$current_tab = '';
            $pp = '';
            $ec = '';
            $detail = '';
            $pl = '';
            $sales = '';
            $ticket = '';
            $meeting = '';
            $deli = '';
            $docs = '';
            $po = '';
            $add = '';
            $comm = '';
            $reminders = '';
            $history = '';
            $gantt = '';
            $checklist = '';
            $profit_loss = '';
            $project_time = '';
            $type = $_GET['type'];
            $note_text = '';
			if($_GET['maintype'] == 'comm') {
                $comm = 'active_tab';
				$current_tab = 'Communication';
                if($_GET['type'] == 'email_comm') {
                    $note_text = 'View all email communication sent through the software for this project.';
                } else if ($_GET['type'] == 'phone_comm') {
                    $note_text = 'View all phone communication sent through the software for this project.';
                }
            }
            if($_GET['type'] == 'project_path') {
                $pp = 'active_tab';
				$current_tab = 'Project Path';
                $note_text = 'This is where you can view all checklists, tickets, tasks and action items attached to each project, sorted into timelines depending on when they were scheduled for. You can move each item by clicking on the orange hand icon in the bottom right corner of the item and dragging to the new time block.';
            }
            if($_GET['type'] == 'gantt_chart') {
                $gantt = 'active_tab';
				$current_tab = 'Gantt Chart';
                $note_text = 'Here you can see which tickets, checklist Items and tasks are scheduled for the project in a daily calendar view. A colored legend displays the status of each ticket, checklist item or task.';
            }
            if($_GET['type'] == 'detail') {
                $detail = 'active_tab';
				$current_tab = 'Details';
                $note_text = 'Here you can add any details relative to this project, including GAP, strategy and any other project details needed.';
            }
            if($_GET['type'] == 'pl') {
                $pl = 'active_tab';
				$current_tab = 'Profit/Loss';
            }
            if($_GET['type'] == 'sales') {
                $sales = 'active_tab';
				$current_tab = 'Sales';
            }
            if($_GET['type'] == 'ticket') {
                $ticket = 'active_tab';
				$current_tab = 'Tickets';
                $note_text = 'View and edit all tickets that have been created for this project from here.';
            }
            if($_GET['type'] == 'meeting') {
                $meeting = 'active_tab';
				$current_tab = 'Meetings';
                $note_text = 'View, add and edit any past, present or future agendas and meetings created for this project.';
            }
            if($_GET['type'] == 'deli') {
                $deli = 'active_tab';
				$current_tab = 'Dates';
                $note_text = 'Recorded here is the date the project was created, the date the project was started and the estimated completion date.';
            }
            if($_GET['type'] == 'docs') {
                $docs = 'active_tab';
				$current_tab = 'Documents';
                $note_text = 'Add and view all documents and links added to this project. Multiple documents and/or links can be added by clicking Add Another Document or Add Another Link. Click Submit once you have added all documents and/or links.';
            }
            if($_GET['type'] == 'po') {
                $po = 'active_tab';
				$current_tab = 'PO';
                $note_text = 'View and email all purchase orders connected to this project.';
            }
            if($_GET['type'] == 'add') {
                $add = 'active_tab';
				$current_tab = 'Additions';
            }
            if($_GET['type'] == 'history') {
                $history = 'active_tab';
				$current_tab = 'History';
                $note_text = 'View the complete history for each task, ticket, checklist item or action taken for this project (displays in chronological order and lists the staff, action, date and time).';
            }
            if($_GET['type'] == 'reminders') {
                $reminders = 'active_tab';
				$current_tab = 'Reminders';
                $note_text = 'Set reminders for staff for specific dates and times, and view past and current reminders for this project. Reminders will display based on the From - To Dates selected, or you can view all by clicking Display All.';
            }
            if($_GET['type'] == 'checklist' || $_GET['type'] == 'company_checklist') {
                $checklist = 'active_tab';
				$current_tab = 'Checklist';
                $note_text = 'Here you can see all of the checklist items for the project, create new checklists for the project and view past checklist items that have been completed.';
            }
            if($_GET['type'] == 'profit_loss') {
                $profit_loss = 'active_tab';
				$current_tab = 'Profit & Loss';
                $note_text = 'View all profit & loss details of the work put towards this project. ';
            }
            if($_GET['type'] == 'project_time') {
                $project_time = 'active_tab';
				$current_tab = 'Total Time Tracked';
                $note_text = 'View the total amount of time tracked towards this project by ticket, task or checklist item, the staff member who completed each item, the date completed and the time tracked for each item.';
            }
            if($_GET['type'] == 'certificates') {
                $certificates = 'active_tab';
				$current_tab = 'Certificates';
            }

			if(empty($_GET['from_url'])) {
				$_GET['from_url'] = WEBSITE_URL."/Project/project.php?type=".get_project($dbc,$projectid,'projecttype');
			}
        ?>
		<h1>Project #<?php echo $projectid.' : '.$b_name.' : '.$project_name; ?></h1>
		<div class="double-gap-top"><a href="<?= $_GET['from_url']; ?>" class="btn config-btn">Back to Dashboard</a></div>
		<script>
		$(document).ready(function() {
			document.title = "Project #<?php echo $projectid.': '.$project_name.($current_tab != '' ? ' | '.$current_tab : ''); ?>";
		});
		</script>
            <input type="hidden" name="projectid" value="<?php echo $projectid; ?>">

        <br><br>
		<div class='mobile-100-container'>

			<a href='review_project.php?type=project_path&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo $pp; ?>" >Project Path</button></a>&nbsp;&nbsp;
			<a href='review_project.php?type=gantt_chart&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $gantt; ?>" >Gantt Chart</button></a>&nbsp;&nbsp;
			<a href='review_project.php?type=checklist&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $checklist; ?>" >Checklist</button></a>&nbsp;&nbsp;
			<a href='review_project.php?type=ticket&projectid=<?php echo $projectid; ?>&category=Active&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $ticket; ?>" >Tickets</button></a>&nbsp;&nbsp;
			<a href='review_project.php?maintype=comm&type=email_comm&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $comm; ?>" >Communication</button></a>&nbsp;&nbsp;
			<a href='review_project.php?type=detail&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $detail; ?>" >Details</button></a>&nbsp;&nbsp;

            <!--
            <a href='review_project.php?type=pl&projectid=<?php echo $projectid; ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $pl; ?>" >Profit/Loss</button></a>&nbsp;&nbsp;
			<a href='review_project.php?type=sales&projectid=<?php echo $projectid; ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $sales; ?>" >Sales</button></a>&nbsp;&nbsp;
            -->
			<a href='review_project.php?type=meeting&projectid=<?php echo $projectid; ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $meeting; ?>" >Agendas & Meetings</button></a>&nbsp;&nbsp;

			<a href='review_project.php?type=deli&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $deli; ?>" >Dates</button></a>&nbsp;&nbsp;
			<a href='review_project.php?type=docs&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $docs; ?>" >Documents</button></a>&nbsp;&nbsp;

			<?php if(tile_visible($dbc, 'purchase_order')) { ?>
				<a href='review_project.php?type=po&projectid=<?php echo $projectid; ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $po; ?>" >PO</button></a>&nbsp;&nbsp;
			<?php } ?>
			<a href='review_project.php?type=profit_loss&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $profit_loss; ?>" >Profit & Loss</button></a>&nbsp;&nbsp;
            <!--
			<a href='review_project.php?type=add&projectid=<?php echo $projectid; ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $add; ?>" >Additions</button></a>&nbsp;&nbsp;
			<a href='review_project.php?type=comm&projectid=<?php echo $projectid; ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $comm; ?>" >Notes</button></a>&nbsp;&nbsp;
            -->
			<a href='review_project.php?type=reminders&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $reminders; ?>" >Reminders</button></a>&nbsp;&nbsp;
			<a href='review_project.php?type=project_time&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $project_time; ?>" >Total Time Tracked</button></a>&nbsp;&nbsp;
			<?php if(strpos(','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT certificate FROM field_config"))['certificate'].',',',Client Project,') !== FALSE) { ?>
				<a href='review_project.php?type=certificates&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $certificates; ?>" >Certificates</button></a>&nbsp;&nbsp;
			<?php } ?>
			<a href='review_project.php?type=history&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $history; ?>" >History</button></a>&nbsp;&nbsp;

		</div>

        <?php if ($note_text != '') { ?>
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE: </span><?php echo $note_text; ?></div>
            <div class="clearfix"></div>
        </div>
        <?php } ?>

        <br>
        <?php
            if($_GET['type'] == 'project_path') {
                include ('review_project_path.php');
            }
            if($_GET['type'] == 'gantt_chart') {
                include ('review_project_gantt_chart.php');
            }
            if($_GET['type'] == 'checklist') {
                include ('review_project_ticket_checklist.php');
            }
            if($_GET['type'] == 'company_checklist') {
                include ('review_project_company_checklist.php');
            }
			if($_GET['maintype'] == 'comm') {
                include ('review_project_communication.php');
            }
            if($_GET['type'] == 'detail') {
                include ('review_project_detail.php');
            }
            if($_GET['type'] == 'pl') {
                $pl = 'active_tab';
            }
            if($_GET['type'] == 'sales') {
                $sales = 'active_tab';
            }
            if($_GET['type'] == 'ticket') {
                $ticket = 'active_tab';
                include ('review_project_ticket.php');
            }
            if($_GET['type'] == 'meeting') {
                $meeting = 'active_tab';
				include ('review_project_agendas_meetings.php');
            }
            if($_GET['type'] == 'deli') {
                $deli = 'active_tab';
                include ('add_project_dates.php');
            }
            if($_GET['type'] == 'docs') {
                $docs = 'active_tab';
                include ('add_project_documents.php');
            }
            if($_GET['type'] == 'po') {
                $po = 'active_tab';
				include('review_project_purchase_orders.php');
            }
            if($_GET['type'] == 'add') {
                $add = 'active_tab';
            }
            if($_GET['type'] == 'comm') {
                $comm = 'active_tab';
            }
            if($_GET['type'] == 'reminders') {
                $reminders = 'active_tab';
                include ('review_reminders.php');
            }
            if($_GET['type'] == 'history') {
                $history = 'active_tab';
                include ('project_history.php');
            }
            if($_GET['type'] == 'profit_loss') {
                $profit_loss = 'active_tab';
                include ('review_project_profit_loss.php');
            }
            if($_GET['type'] == 'project_time') {
                $project_time = 'active_tab';
                include ('review_project_time.php');
            }
            if($_GET['type'] == 'certificates') {
                $certificates = 'active_tab';
                include ('review_project_certificates.php');
            }
        ?>
        </form>
	</div>
</div>
<?php include_once('../footer.php'); ?>
<script type='text/javascript'>
$(function(){
  var gantt_width = jQuery(".gantt-items").width();
  $(".div1").css("width", gantt_width);
  $(".gantt-labels").css("margin-top", "79px");
  $(".wrapper1").scroll(function(){
    $(".gantt-data").scrollLeft($(".wrapper1").scrollLeft());
  });
  $(".gantt-data").scroll(function(){
    $(".wrapper1").scrollLeft($(".gantt-data").scrollLeft());
  });
});
</script>
