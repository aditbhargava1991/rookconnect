function get_addresses(sort_date, equipmentid) {
    define_addresses(sort_date, equipmentid, '', '');
    return;
	// $.ajax({
		// url: '../Calendar/calendar_ajax_all.php?fill=mapping_address',
		// method: 'POST',
		// data: {
			// date: sort_date,
			// equipment: equipmentid
		// },
		// success: function(response) {
			// var start_address = '';
			// var end_address = '';
			// if(response == '') {
				// try {
					// navigator.geolocation.getCurrentPosition(function(position) {
						// $.ajax({
							// url: 'https://maps.googleapis.com/maps/api/geocode/json',
							// method: 'GET',
							// data: {
								// latlng: position.coords.latitude+','+position.coords.longitude,
								// key: geocoder_key
							// },
							// dataType: 'json',
							// success: function(response) {
								// if(response.status == 'OVER_QUERY_LIMIT') {
									// $.ajax({
										// url: '../ajax_all.php?fill=send_email',
										// medthod: 'POST',
										// data: {
											// send_to: 'info@rookconnect.com',
											// subject: 'Geocoding API Over Limit',
											// body: 'This is to let you know that too many Geocoding requests are being sent to the API. You will need to increase the number of available requests. Visit <a href="https://developers.google.com/maps/pricing-and-plans/">Google API Pricing</a> for more details.'
										// }
									// });
									// define_addresses(sort_date, equipmentid, '', '');
								// } else {
									// var address = response.results[0].formatted_address;
									// define_addresses(date, equipmentid, address, address);
								// }
							// }
						// });
					// });
				// } catch(error) { }
			// } else {
				// start_address = response.split('\n');
				// end_address = (start_address[1] == '' ? start_address[0] : start_address[1]);
				// start_address = start_address[0];
			// }
			// define_addresses(sort_date, equipmentid, start_address, end_address);
		// }
	// });
}

function define_addresses(date, equipmentid, origin, destination) {
	overlayIFrameSlider('../Calendar/map_set_addresses.php?date='+date+'&equipment='+equipmentid,'auto',false,true);
    return;
	// overlayIFrameSlider('map_set_addresses.php?origin='+encodeURI(origin)+'&destination='+encodeURI(destination));
	// $('.iframe_overlay .iframe iframe').load(function() {
		// $($('.iframe_overlay iframe').get(0).contentWindow.document).find('.confirm_btn').click(function() {
			// sort_by_map(date, equipmentid, $($('.iframe_overlay iframe').get(0).contentWindow.document).find('[name=origin]').val(), $($('.iframe_overlay iframe').get(0).contentWindow.document).find('[name=destination]').val());
		// });
	// });
}

function sort_by_map(date, equipmentid, origin_address, destination_address, no_order_change, day_start) {
    if((!(equipmentid > 0) || date != (new Date()).getFullYear()+'-'+('0'+(new Date()).getMonth()).substr(-2)+'-'+('0'+(new Date()).getDate()).substr(-2)) && no_order_change !== true) {
        sorting_done = 2;
        return;
    }
	var ticket_addresses = [];
	var tickets = [];
	var stops = [];
	$.ajax({
		url: '../Calendar/calendar_ajax_all.php?fill=get_sortable_tickets',
		method: 'POST',
		data: {
			date: date,
			equipment: equipmentid,
            include_complete: (no_order_change == undefined || no_order_change == false ? true : false)
		},
		dataType: 'text',
		success: function(response) {
			response.split('\n').forEach(function(address) {
				if(address != '') {
					address = address.split('#*#');
					tickets.push(address[0]);
					stops.push(address[1]);
					ticket_addresses.push({location:address[2],stopover:true});
				}
			});
			var mapService = new google.maps.DirectionsService;
            if(origin_address == '' || origin_address == undefined) {
                origin_address = ticket_addresses[0].location;
            }
            if(destination_address == '' || destination_address == undefined) {
                destination_address = ticket_addresses[ticket_addresses.length - 1].location;
            }
            if(origin_address == '' || destination_address == '' || origin_address == undefined || destination_address == undefined) {
                sorting_done = 2;
            } else {
                mapService.route({
                    origin: origin_address,
                    destination: destination_address,
                    travelMode: 'DRIVING',
                    optimizeWaypoints: (no_order_change == undefined || no_order_change == false ? true : false),
                    waypoints: ticket_addresses
                }, function(response, status) {
                    if(status === 'NOT_FOUND') {
                        sorting_done = 2;
                        alert('Please check the addresses you have provided. The map was unable to locate one or more of the addresses, either for the starting address, the ending address, or the pickup or delivery addresses for your sort requests.');
                    } else if(status !== 'OK') {
                        sorting_done = 2;
                        alert('Unable to sort ('+status+'). A note has been sent to support. Please try again later.');
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
                        var stop_order = [];
                        var drive_time = [];
                        var i = 0;
                        response.routes[0].waypoint_order.forEach(function(el) {
                            ticket_order.push(tickets[el]);
                            stop_order.push(stops[el]);
                            drive_time.push(response.routes[0].legs[i++].duration.value);
                        });
                        $.ajax({
                            url: '../Calendar/calendar_ajax_all.php?fill=sort_tickets',
                            method: 'POST',
                            data: {
                                date: date,
                                equipment: equipmentid,
                                start_address: origin_address,
                                end_address: destination_address,
                                ticket_sort: ticket_order,
                                stop_sort: stop_order,
                                drive_time: drive_time,
                                from_current: (no_order_change == undefined || no_order_change == false ? false : true),
                                start_of_day: (day_start != undefined ? day_start : '')
                            },
                            success: function(response) {
                                sorting_done = 2;
                                if(no_order_change == false || no_order_change == undefined) {
                                    window.location.reload();
                                }
                            }
                        });
                    }
                });
            }
		}
	});
}

function get_day_map(date, equipmentid) {
	$.ajax({
		url: '../Calendar/calendar_ajax_all.php?fill=get_ticket_addresses',
		method: 'POST',
		data: {
			date: date,
			equipment: equipmentid
		},
		success: function(response) {
            if(response != '') {
                var waypoints = response.split("\n");
                window.open('https://www.google.com/maps/dir/'+waypoints.join('/').replace(/ /g,'+'),'','postwindow');
            } else {
                alert('No Addresses Found');
            }
		}
	});
}