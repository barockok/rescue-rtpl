var DataContainer = Class.create();
DataContainer.prototype =
{
	initialize : function (countries, ports)
	{
		this.countries = countries;
		this.airports = ports;
		this.fromAirports = ports;
		this.toAirports = ports;
	},
	// below are the getters for each piece of data within this object
	getAirports : function () { return this.airports; },
	getCountries : function () { return this.countries; },
	getToAirports : function () { return this.toAirports; },
	getFromAirports : function () { return this.fromAirports; },
	getAirportByCode : function(portCode) {
		if (this.airports)
		{
			for (var i = 0; i < this.airports.length; i++)
			   if (portCode == this.airports[i].code)
				   return this.airports[i];
		}
		return null;
	}
}


function InitPorts(portCodes, portNames, portCountries, allCountries)
{
	ports = new Array();
	for (var i = 0; i < portCodes.length; i++)
	{
		var country = null;
		var port = portCodes[i];
		var city = "";
		if (port.indexOf("|") >= 0)
		{
			city = port.split("|")[1];
			port = port.split("|")[0];
		}
		for (var j = 0; j < allCountries.length; j++)
			if (allCountries[j].code == portCountries[i])
			{
				country = allCountries[j];
				break;
			}
		ports[i] = new Airport(port, city, portNames[i], country);
	}
	return ports;
}

function InitPorts2(portArray, countries)
{
	var airports = new Array();
	for (var i = 0; i < portArray.length; i++)
	{
		var portFields = portArray[i].split("|");
		var portCode = portFields[0];
		var cityCode = portFields[1];
		var portName = portFields[2];
		var portCountryCode = portFields[3];
		var portCountry = null;
		
		for (var j = 0; j < countries.length; j++)
			if (countries[j].code == portCountryCode)
			{
				portCountry = countries[j];
				break;
			}
		airports.push(new Airport(portCode, cityCode, portName, portCountry));
	}
	return airports;
}

function InitCountries(countryCodes, countryNames)
{
	var countries = new Array();
	for (var i = 0; i < countryCodes.length; i++)
		countries[i] = new Country(countryCodes[i], countryNames[i]);
	return countries;
}


//// this will make sure the ports selection box is rendered in its correct
//// position even if the auto complete port is wrapped inside an absolute positioned
//// containing document
//function getAbsLeft(obj)
//{
//	var l = obj.offsetLeft;
//	while (obj = obj.offsetParent)
//	{
//		if (isCurrentStylePositionStatic(obj))
//			l += obj.offsetLeft;
//		else
//			break;
//	}
//	return l;
//}

//function getAbsTop(obj)
//{
//	var t = obj.offsetTop;
//	while (obj = obj.offsetParent)
//	{
//		if (isCurrentStylePositionStatic(obj))
//			t += obj.offsetTop;
//		else
//			break;
//	}	
//	return t;
//}

//// Test if the "position" style property = "static"
//function isCurrentStylePositionStatic(element)
//{
//	var isStylePositionStatic = true;
//
//	if (element.currentStyle && element.currentStyle.position != "static")
//	{
//		isStylePositionStatic = false;
//	}
//	else if (document.defaultView)
//	{
//		var finalStyle = document.defaultView.getComputedStyle(element, "");
//		if (finalStyle && finalStyle.getPropertyValue("position") != "static")
//			isStylePositionStatic = false;
//	}
//	return isStylePositionStatic;
//}

function changeBkgrnd(obj)
{
	addClassName(obj, "selected");
}
function changeBkgrnd_Blur(obj)
{
	removeClassName(obj, "selected");
}
function addClassName(element, className)
{
	if (!Element.hasClassName(element, className))
		element.className = (element.className + ' ' + className);
}
function removeClassName(element, className)
{
	if (Element.hasClassName(element, className))
		element.className = element.className.replace(className, '');
}


