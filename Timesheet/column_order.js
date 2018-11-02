$(function() {
    $( ".tileSort" ).sortable({
		beforeStop: function(e, li) { li.helper.removeClass('popped-field'); },
		connectWith: ".tileSort",
		delay: 500,
		handle: ".drag_handle",
		items: "li:not(.no-sort)",
		start: function(e, li) {li.helper.addClass('popped-field'); },
		update: function( event, li ) {
			var this_tile = li.item.attr("id");
			var prev_tile = li.item.prev().attr('id');
			if(prev_tile == undefined) {
				prev_tile = '';
			}

			$.ajax({    //create an ajax request to load_page.php
				type: "POST",
				url: "settings_ajax.php?fill=tile_sort",
				data: { current: this_tile, previous: prev_tile },
				dataType: "html",   //expect html to be returned
				success: function(response){
					console.log(response);
				}
			});
		}
    }).disableSelection();
});

$(function() {
    $( "ul.dashboardTiles" ).sortable({
		connectWith: "ul.dashboardTiles",
		delay: 500,
		handle: ".drag_handle",
		items: "li.ui-state-default:not(.no-sort)",
		update: function( event, li ) {
			var this_tile = li.item.data("id");
			var this_board = li.item.data('board');
			var prev_tile = li.item.prev().data('id');
			var board_id = li.item.closest('ul').data('id');
			if(this_board == 'all') {
				var clone = li.item.clone();
				$('ul:contains("All Tile List")').append(clone);
			}
			li.item.data('board', board_id);
			if(board_id == 'all') {
				li.item.remove();
			}
			if(prev_tile == undefined) {
				prev_tile = '';
			}
			if(board_id != undefined) {
				$.ajax({    //create an ajax request to load_page.php
					type: "POST",
					url: "settings_ajax.php?fill=dashboard_sort",
					data: { current: this_tile, previous: prev_tile, dashboard: board_id, source: this_board },
					dataType: "html",   //expect html to be returned
					success: function(response){
						console.log(response);
					}
				});
			}
		}
    }).disableSelection();
});