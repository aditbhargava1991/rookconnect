function get_addresses(sort_date, equipmentid) {
	$.ajax({
		url: 'calendar_ajax_all.php?fill=mapping_address',
		method: 'POST',
		data: {
			date: sort_date,
			equipment: equipmentid
		},
		success: function(response) {
			var start_address = '';
			var end_address = '';
			if(response == '') {
				try {
					navigator.geolocation.getCurrentPosition(function(position) {
						$.ajax({
							url: 'https://maps.googleapis.com/maps/api/geocode/json',
							method: 'GET',
							data: {
								latlng: position.coords.latitude+','+position.coords.longitude,
								key: geocoder_key
							},
							dataType: 'json',
							success: function(response) {
								if(response.status == 'OVER_QUERY_LIMIT') {
									$.ajax({
										url: '../ajax_all.php?fill=send_email',
										medthod: 'POST',
										data: {
											send_to: 'info@rookconnect.com',
											subject: 'Geocoding API Over Limit',
											body: 'This is to let you know that too many Geocoding requests are being sent to the API. You will need to increase the number of available requests. Visit <a href="https://developers.google.com/maps/pricing-and-plans/">Google API Pricing</a> for more details.'
										}
									});
									define_addresses(sort_date, equipmentid, '', '');
								} else {
									var address = response.results[0].formatted_address;
									define_addresses(date, equipmentid, address, address);
								}
							}
						});
					});
				} catch(error) { }
			} else {
				start_address = response.split('\n');
				end_address = (start_address[1] == '' ? start_address[0] : start_address[1]);
				start_address = start_address[0];
			}
			define_addresses(sort_date, equipmentid, start_address, end_address);
		}
	});
}

function define_addresses(date, equipmentid, origin, destination) {
	overlayIFrameSlider('map_set_addresses.php?origin='+encodeURI(origin)+'&destination='+encodeURI(destination));
	$('.iframe_overlay .iframe iframe').load(function() {
		$($('.iframe_overlay iframe').get(0).contentWindow.document).find('.confirm_btn').click(function() {
			sort_by_map(date, equipmentid, $($('.iframe_overlay iframe').get(0).contentWindow.document).find('[name=origin]').val(), $($('.iframe_overlay iframe').get(0).contentWindow.document).find('[name=destination]').val());
		});
	});
}

function sort_by_map(date, equipmentid, origin_address, destination_address) {
	var ticket_addresses = [];
	var tickets = [];
	$.ajax({
		url: 'calendar_ajax_all.php?fill=get_sortable_tickets',
		method: 'POST',
		data: {
			date: date,
			equipment: equipmentid
		},
		dataType: 'text',
		success: function(response) {
			response.split('\n').forEach(function(address) {
				if(address != '') {
					address = address.split('#*#');
					ticket_addresses.push({location:address[1],stopover:true});
					// ticket_addresses.push({location:address[2],stopover:false});
					tickets.push(address[0]);
				}
			});
			var mapService = new google.maps.DirectionsService;
			mapService.route({
				origin: origin_address,
				destination: destination_address,
				travelMode: 'DRIVING',
				optimizeWaypoints: true,
				waypoints: ticket_addresses
			}, function(response, status) {
				if(status === 'NOT_FOUND') {
					alert('Please check the addresses you have provided. The map was unable to locate one or more of the addresses, either for the starting address, the ending address, or the pickup or delivery addresses for your sort requests.');
				} else if(status !== 'OK') {
					alert('Unable to sort. A note has been sent to support. Please try again later.');
					console.log(status);
					$.ajax({
						url: '../ajax_all.php?fill=send_email',
						medthod: 'POST',
						data: {
							send_to: 'info@rookconnect.com',
							subject: 'Google API '+status,
							body: 'This is to let you know that an error occurred while accessing the Google API. The listed error is '+status+'.'
						}
					});
				} else {
					var ticket_order = [];
					response.routes[0].waypoint_order.forEach(function(el) {
						ticket_order.push(tickets[el]);
					});
					$.ajax({
						url: 'calendar_ajax_all.php?fill=sort_tickets',
						method: 'POST',
						data: {
							start_address: origin_address,
							end_address: destination_address,
							ticket_sort: ticket_order
						},
						success: function(response) {
							window.location.reload();
						}
					});
				}
			});
		}
	});
}

function get_day_map(date, equipmentid) {
	$.ajax({
		url: 'calendar_ajax_all.php?fill=get_ticket_addresses',
		method: 'POST',
		data: {
			date: date,
			equipment: equipmentid
		},
		success: function(response) {
			var waypoints = response.split("\n");
			window.open('https://www.google.com/maps/dir/'+waypoints.join('/').replace(/ /g,'+'),'','postwindow');
		}
	});
}