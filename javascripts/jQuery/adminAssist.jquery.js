//A jQuery listener to build a dialog box containing comments for a particular task, and to manipulate the progress bar of an agenda

$(document).ready(function() {
//Open the comments dialog
	$('a.description').click(function() {
		var description = $(this).parent().parent();
		var title = description.children('td.taskName').text();
		var comments = description.children('td.description').children('div.contentHide').text();
		var dueDate = description.children('td.dueDate').text();
		var priority = description.children('td.priority').html();
		
		$('<div title="' + title + '"></div>')
		.html('<div class="layoutControl"><div class="halfLeft"><span style="font-size:10px">Due date: ' + dueDate + '</span></div><div class="halfRight"><div align="right"><span style="font-size:10px">Priority: ' + priority + '</span></div></div></div><p>' + comments + '</p>')
		.dialog({
			modal : true,
			width : 600,
			height : 500,
			buttons : {
				'Close' : function() {
					$(this).dialog('close').remove();
				}
			}
		});
	});
	
//Build the progress bar for an agenda
	$('div.progressBar').each(function() {
		var progressBar = $(this);
		var totalItems = progressBar.parent().children('.totalItems').val();
		var totalComplete = progressBar.parent().children('.totalComplete').val();		
		
		progressBar.progressbar({
			value : Math.round((totalComplete / totalItems) * 100)
		});
		
		progressBar.parent().children('.percentage').text(Math.round((totalComplete / totalItems) * 100) + '% complete');
	});
	
//Manipulate the progress bar when an item is checked or unchecked
	$('a.checkbox').click(function() {
	//Set all necessary variables
		var check = $(this);
		var outside = check.parent().parent().parent().parent().parent().parent();
		var progressBar = outside.children('.progressBar');
		var percentage = outside.children('.percentage');
		
		var totalItems = outside.children('.totalItems').val();
		
	//Update the hidden fields containing the values
		var totalComplete = outside.children('.totalComplete');
		
		if (check.hasClass('checked')) {
			totalComplete.val(parseInt(totalComplete.val()) + 1);
		} else {
			totalComplete.val(parseInt(totalComplete.val()) - 1);
		}
		
	//Update the progress bar
		var totalComplete = outside.children('.totalComplete').val();
		
		progressBar.progressbar({
			value : Math.round((totalComplete / totalItems) * 100)
		});
		
	//Update the percentage text
		percentage.text(Math.round((totalComplete / totalItems) * 100) + '% complete');
	});
});