var FlightDataCache = Class.create();
Object.extend(FlightDataCache.prototype,
{
	initialize : function ()
	{
		this.flightDataCacheArray = new Object();
	},

	getFlightDataItem : function (dataContainer, from, to)
	{
		var itemFrom = this.flightDataCacheArray[from];
		if (itemFrom == null)
		{
			itemFrom = new Object();
			this.flightDataCacheArray[from] = itemFrom;
		}
		var item = itemFrom[to];
		if (item == null)
		{
			item = new FlightData(dataContainer, from, to);
			itemFrom[to] = item;
		}
		return item;
	}
});
var FlightDataCache = new FlightDataCache();
var FlightData = Class.create();
FlightData.prototype =
{
	initialize : function (dataContainer, from, to)
	{
		this.dataContainer = dataContainer;
		this.airports = dataContainer.getAirports();
		this.fromAirports = dataContainer.getFromAirports();
		this.toAirports = dataContainer.getToAirports();
		//this.classes = dataContainer.getClasses();
		//this.matrix = dataContainer.getMatrix();
		this.fromAirport = getObjectFromArrayByCode(from, this.airports);
		this.toAirport = getObjectFromArrayByCode(to, this.airports);
		this.fromAirportIndex = indexOfAirportByCode(from, this.fromAirports);
		this.toAirportIndex = indexOfAirportByCode(to, this.toAirports);
		//this.matrixItem = getMatrixItem(this.fromAirportIndex,this.toAirportIndex,this.matrix, this.fromAirports.length, this.toAirports.length);
	},

	isValidFlight : function (evt)
	{
		//if (this.matrixItem != null) {
		if (this.fromAirport != this.toAirport)
		{
			//if (determineBooleanValueFromMatrixItem(this.matrixItem, this.dataContainer.getValidFlightIndex()))
			return true;
		}
		//}
		return false;
	}
}

// This object holds airport names, codes and its relevant country object
var Airport = Class.create();
Airport.prototype =
{
	initialize : function (code, city, airportName, country)
	{
		this.code = code;
		this.cityCode = city;
		this.airportName = airportName;
		this.country = country;
	},

	toStringNameCode : function () { return this.airportName + " (" + this.code + ")"; },			//Melbourne (MEL)

	toStringWithHighlight : function (highlightValue)
	{
		highlightValue = highlightValue.toLowerCase();
		var airportText = this.toStringNameCode();
		var returnVar = "";
		var matchInd, portDet;
		//1. Name
		var matchInd = this.airportName.toLowerCase().indexOf(highlightValue);
		portDet = this.airportName;
		if (matchInd >= 0)
		{
			var theHL = portDet.substring(matchInd, matchInd + highlightValue.length);
			portDet = portDet.replace(theHL, "<b>" + theHL + "</b>");
		}
		returnVar += portDet;
		//2. Code
		matchInd = this.code.toLowerCase().indexOf(highlightValue);
		portDet = this.code;
		if (matchInd >= 0)
		{
			var theHL = portDet.substring(matchInd, matchInd + highlightValue.length);
			portDet = portDet.replace(theHL, "<b>" + theHL + "</b>");
		}
		returnVar += " (" + portDet + ")";

		return returnVar;
	}
}

// This function takes a airport object and represents it as a hyperlinked String and highlights the text entered to find it.
function getAirportAsHTMLString(currentAirport, highlightValue, highlightMatch, fromTo)
{
//	var anchor = "";
	var highlight = "";
	if (currentAirport.airportName.toLowerCase().indexOf(highlightValue.toLowerCase()) == 0)
	{
//		anchor = "<font id=\"anchor\"></font>"
		if (currentAirport.airportName.toLowerCase() == highlightValue.toLowerCase())
			highlight=" class=\"acMatchHighlight\" ";
	}
//	var openingAnchorTag = anchor + "<a onFocus='changeBkgrnd(this)' onBlur='changeBkgrnd_Blur(this)' href='javascript:void(0)' id='" + fromTo + currentAirport.code + "'" + highlight + "/>";
	var openingAnchorTag = "<a onFocus='changeBkgrnd(this)' onBlur='changeBkgrnd_Blur(this)' href='javascript:void(0)' id='" + fromTo + currentAirport.code + "'" + highlight + "/>";
	var airportText;
	if (highlightMatch)
		airportText = currentAirport.toStringWithHighlight(highlightValue);
	else
		airportText = currentAirport.toStringNameCode();
	var closingAnchorTag = "</a>";
	return openingAnchorTag + airportText + closingAnchorTag;
}
  
