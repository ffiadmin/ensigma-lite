function toggleTypeDiv(field) {
	if (field && document.getElementById('contentAdvanced') && document.getElementById('contentMessage')) {
		switch (field) {
			case "Custom Content" : document.getElementById('contentAdvanced').className = "contentShow"; 
									document.getElementById('contentMessage').className = "contentHide";
									break;
			case "Login" : document.getElementById('contentAdvanced').className = "contentHide"; 
						   document.getElementById('contentMessage').className = "noResults contentShow";
						   break;
			case "Register" : document.getElementById('contentAdvanced').className = "contentShow"; 
							  document.getElementById('contentMessage').className = "contentHide";
							  break;
		}
	}
}