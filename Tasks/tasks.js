$(document).ready(function() {
	tasksInit();
});
function tasksInit() {
	$("#task_path").change(function() {
		var task_path = $("#task_path").val();
		$.ajax({
			type: "GET",
			url: "task_ajax_all.php?fill=task_path_milestone&task_path="+task_path,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#task_milestone_timeline').html(response);
				$("#task_milestone_timeline").trigger("change.select2");
			}
		});
	});
	
    $( ".connectedSortable" ).sortable({
		connectWith: ".connectedSortable",
		handle: ".drag_handle",
		items: "li:not(.no-sort)",
		update: function( event, ui ) {
			var taskid = ui.item.attr("id"); //Done
			var table = ui.item.data('table');
			var id_field = ui.item.data('id-field');
			var table_class = ui.item.parent().attr("class");
			var status = table_class.split(' ')[2];
			
			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "task_ajax_all.php?fill=tasklist&tasklistid="+taskid+"&table="+table+"&id_field="+id_field+"&task_milestone_timeline="+status,
				dataType: "html",   //expect html to be returned
				success: function(response){
				}
			});
		}
    }).disableSelection();

	DoubleScroll(document.getElementById('scrum_tickets'));
}

function DoubleScroll(element) {
	$('.double_scroll_div').remove();
	var scrollbar= document.createElement('div');
	scrollbar.className = 'double_scroll_div';
	scrollbar.appendChild(document.createElement('div'));
	scrollbar.style.overflow= 'auto';
	scrollbar.style.overflowY= 'hidden';
	scrollbar.style.width= '';
	scrollbar.firstChild.style.width= element.scrollWidth+'px';
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

function changeEndAme(sel) {
	$(this).focus();

	$(this).prop("disabled",false);
	var stage = sel.value;
	var typeId = sel.id;
	
	var tasklistid = typeId.split(' ');

	var status = tasklistid[1];
	var task_path = tasklistid[2];
	var taskboardid = tasklistid[3];

	var stage = stage.replace(" ", "FFMSPACE");
	var stage = stage.replace("&", "FFMEND");
	var stage = stage.replace("#", "FFMHASH");

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "task_ajax_all.php?fill=add_task&task_milestone_timeline="+status+"&task_path="+task_path+"&heading="+stage+"&taskboardid="+taskboardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}

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