// This object holds the basic data for a country
var Country = Class.create();
Country.prototype =
{
	initialize : function (code, countryName)
	{
		this.code = code;
		this.countryName = countryName;
	}
}


// This object is the control object for the Port text Box.  It sets up initial data and handles any expected events for the box.
// Most importantly it handles the keyup event which will populate the available airports.
var PortBoxControl = Class.create();
PortBoxControl.prototype =
{
	initialize : function (portIds, oppSelected, pbtOpen, pbtClose, thisForm, dataContainer, isOrigin)
	{
		this.portSelected = $(portIds.portSelected);			//ctl to store selection
		this.oppSelected = $(oppSelected);						//ctl to store selection (opposite end)
		this.portBox = $(portIds.inputBox);						//displayed text box
		this.portBoxListDiv = $(portIds.selectionBoxDiv);		//dropdown box to display ports
		this.pbToggle = $(portIds.toggleButton);
		this.pbtOpen = pbtOpen;
		this.pbtClose = pbtClose;
		this.portBoxClose = $(portIds.closeButton);
		this.portShowTable = $(portIds.showTable);				//the entire dropdown
		this.portShowFrame = this.portShowTable.getElementsByTagName("iframe")[0];
		this.thisForm = $(thisForm);
		this.currentSelection = "";
		this.dataContainer = dataContainer;
		this.isOrigin = isOrigin;
		this.fromTo = (isOrigin ? "from" : "to") + (portIds.index == null ? "" : portIds.index);
		this.lookupID = this.fromTo + "LookUp";
		this.portBox.onkeydown = this.portBoxKeyDown.bindAsEventListener(this);
		this.portBox.onkeyup = this.portBoxKeyUp.bindAsEventListener(this);
		this.portBox.onfocus = this.portBoxFocus.bindAsEventListener(this);
		this.portBox.onfocusout = this.portBoxFocusOut.bindAsEventListener(this);
		this.pbToggle.onclick = this.pbToggleClick.bindAsEventListener(this);
		this.boxOpen = false;
		this.inputType = 'textBox';
		this.oppPortBoxControl = null;							//The PBC opposite in from/to situation
		this.dupPortBoxControl = null;							//The PBC to give the same value as this one (multi-city use)
		this.positionShowTable();
		if (!Prototype.Browser.IE)
		{
			this.pbToggle.style.top = "4px";
		};
		if (this.portSelected && this.portSelected.value != "" && this.dataContainer)
		{
			var airport = this.dataContainer.getAirportByCode(this.portSelected.value);
			if (airport)
				this.portBox.value = airport.toStringNameCode();
		}
	},

	portBoxKeyDown : function (evt)
	{
		if (evt == null) {}
		else if (evt.keyCode == 40)	// || evt.keyCode == 9)
		{  	
			var predictiveTable = $(this.lookupID);
			if (predictiveTable != null)
			{
				var anchorTags = predictiveTable.getElementsByTagName("a");
				if (this.currentSelection == "")
				{
					var firstItem = anchorTags[0];
					this.currentSelection = firstItem;
					changeBkgrnd(firstItem);
				}
				else
				{
					for (var i = 0; i < anchorTags.length; i++)
					{
						if (Element.hasClassName(anchorTags[i], "selected"))
						{
							changeBkgrnd_Blur(anchorTags[i]);
							// if not the last item
							if (i < (anchorTags.length - 1))
								nextItem = anchorTags[i+1];
							else
								nextItem = anchorTags[0];	// ie we are on the last item, go to first
							this.currentSelection = nextItem;
							changeBkgrnd(nextItem);
							i = anchorTags.length;
						}
					}
				}
			}
			else
			{
				this.pbToggleClick(evt);
			}
			fixScrollPosition(this.portBoxListDiv, this.currentSelection);
			return false;
		}
		else if (evt.keyCode == 38)
		{  
			var predictiveTable = $(this.lookupID);
			if (predictiveTable != null)
			{
				var anchorTags = predictiveTable.getElementsByTagName("a");
				if (this.currentSelection != "")
				{
					for (var i = 0; i < anchorTags.length; i++)
					{
						if (Element.hasClassName(anchorTags[i], "selected"))
						{
							changeBkgrnd_Blur(anchorTags[i]);
							// if not the last item
							if (i > 0)
								nextItem = anchorTags[i-1];
							else
								nextItem = anchorTags[anchorTags.length - 1];	// on the first item, go to the last
							this.currentSelection = nextItem;
							changeBkgrnd(nextItem);
							i = anchorTags.length;
						}
					}
					// select last item
				}
				else
				{
					var lastItem = anchorTags[anchorTags.length - 1];
					this.currentSelection = lastItem;
					changeBkgrnd(lastItem);
				}
			}
			fixScrollPosition(this.portBoxListDiv, this.currentSelection);
			return false;
		}
		else if (evt.keyCode == 13)
		{  
			if (this.currentSelection != "")
				this.currentSelection.onclick();
			else
			{
				if (this.portBoxListDiv.getElementsByTagName("a").length == 1)
					this.portBoxListDiv.getElementsByTagName("a")[0].onclick();
			}
			evt.returnValue = false; 
			evt.cancel = true;
		}
		else if (evt.keyCode == 9)
		{  
			if (this.currentSelection != "")
				this.currentSelection.onclick();
			else
			{
				if (this.portBoxListDiv.getElementsByTagName("a").length == 1)
					this.portBoxListDiv.getElementsByTagName("a")[0].onclick();
				evt.returnValue = !this.boxOpen; //if box open and > 1 element then inhibit tab
				evt.cancel = this.boxOpen;
			}
		}
	},

	portBoxKeyUp : function (evt)
	{
		if (evt == null || evt.keyCode == 16 || evt.keyCode == 40 || evt.keyCode == 38 || evt.keyCode == 13 || evt.keyCode == 9 || evt.keyCode == 37 || evt.keyCode == 39 )
		{ }
		else
		{
			this.currentSelection = "";
			this.populateMatchingAirports(evt);
		}
	},

	portBoxFocus : function (evt)
	{
		if (this.portBox.value.indexOf("--") >= 0 || this.portBox.value.indexOf("select") >= 0 || this.portBox.value == "")
		{
			this.portBox.value = "";
			//this.populateAllAirports(evt);
		}
		this.portBox.select();
		this.portBox.onfocus = this.portBoxFocus.bindAsEventListener(this);
	},

	portBoxFocusOut : function (evt)
	{
		if (!this.boxOpen)
			if (this.portBox.value == "")
				this.portBox.value = this.portSelected.value;
	},

	populateAllAirports : function (evt)
	{
		var airports = getAirportsFromAirportCodes(this.isOrigin ? this.dataContainer.getFromAirports() : this.dataContainer.getToAirports(), this.dataContainer.getAirports());
		var validAirports;
		if (this.oppSelected)
			validAirports = getValidAirports(this.oppSelected.value, airports, this.dataContainer, this.isOrigin);
		else
			validAirports = airports;
		this.populateOptions(evt, validAirports, false);
	},

	populateMatchingAirports : function (evt)
	{
		if ((this.portBox.value == "") || (this.portBox.value.indexOf('--') >= 0))
			this.populateAllAirports(evt);
		else
		{
			var matchingAirports;
			if (this.oppSelected)
				matchingAirports = getValidAirportsThatMatchString(this.oppSelected.value, this.portBox.value, this.dataContainer, this.isOrigin);
			else
				matchingAirports = getValidAirportsThatMatchString(null, this.portBox.value, this.dataContainer, this.isOrigin);
			this.populateOptions(evt, matchingAirports, true);
		}
	},

	populateOptions : function (evt, airportsToRender, highlightMatch)
	{
		var divString = '';
	  	this.portBoxListDiv.style.display = 'block';
	  	this.portShowFrame.style.display = 'block';
		this.setBoxOpen(true);
		if (airportsToRender.length == 0)
		{
			this.portBoxListDiv.innerHTML = "<span style='font-size:11px' align='left'>There are no cities matching your request. Try again or click on the + to see valid destination cities.</span>";
			var lisWid = 185;
			this.portBoxListDiv.style.width = lisWid + "px";
			this.portBoxClose.style.left = (lisWid - 18) + "px";
			this.portBoxClose.onclick = this.closePortBoxListDiv.bindAsEventListener(this);
		}
		else
		{
			var toSelections = new Array();
			this.portBoxListDiv.style.width = "305px";
			var optionListStrC1 = "";
			var optionListStrC2 = "";
			var basicColumnLength = 8;
			var singleColumnLength = 8;
			var recordCount = 0;
			singleColumnLength = (singleColumnLength < airportsToRender.length / 2) ? (airportsToRender.length / 2) : singleColumnLength;
			for (var x = 0; x < airportsToRender.length; x++)
			{
				var currentAirport = airportsToRender[x];
				if (recordCount < singleColumnLength)
					optionListStrC1 += getAirportAsHTMLString(currentAirport, this.portBox.value, highlightMatch, this.fromTo) + "<br/>";
				else
					optionListStrC2 += getAirportAsHTMLString(currentAirport, this.portBox.value, highlightMatch, this.fromTo) + "<br/>";
				recordCount++;
			}
//			optionList = "<table id='" + this.lookupID + "'>" + "<tr>" +  "" +
			optionList = "<table id='" + this.lookupID + "' class='acLookUp' >" + "<tr>" +  "" +
				"<td valign=\"top\">" + optionListStrC1 + "</td>";
			if (optionListStrC2 != "")
				optionList += "<td valign=\"top\">" + optionListStrC2 + "</td>";

			optionList += "</tr>" + "</table>";

			var scrolling = recordCount > (2 * basicColumnLength);
			var scrWid = scrolling ? 16 : 0;
			var lisWid;
			if (recordCount <= basicColumnLength)
				lisWid = 151;
			else
				lisWid = 255 + scrWid;
			this.portBoxListDiv.style.width = lisWid + "px";
			this.portBoxClose.style.left = (lisWid - scrWid - 18) + "px";

			this.portBoxListDiv.innerHTML = optionList;
			this.portBoxClose.onclick = this.closePortBoxListDiv.bindAsEventListener(this);
			
			for (var x = 0; x < airportsToRender.length; x++)
			{
				var currentAirport = airportsToRender[x];
				new PortSelection(currentAirport, this);
			}
			if (airportsToRender.length > 0)
				document.onclick = this.closePortBoxListDiv.bindAsEventListener(this);
		}

		this.portShowFrame.style.width = this.portBoxListDiv.style.width;
		this.positionShowTable();
	},

	positionShowTable : function ()
	{
		if (this.portBox.parentElement == null)
			return;
		var pbPos = this.portBox.cumulativeOffset();
		this.portShowTable.style.left = pbPos.left + "px";
		this.portShowTable.style.top = pbPos.top + this.portBox.getDimensions().height + "px";
	},

	pbToggleClick : function (evt)
	{
		if (this.boxOpen)
		{
			this.closePortBoxListDiv();
			return;
		}
		if (this.portBox.value.indexOf("--") >= 0 || this.portBox.value.indexOf("select") >= 0)
			this.portBox.value = "";

		if (this.oppPortBoxControl && this.oppPortBoxControl.boxOpen)
		{
			this.oppPortBoxControl.closePortBoxListDiv();
		}
		this.portShowTable.style.display = 'block';
		this.populateAllAirports(evt);
	  	this.portBox.select();
		var predictiveTable = $(this.lookupID);
		var anchorTags = predictiveTable.getElementsByTagName("a");
		for (var i = 0; i < anchorTags.length; i++)
			if ((this.fromTo + this.portSelected.value) == anchorTags[i].id)
			{
				this.currentSelection = anchorTags[i];
				changeBkgrnd(anchorTags[i]);
				fixScrollPosition(this.portBoxListDiv,this.currentSelection);
			}
	},

	closePortBoxListDiv : function (evt)
	{
		var targ = null;
		if (evt)
		{
			if (evt.target)
				targ = evt.target
			else if (evt.srcElement)
				targ = evt.srcElement
		}
		if (targ == null || targ.id != this.pbToggle.id)
		{
			if (this.boxOpen)
			{
				this.portBoxListDiv.style.display = 'none';
				this.portShowFrame.style.display = 'none';
				if (this.portSelected.value == '')
					this.portBox.value = '';
				else
				{
//					var previousAirport = getObjectFromArrayByCode(this.portSelected.value, this.dataContainer.getAirports());
					var previousAirport = this.dataContainer.getAirportByCode(this.portSelected.value);
					if (this.portSelected.value.indexOf('--') >= 0)
						this.portBox.value = this.portSelected.value;
					else
						this.portBox.value = previousAirport.toStringNameCode();
				}
				this.setBoxOpen(false);
			}
		}
	},

	setpbToggleOC : function (open)
	{
		var image = this.pbToggle.src;
		if (open)
			image = image.replace(this.pbtClose + '.', this.pbtOpen + '.');
		else
			image = image.replace(this.pbtOpen + '.', this.pbtClose + '.');
		this.pbToggle.src = image;
		this.pbToggle.onclick = this.pbToggleClick.bindAsEventListener(this);
	},

	setBoxOpen : function (boxOpen)
	{
		this.boxOpen = boxOpen;
		this.hideShowCloseButton(boxOpen);
		this.setpbToggleOC(!boxOpen);
		if (!boxOpen)
			this.currentSelection = "";
	},

	hideShowCloseButton : function (open)
	{
		var disp = open ? "block" : "none";
		this.portBoxClose.style.display = disp;
		this.portShowTable.style.display = disp;
	},

	setSelected : function (airport)
	{
		this.setBoxOpen(false);
  		
  		if (this.portSelected.tagName == "SELECT")
  		{
  			var sel = this.portSelected.selectedIndex;
  			this.portSelected.value = airport.code;
  			if (this.portSelected.selectedIndex < 0)
  				this.portSelected.selectedIndex = sel;
  		}
  		else if (this.portSelected.tagName == "INPUT")
  		{
  			this.portSelected.value = airport.code;
  		}
  			
  		this.portBox.value = airport.toStringNameCode();
  		this.portBoxListDiv.innerHTML = '';
		this.portBoxListDiv.style.display = 'none';
	}
}


