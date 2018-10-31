// Global IFrame Variable
var iframe_path = '';
var no_tools = false;
$(document).ready(function() {
	// Modify iFrames by adding a GET variable
	var watcher = new MutationObserver(alterIframes);
	$('iframe').each(function() {
		watcher.observe(this, {attributes:true});
	});
	
	initInputs();
	$('.iframe_overlay .iframe iframe').prop('src','/blank_loading_page.php');
	// $('.collapse').on('shown.bs.collapse', function(){
	// 	$(this).parent().find('.panel-heading').addClass('active');
	// 	$(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
	// }).on('hidden.bs.collapse', function(){
	// 	$(this).parent().find('.panel-heading').removeClass('active');
	// 	$(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
	// });
	$('.collapse').on('shown.bs.collapse', function(){
		blockPanelActive($(this).closest('.panel'));
		if($(this).closest('.panel-group').hasClass('collapse-others')) {
			var parent = $(this).closest('.panel-group');
			$(parent).find('.collapse.in').not(this).toggle();
		}
	}).on('hidden.bs.collapse', function(){
		blockPanelRemoveActive($(this).closest('.panel'));
		$('.block-panels .panel-collapse.in').each(function (){
			if($(this).find('.panel').length > 0) {
				blockPanelActive($(this).closest('.panel'));
			}
		});
	});

	$('#slider_example_1').timepicker({
		hourGrid: 4,
		minuteGrid: 10,
		timeFormat: 'hh:mm tt'
	});
	
	initTooltips();
	if($('[data-toggle="popover"]').length > 0) {
		$('[data-toggle="popover"]').popover();
		$('.popover-dismiss').popover({
		  trigger: 'focus'
		});
	}

	if($('#info_toggle_state').val() == 1) {
		Cookies.set('infoToggle','true');
	} else if($('#info_toggle_state').val() == 0) {
		Cookies.set('infoToggle','false');
	}
	if (Cookies.get('infoToggle') == 'false') {
		$('.popover-examples').hide();
	}
	
	$("#info_toggle").off('click').click(function(){
		$(".popover-examples").toggle();
		// var toggleBeforeClick = $(".popover-examples").is(":visible") ? 1 : 0;
		var toggleBeforeClick = $('#info_toggle_state').val();
		Cookies.set('infoToggle', ($('#info_toggle_state').val() == 1 ? true : false), {expires: 86400});
		var toggleState = $('#info_toggle_state').val();
		if(toggleState == 1) {
			$('.switch_info_on').show();
			$('.switch_info_off').hide();
			$('#info_toggle_state').val(0);
		} else {
			$('.switch_info_off').show();
			$('.switch_info_on').hide();
			$('#info_toggle_state').val(1);
		}
		$.ajax({
			type: "GET",
			url: "/ajax_all.php?fill=info_toggle_state&state="+toggleState,
			dataType: "html",
			success: function(response){
				//location.reload();
			}
		});
		$(window).resize();
	});

    $('.live-search-box-report').focus();
    $('.live-search-list-report a').each(function()  {
        $(this).attr('data-search-term', $(this).text().toLowerCase());
    });

    $('.live-search-box-report').on('keyup', function(){

        var searchTerm = $(this).val().toLowerCase();

        $('.live-search-list-report a').each(function(){

            if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
                $(this).show();
            } else {
                if($(this).hasClass('dont-hide')) {
                } else { $(this).hide(); }
            }

        });

    });

    // New collapsible
    if($(window).width() > 479) {
    	var sidebar = $('.standard-collapsible ul').first();
    	var collapse_html = '<a href="" onclick="collapseStandardSidebar(); return false;"><li class="standard-collapsible-link"><h5><< Hide Menu</h5></li></a>';
    	if($(sidebar).find('.standard-sidebar-searchbox').length > 0) {
	    	$(sidebar).find('.standard-sidebar-searchbox').after(collapse_html);	
    	} else {
	    	$(sidebar).prepend(collapse_html);
    	}
		$('.standard-collapsible').click(function(e) {
			var previous_height = $('.standard-collapsible .sidebar').height();
			$('.standard-collapsible .sidebar').height(0);
			if(e.pageX > $('.standard-collapsible').offset().left + $('.standard-collapsible').outerWidth() - 20 && e.pageX < $('.standard-collapsible').offset().left + $('.standard-collapsible').outerWidth()) {
				$('.standard-collapsible').removeClass('collapsed');
			}
			$('.standard-collapsible .sidebar').height(previous_height);
		});
    }

	// Make the collapsible divs collapse
	if ($(window).width() > 479) {
		$('.collapsible').click(function(e) {
			var previous_height = $('.collapsible .sidebar').height();
			$('.collapsible .sidebar').height(0);
			if(e.pageX > $('.collapsible').offset().left + $('.collapsible').outerWidth() - 20 && e.pageX < $('.collapsible').offset().left + $('.collapsible').outerWidth()) {
				$('.collapsible').toggleClass('collapsed');
			}
			$('.collapsible .sidebar').height(previous_height);
		});
	}
	$('.collapsible-horizontal').click(function(e) {
		if(e.pageY > $('.collapsible-horizontal').offset().top + $('.collapsible-horizontal').height() && e.pageY < $('.collapsible-horizontal').offset().top + $('.collapsible-horizontal').outerHeight()) {
			$('.collapsible-horizontal').toggleClass('collapsed');
			$(window).resize();
		}
	});
	$('.btn-horizontal-collapse').click(function() {
		$('.collapsible-horizontal').toggleClass('collapsed');
		$(window).resize();
	});
	
	$('.scalable').resizable({
		handles: 'w',
		resize: function() {
			$('.scalable').css('left','');
			$('body').off('mouseup');
			$('body').mouseup(function(e) {
				$('body').off('mouseup');
				var percentage = Math.round(((window.innerWidth - e.pageX) / window.innerWidth) * 100);
				$.ajax({
					url: '/ajax_all.php?fill=page_options',
					method: 'POST',
					data: {
						field: 'scale_width',
						value: percentage,
						path: window.location.pathname
					}
				});
			});
		}
	});

	if ($(window).width() > 479) {
		$('.scalable').off('click').click(function(e) {
			var div = $(this);
			if(div.hasClass('collapsed')) {
				if(e.pageX > $('.scalable').offset().left && e.pageX < $('.scalable').offset().left + 20) {
					$.ajax({
						url: '/ajax_all.php?fill=get_scale_width&php_self='+window.location.pathname,
						method: 'GET',
						dataType: 'html',
						success: function(response) {
							div.css('width', response);
							div.toggleClass('collapsed');
						}
					});
				}
			} else {
				if(e.pageX > $('.scalable').offset().left + 5 && e.pageX < $('.scalable').offset().left + 23) {
					div.css('width', '0');
					div.toggleClass('collapsed');
				}
			}
		});
	}
	$('.chosen-select-deselect').select2({
	    placeholder: "Select an Option",
		width: '100%',
		allowClear: true
	});
    
    $('.hide-header-footer, .hide-header-footer-down').click(function() {
        $('#main-header, #nav, #footer').toggle();
        var fullscreen = $('#fullscreen').val();
        if ( fullscreen==1 ) {
            $(this).removeClass('down');
            $('.pullup').removeClass('rotate');
            $('.hide-header-footer-down').hide();
            $('#fullscreen').val(0);
            $('.main-screen').removeClass('double-pad-top');
        } else {
            $(this).addClass('down');
            $('.pullup').addClass('rotate');
            $('.hide-header-footer-down').show();
            $('#fullscreen').val(1);
            $('.main-screen').addClass('double-pad-top');
        }
        fullscreen = $('#fullscreen').val();
		$.ajax({
			type: "GET",
			url: "../ajax_all.php?fill=fullscreen&state="+fullscreen,
			dataType: "html"
		});
        $(window).resize();
    });
});

