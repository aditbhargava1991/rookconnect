<?php
/*
Inventory Listing
*/
include ('../include.php');
?>
<style>

</style>
<script type="text/javascript" src="tasks.js"></script>
<link rel="stylesheet" href="tasks.css" type="text/css">

</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('communication');
?>
<?php
    $contactide = $_SESSION['contactid'];
    $get_table_orient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts WHERE contactid='$contactide'"));
    $check_table_orient = $get_table_orient['horizontal_communication'];
    ?>

<script>
$(document).on('change', 'select[name="search_client"]', function() { submitForm(); });
function handleClick(sel) {

    var stagee = sel.value;
	var contactide = $('.contacterid').val();

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "task_ajax_all.php?fill=trellotable&contactid="+contactide+"&value="+stagee,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});

}
setTimeout(function() {


var maxWidth = Math.max.apply( null, $( '.ui-sortable' ).map( function () {
    return $( this ).outerWidth( true );
}).get() );


    var maxHeight = -1;

    $('.ui-sortable').each(function() {
      maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();

    });

$(function() {
  $(".connectedSortable").width(maxWidth).height(maxHeight);
});
$( '.connectedSortable' ).each(function () {
    this.style.setProperty( 'height', maxHeight, 'important' );
	this.style.setProperty( 'width', maxWidth, 'important' );

	<?php if($check_table_orient == 1) { ?>
		$(this).attr('style', 'height:'+maxHeight+'px !important; width:'+maxWidth+'px !important');
	<?php } else { ?>
		$(this).attr('style', 'height:'+maxHeight+'px !important;');
	<?PHP } ?>
});

}, 200);