// This is the control object for each anchor link in the selection list.
var PortSelection = Class.create();
PortSelection.prototype =
{
	initialize : function (airport, portBoxControl)
	{
		this.airport = airport;
		this.portBoxControl = portBoxControl;
		this.airportHref = $(portBoxControl.fromTo + airport.code);
		this.airportHref.onclick = this.airportHrefClick.bindAsEventListener(this);
	},
	airportHrefClick : function (evt)
	{
		this.portBoxControl.setSelected(this.airport);
		if (this.portBoxControl.dupPortBoxControl && (this.portBoxControl.dupPortBoxControl.oppPortBoxControl.portSelected.value != this.airport.code))
			this.portBoxControl.dupPortBoxControl.setSelected(this.airport);	//ensure that setting dupPortBoxControl does not cause same port in its from/to pair
	}
}

// Gets the matching airports and validates that the airport is valid for the from/to combination it will form.
function getValidAirportsThatMatchString(oppAirport, stringToMatch, dataContainer, isOrigin)
{
	var airportCodeList = isOrigin ? dataContainer.getFromAirports() : dataContainer.getToAirports();
	var airports = dataContainer.getAirports();
	var matchingAirports = getAirportsThatMatchString(stringToMatch, airportCodeList, airports);
	// ensure all matching airports are valid for this from/to combination
	var validAirports;
	if (oppAirport)
		validAirports = getValidAirports(oppAirport, matchingAirports, dataContainer, isOrigin);
	else
		validAirports = matchingAirports;
	return validAirports;
}

