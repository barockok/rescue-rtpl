var AutoFocus = new String;
var CcValidation  = new String;
var CcPreselect  = new String;
var CcTotalValue;
var CcCurrency;
var CssClass = new Object;
var SelectedDate = new Object;
var Airports;
var isGroupBooking =  new Boolean(false);

var Status = new Boolean(true);
var SingleFlight =  new Boolean(false);
var goToRow;
//var DateValid = 'M/d/y';
//var DateValidText = 'mm/dd/yyyy';
var DateValid = 'dd/MM/yyyy';
var DateValidText = 'dd/mm/yyyy';

var pl = 3;
//var pl = 0;
var rowheadnfoot = 1;
var rowPassengerList = 6;

var pt = new Boolean(true);
var ptv = new Boolean(true);

var eMsg = new Array();
eMsg[0]	= 'Tidak memungkinkan untuk reservasi bayi lebih banyak dari orang dewasanya';
eMsg[1]	= 'Mohon cek tanggal kepulangan anda ! seharusnya pada hari yang sama atau setelah tanggal keberangkatan';
eMsg[2]	= 'Masukkan nama penumpang';
eMsg[3]	= 'Masukkan tanggal lahir';
eMsg[4]	= 'Masukkan nama penumpang di penumpang ';
eMsg[5]	= 'Masukkan tanggal lahir di penumpang ';
eMsg[6]	= 'Masukkan nomor telepon rumah';
eMsg[7]	= 'Masukkan alamat email';
eMsg[8]	= 'Masukkan alamat rumah';
eMsg[9]	= 'Mohon diterima syarat dan kondisi';
eMsg[10]= 'Masukkan nomor passport di penumpang ';
eMsg[11]= 'Masukkan tempat issue di penumpang ';
eMsg[12]= 'Masukkan tanggal berakhir di penumpang ';
eMsg[13]= 'Masukkan tanggal issue di penumpang ';
eMsg[14]= 'Masukkan alamat email sesuai format';
eMsg[15]= 'Mohon cek tanggal issue anda di penumpang ';
eMsg[16]= 'Mohon cek tanggal berakhir anda di penumpang ';
eMsg[17]= 'Please check your Date of Birth! It must be in the past at passenger ';
eMsg[18]= 'Please check your Date of Birth! It must be in the past';
//SetAutoFocus()

function dgItem_over(object){
	object.className = (object.className == "GridItems0_o" ? "GridItems0" : "GridItems0_o")
}

function dgItem_click(s){
	if (event.srcElement.tagName != 'A'){
		o = document.getElementById(s)
		o.checked = 'true'
	}
}

function ValidFlightSelection(o){
	Status =  true;
	var d = GetSelectFlight('FlightAvailability0',0)
	if (LibDaysDiff(d,new Date()) < 0 ){
		alert('Mohon cek jadwal keberangkatan anda ! minimum sebelum waktu keberangkatan')
		Status =  false;
		return
	}
	if (document.getElementById('FlightAvailability1') != null) {
		var d = GetSelectFlight('FlightAvailability0',1)
		var r = GetSelectFlight('FlightAvailability1',0)
		if (r != undefined){
			if (LibDaysDiff(r,d) < 0){
				alert('Mohon cek tanggal kepulangan anda ! seharusnya pada hari yang sama atau setelah tanggal keberangkatan')
				Status =  false;
				return
			}
		}
	}	
}

