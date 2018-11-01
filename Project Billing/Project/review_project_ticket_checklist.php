<?php
if (isset($_POST['export_pdf'])) {
    $checklistid = $_POST['checklistid'];

    $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM checklist WHERE checklistid='$checklistid'"));

    $security = $get_contact['security'];
    $checklist_type = $get_contact['checklist_type'];
    $checklist_name = $get_contact['checklist_name'];

    DEFINE('CHECKLIST_NAME', $checklist_name);
    class MYPDF extends TCPDF {
        public function Header() {
			$this->SetFont('helvetica', '', 30);
			$footer_text = '<p style="text-align:center; background-color: #516371; color:white; height:100px; ">'.CHECKLIST_NAME.'</p>';
			$this->writeHTMLCell(0, 40, 15 , 15, $footer_text, 0, 0, false, "L", true);
        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    $pdf->SetMargins(PDF_MARGIN_LEFT, 45, PDF_MARGIN_RIGHT);

    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 14);
    $pdf->SetTextColor(53,175,199);

    $html_weekly .= '<table cellpadding="4">';

    $query_check_credentials = "SELECT * FROM checklist_name WHERE checklistid='$checklistid' ORDER BY checked, priority";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);
    while($row = mysqli_fetch_array($result)) {
        $html_weekly .= '<tr>
                        <td width="5%">';
        $checked = '';
        if($row['checked'] == 1) {
            $html_weekly .= '<img src="../img/checkmark.png" width="15px">&nbsp;&nbsp;';
        } else {
            $html_weekly .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $html_weekly .= '</td><td width="95%">'.$row['checklist'].'</td></tr>';
    }

    $html_weekly .= '</table>';

    $pdf->writeHTML($html_weekly, true, false, true, false, '');

    $pdf->Output('download/Checklist_'.$checklistid.'.pdf', 'F');

    if($security == 'My Checklist') {
        $url = 'review_project.php?type=checklist&projectid='.$_POST['projectid'].'&category='.$checklist_type.'&checklistid='.$checklistid;
    } else {
        $url = 'review_project.php?type=company_checklist&projectid='.$_POST['projectid'].'&category='.$checklist_type.'&checklistid='.$checklistid;
    }

    echo '<script type="text/javascript">
    window.location.replace("'.$url.'");
    window.open("download/Checklist_'.$checklistid.'.pdf", "fullscreen=yes");
    </script>';
}
?>
<script type="text/javascript" src="checklist.js"></script>
<style type='text/css'>
.ui-state-disabled  { pointer-events: none !important; }

.display-field {
  display: inline-block;
  text-indent: 2px;
  vertical-align: top;
  width: 97%;
}
</style>
<script>
setTimeout(function() {

var maxWidth = Math.max.apply( null, $( '.ui-sortable' ).map( function () {
    return $( this ).outerWidth( true );
}).get() );

var maxHeight = -1;

$('.ui-sortable').each(function() {
  maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();

});

$(function() {
  $(".connectedChecklist").width(maxWidth).height(maxHeight);
});
$( '.connectedChecklist' ).each(function () {
    this.style.setProperty( 'height', maxHeight, 'important' );
	this.style.setProperty( 'width', maxWidth, 'important' );

	<?php if($check_table_orient == 1) { ?>
		$(this).attr('style', 'height:'+maxHeight+'px !important; width:'+maxWidth+'px !important');
	<?php } else { ?>
		$(this).attr('style', 'height:'+maxHeight+'px !important;');
	<?PHP } ?>
});

}, 200);

$(document).ready(function() {
    $('.close_iframer').click(function(){
        $('.iframe_holder').hide();
        $('.hide_on_iframe').show();
    });
});
function choose_user(target, type, id, url, date) {
    var title   = 'Choose a User';
    $('iframe').load(function() {
        this.contentWindow.document.body.style.overflow = 'hidden';
        this.contentWindow.document.body.style.minHeight = '0';
        this.contentWindow.document.body.style.paddingBottom = '5em';
        var height = $(this).contents().find('option').length * $(this).contents().find('select').height();
        $(this).contents().find('select').data({type: type, id: id});
        this.style.height = (height + this.contentWindow.document.body.offsetHeight + 180) + 'px';
        $(this).contents().find('.btn').off();
        $(this).contents().find('.btn').click(function() {
            if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to send the '+target+' to the selected user(s)?')) {
                if(target == 'alert') {
                    $.ajax({
                        method: 'POST',
                        url: url + 'checklist_ajax.php?fill=checklistalert',
                        data: { id: id, type: type, user: $(this).closest('body').find('select').val() },
                        complete: function(result) { console.log(result.responseText); }
                    });
                }
                else if(target == 'email') {
                    $.ajax({
                        method: 'POST',
                        url: url + 'checklist_ajax.php?fill=checklistemail',
                        data: { id: id, type: type, user: $(this).closest('body').find('select').val() },
                        complete: function(result) { console.log(result.responseText); }
                    });
                }
                else if(target == 'reminder') {
                    $.ajax({
                        method: 'POST',
                        url: url + 'checklist_ajax.php?fill=checklistreminder',
                        data: { id: id, type: type, schedule: date, user: $(this).closest('body').find('select').val() },
                        complete: function(result) { console.log(result.responseText); }
                    });
                }
                $(this).closest('body').find('select').val('');
                $('.close_iframer').click();
            }
            else if($(this).closest('body').find('select').val() == '') {
                $('.close_iframer').click();
            }
        });
    });
    $('#iframe_instead_of_window').attr('src', '<?php echo WEBSITE_URL; ?>/Staff/select_staff.php?target='+target+'&multiple=true');
    $('.iframe_title').text(title);
    $('.iframe_holder').show();
    $('.hide_on_iframe').hide();
}
function send_alert(checklist) {
    checklist_id = $(checklist).parents('span').data('checklist');
    var type = 'checklist';
    var url = '';
    <?php if (isset($_GET['checklistid']) && !empty($_GET['checklistid'])) { ?>
        url = '../Checklist/';
    <?php } ?>
    choose_user('alert', type, checklist_id, url);
}
function send_email(checklist) {
    checklist_id = $(checklist).parents('span').data('checklist');
    var type = 'checklist';
    var url = '';
    <?php if (isset($_GET['checklistid']) && !empty($_GET['checklistid'])) { ?>
        url = '../Checklist/';
    <?php } ?>
    choose_user('email', type, checklist_id, url);
}
function send_reminder(checklist) {
    checklist_id = $(checklist).parents('span').data('checklist');
    var type = 'checklist';
    var url = '';
    <?php if (isset($_GET['checklistid']) && !empty($_GET['checklistid'])) { ?>
        url = '../Checklist/';
    <?php } ?>
    var name_id = (type == 'checklist board' ? 'board_' : '');
    $('[name=reminder_'+name_id+checklist_id+']').show().focus();
    $('[name=reminder_'+name_id+checklist_id+']').keyup(function(e) {
        if(e.which == 13) {
            $(this).blur();
        }
    });
    $('[name=reminder_'+name_id+checklist_id+']').change(function() {
        $(this).hide();
        var date = $(this).val().trim();
        $(this).val('');
        if(date != '') {
            choose_user('reminder', type, checklist_id, url, date);
        }
    });
}
function send_reply(checklist) {
    checklist_id = $(checklist).parents('span').data('checklist');
    var type = 'checklist';
    var url = '';
    <?php if (isset($_GET['checklistid']) && !empty($_GET['checklistid'])) { ?>
        url = '../Checklist/';
    <?php } ?>
    $('[name=reply_'+checklist_id+']').show().focus();
    $('[name=reply_'+checklist_id+']').keyup(function(e) {
        if(e.which == 13) {
            $(this).blur();
        }
    });
    $('[name=reply_'+checklist_id+']').blur(function() {
        $(this).hide();
        var reply = $(this).val().trim();
        $(this).val('');
        if(reply != '') {
            var today = new Date();
            var save_reply = reply + " (Reply added by <?php echo decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?> at "+today.toLocaleString()+")";
            $.ajax({
                method: 'POST',
                url: url + 'checklist_ajax.php?fill=checklistreply',
                data: { id: checklist_id, reply: save_reply },
                complete: function(result) { window.location.reload(); }
            })
        }
    });
}
function add_time(checklist) {
    var url = '';
    <?php if (isset($_GET['checklistid']) && !empty($_GET['checklistid'])) { ?>
        url = '../Checklist/';
    <?php } ?>
    checklist_id = $(checklist).parents('span').data('checklist');
    $('[name=checklist_time_'+checklist_id+']').show();
    $('[name=checklist_time_'+checklist_id+']').timepicker('option', 'onClose', function(time) {
        var time = $(this).val();
        $('[name=checklist_time_'+checklist_id+']').hide();
        $(this).val('00:00');
        if(time != '' && time != '00:00') {
            $.ajax({
                method: 'POST',
                url: url + 'checklist_ajax.php?fill=checklist_quick_time',
                data: { id: checklist_id, time: time+':00' },
                complete: function(result) { console.log(result.responseText); }
            })
        }
    });
    $('[name=checklist_time_'+checklist_id+']').timepicker('show');
}
function attach_file(checklist) {
    checklist_id = $(checklist).parents('span').data('checklist');
    var type = 'checklist';
    var url = '';
    <?php if (isset($_GET['checklistid']) && !empty($_GET['checklistid'])) { ?>
        url = '../Checklist/';
    <?php } ?>
    var file_id = 'attach_'+(type == 'checklist' ? '' : 'board_')+checklist_id;
    $('[name='+file_id+']').change(function() {
        var fileData = new FormData();
        fileData.append('file',$('[name='+file_id+']')[0].files[0]);
        $.ajax({
            contentType: false,
            processData: false,
            type: "POST",
            url: url + "checklist_ajax.php?fill=checklist_upload&type="+type+"&id="+checklist_id,
            data: fileData,
            complete: function(result) {
                console.log(result.responseText);
                window.location.reload();
            }
        });
    });
    $('[name='+file_id+']').click();
}
function flag_item(checklist) {
    checklist_id = $(checklist).parents('span').data('checklist');
    var type = 'checklist';
    var url = '';
    <?php if (isset($_GET['checklistid']) && !empty($_GET['checklistid'])) { ?>
        url = '../Checklist/';
    <?php } ?>
    $.ajax({
        method: "POST",
        url: url + "checklist_ajax.php?fill=checklistflag",
        data: { type: type, id: checklist_id },
        complete: function(result) {
            console.log(result.responseText);
            if(type == 'checklist') {
                $(checklist).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
            } else {
                $(checklist).closest('form').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
            }
        }
    });
}
function archive(checklist) {
    checklist_id = $(checklist).parents('span').data('checklist');
    var type = 'checklist';
    var url = '';
    <?php if (isset($_GET['checklistid']) && !empty($_GET['checklistid'])) { ?>
        url = '../Checklist/';
    <?php } ?>
    if(type == 'checklist' && confirm("Are you sure you want to archive this item?")) {
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: url + "checklist_ajax.php?fill=delete_checklist&checklistid="+checklist_id,
            dataType: "html",   //expect html to be returned
            success: function(response){
                window.location.reload();
                console.log(response.responseText);
            }
        });
    }
    else if(confirm("Are you sure you want to archive this checklist?")) {
        window.location = "<?php echo WEBSITE_URL; ?>/delete_restore.php?action=delete&remove_checklist=all&checklistid=" + checklist_id;
    }
}
</script>
    <?php
    $projectid = $_GET['projectid'];
    ?>

	<br><br>

    <div class="iframe_holder" style="display:none;">
        <img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
        <span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
        <iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
    </div>

    <?php echo '
        <div class="mobile-100-container">
            <a href="../Checklist/add_checklist.php?projectid=' . $projectid . '" class="btn brand-btn mobile-block gap-bottom pull-right">Add Checklist</a>
            <span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Checklist."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>
        </div>';
    ?>

    <br><br>

    <form name="form_sites" method="post" action="" class="form-inline" role="form">
        <div>
        <?php

        $contactid = $_SESSION['contactid'];
        echo '<div class="mobile-100-container">';
        $result = mysqli_query($dbc, "SELECT * FROM checklist WHERE deleted = 0 AND projectid = $projectid");

        $checklistid_url = $_GET['checklistid'];

        $active_checklist = '';
        if (!isset($_GET['checklistid'])) {
            $active_checklist = 'active_tab';
        }
        ?>

        <a href="review_project.php?type=checklist&projectid=<?php echo $projectid ?>"><button type="button" class="mobile-100 btn brand-btn mobile-block <?php echo $active_checklist ?>">Tickets</button></a>&nbsp;&nbsp;

        <?php

        while($row = mysqli_fetch_array($result)) {
            $active_checklist = '';
            if(($checklistid_url == $row['checklistid'])) {
                $active_checklist = 'active_tab';
            }

            echo "<a href='review_project.php?type=checklist&projectid=".$projectid."&checklistid=".$row['checklistid']."'><button type='button' class='mobile-100 btn brand-btn mobile-block ".$active_checklist."' >".$row['checklist_name']."</button></a>&nbsp;&nbsp;";
        }
        ?>
        <br><br>
        </div>
        <?php
			echo '<div class="tab-container">';

            echo '<ul id="sortable'.$i.'" class="connectedChecklist">';

            if (isset($_GET['checklistid']) && !empty($_GET['checklistid'])) {
                $checklistid = $_GET['checklistid'];
                $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist WHERE checklistid='$checklistid'"));
                $checklist_name = $result['checklist_name'];

                echo '<li class="ui-state-default ui-state-disabled no-sort" style="cursor:pointer; font-size: 30px;">'.$checklist_name.'</li>';

                $result = mysqli_query($dbc, "SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND checked = 0 AND deleted = 0 ORDER BY priority");

                while($row = mysqli_fetch_array( $result )) {
                    echo '<li id="'.$row['checklistnameid'].'" class="ui-state-default" '.($row['flag_colour'] == '' ? '' : 'style="background-color: #'.$row['flag_colour'].';"').'>';
                    echo '<span style="cursor:pointer; font-size: 25px;"><input type="checkbox" onclick="checklistChange(this);" value="'.$row['checklistnameid'].'" style="height: 1.25em; width: 1.25em;" name="checklistnameid[]">';
                    echo '<span class="pull-right" style="display:inline-block; width:calc(100% - 2em);" data-checklist="'.$row['checklistnameid'].'">';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Flag This!" onclick="flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Alert" onclick="send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Email" onclick="send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Attach File" onclick="attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Reply" onclick="send_reply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Add Time" onclick="add_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Archive Item" onclick="archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '</span>';
                    echo '<input type="text" name="reply_'.$row['checklistnameid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
                    echo '<input type="text" name="checklist_time_'.$row['checklistnameid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />';
                    echo '<input type="text" name="reminder_'.$row['checklistnameid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
                    echo '<input type="file" name="attach_'.$row['checklistnameid'].'" style="display:none;" class="form-control" />';
                    echo '<br /><span class="display-field">'.html_entity_decode($row['checklist']).'</span>&nbsp;&nbsp;';
                    $documents = mysqli_query($dbc, "SELECT * FROM checklist_name_document WHERE checklistnameid='".$row['checklistnameid']."'");
                    while($doc = mysqli_fetch_array($documents)) {
                        echo '<br /><a href="download/'.$doc['document'].'">'.$doc['document'].' (Uploaded by '.get_staff($dbc, $doc['created_by']).' on '.$doc['created_date'].')</a>';
                    }
                    echo '<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="height:1.5em; width:1.5em;" /></span>';

                    echo '</li>';
                }

                echo '</form>';

                echo '<form name="form_sites2" method="post" action="" class="form-inline no-sort" role="form">';

                echo '<li class="new_task_box no-sort"><input type="checkbox" style="height: 30px; width: 30px;">&nbsp;&nbsp;&nbsp;<input onChange="changeEndAme(this)" name="add_checklist" placeholder="Add New Checklist Item" id="add_new_task '.$checklistid.'" type="text" class="form-control" /></li>';

                $result = mysqli_query($dbc, "SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND checked = 1 AND deleted = 0");

                while($row = mysqli_fetch_array( $result )) {
                    $info = ' : '.$row['updated_date']. ' : '.$row['updated_by'];
                    echo '<li id="'.$row['checklistnameid'].'" class="ui-state-default no-sort"><span style="cursor:pointer; font-size: 20px;"><input type="checkbox" onclick="checklistChange(this);" checked value="'.$row['checklistnameid'].'" style="height: 30px; width: 30px;" name="checklistnameid[]">';

                    echo '&nbsp;&nbsp;'.html_entity_decode($row['checklist']).$info;
                    $documents = mysqli_query($dbc, "SELECT * FROM checklist_name_document WHERE checklistnameid='".$row['checklistnameid']."'");
                    while($doc = mysqli_fetch_array($documents)) {
                        echo '<br /><a href="download/'.$doc['document'].'">'.$doc['document'].' (Uploaded by '.get_staff($dbc, $doc['created_by']).' on '.$doc['created_date'].')</a>';
                    }
                    echo '</span>';

                    echo '</li>';
                }
            } else {
                echo '<li class="ui-state-default ui-state-disabled" style="cursor:pointer; font-size: 30px;">Tickets</li>';

                $result = mysqli_query($dbc, "SELECT t.*, c.name FROM tickets t, contacts c WHERE t.businessid = c.contactid AND projectid='$projectid' AND t.deleted = 0 ORDER BY ticketid DESC");

                while($row = mysqli_fetch_array( $result )) {
                    $checked = '';
                    if($row['status'] == 'Archive') {
                        $checked = ' checked';
                    }
                    echo '<li id="'.$row['ticketid'].'" class="ui-state-default"'.($row['flag_colour'] == '' ? '' : 'style="background-color: #'.$row['flag_colour'].';"').'>';

                    echo '<span style="cursor:pointer; font-size: 25px;"><input type="checkbox" '.$checked.' disabled value="'.$row['ticketid'].'" style="height: 30px; width: 30px;" name="checklistnameid[]">';
                    echo '<span class="pull-right" style="display:inline-block; width:calc(100% - 2em);" data-checklist="'.$row['ticketid'].'">';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Flag This!" onclick="flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Alert" onclick="send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Email" onclick="send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Attach File" onclick="attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Archive Item" onclick="archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="return false;"></span>';
                    echo '</span>';
                    echo '<input type="text" name="reply_'.$row['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
                    echo '<input type="text" name="checklist_time_'.$row['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />';
                    echo '<input type="text" name="reminder_'.$row['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
                    echo '<input type="file" name="attach_'.$row['ticketid'].'" style="display:none;" class="form-control" />';
                    echo '<br /><span class="display-field">#'.$row['ticketid'].' : '.$row['service_type'].' : '.$row['heading'].' : '.$row['status'].'</span>&nbsp;&nbsp;';
                    echo '<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="height:1.5em; width:1.5em;" /></span>';
                    $documents = mysqli_query($dbc, "SELECT * FROM ticket_document WHERE ticketid='".$row['ticketid']."'");
                    while($doc = mysqli_fetch_array($documents)) {
                        echo '<br /><a href="download/'.$doc['document'].'">'.$doc['document'].' (Uploaded by '.get_staff($dbc, $doc['created_by']).' on '.$doc['created_date'].')</a>';
                    }

                    echo '</li>';

                }
            }

            echo '</ul>';
            $i++;

        ?>
        </div>

		</form>