function initTooltips() {
    $('.tooltip.fade').remove();
	if($(".popover-examples a,.no-toggle[title]").is(':ui-tooltip')) {
		$(".popover-examples a,.no-toggle[title]").tooltip('destroy');
	}
	$(".popover-examples a:not([data-placement=bottom]),.no-toggle[title]:not([data-placement=bottom])").tooltip({
		container: 'body',
		placement : 'top',
		trigger: 'hover'
	});
	$(".popover-examples a[data-placement=bottom],.no-toggle[title][data-placement=bottom]").tooltip({
		container: 'body',
		placement : 'bottom',
		trigger: 'hover'
	});
}

function destroyInputs(container) {
	if(container == undefined) {
		container = $('body');
	}
	container = $(container);
	var config = {
		'.chosen-select'           : {},
		'.chosen-select-deselect'  : {allow_single_deselect:true},
		'.chosen-select-no-single' : {disable_search_threshold:10},
		'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
		'.chosen-select-width'     : {width:"95%"}
	}
	for (var selector in config) {
		// container.find(selector).chosen('destroy');
		// container.find(selector).off('touchstart');
	}
	for (edId in tinyMCE.editors) {
		$(tinyMCE.editors[edId].targetElm).removeClass(function(index, className) {
			return (className.split(' ').filter(function(className) {
				return className.indexOf('tinyMCEInit') == 0;
			}).join(' '));
		});
	}
	tinymce.remove(container.selector+' textarea');
	container.find('textarea').removeAttr('id');
	container.find('.datepicker').datepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datepickernoyear').datepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datefuturepicker').datepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.dateandtimepicker').datetimepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datetimepicker').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datetimepicker-5').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datetimepicker-10').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datetimepicker-15').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datetimepicker-20').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datetimepicker-30').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datetimepicker-60').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datetimepicker-24h').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datetimepickerseconds').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.datetimepickerseconds-24h').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.timepicker').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.timepicker-max-5').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.timepicker-5').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.timepicker-10').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.timepicker-15').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.timepicker-20').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.timepicker-30').timepicker('destroy').removeClass('hasDatepicker').removeAttr('id');
	container.find('.select2').remove();
	container.find('[tabindex]').removeAttr('tabindex');
	if($('.scalable').hasClass('ui-resizable')) {
		$('.scalable').resizable('destroy');
	}
	$('.scalable').off('click');
	$('#info_toggle').off('click');
}
var tinyInitI = 0;
function initIconColors() {
    $('img').not('.theme-color-icon').each(function(){
        var self = $(this);
        var src = self.attr('src');
        if(src != undefined &&
            !src.match('/ROOK-Speedometer.png') &&
            !src.match('/ROOK-status-completed.png') &&
            !src.match('/ROOK-status-warning.png') &&
            !src.match('/ROOK-status-approved.png') &&
            !src.match('/ROOK-status-paid.png') &&
            !src.match('/ROOK-status-rejected.jpg') &&
            !src.match('/ROOK-back-icon.png') &&
            !src.match('/ROOK-trash-icon.png') &&
            !$(this).is('.no-colour') &&
            !src.match('/ROOK-status-error.png')) {
	        if ( src.match('/ROOK-') ||
	            src.match('/drag_handle.png') ||
	            src.match('/create_project.png') ||
	            src.match('/notepad-icon-blue.png') ||
	            src.match('/weekly-overview-blue.png') ||
	            src.match('/month-overview-blue.png') ||
	            src.match('/pinned.png') ||
	            src.match('/pinned-filled.png') ||
	            src.match('/email.PNG') ||
	            src.match('/address.PNG') ||
	            src.match('/setting.PNG') ||
	            src.match('/person.PNG') ||
	            src.match('/office_phone.PNG') ||
	            src.match('/birthday.png') ||
	            src.match('/business.PNG') ||
	            src.match('/gender.png') ||
	            src.match('/icons/dropdown-arrow.png') ||
	            src.match('/icons/eyeball.png') ||
	            src.match('/icons/clock-button.png') ||
	            src.match('/icons/save.png') ||
                src.match('/clear-checklist.png') ||
                src.match('/home_phone.PNG') ||
                src.match('/cell_phone.PNG') ||
                src.match('/project-path.png') ||
                src.match('/id-card.png') ||
                src.match('/job.png') ||
                src.match('/icons/recurring.png') ||
                src.match('/icons/range.png') ||
                src.match('/icons/tagging.png') ) {
	            if ( !self.hasClass('white-color') && !self.hasClass('black-color') ) {
	                self.addClass('theme-color-icon');
	            }
	        }
	    }
    });
}
function initInputs(container) {
	initIconColors();
	
	if(container == undefined) {
		container = 'body';
	}
	var selectors = container + ' textarea';
	if($(selectors+':not(.noMceEditor):not([class*=tinyMCEInit])').length > 0) {
		var initClass = 'tinyMCEInit'+(tinyInitI++);
		$(selectors+':not(.noMceEditor):not([class*=tinyMCEInit])').addClass(initClass);
		var initSelector = selectors+'.'+initClass;
		try {
			tinyMCE.remove(initSelector);
		} catch (err) {}
		if(isMobile.any()) {
			//If mobile, don't include contextmenu in plugins so the user can copy and paste text
			tinymce.init({
				setup: function(editor) {
					editor.on('blur', function(e) {
						this.save();
						$(this.getElement()).change();
					}).on('keyup', function(e) {
						this.save();
						$(this.getElement()).keyup();
					}).on('focus', function(e) {
						$(this.getElement()).focus();
					});
				},
				relative_urls: false,
				remove_script_host : false,
				convert_urls : true,
				selector: initSelector,
				theme: "modern",
				external_plugins: {"nanospell": "../tinymce/plugins/nanospell/plugin.js"},
				nanospell_server: "php",
				plugins: [
					"advlist autolink lists link image charmap print preview hr anchor pagebreak",
					"searchreplace wordcount visualblocks visualchars code fullscreen",
					"insertdatetime media nonbreaking save table directionality",
					"emoticons template paste textcolor colorpicker textpattern"
				],
				textpattern_patterns: [
					{start: '*', end: '*', format: 'italic'},
					{start: '**', end: '**', format: 'bold'},
					{start: '=', format: 'h1'},
					{start: '==', format: 'h2'},
					{start: '===', format: 'h3'},
					{start: '====', format: 'h4'},
					{start: '=====', format: 'h5'},
					{start: '======', format: 'h6'},
					{start: '1. ', cmd: 'InsertOrderedList'},
					{start: '* ', cmd: 'InsertUnorderedList'},
					{start: '- ', cmd: 'InsertUnorderedList'}
				],
				menubar: false,
				toolbar: false,
				statusbar: false,
				image_advtab: true,
				templates: [
					{title: 'Test template 1', content: 'Test 1'},
					{title: 'Test template 2', content: 'Test 2'}
				]
			});
		} else {
			try {
				if($(initSelector+':not(.no_tools)').length > 0) {
					tinymce.init({
						setup: function(editor) {
							editor.on('blur', function(e) {
								this.save();
								$(this.getElement()).change();
							}).on('keyup', function(e) {
								this.save();
								$(this.getElement()).keyup();
							}).on('focus', function(e) {
								$(this.getElement()).focus();
							});
						},
						relative_urls: false,
						remove_script_host : false,
						convert_urls : true,
						selector: initSelector+':not(.no_tools)',
						theme: "modern",
						external_plugins: {"nanospell": "../tinymce/plugins/nanospell/plugin.js"},
						nanospell_server: "php",
						plugins: [
							"advlist autolink lists link image charmap print preview hr anchor pagebreak",
							"searchreplace wordcount visualblocks visualchars code fullscreen",
							"insertdatetime media nonbreaking save table "+(no_tools ? "" : "contextmenu")+" directionality",
							"emoticons template paste textcolor colorpicker textpattern"
						],
						textpattern_patterns: [
							{start: '*', end: '*', format: 'italic'},
							{start: '**', end: '**', format: 'bold'},
							{start: '=', format: 'h1'},
							{start: '==', format: 'h2'},
							{start: '===', format: 'h3'},
							{start: '====', format: 'h4'},
							{start: '=====', format: 'h5'},
							{start: '======', format: 'h6'},
							{start: '1. ', cmd: 'InsertOrderedList'},
							{start: '* ', cmd: 'InsertUnorderedList'},
							{start: '- ', cmd: 'InsertUnorderedList'}
						],
						menubar: no_tools ? false : true,
						statusbar: no_tools ? false : true,
						toolbar: no_tools ? false : true,
						toolbar1: no_tools ? false : "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
						toolbar2: no_tools ? false : "print preview media | forecolor backcolor emoticons",
						image_advtab: true,
						templates: [
							{title: 'Test template 1', content: 'Test 1'},
							{title: 'Test template 2', content: 'Test 2'}
						]
					});
				}
				if($(initSelector+'.no_tools').length > 0) {
					tinymce.init({
						setup: function(editor) {
							editor.on('blur', function(e) {
								this.save();
								$(this.getElement()).change();
							}).on('keyup', function(e) {
								this.save();
								$(this.getElement()).keyup();
							}).on('focus', function(e) {
								$(this.getElement()).focus();
							});
						},
						relative_urls: false,
						remove_script_host : false,
						convert_urls : true,
						selector: initSelector+'.no_tools',
						theme: "modern",
						external_plugins: {"nanospell": "../tinymce/plugins/nanospell/plugin.js"},
						nanospell_server: "php",
						plugins: [
							"advlist autolink lists link image charmap print preview hr anchor pagebreak",
							"searchreplace wordcount visualblocks visualchars code fullscreen",
							"insertdatetime media nonbreaking save table directionality",
							"emoticons template paste textcolor colorpicker textpattern"
						],
						textpattern_patterns: [
							{start: '*', end: '*', format: 'italic'},
							{start: '**', end: '**', format: 'bold'},
							{start: '=', format: 'h1'},
							{start: '==', format: 'h2'},
							{start: '===', format: 'h3'},
							{start: '====', format: 'h4'},
							{start: '=====', format: 'h5'},
							{start: '======', format: 'h6'},
							{start: '1. ', cmd: 'InsertOrderedList'},
							{start: '* ', cmd: 'InsertUnorderedList'},
							{start: '- ', cmd: 'InsertUnorderedList'}
						],
						menubar: false,
						statusbar: false,
						toolbar: false,
						toolbar1: false,
						toolbar2: false
					});
				}
			} catch (err) { }
		}
	}

    $( container + ' ' + ".datepicker[data-min-date][data-max-date]" ).each(function() {
		var min_date = $(this).data('min-date');
		var max_date = $(this).data('max-date');
		$(this).datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1920:2025',
			dateFormat: 'yy-mm-dd',
			minDate: min_date,
			maxDate: max_date
		});
	});
    $( container + ' ' + ".datepicker[data-min-date]" ).not("[data-max-date]").each(function() {
		var min_date = $(this).data('min-date');
		$(this).datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1920:2025',
			dateFormat: 'yy-mm-dd',
			minDate: min_date
		});
	});
    $( container + ' ' + ".datepicker[data-max-date]" ).not("[data-min-date]").each(function() {
		var max_date = $(this).data('max-date');
		$(this).datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1920:2025',
			dateFormat: 'yy-mm-dd',
			maxDate: max_date
		});
	});
    $( container + ' ' + ".datepicker").not("[data-min-date],[data-max-date]" ).each(function() {
		$(this).datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1920:2025',
			dateFormat: 'yy-mm-dd'
		});
	});

	$( container + ' ' + ".datepickernoyear").each(function() {
		$(this).datepicker({
			changeMonth: true,
			changeYear: false,
			dateFormat: 'mm-dd'
		}).focus(function () {
			$('.ui-datepicker-year').hide();
		});
	});

    $( container + ' ' + ".datefuturepicker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '1920:2018',
		dateFormat: 'yy-mm-dd',
		minDate: 0
    });

	$(container + ' ' + '.dateandtimepicker').datetimepicker({
		controlType: 'select',
		changeMonth: true,
		changeYear: true,
		yearRange: (new Date().getFullYear - 10) + ':' + (new Date().getFullYear + 5),
		dateFormat: 'yy-mm-dd',
		timeFormat: time_format_style,
		minuteGrid: 15,
		hourMin: 0,
		hourMax: 23,
		//minDate: 0
	});

	$(container + ' ' + '.datetimepicker').timepicker({
		controlType: 'select',
		oneLine: true,
		timeFormat: time_format_style
	});
	$(container + ' ' + '.datetimepicker-5').timepicker({
		controlType: 'select',
		oneLine: true,
		stepMinute: 5,
		timeFormat: time_format_style
	});
	$(container + ' ' + '.datetimepicker-10').timepicker({
		controlType: 'select',
		oneLine: true,
		stepMinute: 10,
		timeFormat: time_format_style
	});
	$(container + ' ' + '.datetimepicker-15').timepicker({
		controlType: 'select',
		oneLine: true,
		stepMinute: 15,
		timeFormat: time_format_style
	});
	$(container + ' ' + '.datetimepicker-20').timepicker({
		controlType: 'select',
		oneLine: true,
		stepMinute: 20,
		timeFormat: time_format_style
	});
	$(container + ' ' + '.datetimepicker-30').timepicker({
		controlType: 'select',
		oneLine: true,
		stepMinute: 30,
		timeFormat: time_format_style
	});
	$(container + ' ' + '.datetimepicker-60').timepicker({
		controlType: 'select',
		oneLine: true,
		stepMinute: 60,
		timeFormat: time_format_style
	});
	$(container + ' ' + '.datetimepicker-24h').timepicker({
		controlType: 'select',
		oneLine: true,
		timeFormat: 'HH:mm'
	});
	$(container + ' ' + '.datetimepickerseconds').timepicker({
		controlType: 'select',
		oneLine: true,
		timeFormat: time_format_seconds
	});
	$(container + ' ' + '.datetimepickerseconds-24h').timepicker({
		controlType: 'select',
		oneLine: true,
		timeFormat: 'HH:mm:ss'
	});
	$(container+' .datetimepicker-60,'+container+' .datetimepicker-30,'+container+' .datetimepicker-20,'+container+' .datetimepicker-15,'+container+' .datetimepicker-10,'+container+' .datetimepicker-5,'+container+' .datetimepicker').each(function() {
		if(this.value > 0 && this.value.length == 4) {
			this.value = this.value.substr(0,2)+':'+this.value.substr(2);
		}
		this.value = formatDateTime('2000-01-01 '+this.value,time_format_style);
	});
	$(container+' .datetimepickerseconds').each(function() {
		this.value = formatDateTime('2000-01-01 '+this.value,time_format_seconds);
	});
	$(container+' .dateandtimepicker').each(function() {
		this.value = formatDateTime(this.value,'Y-m-d '+time_format_style);
	});
	$(container + ' ' + '[class*=datetimepicker][data-datetimepicker-mintime]').each(function() {
		if($(this).data('datetimepicker-mintime') != undefined && $(this).data('datetimepicker-mintime') != '') {
			var currenttime = $(this).val();
			$(this).timepicker('option', {'minTime': $(this).data('datetimepicker-mintime')});
			$(this).val(currenttime);
		}
	});
	$(container + ' ' + '[class*=datetimepicker][data-datetimepicker-maxtime]').each(function() {
		if($(this).data('datetimepicker-maxtime') != undefined && $(this).data('datetimepicker-maxtime') != '') {
			var currenttime = $(this).val();
			$(this).timepicker('option', {'maxTime': $(this).data('datetimepicker-maxtime')});
			$(this).val(currenttime);
		}
	});
	
	$(container + ' ' + '.timepicker').timepicker({
		hour: 0,
		minute: 0,
		showSecond: false,
		hourMax: 24,
		timeOnly: true,
		timeInput: true,
		currentText: ''
	});
	$(container + ' ' + '.timepicker-5').timepicker({
		hour: 0,
		minute: 0,
		showSecond: false,
		hourMax: 24,
		stepMinute: 5,
		timeOnly: true,
		timeInput: true,
		currentText: ''
	});
	$(container + ' ' + '.timepicker-max-5').timepicker({
		hour: 0,
		minute: 0,
		showSecond: false,
		hourMax: 100,
		stepMinute: 5,
		timeOnly: true,
		timeInput: true,
		currentText: ''
	});
	$(container + ' ' + '.timepicker-10').timepicker({
		hour: 0,
		minute: 0,
		showSecond: false,
		hourMax: 24,
		stepMinute: 10,
		timeOnly: true,
		timeInput: true,
		currentText: ''
	});
	$(container + ' ' + '.timepicker-15').timepicker({
		hour: 0,
		minute: 0,
		showSecond: false,
		hourMax: 24,
		stepMinute: 15,
		timeOnly: true,
		timeInput: true,
		currentText: ''
	});
	$(container + ' ' + '.timepicker-20').timepicker({
		hour: 0,
		minute: 0,
		showSecond: false,
		hourMax: 24,
		stepMinute: 20,
		timeOnly: true,
		timeInput: true,
		currentText: ''
	});
	$(container + ' ' + '.timepicker-30').timepicker({
		hour: 0,
		minute: 0,
		showSecond: false,
		hourMax: 24,
		stepMinute: 30,
		timeOnly: true,
		timeInput: true,
		currentText: ''
	});
	
	var config = {
		'.chosen-select'           : {},
		'.chosen-select-deselect'  : {allow_single_deselect:true},
		'.chosen-select-no-single' : {disable_search_threshold:10},
		'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
		'.chosen-select-width'     : {width:"95%"}
	}
	for (var selector in config) {
		// $(container + ' ' + selector).chosen(config[selector]);
		$(container + ' ' + selector).select2({
		    placeholder: "Select an Option",
			width: '100%',
			allowClear: true
		});
	}
	
	$("#info_toggle").off('click').click(function(){
		$(".popover-examples").toggle();
		// var toggleBeforeClick = $(".popover-examples").is(":visible") ? 1 : 0;
		var toggleBeforeClick = $('#info_toggle_state').val();
		Cookies.set('infoToggle', ($('#info_toggle_state').val() == 1 ? true : false), {expires: 86400});
		var toggleState = $('#info_toggle_state').val();
		if(toggleState == 1) {
			$('.switch_info_on').show();
			$('.switch_info_off').hide();
			$('#info_toggle_state').val(0);
		} else {
			$('.switch_info_off').show();
			$('.switch_info_on').hide();
			$('#info_toggle_state').val(1);
		}
		$.ajax({
			type: "GET",
			url: "/ajax_all.php?fill=info_toggle_state&state="+toggleState,
			dataType: "html",
			success: function(response){
				//location.reload();
			}
		});
		$(window).resize();
	});
	
	$('.scalable').resizable({
		handles: 'w',
		resize: function() {
			$('.scalable').css('left','');
			$('body').off('mouseup');
			$('body').mouseup(function(e) {
				$('body').off('mouseup');
				var percentage = Math.round(((window.innerWidth - e.pageX) / window.innerWidth) * 100);
				$.ajax({
					url: '/ajax_all.php?fill=page_options',
					method: 'POST',
					data: {
						field: 'scale_width',
						value: percentage,
						path: window.location.pathname
					}
				});
			});
		}
	});

	if ($(window).width() > 479) {
		$('.scalable').off('click').click(function(e) {
			var div = $(this);
			if(div.hasClass('collapsed')) {
				if(e.pageX > $('.scalable').offset().left && e.pageX < $('.scalable').offset().left + 20) {
					$.ajax({
						url: '/ajax_all.php?fill=get_scale_width&php_self='+window.location.pathname,
						method: 'GET',
						dataType: 'html',
						success: function(response) {
							div.css('width', response);
							div.toggleClass('collapsed');
						}
					});
				}
			} else {
				if(e.pageX > $('.scalable').offset().left + 5 && e.pageX < $('.scalable').offset().left + 23) {
					div.css('width', '0');
					div.toggleClass('collapsed');
				}
			}
		});
	}

	// Chosen touch support
	// if ($(container + ' ' + '.chosen-container').length > 0) {
	// 	$(container + ' ' + '.chosen-container').on('touchstart', function(e){
	// 		e.stopPropagation(); e.preventDefault();
	// 		// Trigger the mousedown event
	// 		$(this).trigger('mousedown');
	// 	});
	// }
}