$( document ).ready(function() {
});
</script>
<div class="container">
	<div class="row hide_on_iframe">

    <h1 class="single-pad-bottom">Scrum <?= TASK_TILE ?>
    <?php
    if(config_visible_function($dbc, 'communication') == 1) {
        echo '<a href="field_config_tasks.php?type=tab" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
    }

    $value_config = ','.get_config($dbc, 'task_ticket').',';
	echo '<br><br><div class="mobile-100-container">';
    if (strpos($value_config, ','."Task".',') !== FALSE) {
    echo "<a href='tasks.php?category=All'><button type='button' class='btn brand-btn mobile-block mobile-100' >My ".TASK_TILE."</button></a>&nbsp;&nbsp;";
    }
    if (strpos($value_config, ','."Ticket".',') !== FALSE) {
    echo "<a href='tickets.php?category=All'><button type='button' class='btn brand-btn mobile-block mobile-100' >".TICKET_TILE."</button></a>&nbsp;&nbsp;";
    }
    if (strpos($value_config, ','."Task".',') !== FALSE) {
    echo "<a href='scrum_tasks.php?category=All'><button type='button' class='btn brand-btn mobile-block mobile-100 active_tab' >Scrum ".TASK_TILE."</button></a>&nbsp;&nbsp;";
    }
    if (strpos($value_config, ','."Ticket".',') !== FALSE) {
    echo "<a href='scrum_tickets.php?category=All'><button type='button' class='btn brand-btn mobile-block mobile-100' >Scrum ".TICKET_TILE."</button></a>&nbsp;&nbsp;";
    }
	echo '<a href="'.WEBSITE_URL.'/Ticket/add_view_ticket_tasklist.php?tile_add=tasktile&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn pull-right mobile-100-pull-right" style="width:auto;">Add Task</a>';
   // echo '<a class="btn brand-btn pull-right" href="#"  onclick="wwindow.open(\''.WEBSITE_URL.'/Ticket/add_view_ticket_tasklist.php?tile_add=tasktile\', \'newwindow\', \'width=1000, height=900\'); return false;">Add Task</a>';
    ?>
	</div>
    </h1>

	<input type='hidden' value='<?php echo $contactide; ?>' class='contacterid'>
	<span style='padding:5px; font-weight:bold;'>Vertical View: </span><input onclick="handleClick(this);" type='radio' style='width:20px; height:20px;' <?php if($check_table_orient !== 1) { echo 'checked'; } ?> name='horizo_vert' class='horizo_vert' value=''>
	<span style='padding:5px; font-weight:bold;'>Horizontal View (Mobile): </span><input onclick="handleClick(this);" <?php if($check_table_orient == 1) { echo 'checked'; } ?> type='radio' style='width:20px; height:20px;' name='horizo_vert' class='horizo_vert' value='1'>

    <form name="form_sites" method="post" action="" class="form-inline" role="form">
        <?php
            $search_client = '';
            $search_date = '';
            $search_end_date = '';
            if(isset($_POST['search_user_submit'])) {
                $search_client = $_POST['search_client'];
                $search_date = $_POST['search_date'];
                $search_end_date = $_POST['search_end_date'];
            }
			if (isset($_POST['display_all_inventory'])) {
				$search_client = '';
                $search_date = '';
                $search_end_date = '';
			}

        ?>
        <br><br>
        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Business:</label>
		  <div class="col-sm-8">
              <select data-placeholder="Pick a Client" name="search_client" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $query = mysqli_query($dbc,"SELECT distinct(am.businessid), c.name FROM contacts c, tasklist am WHERE am.businessid=c.contactid AND c.deleted=0 order by name");
                while($row = mysqli_fetch_array($query)) {
                ?><option <?php if ($row['businessid'] == $search_client) { echo " selected"; } ?> value='<?php echo  $row['businessid']; ?>' ><?php echo decryptIt($row['name']); ?></option>
            <?php	} ?>
            </select>
		  </div>
		</div>

        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Start Date:</label>
		  <div class="col-sm-8">
             <input type="text" name="search_date" value="<?php echo $search_date; ?>" class="datepicker" onchange="submitForm()">
            </select>
		  </div>
		</div>

        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">End Date:</label>
		  <div class="col-sm-8">
             <input type="text" name="search_end_date" value="<?php echo $search_end_date; ?>" class="datepicker" onchange="submitForm()">
            </select>
		  </div>
		</div>

        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label"></label>
		  <div class="col-sm-8">
            <!--<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>-->
			<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
		  </div>
		</div>
        <br><br>
        <?php
        $category = $_GET['category'];
        $tabs = get_config($dbc, 'task_tab');
        $each_tab = explode(',', $tabs);

        $active_all = '';
        if(empty($_GET['category']) || $_GET['category'] == 'All') {
            $active_all = 'active_tab';
        }echo '<br><br><div class="mobile-100-container">';
        echo "<a href='scrum_tasks.php?category=All'><button type='button' class='mobile-100 btn brand-btn mobile-block ".$active_all."' >All</button></a>&nbsp;&nbsp;";
        $board_name = '';
        foreach ($each_tab as $cat_tab) {
            $board_assign = get_tasklist($dbc, 'Task', $cat_tab, 'board_assign');
             $board_name .= "'".get_tasklist($dbc, 'Task', $cat_tab, 'board_name')."',";
            if (strpos($board_assign, ','.$_SESSION['contactid'].',') !== FALSE) {
                $active_daily = '';
                if((!empty($_GET['category'])) && ($_GET['category'] == $cat_tab)) {
                    $active_daily = 'active_tab';
                }
                echo "<a href='scrum_tasks.php?category=".$cat_tab."'><button type='button' class='mobile-100 btn brand-btn mobile-block ".$active_daily."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
            }
        }
        $board_in = rtrim($board_name,',');
        ?>
		</div>
        <br><br>
        <div class="scrum_tickets" id="scrum_tickets">
        <?php

        $tabs = get_config($dbc, 'task_status');
        $each_tab = explode(',', $tabs);
        $i=1;
        foreach ($each_tab as $cat_tab) {
            //$query_check_credentials = "SELECT * FROM tasklist WHERE contactid='$contactid' AND status!='Archived' ORDER BY tasklistid DESC";

            if(empty($_GET['category']) || $_GET['category'] == 'All') {
                if($search_client != '') {
                    $query_check_credentials = "SELECT * FROM tasklist WHERE businessid='$search_client' AND status='$cat_tab' AND category IN ($board_in) ORDER BY tasklistid DESC";
                } else if($search_date != '' && $search_end_date != '') {
                    $query_check_credentials = "SELECT * FROM tasklist WHERE status='$cat_tab' AND category IN ($board_in) AND task_tododate BETWEEN '$search_date' AND '$search_end_date' ORDER BY tasklistid DESC";
                } else {
                    $query_check_credentials = "SELECT * FROM tasklist WHERE status='$cat_tab' AND category IN ($board_in) ORDER BY tasklistid DESC";
                }
            } else {
                if($search_client != '') {
                    $query_check_credentials = "SELECT * FROM tasklist WHERE businessid='$search_client' AND status='$cat_tab' AND category IN ($board_in) AND category='$category' ORDER BY tasklistid DESC";
                } else if($search_date != '' && $search_end_date != '') {
                    $query_check_credentials = "SELECT * FROM tasklist WHERE status='$cat_tab' AND category IN ($board_in) AND task_tododate BETWEEN '$search_date' AND '$search_end_date' AND category='$category' ORDER BY tasklistid DESC";
                } else {
                    $query_check_credentials = "SELECT * FROM tasklist WHERE status='$cat_tab' AND category IN ($board_in) AND category='$category' ORDER BY tasklistid DESC";
                }
            }

            //$query_check_credentials = "SELECT * FROM tasklist WHERE status='$cat_tab' AND category='$category' ORDER BY tasklistid DESC";
            $result = mysqli_query($dbc, $query_check_credentials);
            $status = $cat_tab;
            $status = str_replace("&","FFMEND",$status);
            $status = str_replace(" ","FFMSPACE",$status);
            $status = str_replace("#","FFMSPACE",$status);

			$class_on = '';
			 if($check_table_orient == '1') {
					$class_on = 'horizontal-on';
					$class_on_2 = 'horizontal-on-title';

					?>

					<?php

				} else {
					$class_on = '';
					$class_on_2 = '';
				}

              echo '<ul id="sortable'.$i.'" class="connectedSortable '.$status.' '.$class_on.'"><li class="ui-state-default ui-state-disabled '.$class_on_2.'">'.$cat_tab.'</li>';
              while($row = mysqli_fetch_array( $result )) {
                $business = get_client($dbc, $row['businessid']);
                $contactid = $row['contactid'];
                $staff = get_staff($dbc, $contactid);
                //echo '<li id="'.$row['tasklistid'].'" class="ui-state-default '.$class_on.'"><a href="#"  onclick="wwindow.open(\''.WEBSITE_URL.'/Ticket/add_view_ticket_tasklist.php?tile=tasktile&tasklistid='.$row['tasklistid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;"><b>'.$business.'</b><br>'.$staff.'<br><span  title="'.$row['heading']. '">'.$row['heading']. '</span></a></li>';
				echo '<li id="'.$row['tasklistid'].'" class="ui-state-default '.$class_on.'"><a href="'.WEBSITE_URL.'/Ticket/add_view_ticket_tasklist.php?tile=tasktile&tasklistid='.$row['tasklistid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="'.$row['tasklistid'].'" title="'.$row['heading'].'"><b>'.$business.'</b><br>'.$staff.'<br><span  title="'.$row['heading']. '">'.$row['heading']. '</span></a></li>';
              }
              echo '</ul>';
              //echo '<ul>';
              //echo '<li id="'.$row['tasklistid'].'" class="new_task_box"><input onChange="changeEndAme(this)" name="add_task" id="add_new_task '.$status.' '.$category.'" type="text" class="form-control add_new_task_from_here" /></li>';
               // echo '</ul>';
              $i++;
        }

        ?>
        </div>
		</form>
	</div>
</div>
<script>
    function submitForm(thisForm) {
        if (!$('input[name="search_user_submit"]').length) {
            var input = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "search_user_submit").val("1");
            $('[name=form_sites]').append($(input));
        }

        $('[name=form_sites]').submit();
    }
</script>
<?php include ('../footer.php'); ?>
