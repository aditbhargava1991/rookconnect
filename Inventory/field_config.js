$(document).ready(function() {
	$("#tab_dashboard").change(function() {
        window.location = 'field_config.php?type=dashboard&tab='+this.value;
	});
	$("#tab_field").change(function() {
        window.location = 'field_config.php?type=field&tab='+this.value;
	});

	$("#acc").change(function() {
        var tabs = $("#tab_field").val();
        window.location = 'field_config.php?type=field&tab='+tabs+'&accr='+this.value;
	});
	$('input.show_category_dropdown').on('change', function() {
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "inventory_ajax_all.php?fill=show_category_dropdown&value="+value,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});
	$('input.inventory_default_select_all').on('change', function() {
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "inventory_ajax_all.php?fill=inventory_default_select_all&value="+value,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});
	$('input.show_digi_count').on('change', function() {
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "inventory_ajax_all.php?fill=show_digi_count&value="+value,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});
	$('select[name="default_digi_count_tab"]').on('change', function() {
		var value = encodeURIComponent(this.value);
		$.ajax({
            type: "GET",
            url: "inventory_ajax_all.php?fill=default_digi_count_tab&value="+value,
            dataType: "html",
            success: function(response){}
		});
	});
	$('input.show_impexp_inv').on('change', function() {
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "inventory_ajax_all.php?fill=show_impexp_inv&value="+value,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});
});