function formatDateTime(time_string, time_format) {
	time_string = time_string.replace('undefined','');
	if(time_string.length < 12) {
		return '';
	}
	time = new Date(time_string);
	if(time == 'Invalid Date') {
		return time_string;
	}
	time_format = time_format.replace('HH',pad(time.getHours(),2));
	time_format = time_format.replace('H',time.getHours());
	time_format = time_format.replace('hh',pad(time.getHours() == 0 ? '12' : (time.getHours() > 12 ? time.getHours() - 12 : time.getHours()),2));
	time_format = time_format.replace('h',time.getHours() == 0 ? '12' : (time.getHours() > 12 ? time.getHours() - 12 : time.getHours()));
	time_format = time_format.replace('mm',pad(time.getMinutes(),2));
	time_format = time_format.replace('ss',pad(time.getSeconds(),2));
	time_format = time_format.replace('Y',pad(time.getFullYear(),2));
	time_format = time_format.replace('m',pad(time.getMonth() + 1,2));
	time_format = time_format.replace('d',pad(time.getDate(),2));
	time_format = time_format.replace('TT',time.getHours() >= 12 ? 'PM' : 'AM');
	time_format = time_format.replace('tt',time.getHours() >= 12 ? 'pm' : 'am');
	return time_format;
}
function pad(str, length) {
	str = str.toString();
	while(str.length < length) {
		str = '0'+str;
	}
	return str;
}