function ShowCvvCode(o){
	if (document.getElementById(o + '_cbmFormOfPaymentSubtype') != null){
		var iA = document.getElementById(o + '_cbmFormOfPaymentSubtype').options[document.getElementById(o + '_cbmFormOfPaymentSubtype').selectedIndex].value.split('|')
		if (iA.length > 0 ){
			document.getElementById('CvvForm').style.visibility = (iA[2] == '1') ?  'visible' : 'hidden' ;
			document.getElementById('CvvLabel').style.visibility = (iA[2] == '1') ? 'visible' : 'hidden' ;
/*			document.getElementById('IssueDateForm').style.visibility = (iA[3] == '1') ?  'visible' : 'hidden' ;
			document.getElementById('IssueDateLabel').style.visibility = (iA[3] == '1') ? 'visible' : 'hidden' ;
			document.getElementById('IssueNumberForm').style.visibility = (iA[4] == '1') ?  'visible' : 'hidden' ;
			document.getElementById('IssueNumberLabel').style.visibility = (iA[4] == '1') ? 'visible' : 'hidden' ;
			document.getElementById('IssueRow').style.visibility = (iA[3] == '1' || iA[4] == '1') ? 'visible' : 'hidden' ;
*/
		}
	}
	if (document.getElementById(o + '_tboDocumentNumber') != null){
	    document.getElementById(o + '_tboDocumentNumber').value = "";	    
	}
	if (document.getElementById('CcMessage') != null){
	    document.getElementById('CcMessage').innerHTML = 'Kartu kredit anda akan di charged oleh ' + CcTotalValue.toFixed(2) + ' ' + CcCurrency;
	}
}

function isInteger(val){
	if( isNaN( parseFloat(val))) {
		return false;
	} else {
		return parseFloat(val);
	}
}

function fillDestination(e,o){
	var i = 0;
	var eD = document.getElementById(e);
	var oD = document.getElementById(o);
	var sOv = eD[eD.selectedIndex].value;
	//var sDv = oD[oD.selectedIndex].value
	oD.options.length = 0;
	for (ii=0;ii < Airports.length;ii++){
		var at = Airports[ii].split('|');
		if (at[0] == sOv){
			oD.options[i] = new Option(at[2],at[1]);
			i++;
		}
	}
	eD.focus();
}

function addFullNumerical(num,bit){
	var str = num.toString();
	if(str.length < parseInt(bit)){
		do
		str = "0" + str;
		while(str.length < parseInt(bit));
	}
	return str;
}

function changeToDate(str){
	var dd
	var mm
	var yy
	switch(DateValidText){
		case "dd/mm/yyyy":
			dd = str.substr(0,2)
			mm = str.substr(3,2)
			yy = str.substr(6,4)
		  return new Date(mm+"/"+dd+"/"+yy)
		  break    
		case "mm/dd/yyyy":
			mm = str.substr(0,2)
			dd = str.substr(3,2)
			yy = str.substr(6,4)
		  return new Date(mm+"/"+dd+"/"+yy)
		  break
		default:		
	}
}

function subStringLeft(str, n){
	if (n <= 0)
	    return "";
	else if (n > String(str).length)
	    return str;
	else
	    return String(str).substring(0,n);
}

function subStringRight(str, n){
    if (n <= 0)
       return "";
    else if (n > String(str).length)
       return str;
    else {
       var strLen = String(str).length;
       return String(str).substring(strLen, strLen - n);
    }
}

