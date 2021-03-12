window.onload = setDatesStart();

//Start and end of balance
function currentDay() {
	n =  new Date();
	let d = n.getDate();
	return d}
	
function currentMonth() {
	n =  new Date();
	let m = n.getMonth() + 1;
	return m}

function currentYear() {
	n =  new Date();
	let y = n.getFullYear();
	return y}

function isThisYearLeap(year) {
  if ((year % '4' == '0' && year % '100' != '0') || year % '400' == '0')
    return true;
  else
    return false; }

function checkNumberOfDays (year, month) {
 switch (month) {
   case 1:
   case 3:
   case 5:
   case 7:
   case 8:
   case 10:
   case 12: 
       return '31';
   
   case 4:
   case 6:
   case 9:
   case 11: 
       return '30';

   case 2: 
       if (isThisYearLeap(year)) return '29';
       else                      return '28';
   }
}

function setStartDate(start) {
	if (start==1){
		d = '01';
		m = currentMonth();
			if(m<10) m = '0'+m;
		y = currentYear();
		return y + "-" + m + "-" + d}
	
	else if (start==2){
		d = '01';
		if(currentMonth()==1){
			m = 12;
			y = currentYear()-1;
		}
		else{
			m = currentMonth()-1;
			if(m<10) m = '0'+m;
			y = currentYear();
		}
		return y + "-" + m + "-" + d}
		
	else if (start==3){
		d = '01';
		m = '01';
		y = currentYear();
		return y + "-" + m + "-" + d}
		
	else 
		return ''
}

function setEndDate(end) {
	if (end==1){
		d = currentDay();
			if(d<10) d = '0'+d;
		m = currentMonth();
			if(m<10) m = '0'+m;
		y = currentYear();
		return y + "-" + m + "-" + d}
	
	else if (end==2){
			
		if( currentMonth()==1){
			m = 12
			y = currentYear()-1;
			d = checkNumberOfDays(y, m);
		}
		
		else{
			m = currentMonth()-1;
			y = currentYear();
			d = checkNumberOfDays(y, m);
			
		}
		
		if(m<10) m = '0'+m;
		return y + "-" + m + "-" + d}
		
	else if (end==3){
		d = currentDay();
			if(d<10) d = '0'+d;
		m = currentMonth();
			if(m<10) m = '0'+m;
		y = currentYear();
		return y + "-" + m + "-" + d}
		
	else 
		return ''
}

function setDatesStart() {
	let number = document.getElementById("range").value;
	
	if (number==4){
		document.getElementById("date_start").value = document.getElementById("date1").value;
		document.getElementById("date_end").value = document.getElementById("date2").value;
		}
	
	else {		
		document.getElementById("date_start").value = setStartDate(number);
		document.getElementById("date_end").value = setEndDate(number);
	}
}

function sendForm(){
		document.getElementById("range").value = "4";
		document.form.submit();	
}

function setDates() {
	let number = document.getElementById("range").value;
		
	if (number==4){$("#exampleModal").modal();}
	else {	
		setDatesStart();
		document.form.submit();
	}
}