function round2Fixed(value) {
  value = +value;

  if (isNaN(value))
	return NaN;

  // Shift
  value = value.toString().split('e');
  value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2)));

  // Shift back
  value = value.toString().split('e');
  return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
}

function getParameterByName(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}

function resetChosen(objects, setting) {
	if(setting == undefined) {
		setting = {allow_single_deselect:true};
	}
	// objects.removeClass("chzn-done").css("display", "block").next().remove();
	// objects.chosen(setting);
	objects.parent().find('.select2').remove();
	objects.select2({
	    placeholder: "Select an Option",
		width: '100%',
		allowClear: true
	});
}

function alterIframes(record)
{
	record.forEach(function(change) {
		var src = change.target.src;
		if(src.indexOf('mode=iframe') === -1) {
			src = src + '&mode=iframe';
		}
		if(src.indexOf('?') === -1) {
			src = src.replace('&','?');
		}
		if(src !== change.target.src) {
			change.target.src = src;
		}
	});
}
function overlayIFrame(url, no_reload) {
	$('.iframe_overlay .iframe iframe').height(0);
	$('.iframe_overlay .iframe .iframe_loading').show();
	$('.iframe_overlay .iframe iframe').prop('src',url);
	$('.iframe_overlay .iframe').height($('.iframe_overlay').nextAll('.container').height() + 90);
	$('.iframe_overlay').show();
	$('.iframe_overlay .iframe iframe').off('load').load(function() {
		var container_height = $('.main-screen').closest('.container').height();
		var iframe_height = $($('.iframe_overlay iframe').get(0).contentWindow.document).height();
		var height = iframe_height > container_height ? iframe_height : container_height;
		$('.iframe_overlay iframe').height(height);
		$('.iframe_overlay').height(height);
		$($('.iframe_overlay iframe').get(0).contentWindow.document).find('body').css('overflow-x','hidden');
		$('.iframe_overlay .iframe .iframe_loading').hide();
		$(this).off('load').load(function() {
			$('.iframe_overlay').hide();
			$(this).off('load').attr('src', '/blank_loading_page.php');
			if(!no_reload) {
				window.location.reload();
			}
		});
	});
}

