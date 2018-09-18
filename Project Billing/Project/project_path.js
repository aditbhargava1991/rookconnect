$(function() {
    $(".connectedSortable").sortable({
		connectWith: ".connectedSortable",
		delay: 500,
		handle: ".drag_handle",
		items: "li:not(.no-sort)",
		update: function( event, ui ) {
			var id = ui.item.attr("id");
			var table_class = ui.item.parents('ul').attr("class");
			var status = table_class.split(' ');
			var type = ui.item.attr('class').split(' ');
			if(type[1] == 'ui-state-checklist' && status[1] != 'Unassigned') {
				$.ajax({    //create an ajax request to load_page.php
					type: "GET",
					url: "project_ajax_all.php?fill=checklist_path_milestone&checklistid="+id+"&status="+status[1],
					dataType: "html",   //expect html to be returned
					success: function(response){
					}
				});
			}
			else if(type[1] == 'ui-state-ticket' && status[1] != 'Unassigned') {
				$.ajax({    //create an ajax request to load_page.php
					type: "GET",
					url: "project_ajax_all.php?fill=ticket_path_milestone&ticketid="+id+"&status="+status[1],
					dataType: "html",   //expect html to be returned
					success: function(response){
					}
				});
			} else {
				$(this).sortable('cancel');
			}
		}
    }).disableSelection();
});

function addQuickItem(sel) {
	var id = sel.id.split(' ');
	
	$.ajax({    //create an ajax request to load_page.php
		data: { projectid: id[1], milestone: id[2], item: sel.value },
		type: "POST",
		url: "project_ajax_all.php?fill=add_milestone_item",
		dataType: "html",   //expect html to be returned
		success: function(response){
			$(sel).parent().parent().children('.ui-state-checklist:last').after(response);
		}
	});
	sel.value = '';
}