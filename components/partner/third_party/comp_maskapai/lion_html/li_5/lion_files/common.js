function setActiveStyleSheet(title) {
	var i, a, main;
	for (i = 0; (a = document.getElementsByTagName("link")[i]); i++)
	{
		if (a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title"))
		{
			a.disabled = true;
			if (a.getAttribute("title") == title) a.disabled = false;
		}
	}
	createCookie("textSize", title, 365);
}

function createCookie(name,value,days)
{
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = ";expires="+date.toGMTString();
	}
	else
		expires = "";

	document.cookie = name + "=" + value + expires + ";path=/;";
}


function validateString(e, testChars)
{
	return keyStrokeTest(eventTest(e), testChars);
}

function eventTest(e)
{
	if (window.event)
		return window.event.keyCode;
	else if (e)
		return e.which;
	else
		return null;
}

function keyStrokeTest(key, testChars)
{
	var keychar;

	keychar = String.fromCharCode(key);
	// control keys except enter which is key 13
	if ((key==null) || (key==0) || (key==8) ||	(key==9) || (key==27))
		return true;
	// test against valid characters
	else if (((testChars).indexOf(keychar) > -1))
		return true;

	else
		return false;
}

function alphabetOnly(e)
{
	return validateString(e, "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz")
}

function alphaNumericOnly(e)
{
	return validateString(e, "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890 ")
}

function alphaOnly(e)
{
	return validateString(e, "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz- '.")
}

function numbersOnly(e, dec)
{
	if (dec)
		return validateString(e, "0123456789.");
	else
		return validateString(e, "0123456789");
}

function emailCharOnly(e)
{
	return validateString(e, "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._@");
}


function showProcessingMessage()
{
    if (typeof Page_IsValid != 'undefined')
	{
		if (Page_IsValid)
			swapProcessingDivs();
	}
	else
		swapProcessingDivs();
}

function swapProcessingDivs()
{
	if (typeof(transitionID) != 'undefined')
	{
		document.getElementById("ProcTran").style.display = 'block';
		var modal = $find(transitionID + "mpeTran");
		if (modal) modal.show();
	}
	else
	{
		document.getElementById("Processing").style.display = 'block';
		document.getElementById("Processing").style.visibility = "visible";
		document.getElementById("Processing").style.height = document.documentElement.scrollHeight + 'px';
	}
	setTimeout('document.images["processingImage"].src = "images/Tran/waiting.gif"', 200);
	setWaitImagesTimeout();

}


function getCookieVal (offset)
{
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1)
		endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}


function GetCookie (name) 
{
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;
	while (i < clen) 
	{
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg)
			return getCookieVal (j);
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0)
			break;
	}
	return null;
}


function SetCookie (name, value) 
{
	var argv = SetCookie.arguments;
	var argc = SetCookie.arguments.length;
	var expires = (argc > 2) ? argv[2] : null;
	var path = (argc > 3) ? argv[3] : null;
	var domain = (argc > 4) ? argv[4] : null;
	var secure = (argc > 5) ? argv[5] : false;
	document.cookie = name + "=" + escape (value) + ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) +
		((path == null) ? "" : ("; path=" + path)) +
		((domain == null) ? "" : ("; domain=" + domain)) +
		((secure == true) ? "; secure" : "");
}

function SetHighlightByID(ID, colour)
{
	var i = 1;
	var control = document.getElementById(ID + "_1");
	while (control != null)
	{
		control.style.backgroundColor = colour;
		i++;
		control = document.getElementById(ID + "_" + i);
	}
}

function LoadPopupURL(URLTOPopup)
{
	LoadPopupURL2(URLTOPopup, 803, 600);
}    

function LoadPopupURL2(URLTOPopup, width, height)
{
	winTop=0
	winLeft=0
	winLeft=Math.floor((Math.abs(screen.availWidth-width))/2);
	winTop=Math.floor((Math.abs(screen.availHeight-height))/2);
	window.open(URLTOPopup,'','top='+ winTop + ',left=' + winLeft + ',width=' + width +',height=' + height +',menubar=no,status=yes,location=no,toolbar=no,scrollbars=yes,resizable=no');
}

function MouseOver(ID)
{
	SetHighlightByID(ID, '#DEE7E9');
}

function MouseOut(ID)
{
	SetHighlightByID(ID, '#FFFFFF');
}

function trim(str)
{
   return str.replace(/^\s*|\s*$/g,"");
}


//General func for finding a cssRule returns rule or false
function FindCSSRule(rule)
{
	var i,j;
	var cssRule;
	rule = rule.toLowerCase();
	if (document.styleSheets)
		for (i = 0; i < document.styleSheets.length; i++)
		{
			var styleSheet = document.styleSheets[i];
			j = 0;
			cssRule = false;
			do
			{
				cssRule = styleSheet.cssRules ? styleSheet.cssRules[j] : styleSheet.rules[j];
				if (cssRule && (cssRule.selectorText.toLowerCase() == rule))
					return cssRule;
				j++;
			}
			while (cssRule)
		}

	return false;	//not found
}


function FormatCurrency(amount, points,lang)	//num=float points=int
{
	if (isNaN(amount)) amount = 0.0;
	amount = amount.toFixed(points).split('.');		//eg. 12000 or 250.50
	var befDec = amount[0];
	var aftDec;
//	if (lang != "fr")
		aftDec = (amount.length == 2) ? "." + amount[1] : "";
//	else
//		aftDec = (amount.length == 2) ? "," + amount[1] : "";
	var whole = "";
	var delim = "";
	while(befDec.length != 0)
	{
		if (befDec.length > 3)
		{
			whole = befDec.substr(befDec.length - 3) + delim + whole;
			befDec = befDec.substr(0, befDec.length - 3);
		}
		else
		{
			whole = befDec + delim + whole;
			befDec = "";
		}
//		if (lang != "fr")
			delim = ",";
//		else
//			delim = " ";
	}
	return whole + aftDec;
}

function SetValidatorLast(valNam)
{
	if ((typeof(Page_Validators) != 'undefined') && (Page_Validators != null) &&
		(Page_Validators.length > 0) && (Page_Validators[Page_Validators.length - 1].id != valNam))
	{
		//var newVal = new Array(), 
		var ind = -1;
		var validator;
		for (var i = 0; i < Page_Validators.length; i++)
		{
			validator = Page_Validators[i];
			if (validator.id == valNam)
			{
				ind = i;
				break;
			}
		}
		if (ind >= 0)
		{
			Page_Validators.splice(ind, 1);
			Page_Validators.push(validator);
		}
	}
}