// A jQuery library for manipulating the options for the scheduled email setup

/*
Initialization
--------------------------------------------
*/

//Widen the scope of these variables, for use within the jQuery below and a function
	var day, month, weekMenu, monthMenu, repeat;
	
//Get ready to handle the repeat menu
	var date = new Date();

$(document).ready(function() {
/*
Listen for changes to the first option, and modify the month, week day, repeat dropdown menus accordingly
--------------------------------------------
*/
	$('#day').change(function() {
	//The parent menu being changed
		var parentMenu = $(this);
		var option = $('option:selected', parentMenu);
		
	//The slave menus
		weekMenu = $('#weekDay');
		monthMenu = $('#month');
		repeat = $('#repeat');
		
	//<span> tags showing or hiding menus and text
		var hideDay = $('span.hideDay');
		var monthDetails = $('.monthDetails');
		
	//Get ready to handle the repeat menu
		var repeatOptions = ['forever'];
		repeat.empty();
		convert();
		
	//Show or hide differnet options based on the parent menu's value
		switch(option.attr('value')) {
			case 'day' : 				
			//Hide unnecessary menus and text
				hideDay.hide();
				monthDetails.hide();
				
			//Calculate and build the dates for the repeat menu			
				for(var i = 1; i <= 20; i++) {
				//Increment by one day
					date.setDate(date.getDate() + 1);
					
				//Build the menu options, showing the end date
					if (i == 1) {
						var value = i + ' time (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
					} else {
						var value = i + ' times (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
					}
					
					repeatOptions.push(value);
				}
				
				break;
				
			case 'week' : 		
			//Hide unnecessary menus and text
				hideDay.show();
				monthMenu.hide();
				monthDetails.hide();
				weekMenu.empty();
				
			//Build the week day menu
				var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
				
			//This will select this day of the week
				var selected = date.getDay();
				
				$.each(days, function(index, value) {
					if (index == selected) {
						var append = '<option value="' + value + '" selected="selected">' + value + '</option>';
					} else {
						var append = '<option value="' + value + '">' + value + '</option>';
					}
					
					weekMenu.append(append);
				});
				
			//Calculate and build the dates for the repeat menu				
				for(var i = 1; i <= 20; i++) {
				//Increment by one week
					date.setDate(date.getDate() + 7);
					
				//Build the menu options, showing the end date
					if (i == 1) {
						var value = i + ' time (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
					} else {
						var value = i + ' times (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
					}
					
					repeatOptions.push(value);
				}	
				
				break;
				
			case 'month' : 				
			//Hide unnecessary menus and text
				hideDay.show();
				monthMenu.hide();
				monthDetails.show();
				weekMenu.empty();
				
			//Build the week day menu
				var days = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th', '13th', '14th', '15th', '16th', '17th', '18th', '19th', '20th',' 21st', '22nd', '23rd', '24th', '25th', '26th', '27th', '28th', '29th (or last day if not in month)', '30th (or last day if not in month)', '31st (or last day if not in month)'];
				
			//This will select this day of the month
				var selected = date.getDate();
				
				$.each(days, function(index, value) {
					if (value.replace(/[^0-9]/g, '') == selected) {
						var append = '<option value="' + value + '" selected="selected">' + value + '</option>';
					} else {
						var append = '<option value="' + value + '">' + value + '</option>';
					}
					
					weekMenu.append(append);
				});
				
			//Calculate and build the dates for the repeat menu	
				for(var i = 1; i <= 20; i++) {	
				//Increment	by one month
					date.setMonth(date.getMonth() + 1);
					
				//Build the menu options, showing the end date
					if (i == 1) {
						var value = i + ' time (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
					} else {
						var value = i + ' times (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
					}
					
					repeatOptions.push(value);
				}
				
				break;
				
			case 'year' : 	
			//Hide unnecessary menus and text
				hideDay.show();
				monthMenu.show();
				monthDetails.show();
				weekMenu.empty();
				
			//Build the week day menu			
				var days = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th', '13th', '14th', '15th', '16th', '17th', '18th', '19th', '20th',' 21st', '22nd', '23rd', '24th', '25th', '26th', '27th', '28th', '29th', '30th', '31st'];
			
			//This will select this day of the month
				var selected = date.getDate();
				
				$.each(days, function(index, value) {
					if (value.replace(/[^0-9]/g, '') == selected) {
						var append = '<option value="' + value + '" selected="selected">' + value + '</option>';
					} else {
						var append = '<option value="' + value + '">' + value + '</option>';
					}
					
					weekMenu.append(append);
				});
				
			//Calculate and build the dates for the repeat menu	
				for(var i = 1; i <= 20; i++) {
				//Increment by one year		
					date.setFullYear(date.getFullYear() + 1, month, day);
					
				//Build the menu options, showing the end date
					if (i == 1) {
						var value = i + ' time (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
					} else {
						var value = i + ' times (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
					}
					
					repeatOptions.push(value);
				}
				
				break;
		}
		
	//Add the values constructed above to the repeat menu
		$.each(repeatOptions, function(index, value) {
			var append = '<option value="' + value + '">' + value + '</option>';
			repeat.append(append);
		});
	});

/*
Listen for changes to the month option, and modify the weekday and repeat dropdown menus accordingly
--------------------------------------------
*/
	$('#month').change(function() {
	//The parent menu being changed
		var monthMenu = $('#month');
		var option = $('option:selected', monthMenu);
		
	//The slave menus
		var weekMenu = $('#weekDay');
		repeat = $('#repeat');
		
	//Build the week day menu
		var thirtyOne = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th', '13th', '14th', '15th', '16th', '17th', '18th', '19th', '20th',' 21st', '22nd', '23rd', '24th', '25th', '26th', '27th', '28th', '29th', '30th', '31st'];
		var thirty = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th', '13th', '14th', '15th', '16th', '17th', '18th', '19th', '20th',' 21st', '22nd', '23rd', '24th', '25th', '26th', '27th', '28th', '29th', '30th'];
		var twentyNine = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th', '13th', '14th', '15th', '16th', '17th', '18th', '19th', '20th',' 21st', '22nd', '23rd', '24th', '25th', '26th', '27th', '28th', '29th (28th if not leap year)'];
		
	//Empty its current values
		weekMenu.empty();
		
	//Adjust the number of days accoring to the avaliable days in the month
		switch (option.attr('value')) {
		//31 days
			case 'Janurary' : 
			case 'March' : 
			case 'May' : 
			case 'July' : 
			case 'August' : 
			case 'October' : 
			case 'December' : 
				$.each(thirtyOne, function(index, value) {
					var append = '<option value="' + value + '">' + value + '</option>';
					weekMenu.append(append);
				});
				
				break;
		//30 days
			case 'April' : 
			case 'June' : 
			case 'September' : 
			case 'November' : 
				$.each(thirty, function(index, value) {
					var append = '<option value="' + value + '">' + value + '</option>';
					weekMenu.append(append);
				});
			
				break;
			
		//29 days or less
			case 'February' : 
				$.each(twentyNine, function(index, value) {
					var append = '<option value="' + value + '">' + value + '</option>';
					weekMenu.append(append);
				});
				
				break;
		}
		
	//Calculate and build the dates for the repeat menu	
		var repeatOptions = ['forever'];
		
		convert();
		
		for(var i = 1; i <= 20; i++) {	
		//Increment by one year
			date.setFullYear(date.getFullYear() + 1, month, day);
			
		//Build the menu options, showing the end date
			if (i == 1) {
				var value = i + ' time (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
			} else {
				var value = i + ' times (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
			}
			
			repeatOptions.push(value);
		}
		
	//Empty its current values
		repeat.empty();
		
	//Add the values constructed above to the repeat menu
		$.each(repeatOptions, function(index, value) {
			var append = '<option value="' + value + '">' + value + '</option>';
			repeat.append(append);
		});
	});
	
/*
Listen for changes to the week day option, and modify the repeat dropdown menu accordingly
--------------------------------------------
*/
	$('#weekDay').change(function() {
	//How this will maniuplate the repeat menu depends on the state of the first menu, so grab it as a parent
		var parentMenu = $('#day');
		var option = $('option:selected', parentMenu);
		
	//This menu
		var current = $(this);
		
	//Change the repeat menu based on the parent menu's selection
		switch(option.attr('value')) {
			case 'day' : 
			//No changes needed!
				break;
				
			case 'week' : 
			//Convert the textual day of the week into a JavaScript value
				switch(current.attr('value')) {
					case 'Sunday' : 
					case 'sunday' : 
						var selected = 0; break;
						
					case 'Monday' : 
					case 'monday' : 
						var selected = 1; break;
						
					case 'Tuesday' : 
					case 'tuesday' : 
						var selected = 2; break;
						
					case 'Wednesday' : 
					case 'wednesday' : 
						var selected = 3; break;
						
					case 'Thursday' : 
					case 'thursday' : 
						var selected = 4; break;
						
					case 'Friday' : 
					case 'friday' : 
						var selected = 5; break;
						
					case 'Saturday' : 
					case 'saturday' : 
						var selected = 6; break;						
					
				}
				
			//Reset the date
				date = new Date();
				
			//Get this day of the week
				var day = date.getDay();
				var today = date.getDate();
				
			//Compare the difference from today and the selected day
				var day = day - selected;
				
			//Set the new date, based on the previous comparison
				date.setDate(date.getDate() - day);
				
			//Calculate and build the dates for the repeat menu
				var repeatOptions = ['forever'];
						
				for(var i = 1; i <= 20; i++) {
				//Increment by one week
					if ((today > date.getDate() && i == 1) || i > 1) {
						date.setDate(date.getDate() + 7);
					}
					
				//Build the menu options, showing the end date
					if (i == 1) {
						var value = i + ' time (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
					} else {
						var value = i + ' times (' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + ')';
					}
					
					repeatOptions.push(value);
				}
				
				break;
		}
		
	//Empty its current values
		repeat.empty();
		
	//Add the values constructed above to the repeat menu
		$.each(repeatOptions, function(index, value) {
			var append = '<option value="' + value + '">' + value + '</option>';
			repeat.append(append);
		});
	});
});

//Convert the month and week day menus to numbers, if possible
function convert() {
//Reset the date variable
	date = new Date()
	
//Strip all non-numeric variables out of the day variable
	day = weekMenu.val().replace(/[^0-9]/g, '');
	
//Convert the textual month value into a numeric JavaScript value
	switch(monthMenu.val()) {
		case 'Janurary' : 
			month = 0; break;
			
		case 'February' : 
			month = 1; break;
			
		case 'March' : 
			month = 2; break;
			
		case 'April' : 
			month = 3; break;
			
		case 'May' : 
			month = 4; break;
			
		case 'June' : 
			month = 5; break;
			
		case 'July' : 
			month = 6; break;
			
		case 'August' : 
			month = 7; break;
			
		case 'September' : 
			month = 8; break;
			
		case 'October' : 
			month = 9; break;
			
		case 'November' : 
			month = 10; break;
			
		case 'December' : 
			month = 11; break;
	}
}