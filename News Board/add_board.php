<?php
/*
 * Add Board
 * Called From: index.php
 * Function: Add or edit a Board
 * You won't be able to create a Softwarewide board as there is only one. You can add news items to the Softwarewide board.
 */

include ('../include.php');
include ('../database_connection_htg.php');
error_reporting(0);
$rookconnect = get_software_name();

if (isset($_POST['add_board'])) {
    $contactid = $_SESSION['contactid'];

    if ( isset($_POST['new_newsboard_board']) && !empty($_POST['new_newsboard_board']) ) {
        $new_newsboard_board = filter_var($_POST['new_newsboard_board'],FILTER_SANITIZE_STRING);
        $query = "INSERT INTO `newsboard_boards` (`board_name`, `shared_staff`) VALUES ('$new_newsboard_board', ',$contactid,')";
        mysqli_query($dbc, $query);
        $newsboard_board = mysqli_insert_id($dbc);
    } else {
        $newsboard_board = preg_replace('/[^0-9]/', '', $_POST['newsboard_board']);
    }

    $board_name_query = '';
    if (isset($newsboard_board) && $newsboard_board == 0 && isset($_POST['board_name']) ) {
        $board_name_query = ", `board_name`='". filter_var($_POST['board_name'],FILTER_SANITIZE_STRING) ."'";
    }

    foreach ( $_POST['contactid'] as $staff ) {
        $shared_staff_arr[] = str_replace(',', ' ', filter_var($staff, FILTER_SANITIZE_STRING));
    }
    $shared_staff = ','. implode(',', $shared_staff_arr) .',';

    mysqli_query($dbc, "UPDATE `newsboard_boards` SET `shared_staff`='$shared_staff' $board_name_query WHERE `boardid`='$newsboard_board'");

    echo '<script type="text/javascript">alert("News Board updated successfully.");</script>';
}
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#form1").submit(function( event ) {
            var newsboard_type = $("#newsboard_board").val();
            var contactid = $("#contactid").val();
            if (newsboard_type == '' || contactid == '' ) {
                alert("Please make sure you have filled in all of the required fields.");
                return false;
            }
        });

        $('#newsboard_board').on('change', function() {
            var boardid = $('#newsboard_board option:selected').val();
            if ( boardid == 'NEW' ) {
                $('input[name="new_newsboard_board"]').show();
                $('.edit_board_container').hide();
                $('.board_container').removeClass('col-xs-10').addClass('col-xs-12');
                $('input[name="board_name"]').hide();
            } else {
                $('input[name="new_newsboard_board"]').hide();
                $('.edit_board_container').show();
                $('.board_container').removeClass('col-xs-12').addClass('col-xs-10');
                $('input[name="board_name"]').hide();
            }
        });
    });

    $(document).on('change', '#newsboard_board', changeBoard);
    $(document).on('click', '.archive_board', archiveBoard);
    $(document).on('click', '.edit_board_name', editBoardName);

    function closeSlider() {
        window.location.href="../blank_loading_page.php";
    }
    function changeBoard() {
        var boardid = $('#newsboard_board option:selected').val();
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: 'news_ajax_all.php?fill=get_shared_staff&boardid='+boardid,
            success: function(response) {
                $('.staff_container').html(response);
            }
        });
    }
    function addStaff() {
        var block = $('div.add_staff').last();
        destroyInputs('.add_staff');
        clone = block.clone();
        clone.find('.form-control').val('');
        block.after(clone);
        initInputs('.add_staff');
    }
    function removeStaff(button) {
        if($('div.add_staff').length <= 1) {
            addStaff();
        }
        $(button).closest('div.add_staff').remove();
        $('div.add_staff').first().find('[name="contactid"]').change();
    }
    function archiveBoard() {
        var boardid = $('#newsboard_board option:selected').val();
        if ( boardid == '' || typeof boardid == 'undefined' ) {
            alert('Please select the news board you want to archive.');
        } else {
            var ans = confirm('Are you sure you want to archive the News Board? This will remove the board and all the news items in the board.');
        }

        if ( ans == true ) {
            $.ajax({
                type: 'GET',
                url: 'news_ajax_all.php?fill=archive_board&boardid='+boardid,
                success: function(response) {
                    alert('News board and all the news items in the board archived succesfully.');
                    window.location.replace('index.php');
                }
            });
        }
    }
    function editBoardName() {
        var board_name = $('#newsboard_board option:selected').text();
        $('input[name="board_name"]').show();
        $('input[name="board_name"]').val(board_name);

    }