function overlayIFrameDiv(url, no_reload) {
	$('.iframe_overlay .iframe iframe').height(0);
	$('.iframe_overlay .iframe .iframe_loading').show();
	$('.iframe_overlay .iframe iframe').prop('src',url);
	$('.iframe_overlay .iframe').height($('.main-screen').first().height());
	$('.iframe_overlay').show();
	$('.iframe_overlay .iframe iframe').off('load').load(function() {
		var container_height = $('.main-screen').first().height() + 20;
		$('.iframe_overlay iframe').height(container_height);
		$('.iframe_overlay').height(container_height);
		$($('.iframe_overlay iframe').get(0).contentWindow.document).find('body').css('overflow-x','hidden');
		$('.iframe_overlay .iframe .iframe_loading').hide();
		$(this).off('load').load(function() {
			$('.iframe_overlay').hide();
			$(this).off('load').attr('src', '/blank_loading_page.php');
			if(!no_reload) {
				window.location.reload();
			}
		});
	});
}

function overlayIFrameSlider(url, width, no_confirm, no_reload, height, change_close, force_open = '') {
	var target = '';
	if(window.event != undefined) {
		target = window.event.target;
	}
	if(!$(target).hasClass('no-slider') || force_open == 'true') {
		var iframe = $('.iframe_overlay');
		if(window.top != window) {
			var baseIframe = $(window.top.document).find('.iframe_overlay');
			iframe = baseIframe.clone();
			baseIframe.after(iframe);
		}
        //$(iframe).contents().find('body').css('background-color', 'red');
        if(width == undefined || width == 'auto') {
			width = '50%';
		}
		if(height == undefined || height == '' || height == 'auto' || $(document).width() < 768) {
			height = 'auto';
			iframe.height($('html body').height() - $('#nav:visible').height() - $('#footer:visible').height());
		}
		iframe.find('.iframe').css('position', 'relative');
		iframe.find('.iframe').css('left', '100%');
		iframe.find('.iframe').css('float', 'right');
		iframe.find('.iframe').css('width', width);
		iframe.find('.iframe').css('min-width', '25em');
		iframe.find('.iframe').css('max-width', '100%');
		iframe.find('.iframe .iframe_loading').show();
		$('.hide_on_iframe_overlay').hide();
		iframe.find('.iframe iframe').prop('src',url+(url.indexOf('?') === -1 ? '?' : '&')+'mode=iframe');
		if(height != 'auto') {
			iframe.find('.iframe iframe').height(0);
			iframe.find('.iframe').height(height);
		}
		iframe.show();
		iframe.find('.iframe iframe').off('load').load(function() {
			iframe_path = this.contentWindow.location.pathname;
			iframe.off('click').click(function() {
				if(no_confirm || confirm('Closing out of this window will discard your changes. Are you sure you want to close the window?')) {
					$('.hide_on_iframe_overlay').show();
					if(window.top != window) {
						iframe.remove();
					} else {
						iframe.hide();
						$(this).off('load').attr('src', '/blank_loading_page.php');
					}
					$('html').prop('onclick',null).off('click');
					if(!no_reload) {
						window.location.reload();
					}
					if($(window.document).width() < 768) {
						iframe.closest('.container').css('min-height', '');
					}
				}
			});
			if(height != 'auto') {
				iframe.find('iframe').height(height);
				iframe.height(height);
			}
			if($(window.document).width() < 768) {
				iframe_height = $(iframe.find('iframe').get(0).contentDocument).find('body').innerHeight();
				if(iframe_height > iframe.height()) {
					iframe.height(iframe_height);
				}
				iframe.css('padding-bottom', 0);
				iframe.closest('.container').css('min-height', iframe_height);
			}
			iframe.find('.iframe .iframe_loading').hide();
			$(this).off('load').load(function() {
				if(!change_close || change_close == undefined || this.contentWindow.location.pathname != iframe_path) {
					$('.hide_on_iframe_overlay').show();
					if(window.top != window) {
						iframe.hide();
						setTimeout(function() {
							iframe.remove();
						}, 1000);
					} else {
						iframe.hide();
						$(this).off('load').attr('src', '/blank_loading_page.php');
					}
					if(!no_reload) {
						window.location.reload();
					}
					if($(window.document).width() < 768) {
						iframe.closest('.container').css('min-height', '');
					}
				}
			});
			iframe.find('.iframe iframe').contents().find('html,body').css('height', '100%');
			iframe.find('.iframe iframe').contents().find('html,body').css('overflow', 'auto');
			iframe.find('.iframe iframe').contents().find('html,body').css('-webkit-overflow-scrolling', 'touch');
			iframe.find('.iframe iframe').contents().find('html,body').addClass('innerframe');

			var event = new CustomEvent("overlayIFrameSliderLoad", {
				detail: {
					no_confirm: no_confirm
				}
			});
			window.document.dispatchEvent(event);
		});
		iframe.find('.iframe').animate({left: 0},500);

		var event = new CustomEvent("overlayIFrameSliderInit");
		window.document.dispatchEvent(event);
	}
}

