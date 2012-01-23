SetAutoFocus()

if (goToRow != null){
	document.getElementById(goToRow).scrollIntoView(true);
}

if (document.getElementById('ctrAgentBase_ctrAgentHome_ctrAgentFlightAvailability_ctrFlightSearchBase_rdoOneway') != null) {
	if (document.getElementById('ctrAgentBase_ctrAgentHome_ctrAgentFlightAvailability_ctrFlightSearchBase_rdoOneway').checked == true) {
		HideGroup('FlightSearch','none')
	}
}		

if (CcPreselect.length > 0){
	ShowCvvCode(CcPreselect)
}

function CopyFrequentFlyer(o){
	document.getElementById('ClientNumber_Value').value = document.getElementById('ctrBooking:ctrPassengerInfosBase:ctlPassengerList:PassengerList:_ctl1:ClientNumber').value
	__doPostBack('ctrBooking$ctrPassengerInfosBase$ctlPassengerContactInfo$cboFrequentFlyer','')
}


function HideGroup(Name,Action){
	var NN4 = document.layers? true : false;
	var IE4 = document.all? true : false;
	var FF 	= document.getElementById? true : false;
	if(Action == 'none') {
		SingleFlight = true;
		if (IE4) {
			document.all[Name].style.visibility = "hidden";
		} else if(NN4) {
			document.layers[Name].visibility = "hidden";
		} else if(FF) {
			document.getElementById(Name).style.display = "none";
		} else {
			alert('Unknown browser category.');
		}
	} else {
		SingleFlight = false;
		if (IE4) {
			document.all[Name].style.visibility = "visible";
		} else if(NN4) {
			document.layers[Name].visibility = "show";
		} else if(FF) {
			document.getElementById(Name).style.display = "block";
		} else {
			alert('Unknown browser category.');
		}
	}
/*
	var col = document.getElementsByName(Name)
	for (var i=0;i <= col.length - 1;i++){
		col[i].style.visibility = (Action == 'none') ? 'hidden' : 'visible';
		SingleFlight = (Action == 'none') ? true : false;
	}
*/
}

function CheckAdultInfant(o){
	var iA = document.getElementById(o + '_cmbAdult').options[document.getElementById(o + '_cmbAdult').selectedIndex].value
	var iI = document.getElementById(o + '_cmbInfant').options[document.getElementById(o + '_cmbInfant').selectedIndex].value
	if (parseInt(iA) < parseInt(iI)){
		alert('Tidak memungkinkan untuk reservasi bayi lebih banyak dari orang dewasanya')
		document.getElementById(o + '_cmbInfant').selectedIndex = 0
	}
}

function ValidSDate(o){
	Status =  true;
	if (document.getElementById(o + '_rdoOneway').checked != true) {
		if (LibDaysDiff(GetCalenderDate(o + '_ctlReturnDate'),GetCalenderDate(o + '_ctlDepatureDate')) < 0){
			alert('Mohon cek tanggal kepulangan anda ! seharusnya pada hari yang sama atau setelah tanggal keberangkatan')
			Status =  false;
		}
	}
	var adult = document.getElementById(o + '_ctlPassengerType_cmbAdult');
	var child = document.getElementById(o + '_ctlPassengerType_cmbChild');
	var infant = document.getElementById(o + '_ctlPassengerType_cmbInfant');
	if(adult.value == 0){
		if(child.value == 0 || infant.value > 0){
			alert('You have selected no passengers.')
			Status =  false;
		}
	}
}
	
function GetCalenderDate(o){
	var od = document.getElementById(o+'_cmbDay')
	var ot = document.getElementById(o+'_cmbMonthYear')
	var y = ot.options[ot.selectedIndex].value.slice(0,4)
	var m = ot.options[ot.selectedIndex].value.slice(4,6)
	var d = od.options[od.selectedIndex].value 
	return new Date(y,m-1,d,0,0,0)
}

function CopyFirstPassenger(o,m){
	if (document.getElementById(m + '_cboFirstPassenger').checked){
		var i = 1
		var fn = document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_FirstName').value
		var ln = document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_LastName').value
		var l = trim(ln)
		var f = trim(fn)
		if (l > 2 || f > 1){
			document.getElementById(m + '_tboNameOnCard').value = strim(fn) + ' ' +  strim(ln)
		}
		else{
			document.getElementById(m + '_cboFirstPassenger').checked = false
		}	
	}
	else {
		document.getElementById(m + '_tboNameOnCard').value = ''
	}
}

function SetFirstPassenger(m,s){
	if (document.getElementById(m + '_cboFirstPassenger').checked){
		document.getElementById(m + '_tboNameOnCard').value = s
	}
	else{
		document.getElementById(m + '_tboNameOnCard').value = s
	}
}
function ValidPassengerGrid(o){
	Status =  true
	var e = ''
	var l = trim(document.getElementById(o + '_LastName').value)
	var f = trim(document.getElementById(o + '_FirstName').value)
	var d = document.getElementById(o + '_DateOfBirth').value
	if (l < 2 || f < 1){
		Status =  false;
		e += 'Masukkan nama penumpang \r'
	}
	//if (trim(d) > 0 && Date.isValid(d,DateValid) == false){
	if (trim(d) > 0 && isDateExist(d) == false){
		Status =  false;
		e += 'Masukkan tanggal lahir ' + DateValidText + ' \r'	
	}
	if (e.length > 0) {
		alert(e)
	}
}

function ValidGroupPassengers(o){
	// o = 'ctrBooking_ctrBookingSummery_CtrPassengerList'
	Status =  true
	var e = ''
	var il = (document.getElementById('PassengerList').rows.length - 1) / 2

	for (i=1;i<=il ;i++){
		var ln  = document.getElementById(o+'_PassengerList__ctl' + i + '_Lastname');		
		var fn  = document.getElementById(o+'_PassengerList__ctl' + i + '_Firstname');
		var dn 	= document.getElementById(o+'_PassengerList__ctl' + i + '_DateOfBirth');
		var ped = document.getElementById(o+'_PassengerList__ctl' + i + '_passport_expiry_date'); 
		var pid = document.getElementById(o+'_PassengerList__ctl' + i + '_passport_issue_date'); 
	
		if ((ln != null) && (fn != null)) {
			var l = trim(ln.value);
			var f = trim(fn.value);
			if((IfAlphaOnly(ln.value) == false && l > 0) || (IfAlphaOnly(fn.value) == false && f > 0)){					
//			if(IfAlphaOnly(ln.value) == false || IfAlphaOnly(fn.value) == false){					
				Status =  false;
				e += '<li>Masukkan nama penumpang di penumpang ' + (addFullNumerical(i,3))+ '</li>'
				ln.className = (IfAlphaOnly(ln.value) == false && l > 0) ? 'error' : '';
				fn.className = (IfAlphaOnly(fn.value) == false && f > 0) ? 'error' : '';				
//				ln.className = (IfAlphaOnly(ln.value) == false) ? 'error' : '';
//				fn.className = (IfAlphaOnly(fn.value) == false) ? 'error' : '';
			}
			else{
				ln.className = ''
				fn.className = ''
			}
		}

		if (dn != null) {	// Date of Birth
			var d = dn.value	
			//if (trim(d) > 0 && Date.isValid(d,DateValid) == false){
			if (trim(d) > 0 && isDateExist(d) == false){
				Status =  false;
				e += '<li>' + eMsg[5] + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>'
				dn.className = 'error'
			}
			else{
				if (LibDaysDiff(changeToDate(d),new Date()) > 0){
					Status =  false;
					e += '<li>' + eMsg[17] + (addFullNumerical(i,3))+ '</li>'
					dn.className = 'error'
				}
				else{
					dn.className = ''
				}
			}
		}
	
		if (ped != null) {
			var ed = ped.value
			//if (trim(ed) > 0 && Date.isValid(ed,DateValid) == false){
			if (trim(ed) > 0 && isDateExist(ed) == false){
				Status =  false;
				e += '<li>Masukkan masa berlaku passport di penumpang ' + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>'
				ped.className = 'error'
			}
			else{
				ped.className = ''
			}
		}
		
		if (pid != null) {
			var id = pid.value
			//if (trim(id) > 0 && Date.isValid(id,DateValid) == false){
			if (trim(id) > 0 && isDateExist(id) == false){
				Status =  false;
				e += '<li>Masukkan tanggal issue pasport dipassenger ' + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>'
				pid.className = 'error'
			}
			else{
				pid.className = ''
			}
		}
	}
	
	var tl = (i + 2) / 3
	if (e.length > 0) {
		Container = document.getElementById(o+'_PassengerList__ctl' + tl + '_LabError')
		Container.innerHTML = 	'<ul>' + e + '</ul>'
	}
	else{
		Container = document.getElementById(o+'_PassengerList__ctl' + tl + '_LabError')
		Container.innerHTML = 	''
	}
}

function IfAlphaOnly(s){
	var i
	var b = false 
	if (s.length != 0){
		for (i=0;i < s.length;i++){
			var c = s.charCodeAt(i);
			if (c == 45|| c == 39 || c == 46 || c == 32){
				b = true;
			}
			else if ((c > 47 && c < 58)){
				return false;
			}
			else if (((c > 96 && c < 123) || (c > 64 && c < 91))  == false){
				return false;
			}
		}
		b = true
	}
	else{
		b = false;
	}
	return b
}

