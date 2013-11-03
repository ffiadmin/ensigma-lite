function addAgenda(tableID, cellOneStart, cellOneEnd, cellTwoStart, cellTwoEnd, cellThreeStart, cellThreeEnd, cellFourStart, cellFourEnd) {
	var oRows = document.getElementById(tableID).getElementsByTagName('tr');
	var tbl = document.getElementById(tableID);
	var newRow = tbl.insertRow(tbl.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[tbl.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = cellOneStart + currentID + cellOneEnd;
	
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = cellTwoStart + currentID + cellTwoEnd;
	
	var newCell3 = newRow.insertCell(2);
	newCell3.innerHTML = cellThreeStart + currentID + cellThreeEnd;
	
	var newCell4 = newRow.insertCell(3);
	newCell4.innerHTML = cellFourStart + currentID + cellFourEnd;
	
	var newCell5 = newRow.insertCell(4);
	newCell5.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('agenda', '" + currentID + "')\">";
}

function addCategory(tableID, startHTML, middle1HTML, middle2HTML, endHTML, type) {
	var oRows = document.getElementById(tableID).getElementsByTagName('tr');
	var tbl = document.getElementById(tableID);
	var newRow = tbl.insertRow(tbl.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[tbl.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 25;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	var directory = randomstring.toLowerCase();
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = startHTML + currentID + middle1HTML + directory + middle2HTML + currentID + endHTML;
	
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('files', '" + currentID + "')\">";
}

function deleteObject(tableID, rowID) {
	var tbl = document.getElementById(tableID);
	var row = document.getElementById(rowID);
	
	if (tbl.rows.length > 2) {
		if (tableID == "agenda") {
			row.parentNode.removeChild(row);
		} else {
			var removeConfirm = confirm("Warning: Removing this category will remove all files within this category. Continue?");
			
			if (removeConfirm) {
				row.parentNode.removeChild(row);
			}
		}
	} else {
		alert("You must have at least one item in this list");
	}
}