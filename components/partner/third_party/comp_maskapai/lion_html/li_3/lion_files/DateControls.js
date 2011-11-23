function updateReturnDate(ctlDepMonth, ctlDepDay, ctlRetMonth, ctlRetDay)
{
	var depDate = GetDDLDate(ctlDepDay, ctlDepMonth);		//var depDate = new Date(ctlDepMonth.value + "/" + ctlDepDay.value);
	var retDate = GetDDLDate(ctlRetDay, ctlRetMonth);		//var retDate = new Date(ctlRetMonth.value + "/" + ctlRetDay.value);

	if (retDate < depDate)
	{
		ctlRetMonth.value = ctlDepMonth.value;
		updateDays(ctlRetDay, ctlRetMonth);
		ctlRetDay.value = ctlDepDay.value;
	}
}

function GetDDLDate(ddlDay, ddlMnYr)
{
	return new Date(ddlDay.value + " " + ddlMnYr.value);
}

function updateMonths(ctlMonth)
{
	//Can only book a max of 326 days out.
	for (var i = 0; i < 12; i++) 
	{
		ctlMonth.options[i] = new Option(displayMonths[i], valueMonths[i] );
	}

}

function updateDays(ctlDays, ctlMonth)
{

	var daysInMonth;
	for (var i = 0; i < 12; i++) 
	{
		if (monthName[i].text == ctlMonth.value.substr(0,3))
		{
			daysInMonth = parseInt(monthName[i].value);
		}
	}

    if(daysInMonth == 28)
    {
        year=parseInt(ctlMonth.value.substr(4,4));
        if (isLeapYear(year))
			daysInMonth = daysInMonth + 1;		
    }
	var selectedDayIndex = parseInt(ctlDays.value) - 1;
	ctlDays.options.length = 0;
	var dayOffset = 1;
	var monthIndex = ctlMonth.selectedIndex;
	if (monthIndex == 0)
	{
		dayOffset=dateNow;
		if (selectedDayIndex < dayOffset - 1)
			selectedDayIndex = 0;
		else
			selectedDayIndex = selectedDayIndex - dayOffset + 1;
	}
	populateDateList(dayOffset, daysInMonth, ctlDays, monthIndex);
	if (selectedDayIndex > daysInMonth - 1)
		selectedDayIndex = daysInMonth - 1;
	if (selectedDayIndex > -1)
		ctlDays.options[selectedDayIndex].selected = true;
}

function populateDateList(beginDate, endDate, ctlDays, monthIndex)
{
	daysLeft = (endDate - beginDate) + 1;
	var date;
	var dn = eval("dayName");
	if (dn == null) dn = dayName;
	for (var i = 0; i < daysLeft; i++) 
	{
		date = i+beginDate;
		var index = (firstDayOfMonth[monthIndex] + date - 1) % 7;
		ctlDays.options[i] = new Option(dn[index] + " " + date, date);
	}
}

function isLeapYear(year)
{
	if (year % 4 == 0  && ( !(year % 100 == 0 && year % 400 != 0)))
		return true;
	else
		return false;
}

//txtDefaultDate must be in "dd MMM yyyy" format.
function setupDateControls(ctlDays, ctlMonth, txtDefaultDate )
{
	if (txtDefaultDate == "") 
	{
		updateMonths(ctlMonth);
		updateDays(ctlDays, ctlMonth);
	}
	else
	{
		updateMonths(ctlMonth);
		ctlMonth.value = txtDefaultDate.substr(3,8);
		updateDays(ctlDays, ctlMonth);
		var dayNum;
		if (txtDefaultDate.substr(0,1) == 0)
			dayNum = txtDefaultDate.substr(1,1);
		else
			dayNum = txtDefaultDate.substr(0,2);
		ctlDays.value = dayNum;
	}
}