function ValidUpdatePayment(o){	
	Status 	= true
	var e 	= ''	
	var noc = document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboNameOnCard');
	var cdn	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboDocumentNumber');
	var cvv	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboCvvCode');
	var ad1	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboStreet');
	var cty	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboCity');
	var pc 	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboZipCode');
	var st 	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboState');
	var ctr = document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_cmbCountryRcd');
	var rcvv = document.getElementById(CcPreselect + '_cbmFormOfPaymentSubtype').options[document.getElementById(CcPreselect + '_cbmFormOfPaymentSubtype').selectedIndex].value.split('|')

	if(document.getElementById('formCC').style.display != 'none'){	
		//--->  Name On Card
		if (trim(noc.value) == 0){
			Status =  false;
			e += "<li>Masukkan nama dikartu</li>"
			noc.className = 'error';
		}else{
			noc.className = '';
		}
		//--->  Credit Card Number
		if (trim(cdn.value) == 0){
			Status =  false;
			e += "<li>Masukkan nomor kartu kredit</li>"
			cdn.className = 'error';
		}else{
			cdn.className = '';
		}
		//--->  CVV
		if (trim(cvv.value) == 0 && rcvv[2]==1){
			Status =  false;
			e += "<li>Masukkan nomor cvv</li>"
			cvv.className = 'error';
		}else{
			cvv.className = '';
		}
		//--->  Address 1
		if (trim(ad1.value) == 0){
			Status =  false;
			e += "<li>Masukkan alamat rumah 1</li>"
			ad1.className = 'error';
		}else{
			ad1.className = '';
		}
		//--->  Town/City
		if (trim(cty.value) == 0){
			Status =  false;
			e += "<li>Masukkan nama kota</li>"
			cty.className = 'error';
		}else{
			cty.className = '';
		}
		//--->  Postal Code
		if (trim(pc.value) == 0){
			Status =  false;
			e += "<li>Masukkan kode pos</li>"
			pc.className = 'error';
		}else{
			pc.className = '';
		}
		//--->  County
		//if (trim(st.value) == 0){
		//	Status =  false;
		//	e += "<li>Please supply a valid County</li>"
		//	st.className = 'error';
		//}else{
		//	st.className = '';
		//}
		//--->  Country
		if (trim(ctr.options[ctr.selectedIndex].value)==0){
			Status =  false;
			e += "<li>Please supply a valid Country</li>"
			ctr.className = 'error';
		}else{
			ctr.className = '';
		}
		
		if (e.length > 0) {		
			Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer');
			if(Container != null) {Container.innerHTML = '<ul>' + e + '</ul>'};
		}
		else{
			ContainerBase = document.getElementById('ContainerPayment');
			if(ContainerBase != null) {ContainerBase.style.visibility = 'hidden'};
			ContainerPageHeader = document.getElementById('PageHeader');
			if(ContainerPageHeader != null) {ContainerPageHeader.style.visibility = 'hidden'};
			CvvForms = document.getElementById('CvvForm');
			CvvForms.style.visibility = 'hidden';
			CvvLabels = document.getElementById('CvvLabel');
			CvvLabels.style.visibility = 'hidden';
			Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer');
			Container.innerHTML = '';
			Container = document.getElementById('PaymentCommentContainer');
			if(ContainerBase != null) {Container.style.display = 'block'};
			var bStepBackPayment = document.getElementById('StepBackPayment');
			var bCancelPayment = document.getElementById('CancelPayment');
			if(bStepBackPayment != null) {bStepBackPayment.style.display = 'none'};
			if(bCancelPayment != null) {bCancelPayment.style.display = 'none'};
		}
	}
}

function CheckValidatePayment(o){	
	var e 	= ''	
	var noc = document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboNameOnCard');
	var cdn	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboDocumentNumber');
	var cvv	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboCvvCode');
	var ad1	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboStreet');
	var cty	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboCity');
	var pc 	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboZipCode');
	var st 	= document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_tboState');
	var ctr = document.getElementById(o+'_CtrPaymentsBase_CtlPaymentCreditCard_cmbCountryRcd');
	var rcvv = document.getElementById(CcPreselect + '_cbmFormOfPaymentSubtype').options[document.getElementById(CcPreselect + '_cbmFormOfPaymentSubtype').selectedIndex].value.split('|')

	if(document.getElementById('formCC').style.display != 'none'){	
		//--->  Name On Card
		if (trim(noc.value) == 0){
			e += "<li>Masukkan nama dikartu</li>"
			noc.className = 'error';
		}else{
			noc.className = '';
		}
		//--->  Credit Card Number
		if (trim(cdn.value) == 0){
			e += "<li>Masukkan nomor kartu kredit</li>"
			cdn.className = 'error';
		}else{
			cdn.className = '';
		}
		//--->  CVV
		if (trim(cvv.value) == 0 && rcvv[2]==1){
			e += "<li>Masukkan nomor cvv</li>"
			cvv.className = 'error';
		}else{
			cvv.className = '';
		}
		//--->  Address 1
		if (trim(ad1.value) == 0){
			e += "<li>Masukkan alamat rumah 1</li>"
			ad1.className = 'error';
		}else{
			ad1.className = '';
		}
		//--->  Town/City
		if (trim(cty.value) == 0){
			e += "<li>Masukkan nama kota</li>"
			cty.className = 'error';
		}else{
			cty.className = '';
		}
		//--->  Postal Code
		if (trim(pc.value) == 0){
			e += "<li>Masukkan kode pos</li>"
			pc.className = 'error';
		}else{
			pc.className = '';
		}
		//--->  County
		//if (trim(st.value) == 0){
		//	e += "<li>Please supply a valid County</li>"
		//	st.className = 'error';
		//}else{
		//	st.className = '';
		//}
		//--->  Country
		if (trim(ctr.options[ctr.selectedIndex].value)==0){
			e += "<li>Please supply a valid Country</li>"
			ctr.className = 'error';
		}else{
			ctr.className = '';
		}
		
		return e;
	}
}

