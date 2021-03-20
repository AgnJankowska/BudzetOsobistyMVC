let n =  new Date();
let currentDay = n => n.getDate();
let currentMonth = n => n.getMonth() + 1;
let currentYear = n => n.getFullYear();

window.onload = setDatesStart();
	
function isThisYearLeap(year) {
  return((year % '4' == '0' && year % '100' != '0') || year % '400' == '0') ? true : false; 
}

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
	let d1;
	let m1;
	let y1;
	
	if (start==1){
		d1 = '01';
		m1 = currentMonth(n);
			if(m1<10) m1 = '0'+m1;
		y1 = currentYear(n);
	return y1 + "-" + m1 + "-" + d1}
	
	else if (start==2){
		d1 = '01';
		if(currentMonth(n)==1){
			m1 = 12;
			y1 = currentYear()-1;
		}
		else{
			m1 = currentMonth(n)-1;
			if(m1<10) m1 = '0'+m1;
			y1 = currentYear(n);
		}
		return y1 + "-" + m1 + "-" + d1}
		
	else if (start==3){
		d1 = '01';
		m1 = '01';
		y1 = currentYear(n);
		return y1 + "-" + m1 + "-" + d1}
		
	else 
		return ''
}

function setEndDate(end) {
	let d2;
	let m2;
	let y2;
	
	if (end==1){
		d2 = currentDay(n);
			if(d2<10) d2 = '0'+d2;
		m2 = currentMonth(n);
			if(m2<10) m2 = '0'+m2;
		y2 = currentYear(n);
		return y2 + "-" + m2 + "-" + d2}
	
	else if (end==2){
			
		if( currentMonth(n)==1){
			m2 = 12
			y2 = currentYear(n)-1;
			d2 = checkNumberOfDays(y2, m2);
		}
		
		else{
			m2 = currentMonth(n)-1;
			y2 = currentYear(n);
			d2 = checkNumberOfDays(y2, m2);	
		}
		
		if(m2<10) m2 = '0'+m2;
		return y2 + "-" + m2 + "-" + d2}
		
	else if (end==3){
		d2 = currentDay(n);
			if(d2<10) d2 = '0'+d2;
		m2 = currentMonth(n);
			if(m2<10) m2 = '0'+m2;
		y2 = currentYear(n);
		return y2 + "-" + m2 + "-" + d2}
		
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