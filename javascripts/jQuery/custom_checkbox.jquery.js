//Create a fake checkbox and submit this data in realtime when clicked

$(document).ready(function() {
	$('a.checkbox').click(function() {
		var object = $(this);
		
		if (object.hasClass('visible') || object.hasClass('checked')) {
			if (object.hasClass('visible')) {
				var add = 'invisible';
				var remove = 'visible';
			} else {
				var add = 'unchecked';
				var remove = 'checked';
			}
			
			object.addClass(add);
			object.removeClass(remove);
			
			$.ajax({
				url : document.location.href,
				data : {
					action : 'setAvaliability',
					id : object.attr('id')
				},
				type : 'POST'
			});
		} else {
			if (object.hasClass('invisible')) {
				var add = 'visible';
				var remove = 'invisible';
			} else {
				var add = 'checked';
				var remove = 'unchecked';
			}
			
			object.addClass(add);
			object.removeClass(remove);
			
			$.ajax({
				url : document.location.href,
				data : {
					action : 'setAvaliability',
					id : object.attr('id'),
					option : 'on'
				},
				type : 'POST'
			});
		}
	});
});