function ValidPayment(o){
	var Status 	= true
	var e 	= ''
	var noc = trim(document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboNameOnCard').value)
	var cdn	= trim(document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboDocumentNumber').value)
	var cvv	= trim(document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboCvvCode').value)
	var ad1	= trim(document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboStreet').value)
	var cty	= trim(document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboCity').value)
	var pc 	= trim(document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboZipCode').value)
	var st 	= trim(document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboState').value)
	var ctr = document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_cmbCountryRcd')
	//--->  Name On Card
	if (noc==0){
		Status =  false;
		e += "<li>Masukkan nama dikartu</li>"
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboNameOnCard').className = 'error';
	}else{
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboNameOnCard').className = '';
	}
	//--->  Credit Card Number
	if (cdn==0){
		Status =  false;
		e += "<li>Masukkan nomor kartu kredit</li>"
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboDocumentNumber').className = 'error';
	}else{
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboDocumentNumber').className = '';
	}
	//--->  CVV
	if (cvv==0){
		Status =  false;
		e += "<li>Masukkan nomor cvv</li>"
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboCvvCode').className = 'error';
	}else{
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboCvvCode').className = '';
	}
	//--->  Address 1
	if (ad1==0){
		Status =  false;
		e += "<li>Masukkan alamat rumah 1</li>"
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboStreet').className = 'error';
	}else{
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboStreet').className = '';
	}
	//--->  Town/City
	if (cty==0){
		Status =  false;
		e += "<li>Masukkan nama kota</li>"
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboCity').className = 'error';
	}else{
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboCity').className = '';
	}
	//--->  Postal Code
	if (pc==0){
		Status =  false;
		e += "<li>Masukkan kode pos</li>"
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboZipCode').className = 'error';
	}else{
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboZipCode').className = '';
	}
	//--->  County
	//if (st==0){
	//	Status =  false;
	//	e += "<li>Please supply a valid County</li>"
	//	document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboState').className = 'error';
	//}else{
	//	document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_tboState').className = '';
	//}
	//--->  Country
	if (trim(ctr.options[ctr.selectedIndex].value)==0){
		Status =  false;
		e += "<li>Please supply a valid Country</li>"
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_cmbCountryRcd').className = 'error';
	}else{
		document.getElementById(o+'_CtlPaymentsBase_CtlPaymentCreditCard_cmbCountryRcd').className = '';
	}
	
	return e;
	
	if (e.length > 0) {
//		Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer');
//		Container.innerHTML = 	'<ul>' + e + '</ul>';
	}
	else{
		//ContainerBase = document.getElementById('ContainerPayment');
		//ContainerBase.style.visibility = 'hidden';
		//ContainerPageHeader = document.getElementById('PageHeader');
		//ContainerPageHeader.style.visibility = 'hidden';
		//CvvForms = document.getElementById('CvvForm');
		//CvvForms.style.visibility = 'hidden';
		//CvvLabels = document.getElementById('CvvLabel');
		//CvvLabels.style.visibility = 'hidden';
		//Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer');
		//Container.innerHTML = '';
		//Container = document.getElementById('PaymentCommentContainer');
		//Container.style.display = 'block';
		//var bStepBackPayment = document.getElementById('StepBackPayment');
		//var bCancelPayment = document.getElementById('CancelPayment');
		//bStepBackPayment.style.display = 'none';
		//bCancelPayment.style.display = 'none';
	}
}

function ValidRegistration(o){
	Status = true;
	var e = '';
	var pty	= (document.getElementsByName('PaymentTabs_Value') != null)? document.getElementsByName('PaymentTabs_Value').value : null;
	var pm = document.getElementById(o+'_ctlPassengerContactInfo_phone_mobile')
	var ph = document.getElementById(o+'_ctlPassengerContactInfo_phone_home')
	var pb = document.getElementById(o+'_ctlPassengerContactInfo_phone_business')
	var p = trim(pm.value) + trim(ph.value) + trim(pb.value)	
	var m = document.getElementById(o+'_ctlPassengerContactInfo_contact_email').value
	var c = trim(document.getElementById(o+'_ctlPassengerContactInfo_contact_name').value)	
	var tl = (document.getElementById('PassengerList').rows.length - 3) / 2
	//var il = (document.getElementById('PassengerList').rows.length - pl) / 2
	var il = (document.getElementById('PassengerList').rows.length - rowheadnfoot) / rowPassengerList;
	
	for (i=1;i<=il ;i++){
		var ln = document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_Lastname');		
		var fn = document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_Firstname');		
		var wt = document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_PassengerWeight');
		var dn = document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_DateOfBirth');
        var dn_type = document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_Label2');
        
		if ((ln != null) && (fn != null)) {
			var l = trim(ln.value);
			var f = trim(fn.value);
			if (l < 2 || f < 1){
				Status =  false;
				e += '<li>Masukkan nama penumpang di penumpang ' + (addFullNumerical(i,3))+ '</li>'
				ln.className = (l < 2) ? 'error' : '';
				fn.className = (f < 2) ? 'error' : '';
			}
			else if(IfAlphaOnly(ln.value) == false || IfAlphaOnly(fn.value) == false){
				Status =  false;
				e += '<li>Masukkan nama penumpang di penumpang ' + (addFullNumerical(i,3))+ '</li>'
				ln.className = (IfAlphaOnly(ln.value)) ? '' : 'error';
				fn.className = (IfAlphaOnly(fn.value)) ? '' : 'error';
			}
			else{
				ln.className = ''
				fn.className = ''
			}
		}
		
		if (wt != null) {
			var w = trim(wt.value);
			if (w < 1) {
				Status =  false;
				e += '<li>Please supply a valid Passenger Weight at passenger ' + (addFullNumerical(i,3))+ '</li>';
				wt.className = 'error';
			}
			else{	
				if (isInteger(strim(wt.value)) < 1){
					Status =  false;
					e += '<li>Please supply a valid Passenger Weight at passenger ' + (addFullNumerical(i,3))+ '</li>';
					wt.className = 'error';
				}
				else{
					wt.className = '';
				}
			}
		}
	    
	    /*
		if (dn != null) {	// Date of Birth
			var d = dn.value
			//if (trim(d) > 0 && Date.isValid(d,DateValid) == false){
			if (trim(d) > 0 && isDateExist(d) == false){
				Status =  false;
				e += '<li>' + eMsg[5] + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>'
				dn.className = 'error'
			}
			else{
				if (LibDaysDiff(changeToDate(d),new Date()) > 0){
					Status =  false;
					e += '<li>' + eMsg[17] + (addFullNumerical(i,3))+ '</li>'
					dn.className = 'error'
				}
				else{
					dn.className = ''
				}
			}
		}
		*/
		
		if (dn != null) {	// Date of Birth
			    var d = dn.value;
			    var dt = dn_type.innerHTML;
			    if (trim(d) > 0 && isDateExist(d) == false){
				    Status =  false;
				    e += '<li>' + eMsg[5] + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>';
				    dn.className = 'error';
			    }
			    else{
			        if (LibDaysDiff(changeToDate(d),new Date()) > 0){
					    Status =  false;
					    e += '<li>' + eMsg[17] + (addFullNumerical(i,3))+ '</li>';
					    dn.className = 'error';
				    }
				    else{
				        if (dt == "CHD" || dt == "INF"){
				            document.getElementById('star' + i).style.visibility = "visible";
				            if(isDateExist(d) == false){
				                Status =  false;
					            e += '<li>' + eMsg[5] + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>';
					            dn.className = 'error';
				            }else{
				                //Child  2-11 year
                                //Infant 1-23 month
                                var MIN_CHD_AGE = (2*12);
                                var MAX_CHD_AGE = (11*12);

                                var MIN_INF_AGE = (1);
                                var MAX_INF_AGE = (23);
                                
                                var dd = Number(d.substring(0,2));
                                var mm = Number(d.substring(3,5));
                                var yy = Number(d.substring(6,10));
                                    mm = mm - 1;
                                var iDate = new Date(yy,mm,dd);
                                var cDate = new Date();
                                var mmAge = dateDiff("m",iDate,cDate,1,1); 				        
    				            //var fDate = document.getElementById('ctrBooking_ctrItineraryBase_ctrItineraryGrid_grdItineraryGrid__ctl0_Td46');
							    var fDate = document.getElementById('ctrBooking_ctrPassengerInfosBase_ctlBookingInfo_LabDepartureDate');
				                if(dt == "CHD"){
				                    if(!((MIN_CHD_AGE<=mmAge)&&(mmAge<=MAX_CHD_AGE))){
                                        e += '<li>' + 'Usia anak-anak harus diantara 2-11 tahun ' + (addFullNumerical(i,3))+ '</li>';
                                        Status =  false;
			                            dn.className = 'error';                            
				                    }else{
					                    dn.className = '';
				                    }
				                }else if(dt == "INF"){
                                    if(!((MIN_INF_AGE<=mmAge)&&(mmAge<=MAX_INF_AGE))){
                                        e += '<li>' + 'Usia bayi harus diantara 1-23 bulan ' + (addFullNumerical(i,3))+ '</li>';
			                            Status =  false;
			                            dn.className = 'error';
		                            }else{
					                    dn.className = '';
				                    }
				                }
				            }
			            }else{
		                    dn.className = '';
	                    }
				    }
			    }
	        }

		if (pt == true){
			var p0	= document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_passport_number')
			var p1	= document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_passport_issue_place')
			var p2	= document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_passport_issue_date')
			var p3	= document.getElementById(o+'_ctlPassengerList_PassengerList__ctl' + i + '_passport_expiry_date')

			if (p0 != null) {
				var p0i = trim(p0.value);
				if (ptv == true && p0i < 1){
					Status =  false;
					e += '<li>Masukkan nomor passport di penumpang ' + (addFullNumerical(i,3))+ '</li>'
					p0.className = (p0i < 1) ? 'error' : '';
				}
				else{
					p0.className = ''
				}
			}
/*			
			if (p1 != null) {
				var p1i = trim(p1.value)
				if (ptv == true && p1i < 1){
					Status =  false;
					e += '<li>Masukkan tempat issue di penumpang ' + (addFullNumerical(i,3))+ '</li>'
					p1.className = (p1i < 1) ? 'error' : '';
				}
				else{
					p1.className = ''
				}
			}			
*/				
			if (p2 != null) {	// Issue Date
				var	p2v	= p2.value;
				if(trim(p2v) > 0){
				    //if ((ptv == true || trim(p2v) > 0) && Date.isValid(p2v,DateValid) == false){
				    if ((ptv == true || trim(p2v) > 0) && isDateExist(p2v) == false){
					    Status =  false;
					    e += '<li>' + eMsg[13] + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>'
					    p2.className = 'error'
				    }
				    else{
					    if (LibDaysDiff(changeToDate(p2v),new Date()) > 0){
						    Status =  false;
						    e += '<li>' + eMsg[15] + (addFullNumerical(i,3))+ '</li>'
						    p2.className = 'error'
					    }
					    else{
						    p2.className = ''
					    }
				    }
				}
			}

			if (p3 != null) {	// Expiry Date
				var	p3v	= p3.value
				if(trim(p3v) > 0){
				    //if ((ptv == true || trim(p3v) > 0) && Date.isValid(p3v,DateValid) == false){
				    if ((ptv == true || trim(p3v) > 0) && isDateExist(p3v) == false){
					    Status =  false;
					    e += '<li>' + eMsg[12] + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>'
					    p3.className = 'error'
				    }
				    else{
					    if (LibDaysDiff(changeToDate(p3v),new Date()) < 0){
						    Status =  false;
						    e += '<li>' + eMsg[16] + (addFullNumerical(i,3))+ '</li>'
						    p3.className = 'error'
					    }
					    else{
						    p3.className = ''
					    }
				    }
				}
			}		
		}
	}	

	if (c==0){
		Status =  false;
		e += "<li>Masukkan contact person</li>"
		document.getElementById(o+'_ctlPassengerContactInfo_contact_name').className = 'error';
	}
	else{
		document.getElementById(o+'_ctlPassengerContactInfo_contact_name').className = '';
	}

	if (p==0){
		Status =  false;
		e += "<li>Masukkan nomor telepon rumah</li>"
		pm.className = 'error'
		ph.className = 'error'
		pb.className = 'error'
	}
	else{
		pm.className = ''
		ph.className = ''
		pb.className = ''
	}
	
	if (trim(m) == 0) {
		Status =  false;
		e += "<li>Masukkan alamat email</li>"
		document.getElementById(o+'_ctlPassengerContactInfo_contact_email').className = 'error';
	}else if (ValidEmail(m)!= true){
		Status =  false;
		e += "<li>Masukkan alamat email sesuai format</li>";
		document.getElementById(o+'_ctlPassengerContactInfo_contact_email').className = 'error';
	}else{
		document.getElementById(o+'_ctlPassengerContactInfo_contact_email').className = ''
	}

	if (CcValidation != undefined){
		if (CcValidation.length > 0 && pty == 'CC') {
			var CcStatus =  true;
			var st = document.getElementById(CcValidation + '_tboStreet')
			var cy = document.getElementById(CcValidation + '_tboCity')
			var sa = document.getElementById(CcValidation + '_tboState')
			var zi = document.getElementById(CcValidation + '_tboZipCode')
			if (trim(st.value) == 0){
				CcStatus =  false;
				st.className = 'error'
			}
			else{
				st.className = ''
			}
			if (trim(cy.value) == 0){
				CcStatus =  false;
				cy.className = 'error'	
			}
			else{
				cy.className = ''		
			}
			if (trim(sa.value) == 0){
				CcStatus =  false;
				sa.className = 'error'
			}
			else{
				sa.className = ''	
			}
			if (trim(zi.value) == 0){
				CcStatus =  false;
				zi.className = 'error'
			}
			else{
				zi.className = ''	
			}
			if (!CcStatus){
				Status =  false;
				e += "<li>Masukkan alamat rumah</li>"
			}
		}	

	//if (document.getElementById(o+'_cboConfirm').checked == false){
	//	Status =  false;
	//	e += "<li>Mohon diterima syarat dan kondisi</li>"
	//}

		if (document.getElementById('formCC') != null) {
			if(document.getElementById('formCC').style.display != 'none'){
				var vp = ValidPayment(o);
				if (vp.length > 0){
					Status =  false;
					e += vp
				}
			}
        }
	}
	if (document.getElementById('formExternal') != null) {
	    // Validate Form External
	    if (document.getElementById('formExternal').style.display != 'none') {
	        var refCode = strim(document.getElementById(o + '_CtlPaymentsBase_ctrPaymentExternal_txtRefCode').value);
	        var refConfirmCode = strim(document.getElementById(o + '_CtlPaymentsBase_ctrPaymentExternal_txtRefConfirmCode').value);
	        if (refCode == "") {
	            Status = false;
	            e += "<li>Musakan User ID Klik BCA yang benar</li>";
	            document.getElementById(o + '_CtlPaymentsBase_ctrPaymentExternal_txtRefCode').className = 'error';
	        }
	        else {
	            document.getElementById(o + '_CtlPaymentsBase_ctrPaymentExternal_txtRefCode').className = '';
	        }
	        if (refConfirmCode == "") {
	            Status = false;
	            e += "<li>Musakan Konfirmasi User ID Klik BCA yang benar</li>";
	            document.getElementById(o + '_CtlPaymentsBase_ctrPaymentExternal_txtRefConfirmCode').className = 'error';
	        }
	        else {
	            document.getElementById(o + '_CtlPaymentsBase_ctrPaymentExternal_txtRefConfirmCode').className = '';
	        }
	        if (refCode != "" && refConfirmCode != "") {
	            if (refCode != refConfirmCode) {
	                Status = false;
	                e += "<li>Konfirmasi User ID Klik BCA tidak sama</li>";
	                document.getElementById(o + '_CtlPaymentsBase_ctrPaymentExternal_txtRefCode').className = 'error';
	                document.getElementById(o + '_CtlPaymentsBase_ctrPaymentExternal_txtRefConfirmCode').className = 'error';
	            }
	            else {
	                document.getElementById(o + '_CtlPaymentsBase_ctrPaymentExternal_txtRefCode').className = '';
	                document.getElementById(o + '_CtlPaymentsBase_ctrPaymentExternal_txtRefConfirmCode').className = '';
	            }
	        }
	    }
	}
	
	if (e.length > 0) {
		Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer')
		Container.innerHTML = 	'<ul>' + e + '</ul>'
	}
	else{
        var ip = document.getElementById('ctrBooking_ctrPassengerInfosBase_lblIp');
        if (ip != null) {
	        if(confirm('Alamat IP anda '+ ip.innerHTML + '. Untuk alasan keamanan dan pencegahan fraud, alamat IP anda akan tercatat pada database kami')){
		        Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer')
		        Container.innerHTML = 	''
		        Container = document.getElementById('PaymentCommentContainer')
		        var b = document.getElementById(o+'_btmSubmitBooking')
		        b.style.visibility = 'hidden'
		        Container.style.display = 'block'
		    }else{
		        Status =  false;
		    }
		}
	}	
}

function ValidUpdateBooking(o){
	Status =  true
	var e = ''
	var pm = document.getElementById(o+'_ctrPassengerContactInfo_phone_mobile')
	var ph = document.getElementById(o+'_ctrPassengerContactInfo_phone_home')
//	var pb = document.getElementById(o+'_ctrPassengerContactInfo_phone_business')
	var p = trim(pm.value) + trim(ph.value) 
	var m = document.getElementById(o+'_ctrPassengerContactInfo_contact_email')
	var c = document.getElementById(o+'_ctrPassengerContactInfo_contact_name')
	if (trim(c.value)==0){
		Status =  false;
		e += "<li>Masukkan contact person</li>"
		c.className = 'error';
	}
	else{
		c.className = '';
	}

	if (p==0){
		Status =  false;
		e += "<li>Masukkan nomor telepon rumah</li>"
		pm.className = 'error'
		ph.className = 'error'
//		pb.className = 'error'
	}
	else{
		pm.className = ''
		ph.className = ''
//		pb.className = ''
	}
	
	if (trim(m.value) == 0) {
		Status =  false;
		e += "<li>Masukkan alamat email</li>"
		m.className = 'error';
	}else if (ValidEmail(m.value)!= true){
		Status =  false;
		e += "<li>Masukkan alamat email sesuai format</li>";
		m.className = 'error';
	}else{
		m.className = ''
	}

	if (document.getElementById('formCC') != null) {
		if(document.getElementById('formCC').style.display != 'none'){
			var vp = CheckValidatePayment(o);
			if (vp.length > 0){
				Status =  false;
				e += vp
			}
		}
	}
	
	if (e.length > 0) {
		Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer')
		Container.innerHTML = 	'<ul>' + e + '</ul>'
	}
	else{
		Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer')
		Container.innerHTML = 	''
		//Container = document.getElementById('PaymentCommentContainer')
		var b = document.getElementById(o+'_btmSaveBooking')
		b.style.visibility = 'hidden'
		//Container.style.display = 'block'
	}
}

function ValidGroupRegistration(o1,o2){
	Status =  true
	var e = ''
	var pty	= (document.getElementsByName('PaymentTabs_Value') != null)? document.getElementsByName('PaymentTabs_Value').value : null;
	var pm = document.getElementById(o1+'_phone_mobile')
	var ph = document.getElementById(o1+'_phone_home')
	var pb = document.getElementById(o1+'_phone_business')
	var p = trim(pm.value) + trim(ph.value) + trim(pb.value)	
	var m = document.getElementById(o1+'_contact_email').value
	var c = trim(document.getElementById(o1+'_contact_name').value)	
	var gn = document.getElementById(o1+'_GroupName').value
	var tl = (document.getElementById('PassengerList').rows.length - 3) / 2
	var il = (document.getElementById('PassengerList').rows.length - pl) / 2

	if (c==0){
		Status =  false;
		e += "<li>Masukkan contact person</li>"
		document.getElementById(o1+'_contact_name').className = 'error';
	}
	else{
		document.getElementById(o1+'_contact_name').className = '';
	}

	if (p==0){
		Status =  false;
		e += "<li>Masukkan nomor telepon rumah</li>"
		pm.className = 'error'
		ph.className = 'error'
		pb.className = 'error'
	}
	else{
		pm.className = ''
		ph.className = ''
		pb.className = ''
	}
	
	if (trim(m) == 0) {
		Status =  false;
		e += "<li>Masukkan alamat email</li>"
		document.getElementById(o1+'_contact_email').className = 'error';
	}else if (ValidEmail(m)!= true){
		Status =  false;
		e += "<li>Masukkan alamat email sesuai format</li>";
		document.getElementById(o1+'_contact_email').className = 'error';
	}else{
		document.getElementById(o1+'_contact_email').className = ''
	}
	if (gn==0){
		Status =  false;
		e += "<li>Masukkan nama group</li>"
		document.getElementById(o1+'_GroupName').className = 'error';
	}
	else{
		document.getElementById(o1+'_GroupName').className = '';
	}

	for (i=1;i<=il ;i++){
		var ln = document.getElementById(o2+'_ctlPassengerList_PassengerList__ctl' + i + '_Lastname');		
		var fn = document.getElementById(o2+'_ctlPassengerList_PassengerList__ctl' + i + '_Firstname');		
		var wt = document.getElementById(o2+'_ctlPassengerList_PassengerList__ctl' + i + '_PassengerWeight');
		var dn = document.getElementById(o2+'_ctlPassengerList_PassengerList__ctl' + i + '_DateOfBirth');
		
		if ((ln != null) && (fn != null)) {
			var l = trim(ln.value);
			var f = trim(fn.value);
			if((IfAlphaOnly(ln.value) == false && l > 0) || (IfAlphaOnly(fn.value) == false && f > 0)){					
//			if(IfAlphaOnly(ln.value) == false || IfAlphaOnly(fn.value) == false){					
				Status =  false;
				e += '<li>Masukkan nama penumpang di penumpang ' + (addFullNumerical(i,3))+ '</li>'
				ln.className = (IfAlphaOnly(ln.value) == false && l > 0) ? 'error' : '';
				fn.className = (IfAlphaOnly(fn.value) == false && f > 0) ? 'error' : '';				
//				ln.className = (IfAlphaOnly(ln.value) == false) ? 'error' : '';
//				fn.className = (IfAlphaOnly(fn.value) == false) ? 'error' : '';
			}
			else{
				ln.className = '';
				fn.className = '';
			}
		}
				
		if (wt != null) {
			var w = trim(wt.value);
			if (w < 1) {
				Status =  false;
				e += '<li>Please supply a valid Passenger Weight at passenger ' + (addFullNumerical(i,3))+ '</li>';
				wt.className = 'error';
			}
			else{	
				if (isInteger(strim(wt.value)) < 1){
					Status =  false;
					e += '<li>Please supply a valid Passenger Weight at passenger ' + (addFullNumerical(i,3))+ '</li>';
					wt.className = 'error';
				}
				else{
					wt.className = '';
				}
			}
		}
	
		if (dn != null) {	// Date of Birth
			var d = dn.value
			//if (trim(d) > 0 && Date.isValid(d,DateValid) == false){
			if (trim(d) > 0 && isDateExist(d) == false){
				Status =  false;
				e += '<li>' + eMsg[5] + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>'
				dn.className = 'error'
			}
			else{
				if (LibDaysDiff(changeToDate(d),new Date()) > 0){
					Status =  false;
					e += '<li>' + eMsg[17] + (addFullNumerical(i,3))+ '</li>'
					dn.className = 'error'
				}
				else{
					dn.className = ''
				}
			}
		}
        /*
		if (pt == true){
			var p0	= document.getElementById(o2+'_ctlPassengerList_PassengerList__ctl' + i + '_passport_number')
			var p1	= document.getElementById(o2+'_ctlPassengerList_PassengerList__ctl' + i + '_passport_issue_place')
			var p2	= document.getElementById(o2+'_ctlPassengerList_PassengerList__ctl' + i + '_passport_issue_date')
			var p3	= document.getElementById(o2+'_ctlPassengerList_PassengerList__ctl' + i + '_passport_expiry_date')
	
			if (p0 != null) {
				var p0i = trim(p0.value);
				if (ptv == true && p0i < 1){
					Status =  false;
					e += '<li>Masukkan nomor passport di penumpang ' + (addFullNumerical(i,3))+ '</li>'
					p0.className = (p0i < 1) ? 'error' : '';
				}
				else{
					p0.className = ''
				}
			}

			if (p1 != null) {
				var p1i = trim(p1.value)
				if (ptv == true && p1i < 1){
					Status =  false;
					e += '<li>Masukkan tempat issue di penumpang ' + (addFullNumerical(i,3))+ '</li>'
					p1.className = (p1i < 1) ? 'error' : '';
				}
				else{
					p1.className = ''
				}
			}			
			
			if (p2 != null) {	// Issue Date
				var	p2v	= p2.value
				if ((ptv == true || trim(p2v) > 0) && Date.isValid(p2v,DateValid) == false){
					Status =  false;
					e += '<li>' + eMsg[13] + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>'
					p2.className = 'error'
				}
				else{
					if (LibDaysDiff(changeToDate(p2v),new Date()) > 0){
						Status =  false;
						e += '<li>' + eMsg[15] + (addFullNumerical(i,3))+ '</li>'
						p2.className = 'error'
					}
					else{
						p2.className = ''
					}
				}
			}

			if (p3 != null) {	// Expiry Date
				var	p3v	= p3.value
				if ((ptv == true || trim(p3v) > 0) && Date.isValid(p3v,DateValid) == false){
					Status =  false;
					e += '<li>' + eMsg[12] + (addFullNumerical(i,3))+ ' ' + DateValidText + '</li>'
					p3.className = 'error'
				}
				else{
					if (LibDaysDiff(changeToDate(p3v),new Date()) < 0){
						Status =  false;
						e += '<li>' + eMsg[16] + (addFullNumerical(i,3))+ '</li>'
						p3.className = 'error'
					}
					else{
						p3.className = ''
					}
				}
			}
		}
		*/
	}

	if (CcValidation != undefined){
		if (CcValidation.length > 0 && pty == 'CC') {
			var CcStatus =  true;
			var st = document.getElementById(CcValidation + '_tboStreet')
			var cy = document.getElementById(CcValidation + '_tboCity')
			var sa = document.getElementById(CcValidation + '_tboState')
			var zi = document.getElementById(CcValidation + '_tboZipCode')
			if (trim(st.value) == 0){
				CcStatus =  false;
				st.className = 'error'
			}
			else{
				st.className = ''
			}
			if (trim(cy.value) == 0){
				CcStatus =  false;
				cy.className = 'error'	
			}
			else{
				cy.className = ''		
			}
			if (trim(sa.value) == 0){
				CcStatus =  false;
				sa.className = 'error'
			}
			else{
				sa.className = ''	
			}
			if (trim(zi.value) == 0){
				CcStatus =  false;
				zi.className = 'error'
			}
			else{
				zi.className = ''	
			}
			if (!CcStatus){
				Status =  false;
				e += "<li>Masukkan alamat rumah</li>"
			}
		}	

		//if (document.getElementById(o+'_cboConfirm').checked == false){
		//	Status =  false;
		//	e += "<li>Mohon diterima syarat dan kondisi</li>"
		//}

		if (e.length > 0) {
			Container = document.getElementById(o2+'_ctlErrorContainer_ErrorContainer')
			Container.innerHTML = 	'<ul>' + e + '</ul>'
		}
		else{
			Container = document.getElementById(o2+'_ctlErrorContainer_ErrorContainer')
			Container.innerHTML = 	''
			Container = document.getElementById('PaymentCommentContainer')
			var b = document.getElementById(o2+'_btmSubmitBooking')
			b.style.visibility = 'hidden'
			Container.style.display = 'block'
		}	
	}
}

function ValidUpdateGroupBooking(o){
	Status =  true
	var e = ''
	var pm = document.getElementById(o+'_ctrPassengerContactInfo_phone_mobile');
	var ph = document.getElementById(o+'_ctrPassengerContactInfo_phone_home');
	var pb = document.getElementById(o+'_ctrPassengerContactInfo_phone_business');
	var p = trim(pm.value) + trim(ph.value) + trim(pb.value)	;
	var m = document.getElementById(o+'_ctrPassengerContactInfo_contact_email');
	var c = document.getElementById(o+'_ctrPassengerContactInfo_contact_name');
	var gn = document.getElementById(o+'_ctrPassengerContactInfo_GroupName');

	if (trim(c.value)==0){
		Status =  false;
		e += "<li>Masukkan contact person</li>";
		c.className = 'error';
	}
	else{
		c.className = '';
	}

	if (p==0){
		Status =  false;
		e += "<li>Masukkan nomor telepon rumah</li>";
		pm.className = 'error';
		ph.className = 'error';
		pb.className = 'error';
	}
	else{
		pm.className = '';
		ph.className = '';
		pb.className = '';
	}
	
	if (trim(m.value) == 0) {
		Status =  false;
		e += "<li>Masukkan alamat email</li>";
		m.className = 'error';
	}
	else if (ValidEmail(m.value)!= true){
		Status =  false;
		e += "<li>Masukkan alamat email sesuai format</li>";
		m.className = 'error';
	}
	else{
		m.className = '';
	}
	
	if (trim(gn.value)==0){
		Status =  false;
		e += "<li>Masukkan nama group</li>";
		gn.className = 'error';
	}
	else{
		gn.className = '';
	}

	if (e.length > 0) {
		Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer');
		Container.innerHTML = 	'<ul>' + e + '</ul>';
	}
	else{
		Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer');
		Container.innerHTML = 	'';
	}
}

function ValidAgencyLogon(o){
	Status 	=  true;
	var e 	= '';
	var ln 	= document.getElementById(o+'_tboLastName');
	var fn 	= document.getElementById(o+'_tboFirstName');
	var ul 	= document.getElementById(o+'_tboUserLogon');
	var up 	= document.getElementById(o+'_tboUserPassword');
	
	if (trim(fn.value) < 1){
		Status =  false;
		e += '<li>Masukkan nama awal' + '</li>'
		fn.className = 'error';
	}else{
		fn.className = ''
	}
	
	if (trim(ln.value) < 2){
		Status =  false;
		e += '<li>Masukkan nama akhir' + '</li>'
		ln.className = 'error';
	}else{
		ln.className = ''
	}

	if (trim(ul.value)==0){
		Status =  false;
		e += "<li>Masukkan id pengguna</li>"
		ul.className = 'error';
	}
	else{
		ul.className = '';
	}
	
	if (trim(up.value)==0){
		Status =  false;
		e += "<li>Masukkan kata sandi</li>"
		up.className = 'error';
	}
	else{
		up.className = '';
	}
	
	Container = document.getElementById(o+'_ctlErrorContainer_ErrorContainer');
	if (e.length > 0) {
		Container.innerHTML = '<ul>' + e + '</ul>';
	}else{
		Container.innerHTML = '';
	}
}


function CheckPhones(o){
	var pm = document.getElementById(o+'_ctlPassengerContactInfo_phone_mobile')
	var ph = document.getElementById(o+'_ctlPassengerContactInfo_phone_home')
	var pb = document.getElementById(o+'_ctlPassengerContactInfo_phone_business')
	if (goToRow != null){
		var p = trim(pm.value) + trim(ph.value) + trim(pb.value)
			if (p==0){
			pm.className = 'error'
			ph.className = 'error'
			pb.className = 'error'
		}
		else{
			pm.className = ''
			ph.className = ''
			pb.className = ''
		}
	}
}


function ValidEditPassengers(o){
	Status 	=  true;
	var e 	= '';
	var ln 	= document.getElementById(o+'_PassengerList__ctl1_Lastname');
	var fn 	= document.getElementById(o+'_PassengerList__ctl1_Firstname');
	var pw 	= document.getElementById(o+'_PassengerList__ctl1_PassengerWeight');
	var dn 	= document.getElementById(o+'_PassengerList__ctl1_DateOfBirth');
	var dn_type = document.getElementById(o+'_PassengerList__ctl1_Label2');
	
	if (ln != null){
		if (trim(ln.value) < 2 || IfAlphaOnly(ln.value) == false){
			Status =  false;
			e += '<li>Masukkan nama akhir</li>'
			ln.className = 'error';
		}else{
			ln.className = ''
		}
	}
	
	if (fn != null){
		if (trim(fn.value) < 1  || IfAlphaOnly(fn.value) == false){
			Status =  false;
			e += '<li>Masukkan nama awal</li>'
			fn.className = 'error';
		}else{
			fn.className = ''
		}
	}
	
	if (pw != null){
		if (trim(pw.value)==0){
			Status =  false;
			e += "<li>Masukkan berat badan penumpang</li>"
			pw.className = 'error';
		}else{
			pw.className = '';
		}
	}
    
    /*
	if (dn != null) {	// Date of Birth
		var d = dn.value
		//if (trim(d) > 0 && Date.isValid(d,DateValid) == false){
		if (trim(d) > 0 && isDateExist(d) == false){
			Status =  false;
			e += '<li>' + eMsg[3] + DateValidText + '</li>'
			dn.className = 'error'
		}
		else{
			if (LibDaysDiff(changeToDate(d),new Date()) > 0){
				Status =  false;
				e += '<li>' + eMsg[18] + '</li>'
				dn.className = 'error'
			}
			else{
				dn.className = ''
			}
		}
	}*/
	
	if (dn != null) {	// Date of Birth
        var d = dn.value;
        var dt = dn_type.innerHTML;
        //if (trim(d) > 0 && Date.isValid(d,DateValid) == false){
        if (trim(d) > 0 && isDateExist(d) == false){
            Status =  false;
            e += '<li>' + eMsg[5] + DateValidText + '</li>';
            dn.className = 'error';
        }
        else{
            if (LibDaysDiff(changeToDate(d),new Date()) > 0){
                Status =  false;
                e += '<li>' + eMsg[17] + '</li>';
                dn.className = 'error';
            }
			else{
			    if (dt == "CHD" || dt == "INF"){
			        //if(Date.isValid(d,DateValid) == false){
			        if(isDateExist(d) == false){
				        Status =  false;
					    e += '<li>' + eMsg[5] + DateValidText + '</li>';
				        dn.className = 'error';
				    }
		            else{
                        //Child  2-11 year
                        //Infant 1-23 month
                        var MIN_CHD_AGE = (2*12);
                        var MAX_CHD_AGE = (11*12);

                        var MIN_INF_AGE = (1);
                        var MAX_INF_AGE = (23);
                            
                        var dd = Number(d.substring(0,2));
                        var mm = Number(d.substring(3,5));
                        var yy = Number(d.substring(6,10));
                            mm = mm - 1;
                        var iDate = new Date(yy,mm,dd);
                        var cDate = new Date();
                        var mmAge = dateDiff("m",iDate,cDate,1,1); 				        
				            
				        //var fDate = document.getElementById('ctrBooking_ctrItineraryBase_ctrItineraryGrid_grdItineraryGrid__ctl0_Td46');
					    //var fDate = document.getElementById('ctrBooking_ctrPassengerInfosBase_ctlBookingInfo_LabDepartureDate');
				        if(dt == "CHD"){
				            if(!((MIN_CHD_AGE<=mmAge)&&(mmAge<=MAX_CHD_AGE))){
                                e += '<li>' + 'Usia anak-anak harus diantara 2-11 tahun' + '</li>';
                                Status =  false;
			                    dn.className = 'error';
			                }
				            else{
					            dn.className = '';
					        }
                        }
                        else if(dt == "INF"){
                            if(!((MIN_INF_AGE<=mmAge)&&(mmAge<=MAX_INF_AGE))){
                                e += '<li>' + 'Usia bayi harus diantara 1-23 bulan' + '</li>';
			                    Status =  false;
			                    dn.className = 'error';
			                }
		                    else{
					            dn.className = '';
					        }
				        }
	                }
                }
                else{
		            dn.className = '';
		        }
            }
        }
	}

	if (pt == true){
		var p0	= document.getElementById(o+'_PassengerList__ctl1_passport_number')
		var p1	= document.getElementById(o+'_PassengerList__ctl1_passport_issue_place')
		var p2	= document.getElementById(o+'_PassengerList__ctl1_passport_issue_date')
		var p3	= document.getElementById(o+'_PassengerList__ctl1_passport_expiry_date')

		if (p0 != null) {
			var p0i = trim(p0.value);
			if (ptv == true && p0i < 1){
				Status =  false;
				e += '<li>Please supply a valid Passport Number</li>'
				p0.className = (p0i < 1) ? 'error' : '';
			}
			else{
				p0.className = ''
			}
		}
		
		/*
		if (p1 != null) {
			var p1i = trim(p1.value)
			if (ptv == true && p1i < 1){
				Status =  false;
				e += '<li>Please supply a valid Issue Place</li>'
				p1.className = (p1i < 1) ? 'error' : '';
			}
			else{
				p1.className = ''
			}
		}
		*/		
		
		if (p2 != null) {
			var	p2v	= p2.value
			if(trim(p2v) > 0) {
			    //if ((ptv == true || trim(p2v) > 0) && Date.isValid(p2v,DateValid) == false){
			    if ((ptv == true || trim(p2v) > 0) && isDateExist(p2v) == false){
				    Status =  false;
				    e += '<li>Please supply a valid Issue Date ' + DateValidText + '</li>'
				    p2.className = 'error'
			    }
			    else{
				    if (LibDaysDiff(changeToDate(p2v),new Date()) > 0){
					    Status =  false;
					    e += '<li>Please check your Issue Date! It must be in the past</li>'
					    p2.className = 'error'
				    }
				    else{
					    p2.className = ''
				    }
			    }
			}
		}
		
		if (p3 != null) {
			var	p3v	= p3.value
			if(trim(p3v) > 0) {
			    //if ((ptv == true || trim(p3v) > 0) && Date.isValid(p3v,DateValid) == false){
			    if ((ptv == true || trim(p3v) > 0) && isDateExist(p3v) == false){
				    Status =  false;
				    e += '<li>Masukkan tanggal berakhir ' + DateValidText + '</li>'
				    p3.className = 'error'
			    }
			    else{
				    if (LibDaysDiff(changeToDate(p3v),new Date()) < 0){
					    Status =  false;
					    e += '<li>Mohon cek tanggal berakhir anda</li>'
					    p3.className = 'error'
				    }
				    else{
					    p3.className = ''
				    }
			    }
			}
		}
	}
	
	Container = document.getElementById(o+'_PassengerList__ctl2_LabError');
	if (e.length > 0) {
		Container.innerHTML = '<ul>' + e + '</ul>';
	}else{
		Container.innerHTML = '';
	}
}

function trim(s){	
	return s.replace(/^\s*|\s*$/g,"").length;
}	
	
function strim(s){
	return s.replace(/^\s*|\s*$/g,"");
}	

function OnSubmit(){
	if (Status){
		return true;
	}
	else
	{
		Status = true
		return false;
	}

}

function GetSelectFlight(s,j){
	var o = document.getElementById(s);
	if (o != null){
		var NN4 = document.layers? true : false;
		var IE4 = document.all? true : false;
		var FF 	= document.getElementById? true : false;
		var nodeRadio
		var nodeHidden
		var nodeRadio_WaitList
		for (i=1;i<=o.rows.length-1;i++){
			if (IE4) {
				if(subStringRight(o.rows(i).cells(o.rows(i).cells.length-1).id, 11) == "RowWaitList"){
					nodeRadio_WaitList = o.rows(i).cells(o.rows(i).cells.length-1).children(0);
					nodeRadio = o.rows(i).cells(o.rows(i).cells.length-2).children(0);
					nodeHidden = o.rows(i).cells(o.rows(i).cells.length-2).children(1);
				} else {
					nodeRadio = o.rows(i).cells(o.rows(i).cells.length-1).children(0);
					nodeHidden = o.rows(i).cells(o.rows(i).cells.length-1).children(1);
				}
			} else if(NN4) {
				if(subStringRight(o.rows[i].cells[o.rows[i].cells.length-1].id, 11) == "RowWaitList"){
					nodeRadio_WaitList = o.rows[i].cells[o.rows[i].cells.length-1].childNodes[0];
					nodeRadio = o.rows[i].cells[o.rows[i].cells.length-2].childNodes[0];
					nodeHidden = o.rows[i].cells[o.rows[i].cells.length-2].childNodes[1];
				} else {
					nodeRadio = o.rows[i].cells[o.rows[i].cells.length-1].childNodes[0];
					nodeHidden = o.rows[i].cells[o.rows[i].cells.length-1].childNodes[1];
				}
			} else if(FF) {
				if(subStringRight(o.rows[i].cells[o.rows[i].cells.length-1].id, 11) == "RowWaitList"){
					nodeRadio_WaitList = o.rows[i].cells[o.rows[i].cells.length-1].childNodes[0];
					nodeRadio = o.rows[i].cells[o.rows[i].cells.length-2].childNodes[0];
					nodeHidden = o.rows[i].cells[o.rows[i].cells.length-2].childNodes[1];
				} else {
					nodeRadio = o.rows[i].cells[o.rows[i].cells.length-1].childNodes[0];
					nodeHidden = o.rows[i].cells[o.rows[i].cells.length-1].childNodes[1];
				}
			} else {
				alert('Unknown browser category.');
			}
			if (nodeRadio.type == 'radio' || (nodeRadio_WaitList != undefined && nodeRadio_WaitList.type == 'radio')){
				if (nodeRadio.checked || (nodeRadio_WaitList != undefined && nodeRadio_WaitList.checked)){	
					var s = nodeHidden.name.split('|');
					var d = s[j].split(',')
					//	d[0] is Year (value is yyyy)
					//	d[1] is Month (value is 0-11) ** That's why must be minus 1
					//	d[2] is Day (value is 1-31)
					return new Date(d[0],d[1]-1,d[2],d[3],d[4],0)
				}
			}
		}
	}
}

function LibDaysDiff(d1,d2) {
	var z = Date.UTC(Liby2k(d1.getYear()),d1.getMonth(),d1.getDate(),d1.getHours(),d1.getMinutes(),0) - Date.UTC(Liby2k(d2.getYear()),d2.getMonth(),d2.getDate(),d2.getHours(),d2.getMinutes(),0);
	return z/1000/60/60/24;
}

function Liby2k(number) { return (number < 1000) ? number + 1900 : number; }

if (document.getElementById('ctrHome_ctrFlightSearchBase_ctlDepatureDate_cmbDay')!=null) {
		ChangeCalender('ctrHome_ctrFlightSearchBase_ctlDepatureDate','')
		ChangeCalender('ctrHome_ctrFlightSearchBase_ctlReturnDate','')
}

function SetAutoFocus(){
	if (AutoFocus != undefined && AutoFocus != ""){
		if (document.getElementById(AutoFocus) != null){
			document.getElementById(AutoFocus).focus();
		}
	}
}

function isValidEmail(str){
	str = strim(str);
	return 	(str.indexOf(".") > 0) &&						// not started with "."
			(str.indexOf("@") > 0) && 						// must consist of "@" and not started with it
			(str.lastIndexOf(".") > str.indexOf("@")) && 	// must have "." after "@"
			(str.lastIndexOf(".") <  str.length - 1);		// not ended with "."
}

function ValidEmail(s){
	var a = false;
	var res = false;
	if(typeof(RegExp) == 'function'){
		var b = new RegExp('abc');
		if(b.test('abc') == true){
			a = true;
		}
	}
	if(a == true){
		//reg = new RegExp('^([a-zA-Z0-9\\-\\.\\_]+)'+'(\\@)([a-zA-Z0-9\\-\\.]+)'+'(\\.)([a-zA-Z]{2,4})$');
		reg = new RegExp("^([a-zA-Z0-9\\-\\.\\_\\']+)" + "(\\@)([a-zA-Z0-9\\-\\.]+)" + "(\\.)([a-zA-Z]{2,4})$");
		res = (reg.test(s));
	}
	else{
		res = (s.search('@') >= 1 &&
		s.lastIndexOf('.') > s.search('@') &&
		s.lastIndexOf('.') >= s.length-5)
	}
	return(res);
}

function getCookie(name) {
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } else
    begin += 2;
  var end = document.cookie.indexOf(";", begin);
  if (end == -1)
    end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}

function getSubCookie(name,parent) {
  var dc = parent;
  var prefix = name + "=";
  var begin = dc.indexOf("&" + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } else
    begin += 1;
  var end = parent.indexOf("&", begin);
  if (end == -1)
    end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}

function CreatePopup(l,p){
	if (p == 'f'){
		CreateWnd('html/FareCode/' + l + '.html', 200, 100, true);
		dgItemLock = true
	}
}

function GetCardSubType(CcNumber){
	var l = new Number(CcNumber.slice(0,6))
	var iA = document.getElementById(CcPreselect + '_cbmFormOfPaymentSubtype').options[document.getElementById(CcPreselect + '_cbmFormOfPaymentSubtype').selectedIndex].value.split('|')
	SelectedCardType = iA[0]
	if 	(
			(l >= 413733 && l <= 413737 ) || 
			(l >= 446200 && l <= 446299 ) || 
			(l >= 453978 && l <= 453979) || 
			(l == 454313 ) ||
			(l >= 454432 && l <= 446299 ) || 
			(l == 454742) ||
			(l >= 456725 && l <= 456745 ) || 
			(l >= 465830 && l <= 465879 ) ||
			(l >= 465901 && l <= 465950 ) ||
			(l >= 475110 && l <= 475159 ) ||
			(l >= 475710 && l <= 475759 ) ||
			(l >= 476220 && l <= 476269 ) ||
			(l >= 476340 && l <= 476389 ) ||
			(l >= 484409 && l <= 484410 ) ||
			(l >= 490960 && l <= 490979 ) ||
			(l >= 492181 && l <= 492182 ) ||
			(l == 498824)
		)
	{
		CardType = "VISADRUK"
	}
	else if
		(
			(l  == 424519) ||
			(l >= 424962 && l <= 424963 ) ||
			(l == 450875) ||
			(l >= 484406 && l <= 484408 ) ||
			(l >= 484411 && l <= 484455 ) ||
			(l >= 491730 && l <= 491759 ) ||
			(l == 491880)
		)
	{
		CardType = "VISAELUK"
	}
	else if
		(
			(l >= 448400 && l <= 448699 ) ||
			(l >= 471500 && l <= 471599 )
		)
	{
		CardType = "VISAPU"
	}
	else if
		(
			(l == 490303) ||
			(l == 493698) ||
			(l == 633311) ||
			(l >= 675900 && l <= 675999)
		)
	{
		CardType = "MAESTROUK"
	}
	else if
		(
			(l >= 510000 && l <= 559999)
		)
	{
		CardType = "MC"
	}
	else if
		(
			(l >= 676700 && l <= 676799)
		)
	{
		CardType = "SOLO"
	}
	else if
		(
			(l >= 6180000 && l <= 180099) ||
			(l >= 6213100 && l <= 213199)
		)
	{
		CardType = "JCB"
	}
	else if
		(
			(l >= 352800 && l <= 358999)
		)
	{
		if (SelectedCardType == "DC"){
			CardType = "DC"}
		else if (SelectedCardType == "AMEX"){
			CardType = "AMEX"}
		else{
			CardType = "JCB"
		}
	}
	else if
		(
			(l >= 300000 && l <= 369999)
		)
	{
		if (SelectedCardType == "AMEX"){
			CardType =  "AMEX"}
		else{
			CardType = "DC"
		}
	}
	else if
		(
			(l >= 340000 && l <= 379999)
		)
	{
		if (SelectedCardType == "AMEX"){
			CardType =  "AMEX"}
		else{
			CardType = "JCB"
		}
	}
	else if
		(
			(l >= 300000 && l <= 399999)
		)
	{
		CardType =  "JCB"
	}
	else if
		(
			(l >= 500000 && l <= 509999) ||
			(l >= 560000 && l <= 589999) ||
			(l >= 600000 && l <= 699999)
		)
	{
		CardType = "MAESTRO"
	}
	else if
		(
			(l >= 400000 && l <= 499999) 
		)
	{
		CardType = "VISA"
	}
	else
	{
		CardType = ""
	}
	
	// CardType comes from number from input, not from dropdownlist.
	
	var FeeAmmount = new Number(0);
	var Total = parseFloat(CcTotalValue);
	for (i=0;i < FeesRcd.length ;i++){
		if (CardType.toUpperCase() == FeesRcd[i].toUpperCase()){
			FeeAmmount = parseFloat(FeesAmmount[i]);
		}
	}
	if (CardType != "" && FeeAmmount > 0){
		Total = Total + parseFloat(FeeAmmount);
		document.getElementById('CcMessage').innerHTML = 'Kartu kredit anda akan di charged oleh ' + parseFloat(FeeAmmount).toFixed(2) + ' ' + CcCurrency + ' Service ' + Total.toFixed(2) + ' ' + CcCurrency;
		document.getElementById('CcError').innerHTML = '';
	}
	else
	{
		document.getElementById('CcMessage').innerHTML = 'Kartu kredit anda akan di charged oleh ' + Total.toFixed(2) + ' ' + CcCurrency;
		document.getElementById('CcError').innerHTML = '';
	}
	return CardType;
}

function IsNumeric(value) {
	value = value
	return (!isNaN(value*1) && !IsSpace(value)); }

function IsSpace(value) {
	return (value==' ');
}

function SelectNationality(obj){
	var idStart, idEnd;
	idStart = 'ctrBooking_ctrPassengerInfosBase_ctlPassengerList_PassengerList__ctl';
	idEnd 	= '_nationality_rcd';
	var order = Number(obj.id.replace(idStart,'').replace(idEnd,''))
	var count = document.forms[0].elements.length;
	for (i=0; i<count; i++) {
		var element = document.forms[0].elements[i]; 			
		if (element.type == "select-one"){
			if(Number(element.id.replace(idStart,'').replace(idEnd,'')) > order){
				element.selectedIndex = obj.selectedIndex ;
			}
		} 
	}
}

Date.$VERSION = 1.01;
Date.LZ = function(x){return(x<0||x>9?"":"0")+x};Date.monthNames = new Array('January','February','March','April','May','June','July','August','September','October','November','December');Date.monthAbbreviations = new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');Date.dayNames = new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');Date.dayAbbreviations = new Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');Date.preferAmericanFormat = true;if(!Date.prototype.getFullYear){Date.prototype.getFullYear = function(){var yy=this.getYear();return(yy<1900?yy+1900:yy);}}
Date.parseString = function(val, format){if(typeof(format)=="undefined" || format==null || format==""){var generalFormats=new Array('y-M-d','MMM d, y','MMM d,y','y-MMM-d','d-MMM-y','MMM d','MMM-d','d-MMM');var monthFirst=new Array('M/d/y','M-d-y','M.d.y','M/d','M-d');var dateFirst =new Array('d/M/y','d-M-y','d.M.y','d/M','d-M');var checkList=new Array(generalFormats,Date.preferAmericanFormat?monthFirst:dateFirst,Date.preferAmericanFormat?dateFirst:monthFirst);for(var i=0;i<checkList.length;i++){var l=checkList[i];for(var j=0;j<l.length;j++){var d=Date.parseString(val,l[j]);if(d!=null){return d;}}}return null;}
this.isInteger = function(val){for(var i=0;i < val.length;i++){if("1234567890".indexOf(val.charAt(i))==-1){return false;}}return true;};
this.getInt = function(str,i,minlength,maxlength){for(var x=maxlength;x>=minlength;x--){var token=str.substring(i,i+x);if(token.length < minlength){return null;}if(this.isInteger(token)){return token;}}return null;};val=val+"";format=format+"";var i_val=0;var i_format=0;var c="";var token="";var token2="";var x,y;var year=new Date().getFullYear();var month=1;var date=1;var hh=0;var mm=0;var ss=0;var ampm="";while(i_format < format.length){c=format.charAt(i_format);token="";while((format.charAt(i_format)==c) &&(i_format < format.length)){token += format.charAt(i_format++);}if(token=="yyyy" || token=="yy" || token=="y"){if(token=="yyyy"){x=4;y=4;}if(token=="yy"){x=2;y=2;}if(token=="y"){x=2;y=4;}year=this.getInt(val,i_val,x,y);if(year==null){return null;}i_val += year.length;if(year.length==2){if(year > 70){year=1900+(year-0);}else{year=2000+(year-0);}}}else if(token=="MMM" || token=="NNN"){month=0;var names =(token=="MMM"?(Date.monthNames.concat(Date.monthAbbreviations)):Date.monthAbbreviations);for(var i=0;i<names.length;i++){var month_name=names[i];if(val.substring(i_val,i_val+month_name.length).toLowerCase()==month_name.toLowerCase()){month=(i%12)+1;i_val += month_name.length;break;}}if((month < 1)||(month>12)){return null;}}else if(token=="EE"||token=="E"){var names =(token=="EE"?Date.dayNames:Date.dayAbbreviations);for(var i=0;i<names.length;i++){var day_name=names[i];if(val.substring(i_val,i_val+day_name.length).toLowerCase()==day_name.toLowerCase()){i_val += day_name.length;break;}}}else if(token=="MM"||token=="M"){month=this.getInt(val,i_val,token.length,2);if(month==null||(month<1)||(month>12)){return null;}i_val+=month.length;}else if(token=="dd"||token=="d"){date=this.getInt(val,i_val,token.length,2);if(date==null||(date<1)||(date>31)){return null;}i_val+=date.length;}else if(token=="hh"||token=="h"){hh=this.getInt(val,i_val,token.length,2);if(hh==null||(hh<1)||(hh>12)){return null;}i_val+=hh.length;}else if(token=="HH"||token=="H"){hh=this.getInt(val,i_val,token.length,2);if(hh==null||(hh<0)||(hh>23)){return null;}i_val+=hh.length;}else if(token=="KK"||token=="K"){hh=this.getInt(val,i_val,token.length,2);if(hh==null||(hh<0)||(hh>11)){return null;}i_val+=hh.length;hh++;}else if(token=="kk"||token=="k"){hh=this.getInt(val,i_val,token.length,2);if(hh==null||(hh<1)||(hh>24)){return null;}i_val+=hh.length;hh--;}else if(token=="mm"||token=="m"){mm=this.getInt(val,i_val,token.length,2);if(mm==null||(mm<0)||(mm>59)){return null;}i_val+=mm.length;}else if(token=="ss"||token=="s"){ss=this.getInt(val,i_val,token.length,2);if(ss==null||(ss<0)||(ss>59)){return null;}i_val+=ss.length;}else if(token=="a"){if(val.substring(i_val,i_val+2).toLowerCase()=="am"){ampm="AM";}else if(val.substring(i_val,i_val+2).toLowerCase()=="pm"){ampm="PM";}else{return null;}i_val+=2;}else{if(val.substring(i_val,i_val+token.length)!=token){return null;}else{i_val+=token.length;}}}if(i_val != val.length){return null;}if(month==2){if( ((year%4==0)&&(year%100 != 0) ) ||(year%400==0) ){if(date > 29){return null;}}else{if(date > 28){return null;}}}if((month==4)||(month==6)||(month==9)||(month==11)){if(date > 30){return null;}}if(hh<12 && ampm=="PM"){hh=hh-0+12;}else if(hh>11 && ampm=="AM"){hh-=12;}return new Date(year,month-1,date,hh,mm,ss);}
Date.isValid = function(val,format){return(Date.parseString(val,format) != null);}
Date.prototype.isBefore = function(date2){if(date2==null){return false;}return(this.getTime()<date2.getTime());}
Date.prototype.isAfter = function(date2){if(date2==null){return false;}return(this.getTime()>date2.getTime());}
Date.prototype.equals = function(date2){if(date2==null){return false;}return(this.getTime()==date2.getTime());}
Date.prototype.equalsIgnoreTime = function(date2){if(date2==null){return false;}var d1 = new Date(this.getTime()).clearTime();var d2 = new Date(date2.getTime()).clearTime();return(d1.getTime()==d2.getTime());}
Date.prototype.format = function(format){format=format+"";var result="";var i_format=0;var c="";var token="";var y=this.getYear()+"";var M=this.getMonth()+1;var d=this.getDate();var E=this.getDay();var H=this.getHours();var m=this.getMinutes();var s=this.getSeconds();var yyyy,yy,MMM,MM,dd,hh,h,mm,ss,ampm,HH,H,KK,K,kk,k;var value=new Object();if(y.length < 4){y=""+(+y+1900);}value["y"]=""+y;value["yyyy"]=y;value["yy"]=y.substring(2,4);value["M"]=M;value["MM"]=Date.LZ(M);value["MMM"]=Date.monthNames[M-1];value["NNN"]=Date.monthAbbreviations[M-1];value["d"]=d;value["dd"]=Date.LZ(d);value["E"]=Date.dayAbbreviations[E];value["EE"]=Date.dayNames[E];value["H"]=H;value["HH"]=Date.LZ(H);if(H==0){value["h"]=12;}else if(H>12){value["h"]=H-12;}else{value["h"]=H;}value["hh"]=Date.LZ(value["h"]);value["K"]=value["h"]-1;value["k"]=value["H"]+1;value["KK"]=Date.LZ(value["K"]);value["kk"]=Date.LZ(value["k"]);if(H > 11){value["a"]="PM";}else{value["a"]="AM";}value["m"]=m;value["mm"]=Date.LZ(m);value["s"]=s;value["ss"]=Date.LZ(s);while(i_format < format.length){c=format.charAt(i_format);token="";while((format.charAt(i_format)==c) &&(i_format < format.length)){token += format.charAt(i_format++);}if(value[token] != null){result=result + value[token];}else{result=result + token;}}return result;}
Date.prototype.getDayName = function(){return Date.dayNames[this.getDay()];}
Date.prototype.getDayAbbreviation = function(){return Date.dayAbbreviations[this.getDay()];}
Date.prototype.getMonthName = function(){return Date.monthNames[this.getMonth()];}
Date.prototype.getMonthAbbreviation = function(){return Date.monthAbbreviations[this.getMonth()];}
Date.prototype.clearTime = function(){this.setHours(0);this.setMinutes(0);this.setSeconds(0);this.setMilliseconds(0);
return this;}
Date.prototype.add = function(interval, number){if(typeof(interval)=="undefined" || interval==null || typeof(number)=="undefined" || number==null){
return this;}number = +number;if(interval=='y'){this.setFullYear(this.getFullYear()+number);}else if(interval=='M'){this.setMonth(this.getMonth()+number);}else if(interval=='d'){this.setDate(this.getDate()+number);}else if(interval=='w'){var step =(number>0)?1:-1;while(number!=0){this.add('d',step);while(this.getDay()==0 || this.getDay()==6){this.add('d',step);}number -= step;}}else if(interval=='h'){this.setHours(this.getHours() + number);}else if(interval=='m'){this.setMinutes(this.getMinutes() + number);}else if(interval=='s'){this.setSeconds(this.getSeconds() + number);}
return this;}
