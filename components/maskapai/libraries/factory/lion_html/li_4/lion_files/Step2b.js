function ShowTaxBreakdown()
{
	var modal = $find("mpeTaxBD");
	$("TaxBD").style.display = "block";
	if (modal) modal.show();
}


function DoCombinabilities(ID)
{
	if (onewaytrip || (outFlightCount == 0) || (inFlightCount == 0))
		return;
}


function ClearCombinabilities(table, flights, slots)
{
}


/***** Travel Insurance Start *****/
function ValidateTravelInsurance(source, args)
{
	if (document.getElementById("trInsurance").style.display == "none")
	{
		args.IsValid = true;
		return;
	}
	var result = false;
	var rblInsurance = document.getElementById(insPre + "rblInsurance");
	var radioButtons = rblInsurance.getElementsByTagName("input"); 
	if (radioButtons[0].checked || radioButtons[1].checked)
		result = true;

	args.IsValid = result;
}

function ResetInsuranceDisplay()
{
	if(document.getElementById(insPre + "sYesLinePremium") != null)
		document.getElementById(insPre + "sYesLinePremium").innerHTML = "0";
//	if(document.getElementById(insPre + "trCodeShareMessage") != null)
//		document.getElementById(insPre + "trCodeShareMessage").style.display = "none";
//	if(document.getElementById(insPre + "InsuranceTableDetails") != null)
//		document.getElementById(insPre + "InsuranceTableDetails").style.backgroundColor = "transparent";
	var rblInsurance = document.getElementById(insPre + "rblInsurance");
	if(rblInsurance != null)
	{
		var radioButtons = rblInsurance.getElementsByTagName("input");

		if (radioButtons != null && radioButtons.length > 0)
		{
			// Select default option for Insurance
			if(insuranceSelectedByDefault == "True" || insuranceSelectedByDefault == "true")
			{
				radioButtons[0].checked = true;
				if(document.getElementById("txtUpdateInsurance") != null)
					document.getElementById("txtUpdateInsurance").value = "yes";
			}
			else
			{
				radioButtons[1].checked = true;
				if(document.getElementById("txtUpdateInsurance") != null)
					document.getElementById("txtUpdateInsurance").value = "no";			
			}
//			radioButtons[1].checked = true;
////			radioButtons[0].disabled = false;
////			radioButtons[1].disabled = false;
//			if(document.getElementById("txtUpdateInsurance") != null)
//				document.getElementById("txtUpdateInsurance").value = "no";
		}
	}
}


function RefreshDisplay(btn, message)
{
	var result = btn;	
	
	if(displayInsuranceReminder == null)
		displayInsuranceReminder = "False";
		
	if (btn == "no" && (displayInsuranceReminder == "True" || displayInsuranceReminder == "true"))
	{
		// Friendly Remider Popup
		if (message == null || message == "")
			message = "A Friendly Reminder!\r\n\r\nWe highly recommend that you arrange travel insurance for your customer – not only does this product protect from unexpected events but it also earns you additional revenue through Lion Air incentives.\r\n\r\nTo keep travel insurance in your itinerary, please click [OK].";

		var alteredMessage = "";
		var pairs = message.split("-|-");
		for (var i = 0; i < pairs.length; i++)
		{
			alteredMessage += pairs[i];
			if (i != (pairs.length - 1))
				alteredMessage += "\r\n\r\n";
		}
		if (alteredMessage != "")
			message = alteredMessage;

		var agree = confirm(message);
		if (agree)
		{
			var rblInsurance = document.getElementById(insPre + "rblInsurance");
			if(rblInsurance != null)
			{
				var radioButtons = rblInsurance.getElementsByTagName("input"); 
				if (radioButtons != null && radioButtons.length >= 0)
				{
					radioButtons[0].checked = true;
					result = "yes";
				}
			}
		}
	}
	
	if(document.getElementById("txtUpdateInsurance") != null)
		document.getElementById("txtUpdateInsurance").value = result;
}

function doContinue() {
    var result = false;
    var rblInsurance = document.getElementById(insPre + "rblInsurance");
    var radioButtons = rblInsurance.getElementsByTagName("input");
    
    if (radioButtons[0].checked || radioButtons[1].checked)
        result = true;
        
    if (result)
        swapProcessingDivs();
}

/***** Travel Insurance End *****/
