var	ie=document.all
var	dom=document.getElementById
var	ns4=document.layers

var firstDayOfMonth = new Array;

var	dateNow	 = today.getDate()
var	monthNow = today.getMonth()
var	yearNow	 = today.getYear()
if (yearNow < 2000) { yearNow += 1900	}

thisMonth = monthNow;
thisYear = yearNow;
	
for (var i = 0; i < 13; i++)
{
	firstDayOfMonth[i] = new Date(thisYear, thisMonth, 1).getDay();

	thisMonth = thisMonth + 1;
	if (thisMonth > 12)
	{
		thisMonth = 1;
		thisYear = thisYear + 1;
	}
}

var nMonth, nYear;
var displayMonths = new Array;
var valueMonths = new Array;

nYear = yearNow;

for	(i=0; i<13;	i++)
{
	nMonth = i + monthNow
	
	if (nMonth > 11)
	{
		nMonth = nMonth-12
		nYear = yearNow + 1
	}
	if (eval("monthName") != null)
		displayMonths[i] = monthName_display[nMonth].text + " " + nYear;
	else
		displayMonths[i] = monthName[nMonth].text + " " + nYear;
		
	valueMonths[i] = monthName[nMonth].text + " " + nYear;
}