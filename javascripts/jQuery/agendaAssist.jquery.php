<?php
//Include the parent script
	require_once('../../Connections/connDBA.php');
	
//Generate the JSON array of possible names
	$returnArray = array("Anyone", "Everyone");
	$namesGrabber = query("SELECT * FROM `users` ORDER BY `firstName` ASC", "raw");
	
	while($names = mysql_fetch_array($namesGrabber)) {
		array_push($returnArray, $names['firstName'] . " " . $names['lastName']);
	}
	
	$returnArray = json_encode($returnArray);
	
//Output this as a JavaScript file
	header("Content-type: text/javascript");
?>
// jQuery Autocomplete, Multiple Values from: http://jqueryui.com/demos/autocomplete/multiple.html

/*
Auto-completion UI
----------------------------------------------
*/
var names = <?php echo $returnArray; ?>;

function split(val) {
    return val.split(/,\s*/);
}

function extractLast(term) {
    return split(term).pop();
}

function checkAuto() {
	$('.assignee').bind('keydown', function(event) {
		if (event.keyCode === $.ui.keyCode.TAB && $(this).data('autocomplete').menu.active ) {
			event.preventDefault();
		}
	}).autocomplete({
		minLength : 0,
		source : function(request, response) {
			response($.ui.autocomplete.filter(names, extractLast(request.term)));
		},
		focus : function() {
			return false;
		},
		select : function(event, ui) {
			var terms = split(this.value);
			terms.pop();
			terms.push(ui.item.value);
			terms.push('');
			this.value = terms.join(', ');
			return false;
		}
	});
    
    $('.assignee').live('click', function() {
    	$(this).autocomplete('search', '');
    });
    
    $('.assignee').live('focus', function() {
    	$(this).autocomplete('search', '');
    });
}

/*
Calendar UI
----------------------------------------------
*/
function checkCalendar() {
    $('.dueDate').each(function(){
        $(this).datepicker({
            showButtonPanel : true,
            changeMonth : false,
            numberOfMonths : 1
        });
    });
}

/*
Description UI
----------------------------------------------
*/

$('a.loadDescription').live('click', function() {
	var description = $(this);
    var task = description.parent().parent().children(':eq(1)').children(':input').val();
    var currentInput = description.parent().children(':hidden').val();
    
    if (task == "") {
    	task = "this empty task";
    } else {
    	task = "<strong>" + task + "</strong>";
    }
    
    $('<div title="Add Description"></div>')
    .html('<p>A description may be added to ' + task + '.</p><p><textarea id="description" cols="45" rows="5" style="width:450px;"></textarea></p>')
    .dialog({
    	modal : true,
        draggable : false,
        resizable : false,
        width : 500,
        open : function () {
        	$('#description', this).val(currentInput);
        },
        buttons : {
        	'Submit' : function() {
            	var newInput = $('#description', this).val();
                
                if (newInput == "") {
                	description.removeClass('description').addClass('noDescription');
                    description.removeAttr('onmouseover').attr('onmouseover', 'Tip("Add description")');
                } else {
            		description.removeClass('noDescription').addClass('description');
                    description.removeAttr('onmouseover').attr('onmouseover', 'Tip("Edit description")');
                }
                
                description.parent().children(':hidden').val(newInput); 
            	$(this).dialog('close').remove();
            },
            'Cancel' : function() {
           		$(this).dialog('close').remove();
            }
        }
    });
});

/*
Finalizing
----------------------------------------------
*/
$(document).ready(function() {   
//Private calendar methods
    $('#from, #to').datepicker({
        defaultDate : "+1w",
        showButtonPanel : true,
        changeMonth : false,
        numberOfMonths : 3
	});
    
//Instantiate both the auto-completion and calendar UI components
	checkAuto();
    checkCalendar();
});