function loadingOverlayShow(div, height, width, fullscreen) {
	if(height == undefined) {
		height = $(div).height();
	}
	if(width == undefined) {
		width = $(div).width();
	}
	if(fullscreen == true) {
		$('.loading_overlay').css('position', 'fixed');
		height = $(window).height();
		width = $(window).width();
	}
	$('.loading_overlay').height(height);
	$('.loading_overlay').width(width);
	$('.loading_overlay').show();
}

function loadingOverlayHide() {
	$('.loading_overlay').height(0);
	$('.loading_overlay').width(0);
	$('.loading_overlay').hide();
}

function initPad() { }
function htmlDecode(str) {
	return $('<div/>').html(str).text();
}

function applyTemplate(select) {
	var target = $(select).nextAll('textarea').first();
	var id = target.attr('id');
	tinyMCE.get(id).setContent(select.value);
}

function collapseStandardSidebar() {
	$('.standard-collapsible').addClass('collapsed');
}
function blockPanelActive(panel) {
	$(panel).find('.panel-heading').first().addClass('active');
	$(panel).find('.panel-heading').first().find(".glyphicon-plus").first().removeClass("glyphicon-plus").addClass("glyphicon-minus");
}
function blockPanelRemoveActive(panel) {
	$(panel).find('.panel-heading').first().removeClass('active');
	$(panel).find('.panel-heading').first().find(".glyphicon-minus").first().removeClass("glyphicon-minus").addClass("glyphicon-plus");	
}
function addTimes(timeA, timeB) {
	var AMtimeA = timeA.toString().split(' ');
	var AMtimeB = timeB.toString().split(' ');
	var timeA = AMtimeA[0].split(':');
	var timeB = AMtimeB[0].split(':');
	if(AMtimeA[1] != undefined && AMtimeA[1].toLowerCase() == 'am' && timeA[0] == '12') {
		timeA[0] = 0;
	} else if(AMtimeA[1] != undefined && AMtimeA[1].toLowerCase() == 'pm') {
		timeA[0] = timeA[0] * 1 + (timeA[0] < 12 ? 12 : 0);
	}
	if(AMtimeB[1] != undefined && AMtimeB[1].toLowerCase() == 'am' && timeB[0] == '12') {
		timeB[0] = 0;
	} else if(AMtimeB[1] != undefined && AMtimeB[1].toLowerCase() == 'pm') {
		timeB = timeB[0] * 1 + (timeA[0] < 12 ? 12 : 0);
	}
	for(var i = 0; i < 2; i++) {
		if(isNaN(timeA[i])) {
			timeA[i] = 0;
		}
		if(isNaN(timeB[i])) {
			timeB[i] = 0;
		}
		timeA[i] = timeA[i] * 1 + timeB[i] * 1;
		while(i > 0 && timeA[i] > 60) {
			timeA[i - 1]++;
			timeA[i] -= 60;
		}
		if(i > 0) {
			timeA[i] = timeA[i].toString().padStart(2,'0');
		}
	}
	if(timeA[0] > 11 && AMtimeA[1] != '' && AMtimeA[1] != undefined) {
		AMtimeA[1] = 'pm';
		if(timeA[0] > 12) {
			timeA[0] = timeA[0] * 1 - 12;
		}
	}
	return timeA.join(':')+(AMtimeA[1] != undefined ? ' '+AMtimeA[1] : '');
}
function getQueryStringArray(url) {
	var query_string_arr = {};

	if(url.indexOf('?') != -1) {
		url = url.split('?')[1];
	}
	var query_strings = url.split('&');
	query_strings.forEach(function(query_string) {
		if(query_string.indexOf('=') != -1) {
			var pair = query_string.split('=');
			query_string_arr[pair[0]] = pair[1].replace(/\+/g, " ");
		}
	});

	return query_string_arr;
}
function setMinMaxTime(input) {
	if($(input).data('mintime') != undefined) {
		this.setOptions({
			minTime: $(this).data('mintime')
		});
	}
}