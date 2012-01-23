var req = new XMLHttpRequest();
req.onreadystatechange = servletCallback;

var req2 = new XMLHttpRequest();
req2.onreadystatechange = servletCallback2;



function selectCell(id, type) {
	if (type != "extracover") {
		
		if (type == "comb") {
			var elem = document.getElementById("table_go").getElementsByTagName(
				"input");
			for ( var i = 0; i < elem.length; i++) {
				elem[i].checked = false;
			}

			var elem = document.getElementById("table_back").getElementsByTagName(
				"input");

			for ( var i = 0; i < elem.length; i++) {
				elem[i].checked = false;
			}
		} else {
			var elem = document.getElementById("table_comb");
			
			if (elem != null) {
				var eleminputs = elem.getElementsByTagName("input");
				
				for ( var i = 0; i < eleminputs.length; i++) {
					eleminputs[i].checked = false;
				}
			}
		}
	

		var elem = document.getElementById("table_" + type).getElementsByTagName(
			"input");

		var str = '';

		var item = id.split("|", 3)[0];

		for ( var i = 0; i < elem.length; i++) {
			var tmpId = elem[i].id;
			var tmpItem = tmpId.split("|", 3)[0];

			if (tmpItem != item) {
				elem[i].checked = false;
			}
		}
	} else {
		if (document.getElementsByName("extracover")[0].checked == false) {
			var agree = confirm("Are you sure to remove the extra cover ??");
			if (agree) {
				document.getElementsByName("extracover")[0].checked = false;
			} else {
				document.getElementsByName("extracover")[0].checked = true;
			}

		}
	}
	
	
	var elemGo = document.getElementById("table_go");

	var elemBack = document.getElementById("table_back");

	var elemComb = document.getElementById("table_comb");

	var selectedId = new Array();

	var j = 0;

	if (elemGo != null) {
		var elemGoInput = elemGo.getElementsByTagName("input");
		for ( var i = 0; i < elemGoInput.length; i++) {
			if (elemGoInput[i].checked == true) {
				selectedId[j++] = elemGoInput[i].value;
			}
		}
	}

	if (elemBack != null) {
		var elemBackInputs = elemBack.getElementsByTagName("input");
		for ( var i = 0; i < elemBackInputs.length; i++) {
			if (elemBackInputs[i].checked == true) {
				selectedId[j++] = elemBackInputs[i].value;
			}
		}
	}

	if (elemComb != null) {
		var elemCombInputs = elemComb.getElementsByTagName("input");
		for ( var i = 0; i < elemCombInputs.length; i++) {
			if (elemCombInputs[i].checked == true) {
				selectedId[j++] = elemCombInputs[i].value;
			}
		}
	}

	if (selectedId.length > 0) {
		refreshSummary(selectedId);
	}

}

function setEnableReturn() {
	var returnWay = document.getElementsByName("isReturn").item(1).checked;
	if (returnWay == true) {
		var x = document.getElementById("selectDate2");
		x.disabled = false;
		var y = document.getElementById("selectMonthYear2");
		y.disabled = false;
	} else {
		var x = document.getElementById("selectDate2");
		x.disabled = true;
		var y = document.getElementById("selectMonthYear2");
		y.disabled = true;
	}

}

function refreshSummary(selectedId) {

	var url = './selectedsummary.jsp?selected=';

	for ( var i = 0; i < selectedId.length; i++) {
		var s = selectedId[i].split("|", 3)[2];
		url = url + s;

		if (i < selectedId.length - 1) {
			url = url + ",";
		}
	}
	
	if (document.getElementsByName("extracover")[0].checked) {
		url = url + "&extracover=true";
	}

	req.open("GET", url, true);
	req.send(null);

}

function servletCallback() {
	var field = document.getElementById("bookingSummary");

	if (req.status == 500) {
		alert("Please search again");
		location.href = "home.jsp";
	} else {
		field.innerHTML = req.responseText;
	}
}

