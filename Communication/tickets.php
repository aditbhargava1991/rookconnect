<?php
/*
Inventory Listing
*/
include ('../include.php');

?>
<style>

</style>
<script type="text/javascript" src="tickets.js"></script>
<link rel="stylesheet" href="tasks.css" type="text/css">

</head>
<body>
<?php
			$contactide = $_SESSION['contactid'];
			$get_table_orient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts WHERE contactid='$contactide'"));
			$check_table_orient = $get_table_orient['horizontal_communication'];
			?>

<script>
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
<?php include_once ('../navigation.php');
checkAuthorised('communication');
?>
<div class="container">
	<div class="row hide_on_iframe">

    <h1 class="single-pad-bottom"><?= TICKET_TILE ?>
    <?php
    if(config_visible_function($dbc, 'communication') == 1) {
        echo '<a href="field_config_tasks.php?type=tab" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
    }

    $value_config = ','.get_config($dbc, 'task_ticket').',';
	echo '<br><br><div class="mobile-100-container">';
    if (strpos($value_config, ','."Task".',') !== FALSE) {
    echo "<a href='tasks.php?category=All'><button type='button' class='mobile-100 btn brand-btn mobile-block' >My ".TASK_TILE."</button></a>&nbsp;&nbsp;";
    }
    if (strpos($value_config, ','."Ticket".',') !== FALSE) {
    echo "<a href='tickets.php?category=All'><button type='button' class='mobile-100 btn brand-btn mobile-block active_tab' >".TICKET_TILE."</button></a>&nbsp;&nbsp;";
    }
    if (strpos($value_config, ','."Task".',') !== FALSE) {
    echo "<a href='scrum_tasks.php?category=All'><button type='button' class='mobile-100 btn brand-btn mobile-block' >Scrum ".TASK_TILE."</button></a>&nbsp;&nbsp;";
    }
    if (strpos($value_config, ','."Ticket".',') !== FALSE) {
    echo "<a href='scrum_tickets.php?category=All'><button type='button' class='mobile-100 btn brand-btn mobile-block' >Scrum ".TICKET_TILE."</button></a>&nbsp;&nbsp;";
    }

    //echo '<a class="btn brand-btn pull-right" href="#"  onclick="wwindow.open(\''.WEBSITE_URL.'/Ticket/add_tickets.php?tile_add=tasktile\', \'newwindow\', \'width=1000, height=900\'); return false;">Add Ticket</a>';
	echo '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit=0&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="mobile-100 adder btn brand-btn pull-right mobile-100-pull-right" style="width:auto;">Add '.TICKET_NOUN.'</a>';
	echo '</div>';
    ?>
    </h1>

	<input type='hidden' value='<?php echo $contactide; ?>' class='contacterid'>
	<span style='padding:5px; font-weight:bold;'>Vertical View: </span><input onclick="handleClick(this);" type='radio' style='width:20px; height:20px;' <?php if($check_table_orient !== 1) { echo 'checked'; } ?> name='horizo_vert' class='horizo_vert' value=''>
	<span style='padding:5px; font-weight:bold;'>Horizontal View (Mobile): </span><input onclick="handleClick(this);" <?php if($check_table_orient == 1) { echo 'checked'; } ?> type='radio' style='width:20px; height:20px;' name='horizo_vert' class='horizo_vert' value='1'>

    <form name="form_sites" method="post" action="" class="form-inline" role="form">
        <?php
        $category = $_GET['category'];
        $tabs = get_config($dbc, 'ticket_tab');
        $each_tab = explode(',', $tabs);
		echo '<br><br><div class="mobile-100-container">';
        $active_all = '';
        if(empty($_GET['category']) || $_GET['category'] == 'All') {
            $active_all = 'active_tab';
        }
        echo "<a href='tickets.php?category=All'><button type='button' class='mobile-100 btn brand-btn mobile-block ".$active_all."' >All</button></a>&nbsp;&nbsp;";
        foreach ($each_tab as $cat_tab) {
            $board_assign = get_ticketlist($dbc, $cat_tab, 'board_assign');
            if (strpos($board_assign, ','.$_SESSION['contactid'].',') !== FALSE) {
                $active_daily = '';
                if((!empty($_GET['category'])) && ($_GET['category'] == $cat_tab)) {
                    $active_daily = 'active_tab';
                }
                echo "<a href='tickets.php?category=".$cat_tab."'><button type='button' class='mobile-100 btn brand-btn mobile-block ".$active_daily."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
            }
        }
        ?>
		</div>
        <br><br>
        <div class="scrum_tickets" id="scrum_tickets">
        <?php
        $contactid = $_SESSION['contactid'];
        $tabs = get_config($dbc, 'ticket_status');
        $each_tab = explode(',', $tabs);
        $i=1;
        foreach ($each_tab as $cat_tab) {
            if(empty($_GET['category']) || $_GET['category'] == 'All') {
                $query_check_credentials = "SELECT * FROM tickets WHERE contactid LIKE '%," . $contactid . ",%' AND status='$cat_tab' ORDER BY ticketid DESC";
            } else {
                $query_check_credentials = "SELECT * FROM tickets WHERE contactid LIKE '%," . $contactid . ",%' AND status='$cat_tab' AND category='$category' ORDER BY ticketid DESC";
            }

            //$query_check_credentials = "SELECT * FROM tickets WHERE contactid LIKE '%," . $contactid . ",%' AND status='$cat_tab' ORDER BY ticketid DESC";
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
                //echo '<li id="'.$row['ticketid'].'" class="ui-state-default '.$class_on.'"><a href="#"  onclick="wwindow.open(\''.WEBSITE_URL.'/Ticket/add_tickets.php?ticketid='.$row['ticketid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;"><b>#'.$row['ticketid']. '</b><br>'.$business.'<br>'.$row['to_do_date'].' & '.$row['max_time'].'</a></li>';
				echo '<li id="'.$row['ticketid'].'" class="ui-state-default '.$class_on.'"><a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="'.$row['ticketid'].'" title="'.$row['heading'].'"><b>#'.$row['ticketid']. '</b><br>'.$business.'<br>'.$row['to_do_date'].' & '.$row['max_time'].'</a></li>';
            }
            echo '</ul>';
            $i++;
        }
        ?>
        </div>
		</form>
	</div>
</div>

<?php include ('../footer.php'); ?>