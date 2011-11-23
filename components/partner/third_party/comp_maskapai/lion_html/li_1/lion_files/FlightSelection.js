function getPre(ctl)
{
	return $(fsPre + ctl);
}

function datesSetup()
{
	setupDateControls(getPre("ddlDepDay"), getPre("ddlDepMonth"), getPre("txtDepartureDate").value);
	setupDateControls(getPre("ddlRetDay"), getPre("ddlRetMonth"), getPre("txtReturnDate").value);
}
function updateReturnDDLs()
{
	updateReturnDate(getPre("ddlDepMonth"), getPre("ddlDepDay"), getPre("ddlRetMonth"), getPre("ddlRetDay"));
}

function updateTripDates()
{
	var day = getPre("ddlDepDay").value < 10 ? "0" : "";
	getPre("txtDepartureDate").value = day + getPre("ddlDepDay").value + " " + getPre("ddlDepMonth").value;
	day = getPre("ddlRetDay").value < 10 ? "0" : "";
	getPre("txtReturnDate").value = day + getPre("ddlRetDay").value + " " + getPre("ddlRetMonth").value;
}

function ValidateSeatCount(source, args)
{
	var adtCount = parseInt(getPre("ddlADTCount").value);
	var cnnCount = parseInt(getPre("ddlCNNCount").value);
	var infCount = getPre("ddlINFCount");
	infCount = (infCount == null) ? 0 : parseInt(infCount.value);
	var totalSeats = adtCount + cnnCount;
	var totalAccompanyingInfantSeats = totalSeats - cnnCount;

	if (totalSeats > 7) 
	{
		args.IsValid = false;
		source.errormessage = "* Total requested seats (including children) must not exceed 7";
	}
	else if (totalSeats < 1)
	{
		args.IsValid = false;
		source.errormessage = "* Your reservation must contain at least one adult passenger";
	}
	else if (totalSeats == cnnCount)
	{
		args.IsValid = false;
		source.errormessage = "* Lion Air web booking require at least one adult, or please call our call center or visit our ticketing office for unaccompanied minor booking.";
	}
	else if (totalAccompanyingInfantSeats < infCount)
	{
		args.IsValid = false;
		source.errormessage = "* Every infant must be accompanied by an adult";
	}
	else
	{
		args.IsValid = true;
		source.errormessage = "";
	}
}


function ValidateInputDates(source, args)
{
	updateTripDates();
	var depDate = GetDDLDate(getPre("ddlDepDay"), getPre("ddlDepMonth"));	//new Date(getPre("ddlDepMonth").value + "/" + getPre("ddlDepDay").value);
	var retDate = GetDDLDate(getPre("ddlRetDay"), getPre("ddlRetMonth"));	//new Date(getPre("ddlRetMonth").value + "/" + getPre("ddlRetDay").value);
	args.IsValid = true;

	//Can only book 331 days out.
	var maxDate = new Date(today);
	maxDate.setDate(today.getDate() + 331);

	if (getPre("rbOneWay").checked)
		return;

	if (maxDate < retDate)
	{
		source.errormessage = "* Return date is too far into the future. Tickets can be booked max 331 days in advance.";
		args.IsValid = false;
	}
	if (depDate > retDate)
	{
		source.errormessage = "Return date should be after Departure date";
		args.IsValid = false;
	}
}


function ValidateDepartDate(source, args)
{	
	var depDate = GetDDLDate(getPre("ddlDepDay"), getPre("ddlDepMonth"));	//new Date(getPre("ddlDepMonth").value + "/" + getPre("ddlDepDay").value);
	//Can only book 331 days out.
	var maxDate = new Date(today);
	maxDate.setDate(today.getDate() + 331);

	if (maxDate < depDate)
	{
		source.errormessage = "* Departure date is too far into the future. Tickets can be booked max 331 days in advance.";
		args.IsValid = false;
		return;
	}
}


var PortsAvail = new Array(
	new Array("DXB", "xxx", "August 22, 2008", "from Dubai"),
	new Array("xxx", "DXB", "August 22, 2008", "to Dubai")
	);

function CheckPortAvail(source, args)
{
	var depDate = GetDDLDate(getPre("ddlDepDay"), getPre("ddlDepMonth"));
	var retDate = GetDDLDate(getPre("ddlRetDay"), getPre("ddlRetMonth"));
	var ori = getPre("txtSelOri").value;
	var des = getPre("txtSelDes").value;
	var oneway = getPre("rbOneWay").checked;
	source.errormessage = "";
	args.IsValid = true;

	CheckPortAvails(source, args, ori, des, depDate, retDate, oneway);
}