function dateDiff(p_Interval, p_Date1, p_Date2, p_firstdayofweek, p_firstweekofyear){
	if(!isDate(p_Date1)){return "invalid date: '" + p_Date1 + "'";}
	if(!isDate(p_Date2)){return "invalid date: '" + p_Date2 + "'";}
	var dt1 = new Date(p_Date1);
	var dt2 = new Date(p_Date2);

	// get ms between dates (UTC) and make into "difference" date
	var iDiffMS = dt2.valueOf() - dt1.valueOf();
	var dtDiff = new Date(iDiffMS);

	// calc various diffs
	var nYears  = dt2.getUTCFullYear() - dt1.getUTCFullYear();
	var nMonths = dt2.getUTCMonth() - dt1.getUTCMonth() + (nYears!=0 ? nYears*12 : 0);
	var nQuarters = parseInt(nMonths/3);	//<<-- different than VBScript, which watches rollover not completion
	
	var nMilliseconds = iDiffMS;
	var nSeconds = parseInt(iDiffMS/1000);
	var nMinutes = parseInt(nSeconds/60);
	var nHours = parseInt(nMinutes/60);
	var nDays  = parseInt(nHours/24);
	var nWeeks = parseInt(nDays/7);

	// return requested difference
	var iDiff = 0;
	switch(p_Interval.toLowerCase()){
		case "yyyy": return nYears;
		case "q": return nQuarters;
		case "m": return nMonths;
		case "y": 		// day of year
		case "d": return nDays;
		case "w": return nDays;
		case "ww":return nWeeks;		// week of year	// <-- inaccurate, WW should count calendar weeks (# of sundays) between
		case "h": return nHours;
		case "n": return nMinutes;
		case "s": return nSeconds;
		case "ms":return nMilliseconds;	// millisecond	// <-- extension for JS, NOT available in VBScript
		default: return "invalid interval: '" + p_Interval + "'";
	}
}

function isDate(p_Expression){
	return !isNaN(new Date(p_Expression));		// <<--- this needs checking
}

function AdditionalPaymentTabChanged(o,P){
    // Change Image Buynow button at 'Book now pay later'
    /*
    var objBuyNowBtn = document.getElementById("ctrBooking_ctrPassengerInfosBase_btmSubmitBooking");
    if(objBuyNowBtn != null){
        if (o == "NoPayment"){
            objBuyNowBtn.src = "Images/0/B/bse.gif";
        }
        else{
            objBuyNowBtn.src = "Images/0/B/bbo.gif";
        }
    }
    */
}

// Start for new check date function
var dtCh= "/";
var minYear = 1900;
var maxYear = 2100;

function isInteger(s){
    var i;
    for (i = 0; i < s.length; i++){   
    // Check that current character is number.
    var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
    var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
    // February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}

function DaysArray(n) {
    for (var i = 1; i <= n; i++) {
        this[i] = 31;
        if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
        if (i==2) {this[i] = 29}
    } 
    return this;
}

function isDateExist(dtStr){
	var daysInMonth = DaysArray(12);
	var pos1 = dtStr.indexOf(dtCh);
	var pos2 = dtStr.indexOf(dtCh, pos1 + 1);
	//var strMonth = dtStr.substring(0, pos1);
	var strDay = dtStr.substring(0, pos1);
	//var strDay = dtStr.substring(pos1 + 1, pos2);
	var strMonth = dtStr.substring(pos1 + 1, pos2);
	var strYear = dtStr.substring(pos2 + 1);
	strYr = strYear;
	if (strDay.charAt(0) == "0" && strDay.length > 1) strDay = strDay.substring(1);
	if (strMonth.charAt(0) == "0" && strMonth.length > 1) strMonth = strMonth.substring(1);
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0) == "0" && strYr.length > 1) strYr = strYr.substring(1);
	}
	month = parseInt(strMonth);
	day = parseInt(strDay);
	year = parseInt(strYr);
	if (pos1 == -1 || pos2 == -1){
		//alert("The date format should be : mm/dd/yyyy");
		return false;
	}
	if (strMonth.length < 1 || month < 1 || month > 12){
		//alert("Please enter a valid month");
		return false;
	}
	if (strDay.length < 1 || day < 1 || day > 31 || (month == 2 && day > daysInFebruary(year)) || day > daysInMonth[month]){
		//alert("Please enter a valid day");
		return false;
	}
	if (strYear.length != 4 || year == 0 || year < minYear || year > maxYear){
		//alert("Please enter a valid 4 digit year between " + minYear + " and " + maxYear);
		return false;
	}
	if (dtStr.indexOf(dtCh, pos2 +1 ) != -1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		//alert("Please enter a valid date");
		return false;
	}
    return true;
}
// End for new check date function

function activityNoBooking() {
	alert("No booking found for this activity.");
	return false;
}
