<?php
/*
Footer file - Include in all files.
*/
$_SERVER['page_load_time'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
$_SERVER['page_load_info'] .= 'Page Complete: '.number_format($_SERVER['page_load_time'],5)."\n";
if(!IFRAME_PAGE) :
?>
<!--<footer class="fixed-bottom" id='footer' style='overflow:hidden; display:none;'>-->
<footer id='footer'>
	<div class="container">
		<div class="row">
            <div class="col-sm-9 col-xs-12">
                <p class="padded copyright pull-left">Copyright &copy; <?= date('Y');?> ROOK Connect Software</p>
                <div class="footer-sm"><?php include('Navigation/social_media_links.php'); ?></div>
            </div>
            <div class="col-sm-3">
                <?php
                    $rookconnect      = get_software_name();
                    $footer_link      = ( $rookconnect=='prime' ) ? 'http://cuecreative.ca/' : 'https://www.freshfocusmedia.com/';
                    $footer_name      = ( $rookconnect=='prime' ) ? 'CUE Creative' : 'Fresh Focus Media Inc.';
                    $footer_logo_file = ( $rookconnect=='prime' ) ? 'cue-logo-mini.png' : 'ffm-logo-mini.png';
                ?>
                <a href="<?= $footer_link; ?>" title="<?= $footer_name; ?>" class="pull-right" target="_blank"><img src="<?= WEBSITE_URL; ?>/img/<?= $footer_logo_file; ?>" height="25" style="margin-top:12px;"></a><p class="padded posh-tag text-right hidden-xs">Represented by </p>
            </div>
		</div>
	</div>
</footer>
<?php endif; ?>

<script>
if(isMobile.any()) {
	var count;
	if(jQuery(".mobile-100").length > 0) {
		if(jQuery(".tab-container").length > 0) {
			jQuery(".tab-container").append("<div id='new-item'><div class='complete-mobile'></div></div>")
			if(jQuery(".tab-container1").length > 0)
				jQuery(".tab-container1").append("<div id='new-item'><div class='complete-mobile1'></div></div>")
		}
		else if(jQuery(".tab-container1").length > 0)
			jQuery(".tab-container1").append("<div id='new-item'><div class='complete-mobile1'></div></div>")
		else if(jQuery(".mobile-100-container").length > 0)
			jQuery(".mobile-100-container").append("<div id='new-item'><div class='complete-mobile'></div></div>")
		else { 
			var parentitem = jQuery(".active_tab").parent().attr('name');
			if(parentitem == 'form1')
				jQuery(".active_tab").after("<div id='new-item'><div class='complete-mobile'></div></div>")
			else
				jQuery(".active_tab").parent().after("<div id='new-item'><div class='complete-mobile'></div></div>")
		}
	}

	if(jQuery(".tab-container").length > 0) {
		if(jQuery('.tab-container').find('.mobile-100').length > 0) {
			count = 0;
			jQuery('.tab-container').find(".mobile-100").each(function() {
				if (jQuery(this).attr('class').indexOf("active_tab") >= 0) {
				}
				else {
					var newitemclass = jQuery(this).parent().parent().attr('class');
					var parentitemname = jQuery(this).parent().attr('name');
					var parentclasscount = -1;
					if(typeof jQuery(this).parent().attr('class') !== "undefined")
						parentclasscount = jQuery(this).parent().attr('class').indexOf('tab-container');
					if(count == 0) {
						if(parentitemname == 'form1' || parentclasscount >= 0)
							jQuery(this).prependTo(".complete-mobile");
						else if((newitemclass.indexOf(" tab") >= 0 || newitemclass == "nav-subtab" || newitemclass=="popover-examples list-inline") && newitemclass.indexOf(" tab-container") < 0)
							jQuery(this).parent().parent().prependTo(".complete-mobile");
						else
							jQuery(this).parent().prependTo(".complete-mobile");
					}
					else {
						if(parentitemname == 'form1' || parentclasscount >= 0)
							jQuery(this).appendTo(".complete-mobile");
						else if((newitemclass.indexOf(" tab") >= 0 || newitemclass == "nav-subtab" || newitemclass=="popover-examples list-inline") && newitemclass.indexOf(" tab-container") < 0)
							jQuery(this).parent().parent().appendTo(".complete-mobile");
						else
							jQuery(this).parent().appendTo(".complete-mobile");
					}
					count++;
				}
			});
			
		}
		
		if(jQuery('.tab-container1').find('.mobile-100').length > 0) {	
			count = 0;
			jQuery('.tab-container1').find(".mobile-100").each(function() {
				if (jQuery(this).attr('class').indexOf("active_tab") >= 0) {
				}
				else {
					
					var newitemclass = jQuery(this).parent().parent().attr('class');
					var parentitemname = jQuery(this).parent().attr('name');
					var parentclasscount = -1;
					if(typeof jQuery(this).parent().attr('class') !== "undefined")
						parentclasscount = jQuery(this).parent().attr('class').indexOf('tab-container');
					if(count == 0) {
						if(parentitemname == 'form1' || parentclasscount >= 0)
							jQuery(this).prependTo(".complete-mobile1");
						else if((newitemclass.indexOf(" tab") >= 0 || newitemclass == "nav-subtab" || newitemclass=="popover-examples list-inline") && newitemclass.indexOf(" tab-container") < 0)
							jQuery(this).parent().parent().prependTo(".complete-mobile1");
						else
							jQuery(this).parent().prependTo(".complete-mobile1");
					}
					else {
						if(parentitemname == 'form1' || parentclasscount >= 0)
							jQuery(this).appendTo(".complete-mobile1");
						else if((newitemclass.indexOf(" tab") >= 0 || newitemclass == "nav-subtab" || newitemclass=="popover-examples list-inline") && newitemclass.indexOf(" tab-container") < 0)
							jQuery(this).parent().parent().appendTo(".complete-mobile1");
						else
							jQuery(this).parent().appendTo(".complete-mobile1");
					}
					count++;
				}
			});
		}
	}
	else if(jQuery('.tab-container1').find('.mobile-100').length > 0) {	
		count = 0;
		jQuery('.tab-container1').find(".mobile-100").each(function() {
			if (jQuery(this).attr('class').indexOf("active_tab") >= 0) {
			}
			else {
				
				var newitemclass = jQuery(this).parent().parent().attr('class');
				var parentitemname = jQuery(this).parent().attr('name');
				var parentclasscount = -1;
				if(typeof jQuery(this).parent().attr('class') !== "undefined")
					parentclasscount = jQuery(this).parent().attr('class').indexOf('tab-container');
				if(count == 0) {
					if(parentitemname == 'form1' || parentclasscount >= 0)
						jQuery(this).prependTo(".complete-mobile1");
					else if((newitemclass.indexOf(" tab") >= 0 || newitemclass == "nav-subtab" || newitemclass=="popover-examples list-inline") && newitemclass.indexOf(" tab-container") < 0)
						jQuery(this).parent().parent().prependTo(".complete-mobile1");
					else
						jQuery(this).parent().prependTo(".complete-mobile1");
				}
				else {
					if(parentitemname == 'form1' || parentclasscount >= 0)
						jQuery(this).appendTo(".complete-mobile1");
					else if((newitemclass.indexOf(" tab") >= 0 || newitemclass == "nav-subtab" || newitemclass=="popover-examples list-inline") && newitemclass.indexOf(" tab-container") < 0)
						jQuery(this).parent().parent().appendTo(".complete-mobile1");
					else
						jQuery(this).parent().appendTo(".complete-mobile1");
				}
				count++;
			}
		});
	}
	else if(jQuery(".mobile-100").length > 0) {
		count = 0;
		jQuery(".mobile-100").each(function() {
			if (jQuery(this).attr('class').indexOf("active_tab") >= 0) {
			}
			else {
				var newitemclass = jQuery(this).parent().parent().attr('class');
				var parentitemname = jQuery(this).parent().attr('name');
				var parentclasscount = -1;
				if(typeof jQuery(this).parent().attr('class') !== "undefined")
					parentclasscount = jQuery(this).parent().attr('class').indexOf('tab-container');
				if(count == 0) {
					if(parentitemname == 'form1' || parentclasscount >= 0)
						jQuery(this).prependTo(".complete-mobile");
					else if((newitemclass.indexOf(" tab") >= 0 || newitemclass == "nav-subtab" || newitemclass=="popover-examples list-inline") && newitemclass.indexOf(" tab-container") < 0)
						jQuery(this).parent().parent().prependTo(".complete-mobile");
					else
						jQuery(this).parent().prependTo(".complete-mobile");
				}
				else {
					if(parentitemname == 'form1' || newitemclass.indexOf('tab-container') >= 0)
						jQuery(this).appendTo(".complete-mobile");
					else if((newitemclass.indexOf(" tab") >= 0 || newitemclass == "nav-subtab" || newitemclass=="popover-examples list-inline") && newitemclass.indexOf(" tab-container") < 0)
						jQuery(this).parent().parent().appendTo(".complete-mobile");
					else
						jQuery(this).parent().appendTo(".complete-mobile");
				}
				count++;
			}
		});
	}

	jQuery(".complete-mobile").hide();
	jQuery(".complete-mobile1").hide();
	jQuery(".complete-mobile").after('<center><span class="show-more"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" alt="Show More" class="show-more-img" width="30" /></span></center>'); 
	jQuery(".complete-mobile1").not(':empty').after('<center><span class="show-more1"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png" alt="Show More" class="show-more1-img" width="30" /></span></center>'); 
	if($('.tab-container').find('.active_tab').length == 0) {
		$('.complete-mobile').show();
		$(".show-more-img").attr("src", "<?= WEBSITE_URL; ?>/img/icons/ROOK-minus-icon.png");
		$(".show-more-img").attr("alt", "Show Less");
	} else if($('.tab-container1').find('.active_tab').length == 0) {
		$('.complete-mobile1').show();
		$(".show-more1-img").attr("src", "<?= WEBSITE_URL; ?>/img/icons/ROOK-minus-icon.png");
		$(".show-more-img").attr("alt", "Show Less");
	}
}

$(document).ready(function() {
    $(".show-more").click(function() {
        var name    = $('.show-more-img').attr("src");
        var parts   = name.split('/');
        elem        = parts[parts.length-1];
        if (elem == "ROOK-add-icon.png") {
            jQuery(".complete-mobile").css("display","block");
            jQuery(".show-more-img").attr("src", "<?= WEBSITE_URL; ?>/img/icons/ROOK-minus-icon.png");
            jQuery(".show-more-img").attr("alt", "Show Less");
        } else {
            jQuery(".complete-mobile").css("display","none");
            jQuery(".show-more-img").attr("src", "<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png");
        }
    });
    
    $(".show-more1").click(function() {
        var name    = $('.show-more1-img').attr("src");
        var parts   = name.split('/');
        elem        = parts[parts.length-1];
        if (elem == "ROOK-add-icon.png") {
            jQuery(".complete-mobile1").css("display","block");
            jQuery(".show-more1-img").attr("src", "<?= WEBSITE_URL; ?>/img/icons/ROOK-minus-icon.png");
            jQuery(".show-more-img").attr("alt", "Show Less");
        } else {
            jQuery(".complete-mobile1").css("display","none");
            jQuery(".show-more1-img").attr("src", "<?= WEBSITE_URL; ?>/img/icons/ROOK-add-icon.png");
        }
    });
	$.get('/ajax_save.php?action=browser_load_time&page_build=<?= microtime(true) ?>');
});

</script>
<style>
<?php 
$_SERVER['page_load_time'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
$_SERVER['page_load_info'] .= 'Footer Complete: '.number_format($_SERVER['page_load_time'],5)."\n"; ?>