// Takes a list of airports and returms only those that are valid for the from/to combination
function getValidAirports(oppAirport, airports, dataContainer, isOrigin)
{
	// ensure all matching airports are valid for this from/to combination
	var validAirports = new Array();
	for (var i = 0; i < airports.length; i++)
	{
		var currentAirport = airports[i];
		// check if airport pair valid
		var flightItem = FlightDataCache.getFlightDataItem(dataContainer, oppAirport, currentAirport.code);
//		if (flightItem.isValidFlight() || oppAirport.length == 0)
//			validAirports.push(currentAirport);
		// Ensure destination port list excludes the origin port, but origin port list can include dest port.
		var valid = isOrigin || oppAirport.length == 0 ? true : flightItem.isValidFlight();
		if (valid)
			validAirports.push(currentAirport);
	}
	return validAirports;
}

function fixScrollPosition(currentDiv, currentItem)
{
	if (currentItem.offsetTop > (currentDiv.offsetHeight - 20))
		currentDiv.scrollTop = currentItem.offsetTop;
	if (currentItem.offsetTop < currentDiv.scrollTop)
		currentDiv.scrollTop = 0; 
}

// This is the control object for when a 'from' or 'to' is selected and we are required to do something.
var OriginDestinationHandler = Class.create();
OriginDestinationHandler.prototype =
{
	initialize : function (fromBoxControl, toBoxControl, dataContainer)
	{
		this.fromBoxControl = fromBoxControl;
		this.toBoxControl = toBoxControl;
		
		if (this.fromBoxControl)
		{
			this.fromBoxControl.portSelected.onchange = this.fromToChange.bindAsEventListener(this);
		}
		
		if (this.toBoxControl)
		{
			this.toBox = this.toBoxControl.portBox;
			this.toBoxControl.portSelected.onchange = this.fromToChange.bindAsEventListener(this);
		}
		
		this.dataContainer = dataContainer;
	},
	fromToChange: function (evt)
	{
		// now if the box is open, reload the contents
		if (this.toBoxControl.inputType == 'textBox' && this.toBoxControl.boxOpen)
			this.toBoxControl.populateMatchingAirports(evt);
		else
		{
			if (this.toBoxControl.inputType == 'selectBox')
				this.toBoxControl.populateToBox(this.toSelectionField.value);
			if (this.fromBoxControl.portSelected.value != '' && this.toBoxControl.portSelected.value != '' &&
				this.fromBoxControl.portSelected.value.indexOf('--' < 0) && this.toBoxControl.portSelected.value.indexOf('--' < 0))
			{
				var flightDataItem = FlightDataCache.getFlightDataItem(this.dataContainer, this.fromBoxControl.portSelected.value, this.toBoxControl.portSelected.value);
				// check that flight combo is still valid, if not clear the to box
				if (!flightDataItem.isValidFlight())
				{
					if (this.toBoxControl.inputType == 'textBox')
					{
						this.toBoxControl.portSelected.value = '';
						this.toBox.value = '';
					}
					else
						this.toBoxControl.portSelected.selectedIndex = 0;
				}
			}
		}
	},
	setInitialValues : function (from, to)
	{
		// setup frombox
		var valueMatched = false;
		for (var i = 0; i < this.fromBoxControl.portSelected.options.length; ++i)
			if (this.fromBoxControl.portSelected.options[i].value == from)
			{
				this.fromBoxControl.portSelected.selectedIndex = i;
				valueMatched = true;
				break;
			}
		if (valueMatched == false && from == '')
		{
			// presumes element 0 is always the default eg "Select a City..."
			this.fromBoxControl.portSelected.selectedIndex = 0;
		}

		if (this.toBoxControl.inputType == 'textBox' && to != null && to != '')
		{
//			var toAirport = getObjectFromArrayByCode(to, this.dataContainer.getAirports());
			var toAirport = this.dataContainer.getAirportByCode(this.portSelected.value);
			if (toAirport != null)
			{
				this.toBox.value = toAirport.airportName;
				this.toBoxControl.portSelected.value = toAirport.code;
			}
		}
		
		// run the onchange method to ensure all is setup ok
		this.fromToChange();

		// setup tobox
		if (this.toBoxControl.inputType == 'textBox')
		{
			//var toAirport = getObjectFromArrayByCode(to, this.dataContainer.getAirports());
			//this.toBox.value = toAirport.airportName;
			//this.toSelectionField.value = toAirport.code;
		}
		else
		{
			var valueMatched = false;
			for (var i = 0; i < this.toBoxControl.portBox.options.length; ++i)
				if (this.toBoxControl.portBox.options[i].value == to)
				{
					this.toBoxControl.portBox.selectedIndex = i;
					valueMatched = true;
					break;
				}
			if (valueMatched == false && from == '')
			{
				// presumes element 0 is always the default eg "Select a City..."
				this.toBoxControl.portBox.selectedIndex = 0;
			}
		}
	}
}
//End of OriginDestinationHandler