</script>
</head>

<body>
<?php
include_once ('../navigation.php');
checkAuthorised('newsboard');
?>
<div class="container">
    <div class="row">
        <div class="offset-bottom-15">
            <h3 class="inline gap-left">News Boards</h3>
            <div class="pull-right gap-right offset-top-15"><a class="cursor-hand" onclick="closeSlider();"><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img"></a></div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select the News Board this news item should go under."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                        News Board<span class="red">*</span>:
                    </label>
                    <div class="col-xs-12 no-pad-left board_container">
                        <select id="newsboard_board" name="newsboard_board" class="chosen-select-deselect form-control" required>
                            <option value=""></option>
                            <option value="NEW">Add New News Board</option>
                            <?php
                                $query = mysqli_query($dbc, "SELECT * FROM `newsboard_boards` WHERE `deleted`=0 ORDER BY `board_name`");
                                while ( $boards = mysqli_fetch_assoc($query) ) {
                                    $selected = ( $boardid == $boards['boardid'] ) ? 'selected="selected"' : '';
                                    echo '<option value="'. $boards['boardid'] .'" '. $selected .'>'. $boards['board_name'] .'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-2 text-right edit_board_container" style="display:none;"><img src="../img/icons/ROOK-edit-icon.png" alt="Edit" title="Edit" class="cursor-hand no-toggle inline-img edit_board_name" /></div>
                    <input type="text" name="new_newsboard_board" class="form-control" placeholder="New News Board Name" style="display:none;" />
                    <input type="text" name="board_name" class="form-control" style="display:none;" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12 staff_container">
                    <label class="control-label">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select the News Board this news item should go under."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                        Share with Staff<span class="red">*</span>:
                    </label>
                    <?php foreach(explode(',',trim($contactid,',')) as $line_contactid) { ?>
                        <div class="add_staff">
                            <div class="clearfix"></div>
                            <div class="col-xs-10 no-pad-left">
                                <select data-placeholder="Select a Staff..." id="contactid" name="contactid[]" class="chosen-select-deselect form-control">
                                  <option value=""></option>
                                  <?php $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
                                    foreach($staff_query as $row) { ?>
                                        <option <?php if ($line_contactid == $row['contactid']) {
                                        echo " selected"; } ?> value="<?php echo $row['contactid']; ?>"><?php echo $row['first_name'].' '.$row['last_name']; ?></option>
                                    <?php }
                                  ?>
                                </select>
                            </div>

                            <div class="col-xs-2">
                                <img class="inline-img pull-right cursor-hand" onclick="removeStaff(this);" src="../img/remove.png" />
                                <img class="inline-img pull-right cursor-hand" onclick="addStaff(this);" src="../img/icons/ROOK-add-icon.png" />
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="double-gap-bottom gap-top">
                <span class="header-icon"><img src="../img/icons/ROOK-trash-icon.png" class="pull-left cursor-hand no-toggle archive_board" alt="Archive" title="Archive" /></span>
                <button type="submit" name="add_board" value="Submit" class="btn brand-btn pull-right">Submit</button>
                <a class="btn brand-btn pull-right cursor-hand" onclick="closeSlider();">Cancel</a>
                <div class="clearfix"></div>
            </div>
        </form>
    </div>
</div>
<?php include ('../footer.php'); ?>
