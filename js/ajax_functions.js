var current_fields = [];
var completed_last = function() {}
function statusUnsaved(field) {
	if(typeof(field.name) != 'string') {
		field = this;
	}
	if(this != window && this.value != this.defaultValue && field.value != undefined) {
		current_fields.push(field);
	}
	statusIcon();
	if(this != window && this.value == this.defaultValue) {
		statusDone(field);
	}
}
function statusSaving() {
	$('.statusIcon').prop('src','../img/status_working.gif').prop('title','Saving Changes...').tooltip('destroy');
	initTooltips();
}
function statusDone(field) {
	for(var i = current_fields.length - 1; i >= 0; i--) {
		if(current_fields[i] == field) {
			current_fields.splice(i, 1);
		}
	}
	statusIcon();
}
function statusIcon() {
	setTimeout(function() {
		if(current_fields.length > 0) {
			$('.statusIcon').prop('src','../img/status_incomplete.png').prop('title','Your page has unsaved changes...').tooltip('destroy');
		} else {
			$('.statusIcon').prop('src','../img/status_complete.png').prop('title','All Changes Saved!').tooltip('destroy');
		}
		initTooltips();
	}, 500);
}

var active_field = null;
var saving_field = null;
function unsaved(event) {
	var field = event.target;
	if($(field).is(':focus')) {
		active_field = field;
	} else {
		active_field = null;
	}
	saveIcon();
}
function saveField(event) {
	if(event != undefined && event.target != undefined) {
		current_fields.push(event.target);
	}
	if(saving_field == null && current_fields.length > 0) {
		saving_field = current_fields.shift();
		for(var i = current_fields.length - 1; i >= 0; i--) {
			if(current_fields[i] == saving_field) {
				current_fields.splice(i, 1);
			}
		}
		saveIcon();
		try {
			saveFieldMethod(saving_field);
		} catch (error) { console.log(error); }
	} else if(saving_field == null && current_fields.length == 0) {
		try {
			completed_last();
		} catch (error) { }
		saveIcon();
	} else {
		saveIcon();
	}
}
function doneSaving() {
	saving_field = null;
	saveField();
}
function saveIcon() {
	var icon = $('.statusIcon');
	if(icon.length > 0) {
		if(saving_field == null && active_field != null) {
			icon.prop('src','../img/status_incomplete.png').prop('title','Your page has unsaved changes...').tooltip('destroy');
			initTooltips();
		} else if(saving_field != null) {
			if(icon.attr('src').indexOf('status_working') < 0) {
				icon.prop('src','../img/status_working.gif')
			}
			icon.prop('title','Saving Changes...').tooltip('destroy');
			initTooltips();
		} else if(current_fields.length == 0) {
			setTimeout(function() {
				icon.prop('src','../img/status_complete.png').prop('title','All Changes Saved!').tooltip('destroy');
				initTooltips();
			}, 500);
		}
	}
}