function decorate() {

}

function popupWin(page) {
	var baseUrl = document.URL;
	var link = "";

	var urlSplit = baseUrl.split("/");

	for (i = 0; i < urlSplit.length - 1; i++) {
		link = link + urlSplit[i] + "/";
	}

	link = link + page;

	// alert(link);

	var w = 500;
	var h = 200;
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	if (winl < 0)
		winl = 0;
	if (wint < 0)
		wint = 0;

	windowprops = "height=" + h + ",width=" + w + ",top=" + wint + ",left="
			+ winl + ",location=no,"
			+ "scrollbars=yes,menubars=no,toolbars=no,resizable=no,status=yes";
	window.open(link, "Popup", windowprops);
}

function faredetail() {
	var jspPage = document.body.id;
	var w = 800;
	var h = 500;
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	if (winl < 0)
		winl = 0;
	if (wint < 0)
		wint = 0;

	var page = 'faredetails.jsp';

	windowprops = "height=" + h + ",width=" + w + ",top=" + wint + ",left="
			+ winl + ",location=no,"
			+ "scrollbars=yes,menubars=no,toolbars=no,resizable=yes,status=yes";
	window.open(page, '', 'scrollbars=yes,height=500,width=800,resizable=yes');

}

function fareruledetail() {
	var jspPage = document.body.id;
	var w = 700;
	var h = 400;
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	if (winl < 0)
		winl = 0;
	if (wint < 0)
		wint = 0;

	var page = 'fareruledetail.jsp';

	windowprops = "height=" + h + ",width=" + w + ",top=" + wint + ",left="
			+ winl + ",location=no,"
			+ "scrollbars=yes,menubars=no,toolbars=no,resizable=yes,status=yes";
	window.open(page, '', 'scrollbars=yes,height=600,width=800,resizable=yes');
}

function openprintwindow() {
	win1 = window.open('PNRPDFAction', 'printPnr', 'width=400,height=200');
// win1.print();
}

function clone(obj) {
	// Handle the 3 simple types, and null or undefined
	if (null == obj || "object" != typeof obj)
		return obj;

	// Handle Date
	if (obj instanceof Date) {
		var copy = new Date();
		copy.setTime(obj.getTime());
		return copy;
	}

	// Handle Array
	if (obj instanceof Array) {
		var copy = [];
		for ( var i = 0, len = obj.length; i < len; ++i) {
			copy[i] = clone(obj[i]);
		}
		return copy;
	}

	// Handle Object
	if (obj instanceof Object) {
		var copy = {};
		for ( var attr in obj) {
			if (obj.hasOwnProperty(attr))
				copy[attr] = clone(obj[attr]);
		}
		return copy;
	}

	throw new Error("Unable to copy obj! Its type isn't supported.");
}

function confirmTicketing() {
	var agree = confirm("Issuing ticket(s) will debit agent's deposit. Are you sure you wish to continue?");
	if (agree) {
		return true;
	} else {
		return false;
	}
}

function confirmTicketingWithAgreement() {
	if (!promptAgreeTermAndCond()) {
		return false;
	}
	var agree = confirm("Issuing ticket(s) will debit agent's deposit. Are you sure you wish to continue?");
	if (agree) {
		return true;
	} else {
		return false;
	}
}

function confirmDeleteUser() {
	var agree = confirm("User will be deleted. Are you sure you wish to continue?");
	if (agree) {
		return true;
	} else {
		return false;
	}
}

function confirmCancel() {
	var agree = confirm("PNR will be cancelled. Are you sure you wish to continue?");
	if (agree) {
		return true;
	} else {
		return false;
	}
}

function sendPnr() {
	var email = prompt("Please input email address(s) (with comma separrated)");

	if (email) {
		var emailAr = email.split(",");

		if (emailAr.length == 0) {
			alert("Invalid email address format.");
			return;
		} else {
			for (i = 0; i < emailAr.length; i++) {
				if (!echeck(emailAr[i].trim())) {
					return;
				}
			}
		}

		var url = './PNRPDFAction?send=true&email=' + email;

		location.href = url;
	}

}