function CheckPortAvails( source, args, ori, des, depDate, retDate, oneway)
{
	for (var i = 0; i < PortsAvail.length; i++)
	{
		var pa = PortsAvail[i];
		var fromPort = pa[0];
		var toPort = pa[1];
		var dateText = pa[2];
		var fromDate = new Date(dateText);
		var portText = pa[3];
		
		if ((depDate < fromDate) &&
			((fromPort == "xxx") || (fromPort == ori)) &&
			((toPort == "xxx") || (toPort == des)))
			{
				args.IsValid = false;
				source.errormessage = BuildPortAvailErrorMsg(portText, dateText);
				return;
			}
	}
}

function ValidateInfants(source, args)
{
	source.errormessage = "";
	args.IsValid = true;
	ValidateInfantsPorts(source, args, getPre("txtSelOri").value, getPre("txtSelDes").value);
}

function ValidateInfantsPorts(source, args, ori, des)
{
	ori = pageDataContainer.getAirportByCode(ori);
	des = pageDataContainer.getAirportByCode(des);
	var infCount = getPre("ddlINFCount");
	infCount = (infCount == null) ? 0 : parseInt(infCount.value);
	//if (infCount > 0)
	//	if (ori != null)
	//	{ }
}

function BuildPortAvailErrorMsg(portText, depDateText)
{
	var errmsg = "Lion Air flights " + portText +
		" will commence from " + depDateText + ". " +
		"Please amend your date of travel to proceed with your booking.";
	return errmsg;
}

function ValidateCityPair(source, args) 
{
	args.IsValid = true;
	var ori = getPre("txtSelOri").value;
	var des = getPre("txtSelDes").value;
	//RequiredFieldValidator will pick up "Select City" or "--Depart--", etc
	if (getPre("txtSelOri").value == getPre("txtSelDes").value)
	{
		var arrRet = getPre("rbOneWay").checked ? "Arrival" : "Return";
		source.errormessage = "* Departure and " + arrRet + " ports are the same.";
		args.IsValid = false;			
	}
}
var showProc = true;
function OnAllValid(source, args) 
{
	if (AllValidatorsValid(Page_Validators) && showProc)
		showProcessingMessage();
}


function selectOneWay() {setDesData(true, "--Arrive--", "Arrive:");}
function selectReturn() {setDesData(false, "--Return--", "Return:");}
function setDesData(disabled, optText, lblText)
{
	getPre("ddlRetDay").disabled = disabled;
	getPre("ddlRetMonth").disabled = disabled;
	var desOpt = getPre("txtSelDes");
	if (desOpt.value.search("--") >= 0) desOpt.value = optText;
	desOpt = getPre("txtDes");
	if (desOpt.value.search("--") >= 0) desOpt.value = optText;
	var retLbl = document.getElementById("returnlabel")
	if (retLbl != null) retLbl.innerHTML = lblText;
}


function calendarClick(calNum)
{
	if (calIsOpen(calNum))	
	{
		hideCalendar(calNum);
		return;
	}
	if (calNum == 0)
	{
		hideCalendar(1);
		showCalendar('Srch_DepCal', 'DepCal', 'txtDepartureDate', calNum);
	}
	else
	{
		hideCalendar(0);
		showCalendar('Srch_RetCal', 'RetCal', 'txtReturnDate', calNum);
	}
}

function calIsOpen(calNum)
{
	var cont = (calNum == 0) ? 'Srch_DepCal' : 'Srch_RetCal';
	return $(cont).style.display == 'block';
}

function hideCalendar(calNum)
{
	var cont, imag;
	if (calNum == 0)
	{
		cont = 'Srch_DepCal';
		imag = 'DepCal';
	}
	else
	{
		cont = 'Srch_RetCal';
		imag = 'RetCal';
	}
	$(cont).style.display = 'none';
	imag = $(imag);
	//imag.src = imag.src.replace('_Down', '_Up');
}	

function showCalendar(calCont, calImg, update, calNum)
{
	updateTripDates();
	var cal = $(calCont);
	calImg = $(calImg);
	//calImg.src = calImg.src.replace('_Up', '_Down');
	if (cal.style.display == 'block')
	{
		hideCalendar(calCont);
		return;
	}
	cal.style.display = 'block';
	var ifram = cal.getElementsByTagName("iframe")[0];
	ifram.src = "DualCalendar.aspx?container=" + calCont + "&updatefield=" + fsPre + update + "&defDate=" + getPre(update).value.replace(/ /g, "") + "&calNum=" + calNum;
}	
function SetCalPoss()
{
	SetCalPos($("DepCal"), $("Srch_DepCal"));
	SetCalPos($("RetCal"), $("Srch_RetCal"));
}
function SetCalPos(refEl, cal)
{
	if (refEl === null || cal == null) return;
	var calPos = refEl.cumulativeOffset();
	cal.style.left = calPos.left + calLOff + "px";
	cal.style.top = calPos.top + calTOff + "px";
	//var elL = getAbsLeft(refEl);
	//var elT = getAbsTop(refEl);
	//cal.style.left = elL + calLOff + "px";
	//cal.style.top = elT + calTOff + "px";
}