// Takes a list of airport codes and gets the airport objects for them out of the provided airport list
function getAirportsFromAirportCodes(airportCodes, allAirports)
{
	var airports = new Array();
	for (var i=0; i<airportCodes.length;i++)
	{
		var currentAirport = getObjectFromArrayByCode(airportCodes[i].code, allAirports);
		if (currentAirport) airports.push(currentAirport);
	}
	return airports;
}

// Search the airport list to find airports that match the provided string either by full name or airport code.
function getAirportsThatMatchString(stringToMatch, airportCodeList, airports)
{
	var matchingAirports = new Array();

	if (stringToMatch != '' && stringToMatch != null)
	{
		stringToMatch = String(stringToMatch).replace(/([.*+?^=!:${}()|[\]\/\\])/g, '\\$1');
		var pattern;
		if(stringToMatch.length == 1)
			pattern = new RegExp("^" + stringToMatch, "i");
		else
			pattern = new RegExp("" + stringToMatch, "i");
		var ca;
		for (var i = 0; i < airportCodeList.length; i++)
		{
			ca = getObjectFromArrayByCode(airportCodeList[i].code, airports);
			// check if code matches
			if ((ca.code == stringToMatch) || (ca.cityCode == stringToMatch) || pattern.test(ca.toStringNameCode()) || (ca.airportName == stringToMatch) || pattern.test(ca.code) || pattern.test(ca.cityCode) || pattern.test(ca.airportName))
				matchingAirports.push(ca);
		}
		if (matchingAirports.length == 0 && stringToMatch.length > 1)
		{
			//we'll try country match
			for (var i = 0; i < airportCodeList.length; i++)
			{
				ca = getObjectFromArrayByCode(airportCodeList[i].code, airports);
				if ((ca.country.countryName == stringToMatch) || pattern.test(ca.country.countryName))
					matchingAirports.push(ca);
			}
		}
	}
	return matchingAirports;
}