function echeck(str) {

	var at = "@";
	var dot = ".";
	var lat = str.indexOf(at);
	var lstr = str.length;
	var ldot = str.indexOf(dot);
	if (str.indexOf(at) == -1) {
		alert("Invalid E-mail ID");
		return false;
	}

	if (str.indexOf(at) == -1 || str.indexOf(at) == 0
			|| str.indexOf(at) == lstr) {
		alert("Invalid E-mail ID");
		return false;
	}

	if (str.indexOf(dot) == -1 || str.indexOf(dot) == 0
			|| str.indexOf(dot) == lstr) {
		alert("Invalid E-mail ID");
		return false;
	}

	if (str.indexOf(at, (lat + 1)) != -1) {
		alert("Invalid E-mail ID");
		return false;
	}

	if (str.substring(lat - 1, lat) == dot
			|| str.substring(lat + 1, lat + 2) == dot) {
		alert("Invalid E-mail ID");
		return false;
	}

	if (str.indexOf(dot, (lat + 2)) == -1) {
		alert("Invalid E-mail ID");
		return false;
	}

	if (str.indexOf(" ") != -1) {
		alert("Invalid E-mail ID");
		return false;
	}

	return true;
}

function searchOptionCheck() {
	var txBc = document.getElementsByName("bookingCode")[0];
	var txLn = document.getElementsByName("lastname")[0];
	var txBd = document.getElementsByName("bookingdate")[0];
	var lkBd = document.getElementsByName("bookdatebtn")[0];
	
	txBc.value = '';
	txLn.value = '';
	txBd.value = '';

	var elBc = document.getElementById("searchoptbybookcode");
	var elLn = document.getElementById("searchoptbylastname");
	var elBd = document.getElementById("searchoptbybookdate");
	
	if (elBc.checked) {		 
		txBc.disabled=false;
		txBc.focus();
		txLn.disabled= true;
		txBd.disabled= true;
		lkBd.removeAttribute("href");
	} else if (elLn.checked) {
		txBc.disabled=true;
		txLn.disabled= false;
		txLn.focus();
		txBd.disabled= true;
		lkBd.removeAttribute("href");
	} else if (elBd.checked) {
		txBc.disabled=true;
		txLn.disabled= true;
		txBd.disabled= false;
		lkBd.href = "javascript:NewCal('bookingdate','ddmmmyyyy')";
		txBd.focus();
	}
	

}

function closeApplication() {
	window.onbeforeunload = function (evt) {
	
		var message = 'Are you sure you want to leave?';

		if (typeof evt == 'undefined') {
			evt = window.event;
		}
	
		if (evt) {
			evt.returnValue = message;
		}
	
		return message;
	}; 
}

function selectsplit() {
	var els = document.getElementsByName("splitedpax");
	var all = els.length;
	var checked = 0;
	
	for (i=0; i < els.length; i++) {
		if (els[i].checked) {
			checked++;
		}
	}
	
	for (i=0; i < els.length; i++) {
		if (!els[i].checked) {
			if (all - checked == 1) {
				els[i].disabled = true;
			} else {
				els[i].disabled = false;
			}
		}
	}
	
}

function selectedTableCell(name) {
	document.getElementsByName(name)[0].checked=true;
}

function cellFocus(el, cl) {
	var td = el.parentNode.parentNode;
	if (el.selected) {
		td.className = "avcellTd_Select";
	} else {
		td.className = cl;
	}
}

function promptAgreeTermAndCond() {
	if (!document.getElementById("farerulesAgreement").checked) {
		alert("Aha, you didn't read term and condition. Please read and agree with it first.");
		
		return false;
	} else {
		return true;
	}
}
