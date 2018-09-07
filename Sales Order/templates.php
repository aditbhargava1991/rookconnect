<?php
/*
 * Sales Orders Tile Templates Page
 */
error_reporting(0);
include ('../include.php');
?>
<script type="text/javascript">
$(document).ready(function() {
    /* if($(window).width() > 767) {
        resizeScreen();
        $(window).resize(function() {
            resizeScreen();
        });
    } */
    $(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('#sales_order_div .tile-container').offset().top - 1;
        if(available_height > 200) {
            $('#sales_order_div .tile-sidebar, #sales_order_div .tile-content').height(available_height);
            $('.main-screen-white').css('height','auto');
        }
    }).resize();
    
    //iFrame
    $('.iframe_open').click(function(){
        var templateid = $('#templateid').val();
        var category = $(this).data('category');
        var title = $(this).data('title');
        var contact_category = $(this).data('contact-category');
        $('#iframe_instead_of_window').attr('src', 'get_products.php?templateid='+templateid+'&category='+category+'&contact_category='+contact_category);
        $('.iframe_title').text(title);
        $('.iframe_holder').show();
        $('.hide_on_iframe').hide();
        $('.iframe_holder iframe').outerHeight($('.iframe_holder').closest('.container').outerHeight());
    });

    $('.close_iframer').click(function(){
        $('.iframe_holder').hide();
        $('.hide_on_iframe').show();
        window.location.reload();
    });

    $('iframe').load(function() {
        this.contentWindow.document.body.style.overflow = 'scroll';
        this.contentWindow.document.body.style.minHeight = '0';
        this.contentWindow.document.body.style.paddingBottom = '15em';
        this.style.height = (this.contentWindow.document.body.offsetHeight + 10) + 'px';
    });
});

function resizeScreen() {
    var view_height = $(window).height() > 800 ? $(window).height() : 800;
    //$('#sales_order_div .scale-to-fill,#sales_order_div .scale-to-fill .main-screen,#sales_order_div .tile-sidebar').height($('#sales_order_div .tile-container').height());
    $('#sales_order_div .tile-content, #sales_order_div .tile-sidebar').height($('#sales_order_div').height() - $('.tile-header').height() + 15);
    $('#sales_order_div .main-screen-white').height($('.tile-content').height() - 15);
}
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
    checkAuthorised('sales_order');
    $templateid = $_GET['templateid'];
?>

<div id="sales_order_div" class="container">
    <div class="iframe_holder" style="display:none;">
        <img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
        <span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
        <iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src="" scrolling="yes"></iframe>
    </div>
    <div class="row hide_on_iframe">
		<div class="main-screen"><?php
            include('tile_header.php'); ?>
            
            <div class="tile-container" style="height: 100%;">
                
                <!-- Sidebar -->
                <div class="standard-collapsible tile-sidebar">
                    <ul>
                        <li <?= $templateid == 'new' ? 'class="active"' : '' ?>><a href="?templateid=new">Create New Template</a></li>
                        <?php
                        $template_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order_template` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                        foreach ($template_list as $template) {
                            echo '<li '.($templateid == $template['id'] ? 'class="active"' : '').'><a href="?templateid='.$template['id'].'">'.$template['template_name'].'</a></li>';
                        } ?>
                    </ul>
                </div><!-- .tile-sidebar -->
                
                <!-- Main Screen -->
                <div class="scale-to-fill tile-content set-section-height" style="overflow: auto;">
                    <?php include('template_edit.php'); ?>
                </div>
                
                <div class="clearfix"></div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>