// Returns the index of the airport code within an array
function indexOfAirportByCode(code,airportArray)
{
    for (var i = 0; i < airportArray.length; i++)
       if (code == airportArray[i])
           return i;
    return -1;
}

// Returns the full object from an array when searching by code. Assumes that the object will have a 'code' property.
function getObjectFromArrayByCode(code, ourArray)
{
	for (var i = 0; i < ourArray.length; i++)
	   if (code == ourArray[i].code)
		   return ourArray[i];
    return null;
}


// Create the internal pad prototype
String.prototype._pad = function(width,padChar,side)
{
	var str = [side ? "" : this, side ? this : ""];
	while (str[side].length < (width ? width : 0)
		&& (str[side] = str[1] + (padChar ? padChar : " ") + str[0] ));
	return str[side];
}

// Create pad functions for general use "width" is the total width to pad to, "padChar" is the optional pad character -- default " "
String.prototype.padLeft = function(width,padChar) {
	return this._pad(width,padChar,0) };
String.prototype.padRight = function(width,padChar) {
	return this._pad(width,padChar,1) };
Number.prototype.padLeft = function(width,padChar) {
	return (""+this).padLeft(width,padChar) };
Number.prototype.padRight = function(width,padChar) {
	return (""+this).padRight(width,padChar) };
